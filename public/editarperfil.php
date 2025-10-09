<?php
session_start();
require "../config/bd.php";

$_SESSION['erro'] = "";
$_SESSION['sucesso'] = "";

// Debug temporário (acesso via ?debug=1 na URL - comente após testar)
if (isset($_GET['debug'])) {
    echo "<pre>Debug Sessão: " . print_r($_SESSION, true) . "</pre>";
    if (isset($conn)) {
        $stmt_debug = $conn->prepare("SELECT id, nome, foto FROM usuarios WHERE id = ?");
        $stmt_debug->bind_param("i", $_SESSION['id']);
        $stmt_debug->execute();
        $result = $stmt_debug->get_result();
        $user = $result->fetch_assoc();
        echo "<pre>Debug BD: " . print_r($user, true) . "</pre>";
        $stmt_debug->close();
    }
    exit;
}

// Verificação de login
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true) {
    header("Location: login.php");
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
    $alteracao_feita = false;
    $erros = [];

    // Processar edição de nome
    $stmt_nome = $conn->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
    if (!$stmt_nome) {
        $erros[] = "Erro ao preparar query para nome: " . $conn->error;
    } else {
        $stmt_nome->bind_param("si", $nome, $id);
        if ($stmt_nome->execute()) {
            if ($stmt_nome->affected_rows > 0) {
                $_SESSION['nome'] = $nome; // Atualiza sessão
                $alteracao_feita = true;
            }
        } else {
            $erros[] = "Erro ao executar atualização de nome: " . $stmt_nome->error;
        }
        $stmt_nome->close();
    }

    // Processar upload de foto (se enviada)
    $foto_atual = $_SESSION['foto'] ?? '../assets/img/perfil.png';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/img/usuarios/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_size = $_FILES['foto']['size'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validações
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file_extension, $allowed_extensions)) {
            $erros[] = "Tipo de arquivo não permitido. Use JPG, JPEG, PNG ou GIF.";
        } elseif ($file_size > $max_size) {
            $erros[] = "Arquivo muito grande. Máximo 5MB.";
        } else {
            // Gerar nome único para o arquivo
            $new_filename = 'usuario_' . $id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Deletar foto antiga se não for a padrão
                if ($foto_atual !== '../assets/img/perfil.png' && file_exists($foto_atual)) {
                    unlink($foto_atual);
                }

                // Atualizar banco de dados
                $stmt_foto = $conn->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
                if (!$stmt_foto) {
                    $erros[] = "Erro ao preparar query para foto: " . $conn->error;
                } else {
                    $stmt_foto->bind_param("si", $upload_path, $id); // "s" para string (caminho), "i" para int (id)
                    if ($stmt_foto->execute() && $stmt_foto->affected_rows > 0) {
                        $_SESSION['foto'] = $upload_path; // Atualiza sessão
                        $alteracao_feita = true;
                    } else {
                        $erros[] = "Erro ao atualizar foto no banco de dados: " . $stmt_foto->error;
                    }
                    $stmt_foto->close();
                }
            } else {
                $erros[] = "Erro ao mover o arquivo para o servidor.";
            }
        }
    }

    // Definir mensagens baseadas nos resultados
    if (!empty($erros)) {
        $_SESSION['erro'] = implode(' ', $erros);
    } elseif ($alteracao_feita) {
        $_SESSION['sucesso'] = "Perfil atualizado com sucesso!";
        $sucesso = true;
        header("Location: perfil.php?sucesso=1"); // Redirect com param
        exit;
    } else {
        $_SESSION['erro'] = "Nenhuma alteração foi feita (valores iguais aos atuais).";
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
    <link rel="shortcut icon" href="../assets/img/tremlogo.png" />
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
            filter: brightness(0) invert(1) drop-shadow(0 0 15px rgba(255, 193, 7, 0.8)) sepia(1) saturate(5) hue-rotate(-10deg);
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
    </style>
</head>

<body class="text-light">
    <!-- Header (idêntico ao perfil.php) -->
    <header class="bg-transparent">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent px-3 py-2">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="text-oi">
                        <h1 class="text-light fw-bold mb-0 fs-3">Editar Perfil</h1>
                    </div>
                    <div class="pfp">
                        <img src="<?php echo htmlspecialchars($_SESSION['foto'] ?? '../assets/img/perfil.png'); ?>" alt="Foto de perfil" class="pfp-img" />
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

        <!-- Seção de edição principal -->
        <section class="perfil-header card bg-custom rounded-3 text-center mb-4 p-4">
            <form id="editProfileForm" method="POST" action="" enctype="multipart/form-data">

                <!-- Foto de perfil -->
                <div class="mb-3">
                    <label for="fotoPerfil" class="foto-container">
                        <img src="<?php echo htmlspecialchars($_SESSION['foto'] ?? '../assets/img/perfil.png'); ?>" alt="Foto de perfil" id="previewFoto" class="perfil-foto" />
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger">
                            <i class="bi bi-camera-fill"></i> Alterar
                        </span>
                    </label>
                    <small class="text-white d-block text-center mt-2">Apenas imagens (JPG, PNG, GIF). Máximo 5MB.</small>
                    <input type="file" class="form-control form-control-custom mt-2 d-none" id="fotoPerfil" name="foto" accept="image/*" onchange="previewImage(this)" />
                </div>

                <!-- Nome -->
                <div class="mb-3">
                    <label for="nome" class="form-label fw-bold fs-4 text-light mb-2">Nome Completo</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-custom fs-5 text-center" id="nome" name="nome" value="<?php echo htmlspecialchars($_SESSION['nome'] ?? ''); ?>" placeholder="Digite seu nome" required />
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" name="editar" class="btn btn-danger fw-semibold fs-6 px-4">Salvar Alterações</button>
                    <a href="perfil.php" class="btn btn-outline-danger fw-semibold fs-6 px-4 text-decoration-none">Cancelar</a>
                </div>
            </form>
        </section>

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

    <!-- Script para preview da imagem -->
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewFoto').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</body>

</html>