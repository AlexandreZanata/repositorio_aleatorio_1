<?php
// Define constante para prevenir acesso direto
define('SYSTEM_LOADED', true);

// Incluir arquivos necessários
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// Fazer logout
Auth::logout();

// Redirecionar para página de login
redirect('login.php');
?>