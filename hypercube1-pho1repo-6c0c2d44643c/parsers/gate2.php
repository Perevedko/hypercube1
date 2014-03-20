<?php

include('parser_cfg.php');
include('parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

$gid = $_GET['gid'];
$aid = $_GET['aid'];
$offset = 0;
$cnt = 0;
while (($r = vk_get_photos($db, $gid, $aid, $offset)) != -1) {
	$offset = $r;
}
?>