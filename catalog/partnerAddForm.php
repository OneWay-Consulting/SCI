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

if($_POST['dispatch']=="update" || $_POST['dispatch']=="query")
    $partner = Partner::getRow($mysql, $_POST['id']);


if($_POST['dispatch']=="add"){
    $next = Partner::getNextByType($mysql,"C");
    $next = "C-".str_pad($next[0]->consec,4,"0",STR_PAD_LEFT);
}


?>
<form id="partnerform" name="partnerform" data-toggle="validator" class="form-horizontal" action="partnerquery.php" method="post" onsubmit="return validateFormPartner();">
    <div class="panel panel-primary" style="width: 90%; margin:0 auto;">
        <div class="panel-heading"><h7><strong><?=$titulo;?> de Socio de negocios</strong></h7></div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="_code" class="col-sm-2 col-form-label">C&oacute;digo:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_code" name="_code" onkeyup="hde(this);" value="<? if($_POST['dispatch']=="add"){echo $next;}else{echo $partner[0]->dscode;}?>"
                                  readonly/><div id="_disp"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_name" class="col-sm-2 col-form-label">Raz&oacute;n Social:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_name" name="_name" value="<? echo $partner[0]->dsname;?>" <? if($ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_type" class="col-sm-2 col-form-label">Tipo Socio:</label>
                        <div class="col-sm-10">
                            <select id="_type" name="_type" onchange="checkPartnerCode();" class="form-control" <? if($_POST['dispatch']=="update" || $ac){ echo $di;} ?> required="required">
                                    <option value="" >Seleccione Tipo</option>
                                    <option value="C" <? if($partner[0]->dstype == "C"){ echo "selected";}?> >Cliente</option>
                                    <option value="S" <? if($partner[0]->dstype == "S"){ echo "selected";}?>>Proveedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_rfc" class="col-sm-2 col-form-label">RFC:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_rfc" name="_rfc" value="<? echo $partner[0]->dsrfc;?>" <? if($ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_rfc" class="col-sm-2 col-form-label">Cr&eacute;dito:</label>
                        <div class="col-sm-3">
                            <select id="_credit" name="_credit" class="form-control" <? if($_POST['dispatch']=="query" || $ac){ echo $di;} ?> required="required">
                                <option value="" >Seleccione Tipo</option>
                                <option value="Y" <? if($partner[0]->dscredit == "Y"){ echo "selected";}?>>SI</option>
                                <option value="N" <? if($partner[0]->dscredit == "N"){ echo "selected";}?>>NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_rfc" class="col-sm-2 col-form-label">D&iacute;as Cr&eacute;dito:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_nodias" name="_nodias" value="<? echo $partner[0]->dncreditday;?>" <? if($ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_rfc" class="col-sm-2 col-form-label">Porcentaje Descuento:</label>
                        <div class="col-sm-3">
                            <input type="text" maxlength="3"  class="form-control" id="_pordesc" name="_pordesc" value="<? echo $partner[0]->dddiscountperc;?>" <? if($_POST['dispatch']=="query" || $ac){ echo $ro;} ?> required="required" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_active" class="col-sm-2 col-form-label">Activo:</label>
                        <div class="col-sm-3">
                            <select id="_active" name="_active" class="form-control" <? if($_POST['dispatch']=="query" || $ac){ echo $di;} ?> required="required">
                                <option value="" >Seleccione</option>
                                <option value="1" <? if($partner[0]->dsactive == "1"){ echo "selected";}?>>Activo</option>
                                <option value="0" <? if($partner[0]->dsactive == "0"){ echo "selected";}?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group row">
                        <label for="_address" class="col-sm-2 col-form-label">Direcci&oacute;n:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="2" id="_address" name="_address" <? if($ac){ echo $ro; }?>  placeholder="1234 Main St, City, County. Zipcode"><? echo ($partner[0]->dsaddress);?> </textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_phone" class="col-sm-2 col-form-label">Tel&eacute;fono:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_phone" name="_phone" value="<? echo $partner[0]->dsphone;?>" <? if($ac){ echo $ro; }?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="_mail" class="col-sm-2 col-form-label">Correo:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="_mail" name="_mail" value="<? echo $partner[0]->dsemail;?>" <? if($ac){ echo $ro; }?> placeholder="sample@mail.com" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4"/>
                <div class="col-sm-4">
                    <? if($permission['partner.php']['create']){?>
                    <button type="submit" class="btn btn-primary btn-block" id="btn_addp" name="btn_addp" style="cursor:pointer;" >Guardar</button>
                    <button type="button" class="btn btn-danger btn-block" id="btn_cancel" style="cursor:pointer;" onclick="location.href='partner.php'">Cancelar</button>
                    <? }?>
                </div>
                <div class="col-sm-4"/>
            </div>
        </div>
    </div>
    <input type="hidden" id="_dstype" name="_dstype" value="S"/>
    <input type="hidden" id="dispatch" name="dispatch" value="<? echo $_POST['dispatch']; ?>"/>
    <input type="hidden" id="id" name="id" value="<? echo $_POST['id'];?>"/>
</form>
