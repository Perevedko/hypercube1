<?php
include('parser_cfg.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

$pid = $_GET['pid'];

$stmt = $db->prepare("DELETE FROM sazha1 WHERE photo_id = ?");
echo $db->error;
$stmt->bind_param('d', $pid);
echo $db->error;
$stmt->execute();
$stmt->close();


	
?>