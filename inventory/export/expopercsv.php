<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 07/10/2019
 * Time: 05:21 PM
 */

//include database configuration file
require_once("../../includes/config.php");
session_start();

//get records from database
$mysql = new Mysql;

if((isset($_GET['from']) && isset($_GET['to'])) || $_GET['client']!="" )
    $list = Inventory::getOperationSales($mysql,$_GET['from'],$_GET['to'],$_GET['client']);
else
    $list = Inventory::getOperationSales($mysql);

if(count($list) > 0){
    $delimiter = ",";
    $filename = "Reporte_operaciones.csv";
    //$filename = "members_" . date('Y-m-d') . ".csv";

    //create a file pointer
    $f = fopen('php://memory', 'w');

    //set column headers
    //$fields = array('ID', 'Name', 'Email', 'Phone', 'Created', 'Status');
    $fields('#','OV','Pedido','Estatus Linea','Cod. Cliente','Nom. Cliente','Fecha creaci&oacute;n','Comentario Gral',
            'Estatus','SKU','Nom. Art&iacute;culo','Cantidad','Prec. Unit.','Total','Gu&iacute;a','Gu&iacute;a MC',
            'Canal','Paqueteria','Comentario L.','IMEI','Salida','Fecha');

    fputcsv($f, $fields, $delimiter);

    //output each row of the data, format line as csv and write to file pointer
    for($i=0; $i<count($list); $i++){
        //$lineData = array($row['id'], $row['name'], $row['email'], $row['phone'], $row['created'], $status);
        $lineaData=array(($i+1),
            $list[$i]->pnid,
            $list[$i]->dsrefline,
            $list[$i]->statusline,
            $list[$i]->dscode,
            $list[$i]->dsname,
            $list[$i]->ddcreated,
            $list[$i]->commenth,
            $list[$i]->statusname,
            $list[$i]->itemcode,
            $list[$i]->itemname,
            $list[$i]->dnquantity,
            number_format($list[$i]->dnprice,2,'.',','),
            number_format($list[$i]->total,2,'.',','),
            $list[$i]->dsguia,
            $list[$i]->dsguiac,
            $list[$i]->dscanal,
            $list[$i]->dspaqueteria,
            $list[$i]->dscomentariol,
            $list[$i]->fnidserial,
            $list[$i]->iddelivery,
            $list[$i]->fecdelivery);
        fputcsv($f, $lineData, $delimiter);
    }

    //move back to beginning of file
    fseek($f, 0);

    //set headers to download file rather than displayed
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

    //output all remaining data on a file pointer
    fpassthru($f);
}
exit;

?>