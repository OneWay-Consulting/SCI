<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 26/02/2019
 * Time: 10:14 AM
 */

require_once("../header.php");
_includes();
?>
<script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
_header();

$mysql = new Mysql;
if(isset($_GET))
    $_GET = String::sanitize($_GET,true);
if(isset($_GET['_param'])){
        $list = Item::getCostByItem($mysql,$_GET['_param']);
}else{
    $list = Item::getCostByItem($mysql);
}
?>
<h2><span class="label label-primary">Consulta Costos</span></h2>
<br />
<form id="_inventoryfilter" name="_inventoryfilter" data-toggle="validator" class="form-horizontal" action="<? echo $_SERVER['PHP_SELF'];?>" method="post">
    <div class="container">
        <div class='col-sm-4'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker8'>
                    <input type="text" class="form-control" id="_param" name="_param" placeholder="Texto para buscar DB" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-sunglasses"></span>
                </span>
                </div>
            </div>
        </div>
        <div class='col-sm-2'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker9'>
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                    <input class="form-control" id="myInput" type="text" placeholder="Escriba texto para filtrar de datos:..">
            </div>
        </div>
    </div>
</form>
<br />
<div id="_divfilter" style="width: 100%; align-content: center">
    <div id="alertBoxes"></div>
    <table id="c_purchase" class="table-sm" width="80%" align="center">
        <thead>
        <tr>
            <th>ID Art.</th>
            <th>SKU</th>
            <th style="font-size: small" >Nom. Art</th>
            <th>Fecha</th>
            <th>Doc. Orig.</th>
            <th>Cantidad</th>
            <? if($_SESSION['user']->getUser()->fnidrole == "1"){?>
            <th>Costo</th>
            <? }?>
            <th>Opciones</th>
        </tr>
        </thead>
        <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
        <tr id="<? echo $list[$i]->pnid;?>">
            <td><? echo $list[$i]->pnid;?></td>
            <td><? echo $list[$i]->dscode;?></td>
            <td style="font-size: small" ><? echo $list[$i]->dsname;?></td>
            <td style="font-size: small" ><? echo $list[$i]->dddate;?></td>
            <td><? echo $list[$i]->fniddocori;?></td>
            <td><? echo $list[$i]->ddqtyinv;?></td>
            <? if($_SESSION['user']->getUser()->fnidrole == "1"){?>
            <td style="align-content: right">$<? echo number_format($list[$i]->ddcostinv,2,'.',',');?></td>
            <? }?>
            <td><img src="../images/buttons/serie.png" title="detalle series" style="cursor: pointer" /></td>
        </tr>
        <? }?>
        </tbody>
    </table>
</div>
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

    /*function sendFilter(){
        var parametros = {
            "client": $("#client").val()
        };
        $.ajax({
            data:  parametros, //datos que se envian a traves de ajax
            url:   'pendingfilter.php', //archivo que recibe la peticion
            type:  'post', //método de envio
            beforeSend: function () {
                $("#_divfilter").html("Procesando, espere por favor...");
            },
            success:  function (response) { //una vez que el archivo recibe el request lo procesa y lo devuelve
                $("#_divfilter").html(response);
            }
        });
    }*/

</script>
