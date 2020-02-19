<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 14/03/2019
 * Time: 01:47 PM
 */

class Collection
{

  public static function getPartnerCreditPaid(Mysql $mysql, $idpartner=null){

           $q = "SELECT tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday, ts.pnid AS 'idov', ".
         "      ts.ddcreated, ts.dsreference, ts.dsstatuscredit, SUM(tsl.dnquantity * tsl.dnprice) AS 'facturado',";

     $q.= " ( SELECT SUM(trp.dsamount) ".
          "   FROM trpayment trp INNER JOIN thsales ts2 ON ts2.pnid = trp.fnidsales
          WHERE ts2.fnidpartner = ts.fnidpartner AND (ts2.dsstatuscredit = 3 AND ts2.dsstatus = 3) ".
          "         AND trp.fnidstatus = 1
       ) AS 'debit',";

      if($idpartner!=null)
          $q.= "	   (SELECT tdel.dddate FROM thdelivery tdel WHERE tdel.dscomment = ts.pnid) AS 'deliverydate',  ";

     $q.= "  '' AS 'dum' ".
          " FROM tcpartner tc INNER JOIN thsales ts ON ts.fnidpartner = tc.pnid ".
          "			        INNER JOIN thsalesline tsl ON tsl.thidheader = ts.pnid  ".
          " WHERE 1 = 1 AND (ts.dsstatuscredit = 3 AND ts.dsstatus = 3) ";
     if($idpartner != null)
         $q .= " AND tc.pnid = $idpartner ";

     $q .=" GROUP BY tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday";

      if($idpartner != null)
          $q.=" ,ts.pnid, ts.ddcreated, ts.dsreference, ts.dsstatuscredit ";

      $q .= " ORDER BY tc.dsname ASC ";

      return $mysql->execute($q);

  }

  public static function getPartnerCreditCreditY2(Mysql $mysql, $idpartner=null){

    $q = " SELECT A.*
            FROM ( ";
     $q .=  " SELECT  tc.pnid, tc.dscode, tc.dsname, SUM(CASE WHEN tb.dstype = 'VENTA' THEN tb.dnamount ELSE 0 END) AS 'facturado',
                      SUM(CASE WHEN tb.dstype = 'PAGO' THEN tb.dnamount ELSE 0 END) AS'debit',
                      tc.dncreditday, tb.dddate AS 'ddcreated',  tc.dddiscountperc ";


     if($idpartner!=null)
         $q.= "	, tb.dstype, tb.fniddocument AS 'idov',
                (SELECT tdel.dddate FROM thdelivery tdel WHERE tdel.dscomment = tb.fniddocument ORDER BY tdel.pnid DESC LIMIT 1) AS 'deliverydate',
                tb.pnid AS 'idbalance', tb.fniddelivery, tb.dscomments AS 'dscomments'
                , CASE WHEN tb.dstype = 'PAGO' THEN
                 (SELECT fnidsales FROM trpayment WHERE pnid = tb.fniddocument)
                 ELSE tb.fniddocument END AS 'idovall' ";

    $q.= " FROM tcpartner tc INNER JOIN tcbalance tb ON tb.fnidpartner = tc.pnid	".
        " WHERE tc.dscredit LIKE 'Y'   ";

    if($idpartner != null)
        $q .= " AND tc.pnid = $idpartner ";

    $q .=" GROUP BY tc.pnid, tc.dscode, tc.dsname, tc.dncreditday";

     if($idpartner != null)
         $q.=" ,tb.fniddocument, tb.dddate, tb.dstype, tb.pnid, tb.fniddelivery, tb.dscomments  ";


   if($idpartner == null)
     $q .= " ORDER BY tc.dsname ASC ";

     $q.= ") A ORDER BY A.ddcreated ASC";


     /*if($idpartner == 67)
       echo "<br />getPartnerCreditCreditY2: $q";
     if($idpartner == null)
     echo "<br />getPartnerCreditCreditY2 gral: $q";
*/
     return $mysql->execute($q);
  }

  public static function getRowCollect(Mysql $mysql,$id=null,$iddelivery = null) {
       // query used in paymentgral2.php for group detail to director
       if($iddelivery==null){
        $q = "SELECT tu.fnidpartner, tu.dsstatus AS 'dsstatush', tu.dsname AS 'dsnamep', tu.dscode AS 'dscodep',
                     tl.dscode AS 'dsitemcode', tl.dsname AS 'dsitemname', SUM(tl.dnquantity - tl.dnopenqty) AS 'dnquantity', tl.dnprice,
                     tl.fniditem, '' AS 'dsrefline', tp.dddiscountperc
              FROM thsales tu INNER JOIN thsalesline tl ON tl.thidheader = tu.pnid
                              INNER JOIN tcpartner tp ON tp.pnid =  tu.fnidpartner
              WHERE tu.pnid  = $id AND (tl.dnquantity - tl.dnopenqty) > 0
              GROUP BY tu.fnidpartner, tu.dsstatus, tu.dsname, tu.dscode,
                       tl.dscode, tl.dsname, tl.dnprice, tl.fniditem, tp.dddiscountperc";
     }elseif($iddelivery != null){
       $q = " SELECT tu.fnidpartner, tu.dsstatus AS 'dsstatush', tu.dsname AS 'dsnamep', tu.dscode AS 'dscodep',
                    tl.dscode AS 'dsitemcode', tl.dsname AS 'dsitemname',
                    CASE WHEN SUM(tsel.fnidquantity) = 0 THEN SUM(tl.dnquantity - tl.dnopenqty) ELSE SUM(tsel.fnidquantity) END AS 'dnquantity',
                    tl.dnprice,
                    tl.fniditem, '' AS 'dsrefline', tp.dddiscountperc
             FROM thsales tu INNER JOIN thsalesline tl ON tl.thidheader = tu.pnid
                             INNER JOIN thsalesserial tsel ON tsel.fnidheader = tu.pnid AND tsel.fnidline = tl.pnid
                             INNER JOIN tcpartner tp ON tp.pnid =  tu.fnidpartner
             WHERE tu.pnid  = $id AND (tl.dnquantity - tl.dnopenqty) > 0 AND tsel.fnidheaderdelivery = $iddelivery
             GROUP BY tu.fnidpartner, tu.dsstatus, tu.dsname, tu.dscode,
                      tl.dscode, tl.dsname, tl.dnprice, tl.fniditem, tp.dddiscountperc";
     }
      //echo "<br />getRowCollect:".$q;
      return $mysql->execute($q);
  }//function



}



?>
