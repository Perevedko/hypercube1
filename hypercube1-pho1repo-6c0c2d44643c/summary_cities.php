<?php 
include('cfg.php');

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
$db->set_charset("utf8");

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" type="text/css" href="style.css" />
<title>Список городов - pho1</title>
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

<h1>Список городов</h1>
<p>Данный раздел на стадии тестирования. Если есть, что сказать (предложения, ошибки и т.п.) - пишите <a target="_blank" href="http://2ch.hk/g/res/461132.html">тут</a>.<br>Ваш второй пхп-кун.</p>
<h2>Введи свой город</h2>
<form action="find_city_by_title.php">
<input type="text" name="city_title" />
<input type="submit" value="Найти"/>
</form>
<h2>Топ-50 городов</h2>
<p>Нажмите CTRL+F для того, чтобы найти свой город по быстрому (если он вообще есть тут)</p>
<table><tr>
<td>Город</td>
<td>Количество юзеров</td></tr>
<?php
	$result = $db->query("SELECT t.city_id, city_title, t.cnt FROM cities g RIGHT OUTER JOIN (SELECT city_id, COUNT(*) as cnt FROM users WHERE sex = 1 GROUP BY city_id HAVING city_id IS NOT NULL) t ON t.city_id = g.city_id ORDER BY t.cnt DESC LIMIT 50");
	echo $db->error;
	
	while ($row = $result->fetch_row()) {
		echo '<tr>';
		printf('<td><a href="get_by_city.php?id=%d">%s</a></td>', $row[0], $row[1]);
		printf('<td>%d</td>', $row[2]);
		echo "</tr>";
	}
?></table>

</body></html>
