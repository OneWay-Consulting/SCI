<?php
	require_once("../header.php");
	_includes();
?>
    <script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
	_header();

	    $mysql = new Mysql;
$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

$index = Partner::getAllPages($mysql,$_GET['_page']);
		if(isset($_GET['_filter']))
			$list = Partner::getAllByType($mysql,"C");
		else
			$list = Partner::getAllByType($mysql,$_GET['_filter']);
      ?>
<h2><span class="label label-primary">Cat&aacute;logo de socios de negocio</span></h2>
<br />
<!-- filtros -->
<!--div class="form-group row" style="justify-content:left; float: left;">
    <label for="_filter" class="col-sm-2 col-form-label">Tipo:</label>
    <div class="col-sm-10">
        <select class="form-control" id="_filter" name="_filter" style="width: 100%; alignment: left">
            <option value="C" < ? if($_GET['_filter'] == "C"){ echo "selected";}?>>Cliente</option>
            <option value="S" < ? if($_user['_filter'] == "S"){ echo "selected";}?>>Proveedor</option>
        </select>
    </div>
</div>
<br />
<p>Escriba algun texto para el filtrado de datos:</p>
<input class="form-control" id="myInput" type="text" placeholder="Search.."-->
    <div class="form-row">
        <div class="form-group col-md-6">
            <select class="form-control" id="_filter" name="_filter" style="width: 80%; alignment: left">
                <option>-- Tipo de socio --</option>
                <option value="C" <? if($_GET['_filter'] == "C"){ echo "selected";}?>>Cliente</option>
                <option value="S" <? if($_user['_filter'] == "S"){ echo "selected";}?>>Proveedor</option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <input class="form-control" id="myInput" type="text" placeholder="Escriba alg&uacute;n texto para filtrar ...">
        </div>
    </div>
<center>
    <? if($permission['partner.php']['create']){?>
    <button type="button" class="btn btn-danger btn-sm" onclick="paint('partnerAddForm.php','dispatch=add');" >Agregar Socio</button>
    <? }?>
    <button type="button" class="btn btn-success btn-sm" onclick="window.open('export/exppartner.php','Reporte SN');">Exportar a Excel</button>
</center>
<!-- fin filtros -->
<div id="_divfilter">
    <div id="alertBoxes"></div>
    <table id="c_partners">
      <thead>
      <tr>
				<th>System ID</th>
        <th>C&oacute;digo Partner</th>
        <th>Raz&oacute;n Social</th>
        <th>RFC</th>
        <th>Email</th>
				<th>D&iacute;s cr&eacute;dito</th>
        <th>Tipo</th>
        <th>Opciones</th>
      </tr>
      </thead>
      <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
                <tr id="<? echo $list[$i]->pnid;?>">
											  <td><? echo $list[$i]->pnid;?></td>
                        <td><? echo $list[$i]->dscode;?></td>
                        <td><? echo utf8_encode($list[$i]->dsname);?></td>
                        <td><? echo $list[$i]->dsrfc;?></td>
                        <td><? echo $list[$i]->dsemail;?></td>
												<td><? echo $list[$i]->dncreditday;?></td>
                        <td><? if($list[$i]->dstype == "C"){echo "Cliente";}else{ echo "Proveedor";}?></td>
                        <td><img src="../images/buttons/editar.png" border="0" align="Modificar Registro" title="Modificar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('partnerAddForm.php','dispatch=update&id=<? echo $list[$i]->pnid;?>');" /></td>
                </tr>
                <? }?>
      </tbody>
    </table>
</div>
<?
	_footer();
?>
<script type="text/javascript">
	<? if($_SESSION['msg']){?>
	showAlertBox('<? echo $_SESSION['msg'];?>');
	<?
		unset($_SESSION['msg']);
	} ?>

    $(document).ready(function() {
        $("#myInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
