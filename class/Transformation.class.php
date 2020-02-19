<?php
class Transformation
{

  public static function getAllByType(Mysql $mysql, $datefrom = null, $dateto=null, $filter = null)
  {//list all user by role
      $q = " SELECT tc.*, tu.dsuser
            FROM thtransformation tc INNER JOIN tcuser tu ON tu.idtcuser = tc.fniduser ";

      $q .= " WHERE 1 = 1 ";
      if($datefrom != null && $dateto != null)
          $q .= " AND CAST(tc.dddate AS DATE) BETWEEN '$datefrom' AND '$dateto' ";
      else
          $q .= " AND (CAST(tc.dddate AS DATE) BETWEEN DATE_SUB(CURDATE(), INTERVAL 10 DAY) AND CURDATE())";
      //else
      //    $q .= " AND CAST(tc.ddcreated AS DATE) BETWEEN '$datefrom' AND '$dateto' ";

      if($filter != null)
          $q .= " AND (tc.dscomments LIKE '%$filter%' OR tc.dscomments LIKE '%$filter%')";

      if($iduser != null)
          $q .= " AND tc.fniduser = ".$iduser;


      $q .= " ORDER BY tc.pnid DESC "; //.Mysql::getQueryLimit($page);
      //$q = String::sanitize($q,true);
      //echo "<br />".$q;
      return $mysql->execute($q);
  }

  public static function getSerialByIMEI(Mysql $mysql, $code=null, $IMEI=null){

    $q = " SELECT tsel.*, pl.dscode, pl.fniditem
           FROM thpurchaseserial tsel INNER JOIN thpurchaseline pl ON pl.pnid = tsel.fnidline
           WHERE tsel.fnidserial LIKE '".strtoupper($IMEI)."' ";
    //echo "<br />query getSerialByIMEI: ".$q;
    return $mysql->execute($q);
  }

  public static function getLineNewOC(Mysql $mysql, $idoc, $idline){

    $q = "SELECT thidheader, pnid, dscode, fniditem, fnidware FROM thpurchaseline pl WHERE pnid = $idline AND thidheader = $idoc ";
    //echo "<br />query getLineNewOC: ".$q;
    return $mysql->execute($q);

  }

  public static function getRow(Mysql $mysql, $id){

    $q = " SELECT tc.*, tl.*, tu.dsuser, tori.dscode AS 'dscodeori', tdes.dscode AS 'dscodedes'
          FROM thtransformation tc INNER JOIN tcuser tu ON tu.idtcuser = tc.fniduser
                                   INNER JOIN thtransformationline tl ON tl.fnidheader = tc.pnid
                                   INNER JOIN tcitem tori ON tori.pnid = tl.fniditemori
                                   INNER JOIN tcitem tdes ON tdes.pnid = tl.fniditemnew
          WHERE tc.pnid = ".$id;

    return $mysql->execute($q);

  }

