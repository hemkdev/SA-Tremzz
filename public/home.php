<?php
session_start();
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Home</title>
    <link rel="shortcut icon" href="../assets/img/tremlogo.png" />
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
                        <img src="../assets/img/perfil.png" alt="Foto de perfil" class="pfp-img" />
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
        <!-- Últimas atividades -->
        <div class="atividade mb-5">
            <div class="ult-atividade mb-3">
                <span class="text-danger fw-bold fs-4">Últimas atividades</span>
            </div>
            <div class="card-atividade card card-hover d-flex align-items-center rounded-3 mb-3 p-3 overflow-hidden">
                <div class="card-img flex-shrink-0">
                    <img src="../assets/img/localizacao.png" alt="Ícone local" class="activity-img" />
                </div>
                <div class="card-text ms-3 flex-grow-1">
                    <div class="card-titulo">
                        <a href="#" class="text-light fw-bold fs-5 text-decoration-none">Estação da Luz</a>
                    </div>
                    <div class="card-endereco mt-1">
                        <span class="text-light small d-block">Centro Histórico de São Paulo</span>
                        <span class="text-light small d-block">São Paulo - SP</span>
                    </div>
                </div>
            </div>
            <div class="card-atividade card card-hover d-flex align-items-center rounded-3 p-3 overflow-hidden">
                <div class="card-img flex-shrink-0">
                    <img src="../assets/img/localizacao.png" alt="Ícone local" class="activity-img" />
                </div>
                <div class="card-text ms-3 flex-grow-1">
                    <div class="card-titulo">
                        <a href="#" class="text-light fw-bold fs-5 text-decoration-none">Estação Japão</a>
                    </div>
                    <div class="card-endereco mt-1">
                        <span class="text-light small d-block">Bairro da Liberdade</span>
                        <span class="text-light small d-block">São Paulo - SP</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Serviços -->
        <div class="servicos mt-5">
            <div class="servicos-titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Outros serviços</h3>
            </div>
            <div class="servicos-cards d-flex flex-wrap justify-content-between gap-3">
                <div class="card-servico card card-hover d-flex align-items-center rounded-3 flex-fill p-3">
                    <div class="servico-img flex-shrink-0">
                        <img src="../assets/img/alerta.png" alt="Ícone de alerta" class="service-img" />
                    </div>
                    <div class="servico-text ms-3">
                        <span class="text-light fw-semibold fs-6">Alertas em tempo real</span>
                    </div>
                </div>
                <div class="card-servico card card-hover d-flex align-items-center rounded-3 flex-fill p-3">
                    <div class="servico-img flex-shrink-0">
                        <img src="../assets/img/trem.png" alt="Ícone de trem" class="service-img" />
                    </div>
                    <div class="servico-text ms-3">
                        <span class="text-light fw-semibold fs-6">Status do meu trem</span>
                    </div>
                </div>
                <div class="card-servico card card-hover d-flex align-items-center rounded-3 flex-fill p-3">
                    <div class="servico-img flex-shrink-0">
                        <img src="../assets/img/relogio.png" alt="Ícone de relógio" class="service-img" />
                    </div>
                    <div class="servico-text ms-3">
                        <span class="text-light fw-semibold fs-6">Horários de embarque</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%); z-index: 1000;" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="home.php" class="footer-icon active text-center text-decoration-none p-2" aria-label="Início">
                <img src="../assets/img/casa.png" alt="Início" />
            </a>
            <a href="buscar.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Buscar">
                <img src="../assets/img/lupa.png" alt="Buscar" />
            </a>
            <a href="chat.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Chat">
                <img src="../assets/img/chat.png" alt="Chat" />
            </a>
            <a href="perfil.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Perfil">
                <img src="../assets/img/perfil.png" alt="Perfil" />
            </a>
        </div>
    </footer>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

