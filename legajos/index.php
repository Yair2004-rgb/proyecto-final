<?php
session_start();

// Verificar si la sesión está iniciada
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Obtener todos los años disponibles
$sql = "SELECT DISTINCT YEAR(fecha) AS anio FROM legajos ORDER BY anio DESC";
$anios_result = $conn->query($sql);

// Obtener los meses disponibles según el año seleccionado
$selected_year = isset($_GET['anio']) ? $_GET['anio'] : '';
$selected_month = isset($_GET['mes']) ? $_GET['mes'] : '';
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

if ($selected_year || $search_query || $selected_month) {
    $sql = "SELECT * FROM legajos WHERE 1=1";

    if ($selected_year) {
        $sql .= " AND YEAR(fecha) = '$selected_year'";
    }

    if ($selected_month) {
        $sql .= " AND MONTH(fecha) = '$selected_month'";
    }

    if ($search_query) {
        $sql .= " AND (nombres LIKE '%$search_query%' OR dni LIKE '%$search_query%')";
    }
} else {
    $sql = "SELECT * FROM legajos";
}

$result = $conn->query($sql);

$meses_result = [];
if ($selected_year) {
    $sql_meses = "SELECT DISTINCT MONTH(fecha) AS mes FROM legajos WHERE YEAR(fecha) = '$selected_year' ORDER BY mes";
    $meses_result = $conn->query($sql_meses);
}

$meses_en_espanol = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Legajos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .btn-custom {
            height: auto;
            padding: 5px 15px; /* Ajusta el alto con el padding */
            font-size: 14px;   /* Tamaño de letra */
            margin: 5px 0;     /* Margen vertical entre botones */
        }

        .button-container {
            background-color: white; /* Fondo blanco */
            padding: 10px;          /* Espaciado interno */
            border-radius: 5px;     /* Bordes redondeados */
            box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Sombra */
            margin-top: -20px;      /* Ajusta la posición vertical de los botones */
            width: auto;            /* Ancho automático */
            max-width: 140px;       /* Ancho máximo reducido */
            position: absolute;      /* Posicionamiento absoluto */
            right: 20px;            /* 20px desde la derecha */
            top: 20px;              /* 20px desde la parte superior */
        }

        .text-black {
            color: black; /* Color negro para el texto */
        }
    </style>
    <script>
        // Desactivar la selección de texto y el menú contextual
        document.addEventListener('DOMContentLoaded', function() {
            document.body.oncontextmenu = function() { return false; };
            document.body.style.userSelect = 'none'; // Deshabilitar selección de texto
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 p-3">
                <!-- Contenedor de botones -->
                <div class="button-container d-flex flex-column align-items-center mt-2">
                    <img src="images/admin.png" alt="Descripción de la imagen" class="img-fluid mb-2" style="max-height: 100px; width: auto;"> <!-- Imagen agregada -->
                    <p class="text-center mb-2 text-black">Administrador</p> <!-- Texto agregado con color negro -->
                    <a href="logout.php" class="btn btn-danger btn-sm btn-custom">Cerrar Sesión</a>
                    <a href="register.php" class="btn btn-secondary btn-sm btn-custom">Crear Usuario</a>
                </div>
                <img src="images/Picture1.png" alt="Logo" class="img-fluid" style="max-height: 200px;"> <!-- Imagen sin interacción -->
            </div>
        </div>
        <div class="container mt-5 index-background">
            <h2 class="text-center">Lista de Legajos</h2>
            <div class="col-md-12 text-right">
                <a href="create.php" class="btn btn-primary">Crear Legajo</a>
            </div><br>
            <div class="row">
                <div class="col-md-6">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o DNI" value="<?php echo htmlspecialchars($search_query); ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Formulario para seleccionar año y mes -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get">
                        <div class="input-group">
                            <select name="anio" class="form-control" onchange="this.form.submit()">
                                <option value="">Seleccione un año</option>
                                <?php while ($anio_row = $anios_result->fetch_assoc()): ?>
                                    <option value="<?php echo $anio_row['anio']; ?>" <?php echo $selected_year == $anio_row['anio'] ? 'selected' : ''; ?>><?php echo $anio_row['anio']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <?php if ($selected_year): ?>
                                <select name="mes" class="form-control" onchange="this.form.submit()">
                                    <option value="">Seleccione un mes</option>
                                    <?php while ($mes_row = $meses_result->fetch_assoc()): ?>
                                        <option value="<?php echo $mes_row['mes']; ?>" <?php echo $selected_month == $mes_row['mes'] ? 'selected' : ''; ?>><?php echo $meses_en_espanol[$mes_row['mes']]; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contenido de la tabla -->
            <div class="tab-content">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-styles">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres y Apellidos</th>
                                <th>DNI</th>
                                <th>N° de Folios</th>
                                <th>Fecha</th>
                                <th>N° de Caja</th>
                                <th>Tipo</th>
                                <th>Detalles</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nombres']; ?></td>
                                <td><?php echo $row['dni']; ?></td>
                                <td><?php echo $row['folios']; ?></td>
                                <td><?php echo $row['fecha']; ?></td>
                                <td><?php echo $row['caja']; ?></td>
                                <td><?php echo ucfirst($row['tipo']); ?></td>
                                <td>
                                    <?php if ($row['tipo'] == 'entrada'): ?>
                                        <strong>Lugar de Origen:</strong> <?php echo $row['origen']; ?><br>
                                        <strong>Encargado de Registro:</strong> <?php echo $row['registro_entrada']; ?><br>
                                        <strong>Encargado de Envío:</strong> <?php echo $row['envio']; ?><br>
                                        <strong>Motivo de Entrada:</strong> <?php echo $row['motivo_entrada']; ?>
                                    <?php elseif ($row['tipo'] == 'salida'): ?>
                                        <strong>Lugar de Destino:</strong> <?php echo $row['destino']; ?><br>
                                        <strong>Encargado de Registro:</strong> <?php echo $row['registro_salida']; ?><br>
                                        <strong>Motivo de Salida:</strong> <?php echo $row['motivo_salida']; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm mb-2">Editar</a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
