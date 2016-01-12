<?php
    function checkId ($id) {
    	if (isset($id)) {
			if (is_int($id) === true) {
				return $id;
			}
			elseif (is_string($id) === true && is_numeric($id) === true) {
				if (strpos($id, '.') === false) {
					return $id;
				}
			}
		}
		
		return false;
	}
	
	function br2nl($input) {
		$out = preg_replace('#<br\s*/?>#', "", $input);
		return $out;
	}
	
	function checkString ($string) {
		if (isset($string) && !empty($string)) {
			// connect(); // Connect to DB to make sure that mysql_real_escape_string() function will work
			//if (get_magic_quotes_gpc()) {
		    //	$string = stripslashes($string);
		    //}
			$string2 = preg_replace("/[\n|\r]/","", $string);
			//$string2 = nl2br($string);
			//$string3 = str_replace("<br />", "", $string2);
		    
			return addslashes($string2);
		}
		
	    return "";
		
	}

	function errorResponse ($message) {
		$response = array(
    		"status" => "error",
    		"message" => $message
		);

		return json_encode($response);
	}
?>