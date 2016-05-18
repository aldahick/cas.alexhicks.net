<?php
require_once(__DIR__ . "/inc/utils.php");

if (!check_params(["operation"], $_POST)) {
	print_r($_POST);
	echo("Invalid parameters");
	return;
}

require_once(__DIR__ . "/inc/mysql.php"); // Don't need to connect if they have invalid params

$db = setup_database();
if (!$db) {
	echo("Database connection error");
	return;
}

if ($_POST["operation"] == "login" && check_params(["username", "password", "casurl"], $_POST)) {
    $sql = "SELECT `password`,`salt` FROM `User` WHERE `username`=':username';";
	if (!($rows = $db->query($sql, ["username" => $_POST["username"]]))) {
		echo("Login failed: database error");
		return;
	}
	if (count($rows) == 0) {
		echo("Login failed");
		return;
	}
	$hashed = hash_password($_POST["password"], $rows[0]["salt"]);
	if ($hashed == $rows[0]["password"]) { // login is good, yay us
		$ticket = get_ticket($db, $_POST["username"], $_POST["casurl"]);
		$_SESSION["userid"] = $rows[0]["userid"];
		redirect_cas_url($_POST["casurl"], $ticket);
	} else {
		header("Location: login?casurl=" . urlencode($_POST["casurl"]) . "&error=" . urlencode("Login failed"));
	}
} else if ($_POST["operation"] == "register" && check_params(["username", "password", "password2"], $_POST)) {
	if ($_POST["password"] != $_POST["password2"]) {
		header("Location: register?error=" . urlencode("Passwords must match!"));
		return;
	}
	$groupSql = [];
    $groupParams = [];
	$i = 0;
	foreach ($_POST as $k => $v) {
		if (strpos($k, "group") !== false) { // if the variable name contains "group"
			$groupSql[] =  "(:$i, :" . ($i + 1) . ")";
            $groupParams[] = $_POST["username"];
            $groupParams[] = $v;
			$i += 2;
		}
	}
	$groupSqlString = "INSERT INTO `UserGroup` (`user`, `groupName`) VALUES " . implode(", ", $groupSql) . ";";
	$salt = generate_salt();
	$hashed = hash_password($_POST["password"], $salt);
	$params = array(
		"username" => $_POST["username"],
		"password" => $hashed,
		"salt" => $salt
	);
    $sql = "INSERT INTO `User`(`username`,`password`,`salt`) VALUES(':username', ':password', ':salt');";
	if ($db->query($sql, $params)) {
		if (!$db->query($groupSqlString, $groupParams)) {
			header("Location: register?error=" . urlencode("Registration failed"));
		} else {
			header("Location: register?msg=Success");
		}
	} else {
		header("Location: register?error=" . urlencode("Registration failed"));
	}
} else {
	echo("Unsupported operation " . $_POST["operation"]);
}
?>
