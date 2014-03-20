<?php 
include('cfg.php');
include('parsers/parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
$db->set_charset("utf8"); # на всякий пожарный

$id = get_user_id_by_asshole_input($_GET['id']);

/*
# -----------------------------
# сначала пропарсим группы
$contents = download("https://api.vk.com/method/users.getSubscriptions?v=5.10&user_id=$id&extended=1&count=200");
$contents = json_decode($contents, true);
$input = $contents['response']['items'];

for ($i=0; $i<count($input); $i++) {
	$group_id = $input[$i]['id'];
	$group_name = $input[$i]['name'];
	//$member_count = $input[$i]['members_count'];
	$stmt = $db->prepare("INSERT INTO groups_to_parse(group_id,group_name,group_mc) VALUES(?,?,0) ON DUPLICATE KEY UPDATE group_mc = group_mc + 1");
	echo $db->error;
	$stmt->bind_param('ds', $group_id, $group_name);
	echo $db->error;

	$stmt->execute();
	$stmt->close();
}
// */


# -----------------------------
$error_code = 0;
$error_msg = '';
$contents = download("https://api.vk.com/method/friends.get?user_id=$id");
$contents = json_decode($contents, true);
if (isset($contents['error'])) {
	$error_code = $contents['error']['error_code'];
	$error_msg = $contents['error']['error_msg'];
} else {
	$input = $contents['response'];
	$ids_str_q = implode(',', $input);
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" type="text/css" href="style.css" />
<title>pho1</title>
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
</head>
<body>
<?php if ($error_code != 0) { ?>
<p>Ошибка VK.API #<?php echo $error_code ?>: <?php echo $error_msg ?></p>
<?php } else if (count($input) == 0) { ?>
<p>У данного пользователя нет друзей или все друзья скрыты</p>
<?php } else { ?>
<p>Из <?php echo count($input) ?> друзей найдены следующие:</p><?php
	
	$stmt = $db->prepare("SELECT DISTINCT m.user_id, CASE WHEN u.display_name IS NULL THEN m.user_id ELSE u.display_name END FROM main m
LEFT JOIN users u ON (m.user_id = u.user_id) 
WHERE m.user_id IN ($ids_str_q) ORDER BY m.user_id");
	$stmt->execute();
	
	/* bind result variables */
	$stmt->bind_result($user_id, $display_name);
	
	/* fetch values */
	$cnt = 0;
	echo "<ol>";
	while ($stmt->fetch()) {
		printf ('<li><a target="_blank" href="profile.php?id=%d">%s</a></li>' . "\n", $user_id, $display_name);
		$cnt++;
	}
	echo "</ol>";
	
	if ($cnt == 0)
		echo "<p>(пусто) :(</p>";
		
	$stmt->close();
		
?><?php } ?></body></html>
