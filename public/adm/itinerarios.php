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

// Total itinerarios
$stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM itinerarios");
$stmt_total->execute();
$total_itinerarios = $stmt_total->get_result()->fetch_assoc()['total'];

// Itinerarios por nome (exemplo: contagem de itinerarios com nomes específicos, ou simplesmente total por critério)
$stmt_por_nome = $conn->prepare("SELECT COUNT(*) as total FROM itinerarios WHERE nome LIKE '%Rota%'");  // Exemplo: ajuste com base em critérios reais
$stmt_por_nome->execute();
$itinerarios_por_nome = $stmt_por_nome->get_result()->fetch_assoc()['total'];

// Outra estatística: Itinerarios com descrição (exemplo: não vazia)
$stmt_com_descricao = $conn->prepare("SELECT COUNT(*) as total FROM itinerarios WHERE descricao IS NOT NULL AND descricao != ''");
$stmt_com_descricao->execute();
$itinerarios_com_descricao = $stmt_com_descricao->get_result()->fetch_assoc()['total'];


// Query para lista de itinerarios (com busca se $_GET['busca'] existir)
$busca = $_GET['busca'] ?? '';
$where_clause = !empty($busca) ? "WHERE (i.nome LIKE ? OR i.descricao LIKE ?)" : "";
$params = empty($busca) ? [] : ["%$busca%", "%$busca%"];
$types = empty($busca) ? "" : "ss";

$stmt_itinerarios = $conn->prepare("SELECT i.id, i.nome, i.descricao 
                                   FROM itinerarios i 
                                   $where_clause 
                                   ORDER BY i.id ASC 
                                   LIMIT 20"); // Paginação simples: 20 por página
if (!empty($busca)) {
    $stmt_itinerarios->bind_param($types, ...$params);
}
$stmt_itinerarios->execute();
$itinerarios = $stmt_itinerarios->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_total->close();
$stmt_por_nome->close();
$stmt_com_descricao->close();
$stmt_itinerarios->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Gerenciamento de Itinerários</title>
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
    </style>

</head>

<body>
    <!-- Header -->
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-oi">
                        <h1 class="text-light fw-bold mb-0 fs-3">Gerenciamento de Itinerários</h1>
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

        <!-- Busca e Tabela de Itinerários -->
        <div class="atividades mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="text-danger fw-bold fs-4 mb-0">Lista de Itinerários</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger" onclick="limparModalParaAdicionar()" data-bs-toggle="modal" data-bs-target="#editarModal" style="width: 300px;" >
                        <i class="bi bi-plus-circle me-1"></i> Adicionar Itinerário
                    </button>
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control" id="buscaInput" placeholder="Buscar por nome ou descrição..." value="<?php echo htmlspecialchars($busca); ?>">
                        <button class="btn btn-outline-danger" type="button" onclick="filtrarItinerarios()"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover rounded-3 overflow-hidden" id="itinerariosTable">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Descrição</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($itinerarios)): ?>
                            <?php foreach ($itinerarios as $itinerario): ?>
                                <tr data-nome="<?php echo strtolower($itinerario['nome']); ?>" data-descricao="<?php echo strtolower($itinerario['descricao']); ?>">
                                    <td><?php echo $itinerario['id']; ?></td>
                                    <td><?php echo htmlspecialchars($itinerario['nome']); ?></td>
                                    <td class="content-align-center" ><?php echo htmlspecialchars($itinerario['descricao']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1" onclick="editarItinerario(<?php echo $itinerario['id']; ?>, '<?php echo htmlspecialchars(addslashes($itinerario['nome'])); ?>', '<?php echo htmlspecialchars(addslashes($itinerario['descricao'])); ?>')">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>

                                        <form method="POST" action="model/itinerario.php?id=<?php echo $itinerario['id']; ?>" style="display: inline;" onsubmit="return confirm('Deletar este itinerário? Ação irreversível!');">
                                            <input type="hidden" name="id" value="<?php echo $itinerario['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" name="deletar" ><i class="bi bi-trash"></i> Deletar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-light">Nenhum itinerário encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal para Adicionar/Editar Itinerário -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="editarModalLabel">Adicionar Itinerário</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="editarForm" method="POST" action="model/itinerario.php">
                    <div class="modal-body">
                        <input type="hidden" id="editarId" name="id" value="">
                        <div class="mb-3">
                            <label for="editarNome" class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editarNome" name="nome" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="editarDescricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="editarDescricao" name="descricao" maxlength="255"></textarea>
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
        // Função para filtrar itinerarios na tabela (client-side)
        function filtrarItinerarios() {
            const input = document.getElementById('buscaInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('itinerariosTable');
            const tr = table.getElementsByTagName('tr');

            let visibleRows = 0;
            for (let i = 1; i < tr.length; i++) { // Pula o header (i=0)
                const nome = tr[i].getAttribute('data-nome') || '';
                const descricao = tr[i].getAttribute('data-descricao') || '';
                if (nome.includes(filter) || descricao.includes(filter)) {
                    tr[i].style.display = '';
                    visibleRows++;
                } else {
                    tr[i].style.display = 'none';
                }
            }

            // Atualiza mensagem se nenhum resultado
            const noResultsRow = table.querySelector('tbody tr td[colspan="4"]');
            if (visibleRows === 0 && filter !== '') {
                if (!noResultsRow || noResultsRow.textContent !== 'Nenhum itinerário encontrado.') {
                    const newRow = table.insertRow(-1);
                    const cell = newRow.insertCell(0);
                    cell.colSpan = 4;
                    cell.className = 'text-center text-light';
                    cell.textContent = 'Nenhum itinerário encontrado.';
                    newRow.style.display = '';
                }
            } else if (noResultsRow && filter === '') {
                // Remove mensagem se busca vazia e há resultados
                if (table.querySelector('tbody tr td[colspan="4"]')) {
                    table.querySelector('tbody tr td[colspan="4"]').parentElement.remove();
                }
            }
        }

        // Função para limpar modal ao adicionar novo itinerário
        function limparModalParaAdicionar() {
            // Limpa campos
            document.getElementById('editarId').value = ''; // ID vazio = INSERT no PHP
            document.getElementById('editarNome').value = ''; // Limpa input texto
            document.getElementById('editarDescricao').value = ''; // Limpa textarea

            // Atualiza título do modal para "Adicionar"
            document.getElementById('editarModalLabel').textContent = 'Adicionar Itinerário';

            // Opcional: Foca no primeiro campo para UX
            document.getElementById('editarNome').focus();
        }

        // Função para editar itinerário (popula modal)
        function editarItinerario(id, nome, descricao) {
            document.getElementById('editarId').value = id;
            document.getElementById('editarNome').value = nome;
            document.getElementById('editarDescricao').value = descricao;

            // Atualiza título para "Editar"
            document.getElementById('editarModalLabel').textContent = 'Editar Itinerário';

            const modal = new bootstrap.Modal(document.getElementById('editarModal'));
            modal.show();
        }

        // Event listener para busca em tempo real (opcional: digitação)
        document.getElementById('buscaInput').addEventListener('keyup', function() {
            filtrarItinerarios();
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