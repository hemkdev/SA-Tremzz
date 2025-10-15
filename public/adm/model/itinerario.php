<?php
session_start();

// Verificação de sessão e admin (essencial para segurança)
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: ../../login.php");
    exit;
}

require "../../../config/bd.php"; // Sua conexão MySQLi (ajuste o caminho se necessário)

// Processar apenas se for POST e o botão "editar" foi clicado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    // Sanitizar e validar inputs
    $id = intval($_POST['id'] ?? 0); // Captura o ID hidden do modal. Se vazio, vira 0 (adição)
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    // Validações básicas (campos obrigatórios)
    if (empty($nome) || strlen($nome) > 100) {
        $_SESSION['erro'] = "Nome inválido ou obrigatório (máx. 100 caracteres).";
        header("Location: ../itinerarios.php?erro=1");
        exit;
    }

    // Descrição não é obrigatória, mas pode ter limite se desejar (ex: 255 chars)
    if (!empty($descricao) && strlen($descricao) > 255) {
        $_SESSION['erro'] = "Descrição muito longa (máx. 255 caracteres).";
        header("Location: ../itinerarios.php?erro=1");
        exit;
    }

    // Diferenciar adição de edição
    if ($id === 0) {  // Se ID é 0 ou vazio: ADIÇÃO (novo itinerário)
        $stmt = $conn->prepare("INSERT INTO itinerarios (nome, descricao) VALUES (?, ?)");
        if (!$stmt) {
            $_SESSION['erro'] = "Erro ao preparar INSERT: " . $conn->error;
            header("Location: ../itinerarios.php?erro=1");
            exit;
        }
        $stmt->bind_param("ss", $nome, $descricao);  // Bind para INSERT (sem ID)
        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Itinerário adicionado com sucesso!";
            $stmt->close();
            $conn->close();
            header("Location: ../itinerarios.php?sucesso=1");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao adicionar itinerário: " . $stmt->error;
            $stmt->close();
        }
    } else {  // Se ID > 0: EDIÇÃO (atualizar itinerário existente)
        $stmt = $conn->prepare("UPDATE itinerarios SET nome = ?, descricao = ? WHERE id = ?");
        if (!$stmt) {
            $_SESSION['erro'] = "Erro ao preparar UPDATE: " . $conn->error;
            header("Location: ../itinerarios.php?erro=1");
            exit;
        }
        $stmt->bind_param("ssi", $nome, $descricao, $id);  // Bind para UPDATE (inclui ID no WHERE)
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['sucesso'] = "Itinerário atualizado com sucesso!";
            } else {
                $_SESSION['erro'] = "Nenhum itinerário encontrado com este ID.";
            }
            $stmt->close();
            $conn->close();
            header("Location: ../itinerarios.php?sucesso=1");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar itinerário: " . $stmt->error;
            $stmt->close();
        }
    }

    // Fechar conexão em caso de erro
    $conn->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar'])) {
    // Processar exclusão
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['erro'] = "ID inválido para exclusão.";
        header("Location: ../itinerarios.php?erro=1");
        exit;
    }

    // Exclusão segura com prepared statement
    $stmt = $conn->prepare("DELETE FROM itinerarios WHERE id = ?");
    if (!$stmt) {
        $_SESSION['erro'] = "Erro ao preparar DELETE: " . $conn->error;
        header("Location: ../itinerarios.php?erro=1");
        exit;
    }
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['sucesso'] = "Itinerário excluído com sucesso!";
        } else {
            $_SESSION['erro'] = "Nenhum itinerário encontrado com este ID.";
        }
        $stmt->close();
        $conn->close();
        header("Location: ../itinerarios.php?sucesso=1");
        exit;
    } else {
        $_SESSION['erro'] = "Erro ao excluir itinerário: " . $stmt->error;
        $stmt->close();
    }

    // Fechar conexão em caso de erro
    $conn->close();
} else {
    // Se não for POST válido, redirecione para evitar acesso direto
    header("Location: ../itinerarios.php");
    exit;
}
?>
