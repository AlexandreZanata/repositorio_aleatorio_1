<?php
// Arquivo de configuração com credenciais do banco de dados
// Este arquivo deve estar fora da pasta pública por segurança

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'corporate_system');

// Chave secreta para tokens JWT
define('JWT_SECRET', 'chave_muito_secreta_e_longa_para_seguranca_maxima_2025');

// Tempo de expiração do token (em segundos) - 30 dias
define('TOKEN_EXPIRY', 60 * 60 * 24 * 30);

// URL base do sistema
define('BASE_URL', 'http://localhost/crud');
?>