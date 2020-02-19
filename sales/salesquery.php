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
    if($_POST['dispatch']=="add"){
        //echo "entra al add";
        if(Sales::setRow($mysql,$_POST)){
            $msg = "Se creo nuevo registro";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al crear el registro";
            $_SESSION['msg'] = $msg;
        }
    }elseif($_GET['dispatch']=="delete"){
        $flag = Sales::tryDelete($mysql,$_GET['id']);
        if($flag == 2){ //try to delete user
            if(Sales::unsetRow($mysql,$_GET['id'])){
                $msg = "Se Elimino el registro ".$_GET['id']." exitosamente";
                $_SESSION['msg'] = $msg;
            }else{
                $msg = "Error al eliminar el registro de usuario id: ".$_GET['id'];
                $_SESSION['msg'] = $msg;
            }
        }else{
            $msg = "Error al eliminar Usuario, tiene tablas relacionadas ";
            $_SESSION['msg'] = $msg;
        }//tryDelete
    }elseif($_POST['dispatch']=="update"){
        if($_POST['_status']==3){
                if(Sales::updateRowDelivery($mysql, $_POST)){
                    $msg = "Se Actualizo el registro " . $_POST['id'] . " exitosamente";
                    $_SESSION['msg'] = $msg;
                } else {
                    $msg = "Hubo un error al actualizar el registro: " . $_POST['id'];
                    $_SESSION['msg'] = $msg;
                }
        }else {
            if (Sales::updateRow($mysql, $_POST)) {
                $msg = "Se Actualizo el registro " . $_POST['id'] . " exitosamente";
                $_SESSION['msg'] = $msg;
            } else {
                $msg = "Hubo un error al actualizar el registro: " . $_POST['id'];
                $_SESSION['msg'] = $msg;
            }
        }
    }elseif($_POST['dispatch']=="tosave"){
        if(Sales::saveByFile($mysql,$_POST)){
            $msg = "Se insertaron ventas correctamente!";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al ingresar carga masiva: ".$_POST['_filenamefinal'];
            $_SESSION['msg'] = $msg;
        }
        header("Location: quote.php");
        exit;
    }elseif($_POST['dispatch']=="payment"){
        if(Sales::updatePayment($mysql,$_POST)){
            $msg = "Se actualizo registro ".$_POST['id']." exitosamente!";
            $_SESSION['msg'] = $msg;
        }else{
            $msg = "Hubo un error al actualizar registro ".$_POST['id'];
            $_SESSION['msg'] = $msg;
        }
        header("Location: payment.php");
        exit;
    }elseif($_POST['dispatch']=="paymentdetail"){ //detail
        if(Sales::addPaymentDetail($mysql,$_POST)){
            $msg = "Se registro pago ".$_POST['id']." exitosamente!|".$_POST['_idsn'];
            $_SESSION['msg'] = $msg;

        }else{
            $msg = "Hubo un error al registrar pago ".$_POST['id']."|".$_POST['_idsn'];
            $_SESSION['msg'] = $msg;

        }
        header("Location: payment.php");
        exit;
    }//detail
    header("Location: sales.php");
}
else{
    header("Location: ../index.php");
}


?>
