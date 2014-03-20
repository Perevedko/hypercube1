<?php 
# ToDo: chmod 777 dumps + create dumps
# ToDo: .htaccess na parsere
# ToDo: попросить пользователя запускать apache от текущего пользователя
include('cfg.php');
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Установка</title>
</head>
<body>
<?php

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password);

$create_table_queries = array('CREATE DATABASE ' . $mysql_dbname,
'USE ' . $mysql_dbname,
'
CREATE TABLE main
(
	pid INT NOT NULL,
	user_id INT NOT NULL,
	group_id INT NOT NULL,
	album_id INT NOT NULL,
	src VARCHAR(256) NOT NULL,
	src_preview VARCHAR(256) NOT NULL,
	src_thumb VARCHAR(256) NOT NULL,
	wall_id INT NULL,
	PRIMARY KEY(pid),
	KEY `user_id` (`user_id`),
	KEY `group_id` (`group_id`)
)','
CREATE TABLE groups
(
	group_id INT NOT NULL,
	group_screen_name VARCHAR(256) NOT NULL,
	last_update DATETIME NOT NULL,
	is_private INT(1) NOT NULL,
	parse_albums INT(1) NOT NULL,
	parse_wall INT(1) NOT NULL,
	PRIMARY KEY(group_id)
)','
CREATE TABLE cities
(
	city_id INT NOT NULL,
	city_title VARCHAR(256) NOT NULL,
	PRIMARY KEY(city_id)
)','
CREATE TABLE users(
	user_id INT NOT NULL,
	display_name VARCHAR(256) NOT NULL,
	sex INT(1) NULL,
	photo_50 VARCHAR(256) NOT NULL,
	photo_100 VARCHAR(256) NOT NULL,
	photo_200 VARCHAR(256) NULL,
	photo_200_orig VARCHAR(256) NULL,
	city_id INT NULL,
	cache_has_photos INT(1) NOT NULL,
	PRIMARY KEY (user_id),
	KEY `city_id` (`city_id`),
)','
CREATE TABLE groups_to_parse(
	group_id INT NOT NULL,
	group_name VARCHAR(256) NOT NULL,
	group_mc INT NOT NULL,
	PRIMARY KEY (group_id)
)','
CREATE TABLE sazha1(
	photo_id INT NOT NULL,
	longip INT NOT NULL,
	PRIMARY KEY (photo_id, longip)
)','
CREATE TABLE bad_albums(
	album_id INT NOT NULL,
	PRIMARY KEY (album_id)
)'
);
?>

<pre><?php

if (isset($i_forgot_to_config))
{
	echo "Кажется, Вы забыли настроить cfg.php";
} else {
	for ($i=0; $i<count($create_table_queries); $i++) {
		$query = $create_table_queries[$i];
		$result = $db->query($query);
		
		if ($result === FALSE)
		{
			echo 'Error: ' . $db->error . "\n";
			echo 'Query: ' . $query . "\n";
			echo "---\n";
		}
	}
	echo "Если кроме этого предложения Вы ничего не видите, то установка произошла успешно";
}
?></pre>
</body></html>