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
      background-color: #2c2c2c;
      color: #f8f9fa;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header nav .top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 1.5rem;
    }

    header nav .text-oi h1 {
      margin: 0;
      font-weight: 700;
      font-size: 1.75rem;
      color: #dc3545;
    }

    header nav .pfp img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #dc3545;
    }

    .searchbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #3a3a3a;
      margin: 0 1.5rem 1.5rem;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
    }

    .searchbar .text-bar span {
      font-weight: 600;
      font-size: 1.1rem;
      color: #dc3545;
    }

    .searchbar .img-bar img {
      width: 24px;
      height: 24px;
      filter: invert(38%) sepia(88%) saturate(749%) hue-rotate(340deg) brightness(92%) contrast(89%);
      /* red-ish color */
      cursor: pointer;
    }

    main {
      flex: 1;
      padding: 0 1.5rem 2rem;
      max-width: 900px;
      margin: 0 auto;
      width: 100%;
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
      background-color: #3a3a3a;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      box-shadow: 0 0 8px rgba(220, 53, 69, 0.4);
      overflow: hidden;
    }

    .card-atividade .card-img img {
      width: 80px;
      height: 80px;
      object-fit: contain;
      background-color: #2c2c2c;
      padding: 0.5rem;
    }

    .card-atividade .card-text {
      padding: 0.75rem 1rem;
      color: #f8f9fa;
      flex: 1;
    }

    .card-atividade .card-titulo a {
      font-weight: 700;
      font-size: 1.1rem;
      color: #dc3545;
      text-decoration: none;
    }

    .card-atividade .card-titulo a:hover {
      text-decoration: underline;
      color: #a71d2a;
    }

    .card-atividade .card-endereco span {
      font-size: 0.9rem;
      display: block;
      color: #ccc;
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
      background-color: #3a3a3a;
      border-radius: 0.5rem;
      flex: 1 1 calc(33% - 1rem);
      max-width: calc(33% - 1rem);
      display: flex;
      align-items: center;
      padding: 1rem;
      box-shadow: 0 0 8px rgba(220, 53, 69, 0.4);
      cursor: default;
      transition: background-color 0.3s ease;
    }

    .card-servico:hover {
      background-color: #4a1a1f;
    }

    .servico-img img {
      width: 40px;
      height: 40px;
      object-fit: contain;
      margin-right: 1rem;
      filter: brightness(0) invert(1);
    }

    .servico-text span {
      color: #f8f9fa;
      font-weight: 600;
      font-size: 1rem;
    }

    /* Footer */
    footer.rodape {
      background-color: #1f1f1f;
      padding: 0.75rem 0;
      display: flex;
      justify-content: space-around;
      align-items: center;
      box-shadow: 0 -2px 8px rgba(220, 53, 69, 0.5);
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
      filter: brightness(0) invert(0.6) sepia(1) saturate(5) hue-rotate(-10deg);
    }

    /* Responsivo */
    @media (max-width: 768px) {
      .card-atividade {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .card-atividade .card-img img {
        width: 60px;
        height: 60px;
        margin-bottom: 0.5rem;
      }

      .card-atividade .card-text {
        padding: 0;
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
      <div class="top">
        <div class="text-oi">
          <h1>Olá, Lucas</h1>
        </div>
        <div class="pfp">
          <img src="../assets/img/pfp.jpg" alt="Foto de perfil do usuário Lucas" />
        </div>
      </div>
    </nav>
    <div class="searchbar">
      <div class="text-bar">
        <span>Para onde voce vai?</span>
      </div>
      <div class="img-bar">
        <img src="../assets/img/lupa.png" alt="Ícone de lupa para busca" />
      </div>
    </div>
  </header>

  <main>
    <section class="atividade">
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
    </section>

    <section class="servicos">
      <div class="servicos-titulo">
        <h3>Outros serviços</h3>
      </div>
      <div class="servicos-cards">
        <div class="card-servico" tabindex="0" role="region" aria-label="Alertas em tempo real">
          <div class="servico-img">
            <img src="../assets/img/alerta.png" alt="Ícone de alerta" />
          </div>
          <div class="servico-text">
            <span>Alertas em tempo real</span>
          </div>
        </div>
        <div class="card-servico" tabindex="0" role="region" aria-label="Status do meu trem">
          <div class="servico-img">
            <img src="../assets/img/trem.png" alt="Ícone de trem" />
          </div>
          <div class="servico-text">
            <span>Status do meu trem</span>
          </div>
        </div>
        <div class="card-servico" tabindex="0" role="region" aria-label="Horários de embarque">
          <div class="servico-img">
            <img src="../assets/img/relogio.png" alt="Ícone de relógio" />
          </div>
          <div class="servico-text">
            <span>Horários de embarque</span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="rodape" role="contentinfo" aria-label="Menu de navegação inferior">
    <a href="tela8.html" aria-label="Início">
      <img src="../assets/img/casa.png" alt="Início" />
    </a>
    <a href="tela6.html" aria-label="Buscar">
      <img src="../assets/img/lupa.png" alt="Buscar" />
    </a>
    <a href="tela11.html" aria-label="Chat">
      <img src="../assets/img/chat.png" alt="Chat" />
    </a>
    <a href="tela9.html" aria-label="Perfil">
      <img src="../assets/img/perfil.png" alt="Perfil" />
    </a>
  </footer>

  <!-- Bootstrap JS (opcional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
