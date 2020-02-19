<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 11/11/2018
 * Time: 08:22 PM
 */

class Purchase
{

    public static function getAllByType(Mysql $mysql, $datefrom = null, $dateto=null, $filter = null, $iduser = null)
    {//list all user by role
        $q = " SELECT tc.*, 'COMPRAS' AS 'dstype' , tcs.dsname AS 'statusname', ".
             " ( SELECT (SUM(dnquantity * dnprice) + ".
             "      CASE WHEN dniva <= 0 THEN 0 ELSE (SUM(dnquantity * dnprice)*(dniva/100)) END )
                 FROM thpurchaseline WHERE thidheader = tc.pnid  ".
             " ) AS doctotal ".
             "  FROM thpurchase tc INNER JOIN tcstatus tcs ON tcs.pnid = tc.dsstatus ";
        $q .= " WHERE 1 = 1 ";
        if($datefrom != null && $dateto != null)
            $q .= " AND CAST(tc.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto' ";
        if($filter != null)
            $q .= " AND (tc.dscode LIKE '%$filter%' OR tc.dsname LIKE '%$filter%')";

        if($iduser != null)
            $q .= " AND tc.fnidcreator = ".$iduser;

        $q .= " ORDER BY tc.pnid DESC "; //.Mysql::getQueryLimit($page);
        //$q = String::sanitize($q,true);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getAllPages(Mysql $mysql, $page)
    {
        if (!$page || $page < 0) $page = 0;
        $q = "SELECT count(*) as total FROM thpurchase";
        $tmp = $mysql->execute($q);
        $pags = ceil($tmp[0]->total / LIMIT);
        if ($page >= $pags) $page = $pags - 1;
        return array($tmp[0]->total, $pags, $page, LIMIT * $page);
    }//function

    public static function getRow(Mysql $mysql,$id) {
        $q=" SELECT tu.*, wh.dscode AS 'dswarename', ".
           " tl.dscode AS 'dsitemcode', tl.dsname AS 'dsitemname', tl.pnid AS 'idline', ".
           " tl.thidheader, tl.fniditem, tl.fnidware, tl.dnquantity, tl.dniva, tl.dnprice, tl.dnopenqty, ".
           " tl.ddarrive, tci.dsupc, tci.dsupc2, tl.dsref , tci.dsserial AS 'manser' ".
           " FROM thpurchase tu INNER JOIN thpurchaseline tl ON tl.thidheader = tu.pnid ".
           "                    INNER JOIN tcwarehouse wh ON wh.pnid = tl.fnidware ".
           "                    INNER JOIN tcitem tci ON tci.pnid = tl.fniditem ".
           " WHERE tu.pnid='$id'";
        //echo "<br />getRow: ".$q;
        return $mysql->execute($q);
    }//function

    public static function getSerialByRow(Mysql $mysql,$id){

        $q = "SELECT tpl.pnid AS 'linea', tpl.fniditem AS 'iditem', tpl.dscode AS 'itemcode',".
                          " tps.fnidserial AS 'serial', tps.fnidquantity AS 'quantity' ".
            " FROM thpurchase tp INNER JOIN thpurchaseline tpl ON tpl.thidheader = tp.pnid ".
            "                    INNER JOIN thpurchaseserial tps ON tps.fnidline = tpl.pnid AND tps.fnidheader = tp.pnid ".
            " WHERE tp.pnid = $id ";
        //echo "<br />getSerialByRow: ".$q;
        return $mysql->execute($q);
    }

    public static function setRow(Mysql $mysql,$args) {
        //print_r($args);
        //$mysql->begin();
        $q="INSERT INTO thpurchase(pnid,fnidpartner,dscode,dsname,fnidcreator,".
            " dsreference,dsstatus,ddcreated,ddupdate,dddocdate) " .
            "VALUES (null,".$args['_pnidcliente'].",'".$args['_cliente']."','".$args['_clientename']."',".$args['idcreator'].", ".
             " '".$args['_referencia']."','".$args['_status']."',NOW(),NOW(),NOW())";
        //echo "<br />query header:".$q;
        if(!$mysql->update($q)){
            //$mysql->rollback();
            return false;
        }else{
            $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
            //echo "<br />".$q;
            $id = $mysql->execute($q);
            //var_dump($id);
            //print_r($args);
            for($i=0; $i<count($args['_art']); $i++){

                if($args['_arrive'][$i]!="")
                    $ddate = "'".$args['_arrive'][$i]."'";
                else
                    $ddate = "NULL";

                  $q = " INSERT INTO thpurchaseline(pnid,thidheader, fniditem, dscode, dsname,".
                       " fnidware,dnquantity,dniva,dnprice,dnopenqty,ddarrive,dsref) VALUES( ".
                       " null,".$id[0]->lastid.",".$args['_iditem'][$i].",'".$args['_art'][$i]."','".$args['_desc'][$i]."',".
                      $args['_whscode'][$i].",".$args['_qty'][$i].",".$args['_iva'][$i].",".$args['_price'][$i].",".
                      $args['_qty'][$i].",$ddate,'".$args['_refline'][$i]."')";
                 // echo "<br />".$q;
                //echo "<br />query linea:".$q;
                $mysql->update($q);
                  //if($mysql->update($q)) {
                  //    $mysql->commit();
                      //return true;

                      //$q = "SELECT LAST_INSERT_ID() AS 'lastid'";
                      //$idline = $mysql->execute($q);

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
                  //}else{//if update lines
                  //    $mysql->rollback();
                  //    return false;
                  //}//else
            }//for

            //$mysql->commit();
            return true;
        }//

    }//function

    public static function updateRow(Mysql $mysql, $args){

        $mysql->begin();

        //print_r($args);
        $q = "SELECT pnid FROM thpurchaseline WHERE thidheader = ".$args['id'];
        $line = $mysql->execute($q);
        print_r($line);

        $q = "UPDATE thpurchase SET dsstatus = ".$args['_status'].
            " WHERE pnid = ".$args['id'];
        //echo "<br />1: ".$q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }else{
            //echo "<br />".count($args['_idline']);
            //print_r($args['_idline']);
            $allid = array();
            $countid = 0;
            for($i=0; $i<count($args['_idline']); $i++){
                //echo "<br />--".$args['_idline'][$i];
                //discount

                if($args['_idline'][$i]!=""){
                    $price = str_replace(",","",$args['_price'][$i]);
                    $price = str_replace("$","",$price);
                    $q = "UPDATE thpurchaseline SET dnprice = ".$price.",".
                        " dnquantity = ".$args['_qty'][$i].
                        " WHERE pnid = ".$args['_idline'][$i];
                    $allid[$countid++] = $args['_idline'][$i];
                    //echo "<br />update thpurchaseline".$q;
                }else{
                    $price = str_replace(",","",$args['_price'][$i]);
                    $price = str_replace("$","",$price);
                    $q = "INSERT INTO `thpurchaseline`(`pnid`, `thidheader`, `fniditem`, `dscode`, `dsname`, `fnidware`,".
                        " `dnquantity`, dniva, `dnprice`, `dnopenqty` ) VALUES (".
                        " NULL,".$args['id'].",".$args['_iditem'][$i].",'".$args['_art'][$i]."','".$args['_desc'][$i]."',".
                        $args['_whscode'][$i].",".$args['_qty'][$i].",".$args['_iva'][$i].",".$args['_price'][$i].",".
                        $args['_qty'][$i].")";
                    //echo "<br />insert thpurchaseline".$q;
                }//if idline != ""
                $mysql->update($q);
                //echo  "<br />".$q;

            }//for update or insert lines
            //for delete row
            //print_r($allid);
            for($i=0; $i<count($line); $i++) {
                if(!in_array($line[$i]->pnid,$allid)){
                    //echo "<br /> DELETE FROM tdlineosales WHERE idline = " . $line[$i]->idline;
                    $q = "DELETE FROM thpurchaseline WHERE pnid = ".$line[$i]->pnid;
                    //echo "<br />5. delete from thpurchaseline".$q;
                    $mysql->update($q);
                }//if
            }//for to delete

        } // else update success header

        $mysql->commit();
        return true;

    }//function updateRow

