<?php

require_once 'inc/connection.inc.php';
require_once 'inc/header.func.inc.php';

if(loggedin())
	header('Location: tasks');

$error_message = array(
	"Entered Passwords do not match",
	"Could not process your request. Try Again",
	"Username Already Taken",
	"Invalid Username - Password Combination",
	"Successfully Register. Now Please Login To Proceed", //success case
	"Username cannot have white spaces"
);

if(isset($_POST['signup-submit'])){
	$name = strtolower(mysqli_real_escape_string($connection, htmlspecialchars($_POST['name'])));
	$username = mysqli_real_escape_string($connection, htmlspecialchars($_POST['username']));
	$password = md5(mysqli_real_escape_string($connection, htmlspecialchars($_POST['pass'])));
	$confirmpassword = md5(mysqli_real_escape_string($connection, htmlspecialchars($_POST['confpass'])));
	
	if($confirmpassword != $password){
		$error = 0;
	} elseif(preg_match('/\s/',$username)){
		$error = 5;
	} else {
		$query = "SELECT * FROM `users` WHERE `username`='$username'";
		if($query_run = mysqli_query($connection, $query)){
			if(mysqli_num_rows($query_run) > 0 ){
				$error = 2;
			} else {
				$query = "INSERT INTO `users` (`username`,`password`,`name`) VALUES ('$username','$password','$name')";
				if(!mysqli_query($connection, $query))
					$error = 1;
				else {
					$error = 4;
					header('Location : login.php');
				}
			}
		} else {
			$error = 1;
		}
	}
}

if(isset($_POST['login-submit'])){

	$username = mysqli_real_escape_string($connection, htmlspecialchars($_POST['login-username']));
	$password = md5(mysqli_real_escape_string($connection, htmlspecialchars($_POST['login-pass'])));	
	
	$query = "SELECT * FROM `users` WHERE `username`='$username' AND `password`='$password'";
	
	if($query_run = mysqli_query($connection, $query)){
		if(mysqli_num_rows($query_run) == 0 ){
			$error = 3;
		} else {
			$query_row = mysqli_fetch_assoc($query_run);
			
			$_SESSION['username'] = $query_row['username'];
			$_SESSION['name'] = $query_row['name'];
			$_SESSION['uid'] = $query_row['uid'];
			header('Location: tasks.php');
		}
	} else {
		$error = 1;
	}	
}

include('inc/header.inc.php');
include('inc/navbar.inc.php');
?>
	<div class="container" style="padding-top:50px">
<?php 
	if(isset($error)){
		$message_style = ($error == 4) ? 'success' : 'danger';
		echo '<div class="alert alert-' . $message_style . ' alert-dismissible" style="margin:10px 10px -10px 10px ;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$error_message[$error].'</div>';
	}
?>	
        <div class="row">
            <div class="col-md-6 col-lg-6">
                <form id="login-form" method="POST">
                    <h1>login</h1>
                    <div class="form-group">
                        <label>Username *</label><input name="login-username" type="text" class="form-control input-lg" placeholder="Enter Your Username" required>
                    </div>
                    <div class="form-group">
                        <label>Password *</label><input name="login-pass" type="password" class="form-control input-lg" placeholder="Password" required>
                    </div>
                    <center><button type="submit" name="login-submit" class="btn btn-success btn-lg">Login</button></center>
                </form>
            </div>
            <div class="col-md-6 col-lg-6">
                <form id="login-form" method="POST">
                    <h1>sign up</h1>
                    <div class="form-group">
                        <label>Name *</label><input name="name" type="text" class="form-control" placeholder="Enter Your Name" required>
                    </div>
                    <div class="form-group">
                        <label>Username *</label><input name="username" type="text" class="form-control" placeholder="Enter Username" required>
                    </div>
                    <div class="form-group">
                        <label>Password *</label><input name="pass" type="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label>Re-enter Password *</label><input name="confpass" type="password" class="form-control" placeholder="ReEnter Password" required>
                    </div>
                    <center><button type="submit" name="signup-submit" class="btn btn-warning btn-lg">Sign Up</button></center>
                </form>
            </div>
        </div>
    </div>
<?php include('inc/footer.php');?>