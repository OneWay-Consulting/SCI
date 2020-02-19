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


if($_POST['dispatch']=="detail")
    $ac=0;

if($_POST['dispatch']=="detail") {
    //$ticket = Returns::getRow($mysql, $_POST['id']);
    $listserial = Inventory::getSerialByItemAndWare($mysql,$_POST['id'],$_POST['fnidware']);

}

//print_r($ticket);
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

    <div class="panel panel-info">
        <div class="panel-heading"><h7><strong>Reporte IMEI <? echo $_POST['id'];?></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_cliente">&nbsp;&nbsp;Cod. Articulo:</label>
                          </span>
                            <input type="text" class="form-control" aria-label="..." name="_cliente" id="_cliente" value="<? echo $listserial[0]->dscode;?>"  readonly/>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_clientename">Nombre Articulo:</label>
                          </span>
                            <input type="text" class="form-control" aria-label="..." id="_clientename" name="_clientename" value="<? echo $listserial[0]->dsname;?>" readonly="readonly"/>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <label class="col-sm-2 control-label" for="_ref">Almacen:</label>
                          </span>
                            <input type="text" id="_ref" class="form-control" aria-label="..." id="_ref" name="_ref" value="<? echo $listserial[0]->warename;?>" <? {echo $ro;}?> readonly/>
                        </div><!-- /input-group -->
                    </div><!-- /.col-lg-6 -->
                </div>

                    <table id="c_detailser" border="1" width="50%">
                        <thead>
                            <th width="100px">#</th>
                            <th width="200px">IMEI</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? for($i=0; $i<count($listserial); $i++){?>
                            <tr>
                                <td width="100px"><? echo ($i+1);?></td>
                                <td width="200px"><? echo $listserial[$i]->fnidserial;?></td>
                                <!--td>&nbsp;</td-->
                            </tr>
                        <? }?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="if(confirm('¿Desea salir?')){location.href='stock.php';}">Regresar</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

        <input type="hidden" name="idcreator" id="idcreator" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
