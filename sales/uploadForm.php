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
    <form method="POST" id="uploadForm" name="uploadForm" action="salesquery.php" enctype="multipart/form-data">
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
 							<th>Revisi&oacute;n</th>
                            <th>ID Item</th>
                            <th>SKU</th>
                            <th>Descripci&oacute;n</th>
                            <th>No. Unidades</th>
                            <th>Ref. Gral.</th>
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
                            <th>No. Temp</th>
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
                                        /*if($datos[0]=="963607"){
                                            $debug=1;
                                        }*/

                                        $item = Item::getRowByCode($mysql,$datos[0], $datos[1]);

                                        if($debug){
                                            echo "<br />";
                                            print_r($item);
                                        }

                                        $cliente = Partner::getRowByCode($mysql, $datos[7]); //cambia a partir del 2 se incrementa
                                        /*if($debug){
                                            echo "<br />";
                                            print_r($cliente);
                                        }*/

                                        // en 1 por el precio
                                        //if($item[0]->dscode == "961801")
                                        //    echo "<br />compara nombres base ID: ".$item[0]->dscode."<br />|".$item[0]->dsname."| contra archivo:|".$datos[1]."|";

                                        if(count($item)>0 && $item[0]->dsname == trim($datos[1])) {
                                            //if($item[0]->dscode == "961801")
                                            //    echo "<br />entro a comparar";
                                            //$itemprice = Item::getAllByFilterBySN($mysql,$item[0]->dscode,$cliente[0]->pnid);
                                            $itemprice = Item::getAllByFilterBySNPriceBase($mysql,$item[0]->dscode,$cliente[0]->pnid);

                                        }else{
                                            $msg = "Error: Nombre no coincide";
                                            $errort = 1;
                                            $errort_line = true;
                                        }

                                        //print_r($itemprice);
                                        $price = 0.0;
                                        if($itemprice[0]->pricewithdisc != "")
                                            $price = $itemprice[0]->pricewithdisc;

                                        $arrayl[$line]['price'] = $price;//$datos[4];

                                        /* valida el precio con variación de 10%*/
                                        if (!($datos[5] >= ($price - ($price * .15))) || !($datos[5] <= ($price + ($price * .25))) && !$errort_line) { //cambia a partir del 2 se incrementa
                                            $msg = "Error: Precio excede rango de 20%";
                                            $errort = 1;
                                            $errort_line = true;
                                        }

                                        //echo "<br /> ***** FIN Valida precio *******";
                                        /*fin valida el precio con variacion de 10%*/

                                        //echo "price doc:".$datos[4];
                                        if(!isset($cliente[0]) && !isset($item[0])&& !$errort_line) {
                                            $msg = "Error: No existen datos";
                                            $errort = 1;
                                            $errort_line = true;
                                        }

                                        /*check reference preview*/
                                        $ref = Quote::checkReference($mysql,$datos[3],$refgral,$cliente[0]->pnid); //cambia a partir del 2 se incrementa
                                        if($ref && !$errort_line) {
                                            $msg = "Error: Referencia existe, mismo cliente!";
                                            $errort = 1;
                                            $errort_line = true;
                                        }



                                    ?>
                                    <!--tr class="< ? if(isset($cliente[0]) && isset($item[0]) && ($price==$datos[4])){?>success< ?}else{?>danger< ?}?>"-->
                                    <tr class="<? if($errort_line==0){?>success<?}else{?>danger<?}?>">
                                        <td><? if(isset($cliente[0]) && isset($item[0]) && !$ref && !$errort_line){echo "OK";}
                                            else{echo $msg;} ?></td>
                                        <td><? echo $item[0]->pnid; //iditem?></td>
                                        <td><? echo $datos[0]; //sku?></td>
                                        <td><? echo $item[0]->dsname; //nombre art?></td>
                                        <td><? echo $datos[2]; //unidades?></td>
                                        <td><? echo $refgral; //unidades?></td>
                                        <td><? echo $datos[3]; ?></td>
                                        <td><? echo utf8_decode(trim($datos[4])); ?></td>
                                        <?


                                        ?>
                                        <!--td>< ? echo $datos[4]; ?></td-->
                                        <td>$<? echo number_format($price,2,".",","); ?></td>
                                        <td>$<? echo number_format($datos[5],2,".",","); ?></td>
                                        <td><? echo $datos[6]; ?></td>
                                        <td><? echo $cliente[0]->pnid; //id cliente ?></td>
                                        <td><? echo $datos[7]; //cliente?></td>
                                        <td><? echo $datos[8]; ?></td>
                                        <td><? echo $datos[9]; ?></td>
                                        <td><? echo $datos[11]; ?></td>
                                        <td><? echo $datos[12]; ?></td>
                                        <td><? echo $datos[13]; ?></td>
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
                            <button type="button" class="btn btn-info" id="btn_add" name="btn_add"style="cursor:pointer" onclick="validateUploadSales();" >Guardar</button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-danger" id="btn_cancel" style="cursor:pointer" onclick="location.href='quote.php'">Regresar</button>
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
