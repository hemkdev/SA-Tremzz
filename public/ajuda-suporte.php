<?php
session_start();
// Verificação básica de login
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
    exit;
}
require "../config/bd.php"; // Conexão com BD

// Processar envio do formulário no mesmo arquivo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar inputs
    $assunto = trim($_POST['assunto'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $usuario_id = $_SESSION['id'] ?? 0; // ID do usuário logado

    // Validações básicas
    if (empty($assunto) || strlen($assunto) > 100) {
        $_SESSION['erro'] = "Assunto inválido ou obrigatório (máx. 100 caracteres).";
        header("Location: ajuda-suporte.php");
        exit;
    }

    if (empty($descricao) || strlen($descricao) > 1000) {
        $_SESSION['erro'] = "Descrição inválida ou obrigatória (máx. 1000 caracteres).";
        header("Location: ajuda-suporte.php");
        exit;
    }

    if ($usuario_id <= 0) {
        $_SESSION['erro'] = "Erro de sessão. Faça login novamente.";
        header("Location: login.php");
        exit;
    }

    // Inserir no banco (assume colunas: usuario_id, assunto, descricao, data_hora_abertura, status)
    $stmt = $conn->prepare("INSERT INTO suporte (usuario_id, assunto, descricao, data_hora_abertura, status) VALUES (?, ?, ?, NOW(), 'aberto')");
    if ($stmt) {
        $stmt->bind_param("iss", $usuario_id, $assunto, $descricao);
        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Chamado enviado com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao enviar chamado. Tente novamente.";
        }
        $stmt->close();
    } else {
        $_SESSION['erro'] = "Erro ao preparar consulta SQL.";
    }

    $conn->close();
    header("Location: ajuda-suporte.php");
    exit;
}
// Inicialize variáveis com defaults (para evitar )
$home = 'user/home.php';
$arquivo = 'user/buscar.php';
$icone = 'lupa.png';
$chat = 'user/chat.php';
$perfil = 'user/perfil.php';
// Lógica corrigida para roles (sem exit; aqui, só define variáveis)
if (isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
    $home = 'adm/home.php';
    $arquivo = 'adm/gerenciamento.php';
    $icone = 'gerenciamento.png';
    $chat = 'adm/chat.php';
    $perfil = 'adm/perfil.php';
} elseif (isset($_SESSION["maquinista"]) && $_SESSION["maquinista"] === true) {
    // Defina variáveis para maquinista (ajuste os caminhos conforme sua estrutura)
    $home = 'maq/home.php';  // Exemplo; substitua pelos reais
    $arquivo = 'maq/gerenciamento.php';
    $icone = 'maq_icone.png';
    $chat = 'maq/chat.php';
    $perfil = 'maq/perfil.php';
}

