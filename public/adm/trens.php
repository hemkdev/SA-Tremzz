<?php
session_start();
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: login.php");
    exit;
}
require "../../config/bd.php";

// Listar trens
$stmt = $conn->prepare("SELECT * FROM trens ORDER BY data_cadastro DESC");
$stmt->execute();
$trens = $stmt->get_result()->fetch_assoc();

// Adicionar/Editar (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $modelo = trim($_POST['modelo']);
    $capacidade = intval($_POST['capacidade']);
    $status = $_POST['status'] ?? 'ativo';
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Editar
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE trens SET nome=?, modelo=?, capacidade=?, status=? WHERE id=?");
        $stmt->execute([$nome, $modelo, $capacidade, $status, $id]);
        $_SESSION['sucesso'] = "Trem atualizado!";
    } else {
        // Adicionar
        $stmt = $conn->prepare("INSERT INTO trens (nome, modelo, capacidade, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $modelo, $capacidade, $status]);
        $_SESSION['sucesso'] = "Trem cadastrado!";
    }
    header("Location: trens.php");
    exit;
}

// Excluir (GET com confirmação)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM trens WHERE id=?");
    $stmt->execute([$id]);
    $_SESSION['sucesso'] = "Trem excluído!";
    header("Location: trens.php");
    exit;
}
$conn = null;
?>
<!DOCTYPE html>
<html lang="pt-BR"> <!-- Head igual ao gerenciamento.php: Bootstrap, Poppins, CSS dark -->
<head>
    <!-- Copie o <head> inteiro de gerenciamento.php aqui -->
    <title>TREMzz - Gerenciar Trens</title>
    <style>
        /* Adicione estilos extras se precisar, ex: .table-dark { background: #1e1e1e; } */
    </style>
</head>
<body class="text-light" style="background-color: #121212; font-family: 'Poppins', sans-serif;">
    <!-- Header igual ao gerenciamento.php, mas título "Gerenciar Trens" -->
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-oi">
                        <h1 class="text-light fw-bold mb-0 fs-3">Gerenciar Trens</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION["foto"] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="alert alert-success rounded-3 mb-4"><?php echo $_SESSION['sucesso']; unset($_SESSION['sucesso']); ?></div>
        <?php endif; ?>

        <div class="card bg-custom border border-danger rounded-3 p-4 mb-4"> <!-- bg-custom = #1e1e1e no CSS global -->
            <h2 class="text-danger fw-bold fs-4 mb-3">Lista de Trens</h2>
            <button class="btn btn-outline-danger mb-3" data-bs-toggle="modal" data-bs-target="#modalTrem">Adicionar Trem</button>
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Modelo</th>
                            <th>Capacidade</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trens as $trem): ?>
                            <tr>
                                <td><?php echo $trem['id']; ?></td>
                                <td><?php echo htmlspecialchars($trem['nome']); ?></td>
                                <td><?php echo htmlspecialchars($trem['modelo']); ?></td>
                                <td><?php echo $trem['capacidade']; ?></td>
                                <td><span class="badge <?php echo $trem['status'] === 'ativo' ? 'bg-success' : 'bg-warning'; ?>"><?php echo ucfirst($trem['status']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editarTrem(<?php echo $trem['id']; ?>)">Editar</button>
                                    <a href="?delete=<?php echo $trem['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal para Adicionar/Editar (Bootstrap) -->
    <div class="modal fade" id="modalTrem" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger">Adicionar/Editar Trem</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="trem_id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control bg-secondary text-light" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control bg-secondary text-light" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacidade</label>
                            <input type="number" name="capacidade" class="form-control bg-secondary text-light" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select bg-secondary text-light">
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                                <option value="em_manutencao">Em Manutenção</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-danger">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer igual ao gerenciamento.php, com active em gerenciamento -->
    <footer><!-- Copie o footer inteiro --></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarTrem(id) {
            // Preencha o modal com dados via AJAX ou fetch (simplificado: redirecione ou use JS para popular)
            // Exemplo básico: alert('Edite via modal'); – Implemente fetch para dados reais
            document.getElementById('trem_id').value = id;
            // Adicione fetch para carregar dados no modal
        }
    </script>
</body>
</html>