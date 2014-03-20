<?php

include('parser_cfg.php');
include('parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

$gid = $_GET['gid'];
$access_token = $_GET['access_token'];
$offset = intval($_GET['offset']);
$bad_albums = get_bad_albums($db);

$r = vk_get_photos_private($db, $gid, $offset, $access_token, $bad_albums);

if ($r == -1)
	echo 'FINISH';
else
	printf("CONTINUE%d", $r);
?>