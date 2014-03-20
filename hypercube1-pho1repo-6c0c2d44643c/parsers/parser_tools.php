<?php

function get_user_id_by_asshole_input($in) {
	if ($in == '')
		return '0';
	
	$pos = strrpos($in, '/');
	$pos = ($pos === FALSE ? 0 : $pos + 1);
	$user_id = substr($in, $pos);
	
	# теперь вытаксиваем ид
	if ( (substr($user_id, 0, 2) == 'id') && (ctype_digit(substr($user_id, 2)) == true) )
		$user_id = substr($user_id, 2);
	else if ( ($pos != 0) || (ctype_digit($user_id) == false) ) {
		$contents = download("https://api.vk.com/method/users.get?user_ids=$user_id&v=5.14");
		$contents = json_decode($contents, true);
		if (isset($contents['response']))
			$user_id = $contents['response'][0]['id'];
		else
			$user_id = '0';
	}
	
	return $user_id;
}

function vk_get_photos($db, $gid, $aid, $offset) {
	$contents = download('https://api.vk.com/method/photos.get?v=3.0&count=1000&owner_id=-'.$gid.'&album_id='.$aid.'&offset='.$offset);
	$contents = json_decode($contents, true);
	$input = $contents['response'];
	
	if (count($input) == 0)
		return -1;
	
	for ($i=0; $i<count($input); $i++)
	{
		$pid = $input[$i]['pid'];
		$user_id = $input[$i]['user_id'];
		
		if ($user_id == 100)
			continue;
		
		$group_id = $gid;
		$src = $input[$i]['src_big'];
		if (isset($input[$i]['src_xbig']))
			$src = $input[$i]['src_xbig'];
		if (isset($input[$i]['src_xxbig']))
			$src = $input[$i]['src_xxbig'];
			
		$src_preview = $input[$i]['src_big'];
		$src_thumb = $input[$i]['src'];
		
		$stmt = $db->prepare("INSERT INTO main (pid,user_id,group_id,album_id,src,src_preview,src_thumb) VALUES(?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE album_id = VALUES(album_id)");
		echo $db->error;
		$stmt->bind_param('ddddsss', $pid, $user_id, $group_id, $aid, $src, $src_preview, $src_thumb);

		$stmt->execute();
		$stmt->close();
	}
	
	$offset += count($input);
	
	return $offset;
}

function get_bad_albums($db) {
	$bad_albums = array();
	$stmt = $db->prepare("SELECT album_id FROM bad_albums");
	$stmt->execute();
	$stmt->bind_result($album_id);
	while ($stmt->fetch()) {
		array_push($bad_albums, $album_id);
	}
	$stmt->close();
	return $bad_albums;
}
function vk_get_albums($db, $gid) {
	$contents = download('https://api.vk.com/method/photos.getAlbums?owner_id=-'.$gid);
	$contents = json_decode($contents, true);
	//var_dump($contents);
	$response = $contents['response'];

	$bad_albums = get_bad_albums($db);

	//var_dump($contents);

	$result = array();
	foreach($response as &$album) {
		if (in_array($album['aid'], $bad_albums) == false)
			array_push($result, $album['aid']);
	}
	
	return $result;
}

function create_dump($db, $gid) {
	$content = '';
	$dumpname = 'dump-'.$gid.'.txt.gz';

	$stmt = $db->prepare("SELECT CONCAT('id',user_id), CASE WHEN wall_id IS NULL THEN CONCAT('photo-',group_id,'_',pid) ELSE CONCAT('wall-',group_id,'_',wall_id) END, src FROM main WHERE group_id = ?");
	$stmt->bind_param('d', $gid );
	$stmt->execute();
	$stmt->bind_result($user_id, $link1, $link2);
	while ($stmt->fetch()) {
		$content .= sprintf ('%s %s %s' . "\r\n", $user_id, $link1, $link2);
	}
	$stmt->close();

	$f = gzopen ( "../dumps/$dumpname", 'wb9' );
	gzwrite ( $f, $content);
	gzclose ( $f );
}

