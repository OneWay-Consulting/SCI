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

$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

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
    $partner = Item::getRow($mysql, $_POST['id']);
    $waretr = Item::getWaretr($mysql,$_POST['id']);
}elseif($_POST['dispatch']=="add"){
    $next = Item::getNext($mysql);
}
//print_r($partner);
$listware = Item::getWareHouse($mysql);
$listcompany = Item::getCompany($mysql);
$listbranch = Item::getBranch($mysql);

/*
print_r($listcompany);
echo "<br />";
echo "<br />idcompany:".$listcompany[0]->id;
echo "<br />idcompany:".$listcompany[1]->id;
echo "<br />idcompany:".$listcompany[2]->id;
echo "<br />idcompany partner".$partner[0]->fnidcompany;
*/
//print_r($waretr);

?>
<form id="itemform" name="itemform" data-toggle="validator" class="form-horizontal" action="itemquery.php" method="post" onsubmit="return validateFormItem();">
    <div class="panel panel-primary" style="width: 90%; margin:0 auto;">
        <div class="panel-heading"><h7><strong><?=$titulo;?> de Art&iacute;culo</strong></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="_code" class="col-sm-2 col-form-label">C&oacute;digo:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_code" name="_code" onkeyup="hde(this);" value="<? if($_POST['dispatch']=="add"){echo $next[0]->consec;}else{echo $partner[0]->dscode;}?>" <? if($_POST['dispatch']!="add"){echo $ro;}?> />
                             <div id="_disp"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_name" class="col-sm-2 col-form-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_name" name="_name" value="<? echo utf8_encode($partner[0]->dsname);?>" <? if($ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_upc" class="col-sm-2 col-form-label">UPC:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_upc" name="_upc" value="<? echo $partner[0]->dsupc;?>" <? if($_POST['dispatch']=="query" || $ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_upc" class="col-sm-2 col-form-label">UPC2:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_upc2" name="_upc2" value="<? echo $partner[0]->dsupc2;?>" <? if($_POST['dispatch']=="query" || $ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_parent" class="col-sm-2 col-form-label">No. Fabricante:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_parent" name="_parent" value="<? echo $partner[0]->dspadre;?>" <? if($_POST['dispatch']=="query" || $ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_parent" class="col-sm-2 col-form-label">Estatus:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_status" name="_status" value="<? echo $partner[0]->dspadre;?>" <? if($_POST['dispatch']=="query" || $ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_type" class="col-sm-2 col-form-label">Empresa:</label>
                        <div class="col-sm-10">
                            <select id="_company" name="_company" class="form-control" <? if($_POST['dispatch']=="query" || $ac){ echo $di;} ?> required="required">
                                <option value="" >Seleccione</option>
                                <? for($i=0; $i<count($listcompany); $i++){?>
                                    <option value="<? echo $listcompany[$i]->id;?>" <? if($listcompany[$i]->id == $partner[0]->fnidcompany){ echo "selected";}?>><? echo $listcompany[$i]->dsname;?></option>
                                <? }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_type" class="col-sm-2 col-form-label">Marca:</label>
                        <div class="col-sm-10">
                            <select id="_branch" name="_branch" class="form-control" <? if($_POST['dispatch']=="query" || $ac){ echo $di;} ?> required="required">
                                <option value="" >Seleccione</option>
                                <? for($i=0; $i<count($listbranch); $i++){?>
                                    <option value="<? echo $listbranch[$i]->id;?>" <? if($listbranch[$i]->id == $partner[0]->fnidbranch){ echo "selected";}?>><? echo $listbranch[$i]->dsname;?></option>
                                <? }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_type" class="col-sm-2 col-form-label">Maneja serie:</label>
                        <div class="col-sm-10">
                            <select id="_serie" name="_serie" class="form-control" <? if($ac){ echo $di;} ?> required="required">
                                    <option value="" >Seleccione</option>
                                    <option value="1" <? if($partner[0]->dsserial && $_POST['dispatch']=="update"){ echo "selected";}?> >SI</option>
                                    <option value="0" <? if(!$partner[0]->dsserial && $_POST['dispatch']=="update" ){ echo "selected";}?>>NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_type" class="col-sm-2 col-form-label">Activo:</label>
                        <div class="col-sm-10">
                            <select id="_active" name="_active" class="form-control" <? if($ac){ echo $di;} ?> required="required">
                                <option value="" >Seleccione Tipo</option>
                                <option value="1" <? if($partner[0]->dsactive && $_POST['dispatch']=="update"){ echo "selected";}?> >SI</option>
                                <option value="0" <? if(!$partner[0]->dsactive && $_POST['dispatch']=="update"){ echo "selected";}?>>NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_address" class="col-sm-2 col-form-label">Comentarios:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="2" id="_comments" name="_comments" <? if($ac){ echo $ro; }?>  placeholder="Comentarios opcional"><? echo ($partner[0]->dscomments);?> </textarea>
                        </div>
                    </div>
                </div>
            </div>
            <center>
            <div class="panel panel-default"  style="width: 70%">
                <div class="panel-body">
                    <span class="label label-primary">&Aacute;lmacenes</span>
                    <? for($i=0; $i<count($listware); $i++){?>
                        <div class="checkbox" style="text-align: left">
                            <? if(count($waretr)>0){
                                for($j=0; $j<count($waretr); $j++){
                                    if($waretr[$j]->fnidware == $listware[$i]->pnid AND $waretr[$j]->dbactive){?>
                                        <label><input type="checkbox" id="ware[]" name="ware[]" value="<? echo $listware[$i]->pnid; ?>" checked="checked"><? echo $listware[$i]->dscode." - ".$listware[$i]->dsname;?></label>
                            <?      }elseif($waretr[$j]->fnidware == $listware[$i]->pnid){?>
                                        <label><input type="checkbox" id="ware[]" name="ware[]" value="<? echo $listware[$i]->pnid; ?>"><? echo $listware[$i]->dscode." - ".$listware[$i]->dsname;?></label>
                            <?      }?>
                            <? }
                            }else{//count > 0?>
                                <label><input type="checkbox" id="ware[]" name="ware[]" value="<? echo $listware[$i]->pnid; ?>"><? echo $listware[$i]->dscode." - ".$listware[$i]->dsname;?></label>
                            <? }?>
                        </div>
                    <? }//for?>
                </div>
            </div>
            </center>
            <div class="row">
                <div class="col-sm-4"/>
                <div class="col-sm-4">
                    <? if($permission['item.php']['create']){?>
                    <button type="submit" class="btn btn-primary btn-block" id="btn_addp" name="btn_add" style="cursor:pointer;" >Guardar</button>
                    <? }?>
                    <button type="button" class="btn btn-danger btn-block" id="btn_cancel" style="cursor:pointer;" onclick="location.href='item.php'">Cancelar</button>
                </div>
                <div class="col-sm-4"/>
            </div>
        </div>
    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
    <input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
</form>
