<?php
    include_once("connect.php");
	include_once("common.php");
	
	$user_id = (isset($user_id)) ? $user_id : $_GET['user_id'];
	$module_id = (isset($module_id)) ? $module_id : $_GET['module_id'];
	$show_all_for_user = (isset($show_all_for_user)) ? $show_all_for_user : $_GET['show_all_for_user'];
	$new_module = (isset($new_module)) ? $new_module : $_POST['new_module'];

	$response = "";
	
	if ($user_id && !$show_all_for_user) {
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
	else if ($new_module) {
		$user_id = intval($_POST['user_id']);
		$module_id = intval($_POST['module_id']);
		$progress = checkString($_POST['progress']);
		$is_complete = checkString($_POST['is_complete']);
		$date_completed = checkString($_POST['date_completed']);

		if (checkId($user_id) && checkId($module_id)) {
			$response = moduleUserProgress($user_id, $module_id, $progress, $is_complete, $date_completed);
		}
	}
	else {
		$response = allModules($user_id);
	}

	echo $response;





	function moduleByUserId ($user_id) {
		$result = getModuleByUserId($user_id);
		return formJson($result, "progress", 0);
	}

	function getModuleByUserId ($user_id) {
		$sql = sprintf("SELECT u.user_id, m.module_id, name, brief_desc, m.desc, thumb_url, progress, is_complete, date_completed, COUNT(*) task_count FROM user u JOIN user_module um ON u.user_id=um.user_id JOIN module m ON um.module_id=m.module_id JOIN module_task mt ON m.module_id=mt.module_id  WHERE u.user_id = '%d' GROUP BY m.module_id",
				$user_id);

		return executeSql($sql);
	}

	function moduleByModuleId ($module_id) {
		$result = getModuleById ($module_id);
		return formJson($result, false, 0);
	}

	function getModuleById ($module_id) {
		$sql = sprintf("SELECT * FROM module WHERE module_id = %d",
				$module_id);

		return executeSql($sql);
	}

	function allModules ($user_id) {
		$result = getAllModule($user_id);
		return formJson($result, "is_module_added", $user_id);
	}

	function getAllModule ($user_id) {
		$sql = sprintf("SELECT * FROM module");

		return executeSql($sql);
	}

	function isModuleAlreadyAdded ($user_id, $module_id) {
		$sql = sprintf("SELECT user_module_id FROM user_module WHERE user_id = %d AND module_id = %d",
				$user_id, $module_id
		);

		$exists = executeSql($sql);

		return mysql_num_rows($exists);
	}

	function moduleUserProgress ($user_id, $module_id, $progress, $is_complete, $date_completed) {
		return setModuleUserProgress($user_id, $module_id, $progress, $is_complete, $date_completed);
	}

	function setModuleUserProgress($user_id, $module_id, $progress, $is_complete, $date_completed) {

		$module_already_added = isModuleAlreadyAdded($user_id, $module_id);

		if ($module_already_added) {

			return '{"status": "error", "message": "The user has already selected this module"}';
		}
		else {
			$sql = sprintf("INSERT INTO user_module (user_id, module_id, progress, is_complete, date_completed) VALUE ('%s', '%s', '%s', '%s', '%s')",
				$user_id, $module_id, $progress, $is_complete, $date_completed
			);

			$done = executeSql($sql);

			if ($done) {
				return '{"status": "ok", "message": "Module has been added successfully!", "module_id": '.$module_id.'}';
			}
			else {
				return '{"status": "error", "message": "There was a problem while adding this module. Please try again later"}';
			}
		}
	}
	
	function formJson ($result, $other_info, $user_id) {
		$modules = array();

		while ($item = mysql_fetch_array($result)) {
			$module = array(
				"module_id" => $item['module_id'],
				"name" => $item['name'],
				"name_as_class" => preg_replace('/\W+/','',strtolower(strip_tags($item['name']))),
				"brief_desc" => $item['brief_desc'],
				"desc" => $item['desc'],
				"thumb_url" => $item['thumb_url']
			);

			if ($other_info == "progress") {
				$module["user_progress"] = array (
					"user_id" => $item['user_id'],
					"is_complete" => $item['is_complete'],
					"progress" => $item['progress'],
					"task_count" => $item['task_count'],
					"date_completed" => $item['date_completed']
				);
			}
			else if ($other_info == "is_module_added") {
				if (isModuleAlreadyAdded($user_id, $item['module_id'])) {
					$already_added = true;
				}
				else {
					$already_added = false;
				}
				$module['already_added'] = $already_added;
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