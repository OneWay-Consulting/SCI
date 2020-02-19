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
$list = Purchase::getHistoryByIMEI($mysql, $_POST['serial']);

$lists = Sales::getHistoryByIMEI($mysql, $_POST['serial']);

$listr = Returns::getHistoryByIMEI($mysql, $_POST['serial']);

?>
<br />
<h2><span class="label label-primary">Revisi&oacute;n de Serie <? echo $_POST['serial'];?></span></h2>
<br />
<br />
<br />
<!-- filtros -->
<!-- filtros -->
<form id="_serialfilter" name="_serialfilter" data-toggle="validator" class="form-horizontal" action="<? echo $_SERVER['PHP_SELF'];?>" method="post">
    <div class="container">
        <div class='col-sm-5'>
            <div class="form-group">
                <div class='input-group date' id='serial'>
                    <input class="form-control" id="serial" name="serial" placeholder="Escriba número de serie" type="text" />
                </div>
            </div>
        </div
</form>
<br />
<br />
<div class="toast">
    <div class="toast-header">
        Trazabilidad Compras
    </div>
    <div class="toast-body">
        <table id="c_purchase">
            <thead>
            <tr>
                <th>ID Compra</th>
                <th>ID Line</th>
                <th>Fecha</th>
                <th>Cod. Socio</th>
                <th>Nom. Socio</th>
                <th>SKU</th>
                <th>Descripci&oacute;n</th>
                <th>IMEI</th>
                <th>Folio entrada</th>
                <th>Fecha entrada</th>
            </tr>
            </thead>
            <tbody id="myTable">
            <? if(count($list)>0){
                for($i = 0; $i < count($list); $i++){?>
                    <tr>
                        <td><? echo $list[$i]->pnid; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->idline; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->ddcreated; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->dscode; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->dsname; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->itemcode; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->itemname; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->fnidserial; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->fnidheaderentry; ?></td>
                        <td style="font-size: small"><? echo $list[$i]->fecentry; ?></td>
                    </tr>
                <? }
            }else{?>
                <tr>
                    <td colspan="8">No hay movimientos</td>
                </tr>
            <? }?>
            </tbody>
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
                <th>ID Venta</th>
                <th>ID Line</th>
                <th>Fecha</th>
                <th>Cod. Socio</th>
                <th>Nom. Socio</th>
                <th>SKU</th>
                <th>Descripci&oacute;n</th>
                <th>IMEI</th>
                <th>Folio entrega</th>
                <th>Fecha entrega</th>
            </tr>
            </thead>
            <tbody id="myTableS">
            <? if(count($lists)>0){
                for($i = 0; $i < count($lists); $i++){?>
                    <tr>
                        <td style="font-size: small"><? echo $lists[$i]->pnid; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->idline; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->ddcreated; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->dscode; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->dsname; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->itemcode; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->itemname; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->fnidserial; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->fnidheaderdelivery; ?></td>
                        <td style="font-size: small"><? echo $lists[$i]->fecentry; ?></td>
                    </tr>
                <? }
            }else{?>
                <tr>
                    <td colspan="8">No hay movimientos</td>
                </tr>
            <? }?>
            </tbody>
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
                <th>ID Dev.</th>
                <th>ID Linea</th>
                <th>Fecha</th>
                <th>Cod. Socio</th>
                <th>Nom. Socio</th>
                <th>SKU</th>
                <th>Descripci&oacute;n</th>
                <th>IMEI</th>
                <th>Line Ori</th>
                <th>Folio Dev</th>
            </tr>
            </thead>
            <tbody id="myTableD">
            <? if(count($listr)>0){
                for($i = 0; $i < count($listr); $i++){?>
                    <tr>
                        <td style="font-size: small"><? echo $listr[$i]->pnid; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->idline; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->dddate; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->dscode; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->dsname; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->itemcode; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->itemname; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->fnidserial; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->fnidlineori; ?></td>
                        <td style="font-size: small"><? echo $listr[$i]->pnid; ?></td>
                    </tr>
                <? }
            }else{?>
                <tr>
                    <td colspan="8">No hay movimientos</td>
                </tr>
            <? }?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.toast').toast('show');
    });
</script>

