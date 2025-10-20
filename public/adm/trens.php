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

// Total trens
$stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM Trens");
$stmt_total->execute();
$total_trens = $stmt_total->get_result()->fetch_assoc()['total'];

// Trens disponíveis (status 'Disponível')
$stmt_disponiveis = $conn->prepare("SELECT COUNT(*) as total FROM Trens WHERE status = 'Disponível'");
$stmt_disponiveis->execute();
$trens_disponiveis = $stmt_disponiveis->get_result()->fetch_assoc()['total'];

// Trens em rota (status 'Em rota')
$stmt_em_rota = $conn->prepare("SELECT COUNT(*) as total FROM Trens WHERE status = 'Em rota'");
$stmt_em_rota->execute();
$trens_em_rota = $stmt_em_rota->get_result()->fetch_assoc()['total'];

// Trens em manutenção (status 'Em manutenção')
$stmt_manutencao = $conn->prepare("SELECT COUNT(*) as total FROM Trens WHERE status = 'Em manutenção'");
$stmt_manutencao->execute();
$trens_manutencao = $stmt_manutencao->get_result()->fetch_assoc()['total'];

// Query para lista de trens (com busca se $_GET['busca'] existir)
$busca = $_GET['busca'] ?? '';
$where_clause = !empty($busca) ? "WHERE (t.modelo LIKE ? OR t.tipo_carga LIKE ?)" : "";
$params = empty($busca) ? [] : ["%$busca%", "%$busca%"];
$types = empty($busca) ? "" : "ss";

