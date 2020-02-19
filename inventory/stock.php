<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 19/02/2019
 * Time: 11:08 PM
 */
require_once("../header.php");
_includes();
?>
<script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
_header();

$mysql = new Mysql;
$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

if($_SESSION['user']->getUser()->fnidrole!=7)
    $listware = Item::getWareHouse($mysql);
else
    $listware = Item::getWareHouse($mysql,$_SESSION['user']->getUser()->fnidrole);

if(isset($_POST))
    $_POST = String::sanitize($_POST,true);
if(isset($_POST['_param']) || (isset($_POST['date']) && isset($_POST['date2']))){
    if($_SESSION['user']->getUser()->fnidrole==7){
        $list = Item::getStockGroupByItem($mysql,$_POST['_param'],2);
    }else{
        $list = Item::getStockGroupByItem($mysql,$_POST['_param'],null);
    }
}else{
    if($_SESSION['user']->getUser()->fnidrole==7) {
        $list = Item::getStockGroupByItem($mysql,null,2);
    }else{
        $list = Item::getStockGroupByItem($mysql);
    }
}
?>
<h2><span class="label label-primary">Consulta Stock</span></h2>
<br />
<form id="_inventoryfilter" name="_inventoryfilter" data-toggle="validator" class="form-horizontal" action="<? echo $_SERVER['PHP_SELF'];?>" method="post">
    <div class="container">
        <div class="form-group row">
            <div class='col-sm-4'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker6'>
                        <input class="form-control" id="date" name="date" placeholder="YYYY-MM-DD" type="text" value="<? if(isset($_POST['date'])){echo $_POST['date'];}?>"/>
                        <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                </div>
            </div>
            <div class='col-sm-4'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker7'>
                        <input class="form-control" id="date2" name="date2" placeholder="YYYY-MM-DD" type="text" value="<? if(isset($_POST['date2'])){echo $_POST['date2'];}?>"/>
                        <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                </div>
            </div>
            <div class='col-sm-4'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker8'>
                        <input type="text" class="form-control" id="_param" name="_param" value="<? if(isset($_POST['_param'])){ echo $_POST['_param'];}?>" placeholder="Texto para buscar DB" />
                        <span class="input-group-addon">
                        <span class="glyphicon glyphicon-sunglasses"></span>
                    </span>
                    </div>
                </div>
            </div>
            <div class='col-sm-4'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker9'>
                        <button type="submit" id="buscar" name="buscar" class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <button type="button" class="btn btn-success btn-sm" onclick="window.open('export/expstock.php','Reporte Stock');">Rep. Stock Excel</button>
            </div>
            <?
            $to = "";
            if(isset($_POST['date'] ) AND isset($_POST['date2']))
                $to = 'from='.$_POST['date']."&to=".$_POST['date2'];
            if(isset($_POST['_param'])) {
                if (strlen($to) > 1)
                    $to .= '&filter=' . $_POST['_param'];
                else
                    $to .= 'filter=' . $_POST['_param'];
                }
            //if($permission['inventory.php']['create']){?>
            <? //}?>
        </div><!-- row-->
    </div>
</form>
<div class="col-sm-4">
    <button type="button" class="btn btn-success btn-sm" onclick="window.open('export/exptransfer.php?<? echo $to;?>','Reporte Stock Series');">Rep. Transferencias Excel</button>
</div>
<br />
<div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" id="myInput" type="text" placeholder="Escriba texto para filtrar de datos:..">
    </div>
</div>
<br />
<?
$to = "?a=a";
if(isset($_POST['date'] ) AND isset($_POST['date2']))
    $to .= '&from='.$_POST['date']."&to=".$_POST['date2'];
if(isset($_POST['date']))
    $to .= '&client='.$_POST['client'];
?>
<button type="button" class="btn btn-success btn-sm" onclick="window.open('export/expreturn.php<? echo $to;?>','Reporte Devoluciones');">Rep. Devoluciones Excel</button>
<br />
<div id="_divfilter" style="width: 100%; align-content: center">
    <div id="alertBoxes"></div>
    <table id="c_purchase" width="80%" align="center">
        <thead>
        <tr>
            <th>ID Art.</th>
            <th>SKU</th>
            <th>UPC</th>
            <th>Nom. Art</th>
            <? for($j=0; $j<count($listware); $j++){?>
                <th><? echo $listware[$j]->dsname;?></th>
            <? }?>
            <!--th>Stock</th-->
            <!--th>Opciones</th-->
        </tr>
        </thead>
        <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
        <tr id="<? echo $list[$i]->pnid;?>">
            <td style="font-size: 12px"><? echo $list[$i]->pnid;?></td>
            <td style="font-size: 12px"><? echo $list[$i]->dscode;?></td>
            <td style="font-size: 12px"><? echo $list[$i]->dsupc;?></td>
            <td style="font-size: 12px"><? echo $list[$i]->dsname;?></td>
            <? for($j=0; $j<count($listware); $j++){?>
                <td align="center" style="text-align: right">
                    <? if($_SESSION['user']->getUser()->fnidrole==1){?><a href="#" onclick="sendAction('stockForm.php','dispatch=detail&id=<? echo $list[$i]->dscode;?>&fnidware=<? echo $listware[$j]->pnid;?>');"><? }?>
                        <? $stockbyitem = Item::getStock($mysql,$list[$i]->pnid,$listware[$j]->pnid);
                    echo $stockbyitem[0]->ddquantity;?>
                        <? if($_SESSION['user']->getUser()->fnidrole==1){?></a><?}?>
                </td>
            <? }?>
            <!--td>< ? echo $list[$i]->warename;?></td-->
            <!--td>< ? echo $list[$i]->ddquantity;?></td-->
            <!--td>< ? if($_SESSION['user']->getUser()->fnidrole==1){?><img src="../images/buttons/serie.png" border="0" align="Detalle Series" title="Detalle Series" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('stockForm.php','dispatch=detail&id=< ? echo $list[$i]->pnid;?>&fnidware=1');" />< ? }?>
            </td-->
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

        var date_input=$('input[name="date"]'); //our date input has the name "date"
        var date2_input=$('input[name="date2"]'); //our date input has the name "date"
        var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
        var options={
            format: 'yyyy-mm-dd',
            container: container,
            todayHighlight: true,
            autoclose: true,
        };
        date_input.datepicker(options);
        date2_input.datepicker(options);
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
