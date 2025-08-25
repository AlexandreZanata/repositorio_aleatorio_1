<?php
// Arquivo de funções utilitárias e autenticação
// Gerencia tokens JWT e funções de segurança

// Prevenir acesso direto
if (!defined('SYSTEM_LOADED')) {
    die('Acesso direto não permitido');
}

/**
 * Classe para gerenciar autenticação e tokens
 */
class Auth {
    /**
     * Gera um token JWT
     * * @param array $userData Dados do usuário para incluir no token
     * @return string Token JWT
     */
    public static function generateToken($userData) {
        // Cabeçalho JWT
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);
        $header = self::base64UrlEncode($header);
        
        // --- ALTERAÇÃO CRÍTICA AQUI ---
        // Busca o nome do departamento para salvar no token junto com o ID.
        // Isso garante que o ID numérico e o nome fiquem em chaves separadas,
        // evitando o conflito que impedia a busca de veículos.
        $departmentName = '';
        if (isset($userData['department_id'])) {
            global $db; // Garante que a variável do banco de dados esteja acessível
            $stmt = $db->query("SELECT name FROM departments WHERE id = ?", [$userData['department_id']]);
            $dept = $stmt->fetch();
            $departmentName = $dept['name'] ?? 'N/A';
        }

        // Payload JWT com dados do usuário e expiração
        $payload = json_encode([
            'sub' => $userData['id'],
            'name' => $userData['name'],
            'department_id'   => $userData['department_id'],    // Chave para o ID numérico da secretaria
            'department_name' => $departmentName,                // Chave para o Nome da secretaria
            'subdepartamento' => $userData['subdepartment_id'],
            'email' => $userData['email'],
            'iat' => time(),
            'exp' => time() + TOKEN_EXPIRY
        ]);
        $payload = self::base64UrlEncode($payload);
        
        // Assinatura
        $signature = hash_hmac('sha256', "$header.$payload", JWT_SECRET, true);
        $signature = self::base64UrlEncode($signature);
        
        return "$header.$payload.$signature";
    }
    
    /**
     * Verifica se um token JWT é válido
     * * @param string $token Token JWT para verificar
     * @return array|false Dados do usuário ou false se inválido
     */
    public static function validateToken($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $parts;
        
        // Verificar assinatura
        $valid = hash_hmac('sha256', "$header.$payload", JWT_SECRET, true);
        $valid_signature = self::base64UrlEncode($valid);
        
        if ($signature !== $valid_signature) {
            return false;
        }
        
        // Decodificar payload
        $payload = json_decode(self::base64UrlDecode($payload), true);
        
        // Verificar expiração
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Login do usuário
     * * @param string $email Email do usuário
     * @param string $password Senha do usuário
     * @return array|false Dados do usuário ou false se falhar
     */
    public static function login($email, $password) {
        global $db;
        
        $stmt = $db->query(
            "SELECT id, name, email, password_hash, department_id, subdepartment_id FROM users WHERE email = ?",
            [$email]
        );
        
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        // Verificar senha usando password_verify
        if (password_verify($password, $user['password_hash'])) {
            // Não incluir password_hash nos dados retornados
            unset($user['password_hash']);
            
            // Gerar token
            $token = self::generateToken($user);
            
            // Salvar token em cookie seguro (httponly, secure em produção)
            self::setAuthCookie($token);
            
            // Registrar login bem-sucedido
            self::logLoginAttempt($email, 'success');
            
            return [
                'user' => $user,
                'token' => $token
            ];
        }
        
        // Registrar tentativa de login falha
        self::logLoginAttempt($email, 'failed', 'Invalid password');
        
        return false;
    }
    
    /**
     * Registra tentativas de login
     */
    private static function logLoginAttempt($email, $status, $reason = null) {
        global $db;
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $db->query(
            "INSERT INTO login_attempts (email, ip_address, user_agent, status, failure_reason) VALUES (?, ?, ?, ?, ?)",
            [$email, $ip, $userAgent, $status, $reason]
        );
        
        if ($status === 'failed') {
            self::checkLoginLockout($email, $ip);
        }
    }
    
    /**
     * Verifica se o usuário deve ser bloqueado por muitas tentativas de login
     */
    private static function checkLoginLockout($email, $ip) {
        global $db;
        
        $maxAttempts = self::getSystemSetting('login_attempts', 5);
        $lockoutTime = self::getSystemSetting('lockout_time', 30);
        
        $stmt = $db->query(
            "SELECT COUNT(*) as attempts FROM login_attempts 
            WHERE (email = ? OR ip_address = ?) 
            AND status = 'failed' 
            AND attempt_time > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$email, $ip, $lockoutTime]
        );
        
        $result = $stmt->fetch();
        
        if ($result && $result['attempts'] >= $maxAttempts) {
            $db->query(
                "INSERT INTO activity_logs (activity_type, description, ip_address) VALUES (?, ?, ?)",
                ['security', "Account lockout triggered for email: $email", $ip]
            );
        }
    }
    
    /**
     * Obtém uma configuração do sistema
     */
    private static function getSystemSetting($key, $default = null) {
        global $db;
        
        $stmt = $db->query(
            "SELECT setting_value, data_type FROM system_settings WHERE setting_key = ?",
            [$key]
        );
        
        $setting = $stmt->fetch();
        
        if (!$setting) {
            return $default;
        }
        
        switch ($setting['data_type']) {
            case 'integer': return (int) $setting['setting_value'];
            case 'float': return (float) $setting['setting_value'];
            case 'boolean': return $setting['setting_value'] === 'true';
            case 'json': return json_decode($setting['setting_value'], true);
            default: return $setting['setting_value'];
        }
    }
    
    /**
     * Define cookie de autenticação
     */
    public static function setAuthCookie($token) {
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $httpOnly = true;
        $samesite = 'Strict';
        $expires = time() + TOKEN_EXPIRY;
        
        setcookie('auth_token', $token, [
            'expires' => $expires,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $samesite
        ]);
    }
    
    /**
     * Verifica se o usuário está autenticado
     */
    public static function isLoggedIn() {
        if (isset($_COOKIE['auth_token'])) {
            return self::validateToken($_COOKIE['auth_token']);
        }
        return false;
    }
    
    /**
     * Logout do usuário
     */
    public static function logout() {
        setcookie('auth_token', '', time() - 3600, '/');
    }
    
    /**
     * Helper para encode base64url (JWT)
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Helper para decode base64url (JWT)
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
    
    /**
     * Sanitiza input para prevenir XSS
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Função de redirecionamento seguro
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}
?>