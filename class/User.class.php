<?php
class User{

	public static function getAll(Mysql $mysql,$page) {
        $q=" SELECT tc.*, tr.dsname AS 'role' FROM tcuser tc, tcrole tr " .
			" WHERE tr.idtcrole = tc.fnidrole ".
       		" ORDER BY idtcuser ASC ";//Mysql::getQueryLimit($page);
       		//echo "<br />".$q;
       	return $mysql->execute($q);
	}//function

	public static function listUserByType(Mysql $mysql, $item){
		$q = "SELECT idtcuser, dsuser, fncardcode, dscardname FROM tcuser WHERE fnidrole = $item";
		//echo $q;
		return $mysql->execute($q);
	}

	public static function getAllByType(Mysql $mysql, $type){//list all user by role
        $q="SELECT tc.*, tr.dsname AS 'role' FROM tcuser tc, tcrole tr " .
			" WHERE tr.idtcrole = tc.fnidrole AND tc.fnidrole = $type ".
       		" ORDER BY idtcuser ASC ".Mysql::getQueryLimit($page);
       		//echo "<br />".$q;
       	return $mysql->execute($q);
	}

	public static function getAllPages(Mysql $mysql,$page){
		if(!$page || $page<0)$page=0;
        $q="SELECT count(*) as total FROM tcuser";
		$tmp = $mysql->execute($q);
		$pags = ceil($tmp[0]->total/LIMIT);
		if($page>=$pags)$page=$pags-1;
        return array($tmp[0]->total,$pags,$page,LIMIT*$page);
   	}//function

   	public static function search(Mysql $mysql,$pattern,$value){
        $q="SELECT * FROM vusuario " .
        	"WHERE $pattern LIKE '%$value%' " .
       		"ORDER BY $pattern";
       	return $mysql->execute($q);
   	}//function

	public static function getSearchTotal(Mysql $mysql,$pattern,$value){
        $q="SELECT count(*) as total " .
        	"FROM tcuser " .
        	"WHERE $pattern LIKE '%$value%'";
		$data = $mysql->execute($q);
		return $data[0]->total;
   	}//function

   	public static function getRow(Mysql $mysql,$id) {
        $q="SELECT tu.* FROM tcuser tu WHERE tu.idtcuser='$id'";
		//echo $q;
        return $mysql->execute($q);
   	}//function

	public static function setRow(Mysql $mysql,$args) {
        $mysql->begin();
        $q="INSERT INTO tcuser(idtcuser,dsuser,dspassword,fnidrole,dnactivo,dsemail,dsnombrecom, fncardcode, dscardname, fnproject, dsprojectname ".
             " , GroupCode , whscode , series) " .
        	"VALUES (null,'".$args['_usuario']."','".md5($args['_password'])."',".$args['_rol'].",'".$args['_activo'].
					 "','".$args['_email']."','".$args['_nombre']."','".$args['_cliente']."','".$args['_dscardcode']."','".$args['_proyecto']."','".$args['_dsproyecto']."' ".
                     ",'".$args['_groupcode']."','".$args['_whscode']."','".$args['_serie']."')";
		//echo $q;
        if(!$mysql->update($q)){
        	$mysql->rollback();
        	return false;
        }else{
	        $mysql->commit();
			return true;
        }//

   	}//function

	public static function unsetRow(Mysql $mysql,$id) {
		$data = self::getRow($mysql,$id);
        $mysql->begin();

        $q="DELETE FROM tdpersonaldata WHERE fniduser= $id";
     	//echo "<br />".$q;
	    if(!$mysql->update($q)){
        	$mysql->rollback();
			return false;
        }else{
			 $q = "DELETE FROM tcuser WHERE idtcuser = $id";
	     	 //echo "<br />".$q;
			 if(!$mysql->update($q)){
				$mysql->rollback();
				return false;
			 }
		     $mysql->commit();
			 return true;
        }//if
   	}//function

	public static function tryDelete(Mysql $mysql, $id){
		$data = Ticket::getHistoryByIdUser($mysql,$id);
		//echo "<br />datos : ".count($data);
		if(count($data)==0)
			return 2;
		else
			return 1;
	}

	public static function updateRow(Mysql $mysql,$args){
		if(trim($args['_password']) != "")
			$set = "dspassword='".md5($args['_password'])."',";

        $q="UPDATE tcuser SET ".$set."dsemail='".$args['_email']."', dnactivo=".$args['_activo'].
			" ,dsnombrecom='".$args['_nombre']."', fncardcode='".$args['_cliente']."', dscardname='".$args['_dscardcode']."', ".
			" fnproject='".$args['_proyecto']."', dsprojectname='".$args['_dsproyecto']."', ".
            " GroupCode = '".$args['_groupcode']."', whscode = '".$args['_whscode']."', series='".$args['_serie']."' ".
        	" WHERE idtcuser='".$args['id']."'";

       	//echo "<br />".$q;
   		return $mysql->update($q);

	}//function


	/////////  webservices SAP
	public static function getListClientBySAP($_filter){

		$listclient = array();
		try {
			/*codigo para consumor ws soap*/
				$client = new SoapClient(URLWS);
				$params = array('_filter' => $_filter);
				$result = $client->ListCustomer($params);

				for($i=0; $i<count($result->ListCustomerResult->ClientResponse); $i++){
					 $listclient[$i] = $result->ListCustomerResult->ClientResponse[$i]->CardCode."|".htmlentities($result->ListCustomerResult->ClientResponse[$i]->CardName);
				}

			} catch (Exception $e) {
				trigger_error($e->getMessage(), E_USER_WARNING);
			}
		return $listclient;
	}//function getListClientBySAP

    /* Code to get Atention users by series*/
    public static function getUserToSend(Mysql $mysql, $series, $idrole){

        $mailtosend = "";
        $q = "SELECT dsemail FROM tcuser WHERE series = $series AND fnidrole = $idrole";
        $data = $mysql->execute($q);

        for($i=0; $i<count($data); $i++){
            $mailtosend.= $data[$i]->dsemail.",";
        }

        return $mailtosend;
    }//function getUserToSend

    public static function getUserCreatorById(Mysql $mysql, $fnidusercreator){
        $q = "SELECT dsemail FROM tcuser WHERE idtcuser = $fnidusercreator";
        return $mysql->execute($q);
    }

}//
?>
