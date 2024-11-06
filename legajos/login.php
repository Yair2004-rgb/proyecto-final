<?php
// Inicia la sesión
session_start();

// Incluye el archivo de conexión a la base de datos
include 'db.php';

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene los valores del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepara la consulta SQL para evitar inyección SQL
    $sql = "SELECT * FROM users WHERE username = ?";

    // Prepara la sentencia
    $stmt = $conn->prepare($sql);

    // Vincula los parámetros
    $stmt->bind_param("s", $username);

    // Ejecuta la consulta
    $stmt->execute();

    // Obtiene el resultado
    $result = $stmt->get_result();

    // Verifica si se encontró un usuario
    if ($result->num_rows > 0) {
        // Obtiene la fila del resultado
        $row = $result->fetch_assoc();

        // Verifica la contraseña
        if (password_verify($password, $row['password'])) {
            // Inicia la sesión
            $_SESSION['username'] = $username;

            // Redirige según el rol
            if ($row['role'] === 'admin') {
                header('Location: index.php'); // Redirige a index.php para administradores
            } else {
                header('Location: user_page.php'); // Redirige a la página de usuario
            }
            exit(); // Asegúrate de salir después de redirigir
        } else {
            // Muestra un mensaje de error
            $error = "Contraseña Incorrecta";
        }
    } else {
        // Muestra un mensaje de error
        $error = "Usuario no encontrado";
    }

    // Cierra la conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glowing Inputs Login Form UI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>
      @import url('https://fonts.googleapis.com/css?family=Poppins&display=swap');
      * {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
      }
      body {
        display: flex;
        height: 100vh;
        text-align: center;
        align-items: center;
        justify-content: center;
        background: #151515;
      }
      .login-form {
        position: relative;
        width: 370px;
        height: auto;
        background: #1b1b1b;
        padding: 40px 35px 60px;
        box-sizing: border-box;
        border: 1px solid black;
        border-radius: 5px;
        box-shadow: inset 0 0 1px #272727;
      }
      .text {
        font-size: 30px;
        color: #c7c7c7;
        font-weight: 600;
        letter-spacing: 2px;
      }
      form {
        margin-top: 40px;
      }
      .field {
        margin-top: 20px;
        display: flex;
      }
      .field .fas {
        height: 50px;
        width: 60px;
        color: #868686;
        font-size: 20px;
        line-height: 50px;
        border: 1px solid #444;
        border-right: none;
        border-radius: 5px 0 0 5px;
        background: linear-gradient(#333, #222);
      }
      .field input, form button {
        height: 50px;
        width: 100%;
        outline: none;
        font-size: 19px;
        color: #868686;
        padding: 0 15px;
        border-radius: 0 5px 5px 0;
        border: 1px solid #444;
        caret-color: #339933;
        background: linear-gradient(#333, #222);
      }
      input:focus {
        color: #339933;
        box-shadow: 0 0 5px rgba(0, 255, 0, .2),
                    inset 0 0 5px rgba(0, 255, 0, .1);
        background: linear-gradient(#333933, #222922);
        animation: glow .8s ease-out infinite alternate;
      }
      @keyframes glow {
        0% {
          border-color: #339933;
          box-shadow: 0 0 5px rgba(0, 255, 0, .2),
                      inset 0 0 5px rgba(0, 0, 0, .1);
        }
        100% {
          border-color: #6f6;
          box-shadow: 0 0 20px rgba(0, 255, 0, .6),
                      inset 0 0 10px rgba(0, 255, 0, .4);
        }
      }
      button {
        margin-top: 30px;
        border-radius: 5px !important;
        font-weight: 600;
        letter-spacing: 1px;
        cursor: pointer;
      }
      button:hover {
        color: #339933;
        border: 1px solid #339933;
        box-shadow: 0 0 5px rgba(0, 255, 0, .3),
                    0 0 10px rgba(0, 255, 0, .2),
                    0 0 15px rgba(0, 255, 0, .1),
                    0 2px 0 black;
      }
      .link {
        margin-top: 25px;
        color: #868686;
      }
      .link a {
        color: #339933;
        text-decoration: none;
      }
      .link a:hover {
        text-decoration: underline;
      }
      .alert {
        color: red;
        margin-top: 20px;
        font-size: 14px;
      }
    </style>
</head>
<body>
    <div class="login-form">
        <div class="text">Iniciar Sesion</div>
        <form method="POST" action="login.php">
            <div class="field">
                <div class="fas fa-envelope"></div>
                <input type="text" name="username" placeholder="Usuario" required>
            </div>
            <div class="field">
                <div class="fas fa-lock"></div>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <?php if (isset($error)) { ?>
                <div class="alert">
                    <?= $error ?>
                </div>
            <?php } ?>
            <button type="submit">Iniciar</button>
        </form>
    </div>
</body>
</html>
