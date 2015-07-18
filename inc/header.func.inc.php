<?php 
	ob_start();
	session_start();
	$script_name = $_SERVER['SCRIPT_NAME'];
	@$http_referer = $_SERVER['HTTP_REFERER'];

	function loggedin(){
		if(isset($_SESSION['uid']) && !empty($_SESSION['uid'])) {
			return true;
		} else {
			return false;
		}
	}
?>