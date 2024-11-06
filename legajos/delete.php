<?php
include 'db.php';
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $sql = "DELETE FROM legajos WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Rregistro eliminado con exito";
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();

    // Redirect back to the index page
    header('Location: index.php');
    exit();
}
?>
