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
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #121212;
      color: #e0e0e0;
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding-bottom: 70px; /* espaço para rodapé fixo */
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
      flex: 1;
      overflow-y: auto;
    }

    /* Lista de chats */
    .chat-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .chat-item {
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      background-color: #1e1e1e;
      margin-bottom: 1rem;
      cursor: pointer;
      transition: background-color 0.2s ease;
      position: relative;
    }

    .chat-item:hover {
      background-color: #2a2a2a;
    }

    .chat-item.active {
      color: #fff;
    }

    .chat-item.active .chat-name,
    .chat-item.active .chat-last-message,
    .chat-item.active .chat-time,
    .chat-item.active .badge {
      color: #fff;
    }

    .chat-avatar {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      overflow: hidden;
      margin-right: 1rem;
      flex-shrink: 0;
      border: 2px solid transparent;
      transition: border-color 0.3s ease;
    }

    .chat-avatar img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      filter: brightness(0) invert(1);
    }

    .chat-info {
      flex: 1;
      min-width: 0;
    }

    .chat-name {
      font-weight: 700;
      font-size: 1.1rem;
      color: #e0e0e0;
      margin-bottom: 0.25rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .chat-last-message {
      font-size: 0.9rem;
      color: #b0b0b0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .chat-meta {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      margin-left: 1rem;
      flex-shrink: 0;
      min-width: 60px;
    }

    .chat-time {
      font-size: 0.8rem;
      color: #888;
      margin-bottom: 0.5rem;
      user-select: none;
      white-space: nowrap;
    }

    .badge {
      background-color: #dc3545;
      color: #fff;
      font-size: 0.75rem;
      font-weight: 700;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      min-width: 24px;
      text-align: center;
      user-select: none;
    }

    /* Indicador de mensagem não lida */
    .unread-dot {
      position: absolute;
      top: 12px;
      left: 48px;
      width: 12px;
      height: 12px;
      background-color: #dc3545;
      border-radius: 50%;
      border: 2px solid #121212;
      pointer-events: none;
    }

    /* Scrollbar */
    main::-webkit-scrollbar {
      width: 8px;
    }

    main::-webkit-scrollbar-track {
      background: #121212;
    }

    main::-webkit-scrollbar-thumb {
      background-color: #dc3545;
      border-radius: 4px;
    }

    /* Responsividade */
    @media (max-width: 768px) {
      .chat-avatar {
        width: 48px;
        height: 48px;
        margin-right: 0.75rem;
      }

      .chat-name {
        font-size: 1rem;
      }

      .chat-last-message {
        font-size: 0.85rem;
      }

      .chat-meta {
        min-width: 50px;
        margin-left: 0.75rem;
      }

      .chat-time {
        font-size: 0.75rem;
      }

      .badge {
        min-width: 20px;
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
      }
    }

    /* Footer */
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
  </style>
</head>

<body>
  <header>
    <nav>
      <div class="top d-flex justify-content-between align-items-center">
        <div class="text-oi">
          <h1>Conversas</h1>
        </div>
        <div class="pfp">
          <img src="../assets/img/pfp.jpg" alt="Foto de perfil do usuário Lucas" />
        </div>
      </div>
    </nav>
    <div class="searchbar d-flex justify-content-between align-items-center mx-3">
      <div class="text-bar">
        <span>Buscar conversas</span>
      </div>
      <div class="img-bar">
        <img src="../assets/img/lupa.png" alt="Ícone de lupa para busca" />
      </div>
    </div>
  </header>

  <main>
    <ul class="chat-list">
      <li class="chat-item active" tabindex="0" aria-current="true"
        aria-label="Chat com Estação da Luz, última mensagem: Trem atrasado, 2 mensagens não lidas">
        <div class="chat-avatar">
          <img src="../assets/img/local.png" alt="Avatar Estação da Luz" />
          <span class="unread-dot"></span>
        </div>
        <div class="chat-info">
          <div class="chat-name">Estação da Luz</div>
          <div class="chat-last-message">Trem atrasado</div>
        </div>
        <div class="chat-meta">
          <div class="chat-time">10:45</div>
          <div class="badge" aria-label="2 mensagens não lidas">2</div>
        </div>
      </li>

      <li class="chat-item" tabindex="0" aria-label="Chat com Estação Japão, última mensagem: Tudo funcionando normalmente">
        <div class="chat-avatar">
          <img src="../assets/img/local.png" alt="Avatar Estação Japão" />
        </div>
        <div class="chat-info">
          <div class="chat-name">Estação Japão</div>
          <div class="chat-last-message">Tudo funcionando normalmente</div>
        </div>
        <div class="chat-meta">
          <div class="chat-time">09:30</div>
        </div>
      </li>

      <li class="chat-item" tabindex="0" aria-label="Chat com Suporte, última mensagem: Como posso ajudar?">
        <div class="chat-avatar">
          <img src="../assets/img/chat.png" alt="Avatar Suporte" />
        </div>
        <div class="chat-info">
          <div class="chat-name">Suporte</div>
          <div class="chat-last-message">Como posso ajudar?</div>
        </div>
        <div class="chat-meta">
          <div class="chat-time">Ontem</div>
        </div>
      </li>

      <li class="chat-item" tabindex="0" aria-label="Chat com Status do Trem, última mensagem: Seu trem está a caminho">
        <div class="chat-avatar">
          <img src="../assets/img/trem.png" alt="Avatar Status do Trem" />
        </div>
        <div class="chat-info">
          <div class="chat-name">Status do Trem</div>
          <div class="chat-last-message">
            <?php echo htmlspecialchars($_SESSION['nome']); ?>
            , seu trem está a caminho!
        </div>
        </div>
        <div class="chat-meta">
          <div class="chat-time">Seg</div>
        </div>
      </li>
    </ul>
  </main>

  <footer class="rodape" role="contentinfo" aria-label="Menu de navegação inferior">
    <a href="home.php" aria-label="Início">
      <img src="../assets/img/casa.png" alt="Início" />
    </a>
    <a href="tela6.html" aria-label="Buscar">
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
