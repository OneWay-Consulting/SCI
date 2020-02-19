<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 15/04/2019
 * Time: 01:13 PM
 */

require_once("../../includes/config.php");
session_start();

if(!$_SESSION['active'] || trim($_SESSION['user']->getUser()->idtcuser) == ""){
    header("Location: ../../index.php");
}

$mysql = new Mysql;

$list = Sales::getPartnerCredit($mysql);

//print_r($list);
$filename = "Reporte_cobranza.xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
?>

<table id="c_purchase" width="70%" align="center">
    <thead>
    <tr>
        <th align="center">Cod. Socio</th>
        <th align="center">Nom. Socio</th>
        <th align="center">Total</th>
        <th align="center">Cubierto</th>
        <th align="center">Adeudo</th>
    </tr>
    </thead>
    <tbody id="myTable">
    <? for($i=0; $i<count($list); $i++){?>
        <tr id="<? echo $list[$i]->pnid;?>">
            <td align="center"><? echo $list[$i]->dscode;?></td>
            <td align="center"><? echo $list[$i]->dsname;?></td>
            <td align="center">$<? echo number_format($list[$i]->facturado,2,'.',',');?></td>
            <td align="center">$<? echo number_format($list[$i]->debit,2,'.',',');?></td>
            <td align="center">$<? echo number_format(($list[$i]->facturado - $list[$i]->debit),2,'.',',');?></td>
        </tr>
    <? }?>
    </tbody>
</table>
