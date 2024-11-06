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
        .page-transition {
            transition: opacity 0.5s;
        }

        .page-transition.out {
            opacity: 0;
        }

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

        /* Estilos para la burbuja de chat */
        .chat-bubble {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: background-color 0.3s;
            z-index: 1000; /* Asegura que la burbuja esté siempre por encima */
        }

        .chat-bubble:hover {
            background-color: #0056b3;
        }

        .chat-icon {
            font-size: 24px;
        }

        /* Estilos para el formulario de chat */
        .chat-form {
            display: none; /* Oculto por defecto */
            position: fixed;
            bottom: 80px; /* Justo encima de la burbuja */
            right: 20px;
            background-color: white;
            border: 1px solid #007bff;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            width: 300px; /* Ancho del formulario */
            z-index: 1000;
        }

        .chat-form textarea {
            width: 100%;
            height: 50px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            padding: 5px;
        }

        .chat-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .chat-form button:hover {
            background-color: #0056b3;
        }

        .chat-message {
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 5px;
            background-color: #f1f1f1;
        }
    </style>
</head>
<body class="page-transition" oncontextmenu="return false;" onselectstart="return false;" ondragstart="return false;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 p-3">
                <!-- Contenedor de botones -->
                <div class="button-container d-flex flex-column align-items-center mt-2">
                    <img src="images/picture3.png" alt="Descripción de la imagen" class="img-fluid mb-2" style="max-height: 100px; width: auto;"> <!-- Imagen agregada -->
                    <p class="text-center mb-2 text-black">Usuario</p> <!-- Texto agregado con color negro -->
                    <a href="logout.php" class="btn btn-danger btn-sm btn-custom">Cerrar Sesión</a>
                </div>
                <img src="images/Picture1.png" alt="Logo" class="img-fluid" style="max-height: 200px;"> <!-- Imagen sin interacción -->
            </div>
        </div>
        <div class="container mt-5 index-background">
            <h2 class="text-center">Lista de Legajos</h2>
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
                            <select name="mes" class="form-control" onchange="this.form.submit()">
                                <option value="">Seleccione un mes</option>
                                <?php foreach ($meses_result as $mes_row): ?>
                                    <option value="<?php echo $mes_row['mes']; ?>" <?php echo $selected_month == $mes_row['mes'] ? 'selected' : ''; ?>><?php echo $meses_en_espanol[$mes_row['mes']]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de legajos -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
                                <th>DNI</th>
                                <th>Folios</th>
                                <th>Fecha</th>
                                <th>Caja</th>
                                <th>Tipo</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
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
                                        <?php else: ?>
                                            <strong>Lugar de Salida:</strong> <?php echo $row['salida']; ?><br>
                                            <strong>Encargado de Registro:</strong> <?php echo $row['registro_salida']; ?><br>
                                            <strong>Encargado de Recepción:</strong> <?php echo $row['recepcion']; ?><br>
                                            <strong>Motivo de Salida:</strong> <?php echo $row['motivo_salida']; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Burbuja de chat -->
    <div class="chat-bubble" onclick="toggleChat()">
        <i class="fas fa-comments chat-icon"></i>
    </div>

    <!-- Formulario de chat -->
    <div class="chat-form" id="chatForm">
        <textarea id="chatInput" placeholder="Escribe tu pregunta..."></textarea>
        <button onclick="sendChat()">Enviar</button>
        <div id="chatMessages"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
        document.body.classList.add('page-transition');
        document.querySelectorAll('a[href]').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                document.body.classList.add('out');
                setTimeout(function() {
                    window.location.href = link.href;
                }, 500);
            });
        });

        // Función para mostrar/ocultar el formulario de chat
        function toggleChat() {
            const chatForm = document.getElementById('chatForm');
            chatForm.style.display = chatForm.style.display === 'none' || chatForm.style.display === '' ? 'block' : 'none';
        }

        // Función para enviar mensajes de chat
        function sendChat() {
            const input = document.getElementById('chatInput');
            const messagesDiv = document.getElementById('chatMessages');

            if (input.value.trim() === '') return; // No enviar mensajes vacíos

            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message';
            messageDiv.textContent = input.value;
            messagesDiv.appendChild(messageDiv);
            input.value = ''; // Limpiar el campo de entrada
            messagesDiv.scrollTop = messagesDiv.scrollHeight; // Desplazar hacia abajo
        }
    </script>
</body>
</html>
