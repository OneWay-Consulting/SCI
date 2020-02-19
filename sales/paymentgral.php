<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 04/06/2019
 * Time: 10:01 AM
 */

require_once("../header.php");
_includes();
?>
<!--script src='< ? echo URLWEB;?>js/funciones.js' type="text/javascript"></script-->
<script src="../js/bpop.js"></script>

<style>
    #element_to_pop_up {
        display: none;
    }

</style>
<?
_header();

$mysql = new Mysql;
$list = Sales::getPartnerCreditCreditY($mysql);

$sum = 0;
for($i=0; $i<count($list);$i++){
    $sum += ($list[$i]->facturado - $list[$i]->debit);
    //$sum = ($list[$i]->facturado - ($list[$i]->facturadoo * ($list[$i]->dddiscountperc / 100))) - $list[$i]->debit;
}
?>
<h2><span class="label label-primary">Estado de cuenta | SALDO $<? echo number_format($sum,2,'.',',');?> </span></h2>
<br />
<div class="panel panel-primary" style="width: 90%; margin:0 auto;">
    <div class="panel-body">
        <div class="row">
            <input class="form-control" id="myInput" type="text" placeholder="Escriba texto para filtrado de datos:.."/>
            <table class="table table-responsive table-hover" id="myTable">
                <thead>
                <tr><th width="5%">Detalle</th>
                    <th colspan="6">Nombre</th>
                    <th>Facturado</th><th>Abono</th>
                    <th>Saldo</th><th width="4%">Opci&oacute;n</th></tr>
                </thead>
                <? $montfacturado = 0;
                $montdebit = 0;
                $montrest = 0;
                for($i=0; $i<count($list); $i++){
                  $delivery = Sales::getPartnerCreditCreditY($mysql,$list[$i]->pnid);

                  $saldot = 0;
                  $factt = 0;
                  $pagot  =0;
                  for($j=0; $j<count($delivery); $j++){

                    $factt += ($delivery[$j]->facturado - ($delivery[$j]->facturado * ($delivery[$j]->dddiscountperc / 100)));
                    $pagot += ($delivery[$j]->debit);
                    $saldot += ($delivery[$j]->facturado - ($delivery[$j]->facturado * ($delivery[$j]->dddiscountperc / 100))); //- $delivery[$j]->TOTALCOM;

                  }


                  ?>
                    <tbody>
                    <tr style="background-color: #babaff; cursor: pointer " class="clickable" data-toggle="collapse" data-target="#group-of-rows-<? echo $i;?>" aria-expanded="false" aria-controls="group-of-rows-1">
                        <td align="center"><i class="fa fa-plus" aria-hidden="true">
                                <img style="height:20px; width:20px; cursor:pointer; align-content: center" src="../images/buttons/downlist.png"/>
                            </i></td>
                        <td colspan="6"><? echo utf8_encode($list[$i]->dsname);?></td>
                        <td style="text-align: right">$<? echo number_format($factt,2,'.',','); //echo number_format(($list[$i]->facturado),2,'.',',');?></td>
                        <td style="text-align: right">$<? echo number_format($pagot,2,'.',',');//echo number_format(($list[$i]->debit),2,'.',',');?></td>
                        <td style="text-align: right">$<? echo number_format($saldot - $pagot ,2,'.',',');//echo number_format(($list[$i]->facturado - $list[$i]->debit),2,'.',',');?></td>
                        <?
                        $montfacturado += $list[$i]->facturado;
                        $montdebit += $list[$i]->debit;
                        $montrest += ($list[$i]->facturado - $list[$i]->debit);
                        ?>
                        <td></td>
                    </tr>
                    </tbody>
                    <tbody id="group-of-rows-<? echo $i;?>" class="collapse">
                    <?
                    $factsub = 0;
                    $debitsub = 0;
                    $restsub = 0;
                    $saldo = 0;
                    //if($list[$i]->pnid == 65){
                    //    print_r($delivery);
                    //}
                    for($j=0; $j<count($delivery); $j++){

                        //$facttemp =
                        if($delivery[$j]->debit == "")
                          $saldo += ($delivery[$j]->facturado - ($delivery[$j]->facturado * ($delivery[$j]->dddiscountperc / 100))); //- $delivery[$j]->TOTALCOM;
                        else
                          $saldo +=  ($delivery[$j]->facturado - ($delivery[$j]->facturado * ($delivery[$j]->dddiscountperc / 100))); //- $delivery[$j]->TOTALCOM;
                        ?>
                        <tr>
                            <td>- </td>
                            <td><? //echo $delivery[$j]->dsname; ?></td>
                            <td><? echo $delivery[$j]->idov; ?></td>
                            <td><?
                                  if($delivery[$j]->pnid!="PAGO"){
                                    if($delivery[$j]->deliverydate == ""){?>
                                      <img style="height:25px; width:25px; cursor:pointer; align-content: center" title="Sin dias" src="../images/buttons/debt.png" />
                                  <? }else{
                                      $date1 = new DateTime($delivery[$j]->deliverydate);
                                      //$date2 = new DateTime(explode(" ",$delivery[$j]->ddcreated)[0]);
                                        $date2 = new DateTime(date("Y-m-d"));
                                      $diff = $date1->diff($date2);
                                      //echo $diff->days . ' days ';
                                      if($diff->days > $delivery[$j]->dncreditday){?>
                                          <img style="height:25px; width:25px; cursor:pointer; align-content: center" title="<? echo ($diff->days - $delivery[$j]->dncreditday);?> días" src="../images/buttons/debt.png" />
                                      <?}else{?>
                                          <img style="height:25px; width:25px; cursor:pointer; align-content: center" title="<? echo $diff->days;?> días" src="../images/buttons/tiempo.png" />
                                      <?}?>
                                  <? }
                              }else//IF != PAGO
                              { $saldo = $saldo - $delivery[$j]->debit;
                              }

                              //echo "| facturado: ".$delivery[$j]->facturado;
                              /*
                              $facttemp = $delivery[$j]->facturado;
                              echo "| discperc: ".$delivery[$j]->dddiscountperc;
                              echo "| comisión: ".($delivery[$j]->facturado * ($delivery[$j]->dddiscountperc / 100));
                              echo "| comisión2: ".((($delivery[$j]->facturado * $delivery[$j]->dddiscountperc) / 100));
                              echo "| sin comision: ".($delivery[$j]->facturado * (($delivery[$j]->dddiscountperc/100)));
                              echo "| comisión3: ".($delivery[$j]->facturado / (1+($delivery[$j]->dddiscountperc/100)));
                              $concomisiontemp = ($delivery[$j]->facturado / (1+($delivery[$j]->dddiscountperc/100)));
                              echo "|TOTAL:".($facttemp - $concomisiontemp);
                              echo "|TOTALTEMP:".$delivery[$j]->SINCOMTEMP;
                              */

                                ?>
                            </td>
                            <td><?
                            if($delivery[$j]->pnid!="PAGO"){
                                if($delivery[$j]->deliverydate == ""){?>
                                   Sin d&iacute;as
                                <? }else{
                                    $date1 = new DateTime($delivery[$j]->deliverydate);
                                    //$date2 = new DateTime(explode(" ",$delivery[$j]->ddcreated)[0]);
                                    $date2 = new DateTime(date("Y-m-d"));
                                    $diff = $date1->diff($date2);
                                    //echo $diff->days . ' days ';
                                    if($diff->days > $delivery[$j]->dncreditday){
                                        echo ($diff->days - $delivery[$j]->dncreditday)." d&iacute;as";
                                    }else{
                                        echo 0;
                                    }?>
                                <? }
                              }else{ echo "PAGO";}//if pnid == PAGO
                                ?>
                            </td>
                            <td><? echo explode(" ",$delivery[$j]->ddcreated)[0]; ?></td>
                            <td><? //echo explode(" ",$delivery[$j]->dsreference)[0]; ?></td>
                            <td style="text-align: right">$ <? echo number_format(($delivery[$j]->facturado - (($delivery[$j]->facturado * ($delivery[$j]->dddiscountperc / 100)))),2,'.',',');?></td>
                            <td style="text-align: right">$<? echo number_format(($delivery[$j]->debit),2,'.',',');?></td>
                            <td style="text-align: right">$<? echo number_format($saldo,2,'.',','); //number_format(($delivery[$j]->facturado - $delivery[$j]->debit),2,'.',','); ?></td>
                            <td><img style="height:25px; width:25px; cursor:pointer; align-content: center" title="Visualizar" src="../images/buttons/view2.png" onclick="openPopUp(<? echo $delivery[$j]->idov;?>);"  />
                            </td>
                        </tr>
                        <?
                        $factsub += (($delivery[$j]->facturado - ($delivery[$j]->facturado * ($delivery[$j]->dddiscountperc / 100))));//$delivery[$j]->facturado;
                        $debitsub += $delivery[$j]->debit;
                        $restsub +=  (($delivery[$j]->facturado - ($delivery[$j]->facturado * ($delivery[$j]->dddiscountperc / 100)))); //$delivery[$j]->facturado - $delivery[$j]->debit;
                    }?>
                    <tr>
                        <td>- </td>
                        <td style="font-weight: bolder;"><? echo utf8_encode($list[$i]->dsname);?></td>
                        <td colspan="5" style="text-align: right; font-weight: bolder">TOTAL </td>
                        <td style="text-align: right;">$ <? echo number_format($factsub,2,'.',',');?></td>
                        <td style="text-align: right;">$<? echo number_format($debitsub,2,'.',',');?></td>
                        <td style="text-align: right; font-weight: bolder">$<? echo number_format($factsub - $debitsub,2,'.',',');?></td>
                        <td></td>
                    </tr>
                    </tbody>
                <? }?>
                <tfoot>
                <tr>
                    <td colspan="7" style="text-align: right; font-weight: bolder;"> TOTAL</td>
                    <td style="text-align: right; font-weight: bolder;">$<? echo number_format($montfacturado,2,'.',','); ?></td>
                    <td style="text-align: right; font-weight: bolder;">$<? echo number_format($montdebit,2,'.',','); ?></td>
                    <td style="text-align: right; font-weight: bolder;">$<? echo number_format($montrest,2,'.',','); ?></td>
                    <td>&nbsp;</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!--button id="my-button">POP IT UP</button-->
<!-- Element to pop up -->
<div id="element_to_pop_up">
    <div class="panel panel-info">
        <div class="panel-heading"><h7><strong>Detalle Orden de Venta </strong></h7></div>
        <div class="panel-body">
            <div class="input-group input-group-sm mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="inputGroup-sizing-sm">C&oacute;digo OV</span>
                </div>
                <input type="text" class="form-control form-control-sm" aria-label="Small" id="_codeovb" name="_codeovb" value="" width="20%" aria-describedby="inputGroup-sizing-sm" readonly>
                <input type="text" class="form-control form-control-sm" aria-label="Small" id="_partnernameb" name="_partnernameb" value="" width="20%" aria-describedby="inputGroup-sizing-sm" readonly>
            </div>
            <table class="table" id="myTable2">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>SKU</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total Linea</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>John</td>
                    <td>John</td>
                    <td>John</td>
                    <td>John</td>
                    <td>total</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function openPopUp(idov){
        $('#_codeovb').val(idov);
        getAllLinesByOV(idov);
        $('#element_to_pop_up').bPopup();
    }



</script>
