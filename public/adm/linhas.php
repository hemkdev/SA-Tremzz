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

// Total rotas
$stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM rotas");
$stmt_total->execute();
$total_rotas = $stmt_total->get_result()->fetch_assoc()['total'];

// Rotas em andamento
$stmt_andamento = $conn->prepare("SELECT COUNT(*) as total FROM trens WHERE status = 'Em rota'");
$stmt_andamento->execute();
$total_andamento = $stmt_andamento->get_result()->fetch_assoc()['total'];

// selects no modal
$stmt_itinerarios = $conn->prepare("SELECT id, nome FROM Itinerarios");
$stmt_itinerarios->execute();
$itinerarios = $stmt_itinerarios->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_maquinistas = $conn->prepare("SELECT id, nome FROM usuarios WHERE cargo = 'maquinista'");  // Assumindo campo 'role'
$stmt_maquinistas->execute();
$maquinistas = $stmt_maquinistas->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_trens = $conn->prepare("SELECT id, modelo FROM Trens");
$stmt_trens->execute();
$trens = $stmt_trens->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_estacoes = $conn->prepare("SELECT id, nome FROM Estacoes");
$stmt_estacoes->execute();
$estacoes = $stmt_estacoes->get_result()->fetch_all(MYSQLI_ASSOC);

// Query para lista de rotas (com busca se $_GET['busca'] existir)
$busca = $_GET['busca'] ?? '';
$where_clause = !empty($busca) ? "WHERE (i.nome LIKE ? OR u.nome LIKE ?)" : "";
$params = empty($busca) ? [] : ["%$busca%", "%$busca%"];
$types = empty($busca) ? "" : "ss";

