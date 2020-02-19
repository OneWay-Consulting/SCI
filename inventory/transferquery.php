<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 20/08/2019
 * Time: 06:17 PM
 */

require_once("../includes/config.php");

session_start();

$mysql = new Mysql;

if($_SESSION['active']){
    //print_r($_POST);
    $_POST = String::sanitize($_POST, true);

    //echo "tiene dispatch : ".$_POST['dispatch'];
    if ($_POST['dispatch'] == "add") {
        if (Transfer::setRow($mysql, $_POST)) {
            $msg = "Se inserto registro " . $_POST['id'] . " exitosamente";
            $_SESSION['msg'] = $msg;
        } else {
            $msg = "Hubo un error al ingresar registro: " . $_POST['id'];
            $_SESSION['msg'] = $msg;
        }
    }//add
    elseif($_POST['dispatch']=="entry"){
        if (Transfer::setRowSerial($mysql, $_POST)) {
            $msg = "Se inserto registro " . $_POST['id'] . " exitosamente";
            $_SESSION['msg'] = $msg;
        } else {
            $msg = "Hubo un error al ingresar registro: " . $_POST['id'];
            $_SESSION['msg'] = $msg;
        }
    }
    header("Location: transfer.php");
}//active
else{
    header("Location: ../index.php");
}


?>