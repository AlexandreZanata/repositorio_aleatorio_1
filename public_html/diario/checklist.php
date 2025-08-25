<?php
define('SYSTEM_LOADED', true);
session_start();

require_once '../includes/connection.php';
require_once '../includes/functions.php';

$db = Database::getInstance();
$userData = Auth::isLoggedIn();
if (!$userData) redirect('login.php');

if (!isset($_SESSION['diario_bordo_vehicle_id'])) {
    redirect('diario_bordo.php');
}
$_SESSION['diario_bordo_step'] = 2;
$vehicle_id = $_SESSION['diario_bordo_vehicle_id'];

// 1. Verificar se o veículo está em uso
$stmt = $db->query("SELECT status FROM vehicles WHERE id = ?", [$vehicle_id]);
$vehicle = $stmt->fetch();

if ($vehicle && $vehicle['status'] === 'in use') {
    $stmt = $db->query(
        "SELECT r.start_date, r.start_time, u.name as user_name, u.phone as user_phone
         FROM records r
         JOIN users u ON r.user_id = u.id
         WHERE r.vehicle_id = ? AND r.end_time IS NULL
         ORDER BY r.id DESC LIMIT 1",
        [$vehicle_id]
    );
    $current_trip = $stmt->fetch();
}

// 2. Buscar itens do checklist e o último status deles
$stmt_items = $db->query(
    "SELECT
        ci.id,
        ci.name,
        vcs.status,
        vcs.notes
    FROM checklist_items ci
    LEFT JOIN vehicle_checklist_status vcs ON ci.id = vcs.checklist_item_id AND vcs.vehicle_id = ?
    WHERE ci.is_active = 1
    ORDER BY ci.id",
    [$vehicle_id]
);
$checklist_items = $stmt_items->fetchAll();


include('menu.php');
?>

<link rel="stylesheet" href="../assets/css/diario-bordo.css">

<main class="main-content">
    <div class="diario-bordo-container">
        <div class="diario-bordo-header">
            <div class="icon-container">
                <div data-icon="checklist"></div>
            </div>
            <h2>Diário de Bordo - Etapa 2 de 4</h2>
            <h1>Checklist do Veículo</h1>
        </div>

        <?php if (isset($current_trip)): ?>
            <div class="alert alert-danger">
                <h4>Veículo em Uso!</h4>
                <p>Este veículo já está em uma corrida iniciada por <strong><?php echo htmlspecialchars($current_trip['user_name']); ?></strong> em <?php echo date('d/m/Y', strtotime($current_trip['start_date'])) . ' às ' . date('H:i', strtotime($current_trip['start_time'])); ?>.</p>
                <p>Se isto for um engano, por favor, contate o gestor da sua secretaria para liberar o veículo.</p>
                <p>Contato do motorista atual: <?php echo htmlspecialchars($current_trip['user_phone'] ?? 'Não informado'); ?></p>
                <a href="diario_bordo.php" class="btn btn-secondary">Voltar</a>
            </div>
        <?php else: ?>
            <p>Verifique os itens abaixo. O estado atual foi preenchido pelo último motorista.</p>
            <form id="checklist-form" class="diario-bordo-form" method="POST" action="../api/process_diario.php">
                <input type="hidden" name="action" value="process_checklist">

                <div class="checklist-container">
                    <?php foreach ($checklist_items as $item): ?>
                        <div class="checklist-item">
                            <label class="checklist-label"><?php echo htmlspecialchars($item['name']); ?></label>
                            <div class="checklist-options">
                                <label class="radio-option ok <?php echo ($item['status'] ?? 'ok') === 'ok' ? 'checked' : ''; ?>">
                                    <input type="radio" name="checklist[<?php echo $item['id']; ?>][status]" value="ok" <?php echo ($item['status'] ?? 'ok') === 'ok' ? 'checked' : ''; ?>> OK
                                </label>
                                <label class="radio-option attention <?php echo $item['status'] === 'attention' ? 'checked' : ''; ?>">
                                    <input type="radio" name="checklist[<?php echo $item['id']; ?>][status]" value="attention" <?php echo $item['status'] === 'attention' ? 'checked' : ''; ?>> Atenção
                                </label>
                                <label class="radio-option problem <?php echo $item['status'] === 'problem' ? 'checked' : ''; ?>">
                                    <input type="radio" name="checklist[<?php echo $item['id']; ?>][status]" value="problem" <?php echo $item['status'] === 'problem' ? 'checked' : ''; ?>> Problema
                                </label>
                            </div>
                            <div class="notes-container" style="<?php echo $item['status'] !== 'problem' ? 'display: none;' : ''; ?>">
                                <textarea name="checklist[<?php echo $item['id']; ?>][notes]" class="form-control" placeholder="Descreva o problema aqui..."><?php echo htmlspecialchars($item['notes'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-actions">
                     <a href="diario_bordo.php" class="btn btn-secondary">
                        <div data-icon="arrow-left"></div>
                        Voltar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Assinar e Iniciar Corrida
                        <div data-icon="arrow-right"></div>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>

<script src="../assets/js/diario-bordo.js"></script>

</body>
</html>