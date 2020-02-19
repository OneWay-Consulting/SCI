<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 23/01/2019
 * Time: 05:53 PM
 */
require_once("../includes/config.php");

session_start();

$mysql = new Mysql;

if($_SESSION['active']){
    //print_r($_POST);
    $_POST = String::sanitize($_POST,true);

    //echo "tiene dispatch : ".$_POST['dispatch'];
    if($_POST['dispatch']=="add"){
        //echo "entra al add";
        if(Item::setPLRow($mysql,$_POST)){
            $msg = "Se creo nuevo registro";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al crear el registro";
            $_SESSION['msg'] = $msg;
        }
    }elseif($_POST['dispatch']=="update"){
        if(Item::updatePLRow($mysql,$_POST)){
            $msg = "Se Actualizo el registro ".$_POST['id']." exitosamente";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al actualizar el registro: ".$_POST['id'];
            $_SESSION['msg'] = $msg;
        }
    }elseif($_POST['dispatch']=="tosave"){
        if(Item::saveByFile($mysql,$_POST)){
            $msg = "Se cargo LP correctamente!";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al cargar LP masiva: ".$_POST['_filenamefinal'];
            $_SESSION['msg'] = $msg;
        }
        header("Location: pricelist.php");
        exit;
    }//else massive
    header("Location: pricelist.php");
}
else{
    header("Location: ../index.php");
}

?>