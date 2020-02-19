<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 04/03/2019
 * Time: 09:37 AM
 */
require_once("../header.php");
_includes();
?>
<script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
_header();

$mysql = new Mysql;
$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

//$index = Item::getAll($mysql,$_GET['_page']);
if(isset($_GET['_filter']))
    $list = Role::getAll($mysql);
else
    $list = Role::getAll($mysql,$_GET['_filter']);
?>

<h2><span class="label label-primary">Cat&aacute;logo de Roles</span></h2>
<!-- filtros -->
<input class="form-control" id="myInput" type="text" placeholder="Escriba algun texto para el filtrado de datos...">
<br />
<!--center-->
    <? //if($permission['item.php']['create']){?>
        <!-- button type="button" class="btn btn-danger btn-sm" onclick="paint('roleAddForm.php','dispatch=add');" >Agregar Art&iacute;culos</button-- >
     <? //}?>
</center-->

<!-- fin filtros -->
<div id="_divfilter">
    <div id="alertBoxes"></div>
    <table id="c_items">
        <thead>
        <tr>
            <th>ID Sistema</th>
            <th>Nombre</th>
            <th>Descripci&oacute;n</th>
            <th>Opciones</th>
        </tr>
        </thead>
        <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
            <tr id="<? echo $list[$i]->idtcrole;?>">
                <td><? echo $list[$i]->idtcrole;?></td>
                <td><? echo $list[$i]->dsname;?></td>
                <td><? echo utf8_encode($list[$i]->dsdescription);?></td>
                <td><img src="../images/buttons/editar.png" border="0" align="Modificar Registro" title="Modificar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('roleAddForm.php','dispatch=update&id=<? echo $list[$i]->idtcrole;?>');" /></td>
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

