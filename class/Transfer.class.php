<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 19/08/2019
 * Time: 04:02 PM
 */

class Transfer
{

    public static function getAllByType(Mysql $mysql, $datefrom = null, $dateto=null, $filter = null,$idware=null)
    {//list all user by role
        $q = " SELECT tc.*, tu.dsuser, ts.dsname AS 'statush'
            FROM thtransfer tc INNER JOIN tcuser tu ON tu.idtcuser = tc.fniduser 
                               INNER JOIN tcstatus ts ON ts.pnid = tc.dsstatus ";

        $q .= " WHERE 1 = 1 ";
        if($datefrom != null && $dateto != null)
            $q .= " AND CAST(tc.ddate AS DATE) BETWEEN '$datefrom' AND '$dateto' ";
        else
            $q .= " AND (CAST(tc.ddate AS DATE) BETWEEN DATE_SUB(CURDATE(), INTERVAL 10 DAY) AND CURDATE())";
        //else
        //    $q .= " AND CAST(tc.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto' ";

        if($filter != null)
            $q .= " AND (tc.dscomments LIKE '%$filter%' OR tc.dscomments LIKE '%$filter%')";

        if($idware != null)
            $q .= " AND fnidfromware = $idware OR fnidtoware = $idware";


        $q .= " ORDER BY tc.pnid DESC "; //.Mysql::getQueryLimit($page);
        //$q = String::sanitize($q,true);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getRow(Mysql $mysql,$id) {
        $q="SELECT tu.*, tl.pnid AS 'pnidline', tl.dscode AS 'dsitemcode', tl.dsname AS 'dsitemname', ".
            " tl.dnopenqty, tl.dnquantity, tci.dsupc, tci.dsupc2, tl.fniditem, tl.fnidto, tl.fnidfrom, ".
            " tci.dsserial AS 'manser' ".
            " FROM thtransfer tu INNER JOIN thtransferline tl ON tl.fnidheader = tu.pnid ".
            "                    INNER JOIN tcitem tci ON tci.pnid = tl.fniditem ".
            " WHERE tu.pnid='$id' ";
        //echo $q;
        return $mysql->execute($q);
    }//function

