<?php
    include_once("connect.php");
	include_once("common.php");
	
	$type = (isset($type)) ? $type : $_GET['type'];
	$response = "";
	
	if ($type) {
		switch ($type) {
		    case "coords":
				if (isset($_GET['lat']) && isset($_GET['lng'])) {
					$lat = checkCoord($_GET['lat']);
					$lng = checkCoord($_GET['lng']);
					if ($lat && $lng) {
						$response = closestToLatLng($lat, $lng);
					}
				}
		        break;
			case "address":
				if (isset($_GET['address'])) {
					$address = checkAddress($_GET['address']);
					if ($address) {
						$response = closestToAddress($address);
					}
				}
				break;
		    default:
		    	$response = "Incorrect type";
				break;
		}
	}
	else {
	}

	echo $response;
	
	
	function closestToLatLng ($lat, $lng) {
		$coords->lat = $lat;
		$coords->lng = $lng;
		$dimeter = 0;
		
		do {
			$dimeter = $dimeter + 1;
			$result = getLocationsByLatLng($coords, $dimeter);
		}
		while (mysql_num_rows($result) == 0);
		return formJson($result, $coords, $dimeter);
	}
	
	function closestToAddress ($address) {
		$coords = geoCodeAddress($address);
		$coords->address = $address;
		$dimeter = 0;

		do {
			$dimeter = $dimeter + 1;
			$result = getLocationsByLatLng($coords, $dimeter);
		}
		while (mysql_num_rows($result) == 0);
		return formJson($result, $coords, $dimeter);
	}
	
	function getLocationsByLatLng($coords, $dimeter) {
		$sql = "SELECT masjid_id, masjid_name, masjid_address, masjid_latitude, masjid_longitude, masjid_phone, masjid_fax, masjid_mobile, masjid_email, masjid_website, masjid_comment, masjid_jummah_address, masjid_jummah_comment, (6371 * acos(cos(radians($coords->lat)) * cos(radians(masjid_latitude)) * cos(radians(masjid_longitude) - radians($coords->lng)) + sin(radians($coords->lat)) * sin(radians(masjid_latitude)))) AS distance FROM masjid HAVING distance < $dimeter ORDER BY distance LIMIT 0, 500";
		return executeSql($sql);
	}
	
	function geoCodeAddress ($address) {
		$address = 'address=' . urlencode($address);
		$address = htmlentities($address);
		$url = 'http://maps.googleapis.com/maps/api/geocode/xml?' . $address . '&sensor=true';
		$xml = getContent($url);
		
		foreach ($xml->result as $result) {
			$coords->lat = (string) $result->geometry->location->lat;
			$coords->lng = (string) $result->geometry->location->lng;
		}
		
		return $coords;
	}
	
	class Masjid {
	    public $id;
		public $name;
		public $address;
		public $lat;
		public $lng;
		public $phone;
		public $fax;
		public $mobile;
		public $email;
		public $website;
		public $comment;
		public $jummah_address;
		public $jummah_comment;
		public $distance;
	}
	
	function formJson ($result, $coords, $dimeter) {
		$count = 1;
		$JSON = "{";
		$JSON .= "\"totalCount\": ".mysql_num_rows($result).", ";
		$JSON .= "\"lat\": ".$coords->lat.", ";
		$JSON .= "\"lng\": ".$coords->lng.", ";
		$JSON .= "\"dimeter\": ".$dimeter.", ";
		$JSON .= "\"address\": \"".$coords->address."\", ";
		
		$masjids = array();
		$count = 0;
		while ($item = mysql_fetch_array($result)) {
			$masjid = new Masjid();
			$masjid->id = $item['masjid_id'];
			$masjid->name = $item['masjid_name'];
			$masjid->address = $item['masjid_address'];
			$masjid->lat = $item['masjid_latitude'];
			$masjid->lng = $item['masjid_longitude'];
			$masjid->phone = $item['masjid_phone'];
			$masjid->fax = $item['masjid_fax'];
			$masjid->mobile = $item['masjid_mobile'];
			$masjid->email = $item['masjid_email'];
			$masjid->website = $item['masjid_website'];
			$masjid->comment = $item['masjid_comment'];
			$masjid->jummah_address = $item['masjid_jummah_address'];
			$masjid->jummah_comment = $item['masjid_jummah_comment'];
			$masjid->distance = number_format($item['distance'], 2);
			$masjids[$count] = $masjid;
			$count = $count + 1;
	  	}
		
		$JSON .= "\"masjids\":" . json_encode($masjids).'}';
		
	  	return $JSON;
	}
	
	function formJson2 ($result, $coords) {
		$count = 1;
		$JSON = "{";
		$JSON .= "\"totalCount\": ".mysql_num_rows($result).", ";
		$JSON .= "\"lat\": ".$coords->lat.", ";
		$JSON .= "\"lng\": ".$coords->lng.", ";
		$JSON .= "\"address\": \"".$coords->address."\", ";
		$JSON .= "\"masjids\": [";
		while ($item = mysql_fetch_array($result)) {
			$JSON .= "{\"id\": \"".mysql_escape_string($item['masjid_id'])."\", ";
			$JSON .= "\"name\": \"".br2nl($item['masjid_name'])."\", ";
			$JSON .= "\"address\": \"".mysql_escape_string(br2nl($item['masjid_address']))."\", ";
			$JSON .= "\"lat\": ".mysql_escape_string($item['masjid_latitude']).", ";
			$JSON .= "\"lng\": ".mysql_escape_string($item['masjid_longitude']).", ";
			$JSON .= "\"phone\": \"".mysql_escape_string($item['masjid_phone'])."\", ";
			$JSON .= "\"fax\": \"".mysql_escape_string($item['masjid_fax'])."\", ";
			$JSON .= "\"mobile\": \"".mysql_escape_string($item['masjid_mobile'])."\", ";
			$JSON .= "\"email\": \"".mysql_escape_string($item['masjid_email'])."\", ";
			$JSON .= "\"website\": \"".mysql_escape_string($item['masjid_website'])."\", ";
			$JSON .= "\"comment\": \"".checkString($item['masjid_comment'])."\", ";
			$JSON .= "\"jummah_address\": \"".mysql_escape_string($item['masjid_jummah_address'])."\", ";
			$JSON .= "\"jummah_comment\": \"".checkString($item['masjid_jummah_comment'])."\",";
			$JSON .= "\"distance\": \"".mysql_escape_string(number_format($item['distance'], 2))."\"}";
			
			if ($count == mysql_num_rows($result)) {
	  			$JSON .= "";
	  		}
	  		else {
	  			$JSON .= ", ";
	  		}
	  		$count++;
	  	}
	  	$JSON .= "]}";
		
	  	return $JSON;
	}
?>