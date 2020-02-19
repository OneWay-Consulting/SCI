<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 01/11/2018
 * Time: 04:54 PM
 * mail: zurdokw@gmail.com
 */
?>
<?php
require_once("includes/config.php");

session_start();

if($_SESSION['active'])
    if($_SESSION['user']->getUser()->idtcuser>0)
        header("Location: start.php");

//session_destroy();
$_SESSION['user'] = new Session;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Control de inventario</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="contenedor-form">
    <div class="toggle">
        <span></span>
    </div>

    <div class="formulario">
        <h2>Acceso | Control de inventario</h2>
        <form action="sso/securityaccess.php" method="post">
            <input type="text" id="user" name="user" placeholder="Usuario" required>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <input type="submit" value="Iniciar Sesi&oacute;n">
        </form>
    </div>
</div>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
