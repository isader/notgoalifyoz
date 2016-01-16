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
	
	function checkString ($value) {
		$value = trim($value); //remove empty spaces
	    $value = strip_tags($value); //remove html tags
	    $value = filter_var($value, FILTER_SANITIZE_MAGIC_QUOTES); //addslashes();
	    $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW); //remove /t/n/g/s
	    $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); //remove é à ò ì ` ecc...
	    $value = htmlentities($value, ENT_QUOTES,'UTF-8'); //for major security transform some other chars into html corrispective...

	    return $value;

		/*
		if (isset($string) && !empty($string)) {
			$string = preg_replace("/[\n|\r]/","", $string);
		    
			return addslashes($string);
		}
		
	    return "";
		*/
	}

	function errorResponse ($message) {
		$response = array(
    		"status" => "error",
    		"message" => $message
		);

		return json_encode($response);
	}
?>