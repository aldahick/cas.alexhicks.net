<?php
require_once(__DIR__ . "/php-utils/cas.php");
if (!in_array("Administrator", $userGroups)) {
	echo("Unauthorized access");
	exit;
}
require_once(__DIR__ . "/utils.php");
require_once(__DIR__ . "/mysql.php");
$db = setup_database();
if (!$db) {
	echo("Database connection error");
	exit;
}

$rows = $db->query("SELECT `name` FROM `Group`;", []);
$groups = [];
if ($rows && count($rows) > 0) {
	foreach ($rows as $k => $row) {
		$groups[] = $row["name"];
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Register CAS User</title>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.6/cosmo/bootstrap.min.css" rel="stylesheet" />
		<link href="css/global.css" rel="stylesheet" />
	</head>
	<body>
		<div class="container">
<?php if (isset($_GET["msg"])): ?>
			<div class="text-center text-bold"><?=$_GET["msg"]?></div>
<?php endif; ?>
<?php if (isset($_GET["error"])): ?>
			<div class="text-center text-bold">Error: <span class="error"><?=$_GET["error"]?></span></div>
<?php endif; ?>
			<form class="form-signin" action="post" method="POST">
				<input type="hidden" name="operation" value="register" />
				<h2 class="form-signin-heading text-center">Register CAS User</h2>
				<div class="form-group">
					<label for="username" class="sr-only">Username</label>
					<input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus />
				</div>
				<div class="form-group">
					<label for="password" class="sr-only">Password</label>
					<input type="password" id="password" name="password" class="form-control" placeholder="Password" required />
				</div>
				<div class="form-group">
					<label for="password2" class="sr-only">Password again</label>
					<input type="password" id="password2" name="password2" class="form-control" placeholder="Password again" required />
				</div>
				<label for="groups">Groups</label>
				<div id="groups">
<?php foreach ($groups as $k => $group): ?>
					<div class="checkbox">
						<label><input type="checkbox" name="group<?=$k?>" value="<?=$group?>" /><?=$group?></label>
					</div>
<?php endforeach; ?>
				</div>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
			</form>
		</div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
	</body>
</html>
