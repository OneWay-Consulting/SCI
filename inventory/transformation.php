<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 23/05/2019
 * Time: 12:32 PM
 */

	require_once("../header.php");
	_includes();
?>
    <script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
	_header();

	    $mysql = new Mysql;
        $permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

        //$index = User::getAllPages($mysql,$_GET['_page']);
    if(isset($_POST))
            $_POST = String::sanitize($_POST,true);
		if(isset($_POST['date']) && isset($_POST['date2']) || isset($_POST['client']))
			$list = Transformation::getAllByType($mysql,$_POST['date'], $_POST['date2'], $_POST['client']);
		else
			$list = Transformation::getAllByType($mysql);

		//print_r($list);
      ?>
<br />
<br />
<h2><span class="label label-primary">Bandeja de COMPRA VENTA</span></h2>
<br />
<br />
<br />
<!-- filtros -->
<!-- filtros -->
<form id="_purchasefilter" name="_idpurchasefilter" data-toggle="validator" class="form-horizontal" action="<? echo $_SERVER['PHP_SELF'];?>" method="post">
<div class="container">
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker6'>
                <input class="form-control" id="date" name="date" placeholder="YYYY-MM-DD" type="text" value="<? if(isset($_POST['date'])){echo $_POST['date'];}?>"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker7'>
                <input class="form-control" id="date2" name="date2" placeholder="YYYY-MM-DD" type="text" value="<? if(isset($_POST['date2'])){echo $_POST['date2'];}?>"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker8'>
                <input class="form-control" id="client" name="client" placeholder="Texto en comentarios" type="text" value="<? if(isset($_POST['client'])){echo $_POST['client'];}?>"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-sunglasses"></span>
                </span>
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
        <div class="form-group">
            <div class='input-group date' id='datetimepicker9'>
                <button type="submit" id="buscar" name="buscar" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </div>
</div>
</form>
<input class="form-control" id="myInput" type="text" placeholder="Escriba texto para filtrado de datos:..">
<br />
<center>
    <? if($permission['quote.php']['create']){?>
			<button type="button" class="btn btn-info btn-sm" onclick="location.href='transformationUploadForm.php'" >Cargar SKU COMPRA - VENTA</button>
    </form>
    <? }?>

</center>
<!-- fin filtros -->
<div id="_divfilter">
    <div id="alertBoxes"></div>
    <table id="c_purchase">
      <thead>
      <tr>
        <th>ID Transf</th>
        <th>Fecha</th>
        <th>Creador</th>
        <th>Comentarios</th>
        <th>Opciones</th>
      </tr>
      </thead>
      <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
                <tr id="<? echo $list[$i]->pnid;?>">
                    <td><? echo $list[$i]->pnid;?></td>
                    <td><? echo $list[$i]->dddate;?></td>
                    <td><? echo $list[$i]->dsuser;?></td>
                    <td><? echo $list[$i]->dscomments;?></td>
                    <td><img src="../images/buttons/vision.png" border="0" align="Consultar Registro" title="Consultar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('transformationForm.php','dispatch=query&id=<? echo $list[$i]->pnid;?>');" />
                    </td>
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

    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        var date_input=$('input[name="date"]'); //our date input has the name "date"
        var date2_input=$('input[name="date2"]'); //our date input has the name "date"
        var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
        var options={
            format: 'yyyy-mm-dd',
            container: container,
            todayHighlight: true,
            autoclose: true,
        };
        date_input.datepicker(options);
        date2_input.datepicker(options);
    });

    function redirect(){
        $().redirect('uploadForm.php', {'dispatch': 'add'})
    }

</script>