function update_group_row($db, $gid, $extra_info = array()) {
	$datetime = date("Y-m-d H:i:s");  
	
	$stmt = $db->prepare("SELECT * FROM groups WHERE group_id = ?");
	$stmt->bind_param('d', $gid);
	$stmt->execute();
	$stmt->store_result();
	$rows = $stmt->num_rows;
	$stmt->close();

	if ($rows == 0)
	{
		// видимо еще нет
		
		$contents = download('https://api.vk.com/method/groups.getById?group_id='.$gid);
		$contents = json_decode($contents, true);
		if (isset($contents['response'][0]['screen_name']))
			$group_screen_name = $contents['response'][0]['screen_name'];
		else
			$group_screen_name = 'club'.$gid;
		
		$stmt = $db->prepare("INSERT INTO groups (group_id,group_screen_name,last_update,is_private,parse_albums,parse_wall) VALUES(?,?,?,0,0,0)");
		$stmt->bind_param('dss', $gid, $group_screen_name, $datetime);

		$stmt->execute();
		$stmt->close();
	} else {
		$stmt = $db->prepare("UPDATE groups SET last_update = ? WHERE group_id = ?");
		$stmt->bind_param('sd', $datetime, $gid);

		$stmt->execute();
		$stmt->close();
	}
	
	# теперь добавл€ем extra_info
	if (count($extra_info) != 0) {
		# какие параматры мен€ть
		$extra_info_keys = array_keys($extra_info);
		
		# собственно сами данные. ѕричем дл€ bind_param
		$data = array_values($extra_info);
		array_push($data, $gid);
		array_unshift($data, str_repeat('d', count($data)));
		
		# собственно делаем запрос
		$stmt = $db->prepare("UPDATE groups SET " . implode(' = ?, ', $extra_info_keys) . " = ? WHERE group_id = ?");
		echo $db->error;
		$r = call_user_func_array(array($stmt, 'bind_param'), refValues($data));
		echo $db->error;
		$stmt->execute();
		echo $db->error;
		$stmt->close();
	}
}

function vk_get_photos_wall($db, $gid, $offset)
{
	$contents = download('https://api.vk.com/method/wall.get?count=200&owner_id=-'.$gid.'&offset='.$offset);
	$contents = json_decode($contents, true);
	array_shift($contents['response']);
	

	$input = $contents['response'];

	if (count($input) == 0)
		return -1;

	for ($i=0; $i<count($input); $i++)
	{
	if (isset($input[$i]['signer_id']) == false)
			continue;
		
		if (isset($input[$i]['attachments']) == false)
			continue;
		
		$wall_id = $input[$i]['id'];
		$user_id = $input[$i]['signer_id'];
		$attachments = $input[$i]['attachments'];
		
		for ($j=0; $j<count($attachments); $j++)
		{
			if ($attachments[$j]['type'] != 'photo')
				continue;
			
			$photo = $attachments[$j]['photo'];
			
			$pid = $photo['pid'];
			$src = $photo['src_big'];
			if (isset($photo['src_xbig']))
				$src = $photo['src_xbig'];
			if (isset($photo['src_xxbig']))
				$src = $photo['src_xxbig'];
			if (isset($photo['src_xxxbig']))
				$src = $photo['src_xxxbig'];
			
			$src_preview = $photo['src_big'];
			$src_thumb = $photo['src'];
			
			$stmt = $db->prepare("INSERT IGNORE INTO main (pid,user_id,group_id,album_id,src,src_preview,src_thumb,wall_id) VALUES(?,?,?,0,?,?,?,?)");
			echo $db->error;
			$stmt->bind_param('dddsssd', $pid, $user_id, $gid, $src, $src_preview, $src_thumb, $wall_id);
			echo $db->error;
			$stmt->execute();
			echo $db->error;
			$stmt->close();
		}
	}
	return $offset+count($input);
}

