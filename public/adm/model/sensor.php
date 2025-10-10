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
    $id = intval($_POST['id'] ?? 0); // ALTERAÇÃO 3: Captura o ID hidden do modal. Se vazio, vira 0 (adição)
    $localizacao = trim($_POST['localizacao'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');

    // Validações básicas (campos obrigatórios e valores ENUM válidos)
    $localizacoes_validas = ['Estação 1', 'Estação 2', 'Estação 3', 'Estação principal'];
    $tipos_validos = ['LDR', 'Ultrassônico', 'DHT11'];

    if (empty($localizacao) || !in_array($localizacao, $localizacoes_validas)) {
        $_SESSION['erro'] = "Localização inválida ou obrigatória.";
        header("Location: ../sensores.php?erro=1");
        exit;
    }

    if (empty($tipo) || !in_array($tipo, $tipos_validos)) {
        $_SESSION['erro'] = "Tipo de sensor inválido ou obrigatório.";
        header("Location: ../sensores.php?erro=1");
        exit;
    }

    // Diferenciar adição de edição
    if ($id === 0) {  // Se ID é 0 ou vazio: ADIÇÃO (novo sensor)
        $stmt = $conn->prepare("INSERT INTO sensores (localizacao, tipo) VALUES (?, ?)");
        if (!$stmt) {
            $_SESSION['erro'] = "Erro ao preparar INSERT: " . $conn->error;
            header("Location: ../sensores.php?erro=1");
            exit;
        }
        $stmt->bind_param("ss", $localizacao, $tipo);  // Bind para INSERT (sem ID)
        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Sensor adicionado com sucesso!";
            $stmt->close();
            $conn->close();
            header("Location: ../sensores.php?sucesso=1");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao adicionar sensor: " . $stmt->error;
            $stmt->close();
        }
    } else {  // Se ID > 0: EDIÇÃO (atualizar sensor existente)
        $stmt = $conn->prepare("UPDATE sensores SET localizacao = ?, tipo = ? WHERE id = ?");
        if (!$stmt) {
            $_SESSION['erro'] = "Erro ao preparar UPDATE: " . $conn->error;
            header("Location: ../sensores.php?erro=1");
            exit;
        }
        $stmt->bind_param("ssi", $localizacao, $tipo, $id);  // Bind para UPDATE (inclui ID no WHERE)
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['sucesso'] = "Sensor atualizado com sucesso!";
            } else {
                $_SESSION['erro'] = "Nenhum sensor encontrado com este ID.";
            }
            $stmt->close();
            $conn->close();
            header("Location: ../sensores.php?sucesso=1");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar sensor: " . $stmt->error;
            $stmt->close();
        }
    }

    // Fechar conexão em caso de erro
    $conn->close();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar'])) {

    $id = $_GET['id'];

    $result = mysqli_query($conn, "DELETE FROM sensores WHERE id = $id");

    header("Location: ../sensores.php");
} else {
    // Se não for POST válido, redirecione para evitar acesso direto
    header("Location: ../sensores.php");
    exit;
}