$stmt_rotas = $conn->prepare("SELECT r.id, r.itinerario_id, 
                                r.maquinista_id, 
                                r.trem_id, 
                                r.estacao_origem_id, 
                                r.estacao_destino_id, 
                                r.via_estacao_id, 
                                i.nome AS itinerario, 
                                u.nome AS maquinista, 
                                t.modelo AS trem, 
                                eo.nome AS estacao_origem, 
                                ed.nome AS estacao_destino, 
                                ev.nome AS via_estacao
    FROM rotas r
                              JOIN Itinerarios i ON r.itinerario_id = i.id 
                              JOIN usuarios u ON r.maquinista_id = u.id 
                              JOIN Trens t ON r.trem_id = t.id 
                              JOIN Estacoes eo ON r.estacao_origem_id = eo.id 
                              JOIN Estacoes ed ON r.estacao_destino_id = ed.id 
                              LEFT JOIN Estacoes ev ON r.via_estacao_id = ev.id 
                              $where_clause 
                              ORDER BY r.id DESC 
                              LIMIT 20");
if (!empty($busca)) {
    $stmt_rotas->bind_param($types, ...$params);
}
$stmt_rotas->execute();
$rotas = $stmt_rotas->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_total->close();
$stmt_andamento->close();
$stmt_itinerarios->close();
$stmt_maquinistas->close();
$stmt_trens->close();
$stmt_estacoes->close();
$stmt_rotas->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Gerenciamento de Rotas</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Mesmos estilos do código original */
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

        .footer-icon:hover img {
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

        .table-dark {
            background-color: #1e1e1e;
        }

        .table-dark th,
        .table-dark td {
            border-color: #333;
            color: #e0e0e0;
            vertical-align: middle;
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
    </style>
</head>

<body>
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h1 class="text-light fw-bold mb-0 fs-3">Gerenciamento de Rotas</h1>
                    <img src="<?php echo htmlspecialchars($_SESSION['foto'] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
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

        <div class="stats mb-5">
            <div class="stats-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Dados de Sensores</h3>
            </div>

            <div class="stats-grid d-flex flex-wrap justify-content-between gap-3">
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-list"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Total de rotas</div>
                        <div class="stat-number"><?php echo number_format($total_rotas); ?></div>
                    </div>
                </div>

                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-soundwave"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Rotas em andamento</div>
                        <div class="stat-number"><?php echo number_format($total_andamento); ?></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="atividades mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-danger fw-bold fs-4 mb-0">Lista de Rotas</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger" onclick="limparModalParaAdicionar()" data-bs-toggle="modal" data-bs-target="#editarModal">
                        <i class="bi bi-plus-circle me-1"></i> Adicionar Rota
                    </button>
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control" id="buscaInput" placeholder="Buscar por itinerário ou maquinista..." value="<?php echo htmlspecialchars($busca); ?>">
                        <button class="btn btn-outline-danger" onclick="filtrarRotas()"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover" id="rotasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Itinerário</th>
                            <th>Maquinista</th>
                            <th>Trem</th>
                            <th>Estação Origem</th>
                            <th>Estação Destino</th>
                            <th>Via Estação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rotas)): ?>
                            <?php foreach ($rotas as $rota): ?>
                                <tr data-itinerario="<?php echo strtolower($rota['itinerario']); ?>" data-maquinista="<?php echo strtolower($rota['maquinista']); ?>">
                                    <td><?php echo $rota['id']; ?></td>
                                    <td><?php echo htmlspecialchars($rota['itinerario']); ?></td>
                                    <td><?php echo htmlspecialchars($rota['maquinista']); ?></td>
                                    <td><?php echo htmlspecialchars($rota['trem']); ?></td>
                                    <td><?php echo htmlspecialchars($rota['estacao_origem']); ?></td>
                                    <td><?php echo htmlspecialchars($rota['estacao_destino']); ?></td>
                                    <td><?php echo htmlspecialchars($rota['via_estacao'] ?? 'N/A'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1"
                                            onclick="editarRota(<?php echo $rota['id']; ?>,<?php echo $rota['itinerario_id']; ?>,<?php echo $rota['maquinista_id']; ?>,<?php echo $rota['trem_id']; ?>,<?php echo $rota['estacao_origem_id']; ?>,<?php echo $rota['estacao_destino_id']; ?>,<?php echo $rota['via_estacao_id'] ?? 'null'; ?>,null)">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <form method="POST" action="model/linha.php?id=<?php echo $rota['id']; ?>" style="display: inline;" onsubmit="return confirm('Deletar esta rota? Ação irreversível!');">
                                            <button type="submit" class="btn btn-sm btn-danger" name="deletar"><i class="bi bi-trash"></i> Deletar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-light">Nenhuma rota encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="editarModalLabel">Adicionar Rota</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editarForm" method="POST" action="model/linha.php">
                    <div class="modal-body">
                        <input type="hidden" id="editarId" name="id" value="">
                        <div class="mb-3">
                            <label for="editarItinerario" class="form-label">Itinerário *</label>
                            <select class="form-select" id="editarItinerario" name="itinerario_id" required>
                                <?php foreach ($itinerarios as $it): ?>
                                    <option value="<?php echo $it['id']; ?>"><?php echo htmlspecialchars($it['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editarMaquinista" class="form-label">Maquinista *</label>
                            <select class="form-select" id="editarMaquinista" name="maquinista_id" required>
                                <?php foreach ($maquinistas as $ma): ?>
                                    <option value="<?php echo $ma['id']; ?>"><?php echo htmlspecialchars($ma['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editarTrem" class="form-label">Trem *</label>
                            <select class="form-select" id="editarTrem" name="trem_id" required>
                                <?php foreach ($trens as $tr): ?>
                                    <option value="<?php echo $tr['id']; ?>"><?php echo htmlspecialchars($tr['modelo']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editarEstacaoOrigem" class="form-label">Estação Origem *</label>
                            <select class="form-select" id="editarEstacaoOrigem" name="estacao_origem_id" required>
                                <?php foreach ($estacoes as $es): ?>
                                    <option value="<?php echo $es['id']; ?>"><?php echo htmlspecialchars($es['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editarEstacaoDestino" class="form-label">Estação Destino *</label>
                            <select class="form-select" id="editarEstacaoDestino" name="estacao_destino_id" required>
                                <?php foreach ($estacoes as $es): ?>
                                    <option value="<?php echo $es['id']; ?>"><?php echo htmlspecialchars($es['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editarViaEstacao" class="form-label">Via Estação (opcional)</label>
                            <select class="form-select" id="editarViaEstacao" name="via_estacao_id">
                                <option value="">Nenhuma</option>
                                <?php foreach ($estacoes as $es): ?>
                                    <option value="<?php echo $es['id']; ?>"><?php echo htmlspecialchars($es['nome']); ?></option>
                                <?php endforeach; ?>
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
    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filtrarRotas() {
            const input = document.getElementById('buscaInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('rotasTable');
            const tr = table.getElementsByTagName('tr');

            let visibleRows = 0;
            for (let i = 1; i < tr.length; i++) {
                const itinerario = tr[i].getAttribute('data-itinerario') || '';
                const maquinista = tr[i].getAttribute('data-maquinista') || '';
                if (itinerario.includes(filter) || maquinista.includes(filter)) {
                    tr[i].style.display = '';
                    visibleRows++;
                } else {
                    tr[i].style.display = 'none';
                }
            }

            const noResultsRow = table.querySelector('tbody tr td[colspan="9"]');
            if (visibleRows === 0 && filter !== '') {
                if (!noResultsRow) {
                    const newRow = table.insertRow(-1);
                    const cell = newRow.insertCell(0);
                    cell.colSpan = 9;
                    cell.className = 'text-center text-light';
                    cell.textContent = 'Nenhuma rota encontrada.';
                }
            } else if (noResultsRow) {
                noResultsRow.parentElement.remove();
            }
        }

        function limparModalParaAdicionar() {
            document.getElementById('editarId').value = '';
            document.getElementById('editarItinerario').selectedIndex = 0;
            document.getElementById('editarMaquinista').selectedIndex = 0;
            document.getElementById('editarTrem').selectedIndex = 0;
            document.getElementById('editarEstacaoOrigem').selectedIndex = 0;
            document.getElementById('editarEstacaoDestino').selectedIndex = 0;
            document.getElementById('editarViaEstacao').selectedIndex = 0;
            document.getElementById('editarModalLabel').textContent = 'Adicionar Rota';
        }

        function editarRota(id, itinerarioId, maquinistaId, tremId, estacaoOrigemId, estacaoDestinoId, viaEstacaoId) {
            document.getElementById('editarId').value = id;
            document.getElementById('editarItinerario').value = itinerarioId;
            document.getElementById('editarMaquinista').value = maquinistaId;
            document.getElementById('editarTrem').value = tremId;
            document.getElementById('editarEstacaoOrigem').value = estacaoOrigemId;
            document.getElementById('editarEstacaoDestino').value = estacaoDestinoId;
            document.getElementById('editarViaEstacao').value = viaEstacaoId || '';
            document.getElementById('editarModalLabel').textContent = 'Editar Rota';
            const modal = new bootstrap.Modal(document.getElementById('editarModal'));
            modal.show();
        }

        document.getElementById('buscaInput').addEventListener('keyup', filtrarRotas);
        document.getElementById('editarModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button.textContent.includes('Adicionar')) {
                limparModalParaAdicionar();
            }
        });
    </script>
</body>

</html>