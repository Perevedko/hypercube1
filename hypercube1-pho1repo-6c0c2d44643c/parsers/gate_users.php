<?php

include('parser_cfg.php');
include('parser_tools.php');

function insert($db, $row, $mask) {
	# составляем план "А"
	$questions1 = str_repeat("?,", count($row)-1) . "?"; # для основного запроса
	$columnNames1 = implode(',', array_keys($row));
	$params = array_values($row);
	array_unshift($params, $mask);
	$query = "INSERT INTO users($columnNames1) VALUES($questions1)\n";
	
	# создаем план "Б"
	unset($row['user_id']);
	$row_keys = array_keys($row);
	
	foreach($row_keys as &$row_key)
		$row_key = "$row_key = VALUES($row_key)";

	$row_keys = implode(', ', $row_keys);
	$query .= "ON DUPLICATE KEY UPDATE $row_keys";
	
	//echo $query . "<br>";
	//var_dump($params);
	# выполняем запрос
	$stmt = $db->prepare($query);
	echo $db->error;
	call_user_func_array(array($stmt, 'bind_param'), refValues($params));
	$stmt->execute();
	echo $stmt->error;
	$stmt->close();
}

$db = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
$db->set_charset("utf8"); # на всякий пожарный

if (isset($_GET['offset']))
{
	$offset = $_GET['offset'];
	if (ctype_digit($offset) == false)
		die('invalid offset');
}
else
	$offset = 0;

$max = 300;

$city_ids = get_city_ids($db);
$user_ids = get_user_ids_str($db, $offset, $max);
if ($user_ids == '')
	die('FINISH');

$fields = 'city,sex,photo_50,photo_100,photo_200,photo_200_orig';
$contents = download('https://api.vk.com/method/users.get?v=5.10&user_ids='.$user_ids.'&fields='.$fields);
$contents = json_decode($contents, true);
$input = $contents['response'];

if (count($input) == 0)
	die("FCUK");

for ($i=0; $i<count($input); $i++)
{
	$row = array();
	$row['user_id']			= $input[$i]['id'];
	$row['display_name']	= $input[$i]['first_name'].' '.$input[$i]['last_name'];
	$row['sex']				= $input[$i]['sex']; // 1 - ж, 2 - м
	$row['photo_50']		= $input[$i]['photo_50'];
	$row['photo_100']		= $input[$i]['photo_100'];
	$row['photo_200']		= (isset($input[$i]['photo_200']) ? $input[$i]['photo_200'] : NULL);
	$row['photo_200_orig']	= (isset($input[$i]['photo_200_orig']) ? $input[$i]['photo_200_orig'] : NULL);
	
	// особая обработка города
	if (isset($input[$i]['city']))
	{
		$city_id = $input[$i]['city']['id'];
		$city_title = $input[$i]['city']['title'];
		
		// несем в справочник
		if (in_array($city_id, $city_ids) == false)
		{
			$stmt = $db->prepare("INSERT INTO cities(city_id,city_title) VALUES(?,?)");
			$stmt->bind_param('ds', $city_id, $city_title);
			
			$stmt->execute();
			$stmt->close();
			
			array_push($city_ids, $city_id);
		}
	} else {
		$city_id = NULL;
	}
	$row['city_id'] = $city_id;
	
	// составляем запрос, блияд
	insert($db, $row, 'dsdssssd');
}

printf("CONTINUE%d", $offset+count($input));
?>
