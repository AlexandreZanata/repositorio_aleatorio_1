<?php
define('SYSTEM_LOADED', true);
session_start();

require_once '../includes/connection.php';
require_once '../includes/functions.php';

$db = Database::getInstance();
$userData = Auth::isLoggedIn();

if (!$userData) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acesso negado.']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;

switch ($action) {
    case 'search_vehicle':
        $term = $_GET['prefix'] ?? '';
        // --- CORREÇÃO AQUI ---
        // Usa 'department_id' do token, que é o ID numérico.
        $department_id = $userData['department_id'];

        if (strlen($term) < 1 || !$department_id) {
            echo json_encode([]);
            exit;
        }

        $stmt = $db->query(
            "SELECT v.id, v.vehicle as prefix, v.license_plate, v.type as name, d.name as department_name
             FROM vehicles v
             JOIN departments d ON v.departments_id = d.id
             WHERE (v.vehicle LIKE ? OR v.license_plate LIKE ?) AND v.departments_id = ? 
             LIMIT 10",
            ["%$term%", "%$term%", $department_id]
        );
        
        $vehicles = $stmt->fetchAll();
        header('Content-Type: application/json');
        echo json_encode($vehicles);
        break;

    case 'select_vehicle':
        $vehicle_id = $_POST['vehicle_id'] ?? null;
        if ($vehicle_id) {
            $_SESSION['diario_bordo_vehicle_id'] = $vehicle_id;
            // --- CORREÇÃO DE REDIRECIONAMENTO ---
            header('Location: ../diario/checklist.php');
            exit();
        } else {
            header('Location: ../diario/carros.php?error=veiculo_invalido');
            exit();
        }
        break;

    case 'process_checklist':
        $vehicle_id = $_SESSION['diario_bordo_vehicle_id'];
        $checklists = $_POST['checklist'];
        $user_id = $userData['sub'];

        $db->getConnection()->beginTransaction();
        try {
            foreach ($checklists as $item_id => $data) {
                $status = $data['status'];
                $notes = ($status === 'problem' && !empty($data['notes'])) ? trim($data['notes']) : null;
                $db->query(
                    "INSERT INTO vehicle_checklist_status (vehicle_id, checklist_item_id, status, notes, updated_by_user_id)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE status = VALUES(status), notes = VALUES(notes), updated_by_user_id = VALUES(updated_by_user_id)",
                    [$vehicle_id, $item_id, $status, $notes, $user_id]
                );
            }
            $db->getConnection()->commit();
            // --- CORREÇÃO DE REDIRECIONAMENTO ---
            header('Location: ../diario/diario.php');
            exit();
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            header('Location: ../diario/checklist.php?error=db_error');
            exit();
        }
        break;

     case 'start_trip':
        $vehicle_id = $_SESSION['diario_bordo_vehicle_id'];
        $user_id = $userData['sub'];
        $initial_km = $_POST['initial_km'];
        $destination = $_POST['destination'];

        $db->getConnection()->beginTransaction();
        try {
            $stmt = $db->query(
                "INSERT INTO records (user_id, vehicle_id, initial_km, destination, start_date, start_time)
                 VALUES (?, ?, ?, ?, CURDATE(), CURTIME())",
                [$user_id, $vehicle_id, $initial_km, $destination]
            );
            $record_id = $db->getConnection()->lastInsertId();

            $stmt_status = $db->query("SELECT * FROM vehicle_checklist_status WHERE vehicle_id = ?", [$vehicle_id]);
            while($status_row = $stmt_status->fetch()){
                 $db->query(
                    "INSERT INTO trip_checklists (record_id, checklist_item_id, status, notes) VALUES (?, ?, ?, ?)",
                    [$record_id, $status_row['checklist_item_id'], $status_row['status'], $status_row['notes']]
                );
            }
            $db->query("UPDATE vehicles SET status = 'in use' WHERE id = ?", [$vehicle_id]);
            $db->getConnection()->commit();

            $_SESSION['diario_bordo_record_id'] = $record_id;
            // --- CORREÇÃO DE REDIRECIONAMENTO ---
            header('Location: ../diario/finalizar.php');
            exit();
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            header('Location: ../diario/diario.php?error=start_trip_failed');
            exit();
        }
        break;

    case 'end_trip':
        $record_id = $_SESSION['diario_bordo_record_id'];
        $vehicle_id = $_SESSION['diario_bordo_vehicle_id'];
        $final_km = $_POST['final_km'];
        $stop_point = $_POST['stop_point'];

        $db->getConnection()->beginTransaction();
        try {
            $db->query(
                "UPDATE records SET final_km = ?, stop_point = ?, end_date = CURDATE(), end_time = CURTIME() WHERE id = ?",
                [$final_km, $stop_point, $record_id]
            );
            $db->query("UPDATE vehicles SET status = 'active' WHERE id = ?", [$vehicle_id]);
            // (Lógica de abastecimento viria aqui)
            $db->getConnection()->commit();

            unset($_SESSION['diario_bordo_record_id']);
            unset($_SESSION['diario_bordo_vehicle_id']);

            header('Location: ../dashboard.php?message=trip_ended');
            exit();
        } catch (Exception $e) {
            $db->getConnection()->rollBack();
            header('Location: ../diario/finalizar.php?error=end_trip_failed');
            exit();
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Ação inválida.']);
        break;
}