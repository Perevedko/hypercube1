var gate_url = "gate21.php";
var gate_finish_url = "gate_finish.php";

// функция определяет набор inputs
function get_albums(gid_index)
{
	if (gid_index === undefined)
		gid_index = 0;
	
	if (gid_index >= gids.length)
	{
		console.log("done");
		get_photos();
		return;
	}
	
	var group_id = gids[gid_index];
	
	inputs.push({
		group_id: group_id,
		offset: 0
	});
	
	get_albums(gid_index+1);
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
	var offset = input.offset;
	
	my_block.innerHTML = "group_id=" + group_id + "<br>" + "offset=" + offset;
	
	// отправляем в нашу базу
	
	$.ajax({
		url: gate_url + "?" + "gid=" + group_id + "&offset=" + offset + "&access_token=" + access_token,
		success: function(response) {
			//alert(response.responseText);
			
			if ((response) == 'FINISH') {
				
				inputs.splice(0, 1);
				
				my_block.innerHTML = "group_id=" + group_id + "<br>" + "finishing...";
				
				// завершили обрабатывать аьбом
				$.ajax({
					url: gate_finish_url + "?" + "gid=" + group_id,
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
			}
			else if (response.substring(0, 8) == "CONTINUE") {
				inputs[0].offset = parseInt(response.substring(8));
				get_photos();
			}
			else {
				my_block.innerHTML = response;
				setTimeout(get_photos, 60000);
			}
			
			return;
		},
		error: function(xhr, textStatus, errorThrown){
			my_block.innerHTML += "<br>retrying...";
			setTimeout(get_photos, 60000);
		}
	});
	
}

function start()
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
	my_block.style.left = "0px";
	my_block.style.padding = "0px";
	my_block.style.border = "1px solid #fc0";
	my_block.style.zIndex = "100";
	my_block.innerHTML = '';

	var label_temp;
	label_temp = document.createElement('label');
	label_temp.innerHTML = "gids:";

	var input_box;
	input_box = document.createElement('input');
	input_box.id = "gids_box2";
	input_box.type = "text";
	input_box.size = "40";
	input_box.value = gids_value;
	
	gids_input_box = input_box;
	
	my_block.appendChild(label_temp);
	my_block.appendChild(input_box);
	my_block.appendChild(document.createElement('br'));
	
	var btn = document.createElement('button');
	btn.innerHTML = "Погнали!";
	btn.addEventListener("click", start);
	
	my_block.appendChild(btn);
	document.getElementsByTagName("body")[0].appendChild(my_block);
}

var body = document.getElementsByTagName('body')[0];
var gids = [37276776];
var inputs;

var gids_value = "37276776";

var my_block;
var gids_input_box;

createUI();

