<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 23/05/2019
 * Time: 12:46 PM
 */

class Returns
{

    public static function getAllByType(Mysql $mysql, $datefrom = null, $dateto=null, $filter = null)
    {//list all user by role
        $q = " SELECT tc.* ".
            " ,(SELECT SUM((tql.dnprice * tql.dnquantity) * 1.0) ".
            "  FROM threturnline tql ".
            "  WHERE tql.fnidheader = tc.pnid) AS 'total' ".
            " FROM threturn tc  ";
        $q .= " WHERE 1 = 1 ";
        if($datefrom != null && $dateto != null)
            $q .= " AND CAST(tc.dddate AS DATE) BETWEEN '$datefrom' AND '$dateto' ";
        if($filter != null)
            $q .= " AND (tc.dscode LIKE '%$filter%' OR tc.dsname LIKE '%$filter%')";
        $q .= "ORDER BY tc.pnid DESC "; //.Mysql::getQueryLimit($page);
        //$q = String::sanitize($q,true);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }


    public static function getRow(Mysql $mysql,$id) {
        $q="SELECT tu.*, tl.*, tu.dspartnername AS 'dsnamep', ".
            " tl.pnid AS 'pnidline', tl.dscode AS 'dsitemcode', ".
            " tl.dsrefline ".
            " FROM threturn tu INNER JOIN threturnline tl ON tl.fnidheader = tu.pnid ".
            "                 INNER JOIN tcpartner tcp ON tcp.pnid = tu.fnidpartner ".
            " WHERE tu.pnid='$id' ";
        //echo "<br />getRow:".$q;
        return $mysql->execute($q);
    }//function

    public static function getSerialByRow(Mysql $mysql,$id){

        $q = "SELECT tpl.pnid AS 'linea', tpl.fniditem AS 'iditem', tpl.dscode AS 'itemcode',".
            " tps.fnidserial AS 'serial', tps.fnidquantity AS 'quantity', ti.dsname AS 'itemname', ".
            " tpl.pnid AS 'idlinea', tpl.dsrefline, tpl.fnidheader ".
            " FROM threturn tp INNER JOIN threturnline tpl ON tpl.fnidheader = tp.pnid ".
            "                    INNER JOIN threturnserial tps ON tps.fnidline = tpl.pnid AND tps.fnidheader = tp.pnid ".
            "                   INNER JOIN tcitem ti ON ti.pnid = tpl.fniditem ".
            " WHERE tp.pnid = $id ";

        return $mysql->execute($q);
    }

    public static function getReturnByIdOV($mysql, $id, $idpartner){

        $q="SELECT tu.*, tl.*, tu.dspartnername AS 'dsnamep', ".
            " tl.pnid AS 'pnidline', tl.dscode AS 'dsitemcode', ".
            " (SELECT tcitem.dsname FROM tcitem tcitem WHERE tcitem.dscode = tl.dscode ) AS 'dsitemname', ".
            " tl.dsrefline, tl.dnquantity ".
            " FROM threturn tu INNER JOIN threturnline tl ON tl.fnidheader = tu.pnid ".
            " WHERE tu.fnidpartner = $idpartner AND tl.dsrefline IN (SELECT dsrefline FROM thsalesline WHERE thidheader = $id) ";
        //echo "<br />getReturnByIdOV: ".$q;
        return $mysql->execute($q);

    }

    /*reporte de devoluciones*/
    public static function getOperationReturns(Mysql $mysql, $from, $to, $filter){

      $q = "SELECT ths.pnid, ths.dsreference, ths.dspartnercode AS 'dscode',
                   ths.dddate, ths.dspartnername AS 'dsname',
                     ti.dsname AS 'itemname', ti.dscode AS 'itemcode',
                      1 AS 'dnquantity',
                      tsl.dnprice, (tsl.dnprice *  1) AS 'total',
                     tsser.fnidserial, sl.thidheader AS 'idov', sl.pnid AS 'idlineov', tsl.dsrefline
              FROM threturn ths INNER JOIN threturnline tsl  ON tsl.fnidheader = ths.pnid
                                INNER JOIN tcitem ti ON ti.pnid = tsl.fniditem
                                INNER JOIN threturnserial tsser ON tsser.fnidline = tsl.pnid
                                LEFT JOIN thsalesline sl ON sl.pnid = tsl.fnidlineori 
              WHERE 1 = 1 " ;
      if($from != null && $to != null)
          $q .= " AND ( CAST(ths.dddate AS DATE) BETWEEN '$from' AND '$to') ";

      if($filter != null)
          $q .= " AND (ths.dspartnercode LIKE ('%".$filter."%') OR ths.dspartnername LIKE ('%".$filter."%'))";

      //echo "<br />getOperationReturns: ".$q;
      return $mysql->execute($q);

    }//function getOperationReturns


    /* inventory return */

    /**/
    public static function addReturnRow(Mysql $mysql, $args){

        //print_r($args);
        $debug = 1;

        $mysql->begin();

        /*insert into table returns*/

        $q = " INSERT INTO threturn VALUES (NULL,".$args['_pnidcliente'].",'".$args['_cliente']."',".
            " '".$args['_ref']."','".$args['_clientename']."',NOW(),'".$args['_comments']."')";
        if($debug)
            echo "<br /> insert threturn: ".$q;

        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }

        $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
        $id = $mysql->execute($q);

        for($i =0 ; $i<count($args['_codea']); $i++){
          //print_r($args['_idline']);

            $q = "SELECT dnprice FROM thsalesline WHERE pnid = ".$args['_idline'][$i];
            //echo "<br />price: ".$q;
            $price = $mysql->execute($q);
            //echo "<br />price: ".$price[0]->dnprice;

            $q = "INSERT INTO threturnline VALUES(null,".$id[0]->lastid.",".$args['_fniditem'][$i].
                 ",'".$args['_codea'][$i]."',1,".
                 $price[0]->dnprice.",'".$args['_refline'][$i].
                 "',1,".$args['_idline'][$i].")";
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
                //exit;
            }else{
                $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
                $idline = $mysql->execute($q);

                $q = "INSERT INTO threturnserial VALUES(null,".$idline[0]->lastid.",'".$args['_seriea'][$i]."',".
                     "1,".$id[0]->lastid.")";
                if(!$mysql->update($q)) {
                    $mysql->rollback();
                    return false;
                }//if update
            }//insert into threturnserial

        }//for lines



