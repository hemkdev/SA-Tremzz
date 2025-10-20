<?php
session_start();
require "../../config/bd.php";

$_SESSION['erro'] = "";
$_SESSION['sucesso'] = "";

// Verificação de login
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['id'])) {
    $_SESSION['erro'] = "ID de usuário não encontrado na sessão.";
    header("Location: perfil.php");
    exit;
}

$id = $_SESSION['id']; // Define $id corretamente
$editado = false;
$sucesso = false;

// Exibir mensagens de sessão (antes do processamento, para limpar)
$erro_msg = $_SESSION['erro'] ?? '';
$sucesso_msg = $_SESSION['sucesso'] ?? '';
unset($_SESSION['erro'], $_SESSION['sucesso']); // Limpa para próxima vez

if (isset($_POST['editar'])) {
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["senha"] ?? "");

    // Validação básica (senha opcional)
    if (empty($nome) || empty($email)) {
        $_SESSION['erro'] = "Nome e email são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro'] = "Email inválido.";
    } elseif (!empty($senha) && strlen($senha) < 6) {
        $_SESSION['erro'] = "Senha deve ter pelo menos 6 caracteres.";
    } else {
        // Senha: Se vazia, busca atual do BD
        $senha_hash = '';
        if (empty($senha)) {
            $stmt_atual = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
            if ($stmt_atual) {
                $stmt_atual->bind_param("i", $id);
                $stmt_atual->execute();
                $result = $stmt_atual->get_result();
                if ($row = $result->fetch_assoc()) {
                    $senha_hash = $row['senha']; // Mantém a senha atual
                } else {
                    $_SESSION['erro'] = "Usuário não encontrado no BD.";
                }
                $stmt_atual->close();
            } else {
                $_SESSION['erro'] = "Erro ao buscar senha atual: " . $conn->error;
            }
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Hash novo
        }

        if (empty($_SESSION['erro'])) { // Só prossegue se sem erro
            // Prepared statement com placeholders corretos
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            if (!$stmt) {
                $_SESSION['erro'] = "Erro ao preparar query: " . $conn->error;
            } else {
                $stmt->bind_param("sssi", $nome, $email, $senha_hash, $id); // Agora $id existe
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $_SESSION['nome'] = $nome; // Atualiza sessão
                        $_SESSION['email'] = $email;
                        $_SESSION['sucesso'] = "Perfil atualizado com sucesso!";
                        $sucesso = true;
                        header("Location: perfil.php?sucesso=1"); // Redirect com param
                        exit;
                    } else {
                        $_SESSION['erro'] = "Nenhuma alteração foi feita (valores iguais aos atuais).";
                    }
                } else {
                    $_SESSION['erro'] = "Erro ao executar: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Fechar conexão se aberta
if (isset($conn)) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TREMzz - Editar Perfil</title>
    <link rel="shortcut icon" href="../../assets/img/tremzz_logo.png" />
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
    
    <!-- CSS mínimo para fundos exatos, hovers e filtros (essencial para fidelidade ao tema) -->
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
        .perfil-foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 1rem;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }
        .foto-container {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .foto-container .badge {
            opacity: 0;
            transition: opacity 0.3s ease;
            transform: translate(-50%, -50%);
        }
        .foto-container:hover .badge {
            opacity: 1;
        }
        .form-control-custom {
            background-color: #2a2a2a !important;
            color: #e0e0e0 !important;
            border: #121212;
        }
        .form-control-custom:focus {
            background-color: #2a2a2a !important;
            color: #e0e0e0 !important;
        }
        .form-control-custom::placeholder {
            color: #b0b0b0 !important;
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
        /* Responsividade mínima para mobile */
        @media (max-width: 768px) {
            .perfil-foto {
                width: 100px;
                height: 100px;
            }
        }
            /* Small-screen header tweaks: centraliza título e reduz padding do header */
            @media (max-width: 480px) {
                .navbar {
                    padding: .25rem .5rem !important;
                }
                .navbar .d-flex {
                    flex-direction: column;
                    gap: .25rem;
                    align-items: center;
                }
                .text-oi { width: 100%; text-align: center; }
                .text-oi h1, .header-titulo { text-align: center; margin: 0; font-size: 1.05rem; }
                .pfp { margin-top: 0; }
            }
    </style>
</head>

<body class="text-light">
    <!-- Header (idêntico ao perfil.php) -->
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-oi">
                        <h1 class="text-light fw-bold mb-0 fs-3">Privacidade e Segurança</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION['foto'] ?? '../../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container px-3" style="max-width: 900px; margin-bottom: 2rem;">
        <!-- Alert de Erro (novo: exibe se houver) -->
        <?php if (!empty($erro_msg)): ?>
            <div class="alert alert-danger text-center rounded-3" role="alert">
                <?php echo htmlspecialchars($erro_msg); ?>
            </div>
        <?php endif; ?>

        <!-- Alert de Sucesso (se processado na mesma página) -->
        <?php if (!empty($sucesso_msg)): ?>
            <div class="alert alert-success text-center rounded-3" role="alert">
                <?php echo htmlspecialchars($sucesso_msg); ?>
            </div>
        <?php endif; ?>

        <!-- Seção de edição principal -->
        <section class="perfil-header card bg-custom rounded-3 text-center mb-4 p-4">
            <form id="editProfileForm" method="POST" action="" enctype="multipart/form-data">
    

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold fs-6 text-light mb-2">Email</label>
                    <input type="email" class="form-control form-control-custom fs-6 text-center" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" placeholder="Digite seu email" required />
                </div>

                <!-- Telefone -->
                <div class="mb-3">
                    <label for="telefone" class="form-label fw-bold fs-6 text-light mb-2">Telefone</label>
                    <input type="telefone" class="form-control form-control-custom fs-6 text-center" id="telefone" name="telefone" value="<?php echo htmlspecialchars($_SESSION['telefone'] ?? ''); ?>" placeholder="Adicionar Telefone"/>
                </div>

                      <!-- Senha -->
                <div class="mb-3 position-relative">
                    <label for="senha" class="form-label fw-bold fs-6 text-light mb-2">Mudar Senha</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-custom fs-6 text-center" id="senha" name="senha" value="" placeholder="Adicionar Nova Senha"/>
                        <button type="button" class="btn btn-outline-secondary" id="toggleSenha" tabindex="-1" style="border-radius: 0 0.375rem 0.375rem 0;">
                            <i class="bi bi-eye" id="iconSenha"></i>
                        </button>
                    </div>
                </div>

                <!-- Autenticação de Dois Fatores -->
                <div class="mb-3">
                    <label class="form-label fw-bold fs-6 text-light mb-2">Autenticação de Dois Fatores</label>
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" id="doisFatores" name="doisFatores" <?php echo !empty($_SESSION['doisFatores']) ? 'checked' : ''; ?>>
                        <label class="form-check-label ms-2" for="doisFatores" style="color: #e0e0e0;">
                            Ativar autenticação via código SMS
                        </label>
                    </div>
                    <small class="text-secondary">Receba um código por SMS ou Email ao fazer login.</small>
                </div>

                <!-- Botões de ação -->
                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" name="editar" class="btn btn-danger fw-semibold fs-6 px-4">Salvar Alterações</button>
                    <a href="perfil.php" class="btn btn-outline-danger fw-semibold fs-6 px-4 text-decoration-none">Cancelar</a>
                </div>
            </form>
        </section>

        <script>
                    const senhaInput = document.getElementById('senha');
                    const toggleSenha = document.getElementById('toggleSenha');
                    const iconSenha = document.getElementById('iconSenha');
                    toggleSenha.addEventListener('click', function () {
                        if (senhaInput.type === 'password') {
                            senhaInput.type = 'text';
                            iconSenha.classList.remove('bi-eye');
                            iconSenha.classList.add('bi-eye-slash');
                        } else {
                            senhaInput.type = 'password';
                            iconSenha.classList.remove('bi-eye-slash');
                            iconSenha.classList.add('bi-eye');
                        }
                    });
                </script>

        <!-- Suporte a GET sucesso (para redirect) -->
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success text-center rounded-3" role="alert">
                Perfil atualizado com sucesso!
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer fixed bottom (idêntico ao perfil.php) -->
    <footer class="rodape position-fixed bottom-0 w-100 py-2 px-3" style="max-width: 900px; margin: 0 auto; left: 50%; transform: translateX(-50%); z-index: 1000;" role="contentinfo" aria-label="Menu de navegação inferior">
        <div class="d-flex justify-content-around align-items-center">
            <a href="home.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Início">
                <img src="../../assets/img/casa.png" alt="Início" />
            </a>
            <a href="buscar.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Buscar">
                <img src="../../assets/img/lupa.png" alt="Buscar" />
            </a>
            <a href="chat.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Chat">
                <img src="../../assets/img/chat.png" alt="Chat" />
            </a>
            <a href="perfil.php" class="footer-icon text-center text-decoration-none p-2" aria-label="Perfil">
                <img src="../../assets/img/perfil.png" alt="Perfil" />
            </a>
        </div>
    </footer>

</body>

</html>