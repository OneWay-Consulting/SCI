<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 02/08/2019
 * Time: 12:29 PM
 */

require_once("../includes/config.php");

session_start();

$mysql = new Mysql;

    $_POST = String::sanitize($_POST,true);

    if($_POST['dispatch']=="tosavetransformation"){
        if(Transformation::setRowsSerialTransformationByFile($mysql,$_POST)){
            $msg = "Se realizo registro exitosamente";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al realizar entrada ";
            $_SESSION['msg'] = $msg;
        }
    }
    header("Location: transformation.php");

?>
