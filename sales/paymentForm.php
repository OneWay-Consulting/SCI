<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 21/12/2018
 * Time: 02:50 PM
 */
require_once("../header.php");
_includes();
$mysql = new Mysql;

$ticket = Sales::getHistorySales($mysql, $_REQUEST['id']);

$ac=0;
$ro = "readonly=readonly";
$di = "disabled=disabled";

if($_POST['dispatch']=="payment")
    $titulo = "Consulta";

if($_POST['dispatch']=="payment")
    $ac=0;
else
    $ac=1;

//antes de considerar devoluciones 050619
//$ticket2 = Sales::getRow($mysql,$_POST['id']);
$ticket2 = Sales::getRowCollect($mysql,$_REQUEST['id']);
//print_r($ticket2);
$listpay = Sales::getHystoricPaymentsBySO($mysql,$_REQUEST['id']);
$dev = Returns::getReturnByIdOV($mysql,$_REQUEST['id'],$ticket2[0]->fnidpartner);
//print_r($dev);

$totalov = Sales::getTotalSales($mysql,$_REQUEST['id']);
$amountpay = Sales::getAmountPaymentValid($mysql,$_REQUEST['id'],1);
if($amountpay[0]->amount =="")
    $amountpay[0]->amount = 0.00;

//print_r($totalov);
//print_r($amountpay);

