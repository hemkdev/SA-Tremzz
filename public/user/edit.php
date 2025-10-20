<?php
require_once ("../../config/bd.php");

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
    $foto_atual = $_SESSION['foto'] ?? '../../assets/img/perfil.png';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../../assets/img/usuarios/';
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
                if ($foto_atual !== '../../assets/img/perfil.png' && file_exists($foto_atual)) {
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
    header("Location: perfil.php?sucesso=1"); // Redirect com param (perfil.php está na mesma pasta user/)
        exit;
    } else {
        $_SESSION['erro'] = "Nenhuma alteração foi feita (valores iguais aos atuais).";
    }


header ("location: perfil.php");
?>