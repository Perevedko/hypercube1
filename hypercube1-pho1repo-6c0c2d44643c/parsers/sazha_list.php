<?php 

include('parser_cfg.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
$db->set_charset("utf8"); # на всякий пожарный

# берем фотки из базы
$stmt = $db->prepare("SELECT * FROM main WHERE pid IN (SELECT DISTINCT photo_id FROM sazha1) AND album_id <> 0");
$stmt->execute();
$stmt->bind_result($photo_id, $user_id, $group_id, $album_id, $src, $src_preview, $src_thumb, $wall_id);

$photos = array();
while ($stmt->fetch()) {
	
	$photo = array();
	$photo['photo_id'] = $photo_id;
	$photo['user_id'] = $user_id;
	$photo['group_id'] = $group_id;
	$photo['src'] = $src;
	$photo['src_preview'] = $src_preview;
	$photo['src_thumb'] = $src_thumb;
	$photo['album_id'] = $album_id;
	
	if (is_null($wall_id))
		$link_suffix = 'photo-'.$group_id.'_'.$photo_id;
	else
		$link_suffix = 'wall-'.$group_id.'_'.$wall_id;
	$photo['link_suffix'] = $link_suffix;
	
	array_push($photos, $photo);
}

$stmt->close();

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../style.css" />
<meta name="robots" content="noindex, nofollow" />
<title>Страница профиля</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">

function removeAlbum(pid, aid) {
	var e = document.getElementById('pid' + pid);
	e.parentNode.removeChild(e);
	
	if (aid == 0)
		return;
	
	$.ajax({
		url: "sazha_remove_album.php?aid=" + aid,
	});
	
	$.ajax({
		url: "sazha_remove_from_list.php?pid=" + pid,
	});
	
	return false;
}

function notSazha(pid) {
	var e = document.getElementById('pid' + pid);
	e.parentNode.removeChild(e);
	
	$.ajax({
		url: "sazha_remove_from_list.php?pid=" + pid,
	});
	
	return false;
}

</script>
</head>
<body><?php
if (count($photos) == 0)
	echo "<p>Походу сажи нет</p>";
else {
	foreach($photos as &$photo) {
		echo '<div id="pid'.$photo['photo_id'].'">';
		echo '<img src="'.$photo['src_thumb'].'" /><br>';
		echo '<a targer="_blank" href="http://vk.com/'.$photo['link_suffix'].'">Нормальная ссылка</a><br>';
		echo '<p>pid:'.$photo['photo_id'].'<br>aid:'.$photo['album_id'].'</p>';
		echo '<a href="sazha_remove_from_list.php?pid='.$photo['photo_id'].'" onClick="removeAlbum('.$photo['photo_id'].','.$photo['album_id'].');">Альбом - сажа</a><br>';
		echo '<a href="sazha_remove_from_list.php?pid='.$photo['photo_id'].'" onClick="notSazha('.$photo['photo_id'].');">НЕ сажа</a><hr>';
		echo "</div>\n";
	}
}
?></body></html>
