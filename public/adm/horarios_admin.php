<?php
session_start();
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
    exit;
}

// Verificação de papel de administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php");  // Redireciona para home se não for admin
    exit;
}

// Configuração de conexão com o banco de dados (use PDO para segurança)
$dsn = 'mysql:host=localhost;dbname=seu_banco_de_dados';
$username = 'seu_usuario';
$password = 'sua_senha';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Processamento de formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                // Adicionar novo horário
                $stmt = $pdo->prepare("INSERT INTO schedules (train_number, departure_station, arrival_station, departure_time, arrival_time) VALUES (:train_number, :departure_station, :arrival_station, :departure_time, :arrival_time)");
                $stmt->execute([
                    ':train_number' => $_POST['train_number'],
                    ':departure_station' => $_POST['departure_station'],
                    ':arrival_station' => $_POST['arrival_station'],
                    ':departure_time' => $_POST['departure_time'],
                    ':arrival_time' => $_POST['arrival_time']
                ]);
                break;
            
            case 'edit':
                // Editar horário existente
                $stmt = $pdo->prepare("UPDATE schedules SET train_number = :train_number, departure_station = :departure_station, arrival_station = :arrival_station, departure_time = :departure_time, arrival_time = :arrival_time WHERE id = :id");
                $stmt->execute([
                    ':train_number' => $_POST['train_number'],
                    ':departure_station' => $_POST['departure_station'],
                    ':arrival_station' => $_POST['arrival_station'],
                    ':departure_time' => $_POST['departure_time'],
                    ':arrival_time' => $_POST['arrival_time'],
                    ':id' => $_POST['id']
                ]);
                break;
            
            case 'delete':
                // Excluir horário
                $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                break;
        }
        header("Location: horarios_admin.php");  // Redireciona após a ação
        exit;
    }
}

