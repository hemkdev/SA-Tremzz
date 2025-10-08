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
}

?>