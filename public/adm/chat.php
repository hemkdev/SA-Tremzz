<?php
require "../../config/bd.php";
session_start();
date_default_timezone_set('America/Sao_Paulo'); // Opcional: Define fuso horário (ajuste se necessário)

// Verificação de login e cargo (apenas admin)
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true || ($_SESSION['cargo'] ?? '') !== 'administrador') {
    header("Location: login.php");
    exit;
}

// Busca todos os tickets de suporte, ordenados por mais recente
$tickets = [];
$stmt = $conn->prepare("SELECT s.*, u.nome as usuario_nome 
    FROM suporte s 
    INNER JOIN usuarios u ON s.usuario_id = u.id 
    ORDER BY s.data_hora_abertura DESC
");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Suporte</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
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

    <!-- CSS mínimo para fundos exatos, filtros de ícones e hovers essenciais -->
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

        .pfp-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .pfp-img:hover {
            transform: scale(1.05);
            transition: all 0.3s ease;
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

        .accordion-button {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
            border: none;
        }

        .accordion-button:not(.collapsed) {
            background-color: #2a2a2a !important;
            color: #fff !important;
        }

        .accordion-body {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
            border: none;
        }

        .status-badge {
            font-size: 0.75rem;
        }

        /* Responsividade mínima para mobile */
        @media (max-width: 768px) {
            .accordion-button {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            nav.navbar .container-fluid .d-flex { flex-direction: row !important; justify-content: space-between !important; align-items: center !important; gap: .5rem; }
            .navbar .text-oi { text-align: left !important; }
            .navbar .pfp { margin-left: auto !important; text-align: right !important; }
            .navbar { padding-top: .6rem !important; padding-bottom: .25rem !important; }
            .text-danger.fw-bold.fs-4, .text-danger.fw-bold.fs-5, .text-danger.fw-bold.fs-3 { text-align: center !important; display: block; width: 100%; }
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
                        <h1 class="text-light fw-bold mb-0 fs-3">Suporte</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION['foto'] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
        <!-- Searchbar -->
        <div class="searchbar bg-custom d-flex justify-content-between align-items-center mx-3 mb-3 p-3 rounded-3">
            <div class="text-bar">
                <span class="fw-semibold fs-5 text-light">Buscar</span>
            </div>
            <div class="img-bar">
                <img src="../../assets/img/lupa.png" alt="Ícone de lupa para busca" class="search-icon" />
            </div>
        </div>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem; overflow-y: auto;">
        <div class="accordion" id="suporteAccordion">
            <?php
            if (!empty($tickets)) {
                foreach ($tickets as $index => $ticket) {
                    // Formatação de data_hora_abertura (igual ao original)
                    $dataHoraAbertura = $ticket['data_hora_abertura'];
                    $dataAtual = date('Y-m-d');
                    $dataDaMensagem = date('Y-m-d', strtotime($dataHoraAbertura));

                    if ($dataDaMensagem === $dataAtual) {
                        $tempoFormatado = date('H:i', strtotime($dataHoraAbertura));
                    } else {
                        $tempoFormatado = date('d/m', strtotime($dataHoraAbertura));
                    }

                    // Badge de status
                    $statusClass = ($ticket['status'] === 'aberto') ? 'bg-warning' : (($ticket['status'] === 'em andamento') ? 'bg-info' : 'bg-success');
                    $statusText = ucfirst($ticket['status']);
            ?>
                    <div class="accordion-item bg-custom border-0 mb-3 rounded-3">
                        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-light"><?php echo htmlspecialchars($ticket['assunto']); ?></div>
                                        <div class="small text-light">De: <?php echo htmlspecialchars($ticket['usuario_nome']); ?></div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end ms-3">
                                        <span class="badge <?php echo $statusClass; ?> status-badge"><?php echo $statusText; ?></span>
                                        <div class="small text-light mt-1"><?php echo $tempoFormatado; ?></div>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#suporteAccordion">
                            <div class="accordion-body">
                                <p><?php echo nl2br(htmlspecialchars($ticket['descricao'])); ?></p>
                                <!-- Opcional: Adicionar ações como responder ou fechar ticket -->
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="text-light text-center mt-4">Nenhum ticket de suporte encontrado.</p>';
            }
            ?>
        </div>
    </main>

    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%); z-index: 1000;" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="home.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Início">
                <img src="../../assets/img/casa.png" alt="Início" />
            </a>
            <a href="gerenciamento.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Buscar">
                <img src="../../assets/img/gerenciamento.png" alt="Gerenciamento" />
            </a>
            <a href="chat.php" class="footer-icon active text-center text-decoration-none p-2" aria-label="Chat">
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
