<?php
$error_messages = array(
	"Incorrect Date. Please Enter a Valid Date",
	"Could Not Perform The Specified Action. Please Try Again.",
	"Could Not Load your event list. Please Try Again.",
	"You Can Edit only one Task at a Time."
);

$months = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
$edit_flag = 0;

require 'inc/connection.inc.php';

	if(isset($_POST['submit'])){
		$task = trim($_POST['task']);
		$day_temp = $_POST['day'];
		$month_temp = $_POST['month'];
		$year_temp = $_POST['year'];
		
		if(checkdate($month_temp, $day_temp, $year_temp)){
			$timestamp = strtotime($day_temp.'-'.$month_temp.'-'.$year_temp);
			$query = "INSERT INTO `todo-events` (`description`,`time`) VALUES ('$task','$timestamp')";
			if(!mysql_query($query))
				$error = 1;
			
		} else {
			$error = 0;
		}
	}
	
	if(isset($_POST['taskdonesubmit'])){
		foreach($_POST['tasklist'] as $selectedtasks){
			$query = "UPDATE `todo-events` SET `done`=1 WHERE `id`='$selectedtasks'";
			if(!mysql_query($query))
				$error = 1;
		}
	}
	
	if(isset($_POST['deletetask-submit'])){
		foreach($_POST['tasklist'] as $selectedtasks){
			$query = "DELETE FROM `todo-events` WHERE `id`='$selectedtasks'";
			if(!mysql_query($query))
				$error = 1;
		}
	}
	
	if(isset($_POST['edit-tasks'])){
		if(count($_POST['tasklist']) == 1){
			$selectedtasks = $_POST['tasklist'][0];
			$query_row = mysql_fetch_array(mysql_query("SELECT * FROM `todo-events` WHERE `id`='$selectedtasks'"));
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
		} else {
			$error = 3;
		}
	}

	$query = "SELECT * FROM `todo-events` ORDER BY time ASC";
	if($query_run = mysql_query($query)){
		echo '<form method="POST">'."\n";
		while($query_row = mysql_fetch_assoc($query_run)){
			$id = $query_row['id'];
			$done_flag = $query_row['done'];
			$date = date('d/m/Y', $query_row['time']);
			$event = trim($query_row['description']);
			
			if($done_flag == 0)
				$done_flag = "Not Completed";
			else
				$done_flag = "Completed";
			
			echo '	<input type="checkbox" name="tasklist[]" value="'.$id.'"><b>'.$done_flag.'</b> '.$event.' '.$date.'<br>'."\n";
		}
		echo '<br>'."\n".'<input type="submit" value="Done Tasks" name="taskdonesubmit"> '."\n";
		echo '<input type="submit" value="Delete Tasks" name="deletetask-submit"> '."\n";
		echo '<input type="submit" value="Edit Task" name="edit-tasks"> '."\n";
		echo '</form>'."\n";
	} else {
		$error = 2;
	}
?>

<form method="POST">
	Task : <textarea type="text" required name="task"><?php if($edit_flag) echo $edit_task;?></textarea><br>
	Date : <input type="text" required name="day" value="<?php if($edit_flag) echo $edit_day;?>">
    <select name="month" class="form-control" value="<?php if($edit_flag) echo $edit_month;?>">
<?php
    for ($i=1;$i<=12;$i++)
		if($edit_flag && $i == $edit_month)
			echo "<option value='".$i."' selected>".$i." - ".$months[$i-1]."</option>"."\n";
		else
			echo "<option value='".$i."'>".$i." - ".$months[$i-1]."</option>"."\n";
?>
	</select>
	<input type="text" required name="year" value="<?php if($edit_flag) echo $edit_year;?>"><br>
	<input name="submit" value="Submit!" type="submit" />
</form>