<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 21/11/2018
 * Time: 10:58 PM
 */

class Sales
{
    public static function getAllByType(Mysql $mysql, $datefrom = null, $dateto=null, $filter = null, $iduser = null)
    {//list all user by role
        $q = " SELECT tc.*, 'VENTA' AS 'dstype', tcs.pnid AS 'idstatus', tcs.dsname AS 'statusname' ".
            " ,(SELECT SUM((tsl.dnprice * tsl.dnquantity) * 1.0) ".
            "   FROM  thsalesline tsl ".
            "   WHERE tsl.thidheader = tc.pnid) AS 'total' ".
            " FROM thsales tc INNER JOIN tcstatus tcs ON tcs.pnid = tc.dsstatus ";
        $q .= " WHERE 1 = 1 AND tc.dsstatus <> 6 ";
        if($datefrom != null && $dateto != null)
            $q .= " AND CAST(tc.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto' ";
        else
            $q .= " AND (CAST(tc.ddcreated AS DATE) BETWEEN DATE_SUB(CURDATE(), INTERVAL 5 DAY) AND CURDATE())";
        //else
        //    $q .= " AND CAST(tc.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto' ";

        if($filter != null)
            $q .= " AND (tc.dscode LIKE '%$filter%' OR tc.dsname LIKE '%$filter%')";

        if($iduser != null)
            $q .= " AND tc.fnidcreator = ".$iduser;

        $q .= " ORDER BY tc.pnid DESC "; //.Mysql::getQueryLimit($page);
        //$q = String::sanitize($q,true);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getAllByStatus(Mysql $mysql, $datefrom = null, $dateto=null, $filter = null, $status = 3)
    {//list all user by role
        $q = " SELECT tc.*, 'VENTA' AS 'dstype' FROM thsales tc ";
        $q .= " WHERE 1 = 1 AND tc.dsstatus = ".$status;
        if($datefrom != null && $dateto != null)
            $q .= " AND CAST(tc.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto' ";
        if($filter != null)
            $q .= " AND (tc.dscode LIKE '%$filter%' OR tc.dsname LIKE '%$filter%')";
        $q .= "ORDER BY tc.pnid DESC ";
        //.Mysql::getQueryLimit($page);
        //$q = String::sanitize($q,true);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getRow(Mysql $mysql,$id) {
        $q="SELECT tu.*, tl.*, tu.dsstatus AS 'dsstatush', tu.dsname AS 'dsnamep', ".
            " tl.pnid AS 'pnidline', tl.dscode AS 'dsitemcode', tl.dsname AS 'dsitemname', ".
            " tl.dsstatus AS 'dsstatusl', tcp.dbvalidateguia, tl.dsrefline, tci.dsupc, tci.dsupc2, ".
            " tci.dsserial AS 'manser' ".
            " FROM thsales tu INNER JOIN thsalesline tl ON tl.thidheader = tu.pnid ".
            "                 INNER JOIN tcpartner tcp ON tcp.pnid = tu.fnidpartner ".
            "                 INNER JOIN tcitem tci ON tci.pnid = tl.fniditem".
            " WHERE tu.pnid='$id' ";
        //echo $q;
        return $mysql->execute($q);
    }//function

    public static function getRowCollect(Mysql $mysql,$id) {
        /*$q="SELECT tu.*, tl.*, tu.dsstatus AS 'dsstatush', tu.dsname AS 'dsnamep', tu.dscode AS 'dscodep', ".
            " tl.pnid AS 'pnidline', tl.dscode AS 'dsitemcode', tl.dsname AS 'dsitemname', ".
            " tl.dsstatus AS 'dsstatusl', tcp.dbvalidateguia, tl.dsrefline,  ".
            " (SELECT count(*) FROM thsalesserial tsl ".
            "  WHERE tsl.fnidline = tl.pnid AND tsl.fnidheader = tu.pnid AND tsl.fnidquantity = 0 ) AS 'Dev'   ".
            " FROM thsales tu INNER JOIN thsalesline tl ON tl.thidheader = tu.pnid ".
            "                 INNER JOIN tcpartner tcp ON tcp.pnid = tu.fnidpartner ".
            " WHERE tu.pnid='$id' ";*/
        /*$q =" SELECT tu.dsstatus AS 'dsstatush', tu.dsname AS 'dsnamep', tu.dscode AS 'dscodep', ".
            "       tl.dscode AS 'dsitemcode', tl.dsname AS '', SUM(tl.dnquantity) AS 'dnquantity', tl.dnprice, ".
            "       '' AS 'pnidline', ".
            "      '' AS 'Dev' ".
          " FROM thsalesline tl INNER JOIN thsales tu ON tu.pnid = tl.thidheader ".
          " WHERE tu.pnid = '$id' ".
          " GROUP BY tl.dscode, tl.dsname, tl.dnprice ";
          */
        $q = "SELECT tu.fnidpartner, tu.dsstatus AS 'dsstatush', tu.dsname AS 'dsnamep', tu.dscode AS 'dscodep',
                       tl.dscode AS 'dsitemcode', tl.dsname AS 'dsitemname', SUM(tl.dnquantity - tl.dnopenqty) AS 'dnquantity', tl.dnprice,
                       tl.fniditem, tl.dsrefline
                FROM thsales tu INNER JOIN thsalesline tl ON tl.thidheader = tu.pnid
                WHERE tu.pnid  = $id AND (tl.dnquantity - tl.dnopenqty) > 0
                GROUP BY tu.fnidpartner, tu.dsstatus, tu.dsname, tu.dscode,
                         tl.dscode, tl.dsname, tl.dnprice, tl.fniditem, tl.dsrefline
                 ";
        //echo "<br />getRowCollect:".$q;
        return $mysql->execute($q);
    }//function

    public static function getSerialByRow(Mysql $mysql,$id){

        $q = "SELECT tpl.pnid AS 'linea', tpl.fniditem AS 'iditem', tpl.dscode AS 'itemcode',".
            " tps.fnidserial AS 'serial', tps.fnidquantity AS 'quantity' ".
            " FROM thsales tp INNER JOIN thsalesline tpl ON tpl.thidheader = tp.pnid ".
            "                    INNER JOIN thsalesserial tps ON tps.fnidline = tpl.pnid AND tps.fnidheader = tp.pnid ".
            " WHERE tp.pnid = $id ";

        return $mysql->execute($q);
    }

    public static function setRow(Mysql $mysql,$args) {
        //print_r($args);
        $mysql->begin();
        $q="INSERT INTO thsales(pnid,fnidpartner,dscode,dsname,fnidcreator,".
            " dsreference,dsstatus,ddcreated,ddupdate,dddocdate) " .
            "VALUES (null,".$args['_pnidcliente'].",'".$args['_cliente']."','".$args['_clientename']."',".$args['idcreator'].", ".
            " '".$args['_referencia']."','".$args['_status']."',NOW(),NOW(),NOW() )";
        //echo "<br />query header:".$q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }else{
            $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
            //echo "<br />".$q;
            $id = $mysql->execute($q);
            //var_dump($id);
            //print_r($args);

            for($i=0; $i<count($args['_art']); $i++){
                $q = "INSERT INTO thsalesline(pnid,thidheader, fniditem, dscode, dsname,".
                    " fnidware,dnquantity,dniva,dnprice) VALUES( ".
                    " null,".$id[0]->lastid.",".$args['_iditem'][$i].",'".$args['_art'][$i]."','".$args['_desc'][$i]."',".
                    $args['_whscode'][$i].",".$args['_qty'][$i].",".$args['_iva'][$i].",".$args['_price'][$i].")";
                //echo "<br />".$q;
                //echo "<br />query linea:".$q;
                if($mysql->update($q)) {
                    $mysql->commit();
                    return true;

                    //$q = "SELECT LAST_INSERT_ID() AS 'lastid'";
                    $idline = $mysql->execute($q);

                    //echo "<br />****** SERIAL1";
                    //print_r($args['_linea']);
                    //echo "<br />****** SERIAL2";
                    //print_r($args['_nl']);

                    /* guarda lineas de series * /
                    for ($j = 0; $j < count($args['_linea']); $j++) {
                        if ($args['_linea'][$j] == $args['_nl'][$i]) {
                            $q = "INSERT INTO thpurchaseserial(pnid, fnidline,fnidserial,fnidquantity, fnidheader) VALUES " .
                                " (null," . $idline[0]->lastid . ",'" . $args['_seriea'][$i] . "',1," . $id[0]->lastid . ")";
                            echo "<br />query serial:".$q;
                            $mysql->update($q);
                        }//if
                    }//for
                    */
                }else{//if update lines
                    $mysql->rollback();
                    return false;
                }//else
            }//for

            $mysql->commit();
            return true;
        }//

    }//function

