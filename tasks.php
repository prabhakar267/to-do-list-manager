<?php
require_once 'inc/connection.inc.php';
require_once 'inc/header.func.inc.php';

if(!loggedin())
	header('Location: login');

$error_messages = array(
	"Incorrect Date. Please Enter a Valid Date",
	"Could Not Perform The Specified Action. Please Try Again.",
	"Could Not Load your event list. Please Try Again.",
	"You Can Edit only one Task at a Time.",
	"Select Atleast one Task to perform This Task"
);

$months = array("January","February","March","April","May","June","July","August","September","October","November","December");
$edit_flag = 0;
$no_task_flag = 0;
$userID = $_SESSION['uid'];

include('inc/header.inc.php');
include('inc/navbar.inc.php');
?>
	<div class="container section-tasks">
		<div class="col-md-6 task-list">
<?php
	if(isset($_POST['submit'])){
		$task = mysql_real_escape_string(htmlspecialchars(@$_POST['task']));
		$day_temp = $_POST['day'];
		$month_temp = $_POST['month'];
		$year_temp = $_POST['year'];
		
		if(checkdate($month_temp, $day_temp, $year_temp)){
			$timestamp = strtotime($day_temp.'-'.$month_temp.'-'.$year_temp);
			$query = "INSERT INTO `todo-events` (`description`,`time`,`uid`) VALUES ('$task','$timestamp','$userID')";
			if(!mysql_query($query))
				$error = 1;
			
		} else {
			$error = 0;
		}
	}
	
	if(isset($_POST['taskdonesubmit'])){
		if(count($_POST['tasklist']) >= 1){
			foreach($_POST['tasklist'] as $selectedtasks){
				$query = "UPDATE `todo-events` SET `done`=1 WHERE `id`='$selectedtasks'";
				if(!mysql_query($query))
					$error = 1;
			}
		} else {
			$error = 4;
		}
	}
	
	if(isset($_POST['deletetask-submit'])){
		if(count($_POST['tasklist']) >= 1){
			foreach($_POST['tasklist'] as $selectedtasks){
				$query = "DELETE FROM `todo-events` WHERE `id`='$selectedtasks'";
				if(!mysql_query($query))
					$error = 1;
			}
		} else {
			$error = 4;
		}
	}
	
	if(isset($_POST['edit-tasks'])){
		if(count($_POST['tasklist']) == 1){
			$selectedtasks = $_POST['tasklist'][0];
			$query_row = mysql_fetch_array(mysql_query("SELECT * FROM `todo-events` WHERE `id`='$selectedtasks' AND `uid`='$userID'"));
			$edit_flag = 1;
			$edit_task = $query_row['description'];
			$edit_time = date('d/m/Y', $query_row['time']);
			$edit_time = explode('/',$edit_time);
			$edit_day = $edit_time[0];
			$edit_month = (int)$edit_time[1];
			$edit_year = $edit_time[2];
			$edit_done = $query_row['done'];
				
			$query = "DELETE FROM `todo-events` WHERE `id`='$selectedtasks'";
			if(!mysql_query($query))
				$error = 1;
		} elseif(count($_POST['tasklist']) > 1) {
			$error = 3;
		} else {
			$error = 4;
		}
	}

	$query = "SELECT * FROM `todo-events` WHERE `uid`='$userID' ORDER BY `done` ASC, `time` DESC";
	if($query_run = mysql_query($query)){
		echo "\n".'<form method="POST" id="taskslist">'."\n\n";
		if(mysql_num_rows($query_run) == 0){
			echo '<div class="task"><center>No Tasks</center></div>';
			$no_task_flag = 1;
		} else { 
			while($query_row = mysql_fetch_assoc($query_run)){
				$id = $query_row['id'];
				$done_flag = $query_row['done'];
				$date = date('d/m/Y', $query_row['time']);
				$event = trim($query_row['description']);
				
				if($done_flag == 0)
					$done_flag_text = "Not Completed";
				else
					$done_flag_text = "Completed";
			
				echo '<div class="task">'."\n".'<p class="task-complition">'.$done_flag_text.'<input class="task-checkbox" type="checkbox" name="tasklist[]" value="'.$id.'"></p>'."\n".'<p class="task-desc';
				if($done_flag != 0)
					echo ' task-complete';
				echo '">'.$event.'</p>'."\n".'<p class="task-date">'.$date.'</p>'."\n".'</div>'."\n\n";
			}
		}
		echo '</form>'."\n";
	} else {
		$error = 2;
	}
?>
		</div>
		<div class="col-md-6 add-task-list">
<?php 
	if(isset($error)){
		echo '<div class="alert alert-danger alert-dismissible" style="margin:10px;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.@$error_messages[$error].'</div>';
	}
?>
			<div class="action-buttons <?php if($no_task_flag == 1) echo 'hidden';?>">
				<button form="taskslist" type="submit" class="btn btn-lg btn-success" name="taskdonesubmit" <?php if($no_task_flag == 1) echo 'disabled';?>>Mark as Done</button>
				<button form="taskslist" type="submit" class="btn btn-lg btn-success" name="deletetask-submit" <?php if($no_task_flag == 1) echo 'disabled';?>>Delete Tasks</button>
				<button form="taskslist" type="submit" class="btn btn-lg btn-success" name="edit-tasks" <?php if($no_task_flag == 1) echo 'disabled';?>>Edit Task</button>
			</div>
			<form method="POST" id="addtaskform">
				<div class="row" style="margin: 10px auto">
					<div class="col-md-12">
						<textarea class="form-control" rows="6" type="text" required name="task" placeholder="Enter your task here"><?php if($edit_flag) echo $edit_task;?></textarea>
					</div>
				</div>
				<div class="row" style="margin: 10px auto">
					<div class="col-md-4 col-sm-4 col-xs-4">
						<input type="text" placeholder="Date" class="form-control" required name="day" value="<?php if($edit_flag) echo $edit_day;?>">
					</div>
					<div class="col-md-4 col-sm-4 col-xs-4">
						<select name="month" class="form-control" value="<?php if($edit_flag) echo $edit_month;?>">
<?php
for ($i=1;$i<=12;$i++){
	if($edit_flag && $i == $edit_month)
		echo "<option value='".$i."' selected>".$i." - ".$months[$i-1]."</option>"."\n";
	else
		echo "<option value='".$i."'>".$i." - ".$months[$i-1]."</option>"."\n";
}
?>
						</select>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-4">
						<input type="text" required placeholder="Year" class="form-control" name="year" value="<?php if($edit_flag) echo $edit_year;?>">
					</div>
				</div>
				<div class="row submit-button-row" style="margin-top:20px;">
					<div class="col-md-10 col-md-offset-1">
						<button form="addtaskform" type="submit" class="btn btn-lg btn-block btn-primary" name="submit">Add Task</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php include('inc/footer.php');?>