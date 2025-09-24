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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 70px;
            /* espaço para rodapé fixo */
        }

        header nav .top {
            padding: 1rem 1.5rem;
        }

        header nav .text-oi h1 {
            margin: 0;
            font-weight: 700;
            font-size: 1.75rem;
            color: #fff;
        }

        header nav .pfp img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        header nav .pfp img:hover {
            border-color: #dc3545;
        }

        main {
            max-width: 900px;
            margin: 0 auto 2rem;
            padding: 0 1rem;
            flex: 1;
        }

        /* Seção de perfil principal */
        .perfil-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background-color: #1e1e1e;
            border-radius: 0.5rem;
        }

        .perfil-foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .perfil-nome {
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .perfil-email {
            font-size: 1rem;
            color: #b0b0b0;
            margin-bottom: 1rem;
        }

        .perfil-editar {
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .perfil-editar:hover {
            color: #a71d2a;
        }

        /* Cards de opções */
        .opcoes-titulo {
            color: #dc3545;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .opcoes-cards {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: space-between;
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

        /* Rodapé */
        footer.rodape {
            background-color: #121212;
            padding: 0.75rem 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            position: fixed;
            bottom: 0;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 -2px 8px rgba(220, 53, 69, 0.3); /* Adicionado para consistência */
        }

        footer.rodape a img {
            width: 28px;
            height: 28px;
            filter: brightness(0) invert(1);
            transition: filter 0.3s ease;
        }

        footer.rodape a:hover img {
            filter: brightness(0) invert(0.7) sepia(1) saturate(5) hue-rotate(-10deg);
        }

        footer.rodape a.active img {
            filter: brightness(0) invert(0.7) sepia(1) saturate(5) hue-rotate(-10deg);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .perfil-foto {
                width: 100px;
                height: 100px;
            }

            .perfil-nome {
                font-size: 1.3rem;
            }

            .opcoes-cards {
                flex-direction: column;
            }

            .card-opcao {
                max-width: 100%;
                flex-direction: column; /* Alterado: empilha ícone e texto verticalmente */
                align-items: center; /* Centraliza horizontalmente no eixo principal (agora coluna) */
                justify-content: center; /* Centraliza verticalmente */
                text-align: center;
                padding: 1.5rem 1rem; /* Aumentado padding para melhor espaçamento em mobile */
            }

            .opcao-img img {
                margin-right: 0;
                margin-bottom: 0.75rem; /* Aumentado para melhor separação do ícone e texto */
                width: 48px; /* Aumentado tamanho do ícone em mobile para melhor visibilidade */
                height: 48px;
            }

            .opcao-text {
                width: 100%; /* Garante que o texto ocupe a largura total e centralize */
            }

            .opcao-text h5,
            .opcao-text p {
                text-align: center;
            }

            footer.rodape {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <div class="top d-flex justify-content-between align-items-center">
                <div class="text-oi">
                    <h1>Perfil</h1>
                </div>
                <div class="pfp">
                    <img src="../assets/img/perfil.png" alt="Foto de perfil" />
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="perfil-header">
            <img src="../assets/img/perfil.png" alt="Foto de perfil" class="perfil-foto" />
            <div class="perfil-nome">
                <?php echo htmlspecialchars($_SESSION['nome']); ?>
            </div>
            <div class="perfil-email">
                <?php echo htmlspecialchars($_SESSION['email']); ?>
            </div>
            <a href="#" class="perfil-editar" tabindex="0">Editar Perfil</a>
        </section>

        <section class="opcoes">
            <div class="opcoes-titulo">Opções</div>
            <div class="opcoes-cards">
                <a href="#" class="card-opcao" tabindex="0" aria-label="Editar informações do perfil">
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
                <a href="#" class="card-opcao" tabindex="0" aria-label="Configurações de privacidade">
                    <div class="opcao-img">
                        <img src="../assets/img/seguranca.png" alt="Ícone de segurança" /> 
                    </div>
                    <div class="opcao-text">
                        <h5>Privacidade e Segurança</h5>
                        <p>Proteja sua conta</p>
                    </div>
                </a>
                <a href="#" class="card-opcao" tabindex="0" aria-label="Sobre o aplicativo TREMzz">
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
                <a href="#" class="card-opcao" tabindex="0" aria-label="Sair da conta">
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

    <footer class="rodape" role="contentinfo" aria-label="Menu de navegação inferior">
        <a href="home.php" aria-label="Início">
            <img src="../assets/img/casa.png" alt="Início" />
        </a>
        <a href="buscar.php" aria-label="Buscar">
            <img src="../assets/img/lupa.png" alt="Buscar" />
        </a>
        <a href="chat.php" aria-label="Chat">
            <img src="../assets/img/chat.png" alt="Chat" />
        </a>
        <a href="tperfil.php" class="active" aria-label="Perfil">
            <img src="../assets/img/perfil.png" alt="Perfil" />
        </a>
    </footer>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>