<?php
require_once("../../../config/bd.php");

if (isset($_POST['editar'])) {

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
    $cargo = mysqli_real_escape_string($conn, $_POST['cargo']);

    $result = mysqli_query($conn, "UPDATE usuarios SET nome = '$nome', email = '$email', telefone = '$telefone', cargo = '$cargo' WHERE id = $id ");

        $editado = true;
        header("Location: ../usuarios.php");

} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar'])) {

    $id = $_GET['id'];

    $result = mysqli_query($conn, "DELETE FROM usuarios WHERE id = $id");

    header("Location: ../usuarios.php");
} else {
    // Se não for POST válido, redirecione para evitar acesso direto
    header("Location: ../usuarios.php");
    exit;
}

?>