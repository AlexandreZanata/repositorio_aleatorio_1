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

// Função para gerar avatar padrão caso não tenha foto
function getAvatar($userData) {
    if (isset($userData['avatar_url']) && !empty($userData['avatar_url'])) {
        return htmlspecialchars($userData['avatar_url']);
    }
    // Retorna null para usar o avatar padrão em SVG
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
        <!-- Header (sempre visível) -->
        <header class="header">
            <div class="logo">
                <!-- Tornando o logo clicável em dispositivos móveis para abrir a sidebar -->
                <?php if ($isMobile): ?>
                <button id="logoButton" style="background:none; border:none; padding:0; cursor:pointer; display:flex;">
                    <div data-icon="car"></div>
                    <h1>Frotas Gov</h1>
                </button>
                <?php else: ?>
                <div data-icon="car"></div>
                <h1>Frotas Gov</h1>
                <?php endif; ?>
            </div>
            
            <div class="actions">
                <!-- Botão de menu mobile -->
                <button id="mobileMenuToggle" class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <!-- Ações visíveis no desktop -->
                <button id="darkModeToggle" class="action-btn" title="Alternar tema claro/escuro">
                    <div id="darkModeIcon"></div>
                </button>
                
                <button class="action-btn" title="Notificações">
                    <div data-icon="bell"></div>
                    <span class="notification-badge" data-notifications="3">3</span>
                </button>
                
                <!-- Menu do usuário -->
                <div id="userMenu" class="user-menu">
                    <button id="userMenuToggle" class="user-menu-toggle">
                        <?php if ($avatarUrl = getAvatar($userData)): ?>
                            <img src="<?php echo $avatarUrl; ?>" alt="Perfil">
                        <?php else: ?>
                            <div class="default-avatar">
                                <div data-icon="user"></div>
                            </div>
                        <?php endif; ?>
                    </button>
                    
                    <div class="user-menu-dropdown">
                        <div class="user-info">
                            <?php if ($avatarUrl = getAvatar($userData)): ?>
                                <img src="<?php echo $avatarUrl; ?>" alt="Perfil">
                            <?php else: ?>
                                <div class="default-avatar" style="width: 60px; height: 60px;">
                                    <div data-icon="user"></div>
                                </div>
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($userData['name']); ?></h3>
                            <p><?php echo htmlspecialchars($userData['secretaria']); ?></p>
                        </div>
                        
                        <a href="profile.php" class="user-menu-item">
                            <div data-icon="user"></div>
                            Meu Perfil
                        </a>
                        
                        <a href="settings.php" class="user-menu-item">
                            <div data-icon="settings"></div>
                            Configurações
                        </a>
                        
                        <a href="logout.php" class="user-menu-item logout-btn">
                            <div data-icon="logout"></div>
                            Sair
                        </a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Sidebar para desktop -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <div class="nav-title">Menu Principal</div>
                
                <ul>
                    <li class="nav-item">
                        <a href="diario/carros.php" class="nav-link">
                            <div data-icon="diary"></div>
                            <span>Diário de Bordo</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="contato-gestor.php" class="nav-link">
                            <div data-icon="contact"></div>
                            <span>Contato com o Gestor</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="abastecimento.php" class="nav-link">
                            <div data-icon="fuel"></div>
                            <span>Abastecimento</span>
                        </a>
                    </li>
                </ul>
                
                <div class="nav-title">Gerenciamento</div>
                
                <ul>
                    <li class="nav-item">
                        <a href="profile.php" class="nav-link">
                            <div data-icon="user"></div>
                            <span>Meu Perfil</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link">
                            <div data-icon="settings"></div>
                            <span>Configurações</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">
                            <div data-icon="logout"></div>
                            <span>Sair</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Drawer menu para mobile -->
        <div id="drawerBackdrop" class="drawer-backdrop"></div>
        <div id="drawer" class="drawer">
            <div class="drawer-header">
                <div class="drawer-logo">
                    <div data-icon="car"></div>
                    <h2>Frotas Gov</h2>
                </div>
                
                <button id="drawerClose" class="drawer-close">
                    <div data-icon="close"></div>
                </button>
            </div>
            
            <div class="drawer-user">
                <?php if ($avatarUrl = getAvatar($userData)): ?>
                    <img src="<?php echo $avatarUrl; ?>" alt="Perfil">
                <?php else: ?>
                    <div class="default-avatar" style="width: 70px; height: 70px;">
                        <div data-icon="user"></div>
                    </div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($userData['name']); ?></h3>
                <p><?php echo htmlspecialchars($userData['secretaria']); ?></p>
            </div>
            
            <div class="drawer-nav">
                <div class="drawer-nav-item">
                    <a href="diario-bordo.php" class="drawer-nav-link">
                        <div data-icon="diary"></div>
                        <span>Diário de Bordo</span>
                    </a>
                </div>
                
                <div class="drawer-nav-item">
                    <a href="contato-gestor.php" class="drawer-nav-link">
                        <div data-icon="contact"></div>
                        <span>Contato com o Gestor</span>
                    </a>
                </div>
                
                <div class="drawer-nav-item">
                    <a href="abastecimento.php" class="drawer-nav-link">
                        <div data-icon="fuel"></div>
                        <span>Abastecimento</span>
                    </a>
                </div>
                
                <div class="drawer-nav-item">
                    <a href="profile.php" class="drawer-nav-link">
                        <div data-icon="user"></div>
                        <span>Meu Perfil</span>
                    </a>
                </div>
                
                <div class="drawer-nav-item">
                    <a href="settings.php" class="drawer-nav-link">
                        <div data-icon="settings"></div>
                        <span>Configurações</span>
                    </a>
                </div>
            </div>
            
            <div class="drawer-footer">
                <a href="logout.php" class="drawer-nav-link logout-btn">
                    <div data-icon="logout"></div>
                    <span>Sair</span>
                </a>
            </div>
        </div>
        
        <!-- Conteúdo principal -->
        <main class="main-content">
            <?php if ($isMobile): ?>
                <!-- Versão mobile: mostrar apenas os 3 botões principais -->
                <div class="app-buttons-grid">
                    <a href="diario-bordo.php" class="app-button">
                        <div class="app-button-icon">
                            <div data-icon="diary"></div>
                        </div>
                        <span class="app-button-label">Diário</span>
                    </a>
                    
                    <a href="contato-gestor.php" class="app-button">
                        <div class="app-button-icon">
                            <div data-icon="contact"></div>
                        </div>
                        <span class="app-button-label">Contato</span>
                    </a>
                    
                    <a href="abastecimento.php" class="app-button">
                        <div class="app-button-icon">
                            <div data-icon="fuel"></div>
                        </div>
                        <span class="app-button-label">Abastecimento</span>
                    </a>
                </div>
            <?php else: ?>
                <!-- Versão desktop: mostrar dashboard com gráficos -->
                <div class="dashboard-grid">
                    <div class="stats-card">
                        <div class="stats-header">
                            <span class="stats-title">Total de Corridas</span>
                            <div class="stats-icon">
                                <div data-icon="car"></div>
                            </div>
                        </div>
                        <div class="stats-value">1,243</div>
                        <div class="stats-description">Corridas realizadas</div>
                        <div class="stats-trend up">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            <span>8.2% este mês</span>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="stats-header">
                            <span class="stats-title">Consumo Total</span>
                            <div class="stats-icon">
                                <div data-icon="fuel"></div>
                            </div>
                        </div>
                        <div class="stats-value">3,892L</div>
                        <div class="stats-description">Combustível utilizado</div>
                        <div class="stats-trend down">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            <span>3.1% este mês</span>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="stats-header">
                            <span class="stats-title">Quilômetros Rodados</span>
                            <div class="stats-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                        </div>
                        <div class="stats-value">45,672</div>
                        <div class="stats-description">Total percorrido</div>
                        <div class="stats-trend up">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            <span>12.4% este mês</span>
                        </div>
                    </div>
                </div>
                
                <div class="chart-container" id="tripsContainer">
                    <div class="chart-header">
                        <h3 class="chart-title">Corridas Realizadas</h3>
                        <div class="chart-actions">
                            <button data-chart="trips" data-period="week">Semana</button>
                            <button data-chart="trips" data-period="month">Mês</button>
                            <button class="active" data-chart="trips" data-period="year">Ano</button>
                        </div>
                    </div>
                    <div class="chart">
                        <canvas id="tripsChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-container" id="fuelContainer">
                    <div class="chart-header">
                        <h3 class="chart-title">Consumo de Combustível</h3>
                        <div class="chart-actions">
                            <button data-chart="fuel" data-period="week">Semana</button>
                            <button data-chart="fuel" data-period="month">Mês</button>
                            <button class="active" data-chart="fuel" data-period="year">Ano</button>
                        </div>
                    </div>
                    <div class="chart">
                        <canvas id="fuelChart"></canvas>
                    </div>
                </div>
                
                <div class="data-table-container">
                    <div class="chart-header">
                        <h3 class="chart-title">Últimas Corridas</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Motorista</th>
                                <th>Destino</th>
                                <th>KM</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>21/08/2025</td>
                                <td>Carlos Silva</td>
                                <td>Prefeitura Municipal</td>
                                <td>12.5 km</td>
                                <td>Concluída</td>
                            </tr>
                            <tr>
                                <td>20/08/2025</td>
                                <td>Ana Pereira</td>
                                <td>Secretaria de Educação</td>
                                <td>8.2 km</td>
                                <td>Concluída</td>
                            </tr>
                            <tr>
                                <td>19/08/2025</td>
                                <td>Roberto Alves</td>
                                <td>Hospital Municipal</td>
                                <td>15.8 km</td>
                                <td>Concluída</td>
                            </tr>
                            <tr>
                                <td>18/08/2025</td>
                                <td>Fernanda Costa</td>
                                <td>Centro Administrativo</td>
                                <td>5.3 km</td>
                                <td>Concluída</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
        
        <!-- Botão de ação flutuante para mobile -->
        <a href="#" class="mobile-fab">
            <div data-icon="plus"></div>
        </a>
        
        <!-- Navegação inferior para mobile -->
        <nav class="mobile-bottom-nav">
            <a href="diario-bordo.php" class="mobile-bottom-nav-item">
                <div data-icon="diary"></div>
                <span>Diário</span>
            </a>
            
            <a href="contato-gestor.php" class="mobile-bottom-nav-item">
                <div data-icon="contact"></div>
                <span>Contato</span>
            </a>
            
            <a href="abastecimento.php" class="mobile-bottom-nav-item">
                <div data-icon="fuel"></div>
                <span>Abastecer</span>
            </a>
        </nav>
        
        <!-- Footer para desktop -->
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Frotas Gov - Sistema de Gestão de Frotas</p>
        </footer>
    </div>
    
    <!-- Scripts -->
  <script src="<?php echo BASE_URL; ?>/public_html/assets/js/icons/icons.js"></script>
    <script src="<?php echo BASE_URL; ?>/public_html/assets/js/theme.js"></script>
    <script src="<?php echo BASE_URL; ?>/public_html/assets/js/menu.js"></script>
    <script src="<?php echo BASE_URL; ?>/public_html/assets/js/dashboard.js"></script>
</body>
</html>