        /*end insert into table returns*/

        for($i=0; $i<count($args['_codea']); $i++){

            if($args['_seriea'][$i] != ""){
                //thsalesserial
                $q = "UPDATE thsalesserial SET fnidquantity = 0 ".
                    " WHERE fnidheader = ".$args['_idheader'][$i]." AND fnidline = ".$args['_idline'][$i].
                    " AND fnidserial = '".$args['_seriea'][$i]."' ";
                if($debug)
                    echo "<br />0. UPDATE thsalesserial: ".$q;

                if(!$mysql->update($q)){
                    $mysql->rollback();
                    return false;
                }
            }

            //trcost
            $q = " SELECT trg.fniditem, trg.ddquantity, tc.dscode, tc.dddate, tc.ddqtyinv,
                	  (tc.ddqtyinv * tc.ddcostinv) AS 'costnew',
                       CASE WHEN tc.ddcostinv IS NULL THEN 0 ELSE tc.ddcostinv END AS 'costact',
                       CASE WHEN tc.ddqtyinv IS NULL THEN 0 ELSE tc.ddqtyinv END AS 'stockact'
                 FROM trinventorygral trg LEFT JOIN trcost tc ON tc.fniditem = trg.fniditem
                 WHERE tc.fniditem = ".$args['_fniditem'][$i];

            $q.=" AND tc.dddate = (SELECT dddate FROM trcost WHERE fniditem = '".$args['_fniditem'][$i]."' ORDER BY pnid DESC LIMIT 1 ) ".
                " ORDER BY tc.pnid DESC LIMIT 1 ";
            if($debug)
                echo "<br />1. to get last cost:".$q;
            $tocost = $mysql->execute($q);

            if(count($tocost)>0){
                $qtyant = $tocost[0]->stockact;
                $costant = $tocost[0]->costact;
                //$costnewtmp = $tocost[0]->costnew;
            }else{
                $qtyant = 0;
                $costant = 0.0;
                //$costnewtmp = $args['_price'][$i] * $args['_qtyr'][$i];
            }



            $qtynew = $qtyant + 1;
            $costnew = (($costant * $qtynew)) / ($qtynew);


            $q = "INSERT INTO trcost VALUES(null,".$args['_fniditem'][$i].",'".$args['_codea'][$i].
                "',".$qtynew.",".round($costnew,2,PHP_ROUND_HALF_UP).",".
                $costant.",".$qtyant.",0,NOW(),1)";
            if($debug)
                echo "<br />2. trcost: ".$q;

            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
            //END trcost

            //trinventory
            $q = " INSERT INTO trinventory(pnid,fnidware,fniditem,ddquantity,dsdirection, dsorigen, dslinebase, dscomments) ".
                " VALUES (NULL,1,".$args['_fniditem'][$i].",1,1,0,0,'DEV')";
            if($debug)
                echo "<br />3. query trinventory: ".$q;

            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }


