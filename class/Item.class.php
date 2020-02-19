<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 11/11/2018
 * Time: 08:22 PM
 */

class Item
{

    public static function getAllByType(Mysql $mysql, $type = null, $page = null)
    {//list all user by role
        $q = " SELECT tc.* FROM tcitem tc " .
             " ORDER BY tc.dscode ASC "; //.Mysql::getQueryLimit($page);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getAllByFilter(Mysql $mysql, $filter = null){
        $q = " SELECT tc.*, 0.00  AS 'pricewithdisc'  FROM tcitem tc ";
        $q .= " WHERE dsactive = 1 ";
        if($filter != null)
            $q .= " AND ( tc.dscode LIKE '%".trim($filter)."%' OR ".
                  "       tc.dsname LIKE '%".trim($filter)."%' OR ".
                  "       tc.dsupc LIKE '%".trim($filter)."%' OR ".
                  "       tc.dsupc2 LIKE '%".trim($filter)."%' ) ";
        $q .= " ORDER BY tc.dscode, dsname DESC ";
        //echo "<br />getAllByType: ".$q;
        return $mysql->execute($q);
    }//function getAllByFilter

    public static function getAllByFilterExactly(Mysql $mysql, $filter = null){
        $q = " SELECT tc.*, 0.00  AS 'pricewithdisc'  FROM tcitem tc ";
        $q .= " WHERE dsactive = 1 ";
        if($filter != null)
            $q .= " AND ( tc.dscode LIKE '".trim($filter)."') ";
        $q .= " ORDER BY tc.dscode, dsname DESC ";
        //echo "<br />getAllByFilterExactly: ".$q;
        return $mysql->execute($q);
    }

    public static function getAllByFilterBySN(Mysql $mysql, $filter, $idns=null){

/*        $q = " SELECT item.pnid, item.dscode, item.dsname, item.dsserial, pl.fniditem, pl.ddprice, ".
            " tp.dddiscountperc, (pl.ddprice - ((tp.dddiscountperc * pl.ddprice)/100 )) AS 'pricewithdisc' ".
            " FROM tcitem item LEFT JOIN thpricelistitem pl ON pl.fniditem = item.pnid ".
            "                 INNER JOIN thpricelist thpl ON pl.fnidheader = thpl.idlist ".
            "                 INNER JOIN tcpartner tp ON tp.pnid = ".$idns;
*/
        $q = " SELECT item.pnid, item.dscode, item.dsname, item.dsserial, pl.fniditem, pl.ddprice, ".
            "         tp.dddiscountperc, pl.ddprice AS 'pricewithdisc' ".
            " FROM tcitem item LEFT JOIN thpricelistitem pl ON pl.fniditem = item.pnid ".
            "                 INNER JOIN thpricelist thpl ON pl.fnidheader = thpl.idlist ".
            "                 INNER JOIN tcpartner tp ON tp.pnid = ".$idns;

        $q .= " WHERE item.dsactive = 1 AND thpl.idlist = 1 "; //"AND thpl.fnidclient = ".$idns; por que solo valida base
        if($filter != null)
            $q .= " AND ( item.dscode LIKE '%".trim($filter)."%' OR ".
                  "     item.dsname LIKE '%".trim($filter)."%' OR ".
                  "     item.dsupc LIKE '%".trim($filter)."%') ";

        $q .= " ORDER BY `item`.`dscode` ASC ";
        //echo "<br />getAllByFilterBySN".$q;
        return $mysql->execute($q);
    }

