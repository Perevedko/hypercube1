<?php 
include('cfg.php');
include('parsers/parser_tools.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

$user_id = get_user_id_by_asshole_input($_GET['ids']);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" />
<meta name="robots" content="noindex, nofollow" />
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
<p>Результаты поиска</p><?php

	$stmt = $db->prepare("SELECT user_id, COUNT(*) FROM main WHERE user_id = ? GROUP BY user_id");
	$stmt->bind_param('d', $user_id);
	$stmt->execute();

	$stmt->bind_result($user_id, $pcnt);
	
	$cnt = 0;
	echo "<ol>";
	while ($stmt->fetch()) {
		printf ('<li><a target="_blank" href="profile.php?id=%d">%d</a> (%d)</li>' . "\n", $user_id, $user_id, $pcnt);
		$cnt++;
	}
	echo "</ol>";
	
	if ($cnt == 0)
		echo "<p>(пусто) :(</p>";
		
	$stmt->close();
		
?></body></html>
