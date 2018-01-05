<?php 
include 'lib/User.php';
include 'inc/header.php';
Session::checkSession();

?>

<?php if (isset($_GET['id'])) {
	$userid = (int)$_GET['id'];

	$sesId = Session::get("id"); 
					if ($userid != $sesId) {
						header("Location:index.php");
					}

} 
$user = new User();
if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['updatepass'])) {
	$updatpeass = $user->updateUPassword($userid, $_POST);
}
?>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h2>Change Passowrd <span class="pull-right"><a class="btn btn-primary" href="profile.php?<?php echo $userid; ?>">Back</a></span></h2>
			</div>
			<div class="panel-body">
				<div style="max-width: 600px; margin: 0 auto">
					<?php 
					if (isset($updatpeass)) {
						echo $updatpeass;
					}
					  ?>

					
				<form action="" method="POST">
					<div class="form-group">
						<label for="old_pass">Old Passowrd</label>
						<input type="password" id="old_pass" name="old_pass" class="form-control" value="">
					</div>
					<div class="form-group">
						<label for="password">New Password</label>
						<input type="password" id="password" name="password" class="form-control" value="">
					</div>
					
					
					<button type="submit" name="updatepass" class="btn btn-success">Update</button>
					
				</form>
				
			</div>
			</div>
		</div>
		<!-- Body Text -->
		<?php
include 'inc/footer.php';
		?>
