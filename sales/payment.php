<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 27/11/2018
 * Time: 09:19 PM
 */
require_once("../header.php");
_includes();
?>
<script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
_header();

$mysql = new Mysql;
$list = Sales::getPartnerCredit($mysql);

?>
<h2><span class="label label-primary">Bandeja de cobranza</span></h2>
<br />
<input class="form-control" id="myInput" type="text" placeholder="Escriba texto para filtrado de datos:..">
<br />
<button type="button" class="btn btn-success btn-sm" onclick="window.open('export/paymentgral.php','Reporte Cobranza');">Rep. Cobranza Excel</button>
<center>
<div id="_divfilter" style="width:70%;>
    <div id="alertBoxes"></div>
    <table id="c_purchase" width="70%" align="center">
        <thead>
        <tr>
            <th>Cod. Socio</th>
            <th>Nom. Socio</th>
            <th>Total</th>
            <th>Cubierto</th>
            <th>Adeudo</th>
            <th>Opciones</th>
        </tr>
        </thead>
        <tbody id="myTable">
        <? $facturado = 0;
            $debit = 0;
            for($i=0; $i<count($list); $i++){
             //$debitrow = Sales::getDebit($mysql, $list[$i]->pnid);
                ?>
            <tr id="<? echo $list[$i]->pnid;?>">
                <!--td>< ? echo $list[$i]->pnid;?></td-->
                <td><? echo $list[$i]->dscode;?></td>
                <td><? echo $list[$i]->dsname;?></td>
                <!--td>< ? echo $list[$i]->dddocdate;?></td>
                <td>< ? echo $list[$i]->dsreference;?></td>
                <td>< ? if($list[$i]->dsstatus == 1){echo "CREADO";}elseif($list[$i]->dsstatus == 3){echo "ENTREGADO";}?></td>
                <td>< ? echo $list[$i]->statsalesstatus;?></td-->
                <td style="text-align: right">$<? echo number_format($list[$i]->facturado,2,'.',','); $facturado += $list[$i]->facturado;?></td>
                <td style="text-align: right">$<? echo number_format($list[$i]->debit,2,'.',','); $debit += $list[$i]->debit;?></td>
                <td style="text-align: right">$<? echo number_format(($list[$i]->facturado - $list[$i]->debit),2,'.',',');?></td>
                <!--td>< ? echo $list[$i]->refsalesstatus;?></td-->
                <td><img src="../images/buttons/asignar.gif" border="0" align="Modificar Registro" title="Modificar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('paymentdetail.php','dispatch=listcredit&id=<? echo $list[$i]->pnid;?>');" />
                </td>
            </tr>
        <? }?>
        <tr>
            <td style="text-align: right" colspan="2">TOTALES</td>
            <td style="text-align: right">$<? echo number_format($facturado,2,'.',',');?></td>
            <td style="text-align: right">$<? echo number_format($debit,2,'.',',');?></td>
            <td style="text-align: right">$<? echo number_format(($facturado-$debit),2,'.',',');?></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>
</center>
<?
_footer();
?>
<script type="text/javascript">

    <? if(explode("|",$_SESSION['msg'])[1]>0){
        $_SESSION['msg'] = $_SESSION['msg'];
        ?>
    sendAction('paymentdetail.php','dispatch=listcredit&id=<? echo explode("|",$_SESSION['msg'])[1];?>');
    <? }?>

    <?// if($_SESSION['msg']){?>
    /*showAlertBox(' < ? echo $_SESSION['msg'];?>');*/
    <?
    //unset($_SESSION['msg']);
    //} ?>

    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
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
