<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 12/03/2019
 * Time: 12:24 PM
 */

require_once("../../includes/config.php");
session_start();

if(!$_SESSION['active'] || trim($_SESSION['user']->getUser()->idtcuser) == ""){
    header("Location: ../../index.php");
}

$mysql = new Mysql;
$list = Item::getAllByType($mysql);
$filename = "articulos.xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
?>
<table>
    <tr><td align="left"><img src="<? echo URLWEB;?>/img/logo_fondo_claro.png" width="90" height="45"/></td>
        <td align="center">&nbsp;</td>
        <td></td>
    </tr>
    <td align="center" colspan="3"><h3>Reporte de Art&iacute;culos</h3></td>
    </tr>
    <tr>
        <td align="center" colspan="3">
            <table id="c_partners">
                <thead>
                <tr>
                    <th>ID Sistema</th>
                    <th>C&oacute;digo Art&iacute;culo</th>
                    <th>UPC</th>
                    <th>Nombre Art&iacute;culo</th>
                    <th>Activo</th>
                    <th>Maneja Serie</th>
                    <th>Estatus</th>
                </tr>
                </thead>
                <tbody id="myTable">
                <? for($i=0; $i<count($list); $i++){?>
                    <tr id="<? echo $list[$i]->pnid;?>">
                        <td><? echo $list[$i]->pnid;?></td>
                        <td><? echo $list[$i]->dscode;?></td>
                        <td><? echo $list[$i]->dsupc;?></td>
                        <td><? echo trim(($list[$i]->dsname));?></td>
                        <td><? if($list[$i]->dsactive){echo "Activo";}else{ echo "Inactivo";}?></td>
                        <td><? if($list[$i]->dsserial){echo "Con Series";}else{ echo "SIN series";}?></td>
                        <td><? echo $list[$i]->dsstatus;?></td>
                    </tr>
                <? }?>
                </tbody>
            </table>
        </td>
    </tr>
</table>
