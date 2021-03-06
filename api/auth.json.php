<?php
include_once "config/config.php";
include_once "lib/database.php";
include_once "lib/json.php";
include_once "models/user.php";

$user = NULL;
$returnStatement = array();
if(isset($_REQUEST['register'])) {
	if(createNewUser($user)) {
		$returnStatement['status'] = 0;
		$returnStatement['message'] = "You have been registered.";
		$returnStatement['data']['user']['token'] = $user->token;
		$returnStatement['data']['user']['username'] = $user->username;
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "There was a problem creating a new user.";
	}
} else if(isset($_REQUEST['authenticate'])) {
	if(checkAuth($user)) {
		$returnStatement['status'] = 0;
		$returnStatement['message'] = "The authentication token is valid.";
		$returnStatement['data']['user']['token'] = $user->token;
		$returnStatement['data']['user']['username'] = $user->username;
	} else {
		$returnStatement['status'] = 1;
		$returnStatement['message'] = "The authentication token is either missing or invalid.";
	}
} else {
	$returnStatement['status'] = 1;
	$returnStatement['message'] = "There was no action specified.";
}
mysql_close($db);
returnJSON($returnStatement);

function createNewUser(&$user) {
	if(!isset($_REQUEST['username'])) {
		return false;
	}
	try {
		$user = new User();
	} catch(Exception $e) {
		return false;
	}
	if(!$user->register($_REQUEST['username'])) {
		return false;
	}
	return true;
}

function checkAuth(&$user) {
	if(!isset($_REQUEST['token'])) {
		return false;
	}
	try {
		$user = new User($_REQUEST['token']);
	} catch(Exception $e) {
		return false;
	}
	return true;
}
?>
