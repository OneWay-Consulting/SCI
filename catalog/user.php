<?php
	require_once("../header.php");
	_includes();
?>
    <script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
	_header();
	
	    $mysql = new Mysql;
$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);


$index = User::getAllPages($mysql,$_GET['_page']);
		if($_GET['_filter']!="1" && $_GET['_filter']!="2" && $_GET['_filter']!="3" && $_GET['_filter']!="4")
			$list = User::getAll($mysql,$index[2]);
		else		
			$list = User::getAllByType($mysql,$_GET['_filter']);
      ?>
<h2><span class="label label-primary">Cat&aacute;logo de usuarios</span></h2>
<!-- filtros -->
<input class="form-control" id="myInput" type="text" placeholder="Escriba algun texto para el filtrado de datos..">
<br />
<center>
    <? if($permission['pricelist.php']['create']){?>
    <button type="button" class="btn btn-danger btn-sm" onclick="paint('userAddForm.php','dispatch=add');" >Agregar Usuario</button>
    <? }?>
</center>
<!-- fin filtros -->
<div id="_divfilter">
    <div id="alertBoxes"></div>
    <table id="c_users">
      <thead>
      <tr>
        <th>ID User</th>
        <th>Nombre</th>
        <th>Rol</th>
        <th>Email</th>
        <th>Activo</th>
        <th>Opciones</th>
      </tr>
      </thead>
      <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
                <tr id="<? echo $list[$i]->idtcuser;?>">
                        <td><? echo $list[$i]->dsuser;?></td>
                        <td><? echo utf8_encode($list[$i]->dsnombrecom);?></td>
                        <td><? echo $list[$i]->role;?></td>
                        <td><? echo $list[$i]->dsemail;?></td>
                        <td><? if($list[$i]->dnactivo == 1){echo "ACTIVO";}else{ echo "INACTIVO";}?></td>
                        <td><img src="../images/buttons/editar.png" border="0" align="Modificar Registro" title="Modificar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('userAddForm.php','dispatch=update&id=<? echo $list[$i]->idtcuser;?>');" /></td>
                </tr>
                <? }?>
      </tbody>
    </table>
</div>
<?	
	_footer();
?>
<script type="text/javascript">
	<? if($_SESSION['msg']){?>
	showAlertBox('<? echo $_SESSION['msg'];?>');
	<? 
		unset($_SESSION['msg']);
	} ?>

    $(document).ready(function() {
        $("#myInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
