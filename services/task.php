<?php
    include_once("connect.php");
	include_once("common.php");
	
	$user_id = (isset($user_id)) ? $user_id : $_GET['user_id'];
	$module_id = (isset($module_id)) ? $module_id : $_GET['module_id'];
	$response = "";
	
	if ($module_id) {
		if (checkId($module_id)) {

			$module_array = array();
			$tasks_array = array();

			// Get all tasks assigned to the module
			$task_result = taskByModuleId($module_id);

			while ($task = mysql_fetch_array($task_result)) {
				$step_result = stepByTaskId($task['task_id']);
				$task_array = array(
					"id" => $task['task_id'],
					"name" => $task['name'],
					"brief_desc" => $task['brief_desc'],
					"long_desc" => $task['desc'],
					"thumb_url" => $task['thumb_url']
				);

				if ($user_id) {
					if (checkId($user_id)) {
						$task_progress_result = taskProgressByTaskIdUserId($task['task_id'], $user_id);

						$task_progress_array = array(
							"progress" => "0",
							"is_complete" => "0",
							"date_completed" => ""
						);
						while ($task_progress = mysql_fetch_array($task_progress_result)) {
							$task_progress_array = array(
								"progress" => $task_progress['progress'],
								"is_complete" => $task_progress['is_complete'],
								"date_completed" => $task_progress['date_completed']
							);
						}
						$task_array['task_progress'] = $task_progress_array;
					}
					else {
						$response = errorResponse("The user id provided is not valid");
					}
				}

				$steps_array = array();
				while ($step = mysql_fetch_array($step_result)) {
					$step_array = array(
						"id" => $step['step_id'],
						"name" => $step['name'],
						"brief_desc" => $step['brief_desc']
					);

					if ($user_id) {
						if (checkId($user_id)) {
							$step_progress_result = stepProgressByStepIdUserId($step['step_id'], $user_id);

							$step_progress_array = array(
								"is_complete" => "0",
								"date_completed" => ""
							);
							while ($step_progress = mysql_fetch_array($step_progress_result)) {
								$step_progress_array = array(
									"is_complete" => $step_progress['is_complete'],
									"date_completed" => $step_progress['date_completed']
								);
							}
							$step_array['step_progress'] = $step_progress_array;
						}
					}

					array_push($steps_array, $step_array);
				}

				$task_array['steps'] = $steps_array;
				array_push($tasks_array, $task_array);
			}

			$response_array = array(
    			"status" => "ok",
    			"module_id" => $module_id,
    			"tasks" => $tasks_array
			);

			$response = json_encode($response_array);

		}
		else {
			$response = errorResponse("The module id provided is not valid");
		}
	}
	else {
		$response = errorResponse("module_id is expected");
	}

	echo $response;





	function taskByModuleId ($module_id) {
		return getTaskByModuleId($module_id);
		
	}

	function getTaskByModuleId ($module_id) {
		//$sql = sprintf("SELECT m.module_id, t.task_id, t.name, t.brief_desc, t.desc, t.thumb_url, progress FROM task t JOIN module_task mt ON t.task_id = mt.task_id JOIN module m ON mt.module_id = m.module_id JOIN user_task ut ON t.task_id = ut.task_id JOIN user u ON ut.user_id = u.user_id WHERE m.module_id = %d AND u.user_id = %d",
		//		$module_id, $user_id);

		$sql = sprintf("SELECT t.task_id, name, brief_desc, t.desc, thumb_url FROM task t JOIN module_task mt ON t.task_id = mt.task_id WHERE module_id = %d",
				$module_id);

		return executeSql($sql);
	}

	function stepByTaskId ($task_id) {
		return getStepByTaskId ($task_id);
	}

	function getStepByTaskId ($task_id) {
		//$sql = sprintf("SELECT t.task_id, s.step_id, s.name, s.brief_desc, us.is_complete, us.date_completed FROM step s JOIN task_step ts ON s.step_id = ts.step_id JOIN task t ON ts.task_id = t.task_id JOIN user_step us ON s.step_id = us.step_id JOIN user u ON us.user_id = u.user_id WHERE t.task_id = %d AND u.user_id = %d",
		//		$task_id, $user_id);

		$sql = sprintf("SELECT s.step_id, name, brief_desc FROM step s JOIN task_step ts ON s.step_id = ts.step_id WHERE task_id = %d",
			$task_id);

		return executeSql($sql);
	}

	function taskProgressByTaskIdUserId ($task_id, $user_id) {
		return getTaskProgressByTaskIdUserId($task_id, $user_id);
	}

	function getTaskProgressByTaskIdUserId ($task_id, $user_id) {
		$sql = sprintf("SELECT * FROM user_task WHERE task_id = %d AND user_id = %d",
			$task_id, $user_id);

		return executeSql($sql);
	}

	function stepProgressByStepIdUserId ($step_id, $user_id) {
		return getStepProgressByStepIdUserId($step_id, $user_id);
	}

	function getStepProgressByStepIdUserId ($step_id, $user_id) {
		$sql = sprintf("SELECT * FROM user_step WHERE step_id = %d AND user_id = %d",
			$step_id, $user_id);

		return executeSql($sql);
	}
	
	function formJson ($result) {
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