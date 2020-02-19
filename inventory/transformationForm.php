<?php

require_once("../header.php");
_includes();
$mysql = new Mysql;
$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

$role = Role::getAll($mysql);
$ac=0;
$ro = "readonly=readonly";
$di = "disabled=disabled";

if($_POST['dispatch']=="query")
  $titulo = "Consulta";

  $ac=1;


if($_POST['dispatch']=="query")
  $ticket = Transformation::getRow($mysql, $_POST['id']);


 //print_r($ticket);
?>
<form id="requestform" name="requestform" data-toggle="validator" class="form-horizontal" method="post">
<div class="panel panel-info">
    <div class="panel-heading"><h7><strong><?=$titulo;?> COMPRA VENTA <? echo $_POST[id];?></strong></h7></div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
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
                            <label class="col-sm-2 control-label" for="_comentarios">Comentarios:</label>
                          </span>
                          <textarea class="form-control" rows="3" id="_comentarios" name="_comentarios" maxlength="200" <? if($ac){echo $ro;}?>><? echo $ticket[0]->dscomments;?></textarea>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                </div><!-- referencia comentario -->
            </div>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#articles">Captura Art&iacute;culos</a></li>
        </ul>
        <div class="tab-content">
            <div id="articles" class="tab-pane fade in active">
                <table id="c_detail" border="1">
                    <thead>
                    <tr><th>LINE ID</th>
                        <th>OC ORI</th>
                        <th>LINE ORI</th>
                        <th>ID ITEM</th>
                        <th>SKU ORI</th>
                        <th>IMEI</th>
                        <th>OC DEST</th>
                        <th>LINE DEST</th>
                        <th>ID ITEM Nuevo</th>
                        <th>SKU DEST</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="8">&nbsp;</td>
                    </tr>
                    <?
                    for($i=0; $i<count($ticket); $i++){
                        ?>
                        <tr id="<? echo $i;?>" >
                            <td><? echo $ticket[$i]->pnid;?></td>
                            <td><? echo $ticket[$i]->fnidocori;?></td>
                            <td><? echo $ticket[$i]->fnidlineori;?></td>
                            <td><? echo $ticket[$i]->fniditemori;?></td>
                            <td><? echo $ticket[$i]->dscodeori;?></td>
                            <td><? echo $ticket[$i]->dsserial;?></td>
                            <td><? echo $ticket[$i]->fnidocnew;?></td>
                            <td><? echo $ticket[$i]->fnidlinenew;?></td>
                            <td><? echo $ticket[$i]->fniditemnew;?></td>
                            <td><? echo $ticket[$i]->dscodedes;?></td>
                        </tr>
                    <?
                    } ?>
                    </tbody>
                </table>
            </div>
            <!-- end panel add items-->
        </div><!-- tab -->
        <!-- subtotales -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="if(confirm('Desea salir?')){location.href='transformation.php';}">Regresar</button>
                    </div>
                </div>
            </div>
    </div>
</div><!-- panel -->
<input type="hidden" name="idcreator" id="idcreator" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
<input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
<input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
</form>
