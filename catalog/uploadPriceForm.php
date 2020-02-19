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
if($_POST['dispatch']=="uploaded" && isset($_FILES["_file1"])){

    $ruta = "../uploads/";
    $archivo_nombre= $_POST['_filenamet']."_".$_FILES["_file1"]["name"];
    $archivo_peso= $_FILES["_file1"]["size"];
    $archivo_temporal= $_FILES["_file1"]["tmp_name"];
    $archivo_tipo = trim($_FILES["_file1"]["type"]);
    $_POST['ruta']=$ruta;
    $_POST['nombre']=$archivo_nombre;

    $fileok = true;

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
    <form method="POST" id="uploadForm" name="uploadForm" action="pricequery.php" enctype="multipart/form-data">
<?}else{?>
    <form method="POST" id="uploadForm" name="uploadForm" action="<? echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<? }?>
    <!-- COMPONENT START -->
    <div class="panel-heading"><h7><strong>Carga de Listas de precios</strong></h7></div>
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
                                 <button class="btn btn-warning btn-success" type="button" onclick="validateUploadPrice();">Procesar</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No. Lista</th>
                            <th>Codigo SN</th>
                            <th>Nombre SN</th>
                            <th>SKU</th>
                            <th>Nombre</th>
                            <th>Precio Act.</th>
                            <th>Precio Nuevo</th>
                            <th>Revisi&oacute;n</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                            $linea = 0;

                            try{

                            if(isset($_FILES['_file1'])) {
                                $archivo = fopen($ruta . $archivo_nombre, "r");
                                while (($datos = fgetcsv($archivo, ",")) == true) {
                                    if($count > 0){
                                        $errort_line = true;
                                        $idlist = $datos[0];
                                        $item = Item::getRowByCode($mysql,$datos[2]);
                                        if(trim($datos[1])==''){
                                            $cliente = "";
                                            $idcliente = null;
                                        }
                                        else {
                                            $cliente = Partner::getRowByCode($mysql, $datos[1]);
                                            $idcliente = $cliente[0]->pnid;
                                        }
                                        //print_r($cliente);
                                        $price = 0;
                                        if(count($cliente)>0)
                                            $itemprice = Item::getAllByFilterBySNPriceBase($mysql,$item[0]->dscode,$idcliente);
                                            //$itemprice = Item::getAllByFilterBySN($mysql,$item[0]->dscode,$idcliente);

                                        $price = $itemprice[0]->pricewithdisc;
                                        /*echo "<br />*****";
                                        print_r($itemprice);
                                        echo "<br />pricewithdisc:".$itemprice[0]->pricewithdisc ;
                                        if($itemprice[0]->pricewithdisc >0)
                                            $price = $itemprice[0]->pricewithdisc;                                        $price = 0.0;
                                        echo "<br />price:".$price;
                                        */
                                        if((!isset($cliente[0]) || !isset($item[0])) && $datos[0] != 1) {
                                            $msg = "Error: No existen datos";
                                            $errort = 1;
                                            $fileok = false;
                                            $errort_line = false;
                                        }

                                    ?>
                                    <!--tr class="< ? if(isset($cliente[0]) && isset($item[0]) && ($price==$datos[4])){?>success< ?}else{?>danger< ?}?>"-->
                                    <tr class="<? if($errort_line){?>success<?}else{?>danger<?}?>">
                                        <td><? echo $datos[0]; //id list?></td>
                                        <td><? echo $datos[1]; //partner?></td>
                                        <td><? echo $cliente[0]->dsname; //partner?></td>
                                        <td><? echo $item[0]->dscode; //sku?></td>
                                        <td><? echo $item[0]->dsname; //nombre art?></td>
                                        <td><? echo number_format($price,2,'.',',') ; //precio ant?></td>
                                        <td><? echo number_format($datos[4],2,'.',','); //precio act?></td>
                                        <td><? if($errort_line){echo "OK";}
                                               else{echo $msg; $fileok = false;} ?></td>
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
                        <tr><td colspan="8"><input type="hidden" id="fileok" name="fileok" value="<? echo $fileok;?>"></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateUploadPrice();" >Guardar</button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="location.href='pricelist.php'">Regresar</button>
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
    <input type="hidden" id="_filenamet" name="_filenamet" value="<? echo date("d_m_Y_H_i_s");?>_lp" />
    <input type="hidden" id="_filenamefinal" name="_filenamefinal" value="<? if(isset($_FILES['_file1'])){ echo $archivo_nombre;} ?>" />
    <input type="hidden" id="_error" name="_error" value="<? echo $errort;?>" />

</form>
<script type="application/javascript">
    function validateUploadPrice(){
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
            if($("#fileok").val()==false){
                alert("No se puede guardar los registros por que existe un error!")
                return false;
            }
        }
        document.forms["uploadForm"].submit();
    }

</script>