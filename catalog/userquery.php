<?php
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
		if(User::setRow($mysql,$_POST)){
			$msg = "Se creo nuevo registro";
			$_SESSION['msg'] = $msg;
		}else{
			$msg = "Hubo un error al crear el registro";	
			$_SESSION['msg'] = $msg;
		}
	}elseif($_GET['dispatch']=="delete"){
		$flag = User::tryDelete($mysql,$_GET['id']);
		if($flag == 2){ //try to delete user
			if(User::unsetRow($mysql,$_GET['id'])){
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
		if(User::updateRow($mysql,$_POST)){
			$msg = "Se Actualizo el registro ".$_POST['id']." exitosamente";
			$_SESSION['msg'] = $msg;			
		}else{
			$msg = "Hubo un error al actualizar el registro: ".$_POST['id'];	
			$_SESSION['msg'] = $msg;			
		}
	}
	header("Location: user.php");
}
else{
	header("Location: ../index.php");	
}

?>