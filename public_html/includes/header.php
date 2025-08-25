<?php
// Define constante para prevenir acesso direto
if (!defined('SYSTEM_LOADED')) {
    define('SYSTEM_LOADED', true);
}

// Incluir arquivos necessários (se ainda não incluídos)
if (!class_exists('Database')) {
    require_once 'connection.php';
}
if (!class_exists('Auth')) {
    require_once 'functions.php';
}


// Inicializar banco de dados
$db = Database::getInstance();

// Verificar autenticação
$userData = Auth::isLoggedIn();
if (!$userData) {
    redirect('login.php');
}

// Função para gerar avatar padrão caso não tenha foto
function getAvatar($userData) {
    if (isset($userData['avatar_url']) && !empty($userData['avatar_url'])) {
        return htmlspecialchars($userData['avatar_url']);
    }
    return null;
}

// Verificar se é dispositivo móvel (simplificado)
$isMobile = isset($_SERVER['HTTP_USER_AGENT']) && 
            (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false || 
             strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frotas Gov - Sistema de Gestão de Frotas</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public_html/assets/css/themes/themes.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public_html/assets/css/menu.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public_html/assets/css/dashboard.css">
    
</head>
<body>
    <div class="app-container">
        <header class="header">
            <div class="logo">
                <div data-icon="car"></div>
                <h1>Frotas Gov</h1>
            </div>
            
            <div class="actions">
                <button id="mobileMenuToggle" class="mobile-menu-toggle">
                    <span></span><span></span><span></span>
                </button>
                <button id="darkModeToggle" class="action-btn" title="Alternar tema">
                    <div id="darkModeIcon"></div>
                </button>
                <button class="action-btn" title="Notificações">
                    <div data-icon="bell"></div>
                    <span class="notification-badge" data-notifications="3">3</span>
                </button>
                
                <div id="userMenu" class="user-menu">
                    <button id="userMenuToggle" class="user-menu-toggle">
                        <?php if ($avatarUrl = getAvatar($userData)): ?>
                            <img src="<?php echo $avatarUrl; ?>" alt="Perfil">
                        <?php else: ?>
                            <div class="default-avatar"><div data-icon="user"></div></div>
                        <?php endif; ?>
                    </button>
                    
                    <div class="user-menu-dropdown">
                        <div class="user-info">
                             <?php if ($avatarUrl = getAvatar($userData)): ?>
                                <img src="<?php echo $avatarUrl; ?>" alt="Perfil">
                            <?php else: ?>
                                <div class="default-avatar" style="width: 60px; height: 60px;"><div data-icon="user"></div></div>
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($userData['name']); ?></h3>
                            <p><?php echo htmlspecialchars($userData['secretaria']); ?></p>
                        </div>
                        <a href="profile.php" class="user-menu-item"><div data-icon="user"></div>Meu Perfil</a>
                        <a href="settings.php" class="user-menu-item"><div data-icon="settings"></div>Configurações</a>
                        <a href="logout.php" class="user-menu-item logout-btn"><div data-icon="logout"></div>Sair</a>
                    </div>
                </div>
            </div>
        </header>
        
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <div class="nav-title">Menu Principal</div>
                <ul>
                    <li class="nav-item"><a href="<?php echo BASE_URL; ?>/public_html/dashboard.php" class="nav-link"><div data-icon="home"></div><span>Dashboard</span></a></li>
                    <li class="nav-item"><a href="<?php echo BASE_URL; ?>/public_html/diario/carros.php" class="nav-link"><div data-icon="diary"></div><span>Diário de Bordo</span></a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><div data-icon="contact"></div><span>Contato</span></a></li>
                    <li class="nav-item"><a href="#" class="nav-link"><div data-icon="fuel"></div><span>Abastecimento</span></a></li>
                </ul>
            </nav>
        </aside>