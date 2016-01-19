<?php
    include_once("connect.php");
	include_once("common.php");
	include_once("step.php");
	
	$user_id = (isset($user_id)) ? $user_id : $_GET['user_id'];
	$module_id = (isset($module_id)) ? $module_id : $_GET['module_id'];
	$step_action = (isset($step_action)) ? $step_action : $_GET['step_action'];
	$task_id = (isset($task_id)) ? $task_id : $_GET['task_id'];
	$step_id = (isset($step_id)) ? $step_id : $_GET['step_id'];
	$response = "";
	
	if ($step_action) {
		$step_is_complete = (isset($step_is_complete)) ? $step_is_complete : $_GET['step_is_complete'];
		$step_date_completed = (isset($step_date_completed)) ? $step_date_completed : $_GET['step_date_completed'];

		$task_progress = (isset($task_progress)) ? $task_progress : $_GET['task_progress'];
		$task_is_complete = (isset($task_is_complete)) ? $task_is_complete : $_GET['task_is_complete'];
		$task_date_completed = (isset($task_date_completed)) ? $task_date_completed : $_GET['task_date_completed'];

		$module_progress = (isset($module_progress)) ? $module_progress : $_GET['module_progress'];
		$module_is_complete = (isset($module_is_complete)) ? $module_is_complete : $_GET['module_is_complete'];
		$module_date_completed = (isset($module_date_completed)) ? $module_date_completed : $_GET['module_date_completed'];

		if ($step_action == "record") {
			$step_updated = recordStepProgress($step_id, $user_id, $step_is_complete, $step_date_completed);
			$message = "Tep has been recorded successfully";
		}
		else if ($step_action == "remove") {
			$step_updated = removeStepProgress($step_id, $user_id);
			$message = "Tep has been removed successfully";
		}
		if ($step_updated) {
			$task_updated = updateTaskProgress($task_id, $user_id, $task_progress, $task_is_complete, $task_date_completed);

			if ($task_updated) {
				$module_updated = updateModuleProgress($module_id, $user_id, $module_progress, $module_is_complete, $module_date_completed);

				if ($module_updated) {
					$response = '{"status": "ok", "message": "'.$message.'"}';
				}
				else {
					$response = '{"status": "error", "message": "The module progress could not be updated. Please try again later."}';
				}
			}
			else {
				$response = '{"status": "error", "message": "The task progress could not be updated. Please try again later."}';
			}
		}
		else {
			$response = '{"status": "error", "message": "The step progress could not be recorded. Please try again later."}';
		}
	}
	else if ($module_id) {
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
		$sql = sprintf("SELECT t.task_id, name, brief_desc, t.desc, thumb_url FROM task t JOIN module_task mt ON t.task_id = mt.task_id WHERE module_id = %d",
				$module_id);

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

	function updateTaskProgress ($task_id, $user_id, $progress, $is_complete, $date_completed) {
		$task_already_added = isTaskAlreadyAdded($user_id, $task_id);

		if ($task_already_added) {
			$sql = sprintf("UPDATE user_task SET progress = '%s', is_complete = '%s', date_completed = '%s' WHERE user_id = %d AND task_id = %d",
				$progress, $is_complete, $date_completed, $user_id, $task_id
			);
		}
		else {
			$sql = sprintf("INSERT INTO user_task (user_id, task_id, progress, is_complete, date_completed) VALUE (%d, %d, '%s', '%s', '%s')",
				$user_id, $task_id, $progress, $is_complete, $date_completed
			);
		}

		return executeSql($sql);
	}

	function updateModuleProgress ($module_id, $user_id, $progress, $is_complete, $date_completed) {
		$sql = sprintf("UPDATE user_module SET progress = '%s', is_complete = '%s', date_completed = '%s' WHERE user_id = %d AND module_id = %d",
			$progress, $is_complete, $date_completed, $user_id, $module_id
		);

		echo $sql;

		return executeSql($sql);
	}

	function isTaskAlreadyAdded ($user_id, $task_id) {
		$sql = sprintf("SELECT user_task_id FROM user_task WHERE user_id = %d AND task_id = %d",
				$user_id, $task_id
		);

		$exists = executeSql($sql);

		return mysql_num_rows($exists);
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