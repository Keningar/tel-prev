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
}

function exportarExcel(empleadoAsignacion){

    if (Ext.isEmpty(Ext.getCmp('txtNumero').value)) {

        if (Ext.isEmpty(Ext.getCmp('feAperturaDesde').value) &&
            Ext.isEmpty(Ext.getCmp('feAperturaHasta').value) &&
            Ext.isEmpty(Ext.getCmp('feCierreDesde').value)   &&
            Ext.isEmpty(Ext.getCmp('feCierreHasta').value)) {
            Ext.Msg.alert('Alerta ', "Por favor elegir un rango de fechas sea por <b>Apertura</b> o <b>Cierre</b><br/>"+
                                     "y <b>no mayor a 30 días</b>.");
            return;
        }

        if ((Ext.isEmpty(Ext.getCmp('feAperturaDesde').value) && !Ext.isEmpty(Ext.getCmp('feAperturaHasta').value)) ||
            (!Ext.isEmpty(Ext.getCmp('feAperturaDesde').value) && Ext.isEmpty(Ext.getCmp('feAperturaHasta').value))) {
            Ext.Msg.alert('Alerta ', "Por favor completar el rango de <b>Fecha Apertura</b>.");
            return;
        }

        if ((Ext.isEmpty(Ext.getCmp('feCierreDesde').value) && !Ext.isEmpty(Ext.getCmp('feCierreHasta').value)) ||
            (!Ext.isEmpty(Ext.getCmp('feCierreDesde').value) && Ext.isEmpty(Ext.getCmp('feCierreHasta').value))) {
            Ext.Msg.alert('Alerta ', "Por favor completar el rango de <b>Fecha Cierre</b>.");
            return;
        }

        if (!Ext.isEmpty(Ext.getCmp('feAperturaDesde').value) && !Ext.isEmpty(Ext.getCmp('feAperturaHasta').value)) {

            if (new Date(Ext.getCmp('feAperturaHasta').value) < new Date(Ext.getCmp('feAperturaDesde').value) ) {
                Ext.Msg.alert('Alerta ', "La Fecha Apertura <b>Hasta</b> no puede ser menor a la Fecha Apertura <b>Desde</b>.");
                return;
            }

            if (getDiferenciaTiempo(Ext.getCmp('feAperturaDesde').value , Ext.getCmp('feAperturaHasta').value ) > 31) {
                Ext.Msg.alert('Alerta ', "La <b>Fecha Apertura</b> supera un rango mayor a 30 días.");
                return;
            }
        }

        if (!Ext.isEmpty(Ext.getCmp('feCierreDesde').value) && !Ext.isEmpty(Ext.getCmp('feCierreHasta').value)) {

            if (new Date(Ext.getCmp('feCierreHasta').value) < new Date(Ext.getCmp('feCierreDesde').value) ) {
                Ext.Msg.alert('Alerta ', "La Fecha Cierre <b>Hasta</b> no puede ser menor a la Fecha Cierre <b>Desde</b>.");
                return;
            }

            if (getDiferenciaTiempo(Ext.getCmp('feCierreDesde').value , Ext.getCmp('feCierreHasta').value ) > 31) {
                Ext.Msg.alert('Alerta ', "La <b>Fecha Cierre</b> supera un rango mayor a 30 días.");
                return;
            }
        }
    }

    if(isNaN(comboTipoCaso.getValue())) {
        comboTipoCaso.setValue('');
    }

    if(isNaN(comboHipotesis_index.getValue())) {
        comboHipotesis_index.setValue('');
    }

    if(isNaN(comboNivelCriticidad.getValue())) {
        comboNivelCriticidad.setValue('');
    }

    if(isNaN(Ext.getCmp('comboDepartamento').getValue())) {
        Ext.getCmp('comboDepartamento').value('');
    }

    if(isNaN(Ext.getCmp('comboCiudad').getValue())) {
        Ext.getCmp('comboCiudad').value('');
    }

    if(isNaN(comboEmpleados1.getValue())) {
        comboEmpleados1.setValue('');
    }

    if(isNaN(comboEmpleados2.getValue())) {
        comboEmpleados2.setValue('');
    }

    empresa  = Ext.getCmp('sltEmpresaCaso').value;
    estado   = Ext.getCmp('sltEstado').value;
    empleado = Ext.getCmp('comboEmpleado').value;

    $('#hid_comboHipotesis').val((!isNaN(comboHipotesis_index.getValue()) ? comboHipotesis_index.getValue() : ''));
    $('#hid_sltEstado').val(estado ? estado : '');
    $('#hid_comboNivelCriticidad').val((!isNaN(Ext.getCmp('comboNivelCriticidad').value) ? Ext.getCmp('comboNivelCriticidad').value : ''));
    $('#hid_comboTipoCaso').val((!isNaN(Ext.getCmp('comboTipoCaso').value) ? Ext.getCmp('comboTipoCaso').value : ''));
    $('#hid_usrCreacion').val(empleadoAsignacion?empleadoAsignacion:"");
    $('#hid_usrCierre').val((!isNaN(Ext.getCmp('comboEmpleados2').value) ? Ext.getCmp('comboEmpleados2').value : ''));
    $('#hid_comboDepartamento').val((!isNaN(Ext.getCmp('comboDepartamento').value) ? Ext.getCmp('comboDepartamento').value : ''));
    $('#hid_comboEmpleado').val(empleado ? empleado: '');
    $('#hid_comboCiudad').val((!isNaN(Ext.getCmp('comboCiudad').value) ? Ext.getCmp('comboCiudad').value : ''));
    $('#hid_empresa').val(empresa ? empresa : '');

    Ext.MessageBox.show({
        title      : 'Mensaje',
        msg        : '¿Está seguro de generar el <b>Reporte de Casos</b>?',
        closable   : false,
        multiline  : false,
        icon       : Ext.Msg.QUESTION,
        buttons    : Ext.Msg.YESNO,
        buttonText : {yes: 'Si', no: 'No'},
        fn: function (buttonValue)
        {
            if (buttonValue === 'yes') {

                var txtNumero                = Ext.getCmp('txtNumero').value ? Ext.getCmp('txtNumero').value : '';
                var txtTituloInicial         = Ext.getCmp('txtTituloInicial').value ? Ext.getCmp('txtTituloInicial').value : '';
                var txtVersionInicial        = Ext.getCmp('txtVersionInicial').value ? Ext.getCmp('txtVersionInicial').value : '';
                var txtTituloFinal           = Ext.getCmp('txtTituloFinal').value ? Ext.getCmp('txtTituloFinal').value : '';
                var txtVersionFinal          = Ext.getCmp('txtVersionFinal').value ? Ext.getCmp('txtVersionFinal').value : '';
                var comboHipotesis_index     = Ext.getCmp('comboHipotesis_index').value ? Ext.getCmp('comboHipotesis_index').value : '';
                var txtClienteAfectado       = Ext.getCmp('txtClienteAfectado').value ? Ext.getCmp('txtClienteAfectado').value : '';
                var txtLoginAfectado         = Ext.getCmp('txtLoginAfectado').value ? Ext.getCmp('txtLoginAfectado').value : '';
                var hid_comboHipotesis       = $('#hid_comboHipotesis').val();
                var hid_sltEstado            = $('#hid_sltEstado').val();
                var hid_comboNivelCriticidad = $('#hid_comboNivelCriticidad').val();;
                var hid_comboTipoCaso        = $('#hid_comboTipoCaso').val();;;
                var hid_usrCreacion          = $('#hid_usrCreacion').val();
                var hid_usrCierre            = $('#hid_usrCierre').val();
                var hid_comboDepartamento    = $('#hid_comboDepartamento').val();
                var hid_comboEmpleado        = $('#hid_comboEmpleado').val();
                var hid_comboCiudad          = $('#hid_comboCiudad').val();
                var hid_empresa              = $('#hid_empresa').val();
                var feAperturaDesde          = Ext.getCmp('feAperturaDesde').value ? Ext.getCmp('feAperturaDesde').value : '';
                var feAperturaHasta          = Ext.getCmp('feAperturaHasta').value ? Ext.getCmp('feAperturaHasta').value : '';
                var feCierreDesde            = Ext.getCmp('feCierreDesde').value ? Ext.getCmp('feCierreDesde').value : '';
                var feCierreHasta            = Ext.getCmp('feCierreHasta').value ? Ext.getCmp('feCierreHasta').value : '';

                Ext.MessageBox.wait('Procesando...');
                Ext.Ajax.request({
                    url    :  urlExportarCasos,
                    method : 'post',
                    timeout:  400000,
                    params : {
                        'txtNumero'                : txtNumero,
                        'txtTituloInicial'         : txtTituloInicial,
                        'txtVersionInicial'        : txtVersionInicial,
                        'txtTituloFinal'           : txtTituloFinal,
                        'txtVersionFinal'          : txtVersionFinal,
                        'comboHipotesis_index'     : comboHipotesis_index,
                        'txtClienteAfectado'       : txtClienteAfectado,
                        'txtLoginAfectado'         : txtLoginAfectado,
                        'hid_comboHipotesis'       : hid_comboHipotesis,
                        'hid_sltEstado'            : hid_sltEstado,
                        'hid_comboNivelCriticidad' : hid_comboNivelCriticidad,
                        'hid_comboTipoCaso'        : hid_comboTipoCaso,
                        'hid_usrCreacion'          : hid_usrCreacion,
                        'hid_usrCierre'            : hid_usrCierre,
                        'hid_comboDepartamento'    : hid_comboDepartamento,
                        'hid_comboEmpleado'        : hid_comboEmpleado,
                        'hid_comboCiudad'          : hid_comboCiudad,
                        'hid_empresa'              : hid_empresa,
                        'feAperturaDesde'          : feAperturaDesde,
                        'feAperturaHasta'          : feAperturaHasta,
                        'feCierreDesde'            : feCierreDesde,
                        'feCierreHasta'            : feCierreHasta
                    },
                    success: function (response) {

                        var objData = Ext.JSON.decode(response.responseText); //Obtenemos la respuesta del controlador
                        var status  = objData.status;
                        var message = objData.status === 'ok'
                            ? objData.message+'. En breves minutos llegará el reporte a su correo.'
                            : objData.message;

                        Ext.MessageBox.show({
                            title      : status === 'ok' ? 'Mensaje' : 'Alerta',
                            msg        : message,
                            buttons    : Ext.MessageBox.OK,
                            icon       : status === 'ok' ? Ext.MessageBox.INFO : Ext.MessageBox.ERROR,
                            closable   : false,
                            multiline  : false,
                            buttonText : {ok: 'Cerrar'}
                        });
                    },
                    failure: function (result) {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        }
    });
}

function getDiferenciaTiempo(fechaIni, fechaFin){
  	  
	  var fechaIniS = getDate(fechaIni).split("-");
	  var fechaFinS = getDate(fechaFin).split("-");	 	 
	  
	  fechaF = (String)(fechaFinS[2]+"/"+fechaFinS[1]+"/"+fechaFinS[0]);  
	  
	  fecha  = (String)(fechaIniS[2]+"/"+fechaIniS[1]+"/"+fechaIniS[0]); 
	    
	  var fechaInicio = new Date(fecha);	  	  	  
	  var fechaFin    = new Date(fechaF);   	  	
	  	 
	  var difFecha = fechaFin - fechaInicio;
	  
	  return Math.ceil((((difFecha/1000)/60)/60)/24);
	  
      //     (((fechaResta/1000)))          --> Segundos
      //     (((fechaResta/1000)/60))       --> Minutos
      //     (((fechaResta/1000)/60)/60)    --> Horas
      //     (((fechaResta/1000)/60)/60)/24 --> Días		  	
    
}

function validador(e, tipo) 
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    if (tipo == 'numeros') {
        letras = "0123456789";
        especiales = [8, 37, 36];
    } else if (tipo == 'letras') {
        letras = "abcdefghijklmnopqrstuvwxyz";
        especiales = [8, 37, 32, 46, 36, 38, 40, 44];
    }
    else {
        letras = "abcdefghijklmnopqrstuvwxyz0123456789";
        especiales = [8, 37, 32, 46, 36, 38, 40, 44, 45];
    }

    //46 => .    
    //32 => ' ' espacio
    //8 => backspace       
    //37 => direccional izq
    //39 => direccional der y '
    //44 => ,

    tecla_especial = false
    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }


    if (letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}

function addZero(num)
{
    (String(num).length < 2) ? num = String("0" + num) : num = String(num);
    return num;
}
function getDate(date) 
{
    if (typeof date === 'undefined')
    {
        var currentTime = new Date();
    }
    else
    {
        var currentTime = date;   
    }

    var month = addZero(currentTime.getMonth() + 1);
    var day = addZero(currentTime.getDate());
    var year = currentTime.getFullYear();
    return(day + "-" + month + "-" + year);

}
function getHour(hour) 
{
    if (typeof hour === 'undefined')
    {
        var currentTime = new Date();
    }
    else
    {
        var currentTime = hour;
    }

    var hour = addZero(currentTime.getHours());
    var minute = addZero(currentTime.getMinutes());
    return(hour + ":" + minute);

}