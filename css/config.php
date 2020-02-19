<?php
    define("DBHOST", "localhost");
    define("DBNAME", "onewayin_minerp");
    define("DBUSER", "onewayin");
    define("DBPASS", "0neWay1nt3rnet2OI8$");

	/**Settings for server url and file system*/
	    define("FSROOT", "/home/onewayin/public_html/inventoryadmin/");				//absolute script path
	    define("URLWEB", "http://onewayinternet.com.mx/inventoryadmin/");	//url browser
//    define("FSROOT", "C:/wamp/www/tmp/rivers/controlinventory/prod/");				//absolute script path
//    define("URLWEB", "http://localhost:8080/tmp/rivers/controlinventory/prod/");	//url browser

	/**Settings for WE preferences*/
    define("DATEF",	"%e %b %Y");		//date format for events
    define("TIMEF",	"%H:%M");			//time format for events

	/**Setting for session*/
	//define("COOK",	5);
	//define("STIME",	60);

	/**Setting for apperance*/
	define("LIMIT",	20);
    define("IVA",0);
	setlocale(LC_ALL, "es_ES");
   	session_start();
   	header('Content-Type: text/html; charset=utf-8');

	function __autoload($obj){
		$file = FSROOT."class/" . $obj . ".class.php";
		//echo $file;
		if(file_exists($file))
			require_once($file);
	}//function

?>
