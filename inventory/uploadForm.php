<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 16/01/2019
 * Time: 11:40 AM
 */
require_once("../header.php");
_includes();
?>
<script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
_header();
$mysql = new Mysql;

$errort = 0;
if($_POST['dispatch']=="uploaded" && isset($_FILES["_file1"])){

    $ruta = "../uploads/";
    $archivo_nombre= $_POST['_filenamet']."_".$_FILES["_file1"]["name"];
    $archivo_peso= $_FILES["_file1"]["size"];
    $archivo_temporal= $_FILES["_file1"]["tmp_name"];
    $archivo_tipo = trim($_FILES["_file1"]["type"]);
    $_POST['ruta']=$ruta;
    $_POST['nombre']=$archivo_nombre;

    if($archivo_tipo == "text/plain" || $archivo_tipo == "application/vnd.ms-excel"){
        if (@copy($archivo_temporal, $ruta.$archivo_nombre)){
            $msg = "Archivo subido $archivo_temporal = $archivo_nombre<br>";
        }else{
            $msg = "Error al agregar archivo, consulte al administrador";
        }

        $msg = "Se agrego archivo exitosamente";
    }else{//check type file to upload
        $msg = "Error al agregar archivo, solo se perminten texto plano ";
    }//return mensaje error
}

//print_r($_POST);
?>
<? if($_POST['dispatch']=="uploaded") {?>
<form method="POST" id="uploadForm" name="uploadForm" action="inventoryquery.php" enctype="multipart/form-data">
    <?}else{?>
    <form method="POST" id="uploadForm" name="uploadForm" action="<? echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
        <? }?>
        <!-- COMPONENT START -->
        <div class="panel-heading"><h7><strong>Carga de ventas</strong></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="input-group input-file">
                                <span class="control-fileupload">
                                  <label for="file">Choose a file :</label>
                                  <input type="file" id="_file1" name="_file1">
                                </span>
                                    <span class="input-group-btn">
                                 <button class="btn btn-warning btn-success" type="button" onclick="validateUploadSales();">Procesar</button>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID Item</th>
                                <th>SKU</th>
                                <th>Descripci&oacute;n</th>
                                <th>No. Unidades</th>
                                <th>No. Pedido</th>
                                <th>Guia</th>
                                <th>Precio PL</th>
                                <th>Precio Doc</th>
                                <th>Canal</th>
                                <th>Cliente</th>
                                <th>ID Cliente</th>
                                <th>Fecha</th>
                                <th>Paqueteria</th>
                                <th>Comentarios</th>
                                <th>Comentarios envio</th>
                                <th>Revisi&oacute;n</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                            $linea = 0;
                            if(isset($_FILES['_file1'])) {
                                $archivo = fopen($ruta . $archivo_nombre, "r");
                                while (($datos = fgetcsv($archivo, ",")) == true) {
                                    if($count > 0){

                                        $item = Item::getRowByCode($mysql,$datos[0]);
                                        $cliente = Partner::getRowByCode($mysql, $datos[6]);

                                        $itemprice = Item::getAllByFilterBySN($mysql,$item[0]->dscode,$cliente[0]->pnid);
                                        //print_r($itemprice);
                                        $price = 0.0;
                                        if($itemprice[0]->pricewithdisc != "")
                                            $price = $itemprice[0]->pricewithdisc;

                                        $arrayl[$line]['price'] = $price;//$datos[4];

                                        if($price!=$datos[4])
                                            $msg = "Precio diferente";
                                        //echo "price doc:".$datos[4];
                                        if(!isset($cliente[0]) && !isset($item[0]))
                                            $msg = "No existen datos";
                                        ?>
                                        <tr class="<? if(isset($cliente[0]) && isset($item[0]) && ($price==$datos[4])){?>success<?}else{?>danger<?}?>">
                                            <td><? echo $item[0]->pnid; //iditem?></td>
                                            <td><? echo $datos[0]; //sku?></td>
                                            <td><? echo $item[0]->dsname; //nombre art?></td>
                                            <td><? echo $datos[1]; //unidades?></td>
                                            <td><? echo $datos[2]; ?></td>
                                            <td><? echo $datos[3]; ?></td>
                                            <?


                                            ?>
                                            <!--td>< ? echo $datos[4]; ?></td-->
                                            <td>$<? echo number_format($price,2,".",","); ?></td>
                                            <td>$<? echo number_format($datos[4],2,".",","); ?></td>
                                            <td><? echo $datos[5]; ?></td>
                                            <td><? echo $cliente[0]->pnid; //id cliente ?></td>
                                            <td><? echo $datos[6]; //cliente?></td>
                                            <td><? echo $datos[7]; ?></td>
                                            <td><? echo $datos[9]; ?></td>
                                            <td><? echo $datos[10]; ?></td>
                                            <td><? echo $datos[11]; ?></td>
                                            <td><? if(isset($cliente[0]) && isset($item[0]) && ($price==$datos[4])){echo "OK";}else{echo "Error: ".$msg; $errort=1;} ?></td>
                                        </tr>
                                        <?
                                    } //if count
                                    $count++;
                                }//while
                                fclose($archivo);
                            } //if isset _file1
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateUploadSales();" >Guardar</button>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="location.href='inventory.php'">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <? if($_POST['dispatch']==""){?>
            <input type="hidden" id="dispatch" name="dispatch" value="uploaded" />
        <? }else{?>
            <input type="hidden" id="dispatch" name="dispatch" value="tosave" />
        <? }?>
        <input type="hidden" id="iduser" name="iduser" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
        <input type="hidden" id="_filenamet" name="_filenamet" value="<? echo date("d_m_Y_H_i_s");?>" />
        <input type="hidden" id="_filenamefinal" name="_filenamefinal" value="<? if(isset($_FILES['_file1'])){ echo $archivo_nombre;} ?>" />
        <input type="hidden" id="_error" name="_error" value="<? echo $errort;?>" />

    </form>
    <script type="application/javascript">
        function validateUploadSales(){
            if($("#dispatch").val()=="uploaded"){
                if(document.getElementById('_file1').files[0] == ""){
                    alert("Debe seleccionar archivo a cargar");
                    return false;
                }
            }else if($("#dispatch").val()=="tosave"){
                if($("#_filenamefinal").val()==""){
                    alert("Debe seleccionar archivo para procesar");
                    return false
                }
                if($("#_error").val()==1){
                    alert("No se puede guardar los registros por que existe un error!")
                    return false;
                }
            }
            document.forms["uploadForm"].submit();
        }

    </script>