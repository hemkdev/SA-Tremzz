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
      min-height: 100vh;
      margin: 0;
      padding-bottom: 70px;
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

    .searchbar {
      background-color: #1e1e1e;
      margin: 0 1.5rem 1.5rem;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      box-shadow: none;
    }

    .searchbar .text-bar span {
      font-weight: 600;
      font-size: 1.1rem;
      color: #e0e0e0;
    }

    .searchbar .img-bar img {
      width: 24px;
      height: 24px;
      filter: brightness(0) invert(1);
      cursor: pointer;
      transition: filter 0.3s ease;
    }

    .searchbar .img-bar img:hover {
      filter: brightness(0) invert(0.7) sepia(1) saturate(5) hue-rotate(-10deg);
    }

    main {
      max-width: 900px;
      margin: 0 auto 2rem;
      padding: 0 1rem;
    }

    /* Últimas atividades */
    .atividade .ult-atividade span {
      font-weight: 600;
      font-size: 1.25rem;
      color: #dc3545;
      margin-bottom: 1rem;
      display: inline-block;
    }

    .card-atividade {
      display: flex;
      background-color: #1e1e1e;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      box-shadow: none;
      overflow: hidden;
      transition: background-color 0.3s ease;
      align-items: center; /* vertical center */
      justify-content: center; /* horizontal center */
      gap: 1rem; /* spacing between image and text */
    }

    /* On larger screens, limit max width of text and center content */
    @media (min-width: 769px) {
      .card-atividade {
        justify-content: center;
      }

      .card-atividade .card-text {
        max-width: 400px;
        text-align: center;
      }
    }

    .card-atividade:hover {
      background-color: #dc3545;
    }

    .card-atividade .card-img img {
      width: 80px;
      height: 80px;
      object-fit: contain;
      background-color: transparent;
      padding: 0.5rem;
      filter: brightness(0) invert(1);
    }

    .card-atividade .card-text {
      padding: 0.75rem 1rem;
      color: #e0e0e0;
      flex: none; /* prevent flex-grow */
    }

    .card-atividade .card-titulo a {
      font-weight: 700;
      font-size: 1.1rem;
      color: #fff;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .card-atividade .card-titulo a:hover {
      color: #fff;
      text-decoration: underline;
    }

    .card-atividade .card-endereco span {
      font-size: 0.9rem;
      display: block;
      color: #b0b0b0;
    }

    /* Serviços */
    .servicos {
      margin-top: 2.5rem;
    }

    .servicos-titulo h3 {
      color: #dc3545;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .servicos-cards {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      justify-content: space-between;
    }

    .card-servico {
      background-color: #1e1e1e;
      border-radius: 0.5rem;
      flex: 1 1 calc(33% - 1rem);
      max-width: calc(33% - 1rem);
      display: flex;
      align-items: center;
      padding: 1rem;
      box-shadow: none;
      cursor: default;
      transition: background-color 0.3s ease;
    }

    .card-servico:hover {
      background-color: #dc3545;
    }

    .servico-img img {
      width: 40px;
      height: 40px;
      object-fit: contain;
      margin-right: 1rem;
      filter: brightness(0) invert(1);
    }

    .servico-text span {
      color: #e0e0e0;
      font-weight: 600;
      font-size: 1rem;
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

    /* Responsividade */
    @media (max-width: 768px) {
      .card-atividade {
        flex-direction: column;
        align-items: center;
        text-align: center;
        justify-content: flex-start; /* reset justify-content for small screens */
        gap: 0;
      }

      .card-atividade .card-img img {
        width: 60px;
        height: 60px;
        margin-bottom: 0.5rem;
      }

      .card-atividade .card-text {
        padding: 0;
        max-width: none;
      }

      .servicos-cards {
        flex-direction: column;
      }

      .card-servico {
        max-width: 100%;
        justify-content: center;
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
          <h1>Olá, 
            <?php echo htmlspecialchars($_SESSION['nome']); ?> !
          </h1>
        </div>
        <div class="pfp">
          <img src="../assets/img/pfp.jpg" alt="Foto de perfil do usuário Lucas" />
        </div>
      </div>
    </nav>
    <div class="searchbar d-flex justify-content-between align-items-center mx-3">
      <div class="text-bar">
        <span>Para onde voce vai?</span>
      </div>
      <div class="img-bar">
        <img src="../assets/img/lupa.png" alt="Ícone de lupa para busca" />
      </div>
    </div>
  </header>

  <main>
    <div class="atividade">
      <div class="ult-atividade">
        <span>Últimas atividades</span>
      </div>
      <div class="card-atividade">
        <div class="card-img">
          <img src="../assets/img/local.png" alt="Ícone local" />
        </div>
        <div class="card-text">
          <div class="card-titulo">
            <a href="#" tabindex="0">Estação da Luz</a>
          </div>
          <div class="card-endereco">
            <span>Centro Histórico de São Paulo</span><br />
            <span>São Paulo - SP</span>
          </div>
        </div>
      </div>
      <div class="card-atividade">
        <div class="card-img">
          <img src="../assets/img/local.png" alt="Ícone local" />
        </div>
        <div class="card-text">
          <div class="card-titulo">
            <a href="#" tabindex="0">Estação Japão</a>
          </div>
          <div class="card-endereco">
            <span>Bairro da Liberdade</span><br />
            <span>São Paulo - SP</span>
          </div>
        </div>
      </div>
    </div>

    <div class="servicos">
      <div class="servicos-titulo">
        <h3>Outros serviços</h3>
      </div>
      <div class="servicos-cards d-flex flex-wrap justify-content-between gap-3">
        <div class="card-servico d-flex align-items-center">
          <div class="servico-img">
            <img src="../assets/img/alerta.png" alt="Ícone de alerta" />
          </div>
          <div class="servico-text">
            <span>Alertas em tempo real</span>
          </div>
        </div>
        <div class="card-servico d-flex align-items-center">
          <div class="servico-img">
            <img src="../assets/img/trem.png" alt="Ícone de trem" />
          </div>
          <div class="servico-text">
            <span>Status do meu trem</span>
          </div>
        </div>
        <div class="card-servico d-flex align-items-center">
          <div class="servico-img">
            <img src="../assets/img/relogio.png" alt="Ícone de relógio" />
          </div>
          <div class="servico-text">
            <span>Horários de embarque</span>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="rodape" role="contentinfo" aria-label="Menu de navegação inferior">
    <a href="home.php" aria-label="Início">
      <img src="../assets/img/casa.png" alt="Início" />
    </a>
    <a href="pesquisar.html" aria-label="Buscar">
      <img src="../assets/img/lupa.png" alt="Buscar" />
    </a>
    <a href="chat.php" aria-label="Chat">
      <img src="../assets/img/chat.png" alt="Chat" />
    </a>
    <a href="perfil.php" aria-label="Perfil">
      <img src="../assets/img/perfil.png" alt="Perfil" />
    </a>
  </footer>

  <!-- Bootstrap JS (opcional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

