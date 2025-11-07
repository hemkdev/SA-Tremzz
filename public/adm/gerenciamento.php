<?php
session_start();
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: ../home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Gerenciamento</title>
    <link rel="shortcut icon" href="../../assets/img/tremzz_logo.png" />
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

    <!-- CSS mínimo para fundos exatos, hovers e filtros (essencial para fidelidade) -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            min-height: 100vh;
            padding-bottom: 70px;
        }

        .pfp-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .pfp-img:hover {
            border-color: #dc3545;
        }

        .card-gerenciamento {
            background-color: #1e1e1e;
            border-radius: 0.5rem;
            flex: 1 1 calc(50% - 0.75rem);
            max-width: calc(50% - 0.75rem);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            box-shadow: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-decoration: none;
            color: inherit;
            margin-bottom: 1rem;
            text-align: center;
            height: 180px;
            /* Altura maior para mais espaço harmonioso */
        }

        .card-gerenciamento:hover {
            background-color: #2a2a2a;
            color: #fff;
            transform: translateY(-5px);
            /* Efeito sutil de elevação */
        }

        .gerenciamento-icon {
            font-size: 3.5rem;
            /* Ícone maior para harmonia */
            color: #e0e0e0;
            /* Branco/cinza claro, não vermelho */
            margin-bottom: 1rem;
        }

        .gerenciamento-text h5 {
            color: #e0e0e0;
            font-weight: 600;
            font-size: 1.2rem;
            margin: 0 0 0.5rem 0;
        }

        .gerenciamento-text p {
            color: #b0b0b0;
            font-size: 0.95rem;
            margin: 0;
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

        /* Responsividade para mobile (stack vertical) */
        @media (max-width: 768px) {
            .card-gerenciamento {
                flex: 1 1 100% !important;
                max-width: 100% !important;
                padding: 2rem 1.25rem;
                height: 130px;
                /* Altura ajustada para mobile, ainda harmoniosa */
            }

            .gerenciamento-icon {
                font-size: 3rem;
            }

            .gerenciamento-text h5 {
                font-size: 1.1rem;
            }

            .gerenciamento-text p {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            header { padding-top: 1rem; }
            .d-flex.justify-content-between { flex-wrap: nowrap; }
            .text-oi { text-align: left; }
            .pfp { text-align: right; }
            .gerenciamento-titulo { text-align: center; width: 100%; }

            .gerenciamento-cards {
                padding: 0 1rem;
                display: grid !important;
                grid-template-columns: repeat(2, 1fr);
                gap: 0.6rem;
                align-items: stretch;
            }

            .card-gerenciamento {
                flex: unset !important;
                max-width: 100% !important;
                padding: 0.6rem 0.5rem;
                aspect-ratio: 1 / 1;
                height: auto;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .gerenciamento-icon {
                font-size: 2rem;
                margin-bottom: 0.6rem;
            }

            .gerenciamento-text h5 {
                font-size: 0.95rem;
                margin-bottom: 0.25rem;
            }

            .gerenciamento-text p {
                font-size: 0.8rem;
            }
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
                        <h1 class="header-titulo text-light fw-bold mb-0 fs-3">Gerenciamento</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION["foto"] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">

        <!-- Seção de gerenciamento -->
        <section class="gerenciamento-section">
            <div class="gerenciamento-titulo fw-bold fs-5 mb-4 text-danger">Gerencie o Sistema</div>
            <div class="gerenciamento-cards d-flex flex-wrap justify-content-between gap-3">

                <a href="sensores.php" class="card-gerenciamento" tabindex="0" aria-label="Gerenciar sensores">
                    <div class="gerenciamento-icon">
                        <i class="bi bi-thermometer-high"></i> <!-- Ícone para sensores -->
                    </div>
                    <div class="gerenciamento-text">
                        <h5>Sensores</h5>
                        <p>Monitorar e configurar sensores</p>
                    </div>
                </a>

                <a href="linhas.php" class="card-gerenciamento" tabindex="0" aria-label="Gerenciar linhas de trem">
                    <div class="gerenciamento-icon">
                        <i class="bi bi-train-freight-front"></i> <!-- Ícone para linhas -->
                    </div>
                    <div class="gerenciamento-text">
                        <h5>Linhas</h5>
                        <p>Editar linhas de trens</p>
                    </div>
                </a>

                <a href="itinerarios.php" class="card-gerenciamento" tabindex="0" aria-label="Gerenciar itinerários">
                    <div class="gerenciamento-icon">
                        <i class="bi bi-map"></i> <!-- Ícone para itinerários -->
                    </div>
                    <div class="gerenciamento-text">
                        <h5>Itinerários</h5>
                        <p>Configurar horários e itinerários</p>
                    </div>
                </a>

                <a href="trens.php" class="card-gerenciamento" tabindex="0" aria-label="Gerenciar trens">
                    <div class="gerenciamento-icon">
                        <i class="bi bi-train-front"></i> <!-- Ícone para trens -->
                    </div>
                    <div class="gerenciamento-text">
                        <h5>Trens</h5>
                        <p>Cadastrar e editar trens</p>
                    </div>
                </a>

                <a href="" class="card-gerenciamento" tabindex="0" aria-label="Gerenciar Chamados de Suporte">
                    <div class="gerenciamento-icon">
                        <i class="bi bi-chat-dots"></i> <!-- Ícone para Chamados de Suporte -->
                    </div>
                    <div class="gerenciamento-text">
                        <h5>Chamados de Suporte</h5>
                        <p>Gerenciar chamados de suporte</p>
                    </div>
                </a>

                <a href="manutencoes.php" class="card-gerenciamento" tabindex="0" aria-label="Gerenciar manutenção de trens">
                    <div class="gerenciamento-icon">
                        <i class="bi bi-tools"></i> <!-- Ícone para manutenção de trens -->
                    </div>
                    <div class="gerenciamento-text">
                        <h5>Manutenção de Trens</h5>
                        <p>Gerenciar manutenção de trens</p>
                    </div>
                </a>
                
            </div>
        </section>
    </main>

    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%); z-index: 1000;" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="home.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Início">
                <img src="../../assets/img/casa.png" alt="Início" />
            </a>
            <a href="gerenciamento.php" class="footer-icon active text-center text-decoration-none p-2" aria-label="Gerenciamento">
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
</body>

</html>