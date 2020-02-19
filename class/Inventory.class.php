<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 14/03/2019
 * Time: 01:47 PM
 */

class Inventory
{

    public static function  getOperationSales(Mysql $mysql, $datefrom = null, $dateto = null, $idclient = null){

      $q = " SELECT ths.dsreference, ths.dscode, ths.dsname, ths.ddcreated, ts.dsname AS 'statusname',
                     tsl.dsname AS 'itemname', tsl.dscode AS 'itemcode',
                      CASE WHEN tsser.fnidserial IS NULL THEN tsl.dnquantity ELSE 1 END AS 'dnquantity',
                      tsl.dnprice, (tsl.dnprice * tsl.dnquantity) AS 'total',
                     ths.dscomments AS 'commenth', tsl.dsguia, tsl.dsguiac, tsl.dscanal, tsl.dspaqueteria, tsl.dscomentariol,
                     tsser.fnidserial, tsl.dsrefline, tsl.dsstatus AS 'statusline', ths.pnid,
                     (SELECT trinv.dscomments FROM trinventory trinv WHERE trinv.dsdirection = -1 AND trinv.dsorigen = ths.pnid AND trinv.dslinebase = tsl.pnid LIMIT 1) AS 'iddelivery',
                     (SELECT tdel.dddate FROM thdelivery tdel WHERE tdel.pnid = tsser.fnidheaderdelivery ) AS 'fecdelivery'
              FROM thsales ths INNER JOIN tcstatus ts ON ts.pnid = ths.dsstatus
                               INNER JOIN thsalesline tsl  ON tsl.thidheader = ths.pnid
                               LEFT JOIN thsalesserial tsser ON tsser.fnidline = tsl.pnid
              WHERE 1 = 1 " ;

/*
        $q = "SELECT ths.dsreference, ths.dscode, ths.dsname, ths.ddcreated, ts.dsname AS 'statusname',
                       tsl.dsname AS 'itemname', tsl.dscode AS 'itemcode',
                        CASE WHEN tsser.fnidserial IS NULL THEN tsl.dnquantity ELSE 1 END AS 'dnquantity',
                        tsl.dnprice, (tsl.dnprice * tsl.dnquantity) AS 'total',
                       ths.dscomments AS 'commenth', tsl.dsguia, tsl.dsguiac, tsl.dscanal, tsl.dspaqueteria, tsl.dscomentariol,
                       tsser.fnidserial, tsl.dsrefline, tsl.dsstatus AS 'statusline', ths.pnid,
                       trinv.dscomments AS 'iddelivery'
                FROM thsales ths INNER JOIN tcstatus ts ON ts.pnid = ths.dsstatus
                                 INNER JOIN thsalesline tsl  ON tsl.thidheader = ths.pnid
                                 LEFT JOIN thsalesserial tsser ON tsser.fnidline = tsl.pnid
                                 LEFT JOIN trinventory trinv  ON trinv.dsdirection = -1 AND trinv.dsorigen = ths.pnid AND trinv.dslinebase = tsl.pnid
                WHERE 1 = 1 " ;
*/

        if($datefrom != null && $dateto != null)
            $q .= " AND ( CAST(ths.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto') ";
        if($idclient != null)
            $q .= " AND (ths.dsname LIKE ('%".$idclient."%') OR ths.dscode LIKE ('%".$idclient."%'))";
          $q .= " ORDER BY tsl.pnid ASC";
        //echo "<br />getOperationSales: ".$q;
        return $mysql->execute($q);

    }//getOperationSales

    public static function  getOperationSales2(Mysql $mysql, $datefrom = null, $dateto = null, $idclient = null){

        $q = " SELECT ths.dsreference, ths.dscode, ths.dsname, ths.ddcreated, ts.dsname AS 'statusname',
                     tsl.dsname AS 'itemname', tsl.dscode AS 'itemcode',
                      CASE WHEN tsser.fnidserial IS NULL THEN tsl.dnquantity ELSE 1 END AS 'dnquantity',
                      tsl.dnprice, (tsl.dnprice * tsl.dnquantity) AS 'total',
                     ths.dscomments AS 'commenth', tsl.dsguia, tsl.dsguiac, tsl.dscanal, tsl.dspaqueteria, tsl.dscomentariol,
                     tsser.fnidserial, tsl.dsrefline, tsl.dsstatus AS 'statusline', ths.pnid,
                     (SELECT trinv.dscomments FROM trinventory trinv WHERE trinv.dsdirection = -1 AND trinv.dsorigen = ths.pnid AND trinv.dslinebase = tsl.pnid LIMIT 1) AS 'iddelivery',
                     (SELECT tdel.dddate FROM thdelivery tdel WHERE tdel.pnid = tsser.fnidheaderdelivery ) AS 'fecdelivery'
              FROM thsales ths INNER JOIN tcstatus ts ON ts.pnid = ths.dsstatus
                               INNER JOIN thsalesline tsl  ON tsl.thidheader = ths.pnid
                               INNER JOIN thsalesserial tsser ON tsser.fnidline = tsl.pnid
              WHERE 1 = 1 " ;


        if($datefrom != null && $dateto != null)
            $q .= " AND ( CAST(ths.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto') ";
        if($idclient != null)
            $q .= " AND (ths.dsname LIKE ('%".$idclient."%') OR ths.dscode LIKE ('%".$idclient."%'))";
        $q .= " ORDER BY tsl.pnid ASC";
        //echo "<br />getOperationSales: ".$q;
        return $mysql->execute($q);

    }//getOperationSales2