    public static function getAllByFilterBySNAndWhsCode(Mysql $mysql, $filter, $idns=null, $idware =1){

        $q = " SELECT item.pnid, item.dscode, item.dsname, item.dsserial, pl.fniditem, pl.ddprice, ".
            "         tp.dddiscountperc, pl.ddprice AS 'pricewithdisc', tg.ddquantity AS 'stock'  ".
            " FROM tcitem item LEFT JOIN thpricelistitem pl ON pl.fniditem = item.pnid ".
            "                 INNER JOIN thpricelist thpl ON pl.fnidheader = thpl.idlist ".
            "                 LEFT JOIN tcpartner tp ON tp.pnid = ".$idns.
            "                 LEFT JOIN trinventorygral tg ON tg.fniditem = item.pnid ";

        $q .= " WHERE item.dsactive = 1 AND thpl.idlist = 1 AND tg.fnidware = ".$idware; //"AND thpl.fnidclient = ".$idns; por que solo valida base

        if($filter != null)
            $q .= " AND ( item.dscode LIKE '%".trim($filter)."%' OR ".
                "     item.dsname LIKE '%".trim($filter)."%' OR ".
                "     item.dsupc LIKE '%".trim($filter)."%') ";

        $q .= " ORDER BY `item`.`dscode` ASC ";
        //echo "<br />getAllByFilterBySNAndWhsCode".$q;
        return $mysql->execute($q);

    }

    public static function getAllByFilterBySNPriceBase(Mysql $mysql, $filter, $idns){
        $q = " SELECT item.pnid, item.dscode, item.dsname, item.dsserial, pl.fniditem, pl.ddprice, ".
            " pl.ddprice AS 'pricewithdisc' ".
            " FROM tcitem item LEFT JOIN thpricelistitem pl ON pl.fniditem = item.pnid ".
            "                 INNER JOIN thpricelist thpl ON pl.fnidheader = thpl.idlist ";


        $q .= " WHERE item.dsactive = 1 AND thpl.fnidclient = 0 ";
        if($filter != null)
            $q .= " AND ( item.dscode LIKE '".trim($filter)."' OR item.dsname LIKE '".trim($filter)."' ) ";

        $q .= " ORDER BY `item`.`dscode` ASC ";

        //if($filter=="961801")
        //    echo "<br />getAllByFilterBySN".$q;

        return $mysql->execute($q);
    }

    /*CODE to manager price list*/

    public static function getAllByBasePrice(Mysql $mysql, $filter = null,$fnidware = 1){

            $q = " SELECT item.pnid, item.dscode, item.dsname, item.dsserial, pl.fniditem, pl.ddprice, ".
                "         pl.ddprice AS 'pricewithdisc', invg.ddquantity AS 'stock', ".
                " (SELECT SUM(tlsl.dnquantity)
                   FROM thsales tls INNER JOIN thsalesline tlsl
                   WHERE tls.dsstatus = 1 AND tlsl.thidheader = tls.pnid AND ".
                "        tlsl.fniditem = ".$filter.")  AS 'stockavailable'".
                " FROM tcitem item LEFT JOIN thpricelistitem pl ON pl.fniditem = item.pnid ".
                "                 INNER JOIN thpricelist thpl ON pl.fnidheader = thpl.idlist AND thpl.dbbase = 1 ".
                "                 LEFT JOIN trinventorygral invg ON invg.fniditem = item.pnid  ";
            $q .= " WHERE item.dsactive = 1 AND thpl.dbbase = 1 AND item.pnid = ".$filter." AND invg.fnidware = $fnidware ";
            /*if($filter != null)
                $q .= " AND ( item.dscode LIKE '%".trim($filter)."%' OR item.dsname LIKE '%".trim($filter)."%' ) ";
            */
            $q .= " ORDER BY `item`.`dscode` ASC ";
            //echo "<br />getAllByBasePrice:".$q;
            return $mysql->execute($q);

    }//function getAllByBasePrice

    public static function getAllPriceList(Mysql $mysql, $idlist = null){

        $q = "SELECT *, pl.dsname AS 'namelist', pl.dnactive AS 'statuslist', tp.dsname AS 'namesn', tp.dscode AS 'codesn' ".
             " FROM thpricelist pl LEFT JOIN tcpartner tp ON tp.pnid = pl.fnidclient ";
        if($idlist != null)
            $q .= " WHERE pl.idlist = ".$idlist;
        return $mysql->execute($q);

    }

