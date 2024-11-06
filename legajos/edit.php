<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>
<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM legajos WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $tipo = $row['tipo'];
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
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

        $sql = "UPDATE legajos SET nombres='$nombres', dni='$dni', folios='$folios', fecha='$fecha', caja='$caja', tipo='$tipo', origen='$origen', registro_entrada='$registro_entrada', envio='$envio', motivo_entrada='$motivo_entrada' WHERE id=$id";
    } else if ($tipo == 'salida') {
        $salida = $_POST['salida'];
        $registro_salida = $_POST['registro_salida'];
        $retiro = $_POST['retiro'];
        $motivo_salida = $_POST['motivo_salida'];

        $sql = "UPDATE legajos SET nombres='$nombres', dni='$dni', folios='$folios', fecha='$fecha', caja='$caja', tipo='$tipo', salida='$salida', registro_salida='$registro_salida', retiro='$retiro', motivo_salida='$motivo_salida' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Registro actualizado con éxito";
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Legajo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="row">
        <div class="col-md-12 p-3 text-center">
            <a href="index.php"><img src="images/Picture1.png" alt="Logo" class="img-fluid" style="max-height: 200px;"></a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <a href="index.php" class="btn logoutbtn">Cancelar</a> <!-- Clase logoutbtn -->
        </div>
    </div>
    <div class="container mt-5">
        <div class="card index-background"> <!-- Cambié bg-yellow a index-background -->
            <div class="card-header">
                <h2 class="text-center">Editar Legajo</h2>
            </div>
            <div class="card-body">
                <form action="edit.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="form-group">
                        <label for="nombres">Nombres y Apellidos:</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo $row['nombres']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dni">DNI:</label>
                        <input type="text" class="form-control" id="dni" name="dni" value="<?php echo $row['dni']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="folios">N° de Folios:</label>
                        <input type="number" class="form-control" id="folios" name="folios" value="<?php echo $row['folios']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha">Fecha:</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $row['fecha']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="caja">N° de Caja:</label>
                        <input type="text" class="form-control" id="caja" name="caja" value="<?php echo $row['caja']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="entrada">Entrada:</label>
                        <input type="radio" id="entrada" name="tipo" value="entrada" <?php if($tipo == 'entrada') echo 'checked'; ?> onclick="toggleFields('entrada')">
                        <label for="salida">Salida:</label>
                        <input type="radio" id="salida" name="tipo" value="salida" <?php if($tipo == 'salida') echo 'checked'; ?> onclick="toggleFields('salida')">
                    </div>
                    <div id="entrada-fields" class="linked-fields" style="<?php echo $tipo == 'entrada' ? '' : 'display:none;'; ?>">
                        <div class="form-group">
                            <label for="origen">Lugar de Origen:</label>
                            <input type="text" class="form-control" id="origen" name="origen" value="<?php echo $row['origen']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="registro_entrada">Encargado de Registro (Entrada):</label>
                            <input type="text" class="form-control" id="registro_entrada" name="registro_entrada" value="<?php echo $row['registro_entrada']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="envio">Encargado de Envío:</label>
                            <input type="text" class="form-control" id="envio" name="envio" value="<?php echo $row['envio']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="motivo_entrada">Motivo de Entrada:</label>
                            <input type="text" class="form-control" id="motivo_entrada" name="motivo_entrada" value="<?php echo $row['motivo_entrada']; ?>">
                        </div>
                    </div>
                    <div id="salida-fields" class="linked-fields" style="<?php echo $tipo == 'salida' ? '' : 'display:none;'; ?>">
                        <div class="form-group">
                            <label for="salida">Lugar de Salida:</label>
                            <input type="text" class="form-control" id="salida" name="salida" value="<?php echo $row['salida']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="registro_salida">Encargado de Registro (Salida):</label>
                            <input type="text" class="form-control" id="registro_salida" name="registro_salida" value="<?php echo $row['registro_salida']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="retiro">Encargado de Retiro:</label>
                            <input type="text" class="form-control" id="retiro" name="retiro" value="<?php echo $row['retiro']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="motivo_salida">Motivo de Salida:</label>
                            <input type="text" class="form-control" id="motivo_salida" name="motivo_salida" value="<?php echo $row['motivo_salida']; ?>">
                        </div>
                    </div>
                    <button type="submit" name="update" class="btn btn-success">Actualizar</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function toggleFields(type) {
            if (type === 'entrada') {
                document.getElementById('entrada-fields').style.display = 'block';
                document.getElementById('salida-fields').style.display = 'none';
            } else {
                document.getElementById('entrada-fields').style.display = 'none';
                document.getElementById('salida-fields').style.display = 'block';
            }
        }
    </script>
</body>
</html>
