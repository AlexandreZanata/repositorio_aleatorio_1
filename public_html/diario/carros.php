<?php
// Define constante para prevenir acesso direto
define('SYSTEM_LOADED', true);
session_start();

// Inclui o cabeçalho
require_once '../includes/header.php'; 

// Limpa sessão de corridas anteriores ao chegar no passo 1
unset($_SESSION['diario_bordo_vehicle_id']);
unset($_SESSION['diario_bordo_record_id']);
$_SESSION['diario_bordo_step'] = 1;
?>

<link rel="stylesheet" href="../assets/css/diario-bordo.css">

<main class="main-content">
    <div class="diario-bordo-container">
        <div class="diario-bordo-header">
            <div class="icon-container">
                <div data-icon="car"></div>
            </div>
            <h2>Diário de Bordo - Etapa 1 de 4</h2>
            <h1>Escolha do Veículo</h1>
            <p>Digite o prefixo do veículo para iniciar.</p>
        </div>

        <form id="vehicle-form" class="diario-bordo-form" method="POST" action="../api/process_diario.php">
            <input type="hidden" name="action" value="select_vehicle">
            
            <div class="form-group">
                <label for="prefixo">Prefixo do Veículo</label>
                <input type="text" id="prefixo" name="prefixo" class="form-control" placeholder="Ex: V-123, A-01, 23" required>
                <div id="prefix-suggestions"></div>
            </div>

            <div class="vehicle-info-box" id="vehicle-info" style="display: none;">
                <div class="form-group">
                    <label>Placa</label>
                    <input type="text" id="placa" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Nome do Veículo</label>
                    <input type="text" id="nome-veiculo" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Secretaria</label>
                    <input type="text" id="secretaria" class="form-control" readonly>
                </div>
                 <input type="hidden" id="vehicle_id" name="vehicle_id">
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="submit-btn" disabled>
                Avançar para o Checklist
            </button>
        </form>
    </div>
</main>

<script src="../assets/js/diario-bordo.js"></script>

<?php
// Inclui o rodapé
require_once '../includes/footer.php'; 
?>