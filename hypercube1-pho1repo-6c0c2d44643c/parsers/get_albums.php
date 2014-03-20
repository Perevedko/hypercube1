<?php

# ToDo: добавить фильтр из bad_albums
include('parser_cfg.php');
include('parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
$gid = $_GET['gid'];

$result = vk_get_albums($db, $gid);

$response = array();
$response['response'] = $result;
echo json_encode($response);
?>