<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 28/11/2018
 * Time: 08:47 AM
 */
	require_once("../header.php");
	_includes();
?>
    <script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
	_header();
$mysql = new Mysql;

$errort = 0;
if($_POST['dispatch']=="uploadedtransformation" && isset($_FILES["_file1"])){

    $ruta = "../uploads/logTransformation/";
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
<? if($_POST['dispatch']=="uploadedtransformation") {?>
    <form method="POST" id="uploadForm" name="uploadForm" action="transformationquery.php" enctype="multipart/form-data">
<?}else{?>
    <form method="POST" id="uploadForm" name="uploadForm" action="<? echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<? }?>
    <!-- COMPONENT START -->
    <div class="panel-heading"><h7><strong>Carga de SKU a convertir</strong></h7></div>
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
                                 <button class="btn btn-warning btn-success" type="button" onclick="validateUploadTransformation();">Procesar</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-striped">
                        <thead>
                        <tr>
													  <th>Validaci&oacute;n</th>
 													  <th>OC ORI</th>
                            <th>LINE ORI</th>
														<th>ITEM ID ORI</th>
														<th>SKU ORI</th>
                            <th>IMEI</th>
                            <th>OC DEST</th>
                            <th>LINE DEST</th>
														<th>ITEM ID DET</th>
                            <th>SKU DEST</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                            $linea = 0;

                            try{

                            $refgral = $_SESSION['user']->getUser()->idtcuser."_".date("Y-m-d_H-i-s");

                            if(isset($_FILES['_file1'])) {
                                $archivo = fopen($ruta . $archivo_nombre, "r");
                                while (($datos = fgetcsv($archivo, ",")) == true) {
                                    if($count > 0){

                                        $errort_line = false;

                                        $debug = 0;

                                        $item = Transformation::getSerialByIMEI($mysql,$datos[0], $datos[1]);
                                        if($debug){
                                            echo "<br />";
                                            print_r($item);
                                        }

                                        $lineocnew = Transformation::getLineNewOC($mysql, $datos[2],$datos[3]); //cambia a partir del 2 se incrementa
                                        if($debug){
                                            echo "<br />";
                                            print_r($lineocew);
                                        }

                                        // se valida IMEI y SKU origen
                                        if($datos[0] != $item[0]->dscode ) {
                                            $msg = "Error: IMEI no existe para SKU Ingresado, actual: ".$item[0]->dscode;
                                            $errort = 1;
                                            $errort_line = true;
                                        }

																				//echo "<br />".$lineocnew[0]->dscode." contra ".$datos[4];
                                        if($lineocnew[0]->dscode != $datos[4]) {
                                            $msg = "Error: SKU no coincide con el deseado, actual:".$lineocnew[0]->dscode;
                                            $errort = 1;
                                            $errort_line = true;
                                        }


                                    ?>
                                    <tr class="<? if($errort_line==0){?>success<?}else{?>danger<?}?>">
                                        <td><? if(!$errort_line){echo "OK";}
                                            else{echo $msg;} ?></td>
                                        <td><? echo $item[0]->fnidheader; //oc ori?></td>
                                        <td><? echo $item[0]->fnidline; //line ori?></td>
                                        <td><? echo $item[0]->fniditem; //id item?></td>
																				<td><? echo $item[0]->dscode; //id item?></td>
																				<td><? echo $datos[1]; //oc new?></td>
                                        <td><? echo $datos[2]; //oc new?></td>
																				<td><? echo $datos[3]; //line new?></td>
                                        <td><? echo $lineocnew[0]->fniditem; ?></td>
																				<td><? echo $lineocnew[0]->dscode; ?></td>
                                    </tr>
                                <?
                                    } //if count
                                    $count++;
                                }//while
                                fclose($archivo);
                            } //if isset _file1
                        }catch(Exception $e){
                            echo $e->getMessage();
                        }

                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateUploadTransformation();" >Guardar</button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="location.href='transformation.php'">Regresar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? if($_POST['dispatch']==""){?>
        <input type="hidden" id="dispatch" name="dispatch" value="uploadedtransformation" />
    <? }else{?>
        <input type="hidden" id="dispatch" name="dispatch" value="tosavetransformation" />
    <? }?>
    <input type="hidden" id="iduser" name="iduser" value="<? echo $_SESSION['user']->getUser()->idtcuser;?>" />
    <input type="hidden" id="_filenamet" name="_filenamet" value="<? echo date("d_m_Y_H_i_s");?>" />
    <input type="hidden" id="_filenamefinal" name="_filenamefinal" value="<? if(isset($_FILES['_file1'])){ echo $archivo_nombre;} ?>" />
    <input type="hidden" id="_error" name="_error" value="<? echo $errort;?>" />

</form>
<script type="application/javascript">
    function validateUploadTransformation(){
        if($("#dispatch").val()=="uploadedtransformation"){
            if(document.getElementById('_file1').files[0] == ""){
                alert("Debe seleccionar archivo a cargar");
                return false;
            }
        }else if($("#dispatch").val()=="tosavetransformation"){
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
