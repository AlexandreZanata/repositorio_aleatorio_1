<?php
define('SYSTEM_LOADED', true);
session_start(); // Inicia a sessão para persistência

require_once '../includes/connection.php';
require_once '../includes/functions.php';

$db = Database::getInstance();
$userData = Auth::isLoggedIn();
if (!$userData) {
    redirect('../login.php');
}

// Lógica de navegação e persistência
$step = $_SESSION['diario_bordo_step'] ?? 1;
$vehicle_id = $_SESSION['diario_bordo_vehicle_id'] ?? null;
$record_id = $_SESSION['diario_bordo_record_id'] ?? null;

// Verifica se há uma corrida em aberto para este usuário
if (!$record_id) {
    $stmt = $db->query("SELECT id, vehicle_id FROM records WHERE user_id = ? AND end_time IS NULL ORDER BY id DESC LIMIT 1", [$userData['id']]);
    $open_trip = $stmt->fetch();
    if ($open_trip) {
        $_SESSION['diario_bordo_step'] = 4;
        $_SESSION['diario_bordo_record_id'] = $open_trip['id'];
        $_SESSION['diario_bordo_vehicle_id'] = $open_trip['vehicle_id'];
        $step = 4;
    }
}

// Redireciona para a etapa correta
switch ($step) {
    case 2:
        redirect('checklist.php');
        break;
    case 3:
        redirect('diario.php');
        break;
    case 4:
        redirect('finalizar.php');
        break;
    case 1:
    default:
        redirect('carros.php');
        break;
}