    public static function updateRowOLD(Mysql $mysql, $args){

         $q="UPDATE tcitem SET dscode='".$args['_code']."', dsname='".$args['_name']."'".
            " , dsactive=".$args['_active'].", dscomments='".$args['_comments']."',  ".
            " dsserial=".$args['_serie']." ".
            " WHERE pnid='".$args['id']."'";

        //return
        //echo "<br />".$q;
        $mysql->update($q);


        $idnotexist = "";

        for($i=0; $i<count($args['ware']); $i++){
            $q = "SELECT * FROM tritemware WHERE fnidware = ".$args['ware'][$i]." AND fniditem = ".$args['id'];
            //echo "<br />".$q;
            $rowtemp = $mysql->execute($q);
            //if($args['ware'][$i])
            $idnotexist .= $args['ware'][$i].",";

            if($rowtemp>0){
                $q = "UPDATE tritemware SET dbactive=1 WHERE fnidware = ".$args['ware'][$i]." AND fniditem = ".$args['id'];
            }else{
                $q = "INSERT INTO tritemware(pnidtr,fnidware, fniditem, dbactive) VALUES(null,".$args['ware'][$i].",".$args['id'].",1)";
            }

            echo "<br />QUERY lines OC:".$q;
            $mysql->update($q);
        }//for

        $idnotexist .= "0";
        $q = " UPDATE tritemware SET dbactive = 0 WHERE fniditem=".$args['id']." AND fnidware NOT IN (".$idnotexist.") ";
        //echo "<br />".$q;

        //echo "<br />".$q;
        return $mysql->update($q);
    }//update row

