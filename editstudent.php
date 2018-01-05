<?php
include 'inc/header.php';
include 'lib/Database.php';
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="pannel-heading">
			<h2>Update Student <a class="pull-right btn btn-primary" href="index.php">Back</a></h2>
		</div>

		<?php 
		$id = $_GET['id'];
		$db = new Database();
		$table = "tbl_student";
		$wherecond = array(
			'where' =>array('id'=> $id),
			'return_type' => 'single'
			);
		$value = $db->select($table, $wherecond);
		if (!empty($value)) {
		 ?>
		<form action="lib/process_student.php" method="post">
			<div class="form-group">
				<label for="name">Student Name</label>
				<input class="form-control" type="text" name="name" id="name" required="1" value="<?php echo $value['name']; ?>">
			</div>
			<div class="form-group">
				<label for="email">Student Email</label>
				<input class="form-control" type="text" name="email" id="email" required="1" value="<?php echo $value['email']; ?>">
			</div>
			<div class="form-group">
				<label for="phone">Student Phone</label>
				<input class="form-control" type="text" name="phone" id="phone" required="1" value="<?php echo $value['phone']; ?>">
			</div>
			<div class="form-group">
				<input type="hidden" name="id" value="<?php echo $value['id']; ?>">
				<input type="hidden" name="action" value="edit">
				<input class ="btn btn-primary" type="submit" name="submit" value="Update Student">
			</div>
		</form>
	</div>

<?php } ?>


</div>
<?php
include 'inc/footer.php';
?>
