<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 30/08/2019
 * Time: 09:19 AM
 */

require_once("../header.php");
_includes();
_header();

$mysql = new Mysql();

if(isset($_POST['SKU']) && isset($_POST['_date'])){
    $listp = Inventory::getPurchaseOperationBySKUAndDate($mysql, $_POST['SKU'], $_POST['_date'], $_POST['_dateto']);
    $lists = Inventory::getSalesOperationBySKUAndDate($mysql, $_POST['SKU'], $_POST['_date'], $_POST['_dateto']);
    $listd = Inventory::getReturnOperationBySKUAndDate($mysql, $_POST['SKU'], $_POST['_date'], $_POST['_dateto']);
    $listt = Inventory::getTransferOperationBySKUAndDate($mysql, $_POST['SKU'], $_POST['_date'], $_POST['_dateto']);
}

?>
<br />
<h2><span class="label label-primary">Revisi&oacute;n de SKU <? echo $_POST['SKU'];?></span></h2>
<br />
<!-- filtros -->
<form id="_serialfilter" name="_serialfilter" data-toggle="validator" class="form-horizontal" action="<? echo $_SERVER['PHP_SELF'];?>" method="post">
    <div class="container">
        <div class='col-sm-3'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker9'>
                    <input class="form-control" id="_date" name="_date" placeholder="YYYY-MM-DD" type="text" value="<? if(isset($_POST['_date'])){echo $_POST['_date'];}?>"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>
        <div class='col-sm-3'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker9'>
                    <input class="form-control" id="_dateto" name="_dateto" placeholder="YYYY-MM-DD" type="text" value="<? if(isset($_POST['_dateto'])){echo $_POST['_dateto'];}?>"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>
        <div class='col-sm-3'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker9'>
                    <input class="form-control" id="SKU" name="SKU" placeholder="SKU ITEM" type="text" value="<? echo $_POST['SKU'];?>"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-sunglasses"></span>
                </span>
                </div>
            </div>
        </div>
        <div class='col-sm-3'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker9'>
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- filtros -->
<br />
<br />
<div class="toast">
    <div class="toast-header">
        Trazabilidad por Compra
    </div>
    <div class="toast-body">
        <table id="c_purchase">
            <thead>
            <tr>
                <th>#</th>
                <th>ID DOC</th>
                <th>ID Line</th>
                <th>Fecha</th>
                <th>Cod. Socio</th>
                <th>Nom. Socio</th>
                <th>ID ITEM</th>
                <th>SKU</th>
                <th>Descripci&oacute;n</th>
                <th>Cantidad</th>
                <th>Abierta</th>
                <th>Dif</th>
            </tr>
            </thead>
            <tbody id="myTable">
            <? $acum = 0;
                if(count($listp)>0){

                for($i = 0; $i < count($listp); $i++){?>
                    <tr>
                        <td><? echo ($i+1); ?></td>
                        <td><? echo $listp[$i]->pnid; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->idline; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->ddcreated; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->partnercode; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->partnername; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->iditem; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->itemcode; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->itemname; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->dnquantity; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->dnopenqty; ?></td>
                        <td style="font-size: small"><? echo $listp[$i]->dif; $acum += $listp[$i]->dif; ?></td>
                    </tr>
                <? }
            }else{?>
                <tr>
                    <td colspan="12">No hay movimientos</td>
                </tr>
            <? }?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="11" align="right">Total IMEI</th>
                    <th><? echo $acum;?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="toast">
    <div class="toast-header">
        Trazabilidad Ventas
    </div>
    <div class="toast-body">
        <table id="c_sales">
            <thead>
            <tr>
                <th>#</th>
                <th>ID DOC</th>
                <th>ID Line</th>
                <th>Fecha</th>
                <th>Cod. Socio</th>
                <th>Nom. Socio</th>
                <th>ID ITEM</th>
                <th>SKU</th>
                <th>Descripci&oacute;n</th>
                <th>Cantidad</th>
                <th>Abierta</th>
                <th>Dif</th>
            </tr>
            </thead>
            <tbody id="myTable">
            <? $acum = 0;
            if(count($lists)>0){

                for($i = 0; $i < count($lists); $i++){?>
                    <tr>
                        <td><? echo ($i+1); ?></td>
                        <td><? echo $list[$i]->pnid; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->idline; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->ddcreated; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->partnercode; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->partnername; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->iditem; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->itemcode; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->itemname; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->dnquantity; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->dnopenqty; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->dif; $acum += $lists[$i]->dif; ?></td>
                    </tr>
                <? }
            }else{?>
                <tr>
                    <td colspan="12">No hay movimientos</td>
                </tr>
            <? }?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="11" align="right">Total IMEI</th>
                <th><? echo $acum;?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="toast">
    <div class="toast-header">
        Trazabilidad Devoluciones
    </div>
    <div class="toast-body">
        <table id="c_dev">
            <thead>
            <tr>
                <th>#</th>
                <th>ID DOC</th>
                <th>ID Line</th>
                <th>Fecha</th>
                <th>Cod. Socio</th>
                <th>Nom. Socio</th>
                <th>ID ITEM</th>
                <th>SKU</th>
                <th>Descripci&oacute;n</th>
                <th>Cantidad</th>
                <th>Abierta</th>
                <th>Dif</th>
            </tr>
            </thead>
            <tbody id="myTable">
            <? $acum = 0;
            if(count($listd)>0){

                for($i = 0; $i < count($listd); $i++){?>
                    <tr>
                        <td><? echo ($i+1); ?></td>
                        <td><? echo $listd[$i]->pnid; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->idline; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->ddcreated; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->partnercode; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->partnername; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->iditem; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->itemcode; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->itemname; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->dnquantity; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->dnopenqty; ?></td>
                        <td style="font-size: small"><? echo $listd[$i]->dif; $acum += $listd[$i]->dif; ?></td>
                    </tr>
                <? }
            }else{?>
                <tr>
                    <td colspan="12">No hay movimientos</td>
                </tr>
            <? }?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="11" align="right">Total IMEI</th>
                <th><? echo $acum;?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="toast">
    <div class="toast-header">
        Trazabilidad Transferencias
    </div>
    <div class="toast-body">
        <table id="c_trans">
            <thead>
            <tr>
                <th>#</th>
                <th>ID DOC</th>
                <th>ID Line</th>
                <th>Fecha</th>
                <th>Cod. Socio</th>
                <th>Nom. Socio</th>
                <th>ID ITEM</th>
                <th>SKU</th>
                <th>Descripci&oacute;n</th>
                <th>Cantidad</th>
                <th>Abierta</th>
                <th>Dif</th>
            </tr>
            </thead>
            <tbody id="myTable">
            <? $acum = 0;
            if(count($listt)>0){

                for($i = 0; $i < count($listt); $i++){?>
                    <tr>
                        <td><? echo ($i+1); ?></td>
                        <td><? echo $listt[$i]->pnid; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->idline; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->ddcreated; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->partnercode; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->partnername; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->iditem; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->itemcode; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->itemname; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->dnquantity; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->dnopenqty; ?></td>
                        <td style="font-size: small"><? echo $listt[$i]->dif; $acum += $list[$i]->dif; ?></td>
                    </tr>
                <? }
            }else{?>
                <tr>
                    <td colspan="12">No hay movimientos</td>
                </tr>
            <? }?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="11" align="right">Total IMEI</th>
                <th><? echo $acum;?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.toast').toast('show');
    });
</script>
<? _footer();?>
<script type="text/javascript">

    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        var date_input=$('input[name="_date"]'); //our date input has the name "date"
        var date_input2=$('input[name="_dateto"]'); //our date input has the name "date"
        var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
        var options={
            format: 'yyyy-mm-dd',
            container: container,
            todayHighlight: true,
            autoclose: true,
        };
        date_input.datepicker(options);
        date_input2.datepicker(options);
    });

</script>


