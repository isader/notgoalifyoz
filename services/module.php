<?php
    include_once("connect.php");
	include_once("common.php");
	
	$user_id = (isset($user_id)) ? $user_id : $_GET['user_id'];
	$module_id = (isset($module_id)) ? $module_id : $_GET['module_id'];
	$response = "";
	
	if ($user_id) {
		if (checkId($user_id)) {
			$response = moduleByUserId($user_id);
		}
		else {
			$response = errorResponse("The user id provided is not valid");
		}
	}
	else if ($module_id) {
		if (checkId($module_id)) {
			$response = moduleByModuleId($module_id);
		}
		else {
			$response = errorResponse("The module id provided is not valid");
		}
	}
	else {
		$response = allModules();
	}

	echo $response;





	function moduleByUserId ($user_id) {
		$result = getModuleByUserId($user_id);
		return formJson($result, true);
	}

	function getModuleByUserId ($user_id) {
		$sql = sprintf("SELECT u.user_id, m.module_id, name, brief_desc, m.desc, thumb_url, progress, is_complete, date_complete, COUNT(mt.module_id) task_count FROM user u JOIN user_module um ON u.user_id=um.user_id JOIN module m ON um.module_id=m.module_id JOIN module_task mt ON m.module_id=mt.module_id WHERE u.user_id = '%d'",
				$user_id);

		return executeSql($sql);
	}

	function moduleByModuleId ($module_id) {
		$result = getModuleById ($module_id);
		return formJson($result, false);
	}

	function getModuleById ($module_id) {
		$sql = sprintf("SELECT * FROM module WHERE module_id = %d",
				$module_id);

		return executeSql($sql);
	}

	function allModules () {
		$result = getAllModule();
		return formJson($result, false);
	}

	function getAllModule () {
		$sql = sprintf("SELECT * FROM module");

		return executeSql($sql);
	}
	
	function formJson ($result, $otherInfo) {
		$modules = array();

		while ($item = mysql_fetch_array($result)) {
			$module = array(
				"module_id" => $item['module_id'],
				"name" => $item['name'],
				"brief_desc" => $item['brief_desc'],
				"desc" => $item['desc'],
				"thumb_url" => $item['thumb_url']
			);

			if ($otherInfo) {
				$module["user_progress"] = array (
					"user_id" => $item['user_id'],
					"is_complete" => $item['is_complete'],
					"progress" => $item['progress'],
					"date_completed" => $item['date_completed']
				);
			}

			array_push($modules, $module);
	  	}

		$response = array(
    		"status" => "ok",
    		"modules" => $modules
		);

		return json_encode($response);
	}
?>