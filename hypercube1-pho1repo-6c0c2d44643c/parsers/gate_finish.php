<?php

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

include('parser_cfg.php');
include('parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

// Задачи:
// 1. Обновить дату для $gid в таблице groups
// 2. Создать gz-архив с дампом dump-$gid.txt.gz

var_dump();
$gid = $_GET['gid'];
if (ctype_digit($gid) == false)
	die('invalid gid');

// ---------
// 1. Обновить дату для $gid в таблице groups
$ref = $_SERVER['HTTP_REFERER'];
if (endsWith($ref, 'gate2.html'))
	$extra_info = array('is_private' => 0, 'parse_albums' => 1);
else if (endsWith($ref, 'gate21.html'))
	$extra_info = array('is_private' => 1, 'parse_albums' => 1);
else if (endsWith($ref, 'gate_wall2.html'))
	$extra_info = array('is_private' => 0, 'parse_wall' => 1);
else
	$extra_info = array();

update_group_row($db, $gid, $extra_info);

// ---------
// 2. Создать gz-архив с дампом dump-$gid.txt.gz

create_dump($db, $gid);

echo 'done';
?>