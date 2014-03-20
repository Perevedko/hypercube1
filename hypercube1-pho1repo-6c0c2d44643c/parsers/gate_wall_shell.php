<?php

include('parser_cfg.php');
include('parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

# выдергиваем группы из базы.
echo "fetching groups to parse\n";
$gids = get_groups_to_parse_walls($db, 0);
var_dump($gids);

# параметры для перебираемых групп
$extra_info = array('is_private' => 0, 'parse_wall' => 1);

foreach($gids as &$gid) {
	echo "gid=$gid\n";
	
	$offset = 0;
	while (($r = vk_get_photos_wall($db, $gid, $offset)) != -1) {
		$offset = $r;
		echo "gid=$gid\toffset=$offset\n";
	}
	
	echo "gid=$gid,finishing...\n";
	update_group_row($db, $gid, $extra_info);
	create_dump($db, $gid);
}

?>