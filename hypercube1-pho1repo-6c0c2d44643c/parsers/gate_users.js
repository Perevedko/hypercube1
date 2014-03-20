var gate_url = "gate_users.php";
var offset = 0;

// функция по inputs уже находит фотки
function next_offset()
{
	my_block.innerHTML = "offset=" + offset;
	
	// отправляем в нашу базу
	
	$.ajax({
		url: gate_url + "?" + "offset=" + offset,
		success: function(response) {
			//alert(response);
			
			if (response == 'FINISH') {
				alert('done');
			}
			else if (response.substring(0, 8) == "CONTINUE") {
				offset = parseInt(response.substring(8));
				next_offset();
			}
			else {
				my_block.innerHTML = response;
				setTimeout(next_offset, 60000);
			}
			
			return;
		},
		error: function(xhr, textStatus, errorThrown){
			my_block.innerHTML += "<br>retrying...";
			setTimeout(next_offset, 60000);
		}
	});
	
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

	var btn = document.createElement('button');
	btn.innerHTML = "Погнали!";
	btn.addEventListener("click", next_offset);
	
	my_block.appendChild(btn);
	document.getElementsByTagName("body")[0].appendChild(my_block);
}

var body = document.getElementsByTagName('body')[0];
var my_block;

createUI();