  public static function setRowsSerialTransformationByFile(Mysql $mysql, $args){

    $count = 0;
    $refgral = $args['iduser']."_".date("Y-m-d_H-i-s");

    $arrayl = array();

    $line = 0;
    $archivo = fopen("../uploads/logTransformation/".$args['_filenamefinal'], "r");
    $datos = fgetcsv($archivo, ","); //quita cabeceras
    while (($datos = fgetcsv($archivo, ",")) == true) {
        //echo "<br />";

        $item = Transformation::getSerialByIMEI($mysql,$datos[0], $datos[1]);
        $lineocnew = Transformation::getLineNewOC($mysql, $datos[2],$datos[3]);

        $arrayl[$line]['ocori'] = $item[0]->fnidheader;
        $arrayl[$line]['lineori'] = $item[0]->fnidline;
        $arrayl[$line]['itemori'] = $item[0]->fniditem;
        $arrayl[$line]['imei'] = strtoupper($datos[1]);
        $arrayl[$line]['ocdest'] = $datos[2];
        $arrayl[$line]['linedest'] = $datos[3];
        $arrayl[$line]['itemdest'] = $lineocnew[0]->fniditem;
        $arrayl[$line]['fnidware'] = $lineocnew[0]->fnidware;
        $arrayl[$line]['comment'] = $datos[5];
        $line++;

    }//while

    fclose($archivo);

    $mysql->begin();

    $q = "INSERT INTO thtransformation VALUES(null,NOW(),".
         $args['iduser'].",'')";
     //echo "<br />insert thtransformation: ".$q;
    if(!$mysql->update($q)){
      $mysql->rollback();
      return false;
    }

    $q = "SELECT LAST_INSERT_ID() AS 'lastid' ";
    $lastid = $mysql->execute($q);

    for($i=0; $i<count($arrayl); $i++){

        /*se actualizan IMEI al nuevo documento*/
        $q = " UPDATE thpurchaseserial SET fnidline = ".$arrayl[$i]['linedest'].
             ", fnidheader = ".$arrayl[$i]['ocdest']." WHERE fnidserial LIKE '".strtoupper($arrayl[$i]['imei'])."' ";
        //echo "<br /> thpurchaseserial : ".$q;
        if(!$mysql->update($q)){
          $mysql->rollback();
          return false;
        }


        /*se actualizan cantidades pendientes para cada documento origen y destinno*/
        $q = " UPDATE thpurchaseline SET dnopenqty = dnopenqty + 1 WHERE pnid = ".$arrayl[$i]['lineori'];
        //echo "<br /> thpurchaseline ori : ".$q;
        if(!$mysql->update($q)){
          $mysql->rollback();
          return false;
        }

        $q = "UPDATE thpurchaseline SET dnopenqty = dnopenqty - 1 WHERE pnid =".$arrayl[$i]['linedest'];
        //echo "<br /> thpurchaseline dest : ".$q;
        if(!$mysql->update($q)){
          $mysql->rollback();
          return false;
        }

        $q = "UPDATE thpurchase SET dsstatus = 2 WHERE pnid = ".$arrayl[$i]['ocdest'];
        //echo "<br /> thpurchase : ".$q;
        if(!$mysql->update($q)){
          $mysql->rollback();
          return false;
        }

        /*se actualiza inventario para origen y destino*/
        $q = " UPDATE trinventorygral SET ddquantity = ddquantity - 1 WHERE fniditem = ".
            $arrayl[$i]['itemori']." AND fnidware = ".$arrayl[$i]['fnidware'];
        //echo "<br /> trinventorygral : ".$q;
        if(!$mysql->update($q)){
          $mysql->rollback();
          return false;
        }

        $q = "SELECT COUNT(*) AS 'conta' FROM trinventorygral WHERE fniditem = ".
              $arrayl[$i]['itemdest']." AND fnidware = ".$arrayl[$i]['fnidware'];
        //echo "<br /> count trinventorygral new : ".$q;
        $cont = $mysql->execute($q);

        if($cont[0]->conta>0){
            $q = " UPDATE trinventorygral SET ddquantity = ddquantity + 1 WHERE fniditem = ".
                $arrayl[$i]['itemdest']." AND fnidware = ".$arrayl[$i]['fnidware'];
        }else{
            $q = " INSERT INTO trinventorygral VALUES (null,".
                 $arrayl[$i]['fnidware'].",".
                 $arrayl[$i]['itemdest'].",1,'')";
        }
        //echo "<br /> insert o urpdate : ".$q;
        if(!$mysql->update($q)){
          $mysql->rollback();
          return false;
        }

        $q = " INSERT INTO thtransformationline VALUES(null,".$lastid[0]->lastid.
              ",".$arrayl[$i]['ocori'].
              ",".$arrayl[$i]['lineori'].
              ",".$arrayl[$i]['itemori'].
              ",'".strtoupper($arrayl[$i]['imei']).
              "',".$arrayl[$i]['itemdest'].
              ",'".$arrayl[$i]['ocdest'].
              "',".$arrayl[$i]['linedest'].
              ",".$arrayl[$i]['fnidware'].")";
        //echo "<br />insert thtransformationline: ".$q;
        if(!$mysql->update($q)){
          $mysql->rollback();
          return false;
        }

    } //for header
    $mysql->commit();
    return true;
  }//function setRowsSerialTransformationByFile

}//class
