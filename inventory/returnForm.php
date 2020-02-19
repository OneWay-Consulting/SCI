<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 17/04/2019
 * Time: 05:43 PM
 */

require_once("../header.php");
_includes();
$mysql = new Mysql;

$ac=0;
$ro = "readonly=readonly";
$di = "disabled=disabled";


if($_POST['dispatch']=="addreturn" || $_POST['dispatch']=="update")
    $ac=0;
else
    $ac=1;

if($_POST['dispatch']=="update" || $_POST['dispatch']=="query") {
    $ticket = Returns::getRow($mysql, $_POST['id']);
    $listserial = Returns::getSerialByRow($mysql,$_POST['id']);
}

//print_r($ticket);

?>
<script src="../js/requestSoap.js"></script>
<script src="../js/return.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<form id="requestform" name="requestform" data-toggle="validator" class="form-horizontal" action="returnquery.php" method="post">
    <div class="panel panel-info">
        <div class="panel-heading"><h7><strong>Devoluciones <? echo $_POST['id'];?></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_cliente">&nbsp;&nbsp;Cod. Cliente:</label>
                          </span>
                            <input type="text" class="form-control" aria-label="..." name="_cliente" id="_cliente" value="<? echo $ticket[0]->dspartnercode;?>" <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?> onKeyUp="checkInputSN();"  onchange="setValuesSOAP();deleteTableAllRow();" required="required"/>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_clientename">Raz&oacute;n Social:</label>
                          </span>
                            <input type="text" class="form-control" aria-label="..." id="_clientename" name="_clientename" value="<? echo $ticket[0]->dsnamep;?>" readonly="readonly"  required="required"/>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_ref">Referencia:</label>
                          </span>
                            <input type="text" id="_ref" class="form-control" aria-label="..." id="_ref" name="_ref" value="<? echo $ticket[0]->dsreference;?>" <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?> required="required"/>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_ref">Comentarios:</label>
                          </span>
                            <input type="text" class="form-control" aria-label="..." id="_comments" name="_comments" value="<? echo $ticket[0]->dscomments;?>" <? if($_POST['dispatch']=="update" || $ac){echo $ro;}?> />
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->                </div>

                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" id="_liseries" href="#serie">Art&iacute;culos</a></li>
                    <!--li><a data-toggle="tab" id="_tabguia" href="#serie">Gu&iacute;as</a></li-->
                </ul>
                <div class="tab-content">
                    <!-- articulos-->

                    <div id="serie" class="tab-pane fade in active">
                    <div class="panel panel-default">
                        <div class="panel-body">
                                <? if($_POST['dispatch']=="addreturn"){?>
                                <div class="row">

                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_dscodeaG">SKU:</label>
                                  </span>
                                            <input type="text" class="form-control" aria-label="..." id="_dscodeaG" name="_dscodeaG" value="" />
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
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_lineaG">No. Pedido:</label>
                                  </span>
                                            <input type="text" class="form-control" aria-label="..." id="_reflineG" name="_reflineG" value=""/>
                                            <input type="hidden" id="_idlineG" name="_idlineG" value="" />
                                            <input type="hidden" id="_idheaderG" name="_idheaderG" value="" />
                                            <input type="hidden" id="_fniditemG" name="_fniditemG" value="" />
                                            <input type="hidden" id="_fnidwareG" name="_fnidwareG" value="" />
                                        </div>



                                    </div>
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="_fecha">Lote/Serie:</label>
                                  </span>
                                            <input type="text" class="form-control" aria-label="..." id="_serieaG" name="_serieaG"
                                                   onkeypress="if(enterpressalertReturn(event)){if(true){addTableRow('c_detailser',
                                                           Array('<input type=\'text\' id=\'_codea[]\' name=\'_codea[]\' value=\''+document.getElementById('_dscodeaG').value+'\' style=\'width:100px\' readonly />',
                                                           '<input type=\'text\' name=\'_refline[]\' id=\'_refline[]\' value=\''+document.getElementById('_reflineG').value+'\' style=\'width:150px\' readonly />',
                                                           '<input type=\'text\' name=\'_seriea[]\' id=\'_seriea[]\'  value=\''+document.getElementById('_serieaG').value+'\' style=\'width:250px\' readonly />',
                                                           '<input type=\'text\' name=\'_qtya[]\' id=\'_qtya[]\' value=\'1\' style=\'width:50px\' readonly />',
                                                           '<input type=\'text\' name=\'_idline[]\' id=\'_idline[]\' value=\''+document.getElementById('_idlineG').value+'\' style=\'width:50px\' readonly />',
                                                           '<input type=\'text\' name=\'_idheader[]\' id=\'_idheader[]\' value=\''+document.getElementById('_idheaderG').value+'\' style=\'width:50px\' readonly />',
                                                           '<input type=\'text\' name=\'_fniditem[]\' id=\'_fniditem[]\' value=\''+document.getElementById('_fniditemG').value+'\' style=\'width:50px\' readonly />',
                                                           '<input type=\'text\' name=\'_fnidware[]\' id=\'_fnidware[]\' value=\''+document.getElementById('_fnidwareG').value+'\' style=\'width:50px\' readonly />',
                                                           '<img src=<? echo URLWEB;?>images/buttons/eliminar.png style=\'cursor:pointer; width:30px;\' title=\'Eliminar Registro\' OnClick=deleteTableRowSer(\'c_detailser\',this);>')
                                                           );
                                                           this.value='';
                                                           document.getElementById('_seriesok').value = 0;
                                                           $('#_reflineG').val('');
                                                           $('#_dscodeaG').val('');
                                                           $('#_dscodeaG').focus();
                                                           $('#desc').val('');
                                                           $('#manser').val('');
                                                           }
                                                           }else{console.log('no entro');}" />
                                        </div>
                                    </div>
                                    <input type="hidden" class="form-control" aria-label="..." id="_iditemaG" name="_iditemaG"/>
                                    <input type="hidden" class="form-control" aria-label="..." id="_linenumaG" name="_linenumaG"/>
                                </div><!-- panel-body-->
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <label class="col-sm-2 control-label" for="desc">Maneja Series:</label>
                                  </span>
                                                    <input type="text" class="form-control" id="manser" name="manser" readonly/>
                                                </div><!-- /input-group -->
                                            </div>
                                        </div>
                                    </div>
                            <? }?>

                            <div class="row">
                                <table id="c_detailser" border="1">
                                    <thead>
                                        <th>SKU</th>
                                        <th>Pedido</th>
                                        <th>Serie</th>
                                        <th>Cantidad</th>
                                        <th>ID Linea</th>
                                        <th>ID Header</th>
                                        <th>ID Item</th>
                                        <th>Opciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <? for($i=0; $i<count($listserial); $i++){?>
                                        <tr>
                                            <td><? echo $listserial[$i]->itemcode;?></td>
                                            <td><? echo $listserial[$i]->dsrefline;?></td>
                                            <td><? echo $listserial[$i]->serial;?></td>
                                            <td><? echo $listserial[$i]->quantity;?></td>
                                            <td><? echo $listserial[$i]->idlinea;?></td>
                                            <td><? echo $listserial[$i]->fnidheader;?></td>
                                            <td><? echo $listserial[$i]->iditem;?></td>
                                            <td><? echo $listserial[$i]->itemname;?></td>
                                            <!--td>&nbsp;</td-->
                                        </tr>
                                    <? }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- panel-->
                </div>
                    <!-- articulos -->
                </div>
                <div class="row">
                    <? if($_POST['dispatch']=="addreturn"){?>
                    <div class="col-lg-6">
                        <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateFormReturn()" >Guardar</button>
                    </div>
                    <? }?>
                    <div class="col-lg-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="if(confirm('¿Desea salir? Se perderan datos capturados!')){location.href='return.php';}">Regresar</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <input type="hidden" name="idcreator" id="idcreator" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
    <input type="hidden" id="_dstype" name="_dstype" value="C"/>
    <input type="hidden" id="_pnidcliente" name="_pnidcliente"/>
    <input type="hidden" id="articulo" name="articulo"/>
    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch'];?>" />
    <input type="hidden" name="_seriesok" id="_seriesok" value="0"/>
</form>
<script>
    $("#_dscodeaG").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);

        if(code==13){
            console.log('se genera la lectura');
            var _codetosearch = $(this).val();
            var _manser = true;

            console.log('_codetosearch:' + _codetosearch);

            setValuesToReadSeriesReturn(_codetosearch);

        }//if == 13
    });
</script>
