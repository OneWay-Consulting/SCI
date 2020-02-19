<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 04/01/2019
 * Time: 01:39 PM*/


require_once("../header.php");
_includes();
?>
<script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
_header();

$mysql = new Mysql;
$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);


$list = Item::getAllPriceList($mysql);
?>
<br />
<br />
<h2><span class="label label-primary">Listas de precio</span></h2>
<br />
<br />
<br />
<input class="form-control" id="myInput" type="text" placeholder="Escriba texto para filtrado de datos:..">
<br />
<center>
    <? if($permission['pricelist.php']['create']){?>
    <button type="button" class="btn btn-danger btn-sm" onclick="paint('priceAddForm.php','dispatch=add');" >Capturar Lista</button>
    <button type="button" class="btn btn-info btn-sm" onclick="location.href='uploadPriceForm.php'" >Cargar Precios</button>
    <? }?>
</center>
<br />
<div id="_divfilter">
    <div id="alertBoxes"></div>
    <table id="c_purchase">
        <thead>
        <tr>
            <th>ID Lista</th>
            <th>Cod. Socio</th>
            <th>Nom. Socio</th>
            <th>Nombre Lista</th>
            <th>Base</th>
            <th>Estatus</th>
            <th>Opciones</th>
        </tr>
        </thead>
        <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
            <tr id="<? echo $list[$i]->idlist;?>">
                <td><? echo $list[$i]->idlist;?></td>
                <td><? echo $list[$i]->dscode;?></td>
                <td><? echo utf8_encode($list[$i]->dsname);?></td>
                <td><? echo $list[$i]->namelist;?></td>
                <td><? if($list[$i]->dbbase){echo "BASE";}else{echo "Cliente";};?></td>
                <td><? if($list[$i]->dnactive){echo "Activa";}else{echo "INACTIVA";};?></td>
                <td><img src="../images/buttons/editar.png" border="0" align="Modificar Registro" title="Modificar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('priceAddForm.php','dispatch=update&id=<? echo $list[$i]->idlist;?>');" />
                    <img src="../images/buttons/excel.jpg" border="0" align="Exportar Excel" title="Exportar Excel" style="cursor:pointer; height:20px; width:20px;" onclick="window.open('export/expLP.php?LP=<? echo $list[$i]->idlist;?>','Reporte Lista de Precios');" />
                </td>
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

    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

</script>
