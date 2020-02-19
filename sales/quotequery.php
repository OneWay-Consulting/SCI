<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 16/11/2018
 * Time: 12:29 PM
 */

//require_once("../header.php");
//_includes();
require_once("../includes/config.php");

session_start();

$mysql = new Mysql;

if($_SESSION['active']){
    //print_r($_POST);
    $_POST = String::sanitize($_POST,true);

    //echo "tiene dispatch : ".$_POST['dispatch'];
    if($_POST['dispatch']=="add") {
        if(Quote::setRow($mysql,$_POST)){
            $msg = "Se inserto registro " . $_POST['id'] . " exitosamente";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al ingresar registro: " . $_POST['id'];
            $_SESSION['msg'] = $msg;
        }
    }elseif($_POST['dispatch']=="update") {
        if($_POST['_status']==1){
            echo "<br />_status == 1";
            if (Quote::updateRowToCancel($mysql, $_POST)) {
                $msg = "Se Actualizo el registro " . $_POST['id'] . " exitosamente";
                $_SESSION['msg'] = $msg;
            } else {
                $msg = "Hubo un error al actualizar el registro: " . $_POST['id'];
                $_SESSION['msg'] = $msg;
            }
        } elseif ($_POST['_status'] == 4) {
            echo "<br />_status == 4";
            if (Quote::updateRowToSales($mysql, $_POST)) {
                $msg = "Se Actualizo el registro " . $_POST['id'] . " exitosamente";
                $_SESSION['msg'] = $msg;
            } else {
                $msg = "Hubo un error al actualizar el registro: " . $_POST['id'];
                $_SESSION['msg'] = $msg;
            }
        }//_status = 4
    }elseif($_POST['dispatch']=="cancel"){
        if (Quote::cancelQuote($mysql, $_POST)) {
            $msg = "Se cancelaron Cotizaciones exitosamente";
            $_SESSION['msg'] = $msg;
        } else {
            $msg = "Hubo un error al cancelar cotizaciones";
            $_SESSION['msg'] = $msg;
        }
    }//update
    //echo "<br />mensaje:".$msg;
    header("Location: quote.php");
}
else{
    header("Location: ../index.php");
}

?>