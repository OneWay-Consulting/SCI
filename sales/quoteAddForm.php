<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 06/11/2018
 * Time: 12:40 PM
 */

require_once("../header.php");
_includes();
$mysql = new Mysql;
$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

$role = Role::getAll($mysql);
$ac=0;
$ro = "readonly=readonly";
$di = "disabled=disabled";

if($_POST['dispatch']=="update"){
    $titulo = "Actualizaci&oacute;n";
}elseif($_POST['dispatch']=="query"){
    $titulo = "Consulta";
}else
    $titulo = "Crear";

if($_POST['dispatch']=="add" || $_POST['dispatch']=="update")
    $ac=0;
else
    $ac=1;

if($_POST['dispatch']=="update" || $_POST['dispatch']=="query")
    $ticket = Quote::getRow($mysql, $_POST['id']);
//print_r($ticket);
$listcustomer = User::listUserByType($mysql,"2");

if($_SESSION['user']->getUser()->fnidrole!=7)
    $listware = Item::getWareHouse($mysql);
else
    $listware = Item::getWareHouse($mysql,$_SESSION['user']->getUser()->fnidrole);
//$listserial = Purchase::getSerialByRow($mysql,$_POST[id]);

//print_r($listserial);
?>
<!-- CODE TO MANAGER GRID -->
<!--link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script-- src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->
<script src="../js/requestSoap.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- END CODE TO MANAGER GRID -->
<form id="requestform" name="requestform" data-toggle="validator" class="form-horizontal" action="quotequery.php" method="post">
    <div class="panel panel-info">
        <div class="panel-heading"><h7><strong><?=$titulo;?> Cotizaci&oacute;n <?echo $_POST['id'];?></strong></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_status">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estatus:</label>
                          </span>
                                <select id="_status" name="_status" class="form-control" required="required">
                                    <?  //if($ticket[0]->dsstatush != 5 && $ticket[0]->dsstatus != 4){
                                    if($ticket[0]->dsstatush == 1) {
                                        ?>
                                        <option value="1" <? if ($ticket[0]->dsstatush == 1) {
                                            echo "selected";
                                        } ?>>CREADO
                                        </option>
                                        <?
                                    } if($ticket[0]->dsstatush == 4 || $ticket[0]->dsstatush == 1){
                                        ?>
                                    <option value="4" <? if($ticket[0]->dsstatush == 4){ echo "selected";}?>>AUTORIZADO</option>
                                    <? }else{?>
                                        <option value="1" <? if ($ticket[0]->dsstatush == 1) {
                                            echo "selected";
                                        } ?>>CREADO
                                        </option>
                                        <option value="5" <? if ($ticket[0]->dsstatush == 1) {
                                            echo "selected";
                                        } ?>>CERRADO</option>
                                    <? }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_fecha">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." id="_fecha" name="_fecha" value="<? if($_POST['dispatch']=="update" || $ac){echo $ticket[0]->ddcreated;}else{echo date("Y-m-d H:i:s");}?>" readonly/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_cliente">&nbsp;&nbsp;Cod. Cliente:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." name="_cliente" id="_cliente" value="<? echo $ticket[0]->dscodep;?>" <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?> onKeyUp="checkInputSN();"  onchange="setValuesSOAP();deleteTableAllRow();" required="required"/>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_clientename">Raz&oacute;n Social:</label>
                          </span>
                                <input type="text" id="_clientename" class="form-control" aria-label="..." id="_clientename" name="_clientename" value="<? echo $ticket[0]->dsnamep;?>" readonly="readonly"  required="required"/>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                    </div><!-- /.row cliente-->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_referencia">&nbsp;&nbsp;&nbsp;&nbsp;Referencia:</label>
                          </span>
                                <input type="text" class="form-control" id="_referencia" name="_referencia" value="<? if($_POST['dispatch']!="add"){echo $ticket[0]->dsreference;}else{echo $_SESSION['user']->getUser()->idtcuser."_".date("Y-m-d_H-i-s");};?>"  required="required" readonly/>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_comentarios">Comentarios:</label>
                          </span>
                                <textarea class="form-control" rows="3" id="_comentarios" name="_comentarios" maxlength="200" <? if($_POST['dispatch']=="update" || $ac){echo $di;}?>><? echo $ticket[0]->dscomments;?></textarea>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                    </div><!-- referencia comentario -->
                </div>
            </div>

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#articles">Listado Art&iacute;culos</a></li>
                <!--li><a data-toggle="tab" href="#serie">Series</a></li-->
            </ul>
            <div class="tab-content">
                <div id="articles" class="tab-pane fade in active">

                    <!-- panel add items -->
                    <? if($_POST['dispatch']=="add"){?>
                        <!-- panel add items -->
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="articulo">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Art&iacute;culo:</label>
                                  </span>
                                            <input type="text" class="form-control" name="articulo" id="articulo" onKeyUp="checkInputDM();" onchange="setValuesSOAP();"  />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="desc">Descripci&oacute;n:</label>
                                  </span>
                                            <input type="text" class="form-control" id="desc" name="desc" readonly/>
                                        </div><!-- /input-group -->
                                    </div>
                                </div><!-- item descripcion-->
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="precio">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Precio: MXN $</label>
                                  </span>
                                            <input type="text" class="form-control" name="precio" id="precio"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="iva">&nbsp;IVA:</label>
                                  </span>
                                            <select id="iva" name="iva" class="form-control"><option value="0">0</option><option value="16" selected>16</option></select>
                                        </div>
                                    </div>
                                </div><!-- stock price-->
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_ware">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&Aacute;lmacen:</label>
                                  </span>
                                            <select id="_ware" name="_ware" class="form-control">
                                                <? for($i=0; $i<count($listware); $i++){?>
                                                    <option value="<? echo $listware[$i]->pnid;?>"><? echo $listware[$i]->dsname;?></option>
                                                <? }?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="manser">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Maneja Series:</label>
                                  </span>
                                            <input type="text" class="form-control" name="manser" id="manser" readonly/>
                                            <input type="hidden" id="_idarticle" name="_idarticle" />
                                        </div>
                                    </div>
                                </div><!-- stock price-->
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="qty">Cantidad:</label>
                                  </span>
                                            <input type="text" class="form-control" name="qty" id="qty"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">&nbsp;
                                            <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="qty">No. Pedido:</label>
                                  </span>
                                    <input type="text" class="form-control" name="nopedido" id="nopedido" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-info" onclick="if( valideateToAdd()){addTableRow('c_detail',Array('','<input type=hidden id=\'_nl[]\' name=\'_nl[]\' value=\''+document.getElementById('c_detail').rows.length+'\' /><input type=hidden id=\'_iditem[]\' name=\'_iditem[]\' value=\''+document.getElementById('_idarticle').value+'\' /><input type=\'text\' name=\'_art[]\' value=\''+document.getElementById('articulo').value+'\' style=\'width:100px\' readonly />','<input type=\'text\' name=\'_desc[]\' id=\'_desc[]\' value=\''+document.getElementById('desc').value+'\' style=\'width:250px\' readonly />',
                                                    '<input type=\'hidden\' id=\'_whscode[]\' name=\'_whscode[]\' value=\''+document.getElementById('_ware').value+'\'  /><input type=\'text\' name=\'_qty[]\' id=\'_qty[]\' value=\''+document.getElementById('qty').value+'\' style=\'width:60px\' onChange=\'calculateSub();\' />',
                                                    '<input type=\'text\' name=\'_qtyo[]\' id=\'_qtyo[]\' value=\'0\' style=\'width:60px\' onChange=\'calculateSub();\' readonly />',
                                                    '<input type=\'text\' name=\'_qtyc[]\' id=\'_qtyc[]\' value=\''+document.getElementById('qty').value+'\' style=\'width:60px\' onChange=\'calculateSub();\' />',
                                                    '<input type=\'text\' name=\'_stock[]\' id=\'_stock[]\' value=\'0\' style=\'width:60px\' onChange=\'calculateSub();\' readonly />',
                                                    '<input type=\'text\' name=\'_priceLP[]\' id=\'_priceLP[]\' value=\''+document.getElementById('precio').value+'\' style=\'width:100px\' readonly />',
                                                    '<input type=\'text\' name=\'_price[]\' id=\'_price[]\' value=\''+document.getElementById('precio').value+'\' style=\'width:100px\' readonly />',
                                                    '',
                                                    '<input type=\'text\' name=\'_refline[]\' id=\'_refline[]\' value=\''+document.getElementById('nopedido').value+'\' style=\'width:50px\' onChange=\'calculateSub();\' readonly />',                                                    '<img src=<? echo URLWEB;?>images/buttons/eliminar.png style=\'cursor:pointer; width:30px;\' title=Eliminar Registro OnClick=deleteTableRow(\'c_detail\',\''+document.getElementById('c_detail').rows.length+'\');><input type=\'hidden\' id=\'_idline[]\' name=\'_idline[]\' /><input type=\'hidden\' name=\'_sub[]\' id=\'_sub[]\' value=\''+(document.getElementById('precio').value)+'\' /> <input type=\'hidden\' name=\'_iva[]\' id=\'_iva[]\' value=\''+document.getElementById('iva').value+'\' /> '));cleanValues();calculateSub();}else{alert('Debe ingresar un articulo valido!');}hde(document.getElementById('c_detail'));">Agregar art&iacute;culo</button>
                                            <!--button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Agregar Art&iacute;culo</button-->
                                        </div><!-- /input-group -->
                                    </div>
                                </div><!-- discount-->
                            </div>
                        </div>
                    <? } //dispatch add ?>
                    <!-- end panel add items-->

                    <table id="c_detail" border="1">
                        <thead>
                        <tr><th>ID Linea</th>
                            <th>SKU</th>
                            <th>Descripci&oacuten</th>
                            <th>Cant. Ori.</th>
                            <th>Pend.</th>
                            <th>Cantidad</th>
                            <th>Disp.</th>
                            <th>P. Base $</th>
                            <th>P. Unit $</th>
                            <th>No. Pedido</th>
                            <th>Estatus</th>
                            <th><label for="cbox2">Sel</label><input type="checkbox" id="select_all" onchange="selectAll(this.checked);" /></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="10">&nbsp;</td>
                        </tr>
                        <?
                        $complete = true;
                        for($i=0; $i<count($ticket); $i++){

                            /*precio*/
                            //print_r($ticket);

                            $itemprice = Item::getAllByBasePrice($mysql,$ticket[$i]->fniditem,$ticket[$i]->fnidware);
                            //print_r($itemprice);
                            //echo "<br />******";
                            //print_r($ticket);
                            /*fin obtiene precio*/

                            $subtotallineas += ($ticket[$i]->dnquantity * $ticket[$i]->dnprice);
                            $sumiva += (($ticket[$i]->dnquantity * $ticket[$i]->dnprice)*$ticket[$i]->dniva)/100;
                            $sumdisc += $rowdisc;
                            ?>
                            <tr id="<? echo $i;?>" >
                                <td><? echo $ticket[$i]->pnidline;?></td>
                                <td><input type='text' name='_art[]' id="_art[]" value='<? echo $ticket[$i]->dsitemcode;?>' style='width:70px' <? echo $ro;?>/></td>
                                <td><input type='text' name='_desc[]' id='_desc[]' value='<? echo $ticket[$i]->dsitemname;?>' style='width:300px' <? echo $ro;?> /></td>
                                <td style="background-color: #ff851b"><input type='text' name='_qty[]' id='_qty[]' value='<? echo $ticket[$i]->dnquantity;?>' style='width:60px' <? echo $ro;?> /></td>
                                <td style="background-color: #ff851b"><input type='text' name='_qtyo[]' id='_qtyo[]' value='<? echo(($ticket[$i]->dnopenqty <= 0)?0:$ticket[$i]->dnopenqty );?>' style='width:60px' <? echo $ro;?> /></td>
                                <td style="background-color: #4f8a10"><input type='text' name='_qtyc[]' id='_qtyc[]' value='<? echo(($ticket[$i]->dnopenqty <= 0)?0:$ticket[$i]->dnopenqty );?>' style='width:60px' <? if($ticket[$i]->dnopenqty <= 0){ echo $ro;}else{$complete=false;}?>/></td>
                                <td style="background-color: #ff851b"><input type='text' name='_stock[]' id='_stock[]' value='<? if($itemprice[0]->stock >0){echo ($itemprice[0]->stock );}else{if($_SESSION['user']->getUser()->fnidrole==7){echo "999";}else{echo 0;}}?>' style='width:70px' <? echo $ro;?> /></td>
                                <td><input type='text' name='_priceLP[]' id='_priceLP[]'  value='<? echo number_format($itemprice[0]->ddprice,2,".",",");?>' style='width:100px' <? echo $ro;?>/></td>
                                <td><input type='text' name='_price[]' id='_price[]'  value='<? echo number_format($ticket[$i]->dnprice,2,".",",");?>' style='width:100px' <? echo $ro;?>/></td>
                                <td><input type='text' name='_refline[]' id='_refline[]'  value='<? echo $ticket[$i]->dsrefline;?>' style='width:90px' <? echo $ro;?>/></td>
                                <td>
                                    <select id="_statuspart[]" name="_statuspart[]" style='width:90px'>
                                        <? if($ticket[$i]->dsstatusl!="CANCEL"){?>
                                        <option value=""></option>
                                        <? }?>
                                        <option value="CANCEL">CANCELAR</option>
                                    </select>
                                </td>
                                <td><input type='hidden' name='_iditem[]' id='_iditem[]'  value='<? echo ($ticket[$i]->fniditem);?>' />
                                    <input type='hidden' id="_idline[]" name="_idline[]" value="<? echo $ticket[$i]->pnidline;?>" />
                                    <input type='hidden' name='_iva[]' id='_iva[]' value='<? echo $ticket[$i]->dniva;?>' />
                                    <? //if($ticket[$i]->dnopenqty > 0){?>
                                    <!--input type="checkbox" id="_aut[]" name="_aut[]" value="< ? echo $ticket[$i]->pnidline;?>" /-->
                                    <input type="checkbox" id="_aut_<? echo $ticket[$i]->pnidline;?>" name="_aut_<? echo $ticket[$i]->pnidline;?>" value="<? echo $ticket[$i]->pnidline;?>" />
                                    <? //}?>
                                    <? if($ticket[$i]->dnquantity > ($itemprice[0]->stock /*- $itemprice[0]->stockavailable*/)){?><img src="../images/buttons/advertencia.png" alt="Stock insuficiente" title="Stock insuficiente" /><?}?>
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>

                </div><!-- articles-->
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
                                <input class="form-control" type="textbox" id="ivatot" name="ivatot" value="<? echo "$".number_format((($subtotallineas - $sumdisc) * 0),2);?>" readonly="readonly"/>
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
                                <input class="form-control" type="textbox" id="total" name="total" value="<? echo "$".number_format((($subtotallineas - $sumdisc) * 0),2);?>" readonly="readonly"/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- subtotales -->
            <div class="row">
                <? if($ticket[0]->dsstatush != 5){?>
                    <? //if(!$complete || $_POST['dispatch']=="add"){?>
                <div class="col-lg-6">
                    <div class="input-group">
                        <? if($permission['quote.php']['create']){?>
                            <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateFormQuote(event)" >Guardar</button>
                            <? }?>
                    </div>
                </div>
                        <? //}?>
                <? }?>
                <div class="col-lg-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="location.href='quote.php'">Regresar</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- panel -->
    <input type="hidden" name="idcreator" id="idcreator" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
    <input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
    <input type="hidden" id="_dstype" name="_dstype" value="C"/>
    <input type="hidden" id="_pnidcliente" name="_pnidcliente"/>
</form>

<br /><br /><br />
<script>

    function selectAll(valor){
        //$("form input:checkbox").attr("checked" , true);

        /*var checkboxes = $(this).closest('form').find(':checkbox');
        if($(this).is(':checked')) {
            checkboxes.attr('checked', 'checked');
        } else {
            checkboxes.removeAttr('checked');
        }
        */

        var checked_status = valor;
        $("input[name^='_aut_']").each(function(){
            this.checked = checked_status;
        });

    }//function
</script>
