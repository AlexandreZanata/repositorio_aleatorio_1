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

// Redirecionar para página de login
redirect('login.php');
?>