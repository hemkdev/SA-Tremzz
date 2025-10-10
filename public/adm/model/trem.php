<?php
session_start();

// Verificação de sessão e admin (essencial para segurança)
if (!isset($_SESSION["conectado"]) || $_SESSION["conectado"] !== true || !isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: ../../login.php");
    exit;
}

require "../../../config/bd.php"; // Sua conexão MySQLi (ajuste o caminho se necessário)

// Processar apenas se for POST e o botão "editar" foi clicado (adição/edição)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    // Sanitizar e validar inputs
    $id = intval($_POST['id'] ?? 0); // Captura o ID hidden do modal. Se vazio, vira 0 (adição)
    $modelo = trim($_POST['modelo'] ?? '');
    $tipo_carga = trim($_POST['tipo_carga'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Validações básicas (campos obrigatórios e valores ENUM válidos)
    $statuses_validos = ['Disponível', 'Em rota', 'Em manutenção'];

    if (empty($modelo) || strlen($modelo) > 50) {
        $_SESSION['erro'] = "Modelo inválido ou obrigatório (máx. 50 caracteres).";
        header("Location: ../trens.php?erro=1");
        exit;
    }

    if (empty($tipo_carga) || strlen($tipo_carga) > 50) {
        $_SESSION['erro'] = "Tipo de carga inválido ou obrigatório (máx. 50 caracteres).";
        header("Location: ../trens.php?erro=1");
        exit;
    }

    if (empty($status) || !in_array($status, $statuses_validos)) {
        $_SESSION['erro'] = "Status inválido ou obrigatório.";
        header("Location: ../trens.php?erro=1");
        exit;
    }

    // Diferenciar adição de edição
    if ($id === 0) {  // Se ID é 0 ou vazio: ADIÇÃO (novo trem)
        $stmt = $conn->prepare("INSERT INTO Trens (modelo, tipo_carga, status) VALUES (?, ?, ?)");
        if (!$stmt) {
            $_SESSION['erro'] = "Erro ao preparar INSERT: " . $conn->error;
            header("Location: ../trens.php?erro=1");
            exit;
        }
        $stmt->bind_param("sss", $modelo, $tipo_carga, $status);  // Bind para INSERT (sem ID)
        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Trem adicionado com sucesso!";
            $stmt->close();
            $conn->close();
            header("Location: ../trens.php?sucesso=1");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao adicionar trem: " . $stmt->error;
            $stmt->close();
        }
    } else {  // Se ID > 0: EDIÇÃO (atualizar trem existente)
        $stmt = $conn->prepare("UPDATE Trens SET modelo = ?, tipo_carga = ?, status = ? WHERE id = ?");
        if (!$stmt) {
            $_SESSION['erro'] = "Erro ao preparar UPDATE: " . $conn->error;
            header("Location: ../trens.php?erro=1");
            exit;
        }
        $stmt->bind_param("sssi", $modelo, $tipo_carga, $status, $id);  // Bind para UPDATE (inclui ID no WHERE)
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['sucesso'] = "Trem atualizado com sucesso!";
            } else {
                $_SESSION['erro'] = "Nenhum trem encontrado com este ID.";
            }
            $stmt->close();
            $conn->close();
            header("Location: ../trens.php?sucesso=1");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar trem: " . $stmt->error;
            $stmt->close();
        }
    }

    // Fechar conexão em caso de erro
    $conn->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar'])) {
    // Processar exclusão (usando POST para segurança, id via POST)
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['erro'] = "ID inválido para exclusão.";
        header("Location: ../trens.php?erro=1");
        exit;
    }

    // Verificar se trem está em uso (ex: em Rotas ativas) - opcional, para evitar exclusão em cascata
    $stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM Rotas WHERE trem_id = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $em_uso = $stmt_check->get_result()->fetch_assoc()['count'] > 0;
    $stmt_check->close();

    if ($em_uso) {
        $_SESSION['erro'] = "Não é possível excluir este trem, pois está associado a rotas ativas.";
        header("Location: ../trens.php?erro=1");
        exit;
    }

    // Exclusão segura com prepared statement
    $stmt = $conn->prepare("DELETE FROM Trens WHERE id = ?");
    if (!$stmt) {
        $_SESSION['erro'] = "Erro ao preparar DELETE: " . $conn->error;
        header("Location: ../trens.php?erro=1");
        exit;
    }
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['sucesso'] = "Trem excluído com sucesso!";
        } else {
            $_SESSION['erro'] = "Nenhum trem encontrado com este ID.";
        }
        $stmt->close();
        $conn->close();
        header("Location: ../trens.php?sucesso=1");
        exit;
    } else {
        $_SESSION['erro'] = "Erro ao excluir trem: " . $stmt->error;
        $stmt->close();
    }

    // Fechar conexão em caso de erro
    $conn->close();
} else {
    // Se não for POST válido, redirecione para evitar acesso direto
    header("Location: ../trens.php");
    exit;
}
?>