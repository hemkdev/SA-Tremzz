<?php
// Include the database connection file
require_once("../../../config/bd.php");

// Get id parameter value from URL
$id = $_GET['id'];

// Delete row from the database table
$result = mysqli_query($conn, "DELETE FROM usuarios WHERE id = $id");

header("Location: ../usuarios.php");
?>