    public static function setRow(Mysql $mysql,$args) {
        //print_r($args);
        $mysql->begin();
        $q="INSERT INTO thtransfer(pnid,dsstatus,ddate,fniduser,dscomments,fnidfromware,fnidtoware,dsreference) " .
            "VALUES (null,1,NOW(),".$args['idcreator'].", ".
            " '".$args['_comentarios']."',".$args['_whsfromg'].",".$args['_whstog'].",'".$args['_referencia']."')";
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

            for($i=0; $i<count($args['_art']); $i++) {
                $q = "INSERT INTO thtransferline(pnid,fnidheader, fniditem, dscode, dsname," .
                    " dnquantity,dnopenqty,fnidfrom,fnidto) VALUES( " .
                    " null," . $id[0]->lastid . "," . $args['_iditem'][$i] . ",'" . $args['_art'][$i] . "','" . $args['_desc'][$i] . "'," .
                     $args['_qty'][$i]."," . $args['_qty'][$i].
                    ",".$args['_whscode'][$i].",".$args['_whscodeto'][$i].")";
                //echo "<br />".$q;
                echo "<br />query linea:".$q;
                if (!$mysql->update($q)) {
                    $mysql->rollback();
                    return false;
                }
            }
            //}//for

            $mysql->commit();
            return true;
        }//else commit header

    }//function

    public static function setRowSerial(Mysql $mysql, $args){

        $mysql->begin();

        // log de inserciones con series */
        try{
            $filename = "../uploads/logTransfer/TRF_".$args['id']."_".$args['idcreator']."_".date("d_m_Y_H_i_s").".csv";
            $towrite = "";
            for ($j = 0; $j < count($args['_linea']); $j++) {
                //docnum, linenum, itemid, itemcode, quantity, IMEI, origen, destino
                $towrite .= "'".$args['id']."',".$args['_linea'][$j].",".$args['_idarticlea'][$j].
                    ",'".$args['_codea'][$j]."','".strtoupper($args['_seriea'][$j])."'".
                    ",'".$args['_whscodel'][$j]."','".$args['_whscodetol'][$j]."'"."\r\n";
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

        //print_r($args);

        $q = "INSERT INTO thtransferfol VALUES (null,NOW(),'".$args['id']."','".$args['idcreator']."'); ";
        $mysql->update($q);

        $q = "SELECT LAST_INSERT_ID() AS 'lastid' ";
        $idtransfer = $mysql->execute($q);

        for($i=0; $i<count($args['_art']); $i++){
            //echo "<br /> ---------$i-----------";
            if($args['_qtyr'][$i] > 0){

                $q = " INSERT INTO trinventory(pnid,fnidware,fniditem,ddquantity, dsdirection, dsorigen, dslinebase, dscomments) ".
                    " SELECT NULL, fnidto, fniditem, ".$args['_qtyr'][$i].", 3,".$args['id'].",'".$args['_idline'][$i]."','TRANSFER' ".
                    " FROM thtransferline ".
                    " WHERE fnidheader = ".$args['id']." AND pnid = ".$args['_idline'][$i];
                //echo "<br />3. query trinventory: ".$q;
                if(!$mysql->update($q)){
                    $mysql->rollback();
                    return false;
                }

                /*actualiza si existe el origen y el destino*/
                $q = " SELECT * FROM trinventorygral WHERE fniditem = ".$args['_iditem'][$i]." AND fnidware = ".$args['_to'][$i];
                //echo "<br />4. query trinventorygral: ".$q;
                $row = $mysql->execute($q);
                if(count($row)>0) {
                    $q = "UPDATE trinventorygral SET ddquantity = ddquantity + " . $args['_qtyr'][$i] .
                          " WHERE fniditem = " . $args['_iditem'][$i]." AND fnidware = ".$args['_to'][$i];
                }else{
                    $q = "INSERT INTO trinventorygral VALUES(null,".$args['_to'][$i].",".$args['_iditem'][$i].",".$args['_qtyr'][$i].",'')";
                }
                //echo "<br />5. query insert o update trinventorygral destiny:".$q;
                if(!$mysql->update($q)){
                    $mysql->rollback();
                    return false;
                }

                $q = "UPDATE trinventorygral SET ddquantity = ddquantity - " . $args['_qtyr'][$i].
                    " WHERE fniditem = " . $args['_iditem'][$i]." AND fnidware = ".$args['_from'][$i];
                //echo "<br />5.1 - query insert o update trinventorygral origen:".$q;
                if(!$mysql->update($q)){
                    $mysql->rollback();
                    return false;
                }
                /*FIN actualiza si existe el origen y el destino*/

                //update open qty in purchase order
                $q = "UPDATE  thtransferline SET dnopenqty = (dnopenqty - ".$args['_qtyr'][$i].")".
                    " WHERE pnid = ".$args['_idline'][$i]." AND fnidheader = ".$args['id'];
                //echo "<br />update openqty: ".$q;
                if(!$mysql->update($q)){
                    $mysql->rollback();
                    return false;
                }
                //end update open qty in purchase order
                //echo "<br />FIN ======= FIN ITEMS ======";
            }//if _qtyr > 0
        }//for items

        /*actualiza series*/
        for ($j = 0; $j < count($args['_linea']); $j++) {
            //echo "<br />******* serie: $j *****";
            $q = " UPDATE thpurchaseserial SET fnidware = ".$args['_whscodetol'][$j]." WHERE fnidserial LIKE '".strtoupper($args['_seriea'][$j])."' ";
            //echo "<br />query update thpurchaseserial: ".$q;
            $mysql->update($q);

            $q = "INSERT INTO thtransferserial VALUES(NULL,".$args['_linea'][$j].",'".
                strtoupper($args['_seriea'][$j])."',".$args['id'].",1,".$idtransfer[0]->lastid.")";
            //echo "<br />query insert thtransferserial: ".$q;
            $mysql->update($q);
        }

            /*fin actualiza series*/

        $q = "UPDATE thtransfer SET dsstatus = 2 WHERE pnid = ".$args['id'];
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }

        $mysql->commit();
        return true;

    }

    public static function getSerialByRow(Mysql $mysql,$id){

        $q = "SELECT tpl.pnid AS 'linea', tpl.fniditem AS 'iditem', tpl.dscode AS 'itemcode',".
            " tps.dsserial AS 'serial', tps.dnquantity AS 'quantity' ".
            " FROM thtransfer tp INNER JOIN thtransferline tpl ON tpl.fnidheader = tp.pnid ".
            "                    INNER JOIN thtransferserial tps ON tps.fnidline = tpl.pnid AND tps.fnidheader = tp.pnid ".
            " WHERE tp.pnid = $id ";
        //echo "<br />getSerialByRow: ".$q;
        return $mysql->execute($q);

    }

    public static function checkSerieValidToTransfer(Mysql $mysql, $serie, $item, $whscode){

        $q = " SELECT psl.fnidserial 
               FROM thpurchaseserial psl INNER JOIN thpurchaseline pl ON pl.pnid = psl.fnidline 
               WHERE psl.fnidserial IN ('".strtoupper($serie)."') AND 
                     psl.fnidquantity = 1 AND pl.fniditem = $item AND psl.fnidware = $whscode ";
        //echo " <br /> checkSerieValidToTransfer: ".$q;
        return $mysql->execute($q);

    }

    public static function checkSerieValidToTransferByWhsCode(Mysql $mysql, $serie, $whscode){

        $q = " SELECT psl.fnidserial 
               FROM thpurchaseserial psl INNER JOIN thpurchaseline pl ON pl.pnid = psl.fnidline 
               WHERE psl.fnidserial IN (".strtoupper($serie).") AND 
                     psl.fnidquantity <> 1 AND psl.fnidware <> $whscode ";
        //echo " <br /> checkSerieValidToTransfer: ".$q;
        return $mysql->execute($q);

    }


}

?>