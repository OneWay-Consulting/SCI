<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 03/01/2019
 * Time: 12:30 PM
 */

class Quote
{

    public static function getAllByType(Mysql $mysql, $datefrom = null, $dateto=null, $filter = null, $iduser=null)
    {//list all user by role
        $q = " SELECT tc.*, 'VENTA' AS 'dstype', tcs.pnid AS 'idstatus', tcs.dsname AS 'statusname'  ".
            " ,(SELECT SUM((tql.dnprice * tql.dnquantity) * 1.0) ".
            "  FROM  thquoteline tql ".
            "  WHERE tql.thidheader = tc.pnid) AS 'total' ".
            " FROM thquote tc INNER JOIN tcstatus tcs ON tcs.pnid = tc.dsstatus ";
        $q .= " WHERE 1 = 1  AND tc.dsstatus <> 6 ";
        if($datefrom != null && $dateto != null)
            $q .= " AND CAST(tc.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto' ";
        else
            $q .= " AND (CAST(tc.ddcreated AS DATE) BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE()) ";

        if($filter != null)
            $q .= " AND (tc.dscode LIKE '%$filter%' OR tc.dsname LIKE '%$filter%')";

        if($iduser != null)
            $q .= " AND tc.fnidcreator = ".$iduser;

        $q .= " ORDER BY tc.pnid DESC "; //.Mysql::getQueryLimit($page);
        //$q = String::sanitize($q,true);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getAllByStatus(Mysql $mysql, $datefrom = null, $dateto=null, $filter = null)
    {//list all user by role
        $q = " SELECT tc.*, 'VENTA' AS 'dstype' FROM thquote tc ";
        $q .= " WHERE 1 = 1 AND tc.dsstatus = 3";
        if($datefrom != null && $dateto != null)
            $q .= " AND CAST(tc.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto' ";
        if($filter != null)
            $q .= " AND (tc.dscode LIKE '%$filter%' OR tc.dsname LIKE '%$filter%')";
        $q .= "ORDER BY tc.pnid DESC "; //.Mysql::getQueryLimit($page);
        //$q = String::sanitize($q,true);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getRow(Mysql $mysql,$id) {
        $q="SELECT tu.*, tu.dscode AS 'dscodep', tu.dsname AS 'dsnamep', tl.*, tu.dsstatus AS 'dsstatush', ".
            " tl.pnid AS 'pnidline', tl.dscode AS 'dsitemcode', tl.dsname AS 'dsitemname', ".
            " tl.dsstatus AS 'dsstatusl', tl.dsrefline, tl.dnopenqty, tl.fnidware ".
            " FROM thquote tu INNER JOIN thquoteline tl ON tl.thidheader = tu.pnid ".
            " WHERE tu.pnid='$id' ";
        //echo $q;
        return $mysql->execute($q);
    }//function

    public static function setRow(Mysql $mysql,$args) {
        //print_r($args);
        $mysql->begin();
        $q="INSERT INTO thquote(pnid,fnidpartner,dscode,dsname,fnidcreator,".
            " dsreference,dsstatus,ddcreated,ddupdate,dddocdate,dscomments) " .
            "VALUES (null,".$args['_pnidcliente'].",'".$args['_cliente']."','".$args['_clientename']."',".$args['idcreator'].", ".
            " '".$args['_referencia']."','".$args['_status']."',NOW(),NOW(),NOW(),'".$args['_comentarios']."' )";
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
                $q = "INSERT INTO thquoteline(pnid,thidheader, fniditem, dscode, dsname," .
                    " fnidware,dnquantity,dniva,dnprice,dnopenqty,dsrefline) VALUES( " .
                    " null," . $id[0]->lastid . "," . $args['_iditem'][$i] . ",'" . $args['_art'][$i] . "','" . $args['_desc'][$i] . "'," .
                    $args['_whscode'][$i] . "," . $args['_qty'][$i] . "," . $args['_iva'][$i] . "," . $args['_price'][$i] . "," . $args['_qty'][$i]
                    . ",'".$args['_refline'][$i]."')";
                //echo "<br />".$q;
                //echo "<br />query linea:".$q;
                if (!$mysql->update($q)) {
                    $mysql->rollback();
                    return false;
                }

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
            }
            //}//for

            $mysql->commit();
            return true;
        }//else commit header

    }//function

    public static function updateRowToCancel(Mysql $mysql, $args){

        $flagheader = false;
        $flagupdatecancel = false;
        //print_r($args);

        for($i=0; $i<count($args['_idline']); $i++){
            if(isset($args['_aut_'.$args['_idline'][$i]])) {
                if ($args['_qtyc'][$i] > 0)
                    $flagheader = true;

                if($args['_statuspart'][$i] == "CANCEL"){
                    $q = "UPDATE thquoteline SET dsstatus = 'CANCEL' ".
                        " WHERE pnid = ".$args['_idline'][$i];
                    //echo "<br />UPDATE to cancel:".$q;
                    $mysql->update($q);
                    $flagupdatecancel = true;
                }
            }// if _aut_args
        }


        if(!$flagheader && !$flagupdatecancel)
            return false;

        if($flagheader  && $flagupdatecancel)
            return true;

    }

    public static  function updateRowToSales(Mysql $mysql, $args){

        $flagheader = false;
        $flagupdatecancel = false;
        //print_r($args);

        for($i=0; $i<count($args['_idline']); $i++){
            if(isset($args['_aut_'.$args['_idline'][$i]])) {
                if ($args['_qtyc'][$i] > 0)
                    $flagheader = true;

                if($args['_statuspart'][$i] == "CANCEL"){
                    $q = "UPDATE thquoteline SET dsstatus = 'CANCEL' ".
                         " WHERE pnid = ".$args['_idline'][$i];
                    //echo "<br />UPDATE to cancel:".$q;
                    $mysql->update($q);
                    $flagupdatecancel = true;
                }
            }// if _aut_args
        }
        /*
        echo "cont _aut:".count($args['_aut']);
        }*/

        /*if(!$flagheader && !$flagupdatecancel)
            return false;
        */

        if(!$flagheader  && $flagupdatecancel)
            return true;

        if($args['_status']!=4)
            return true;

        //print_r($args);
        $mysql->begin();
        $q = "INSERT INTO thsales (SELECT null,fnidpartner,dscode,dsname,fnidcreator,".
            "   dsreference,1,NOW(),NOW(),dddocdate,dscomments, 1 ".
            "           FROM thquote WHERE pnid = ".$args['id']." ) ";
        //echo "<br />cabecera:".$q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }

        $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
        //echo "<br />id:".$q;
        //$id = "";
        $id = $mysql->execute($q);

        for($i=0; $i<count($args['_idline']); $i++){

            //if(in_array($args['_idline'][$i],$args['_aut'])){
            //echo "<br />_aut_".$args['_idline'][$i]."  |".$args['_aut_'.$args['_idline'][$i]];
            if(isset($args['_aut_'.$args['_idline'][$i]])){
                //echo "<br />entro a isset de _aut_";
                if ($args['_statuspart'][$i] == "" && $args['_qtyc'][$i] > 0) {

                    $q = "INSERT INTO thsalesline (SELECT null, " . $id[0]->lastid . ", fniditem,dscode,dsname,fnidware," .
                        $args['_qtyc'][$i] . ",dniva, " .
                        "  dnprice,dsguia,dscanal,dsstatus,dspaqueteria, dscomentariol, pnid,null,null, dsrefline, " . $args['_qtyc'][$i] .
                        " FROM thquoteline WHERE pnid = " . $args['_idline'][$i] . ")";
                    //echo "<br />linea:".$q;
                    if (!$mysql->update($q)) {
                        $mysql->rollback();
                        return false;
                    }

                    $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
                    //echo "<br />idlinedest:".$q;
                    $idline = $mysql->execute($q);

                    $q = "UPDATE thquoteline " .
                        " SET dsstatusaut = 'AUTORIZADO', dsuseraut = '" . $args['idcreator'] . "', dnlinedest= " . $idline[0]->lastid .
                        " , dnopenqty = " . ($args['_qtyo'][$i] - $args['_qtyc'][$i]) .
                        " WHERE pnid = " . $args['_idline'][$i];//$idline[0]->lastid;
                    //echo "<br />update quote line:".$q;
                    if (!$mysql->update($q)) {
                        $mysql->rollback();
                        return false;
                    }
                }//_statuspart = cancel
            }//issset
        }

        $q = "UPDATE thquote SET dsstatus = '4' WHERE pnid =  ".$args['id'];
        //echo "<br />update quote:".$q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }

        $mysql->commit();
        return true;

    }//function updateRowToSales

    public static function cancelQuote(Mysql $mysql){

        $q = " SELECT pnid, dddocdate FROM thquote WHERE dddocdate <= DATE_SUB(CURDATE(), INTERVAL 5 DAY) AND dsstatus <> 5  ";
        //echo "<br /> get to cancel:".$q;
        $list = $mysql->execute($q);

        for($i=0; $i<count($list); $i++){
            $q = "UPDATE thquote SET dsstatus = '5'  WHERE pnid = ".$list[$i]->pnid;
            //echo "<br /> update to close:".$q;
            $mysql->update($q);
        }

        return true;
    }

    public static function saveByFile($mysql, $args){

        $count = 0;
        $archivo = fopen("../uploads/".$args['_filenamefinal'], "r");
        //$mysql->begin();

        $arrayh = array();
        $arrayl = array();
        $pedidoc = "";

        $datos = fgetcsv($archivo, ",");
        $datos = fgetcsv($archivo, ",");
        $pedidoc = $datos[2];
        fclose($archivo);


        $counth = 0;
        $item = Item::getRowByCode($mysql, $datos[0]);
        $cliente = Partner::getRowByCode($mysql, $datos[6]);


        $arrayh[$counth]['pedido'] = $datos[2];
        $arrayh[$counth]['idpartner'] = $cliente[0]->pnid;
        $arrayh[$counth]['dscode'] = $cliente[0]->dscode;
        $arrayh[$counth]['dsname'] = $cliente[0]->dsname;
        $arrayh[$counth]['idcreator'] = $args['iduser'];
        $arrayh[$counth]['dsreference'] = $datos[2];
        $arrayh[$counth]['dddocdate'] = $datos[7];
        $arrayh[$counth]['dsstatus'] = 1;
        $arrayh[$counth]['dscomments'] = $datos[10];
        $arrayh[$counth]['lines'] = array();

        $line = 0;
        $archivo = fopen("../uploads/".$args['_filenamefinal'], "r");
        while (($datos = fgetcsv($archivo, ",")) == true) {
            //echo "<br />";
            if ($count > 0) {

                $item = Item::getRowByCode($mysql, $datos[0]);
                $cliente = Partner::getRowByCode($mysql, $datos[6]);

                if($pedidoc == $datos[2]){
                    $arrayl[$line]['pedido'] = $pedidoc;
                    $arrayl[$line]['iditem'] = $item[0]->pnid;
                    $arrayl[$line]['itemcode'] = $item[0]->dscode;
                    $arrayl[$line]['itemname'] = $item[0]->dsname;
                    $arrayl[$line]['qty'] = $datos[1];
                    $arrayl[$line]['guia'] = $datos[3];

                    $itemprice = Item::getAllByFilterBySN($mysql,$item[0]->dscode,$cliente[0]->pnid);
                    $price = 0.0;
                    if($itemprice[0]->pricewithdisc != "")
                        $price = $itemprice[0]->pricewithdisc;

                    $arrayl[$line]['price'] = $price;//$datos[4];
                    $arrayl[$line]['canal'] = $datos[5];
                    $arrayl[$line]['paqueteria'] = $datos[9];
                    $arrayl[$line]['coml'] = $datos[11];
                    $arrayl[$line]['whscode'] = $datos[12];
                    //$arrayh[$counth]['lines'] = $arrayl;
                    $line++;
                }else{
                    $arrayh[$counth]['lines'] = $arrayl;
                    $pedidoc = $datos[2];
                    $line=0;
                    $counth++;

                    $arrayh[$counth]['pedido'] = $datos[2];
                    $arrayh[$counth]['idpartner'] = $cliente[0]->pnid;
                    $arrayh[$counth]['dscode'] = $cliente[0]->dscode;
                    $arrayh[$counth]['dsname'] = $cliente[0]->dsname;
                    $arrayh[$counth]['idcreator'] = $args['iduser'];
                    $arrayh[$counth]['dsreference'] = $datos[2];
                    $arrayh[$counth]['dddocdate'] = $datos[7];
                    $arrayh[$counth]['dsstatus'] = 1;
                    $arrayh[$counth]['dscomments'] = $datos[10];
                    $arrayh[$counth]['lines'] = array();

                    $arrayl = array();
                    $arrayl[$line]['pedido'] = $pedidoc;
                    $arrayl[$line]['iditem'] = $item[0]->pnid;
                    $arrayl[$line]['itemcode'] = $item[0]->dscode;
                    $arrayl[$line]['itemname'] = $item[0]->dsname;
                    $arrayl[$line]['qty'] = $datos[1];
                    $arrayl[$line]['guia'] = $datos[3];

                    $itemprice = Item::getAllByFilterBySN($mysql,$item[0]->dscode,$cliente[0]->pnid);
                    $price = 0.0;
                    if($itemprice[0]->pricewithdisc != "")
                        $price = $itemprice[0]->pricewithdisc;

                    $arrayl[$line]['price'] = $price;//$datos[4];
                    $arrayl[$line]['canal'] = $datos[5];
                    $arrayl[$line]['paqueteria'] = $datos[9];
                    $arrayl[$line]['coml'] = $datos[11];
                    $arrayl[$line]['whscode'] = $datos[12];
                }
            }//if > 0
            $count++;
        }//while

        $arrayh[$counth]['lines'] = $arrayl;

        fclose($archivo);

        $mysql->begin();
        for($i=0; $i<count($arrayh); $i++){

            $q="INSERT INTO thquote(pnid,fnidpartner,dscode,dsname,fnidcreator,".
                " dsreference,dsstatus,ddcreated,ddupdate,dddocdate,dscomments) " .
                "VALUES (null,".$arrayh[$i]['idpartner'].",'".$arrayh[$i]['dscode']."','".$arrayh[$i]['dsname']."',".
                $arrayh[$i]['idcreator'].", ".
                " '".$arrayh[$i]['pedido']."','".$arrayh[$i]['dsstatus']."',NOW(),NOW(),'".$arrayh[$i]['dddocdate']."',".
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

                    $q = "INSERT INTO thquoteline(pnid,thidheader, fniditem, dscode, dsname,".
                        " fnidware,dnquantity,dniva,dnprice,dsguia,dscanal,dsstatus,dspaqueteria,dscomentariol,dnopenqty) VALUES( ".
                        " null,".$id[0]->lastid.",".$arrayh[$i]['lines'][$j]['iditem'].",'".$arrayh[$i]['lines'][$j]['itemcode'].
                        "','".$arrayh[$i]['lines'][$j]['itemname'].
                        "','".$arrayh[$i]['lines'][$j]['whscode']."',".$arrayh[$i]['lines'][$j]['qty'].
                        ",0,".$arrayh[$i]['lines'][$j]['price'].",'".$arrayh[$i]['lines'][$j]['guia'].
                        "','".$arrayh[$i]['lines'][$j]['canal']."','','".$arrayh[$i]['lines'][$j]['paqueteria'].
                        "','".$arrayh[$i]['lines'][$j]['coml']."',".$arrayh[$i]['lines'][$j]['qty'].")";
                    echo "<br />line:".$q;
                    if(!$mysql->update($q)) {
                        //$mysql->commit();
                        $mysql->rollback();
                        return false;

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

    public static function checkReference(Mysql $mysql, $refline, $refgral = null, $idclient = null){
        $q = "SELECT count(*) AS 'cont' ".
             " FROM thquote tq INNER JOIN thquoteline tl ON tl.thidheader = tq.pnid  ".
             " WHERE tl.dsrefline LIKE '$refline' AND tq.fnidpartner = $idclient AND tl.dsstatus NOT LIKE 'CANCEL' ";
        //echo "<br />".$q;
        $list = $mysql->execute($q);

        if($list[0]->cont>0)
            return true;
        else
            return false;

    }//checkReference



}//class
