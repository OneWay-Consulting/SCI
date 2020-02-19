<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 21/11/2018
 * Time: 08:04 PM
 */

?>

<?php
require_once("../header.php");
_includes();
?>
<script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
_header();

$mysql = new Mysql;
$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

if(isset($_POST))
    $_POST = String::sanitize($_POST,true);

if($_SESSION['user']->getUser()->fnidrole==7)
  $iduser = $_SESSION['user']->getUser()->idtcuser;
else
  $iduser = null;


if(isset($_POST['date']) && isset($_POST['date2']) || isset($_POST['_doctype']) || isset($_POST['client'])){
    if($_POST['_doctype'] == "S")
        $list = Purchase::getAllByType($mysql,$_POST['date'], $_POST['date2'],$_POST['client'],$iduser);
    elseif($_POST['_doctype'] == "C")
        $list = Sales::getAllByType($mysql,$_POST['date'], $_POST['date2'],$_POST['client'],$iduser);
}else{
    //$list = Purchase::getAllByType($mysql);
    if($iduser == null)
      $list = Sales::getAllByType($mysql);
    else
    $list = Sales::getAllByType($mysql,null,null,null,$iduser);
}

//print_r($list);
?>
<br />
<br />
<h2><span class="label label-primary">Bandeja de Inventario</span></h2>
<br />
<br />
<br />
<!-- filtros -->
<!-- filtros -->
<form id="_inventoryfilter" name="_inventoryfilter" data-toggle="validator" class="form-horizontal" action="<? echo $_SERVER['PHP_SELF'];?>" method="post">
<div class="container">
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker6'>
                <input class="form-control" id="date" name="date" placeholder="YYYY-MM-DD" type="text" value="<? if(isset($_POST['date'])){echo $_POST['date'];}?>"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker7'>
                <input class="form-control" id="date2" name="date2" placeholder="YYYY-MM-DD" type="text" value="<? if(isset($_POST['date2'])){echo $_POST['date2'];}?>"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker8'>
                <select  class="form-control" id="_doctype" name="_doctype">
                    <!--option value="">Seleccione</option-->
                    <option value="C" <? if($_POST['_doctype']=="C"){ echo "selected";}?>>VENTAS</option>
                    <option value="S" <? if($_POST['_doctype']=="S"){ echo "selected";}?>>COMPRAS</option>
                </select>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-sunglasses"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker8'>
                <input class="form-control" id="client" name="client" placeholder="C&oacute;digo o nombre de SN" type="text" value="<? if(isset($_POST['client'])){echo $_POST['client'];}?>"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-sunglasses"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker9'>
                <button type="submit" id="buscar" name="buscar" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </div>
</div>
</form>
<input class="form-control" id="myInput" type="text" placeholder="Escriba texto para filtrado de datos:..">
<br />
<?
//print_r($_POST);
$to = "?a=a";
if(isset($_POST['date'] ) AND isset($_POST['date2']))
    $to .= '&from='.$_POST['date']."&to=".$_POST['date2'];
if(isset($_POST['date']))
    $to .= '&client='.$_POST['client'];

if($_POST['_doctype']=="C" || !isset($_POST['_doctype'])){?>
<center>
    <? if($permission['inventorySalesForm.php']['create'] && ($_SESSION['user']->getUser()->fnidrole != 6 && $_SESSION['user']->getUser()->fnidrole != 7 )){?>
    <button type="button" class="btn btn-info btn-sm" onclick="location.href='uploadSalesSerialForm.php'" >Entrega masiva</button>
    <? }?>
    <? if($_SESSION['user']->getUser()->fnidrole != 7){?>
    <button type="button" class="btn btn-success btn-sm" onclick="window.open('export/expoper.php<? echo $to;?>','Reporte Operaciones');">Rep. Operaciones Excel</button>
        <button type="button" class="btn btn-warning btn-sm" onclick="window.open('export/expoper2.php<? echo $to;?>','Reporte Operaciones Series');">Rep. Operaciones Series Excel</button>
    <? }?>
    <!--button type="button" class="btn btn-success btn-sm" onclick="window.open('export/expoperser.php< ? echo $to;?>','Reporte Series');">Rep. Series Excel</button-->
    <? if($permission['inventorySalesForm.php']['create'] && $_SESSION['user']->getUser()->fnidrole != 6){?>
    <button type="button" class="btn btn-warning btn-sm" onclick="paint('returnForm.php','dispatch=addreturn');">Crear Devoluci&oacute;n</button>
   <? }?>
</center>
<? }elseif($_POST['_doctype']=="S"){?>
  <button type="button" class="btn btn-success btn-sm" onclick="window.open('export/expoperpurchase.php<? echo $to;?>','Reporte Operaciones Compras');">Rep. Operaciones Compras Excel</button>
    <button type="button" class="btn btn-info btn-sm" onclick="location.href='uploadPurchaseSerialForm.php'" >Recepci&oacute;n masiva</button>
<?}?>
<!-- fin filtros -->
<div id="_divfilter">
    <div id="alertBoxes"></div>
    <table id="c_purchase">
        <thead>
        <tr>
            <th>ID Compra</th>
            <th>Cod. Socio</th>
            <th>Nom. Socio</th>
            <th>Referencia</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th>Opciones</th>
        </tr>
        </thead>
        <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
            <tr id="<? echo $list[$i]->pnid;?>">
                <td><? echo $list[$i]->pnid;?></td>
                <td><? echo $list[$i]->dscode;?></td>
                <td style="font-size: small"><? echo $list[$i]->dsname;?></td>
                <td style="font-size: small"><? echo $list[$i]->dsreference;?></td>
                <td style="font-size: small"><? echo $list[$i]->ddcreated;?></td>
                <!--td>< ? echo $list[$i]->dstype;?></td-->
                <td><? echo $list[$i]->statusname;?></td>
                <td><? if($permission['inventory.php']['create'] || $permission['inventory.php']['query']){?>
                    <? if($list[$i]->dstype == "COMPRAS"){?>
                    <img src="../images/buttons/entrada.png" border="0" align="Modificar Registro" title="Generar Entrada" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('inventoryPurchaseForm.php','dispatch=entry&id=<? echo $list[$i]->pnid;?>');" />
                    <? }elseif($list[$i]->dstype == "VENTA"){?>
                        <img src="../images/buttons/salida.png" border="0" align="Modificar Registro" title="Generar Entrega" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('inventorySalesForm.php','dispatch=delivery&id=<? echo $list[$i]->pnid;?>');" />
                    <? }elseif($list[$i]->dstype == "ENTREGADO"){?>
                    <img src="../images/buttons/salida.png" border="0" align="Modificar Registro" title="Generar Entrega" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('inventorySalesForm.php','dispatch=delivery&id=<? echo $list[$i]->pnid;?>');" />
                    <? }?>
                    <? }?>
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

    function sendFilter(){
        var parametros = {
            "date1" : $("#date").val(),
            "date2" : $("#date2").val(),
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
    }

</script>
