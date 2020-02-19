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

$partner = Partner::getRow($mysql, $_GET['id']);
$delivery = Sales::getPartnerCredit($mysql,$partner[0]->pnid);

//print_r($list);
$filename = "Reporte_cobranza".$partner[0]->dscode.".xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");

?>
<table class="table table-striped">
    <thead>
    <tr>
        <th># Orden</th>
        <th>Fecha</th>
        <th>Referencia</th>
        <th>Monto Total</th>
        <th>Monto Pendiente</th>
        <th>Estatus Credito</th>
    </tr>
    </thead>
    <? for($i=0; $i<count($delivery); $i++){
        $amountpay = Sales::getAmountPaymentValid($mysql,$delivery[$i]->idov,1);
        //echo "<br />****";
        //print_r($amountpay);
        if($amountpay[0]->amount =="")
            $amountpay[0]->amount = 0.00;
        ?>
        <tr>
            <td><? echo $delivery[$i]->idov; ?></td>
            <td><? echo $delivery[$i]->ddcreated; ?></td>
            <td><? echo $delivery[$i]->dsreference; ?></td>
            <td>$<? echo number_format($delivery[$i]->facturado,2,'.',',');?></td>
            <td>$<? echo number_format(($delivery[$i]->facturado - $amountpay[0]->amount),2,'.',',');?></td>
            <td width="10%"><? if($delivery[$i]->dsstatuscredit == 1){echo "PENDIENTE";}
                elseif($delivery[$i]->dsstatuscredit == 2){ echo "PARCIAL";}
                elseif($delivery[$i]->dsstatuscredit == 3){ echo "PAGADO";}
                ?>
            </td>
        </tr>
        </label>
    <? }//for?>
</table>
