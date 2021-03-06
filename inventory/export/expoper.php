<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 14/03/2019
 * Time: 01:39 PM
 */
require_once("../../includes/config.php");
session_start();

if(!$_SESSION['active'] || trim($_SESSION['user']->getUser()->idtcuser) == ""){
    header("Location: ../../index.php");
}

$mysql = new Mysql;

if((isset($_GET['from']) && isset($_GET['to'])) || $_GET['client']!="" )
    $list = Inventory::getOperationSales($mysql,$_GET['from'],$_GET['to'],$_GET['client']);
else
    $list = Inventory::getOperationSales($mysql);

//print_r($list);
$filename = "Reporte_operaciones.xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
?>

<table>
    <!--tr><td align="left"><img src="< ? echo URLWEB;?>/img/logo_fondo_claro.png" width="90" height="45"/></td>
        <td align="center">&nbsp;</td>
        <td></td>
    </tr-->
    <td align="center" colspan="3"><h3>Reporte de operaciones</h3></td>
    </tr>
    <tr>
        <td align="center" colspan="3">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>OV</th>
                    <th>Pedido</th>
                    <th>Estatus Linea</th>
                    <th>Cod. Cliente</th>
                    <th>Nom. Cliente</th>
                    <th>Fecha creaci&oacute;n</th>
                    <th>Comentario Gral</th>
                    <th>Estatus</th>
                    <th>SKU</th>
                    <th>Nom. Art&iacute;culo</th>
                    <th>Cantidad</th>
                    <th>Prec. Unit.</th>
                    <th>Total</th>
                    <!--th>Comentarios</th-->
                    <th>Gu&iacute;a</th>
                    <th>Gu&iacute;a MC</th>
                    <th>Canal</th>
                    <th>Paqueteria</th>
                    <th>Comentario L.</th>
                    <th>IMEI</th>
                    <th>Salida</th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody id="myTable">
                <? for($i=0; $i<count($list); $i++){?>
                    <tr>
                        <td><? echo ($i+1);?></td>
                        <td><? echo $list[$i]->pnid;;?></td>
                        <td><? echo $list[$i]->dsrefline;?></td>
                        <td><? echo $list[$i]->statusline;?></td>
                        <td><? echo $list[$i]->dscode;?></td>
                        <td><? echo $list[$i]->dsname;?></td>
                        <td><? echo $list[$i]->ddcreated;?></td>
                        <td><? echo $list[$i]->commenth;?></td>
                        <td><? echo $list[$i]->statusname;?></td>
                        <td><? echo $list[$i]->itemcode;?></td>
                        <td><? echo $list[$i]->itemname;?></td>
                        <td><? echo $list[$i]->dnquantity;?></td>
                        <td><? echo number_format($list[$i]->dnprice,2,'.',','); ?></td>
                        <td><? echo number_format($list[$i]->total,2,'.',','); ?></td>
                        <!--td>< ? echo $list[$i]->commenth;?></td-->
                        <td><? echo $list[$i]->dsguia;?></td>
                        <td><? echo $list[$i]->dsguiac;?></td>
                        <td><? echo $list[$i]->dscanal;?></td>
                        <td><? echo $list[$i]->dspaqueteria;?></td>
                        <td><? echo $list[$i]->dscomentariol;?></td>
                        <td><? echo $list[$i]->fnidserial;?></td>
                        <td><? echo $list[$i]->iddelivery;?></td>
                        <td><? echo $list[$i]->fecdelivery;?></td>
                    </tr>
                <? }//for?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
