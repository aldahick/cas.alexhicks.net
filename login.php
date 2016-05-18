<?php
/*
GET params:
casurl: url to redirect the cas ticket to (?casticket=fkiasnxdfqasjiko)
*/
require_once(__DIR__ . "/inc/utils.php");

if (!check_params(["casurl"], $_GET)) {
	echo("Invalid parameters");
	exit;
}
$url = $_GET["casurl"];

if (isset($_SESSION["userid"])) {
	require_once(__DIR__ . "/inc/mysql.php");
	$db = setup_database();
	if (!$db) {
		$_GET["error"] = "Database connection error while refreshing login";
	} else {
		$ticket = get_ticket($db, $_SESSION["userid"], $url);
		redirect_cas_url($url, $ticket);
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>CAS Login</title>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.6/cosmo/bootstrap.min.css" rel="stylesheet" />
		<link href="css/global.css" rel="stylesheet" />
	</head>
	<body>
		<div class="container">
<?php if (isset($_GET["error"])): ?>
			<div class="text-center text-bold">Error: <span class="error"><?=$_GET["error"]?></span></div>
<?php endif; ?>
			<form class="form-signin" action="post" method="POST">
				<input type="hidden" name="operation" value="login" />
				<input type="hidden" name="casurl" value="<?=$url?>" />
				<h2 class="form-signin-heading text-center">CAS Login</h2>
				<label for="username" class="sr-only">Username</label>
				<input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus />
				<label for="password" class="sr-only">Password</label>
				<input type="password" id="password" name="password" class="form-control" placeholder="Password" required />
				<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
			</form>
		</div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
	</body>
</html>
