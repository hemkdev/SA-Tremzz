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
    <title>TREMzz - Sobre Nós</title>
    <link rel="shortcut icon" href="../img/tremlogo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    
    <!-- CSS mínimo para tema escuro e título vermelho suave -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            min-height: 100vh;
            padding-bottom: 70px;
        }
        .sobre-titulo {
            color: #b02a37; /* Vermelho suave, menos chamativo que #dc3545 */
            font-weight: 600;
        }
        .foto-placeholder {
            background-color: #1e1e1e;
            border-radius: 0.5rem;
            height: 300px; /* Altura fixa para ocupar quase todo o espaço vertical disponível */
            width: 100%; /* Largura total, mesma do texto */
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 1.2rem;
        }
        .texto-sobre {
            background-color: #1e1e1e;
            border-radius: 0.5rem;
            padding: 1.5rem;
            line-height: 1.6;
            width: 100%; /* Largura total, alinhada com a foto */
        }
        /* Footer estilos (mínimos, como no dashboard) */
        .rodape {
            background-color: #121212;
            border: none;
            box-shadow: none;
            z-index: 1000;
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
    </style>
</head>

<body>
    <main class="container px-3" style="max-width: 900px; margin: 0 auto; padding-top: 2rem;">
        <!-- Título -->
        <div class="row justify-content-center mb-5">
            <div class="col-12 text-center">
                <h2 class="sobre-titulo fs-2 mb-0">Sobre Nós</h2>
            </div>
        </div>

        <!-- Seção Foto e Texto (vertical: foto por cima, texto abaixo; ambos full width) -->
        <div class="row g-4">
            <!-- Espaço para Foto (por cima, ocupando largura total e quase todo o espaço do col) -->
            <div class="col-12">
                <div class="foto-placeholder">
                    <img src="../assets/img/osguri.jpg" class="img-fluid rounded w-100" alt="Sobre Nós" style="height: 300px; object-fit: cover;"
                    
                </div>
            </div>
            
            <!-- Espaço para Texto (abaixo, mesma largura da foto) -->
            <div class="col-12">
                <div class="texto-sobre">
                    <p class="mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                    <p class="mb-3">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    <p class="mb-0">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (mantido idêntico ao dashboard) -->
    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%);" role="contentinfo" aria-label="Menu de navegação inferior">
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
            <a href="perfil.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Perfil">
                <img src="../assets/img/perfil.png" alt="Perfil" />
            </a>
        </div>
    </footer>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>