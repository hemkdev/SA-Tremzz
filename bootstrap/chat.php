<?php
require "../config/bd.php";
session_start();
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['id'];

 $stmt = $conn->prepare("SELECT * FROM mensagens WHERE usuario_id = ? ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $mensagens = $stmt->get_result();



?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Conversas</title>
    <link rel="shortcut icon" href="../img/tremlogo.png" />
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
    
    <!-- CSS mínimo para fundos exatos, filtros de ícones e hovers essenciais (Bootstrap não suporta nativamente #121212 ou filter invert) -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            min-height: 100vh;
            padding-bottom: 70px;
        }
        .bg-custom {
            background-color: #1e1e1e !important;
        }
        .bg-custom-hover:hover {
            background-color: #2a2a2a !important;
            color: #fff !important;
        }

        .chat-icon {
            width: 56px;
            height: 56px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
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
        .pfp-img:hover {
            transform: scale(1.2);
            transition: transform 0.5s ease;
        }
        .unread-dot {
            position: absolute;
            top: 12px;
            left: 44px;
            width: 12px;
            height: 12px;
            background-color: #dc3545;
            border-radius: 50%;
            border: 2px solid #121212;
            pointer-events: none;
        }
        .footer-icon img {
            width: 28px;
            height: 28px;
            filter: brightness(0) invert(1);
            transition: filter 0.3s ease;
        }
        .footer-icon:hover img, .footer-icon.active img {
            filter: brightness(0) invert(0.7) sepia(1) saturate(5) hue-rotate(-10deg);
        }
        /* Responsividade mínima para mobile (Bootstrap cuida do resto) */
        @media (max-width: 768px) {
            .chat-icon {
                width: 48px;
                height: 48px;
                margin-right: 0.75rem;
            }
        }
    </style>
</head>

<body class="text-light">
    <!-- Header -->
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-oi">
                        <h1 class="text-light fw-bold mb-0 fs-3">Conversas</h1>
                    </div>
                    <div class="pfp">
                        <img src="../assets/img/perfil.png" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
        <!-- Searchbar -->
        <div class="searchbar bg-custom d-flex justify-content-between align-items-center mx-3 mb-3 p-3 rounded-3">
            <div class="text-bar">
                <span class="fw-semibold fs-5 text-light">Buscar conversas</span>
            </div>
            <div class="img-bar">
                <img src="../assets/img/lupa.png" alt="Ícone de lupa para busca" class="search-icon" />
            </div>
        </div>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem; overflow-y: auto;">
        <div class="list-group list-group-flush">

            <!-- Estação da Luz -->
            <a href="#" class="list-group-item list-group-item-action bg-custom bg-custom-hover d-flex align-items-center rounded-3 mb-3 p-3 text-decoration-none active border-0" tabindex="0" aria-current="true" aria-label="Chat com Estação da Luz, última mensagem: Trem atrasado, 2 mensagens não lidas" style="transition: background-color 0.2s ease;">
                <div class="position-relative flex-shrink-0 me-3">
                    <img src="../assets/img/local.png" alt="Avatar Estação da Luz" class="chat-icon" />
                    <span class="unread-dot"></span>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="chat-name fw-bold fs-6 text-light mb-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Estação da Luz</div>
                    <div class="chat-last-message text-light small" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                       <?php echo htmlspecialchars($mensagem['texto']);   ?>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-end ms-3 flex-shrink-0" style="min-width: 60px;">
                    <div class="chat-time text-light small mb-2" style="white-space: nowrap;">10:45</div>
                    <span class="badge bg-danger rounded-pill" aria-label="2 mensagens não lidas" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; min-width: 24px; text-align: center;">2</span>
                </div>
            </a>

            <!-- Estação Japão -->
            <a href="#" class="list-group-item list-group-item-action bg-custom bg-custom-hover d-flex align-items-center rounded-3 mb-3 p-3 text-decoration-none border-0" tabindex="0" aria-label="Chat com Estação Japão, última mensagem: Tudo funcionando normalmente" style="transition: background-color 0.2s ease;">
                <div class="flex-shrink-0 me-3">
                    <img src="../assets/img/local.png" alt="Avatar Estação Japão" class="chat-icon" />
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="chat-name fw-bold fs-6 text-light mb-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Estação Japão</div>
                    <div class="chat-last-message text-light small" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Tudo funcionando normalmente</div>
                </div>
                <div class="d-flex flex-column align-items-end ms-3 flex-shrink-0" style="min-width: 60px;">
                    <div class="chat-time text-light small" style="white-space: nowrap;">09:30</div>
                </div>
            </a>

            <!-- Chat com Suporte -->
            <a href="#" class="list-group-item list-group-item-action bg-custom bg-custom-hover d-flex align-items-center rounded-3 mb-3 p-3 text-decoration-none border-0" tabindex="0" aria-label="Chat com Suporte, última mensagem: Como posso ajudar?" style="transition: background-color 0.2s ease;">
                <div class="flex-shrink-0 me-3">
                    <img src="../assets/img/chat.png" alt="Avatar Suporte" class="chat-icon" />
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="chat-name fw-bold fs-6 text-light mb-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Suporte</div>
                    <div class="chat-last-message text-light small" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Como posso ajudar?</div>
                </div>
                <div class="d-flex flex-column align-items-end ms-3 flex-shrink-0" style="min-width: 60px;">
                    <div class="chat-time text-light small" style="white-space: nowrap;">Ontem</div>
                </div>
            </a>

            <!-- Chat com Status do Trem -->
            <a href="#" class="list-group-item list-group-item-action bg-custom bg-custom-hover d-flex align-items-center rounded-3 mb-3 p-3 text-decoration-none border-0" tabindex="0" aria-label="Chat com Status do Trem, última mensagem: Seu trem está a caminho" style="transition: background-color 0.2s ease;">
                <div class="flex-shrink-0 me-3">
                    <img src="../assets/img/trem.png" alt="Avatar Status do Trem" class="chat-icon" />
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="chat-name fw-bold fs-6 text-light mb-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Status do Trem</div>
                    <div class="chat-last-message text-light small" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                      <?php echo htmlspecialchars($_SESSION['nome']); ?>, seu trem está a caminho!
                    </div>
                </div>
                <div class="d-flex flex-column align-items-end ms-3 flex-shrink-0" style="min-width: 60px;">
                    <div class="chat-time text-light small" style="white-space: nowrap;">Seg</div>
                </div>
            </a>
        </div>
    </main>
    
    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%); z-index: 1000;" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="home.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Início">
                <img src="../assets/img/casa.png" alt="Início" />
            </a>
            <a href="buscar.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Buscar">
                <img src="../assets/img/lupa.png" alt="Buscar" />
            </a>
            <a href="chat.php" class="footer-icon active text-center text-decoration-none p-2" aria-label="Chat">
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

