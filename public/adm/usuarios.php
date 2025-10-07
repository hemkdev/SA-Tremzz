<?php
session_start();
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: home.php");
    exit;
}

require "../../config/bd.php";

// ID do admin atual para excluir da lista
$admin_id = $_SESSION['id'] ?? 0;

// Total usuários
$stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE id != ?");
$stmt_total->bind_param("i", $admin_id);
$stmt_total->execute();
$total_usuarios = $stmt_total->get_result()->fetch_assoc()['total'];

// Maquinistas
$stmt_maquinistas = $conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE cargo = 'Maquinista' AND id != ?");
$stmt_maquinistas->bind_param("i", $admin_id);
$stmt_maquinistas->execute();
$maquinistas = $stmt_maquinistas->get_result()->fetch_assoc()['total'];

// Outros usuários
$outros_usuarios = $total_usuarios - $maquinistas;

// Telefones cadastrados (não vazios)
$stmt_telefones = $conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE telefone IS NOT NULL AND telefone != '' AND id != ?");
$stmt_telefones->bind_param("i", $admin_id);
$stmt_telefones->execute();
$telefones_cadastrados = $stmt_telefones->get_result()->fetch_assoc()['total'];

// Query para lista de usuários (com busca se $_GET['busca'] existir)
$busca = $_GET['busca'] ?? '';
$where_clause = "WHERE u.id != ? AND (u.nome LIKE ? OR u.email LIKE ? OR u.telefone LIKE ?)";
$params = [$admin_id, "%$busca%", "%$busca%", "%$busca%"];
$types = "isss";

