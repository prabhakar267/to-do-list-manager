<div class="top-info-bar">
	To-Do List Manager
<?php
if(loggedin())
	echo '<a href="logout.php"><button class="pull-right btn btn-danger">Logout</button></a>';
?>
	<!--<button class="pull-right btn btn-danger" data-toggle="modal" data-target="#moreInfoModal">More Info</button>-->
</div>

<div class="modal fade" id="moreInfoModal" tabindex="-1" role="dialog" aria-labelledby="moreInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="moreInfoModalLabel">To-Do List Manager</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>