    public static function getAllItemByList(Mysql $mysql, $idlist){

        /*
        $q = "SELECT ti.pnid, ti.dscode, ti.dsname, tri.pnidtr, tri.fnidheader, tri.ddprice AS 'precioPL', tri.ddprice AS 'precioBase'
FROM tcitem ti LEFT JOIN thpricelistitem tri ON tri.fniditem = ti.pnid AND tri.fnidheader = ".$idlist."
               LEFT JOIN thpricelistitem trib ON trib.fniditem = ti.pnid AND trib.fnidheader = 1
WHERE ti.dsactive = 1
ORDER BY ti.dscode ASC";
*/
        $q = "SELECT ti.pnid, ti.dscode, ti.dsname, tri.pnidtr, tri.fnidheader, tri.ddprice AS 'precioPL', tri.ddprice AS 'precioBase'
FROM tcitem ti LEFT JOIN thpricelistitem tri ON tri.fniditem = ti.pnid AND tri.fnidheader = ".$idlist."
WHERE ti.dsactive = 1
ORDER BY ti.dscode ASC";
        //echo "<br />getAllItemByList:".$q;
        return $mysql->execute($q);
    }//all items in the pricelist

    public static function setPLRow(Mysql $mysql, $args){

        $q = "INSERT INTO thpricelist VALUES(null,".$args['_cliente'].",'".$args['_name']."',".$args['_active'].",0)";
        //echo "<br /> insert cabecera".$q;
        $mysql->update($q);

        $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
        $idline = $mysql->execute($q);

        for($i=0; $i<count($args['iditem']); $i++){
            $q = " INSERT INTO thpricelistitem VALUES(null,".$idline[0]->lastid.",".$args['iditem'][$i].",'".
                  $args['codeitem'][$i]."',".$args['pricel'][$i].")";
            //echo "<br /> insert line".$q;
            $mysql->update($q);
        }

        return true;

    }//function

    public static function updatePLRow(Mysql $mysql, $args){

        $q = "UPDATE thpricelist SET dnactive = ".$args['_active']." WHERE idlist = ".$args['id'];
        //echo "<br /> update thpricelist query:".$q;
        $mysql->update($q);

        //print_r($args['pricel']);

        for($i=0; $i<count($args['idtr']); $i++){
            if($args['idtr'][$i]==""){
               // echo "<br />i:".$i;
               // echo "<br />i pricel:".$args['pricel'][$i];
                $q = " INSERT INTO thpricelistitem VALUES(null,".$args['id'].",".$args['iditem'][$i].",'".$args['codeitem'][$i]."',".$args['pricel'][$i].") ";
            }else{
                $q = " UPDATE thpricelistitem SET ddprice = ".$args['pricel'][$i]." WHERE pnidtr = ".$args['idtr'][$i];
            }
            //echo "<br /> update thpricelistitem query:".$q;
            $mysql->update($q);
        }

        return true;
    }

    public static function saveByFile(Mysql $mysql, $args){

        $count = 0;
        $arrayl = array();
        $line = 0;

        $archivo = fopen("../uploads/".$args['_filenamefinal'], "r");
        while (($datos = fgetcsv($archivo, ",")) == true) {
            //echo "<br />";
            if ($count > 0) {

                    $arrayl[$line]['idlist'] = $datos[0];
                    $arrayl[$line]['sku'] = $datos[2];
                    $arrayl[$line]['price'] = $datos[4];

                    $line++;
            }//if > 0
            $count++;
        }//while


        //fclose($archivo);

        for($i=0; $i<count($arrayl); $i++){

            $q = "SELECT fniditem FROM thpricelistitem WHERE fnidheader = ".$arrayl[$i]['idlist'].
                 " AND dsitemcode = '".$arrayl[$i]['sku']."'";
            //echo "<br />consulta existe: ".$q;
            $res = $mysql->execute($q);

            if( count($res) > 0)
                $q = " UPDATE thpricelistitem SET ddprice = ".$arrayl[$i]['price'].
                    " WHERE fnidheader = ".$arrayl[$i]['idlist']." AND dsitemcode = '".$arrayl[$i]['sku']."'";
             else {
                 $q = "SELECT pnid FROM tcitem WHERE dscode = '".$arrayl[$i]['sku']."'";
                 //echo "<br />consulta tcitem itemcode: ".$q;
                 $res2 = $mysql->execute($q);

                 $q = " INSERT INTO thpricelistitem VALUES(null," . $arrayl[$i]['idlist'] . "," . $res2[0]->pnid .
                     ",'" . $arrayl[$i]['sku'] . "'," . $arrayl[$i]['price'] . ")";
             }
            //echo "<br />update price:".$q;
            $mysql->update($q);
        }//for

         //for header
        return true;
        /*fin guardado de datos*/

    }//function saveByFile
    /*END CODE to manager price list*/

