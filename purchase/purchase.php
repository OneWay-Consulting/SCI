<?php
	require_once("../header.php");
	_includes();
?>
    <script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
<?
	_header();

$mysql = new Mysql;

$permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

if($_SESSION['user']->getUser()->fnidrole==7)
  $iduser = $_SESSION['user']->getUser()->idtcuser;
else
  $iduser = null;


//$index = User::getAllPages($mysql,$_GET['_page']);
    if(isset($_POST))
        $_POST = String::sanitize($_POST,true);
		if(isset($_POST['date']) && isset($_POST['date2']) || isset($_POST['client']))
			$list = Purchase::getAllByType($mysql,$_POST['date'], $_POST['date2'], $_POST['client'],$iduser);
		else
			$list = Purchase::getAllByType($mysql,null,null,null,$iduser);
?>
<br />
<br />
<h2><span class="label label-primary">Bandeja de compras</span></h2>
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
                <input class="form-control" id="client" name="client" placeholder="C&oacute;digo o nombre de proveedor" type="text" value="<? if(isset($_POST['client'])){echo $_POST['client'];}?>"/>
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
    <? if($permission['purchase.php']['create']){?>
    <button type="button" class="btn btn-danger btn-sm" onclick="paint('purchaseAddForm.php','dispatch=add');" >Capturar Compra</button>
    <? }?>
</center>
<!-- fin filtros -->
<div id="_divfilter">
    <div id="alertBoxes"></div>
    <table id="c_purchase">
      <thead>
      <tr>
        <th>ID Compra</th>
        <th>Cod. Socio</th>
        <th>Nom. Socio</th>
        <th>Referencia</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Estatus</th>
        <th>Opciones</th>
      </tr>
      </thead>
      <tbody id="myTable">
        <? for($i=0; $i<count($list); $i++){?>
                <tr id="<? echo $list[$i]->pnid;?>">
                    <td><? echo $list[$i]->pnid;?></td>
                    <td><? echo $list[$i]->dscode;?></td>
                    <td><? echo $list[$i]->dsname;?></td>
                    <td><? echo $list[$i]->dsreference;?></td>
                    <td><? echo $list[$i]->dddocdate;?></td>
                    <td style="text-align: right">$<? echo number_format($list[$i]->doctotal,2,'.',',');?></td>
                    <td><? if($list[$i]->dsstatus == 1){echo "CREADO";}elseif($list[$i]->dsstatus == 2){ echo "RECIBIDO";}elseif($list[$i]->dsstatus == 6){ echo "CANCELADO";}?></td>
                    <td><? if($list[$i]->dsstatus == 1){?>
                        <img src="../images/buttons/editar.png" border="0" align="Modificar Registro" title="Modificar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('purchaseAddForm.php','dispatch=update&id=<? echo $list[$i]->pnid;?>');" />
                        <? }elseif($list[$i]->dsstatus == 2){?>
                            <img src="../images/buttons/vision.png" border="0" align="Consultar Registro" title="Consultar Registro" style="cursor:pointer; height:20px; width:20px;" onclick="sendAction('purchaseAddForm.php','dispatch=query&id=<? echo $list[$i]->pnid;?>');" />
                        <?}?>
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

    /*function sendFilter(){
        var parametros = {
            "date1" : $("#date").val(),
            "date2" : $("#date2").val(),
            "client": $("#client").val()
        };
        $.ajax({
            data:  parametros, //datos que se envian a traves de ajax
            url:   'pendingfilter.php', //archivo que recibe la peticion
            type:  'post', //método de envio
            beforeSend: function () {
                $("#_divfilter").html("Procesando, espere por favor...");
            },
            success:  function (response) { //una vez que el archivo recibe el request lo procesa y lo devuelve
                $("#_divfilter").html(response);
            }
        });
    }*/

</script>
