<?php

	include_once("../services/connect.php");
	include_once("../services/common.php");

	$isUpdate = (isset($isUpdate)) ? $isUpdate : $_POST['myprofile_flag'];

	if ($isUpdate) {
		$user_id = (isset($user_id)) ? $user_id : $_POST['user_id'];
		$first_name = (isset($first_name)) ? $first_name : $_POST['first_name'];
        $last_name = (isset($last_name)) ? $last_name : $_POST['last_name'];
        $age = (isset($age)) ? $age : $_POST['age'];

        $user_id = checkId($user_id);
        $first_name = checkString($first_name);
        $last_name = checkString($last_name);
        $age = checkString($age);

        $update_result = updateProfile($user_id, $first_name, $last_name, $age);

        if ($update_result) {
        	$response = '{"status": "ok", "message": "The user profile has been updated successfully!"}';
        }
        else {
        	$response = '{"status": "error", "message": "The user profile could not be updated. Please try again later."}';
        }

        echo $response;
	}

	function updateProfile ($user_id, $first_name, $last_name, $age) {
		$sql = sprintf("UPDATE user SET first_name = '%s', last_name = '%s', age = '%s' WHERE user_id = %d",
				$first_name,
				$last_name,
				$age,
				$user_id
		);

		return executeSql($sql);
	}
?>