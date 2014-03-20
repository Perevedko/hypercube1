var gate_url = "gate2.php";
var gate_finish_url = "gate_finish.php";
var gate_get_albums = "get_albums.php";

function generate_url(link)
{
	//if (use_pepachino == true)
		//return 'http://pepachino.com/browse.php?b=12&u=' + encodeURIComponent(link);
	
	return link;
}

// функция определяет набор inputs
function get_albums(gid_index)
{
	if (gid_index === undefined)
		gid_index = 0;
	
	if (gid_index >= gids.length)
	{
		get_photos();
		return;
	}
	
	var group_id = gids[gid_index];
	
	$.ajax({
		url: gate_get_albums + "?gid=" + group_id,
		success: function( response ) {
			var album_ids = [];
			
			var json = JSON.parse(response).response;
			for (var i=0; i<json.length; i++)
				album_ids.push(json[i]);
			
			inputs.push({
				group_id: group_id,
				album_ids: album_ids
			});
			
			get_albums(gid_index+1);
		},
		error: function(xhr, textStatus, errorThrown){
			my_block.innerHTML += "<br>retrying...";
			setTimeout(get_albums, 60000);
		}
	});
}

// функция по inputs уже находит фотки
function get_photos()
{
	if (inputs.length == 0)
	{
		alert('done');
		return;
	}
	
	var input = inputs[0];
	var group_id = input.group_id;
	
	if (input.album_ids.length == 0)
	{
		my_block.innerHTML = "group_id=" + group_id + "<br>" + "finishing...";
		
		// завершили обрабатывать аьбом
		$.ajax({
			url: generate_url(gate_finish_url + "?" + "gid=" + group_id),
			success: function( response ) {
			
				// удаляем первый элемент inputs
				inputs.splice(0, 1);
				
				get_photos();
			},
			error: function(xhr, textStatus, errorThrown){
				my_block.innerHTML += "<br>retrying...";
				setTimeout(get_photos, 60000);
			}
		});
		
		return;
	}
	
	
	var album_id = input.album_ids[0];
	
	my_block.innerHTML = "group_id=" + group_id + "<br>" + "album_id=" + album_id + "<br>" + "album_left_to_parse=" + input.album_ids.length;
	
	$.ajax({
		url: generate_url(gate_url + "?" + "gid=" + group_id + "&aid=" + album_id),
		success: function( response ) {
			//alert(response);
			// удаляем албум_id. дальше не рассматриваем
			inputs[0].album_ids.splice(0, 1);
			get_photos();
		},
		error: function(xhr, textStatus, errorThrown){
			my_block.innerHTML += "<br>retrying...";
			setTimeout(get_photos, 60000);
		}
	});
}

function gate2_start()
{
	my_block.innerHTML = "Загрузка...";
	
	gids = JSON.parse('[' + gids_input_box.value + ']');
	
	inputs = [];
	
	get_albums();
}

// создаем div-элемент
function createUI()
{
	my_block = document.createElement('div');
	my_block.style.position = "fixed";
	my_block.style.bottom = "0px";
	my_block.style.right = "0px";
	my_block.style.padding = "0px";
	my_block.style.border = "1px solid #fc0";
	my_block.style.zIndex = "100";
	my_block.innerHTML = '';

	var label_temp;
	label_temp = document.createElement('label');
	label_temp.innerHTML = "gids:";

	var input_box;
	input_box = document.createElement('input');
	input_box.id = "gids_box";
	input_box.type = "text";
	input_box.size = "40";
	input_box.value = gids_value;
	
	gids_input_box = input_box;
	
	my_block.appendChild(label_temp);
	my_block.appendChild(input_box);
	my_block.appendChild(document.createElement('br'));
	
	var btn = document.createElement('button');
	btn.innerHTML = "Погнали!";
	btn.addEventListener("click", gate2_start);
	
	my_block.appendChild(btn);
	body.appendChild(my_block);
}

var body = document.getElementById('body_id');
var gids = [28627911];
var inputs;

var gids_value = "28627911";

var my_block;
var gids_input_box;

createUI();
