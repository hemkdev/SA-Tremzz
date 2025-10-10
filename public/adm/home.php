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

// Dados fictícios
$total_usuarios = 1250;
$total_mensagens = 4500;
$usuarios_hoje = 150;
$mensagens_hoje = 320;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Dashboard Administrativo</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- Bootstrap Icons para ícones opcionais -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Chart.js para gráficos (mínimo necessário para visualização) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS mínimo para cores de fundo, hovers e filtros (essencial para fidelidade) -->
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

        .card-hover:hover .text-muted {
            color: #f8f9fa !important;
        }

        .card-hover:hover a {
            color: #fff !important;
            text-decoration: underline;
        }

        .card-hover:hover .stat-number {
            color: #fff;
        }

        .pfp-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .stat-icon,
        .chart-icon {
            filter: brightness(0) invert(1);
            width: 40px;
            height: 40px;
            object-fit: contain;
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

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #dc3545;
        }

        .chart-container {
            position: relative;
            height: 300px;
            background-color: #1e1e1e;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .table-dark {
            background-color: #1e1e1e;
        }

        .table-dark th,
        .table-dark td {
            border-color: #333;
            color: #e0e0e0;
        }

        @media (max-width: 768px) {
            .stats-grid {
                flex-direction: column !important;
            }

            .stats-grid .card {
                margin-bottom: 1rem;
            }

            .chart-row {
                flex-direction: column !important;
            }

            .chart-row .col-md-6 {
                margin-bottom: 1rem;
            }
        }

        @media (min-width: 769px) {
            .stats-grid {
                gap: 1rem;
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
                        <h1 class="text-light fw-bold mb-0 fs-3">Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?> !</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION["foto"] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <!-- Estatísticas Principais -->
        <div class="stats mb-5">
            <div class="stats-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Dashboard Geral</h3>
            </div>

            <div class="stats-grid d-flex flex-wrap justify-content-between gap-3">

                <div class="card card-hover d-flex align-items-center rounded-3 flex-fill p-3">
                    <div class="stat-icon me-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-text">
                        <div class="stat-label text-light small">Total de Usuários</div>
                        <div class="stat-number"><?php echo $total_usuarios; ?></div>
                    </div>
                </div>

                <div class="card card-hover d-flex align-items-center rounded-3 flex-fill p-3">
                    <div class="stat-icon me-3">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                    <div class="stat-text">
                        <div class="stat-label text-light small">Total de Mensagens</div>
                        <div class="stat-number"><?php echo number_format($total_mensagens); ?></div>
                    </div>
                </div>

                <div class="card card-hover d-flex align-items-center rounded-3 flex-fill p-3">
                    <div class="stat-icon me-3">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="stat-text">
                        <div class="stat-label text-light small">Usuários Hoje</div>
                        <div class="stat-number"><?php echo $usuarios_hoje; ?></div>
                    </div>
                </div>

                <div class="card card-hover d-flex align-items-center rounded-3 flex-fill p-3">
                    <div class="stat-icon me-3">
                        <i class="bi bi-envelope-check-fill"></i>
                    </div>
                    <div class="stat-text">
                        <div class="stat-label text-light small">Mensagens Hoje</div>
                        <div class="stat-number"><?php echo $mensagens_hoje; ?></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Gráficos -->
        <div class="charts mt-5 mb-5">
            <div class="charts-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Gráficos de Atividade</h3>
            </div>
            <div class="chart-row row">
                <div class="col-md-6 mb-3">
                    <div class="card card-hover rounded-3 p-3">
                        <h5 class="text-light mb-3">Crescimento de Usuários</h5>
                        <div class="chart-container">
                            <canvas id="usuariosChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card card-hover rounded-3 p-3">
                        <h5 class="text-light mb-3">Mensagens por Dia</h5>
                        <div class="chart-container">
                            <canvas id="mensagensChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atividades Recentes -->
        <div class="atividades mt-5">
            <div class="atividades-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Atividades Recentes</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover rounded-3 overflow-hidden">
                    <thead>
                        <tr>
                            <th scope="col">Usuário</th>
                            <th scope="col">Ação</th>
                            <th scope="col">Data/Hora</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>João Silva</td>
                            <td>Enviou mensagem para Estação da Luz</td>
                            <td>2023-10-15 14:30</td>
                            <td><span class="badge bg-success">Concluída</span></td>
                        </tr>
                        <tr>
                            <td>Maria Oliveira</td>
                            <td>Consultou status do trem</td>
                            <td>2023-10-15 13:45</td>
                            <td><span class="badge bg-warning">Pendente</span></td>
                        </tr>
                        <tr>
                            <td>Pedro Santos</td>
                            <td>Login no sistema</td>
                            <td>2023-10-15 12:20</td>
                            <td><span class="badge bg-info">Ativa</span></td>
                        </tr>
                        <tr>
                            <td>Enzo Ronchi</td>
                            <td>Estorou o anel</td>
                            <td>2023-10-15 11:10</td>
                            <td><span class="badge bg-success">Concluída</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="container d-flex justify-content-around align-items-center" style="max-width: 900px;">
            <a href="home.php" class="footer-icon active text-center text-decoration-none p-2" aria-label="Início">
                <img src="../../assets/img/casa.png" alt="Início" />
            </a>
            <a href="gerenciamento.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Buscar">
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

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para Gráficos com Chart.js (dados fictícios) -->
    <script src="../../js/graficos_adm.js"> </script>
</body>

</html>