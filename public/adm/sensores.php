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

// Total sensores
$stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM sensores");
$stmt_total->execute();
$total_sensores = $stmt_total->get_result()->fetch_assoc()['total'];

// Sensores por tipo
$stmt_ldr = $conn->prepare("SELECT COUNT(*) as total FROM sensores WHERE tipo = 'LDR'");
$stmt_ldr->execute();
$sensores_ldr = $stmt_ldr->get_result()->fetch_assoc()['total'];

$stmt_ultrassonico = $conn->prepare("SELECT COUNT(*) as total FROM sensores WHERE tipo = 'Ultrassônico'");
$stmt_ultrassonico->execute();
$sensores_ultrassonico = $stmt_ultrassonico->get_result()->fetch_assoc()['total'];

$stmt_dht11 = $conn->prepare("SELECT COUNT(*) as total FROM sensores WHERE tipo = 'DHT11'");
$stmt_dht11->execute();
$sensores_dht11 = $stmt_dht11->get_result()->fetch_assoc()['total'];




// Query para lista de sensores
$busca = $_GET['busca'] ?? '';
$where_clause = !empty($busca) ? "WHERE (localizacao LIKE ? OR tipo LIKE ?)" : "";
$params = empty($busca) ? [] : ["%$busca%", "%$busca%"];
$types = empty($busca) ? "" : "ss";

