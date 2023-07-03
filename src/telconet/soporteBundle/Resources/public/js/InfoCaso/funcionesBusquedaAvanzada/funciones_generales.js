var win;
var winSintomas;
var winHipotesis;
var winTareas;
var winAsignadoTarea;
var winVerAfectados;

function cierraVentanaByIden(winID){
    winID.close();
    winID.destroy();
} 

function eliminarSeleccion(datosSelect, band, sm)
{
	var boolViejo = false;	
	var totalDatos	= datosSelect.getSelectionModel().getCount();
	var totalSeleccionados = sm.getSelection().length;
	
    if(sm.getSelection().length > 0)
    {
		var estado = 0;
		for(var i=0 ;  i < sm.getSelection().length ; ++i)
		{
			if(band == "gridHipotesis")
			{
				if(sm.getSelection()[i].data.origen == "Nuevo")
				{
					datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
				}
				else
				{
					boolViejo = true;
				}
			}
			else if(band == "gridMaterialTareas")
			{
				if(sm.getSelection()[i].data.fin_origen == "Nuevo")
				{
					datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
				}
				else
				{
					boolViejo = true;
				}
			}
			else
			{
				datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
			}
		}  
		
		if(band == "gridHipotesis" && !boolViejo)
		{
			Ext.Msg.alert('Alerta ', "Uno de los registros de Hipotesis no es nuevo, no tiene privilegios para borrar este dato");
		}
		if(band == "gridMaterialTareas" && !boolViejo)
		{
			Ext.Msg.alert('Alerta ', "Los materiales de esta plantilla de Tarea no se puede elmininar, no tiene privilegios para borrar este dato");
		}
	}
	
	/*
	if(totalDatos > 0)
	{
		for(var i = 0; i < totalDatos; i++)
		{
			if(band == "gridHipotesis")
			{
				alert(' -- i =' +i +' -- hipotesis =' +datosSelect.getStore().getAt(i).nombre_hipotesis+ ' -- origen =' +datosSelect.getStore().getAt(i).data.origen);
				if(datosSelect.getStore().getAt(i).data.origen == "Nuevo")
				{
					alert('entro a nuevo borar');
					//datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
				}
				else
				{
					alert('false no entro');
					boolViejo = true;
				}
			}
			else
			{
				datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
			}
		}
		
		if(band == "gridHipotesis" && !boolViejo)
		{
			Ext.Msg.alert('Alerta ', "Uno de los registros de Hipotesis no es nuevo, no tiene privilegios para borrar este dato");
		}
	}*/
}

function exportarExcel(){
    window.open("exportarConsulta");
}