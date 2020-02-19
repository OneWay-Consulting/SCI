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
		}elseif($_POST['dispatch']=="query" || $_POST['dispatch']=="delivery"){
			$titulo = "Consulta";
		}else
			$titulo = "Crear";

		if($_POST['dispatch']=="add" || $_POST['dispatch']=="update")
			$ac=0;
		else
			$ac=1;

//print_r($_POST);
//print_r($ticket);


		if($_POST['dispatch']=="update" || $_POST['dispatch']=="query" || $_POST['dispatch']=="delivery")
			$ticket = Sales::getRow($mysql, $_POST['id']);

		if($ticket[0]->dsstatush == 3);
		    $listserial = Sales::getSerialByRow($mysql,$_POST[id]);

		 //print_r($_POST);
		 //print_r($ticket);
		//$listcustomer = User::listUserByType($mysql,"2");
        //$listware = Item::getWareHouse($mysql);


?>
<!-- CODE TO MANAGER GRID -->
<!--link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script-- src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->
<script src="../js/requestSoap.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- END CODE TO MANAGER GRID -->
<form id="requestform" name="requestform" data-toggle="validator" class="form-horizontal" action="inventoryquery.php" method="post">
<div class="panel panel-info">
    <div class="panel-heading"><h7><strong><?=$titulo;?> Venta <? echo $_POST['id'];?></strong></h7></div>
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
                                <? if($ticket[0]->dsstatush!=3){?>
                                    <option value="1" <? if($ticket[0]->dsstatush==1){ echo "SELECTED";}?>>CREADO</option>
                                <? }?>
                                    <option value="3" <? if($ticket[0]->dsstatush==3){ echo "SELECTED";}?>>ENTREGADO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_fecha">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha:</label>
                          </span>
                            <input type="text" class="form-control" aria-label="..." id="_fecha" name="_fecha" value="<? if($_POST['dispatch']=="delivery"){echo $ticket[0]->ddcreated;}else{echo date("Y-m-d H:i:s");}?>" <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?>/>
                        </div><!-- /input-group -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_cliente">&nbsp;&nbsp;Cod. Proveedor:</label>
                          </span>
                            <input type="text" class="form-control" aria-label="..." name="_cliente" id="_cliente" value="<? echo $ticket[0]->dscode;?>" <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?> onKeyUp="checkInputSN();"  onchange="setValuesSOAP();deleteTableAllRow();" required="required"/>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_clientename">Nom. Proveedor:</label>
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
                            <input type="text" class="form-control" id="_referencia" name="_referencia" value="<? echo $ticket[0]->dsreference;?>"  <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?> required="required"/>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_comentarios">Comentarios:</label>
                          </span>
                          <textarea class="form-control" rows="3" id="_comentarios" name="_comentarios" maxlength="200" <? if($ac){echo $ro;}?>><? echo $ticket[0]->dscomments;?></textarea>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                </div><!-- referencia comentario -->
            </div>
        </div>
        <button class="btn btn-warning" type="button">
            Total por surtir <span class="badge" id="_totalqty">0</span>
        </button>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#articles">Captura Art&iacute;culos</a></li>
            <li><a data-toggle="tab" id="_liseries" href="#serie">Series</a></li>
            <!--li><a data-toggle="tab" id="_tabguia" href="#serie">Gu&iacute;as</a></li-->
        </ul>
        <div class="tab-content">
            <div id="articles" class="tab-pane fade in active">
                <table id="c_detail" border="1">
                    <thead>
                    <tr><th>ID Line</th>
                        <th>SKU</th>
                        <th>Descripci&oacuten</th>
                        <!--th>&Aacute;lmacen</th-->
                        <th>No. Pedido</th>
                        <th>Cant. Ori</th>
                        <th>Cant. Pend.</th>
                        <th>Entregar</th>
                        <!--th>Precio Uni $</th-->
                        <!--th>IVA</th-->
                        <? if($ticket[0]->dsstatush == 3){?>
                            <th>Gu&iacute;a</th>
                            <th>Gu&iacute;a Confirm.</th>
                            <th>Estatus Confirm.</th>
                        <? }?>
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="8">&nbsp;</td>
                    </tr>
                    <?
                    $totalqty = 0;
                    for($i=0; $i<count($ticket); $i++){
                        /*$idfull .= $ticket[$i]->idline."&";
                        $discbyprice = (($ticket[$i]->ddprice * $ticket[$i]->dddiscount)/100);
                        $pricewithdisc = $ticket[$i]->ddprice - $discbyprice;
                        $subtotallineas += ($ticket[$i]->ddquantity * $ticket[$i]->ddprice);
                        $rowdisc = ($ticket[$i]->ddquantity * $discbyprice);
                        $sumdisc += $rowdisc;
                        */
                        $totalqty += $ticket[$i]->dnopenqty;
                        ?>
                        <tr id="<? echo $i;?>" >
                            <td><? echo $ticket[$i]->pnidline;?></td>
                            <td><input type='text' name='_art[]' id="_art[]" value='<? echo $ticket[$i]->dsitemcode;?>' style='width:100px' <? echo $ro;?>/></td>
                            <td><input type='hidden' name='_desc[]' id='_desc[]' value='<? echo $ticket[$i]->dsitemname;?>' onchange="$('#_serieaG').focus();"/><? echo utf8_encode($ticket[$i]->dsitemname);?></td>
                            <!--td><input type='text' name='_whscode[]' id='_whscode[]' value='< ? echo $ticket[$i]->fnidware;?>' style='width:250px' < ? echo $ro;?> /></td-->
                            <td><input type='text' name='_refline[]' id='_refline[]' value='<? echo $ticket[$i]->dsrefline;?>' style='width:120px' <? echo $ro;?> />
                            <!--td><input type='text' name='_qty[]' id='_qty[]' onChange="calculateSub();"  value='< ? echo $ticket[$i]->dnquantity;?>' style='width:100px' < ? echo $ro;?> /-->
                            <td><input type='text' name='_qty[]' id='_qty[]' value='<? echo $ticket[$i]->dnquantity;?>' style='width:100px' <? echo $ro;?> /></td>
                            <td><input type='text' name='_qtyo[]' id='_qtyo[]' value='<? echo $ticket[$i]->dnopenqty;?>' style='width:100px' <? echo $ro;?> /></td>
                            <td style="background-color: #4f8a10"><input type='text' name='_qtyr[]' id='_qtyr[]' onChange="calculateQty();"  value='<? echo $ticket[$i]->dnopenqty;?>' <? if($ticket[$i]->dnopenqty <= 0){echo $ro;}?> style='width:100px'/></td>

                            <input type='hidden' name='_price[]' id='_price[]'  value='<? echo number_format($ticket[$i]->dnprice,2,'.',',');?>' style='width:100px' <? echo $ro;?>/>
                            <input type='hidden' name='_iva[]' id='_iva[]'  value='<? echo $ticket[$i]->dniva;?>' style='width:100px' <? echo $ro;?>/>
                            </td>
                            <? if($ticket[0]->dsstatush == 3){?>
                                <td><input type='text' name='_guia[]' id='_guia[]'  value='<? echo $ticket[$i]->dsguia;?>' style='width:100px' />
                                <td><input type='text' name='_guiac[]' id='_guiac[]' value='<? echo $ticket[$i]->dsguiac;?>' style='width:100px' <? if($ticket[$i]->dsguia == $ticket[$i]->dsguiac && $ticket[$i]->dsstatusguia == "E"){ echo $ro;}?> />
                                <td><select id="_statuslg" name="_statuslg" >
                                        <option value="">-Seleccione-</option>
                                        <option value="C" <? if($ticket[$i]->dsstatusguia == "C"){ echo "selected";}?>>Creado</option>
                                        <option value="S" <? if($ticket[$i]->dsstatusguia == "S"){ echo "selected";}?>>Surtido</option>
                                        <option value="E" <? if($ticket[$i]->dsstatusguia == "E"){ echo "selected";}?>>Entregado</option>
                                    </select>
                                </td>
                            <? }?>
                            <td><input type='hidden' name='_iditem[]' id='_iditem[]'  value='<? echo ($ticket[$i]->fniditem);?>' />
                                <input type='hidden' id="_idline[]" name="_idline[]" value="<? echo $ticket[$i]->pnidline;?>" />
                                <input type='hidden' id="_whscode[]" name="_whscode[]" value="<? echo $ticket[$i]->fnidware;?>" />
                                <? if($_POST['dispatch']=="delivery"){?>
                                    <? if($ticket[$i]->dnopenqty > 0){?>
                                        <img src="<? echo URLWEB;?>images/buttons/serie.png" id="_idart_<? echo ($i+1);?>" name="_idart_<? echo ($i+1);?>" style='cursor:pointer; width:30px;' title="Asignar series" OnClick="setValuesToReadSeries('<? echo $ticket[$i]->pnidline;?>','<? echo $ticket[$i]->fniditem;?>','<? echo $ticket[$i]->dsitemcode;?>',<? echo $ticket[$i]->dnquantity?>,<? echo $i?>,'<? echo $ticket[$i]->dsitemname;?>','<? echo $ticket[$i]->fnidware;?>',<? echo ($i+1);?>); 
										document.getElementById('_serieaG').focus(); setTimeout(function() { $('input[name=_serieaG]').focus() }, 1000);document.getElementById('_liseries').click(); ">
                                        <img id="_idimg_<? echo $ticket[$i]->pnidline;?>" src="<? echo URLWEB;?>images/buttons/advertencia.png" style='cursor:pointer; width:25px;' title="Falta" />
                                    <? }else{?>
                                        <img src="<? echo URLWEB;?>images/buttons/complete.png" id="_idart_<? echo ($i+1);?>" name="_idart_<? echo ($i+1);?>" style='cursor:pointer; width:30px;' title="Completado" />
                                    <? }?>
                                <? }?>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
            <!-- end panel add items-->
            <div id="serie" class="tab-pane fade"><!-- serie-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <? //if($ticket[0]->dsstatush == 1){
                        if($totalqty > 0){
                        ?>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_lineaG">ID Linea:</label>
                                  </span>
                                    <input type="text" class="form-control" aria-label="..." id="_lineaG" name="_lineaG" value="" <? echo $ro;?>/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_fecha">Art&iacute;culo:</label>
                                  </span>
                                    <input type="text" class="form-control" aria-label="..." id="_dscodeaG" name="_dscodeaG" value="" <? echo $ro;?>/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_lineaG">Nombre Art:</label>
                                  </span>
                                    <input type="text" class="form-control" aria-label="..." id="_dsnameG" name="_dsnameG" value="" <? echo $ro;?>/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_fecha">Cantidad:</label>
                                  </span>
                                    <input type="text" class="form-control" aria-label="..." id="_qtyaG" name="_qtyaG" value="" readonly/>
                                    <input type="hidden" id="_whscodeG" name="_whscodeG"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_fecha">Lote/Serie:</label>
                                  </span>
                                    <input type="text" class="form-control" aria-label="..." id="_serieaG" name="_serieaG"
                                           onkeypress="if(enterpressalert(event) && valideateToAddDelivery()){if(document.getElementById('_seriesok').value == 1){addTableRow('c_detailser',
                                                   Array('<input type=text id=\'_linea[]\' name=\'_linea[]\' style=\'width:50px\' value=\''+document.getElementById('_lineaG').value+'\' />',
                                                   '<input type=text id=\'_idarticlea[]\' name=\'_idarticlea[]\'  value=\''+document.getElementById('_iditemaG').value+'\' readonly/>',
                                                   '<input type=\'text\' name=\'_codea[]\' value=\''+document.getElementById('_dscodeaG').value+'\' style=\'width:150px\' readonly />',
                                                   '<input type=\'text\' name=\'_seriea[]\' value=\''+this.value+'\' style=\'width:250px\' readonly />',
                                                   '<input type=\'text\' name=\'_qtya[]\' id=\'_qtya[]\' value=\'1\' style=\'width:50px\' readonly />',
                                                   '<input type=\'text\' name=\'_whscodel[]\' id=\'_whscodel[]\' value=\''+document.getElementById('_whscodeG').value+'\' /><img src=<? echo URLWEB;?>images/buttons/eliminar.png style=\'cursor:pointer; width:30px;\' title=\'Eliminar Registro\' OnClick=deleteTableRowSer(\'c_detailser\',this);$(\'#_totalqty\').text(parseInt($(\'#_totalqty\').text())+1);changeImg('+document.getElementById('_lineaG').value+'); >')
                                                   );
                                            this.value='';
                                            $('#_totalqty').text($('#_totalqty').text()-1);
                                               document.getElementById('_seriesok').value = 0;
                                               //console.log('a cambiar:'+document.getElementById('_linenumaG').value);
                                               //document.getElementById('_idart_'+(parseInt(document.getElementById('_linenumaG').value)+1)).click();
                                                   if(validateToSelectNextLine()){
                                                       document.getElementById('_idart_'+(parseInt(document.getElementById('_linenumaG').value)+1)).click();
                                                   }

                                           }
                                           }else{console.log('no entro');}" />
                                </div>
                            </div>
                            <input type="hidden" class="form-control" aria-label="..." id="_iditemaG" name="_iditemaG"/>
                            <input type="hidden" class="form-control" aria-label="..." id="_linenumaG" name="_linenumaG"/>
                        </div><!-- panel-body-->
                        <? }?>
                        <div class="row">
                            <table id="c_detailser" border="1">
                                <thead>
                                <tr><th>Linea</th>
                                    <th>ID Item</th>
                                    <th>C&oacute;digo</th>
                                    <th>Serie</th>
                                    <th>Cantidad</th>
                                    <!--th>Opciones</th-->
                                </tr>
                                </thead>
                                <tbody>
                                <? for($i=0; $i<count($listserial); $i++){?>
                                    <tr>
                                        <td><? echo $listserial[$i]->linea;?></td>
                                        <td><? echo $listserial[$i]->iditem;?></td>
                                        <td><? echo $listserial[$i]->itemcode;?></td>
                                        <td><? echo $listserial[$i]->serial;?></td>
                                        <td><? echo $listserial[$i]->quantity;?></td>
                                        <!--td>&nbsp;</td-->
                                    </tr>
                                <? }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- panel-->
            </div><!-- series -->
            <!-- guias -->
            <div class="panel panel-default">
                <div class="panel-body">

                </div>
            </div>
            <!-- fin guias -->

        </div><!-- tab -->

        <!-- subtotales -->
        <div class="row">
            <div class="col-lg-6">
            <? //if($ticket[0]->dsstatush != 3){
                if($totalqty>0){
            ?>
                    <? if($permission['inventoryPurchaseForm.php']['create']){?>
                        <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateFormDelivery()" >Guardar</button>
                    <? }?>
            <? }elseif($ticket[0]->dsstatush == 3){?>
                    <? if($permission['inventoryPurchaseForm.php']['create']){?>
                        <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateFormConfirmDelivery()" >Guardar Gu&iacute;as</button>
                    <? }?>
            <? }?>
            </div>
            <div class="col-lg-6">
                <div class="input-group">
                    <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="if(confirm('¿Desea salir? Se perderan datos capturados!')){location.href='inventory.php';}">Regresar</button>
                </div>
            </div>
        </div>
    </div>
</div><!-- panel -->
<input type="hidden" name="idcreator" id="idcreator" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
<? //if($ticket[0]->dsstatush==3){
if($totalqty<=0){
?>
    <input type="hidden" id="dispatch" name="dispatch" value="confirmdelivery"/>
<? }else{?>
    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
<? }?>
<input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
<input type="hidden" id="_dstype" name="_dstype" value="C"/>
    <input type="hidden" id="_dbvalidateguia" name="_dbvalidateguia" value="<? echo $ticket[0]->dbvalidateguia;?>"/>
<input type="hidden" id="_pnidcliente" name="_pnidcliente"/>
    <input type="hidden" name="_seriesok" id="_seriesok" value="0"/>
    <input type="hidden" name="_imgcount" id="_imgcount" value="<? echo count($ticket);?>"/>
</form>


<br /><br /><br />
<script>
    $("#_totalqty").text(<? echo $totalqty;?>);

    function changeImg(id){
        $('#_idimg_'+id).attr('src','../images/buttons/advertencia.png');
        //$('#_idimg_'+linetocheck).attr('src', '../images/buttons/complete.png');
    }

</script>
