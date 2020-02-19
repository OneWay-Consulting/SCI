<?php
require_once("../includes/config.php");
	if(!isset($_SESSION['user']))
    	header("Location: ".URLWEB."sso/logout.php");

	$mysql = new Mysql;
	if($_SESSION['user']->connect($mysql,addslashes(trim($_POST['user'])),addslashes(trim($_POST['password'])))){
		if($_SESSION['user']->getUser()->dnactivo==0){
			//$msg = "Error de Inicio de Session:<br />Su usuario se escuentra Desactivado, favor de <br /> enviar correo a a@a.com para mas informacion.".
			//		"<br /> Gracias!.";
			//$_SESSION['msg']=$msg;
			$url="Location: ".URLWEB."index.php";
		}elseif($_SESSION['user']->getUser()->dnactivo==1)
			$_SESSION['active']=1;
			$url="Location: ".URLWEB."start.php";
	}
	else{
		$msg = "Error de Inicio de Session <br />Su usuario y password son incorrectos, favor de verificarlos";
		$_SESSION['msg']=$msg;
		$url="Location: ".URLWEB."index.php";
	}
	//echo $url;
	header($url);
?>
