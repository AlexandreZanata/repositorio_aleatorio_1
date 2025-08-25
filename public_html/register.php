<?php
// Define constante para prevenir acesso direto
define('SYSTEM_LOADED', true);

// Incluir arquivos necessários
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// Inicializar banco de dados
$db = Database::getInstance();

// Verificar se usuário já está logado
$userData = Auth::isLoggedIn();
if ($userData) {
    // Já está logado, redirecionar para dashboard
    redirect('dashboard.php');
}

// Buscar departamentos do banco de dados
$departments = [];
try {
    $stmt = $db->query("SELECT id, name FROM departments WHERE is_active = 1 ORDER BY name", []);
    while ($row = $stmt->fetch()) {
        $departments[$row['id']] = $row['name'];
    }
} catch (Exception $e) {
    // Em caso de erro, log silencioso
    error_log("Erro ao buscar departamentos: " . $e->getMessage());
}

// Inicialização de variáveis
$error = '';
$success = '';
$name = $cpf = $email = '';
$department_id = 0;
$errors = []; // Array para armazenar múltiplos erros

// Processar cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitizar todas as entradas
        $name = isset($_POST['name']) ? Auth::sanitizeInput(trim($_POST['name'])) : '';
        $cpf = isset($_POST['cpf']) ? Auth::sanitizeInput(trim($_POST['cpf'])) : '';
        $email = isset($_POST['email']) ? Auth::sanitizeInput(trim($_POST['email'])) : '';
        $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;
        $password = isset($_POST['password']) ? $_POST['password'] : ''; // Senhas não devem ser sanitizadas antes do hash
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validação de dados
        validateRegistrationData($name, $cpf, $email, $department_id, $password, $confirm_password, $errors);
        
        // Se não houver erros, continuar com o processo de registro
        if (empty($errors)) {
            // Formatar o nome (primeira letra de cada palavra em maiúsculo)
            $name = formatName($name);
            
            // Formatar CPF (000.000.000-00)
            $cpf = formatCPF($cpf);
            
            // Gerar username único baseado no email
            $baseUsername = explode('@', $email)[0];
            $username = generateUniqueUsername($baseUsername, $db);
            
            // Gerar hash da senha com custo aumentado para maior segurança
            $passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
            
            // Verificar se e-mail já existe
            if (isEmailRegistered($email, $db)) {
                $errors[] = 'Este e-mail já está cadastrado.';
            } else {
                // Iniciar transação usando consulta SQL direta
                $db->query("START TRANSACTION");
                
                try {
                    // Inserir usuário
                    $db->query(
                        "INSERT INTO users (username, email, password_hash, name, cpf, department_id, access_level_id, is_active, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [$username, $email, $passwordHash, $name, $cpf, $department_id, 5, 0, 0]
                    );
                    
                    // Confirmar transação
                    $db->query("COMMIT");
                    
                    $success = 'Sua conta foi criada com sucesso! Contate o administrador para a ativação da sua conta.';
                    
                    // Limpar campos após sucesso
                    $name = $cpf = $email = '';
                    $department_id = 0;
                } catch (Exception $ex) {
                    // Se ocorrer qualquer erro, reverter a transação
                    $db->query("ROLLBACK");
                    throw $ex; // Relançar exceção para ser tratada pelo bloco catch externo
                }
            }
        }
    } catch (PDOException $e) {
        // Capturar erros específicos de banco de dados
        
        // Tratar erros específicos para dar feedback útil ao usuário
        if ($e->getCode() == '23000') { // Violação de restrição de integridade
            if (strpos($e->getMessage(), 'username') !== false) {
                $errors[] = 'Nome de usuário já existe. Tente novamente com outro e-mail.';
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                $errors[] = 'Este e-mail já está cadastrado.';
            } elseif (strpos($e->getMessage(), 'cpf') !== false) {
                $errors[] = 'Este CPF já está cadastrado.';
            } else {
                $errors[] = 'Erro ao processar cadastro. Verifique seus dados e tente novamente.';
            }
        } else {
            $errors[] = 'Erro ao processar cadastro. Por favor, tente novamente mais tarde.';
        }
        
        // Log detalhado do erro para análise
        error_log("Erro no cadastro: " . $e->getMessage());
    } catch (Exception $e) {
        // Capturar outros erros não específicos
        $errors[] = 'Erro inesperado. Por favor, tente novamente mais tarde.';
        error_log("Erro inesperado no cadastro: " . $e->getMessage());
    }
    
    // Consolidar erros em uma única mensagem
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    }
}

