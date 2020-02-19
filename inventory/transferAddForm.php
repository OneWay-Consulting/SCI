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

//print_r($_POST);

if($_POST['dispatch']=="update"){
    $titulo = "Actualizaci&oacute;n";
}elseif($_POST['dispatch']=="query"){
    $titulo = "Consulta";
}else
    $titulo = "Crear";

if($_POST['dispatch']=="add")
    $ac=0;
else
    $ac=1;

if($_POST['dispatch']=="entry" || $_POST['dispatch']=="query"){
    $ticket = Transfer::getRow($mysql, $_POST['id']);
    $listserial = Transfer::getSerialByRow($mysql,$_POST[id]);
}

if($_SESSION['user']->getUser()->fnidrole==7)
    $listware = Item::getSpecificWareHouseBy($mysql);
else
    $listware = Item::getWareHouse($mysql);

//print_r($listserial);
?>
<!-- CODE TO MANAGER GRID -->
<!--script src="../js/requestSoap.js"></script-->

<script src="../js/transfer.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- END CODE TO MANAGER GRID -->
<form id="requestform" name="requestform" data-toggle="validator" class="form-horizontal" action="transferquery.php" method="post">
    <div class="panel panel-info">
        <div class="panel-heading"><h7><strong><?=$titulo;?> Transferencia <?echo $_POST['id'];?></strong></h7></div>
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
                                    <? if($ticket[0]->dsstatus == 1){?>
                                        <option value="1" >CREADO</option>
                                        <option value="2" selected>RECIBIDO</option>
                                    <? }elseif($ticket[0]->dsstatus == 2){?>
                                        <option value="2" selected>RECIBIDO</option>
                                    <? }else{?>
                                        <option value="1" selected>CREADO</option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_fecha">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." id="_fecha" name="_fecha" value="<? if($_POST['dispatch']=="update" || $ac){echo $ticket[0]->ddate;}else{echo date("Y-m-d H:i:s");}?>" readonly/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
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
                                <textarea class="form-control" rows="3" id="_comentarios" name="_comentarios" maxlength="200" <? if($_POST['dispatch']=="entry"){echo $di;}?>><? echo $ticket[0]->dscomments;?></textarea>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                    </div><!-- referencia comentario -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_whsfromg">Origen:</label>
                          </span>
                                <select id="_whsfromg" name="_whsfromg" class="form-control" required="required" <? if($ac){echo $di;}?>>
                                        <option value="0" selected>--Origen--</option>
                                        <? for($i=0; $i<count($listware); $i++){?>
                                            <option value="<? echo $listware[$i]->pnid;?>" <? if($listware[$i]->pnid==$ticket[0]->fnidfromware){echo "selected";}?> ><? echo $listware[$i]->dsname;?></option>
                                        <? }?>
                                </select>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_whstog">Destino:</label>
                          </span>
                                <select id="_whstog" name="_whstog" class="form-control" required="required" <? if($ac){echo $di;}?>>
                                        <option value="0" selected>--Destino--</option>
                                        <? for($i=0; $i<count($listware); $i++){?>
                                            <option value="<? echo $listware[$i]->pnid;?>" <? if($listware[$i]->pnid==$ticket[0]->fnidtoware){echo "selected";}?>><? echo $listware[$i]->dsname;?></option>
                                        <? }?>
                                </select>
                            </div><!-- /input-group -->
                        </div><!-- /.col-lg-6 -->
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#articles">Listado Art&iacute;culos</a></li>
                <li><a data-toggle="tab" id="_liseries" href="#serie">Series</a></li>
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
                                    <label class="col-sm-2 control-label" for="itemcode">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Art&iacute;culo:</label>
                                  </span>
                                            <input type="text" class="form-control" name="itemcode" id="itemcode" onKeyUp="checkInputTrans();" onchange="setValuesSOAPTrans();"  />
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="itemname">Descripci&oacute;n:</label>
                                  </span>
                                            <input type="text" class="form-control" id="itemname" name="itemname" readonly/>
                                        </div><!-- /input-group -->
                                    </div>
                                </div><!-- item descripcion-->
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
                                    <label class="col-sm-2 control-label" for="stock">Stock:</label>
                                  </span>
                                            <input type="text"class="form-control"  id="stock" name="stock" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <button type="button" class="btn btn-info" onclick="if( validateToAddTransfer()){addTableRow('c_detail',Array('','<input type=hidden id=\'_nl[]\' name=\'_nl[]\' value=\''+document.getElementById('c_detail').rows.length+'\' /><input type=hidden id=\'_iditem[]\' name=\'_iditem[]\' value=\''+document.getElementById('_idarticle').value+'\' /><input type=\'text\' name=\'_art[]\' value=\''+document.getElementById('itemcode').value+'\' style=\'width:100px\' readonly />','<input type=\'text\' name=\'_desc[]\' id=\'_desc[]\' value=\''+document.getElementById('itemname').value+'\' style=\'width:250px\' readonly />',
                                                    '<input type=\'hidden\' id=\'_whscode[]\' name=\'_whscode[]\' value=\''+document.getElementById('_whsfromg').value+'\'  /><input type=\'text\' name=\'_qty[]\' id=\'_qty[]\' value=\''+document.getElementById('qty').value+'\' style=\'width:60px\' />',
                                                    '<input type=\'hidden\' id=\'_whscodeto[]\' name=\'_whscodeto[]\' value=\''+document.getElementById('_whstog').value+'\'  /><input type=\'text\' name=\'_qtyo[]\' id=\'_qtyo[]\' value=\''+document.getElementById('qty').value+'\' style=\'width:60px\' readonly />',
                                                    '<input type=\'text\' name=\'_qtyc[]\' id=\'_qtyc[]\' value=\'0\' style=\'width:60px\' />',
                                                    '<input type=\'text\' name=\'_stock[]\' id=\'_stock[]\' value=\''+document.getElementById('stock').value+'\' style=\'width:60px\' readonly />',
                                                    '<img src=<? echo URLWEB;?>images/buttons/eliminar.png style=\'cursor:pointer; width:30px;\' title=Eliminar Registro OnClick=deleteTableRow(\'c_detail\',\''+document.getElementById('c_detail').rows.length+'\');><input type=\'hidden\' id=\'_idline[]\' name=\'_idline[]\' /> '));cleanValuesTrans();}else{alert('Debe ingresar un articulo valido!');}hde(document.getElementById('c_detail'));">Agregar art&iacute;culo</button>
                                            <input type="hidden" class="form-control" name="precio" id="precio"/>
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
                            <? if($_POST['dispatch']=="add"){?>
                                <th>Disp.</th>
                            <? }?>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <? if($_POST['dispatch']=="add"){?>
                                <td colspan="8">&nbsp;</td>
                            <? }else{?>
                                <td colspan="7">&nbsp;</td>
                            <? }?>
                        </tr>
                        <?
                        $complete = true;
                        for($i=0; $i<count($ticket); $i++){
                            ?>
                            <tr id="<? echo $i;?>" >
                                <td><? echo $ticket[$i]->pnidline;?></td>
                                <td><input type='text' name='_art[]' id="_art[]" value='<? echo $ticket[$i]->dsitemcode;?>' style='width:100px' <? echo $ro;?>/></td>
                                <td><input type='text' name='_desc[]' id='_desc[]' value='<? echo $ticket[$i]->dsitemname;?>' style='width:500px' <? echo $ro;?> /></td>
                                <td style="background-color: #ff851b"><input type='text' name='_qty[]' id='_qty[]' value='<? echo $ticket[$i]->dnquantity;?>' style='width:60px' <? echo $ro;?> /></td>
                                <td style="background-color: #ff851b"><input type='text' name='_qtyo[]' id='_qtyo[]' value='<? echo(($ticket[$i]->dnopenqty <= 0 || $ticket[$i]->dnopenqty=="")?0:$ticket[$i]->dnopenqty );?>' style='width:60px' <? echo $ro;?> /></td>
                                <td style="background-color: #4f8a10"><input type='text' name='_qtyr[]' id='_qtyr[]' value='<? echo(($ticket[$i]->dnopenqty <= 0 || $ticket[$i]->dnopenqty=="")?0:$ticket[$i]->dnopenqty );?>' style='width:60px' <? if($ticket[$i]->dnopenqty <= 0){ echo $ro;}else{$complete=false;}?>/></td>
                                <td><input type='hidden' name='_iditem[]' id='_iditem[]'  value='<? echo ($ticket[$i]->fniditem);?>' />
                                    <input type='hidden' id="_idline[]" name="_idline[]" value="<? echo $ticket[$i]->pnidline;?>" />
                                    <input type='hidden' id="_upc[]" name="_upc[]" value="<? echo $ticket[$i]->dsupc;?>" />
                                    <input type='hidden' id="_upc2[]" name="_upc2[]" value="<? echo trim($ticket[$i]->dsupc2);?>" />
                                    <input type='hidden' id="_to[]" name="_to[]" value="<? echo trim($ticket[$i]->fnidto);?>" />
                                    <input type='hidden' id="_from[]" name="_from[]" value="<? echo trim($ticket[$i]->fnidfrom);?>" />
                                    <? if($_POST['dispatch']=="entry"){?>
                                        <img src="<? echo URLWEB;?>images/buttons/serie.png" id="_idart_<? echo $ticket[$i]->pnidline;?>" name="_idart_<? echo $ticket[$i]->pnidline;?>" style='cursor:pointer; width:30px;' title="Asignar series" OnClick="setValuesToReadSeriesTransf('<? echo $ticket[$i]->pnidline;?>','<? echo $ticket[$i]->fniditem;?>','<? echo $ticket[$i]->dsitemcode;?>',<? echo $ticket[$i]->dnquantity?>,<? echo $i?>,'<? echo $ticket[$i]->dsitemname;?>','<? echo $ticket[$i]->fnidfrom;?>',<? echo ($i+1);?>,<? echo $ticket[$i]->fnidto;?>);  document.getElementById('_liseries').click();">
                                        <img id="_idimg_<? echo $ticket[$i]->pnidline;?>" src="<? echo URLWEB;?>images/buttons/advertencia.png" style='cursor:pointer; width:25px;' title="Falta" />
                                    <? }?></td>
                                <input type="hidden" id="ok_<? echo $ticket[$i]->pnidline;?>" value="0" />
                                <input type="hidden" id="manser_<? echo $ticket[$i]->pnidline;?>" value="<? echo $ticket[$i]->manser;?>" />
                                </td>
                            </tr>
                        <? if($ticket[$i]->dnopenqty > 0)
                                $documentopen = true;
                        } ?>
                        </tbody>
                    </table>

                </div><!-- articles-->
                <div id="serie" class="tab-pane fade"><!-- serie-->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <? if($documentopen){?>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_fecha">Art&iacute;culo:</label>
                                  </span>
                                            <input type="text" class="form-control" aria-label="..." id="_dscodeaG" name="_dscodeaG" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_fecha">Serie:</label>
                                  </span>
                                            <input type="text" class="form-control" aria-label="..." id="_serieaG" name="_serieaG"
                                                   onkeypress="if(enterpressalert(event) && validateToAddTransferSerial()){
                                                           if(document.getElementById('_seriesok').value == 1){
                                                            addTableRow('c_detailser',
                                                           Array('<input type=text id=\'_linea[]\' name=\'_linea[]\' style=\'width:50px\' value=\''+document.getElementById('_lineaG').value+'\' readonly/>',
                                                           '<input type=text id=\'_idarticlea[]\' name=\'_idarticlea[]\'  value=\''+document.getElementById('_iditemaG').value+'\' readonly/>',
                                                           '<input type=\'text\' name=\'_codea[]\' value=\''+document.getElementById('_dscodeaG').value+'\' style=\'width:150px\' readonly />',
                                                           '<input type=\'text\' id=\'_seriea[]\' name=\'_seriea[]\' value=\''+this.value+'\' style=\'width:250px\' readonly />',
                                                           '<input type=\'text\' name=\'_qtya[]\' id=\'_qtya[]\' value=\'1\' style=\'width:50px\' readonly />',
                                                           '<input type=\'hidden\' name=\'_whscodetol[]\' id=\'_whscodetol[]\' value=\''+document.getElementById('_whscodetoG').value+'\' /><input type=\'hidden\' name=\'_whscodel[]\' id=\'_whscodel[]\' value=\''+document.getElementById('_whscodeG').value+'\' /><img src=<? echo URLWEB;?>images/buttons/eliminar.png style=\'cursor:pointer; width:30px;\' title=Eliminar Registro OnClick=if(deleteTableRowSer(\'c_detailser\',this)){$(\'#_totalqty\').text(parseInt($(\'#_totalqty\').text())+1);changeImg('+document.getElementById('_lineaG').value+');$(\'#ok_'+document.getElementById('_lineaG').value+'\').val(0)} >'));
                                                           this.value='';
                                                           $('#_dscodeaG').val('');
                                                           $('#_lineaG').val('');
                                                           $('#_dsnameG').val('');
                                                           $('#_qtyaG').val('');
                                                           $('#_whscodeG').val();
                                                           $('#_totalqty').text($('#_totalqty').text()-1);
                                                           $('#_dscodeaG').focus();
                                                           document.getElementById('_seriesok').value = 0;
                                                           }}" />
                                        </div>
                                    </div>
                                </div>
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
                                    <input type="hidden" class="form-control" aria-label="..." id="_iditemaG" name="_iditemaG"/>
                                    <input type="hidden" class="form-control" aria-label="..." id="_linenumaG" name="_linenumaG"/>
                                    <input type="hidden" id="_whscodeG" name="_whscodeG"/>
                                    <input type="hidden" id="_whscodetoG" name="_whscodetoG"/>
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

            <div class="row">
                <? if($ticket[0]->dsstatush != 5){?>
                    <? //if(!$complete || $_POST['dispatch']=="add"){?>
                <div class="col-lg-6">
                    <div class="input-group">
                        <? if($permission['transfer.php']['create']){?>
                            <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateFormTransfer(event)" >Guardar</button>
                            <? }?>
                    </div>
                </div>
                        <? //}?>
                <? }?>
                <div class="col-lg-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="location.href='transfer.php'">Regresar</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- panel -->
    <input type="hidden" name="idcreator" id="idcreator" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
    <input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
    <input type="hidden" id="_dstype" name="_dstype" value="C"/>
    <input type="hidden" id="_pnidcliente" name="_pnidcliente" value="10"/>
    <input type="hidden" name="_seriesok" id="_seriesok" value="0"/>
