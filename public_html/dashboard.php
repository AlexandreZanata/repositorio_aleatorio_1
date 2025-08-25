<?php
// Define constante para prevenir acesso direto
define('SYSTEM_LOADED', true);

// Incluir arquivos necessários
require_once 'includes/connection.php';
require_once 'includes/functions.php';

// Inicializar banco de dados
$db = Database::getInstance();

// Verificar autenticação
$userData = Auth::isLoggedIn();
if (!$userData) {
    redirect('login.php');
}

// Incluir o menu (que já contém a estrutura base HTML)
include('menu.php');
?>