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
    $id_trem = intval($_POST['id_trem'] ?? 0);
    $tipo = trim($_POST['tipo'] ?? '');
    $data_agendada = trim($_POST['data_agendada'] ?? '');
    $data_conclusao = trim($_POST['data_conclusao'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Validações básicas (campos obrigatórios e valores válidos)
    $tipos_validos = ['técnica', 'sistema'];
    $statuses_validos = ['Pendente', 'Em andamento', 'Concluída'];

    if ($id_trem <= 0) {
        $_SESSION['erro'] = "Trem inválido ou obrigatório.";
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }

    // Verificar se o trem existe
    $stmt_check_trem = $conn->prepare("SELECT COUNT(*) as count FROM Trens WHERE id = ?");
    $stmt_check_trem->bind_param("i", $id_trem);
    $stmt_check_trem->execute();
    if ($stmt_check_trem->get_result()->fetch_assoc()['count'] == 0) {
        $_SESSION['erro'] = "Trem selecionado não existe.";
        $stmt_check_trem->close();
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }
    $stmt_check_trem->close();

    if (empty($tipo) || !in_array($tipo, $tipos_validos)) {
        $_SESSION['erro'] = "Tipo inválido ou obrigatório.";
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }

    if (empty($data_agendada) || !strtotime($data_agendada)) {
        $_SESSION['erro'] = "Data agendada inválida ou obrigatória.";
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }

    if (empty($data_conclusao) || !strtotime($data_conclusao)) {
        $_SESSION['erro'] = "Data conclusão inválida ou obrigatória.";
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }

    if (strtotime($data_agendada) > strtotime($data_conclusao)) {
        $_SESSION['erro'] = "Data agendada não pode ser posterior à data conclusão.";
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }

    if (empty($status) || !in_array($status, $statuses_validos)) {
        $_SESSION['erro'] = "Status inválido ou obrigatório.";
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }

    // Diferenciar adição de edição
    if ($id === 0) {  // Se ID é 0 ou vazio: ADIÇÃO (nova manutenção)
        $stmt = $conn->prepare("INSERT INTO manutencoes (id_trem, tipo, data_agendada, data_conclusao, status) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            $_SESSION['erro'] = "Erro ao preparar INSERT: " . $conn->error;
            header("Location: ../manutencoes.php?erro=1");
            exit;
        }
        $stmt->bind_param("issss", $id_trem, $tipo, $data_agendada, $data_conclusao, $status);  // Bind para INSERT (sem ID)
        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Manutenção adicionada com sucesso!";
            $stmt->close();
            $conn->close();
            header("Location: ../manutencoes.php?sucesso=1");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao adicionar manutenção: " . $stmt->error;
            $stmt->close();
        }
    } else {  // Se ID > 0: EDIÇÃO (atualizar manutenção existente)
        $stmt = $conn->prepare("UPDATE manutencoes SET id_trem = ?, tipo = ?, data_agendada = ?, data_conclusao = ?, status = ? WHERE id = ?");
        if (!$stmt) {
            $_SESSION['erro'] = "Erro ao preparar UPDATE: " . $conn->error;
            header("Location: ../manutencoes.php?erro=1");
            exit;
        }
        $stmt->bind_param("issssi", $id_trem, $tipo, $data_agendada, $data_conclusao, $status, $id);  // Bind para UPDATE (inclui ID no WHERE)
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['sucesso'] = "Manutenção atualizada com sucesso!";
            } else {
                $_SESSION['erro'] = "Nenhuma manutenção encontrada com este ID.";
            }
            $stmt->close();
            $conn->close();
            header("Location: ../manutencoes.php?sucesso=1");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar manutenção: " . $stmt->error;
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
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }

    // Exclusão segura com prepared statement
    $stmt = $conn->prepare("DELETE FROM manutencoes WHERE id = ?");
    if (!$stmt) {
        $_SESSION['erro'] = "Erro ao preparar DELETE: " . $conn->error;
        header("Location: ../manutencoes.php?erro=1");
        exit;
    }
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['sucesso'] = "Manutenção excluída com sucesso!";
        } else {
            $_SESSION['erro'] = "Nenhuma manutenção encontrada com este ID.";
        }
        $stmt->close();
        $conn->close();
        header("Location: ../manutencoes.php?sucesso=1");
        exit;
    } else {
        $_SESSION['erro'] = "Erro ao excluir manutenção: " . $stmt->error;
        $stmt->close();
    }

    // Fechar conexão em caso de erro
    $conn->close();
} else {
    // Se não for POST válido, redirecione para evitar acesso direto
    header("Location: ../manutencoes.php");
    exit;
}
?>
