<?php

include('parser_cfg.php');
include('parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

# ����������� ������ �� ����.
echo "fetching groups to parse\n";
$gids = get_groups_to_parse_albums($db, 1);
var_dump($gids);

# ��������� ��� ������������ �����
$extra_info = array('is_private' => 1, 'parse_albums' => 1);

$access_token = file_get_contents("access_token.txt");
$bad_albums = get_bad_albums($db);

foreach($gids as &$gid) {
	echo "gid=$gid\n";
	
	$offset = 0;
	while (($r = vk_get_photos_private($db, $gid, $offset, $access_token, $bad_albums)) != -1) {
		$offset = $r;
		echo "gid=$gid\toffset=$offset\n";
	}
	
	echo "gid=$gid,finishing...\n";
	update_group_row($db, $gid, $extra_info);
	create_dump($db, $gid);
}

?>