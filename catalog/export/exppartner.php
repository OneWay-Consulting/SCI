<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 12/03/2019
 * Time: 11:22 AM
 */

require_once("../../includes/config.php");
session_start();

if(!$_SESSION['active'] || trim($_SESSION['user']->getUser()->idtcuser) == ""){
    header("Location: ../../index.php");
}

$mysql = new Mysql;
$list = Partner::getAllByType($mysql,"C");
$filename = "socios.xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
?>

<table>
    <tr><td align="left"><img src="<? echo URLWEB;?>/img/logo_fondo_claro.png" width="90" height="45"/></td>
        <td align="center">&nbsp;</td>
        <td></td>
    </tr>
        <td align="center" colspan="3"><h3>Reporte de Socios de Negocios</h3></td>
    </tr>
    <tr>
        <td align="center" colspan="3">
            <table id="c_partners">
                <thead>
                <tr>
                    <th>C&oacute;digo Partner</th>
                    <th>Raz&oacute;n Social</th>
                    <th>RFC</th>
                    <th>Email</th>
                    <th>Tipo</th>
                </tr>
                </thead>
                <tbody id="myTable">
                <? for($i=0; $i<count($list); $i++){?>
                    <tr id="<? echo $list[$i]->pnid;?>">
                        <td><? echo $list[$i]->dscode;?></td>
                        <td><? echo trim($list[$i]->dsname);?></td>
                        <td><? echo $list[$i]->dsrfc;?></td>
                        <td><? echo $list[$i]->dsemail;?></td>
                        <td><? if($list[$i]->dstype == "C"){echo "Cliente";}else{ echo "Proveedor";}?></td>
                    </tr>
                <? }?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
