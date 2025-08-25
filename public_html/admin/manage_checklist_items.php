<?php
define('SYSTEM_LOADED', true);
session_start();

require_once '../includes/connection.php';
require_once '../includes/functions.php';

$db = Database::getInstance();
$userData = Auth::isLoggedIn();
if (!$userData || $userData['access_level_id'] > 2) {
    redirect('../login.php');
}

$message = '';
$error = '';

// Processar formulário de adição/edição de item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Adicionar novo item
        if ($_POST['action'] === 'add') {
            $name = trim($_POST['name']);
            
            if (empty($name)) {
                $error = 'O nome do item é obrigatório.';
            } else {
                try {
                    $db->query(
                        "INSERT INTO checklist_items (name, is_active) VALUES (?, 1)",
                        [$name]
                    );
                    $message = 'Item adicionado com sucesso!';
                } catch (Exception $e) {
                    $error = 'Erro ao adicionar item.';
                }
            }
        }
        // Editar item existente
        elseif ($_POST['action'] === 'edit') {
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if (empty($name)) {
                $error = 'O nome do item é obrigatório.';
            } else {
                try {
                    $db->query(
                        "UPDATE checklist_items SET name = ?, is_active = ? WHERE id = ?",
                        [$name, $is_active, $id]
                    );
                    $message = 'Item atualizado com sucesso!';
                } catch (Exception $e) {
                    $error = 'Erro ao atualizar item.';
                }
            }
        }
        // Excluir item
        elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'];
            
            try {
                // Verificar se o item está sendo usado
                $stmt = $db->query(
                    "SELECT COUNT(*) as count FROM vehicle_checklist_status WHERE checklist_item_id = ?",
                    [$id]
                );
                $result = $stmt->fetch();
                
                if ($result['count'] > 0) {
                    // Se estiver em uso, apenas desativa
                    $db->query(
                        "UPDATE checklist_items SET is_active = 0 WHERE id = ?",
                        [$id]
                    );
                    $message = 'Item desativado com sucesso pois está em uso.';
                } else {
                    // Se não estiver em uso, remove completamente
                    $db->query(
                        "DELETE FROM checklist_items WHERE id = ?",
                        [$id]
                    );
                    $message = 'Item excluído com sucesso!';
                }
            } catch (Exception $e) {
                $error = 'Erro ao excluir item.';
            }
        }
    }
}

// Buscar todos os itens de checklist
$stmt = $db->query("SELECT * FROM checklist_items ORDER BY is_active DESC, name");
$checklist_items = $stmt->fetchAll();

include('../menu.php');
?>

<main class="main-content">
    <div class="page-header">
        <h1>Gerenciar Itens de Checklist</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3>Adicionar Novo Item</h3>
        </div>
        <div class="card-body">
            <form method="POST" class="form-inline">
                <input type="hidden" name="action" value="add">
                <div class="form-group mr-2">
                    <input type="text" name="name" class="form-control" placeholder="Nome do item" required>
                </div>
                <button type="submit" class="btn btn-primary">Adicionar</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3>Itens Existentes</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($checklist_items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>
                            <?php if ($item['is_active']): ?>
                                <span class="badge badge-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary edit-item" 
                                data-id="<?php echo $item['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                data-active="<?php echo $item['is_active']; ?>">
                                Editar
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-item" 
                                data-id="<?php echo $item['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($item['name']); ?>">
                                Excluir
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label for="edit-name">Nome</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="edit-active" name="is_active">
                                <label class="custom-control-label" for="edit-active">Ativo</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Deseja realmente excluir o item "<span id="delete-item-name"></span>"?</p>
                    <p class="text-danger">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-id">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manipuladores para botões de edição
        document.querySelectorAll('.edit-item').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const isActive = this.getAttribute('data-active') === '1';
                
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-active').checked = isActive;
                
                $('#editModal').modal('show');
            });
        });
        
        // Manipuladores para botões de exclusão
        document.querySelectorAll('.delete-item').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                document.getElementById('delete-id').value = id;
                document.getElementById('delete-item-name').textContent = name;
                
                $('#deleteModal').modal('show');
            });
        });
    });
</script>

<?php include('../footer.php'); ?>