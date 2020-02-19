<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 23/05/2019
 * Time: 01:49 PM
 */


require_once("../includes/config.php");

session_start();

$mysql = new Mysql;

if($_SESSION['active']){

    $_POST = String::sanitize($_POST,true);

    if($_POST['dispatch']=="addreturn") {
        if (Returns::addReturnRow($mysql, $_POST)) {
            $msg = "Se realizo devoluci&oacute;n exitosamente";
            $_SESSION['msg'] = $msg;
        } else {
            $msg = "Hubo un error al realizar devoluci&ouacte;n ";
            $_SESSION['msg'] = $msg;
        }
    }

    header("Location: return.php");
}
else{
    header("Location: ../index.php");
}

?>