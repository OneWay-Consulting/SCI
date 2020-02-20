<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 01/11/2018
 * Time: 05:43 PM
 * mail: zurdokw@gmail.com
 */
function _includes(){
    require_once("includes/config.php");

    session_start();

    if(!$_SESSION['active'] || trim($_SESSION['user']->getUser()->idtcuser) == ""){
        header("Location: index.php");
    }

    ?>
    <html>
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Optional theme V3 -->
        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


        <!-- Bootstrap Date-Picker Plugin -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
        <!-- enddatepicker -->

        <link rel="stylesheet" href="<? echo URLWEB;?>css/responsive.css">
        <link rel="stylesheet" href="<? echo URLWEB;?>css/estilosInt.css">
        <script src='<? echo URLWEB;?>js/funciones.js' type="text/javascript"></script>
        <script src='<? echo URLWEB;?>js/funciones-user.js' type="text/javascript"></script>


        <title>Control webXex</title>
    </head>
<? }
function _header(){

    $mysql = new Mysql;
    $permission = Role::getPermissionsByRole($mysql, $_SESSION['user']->getUser()->fnidrole);

    ?>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<? echo URLWEB;?>start.php">
                    <img src="<? echo URLWEB;?>/img/logo.png" width="95" height="40" class="d-inline-block align-top" alt="" style="margin-top: -10px;" />
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cat&aacute;logos<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <? if($_SESSION['user']->getUser()->fnidrole==1){?>
                            <li><a href="<? echo URLWEB;?>catalog/role.php">Roles</a></li>
                            <? } if($permission['user.php']['create'] || $permission['user.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>catalog/user.php">Usuarios</a></li>
                            <? } if($permission['user.php']['create'] || $permission['partner.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>catalog/partner.php">Socios de negocio</a></li>
                            <? } if($permission['user.php']['create'] || $permission['item.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>catalog/item.php">Art&iacute;culos</a></li>
                            <? } if($permission['user.php']['create'] || $permission['pricelist.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>catalog/pricelist.php">Precios</a></li>
                            <? }?>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Ventas<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <? if($permission['quote.php']['create'] || $permission['quote.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>sales/quote.php">Cotizaci&oacute;n</a></li>
                            <? } if($permission['sales.php']['create'] || $permission['sales.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>sales/sales.php">Orden venta</a></li>
                            <? }?>
                        </ul>
                    </li>
                    <? if($permission['payment.php']['create'] || $permission['payment.php']['query']){?>
                    <li><a href="<? echo URLWEB;?>sales/payment.php">Cobranza</a></li>
                    <!--li><a href="<? echo URLWEB;?>sales/paymentpaid.php">Cobranza Pagado</a></li-->
                    <? } if($permission['purchase.php']['create'] || $permission['purchase.php']['query']){?>
                    <li><a href="<? echo URLWEB;?>purchase/purchase.php">Compras</a></li>
                    <? } if($permission['inventory.php']['create'] || $permission['inventory.php']['query']){?>
                    <!--li><a href="< ? echo URLWEB;?>inventory/inventory.php">Inventarios</a></li-->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Inventario<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                    <li><a href="<? echo URLWEB;?>inventory/inventory.php">Inventarios</a></li>
                                    <li><a href="<? echo URLWEB;?>inventory/return.php">Devoluciones</a></li>
                                    <li><a href="<? echo URLWEB;?>inventory/transfer.php">Transferencia Stock</a></li>
                                    <? if($_SESSION['user']->getUser()->fnidrole!=7){?>
                                        <li><a href="<? echo URLWEB;?>inventory/transformation.php">Compra Venta</a></li>
                                        <li><a href="<? echo URLWEB;?>inventory/checkserial.php">Consulta IMEI</a></li>
                                        <li><a href="<? echo URLWEB;?>inventory/checkSKU.php">Consulta SKU</a></li>
                                    <? }?>
                            </ul>
                        </li>
                    <? }?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reporte<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <? if($permission['cost.php']['create'] || $permission['cost.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>inventory/cost.php">Costo</a></li>
                            <? }  if($permission['stock.php']['create'] || $permission['stock.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>inventory/stock.php">Consulta Stock</a></li>
                            <? } if($permission['payment.php']['create'] || $permission['payment.php']['query']){?>
                            <!--li><a href="< ? echo URLWEB;?>sales/paymentgral.php">Estado de cuenta</a></li-->
                            <? } if($permission['payment.php']['create'] || $permission['payment.php']['query']){?>
                            <li><a href="<? echo URLWEB;?>sales/paymentgral2.php">Estado de cuenta V2</a></li>
                            <? }?>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">Usuario: <strong><? echo $_SESSION['user']->getUser()->dsuser;?></strong>&nbsp;&nbsp;Rol: <strong><? echo $_SESSION['user']->getUser()->role;?></strong></a></li>
                    <li><a href="<? echo URLWEB;?>sso/logout.php"><strong>Cerrar sesi&oacute;n</strong></a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
<div id="_content">
<? }
function _footer(){?>
    </div>
    </body>
<? }?>
