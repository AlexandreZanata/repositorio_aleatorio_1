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

// Processar login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error = 'Preencha todos os campos.';
    } else {
        // Tentar login
        $result = Auth::login($email, $password);
        
        if ($result) {
            // Login bem-sucedido
            redirect('dashboard.php');
        } else {
            $error = 'E-mail ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Frotas Gov</title>
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
                <p>Acesse sua conta para continuar</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" class="login-form">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" required autocomplete="email" placeholder="Digite seu e-mail">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="Digite sua senha">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="forgot-password">
                    <a href="#">Esqueceu a senha?</a>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </div>
                
                <div class="create-account">
                    <p>Não tem uma conta? <a href="register.php">Criar conta</a></p>
                </div>
            </form>
            
            <div class="login-footer">
                <p>&copy; <?php echo date('Y'); ?> Frotas Gov - Todos os direitos reservados</p>
            </div>
        </div>
    </div>
    
    <script>
    function togglePassword(id) {
        const passwordField = document.getElementById(id);
        const icon = passwordField.nextElementSibling.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>