// Se role inválida, redirecione (opcional, para segurança)
if (!isset($_SESSION["admin"]) && !isset($_SESSION["maquinista"]) && !isset($_SESSION["conectado"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Ajuda e Suporte</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
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
        .suporte-titulo {
            color: #b02a37; /* Vermelho suave, menos chamativo que #dc3545 */
            font-weight: 600;
        }
        .suporte-card {
            background-color: #1e1e1e;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #333;
        }
        .suporte-card h5 {
            color: #b02a37;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .suporte-card p {
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .suporte-link {
            color: #b02a37;
            text-decoration: none;
            font-weight: 500;
        }
        .suporte-link:hover {
            text-decoration: underline;
        }
        /* Estilos para FAQ Accordion integrados */
        .faq-accordion-item {
            background-color: #1e1e1e;
            border: 1px solid #333;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .faq-accordion-header {
            background-color: #2a2a2a;
            border: none;
            color: #e0e0e0;
        }
        .faq-accordion-header button {
            color: #e0e0e0;
            font-weight: 500;
        }
        .faq-accordion-header button:hover {
            color: #b02a37;
        }
        .faq-accordion-body {
            background-color: #1e1e1e;
            color: #e0e0e0;
            line-height: 1.6;
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
        /* Estilos para modal */
        .modal-content {
            background-color: #1e1e1e;
            color: #e0e0e0;
            border: none;
        }
        .modal-header, .modal-footer {
            border-color: #333;
        }
        .form-control, .form-select {
            background-color: #2a2a2a;
            border-color: #333;
            color: #e0e0e0;
        }
        .form-control:focus, .form-select:focus {
            background-color: #2a2a2a;
            border-color: #b02a37;
            color: #e0e0e0;
            box-shadow: 0 0 0 0.25rem rgba(176, 42, 55, 0.25);
        }
    </style>
</head>

<body>
    <?php
    // Mostrar mensagens de sucesso/erro armazenadas na sessão e limpar
    if (!empty($_SESSION['sucesso'])) {
        echo '<div class="container mt-3"><div class="alert alert-success alert-dismissible fade show" role="alert">'.htmlspecialchars($_SESSION['sucesso']).'<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button></div></div>';
        unset($_SESSION['sucesso']);
    }
    if (!empty($_SESSION['erro'])) {
        echo '<div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show" role="alert">'.htmlspecialchars($_SESSION['erro']).'<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Fechar"></button></div></div>';
        unset($_SESSION['erro']);
    }
    ?>
    <main class="container px-3" style="max-width: 900px; margin: 0 auto; padding-top: 2rem;">
        <!-- Título -->
        <div class="row justify-content-center mb-5">
            <div class="col-12 text-center">
                <h2 class="suporte-titulo fs-2 mb-0">Ajuda e Suporte</h2>
            </div>
        </div>

        <!-- Seção de Introdução -->
        <div class="row g-4">
            <div class="col-12">
                <div class="suporte-card">
                    <h5>Precisa de ajuda com o TREMzz?</h5>
                    <p class="mb-0">Estamos aqui para tornar sua experiência no transporte ferroviário mais simples e segura. Escolha uma das opções abaixo para obter suporte rápido. Nossa equipe responde em até 24 horas para e-mails e em tempo real via chat in-app.</p>
                </div>
            </div>
        </div>

        <!-- Seção de Opções de Suporte -->
        <div class="row g-4">
            <!-- Opção 1: FAQ com Accordion Integrado -->
            <div class="col-12">
                <div class="suporte-card">
                    <h5>Perguntas Frequentes</h5>
                    <p>Encontre respostas para dúvidas comuns sobre horários, notificações e uso do app. Clique nas perguntas para expandir.</p>
                    
                    <!-- FAQ Accordion -->
                    <div class="accordion" id="faqAccordion">
                        <!-- Pergunta 1 -->
                        <div class="accordion-item faq-accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed faq-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Como posso verificar os horários de trens no TREMzz?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body faq-accordion-body">
                                    No app, acesse a aba "Buscar" e insira sua origem e destino. O TREMzz exibe horários em tempo real, incluindo opções de trens diretos e com conexões, com filtros por hora e tipo de serviço.
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 2 -->
                        <div class="accordion-item faq-accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed faq-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    O que acontece se houver um atraso no meu trem?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body faq-accordion-body">
                                    O TREMzz envia notificações push imediatas sobre atrasos, cancelamentos ou desvios. Você pode reescolher horários alternativos diretamente no app e receber atualizações da malha ferroviária.
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 3 -->
                        <div class="accordion-item faq-accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed faq-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Como reporto um problema ou acidente na linha?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body faq-accordion-body">
                                    Use a aba "Chat" para enviar relatos em tempo real, com fotos e localização GPS. Nossa equipe de suporte responde em minutos, e o app integra com autoridades ferroviárias para ações rápidas.
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 4 -->
                        <div class="accordion-item faq-accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed faq-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    O app funciona em todos os dispositivos?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body faq-accordion-body">
                                    Sim, o TREMzz é otimizado para smartphones Android e iOS, com interface responsiva. Baixe na Google Play ou App Store e ative notificações para o melhor uso.
                                </div>
                            </div>
                        </div>

                        <!-- Pergunta 5 -->
                        <div class="accordion-item faq-accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed faq-accordion-header" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    Como reservo bilhetes pelo app?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                                <div class="accordion-body faq-accordion-body">
                                    Após buscar horários, selecione o trem desejado e prossiga para pagamento seguro via cartão ou PIX. O bilhete digital é enviado por e-mail e salvo no seu perfil no app.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opção 2: Chat In-App -->
            <div class="col-12">
                <div class="suporte-card">
                    <h5> Relatar problema</h5>
                    <p>Relate diretamente a nossa equipe de suporte para problemas urgentes, como atrasos ou relatos de incidentes.</p>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalSuporte" class="suporte-link">Abrir chamado</a>
                </div>
            </div>
        </div>

        <!-- Opção 4: Contato Adicional -->
        <div class="row g-4">
            <div class="col-12">
                <div class="suporte-card">
                    <h5>Outros Canais de Suporte</h5>
                    <p>Telefone: (11) 4002-8922 (Seg-Sex, 8h-18h) | Redes Sociais: Siga-nos no Instagram @tremzz_oficial para dicas e atualizações.</p>
                    <p class="mb-0"><strong>Dica:</strong> Para emergências na malha ferroviária, ligue para 190 ou use o chat para relatos rápidos.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para Abrir Chamado -->
    <div class="modal fade" id="modalSuporte" tabindex="-1" aria-labelledby="modalSuporteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="modalSuporteLabel">Abrir Chamado de Suporte</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form method="POST" action=""> <!-- envia para este mesmo arquivo -->
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="assunto" class="form-label">Assunto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="assunto" name="assunto" required maxlength="100" placeholder="Ex: Problema com horário de trem">
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="descricao" name="descricao" required maxlength="1000" rows="4" placeholder="Descreva o problema em detalhes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Enviar Chamado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer (mantido idêntico ao dashboard) -->
    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%);" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="<?php echo htmlspecialchars($home); ?>" class="footer-icon text-center text-decoration-none p-2" aria-label="Início">
                <img src="../assets/img/casa.png" alt="Ícone Início" />
            </a>
            <a href="<?php echo htmlspecialchars($arquivo); ?>" class="footer-icon text-center text-decoration-none p-2" aria-label="Buscar">
                <img src="../assets/img/<?php echo htmlspecialchars($icone); ?>" alt="Ícone Buscar" />
            </a>
            <a href="<?php echo htmlspecialchars($chat); ?>" class="footer-icon text-center text-decoration-none p-2" aria-label="Chat">
                <img src="../assets/img/chat.png" alt="Ícone Chat" />
            </a>
            <a href="<?php echo htmlspecialchars($perfil); ?>" class="footer-icon text-center text-decoration-none p-2" aria-label="Perfil">
                <img src="../assets/img/perfil.png" alt="Ícone Perfil" />
            </a>
        </div>
    </footer>

    <!-- Bootstrap JS (necessário para accordions e modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>