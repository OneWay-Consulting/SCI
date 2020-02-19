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

//if($_SESSION['active']){
    //print_r($_POST);
    $_POST = String::sanitize($_POST,true);

    //echo "tiene dispatch : ".$_POST['dispatch'];
    if($_POST['dispatch']=="entry"){
        //echo "entra al add";
        if(Purchase::setRowsSerialPurchase($mysql,$_POST)){
            $msg = "Se realizo registro de entrada";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al crear el registro de entrada";
            $_SESSION['msg'] = $msg;
        }
    }elseif($_POST['dispatch']=="delivery") {
        //$flag = Purchase::tryDelete($mysql,$_GET['id']);
        //if($flag == 2){ //try to delete user
        if ($_POST['_status'] == 3){
            if (Sales::setRowsSerialSales($mysql, $_POST)){
                $msg = "Se realizo registro de entrega";
                $_SESSION['msg'] = $msg;
            } else {
                $msg = "Hubo un error al crear el registro de salida";
                $_SESSION['msg'] = $msg;
            }
        }//$_POST['_status'] == 3
    }elseif($_POST['dispatch']=="confirmdelivery"){
        if(Sales::updateConfirmDeliveryRow($mysql, $_POST)){
            $msg = "Se realizo actualizo confirmacion";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al crear el actulizar datos de confirmacion";
            $_SESSION['msg'] = $msg;
        }
    }elseif($_POST['dispatch']=="update"){
        if(Purchase::updateRow($mysql,$_POST)){
            $msg = "Se Actualizo el registro ".$_POST['id']." exitosamente";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al actualizar el registro: ".$_POST['id'];
            $_SESSION['msg'] = $msg;
        }
    /*}elseif($_POST['dispatch']=="addreturn"){
        if(Inventory::addReturnRow($mysql,$_POST)){
            $msg = "Se realizo devoluci&oacute;n exitosamente";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al realizar devoluci&ouacte;n ";
            $_SESSION['msg'] = $msg;
        }
    */
    }elseif($_POST['dispatch']=="tosavepurchase"){
        if(Purchase::setRowsSerialPurchaseByFile($mysql,$_POST)){
            $msg = "Se realizo registro exitosamente";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al realizar entrada ";
            $_SESSION['msg'] = $msg;
        }
    }elseif($_POST['dispatch']=="tosavesales"){
        if(Sales::setRowsSerialSalesByFile($mysql,$_POST)){
            $msg = "Se realizo registro exitosamente";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al realizar entrada ";
            $_SESSION['msg'] = $msg;
        }
    }
    header("Location: inventory.php");
//}
//else{
//    header("Location: ../index.php");
//}


?>
