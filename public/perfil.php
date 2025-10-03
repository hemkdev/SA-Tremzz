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
    <title>TREMzz - Perfil</title>
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

        .perfil-foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 1rem;
        }
        
        #quadrado {
            background-color: #1e1e1e;
        }

        .card-opcao {
            background-color: #1e1e1e;
            border-radius: 0.5rem;
            flex: 1 1 calc(50% - 0.5rem);
            max-width: calc(50% - 0.5rem);
            display: flex;
            align-items: center;
            padding: 1rem;
            box-shadow: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: inherit;
            margin-bottom: 1rem;
        }

        .card-opcao:hover {
            background-color: #2a2a2a;
            color: #fff;
        }

        .opcao-img img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 1rem;
            filter: brightness(0) invert(1);
        }

        .opcao-text h5 {
            color: #e0e0e0;
            font-weight: 600;
            font-size: 1rem;
            margin: 0 0 0.25rem 0;
        }

        .opcao-text p {
            color: #b0b0b0;
            font-size: 0.85rem;
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

        /* Responsividade para mobile (mantendo alinhamento à esquerda, sem stack vertical) */
        @media (max-width: 768px) {
            .perfil-foto {
                width: 100px;
                height: 100px;
            }

            .card-opcao {
                flex: 1 1 100% !important;
                max-width: 100% !important;
                padding: 1.25rem;
            }

            .opcao-img img {
                width: 36px;
                height: 36px;
                margin-right: 0.75rem;
            }

            .opcao-text h5 {
                font-size: 1.1rem;
            }

            .opcao-text p {
                font-size: 0.9rem;
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
                        <h1 class="text-light fw-bold mb-0 fs-3">Perfil</h1>
                    </div>
                    <div class="pfp">
                        <img src="../assets/img/perfil.png" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">

        <!-- Seção de perfil principal -->
        <section class="perfil-header card rounded-3 text-center mb-4 p-4" id="quadrado">
            <img src="../assets/img/perfil.png" alt="Foto de perfil" class="perfil-foto" />
            <div class="perfil-nome fw-bold fs-4 text-light mb-2">
                <?php echo htmlspecialchars($_SESSION['nome']); ?>
            </div>
            <div class="perfil-email text-light fs-6 mb-3">
                <?php echo htmlspecialchars($_SESSION['email']); ?>
            </div>
        </section>

        <!-- Opções -->
        <section class="opcoes">
            <div class="opcoes-titulo text-danger fw-bold fs-5 mb-3">Opções</div>
            <div class="opcoes-cards d-flex flex-wrap justify-content-between gap-3">
                <a href="editarperfil.php" class="card-opcao" tabindex="0" aria-label="Editar informações do perfil">
                    <div class="opcao-img">
                        <img src="../assets/img/perfil.png" alt="Ícone de perfil" />
                    </div>
                    <div class="opcao-text">
                        <h5>Editar Informações</h5>
                        <p>Nome, email e foto</p>
                    </div>
                </a>
                <a href="#" class="card-opcao" tabindex="0" aria-label="Ajustes de notificações">
                    <div class="opcao-img">
                        <img src="../assets/img/alerta.png" alt="Ícone de alerta" />
                    </div>
                    <div class="opcao-text">
                        <h5>Notificações</h5>
                        <p>Gerencie alertas e avisos</p>
                    </div>
                </a>
                <a href="privacidade.php" class="card-opcao" tabindex="0" aria-label="Configurações de privacidade">
                    <div class="opcao-img">
                        <img src="../assets/img/seguranca.png" alt="Ícone de segurança" />
                    </div>
                    <div class="opcao-text">
                        <h5>Privacidade e Segurança</h5>
                        <p>Proteja sua conta</p>
                    </div>
                </a>
                <a href="sobre.php" class="card-opcao" tabindex="0" aria-label="Sobre o aplicativo TREMzz">
                    <div class="opcao-img">
                        <img src="../assets/img/info.png" alt="Ícone de informação" />
                    </div>
                    <div class="opcao-text">
                        <h5>Sobre o TREMzz</h5>
                        <p>Versão e informações</p>
                    </div>
                </a>
                <a href="#" class="card-opcao" tabindex="0" aria-label="Ajuda e suporte">
                    <div class="opcao-img">
                        <img src="../assets/img/chat.png" alt="Ícone de chat" />
                    </div>
                    <div class="opcao-text">
                        <h5>Ajuda e Suporte</h5>
                        <p>Entre em contato conosco</p>
                    </div>
                </a>
                <a name="logout" href="logout.php" class="card-opcao" tabindex="0" aria-label="Sair da conta" >
                    <div class="opcao-img">
                        <img src="../assets/img/sair.png" alt="Ícone de saída" />
                    </div>
                    <div class="opcao-text">
                        <h5>Sair da Conta</h5>
                        <p>Faça logout</p>
                    </div>
                </a>
            </div>
        </section>
    </main>

    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%); z-index: 1000;" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="home.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Início">
                <img src="../assets/img/casa.png" alt="Início" />
            </a>
            <a href="buscar.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Buscar">
                <img src="../assets/img/lupa.png" alt="Buscar" />
            </a>
            <a href="chat.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Chat">
                <img src="../assets/img/chat.png" alt="Chat" />
            </a>
            <a href="tperfil.php" class="footer-icon active text-center text-decoration-none p-2" aria-label="Perfil">
                <img src="../assets/img/perfil.png" alt="Perfil" />
            </a>
        </div>
    </footer>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>