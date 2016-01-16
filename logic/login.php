<?php
	include_once("services/connect.php");
	include_once("services/common.php");

	function login ($email, $password) {
		$sql = sprintf("SELECT user_id, first_name FROM user WHERE email = '%s' AND password = '%s'",
				$email,
				md5($password)
		);

		return executeSql($sql);
	}

	function signup ($first_name, $last_name, $age, $email, $password) {
		$sql = sprintf("SELECT user_id FROM user WHERE email = '%s'",
				$email
		);

		$exists = executeSql($sql);

		if (mysql_num_rows($exists) == 1) {
			return "exists";
		}
		else {
			$sql = sprintf("INSERT INTO user (first_name, last_name, age, email, password, receive_notifications, is_new, lang_id) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$first_name,
					$last_name,
					$age,
					$email,
					md5($password),
					"0",
					"1",
					"1");

			return executeSql($sql);
		}
	}
?>