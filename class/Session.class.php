<?php
class Session {
	private $stime, $user, $flag, $perms, $logs;

	public function __construct(){
		$this->stime = 0;
		//$this->cook = 1;
		$this->user = null;
		$this->flag = false;
		$this->logs = array();
		$this->perms = array();
	}//function

	public function setLog($mod,$val){
		$this->logs[$mod]=$val;
	}//function

	public function setUser($usr){
		$this->user = $usr;
	}//function

	public function connect(Mysql $mysql,$log,$pass){
	    //echo "<br />pass:".$pass;
		//$pass = md5($pass);
		$q="SELECT tc.*, tr.dsname AS 'role' FROM tcuser tc, tcrole tr  ".
		   " WHERE tr.idtcrole = tc.fnidrole AND tc.dsuser='$log' AND tc.dspassword=MD5('$pass') AND tc.dnactivo";
		//echo "<br/>".$q;
		if($reg = $mysql->execute($q)){
			$this->user = $reg[0];
			$this->flag = true;
			$this->stime = 0;
			//$this->cook = 1;
			return true;
		}//if

		return false;
	}//function

	public function __toString(){
		return $this->user->usuario;
	}//function

	public function getId(){
		return $this->user->id;
	}//function

	public function getLogin(){
		return $this->user->usuario;
	}//function

	public function getKey(){
		return $this->user->clave;
	}//function

	public function isConnected(){
		return $this->flag;
	}//function

	public function getUserType(){
		if($this->user-tipo == 1)
			return "S";
		return $this->user->tipo;
	}//function

	public function getLog($mod){
		return $this->logs[$mod];
	}//function

	public function getUser(){
		return $this->user;
	}
}//class
?>