    /*inventory purchase*/
    public static function setRowsSerialPurchase(Mysql $mysql, $args){

        $mysql->begin();

        // log de inserciones con series */
            try{
              $filename = "../uploads/logInventoryEntry/OC_".$args['id']."_".$args['idcreator']."_".date("d_m_Y_H_i_s").".csv";
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

        //print_r($args);

        $q = "INSERT INTO thentry VALUES (null,NOW(), '".$args['id']."'); ";
        $mysql->update($q);

        $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
        $identry = $mysql->execute($q);


        /*code to calculate cost*/
        //$q = "SELECT pl.fniditem, pl.dnquantity, pl.dnprice, (pl.dnquantity * pl.dnprice) AS 'monto' ".
        //    " FROM thpurchaseline pl WHERE thidheader = ".$args['id'];


        $exist = array();
        for($i=0; $i<count($args['_art']); $i++) {
            $q = " SELECT * FROM trcost WHERE fniditem = ".$args['_iditem'][$i];
            $row = $mysql->execute($q);

            if(count($row)>0)
                $exist[$i]=1;
            else
                $exist[$i]=0;
        }

        /*end code to calculate cost*/

        for($i=0; $i<count($args['_art']); $i++){


          if($args['_qtyr'][$i] > 0){

            //echo "< br />======line1:".$args['_art'][$i]."=============";
            /* code to calculate cost*/

            $q = " SELECT trg.fniditem, trg.ddquantity, tc.dscode, tc.dddate, pl.dnquantity,
                	   pl.dnprice, (pl.dnquantity * pl.dnprice) AS 'costnew',
                       CASE WHEN tc.ddcostinv IS NULL THEN 0 ELSE tc.ddcostinv END AS 'costact',
                       CASE WHEN tc.ddqtyinv IS NULL THEN 0 ELSE tc.ddqtyinv END AS 'stockact'
                 FROM trinventorygral trg LEFT JOIN trcost tc ON tc.fniditem = trg.fniditem
                 						 INNER JOIN thpurchaseline pl ON pl.fniditem = trg.fniditem
                 WHERE pl.thidheader =  ".$args['id']." AND pl.fniditem = ".$args['_iditem'][$i];
            if($exist[$i]==1)
                $q.=" AND tc.dddate = (SELECT dddate FROM trcost WHERE fniditem = pl.fniditem ORDER BY pnid DESC LIMIT 1 )";
          //echo "<br />1. to get last cost:".$q;
            $tocost = $mysql->execute($q);

            if(count($tocost)>0){
                $qtyant = $tocost[0]->stockact;
                $costant = $tocost[0]->costact;
                $costnewtmp = $tocost[0]->costnew;
            }else{
                $qtyant = 0;
                $costant = 0.0;
                $costnewtmp = $args['_price'][$i] * $args['_qtyr'][$i];
            }



            $qtynew = $qtyant + $args['_qtyr'][$i];
            $costnew = (($costant * $qtyant) + ($costnewtmp)) / ($qtynew);


            $q = "INSERT INTO trcost VALUES(null,".$args['_iditem'][$i].",'".$args['_art'][$i].
                 "',".$qtynew.",".round($costnew,2,PHP_ROUND_HALF_UP).",".
                 $costant.",".$qtyant.",".$args['id'].",NOW(),1)";
            //echo "<br />2. trcost: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            /*end code to calculate cost*/


            $q = " INSERT INTO trinventory(pnid,fnidware,fniditem,ddquantity,dsdirection, dsorigen, dslinebase, dscomments) ".
                " SELECT NULL, fnidware, fniditem, ".$args['_qtyr'][$i].", 1,".$args['id'].",'".$args['_idline'][$i]."','' ".
                " FROM thpurchaseline ".
                " WHERE thidheader = ".$args['id']." AND pnid = ".$args['_idline'][$i];
            //echo "<br />3. query trinventory: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            $q = " SELECT * FROM trinventorygral WHERE fniditem = ".$args['_iditem'][$i]." AND fnidware = ".$args['_whscode'][$i];
            //echo "<br />4. query trinventorygral: ".$q;
            $row = $mysql->execute($q);
            if(count($row)>0) {
                $q = "UPDATE trinventorygral SET ddquantity = ddquantity + " . $args['_qtyr'][$i] . " WHERE fniditem = " . $args['_iditem'][$i]." AND fnidware = ".$args['_whscode'][$i];
            }else{
                $q = "INSERT INTO trinventorygral VALUES(null,1,".$args['_iditem'][$i].",".$args['_qtyr'][$i].",'')";
            }
            //echo "<br />5. query insert o update trinventorygral:".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            //update open qty in purchase order
            $q = "UPDATE  thpurchaseline SET dnopenqty = (dnopenqty - ".$args['_qtyr'][$i].")".
                 " WHERE pnid = ".$args['_idline'][$i]." AND thidheader = ".$args['id'];
            //echo "<br />update openqty: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
            //end update open qty in purchase order


            //echo "<br />FIN ======= FIN ITEMS ======";


            }//if _qtyr > 0

        }//for items
        //$mysql->commit();

        //$mysql->begin();

        //if()
        for ($j = 0; $j < count($args['_linea']); $j++) {

        //if($args['_qtyr'][$j]>0){


                //echo "<br /> ****** INVENTARIOS ****".$args['_linea'][$j];


                $q = "SELECT pnid FROM trinventory WHERE dsorigen = ".$args['id']." AND dslinebase = ".$args['_linea'][$j];
                //echo "<br /> 1. consulta trinventory".$q;
                $idline = $mysql->execute($q);
                //print_r($idline);

                $q = "INSERT INTO thpurchaseserial(pnid, fnidline,fnidserial,fnidquantity, fnidheader,fnidheaderentry) VALUES " .
                    " (null," . $args['_linea'][$j] . ",'" .strtoupper($args['_seriea'][$j]) . "',1," . $args['id'].",".$identry[0]->lastid.")";
                //echo "<br />2. query purchase serial:".$q;
                if(!$mysql->update($q)){
                    $mysql->rollback();
                    return false;
                }

                $q = "INSERT INTO trinventoryserial(pnid, fnidware,fniditem,dsserial, fnidinventory, dnquantity) VALUES " .
                    " (null, NULL, " . $args['_idarticlea'][$j] . ",'" .strtoupper($args['_seriea'][$j]). "'," . $idline[0]->pnid.",1)";
                //echo "<br />3.query trinventory serial:".$q;
                if(!$mysql->update($q)){
                    $mysql->rollback();
                    return false;
                }

              //  }//_qtyr > 0
        }//for



        $q = "UPDATE thpurchase SET dsstatus = 2 WHERE pnid = ".$args['id'];
        //echo "<br />query update thpurchse serial:".$q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }

        $mysql->commit();
        //$mysql->rollback();
        return true;
    }

    public static function setRowsSerialPurchaseByFile(Mysql $mysql, $args){

        $debug = 0;

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

        $args['id'] = $datos[0];

        fclose($archivo);

        $i=0;
        $arr_qty = array();
        $arr_cod = array();

        $archivo = fopen("../uploads/".$args['_filenamefinal'], "r");
        while (($datos = fgetcsv($archivo, ",")) == true) {
            //echo "<br />";
            if ($count > 0) {
                $item=Item::getRowByCode($mysql, $datos[2]);

                if(array_key_exists($datos[1],$arr_cod)){
                    $arr_cod[$datos[1]]=$arr_cod[$datos[1]]+1;
                }else{
                    $arr_cod[$datos[1]]=1;
                    $args['_art'][count($args['_art'])]= $datos[2];
                    $args['_linea'][count($args['_linea'])]= $datos[1];
                    $args['_iditemGral'][count($args['_iditemGral'])]= $item[0]->pnid;
                }


                $args['_iditem'][$i] = $item[0]->pnid;
                $row=Purchase::getRowDetail($mysql, $datos[0], $datos[1], $datos[2]);
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


        $mysql->begin();

        //print_r($args);

        $exist = array();
        for($i=0; $i<count($args['_art']); $i++) {
            $q = " SELECT * FROM trcost WHERE dscode = ".$args['_iditem'][$i];
            $row = $mysql->execute($q);

            if(count($row)>0)
                $exist[$i]=1;
            else
                $exist[$i]=0;

        }

        /*end code to calculate cost*/

        for($i=0; $i<count($args['_art']); $i++){
            if($debug)
                echo "< br />======line1:".$args['_art'][$i]."=============: i:".$i;
            /* code to calculate cost*/
            $q = " SELECT trg.fniditem, trg.ddquantity, tc.dscode, tc.dddate, pl.dnquantity,
                	   pl.dnprice, (pl.dnquantity * pl.dnprice) AS 'costnew',
                       CASE WHEN tc.ddcostinv IS NULL THEN 0 ELSE tc.ddcostinv END AS 'costact',
                       CASE WHEN tc.ddqtyinv IS NULL THEN 0 ELSE tc.ddqtyinv END AS 'stockact'
                 FROM trinventorygral trg LEFT JOIN trcost tc ON tc.fniditem = trg.fniditem
                 						 INNER JOIN thpurchaseline pl ON pl.fniditem = trg.fniditem
                 WHERE pl.thidheader =  ".$args['id']." AND pl.fniditem = ".$args['_iditemGral'][$i];
            if($exist[$i]==1)
                $q.=" AND tc.dddate = (SELECT dddate FROM trcost WHERE fniditem = pl.fniditem ORDER BY pnid DESC LIMIT 1 )";
            if($debug)
                echo "<br />1. to get last cost:".$q;
            $tocost = $mysql->execute($q);

            if(count($tocost)>0){
                $qtyant = $tocost[0]->stockact;
                $costant = $tocost[0]->costact;
                $costnewtmp = $tocost[0]->costnew;
            }else{
                $qtyant = 0;
                $costant = 0.0;
                $costnewtmp = $args['_price'][$i] * $arr_cod[$args['_linea'][$i]]; //$args['_qtyr'][$i];
            }



            $qtynew = $qtyant + $arr_cod[$args['_linea'][$i]];//$args['_qtyr'][$i];
            $costnew = (($costant * $qtyant) + ($costnewtmp)) / ($qtynew);


            $q = "INSERT INTO trcost VALUES(null,".$args['_iditemGral'][$i].",'".$args['_art'][$i].
                "',".$qtynew.",".round($costnew,2,PHP_ROUND_HALF_UP).",".
                $costant.",".$qtyant.",".$args['id'].",NOW(),1)";
            if($debug)
                echo "<br />2. trcost: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            /*end code to calculate cost*/
            if($debug){
                echo "<br /> ==============================";
                print_r($arr_cod);
                echo "<br /> ==============================";
                print_r($args['_linea']);
                echo "<br /> ==============================";
            }

            $q = " INSERT INTO trinventory(pnid,fnidware,fniditem,ddquantity,dsdirection, dsorigen, dslinebase, dscomments) ".
                " SELECT NULL, fnidware, fniditem, ".$arr_cod[$args['_linea'][$i]].", 1,".$args['id'].",'".$args['_linea'][$i]."','' ".
                " FROM thpurchaseline ".
                " WHERE thidheader = ".$args['id']." AND pnid = ".$args['_linea'][$i];
            if($debug)
                echo "<br />3. query trinventory: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            $q = " SELECT * FROM trinventorygral WHERE fniditem = ".$args['_iditemGral'][$i];
            if($debug)
                echo "<br />4. query trinventorygral: ".$q;
            $row = $mysql->execute($q);
            if(count($row)>0) {
                $q = "UPDATE trinventorygral SET ddquantity = ddquantity + " .  $arr_cod[$args['_linea'][$i]] . " WHERE fniditem = " . $args['_iditemGral'][$i];
            }else{
                $q = "INSERT INTO trinventorygral VALUES(null,1,".$args['_iditemGral'][$i].",". $arr_cod[$args['_linea'][$i]].",'')";
            }
            if($debug)
                echo "<br />5. query insert o update trinventorygral:".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            //update open qty in purchase order
            $q = "UPDATE  thpurchaseline SET dnopenqty = (dnopenqty - ". $arr_cod[$args['_linea'][$i]].")".
                " WHERE pnid = ".$args['_linea'][$i]." AND thidheader = ".$args['id'];
            if($debug)
                echo "<br />update openqty: ".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
            //end update open qty in purchase order


            //echo "<br />FIN ======= FIN ITEMS ======";
        }//for items
        //$mysql->commit();

        //$mysql->begin();

        for ($j = 0; $j < count($args['_idline']); $j++) {

            //echo "<br /> ****** INVENTARIOS ****".$args['_linea'][$j];


            $q = "SELECT pnid FROM trinventory WHERE dsorigen = ".$args['id']." AND dslinebase = ".$args['_idline'][$j];
            if($debug)
                echo "<br /> 1. consulta trinventory".$q;
            $idline = $mysql->execute($q);
            //print_r($idline);

            $q = "INSERT INTO thpurchaseserial(pnid, fnidline,fnidserial,fnidquantity, fnidheader,fnidware) VALUES " .
                " (null," . $args['_idline'][$j] . ",'" .strtoupper($args['_seriea'][$j]). "',1," . $args['id'].",1)";
            if($debug)
                echo "<br />2. query purchase serial:".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }

            $q = "INSERT INTO trinventoryserial(pnid, fnidware,fniditem,dsserial, fnidinventory, dnquantity) VALUES " .
                " (null, 1, " . $args['_iditem'][$j] . ",'" .strtoupper($args['_seriea'][$j]). "'," .$idline[0]->pnid.",0)";
            if($debug)
                echo "<br />3.query trinventory serial:".$q;
            if(!$mysql->update($q)){
                $mysql->rollback();
                return false;
            }
        }//for

        $q = "UPDATE thpurchase SET dsstatus = 2 WHERE pnid = ".$args['id'];
        if($debug)
            echo "<br />query update thpurchse serial:".$q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }

        $mysql->commit();
        //$mysql->rollback();

        return true;

    }

    public static function checkSerieIntoInventoryEntry(Mysql $mysql, $args){

        $q = "SELECT fnidserial FROM thpurchaseserial WHERE fnidserial IN (".strtoupper($args).") ";
        //echo "checkSerieIntoInventoryEntry: ".$q;
        return $mysql->execute($q);

    }

    public static function getRowDetail(Mysql $mysql, $idheader, $idline, $sku){
        $q = "SELECT * FROM thpurchaseline ".
             " WHERE thidheader = $idheader  AND dscode LIKE '".strtoupper($sku)."' AND pnid = $idline ";
        return $mysql->execute($q);

    }//function getRowDetail

    public static function getHistoryByIMEI(Mysql $mysql,$imei){

        $q = "SELECT th.pnid, th.dscode , th.dsname , th.ddcreated, tl.pnid AS 'idline', 
                     tl.fniditem, tl.dscode AS 'itemcode', tl.dsname AS 'itemname', ts.fnidserial,
                     ten.dddate AS 'fecentry', ts.fnidheaderentry 
              FROM thpurchase th INNER JOIN thpurchaseline tl ON tl.thidheader = th.pnid 
				                 INNER JOIN thpurchaseserial ts ON ts.fnidline = tl.pnid 
                                 LEFT JOIN 	thentry ten ON ten.pnid = fnidheaderentry 			                 
              WHERE ts.fnidserial LIKE '".strtoupper($imei)."' ";
        //echo "<br /> getHistoryByIMEI:".$q;
        return $mysql->execute($q);
    }
}
