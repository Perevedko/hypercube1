<?php 

include('cfg.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
$db->set_charset("utf8"); # на всякий пожарный

$id = $_GET['id'];

# берем фотки из базы
$stmt = $db->prepare("SELECT `pid`, `user_id`, `group_id`, `src`, `src_preview`, `src_thumb`, `wall_id` FROM main WHERE user_id = ?");
echo $db->error;
$stmt->bind_param('d', $id);
$stmt->execute();
$stmt->bind_result($photo_id, $user_id, $group_id, $src, $src_preview, $src_thumb, $wall_id);

$photos = array();
while ($stmt->fetch()) {
	
	$photo = array();
	$photo['photo_id'] = $photo_id;
	$photo['user_id'] = $user_id;
	$photo['group_id'] = $group_id;
	$photo['src'] = $src;
	$photo['src_preview'] = $src_preview;
	$photo['src_thumb'] = $src_thumb;
	
	if (is_null($wall_id))
		$link_suffix = 'photo-'.$group_id.'_'.$photo_id;
	else
		$link_suffix = 'wall-'.$group_id.'_'.$wall_id;
	$photo['link_suffix'] = $link_suffix;
	
	array_push($photos, $photo);
}

$stmt->close();

# берем общую инфу из базы
$stmt = $db->prepare("SELECT display_name, photo_200_orig, c.city_title, c.city_id FROM users u LEFT OUTER JOIN cities c ON c.city_id = u.city_id WHERE user_id = ?");
$stmt->bind_param('d', $id);
$stmt->execute();
$stmt->bind_result($display_name, $photo_200_orig, $city_title, $city_id);
$stmt->fetch();
$stmt->close();

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" />
<meta name="robots" content="noindex, nofollow" />
<title>Страница профиля</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
var photos = <?php echo json_encode($photos) ?>;
</script>
<script type="text/javascript" src="fancybox/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" href="fancybox/jquery.fancybox.css" type="text/css" media="screen" />
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-48208431-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script type="text/javascript">

function removeThis(pid) {
	var e = document.getElementById('pid' + pid);
	e.innerHTML = "<p>Ваш запрос добавлен в базу. Спасибо, что сохраняете интернет чистым :)</p>"
	
	$.ajax({
		url: "sazha.php?pid=" + pid,
	});
}

$(document).ready(function() {

	$("a[rel=group]").fancybox({
		beforeShow : function() {
			this.title = '<a class="white_text" target="_blank" href="' + photos[this.index].src + '">Прямая ссылка</a> || <a class="white_text" target="_blank" href="http://vk.com/' + photos[this.index].link_suffix + '">Нормальная ссылка</a> || <a class="white_text" href="sazha.php?pid=' + photos[this.index].photo_id + '" onClick="removeThis('+ photos[this.index].photo_id + '); return false;">Это сажа</a>';
			//this.title = (this.title ? '' + this.title + '' : '') + 'Image ' + (this.index + 1) + ' of ' + this.group.length + '. You can find the whole gallery  <a href="/parth/to/gallery">here</a>';
		}
	});

});



</script>
</head>
<body><?php
if (count($photos) == 0)
	echo "<p>Пользователя с таким ид нету в базе</p>";
else {
	echo "<a href='https://vk.com/id$user_id'><h1>$display_name</h1></a>\n";
	echo "<a href='get_by_city.php?id=$city_id'><p>$city_title</p></a>\n";
	echo "<div class='upper_right'><img src='$photo_200_orig'></div>\n";
	foreach($photos as &$photo) {
		echo '<div id="pid'.$photo['photo_id'].'">';
		echo '<a rel="group" href="'.$photo['src_preview'].'"><img src="'.$photo['src_thumb'].'" /></a>';
		echo "</div>\n";
	}
}
?><div class="lower_right_fixed">ВНИМАНИЕ!<br>Если Вы нашли фото, в котором не изображена девушка из профиля, то просьба - нажмите на кнопку &quot;Это сажа&quot;<br>Этим действием Вы помогаете админу фильтровать мусор и предоставлять более качественный контент.<br>Спасибо, что прочитали. Ваш второй пхп-кун</div></body></html>
