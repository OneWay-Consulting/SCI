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

$ac=0;
$ro = "readonly=readonly";
$di = "disabled=disabled";

//print_r($_POST);
if($_POST['dispatch']=="update"){
    $titulo = "Actualizaci&oacute;n";
}elseif($_POST['dispatch']=="query"){
    $titulo = "Consulta";
}else
    $titulo = "Alta";

if($_POST['dispatch']=="add" || $_POST['dispatch']=="update")
    $ac=0;
else
    $ac=1;

if($_POST['dispatch']=="update" || $_POST['dispatch']=="query") {
    $pricelist = Item::getAllPriceList($mysql, $_POST['id']);
    $listitem = Item::getAllItemByList($mysql, $_POST['id']);
    //$waretr = Item::getWaretr($mysql,$_POST['id']);
}elseif($_POST['dispatch']=="add" ){
    $listitem = Item::getAllItemByList($mysql, 1);
}
//print_r($pricelist);
$listpartner = Partner::getAllByType($mysql,"C");

//$listbase = Item::getAllByBasePrice($mysql);
//print_r($listbase);

?>
<form id="itemform" name="itemform" data-toggle="validator" class="form-horizontal" action="pricequery.php" method="post" onsubmit="return validateFormItem();">
    <div class="panel panel-primary" style="width: 90%; margin:0 auto;">
        <div class="panel-heading"><h7><strong><?=$titulo;?> de Lista de precios</strong></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="_name" class="col-sm-2 col-form-label">Nombre Lista:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_name" name="_name" value="<? echo $pricelist[0]->namelist;?>" <? if($_POST['dispatch']=="update"){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_code" class="col-sm-2 col-form-label">Cliente:</label>
                        <div class="col-sm-10">
                            <select id="_cliente" name="_cliente" class="form-control" <? if($_POST['dispatch']=="update"){ echo $di;} ?> required="required">
                                <option value="" >Seleccione</option>
                                <? for($i=0; $i<count($listpartner); $i++){?>
                                    <option value="<? echo $listpartner[$i]->pnid;?>" <? if($listpartner[$i]->pnid==$pricelist[0]->fnidclient){ echo "selected";}?> ><? echo utf8_encode($listpartner[$i]->dsname);?></option>
                                <? }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_type" class="col-sm-2 col-form-label">Activo:</label>
                        <div class="col-sm-10">
                            <select id="_active" name="_active" class="form-control" required="required">
                                <option value="" >Seleccione Tipo</option>
                                <option value="1" <? if($pricelist[0]->statuslist == 1){ echo "selected";}?> >SI</option>
                                <option value="0" <? if($pricelist[0]->statuslist == 0){ echo "selected";}?>>NO</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4"/>
                <div class="col-sm-4">
                    <? if($permission['pricelist.php']['create']){?>
                    <button type="submit" class="btn btn-primary btn-block" id="btn_addp" name="btn_add" style="cursor:pointer;" >Guardar</button>
                    <? }?>
                    <button type="button" class="btn btn-danger btn-block" id="btn_cancel" style="cursor:pointer;" onclick="location.href='pricelist.php'">Regresar</button>
                </div>
                <div class="col-sm-4"/>
            </div>
            <br />
            <input class="form-control" id="myInput" type="text" placeholder="Escriba texto para filtrado articulos..">
            <br />
            <center>
                <div class="panel panel-default"  style="width: 85%">
                    <div class="panel-body">
                        <span class="label label-primary">Art&iacute;culos</span>
                        <table align="center">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Art. ID</th>
                                    <th>Art. Code</th>
                                    <th>Art. Nombre </th>
                                    <th>Precio Base</th>
                                    <th>Precio Lista</th>
                                </tr>
                                </thead>
                                <tbody id="myTable">
                        <? for($i=0; $i<count($listitem); $i++){?>
                                <? if(count($listitem)>0){?>
                                <tr>
                                    <td><? echo ($i+1);?></td>
                                    <td><input type="hidden" class="form-control"  id="idtr[]" name="idtr[]" value="<? echo $listitem[$i]->pnidtr; ?>">
                                        <input type="hidden" class="form-control"  id="iditem[]" name="iditem[]" size="30" value="<? echo $listitem[$i]->pnid; ?>" readonly>
                                        <? echo $listitem[$i]->pnid;?>
                                    </td>
                                    <td><input type="hidden" class="form-control"  id="codeitem[]" name="codeitem[]" value="<? echo $listitem[$i]->dscode; ?>" readonly>
                                        <? echo $listitem[$i]->dscode;?>
                                    </td>
                                    <td><input type="hidden" class="form-control"  size="150" value="<? echo $listitem[$i]->dsname; ?>" readonly>
                                        <? echo $listitem[$i]->dsname;?>
                                    </td>
                                    <td><input type="text" class="form-control"  value="<? echo number_format($listitem[$i]->precioBase,2); ?>" readonly></td>
                                    <? if($_POST['dispatch']=="update"){?>
                                    <td><input type="text" class="form-control"  id="pricel[]" name="pricel[]" value="<? if($listitem[$i]->precioPL==""){echo "0.00";}else{echo $listitem[$i]->precioPL;} ?>"></td>
                                    <?}elseif($_POST['dispatch']=="add"){?>
                                        <td><input type="text" class="form-control"  id="pricel[]" name="pricel[]" value="<? if($listitem[$i]->precioBase==""){echo "0.00";}else{$listitem[$i]->precioBase;}; ?>"></td>
                                    <? }?>
                                </tr>
                                    </label>
                                <? }?>
                        <? }//for?>
                                </tbody>
                        </table>
                    </div>
                </div>
            </center>

        </div>
        <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
        <input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
</form>

<script type="text/javascript">
    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

</script>

