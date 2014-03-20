<?php 
include('cfg.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" type="text/css" href="style.css" />
<title>Пропарсенные группы - pho1</title>
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

<h1>Пропарсеные группы</h1>
<table><tr>
<td>Ссылка</td>
<td>Количество фоток</td>
<td>Дамп (txt.gz)</td>
<td>Последнее обновление (YEKT,MSK+2)</td></tr>
<?php
	$result = $db->query("SELECT t.group_id, CASE WHEN g.group_screen_name IS NULL THEN CONCAT('club',t.group_id) ELSE  g.group_screen_name END as name, t.cnt, g.last_update FROM groups g RIGHT OUTER JOIN (SELECT group_id, COUNT(*) as cnt FROM main GROUP BY group_id) t ON t.group_id = g.group_id ORDER BY name");
	echo $db->error;
	
	
	while ($row = $result->fetch_row()) {
		echo '<tr>';
		printf('<td><a target="_blank" href="https://vk.com/%s">%s</a></td>', $row[1], $row[1]);
		printf('<td>%d</td>', $row[2]);
		printf('<td><a href="dumps/dump-%d.txt.gz">скачать</a></td>', $row[0]);
		printf('<td>%s</td>', $row[3]);
		echo "</tr>";
	}
?></table>

</body></html>