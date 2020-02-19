<?php
	class Role{
		
		public static function getAll(Mysql $mysql) {
			$q="SELECT * FROM tcrole " .
				" ORDER BY idtcrole DESC ";
			return $mysql->execute($q);
		}//function

        public static function getRow(Mysql $mysql, $id){
		    $q = "SELECT * FROM tcrole WHERE idtcrole = ".$id;
		    return $mysql->execute($q);
        }

        public static function setRow(){

        }

        public static function updateRow(Mysql $mysql, $args){

		    //print_r($args);

		    $q = " UPDATE tcrole SET dsname = '".$args['_name']."', dsdescription = '".$args['_comments']."' ".
                 " WHERE idtcrole = '".$args['id']."'";
            //echo "<br />".$args['']."query tcrole:".$q;
            $mysql->update($q);

            for($i=0; $i<count($args['_idmod']); $i++){

                if($args['_exist'][$i] == ""){ //add

                    $create = ($args['_create'][$i])?"1":"0";
                    $query = ($args['_query'][$i])?"1":"0";


                    $q = " INSERT INTO tcmoduleperm(pnid,fnidrole, fnidmodule,dncreated, dnquery) VALUES ".
                         "(null,".$args['id'].",".$args['_idmod'][$i].",".$create.",".$query." )";

                }else{ //update
                    $create = ($args['_create'][$i])?"1":"0";
                    $query = ($args['_query'][$i])?"1":"0";

                    $q = " UPDATE tcmoduleperm SET dncreated = ".$create.", dnquery=".$query.
                         " WHERE pnid = ".$args['_exist'][$i];

                }//update
                //echo "<br />".$args['']."query moduleperm:".$q;
                $mysql->update($q);
            }

            return true;
        }

        public static function getPermissionsByRole(Mysql $mysql, $idrole){

		    $permission = null;
		    $q = " SELECT tm.dspage, tmp.dncreated, tmp.dnquery, tmp.pnid ".
                 " FROM tcmodule tm LEFT JOIN tcmoduleperm tmp ON tmp.fnidmodule = tm.pnid ".
                 " WHERE tmp.fnidrole = ".$idrole;

		    $list = $mysql->execute($q);
		    for($i=0; $i<count($list); $i++){
                $permission[$list[$i]->dspage] = array('create'=>$list[$i]->dncreated,
                                                       'query'=>$list[$i]->dnquery,
                                                       'id'=>$list[$i]->pnid
                                        );
            }//for

            return $permission;

        }//function getPermission

        public static function getModules(Mysql $mysql){
		    $q = "SELECT * FROM tcmodule ORDER BY pnid ASC ";
		    return $mysql->execute($q);
        }

	
	}//class
?>