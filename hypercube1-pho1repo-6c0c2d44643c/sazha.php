<?php
include('cfg.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
$db->set_charset("utf8"); # на всякий пожарный

$pid = $_GET['pid'];
$longip = ip2long($_SERVER['REMOTE_ADDR']);
$stmt = $db->prepare("INSERT IGNORE INTO sazha1(photo_id,longip) VALUES(?,?)");
echo $db->error;
$stmt->bind_param('dd', $pid , $longip);
echo $db->error;

$stmt->execute();
$stmt->close();
	
?>