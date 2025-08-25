<?php
define('SYSTEM_LOADED', true);
session_start();

require_once '../includes/connection.php';
require_once '../includes/functions.php';

$db = Database::getInstance();
$userData = Auth::isLoggedIn();
if (!$userData) redirect('../login.php');

if (!isset($_SESSION['diario_bordo_vehicle_id'])) {
    redirect('diario_bordo.php');
}
$_SESSION['diario_bordo_step'] = 3;
$vehicle_id = $_SESSION['diario_bordo_vehicle_id'];

// Buscar o último KM final do veículo
$stmt = $db->query("SELECT final_km FROM records WHERE vehicle_id = ? ORDER BY id DESC LIMIT 1", [$vehicle_id]);
$last_record = $stmt->fetch();
$last_km = $last_record['final_km'] ?? 0;

include('../menu.php');
?>

<link rel="stylesheet" href="../assets/css/diario-bordo.css">

<main class="main-content">
    <div class="diario-bordo-container">
        <div class="diario-bordo-header">
            <div class="icon-container">
                <div data-icon="play-circle"></div>
            </div>
            <h2>Diário de Bordo - Etapa 3 de 4</h2>
            <h1>Iniciar Corrida</h1>
            <p>Preencha os dados abaixo para começar sua rota.</p>
        </div>

        <form id="start-trip-form" class="diario-bordo-form" method="POST" action="api/process_diario.php">
            <input type="hidden" name="action" value="start_trip">

            <div class="form-group">
                <label for="initial_km">Km Atual</label>
                <input type="number" step="0.1" id="initial_km" name="initial_km" class="form-control" value="<?php echo htmlspecialchars($last_km); ?>" required>
                <small class="form-text">O último KM registrado foi <?php echo htmlspecialchars($last_km); ?>. Corrija se necessário.</small>
            </div>

            <div class="form-group">
                <label for="destination">Destino</label>
                <textarea id="destination" name="destination" class="form-control" rows="3" placeholder="Descreva o destino ou a rota da corrida." required></textarea>
            </div>

            <div class="form-actions">
                 <a href="diario_bordo_2_checklist.php" class="btn btn-secondary">
                    <div data-icon="arrow-left"></div>
                    Voltar ao Checklist
                </a>
                <button type="submit" class="btn btn-primary">
                    Iniciar Corrida
                    <div data-icon="flag"></div>
                </button>
            </div>
        </form>
    </div>
</main>

<script src="../assets/js/diario-bordo.js"></script>

</body>
</html>