function vk_get_photos_private($db, $gid, $offset, $access_token, $bad_albums) {
	$contents = download('https://api.vk.com/method/photos.getAll?v=3.0&count=200&no_service_albums=1&owner_id=-'.$gid.'&offset='.$offset.'&access_token='.$access_token);
	$contents = json_decode($contents, true);
	array_shift($contents['response']);

	$input = $contents['response'];

	if (count($input) == 0)
		return -1;

	for ($i=0; $i<count($input); $i++)
	{
		$pid = $input[$i]['pid'];
		$aid = $input[$i]['aid'];
		$user_id = $input[$i]['user_id'];
		
		if ($user_id == 100)
			continue;
			
		if (in_array($aid, $bad_albums))
			continue;
		
		$group_id = $gid;
		$src = $input[$i]['src_big'];
		if (isset($input[$i]['src_xbig']))
			$src = $input[$i]['src_xbig'];
		if (isset($input[$i]['src_xxbig']))
			$src = $input[$i]['src_xxbig'];
			
		$src_preview = $input[$i]['src_big'];
		$src_thumb = $input[$i]['src'];
		
		$stmt = $db->prepare("INSERT INTO main (pid,user_id,group_id,album_id,src,src_preview,src_thumb) VALUES(?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE album_id = VALUES(album_id)");
		echo $db->error;
		$stmt->bind_param('ddddsss', $pid, $user_id, $group_id, $aid, $src, $src_preview, $src_thumb);

		$stmt->execute();
		$stmt->close();
	}

	return $offset+count($input);
}

function get_city_ids($db) {
	// кэшируем сити_ид
	$city_ids = array();
	$stmt = $db->prepare("SELECT city_id FROM cities");
	$stmt->execute();
	$stmt->bind_result($city_id);
	while ($stmt->fetch()) 
		array_push($city_ids, $city_id);
	$stmt->close();
	
	return $city_ids;
}

function get_user_ids_str($db, $offset, $max) {
	$stmt = $db->prepare("SELECT DISTINCT user_id FROM main LIMIT ".$offset.','.$max);
	$stmt->execute();
	$stmt->bind_result($user_id);
	$user_ids = '';
	while ($stmt->fetch()) {
		$user_ids .= $user_id . ',';
	}
	rtrim($user_ids, ",");
	$stmt->close();
	
	return $user_ids;
}

function get_groups_to_parse_albums($db, $private_groups) {
	$stmt = $db->prepare("SELECT group_id FROM groups WHERE is_private = ? AND parse_albums = 1");
	echo $db->error;
	$stmt->bind_param('d', $private_groups);
	
	$stmt->execute();
	$stmt->bind_result($group_id);
	$gids = array();
	while ($stmt->fetch()) {
		array_push($gids, $group_id);
	}
	$stmt->close();
	
	return $gids;
}

function get_groups_to_parse_walls($db, $private_groups) {
	$stmt = $db->prepare("SELECT group_id FROM groups WHERE is_private = ? AND parse_wall = 1");
	echo $db->error;
	$stmt->bind_param('d', $private_groups);
	
	$stmt->execute();
	$stmt->bind_result($group_id);
	$gids = array();
	while ($stmt->fetch()) {
		array_push($gids, $group_id);
	}
	$stmt->close();
	
	return $gids;
}

function download($link)
{
	$output = '';
	while ($output == '')
	{
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, $link);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4'));
		$output = curl_exec($ch);  
		
		
		if ($output == '')
			echo curl_error($ch) . "\n";
		
		curl_close($ch);
	}
	return $output;
}

function refValues($arr){
	if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
	{
		$refs = array();
		foreach($arr as $key => $value)
			$refs[$key] = &$arr[$key];
		return $refs;
	}
	return $arr;
}

?>