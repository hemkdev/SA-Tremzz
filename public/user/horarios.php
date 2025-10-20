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
    <title>TREMzz - Horários de Embarque</title>
    <link rel="shortcut icon" href="../../assets/img/tremzz_logo.png" />
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
        /* Small-screen header tweaks: keep header layout, add small top padding, center red subtitles only */
        @media (max-width: 480px) {
            .navbar { padding-top: .6rem !important; padding-bottom: .25rem !important; }
            .text-oi { text-align: left; }
            .pfp { text-align: right; }
            .text-danger.fw-bold.fs-4,
            .text-danger.fw-bold.fs-5,
            .text-danger.fw-bold.fs-3 { text-align: center; display: block; width: 100%; }
        }
        /* Estilização aprimorada para tabela */
        .horarios-table {
            width: 100%;
            background-color: #1e1e1e;  /* Fundo escuro */
            color: #ffffff;  /* Texto branco */
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);  /* Sombra para profundidade */
            border: 1px solid #333;  /* Borda sutil */
        }
        .horarios-table th, .horarios-table td {
            padding: 1.2rem;  /* Mais espaço para melhor visualização */
            text-align: center;
            color: #ffffff;
            background-color: #1e1e1e;  /* Fundo escuro para células */
            border-color: #333;  /* Borda escura */
        }
        .horarios-table th {
            background-color: #121212;  /* Fundo mais escuro para cabeçalho */
            font-weight: bold;
            text-transform: uppercase;  /* Letras maiúsculas para destaque */
        }
        .horarios-table tr {
            background-color: #1e1e1e;
            transition: background-color 0.3s ease;  /* Transição suave no hover */
        }
        .horarios-table tr:hover {
            background-color: #dc3545;  /* Cor de hover */
            color: #ffffff;
        }
        .horarios-table.table-striped tbody tr:nth-of-type(odd) {
            background-color: #252525;  /* Tom ligeiramente diferente para listras */
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
                        <img src="<?php echo htmlspecialchars($_SESSION['foto'] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
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
                <img src="../../assets/img/lupa.png" alt="Ícone de lupa para busca" class="search-icon" />
            </div>
        </div>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <!-- Horários de Embarque -->
        <div class="horarios mb-5">
            <div class="horarios-titulo mb-3">
                <span class="text-danger fw-bold fs-4">Horários de Embarque</span>
            </div>
            <table class="horarios-table table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Trem</th>
                        <th>Estação de Partida</th>
                        <th>Estação de Chegada</th>
                        <th>Hora de Partida</th>
                        <th>Hora de Chegada</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Trem 101</td>
                        <td>Estação da Luz</td>
                        <td>Centro Histórico de São Paulo</td>
                        <td>08:00 AM</td>
                        <td>09:15 AM</td>
                    </tr>
                    <tr>
                        <td>Trem 202</td>
                        <td>Estação Japão</td>
                        <td>Bairro da Liberdade</td>
                        <td>10:30 AM</td>
                        <td>11:45 AM</td>
                    </tr>
                    <tr>
                        <td>Trem 303</td>
                        <td>Estação Central</td>
                        <td>Estação Sul</td>
                        <td>12:00 PM</td>
                        <td>01:00 PM</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%); z-index: 1000;" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="home.php" class="footer-icon active text-center text-decoration-none p-2" aria-label="Início">
                <img src="../../assets/img/casa.png" alt="Início" />
            </a>
            <a href="buscar.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Buscar">
                <img src="../../assets/img/lupa.png" alt="Buscar" />
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
</body>

</html>
