var win;
var winSintomas;
var winHipotesis;
var winTareas;
var winAsignadoTarea;

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
				if(typeof sm.getSelection()[i].data.criterios_sintoma == 'undefined'){				
				    datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
				}else{
				   //gridSintomas.down('#removeButton').setDisabled(selections.length == 0);
				  //alert('no borrar');
				     Ext.Msg.alert('Alerta ', "No puede eliminar sintomas existentes");
				}
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

function validador(e,tipo) {      
        
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
       
    //console.log(key);
    
    if(tipo=='numeros'){    
      letras = "0123456789";
      especiales = [8, 37,36];
    }else if(tipo=='letras'){
      letras = "abcdefghijklmnopqrstuvwxyz";
      especiales = [8, 37, 32,46,36,38,40,44];
    }
    else{ 
      letras = "abcdefghijklmnopqrstuvwxyz0123456789";
      especiales = [8, 37, 32,46,36,38,40,44,45];
    }
    
    //46 => .    
    //32 => ' ' espacio
    //8 => backspace       
    //37 => direccional izq
    //39 => direccional der y '
    //44 => ,

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }
                

    if(letras.indexOf(tecla) == -1 && !tecla_especial)   
        return false;
}