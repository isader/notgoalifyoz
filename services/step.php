<?php

	include_once("connect.php");
	include_once("common.php");

	function stepByTaskId ($task_id) {
		return getStepByTaskId ($task_id);
	}

	function getStepByTaskId ($task_id) {
		//$sql = sprintf("SELECT t.task_id, s.step_id, s.name, s.brief_desc, us.is_complete, us.date_completed FROM step s JOIN task_step ts ON s.step_id = ts.step_id JOIN task t ON ts.task_id = t.task_id JOIN user_step us ON s.step_id = us.step_id JOIN user u ON us.user_id = u.user_id WHERE t.task_id = %d AND u.user_id = %d",
		//		$task_id, $user_id);

		$sql = sprintf("SELECT s.step_id, name, brief_desc FROM step s JOIN task_step ts ON s.step_id = ts.step_id WHERE task_id = %d ORDER BY step_id ASC",
			$task_id);

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

	function isStepAlreadyAdded ($user_id, $step_id) {
		$sql = sprintf("SELECT user_step_id FROM user_step WHERE user_id = %d AND step_id = %d",
				$user_id, $step_id
		);

		$exists = executeSql($sql);

		return mysql_num_rows($exists);
	}


	function recordStepProgress ($step_id, $user_id, $is_complete, $date_completed) {
		$step_already_added = isStepAlreadyAdded($user_id, $step_id);

		if ($step_already_added) {

			return '{"status": "error", "message": "The user has already completed this task"}';
		}
		else {
			$sql = sprintf("INSERT INTO user_step (user_id, step_id, is_complete, date_completed) VALUE ('%s', '%s', '%s', '%s')",
				$user_id, $step_id, $is_complete, $date_completed
			);

			$done = executeSql($sql);

			if ($done) {
				return '{"status": "ok", "message": "Step has been added successfully!", "step_id": '.$step_id.'}';
			}
			else {
				return '{"status": "error", "message": "There was a problem while checking this step. Please try again later"}';
			}
		}
	}

	function removeStepProgress ($step_id, $user_id) {
		$sql = sprintf("DELETE FROM user_step WHERE user_id = '%d' AND step_id = '%d'",
			$user_id, $step_id);

		return executeSql($sql);
	}
?>