$stmt_usuarios = $conn->prepare("SELECT u.id, u.nome, u.email, u.telefone, u.cargo 
                                     FROM usuarios u $where_clause 
                                     ORDER BY u.id DESC 
                                     LIMIT 20"); // Paginação simples: 20 por página
$stmt_usuarios->bind_param($types, ...$params);
$stmt_usuarios->execute();
$usuarios = $stmt_usuarios->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_total->close();
$stmt_maquinistas->close();
$stmt_telefones->close();
$stmt_usuarios->close();


$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Gerenciamento de Usuários</title>
    <link rel="shortcut icon" href="../assets/img/tremlogo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">


    <!-- CSS mínimo (baseado no original + adições para tabela e modais) -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            min-height: 100vh;
            padding-bottom: 70px;
        }

        .card-hover {
            background-color: #1e1e1e !important;
            transition: background-color 0.3s ease;
            border: none;
        }

        .card-hover:hover {
            background-color: #2a2a2a !important;
            color: #fff !important;
        }

        .pfp-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .footer-icon img {
            width: 28px;
            height: 28px;
            filter: brightness(0) invert(1);
            transition: filter 0.3s ease;
        }

        .footer-icon:hover img,
        .footer-icon.active img {
            filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255, 193, 7, 0.8)) sepia(1) saturate(5) hue-rotate(-10deg);
        }

        .rodape {
            background-color: #121212;
            border: none;
            box-shadow: none;
            z-index: 1000;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #dc3545;
        }

        .stat-icon i {
            font-size: 2rem;
            color: #fff;
        }

        /* Adições mínimas para tabela e modais */
        .table-dark {
            background-color: #1e1e1e;
        }

        .table-dark th,
        .table-dark td {
            border-color: #333;
            color: #e0e0e0;
            vertical-align: middle;
        }

        .table-dark .btn {
            white-space: nowrap;
        }

        .modal-content {
            background-color: #1e1e1e;
            color: #e0e0e0;
            border: none;
        }

        .modal-header,
        .modal-footer {
            border-color: #333;
        }

        .form-control,
        .form-select {
            background-color: #2a2a2a;
            border-color: #333;
            color: #e0e0e0;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #2a2a2a;
            border-color: #dc3545;
            color: #e0e0e0;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }

        .btn-warning {
            background-color: #e2ab05ff;
            border-color: #e2ab05ff;
            color: #fff;
        }

        .btn-warning:hover {
            background-color: #ce9d09ff;
            border-color: #ce9d09ff;
            color: #fff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
        }

        @media (max-width: 768px) {
            .stats-grid {
                flex-direction: column !important;
            }

            .stats-grid .card {
                margin-bottom: 1rem;
            }
        }
    </style>

</head>

<body>
    <!-- Header -->
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-oi">
                        <h1 class="text-light fw-bold mb-0 fs-3"> Gerenciamento de usuários </h1>
                    </div>
                    <div class="pfp">
                        <img src="../../assets/img/perfil.png" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <!-- Estatísticas de Usuários -->
        <div class="stats mb-5">
            <div class="stats-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4"> Dados de usuários </h3>
            </div>

            <div class="stats-grid d-flex flex-wrap justify-content-between gap-3">
                <!-- Card 1: Total de Usuários -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2"> <!-- mb-2 para espaçamento abaixo do ícone -->
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Total de Usuários</div>
                        <div class="stat-number"><?php echo number_format($total_usuarios); ?></div>
                    </div>
                </div>

                <!-- Card 2: Maquinistas (ícone de trem mantido/temático) -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-person-badge"></i> <!-- Ícone de trem para maquinistas -->
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Maquinistas</div>
                        <div class="stat-number"><?php echo number_format($maquinistas); ?></div>
                    </div>
                </div>

                <!-- Card 3: Outros Usuários -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Usuários</div>
                        <div class="stat-number"><?php echo number_format($outros_usuarios); ?></div>
                    </div>
                </div>

                <!-- Card 4: Telefones Cadastrados -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Telefones Cadastrados</div>
                        <div class="stat-number"><?php echo number_format($telefones_cadastrados); ?></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Busca e Tabela de Usuários -->
        <div class="atividades mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-danger fw-bold fs-4 mb-0">Lista de Usuários</h3>
                <div class="input-group" style="max-width: 300px;">
                    <input type="text" class="form-control" id="buscaInput" placeholder="Buscar por nome, email ou telefone..." value="<?php echo htmlspecialchars($busca); ?>">
                    <button class="btn btn-outline-danger" type="button" onclick="filtrarUsuarios()"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover rounded-3 overflow-hidden" id="usuariosTable">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Email</th>
                            <th scope="col">Cargo</th>
                            <th scope="col">Telefone</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr data-nome="<?php echo strtolower($usuario['nome']); ?>" data-email="<?php echo strtolower($usuario['email']); ?>" data-telefone="<?php echo strtolower($usuario['telefone']); ?>">
                                    <td><?php echo $usuario['id']; ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['cargo']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['telefone'] ?? 'Não cadastrado'); ?></td>
                                    
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1" onclick="editarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars(addslashes($usuario['nome'])); ?>', '<?php echo htmlspecialchars(addslashes($usuario['email'])); ?>', '<?php echo htmlspecialchars(addslashes($usuario['telefone'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($usuario['cargo'])); ?>')">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>

                                        <form method="POST" action="delete.php?id=<?php echo $usuario['id']; ?>" style="display: inline;" onsubmit="return confirm('Deletar este usuário? Ação irreversível!');">
                                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Deletar</button>
                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-light">Nenhum usuário encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação simples 
            <nav aria-label="Paginação de usuários">
                <ul class="pagination justify-content-center">
                    <li class="page-item"><a class="page-link bg-secondary text-light border-0" href="#">Anterior</a></li>
                    <li class="page-item"><a class="page-link bg-danger text-light border-0" href="#">1</a></li>
                    <li class="page-item"><a class="page-link bg-secondary text-light border-0" href="#">Próximo</a></li>
                </ul>
            </nav>
            -->

        </div>
    </main>

    <!-- Modal para Editar Usuário -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <!-- Modal de Edição de Usuário -->
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="editarModalLabel">Editar Usuário</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="editarForm" method="POST" action="update_usuario.php">
                    <div class="modal-body">
                        <input type="hidden" id="editarId" name="id" value="">
                        <div class="mb-3">
                            <label for="editarNome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="editarNome" name="nome" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="editarEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editarEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editarTelefone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control" id="editarTelefone" name="telefone" placeholder="Ex: 47 99999-9999" maxlength="15">
                        </div>
                        <div class="mb-3">
                            <label for="editarCargo" class="form-label">Cargo</label>
                            <select class="form-select" id="editarCargo" name="cargo" required>
                                <option value="Usuário">Usuário</option>
                                <option value="Maquinista">Maquinista</option>
                                <option value="Administrador">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="container d-flex justify-content-around align-items-center" style="max-width: 900px;">
            <a href="home.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Início">
                <img src="../../assets/img/casa.png" alt="Início" />
            </a>
            <a href="gerenciamento.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Gerenciamento">
                <img src="../../assets/img/gerenciamento.png" alt="Gerenciamento" />
            </a>
            <a href="chat.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Chat">
                <img src="../../assets/img/chat.png" alt="Chat" />
            </a>
            <a href="perfil.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Perfil">
                <img src="../../assets/img/perfil.png" alt="Perfil" />
            </a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts JS Inline (para busca e edição) -->
    <script>
        // Função para filtrar usuários na tabela (client-side)
        function filtrarUsuarios() {
            const input = document.getElementById('buscaInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('usuariosTable');
            const tr = table.getElementsByTagName('tr');

            let visibleRows = 0;
            for (let i = 1; i < tr.length; i++) { // Pula o header (i=0)
                const nome = tr[i].getAttribute('data-nome') || '';
                const email = tr[i].getAttribute('data-email') || '';
                const telefone = tr[i].getAttribute('data-telefone') || '';
                if (nome.includes(filter) || email.includes(filter) || telefone.includes(filter)) {
                    tr[i].style.display = '';
                    visibleRows++;
                } else {
                    tr[i].style.display = 'none';
                }
            }

            // Atualiza mensagem se nenhum resultado
            const noResultsRow = table.querySelector('tbody tr td[colspan="6"]');
            if (visibleRows === 0 && filter !== '') {
                if (!noResultsRow || noResultsRow.textContent !== 'Nenhum usuário encontrado.') {
                    const newRow = table.insertRow(-1);
                    const cell = newRow.insertCell(0);
                    cell.colSpan = 6;
                    cell.className = 'text-center text-light';
                    cell.textContent = 'Nenhum usuário encontrado.';
                    newRow.style.display = '';
                }
            } else if (noResultsRow && filter === '') {
                // Remove mensagem se busca vazia e há resultados
                if (table.querySelector('tbody tr td[colspan="6"]')) {
                    table.querySelector('tbody tr td[colspan="6"]').parentElement.remove();
                }
            }
        }

        // Função para editar usuário (popula modal)
        function editarUsuario(id, nome, email, telefone, cargo) {
            document.getElementById('editarId').value = id;
            document.getElementById('editarNome').value = nome;
            document.getElementById('editarEmail').value = email;
            document.getElementById('editarTelefone').value = telefone;
            document.getElementById('editarCargo').value = cargo;

            const modal = new bootstrap.Modal(document.getElementById('editarModal'));
            modal.show();
        }

        // Event listener para busca em tempo real (opcional: digitação)
        document.getElementById('buscaInput').addEventListener('keyup', function() {
            filtrarUsuarios();
        });
    </script>

</body>

</html>