    public static function getOperationSalesOpen(Mysql $mysql, $datefrom = null, $dateto = null){

        $q = " SELECT ths.dsreference, ths.dscode, ths.dsname, ths.ddcreated, ts.dsname AS 'statusname',
                       tsl.dsname AS 'itemname', tsl.dscode AS 'itemcode', tsl.dnopenqty AS 'dnquantity', tsl.dnprice, (tsl.dnprice * tsl.dnopenqty) AS 'total',
                       ths.dscomments AS 'commenth', tsl.dsguia, tsl.dscanal, tsl.dspaqueteria, tsl.dscomentariol,
                       tsser.fnidserial, tsl.dsrefline, tsl.dsstatus AS 'statusline', ths.pnid
                FROM thsales ths INNER JOIN tcstatus ts ON ts.pnid = ths.dsstatus
                                 INNER JOIN thsalesline tsl  ON tsl.thidheader = ths.pnid
                                 LEFT JOIN thsalesserial tsser ON tsser.fnidline = tsl.pnid
                WHERE 1 = 1 AND ths.dsstatus = 3 AND tsl.dnopenqty > 0 AND ths.dsstatus <> 6 AND  tsl.dsstatus <> 'CANCEL' " ;
        if($datefrom != null && $dateto != null)
            $q .= " AND ( CAST(ths.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto') ";
        else
            $q .= " AND ( CAST(ths.ddcreated AS DATE) BETWEEN DATE_SUB(NOW(), INTERVAL 15 DAY) AND NOW() ) ";
        //echo "<br />getOperationSalesOpen: ".$q;
        return $mysql->execute($q);

    }

    public static function getOperationSalesSerial($mysql, $from=null, $to=null, $filter=null){

        $q = " SELECT ths.dsreference, ths.dscode, ths.dsname, ths.ddcreated, ts.dsname AS 'statusname',
                       tsl.dsname AS 'itemname', tsl.dscode AS 'itemcode', tsl.dnquantity, tsl.dnprice, (tsl.dnprice * tsl.dnquantity) AS 'total',
                       ths.dscomments AS 'commenth', tsl.dsguia, tsl.dscanal, tsl.dspaqueteria, tsl.dscomentariol,
                       tsser.fnidserial, tsl.dsrefline, tsl.dsstatus AS 'statusline', ths.pnid
                FROM thsales ths INNER JOIN tcstatus ts ON ts.pnid = ths.dsstatus
                                 INNER JOIN thsalesline tsl  ON tsl.thidheader = ths.pnid
                                 LEFT JOIN thsalesserial tsser ON tsser.fnidline = tsl.pnid
                WHERE 1 = 1 AND ths.dsstatus = 3 ";
        if($from != null && $to != null)
            $q .= " AND ( CAST(ths.ddcreated AS DATE) BETWEEN '$from' AND '$to') ";

        if($filter != "")
            $q .= " AND (tsl.dsname LIKE '%$filter%' OR  tsl.dscode LIKE '%$filter%') ";

        //echo "<br />getOperationSalesSerial: ".$q;
        return $mysql->execute($q);

    }

    public static function getOperationPurchase(Mysql $mysql, $from=null, $to=null,$filter=null){
      $q = "SELECT ths.dsreference, ths.dscode, ths.dsname, ths.ddcreated, ts.dsname AS 'statusname',
                     tsl.dsname AS 'itemname', tsl.dscode AS 'itemcode',
                      CASE WHEN tsser.fnidserial IS NULL THEN tsl.dnquantity ELSE 1 END AS 'dnquantity',
                      tsl.dnprice, (tsl.dnprice *  CASE WHEN tsser.fnidserial IS NULL THEN tsl.dnquantity ELSE 1 END) AS 'total',
                     tsser.fnidserial, ths.pnid,
                     tent.pnid AS 'identry',
                     tent.dddate AS 'dateentry'
              FROM thpurchase ths INNER JOIN tcstatus ts ON ts.pnid = ths.dsstatus
                               INNER JOIN thpurchaseline tsl  ON tsl.thidheader = ths.pnid
                               LEFT JOIN thpurchaseserial tsser ON tsser.fnidline = tsl.pnid
                               LEFT JOIN thentry tent ON tent.pnid = tsser.fnidheaderentry
              WHERE 1 = 1 " ;
      if($from != null && $to != null)
          $q .= " AND ( CAST(ths.ddcreated AS DATE) BETWEEN '$from' AND '$to') ";
      if($filter != null)
          $q .= " AND (ths.dsname LIKE ('%".$filter."%') OR ths.dscode LIKE ('%".$filter."%'))";

      //echo "<br />getOperationSales: ".$q;
      return $mysql->execute($q);
    }