/**
 * Função para validar os dados de registro
 */
function validateRegistrationData($name, $cpf, $email, $department_id, $password, $confirm_password, &$errors) {
    // Verificar campos obrigatórios
    if (empty($name)) {
        $errors[] = 'Nome completo é obrigatório.';
    } elseif (str_word_count($name) < 2) {
        $errors[] = 'Informe nome e sobrenome.';
    }
    
    // Validar CPF (formato)
    if (empty($cpf)) {
        $errors[] = 'CPF é obrigatório.';
    } elseif (!preg_match('/^[0-9]{3}\.?[0-9]{3}\.?[0-9]{3}\-?[0-9]{2}$/', preg_replace('/[^0-9]/', '', $cpf))) {
        $errors[] = 'CPF inválido.';
    }
    
    // Validar e-mail
    if (empty($email)) {
        $errors[] = 'E-mail é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Formato de e-mail inválido.';
    }
    
    // Validar secretaria
    if ($department_id <= 0) {
        $errors[] = 'Selecione uma secretaria.';
    }
    
    // Validar senha
    if (empty($password)) {
        $errors[] = 'Senha é obrigatória.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula e um número.';
    }
    
    // Validar confirmação de senha
    if ($password !== $confirm_password) {
        $errors[] = 'As senhas não coincidem.';
    }
}

/**
 * Função para formatar o nome (primeira letra de cada palavra em maiúsculo)
 */
function formatName($name) {
    return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
}

/**
 * Função para formatar CPF
 */
function formatCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Limita a 11 dígitos
    $cpf = substr($cpf, 0, 11);
    
    // Formata no padrão 000.000.000-00 se tiver 11 dígitos
    if (strlen($cpf) === 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    
    return $cpf;
}

/**
 * Verifica se o email já está registrado
 */
function isEmailRegistered($email, $db) {
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE email = ?", [$email]);
    $result = $stmt->fetch();
    return $result['count'] > 0;
}

/**
 * Gera um nome de usuário único
 */
function generateUniqueUsername($baseUsername, $db) {
    $username = $baseUsername;
    $counter = 1;
    
    // Verificar se o username já existe e incrementar contador até encontrar um único
    while (true) {
        $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE username = ?", [$username]);
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            break; // Username é único
        }
        
        // Adicionar contador ao username base
        $username = $baseUsername . $counter;
        $counter++;
    }
    
    return $username;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Frotas Gov</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-main">
            <div class="login-header">
                <h1>Frotas Gov</h1>
                <p>Crie sua conta para acessar o sistema</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php" class="login-form" id="register-form">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>" placeholder="Digite seu nome completo">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <div class="input-wrapper">
                        <input type="text" id="cpf" name="cpf" required value="<?php echo htmlspecialchars($cpf); ?>" placeholder="000.000.000-00" maxlength="14">
                        <i class="fas fa-id-card input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>" placeholder="Digite seu e-mail">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="department_id">Secretaria</label>
                    <div class="input-wrapper">
                        <select id="department_id" name="department_id" required>
                            <option value="">Selecione uma secretaria</option>
                            <?php foreach ($departments as $id => $name): ?>
                                <option value="<?php echo $id; ?>" <?php echo ($department_id == $id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-building input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="Digite sua senha">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="password-hint">Mínimo 6 caracteres, com letras maiúsculas, minúsculas e números</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Senha</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirme sua senha">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i> Criar Conta
                    </button>
                </div>
                
                <div class="create-account">
                    <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
                </div>
            </form>
            
            <div class="login-footer">
                <p>&copy; <?php echo date('Y'); ?> Frotas Gov - Todos os direitos reservados</p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/create_account.js"></script>
</body>
</html>