            //trinventory gral
            $q = " SELECT * FROM trinventorygral ".
                " WHERE fniditem = ".$args['_fniditem'][$i]." AND fnidware = ".$args['_fnidware'][$i];

            if($debug)
                echo "<br />4. query trinventorygral: ".$q;

            $row = $mysql->execute($q);
            if(count($row)>0) {
                $q = "UPDATE trinventorygral SET ddquantity = ddquantity + 1 ".
                    " WHERE fniditem = " . $args['_fniditem'][$i]." AND fnidware = ".$args['_fnidware'][$i];
            }

            if($debug)
                echo "<br />5. query insert o update trinventorygral:".$q;

            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }


            //trinventory serial
            if($args['_seriea'][$i] != "") { //only for serial items
                $q = " UPDATE trinventoryserial SET dnquantity  = 1 " .
                    " WHERE fniditem = " . $args['_fniditem'][$i] . " AND dsserial LIKE '" . $args['_seriea'][$i] . "' " .
                    " AND dnquantity = -1 ";
                if ($debug)
                    echo "<br /> 6. query update trinventoryserial: " . $q;

                if (!$mysql->update($q)) {
                    $mysql->rollback();
                    return false;
                }

                $q = " UPDATE thpurchaseserial SET fnidquantity  = 1 " .
                    " WHERE fnidserial LIKE '" . $args['_seriea'][$i] . "' ";
                if ($debug)
                    echo "<br /> 7. query update thpurchaserial: " . $q;

                if (!$mysql->update($q)) {
                    $mysql->rollback();
                    return false;
                }
            }//only for serial items

        }//for

        $mysql->commit();
        return true;
    }
    /* end inventory return*/

    public static function getHistoryByIMEI(Mysql $mysql,$imei){

        $q = "SELECT th.pnid, tp.dscode, tp.dsname, th.dddate, tl.pnid AS 'idline', 
                     tl.fniditem, tl.dscode AS 'itemcode', ti.dsname AS 'itemname', ts.fnidserial, tl.fnidlineori                     
              FROM threturn th INNER JOIN threturnline tl ON tl.fnidheader = th.pnid 
				                 INNER JOIN threturnserial ts ON ts.fnidline = tl.pnid     
				                 INNER JOIN tcpartner tp ON tp.pnid = th.fnidpartner   
				                 LEFT JOIN tcitem ti ON ti.pnid = tl.fniditem                            			                 
              WHERE ts.fnidserial LIKE '$imei' ";
        //echo "<br /> getHistoryByIMEI:".$q;
        return $mysql->execute($q);
    }

}
