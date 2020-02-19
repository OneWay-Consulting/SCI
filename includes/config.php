<?php

ini_set('log_errors','On');
ini_set('display_errors','Off');
ini_set('error_reporting', E_ALL );
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

    define("DBHOST", "localhost");
    define("DBNAME", "onewayin_minerp");
    define("DBUSER", "root");
    define("DBPASS", "");

	/**Settings for server url and file system*/
//	    define("FSROOT", "/home/onewayin/public_html/inventoryadmin/");				//absolute script path
//	    define("URLWEB", "http://onewayinternet.com.mx/inventoryadmin/");	//url browser
    define("FSROOT", "C:/wamp/www/ow/minierp/");				//absolute script path
    define("URLWEB", "http://localhost:8080/ow/minierp/");	//url browser

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