    public static function getAllPages(Mysql $mysql, $page)
    {
        if (!$page || $page < 0) $page = 0;
        $q = "SELECT count(*) as total FROM tcitem";
        $tmp = $mysql->execute($q);
        $pags = ceil($tmp[0]->total / LIMIT);
        if ($page >= $pags) $page = $pags - 1;
        return array($tmp[0]->total, $pags, $page, LIMIT * $page);
    }//function

    public static function getRow(Mysql $mysql,$id) {
        $q="SELECT tu.* FROM tcitem tu WHERE tu.pnid='$id'";
        //echo $q;
        return $mysql->execute($q);
    }//function

    public static function getRowByCode(Mysql $mysql, $code, $itemname = null){
        $q = "SELECT tu.* FROM tcitem tu WHERE tu.dscode = '$code' ";
        //if($itemname != null);
            //$q.=" AND tu.dsname LIKE '%$itemname%'";
        //if($code == "961801")
        //   echo "<br />QUERY getRowByCode item:".$q;
        return $mysql->execute($q);
    }//function

    public static function setRow(Mysql $mysql,$args) {
        //print_r($args);
        $mysql->begin();
        $q="INSERT INTO tcitem(pnid,dscode,dsname,dsactive,dscomments,dsserial,dsupc, dspadre,dsupc2,fnidbranch, fnidcompany) " .
            "VALUES (null,'".$args['_code']."','".$args['_name']."',".$args['_active'].",'".$args['_comments']."' ".
            " ,".$args['_serie'].",'".$args['_upc']."','".$args['_parent']."','".$args['_upc2']."',".$args['_company'].",".$args['_branch'].")";
        //echo $q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }else{
            $q = "SELECT LAST_INSERT_ID() AS 'lastid'";
            $id = $mysql->execute($q);
            //var_dump($id);

            for($i=0; $i<count($args['ware']); $i++){
                  $q = "INSERT INTO tritemware(pnidtr,fnidware, fniditem, dbactive) ".
                       " VALUES(null,".$_POST['ware'][$i].",".$id[0]->lastid.",1)";
                  //echo "<br />".$q;
                  $mysql->update($q);
            }//for
            $mysql->commit();
            return true;
        }//

    }//function

    public static function updateRow(Mysql $mysql, $args){

        $q="UPDATE tcitem SET dscode='".$args['_code']."', dsname='".$args['_name']."'".
            " , dsactive=".$args['_active'].", dscomments='".$args['_comments']."',  ".
            " dsserial=".$args['_serie'].", fnidcompany = ".$args['_company'].", fnidbranch = ".$args['_branch'].
            " , dsupc='".$args['_upc']."', dspadre = '".$args['_parent']."' ".
            " , dsstatus ='".$args['_status']."', dsupc2 = '".$args['_upc2']."'".
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

            if(count($rowtemp)>0){
                $q = "UPDATE tritemware SET dbactive=1 WHERE fnidware = ".$args['ware'][$i]." AND fniditem = ".$args['id'];
            }else{
                $q = "INSERT INTO tritemware(pnidtr,fnidware, fniditem, dbactive) VALUES(null,".$_POST['ware'][$i].",".$args['id'].",1)";
            }

            //echo "<br />".$q;
            $mysql->update($q);
        }//for

        $idnotexist .= "0";
        $q = " UPDATE tritemware SET dbactive = 0 WHERE fniditem=".$args['id']." AND fnidware NOT IN (".$idnotexist.") ";
        //echo "<br />".$q;

        //echo "<br />".$q;
        return $mysql->update($q);
    }//update row

    public static function getWareHouse(Mysql $mysql,$idrole=null){

        if($idrole!=null)
            $q = " SELECT * FROM tcwarehouse WHERE dbactive = 1 AND pnid = 2 ORDER BY pnid ASC ";
         else
            $q = " SELECT * FROM tcwarehouse WHERE dbactive = 1 ORDER BY pnid ASC ";
        return $mysql->execute($q);
    }//function getWareHouse

    public static function getSpecificWareHouseBy(Mysql $mysql, $idrole=null){
        $q = " SELECT * FROM tcwarehouse WHERE dbactive = 1 AND pnid IN (1,2) ORDER BY pnid ASC ";
        return $mysql->execute($q);
    }

    public static function getWaretr(Mysql $mysql, $id){
          $q = "SELECT * FROM tritemware WHERE fniditem = ".$id;
          return $mysql->execute($q);
    }

    public static function getBranch(Mysql $mysql){
        $q = "SELECT * FROM tcbranch ORDER BY dsname ASC";
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getCompany(Mysql $mysql){
        $q = "SELECT * FROM tccompany ORDER BY dsname ASC";
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getNext(Mysql $mysql){
        $q = "SELECT MAX(CONVERT(dscode, UNSIGNED))+1 AS 'consec' FROM `tcitem` ORDER BY CONVERT (dscode, UNSIGNED) DESC";
        return $mysql->execute($q);
    }

    public static function getStock(Mysql $mysql, $filter = null, $ware=null){

        $q = " SELECT item.pnid, item.dscode, item.dsname, item.dsupc, invg.ddquantity, ".
             "        tw.dsname AS 'warename' ".
             " FROM trinventorygral invg INNER JOIN tcitem item ON invg.fniditem = item.pnid ".
             "                           INNER JOIN tcwarehouse tw ON tw.pnid = invg.fnidware ".
             " WHERE 1 = 1 ";

        if($filter != null)
            $q .= " AND (item.pnid = $filter) ";
        if($ware != null)
            $q .= " AND tw.pnid = $ware";
        $q.= " ORDER BY dscode DESC ";
        //echo "<br />getStock: ".$q;
        return $mysql->execute($q);

    }

    public static function getStockGroupByItem(Mysql $mysql, $filter = null, $ware=null){
        $q = " SELECT item.pnid, item.dscode, item.dsname, item.dsupc ".
            " FROM trinventorygral invg INNER JOIN tcitem item ON invg.fniditem = item.pnid ".
            "                           INNER JOIN tcwarehouse tw ON tw.pnid = invg.fnidware ".
            " WHERE 1 = 1 ";

        if($filter != null)
            $q .= " AND (item.dscode LIKE '%$filter%' OR item.dsname LIKE '%$filter%') ";
        if($ware != null)
            $q .= " AND tw.pnid = $ware";
        $q.= " GROUP BY item.pnid, item.dscode, item.dsname, item.dsupc";
        $q.= " ORDER BY dscode DESC   ";
        //echo "<br />getStock: ".$q;
        return $mysql->execute($q);
    }

    public static function getCostByItem(Mysql $mysql, $filter=null, $datefrom = null, $dateto = null){

        $q = " SELECT tc.*, ti.dscode, ti.dsname ".
             " FROM trcost tc INNER JOIN tcitem ti ON ti.pnid = tc.fniditem ".
             " WHERE tc.dsdirection = '1' ".
             " ORDER BY tc.dddate ASC ";

        return $mysql->execute($q);

    }//function to get cost
}