// Buscar horários do banco de dados
$stmt = $pdo->query("SELECT * FROM schedules");
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Horários de Embarque (Admin)</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- CSS mínimo (com ajustes para a tabela) -->
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
        }
        .card-hover:hover {
            background-color: #dc3545 !important;
            color: #fff !important;
        }
        .card-hover:hover .text-muted {
            color: #f8f9fa !important;
        }
        .card-hover:hover a {
            color: #fff !important;
            text-decoration: underline;
        }
        .searchbar {
            background-color: #1e1e1e;
            border-radius: 0.5rem;
        }
        .search-icon {
            width: 24px;
            height: 24px;
            filter: brightness(0) invert(1);
            transition: filter 0.3s ease;
            cursor: pointer;
        }
        .search-icon:hover {
            filter: brightness(0) invert(0.7) sepia(1) saturate(5) hue-rotate(-10deg);
        }
        .pfp-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }
        .activity-img, .service-img {
            filter: brightness(0) invert(1);
        }
        .activity-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            padding: 0.5rem;
        }
        .service-img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 1rem;
        }
        .footer-icon img {
            width: 28px;
            height: 28px;
            filter: brightness(0) invert(1);
            transition: filter 0.3s ease;
        }
        .footer-icon:hover img,
        .footer-icon.active img {
            filter: brightness(0) invert(1)          
            drop-shadow(0 0 15px rgba(255, 193, 7, 0.8))
            sepia(1) saturate(5) hue-rotate(-10deg);
        }
        .rodape {
            background-color: #121212;
            border: none;
            box-shadow: none;
            z-index: 1000;
        }
        /* Estilo para links nos cards de serviço */
        .servico-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .servico-link:hover .card-servico {
            background-color: #dc3545 !important;
            color: #fff !important;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .servico-link:hover .servico-text {
            color: #fff !important;
        }
        @media (max-width: 768px) {
            .card-atividade {
                flex-direction: column !important;
                text-align: center;
                gap: 0.5rem;
            }
            .card-atividade .activity-img {
                width: 60px;
                height: 60px;
                margin-bottom: 0.5rem;
            }
            .card-atividade .card-text {
                padding: 0 !important;
                max-width: none;
            }
            .servicos-cards {
                flex-direction: column !important;
            }
            .card-servico {
                justify-content: center !important;
                text-align: center !important;
            }
        }
        @media (min-width: 769px) {
            .card-atividade {
                justify-content: center !important;
            }
            .card-atividade .card-text {
                max-width: 400px;
                text-align: center;
            }
        }
        /* Estilização aprimorada para tabela */
        .horarios-table {
            width: 100%;
            background-color: #1e1e1e;
            color: #ffffff;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            border: 1px solid #333;
        }
        .horarios-table th, .horarios-table td {
            padding: 1.2rem;
            text-align: center;
            color: #ffffff;
            background-color: #1e1e1e;
            border-color: #333;
        }
        .horarios-table th {
            background-color: #121212;
            font-weight: bold;
            text-transform: uppercase;
        }
        .horarios-table tr {
            background-color: #1e1e1e;
            transition: background-color 0.3s ease;
        }
        .horarios-table tr:hover {
            background-color: #dc3545;
            color: #ffffff;
        }
        .horarios-table.table-striped tbody tr:nth-of-type(odd) {
            background-color: #252525;
        }
        .horarios-table.table-striped tbody tr:nth-of-type(even) {
            background-color: #1e1e1e;
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
                        <h1 class="text-light fw-bold mb-0 fs-3">Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION['foto'] ?? '../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
        <!-- Searchbar -->
        <div class="searchbar d-flex justify-content-between align-items-center mx-3 mb-3 p-3">
            <div class="text-bar">
                <span class="fw-semibold fs-5 text-light">Para onde voce vai?</span>
            </div>
            <div class="img-bar">
                <img src="../assets/img/lupa.png" alt="Ícone de lupa para busca" class="search-icon" />
            </div>
        </div>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <!-- Formulário para adicionar novo horário -->
        <div class="mb-4">
            <h4 class="text-light">Adicionar Novo Horário</h4>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" name="train_number" class="form-control bg-dark text-white" placeholder="Número do Trem" required>
                    </div>
                    <div class="col">
                        <input type="text" name="departure_station" class="form-control bg-dark text-white" placeholder="Estação de Partida" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" name="arrival_station" class="form-control bg-dark text-white" placeholder="Estação de Chegada" required>
                    </div>
                    <div class="col">
                        <input type="time" name="departure_time" class="form-control bg-dark text-white" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <input type="time" name="arrival_time" class="form-control bg-dark text-white" required>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-danger">Adicionar</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Horários de Embarque -->
        <div class="horarios mb-5">
            <div class="horarios-titulo mb-3">
                <span class="text-danger fw-bold fs-4">Horários de Embarque</span>
            </div>
            <?php if (empty($schedules)): ?>
                <div class="alert alert-warning text-center" style="background-color: #1e1e1e; color: #ffffff;">
                    Nenhum horário disponível.
                </div>
            <?php else: ?>
                <table class="horarios-table table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Trem</th>
                            <th>Estação de Partida</th>
                            <th>Estação de Chegada</th>
                            <th>Hora de Partida</th>
                            <th>Hora de Chegada</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['train_number']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['departure_station']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['arrival_station']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['departure_time']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['arrival_time']); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?php echo $schedule['id']; ?>" data-train="<?php echo htmlspecialchars($schedule['train_number']); ?>" data-departure="<?php echo htmlspecialchars($schedule['departure_station']); ?>" data-arrival="<?php echo htmlspecialchars($schedule['arrival_station']); ?>" data-dep-time="<?php echo htmlspecialchars($schedule['departure_time']); ?>" data-arr-time="<?php echo htmlspecialchars($schedule['arrival_time']); ?>">Editar</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $schedule['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Modal para Editar -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Horário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" id="edit-id" name="id">
                            <div class="mb-3">
                                <input type="text" id="edit-train" name="train_number" class="form-control bg-dark text-white" placeholder="Número do Trem" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" id="edit-departure" name="departure_station" class="form-control bg-dark text-white" placeholder="Estação de Partida" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" id="edit-arrival" name="arrival_station" class="form-control bg-dark text-white"