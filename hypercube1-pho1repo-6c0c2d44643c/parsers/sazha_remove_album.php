<?php
include('parser_cfg.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

$aid = $_GET['aid'];

$stmt = $db->prepare("INSERT INTO bad_albums(album_id) VALUES(?)");
echo $db->error;
$stmt->bind_param('d', $aid);
echo $db->error;
$stmt->execute();
$stmt->close();

$stmt = $db->prepare("DELETE FROM main WHERE album_id = ?");
echo $db->error;
$stmt->bind_param('d', $aid);
echo $db->error;
$stmt->execute();
$stmt->close();



?>