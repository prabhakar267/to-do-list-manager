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
	"Successfully Register. Now Please Login To Proceed",
	"Username cannot have white spaces"
);

if(isset($_POST['signup-submit'])){
	$name = strtolower(mysql_real_escape_string(htmlspecialchars($_POST['name'])));
	$username = mysql_real_escape_string(htmlspecialchars($_POST['username']));
	$password = md5(mysql_real_escape_string(htmlspecialchars($_POST['pass'])));
	$confirmpassword = md5(mysql_real_escape_string(htmlspecialchars($_POST['confpass'])));
	if($confirmpassword != $password){
		$error = 0;
	} elseif(preg_match('/\s/',$username)){
		$error = 5;
	} else {
		$query = "SELECT * FROM `todo-users` WHERE `username`='$username'";
		if($query_run = mysql_query($query)){
			if(mysql_num_rows($query_run) > 0 ){
				$error = 2;
			} else {
				$query = "INSERT INTO `todo-users` (`username`,`password`,`name`) VALUES ('$username','$password','$name')";
				if(!mysql_query($query))
					$error = 1;
				else {
					$error = 4;
					header('Location : login');
				}
			}
		} else {
			$error = 1;
		}
	}
}

if(isset($_POST['login-submit'])){
	$username = mysql_real_escape_string(htmlspecialchars($_POST['login-username']));
	$password = md5(mysql_real_escape_string(htmlspecialchars($_POST['login-pass'])));	
	
	$query = "SELECT * FROM `todo-users` WHERE `username`='$username' AND `password`='$password'";
	if($query_run = mysql_query($query)){
		if(mysql_num_rows($query_run) == 0 ){
			$error = 3;
		} else {
			$query_row = mysql_fetch_assoc($query_run);
			
			$_SESSION['username'] = $query_row['username'];
			$_SESSION['name'] = $query_row['name'];
			$_SESSION['uid'] = $query_row['uid'];
			header('Location: tasks');
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
		echo '<div class="alert alert-danger alert-dismissible" style="margin:10px 10px -10px 10px ;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.@$error_message[$error].'</div>';
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