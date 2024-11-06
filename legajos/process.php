<?php
include 'db.php';

if (isset($_POST['save'])) {
    $nombres = $_POST['nombres'];
    $dni = $_POST['dni'];
    $folios = $_POST['folios'];
    $fecha = $_POST['fecha'];
    $caja = $_POST['caja'];
    $tipo = $_POST['tipo'];

    if ($tipo == 'entrada') {
        $origen = $_POST['origen'];
        $registro_entrada = $_POST['registro_entrada'];
        $envio = $_POST['envio'];
        $motivo_entrada = $_POST['motivo_entrada'];

        $sql = "INSERT INTO legajos (nombres, dni, folios, fecha, caja, tipo, origen, registro_entrada, envio, motivo_entrada)
                VALUES ('$nombres', '$dni', '$folios', '$fecha', '$caja', '$tipo', '$origen', '$registro_entrada', '$envio', '$motivo_entrada')";

    } else if ($tipo == 'salida') {
        $salida = $_POST['salida'];
        $registro_salida = $_POST['registro_salida'];
        $retiro = $_POST['retiro'];
        $motivo_salida = $_POST['motivo_salida'];

        $sql = "INSERT INTO legajos (nombres, dni, folios, fecha, caja, tipo, salida, registro_salida, retiro, motivo_salida)
                VALUES ('$nombres', '$dni', '$folios', '$fecha', '$caja', '$tipo', '$salida', '$registro_salida', '$retiro', '$motivo_salida')";
    }

    if ($conn->query($sql) === TRUE) {
        // echo "Nuevo registro creado con exito";
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
