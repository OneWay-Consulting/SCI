<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 10/11/2018
 * Time: 02:24 PM
 */
?>
<?php
require_once("../header.php");
_includes();
$mysql = new Mysql;

$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

$role = Role::getAll($mysql);
$titulo;
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

if($_POST['dispatch']=="update" || $_POST['dispatch']=="query")
    $_user = User::getRow($mysql, $_POST['id']);

?>
<form id="requestform" name="requestform" data-toggle="validator" class="form-horizontal" action="userquery.php" method="post" onsubmit="return validateForm();">
    <div class="panel panel-primary" style="width: 70%; margin:0 auto;">
        <div class="panel-heading"><h7><strong><?=$titulo;?> de Solicitudes</strong></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="_usuario" class="col-sm-2 col-form-label">Usuario:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_usuario" name="_usuario" onkeyup="hde(this);" value="<? echo $_user[0]->dsuser;?>"
                                   onchange="" <? if($_POST['dispatch']=="update" || $ac){ echo $ro; }?> /><div id="_disp"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_usuario" class="col-sm-2 col-form-label">Password:</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="_password" name="_password" onkeyup="hde(this);" <? if($ac){echo $ro;}?> placeholder="Password" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_usuario" class="col-sm-2 col-form-label">Confirmar password:</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="_passwordconf" name="_passwordconf" onkeyup="hde(this);" <? if($ac){echo $ro;}?> placeholder="Confirmar Password" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_rol" class="col-sm-2 col-form-label">Rol:</label>
                        <div class="col-sm-10">
                            <select id="_rol" class="form-control" name="_rol" onchange="hde(this);" <? if($_POST['dispatch']=="update" || $ac){echo $di;}?>>
                                <option value="">-- Seleccione --</option>
                                <? for($i=0; $i<count($role); $i++){?>
                                    <option value="<? echo $role[$i]->idtcrole;?>" <? if($role[$i]->idtcrole == $_user[0]->fnidrole){ echo "selected";}?>>
                                        <? echo $role[$i]->dsname;?>
                                    </option>
                                <? }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_email" class="col-sm-2 col-form-label">E-mail:</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="_email" name="_email" placeholder="example@mail.com" value="<? echo $_user[0]->dsemail;?>" <? if($ac){echo $ro;}?>/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_activo" class="col-sm-2 col-form-label">Activo:</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="_activo" name="_activo" onchange="hde(this);" <? if($ac){echo $di;}?>>
                                <option value="">--Seleccione--</option>
                                <option value="0" <? if($_user[0]->dnactivo == 0){ echo "selected";}?>>NO</option>
                                <option value="1" <? if($_user[0]->dnactivo == 1){ echo "selected";}?>>SI</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_nombre" class="col-sm-2 col-form-label">Nombre:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_nombre" name="_nombre" placeholder="Nombre de usuario" value="<? echo $_user[0]->dsemail;?>" <? if($ac){echo $ro;}?>/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4"/>
                        <div class="col-sm-4">
                            <? if($permission['user.php']['create']){?>
                            <button type="submit" class="btn btn-primary btn-block" id="btn_add" name="btn_add" style="cursor:pointer;" >Guardar</button>
                            <? }?>
                                <button type="button" class="btn btn-danger btn-block" id="btn_cancel" style="cursor:pointer;" onclick="location.href='user.php'">Cancelar</button>
                        </div>
                        <div class="col-sm-4"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
    <input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
</form>

