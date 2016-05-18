<?php

session_start(["cookie_httponly" => true]);

function check_params($params, $arr) {
	if (!$arr) {
		$arr = $_POST;
	}
	foreach ($params as $k => $param) {
		if (!isset($arr[$param])) {
			return false;
		}
	}
	return true;
}

function get_database_error($db) {
	return "Database error: (" . $db->errno . "): " . $db->error;
}

function get_ticket($db, $username, $casurl) {
    $sql = "UPDATE `UserTicket` SET `valid`=0 WHERE `user`=':user' AND `url`=':url' AND `valid`=1;";
    $params = ["user" => $username, "url" => $casurl];
	$db->query($sql, $params);
	$ticket = md5(uniqid());
    $params["ticket"] = $ticket;
    $sql = "INSERT INTO `UserTicket`(`ticket`,`user`,`url`,`time`,`valid`) VALUES(':ticket', ':user', ':url', NOW(), 1);";
	$db->query($sql, $params);
	return $ticket;
}

function generate_salt() {
	return substr(base64_encode(mt_rand()), 0, 20);
}

function hash_password($password, $salt) {
	return hash("sha256", $password . $salt);
}

function redirect_cas_url($casurl, $ticket) {
	header("Location: " . $casurl . (strpos($casurl, "?") === false ? "?" : "&") . "casticket=" . urlencode($ticket));
}

?>
