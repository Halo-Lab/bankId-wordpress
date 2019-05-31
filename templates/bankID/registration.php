<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
	<div>
		<label for="personal_number">Personal number</label>
		<input type="text" name="personal_number" value="<?php echo (isset($_POST['personal_number']) ? $_POST['personal_number'] : null); ?>">
	</div>

	<input type="submit" name="submit" value="Register"/>
</form>