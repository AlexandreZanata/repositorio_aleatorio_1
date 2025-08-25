<?php
define('SYSTEM_LOADED', true);
session_start();

require_once '../includes/connection.php';
require_once '../includes/functions.php';

$db = Database::getInstance();
$userData = Auth::isLoggedIn();
if (!$userData) redirect('../login.php');

if (!isset($_SESSION['diario_bordo_record_id'])) {
    redirect('diario_bordo.php');
}
$_SESSION['diario_bordo_step'] = 4;
$record_id = $_SESSION['diario_bordo_record_id'];

// Buscar dados da corrida atual
$stmt = $db->query("SELECT initial_km FROM records WHERE id = ?", [$record_id]);
$current_record = $stmt->fetch();
$initial_km = $current_record['initial_km'] ?? 0;

// Buscar postos de gasolina
$stations_stmt = $db->query("SELECT id, name FROM gas_stations WHERE is_active = 1 ORDER BY name");
$gas_stations = $stations_stmt->fetchAll();

include('../menu.php');
?>

<link rel="stylesheet" href="../assets/css/diario-bordo.css">

<main class="main-content">
    <div class="diario-bordo-container">
        <div class="diario-bordo-header">
            <div class="icon-container">
                <div data-icon="stop-circle"></div>
            </div>
            <h2>Diário de Bordo - Etapa 4 de 4</h2>
            <h1>Finalizar Corrida</h1>
            <p>Corrida em andamento. Preencha para finalizar.</p>
        </div>

        <form id="end-trip-form" class="diario-bordo-form" method="POST" action="api/process_diario.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="end_trip">

            <div class="form-group">
                <label for="final_km">Km Final</label>
                <input type="number" step="0.1" id="final_km" name="final_km" class="form-control" placeholder="KM ao finalizar" required min="<?php echo $initial_km; ?>">
                 <small class="form-text">O KM inicial foi <?php echo htmlspecialchars($initial_km); ?>.</small>
            </div>

            <div class="form-group">
                <label for="stop_point">Ponto de Parada</label>
                <input type="text" id="stop_point" name="stop_point" class="form-control" placeholder="Ex: Pátio da Prefeitura" required>
            </div>

            <div class="refueling-section">
                <h3>Registrar Abastecimento (Opcional)</h3>
                <div class="tabs">
                    <button type="button" class="tab-link active" data-tab="credenciado">Posto Credenciado</button>
                    <button type="button" class="tab-link" data-tab="manual">Abastecimento Manual</button>
                </div>

                <div id="credenciado" class="tab-content active">
                    <div class="form-group">
                        <label for="refuel_km_cred">Km de Abastecimento</label>
                        <input type="number" step="0.1" name="refuel_km_cred" class="form-control">
                    </div>
                     <div class="form-group">
                        <label for="refuel_liters_cred">Litros Abastecidos</label>
                        <input type="number" step="0.01" name="refuel_liters_cred" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="station_id">Posto de Gasolina</label>
                        <select name="station_id" class="form-control">
                            <option value="">Selecione o Posto</option>
                            <?php foreach($gas_stations as $station): ?>
                                <option value="<?php echo $station['id']; ?>"><?php echo htmlspecialchars($station['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    </div>

                <div id="manual" class="tab-content">
                    <div class="form-group">
                        <label for="refuel_km_man">Km de Abastecimento</label>
                        <input type="number" step="0.1" name="refuel_km_man" class="form-control">
                    </div>
                     <div class="form-group">
                        <label for="refuel_liters_man">Litros Abastecidos</label>
                        <input type="number" step="0.01" name="refuel_liters_man" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="refuel_station_man">Nome do Posto</label>
                        <input type="text" name="refuel_station_man" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="refuel_value_man">Valor Total Abastecido</label>
                        <input type="number" step="0.01" name="refuel_value_man" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label for="invoice">Nota Fiscal (Opcional)</label>
                    <input type="file" name="invoice" class="form-control">
                </div>
            </div>


            <button type="submit" class="btn btn-danger btn-block">
                Finalizar Corrida
                <div data-icon="check-circle"></div>
            </button>
        </form>
    </div>
</main>

<script src="../assets/js/diario-bordo.js"></script>
</body>
</html>