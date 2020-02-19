<?php
	require_once("../header.php");
	_includes();
?>
    <script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
	_header();

	    $mysql = new Mysql;
        $permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

        $index = Item::getAllPages($mysql,$_GET['_page']);
		if(isset($_GET['_filter']))
			$list = Item::getAllByType($mysql);
		else
			$list = Item::getAllByType($mysql,$_GET['_filter']);
      ?>

<h2><span class="label label-primary">Cat&aacute;logo de Art&iacute;culos</span></h2>
<!-- filtros -->
<p>Escriba algun texto para el filtrado de datos:</p>
<input class="form-control" id="myInput" type="text" placeholder="Search..">
<br />
<center>
    <? if($permission['item.php']['create']){?>
    <button type="button" class="btn btn-danger btn-sm" onclick="paint('itemAddForm.php','dispatch=add');" >Agregar Art&iacute;culos</button>
    <? }?>
    <button type="button" class="btn btn-success btn-sm" onclick="window.open('export/expitem.php','Reporte Articulos');">Exportar a Excel</button>
</center>

<!-- fin filtros -->
<div id="_divfilter">
    <div id="alertBoxes"></div>
    <table id="c_items">
        <thead>
        <tr>
            <th>ID Sistema</th>
            <th>C&oacute;digo Art&iacute;culo</th>
            <th>Nombre Art&iacute;culo</th>
            <th>UPC</th>
						<th>UPC2</th>
            <th>Activo</th>
            <th>Maneja Serie</th>
            <th>Estatus</th>
            <th>Opciones</th>
        </tr>
        </thead>
        <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
            <tr id="<? echo $list[$i]->pnid;?>">
                <td><? echo $list[$i]->pnid;?></td>
                <td><? echo $list[$i]->dscode;?></td>
                <td><? echo utf8_encode($list[$i]->dsname);?></td>
                <td><? echo $list[$i]->dsupc;?></td>
								<td><? echo $list[$i]->dsupc2;?></td>
                <td><? if($list[$i]->dsactive){echo "Activo";}else{ echo "Inactivo";}?></td>
                <td><? if($list[$i]->dsserial){echo "Con Series";}else{ echo "SIN series";}?></td>
                <td><? echo $list[$i]->dsstatus;?></td>
                <td><img src="../images/buttons/editar.png" border="0" align="Modificar Registro" title="Modificar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('itemAddForm.php','dispatch=update&id=<? echo $list[$i]->pnid;?>');" /></td>
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
