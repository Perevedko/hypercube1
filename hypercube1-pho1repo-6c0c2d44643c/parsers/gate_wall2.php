<?php

include('parser_cfg.php');
include('parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

$gid = $_GET['gid'];
$offset = intval($_GET['offset']);

$r = vk_get_photos_wall($db, $gid, $offset);

if ($r == -1)
	echo 'FINISH';
else
	printf("CONTINUE%d", $r);
?>