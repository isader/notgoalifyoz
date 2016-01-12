<?php
    include_once("connect.php");
	include_once("common.php");
	
	$user_id = (isset($user_id)) ? $user_id : $_GET['user_id'];
	$response = "";
	
	if ($user_id) {
		if (checkId($user_id)) {
			$response = userById($user_id);
		}
		else {
			$response = errorResponse("The user id provided is not valid");
		}
	}
	else {
		$response = errorResponse("The user id was not provided");
	}

	echo $response;





	function userById ($user_id) {
		$result = getUserById($user_id);
		return formJson($result);
	}

	function getUserById ($user_id) {
		$sql = sprintf("SELECT user_id, first_name, last_name, age, email, receive_notifications, is_new, lang_id FROM user WHERE user_id = '%d'",
				$user_id);

		return executeSql($sql);
	}
	
	function formJson ($result) {
		$users = array();

		while ($item = mysql_fetch_array($result)) {
			$user = array(
				"user_id" => $item['user_id'],
				"first_name" => $item['first_name'],
				"last_name" => $item['last_name'],
				"age" => $item['age'],
				"email" => $item['email'],
				"receive_notifications" => $item['receive_notifications'],
				"is_new" => $item['is_new'],
				"lang_id" => $item['lang_id']
			);

			array_push($users, $user);
	  	}

		$response = array(
    		"status" => "ok",
    		"users" => $users
		);

		return json_encode($response);
	}
?>