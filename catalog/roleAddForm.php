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
    $role = Role::getRow($mysql, $_POST['id']);
    $listmodule = Role::getModules($mysql,$_POST['id']);
    $listmoduleinrole = Role::getPermissionsByRole($mysql,$role[0]->idtcrole);
}elseif($_POST['dispatch']=="add"){
    $listmodule = Role::getModules($mysql);
//    $next = Item::getNext($mysql);
}

//print_r($waretr);

?>
<form id="itemform" name="itemform" data-toggle="validator" class="form-horizontal" action="rolequery.php" method="post" onsubmit="return validateFormItem();">
    <div class="panel panel-primary" style="width: 90%; margin:0 auto;">
        <div class="panel-heading"><h7><strong><?=$titulo;?> de Art&iacute;culo</strong></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="_code" class="col-sm-2 col-form-label">C&oacute;digo:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_code" name="_code" onkeyup="hde(this);" value="<? if($_POST['dispatch']=="add"){echo $next[0]->consec;}else{echo $role[0]->idtcrole;}?>" readonly/>
                             <div id="_disp"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_name" class="col-sm-2 col-form-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_name" name="_name" value="<? echo $role[0]->dsname;?>" <? if($ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_address" class="col-sm-2 col-form-label">Comentarios:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="2" id="_comments" name="_comments" <? if($ac){ echo $ro; }?>  placeholder="Comentarios opcional"><? echo ($role[0]->dsdescription);?> </textarea>
                        </div>
                    </div>
                </div>
            </div>
            <center>
            <div class="panel panel-default"  style="width: 70%">
                <div class="panel-body">
                    <span class="label label-primary">M&oacute;dulos</span>
                    <table>
                        <thead>
                        <tr><td>M&oacute;dulo</td>
                            <td>Crear</td>
                            <td>Vista</td>
                            <td>Descripci&oacute;n</td>
                            </tr>
                        </thead>
                        <tbody>
                        <? for($i=0; $i<count($listmodule); $i++){?>
                            <tr><td><input type="hidden" id="_exist[]" name="_exist[]" value="<? echo $listmoduleinrole[$listmodule[$i]->dspage]['id'];?>" /> <? echo $listmodule[$i]->dspage;?></td>
                                <td align="center">
                                    <select id="_create[]" name="_create[]">
                                        <option value="1"  <? if($listmoduleinrole[$listmodule[$i]->dspage]['create']){ echo "selected";}?> >Habilitado</option>
                                        <option value="0"  <? if(!$listmoduleinrole[$listmodule[$i]->dspage]['create']){ echo "selected";}?> >Inhabilitado</option>
                                    </select>
                                </td>
                                <td align="center">
                                    <select id="_query[]" name="_query[]">
                                        <option value="1"  <? if($listmoduleinrole[$listmodule[$i]->dspage]['query']){ echo "selected";}?> >Habilitado</option>
                                        <option value="0"  <? if(!$listmoduleinrole[$listmodule[$i]->dspage]['query']){ echo "selected";}?> >Inhabilitado</option>
                                    </select>
                                </td>
                                <td><input type="hidden" id="_idmod[]" name="_idmod[]" value="<? echo $listmodule[$i]->pnid;?>" /><? echo utf8_encode($listmodule[$i]->dscomment);?></td>
                            </tr>
                        <?} ?>
                        </tbody>

                    </table>
                </div>
            </div>
            </center>
            <div class="row">
                <div class="col-sm-4"/>
                <div class="col-sm-4">
                    <? if($permission['role.php']['create']){?>
                    <button type="submit" class="btn btn-primary btn-block" id="btn_addp" name="btn_add" style="cursor:pointer;" >Guardar</button>
                    <? }?>
                    <button type="button" class="btn btn-danger btn-block" id="btn_cancel" style="cursor:pointer;" onclick="location.href='role.php'">Regresar</button>
                </div>
                <div class="col-sm-4"/>
            </div>
        </div>
    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
    <input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
</form>

