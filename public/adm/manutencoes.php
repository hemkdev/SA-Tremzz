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

// Total manutenções
$stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM manutencoes");
$stmt_total->execute();
$total_manutencoes = $stmt_total->get_result()->fetch_assoc()['total'];

// Manutenções concluídas
$stmt_concluidas = $conn->prepare("SELECT COUNT(*) as total FROM manutencoes WHERE status = 'Concluída'");
$stmt_concluidas->execute();
$manutencoes_concluidas = $stmt_concluidas->get_result()->fetch_assoc()['total'];

// Manutenções em andamento
$stmt_em_andamento = $conn->prepare("SELECT COUNT(*) as total FROM manutencoes WHERE status = 'Em andamento'");
$stmt_em_andamento->execute();
$manutencoes_em_andamento = $stmt_em_andamento->get_result()->fetch_assoc()['total'];

// Manutenções pendentes
$stmt_pendentes = $conn->prepare("SELECT COUNT(*) as total FROM manutencoes WHERE status = 'Pendente'");
$stmt_pendentes->execute();
$manutencoes_pendentes = $stmt_pendentes->get_result()->fetch_assoc()['total'];

// Manutenções agendadas para a semana (próximos 7 dias)
$stmt_semana = $conn->prepare("SELECT COUNT(*) as total FROM manutencoes WHERE data_agendada BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
$stmt_semana->execute();
$manutencoes_semana = $stmt_semana->get_result()->fetch_assoc()['total'];

// Buscar trens para o select no modal
$stmt_trens = $conn->prepare("SELECT id, modelo FROM Trens ORDER BY modelo ASC");
$stmt_trens->execute();
$trens = $stmt_trens->get_result()->fetch_all(MYSQLI_ASSOC);

// Query para lista de manutenções (com busca se $_GET['busca'] existir)
$busca = $_GET['busca'] ?? '';
$where_clause = !empty($busca) ? "WHERE (m.tipo LIKE ? OR m.status LIKE ? OR t.modelo LIKE ?)" : "";
$params = empty($busca) ? [] : ["%$busca%", "%$busca%", "%$busca%"];
$types = empty($busca) ? "" : "sss";

$stmt_manutencoes = $conn->prepare("SELECT m.id, m.id_trem, m.tipo, m.data_agendada, m.data_conclusao, m.status, t.modelo as trem_modelo 
                                   FROM manutencoes m 
                                   INNER JOIN Trens t ON m.id_trem = t.id 
                                   $where_clause 
                                   ORDER BY m.id DESC 
                                   LIMIT 20"); // Paginação simples: 20 por página
if (!empty($busca)) {
    $stmt_manutencoes->bind_param($types, ...$params);
}
$stmt_manutencoes->execute();
$manutencoes = $stmt_manutencoes->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_total->close();
$stmt_concluidas->close();
$stmt_em_andamento->close();
$stmt_pendentes->close();
$stmt_semana->close();
$stmt_trens->close();
$stmt_manutencoes->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Gerenciamento de Manutenções</title>
    <link rel="shortcut icon" href="../../assets/img/tremzz_logo.png" />
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
    </style>

</head>

<body>
    <!-- Header -->
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-oi">
                        <h1 class="text-light fw-bold mb-0 fs-3">Gerenciamento de Manutenções</h1>
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

        <!-- Estatísticas de Manutenções -->
        <div class="stats mb-5">
            <div class="stats-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Dados de Manutenções</h3>
            </div>

            <div class="stats-grid d-flex flex-wrap justify-content-between gap-3">
                <!-- Card 1: Concluídas -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Concluídas</div>
                        <div class="stat-number"><?php echo number_format($manutencoes_concluidas); ?></div>
                    </div>
                </div>

                <!-- Card 2: Em Andamento -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Em Andamento</div>
                        <div class="stat-number"><?php echo number_format($manutencoes_em_andamento); ?></div>
                    </div>
                </div>

                <!-- Card 3: Pendentes -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Pendentes</div>
                        <div class="stat-number"><?php echo number_format($manutencoes_pendentes); ?></div>
                    </div>
                </div>

                <!-- Card 4: Agendadas para a Semana -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Agendadas p/ Semana</div>
                        <div class="stat-number"><?php echo number_format($manutencoes_semana); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Busca e Tabela de Manutenções -->
        <div class="atividades mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-danger fw-bold fs-4 mb-0">Lista de Manutenções</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger" onclick="limparModalParaAdicionar()" data-bs-toggle="modal" data-bs-target="#editarModal">
                        <i class="bi bi-plus-circle me-1"></i> Adicionar Manutenção
                    </button>
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control" id="buscaInput" placeholder="Buscar por tipo, status ou modelo..." value="<?php echo htmlspecialchars($busca); ?>">
                        <button class="btn btn-outline-danger" type="button" onclick="filtrarManutencoes()"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover rounded-3 overflow-hidden" id="manutencoesTable">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Trem</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Data Agendada</th>
                            <th scope="col">Data Conclusão</th>
                            <th scope="col">Status</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($manutencoes)): ?>
                            <?php foreach ($manutencoes as $manutencao): ?>
                                <tr data-tipo="<?php echo strtolower($manutencao['tipo']); ?>" data-status="<?php echo strtolower($manutencao['status']); ?>" data-modelo="<?php echo strtolower($manutencao['trem_modelo']); ?>">
                                    <td><?php echo $manutencao['id']; ?></td>
                                    <td><?php echo htmlspecialchars($manutencao['trem_modelo']); ?></td>
                                    <td><?php echo htmlspecialchars($manutencao['tipo']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($manutencao['data_agendada'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($manutencao['data_conclusao'])); ?></td>
                                    <td>
                                        <?php
                                        $status = $manutencao['status'];
                                        $badge_class = ($status === 'Pendente') ? 'bg-warning' : (($status === 'Em andamento') ? 'bg-info' : 'bg-success');
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1" onclick="editarManutencao(<?php echo $manutencao['id']; ?>, <?php echo $manutencao['id_trem']; ?>, '<?php echo htmlspecialchars(addslashes($manutencao['tipo'])); ?>', '<?php echo $manutencao['data_agendada']; ?>', '<?php echo $manutencao['data_conclusao']; ?>', '<?php echo htmlspecialchars(addslashes($manutencao['status'])); ?>')">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>

                                        <form method="POST" action="model/manutencao.php?id=<?php echo $manutencao['id']; ?>" style="display: inline;" onsubmit="return confirm('Deletar esta manutenção? Ação irreversível!');">
                                            <input type="hidden" name="id" value="<?php echo $manutencao['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-light">Nenhuma manutenção encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal para Adicionar/Editar Manutenção -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="editarModalLabel">Adicionar Manutenção</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="editarForm" method="POST" action="model/manutencao.php">
                    <div class="modal-body">
                        <input type="hidden" id="editarId" name="id" value="">
                        <div class="mb-3">
                            <label for="editarIdTrem" class="form-label">Trem <span class="text-danger">*</span></label>
                            <select class="form-select" id="editarIdTrem" name="id_trem" required>
                                <option value="">Selecione um trem...</option>
                                <?php foreach ($trens as $trem): ?>
                                    <option value="<?php echo $trem['id']; ?>"><?php echo htmlspecialchars($trem['modelo']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editarTipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select" id="editarTipo" name="tipo" required>
                                <option value="técnica">Técnica</option>
                                <option value="sistema">Sistema</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editarDataAgendada" class="form-label">Data Agendada <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editarDataAgendada" name="data_agendada" required>
                        </div>
                        <div class="mb-3">
                            <label for="editarDataConclusao" class="form-label">Data Conclusão <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="editarDataConclusao" name="data_conclusao" required>
                        </div>
                        <div class="mb-3">
                            <label for="editarStatus" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="editarStatus" name="status" required>
                                <option value="Pendente">Pendente</option>
                                <option value="Em andamento">Em andamento</option>
                                <option value="Concluída">Concluída</option>
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
        // Função para filtrar manutenções na tabela (client-side)
        function filtrarManutencoes() {
            const input = document.getElementById('buscaInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('manutencoesTable');
            const tr = table.getElementsByTagName('tr');

            let visibleRows = 0;
            for (let i = 1; i < tr.length; i++) { // Pula o header (i=0)
                const tipo = tr[i].getAttribute('data-tipo') || '';
                const status = tr[i].getAttribute('data-status') || '';
                const modelo = tr[i].getAttribute('data-modelo') || '';
                if (tipo.includes(filter) || status.includes(filter) || modelo.includes(filter)) {
                    tr[i].style.display = '';
                    visibleRows++;
                } else {
                    tr[i].style.display = 'none';
                }
            }

            // Atualiza mensagem se nenhum resultado
            const noResultsRow = table.querySelector('tbody tr td[colspan="7"]');
            if (visibleRows === 0 && filter !== '') {
                if (!noResultsRow || noResultsRow.textContent !== 'Nenhuma manutenção encontrada.') {
                    const newRow = table.insertRow(-1);
                    const cell = newRow.insertCell(0);
                    cell.colSpan = 7;
                    cell.className = 'text-center text-light';
                    cell.textContent = 'Nenhuma manutenção encontrada.';
                    newRow.style.display = '';
                }
            } else if (noResultsRow && filter === '') {
                // Remove mensagem se busca vazia e há resultados
                if (table.querySelector('tbody tr td[colspan="7"]')) {
                    table.querySelector('tbody tr td[colspan="7"]').parentElement.remove();
                }
            }
        }

        // Função para limpar modal ao adicionar nova manutenção
        function limparModalParaAdicionar() {
            // Limpa campos
            document.getElementById('editarId').value = ''; // ID vazio = INSERT no PHP
            document.getElementById('editarIdTrem').selectedIndex = 0; // Reseta select para primeira opção
            document.getElementById('editarTipo').selectedIndex = 0; // Reseta select
            document.getElementById('editarDataAgendada').value = ''; // Limpa date
            document.getElementById('editarDataConclusao').value = ''; // Limpa date
            document.getElementById('editarStatus').selectedIndex = 0; // Reseta select

            // Atualiza título do modal para "Adicionar"
            document.getElementById('editarModalLabel').textContent = 'Adicionar Manutenção';

            // Opcional: Foca no primeiro campo para UX
            document.getElementById('editarIdTrem').focus();
        }

        // Função para editar manutenção (popula modal)
        function editarManutencao(id, id_trem, tipo, data_agendada, data_conclusao, status) {
            document.getElementById('editarId').value = id;
            document.getElementById('editarIdTrem').value = id_trem;
            document.getElementById('editarTipo').value = tipo;
            document.getElementById('editarDataAgendada').value = data_agendada;
            document.getElementById('editarDataConclusao').value = data_conclusao;
            document.getElementById('editarStatus').value = status;

            // Atualiza título para "Editar"
            document.getElementById('editarModalLabel').textContent = 'Editar Manutenção';

            const modal = new bootstrap.Modal(document.getElementById('editarModal'));
            modal.show();
        }

        // Event listener para busca em tempo real (opcional: digitação)
        document.getElementById('buscaInput').addEventListener('keyup', function() {
            filtrarManutencoes();
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