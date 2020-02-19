<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 12/03/2019
 * Time: 12:34 PM
 */


require_once("../../includes/config.php");
session_start();

if(!$_SESSION['active'] || trim($_SESSION['user']->getUser()->idtcuser) == ""){
    header("Location: ../../index.php");
}

$mysql = new Mysql;


$pricelist = Item::getAllPriceList($mysql, $_REQUEST['LP']);
$listitem = Item::getAllItemByList($mysql, $_REQUEST['LP']);

$filename = "listadeprecios_".$pricelist[0]->idlist.".xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");

?>

<table>
    <tr><td align="left"><img src="<? echo URLWEB;?>/img/logo_fondo_claro.png" width="90" height="45"/></td>
        <td align="center">&nbsp;</td>
        <td></td>
    </tr>
    <td align="center" colspan="3"><h3>Lista de precios</h3></td>
    </tr>
    <tr>
        <td align="center" colspan="3">
            <table class="table table-striped">
    <thead>
    <tr>
        <th>#</th>
        <th>Cod. Cliente</th>
        <th>Nom. Cliente</th>
        <th>Art. ID</th>
        <th>Art. Code</th>
        <th>Art. Nombre </th>
        <th>Precio Base</th>
        <th>Precio Lista</th>
    </tr>
    </thead>
    <tbody id="myTable">
    <? for($i=0; $i<count($listitem); $i++){?>
            <tr>
                <td><? echo ($i+1);?></td>
                <td><? echo $pricelist[0]->codesn;?></td>
                <td><? echo $pricelist[0]->namesn;?></td>
                <td><? echo $listitem[$i]->pnid;?></td>
                <td><? echo $listitem[$i]->dscode;?></td>
                <td><? echo $listitem[$i]->dsname;?></td>
                <td><? echo number_format($listitem[$i]->precioBase,2,'.',','); ?></td>
                <td><? echo number_format($listitem[$i]->precioPL,2,'.',','); ?></td>
            </tr>
    <? }//for?>
    </tbody>
</table>
        </td>
    </tr>
</table>
