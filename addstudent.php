<?php
include 'inc/header.php';
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="pannel-heading">
			<h2>Add Student <a class="pull-right btn btn-primary" href="index.php">Back</a></h2>
		</div>
		<form action="lib/process_student.php" method="post">
			<div class="form-group">
				<label for="name">Student Name</label>
				<input class="form-control" type="text" name="name" id="name" required="1">
			</div>
			<div class="form-group">
				<label for="email">Student Email</label>
				<input class="form-control" type="text" name="email" id="email" required="1">
			</div>
			<div class="form-group">
				<label for="phone">Student Phone</label>
				<input class="form-control" type="text" name="phone" id="phone" required="1">
			</div>
			<div class="form-group">
				<input type="hidden" name="action" value="add">
				<input class ="btn btn-primary" type="submit" name="submit" value="Add Student">
			</div>
		</form>
	</div>
</div>
<?php
include 'inc/footer.php';
?>
