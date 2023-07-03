function ejecutar() 
{ 
	//alert("llego");
	var visita_sele; 
	visita_sele = document.getElementById('crear_idEstado').value; 
	//alert(visita_sele);
	if (visita_sele=='3')
	{
		document.getElementById('motivo_bn').style.display='none'; 
		document.getElementById('motivo_ll').removeAttribute('style'); 
	}
	else
	{
		document.getElementById('motivo_bn').style.display='block'; 
		document.getElementById('motivo_ll').style.display='none'; 
	}
}
