<?php
	$connect_error = 'Could not connect';
	$mysql_host = 'localhost';
	$mysql_user = 'mkstin_prabhakar';
	$mysql_pass = 'pr@bh@k@r';
	$mysql_data = 'mkstin_pg_projects';
	
	if(!@mysql_connect($mysql_host , $mysql_user , $mysql_pass) || !@mysql_select_db($mysql_data))
		die($connect_error);
?>