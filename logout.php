<?php
	require 'inc/header.func.inc.php';
	session_destroy();
	if($http_referer)
		header('Location: '.$http_referer);
	else
		header('Location: login');
?>