$stmt_sensores = $conn->prepare("SELECT id, localizacao, tipo, data_hora_adicao 
                                     FROM sensores $where_clause 
                                     ORDER BY id DESC 
                                     LIMIT 20"); // Paginação simples: 20 por página
if (!empty($busca)) {
    $stmt_sensores->bind_param($types, ...$params);
}
$stmt_sensores->execute();
$sensores = $stmt_sensores->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_total->close();
$stmt_ldr->close();
$stmt_ultrassonico->close();
$stmt_dht11->close();
$stmt_sensores->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Gerenciamento de Sensores</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
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
                        <h1 class="text-light fw-bold mb-0 fs-3">Gerenciamento de Sensores</h1>
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

        <!-- Estatísticas de Sensores -->
        <div class="stats mb-5">
            <div class="stats-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Dados de Sensores</h3>
            </div>

            <div class="stats-grid d-flex flex-wrap justify-content-between gap-3">
                <!-- Card 1: Total de Sensores -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-list"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Total de Sensores</div>
                        <div class="stat-number"><?php echo number_format($total_sensores); ?></div>
                    </div>
                </div>

                <!-- Card 2: Sensores LDR -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-brightness-high"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Sensores LDR</div>
                        <div class="stat-number"><?php echo number_format($sensores_ldr); ?></div>
                    </div>
                </div>

                <!-- Card 3: Sensores Ultrassônico -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-soundwave"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Sensores Ultrassônicos</div>
                        <div class="stat-number"><?php echo number_format($sensores_ultrassonico); ?></div>
                    </div>
                </div>

                <!-- Card 4: Sensores DHT11 -->
                <div class="card card-hover d-flex flex-column align-items-center text-center rounded-3 flex-fill p-3">
                    <div class="stat-icon mb-2">
                        <i class="bi bi-thermometer"></i>
                    </div>
                    <div class="stat-text w-100">
                        <div class="stat-label text-light small">Sensores DHT11</div>
                        <div class="stat-number"><?php echo number_format($sensores_dht11); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Busca e Tabela de Sensores -->
        <div class="atividades mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-danger fw-bold fs-4 mb-0">Lista de Sensores</h3>
                <div class="d-flex gap-2"> <!-- Container para botão + busca, alinhados à direita -->
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editarModal">
                        <i class="bi bi-plus-circle me-1"></i> Adicionar Sensor
                    </button>
                    <div class="input-group" style="max-width: 250px;">
                        <input type="text" class="form-control" id="buscaInput" placeholder="Buscar por localização ou tipo..." value="<?php echo htmlspecialchars($busca); ?>">
                        <button class="btn btn-outline-danger" type="button" onclick="filtrarSensores()"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover rounded-3 overflow-hidden" id="sensoresTable">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Localização</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Data de Adição</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($sensores)): ?>
                            <?php foreach ($sensores as $sensor): ?>
                                <tr data-localizacao="<?php echo strtolower($sensor['localizacao']); ?>" data-tipo="<?php echo strtolower($sensor['tipo']); ?>">
                                    <td><?php echo $sensor['id']; ?></td>
                                    <td><?php echo htmlspecialchars($sensor['localizacao']); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['tipo']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($sensor['data_hora_adicao'])); ?></td>

                                    <td>
                                        <button class="btn btn-sm btn-warning me-1" onclick="editarSensor(<?php echo $sensor['id']; ?>, '<?php echo htmlspecialchars(addslashes($sensor['localizacao'])); ?>', '<?php echo htmlspecialchars(addslashes($sensor['tipo'])); ?>')">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>

                                        <form method="POST" action="model/sensor.php?id=<?php echo $sensor['id']; ?>" style="display: inline;" onsubmit="return confirm('Deletar este sensor? Ação irreversível!');">
                                            <input type="hidden" name="id" value="<?php echo $sensor['id']; ?>">
                                            <button type="submit" name="deletar" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Deletar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-light">Nenhum sensor encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação simples (comentada, igual ao original) -->
            <!-- <nav aria-label="Paginação de sensores">
                <ul class="pagination justify-content-center">
                    <li class="page-item"><a class="page-link bg-secondary text-light border-0" href="#">Anterior</a></li>
                    <li class="page-item"><a class="page-link bg-danger text-light border-0" href="#">1</a></li>
                    <li class="page-item"><a class="page-link bg-secondary text-light border-0" href="#">Próximo</a></li>
                </ul>
            </nav> -->
        </div>
    </main>

    <!-- Modal para Editar Sensor -->
    <!-- Modal para Adicionar/Editar Sensor -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="editarModalLabel">Adicionar Sensor</h5> <!-- Título default para add; JS muda para edit -->
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="editarForm" method="POST" action="model/sensor.php">
                    <div class="modal-body">
                        <input type="hidden" id="editarId" name="id" value="">
                        <div class="mb-3">
                            <label for="editarLocalizacao" class="form-label">Localização <span class="text-danger">*</span></label>
                            <select class="form-select" id="editarLocalizacao" name="localizacao" required>
                                <option value="">Selecione localização</option>
                                <option value="Estação 1">Estação 1</option>
                                <option value="Estação 2">Estação 2</option>
                                <option value="Estação 3">Estação 3</option>
                                <option value="Estação principal">Estação principal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editarTipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select" id="editarTipo" name="tipo" required>
                                <option value="">Selecione tipo</option>
                                <option value="LDR">LDR</option>
                                <option value="Ultrassônico">Ultrassônico</option>
                                <option value="DHT11">DHT11</option>
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
        // Função para filtrar sensores na tabela (client-side)
        function filtrarSensores() {
            const input = document.getElementById('buscaInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('sensoresTable');
            const tr = table.getElementsByTagName('tr');

            let visibleRows = 0;
            for (let i = 1; i < tr.length; i++) { // Pula o header (i=0)
                const localizacao = tr[i].getAttribute('data-localizacao') || '';
                const tipo = tr[i].getAttribute('data-tipo') || '';
                if (localizacao.includes(filter) || tipo.includes(filter)) {
                    tr[i].style.display = '';
                    visibleRows++;
                } else {
                    tr[i].style.display = 'none';
                }
            }

            // Atualiza mensagem se nenhum resultado
            const noResultsRow = table.querySelector('tbody tr td[colspan="5"]');
            if (visibleRows === 0 && filter !== '') {
                if (!noResultsRow || noResultsRow.textContent !== 'Nenhum sensor encontrado.') {
                    const newRow = table.insertRow(-1);
                    const cell = newRow.insertCell(0);
                    cell.colSpan = 5;
                    cell.className = 'text-center text-light';
                    cell.textContent = 'Nenhum sensor encontrado.';
                    newRow.style.display = '';
                }
            } else if (noResultsRow && filter === '') {
                // Remove mensagem se busca vazia e há resultados
                if (table.querySelector('tbody tr td[colspan="5"]')) {
                    table.querySelector('tbody tr td[colspan="5"]').parentElement.remove();
                }
            }
        }

        // Função para limpar modal ao adicionar novo sensor
        function limparModalParaAdicionar() {
            // Limpa campos
            document.getElementById('editarId').value = ''; // ID vazio = INSERT no PHP
            document.getElementById('editarLocalizacao').value = ''; // Seleciona placeholder vazio
            document.getElementById('editarTipo').value = ''; // Seleciona placeholder vazio

            // Atualiza título do modal para "Adicionar"
            document.getElementById('editarModalLabel').textContent = 'Adicionar Sensor';

            // Opcional: Foca no primeiro campo para UX
            document.getElementById('editarLocalizacao').focus();
        }

        // Opcional: Use evento do Bootstrap para reset automático em todo show do modal (se quiser mais robusto)
        document.getElementById('editarModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Botão que abriu o modal
            const isAdd = button && button.textContent.includes('Adicionar'); // Detecta se é o botão de add
            if (isAdd) {
                limparModalParaAdicionar(); // Limpa se for adição
            }
        });

        // Função para editar sensor
        function editarSensor(id, localizacao, tipo) {
            document.getElementById('editarId').value = id;
            document.getElementById('editarLocalizacao').value = localizacao; // Seleciona o valor exato
            document.getElementById('editarTipo').value = tipo; // Seleciona o valor exato

            // Atualiza título para "Editar"
            document.getElementById('editarModalLabel').textContent = 'Editar Sensor';

            const modal = new bootstrap.Modal(document.getElementById('editarModal'));
            modal.show();
        }

        // Event listener para busca em tempo real (opcional: digitação)
        // Event listener para reset automático no show do modal (detecta origem)
        document.getElementById('editarModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Botão que abriu o modal
            const isAddButton = button && (button.onclick && button.onclick.toString().includes('limparModalParaAdicionar')); // Detecta se é o botão de add
            if (isAddButton) {
                limparModalParaAdicionar(); // Limpa se for adição
            }
        });
    </script>

</body>

</html>