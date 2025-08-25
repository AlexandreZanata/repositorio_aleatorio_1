<?php
// Prevenir acesso direto e garantir que a configuração seja carregada
if (!defined('SYSTEM_LOADED')) {
    define('SYSTEM_LOADED', true);
}
// Carrega o config para ter a BASE_URL, essencial para os caminhos corretos
require_once dirname(__DIR__) . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diário de Bordo - Frotas Gov</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public_html/assets/css/themes/themes.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public_html/assets/css/diario-bordo.css">
</head>
<body class="diario-bordo-page">
    <header class="diario-header-simple">
        <h1>Frotas Gov</h1>
    </header>
    <div class="diario-bordo-main-content">