    public static function getSerialByItemAndWare(Mysql $mysql, $iditem, $fnidware=1){

        $q = " SELECT tl.dsname, tl.dscode, tsel.fnidserial, wh.dsname AS 'warename' ".
             " FROM thpurchaseline tl INNER JOIN thpurchaseserial tsel ON tsel.fnidline = tl.pnid ".
             "                        LEFT JOIN tcwarehouse wh ON wh.pnid = tl.fnidware ".
             " WHERE tsel.fnidquantity = 1 AND tl.dscode IN ('$iditem') AND tsel.fnidware = $fnidware ";
        //echo "<br />getSerialByItemAndWare: ".$q;
        return $mysql->execute($q);
    }


    public static function getSalesOperationBySKUAndDate(Mysql $mysql, $sku, $date, $dateto){

        $q = " SELECT 'VENTA' AS 'Tipo', th.pnid AS 'idheader', th.dscode AS 'partnercode', th.dsname AS 'partnername', th.ddcreated,
	   tsl.pnid AS 'idline', tsl.fniditem AS 'iditem', tsl.dscode AS 'itemcode' , tsl.dsname AS 'itemname',tsl.dnquantity, tsl.dnopenqty,
	   (tsl.dnquantity - tsl.dnopenqty) AS 'dif'
FROM thsales th INNER JOIN thsalesline tsl ON tsl.thidheader = th.pnid 
WHERE (CAST(ddcreated AS DATE) BETWEEN CAST('$date' AS DATE) AND CAST('$dateto' AS DATE)) AND tsl.dscode LIKE '$sku'  ";

        //echo "<br />getSalesOperationBySKUAndDate: ".$q;
        return $mysql->execute($q);
    }

    public static function getPurchaseOperationBySKUAndDate(Mysql $mysql, $sku, $date, $dateto){
        $q = "SELECT 'COMPRA' AS 'Tipo', th.pnid AS 'idheader', th.dscode AS 'partnercode', th.dsname AS 'partnername', th.ddcreated,
	   tsl.pnid AS 'idline', tsl.fniditem AS 'iditem', tsl.dscode AS 'itemcode' , tsl.dsname AS 'itemname', tsl.dnquantity, tsl.dnopenqty,
	   (tsl.dnquantity - tsl.dnopenqty) AS 'dif'
FROM thpurchase th INNER JOIN thpurchaseline tsl ON tsl.thidheader = th.pnid 
WHERE (CAST(ddcreated AS DATE) BETWEEN CAST('$date' AS DATE) AND CAST('$dateto' AS DATE)) AND tsl.dscode LIKE '$sku'  ";

        //echo "<br />getPurchaseOperationBySKUAndDate: ".$q;
        return $mysql->execute($q);
    }

    public static function getTransferOperationBySKUAndDate(Mysql $mysql, $sku, $date, $dateto){
        $q = "SELECT 'TRANSFERENCIA' AS 'Tipo', th.pnid AS 'idheader', '' AS 'partnercode', '' AS 'partnername', 
        th.ddate AS 'ddcreated',
	   tsl.pnid AS 'idline', tsl.fniditem AS 'iditem', tsl.dscode AS 'itemcode' , tsl.dsname AS 'itemname', tsl.dnquantity, tsl.dnopenqty AS 'dnopenqty',
	   (tsl.dnquantity - tsl.dnopenqty) AS 'dif'
FROM thtransfer th INNER JOIN thtransferline tsl ON tsl.fnidheader = th.pnid 
WHERE (CAST(ddate AS DATE) BETWEEN CAST('$date' AS DATE) AND CAST('$dateto' AS DATE)) AND tsl.dscode LIKE '$sku'  ";

        //echo "<br />getTransferOperationBySKUAndDate: ".$q;
        return $mysql->execute($q);
    }

    public static function getReturnOperationBySKUAndDate(Mysql $mysql, $sku, $date, $dateto){
        $q = "SELECT 'DEVOLU' AS 'Tipo', th.pnid AS 'idheader', th.dspartnercode AS 'partnercode', th.dspartnername AS 'partnername',
        th.dddate AS 'ddcreated',
	   tsl.pnid AS 'idline', tsl.fniditem AS 'iditem', tsl.dscode AS 'itemcode' , '' AS 'itemname', tsl.dnquantity, '' AS 'dnopenqty',
	   (tsl.dnquantity ) AS 'dif'
FROM threturn th INNER JOIN threturnline tsl ON tsl.fnidheader = th.pnid 
WHERE (CAST(dddate AS DATE) BETWEEN CAST('$date' AS DATE) AND CAST('$dateto' AS DATE)) AND tsl.dscode LIKE '$sku'  ";

        //echo "<br />getReturnOperationBySKUAndDate: ".$q;
        return $mysql->execute($q);
    }


}



?>
