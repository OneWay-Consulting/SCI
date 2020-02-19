<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 19/03/2019
 * Time: 09:49 PM
 */
require_once("../../includes/config.php");
session_start();

if(!$_SESSION['active'] || trim($_SESSION['user']->getUser()->idtcuser) == ""){
    header("Location: ../../index.php");
}

$mysql = new Mysql;
$list = Item::getStockGroupByItem($mysql);

if($_SESSION['user']->getUser()->fnidrole!=7)
    $listware = Item::getWareHouse($mysql);
else
    $listware = Item::getWareHouse($mysql,$_SESSION['user']->getUser()->fnidrole);


$filename = "Reporte_stock.xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
?>
<table>
    <tr><td align="left"><img src="<? echo URLWEB;?>/img/logo_fondo_claro.png" width="90" height="45"/></td>
        <td align="center">&nbsp;</td>
        <td></td>
    </tr>
    <td align="center" colspan="3"><h3>Reporte de operaciones</h3></td>
    </tr>
    <tr>
        <td align="center" colspan="3">
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
                </tr>
                </thead>
                <tbody id="myTable">
                <? for($i=0; $i<count($list); $i++){?>
                    <tr id="<? echo $list[$i]->pnid;?>">
                        <td><? echo $list[$i]->pnid;?></td>
                        <td><? echo $list[$i]->dscode;?></td>
                        <td><? echo $list[$i]->dsupc;?></td>
                        <td><? echo $list[$i]->dsname;?></td>
                        <? for($j=0; $j<count($listware); $j++){?>
                            <td><? $stockbyitem = Item::getStock($mysql,$list[$i]->pnid,$listware[$j]->pnid);
                                echo $stockbyitem[0]->ddquantity;?></td>
                        <? }?>
                    </tr>
                <? }?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
