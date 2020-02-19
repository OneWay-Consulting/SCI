<?php
	class String{
		
		public static function sanitize($value,$active){
			if(gettype($value)=="array"){
				$keys=array_keys($value);
				for($i=0;$i<count($keys);$i++)
					$value[$keys[$i]] = self::sanitize($value[$keys[$i]],$active);
			}else{
				if($active)
					$value = addslashes(trim($value));
				else
					$value = stripslashes(trim($value));
			}//if
			return $value;
		}//function
	
	}//class
?>