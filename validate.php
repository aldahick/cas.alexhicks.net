<?php
require_once(__DIR__ . "/inc/utils.php");

if (!check_params(["casurl", "casticket"], $_GET)) {
	echo("no\n422");
	exit;
}

require_once(__DIR__ . "/inc/mysql.php");

$db = setup_database();
if (!$db) {
	echo("no\n500");
	exit;
}

$sql = "SELECT
    `UserTicket`.`ticket` AS \"ticket\"
	`User`.`username` AS \"username\"
FROM `UserTicket`, `User`
WHERE
	`User`.`username`=`UserTicket`.`user` AND
	`ticket`=':ticket' AND
	`url`=':url' AND
	`valid`=1;";
$rows = $db->query($sql, ["ticket" => $_GET["casticket"], "url" => $_GET["casurl"]]);
if (!$rows || count($rows) == 0) {
	echo("no\n404 " . $_GET["casurl"]);
	exit;
}

$username = $rows[0]["username"];
$ticket = $rows[0]["ticket"];
$sql = "SELECT
	`UserGroup`.`groupName` as \"group\"
FROM `UserGroup`
WHERE
    `UserGroup`.`user` = :username;";

$rows = $db->query($sql, ["username" => $username]);
$groups = [];
if ($rows && count($rows) > 0) {
	foreach ($rows as $k => $row) {
		$groups[] = $row["group"];
	}
}
$sql = "UPDATE `UserTicket` SET `valid`=0 WHERE `ticket` = :ticket;";
if (!$db->query($sql, ["ticket" => $ticket])) {
	echo("no\n500 " . $username . " | " . get_database_error($db->db));
	exit;
}

header("Content-Type: application/json");
echo(json_encode(array(
	"username" => $username,
	"groups" => $groups
)));
?>
