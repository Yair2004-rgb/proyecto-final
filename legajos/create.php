<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Legajos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="row">
    <div class="col-md-12 p-3 d-flex justify-content-around">
        <a href="index.php"><img src="images/Picture1.png" alt="Logo" class="img-fluid" style="max-height: 200px;"></a>
    </div>
    <div class="col-md-12 p-3 text-center">
        <a href="index.php" class="btn btn-secondary logoutbtn">Cancelar</a>
    </div>
</div>
<div class="container mt-5">
    <div class="card index-background"> <!-- Cambié bg-yellow a index-background -->
        <div class="card-header">
            <h2 class="text-center">Nuevo Registro</h2>
        </div>
        <div class="card-body">
            <form action="process.php" method="POST">
                <div class="form-group">
                    <label for="dni">DNI:</label>
                    <input type="text" class="form-control" id="dni" name="dni" required onblur="fetchDNIInfo()">
                </div>
                <div class="form-group">
                    <label for="nombres">Nombres y Apellidos:</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" required>
                </div>
                <div class="form-group">
                    <label for="folios">N° de Folios:</label>
                    <input type="number" class="form-control" id="folios" name="folios" required>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <div class="form-group">
                    <label for="caja">N° de Caja:</label>
                    <input type="text" class="form-control" id="caja" name="caja" required>
                </div>
                <div class="form-group">
                    <label for="entrada">Entrada:</label>
                    <input type="radio" id="entrada" name="tipo" checked value="entrada" required onclick="toggleFields('entrada')">
                    <label for="salida">Salida:</label>
                    <input type="radio" id="salida" name="tipo" value="salida" required onclick="toggleFields('salida')">
                </div>
                <div id="entrada-fields" class="linked-fields">
                    <div class="form-group">
                        <label for="origen">Lugar de Origen:</label>
                        <input type="text" class="form-control" id="origen" name="origen">
                    </div>
                    <div class="form-group">
                        <label for="registro_entrada">Encargado de Registro (Entrada):</label>
                        <input type="text" class="form-control" id="registro_entrada" name="registro_entrada">
                    </div>
                    <div class="form-group">
                        <label for="envio">Encargado de Envío:</label>
                        <input type="text" class="form-control" id="envio" name="envio">
                    </div>
                    <div class="form-group">
                        <label for="motivo_entrada">Motivo de Entrada:</label>
                        <input type="text" class="form-control" id="motivo_entrada" name="motivo_entrada">
                    </div>
                </div>
                <div id="salida-fields" class="linked-fields" style="display:none;">
                    <div class="form-group">
                        <label for="salida">Lugar de Salida:</label>
                        <input type="text" class="form-control" id="salida" name="salida">
                    </div>
                    <div class="form-group">
                        <label for="registro_salida">Encargado de Registro (Salida):</label>
                        <input type="text" class="form-control" id="registro_salida" name="registro_salida">
                    </div>
                    <div class="form-group">
                        <label for="retiro">Encargado de Retiro:</label>
                        <input type="text" class="form-control" id="retiro" name="retiro">
                    </div>
                    <div class="form-group">
                        <label for="motivo_salida">Motivo de Salida:</label>
                        <input type="text" class="form-control" id="motivo_salida" name="motivo_salida">
                    </div>
                </div>
                <button type="submit" name="save" class="btn btn-success">Guardar</button>
                <button type="reset" class="btn btn-warning">Limpiar</button>
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

    function fetchDNIInfo() {
        const dni = document.getElementById('dni').value;
        if (dni) {
            fetch(`fetch_dni.php?dni=${dni}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else if (data.nombres) {
                        document.getElementById('nombres').value = data.nombres + ' ' + data.apellidoPaterno + ' ' + data.apellidoMaterno;
                    }
                })
                .catch(error => {
                    console.error('Error fetching DNI info:', error);
                    alert('Hubo un error al obtener la información del DNI.');
                });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('fecha').value = today;
    });
</script>
</body>
</html>