?>
<form id="requestform" name="requestform" data-toggle="validator" class="form-horizontal" action="salesquery.php" method="post" onsubmit="return validatePaymentForm();">
    <div class="panel panel-primary" style="width: 70%; margin:0 auto;">
        <div class="panel-heading"><h7><strong>Estatus de Cobranza</strong></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div>
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label"  style="width:200px" for="_idov">ID Venta:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." id="_idov" name="_idov" value="<? echo $_POST['id'];?>" readonly/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_status">Estatus:</label>
                          </span>
                                <select id="_status" name="_status" class="form-control" required="required" disabled>
                                    <? if($ticket2[0]->dsstatush == 1){?>
                                        <option value="1" selected>CREADO</option>
                                    <? }elseif($ticket2[0]->dsstatush == 3){?>
                                        <option value="3" selected>ENTREGADO</option>
                                    <? }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label"  style="width:200px" for="_fecha">Fecha:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." id="_fecha" name="_fecha" value="<? echo $ticket2[0]->dddocdate;?>" readonly/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" style="width:200px"  for="_cliente">&nbsp;&nbsp;Cod. Cliente:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." name="_cliente" id="_cliente" value="<? echo $ticket2[0]->dscodep;?>" required="required" readonly/>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_clientename">Raz&oacute;n Social:</label>
                          </span>
                                <input type="text" id="_clientename" class="form-control" aria-label="..." id="_clientename" name="_clientename" value="<? echo $ticket2[0]->dsnamep;?>" readonly="readonly"  required="required"/>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                    </div><!-- /.row cliente-->
                    <div>
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" style="width:200px" for="_ref">Referencia Cobranza:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." name="_ref" id="_ref" value="<? echo $ticket2[0]->refsalesstatus;?>" required="required"/>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_status">Estatus Cobranza:</label>
                          </span>
                            <select id="_status" name="_status" class="form-control" required="required">
                                <option value="PENDIENTE" <? if($ticket[0]->statsalesstatus == "PENDIENTE"){ echo "selected";}?>>PENDIENTE</option>
                                <option value="PARCIAL" <? if($ticket[0]->statsalesstatus == "PARCIAL"){ echo "selected";}?>>PARCIAL</option>
                                <option value="PAGADO" <? if($ticket[0]->statsalesstatus == "PAGADO"){ echo "selected";}?>>PAGADO</option>
                            </select>
                                <!--input type="text" id="_clientename" class="form-control" aria-label="..." id="_status" name="_status" value="< ? echo $ticket[0]->dsname;?>" required="required"/-->
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                    </div>
                    <div class="row">
                        <div>
                            <div class="col-lg-6">
                                <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label"  style="width:200px" for="_mpending">&nbsp;&nbsp;Monto Pendiente:</label>
                          </span>
                                    <input type="text" class="form-control" aria-label="..." name="_mpending" id="_mpending" value="<? echo number_format((($totalov[0]->total * 1) - $amountpay[0]->amount),2,'.',',');?>" readonly "/>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                            <div class="col-lg-6">
                                <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_mpay">Abono:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." id="_mpay" name="_mpay" value="" required="required"/>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                        </div>
                    </div><!-- amount -->
                    <div class="row">
                      <div class="col-lg-6">
                          <div class="input-group">
                        <span class="input-group-addon">
                          <label class="col-sm-2 control-label" for="_paytype">Tipo Pago:</label>
                        </span>
                          <select id="_paytype" name="_paytype" class="form-control" required="required">
                              <option value="PAGO">PAGO</option>
                              <option value="PARCIAL">PARCIAL</option>
                              <option value="NC" >NC</option>
                              <option value="APORTACION">APORTACION</option>
                              <option value="COMISION">COMISION</option>
                          </select>
                              <!--input type="text" id="_clientename" class="form-control" aria-label="..." id="_status" name="_status" value="< ? echo $ticket[0]->dsname;?>" required="required"/-->
                          </div><!-- /input-group -->
                      </div><!-- /.col-lg-6 -->
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button type="submit" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer">Guardar</button>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="sendAction('paymentdetail.php','dispatch=listcredit&id=<? echo $ticket2[0]->fnidpartner;?>');">Regresar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#listpayment">Pagos</a></li>
                <li><a data-toggle="tab" href="#articles">Detalle Venta</a></li>
                <li><a data-toggle="tab" href="#return">Devoluciones</a></li>
            </ul>
            <div class="tab-content">
                <div id="articles" class="tab-pane fade in">
                    <!-- end panel add items-->
                    <table id="c_detail" border="1">
                        <thead>
                        <tr><th>#</th>
                            <th>SKU</th>
                            <th>No. Pedido</th>
                            <th>Descripci&oacuten</th>
                            <th>Cant Ori</th>
                            <th>Cantidad</th>
                            <th>P. Base $</th>
                            <th>P. Uni $</th>
                            <!--th>IVA</th-->
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="10">&nbsp;</td>
                        </tr>
                        <?
                        for($i=0; $i<count($ticket2); $i++){
                            /*precio*/
                            //print_r($ticket2);
                            $itemprice = Item::getAllByBasePrice($mysql,$ticket2[$i]->fniditem);
                            /*fin obtiene precio*/

                            $subtotallineas += (($ticket2[$i]->dnquantity - $ticket2[$i]->Dev) * $ticket2[$i]->dnprice);
                            $sumdisc += $rowdisc;
                            ?>
                            <tr id="<? echo $i;?>" >
                                <td><? echo $i+1;?></td>
                                <td><input type='text' value='<? echo $ticket2[$i]->dsitemcode;?>' style='width:80px; font-size: 10px' disabled/></td>
                                <td><input type='text' value='<? echo $ticket2[$i]->dsrefline;?>' style='width:100px; font-size: 10px' disabled/></td>
                                <td><input type='text'  value='<? echo $ticket2[$i]->dsitemname;?>' style='width:150px; font-size: 10px' disabled/></td>
                                <td><input type='text' value='<? echo $ticket2[$i]->dnquantity;?>' style='width:70px; font-size: 10px' disabled/></td>
                                <td><input type='text' value='<? echo $ticket2[$i]->dnquantity - $ticket2[$i]->Dev;?>' style='width:70px; font-size: 10px' disabled/></td>
                                <td align="right"><input type='text' value='<? echo number_format($itemprice[0]->ddprice,2,".",",");?>' style='width:100px; font-size: 10px; text-align: right;' disabled/></td>
                                <td><input type='text' value='<? echo number_format($ticket2[$i]->dnprice,2,".",",");?>' style='width:100px; font-size: 10px; text-align: right;' disabled/>
                                    <input type='hidden' value='<? echo $ticket2[$i]->dniva;?>'/>
                                    <input type='hidden' value='<? echo ($ticket2[$i]->fniditem);?>' />
                                    <input type='hidden' value="<? echo $ticket2[$i]->pnidline;?>" />
                                </td>
                                <td><input type='text' value='<? echo number_format(($ticket2[$i]->dnprice * ($ticket2[$i]->dnquantity - $ticket2[$i]->Dev)),2,".",",");?>' style='width:100px; font-size: 10px' disabled/></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
                <!-- articles-->
                <div id="listpayment" class="tab-pane fade in active">
                    <table>
                        <thead>
                            <tr><th>Tipo</th>
                                <th>ID Pago</th>
                                <th>Fecha</th>
                                <th>Referencia</th>
                                <th>Abono</th>
                                <th>Abono Total</th>
                                <th>Pendiente</th>
                                <th>Registrado por</th>
                            </tr>
                        </thead>
                        <tbody>
                        <? for($i=0; $i<count($listpay); $i++){?>
                            <tr><td><? echo $listpay[$i]->dspaymentype;?></td>
                                <td><? echo $listpay[$i]->pnid;?></td>
                                <td><? echo $listpay[$i]->dddate;?></td>
                                <td><? echo $listpay[$i]->dsreference;?></td>
                                <td style="text-align: right">$<? echo number_format($listpay[$i]->dsamount,2,'.',',');?></td>
                                <td style="text-align: right">$<? echo number_format($listpay[$i]->dsamount,2,'.',',');?></td>
                                <td style="text-align: right">$<? echo number_format(($listpay[$i]->dscredit - $listpay[$i]->dsamount),2,'.',',');?></td>
                                <td style="text-align: center"><? echo $listpay[$i]->dsuser;?></td>
                            </tr>
                        <? }?>
                        </tbody>
                    </table>
                </div>
                <!-- returns -->
                <div id="return" class="tab-pane fade in">
                    <table id="c_detail" border="1">
                        <thead>
                        <tr><th>SKU</th>
                            <th>No. Pedido</th>
                            <th>Descripci&oacuten</th>
                            <th>Cantidad</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <?
                        for($i=0; $i<count($dev); $i++){
                            ?>
                            <tr id="<? echo $i;?>" >
                                <td><input type='text' value='<? echo $dev[$i]->dsitemcode;?>' style='width:80px; font-size: 10px' disabled/></td>
                                <td><input type='text' value='<? echo $dev[$i]->dsrefline;?>' style='width:100px; font-size: 10px' disabled/></td>
                                <td><input type='text'  value='<? echo $dev[$i]->dsitemname;?>' style='width:150px; font-size: 10px' disabled/></td>
                                <td><input type='text' value='<? echo $dev[$i]->dnquantity;?>' style='width:70px; font-size: 10px' disabled/></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div><!-- tab -->

            <!-- totales -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">&nbsp;</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="subtot">&nbsp;&nbsp;Subtotal: MXN $</label>
                                  </span>
                                <input class="form-control" type="textbox" id="subtot" name="subtot" value="<? echo "$".number_format($subtotallineas,2);?>" readonly="readonly"/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">&nbsp;</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_moneda">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IVA: MXN $</label>
                          </span>
                                <input class="form-control" type="textbox" id="ivatot" name="ivatot" value="<? echo "$".number_format(0.00,2);?>" readonly="readonly"/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">&nbsp;</span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="total">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total: MXN $</label>
                          </span>
                                <? $subtotalCdesc = ($subtotallineas - $sumdisc);?>
                                <input class="form-control" type="textbox" id="total" name="total" value="<? echo "$".number_format(($subtotalCdesc),2);?>" readonly="readonly"/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- subtotales -->

        </div>
    </div>


    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
    <input type="hidden" id="_idsn" name="_idsn" value="<? echo $ticket2[0]->fnidpartner;?>"/>
    <input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
    <input type="hidden" id="iduser" name="iduser" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>"/>
</form>
