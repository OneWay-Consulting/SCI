<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 30/01/2019
 * Time: 07:05 PM
 */
?>
<?php
require_once("../header.php");
_includes();

$mysql = new Mysql();

$partner = Partner::getRow($mysql, $_POST['id']);
$delivery = Collection::getPartnerCreditPaid($mysql,$partner[0]->pnid);

//print_r($delivery);
//echo "<br />****$delivery";
//print_r($_POST);
?>

<div class="panel panel-primary" style="width: 90%; margin:0 auto;">
    <div class="panel-heading"><h7><strong>Detalle cobranza</strong></h7></div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group row">
                    <label for="_code" class="col-sm-2 col-form-label">C&oacute;digo:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="_code" name="_code" value="<? echo $partner[0]->dscode;?>"
                               readonly />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="_name" class="col-sm-2 col-form-label">Raz&oacute;n Social:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="_name" name="_name" value="<? echo $partner[0]->dsname;?>" readonly />
                    </div>
                </div>

<!--div id="_divfilter" style="width:70%;-->
    <div id="alertBoxes"></div>
<div class="panel panel-default"  style="width: 95%">
    <div class="panel-body">
        <table align="center">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th># Orden</th>
                    <th>Fecha</th>
                    <th>Referencia</th>
                    <th>Monto Total</th>
                    <th>Monto Pendiente</th>
                    <th>Estatus Credito</th>
                    <th>Opciones</th>
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
                            <td><input type="text" class="form-control" size="5"  id="idov[]" name="idov[]" value="<? echo $delivery[$i]->idov; ?>" readonly></td>
                            <td><input type="text" class="form-control" size="20"  value="<? echo $delivery[$i]->ddcreated; ?>" readonly></td>
                            <td><input type="text" class="form-control"  value="<? echo $delivery[$i]->dsreference; ?>" readonly></td>
                            <td><input type="text" class="form-control"  style="text-align: right" value="$<? echo number_format($delivery[$i]->facturado,2,'.',',');?>" readonly></td>
                            <td><input type="text" class="form-control"  style="text-align: right" value="$<? echo number_format(($delivery[$i]->facturado - $amountpay[0]->amount),2,'.',',');?>" readonly></td>
                            <td width="10%"><? if($delivery[$i]->dsstatuscredit == 1){echo "PENDIENTE";}
                                   elseif($delivery[$i]->dsstatuscredit == 2){ echo "PARCIAL";}
                                   elseif($delivery[$i]->dsstatuscredit == 3){ echo "PAGADO";}
                                ?>
                            </td>
                            <td><img src="../images/buttons/dinero.png" width="20" height="20" alt="Pago" title="Pago" style="cursor:pointer" onclick="sendAction('paymentPaidForm.php','dispatch=paymentdetailpaid&id=<? echo $delivery[$i]->idov;?>');" /> </td>
                        </tr>
                        </label>
                <? }//for?>
            </table>
    </div>
</div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="location.href='paymentpaid.php'">Regresar</button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-success btn-sm" onclick="window.open('export/paymentdetail.php?id=<? echo $partner[0]->pnid;?>','Reporte Cobranza');">Rep. Cobranza Excel</button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
<? if($_SESSION['msg']){?>
    showAlertBox('<? echo $_SESSION['msg'];?>');
    <?
    unset($_SESSION['msg']);
} ?>
</script>
