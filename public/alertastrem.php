<?php
session_start();
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
    exit;
}

// Dados fictícios para estações e horários (pode ser substituído por banco de dados)
$estacoes = [
    'S1',
    'S2',
    'S3',
    'Estação Principal'
];

$horarios = [
    '06:00 - 07:30',
    '08:00 - 09:30',
    '12:00 - 13:30',
    '16:00 - 17:30',
    '18:00 - 19:30'
];

// Simular alertas em tempo real (array fictício; em produção, viria de API/WebSocket)
$alertas = [
    ['tipo' => 'Atraso', 'mensagem' => 'Trem da Linha 1 atrasado em 15 minutos na Estação da Luz.', 'horario' => 'Agora', 'status' => 'Urgente'],
    ['tipo' => 'Incidente', 'mensagem' => 'Sinalização defeituosa entre Brás e Sé. Reduza velocidade.', 'horario' => 'Há 5 min', 'status' => 'Aviso'],
    ['tipo' => 'Acidente', 'mensagem' => 'Colisão menor reportada na Estação Pinheiros. Linha interrompida.', 'horario' => 'Há 10 min', 'status' => 'Crítico'],
    ['tipo' => 'Manutenção', 'mensagem' => 'Obras na via entre Tatuapé e Luz. Desvio programado.', 'horario' => 'Em 30 min', 'status' => 'Planejado']
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Alertas em Tempo Real</title>
    <link rel="shortcut icon" href="../assets/img/tremzz_logo.png" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Fonte Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- CSS personalizado (baseado no original) -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            min-height: 100vh;
            padding-bottom: 70px;
        }

        .card-hover {
            background-color: #1e1e1e !important;
            transition: background-color 0.3s ease;
            border: none;
        }

        .card-hover:hover {
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

        .form-select, .form-control {
            background-color: #2a2a2a;
            border-color: #333;
            color: #e0e0e0;
        }

        .form-select:focus, .form-control:focus {
            background-color: #2a2a2a;
            border-color: #dc3545;
            color: #e0e0e0;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Estilo para espaçamento visual nas opções do select (padding interno) */
        .form-select option {
            padding: 8px 12px; /* Adiciona espaçamento interno em cada opção */
            margin: 2px 0; /* Margem vertical simulada (funciona em alguns browsers) */
        }

        .alerta-card {
            background-color: #1e1e1e;
            border-left: 4px solid #dc3545;
            transition: background-color 0.3s ease;
        }

        .alerta-card.urgente { border-left-color: #dc3545; }
        .alerta-card.aviso { border-left-color: #ffc107; }
        .alerta-card.critico { border-left-color: #dc3545; }
        .alerta-card.planejado { border-left-color: #28a745; }

        .alerta-card:hover {
            background-color: #2a2a2a;
        }

        .btn-reportar {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-reportar:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: #fff;
        }

        .badge-urgente { background-color: #dc3545; }
        .badge-aviso { background-color: #ffc107; color: #000; }
        .badge-critico { background-color: #dc3545; }
        .badge-planejado { background-color: #28a745; }

        /* Espaçamento para os selects: lado a lado com gap moderado (não grudados) */
        .form-row {
            gap: 1rem; /* Espaçamento horizontal entre os selects (ajustável: 1rem = ~16px) */
            justify-content: space-between; /* Distribui o espaço uniformemente */
        }

        .form-row .col-md-4 {
            flex: 1; /* Garante que cada coluna ocupe espaço igual */
            max-width: 30%; /* Limita a largura para evitar grudar nas bordas */
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 1rem; /* Mantém espaçamento vertical em mobile */
            }
            .form-row .col-md-4 {
                max-width: 100%; /* Em mobile, ocupa largura total */
                margin-bottom: 0; /* Remove margem extra em mobile */
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
                        <h1 class="text-light fw-bold mb-0 fs-3">Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?> !</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION["foto"] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <!-- Seção de Seleção de Viagem -->
        <div class="selecao-viagem mb-5">
            <div class="titulo mb-3">
                <h3 class="text-danger fw-bold fs-4">Monitorar Viagem</h3>
                <p class="text-muted small">Selecione os detalhes da sua viagem para receber alertas em tempo real.</p>
            </div>
            <div class="card card-hover rounded-3 p-4">
                <form id="viagemForm" class="form-row d-flex flex-wrap">
                    <div class="col-md-4 mb-3">
                        <label for="horario" class="form-label text-light">Horário da Viagem</label>
                        <select class="form-select" id="horario" required>
                            <option value="">Selecione um horário</option>
                            <?php foreach ($horarios as $horario): ?>
                                <option value="<?php echo htmlspecialchars($horario); ?>"><?php echo htmlspecialchars($horario); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="estacaoSaida" class="form-label text-light">Estação de Saída</label>
                        <select class="form-select" id="estacaoSaida" required>
                            <option value="">Selecione saída</option>
                            <?php foreach ($estacoes as $estacao): ?>
                                <option value="<?php echo htmlspecialchars($estacao); ?>"><?php echo htmlspecialchars($estacao); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="estacaoChegada" class="form-label text-light">Estação de Chegada</label>
                        <select class="form-select" id="estacaoChegada" required>
                            <option value="">Selecione chegada</option>
                            <?php foreach ($estacoes as $estacao): ?>
                                <option value="<?php echo htmlspecialchars($estacao); ?>"><?php echo htmlspecialchars($estacao); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger">Iniciar Monitoramento</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Seção de Alertas em Tempo Real -->
        <div class="alertas mt-5 mb-5" id="alertasSection" style="display: none;">
            <div class="titulo mb-3 d-flex justify-content-between align-items-center">
                <h3 class="text-danger fw-bold fs-4">Alertas em Tempo Real</h3>
                <button class="btn btn-outline-danger btn-sm" id="reportarIncidente">Reportar Incidente</button>
            </div>
            <div class="list-group" id="alertasList">
                <?php foreach ($alertas as $alerta): ?>
                    <div class="list-group-item list-group-item-action alerta-card <?php echo strtolower($alerta['status']); ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-light"><?php echo htmlspecialchars($alerta['tipo']); ?></h5>
                            <small class="text-muted"><?php echo htmlspecialchars($alerta['horario']); ?></small>
                        </div>
                        <p class="mb-1"><?php echo htmlspecialchars($alerta['mensagem']); ?></p>
                        <span class="badge <?php echo 'badge-' . strtolower($alerta['status']); ?>"><?php echo htmlspecialchars($alerta['status']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3 text-center">
                <small class="text-muted">Atualizando a cada 30 segundos... (Simulação em tempo real)</small>
            </div>
        </div>

        <!-- Modal para Reportar Incidente -->
        <div class="modal fade" id="reportarModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Reportar Acidente/Incidente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="reportarForm">
                            <div class="mb-3">
                                <label for="tipoIncidente" class="form-label">Tipo de Incidente</label>
                                <select class="form-select" id="tipoIncidente" required>
                                    <option value="">Selecione</option>
                                    <option value="Acidente">Acidente</option>
                                    <option value="Atraso">Atraso</option>
                                    <option value="Manutenção">Manutenção</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" rows="3" placeholder="Descreva o que aconteceu..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="local" class="form-label">Local (Estação ou Via)</label>
                                <input type="text" class="form-control" id="local" placeholder="Ex: Estação da Luz" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="enviarRelatorio">Enviar Relatório</button>
                    </div>
                </div>
            </div>
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
            <a href="chat.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Chat">
                <img src="../assets/img/chat.png" alt="Chat" />
            </a>
            <a href="perfil.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Perfil">
                <img src="../assets/img/perfil.png" alt="Perfil" />
            </a>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para Funcionalidades em Tempo Real e Interações -->
    <script>
        // Mostrar seção de alertas ao submeter o formulário
        document.getElementById('viagemForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const horario = document.getElementById('horario').value;
            const saida = document.getElementById('estacaoSaida').value;
            const chegada = document.getElementById('estacaoChegada').value;
            
            if (horario && saida && chegada) {
                alert('Monitoramento iniciado para: ' + horario + ' de ' + saida + ' para ' + chegada);
                document.getElementById('alertasSection').style.display = 'block';
                // Aqui você pode enviar para o backend via AJAX para filtrar alertas reais
            } else {
                alert('Por favor, preencha todos os campos.');
            }
        });

        // Abrir modal de relatório
        document.getElementById('reportarIncidente').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('reportarModal'));
            modal.show();
        });

        // Enviar relatório (simulação)
        document.getElementById('enviarRelatorio').addEventListener('click', function() {
            const tipo = document.getElementById('tipoIncidente').value;
            const descricao = document.getElementById('descricao').value;
            const local = document.getElementById('local').value;
            
            if (tipo && descricao && local) {
                alert('Relatório enviado com sucesso! Obrigado por reportar.');
                bootstrap.Modal.getInstance(document.getElementById('reportarModal')).hide();
                // Limpar formulário
                document.getElementById('reportarForm').reset();
                // Aqui envie para o backend via AJAX
            } else {
                alert('Por favor, preencha todos os campos.');
            }
        });

        // Simulação de atualização em tempo real (a cada 30s, adicione um alerta fictício)
        setInterval(function()) {
            if (document.getElementById('alertasSection').style.display !== 'none') {
                const list = document.getElementById('alertasList');
                const novoAlerta = {
                    tipo: 'Novo Atraso',
                    mensagem: 'Atualização: Trem atrasado em 20 minutos devido a incidente.',
                    horario: 'Agora',
                    status: 'Urgente'
                }
            }        
                };
                const novoItem = `
                    <div class="list-group-item list-group-item-action alerta-card urgente">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 text-light">${novoAlerta.tipo}</h5>
                            <small class="text-muted">${novoAlerta.horario}</small>
                        </div>
                        <p class="mb-1">${n
    