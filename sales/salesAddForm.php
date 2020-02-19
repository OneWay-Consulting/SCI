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
$role = Role::getAll($mysql);
$ac=0;
$ro = "readonly=readonly";
$di = "disabled=disabled";


$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

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
    $ticket = Sales::getRow($mysql, $_POST['id']);
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
<form id="requestform" name="requestform" data-toggle="validator" class="form-horizontal" action="salesquery.php" method="post">
    <div class="panel panel-info">
        <div class="panel-heading"><h7><strong><?=$titulo;?> Venta <? echo $ticket[0]->$_POST['id'];?></strong></h7></div>
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
                                    <? if($ticket[0]->dsstatush == 1){?>
                                        <option value="1" selected>CREADO</option>
                                        <option value="6">CANCELADO</option>
                                    <? }elseif($ticket[0]->dsstatush == 3){?>
                                    <option value="3" selected>ENTREGADO</option>
                                    <? }elseif($ticket[0]->dsstatush == 6){?>
                                        <option value="6" selected>CANCELADO</option>
                                    <? }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_fecha">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." id="_fecha" name="_fecha" value="<? if($_POST['dispatch']=="update" || $ac){echo $ticket[0]->dddocdate;}else{echo date("Y-m-d H:i:s");}?>" <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?>/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_cliente">&nbsp;&nbsp;Cod. Cliente:</label>
                          </span>
                                <input type="text" class="form-control" aria-label="..." name="_cliente" id="_cliente" value="<? echo $ticket[0]->dscode;?>" <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?> onKeyUp="checkInputSN();"  onchange="setValuesSOAP();deleteTableAllRow();" required="required"/>
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
                                <input type="text" class="form-control" id="_referencia" name="_referencia" value="<? echo $ticket[0]->dsreference;?>"  required="required" <? if($_POST['dispatch']=="update" || $ac){echo $di;}?>/>
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
                <li class="active"><a data-toggle="tab" href="#articles">Captura Art&iacute;culos</a></li>
                <!--li><a data-toggle="tab" href="#serie">Series</a></li-->
            </ul>
            <div class="tab-content">
                <div id="articles" class="tab-pane fade in active">
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
                                        <!--span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="qty">Serie:</label>
                                  </span>
                                    <input type="text" class="form-control" name="auxser" id="auxser" onkeypress="if(enterpressalert(event) && valideateToAdd()){addTableRow('c_detailser',Array('<input type=text id=\'_linea[]\' name=\'_linea[]\' style=\'width:50px\' value=\''+document.getElementById('c_detail').rows.length+'\' />','<input type=text id=\'_idarticlea[]\' name=\'_idarticlea[]\'  value=\''+document.getElementById('_idarticle').value+'\' readonly/>','<input type=\'text\' name=\'_codea[]\' value=\''+document.getElementById('articulo').value+'\' style=\'width:150px\' readonly />','<input type=\'text\' name=\'_seriea[]\' value=\''+this.value+'\' style=\'width:250px\' readonly />','<input type=\'text\' name=\'_qtya[]\' id=\'_qtya[]\' value=\'1\' style=\'width:50px\' readonly />','<img src=<? echo URLWEB;?>images/buttons/eliminar.png style=\'cursor:pointer; width:30px;\' title=Eliminar Registro OnClick=deleteTableRow(\'c_detailser\',\''+document.getElementById('c_detailser').rows.length+'\');>'));this.value=''}" /-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-info" onclick="if( valideateToAdd()){addTableRow('c_detail',Array('<input type=hidden id=\'_nl[]\' name=\'_nl[]\' value=\''+document.getElementById('c_detail').rows.length+'\' /><input type=hidden id=\'_iditem[]\' name=\'_iditem[]\' value=\''+document.getElementById('_idarticle').value+'\' /><input type=\'text\' name=\'_art[]\' value=\''+document.getElementById('articulo').value+'\' style=\'width:100px\' readonly />','<input type=\'text\' name=\'_desc[]\' id=\'_desc[]\' value=\''+document.getElementById('desc').value+'\' style=\'width:250px\' readonly />','<input type=\'text\' value=\''+document.getElementById('_ware').options[document.getElementById('_ware').selectedIndex].text+'\' /><input type=\'hidden\' id=\'_whscode[]\' name=\'_whscode[]\' value=\''+document.getElementById('_ware').value+'\'  />','<input type=\'text\' name=\'_qty[]\' id=\'_qty[]\' value=\''+document.getElementById('qty').value+'\' style=\'width:60px\' onChange=\'calculateSub();\' />','<input type=\'text\' name=\'_price[]\' id=\'_price[]\' value=\''+document.getElementById('precio').value+'\' style=\'width:100px\' readonly />','<input type=\'text\' name=\'_iva[]\' id=\'_iva[]\' value=\''+document.getElementById('iva').value+'\' style=\'width:50px\' onChange=\'calculateSub();\' />','<img src=<? echo URLWEB;?>images/buttons/eliminar.png style=\'cursor:pointer; width:30px;\' title=Eliminar Registro OnClick=deleteTableRow(\'c_detail\',\''+document.getElementById('c_detail').rows.length+'\');><input type=\'hidden\' id=\'_idline[]\' name=\'_idline[]\' /><input type=\'hidden\' name=\'_sub[]\' id=\'_sub[]\' value=\''+(document.getElementById('precio').value)+'\' />'));cleanValues();calculateSub();}else{alert('Debe ingresar un articulo valido!');}hde(document.getElementById('c_detail'));">Agregar art&iacute;culo</button>
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
                        <tr><th>SKU</th>
                            <th>Descripci&oacuten</th>
                            <th>Pedido</th>
                            <!--th>&Aacute;lmacen</th-->
                            <th>Cantidad</th>
                            <th>Precio Uni $</th>
                            <th>IVA</th>
                            <? //if($ticket[0]->dsstatush==3){?>
                                <th>Paqueteria</th>
                                <!--th>Estaus linea</th-->
                                <th>Comentario</th>
                            <? //}?>
                            <? if($ticket[0]->dsstatush!=3){?>
                                <th>Opciones</th>
                                <th><label for="cbox2">Sel</label><input type="checkbox" id="select_all" onchange="selectAll(this.checked);" /></th>
                            <? }?>

                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <? if($ticket[0]->dsstatush==3){?>
                                <td colspan="9">&nbsp;</td>
                            <? }else{ ?>
                                <td colspan="9">&nbsp;</td>
                            <? }?>
                        </tr>
                        <?
                        for($i=0; $i<count($ticket); $i++){
                            $idfull .= $ticket[$i]->idline."&";
                            $discbyprice = (($ticket[$i]->ddprice * $ticket[$i]->dddiscount)/100);
                            $pricewithdisc = $ticket[$i]->ddprice - $discbyprice;
                            $subtotallineas += ($ticket[$i]->ddquantity * $ticket[$i]->ddprice);
                            $rowdisc = ($ticket[$i]->ddquantity * $discbyprice);
                            $sumdisc += $rowdisc;
                            ?>
                            <tr id="<? echo $i;?>" >
                                <td><input type='text' name='_art[]' id="_art[]" value='<? echo $ticket[$i]->dsitemcode;?>' style='width:100px' <? echo $ro;?>/></td>
                                <td><input type='text' name='_desc[]' id='_desc[]' value='<? echo $ticket[$i]->dsitemname;?>' style='width:250px' <? echo $ro;?> /></td>
                                <td><input type='text' name='_refline[]' id='_refline[]'  value='<? echo $ticket[$i]->dsrefline;?>' style='width:90px' <? echo $ro;?>/></td>
                                <!--td><input type='text' name='_whscode[]' id='_whscode[]' value='< ? echo $ticket[$i]->fnidware;?>' style='width:250px' < ? echo $ro;?> /></td-->
                                <td><input type='text' name='_qty[]' id='_qty[]' onChange="calculateSub();"  value='<? echo $ticket[$i]->dnquantity;?>' style='width:100px' <? echo $ro;?> /></td>
                                <td><input type='text' name='_price[]' id='_price[]'  value='<? echo $ticket[$i]->dnprice;?>' style='width:100px' <? echo $ro;?>/></td>
                                <td><input type='text' name='_iva[]' id='_iva[]'  value='<? echo $ticket[$i]->dniva;?>' style='width:100px' <? echo $ro;?>/></td>
                                <? //if($ticket[0]->dsstatush==3){ ?>
                                    <td><input type='text' name='_paq[]' id="_paq[]" value='<? echo $ticket[$i]->dspaqueteria;?>' style='width:100px' /></td>
                                    <!--td><input type='text' name='_statusl[]' id='_statusl[]' value='< ? echo $ticket[$i]->dsstatusl;?>' style='width:100px'  /></td-->
                                    <td><input type='text' name='_coml[]' id='_coml[]' value='<? echo $ticket[$i]->dscomentariol;?>' style='width:100px' /></td>
                                <? //} ?>
                                <td><input type='hidden' name='_whscode[]' id='_whscode[]' value='<? echo $ticket[$i]->fnidware;?>'/>
                                    <input type='hidden' name='_iditem[]' id='_iditem[]'  value='<? echo ($ticket[$i]->fniditem);?>' />
                                    <input type='hidden' id="_idline[]" name="_idline[]" value="<? echo $ticket[$i]->pnidline;?>" />
                                    <input type='hidden' id="_upc[]" name="_upc[]" value="<? echo $ticket[$i]->dsupc;?>" />
                                    <? if($_POST['dispatch']=="entry"){?>
                                        <img src="<? echo URLWEB;?>images/buttons/serie.png" style='cursor:pointer; width:30px;' title="Asignar series" OnClick="setValuesToReadSeries('<? echo $ticket[$i]->idline;?>','<? echo $ticket[$i]->fniditem;?>','<? echo $ticket[$i]->dsitemcode;?>',<? echo $ticket[$i]->dnquantity?>);">
                                    <? }?>
                                    <select id="_statuspart[]" name="_statuspart[]" style='width:90px'>
                                        <? if($ticket[$i]->dsstatusl!="CANCEL"){?>
                                            <option value=""></option>
                                        <? }?>
                                        <option value="CANCEL">CANCELAR</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="checkbox" id="_aut_<? echo $ticket[$i]->pnidline;?>" name="_aut_<? echo $ticket[$i]->pnidline;?>" value="<? echo $ticket[$i]->pnidline;?>" />
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>

                </div><!-- articles-->
                <div id="serie" class="tab-pane fade"><!-- serie-->
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <table id="c_detailser" border="1">
                                <thead>
                                <tr><th>Linea</th>
                                    <th>ID Item</th>
                                    <th>SKU</th>
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
                                        <td></td>
                                    </tr>
                                <? }?>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- panel-->
                </div><!-- serie-->
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
                                <input class="form-control" type="textbox" id="ivatot" name="ivatot" value="<? echo "$".number_format((($subtotallineas - $sumdisc) * 0.16),2);?>" readonly="readonly"/>
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
                                <input class="form-control" type="textbox" id="total" name="total" value="<? echo "$".number_format((($subtotalCdesc * 0.16) + $subtotalCdesc),2);?>" readonly="readonly"/>
                            </div><!-- /input-group -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- subtotales -->
            <? //echo print_r($permission);?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="input-group">
                        <? if($permission['quote.php']['create']){
                            if($ticket[0]->dsstatush!=6){?>
                        <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateFormSales()" >Guardar</button>
                        <? }}?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="location.href='sales.php'">Cancelar</button>
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

        var checked_status = valor;
        $("input[name^='_aut_']").each(function(){
            this.checked = checked_status;
        });

    }//function
</script>