</form>

<br /><br /><br />
<script>

    $("#_dscodeaG").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);

        if(code==13){
            console.log('se genera la lectura');
            var _codetosearch = $(this).val();
            var _lineinturn = '';
            var _existbar = false;
            var i =0 ;
            var _upc2 = '';


            console.log('_codetosearch:' + _codetosearch);

            $('input[name="_upc[]"]').each(function() {

                if($('input[name="_art[]"]').length > 1) {
                    _lineinturn = document.forms["requestform"].elements["_idline[]"][i].value;
                    _upc2 = document.forms["requestform"].elements["_upc2[]"][i].value;

                }
                else {
                    _lineinturn = document.forms["requestform"].elements["_idline[]"].value;
                    _upc2 = document.forms["requestform"].elements["_upc2[]"].value;
                }

                //console.log('_lineinturn:' + _lineinturn + ' en i:'+i);
                //console.log('this val _upc[]:' + $(this).val());

                if(_codetosearch == $(this).val()){
                    //console.log("ok_lineinturn: "+document.getElementById('ok_'+_lineinturn).value);
                    if(document.getElementById('ok_'+_lineinturn).value == 0){
                        $('#_idart_'+_lineinturn).click();
                        console.log('Termina en i:'+i);
                        _existbar = true;
                        return false;
                    }
                }//if _codetosearch
                else if(_upc2 != ""){
                    if(_codetosearch == _upc2){
                        //console.log("ok_lineinturn: "+document.getElementById('ok_'+_lineinturn).value);
                        if(document.getElementById('ok_'+_lineinturn).value == 0){
                            $('#_idart_'+_lineinturn).click();
                            console.log('Termina en i:'+i);
                            _existbar = true;
                            return false;
                        }
                    }//if _codetosearch upc2
                }

                i++;
            });

            if(!_existbar){
                alert('No existe articulo '+$(this).val()+' pendiente en OC');
                return false;
            }
        }//if == 13
    });

    $("#_totalqty").text(<? echo $totalqty;?>);

    function changeImg(id){
        console.log('function changeImg| id:'+id);
        $('#_idimg_'+id).attr('src','../images/buttons/advertencia.png');
        //$('#_idimg_'+linetocheck).attr('src', '../images/buttons/complete.png');
    }

</script>
