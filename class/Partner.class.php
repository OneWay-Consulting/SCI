<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 06/11/2018
 * Time: 11:23 AM
 */

class Partner
{

    public static function getAllByType(Mysql $mysql, $type = null, $filter = null, $page = null)
    {//list all user by role
        $q = "SELECT tc.* FROM tcpartner tc ".
            " WHERE 1 = 1 ";

        if($type!=null)
            $q .= " AND dstype='$type'";
        if($filter!=null)
            $q .= " AND (dsname LIKE '%$filter%' OR  dscode LIKE '%$filter%')";
        //else
        //    $q .= " AND dstype='C'";

        $q .= " ORDER BY tc.pnid DESC "; //.Mysql::getQueryLimit($page);
        //echo "<br />".$q;
        return $mysql->execute($q);
    }

    public static function getAllPages(Mysql $mysql, $page)
    {
        if (!$page || $page < 0) $page = 0;
        $q = "SELECT count(*) as total FROM tcpartner";
        $tmp = $mysql->execute($q);
        $pags = ceil($tmp[0]->total / LIMIT);
        if ($page >= $pags) $page = $pags - 1;
        return array($tmp[0]->total, $pags, $page, LIMIT * $page);
    }//function

    public static function getRow(Mysql $mysql,$id) {
        $q="SELECT tu.* FROM tcpartner tu WHERE tu.pnid='$id'";
        //echo "<br />getRow: ".$q;
        return $mysql->execute($q);
    }//function

    public static function getRowByCode(Mysql $mysql, $code, $type=null){
        $q = "SELECT tu.* FROM tcpartner tu WHERE tu.dscode = '$code' ";
        if($type==null)
            $q .= " AND dstype = 'C' ";
        else
            $q .= " AND dstype = '$type' ";
        //echo "<br />partner:".$q;
        return $mysql->execute($q);
    }

    public static function setRow(Mysql $mysql,$args) {
        $mysql->begin();
        $q="INSERT INTO tcpartner(pnid,dscode,dsname,dsrfc,dsaddress,dstype,dsemail, dsphone, ".
            " dscredit, dncreditday, dddiscountperc, dsactive) " .
            "VALUES (null,'".$args['_code']."','".$args['_name']."','".$args['_rfc']."','".$args['_address'].
            "','".$args['_type']."','".$args['_mail']."','".$args['_phone']."',".
            "'".$args['_credit']."',".$args['_nodias'].",".$args['_pordesc'].",".$args['_active'].")";
        //echo $q;
        if(!$mysql->update($q)){
            $mysql->rollback();
            return false;
        }else{
            $mysql->commit();
            return true;
        }//

    }//function

    public static function updateRow(Mysql $mysql, $args){

        $q="UPDATE tcpartner SET dscode='".$args['_code']."', dsname='".$args['_name']."'".
            " , dsrfc='".$args['_rfc']."', dsaddress='".$args['_address']."',  ".
            " dsemail='".$args['_mail']."', dsphone='".$args['_phone']."', ".
            " dscredit = '".$args['_credit']."', dncreditday=".$args['_nodias'].",".
            " dddiscountperc = ".$args['_pordesc'].", dsactive = ".$args['_active'].
            " WHERE pnid='".$args['id']."'";

        //echo "<br />".$q;
        return $mysql->update($q);
    }//update row

    public static function getNextByType(Mysql $mysql, $type){

        $q = "SELECT MAX(pnid)+1 AS 'consec' FROM tcpartner ";
        return $mysql->execute($q);
    }

}