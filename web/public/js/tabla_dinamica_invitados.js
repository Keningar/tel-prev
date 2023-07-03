function addRow() 
{
	var table = document.getElementById("tabla");
	
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);

	//variable para mi invitado a agregar
	var invitado=document.getElementById("crear_invitado").value;
	
	var cell1 = row.insertCell(0);
	var element1 = document.createElement("input");
	element1.type = "checkbox";
	cell1.appendChild(element1);
	
	/*
	var cell2 = row.insertCell(1);
	cell2.innerHTML = rowCount + 1;
	*/
	var cell3 = row.insertCell(1);
	var element2 = document.createElement("input");
	element2.type = "text";
	element2.id ="crear_invitado_t";
	element2.name ="crear[invitado_t][]";
	element2.value =invitado;
	cell3.appendChild(element2);
	
	//borro el contenido de la caja de texto
	document.getElementById("crear_invitado").value="";
 
}
 
function deleteRow() 
{
	try 
	{
		var table = document.getElementById("tabla");
		var rowCount = table.rows.length;

		for(var i=0; i<rowCount; i++) 
		{
			var row = table.rows[i];
			var chkbox = row.cells[0].childNodes[0];
			if(null != chkbox && true == chkbox.checked) 
			{
				table.deleteRow(i);
				rowCount--;
				i--;
			}

		}
	}
	catch(e) 
	{
		alert(e);
	}
}