$stmt_trens = $conn->prepare("SELECT t.id, t.modelo, t.tipo_carga, t.status 
                                   FROM Trens t 
                                   $where_clause 
                                   ORDER BY t.id ASC 
                                   LIMIT 20"); // Paginação simples: 20 por página
if (!empty($busca)) {
    $stmt_trens->bind_param($types, ...$params);
}
$stmt_trens->execute();
$trens = $stmt_trens->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_total->close();
$stmt_disponiveis->close();
$stmt_em_rota->close();
$stmt_manutencao->close();
$stmt_trens->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Gerenciamento de Trens</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- CSS mínimo (baseado no sensores.php + adições para tabela e modais) -->
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
        @media (max-width: 480px) {
            .navbar { padding-top: .6rem !important; padding-bottom: .25rem !important; }
            .d-flex.justify-content-between { flex-wrap: nowrap; }
            .text-oi { text-align: left; }
            .pfp { text-align: right; }
            .text-danger.fw-bold.fs-4,
            .text-danger.fw-bold.fs-5 { text-align: center; display: block; width: 100%; }
        }
        /* ADM HEADER OVERRIDE: manter título à esquerda e pfp à direita em telas <480px */
        @media (max-width: 480px) {
            nav.navbar .container-fluid .d-flex { flex-direction: row !important; justify-content: space-between !important; align-items: center !important; gap: .5rem; }
            .navbar .text-oi { text-align: left !important; }
            .navbar .pfp { margin-left: auto !important; text-align: right !important; }
            .navbar { padding-top: .6rem !important; padding-bottom: .25rem !important; }
            .text-danger.fw-bold.fs-4, .text-danger.fw-bold.fs-5, .text-danger.fw-bold.fs-3 { text-align: center !important; display: block; width: 100%; }
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
                        <h1 class="text-light fw-bold mb-0 fs-3">Gerenciamento de Trens</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION['foto'] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="alert alert-success rounded-3 mb-4"><?php echo $_SESSION['sucesso'];
                                                            unset($_SESSION['sucesso']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger rounded-3 mb-4"><?php echo $_SESSION['erro'];
                                                            unset($_SESSION['erro']); ?></div>
        <?php endif; ?>

        <!-- Estatísticas de Trens -->
        <div class="stats mb-5">
            <div class="stats-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Dados de Trens</h3>
            </div>

            <div class="stats-grid d-flex flex-wrap justify-content-between gap-3">
                <!-- Card 1: Total de Trens -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-train-freight-front"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Total de Trens</div>
                        <div class="stat-number"><?php echo number_format($total_trens); ?></div>
                    </div>
                </div>

                <!-- Card 2: Trens Disponíveis -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Disponíveis</div>
                        <div class="stat-number"><?php echo number_format($trens_disponiveis); ?></div>
                    </div>
                </div>

                <!-- Card 3: Trens Em Rota -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-arrow-right-circle-fill"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Em Rota</div>
                        <div class="stat-number"><?php echo number_format($trens_em_rota); ?></div>
                    </div>
                </div>

                <!-- Card 4: Trens Em Manutenção -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Em Manutenção</div>
                        <div class="stat-number"><?php echo number_format($trens_manutencao); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Busca e Tabela de Trens -->
        <div class="atividades mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-danger fw-bold fs-4 mb-0">Lista de Trens</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger" onclick="limparModalParaAdicionar()" data-bs-toggle="modal" data-bs-target="#editarModal">
                        <i class="bi bi-plus-circle me-1"></i> Adicionar Trem
                    </button>
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control" id="buscaInput" placeholder="Buscar por modelo ou tipo de carga..." value="<?php echo htmlspecialchars($busca); ?>">
                        <button class="btn btn-outline-danger" type="button" onclick="filtrarTrens()"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover rounded-3 overflow-hidden" id="trensTable">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Modelo</th>
                            <th scope="col">Tipo de Carga</th>
                            <th scope="col">Status</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($trens)): ?>
                            <?php foreach ($trens as $trem): ?>
                                <tr data-modelo="<?php echo strtolower($trem['modelo']); ?>" data-tipo="<?php echo strtolower($trem['tipo_carga']); ?>">
                                    <td><?php echo $trem['id']; ?></td>
                                    <td><?php echo htmlspecialchars($trem['modelo']); ?></td>
                                    <td><?php echo htmlspecialchars($trem['tipo_carga']); ?></td>
                                    <td>
                                        <?php
                                        $status = $trem['status'];
                                        $badge_class = ($status === 'Disponível') ? 'bg-success' : (($status === 'Em rota') ? 'bg-warning' : 'bg-danger');
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1" onclick="editarTrem(<?php echo $trem['id']; ?>, '<?php echo htmlspecialchars(addslashes($trem['modelo'])); ?>', '<?php echo htmlspecialchars(addslashes($trem['tipo_carga'])); ?>', '<?php echo htmlspecialchars(addslashes($trem['status'])); ?>')">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>

                                        <form method="POST" action="model/delete_trens.php?id=<?php echo $trem['id']; ?>" style="display: inline;" onsubmit="return confirm('Deletar este trem? Ação irreversível!');">
                                            <input type="hidden" name="id" value="<?php echo $trem['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Deletar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-light">Nenhum trem encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal para Adicionar/Editar Trem -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="editarModalLabel">Adicionar Trem</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="editarForm" method="POST" action="model/trem.php">
                    <div class="modal-body">
                        <input type="hidden" id="editarId" name="id" value="">
                        <div class="mb-3">
                            <label for="editarModelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editarModelo" name="modelo" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="editarTipoCarga" class="form-label">Tipo de Carga <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editarTipoCarga" name="tipo_carga" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="editarStatus" class="form-label">Status</label>
                            <select class="form-select" id="editarStatus" name="status" required>
                                <option value="Disponível">Disponível</option>
                                <option value="Em rota">Em rota</option>
                                <option value="Em manutenção">Em manutenção</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger" name="editar">Salvar Alterações</button>
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
        // Função para filtrar trens na tabela (client-side)
        function filtrarTrens() {
            const input = document.getElementById('buscaInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('trensTable');
            const tr = table.getElementsByTagName('tr');

            let visibleRows = 0;
            for (let i = 1; i < tr.length; i++) { // Pula o header (i=0)
                const modelo = tr[i].getAttribute('data-modelo') || '';
                const tipo = tr[i].getAttribute('data-tipo') || '';
                if (modelo.includes(filter) || tipo.includes(filter)) {
                    tr[i].style.display = '';
                    visibleRows++;
                } else {
                    tr[i].style.display = 'none';
                }
            }

            // Atualiza mensagem se nenhum resultado
            const noResultsRow = table.querySelector('tbody tr td[colspan="5"]');
            if (visibleRows === 0 && filter !== '') {
                if (!noResultsRow || noResultsRow.textContent !== 'Nenhum trem encontrado.') {
                    const newRow = table.insertRow(-1);
                    const cell = newRow.insertCell(0);
                    cell.colSpan = 5;
                    cell.className = 'text-center text-light';
                    cell.textContent = 'Nenhum trem encontrado.';
                    newRow.style.display = '';
                }
            } else if (noResultsRow && filter === '') {
                // Remove mensagem se busca vazia e há resultados
                if (table.querySelector('tbody tr td[colspan="5"]')) {
                    table.querySelector('tbody tr td[colspan="5"]').parentElement.remove();
                }
            }
        }

        // Função para limpar modal ao adicionar novo trem
        function limparModalParaAdicionar() {
            // Limpa campos
            document.getElementById('editarId').value = ''; // ID vazio = INSERT no PHP
            document.getElementById('editarModelo').value = ''; // Limpa input texto
            document.getElementById('editarTipoCarga').value = ''; // Limpa input texto
            document.getElementById('editarStatus').selectedIndex = 0; // Reseta select para primeira opção (Disponível)

            // Atualiza título do modal para "Adicionar"
            document.getElementById('editarModalLabel').textContent = 'Adicionar Trem';

            // Opcional: Foca no primeiro campo para UX
            document.getElementById('editarModelo').focus();
        }

        // Função para editar trem (popula modal)
        function editarTrem(id, modelo, tipo_carga, status) {
            document.getElementById('editarId').value = id;
            document.getElementById('editarModelo').value = modelo;
            document.getElementById('editarTipoCarga').value = tipo_carga;
            document.getElementById('editarStatus').value = status; // Seleciona o status exato

            // Atualiza título para "Editar"
            document.getElementById('editarModalLabel').textContent = 'Editar Trem';

            const modal = new bootstrap.Modal(document.getElementById('editarModal'));
            modal.show();
        }

        // Event listener para busca em tempo real (opcional: digitação)
        document.getElementById('buscaInput').addEventListener('keyup', function() {
            filtrarTrens();
        });

        // Event listener para reset automático no show do modal (detecta origem)
        document.getElementById('editarModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Botão que abriu o modal
            const isAddButton = button && button.textContent.includes('Adicionar'); // Detecta se é o botão de add
            if (isAddButton) {
                limparModalParaAdicionar(); // Limpa se for adição
            }
            // Para edição, o onclick já popula antes de show
        });
    </script>

</body>

</html>