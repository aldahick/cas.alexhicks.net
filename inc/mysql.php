<?php
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/../php-utils/Database.php");

function setup_database() {
	global $MYSQL_HOSTNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD, $MYSQL_DATABASE, $MYSQL_PORT;
	$db = new Database($MYSQL_HOSTNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD, $MYSQL_DATABASE, $MYSQL_PORT);
	if ($db->is_dead) {
		return false;
	}
	return $db;
}
?>
