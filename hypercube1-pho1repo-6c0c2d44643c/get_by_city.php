<?php 
include('cfg.php');

function download($link)
{
	$ch = curl_init();  
	curl_setopt($ch, CURLOPT_URL, $link);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HEADER, 0);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	$output = curl_exec($ch);  
	curl_close($ch);
	return $output;
}

$id = $_GET['id'];
if (isset($_GET['offset']))
{
	$offset = $_GET['offset'];
	if (ctype_digit($offset) == false)
		die('invalid offset');
}
else
	$offset = 0;
$max = 15;

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
$db->set_charset("utf8"); # на всякий пожарный

$stmt = $db->prepare("SELECT city_id, city_title FROM cities WHERE city_id = ?");
$stmt->bind_param('d', $id);
$stmt->execute();
$stmt->bind_result($city_id, $city_title);
$stmt->fetch();
$stmt->close();

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" type="text/css" href="style.css" />
<title>Список юзеров в городе <?php echo $city_title ?></title>
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
<p>Из города &quot;<?php echo $city_title ?>&quot; найдены следующие юзеры:</p><?php
	
	#$stmt = $db->prepare("SELECT user_id, display_name, photo_100 FROM users WHERE city_id = ? AND user_id IN (SELECT DISTINCT user_id FROM main)");
	$stmt = $db->prepare("SELECT user_id, display_name, photo_200 FROM users WHERE city_id = ? AND sex = 1 LIMIT ?,?");
	$stmt->bind_param('ddd', $id, $offset, $max);
	$stmt->execute();
	$stmt->bind_result($user_id, $display_name, $ava);

	$cnt = 0;
	$offset1 = $offset+1;
	echo "<ol start='$offset1'>";
	while ($stmt->fetch()) {
		echo "<li><br>";
		echo "<a target='_blank' href='profile.php?id=$user_id'><img src='$ava' /><br>$display_name</a>";
		echo "</li>\n";
		$cnt++;
	}
	echo "</ol>";
	
	if ($cnt == 0)
		echo "<p>(пусто) :(</p>";
	else {
		$offset_prev = $offset - $max;
		$offset_next = $offset + $max;
		echo "<div class='upper_right_fixed style1'>";
		echo "<a href='get_by_city.php?id=$id&offset=$offset_next'>&gt;&gt;&gt;&gt; сюда </a><br>";
		
		if ($offset != 0)
			echo "<a href='get_by_city.php?id=$id&offset=$offset_prev'>&lt;&lt;&lt;&lt;&lt; туда</a>";
		else
			echo "&nbsp;";
		
		echo "</div>";
	}
		
	$stmt->close();
		
?></body></html>