    public static function updateRowDelivery(Mysql $mysql, $args){

        for($i=0; $i<count($args['_art']); $i++){
            $q = " UPDATE thsalesline SET dsstatus = '".$args['_statusl'][$i].
                "', dspaqueteria = '".$args['_paq'][$i]."', dscomentariol = '".$args['_coml'][$i]."' ";
            $q .= " WHERE pnid = ".$args['_idline'][$i];
            //echo "<br />".$q;
            $mysql->update($q);
        }
        return true;
    }

    public static function updateRow(Mysql $mysql, $args){

        $flagheader = false;
        $flagupdatecancel = false;
        //print_r($args);

        for($i=0; $i<count($args['_idline']); $i++){
            if(isset($args['_aut_'.$args['_idline'][$i]])) {
                if ($args['_qty'][$i] > 0)
                    $flagheader = true;

                if($args['_statuspart'][$i] == "CANCEL"){
                    $q = "UPDATE thsalesline SET dsstatus = 'CANCEL' ".
                        " WHERE pnid = ".$args['_idline'][$i];
                    //echo "<br />UPDATE to cancel:".$q;
                    $mysql->update($q);
                    $flagupdatecancel = true;
                }else{
                    $q = "UPDATE thsalesline SET dspaqueteria = '".$args['_paq'][$i]."', dscomentariol = '".$args['_coml'][$i]."'".
                        " WHERE pnid = ".$args['_idline'][$i];
                    $mysql->update($q);
                }
            }// if _aut_args
        }

        $q = "UPDATE thsales SET dsstatus = ".$args['_status']." WHERE pnid = ".$args['id'];
        return $mysql->update($q);

    }

    public static function setRowsSerialSales(Mysql $mysql, $args){
        //echo "<br />function setRowsSerialSales<br />";
        //var_dump($args);
        //echo "<br />count _art:".count($args['_qty']);
        $mysql->begin();

        // log de inserciones con series */
        try{
            $filename = "../uploads/logInventoryDelivery/".$args['id']."_".$args['idcreator']."_".date("d_m_Y_H_i_s").".csv";
            $towrite = "";
            for ($j = 0; $j < count($args['_linea']); $j++) {
                //docnum, linenum, itemid, itemcode, quantity, IMEI $args['_seriea'][$j]
                $towrite .= "'".$args['id']."',".$args['_linea'][$j].",".$args['_idarticlea'][$j].
                    ",'".$args['_codea'][$j]."','".strtoupper($args['_seriea'][$j])."'"."\r\n";

            }
            if (!$handle = fopen($filename, "w")) {
                echo "El archivo no se puede crear";
                //exit;
            }
            if (fwrite($handle, utf8_decode($towrite)) === false){
                echo "El archivo no se puede escribir";
                //exit;
            }
            fclose($handle);
        }catch(customException $e){
            echo $e->errorMessage();
        }
        //end log de inserciones con series

        /*insert into table for id delivery*/
        $q = "INSERT INTO thdelivery VALUES (null,current_date(), '".$args['id']."'); ";
        $mysql->update($q);

        $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
        $iddelivery = $mysql->execute($q);

        $amount = 0.0;
        for($i=0; $i<count($args['_art']); $i++){

            /*if qtyr > 0 to partials*/
            if($args['_qtyr'][$i]>0) {
                $q = " INSERT INTO trinventory(pnid,fnidware,fniditem,ddquantity,dsdirection, dsorigen, dslinebase, dscomments) " .
                    " SELECT NULL, fnidware, fniditem, ".$args['_qtyr'][$i].", -1," . $args['id'] . ",".
                    " '" . $args['_idline'][$i] . "','".$iddelivery[0]->lastid."' " .
                    " FROM thsalesline " .
                    " WHERE thidheader = " . $args['id'] . " AND pnid = " . $args['_idline'][$i];
                //echo "<br />query trinventory".$q;
                if (!$mysql->update($q)) {
                    $mysql->rollback();
                    return false;
                }

                $q = "UPDATE trinventorygral SET ddquantity = (ddquantity - " . $args['_qtyr'][$i] . ") WHERE fniditem = " . $args['_iditem'][$i]." AND fnidware = ".$args['_whscode'][$i];
                if (!$mysql->update($q)) {
                    $mysql->rollback();
                    return false;
                }

                /************/
                /* se insertara la cantidad nueva cuando se actualiza el inventario*/

                try {

                    $q = " SELECT trg.fniditem, trg.ddquantity, tc.dscode, tc.dddate, " . $args['_qtyr'][$i] . " AS dnquantity,
                	   pl.dnprice,
                       CASE WHEN tc.ddcostinv IS NULL THEN 0 ELSE tc.ddcostinv END AS 'costact',
                       CASE WHEN tc.ddqtyinv IS NULL THEN 0 ELSE tc.ddqtyinv END AS 'stockact'
                 FROM trinventorygral trg LEFT JOIN trcost tc ON tc.fniditem = trg.fniditem
                 						 INNER JOIN thsalesline pl ON pl.fniditem = trg.fniditem
                 WHERE pl.thidheader =  " . $args['id'] . " AND pl.fniditem = " . $args['_iditem'][$i];
                    $q .= " AND tc.dddate = (SELECT dddate FROM trcost WHERE fniditem = pl.fniditem ORDER BY pnid DESC LIMIT 1 )";

                    //echo "<br />1. to get last cost:".$q;

                    $tocost = $mysql->execute($q);

                    if (count($tocost) > 0) {
                        $qtyant = $tocost[0]->stockact;
                        $costant = $tocost[0]->costact;
                    } else {
                        $qtyant = 0;
                        $costant = 0.0;
                    }

                    $qtynew = $qtyant - $args['_qtyr'][$i];
                    $costnew = (($costant * $qtynew)) / ($qtynew);


                    $q = "INSERT INTO trcost VALUES(null," . $args['_iditem'][$i] . ",'" . $args['_art'][$i] .
                        "'," . $qtynew . "," . round($costnew, 2, PHP_ROUND_HALF_UP) . "," .
                        $costant . "," . $qtyant . "," . $args['id'] . ",NOW(),-1)";
                    //echo "<br />2. trcost: ".$q;
                    if (!$mysql->update($q)) {
                        $mysql->rollback();
                        return false;
                    }
                }catch(customException $e){
                    //echo $e->errorMessage();
                }

                /* fin se insertara la cantidad nueva cuando se actualiza el inventario*/
                /************/


                /* update to open qty manager partials*/
                $q = "UPDATE  thsalesline SET dnopenqty = (dnopenqty - ".$args['_qtyr'][$i].")".
                    " WHERE pnid = ".$args['_idline'][$i]." AND thidheader = ".$args['id'];
                //echo "<br />update openqty: ".$q;
                if(!$mysql->update($q)){
                    $mysql->rollback();
                    return false;
                }
                /* END update to open qty manager partials*/

                /*Code to set in balance all arts receive*/
                //echo "<br />to calculdate amount: ".$args['_qtyr'][$i]."|".$args['_price'][$i];
                $args['_price'][$i] = str_replace(",","",$args['_price'][$i]);
                //echo "<br />to calculdate amount: ".$args['_qtyr'][$i]."|".$args['_price'][$i];
                $amount = $amount + ($args['_qtyr'][$i] * $args['_price'][$i]);
                /*end code to set in balance all arts receive*/

            }//if qtyr > 0

        }

        if($_POST['dispatch']=="confirmdelivery"){
            /*update confirm delivery*/
            for ($j = 0; $j < count($args['_idline']); $j++) {
                $q = " UPDATE thsalesline SET dsguiac = '" . $args['_guiac'][$j] . "', dsguia = '".$args['_guiac'][$j]."' dsstatusguia = 'E'  " .
                    " WHERE pnid = ".$args['_idline'][$j];
                //echo "<br />query updateConfirmDeliveryRow:".$q;
                $mysql->update($q);
            }
        }
        /*end confirm delivery*/

        //echo "<br />count _art:".count($args['_qty']);
        //print_r($args['_whscode']);
        for ($j = 0; $j < count($args['_linea']); $j++) {
            //if ($args['_linea'][$j] == $args['_nl'][$i]) {

            $q = "SELECT pnid FROM trinventory WHERE dsorigen = ".$args['id']." AND dslinebase = ".$args['_linea'][$j];
            //echo "<br />consulta trinventory".$q;
            $idline = $mysql->execute($q);

            $q = "INSERT INTO thsalesserial(pnid, fnidline,fnidserial,fnidquantity, fnidheader, fnidheaderdelivery) VALUES " .
                " (null," . $args['_linea'][$j] . ",'" .strtoupper( $args['_seriea'][$j]). "',1," . $args['id'].",".$iddelivery[0]->lastid.")";
            //echo "<br />query purchase serial:".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            $q = "INSERT INTO trinventoryserial(pnid, fnidware,fniditem,dsserial, fnidinventory, dnquantity) VALUES " .
                " (null, ".$args['_whscodel'][$j].", " . $args['_idarticlea'][$j] . ",'" .strtoupper($args['_seriea'][$j]) . "'," . $idline[0]->pnid.",-1)";
            //echo "<br />query trinventory serial:".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            //actualiza la linea serie en orden de compra
            $q = "UPDATE thpurchaseserial SET `fnidquantity` = -1 WHERE fnidserial LIKE '".strtoupper($args['_seriea'][$j])."'";
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            //actualiza la linea serie en inventario
            $q = "UPDATE  trinventoryserial SET `dnquantity` = 0 ".
                " WHERE dsserial LIKE '".strtoupper($args['_seriea'][$j])."' AND fniditem = ".$args['_idarticlea'][$j];
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

        }//for

        /* code to insert into tcbalance the last delivery */
        try{

            /*
            $q = " INSERT INTO tcbalance (pnid, dstype, fniddocument, dddate, dnamount, dscomments, fnidpartner, fniddelivery)
                    SELECT NULL,'VENTA', tsel.thidheader, NOW(),  SUM(tsel.dnprice * tss.fnidquantity), '', th.fnidpartner, tdel.pnid
                FROM thdelivery tdel INNER JOIN thsalesserial tss ON tss.fnidheaderdelivery = tdel.pnid
                                     INNER JOIN thsalesline tsel ON tsel.pnid = tss.fnidline AND tss.fnidheader = tsel.thidheader
                                     INNER JOIN thsales th ON th.pnid = tsel.thidheader
                WHERE tdel.pnid = ".$iddelivery[0]->lastid."
                 GROUP BY tsel.thidheader, th.fnidpartner, tdel.pnid ";
              */
            $q = " INSERT INTO tcbalance (pnid, dstype, fniddocument, dddate, dnamount, dscomments, fnidpartner, fniddelivery)
                  SELECT NULL,'VENTA', th.pnid, NOW(),  $amount, '', th.fnidpartner, tdel.pnid
              FROM thdelivery tdel INNER JOIN thsales th ON th.pnid = tdel.dscomment
              WHERE tdel.pnid = ".$iddelivery[0]->lastid."
               GROUP BY th.pnid, th.fnidpartner, tdel.pnid ";

            //echo "<br /> tcbalance antes de guardar en archivo:".$q;
            $mysql->update($q);

            $filename = "../uploads/logInventoryBalance/".$args['id']."_".$iddelivery[0]->lastid."_".$args['idcreator']."_".date("d_m_Y_H_i_s").".csv";
            $towrite = $q;

            if (!$handle = fopen($filename, "w")) {
                echo "El archivo no se puede crear";
                //exit;
            }
            if (fwrite($handle, utf8_decode($towrite)) === false){
                echo "El archivo no se puede escribir";
                //exit;
            }
            fclose($handle);
        }catch(customException $e){
            echo $e->errorMessage();
        }
        /* end code to insert into tcbalance the last delivery*/

        $q = "UPDATE thsales SET dsstatus = 3 WHERE pnid = ".$args['id'];
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }

