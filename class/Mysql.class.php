<?php
class Mysql{
	private $link;
	private $error;

	public function __construct(){
		$this->link = mysqli_connect(DBHOST, DBUSER, DBPASS);
		if(!$this->link)
		    die('Not connected : ' . mysqli_error());

		if (!mysqli_select_db($this->link, DBNAME))
		    die ('Can\'t use db : ' . mysqli_error($this->link));

	}//function

	public function __destruct() {
		mysqli_close($this->link);
	}//function

    public function __toString(){
    	return "ConnectorId => ". $this->link;
	}//function

	public function execute($q) {
    	if(!$q)
        	return false;

        if(!$res = mysqli_query($this->link, $q)){
        	$this->error[0] = mysqli_error($this->link);
            $this->err[1] = $q;
            echo $this->getLastError();
            return false;
        }//if
        $data = array ();
        while ($reg = mysqli_fetch_object($res))
        	$data[] = $reg;

		return $data;
	}//function

	public function update($q) {
    	if(!$q)
        	return false;
		//echo "<br />function mysql update: ".$q;
		if(mysqli_query($this->link, $q))
			return true;
        else{
        	$this->error[0] = mysqli_error($this->link);
            $this->error[1] = $q;
            echo $this->getLastError();
           	return false;
        }//if
	}//function

	public function begin(){
		@mysqli_query("START TRANSACTION");
	}//function

	public function commit(){
		@mysqli_query("COMMIT");
	}//function

	public function rollback(){
		@mysqli_query("ROLLBACK");
	}//function

	public function getLastError(){
    	return "@->".$this->error[0]."<br/>@->".$this->err[1];
	}//function

	public static function getQueryLimit($p){
		if(!$p||$p<0)$p=0;
		$l = LIMIT;
		if(!$l)$l=40;
		$o = $p*$l;
		return "LIMIT $l OFFSET $o";
	}//function
}//class
?>
