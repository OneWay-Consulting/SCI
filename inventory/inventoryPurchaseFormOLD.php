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

		if($_POST['dispatch']=="update" || $_POST['dispatch']=="query" || $_POST['dispatch']=="entry")
			$ticket = Purchase::getRow($mysql, $_POST['id']);

		if($ticket[0]->dsstatus == 2);
		    $listserial = Purchase::getSerialByRow($mysql,$_POST[id]);
        //print_r($ticket);
		//$listcustomer = User::listUserByType($mysql,"2");
        //$listware = Item::getWareHouse($mysql);

        $documentopen = false;
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
    <div class="panel-heading"><h7><strong><?=$titulo;?> Compra</strong></h7></div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_status">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estatus:</label>
                          </span>
                            <select id="_status" name="_status" class="form-control" required="required" >
                                <? if($ticket[0]->dsstatus == "1"){?>
                                    <option value="1" <? if($ticket[0]->dsstatus==1){ echo "SELECTED";}?>>CREADO</option>
                                <? }?>
                                    <option value="2" <? if($ticket[0]->dsstatus==2){ echo "SELECTED";}?>>RECIBIDO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_fecha">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha:</label>
                          </span>
                            <input type="text" class="form-control" aria-label="..." id="_fecha" name="_fecha" value="<? echo date("Y-m-d H:i:s");?>"/>
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
        </ul>
        <div class="tab-content">
            <div id="articles" class="tab-pane fade in active">
                <table id="c_detail" border="1">
                    <thead>
                    <tr><th>LINE ID</th>
                        <th>SKU</th>
                        <th>Descripci&oacuten</th>
                        <th>&Aacute;lmacen</th>
                        <th>Cant. Ori</th>
                        <th>Cant. Pend.</th>
                        <th>a Recibir</th>
                        <!--th>Precio Uni</th-->
                        <!--th>IVA</th-->
                        <th>Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="8">&nbsp;</td>
                    </tr>
                    <?
                    for($i=0; $i<count($ticket); $i++){
                        $idfull .= $ticket[$i]->idline."&";

                        $totalqty += $ticket[$i]->dnquantity;

                        ?>
                        <tr id="<? echo $i;?>" >
                            <td><? echo $ticket[$i]->idline;?></td>
                            <td><input type='text' name='_art[]' id="_art[]" value='<? echo $ticket[$i]->dsitemcode;?>' style='width:100px' <? echo $ro;?>/></td>
                            <td><input type='text' name='_desc[]' id='_desc[]' value='<? echo $ticket[$i]->dsitemname;?>' style='width:350px' <? echo $ro;?> /></td>
                            <td><input type='text' value='<? echo $ticket[$i]->dsnameware;?>' style='width:250px' <? echo $ro;?> />
                                <input type='hidden' name='_whscode[]' id='_whscode[]' value='<? echo $ticket[$i]->fnidware;?>' /></td>
                            <td><input type='text' name='_qty[]' id='_qty[]' value='<? echo $ticket[$i]->dnquantity;?>' style='width:100px' <? echo $ro;?> /></td>
                            <td><input type='text' name='_qtyo[]' id='_qtyo[]' value='<? echo $ticket[$i]->dnopenqty;?>' style='width:100px' <? echo $ro;?> /></td>
                            <td style="background-color: #4f8a10"><input type='text' name='_qtyr[]' id='_qtyr[]' onChange="calculateQty();"  value='<? echo $ticket[$i]->dnopenqty;?>' <? if($ticket[$i]->dnopenqty <= 0){echo $ro;}?> style='width:100px'/></td>
                            <!--td>$<input type='text' name='_price[]' id='_price[]'  value='< ? echo $ticket[$i]->dnprice;?>' style='width:100px' < ? echo $ro;?>/></td-->
                            <!--td><input type='text' name='_iva[]' id='_iva[]'  value='< ? echo $ticket[$i]->dniva;?>' style='width:100px' < ? echo $ro;?>/></td-->
                            <td><input type='hidden' name='_iditem[]' id='_iditem[]'  value='<? echo ($ticket[$i]->fniditem);?>' />
                                <input type='hidden' id="_idline[]" name="_idline[]" value="<? echo $ticket[$i]->idline;?>" />
                                <input type='hidden' id="_price[]" name="_price[]" value="<? echo $ticket[$i]->dnprice;?>" />
                                <input type='hidden' id="_iva[]" name="_iva[]" value="<? echo $ticket[$i]->dniva;?>" />
                                <input type='hidden' id="_upc[]" name="_upc[]" value="<? echo $ticket[$i]->dsupc;?>" />
                                <!--input type='hidden' id="_idline[]" name="_idline[]" value="< ? echo $ticket[$i]->idline;?>" /-->
                                <? if($_POST['dispatch']=="entry"){?>
                                    <img src="<? echo URLWEB;?>images/buttons/serie.png" id="_idart_<? echo ($i+1);?>" name="_idart_<? echo ($i+1);?>" style='cursor:pointer; width:30px;' title="Asignar series" OnClick="setValuesToReadSeries('<? echo $ticket[$i]->idline;?>','<? echo $ticket[$i]->fniditem;?>','<? echo $ticket[$i]->dsitemcode;?>',<? echo $ticket[$i]->dnquantity?>,<? echo $i?>,'<? echo $ticket[$i]->dsitemname;?>','<? echo $ticket[$i]->fnidware;?>',<? echo ($i+1);?>);  document.getElementById('_liseries').click();">
                                    <img id="_idimg_<? echo $ticket[$i]->idline;?>" src="<? echo URLWEB;?>images/buttons/advertencia.png" style='cursor:pointer; width:25px;' title="Falta" />
                                <? }?></td>
                        </tr>
                    <?
                        if($ticket[$i]->dnopenqty > 0)
                            $documentopen = true;
                    } ?>
                    </tbody>
                </table>
            </div>
            <!-- end panel add items-->
            <div id="serie" class="tab-pane fade"><!-- serie-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <? if($documentopen){?>
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
                                    <input type="text" class="form-control" aria-label="..." id="_qtyaG" name="_qtyaG" value="" <? echo $ro;?>/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_fecha">Serie:</label>
                                  </span>
                                    <input type="text" class="form-control" aria-label="..." id="_serieaG" name="_serieaG"
                                           onkeypress="if(enterpressalert(event) && valideateToAdd()){
                                               addTableRow('c_detailser',
                                               Array('<input type=text id=\'_linea[]\' name=\'_linea[]\' style=\'width:50px\' value=\''+document.getElementById('_lineaG').value+'\' readonly/>',
                                                   '<input type=text id=\'_idarticlea[]\' name=\'_idarticlea[]\'  value=\''+document.getElementById('_iditemaG').value+'\' readonly/>',
                                                   '<input type=\'text\' name=\'_codea[]\' value=\''+document.getElementById('_dscodeaG').value+'\' style=\'width:150px\' readonly />',
                                                   '<input type=\'text\' id=\'_seriea[]\' name=\'_seriea[]\' value=\''+this.value+'\' style=\'width:250px\' readonly />',
                                                   '<input type=\'text\' name=\'_qtya[]\' id=\'_qtya[]\' value=\'1\' style=\'width:50px\' readonly />',
                                                   '<input type=\'hidden\' name=\'_whscodel[]\' id=\'_whscodel[]\' value=\''+document.getElementById('_whscodeG').value+'\' /><img src=<? echo URLWEB;?>images/buttons/eliminar.png style=\'cursor:pointer; width:30px;\' title=Eliminar Registro OnClick=if(deleteTableRowSer(\'c_detailser\',this)){$(\'#_totalqty\').text(parseInt($(\'#_totalqty\').text())+1);changeImg('+document.getElementById('_lineaG').value+');} >'));
                                               this.value='';
                                               $('#_totalqty').text($('#_totalqty').text()-1);
                                               document.getElementById('_seriesok').value = 0;
                                               //document.getElementById('_idart_'+(parseInt(document.getElementById('_linenumaG').value)+
                                           }" />
                                </div>
                            </div>
                            <input type="text" class="form-control" aria-label="..." id="_iditemaG" name="_iditemaG"/>
                            <input type="text" class="form-control" aria-label="..." id="_linenumaG" name="_linenumaG"/>
                            <input type="text" id="_whscodeG" name="_whscodeG"/>
                            <!--input type="hidden" class="form-control" aria-label="..." id="_iditemaG" name="_iditemaG"/-->
                        </div><!-- panel-body-->
                        <?}?>
                        <div class="row">
                            <table id="c_detailser" border="1">
                                <thead>
                                <tr><th>Linea</th>
                                    <th>ID Item</th>
                                    <th>C&oacute;digo</th>
                                    <th>Serie</th>
                                    <th>Cantidad</th>
                                    <th>Opciones</th>
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
                                        <td>&nbsp;</td>
                                    </tr>
                                <? }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- panel-->
            </div><!-- series -->
        </div><!-- tab -->

        <!-- totales -->
        <!-- div class="panel panel-default">
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
                            <input class="form-control" type="textbox" id="subtot" name="subtot" value="< ? echo "$".number_format($subtotallineas,2);?>" readonly="readonly"/>
                        </div><!-- /input-group -- >
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
                          <input class="form-control" type="textbox" id="ivatot" name="ivatot" value="< ? echo "$".number_format((($subtotallineas - $sumdisc) * 0.16),2);?>" readonly="readonly"/>
                        </div><!-- /input-group -- >
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
						  < ? $subtotalCdesc = ($subtotallineas - $sumdisc);?>
                          <input class="form-control" type="textbox" id="total" name="total" value="< ? echo "$".number_format((($subtotalCdesc * 0.16) + $subtotalCdesc),2);?>" readonly="readonly"/>
                        </div><!-- /input-group -- >
                    </div>
                </div>
            </div>
        </div-->
        <!-- subtotales -->
            <div class="row">
                <? //if($ticket[0]->dsstatus == "1"){?>
                <div class="col-lg-6">
                    <div class="input-group">
                        <? if($permission['inventoryPurchaseForm.php']['create'] && $documentopen){?>
                        <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateFormPurchaseEntry()" >Guardar</button>
                        <? }?>
                    </div>
                </div>
                <? //}?>
                <div class="col-lg-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="if(confirm('Desea salir? se perderan los datos capturados')){location.href='inventory.php';}">Regresar</button>
                    </div>
                </div>
            </div>
    </div>
</div><!-- panel -->
<input type="hidden" name="idcreator" id="idcreator" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
<input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
<input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
<input type="hidden" id="_dstype" name="_dstype" value="S"/>
<input type="hidden" id="_pnidcliente" name="_pnidcliente"/>
<input type="hidden" name="_imgcount" id="_imgcount" value="<? echo count($ticket);?>"/>
<input type="hidden" name="_seriesok" id="_seriesok" value="0"/>

</form>

<br /><br /><br />
<script>
    $("#_totalqty").text(<? echo $totalqty;?>);

    function changeImg(id){
        console.log('function changeImg| id:'+id);
        $('#_idimg_'+id).attr('src','../images/buttons/advertencia.png');
        //$('#_idimg_'+linetocheck).attr('src', '../images/buttons/complete.png');
    }



</script>