        /*code to insert into status payment*/
        $q = "SELECT * FROM trsalesstatus WHERE fnidsales = ".$args['id'];
        $row = $mysql->execute($q);

        if(count($row) > 0){
            $q = "UPDATE trsalesstatus  SET dsreference = '', dsstatus = 'PENDIENTE' WHERE fnidsales = ".$args['id'];
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
        }else{
            $q = " INSERT INTO trsalesstatus VALUES(null,'','PENDIENTE', ".$args['id'].")";
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
        }
        /*end to insert into status payment*/
        $mysql->commit();
        return true;
    }

    public static function updateConfirmDeliveryRow(Mysql $mysql, $args)
    {

        //print_r($args);
        for ($j = 0; $j < count($args['_idline']); $j++) {
            $q = " UPDATE thsalesline SET dsguiac = '".$args['_guiac'][$j]."', dsguia = '".$args['_guiac'][$j]."', dsstatusguia = 'E'
                   WHERE pnid = ".$args['_idline'][$j];
            //echo "<br />query updateConfirmDeliveryRow:".$q;
            $mysql->update($q);
        }

        return true;

    }

    public static function saveByFile($mysql, $args){

        $count = 0;
        $refgral = $args['iduser']."_".date("Y-m-d_H-i-s");

        $archivo = fopen("../uploads/".$args['_filenamefinal'], "r");
        //$mysql->begin();

        $arrayh = array();
        $arrayl = array();
        $pedidoc = "";

        $datos = fgetcsv($archivo, ",");
        $datos = fgetcsv($archivo, ",");

        //cambio para actualizar masivamente refgral y cliente
        //$pedidoc = $datos[3];
        $pedidoc = $datos[7];
        $pedidoline = $datos[3];

        fclose($archivo);


        $counth = 0;
        $item = Item::getRowByCode($mysql, $datos[0]);
        $cliente = Partner::getRowByCode($mysql, $datos[7]);
        //print_r($cliente);

        $arrayh[$counth]['pedido'] = $datos[3];
        $arrayh[$counth]['idpartner'] = $cliente[0]->pnid;
        $arrayh[$counth]['dscode'] = $cliente[0]->dscode;
        $arrayh[$counth]['dsname'] = $cliente[0]->dsname;
        $arrayh[$counth]['idcreator'] = $args['iduser'];
        $arrayh[$counth]['dsreference'] = $datos[3];
        $arrayh[$counth]['dddocdate'] = $datos[8];
        $arrayh[$counth]['dsstatus'] = 1;
        $arrayh[$counth]['dscomments'] = $datos[11];
        $arrayh[$counth]['refgral'] = $refgral;
        $arrayh[$counth]['lines'] = array();

        $line = 0;
        $archivo = fopen("../uploads/".$args['_filenamefinal'], "r");
        while (($datos = fgetcsv($archivo, ",")) == true) {
            //echo "<br />";
            if ($count > 0) {

                $item = Item::getRowByCode($mysql, $datos[0]);
                $cliente = Partner::getRowByCode($mysql, $datos[7]);

                //if($pedidoc == $datos[3]){
                if($pedidoc == $datos[7]){
                    $arrayl[$line]['pedido'] = $datos[3];
                    $arrayl[$line]['iditem'] = $item[0]->pnid;
                    $arrayl[$line]['itemcode'] = $item[0]->dscode;
                    $arrayl[$line]['itemname'] = $item[0]->dsname;
                    $arrayl[$line]['qty'] = $datos[2];
                    $arrayl[$line]['guia'] = $datos[4];

                    $itemprice = Item::getAllByFilterBySN($mysql,$item[0]->dscode,$cliente[0]->pnid);
                    $price = 0.0;
                    if($itemprice[0]->pricewithdisc != "")
                        $price = $itemprice[0]->pricewithdisc;

                    $arrayl[$line]['price'] = $datos[5]; //$price;//$datos[4];
                    $arrayl[$line]['canal'] = $datos[6];
                    $arrayl[$line]['paqueteria'] = $datos[10];
                    $arrayl[$line]['coml'] = $datos[11];
                    //$arrayl[$line]['whscode'] = $datos[13];
                    if($datos[13]=="" || $datos[13]=="0")
                        $arrayl[$line]['whscode'] = "1";
                    else
                        $arrayl[$line]['whscode'] = $datos[13];
                    //$arrayh[$counth]['lines'] = $arrayl;
                    $line++;
                }else{
                    $arrayh[$counth]['lines'] = $arrayl;
                    $pedidoc = $datos[7];
                    $pedidoline = $datos[3];
                    $line=0;
                    $counth++;

                    $arrayh[$counth]['pedido'] = $datos[3];
                    $arrayh[$counth]['idpartner'] = $cliente[0]->pnid;
                    $arrayh[$counth]['dscode'] = $cliente[0]->dscode;
                    $arrayh[$counth]['dsname'] = $cliente[0]->dsname;
                    $arrayh[$counth]['idcreator'] = $args['iduser'];
                    $arrayh[$counth]['dsreference'] = $datos[2];
                    $arrayh[$counth]['dddocdate'] = $datos[7];
                    $arrayh[$counth]['dsstatus'] = 1;
                    $arrayh[$counth]['dscomments'] = $datos[12];
                    $arrayh[$counth]['refgral'] = $refgral;
                    $arrayh[$counth]['lines'] = array();

                    $arrayl = array();
                    $arrayl[$line]['pedido'] = $datos[3];//$pedidoc;
                    $arrayl[$line]['iditem'] = $item[0]->pnid;
                    $arrayl[$line]['itemcode'] = $item[0]->dscode;
                    $arrayl[$line]['itemname'] = $item[0]->dsname;
                    $arrayl[$line]['qty'] = $datos[2];
                    $arrayl[$line]['guia'] = $datos[4];

                    $itemprice = Item::getAllByFilterBySN($mysql,$item[0]->dscode,$cliente[0]->pnid);
                    $price = 0.0;
                    if($itemprice[0]->pricewithdisc != "")
                        $price = $itemprice[0]->pricewithdisc;

                    $arrayl[$line]['price'] = $datos[5];//$datos[4];
                    $arrayl[$line]['canal'] = $datos[6];
                    $arrayl[$line]['paqueteria'] = $datos[10];
                    $arrayl[$line]['coml'] = $datos[11];
                    //$arrayl[$line]['whscode'] = $datos[13];
                    if($datos[13]=="" || $datos[13]=="0")
                        $arrayl[$line]['whscode'] = "1";
                    else
                        $arrayl[$line]['whscode'] = $datos[13];
                }
            }//if > 0
            $count++;
        }//while

        $arrayh[$counth]['lines'] = $arrayl;

        fclose($archivo);

        /*var_dump($arrayh);
        for($i=0; $i<count($arrayh); $i++){
            var_dump($arrayh[$i]['lines']);
        }
        */
        $mysql->begin();
        for($i=0; $i<count($arrayh); $i++){

            $q="INSERT INTO thquote(pnid,fnidpartner,dscode,dsname,fnidcreator,".
                " dsreference,dsstatus,ddcreated,ddupdate,dddocdate,dscomments) " .
                "VALUES (null,".$arrayh[$i]['idpartner'].",'".$arrayh[$i]['dscode']."','".$arrayh[$i]['dsname']."',".
                $arrayh[$i]['idcreator'].", ".
                " '".$arrayh[$i]['refgral']."','".$arrayh[$i]['dsstatus']."',NOW(),NOW(),'".$arrayh[$i]['dddocdate']."',".
                " '".$arrayh[$i]['dscomments']."')";
            //echo "<br />query header:".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }else{
                $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
                //echo "<br />".$q;
                $id = $mysql->execute($q);

                for($j=0; $j<count($arrayh[$i]['lines']); $j++){

                    //var_dump($arrayh[$i]['lines']);
                    //echo "<br />".$arrayh[$i]['lines'][$j]['iditem'];
                    //echo "<br />".$arrayh[$i]['lines'][$j]['price'];

                    $q = "INSERT INTO thquoteline(pnid,thidheader, fniditem, dscode, dsname,".
                        " fnidware,dnquantity,dniva,dnprice,dsguia,dscanal,dsstatus,".
                        " dspaqueteria,dscomentariol,dnopenqty,dsrefline) VALUES( ".
                        " null,".$id[0]->lastid.",".$arrayh[$i]['lines'][$j]['iditem'].",'".$arrayh[$i]['lines'][$j]['itemcode'].
                        "','".$arrayh[$i]['lines'][$j]['itemname'].
                        "','".$arrayh[$i]['lines'][$j]['whscode']."',".$arrayh[$i]['lines'][$j]['qty'].
                        ",0,".$arrayh[$i]['lines'][$j]['price'].",'".$arrayh[$i]['lines'][$j]['guia'].
                        "','".$arrayh[$i]['lines'][$j]['canal']."','','".$arrayh[$i]['lines'][$j]['paqueteria'].
                        "','".$arrayh[$i]['lines'][$j]['coml']."',".$arrayh[$i]['lines'][$j]['qty'].
                        ",'".$arrayh[$i]['lines'][$j]['pedido']."')";
                    //echo "<br />line:".$q;
                    if(!$mysql->update($q)) {
                        //$mysql->commit();
                        $mysql->rollback();
                        return false;

                        /*}else{//if update lines
                            $mysql->rollback();
                            return false;*/
                    }//else


                }//for

                //$mysql->commit();
                //return true;
            }//


        } //for header
        $mysql->commit();
        return true;
        /*fin guardado de datos*/

    }//function saveByFile

    /*codigo cobranza*/

    public static function getPartnerCredit(Mysql $mysql, $idpartner=null){

        /*$q = "SELECT tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday, ts.pnid AS 'idov',
                         ts.ddcreated, ts.dsreference, ts.dsstatuscredit, SUM((tsl.dnquantity - sl.dnopenqty) * tsl.dnprice) AS 'facturado',
                         (SELECT SUM(trp.dsamount) FROM trpayment trp INNER JOIN thsales ts2 ON ts2.pnid = trp.fnidsales
                             WHERE ts2.fnidpartner = ts.fnidpartner AND (ts2.dsstatuscredit <> 3 AND ts2.dsstatus = 3)  AND trp.fnidstatus = 1
                             ) AS 'debit', '' AS 'dum'
                  FROM tcpartner tc INNER JOIN thsales ts ON ts.fnidpartner = tc.pnid
                                    INNER JOIN thsalesline tsl ON tsl.thidheader = ts.pnid
                  WHERE 1 = 1 AND (ts.dsstatuscredit <> 3 AND ts.dsstatus = 3)
                  GROUP BY tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday
                  ORDER BY tc.dsname ASC ";
        */

        $q = "SELECT tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday, ts.pnid AS 'idov', ".
            "      ts.ddcreated, ts.dsreference, ts.dsstatuscredit, SUM((tsl.dnquantity - tsl.dnopenqty) * tsl.dnprice) AS 'facturado',";

        $q.= " ( SELECT SUM(trp.dsamount) ".
            "   FROM trpayment trp INNER JOIN thsales ts2 ON ts2.pnid = trp.fnidsales
		        WHERE ts2.fnidpartner = ts.fnidpartner AND (ts2.dsstatuscredit <> 3 AND ts2.dsstatus = 3) ".
            "         AND trp.fnidstatus = 1
		     ) AS 'debit',";

        if($idpartner!=null)
            $q.= "	   (SELECT tdel.dddate FROM thdelivery tdel WHERE tdel.dscomment = ts.pnid  ORDER BY tdel.pnid DESC LIMIT 1) AS 'deliverydate',  ";

        $q.= "  '' AS 'dum' ".
            " FROM tcpartner tc INNER JOIN thsales ts ON ts.fnidpartner = tc.pnid ".
            "			        INNER JOIN thsalesline tsl ON tsl.thidheader = ts.pnid  ".
            " WHERE 1 = 1 AND (ts.dsstatuscredit <> 3 AND ts.dsstatus = 3) ";
        if($idpartner != null)
            $q .= " AND tc.pnid = $idpartner ";

        $q .=" GROUP BY tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday";

        if($idpartner != null)
            $q.=" ,ts.pnid, ts.ddcreated, ts.dsreference, ts.dsstatuscredit ";

        $q .= " ORDER BY tc.dsname ASC ";

        /*if($idpartner == null)
            echo "<br /> getPartnerCredit: ".$q;
          if($idpartner == 65)
            echo "<br /> getPartnerCredit: ".$q;
        */

        return $mysql->execute($q);

    }

    public static function getPartnerCreditCreditY(Mysql $mysql, $idpartner=null){

        $q = " SELECT A.*
               FROM ( ";
        $q .=  "    SELECT tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday, ts.pnid AS 'idov', ".
            "      ts.ddcreated, ts.dsreference, ts.dsstatuscredit,
                  ( SUM((tsl.dnquantity - tsl.dnopenqty) * tsl.dnprice ) ) AS 'facturado', ";

        if($idpartner==null)
            $q.= " ( SELECT SUM(trp.dsamount) ".
                "   FROM trpayment trp INNER JOIN thsales ts2 ON ts2.pnid = trp.fnidsales
           WHERE ts2.fnidpartner = ts.fnidpartner AND (ts2.dsstatuscredit <> 3 AND ts2.dsstatus = 3) ".
                "         AND trp.fnidstatus = 1 AND ts2.pnid = ts.pnid
        ) AS 'debit',";
        else
            $q .= " 0  AS 'debit', ";


        if($idpartner!=null)
            $q.= "	   (SELECT tdel.dddate FROM thdelivery tdel WHERE tdel.dscomment = ts.pnid  ORDER BY tdel.pnid DESC LIMIT 1) AS 'deliverydate',  ";

        $q.= "  '' AS 'dum', tc.dddiscountperc,
               ( ( SUM((tsl.dnquantity - tsl.dnopenqty) * tsl.dnprice ) ) / (1+(tc.dddiscountperc/100)) ) AS 'SINCOMTEMP' ".
            " FROM tcpartner tc INNER JOIN thsales ts ON ts.fnidpartner = tc.pnid ".
            "			        INNER JOIN thsalesline tsl ON tsl.thidheader = ts.pnid  ".
            " WHERE 1 = 1 AND (ts.dsstatuscredit <> 3 AND ts.dsstatus = 3) AND tc.dscredit LIKE 'Y' ";
        if($idpartner != null)
            $q .= " AND tc.pnid = $idpartner ";

        $q .=" GROUP BY tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday";

        if($idpartner != null)
            $q.=" ,ts.pnid, ts.ddcreated, ts.dsreference, ts.dsstatuscredit ";

        /* se agregan pagos*/
        if($idpartner != null){
            $q .= "UNION
               SELECT 'PAGO', ts2.dscode, ts2.dsname, '', NULL, trp2.pnid, trp2.dddate, trp2.dsreference, '',
                       0.00, trp2.dsamount, '','','', '0'
               FROM trpayment trp2 INNER JOIN thsales ts2 ON trp2.fnidsales = ts2.pnid
              WHERE 1 = 1 AND (ts2.dsstatuscredit <> 3 AND ts2.dsstatus = 3) AND ts2.fnidpartner = $idpartner AND trp2.dsreference <> 'COMISION' ";
        }
        /* fin se agregan pagos*/

        if($idpartner != null){
            //$q .= " ORDER BY ts.ddcreated ASC ";
            //$q .= " ORDER BY trp2.dddate ASC";
        }else
            $q .= " ORDER BY tc.dsname ASC ";

        $q.= ") A ORDER BY A.ddcreated ASC";

        /*
                if($idpartner == 67)
                  echo "<br />getPartnerCreditCreditY: $q";
                if($idpartner == null)
                echo "<br />getPartnerCreditCreditY gral: $q";
        */
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
                  tb.pnid AS 'idbalance'  ";

        $q.= " FROM tcpartner tc INNER JOIN tcbalance tb ON tb.fnidpartner = tc.pnid	".
            " WHERE tc.dscredit LIKE 'Y'   ";

        if($idpartner != null)
            $q .= " AND tc.pnid = $idpartner ";

        $q .=" GROUP BY tc.pnid, tc.dscode, tc.dsname, tc.dncreditday";

        if($idpartner != null)
            $q.=" ,tb.fniddocument, tb.dddate, tb.dstype, tb.pnid  ";


        if($idpartner == null)
            $q .= " ORDER BY tc.dsname ASC ";

        $q.= ") A ORDER BY A.ddcreated ASC";

        /*
               if($idpartner == 67)
                 echo "<br />getPartnerCreditCreditY2: $q";
               if($idpartner == null)
               echo "<br />getPartnerCreditCreditY2 gral: $q";
        */
        return $mysql->execute($q);
    }

    public static function getPartnerCreditOLD1906(Mysql $mysql, $id=null){

        $q = "SELECT tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday, ";
        $q .= " SUM(tsl.dnprice * tsl.dnquantity) AS 'facturado', SUM(tsl.dnquantity) AS 'countbysale', ";
        /*
        $q .=" SUM((tsl.dnprice * (tsl.dnquantity - ".
            " ( SELECT count(*) FROM thsalesserial tss ".
            "  WHERE tss.fnidline = tsl.pnid AND tss.fnidheader = ts.pnid AND tss.fnidquantity = 0 ))) * 1.0) AS 'facturado', ";
        */
        //    " (SELECT SUM(trp.dsamount) FROM trpayment trp WHERE trp.fnidsales = ts.pnid AND trp.fnidstatus = 1 ) AS 'debit'";
        $q .= " SUM(trp.dsamount) AS 'debit'";

        if($id!=null)
            $q .= " , ts.pnid AS 'idov', ts.ddcreated, ts.dsreference, ts.dsstatuscredit ";

        if($id!=null)
            $q .= ", (SELECT tdel.dddate FROM thdelivery tdel WHERE tdel.dscomment = ts.pnid) AS 'deliverydate' ";
        /*$q .= " , (SELECT tdel.dddate ".
          "    FROM thdelivery tdel INNER JOIN trinventory tinv ON tinv.dscomments = tdel.pnid ".
          "   WHERE tinv.dsorigen = ts.pnid AND tinv.dsdirection = -1 ".
          "    ORDER BY tdel.dddate DESC LIMIT 1   ) AS 'deliverydate' ";*/

        $q .=" FROM tcpartner tc INNER JOIN thsales ts ON ts.fnidpartner = tc.pnid
                              INNER JOIN thsalesline tsl ON tsl.thidheader = ts.pnid
                              LEFT JOIN trpayment trp ON trp.fnidsales = ts.pnid AND trp.fnidstatus = 1
            WHERE 1 = 1 AND (ts.dsstatuscredit <> 3 AND ts.dsstatus = 3) ";

        if($id!=null)
            $q .=" AND tc.pnid = ".$id;

        $q .= " GROUP BY tc.pnid, tc.dscode, tc.dsname, tc.dsrfc, tc.dncreditday  ";

        if($id!=null)
            $q .= ", ts.pnid, ts.ddcreated, ts.dsreference, ts.dsstatuscredit ";

        $q .= " ORDER BY tc.dsname ASC ";
        /*if($id == null)
            echo "<br /> getPartnerCredit: ".$q;
        if($id == 67)*/
        echo "<br /> getPartnerCredit: ".$q;


        return $mysql->execute($q);
    }

    public static function getDebit(Mysql $mysql, $id, $idov = null){
        $q = " SELECT SUM(tpay.dsamount) AS 'debit'
               FROM trpayment tpay inner join thsales ts ON tpay.fnidsales = ts.pnid AND ts.fnidpartner = $id ";
        if($idov!=null)
            $q .= " WHERE tpay.fnidsales = $idov";

        $q.=" GROUP BY ts.dscode, ts.pnid
               ORDER BY tpay.pnid DESC ";

        return $mysql->execute($q);
    }

    //public static function getPartnerDebit()

    public static function getHistorySales($mysql, $filter=null){
        /*$q = " SELECT trsal.pnid AS 'idsalesstatus', trsal.dsreference AS 'refsalesstatus', ".
             " trsal.dsstatus AS 'statsalesstatus', ts.* ".
             " ".
             " FROM trsalesstatus trsal INNER JOIN thsales ts ON ts.pnid = trsal.fnidsales ".
            " WHERE ts.dsstatus = 3".
             " ORDER BY trsal.pnid DESC ";*/
        $q = "SELECT trsal.pnid AS 'idsalesstatus', trsal.dsreference AS 'refsalesstatus', ".
            "              trsal.dsstatus AS 'statsalesstatus', ts.*, ".
            "			  tp.dncreditday, tp.pnid AS 'idpartner',  ".
            "              DATE_ADD(ts.ddcreated, INTERVAL tp.dncreditday DAY) AS 'feclimit',  ".
            "              DATEDIFF(CURDATE(),DATE_ADD(ts.ddcreated, INTERVAL tp.dncreditday DAY)) AS 'daytrans'  ".
            " FROM trsalesstatus trsal INNER JOIN thsales ts ON ts.pnid = trsal.fnidsales  ".
            "                          INNER JOIN tcpartner tp ON tp.pnid = ts.fnidpartner  ".
            " WHERE ts.dsstatus = 3 ";
        if($filter != null)
            $q .= " AND ts.pnid = ".$filter;
        $q .= " ORDER BY trsal.pnid DESC	";

        //echo "<br />getHistorySales: ".$q;
        return $mysql->execute($q);
    }

    public static function updatePayment(Mysql $mysql, $args){

        $q = " UPDATE trsalesstatus SET dsreference = '".$args['_ref']."', dsstatus = '".$args['_status'].
            " ' WHERE fnidsales = ".$args['id'];
        //echo $q;
        $mysql->update($q);

        return true;

    }

    public static function getHystoricPaymentsBySO(Mysql $mysql, $id=null, $idpayment=null){

        $q = " SELECT trp.*, tc.dsuser FROM trpayment trp INNER JOIN tcuser tc ON trp.fniduser = tc.idtcuser ".
            " WHERE 1 = 1 ";
        if($id!=null)
            $q.= " AND trp.fnidsales = ".$id;
        if($idpayment!=null)
            $q .= " AND trp.pnid = ".$idpayment;
        //echo "<br />getHystoricPaymentsBySO: $q";
        return $mysql->execute($q);

    }

    public static function getTotalSales(Mysql $mysql, $id){

        $q = "SELECT SUM(((sl.dnquantity - sl.dnopenqty ) - ".
            " (SELECT count(*) FROM thsalesserial tss ".
            "  WHERE tss.fnidline = sl.pnid AND tss.fnidheader = sh.pnid AND tss.fnidquantity = 0)) * sl.dnprice) AS 'total' ".
            " FROM thsales sh INNER JOIN thsalesline sl ON sl.thidheader = sh.pnid ".
            " WHERE sh.pnid = $id";
        return $mysql->execute($q);
    }

    public static function getAmountPaymentValid(Mysql $mysql, $id, $idstatus = 1){

        $q = "SELECT SUM(p.dsamount) AS 'amount' FROM trpayment p ".
            " WHERE p.fnidsales = ".$id." AND p.fnidstatus = $idstatus";

        //echo "<br /> getAmountPaymentValid: ".$q;

        return $mysql->execute($q);
    }

    public static function addPaymentDetail(Mysql $mysql, $args){

        $q = "INSERT INTO trpayment VALUES(NULL,".$args['_idov'].",NOW(),'".$args['_ref'].
            "',".$args['_mpay'].",".str_replace(',',"",$args['_mpending']).",".$args['iduser'].",1,'".$args['_paytype']."')";

        //echo "<br />addpayment insert: ".$q;

        if($mysql->update($q)) {

            $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
            $idpaytemp  = $mysql->execute($q);


            $pending = str_replace(",","", $_POST['_mpending']);
            $pay = str_replace(",","",$_POST['_mpay']);

            if ($pay >= $pending) {
                $q = " UPDATE thsales SET dsstatuscredit = 3 WHERE pnid = " . $_POST['_idov'];
                $mysql->update($q);
                //echo "<br />addpayment update 3 thsales: " . $q;
            }else{
                $q = " UPDATE thsales SET dsstatuscredit = 2 WHERE pnid = " . $_POST['_idov'];
                $mysql->update($q);
                //echo "<br />addpayment update 2 thsales: " . $q;
            }// if $pay >= pending

            /*code to insert into tcbalance*/

            try{

                $q = " INSERT INTO tcbalance (pnid, dstype, fniddocument, dddate, dnamount, dscomments, fnidpartner, fniddelivery)
                          SELECT NULL,'PAGO', ".$idpaytemp[0]->lastid.", NOW(), ".$args['_mpay'].", '".$args['_paytype']."', th.fnidpartner, NULL
                      FROM thsales th
                      WHERE th.pnid = ".$args['_idov'];
                $mysql->update($q);

                $filename = "../uploads/logInventoryBalance/".$args['_idov']."_P_".$args['iduser']."_".date("d_m_Y_H_i_s").".txt";
                $towrite = $q;

                if (!$handle = fopen($filename, "w")) {
                    echo "El archivo no se puede crear";
                    //exit;
                }
                if (fwrite($handle, utf8_decode($towrite)) === false){
                    echo "El archivo no se puede escribir";
                    //exit;
                }
                fclose($handle);
            }catch(customException $e){
                echo $e->errorMessage();
            }
            /* end code to insert into tcbalance*/

        }// if $mysql->update

        return true;

    }//function addpaymentdetail

    public static function checkSerieIntoInventoryExit(Mysql $mysql, $args, $item){

        /*$q = "SELECT dsserial FROM trinventoryserial ".
             " WHERE dsserial IN ('$args') AND (dnquantity = 1 AND fniditem = $item )";
        */
        /*$q = "SELECT dsserial FROM trinventoryserial ".
            " WHERE dsserial IN ('$args') AND dnquantity = 1 AND fniditem = $item ";*/

        $q = " SELECT fnidserial FROM thsalesserial WHERE fnidserial IN ('".strtoupper($args)."') AND fnidquantity = 1 ";
        /*$q = "SELECT dsserial FROM trinventoryserial ".
            " WHERE dsserial IN ('$args') AND dnquantity = 1 ";*/
        //echo " <br /> checkSerieIntoInventoryExit: ".$q;
        return $mysql->execute($q);

    }

    public static function checkSerieInDB(Mysql $mysql, $args){

        $q = "SELECT dsserial FROM trinventoryserial WHERE dsserial IN ('".strtoupper($args)."')";
        //echo "<br />".$q;
        return $mysql->execute($q);

    }

    public static function checkSerieValidToReturn(Mysql $mysql,$ser, $sku, $ref, $cardcode ){

        /*
         if($cardcode=="C-0070"){
           $q = "SELECT VS.fnidserial, SL.fniditem, VS.fnidline, VS.fnidheader ".
               " FROM thsalesserial VS INNER JOIN thsalesline SL ON SL.pnid = VS.fnidline ".
               "                       INNER JOIN thsales HS ON HS.pnid =  SL.thidheader ".
               " WHERE SL.dscode LIKE '$sku' AND TRIM(SL.dsrefline) LIKE '%$ref%' AND ".
               "       VS.fnidserial LIKE '$ser' AND HS.dscode = '$cardcode' ";
             }else{
               $q = "SELECT VS.fnidserial, SL.fniditem, VS.fnidline, VS.fnidheader ".
                   " FROM thsalesserial VS INNER JOIN thsalesline SL ON SL.pnid = VS.fnidline ".
                   "                       INNER JOIN thsales HS ON HS.pnid =  SL.thidheader ".
                   " WHERE SL.dscode LIKE '$sku' AND TRIM(SL.dsrefline) LIKE '$ref' AND ".
                   "       VS.fnidserial LIKE '$ser' AND HS.dscode = '$cardcode' ";
             }
        */
        $q = "SELECT pnid, dscode, dsserial FROM tcitem WHERE dscode LIKE '$sku' ";
        //echo "<br />checkSerieValidToReturn item: ".$q;
        $item = $mysql->execute($q);

        if(count($item)<=0){
            $q = "";
        }else{
            $q = " SELECT VS.fnidserial, SL.fniditem, VS.fnidline, VS.fnidheader, SL.fnidware ".
                " FROM thsalesserial VS INNER JOIN thsalesline SL ON SL.pnid = VS.fnidline ".
                "                       INNER JOIN thsales HS ON HS.pnid =  SL.thidheader ".
                " WHERE SL.dscode LIKE '$sku' AND  ".
                "       VS.fnidserial LIKE '".strtoupper($ser)."' AND HS.dscode = '$cardcode' AND VS.fnidquantity = 1 ";
        }

        //echo "<br />checkSerieValidToReturn: ".$q;
        $list = $mysql->execute($q);


        return $mysql->execute($q);
    }

    public static function checkSerieValidToReturnWithOutSerial(Mysql $mysql,$ser=null, $sku, $ref, $cardcode ){

        /*
         if($cardcode=="C-0070"){
           $q = "SELECT VS.fnidserial, SL.fniditem, VS.fnidline, VS.fnidheader ".
               " FROM thsalesserial VS INNER JOIN thsalesline SL ON SL.pnid = VS.fnidline ".
               "                       INNER JOIN thsales HS ON HS.pnid =  SL.thidheader ".
               " WHERE SL.dscode LIKE '$sku' AND TRIM(SL.dsrefline) LIKE '%$ref%' AND ".
               "       VS.fnidserial LIKE '$ser' AND HS.dscode = '$cardcode' ";
             }else{
               $q = "SELECT VS.fnidserial, SL.fniditem, VS.fnidline, VS.fnidheader ".
                   " FROM thsalesserial VS INNER JOIN thsalesline SL ON SL.pnid = VS.fnidline ".
                   "                       INNER JOIN thsales HS ON HS.pnid =  SL.thidheader ".
                   " WHERE SL.dscode LIKE '$sku' AND TRIM(SL.dsrefline) LIKE '$ref' AND ".
                   "       VS.fnidserial LIKE '$ser' AND HS.dscode = '$cardcode' ";
             }
        */
        $q = "SELECT pnid, dscode, dsserial FROM tcitem WHERE dscode LIKE '$sku' ";
        $item = $mysql->execute($q);

        if(count($item)>0){
            $q = " SELECT SL.fniditem, SL.pnid AS 'fnidline', HS.pnid AS 'fnidheader', SL.fnidware ".
                " FROM thsalesline SL INNER JOIN thsales HS ".
                " WHERE SL.dscode LIKE '$sku' AND HS.dscode = '$cardcode' AND sl.dsrefline = '$ref' ";
        }else{
            $q = "";
        }

        //echo "<br />checkSerieValidToReturnWithOutSerial: ".$q;
        //$list = $mysql->execute($q);


        return $mysql->execute($q);
    }

    public static function getRowDetail(Mysql $mysql, $idheader, $idline, $sku){
        $q = "SELECT * FROM thsalesline ".
            " WHERE thidheader = $idheader  AND dscode LIKE '$sku' AND pnid = $idline ";
        //echo "<br />getRowDetail: ".$q;
        return $mysql->execute($q);

    }//function getRowDetail

    public static function setRowsSerialSalesByFile(Mysql $mysql, $args){


        $debug = 1;
        /***********************/
        /* lectura de archivo */

        $count = 0;
        $refgral = $args['iduser']."_".date("Y-m-d_H-i-s");

        $archivo = fopen("../uploads/".$args['_filenamefinal'], "r");
        //$mysql->begin();

        $arrayh = array();
        $arrayl = array();
        $pedidoc = "";

        $datos = fgetcsv($archivo, ",");
        $datos = fgetcsv($archivo, ",");

        $args['id'] = str_replace("'","",$datos[0]);

        fclose($archivo);

        $i=0;
        $arr_qty = array();
        $arr_cod = array();

        $archivo = fopen("../uploads/".$args['_filenamefinal'], "r");
        while (($datos = fgetcsv($archivo, ",")) == true) {
            //echo "<br />";
            if ($count > 0) {
                $datos[0] = str_replace("'","",$datos[0]);
                $datos[3] = str_replace("'","",$datos[3]);
                $datos[4] = str_replace("'","",$datos[4]);

                $item=Item::getRowByCode($mysql, $datos[3]);

                if(array_key_exists($datos[1],$arr_cod)){
                    $arr_cod[$datos[1]]=$arr_cod[$datos[1]]+1;
                }else{
                    $arr_cod[$datos[1]]=1;
                    $args['_art'][count($args['_art'])]= $datos[3];
                    $args['_linea'][count($args['_linea'])]= $datos[1];
                    $args['_iditemGral'][count($args['_iditemGral'])]= $item[0]->pnid;
                }


                $args['_iditem'][$i] = $item[0]->pnid;
                $row=Sales::getRowDetail($mysql, $datos[0], $datos[1], $datos[3]);
                $args['_price'][$i] = $row[0]->dnprice;
                $args['_idline'][$i] = $datos[1];
                //$args['_linea'][$i] = $datos[1];
                $args['_seriea'][$i] = strtoupper($datos[4]);

                $i++;
            } //if count
            $count++;
        }//while

        fclose($archivo);
        /* fin lectura de archivo*/
        /*************************/

        /*insert into table for id delivery*/
        $q = "INSERT INTO thdelivery VALUES (null,current_date(), '".$args['id']."'); ";
        echo "<br />".$q;
        $mysql->update($q);

        $q = " SELECT LAST_INSERT_ID() AS 'lastid'";
        $iddelivery = $mysql->execute($q);

        //echo "<br />function setRowsSerialSales<br />";
        //var_dump($args);
        //echo "<br />count _art:".count($args['_qty']);
        $mysql->begin();

        for($i=0; $i<count($args['_art']); $i++){

            $q = " INSERT INTO trinventory(pnid,fnidware,fniditem,ddquantity,dsdirection, dsorigen, dslinebase, dscomments) ".
                " SELECT NULL, fnidware, fniditem, dnquantity, -1,".$args['id'].",'".$args['_linea'][$i]."','' ".
                " FROM thsalesline ".
                " WHERE thidheader = ".$args['id']." AND pnid = ".$args['_linea'][$i];
            if($debug)
                echo "<br />1. query trinventory".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            //$q = "UPDATE trinventorygral SET ddquantity = (ddquantity - " . $args['_qty'][$i] . ") WHERE fniditem = " . $args['_iditem'][$i];
            $q = "UPDATE trinventorygral SET ddquantity = (ddquantity - " . $arr_cod[$args['_linea'][$i]] .
                " ) WHERE fniditem = " . $args['_iditem'][$i]. " AND fniditem = 1";
            if($debug)
                echo "<br />2. query update trinventory".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            /************/
            /* se insertara la cantidad nueva cuando se actualiza el inventario*/

            $q = " SELECT trg.fniditem, trg.ddquantity, tc.dscode, tc.dddate, pl.dnquantity,
                	   pl.dnprice,
                       CASE WHEN tc.ddcostinv IS NULL THEN 0 ELSE tc.ddcostinv END AS 'costact',
                       CASE WHEN tc.ddqtyinv IS NULL THEN 0 ELSE tc.ddqtyinv END AS 'stockact'
                 FROM trinventorygral trg LEFT JOIN trcost tc ON tc.fniditem = trg.fniditem
                 						 INNER JOIN thsalesline pl ON pl.fniditem = trg.fniditem
                 WHERE pl.thidheader = ".$args['id']." AND pl.fniditem = ".$args['_iditemGral'][$i];
            $q.=" AND tc.dddate = (SELECT dddate FROM trcost WHERE fniditem = pl.fniditem ORDER BY pnid DESC LIMIT 1 )";

            if($debug)
                echo "<br />3. to get last cost:".$q;

            $tocost = $mysql->execute($q);

            if(count($tocost)>0){
                $qtyant = $tocost[0]->stockact;
                $costant = $tocost[0]->costact;
            }else{
                $qtyant = 0;
                $costant = 0.0;
            }

            //$qtynew = $qtyant - $args['_qty'][$i];
            $qtynew = $qtyant - $arr_cod[$args['_linea'][$i]];
            $costnew = (($costant * $qtynew)) / ($qtynew);


            $q = "INSERT INTO trcost VALUES(null,".$args['_iditemGral'][$i].",'".$args['_art'][$i].
                "',".$qtynew.",".round($costnew,2,PHP_ROUND_HALF_UP).",".
                $costant.",".$qtyant.",".$args['id'].",NOW(),-1)";
            if($debug)
                echo "<br />4. insert into trcost: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
            /* fin se insertara la cantidad nueva cuando se actualiza el inventario*/
            /************/


        }


        //echo "<br />count _art:".count($args['_qty']);
        //print_r($args['_whscode']);
        for ($j = 0; $j < count($args['_idline']); $j++) {
            //if ($args['_linea'][$j] == $args['_nl'][$i]) {

            $q = "SELECT pnid FROM trinventory WHERE dsorigen = ".$args['id']." AND dslinebase = ".$args['_idline'][$j];
            if($debug)
                echo "<br />5. consulta trinventory: ".$q;
            $idline = $mysql->execute($q);

            $q = "INSERT INTO thsalesserial(pnid, fnidline,fnidserial,fnidquantity, fnidheader,fnidheaderdelivery) VALUES " .
                " (null," . $args['_idline'][$j] . ",'" . strtoupper($args['_seriea'][$j]) . "',1," . $args['id'].",".
                $iddelivery[0]->lastid.")";
            if($debug)
                echo "<br />6. query thsalesserial: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            $q = "INSERT INTO trinventoryserial(pnid, fnidware,fniditem,dsserial, fnidinventory, dnquantity) VALUES " .
                " (null, 1, " . $args['_iditem'][$j] . ",'" . strtoupper($args['_seriea'][$j]) . "'," . $idline[0]->pnid.",-1)";
            if($debug)
                echo "<br />7. query trinventory serial:".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            //actualiza la linea serie en orden de compra
            $q = "UPDATE thpurchaseserial SET `fnidquantity` = -1 WHERE fnidserial LIKE '".strtoupper( $args['_seriea'][$j])."'";
            if($debug)
                echo "<br />8. thpurchaseserial: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            //actualiza la linea serie en inventario
            $q = "UPDATE  trinventoryserial SET `dnquantity` = 0 ".
                " WHERE dsserial LIKE '".strtoupper($args['_seriea'][$j])."' AND fniditem = ".$args['_iditem'][$j];
            if($debug)
                echo "<br />9. update trinventoryserial: ".$q;

            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

        }//for

        $q = "UPDATE thsales SET dsstatus = 3 WHERE pnid = ".$args['id'];
        if($debug)
            echo "<br />10. update thsales: ".$q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }

        /* code to insert into tcbalance the last delivery */
        try{


            $q = " INSERT INTO tcbalance (pnid, dstype, fniddocument, dddate, dnamount, dscomments, fnidpartner, fniddelivery)
                    SELECT NULL,'VENTA', tsel.thidheader, NOW(),  SUM(tsel.dnprice * tss.fnidquantity), '', th.fnidpartner, tdel.pnid
                FROM thdelivery tdel INNER JOIN thsalesserial tss ON tss.fnidheaderdelivery = tdel.pnid
                                     INNER JOIN thsalesline tsel ON tsel.pnid = tss.fnidline AND tss.fnidheader = tsel.thidheader
                                     INNER JOIN thsales th ON th.pnid = tsel.thidheader
                WHERE tdel.pnid = ".$iddelivery[0]->lastid."
                 GROUP BY tsel.thidheader, th.fnidpartner, tdel.pnid ";
            echo "<br />tcbalance:".$q;
            $mysql->update($q);

            $filename = "../uploads/logInventoryBalance/".$args['id']."_".$iddelivery[0]->lastid."_01_".date("d_m_Y_H_i_s").".csv";
            $towrite = $q;

            if (!$handle = fopen($filename, "w")) {
                echo "El archivo no se puede crear";
                //exit;
            }
            if (fwrite($handle, utf8_decode($towrite)) === false){
                echo "El archivo no se puede escribir";
                //exit;
            }
            fclose($handle);
        }catch(customException $e){
            echo $e->errorMessage();
        }
        /* end code to insert into tcbalance the last delivery*/

        /*code to insert into status payment*/
        $q = "SELECT * FROM trsalesstatus WHERE fnidsales = ".$args['id'];
        $row = $mysql->execute($q);

        if(count($row) > 0){
            $q = "UPDATE trsalesstatus  SET dsreference = '', dsstatus = 'PENDIENTE' WHERE fnidsales = ".$args['id'];
            if($debug)
                echo "<br />11. update trsalesstatus: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
        }else{
            $q = " INSERT INTO trsalesstatus VALUES(null,'','PENDIENTE', ".$args['id'].")";
            if($debug)
                echo "<br />11. insert trsalesstatus: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
        }
        /*end to insert into status payment*/
        $mysql->commit();
        return true;
    }

    public static function getHistoryByIMEI(Mysql $mysql,$imei){

        $q = "SELECT th.pnid, th.dscode , th.dsname, th.ddcreated, tl.pnid AS 'idline', 
                     tl.fniditem, tl.dscode AS 'itemcode', tl.dsname AS 'itemname', ts.fnidserial,
                     ten.dddate AS 'fecentry', ts.fnidheaderdelivery 
              FROM thsales th INNER JOIN thsalesline tl ON tl.thidheader = th.pnid 
				                 INNER JOIN thsalesserial ts ON ts.fnidline = tl.pnid 
                                 LEFT JOIN 	thdelivery ten ON ten.pnid = fnidheaderdelivery 			                 
              WHERE ts.fnidserial LIKE '".strtoupper($imei)."' ";
        //echo "<br /> getHistoryByIMEI:".$q;
        return $mysql->execute($q);
    }


}//class
