Ext.onReady(function()
{
    fecha_apertura = document.getElementById("fecha_apertura");
    hora_apertura  = document.getElementById("hora_apertura");
    estado_caso    = document.getElementById("ultimo_estado");
    bandera        = document.getElementById("bandera");
    fecha_actual   = document.getElementById("fecha_actual");
    hora_actual    = document.getElementById("hora_actual");
    if(fecha_apertura && hora_apertura && estado_caso.value != 'Cerrado' && estado_caso.value != 'Creado' && bandera.value == 'S')
    {
        fecha_ejecucion = Ext.create('Ext.form.Panel', {
            renderTo: 'div_fe_ejecucion',
            id: 'fe_apertura_ejecucion',
            name:'fe_apertura_ejecucion',
            width: 144,
            frame:false,
            bodyPadding: 0,
            height:30,
            border:0,
            margin:0,
            items: [{
                xtype: 'datefield',
                id: 'fe_apertura_ejecucion_value',
                name:'fe_apertura_ejecucion_value',
                editable: false,
                anchor: '100%',
                format: 'Y-m-d',
                value:fecha_apertura.value,
                minValue: fecha_actual.value
            }]
        });

        hora_ejecucion = Ext.create('Ext.form.Panel', {
            width: 85,
            frame:false,
            height:30,
            id: 'ho_apertura_ejecucion',
            name:'ho_apertura_ejecucion',
            border:0,
            margin:0,
            renderTo: 'div_hora_ejecucion',
            items: [{
                xtype: 'timefield',
                format: 'H:i',
                id: 'ho_apertura_ejecucion_value',
                name: 'ho_apertura_ejecucion_value',
                minValue: '00:01 AM',
                maxValue: '23:59 PM',
                increment: 1,
                value:hora_apertura.value,
                anchor: '100%'
            }]
        });
    }
});

var objStoreArbolHipotesis = 
{ 
    total: 'total',
    proxy: {
        type  : 'ajax',
        url   : url_admiHipotesisArbolGrid,
        reader: {
            type          : 'json',
            totalProperty : 'total',
            root          : 'encontrados'
        },
        extraParams: {
                nombre: '',
                estado: 'Activo',
                start : 0,
                limit : 999
        }
    },
    fields:
    [
        {name:'id_hipotesis', mapping:'id_hipotesis'},
        {name:'nombre_hipotesis', mapping:'nombre_hipotesis'},
        {name:'descripcion_hipotesis', mapping:'descripcion_hipotesis'},
    ]
};
var storeArbolHipotesisNivel1 = new Ext.data.Store(objStoreArbolHipotesis);
storeArbolHipotesisNivel1.proxy.extraParams = {padreHipotesis: '0',estado:'Activo'};
var storeArbolHipotesisNivel2 = new Ext.data.Store(objStoreArbolHipotesis);
var storeArbolHipotesisNivel3 = new Ext.data.Store(objStoreArbolHipotesis);

function comparaFechas(fecha_apertura, fechaDetTareas)
{
    var dateDetTarea;
    var fechaApertura = fecha_apertura.trim();
    if (typeof fechaDetTareas === 'undefined' || fechaDetTareas == null) {
        dateDetTarea = Date.now();
    }else{
        var arrayFechaDetTarea = fechaDetTareas.split('-');
        var strFechaDetTareas;
        if (arrayFechaDetTarea[2] > 31) {
            strFechaDetTareas = [ arrayFechaDetTarea[2], arrayFechaDetTarea[1], arrayFechaDetTarea[0] ].join('-');
        }else{
            strFechaDetTareas = [ arrayFechaDetTarea[0], arrayFechaDetTarea[1], arrayFechaDetTarea[2] ].join('-');
        }
        dateDetTarea = Date.parse(strFechaDetTareas);
    }
    var arrayFechaApertura = fechaApertura.split('-');
    var strFechaApertura;
    if (arrayFechaApertura[2] > 31) {
        strFechaApertura = [ arrayFechaApertura[2], arrayFechaApertura[1], arrayFechaApertura[0] ].join('-');
    }else{
        strFechaApertura = [ arrayFechaApertura[0], arrayFechaApertura[1], arrayFechaApertura[2] ].join('-');
    }
    var dateApertura = Date.parse(strFechaApertura);
    return dateApertura > dateDetTarea;
}

function setearFecha()
{
    fechaAsignacion = Ext.getCmp('fe_apertura_ejecucion').getValues().fe_apertura_ejecucion_value;
	$("#fecha_asignacion").val(fechaAsignacion);

    horaAsignacion = Ext.getCmp('ho_apertura_ejecucion').getValues().ho_apertura_ejecucion_value;
	$("#hora_asignacion").val(horaAsignacion);

    if(fechaAsignacion == fecha_actual.value && horaAsignacion <= hora_actual.value)
    {
        Ext.Msg.alert("Alerta","La fecha de asignación debe ser mayor o igual que la fecha de apertura");
        return false;
    }
    else
    {
        Ext.get(document.body).mask('Actualizando la Fecha...');
        return true;
    }
}

function validarFechaTareaReprogramada(fechaInicio, horaInicio, fechaFin, horaFin) 
{     
    if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 0) 
    {
        Ext.Msg.alert('Alerta ', 'No puede finalizar la tarea, aun no se cumple la fecha de planificacion');
        return -1;
    } 
    else 
    {
        //son fechas iguales por tanto se valida la diferencia por horas
        if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 2) 
        {     
            if (validate_fechaMayorQue(horaInicio, horaFin, 'hora') === 0) 
            {
                Ext.Msg.alert('Alerta ', 'No puede finalizar la tarea, aun no se cumple la hora de planificacion');
                return -1;
            }
        } 
        else
        {
            return 1;
        }
    }
}
function getTiempoTotal(fechaInicio,horaInicio,fechaFin, horaFin,tipo)
{
    /* Verificar que tanto la fechaInicio como la fechaFin se encuentren en el formato dd-mm-yyyy, para que la resta entre fechas sea correcta*/
    var matchFechaInicio = fechaInicio.match(/^(\d{2})\-(\d{2})\-(\d{4})$/);
    var matchFechaFin = fechaFin.match(/^(\d{2})\-(\d{2})\-(\d{4})$/);
    
    var fechaInicioTmp="";
    var fechaFinTmp="";
    if(matchFechaInicio==null)
    {
        /*Convertir al formato dd-mm-YYYY, porque la fechaInicio viene en formato yyyy-mm-dd*/
        fechaInicioTmp=fechaInicio.split("-");
        fechaInicio = (String)(fechaInicioTmp[2] + "-" + fechaInicioTmp[1] + "-" + fechaInicioTmp[0]);
        
    }
    
    if(matchFechaFin==null)
    {
        /*Convertir al formato dd-mm-YYYY, porque la fechaFin viene en formato yyyy-mm-dd*/
        fechaFinTmp=matchFechaFin.split("-");
        fechaFin = (String)(fechaFinTmp[2] + "-" + fechaFinTmp[1] + "-" + fechaFinTmp[0]);
    }
    
   if(tipo === 'fecha') 
   {
        if (validate_fechaMayorQue(horaInicio, horaFin, 'hora') === 1) 
        {
            if (validate_fechaMayorQue(fechaInicio, fechaFin, tipo) === 0) 
            {
                Ext.Msg.alert('Alerta ', 'Fecha de Cierre no puede ser menor a la Fecha de Apertura');
                return -1;
            }
        } 
        else 
        {
            Ext.Msg.alert('Alerta ', 'La Hora de cierre es menro que la fecha de Apertura, corrija');
            return -1;
        }
    }
    if (tipo === 'hora') 
    {
        if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 2) 
        {
            if (validate_fechaMayorQue(horaInicio, horaFin, tipo) === 0) 
            {
                Ext.Msg.alert('Alerta ', 'Hora de Cierre no puede ser menor a la Hora de Apertura');
                return -1;
            }
        } 
        else if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 0) 
        {
            Ext.Msg.alert('Alerta ', 'La Fecha de cierre es menor que la fecha de Apertura, corrija');
            return -1;
        }
    }
    
    ///////////////////////////////////////////////////////////////////////////////7
    
	var fechas = fechaInicio.split("-");

    //FECHAS - DETERMINAR DIAS
    fecha = (String)(fechas[2] + "/" + fechas[1] + "/" + fechas[0]);
  
    var fechaFinS = fechaFin.split("-");

    fechaF = (String)(fechaFinS[2] + "/" + fechaFinS[1] + "/" + fechaFinS[0]);

    var fechaInicio = new Date(fecha);
    var fechaFin = new Date(fechaF);

    var horaFin = horaFin;

    var horasTotalesInicio = horaInicio.split(":");
    var horasTotalesFin    = horaFin.split(":");

    var difFecha = fechaFin - fechaInicio;

    //     (((fechaResta/1000)))          --> Segundos
    //     (((fechaResta/1000)/60))       --> Minutos
    //     (((fechaResta/1000)/60)/60)    --> Horas
    //     (((fechaResta/1000)/60)/60)/24 --> Días	

    var diasTotales = Math.ceil((((difFecha / 1000) / 60) / 60) / 24); //dias totales        

    var minutosAdjudicar = "";

    if (diasTotales > 0) 
    {
        diasTotales = diasTotales - 1;

        minutosInicio = (24 * 60) - (parseInt(horasTotalesInicio[0]) * 60 + parseInt(horasTotalesInicio[1]));
        minutosFin = (parseInt(horasTotalesFin[0]) * 60 + parseInt(horasTotalesFin[1]));

        minutosTotales = minutosInicio + minutosFin;

        minutosAdjudicar = (diasTotales * 1440) + minutosTotales; //minutos						
    }
    else
    {
        minutosInicio = parseInt(horasTotalesInicio[0]) * 60 + parseInt(horasTotalesInicio[1]);
        minutosFin = parseInt(horasTotalesFin[0]) * 60 + parseInt(horasTotalesFin[1]);

        minutosAdjudicar = minutosFin - minutosInicio;
    }    
    return  minutosAdjudicar < 0 ? minutosAdjudicar * -1 : minutosAdjudicar;
}

 function validate_fechaMayorQue(fechaInicial,fechaFinal,tipo)
{        
      if (tipo === 'fecha') 
      {
        valuesStart = fechaInicial.split("-");
        valuesEnd   = fechaFinal.split("-");
       
       //Si los años son diferentes
        if (parseInt(valuesStart[2]) !== parseInt(valuesEnd[2]))
        {
            if (parseInt(valuesStart[2]) > parseInt(valuesEnd[2])) // Si el año de programacion es mayor lanza el mensaje
            {
                return 0;
            }
            else //Si el año de promagramacion ya paso por ende se puede finalizar la tarea
            {
                return 1;
            }
        }
        //Si el año es igual se valida meses y luego por dias
        else if (parseInt(valuesStart[1]) > parseInt(valuesEnd[1]))
        {
            return 0;
        }
        else if (parseInt(valuesStart[1]) < parseInt(valuesEnd[1]))//Significa que el dia de ejecucion ya paso y puede ser finalizado
        {  
            return 1;
        }
        else if (parseInt(valuesStart[0]) > parseInt(valuesEnd[0]))
        {
            return 0;
        }
        else //Se valida la diferencia entre dias
        {
            if (parseInt(valuesStart[0]) === parseInt(valuesEnd[0]))
            {
                return 2;
            }
            else if (parseInt(valuesStart[0]) < parseInt(valuesEnd[0]))
            {
                return 1;
            }
        }
    } 
    else 
    {
        valuesStart = fechaInicial.split(":");
        valuesEnd   = fechaFinal.split(":");

        if (parseInt(valuesStart[0]) > parseInt(valuesEnd[0]))
            return 0;

        else if (parseInt(valuesStart[0]) === parseInt(valuesEnd[0])) //es la misma hora
        {
            if (parseInt(valuesStart[1]) > parseInt(valuesEnd[1]))
            {
                return 0;
            }
            else
            {
                return 1;
            }
        }
        else
        {
            return 1;
        }
    }
}


/*
 * Para cerrar los Casos
 */
function obtenerDatosCasosCierre(id_caso,conn,esCancelada)
{
    conn.request({
        method: 'POST',
        params: {
            id_caso: id_caso,
            es_cancelado: esCancelada
        },
        url: url_obtenerDatosCierre,
        success: function(response)
        {
            var json = Ext.JSON.decode(response.responseText);

            if (json)
            {
                cerrarCaso(json.encontrados[0]);
            }
            else
            {
                Ext.Msg.alert('Alerta ', json.mensaje);
            }
        },
        failure: function() {

            Ext.Msg.alert('Alerta ', 'FAllo');
        }
    });

}

function arrayToHtmlList(soluciones)
{
    let solucionesFinal = ["Sin Tareas Solución"];
    if (soluciones !== undefined && soluciones.length != 0) {
        solucionesFinal = soluciones;
    }
    let html = '';
    html += '<ul>';
    solucionesFinal.forEach(item => {
        html += '<li>' + item.nombreTarea + '</li>';
    });
    html += '</ul>';
    return html;
}

function cerrarCaso(data)
{
    var id_caso          = data.id_caso;
    var numero           = data.numero_caso;
    var fecha            = data.fecha_apertura;
    var hora             = data.hora_apertura;
    var tieneTareas      = data.tiene_tareas;
    var fechaFin         = data.fecha_final;
    var horaFin          = data.hora_final;
    var hipotesisIniciales = data.hipotesisIniciales;
    var fechaActual      = data.fechaActual;
    var horaActual       = data.horaActual;
    var tipoAfectacion   = data.afectacion;
    var fechaActualArray = fechaActual.split("-");
    fechaActual          = fechaActualArray[2]+"-"+fechaActualArray[1]+"-"+fechaActualArray[0];
    var observacionHeredaVersionFinal = "";
    var tiempoTotalCaso   = data.tiempoTotalCaso;
    var nuevoEsquema = data.nuevoEsquema;
    var boolDetTarea = comparaFechas(data.fecha_apertura, (typeof fechaDetTareas === 'undefined')? null : fechaDetTareas);



    // Funcion que crea la pantalla de indisponibilidad, de manera que permanezca oculta
    //para poder obtener los valores a guardar y realizar un unico commit
    obtenerArbolHipotesis(data);
    verIndisponibilidad(data);
    obtenerTiempoAfectacionIndisponibilidadCaso(data);
    


    if(data.observacionVersionFinal) {
        observacionHeredaVersionFinal=data.observacionVersionFinal;
    }

    if(!tieneTareas) { //Cuando tiene tareas la fecha de cierre efectiva del caso es da la tarea que da solucion al caso
        fechaFin = fechaActual;
        horaFin  = horaActual;        
    }

    if(nuevoEsquema == "S" && data.casoTN !== 1) {
        tiempo_total = tiempoTotalCaso;
    } else {
        tiempo_total = getTiempoTotal(fecha, hora, fechaFin, horaFin);
    }

    comboArbolHipotesisNivel1 = Ext.create('Ext.form.ComboBox', {
	   id:'comboArbolHipotesisNivel1',
	   store: storeArbolHipotesisNivel1,
	   displayField: 'nombre_hipotesis',
	   valueField: 'id_hipotesis',
       height:30,
	   border:0,
	   margin:0,
	   fieldLabel: 'Nivel 1:',	
	   queryMode: "remote",
       emptyText: '',
       listConfig: {
            getInnerTpl: function() {
                return '<div data-qtip="{descripcion_hipotesis}">{nombre_hipotesis} </div>';
            }
       },
	   listeners:{
		   select: function(combo)
		   {
			Ext.getCmp('comboArbolHipotesisNivel2').value = "";
			Ext.getCmp('comboArbolHipotesisNivel2').setRawValue("");
		    Ext.getCmp('comboArbolHipotesisNivel2').reset();
            Ext.getCmp('comboArbolHipotesisNivel2').setDisabled(false);
			Ext.getCmp('comboArbolHipotesisNivel3').value = "";
			Ext.getCmp('comboArbolHipotesisNivel3').setRawValue("");
		    Ext.getCmp('comboArbolHipotesisNivel3').reset();
			Ext.getCmp('comboArbolHipotesisNivel3').setDisabled(true);
            storeArbolHipotesisNivel2.proxy.extraParams = {caso: id_caso};
			storeArbolHipotesisNivel2.proxy.extraParams = {padreHipotesis: combo.getValue(),estado:'Activo'};
			storeArbolHipotesisNivel2.load();
		   }
	   }
    });
    comboArbolHipotesisNivel2 = Ext.create('Ext.form.ComboBox', {
		id:'comboArbolHipotesisNivel2',
		store: storeArbolHipotesisNivel2,
		displayField: 'nombre_hipotesis',
		valueField: 'id_hipotesis',
		height:30,
		border:0,
		margin:0,
		fieldLabel: 'Nivel 2:',	
		queryMode: "remote",
		emptyText: '',
        disabled: true,
        listConfig: {
            getInnerTpl: function() {
                return '<div data-qtip="{descripcion_hipotesis}">{nombre_hipotesis} </div>';
            }
        },
		listeners:{
			select: function(combo)
			{
			 Ext.getCmp('comboArbolHipotesisNivel3').value = "";
			 Ext.getCmp('comboArbolHipotesisNivel3').setRawValue("");
			 Ext.getCmp('comboArbolHipotesisNivel3').reset();
			 Ext.getCmp('comboArbolHipotesisNivel3').setDisabled(false);
			 storeArbolHipotesisNivel3.proxy.extraParams = {padreHipotesis: combo.getValue(),estado:'Activo'};
			 storeArbolHipotesisNivel3.load();
			}
		}
	 });
	 comboArbolHipotesisNivel3 = Ext.create('Ext.form.ComboBox', {
		id:'comboArbolHipotesisNivel3',
		store: storeArbolHipotesisNivel3,
		displayField: 'nombre_hipotesis',
		valueField: 'id_hipotesis',
		height:30,
		border:0,
		margin:0,
		fieldLabel: 'Nivel 3:',	
		queryMode: "remote",
		emptyText: '',
        disabled: true,
        listConfig: {
            getInnerTpl: function() {
                return '<div data-qtip="{descripcion_hipotesis}">{nombre_hipotesis} </div>';
            }
       }
     });
    fieldSetTituloHipotesis = Ext.create('Ext.form.FieldSet', {
            xtype : 'fieldset',
            title : 'Título Final',
            defaults: {anchor: '100%'},
            items :
            [
                comboArbolHipotesisNivel1,
                comboArbolHipotesisNivel2,
                comboArbolHipotesisNivel3,
            ]
    });
    cierre_comboHipotesisStore = new Ext.data.Store({
        pageSize:1000,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_admiHipotesisGrid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo',
                caso:id_caso
            }
        },
        fields:
        [
            {name:'id_hipotesis', mapping:'id_hipotesis'},
            {name:'nombre_hipotesis', mapping:'nombre_hipotesis'}
        ]
    });

    let esTn = (typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim() == 'TN');
    let solucionesDisplay = '';
    if (esTn && boolDetTarea) {
        solucionesDisplay = arrayToHtmlList(data.solucionesFinal);
    }
    
    soluciones = Ext.create('Ext.form.Panel',{
        id: 'soluciones',
        name: 'soluciones',
        displayField: 'soluciones',
        fieldLabel: 'Soluciones:',
        border: 0,
        hidden: (!(esTn && boolDetTarea) || (typeof data.solucionesFinal === 'undefined')),
        items:[{
            xtype: 'displayfield',
            fieldLabel: 'Soluciones',
            name: 'soluciones',
            value: solucionesDisplay
        }],
    });

    cierre_comboHipotesis = Ext.create('Ext.form.ComboBox', {
        id:'tituloFinalHipotesis',
        name:'tituloFinalHipotesis',
        store: cierre_comboHipotesisStore,
        displayField: 'nombre_hipotesis',
        valueField: 'id_hipotesis',
        height:30,
        border:0,
        margin:0,
        fieldLabel: 'Titulo Final',
        queryMode: "remote",
        emptyText: ''
    });

    fecha_cierre = Ext.form.DateField({
        fieldLabel: 'Fecha de Cierre:',
        xtype: 'textfield',
        id: 'fe_cierre_value',
        name: 'fe_cierre_value',
        format: 'Y-m-d',
        editable: false,
        readOnly: true,
        value: fechaFin,
        listeners: {
            select: {
                fn: function(e)
                {
                    date = e.getValue();
                    total = getTiempoTotal(fecha, hora, date, Ext.getCmp('ho_cierre_value').value, 'fecha');

                    if (total !== -1) {
                        Ext.getCmp('tiempo_total_caso').setValue(total);
                    } else {
                        Ext.getCmp('tiempo_total_caso').setValue(getTiempoTotal(fecha, hora, fechaFin, horaFin, ''));
                        Ext.getCmp('fe_cierre_value').setValue(date);
                    }
                }
            }
        }
    });

    hora_cierre = Ext.form.DateField({
        fieldLabel: 'Hora de Cierre:',
        xtype: 'textfield',
        format: 'H:i',
        id: 'ho_cierre_value',
        name: 'ho_cierre_value',
        editable: false,
        increment: 1,
        readOnly: true,
        value: horaFin,
        listeners: {
            select: {
                fn: function(e)
                {
                    date  = e.getValue();
                    total = getTiempoTotal(fecha, hora, Ext.getCmp('fe_cierre_value').value, date, 'hora');

                    if (total !== -1) {
                        Ext.getCmp('tiempo_total_caso').setValue(total);
                    } else {
                        Ext.getCmp('tiempo_total_caso').setValue(getTiempoTotal(fecha, hora, fechaFin, horaFin, ''));
                        Ext.getCmp('ho_cierre_value').setValue(date);
                    }
                }
            }
        }
    });

    //Panel para el cierre del caso
    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding   : 5,
        waitMsgTarget : true,
        autoScroll: true,
        fieldDefaults : {
            labelAlign : 'left',
            labelWidth : 200,
            msgTarget  : 'side'
        },
        items :
        [
            {
                xtype : 'fieldset',
                defaults : {
                    width: 500
                },
                items :
                [
                    {
                        xtype      : 'textfield',
                        id         : 'numero_casoSintoma',
                        fieldLabel : 'Caso:',
                        name       : 'numero_casoSintoma',
                        value      : numero,
                        readOnly   : true
                    },
                    fecha_cierre,
                    hora_cierre,
                    {
                        xtype      : 'textfield',
                        id         : 'hipotesisIniciales',
                        fieldLabel : 'Hipotesis Inicial:',
                        name       : 'hipotesisIniciales',
                        value      : hipotesisIniciales,
                        readOnly   : true
                    },
                    fieldSetTituloHipotesis,
                    soluciones,
                    cierre_comboHipotesis,
                    {
                        xtype      : 'combobox',
                        id         : 'sltAfectacion',
                        fieldLabel : 'Tipo Afectacion',
                        width      : 500,
                        value      : 'SELECCION',
                        store      : [
                            ['SELECCION'     , 'Seleccione..'],
                            ['CAIDA'         , 'Caida'],
                            ['INTERMITENCIA' , 'Intermitencia'],
                            ['SINAFECTACION' , 'Tiempo atribuible al cliente']
                        ],
                        listeners : {
                            select : function(combo) {
                                if (combo.value === 'SINAFECTACION') {
                                    Ext.Msg.alert('Alerta ', 'Al elegir <b>'+combo.rawValue+'</b>, todo el tiempo será '+
                                                             'imputable al cliente.');
                                }
                            }
                        }
                    },
                    {
                        xtype      : 'textarea',
                        id         : 'versionFinal',
                        fieldLabel : 'Version final',
                        name       : 'versionFinal',
                        rows       : 5,
                        value      : observacionHeredaVersionFinal,
                        allowBlank : false
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'fe_apertura',
                        fieldLabel : 'Fecha Apertura Caso:',
                        name       : 'fe_apertura',
                        increment  : 1,
                        value      : fecha,
                        readOnly   : true
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'ho_apertura',
                        fieldLabel : 'Hora Apertura Caso:',
                        name       : 'ho_apertura',
                        increment  : 1,
                        value      : hora,
                        readOnly   : true
                    },
                    {
                        xtype      : 'textfield',
                        id         : 'tiempo_total_caso',
                        fieldLabel : 'Tiempo Total del Caso (minutos):',
                        name       : 'tiempo_total_caso',
                        increment  : 1,
                        value      : tiempo_total,
                        readOnly   : true
                    }
                ]
            },
            
            {
                xtype: 'button',
                id: 'btnIndisponibilidad',
                text : 'Indisponibilidad',
                //formBind: true,
                hidden: true,
                disabled: false,
                style: {
                    marginLeft: '80%'
                },
                handler: function() {
                    Ext.getCmp('winIndisponibilidadCaso').setVisible(true);
                },
            }
        ],
        buttons:
        [
            {
                text     : '<i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;Guardar',
                formBind : true,
                handler  : function()
                {
                    var idTituloFinalHipotesis = null;

                    if (strBuscaPorArbolHipotesis === 'S')
                    {
                        if(Ext.isEmpty(Ext.getCmp('comboArbolHipotesisNivel3').value))
                        {
                            Ext.Msg.alert('Alerta ', 'Debe escoger el Nivel 3');
                            return;    
                        }
                        else
                        {
                            idTituloFinalHipotesis = Ext.getCmp('comboArbolHipotesisNivel3').value;
                        }
                    }
                    else{
                        if(Ext.isEmpty(Ext.getCmp('tituloFinalHipotesis').value))
                        {
                            Ext.Msg.alert('Alerta ', 'Debe escoger el título Final');
                            return;     
                        }
                        else
                        {
                            idTituloFinalHipotesis = Ext.getCmp('tituloFinalHipotesis').value;
                        }
                    }

                    if (Ext.isEmpty(Ext.getCmp('versionFinal').value)) {
                        Ext.Msg.alert('Alerta ', 'Debe ingresar la versión Final');
                        return;
                    }

                    if (Ext.isEmpty(Ext.getCmp('sltAfectacion').value) || Ext.getCmp('sltAfectacion').value === 'SELECCION') {
                        Ext.Msg.alert('Alerta ', 'Debe escoger un <b>Tipo de Afectación</b>.');
                        return;
                    }

                    // Se agregan variables para Indisponibilidad
                    var strGuardar = 'NO';
                    var strIndisponibilidadI = '';
                    var strTipoI = 'C';
                    var intTiempoAfectacionI = '0';
                    var strMasivoI = '';
                    var intComboResponsableI = '';
                    var intClientesAfectadosI = '0';
                    var strObservacionesI = '';
                    var strOltI = '';
                    var strPuertoI = '';
                    var strCajaI = '';
                    var strSplitterI = '';
                    var intIdHipotesisInicialI = '';
                    var i;
                    var j;

                    if(Ext.getCmp('btnIndisponibilidad').isVisible()){

                        strGuardar = 'SI';

                        strIndisponibilidadI = Ext.getCmp('comboIndisponibilidad').getValue();
                        intIdHipotesisInicialI = comboArbolHipotesisNivel3Indisponibilidad.getValue();

                        if(strIndisponibilidadI == 'SI'){
                            
                            intTiempoAfectacionI = Ext.getCmp('tiempoAfectacion').getValue();
                            strMasivoI = Ext.getCmp('comboMasivo').getValue();

                            if(strMasivoI == 'SI'){
                            
                                intClientesAfectadosI = Ext.getCmp('clientesAfectados').getValue();
                                intComboResponsableI = Ext.getCmp('comboResponsable').getValue();
                                strObservacionesI = Ext.getCmp('observaciones').getValue();

                                strOltI = Ext.getCmp('oltSeleccionados').getValue();

                                if (strOltI == ''){
                                    Ext.Msg.alert("Alerta","Debe escoger un elemento Olt");
                                    return false;
                                }else if (intClientesAfectadosI == null){
                                    Ext.Msg.alert("Alerta","Debe llenar clientes afectados");
                                    return false;
                                }else if (intComboResponsableI == null){
                                    Ext.Msg.alert("Alerta","Debe escoger un responsable del problema");
                                    return false;
                                }

                                // combo puerto
                                if(comboPuertoC.valueModels != null){
                                    
                                    for (i = 0; i<comboPuertoC.valueModels.length; i++){

                                        for (j = 0; j<storePuerto.data.items.length; j++){

                                            if (comboPuertoC.valueModels[i].data.idInterface == storePuerto.data.items[j].data.idInterface){
                                                
                                                if (strPuertoI == ''){
                                                    strPuertoI = comboPuertoC.valueModels[i].data.idInterface;
                                                }else{
                                                    strPuertoI = strPuertoI + ', ' + comboPuertoC.valueModels[i].data.idInterface;
                                                }
                                                break;
                    
                                            }

                                        }

                                    }
                                }

                                // combo caja
                                if(comboCaja.valueModels != null){
                                    
                                    for (i = 0; i<comboCaja.valueModels.length; i++){

                                        for (j = 0; j<storeCaja.data.items.length; j++){

                                            if (comboCaja.valueModels[i].data.idCaja == storeCaja.data.items[j].data.idCaja){
                                                
                                                if (strCajaI == ''){
                                                    strCajaI = comboCaja.valueModels[i].data.idCaja;
                                                }else{
                                                    strCajaI = strCajaI + ', ' + comboCaja.valueModels[i].data.idCaja;
                                                }
                                                break;
                    
                                            }

                                        }

                                    }
                                }

                                // combo splitter
                                if(comboSplitter.valueModels != null){
                                    
                                    for (i = 0; i<comboSplitter.valueModels.length; i++){

                                        for (j = 0; j<storeSplitter.data.items.length; j++){

                                            if (comboSplitter.valueModels[i].data.idSplitter == storeSplitter.data.items[j].data.idSplitter){
                                                
                                                if (strSplitterI == ''){
                                                    strSplitterI = comboSplitter.valueModels[i].data.idSplitter;
                                                }else{
                                                    strSplitterI = strSplitterI + ', ' + comboSplitter.valueModels[i].data.idSplitter;
                                                }
                                                break;
                    
                                            }

                                        }

                                    }
                                }
                    
                            }

                        }

                    }



                    Ext.MessageBox.wait("Cerrando Caso...");
                    Ext.Ajax.request({
                        method : 'POST',
                        url    : url_cerrarCaso,
                        params :
                        {
                            id_caso              : id_caso,
                            fe_cierre            : Ext.getCmp('fe_cierre_value').value,
                            hora_cierre          : Ext.getCmp('ho_cierre_value').value,
                            tituloFinalHipotesis : idTituloFinalHipotesis,
                            versionFinal         : Ext.getCmp('versionFinal').value,
                            tiempo_total_caso    : Ext.getCmp('tiempo_total_caso').value,
                            tipo_afectacion      : Ext.getCmp('sltAfectacion').value,
                            strGuardar              : strGuardar,
                            strIndisponibilidadI    : strIndisponibilidadI,
                            strTipoI                : strTipoI,
                            intTiempoAfectacionI    : intTiempoAfectacionI,
                            strMasivoI              : strMasivoI,
                            intComboResponsableI    : intComboResponsableI,
                            intClientesAfectadosI   : intClientesAfectadosI,
                            strObservacionesI       : strObservacionesI,
                            strOltI                 : strOltI,
                            strPuertoI              : strPuertoI,
                            strCajaI                : strCajaI,
                            strSplitterI            : strSplitterI,
                            intIdHipotesisInicialI   : intIdHipotesisInicialI
                        },
                        success: function(response)
                        {
                            var json = Ext.JSON.decode(response.responseText);
                            if (!json.success) {
                                Ext.Msg.alert('Error', json.mensaje);
                            } else {
                                winIndisponibilidadCaso.destroy();
                                winCerarrCaso.destroy();

                                Ext.MessageBox.show({
                                    title : "Mensaje",
                                    msg : json.mensaje,
                                    multiline : false,
                                    icon : Ext.MessageBox.INFO,
                                    buttons : Ext.Msg.YES,
                                    buttonText : {yes: 'ok'},
                                    fn : function (buttonValue) {
                                        if (buttonValue === "yes") {
                                            document.location.reload(true);
                                        }
                                    }
                                });
                            }
                        },
                        failure: function(result)
                        {
                            Ext.MessageBox.show({
                                title   : 'Error',
                                msg     : result.statusText,
                                buttons : Ext.MessageBox.OK,
                                icon    : Ext.MessageBox.ERROR
                            });
                        }
                    });
                }
            },
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cancelar',
                handler: function() {
                    if (typeof gridDetallesTareasCerrarCasoTN !== 'undefined') {
                        gridDetallesTareasCerrarCasoTN.tip.destroy();
                    }
                    
                    winIndisponibilidadCaso.destroy();
                    winCerarrCaso.destroy();
                    
                    if(data.casoTN == "TN") {
                        document.location.reload(true);
                    }
                }
            }
        ]
    });
    if (strBuscaPorArbolHipotesis === 'S')
    {
        cierre_comboHipotesis.setVisible(false);
        comboArbolHipotesisNivel1.setVisible(true);
        comboArbolHipotesisNivel2.setVisible(true);
        comboArbolHipotesisNivel3.setVisible(true);
        fieldSetTituloHipotesis.setVisible(true);
    }
    else
    {
        cierre_comboHipotesis.setVisible(true);
        comboArbolHipotesisNivel1.setVisible(false);
        comboArbolHipotesisNivel2.setVisible(false);
        comboArbolHipotesisNivel3.setVisible(false);
        fieldSetTituloHipotesis.setVisible(false);
    }

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    }); 


    // verificar perfil TAP
    conn.request({
        url: url_verificarRolTap,
        method: 'post',
        success: function(response){	
            if((response.responseText) === 'S'){
                Ext.getCmp('btnIndisponibilidad').setVisible(true);
            }
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });


    winCerarrCaso = Ext.create('Ext.window.Window', {
            title    : 'Cerrar Caso',
            modal    : true,
            closable : false,
            width    : 620,
            height   : 520,
            layout   : 'fit',
            items    : [formPanel]
    }).show();
}



/************************************************************************ */
/*********************** INDISPONIBILIDAD ******************************** */
/************************************************************************ */

var winIndisponibilidadCaso;
var intHipotesisId1;
var strHipotesisNombre1;
var intHipotesisId2;
var strHipotesisNombre2;
var intHipotesisId3;
var strHipotesisNombre3;

function verIndisponibilidad(data)
{

    actualizarTiempoAfectacion = Ext.create('Ext.Button', {
        id          : 'actualizarTiempoAfectacion',
        text        : '<i class="fa fa-refresh" aria-hidden="true"></i>',
        tooltip     : 'Actualizar tiempo afectacion',
        tooltipType : 'title',
        style       : 'position: absolute; margin: -8% 0% 0% 50%;',
        hidden       : true,
        handler: function() {
            obtenerTiempoAfectacionIndisponibilidadCaso(data);
        }
    });

    /*** combo olt multiple ***/
    storeOlt = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getElementosPorTipo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        sorters: [{
             property : 'nombreElemento',
             direction: 'ASC'
        }],
        fields: [
            {name: 'idElemento', mapping: 'idElemento'},
            {name: 'nombreElemento', mapping: 'nombreElemento'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount1', {
        alias: 'plugin.selectedCount1',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    var acumuladorValue = '';
                    var acumuladorDescripcion = '';

                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;

                        

                        // acumula lo seleccionado en label aparte, para poder dejar el combo libre para 
                        // realizar busquedas
                        //anteriorValue = Ext.getCmp('oltValue').getValue();
                        acumuladorDescripcion = Ext.getCmp('oltSeleccionados').getValue();

                        if (combo.valueModels != null){
                            
                            if (combo.valueModels.length > 0){

                                if (acumuladorDescripcion != ''){
                                    //anteriorValue = anteriorValue + ',';
                                    acumuladorDescripcion = acumuladorDescripcion + ', ';
                                }

                                // si no existe lo agrego
                                if(acumuladorDescripcion.indexOf(combo.rawValue) == -1){

                                    //Ext.getCmp('oltValue').setValue(anteriorValue + combo.valueModels[0].data.idElemento);
                                    Ext.getCmp('oltSeleccionados').setValue(acumuladorDescripcion + combo.rawValue);

                                }
                                
                            }

                        }

                        Ext.getCmp('comboOlt').value = "";
                        Ext.getCmp('comboOlt').setRawValue("");
                    }
                }
            })
        }
    });

    comboOlt = Ext.create('Ext.form.ComboBox', {
        id           : 'comboOlt',
        store        :  storeOlt,
        displayField : 'nombreElemento',
        valueField   : 'idElemento',
        fieldLabel   : 'Elemento',
        width        :  390,
        queryMode    : "remote",
        plugins      : ['selectedCount1'],
        disabled     : false,
        editable     : true,
        multiSelect  : false,
        hidden       : true
    });
    /*** combo olt multiple ***/

    /*** combo puerto multiple ***/
    storePuerto = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getInterfacesPorElemento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        sorters: [{
             property : 'idInterface',
             direction: 'ASC'
        }],
        fields: [
            {name: 'idInterface', mapping: 'idInterface'},
            {name: 'nombreInterface', mapping: 'nombreInterface'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount2', {
        alias: 'plugin.selectedCount2',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;
                    }
                }
            })
        }
    });

    comboPuertoC = Ext.create('Ext.form.ComboBox', {
        id           : 'comboPuertoC',
        store        :  storePuerto,
        displayField : 'idInterface',
        valueField   : 'nombreInterface',
        fieldLabel   : 'Puerto Elemento',
        width        :  390,
        queryMode    : "remote",
        plugins      : ['selectedCount2'],
        disabled     : false,
        editable     : true,
        multiSelect  : true,
        hidden       : true,
        displayTpl   : '<tpl for=".">{nombreInterface}<tpl if="xindex < xcount">,</tpl> </tpl>',
        listConfig   : {
            itemTpl: '{nombreInterface} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            change: function(combo, records, eOpts) {

                var acumulador = '';

                if(combo.valueModels != null){

                    nombreOlt = Ext.getCmp('oltSeleccionados').getValue();

                    if(nombreOlt.indexOf(",") == -1){
                    
                        for (var i = 0; i<combo.valueModels.length; i++){

                            for (var j = 0; j<storePuerto.data.items.length; j++){

                                if (combo.valueModels[i].data.idInterface == storePuerto.data.items[j].data.idInterface){
                                    
                                    if (acumulador == ''){
                                        acumulador = combo.valueModels[i].data.idInterface;
                                    }else{
                                        acumulador = acumulador + ', ' + combo.valueModels[i].data.idInterface;
                                    }
                                    break;
        
                                }

                            }

                        }

                        nombreOlt = Ext.getCmp('oltSeleccionados').getValue();
                        storeCaja.proxy.extraParams = {nombreOlt: nombreOlt, idPuerto: acumulador};
                        storeCaja.load({params: {}});

                        obtenerClientesAfectadosIndisponibilidad(acumulador, '', '');
                    } 

                } 
                
            }
        }
    });
    /*** combo puerto multiple ***/

    /*** combo caja multiple ***/
    storeCaja = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getElementosContenedoresPorPuerto,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        sorters: [{
             property : 'nombreCaja',
             direction: 'ASC'
        }],
        fields: [
            {name: 'idCaja', mapping: 'idCaja'},
            {name: 'nombreCaja', mapping: 'nombreCaja'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount3', {
        alias: 'plugin.selectedCount3',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;
                    }
                }
            })
        }
    });

    comboCaja = Ext.create('Ext.form.ComboBox', {
        id           : 'comboCaja',
        store        :  storeCaja,
        displayField : 'idCaja',
        valueField   : 'nombreCaja',
        fieldLabel   : 'Caja Elemento',
        width        :  390,
        queryMode    : "remote",
        plugins      : ['selectedCount3'],
        disabled     : false,
        editable     : true,
        multiSelect  : true,
        hidden       : true,
        displayTpl   : '<tpl for=".">{nombreCaja}<tpl if="xindex < xcount">,</tpl></tpl>',
        listConfig   : {
            itemTpl: '{nombreCaja} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            change: function(combo, records, eOpts) {

                var acumuladorPuerto = '';  
                var acumuladorCaja = '';  
                var i
                var j;

                if(comboPuertoC.valueModels != null){

                    if(comboPuertoC.getRawValue() != 'NO APLICA'){
                    
                        for (i = 0; i<comboPuertoC.valueModels.length; i++){

                            for (j = 0; j<storePuerto.data.items.length; j++){

                                if (comboPuertoC.valueModels[i].data.idInterface == storePuerto.data.items[j].data.idInterface){
                                    
                                    if (acumuladorPuerto == ''){
                                        acumuladorPuerto = comboPuertoC.valueModels[i].data.idInterface;
                                    }else{
                                        acumuladorPuerto = acumuladorPuerto + ', ' + comboPuertoC.valueModels[i].data.idInterface;
                                    }
                                    break;
        
                                }

                            }

                        }



                        if(comboCaja.valueModels != null){
                        
                            for (i = 0; i<comboCaja.valueModels.length; i++){
        
                                for (j = 0; j<storeCaja.data.items.length; j++){
        
                                    if (comboCaja.valueModels[i].data.idCaja == storeCaja.data.items[j].data.idCaja){
                                        
                                        if (acumuladorCaja == ''){
                                            acumuladorCaja = comboCaja.valueModels[i].data.idCaja;
                                        }else{
                                            acumuladorCaja = acumuladorCaja + ', ' + comboCaja.valueModels[i].data.idCaja;
                                        }
                                        break;
            
                                    }
        
                                }
        
                            }
                        }

                        nombreOlt = Ext.getCmp('oltSeleccionados').getValue();
                        storeSplitter.proxy.extraParams = {nombreOlt: nombreOlt, idPuerto: acumuladorPuerto, idCaja: acumuladorCaja};
                        storeSplitter.load({params: {}});

                        obtenerClientesAfectadosIndisponibilidad(acumuladorPuerto, acumuladorCaja, '');

                    } 
                } 
                
            }
        }
    });
    /*** combo caja multiple ***/
    
    /*** combo splitter multiple ***/
    storeSplitter = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_getElementosConectorPorElementoContenedor,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        sorters: [{
             property : 'nombreSplitter',
             direction: 'ASC'
        }],
        fields: [
            {name: 'idSplitter', mapping: 'idSplitter'},
            {name: 'nombreSplitter', mapping: 'nombreSplitter'}
        ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount4', {
        alias: 'plugin.selectedCount4',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombre_estado_tarea === 'Todos') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;
                    }
                }
            })
        }
    });

    comboSplitter = Ext.create('Ext.form.ComboBox', {
        id           : 'comboSplitter',
        store        :  storeSplitter,
        displayField : 'idSplitter',
        valueField   : 'nombreSplitter',
        fieldLabel   : 'Splitter Elemento',
        width        :  390,
        queryMode    : "remote",
        plugins      : ['selectedCount4'],
        disabled     : false,
        editable     : true,
        multiSelect  : true,
        hidden       : true,
        displayTpl   : '<tpl for="."> {nombreSplitter} <tpl if="xindex < xcount">, </tpl> </tpl>',
        listConfig   : {
            itemTpl: '{nombreSplitter} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            change: function(combo, records, eOpts) {

                var acumuladorSplitter = '';
                var acumuladorPuerto = '';  
                var acumuladorCaja = ''; 
                var i;
                var j;

                if(combo.valueModels != null){
                    
                    for (i = 0; i<combo.valueModels.length; i++){

                        for (j = 0; j<storeSplitter.data.items.length; j++){

                            if (combo.valueModels[i].data.idSplitter == storeSplitter.data.items[j].data.idSplitter){
                                
                                if (acumuladorSplitter == ''){
                                    acumuladorSplitter = combo.valueModels[i].data.idSplitter;
                                }else{
                                    acumuladorSplitter = acumuladorSplitter + ', ' + combo.valueModels[i].data.idSplitter;
                                }
                                break;
    
                            }

                        }

                    }


                    for (i = 0; i<comboPuertoC.valueModels.length; i++){

                        for (j = 0; j<storePuerto.data.items.length; j++){

                            if (comboPuertoC.valueModels[i].data.idInterface == storePuerto.data.items[j].data.idInterface){
                                
                                if (acumuladorPuerto == ''){
                                    acumuladorPuerto = comboPuertoC.valueModels[i].data.idInterface;
                                }else{
                                    acumuladorPuerto = acumuladorPuerto + ', ' + comboPuertoC.valueModels[i].data.idInterface;
                                }
                                break;
    
                            }

                        }

                    }



                    if(comboCaja.valueModels != null){
                    
                        for (i = 0; i<comboCaja.valueModels.length; i++){
    
                            for (j = 0; j<storeCaja.data.items.length; j++){
    
                                if (comboCaja.valueModels[i].data.idCaja == storeCaja.data.items[j].data.idCaja){
                                    
                                    if (acumuladorCaja == ''){
                                        acumuladorCaja = comboCaja.valueModels[i].data.idCaja;
                                    }else{
                                        acumuladorCaja = acumuladorCaja + ', ' + comboCaja.valueModels[i].data.idCaja;
                                    }
                                    break;
        
                                }
    
                            }
    
                        }
                    }

                    obtenerClientesAfectadosIndisponibilidad(acumuladorPuerto, acumuladorCaja, acumuladorSplitter);

                } 
                
            }
        }
    });
    /*** combo splitter multiple ***/

    /*** combo responsable ***/
    comboResponsableStore = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        limit:1000,
        proxy: {
        type: 'ajax',
            url : url_empresaIndisponibilidadTarea,
            reader:
        {
        type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        }
        },
        fields:
        [
        {name:'codigo', mapping:'valor1'},
        {name:'descripcion', mapping:'valor2'}
        ]
    });
        
    comboResponsable = Ext.create('Ext.form.ComboBox', {
        id:'comboResponsable',
        store: comboResponsableStore,
        displayField: 'descripcion',
        valueField: 'codigo',
        height:30,
        width:200,
        border:0,
        margin:0,
        fieldLabel: 'Responsable del problema',
        queryMode: "remote",
        emptyText: '',
        hidden: true,
        editable: false
    });
    /*** combo responsable ***/


    /*** combo tarea ***/
    comboTareaStore2 = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        limit:1000,
        proxy: {
            type: 'ajax',
            url : url_gridTarea,
            reader:
        {
            type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        },
            extraParams:
        {
            nombre: '',
            estado: 'Activo',
            visible: 'SI',
            caso:data.id_caso
        }
        },
        fields:
        [
        {name:'id_tarea', mapping:'id_tarea'},
        {name:'nombre_tarea', mapping:'nombre_tarea'}
        ]
    });
        
    comboTarea2 = Ext.create('Ext.form.ComboBox', {
        id:'comboTarea2',
        store: comboTareaStore2,
        displayField: 'nombre_tarea',
        valueField: 'id_tarea',
        height:30,
        width:390,
        border:0,
        margin:0,
        fieldLabel: 'Tarea Inicial',
        queryMode: "remote",
        emptyText: '',
        editable: true
    });
    Ext.getCmp('comboTarea2').setRawValue('');
    /*** combo tarea ***/

    /*** combo indisponibilidad ***/   
    comboIndisponibilidadStore = Ext.create('Ext.data.Store', {
        fields: ['codigo', 'descripcion'],
        data : [
            {"codigo":"NO", "descripcion":"NO"},
            {"codigo":"SI", "descripcion":"SI"}
        ]
    });
    
    comboIndisponibilidad = Ext.create('Ext.form.ComboBox', {
        id:'comboIndisponibilidad',
        store: comboIndisponibilidadStore,
        displayField: 'descripcion',
        valueField: 'codigo',
        height:30,
        width:200,
        border:0,
        margin:0,
        fieldLabel: 'Indisponibilidad',
        queryMode: "remote",
        emptyText: '',
        editable: false,
        listeners: {
            select: function(combo, records, eOpts) {
                
                var cmbIndisponibilidad = records[0].get('descripcion');
                
                if (cmbIndisponibilidad == 'SI'){
                    setVisibleIndisponibilidad(true);
                }else{
                    setVisibleIndisponibilidad(false);
                }
            }
        }
    });
    Ext.getCmp('comboIndisponibilidad').value = "NO";
    Ext.getCmp('comboIndisponibilidad').setRawValue('NO');
    /*** combo indisponibilidad ***/   

    /*** combo masivo ***/   
    comboMasivoStore = Ext.create('Ext.data.Store', {
        fields: ['codigo', 'descripcion'],
        data : [
            {"codigo":"SI", "descripcion":"SI"},
            {"codigo":"NO", "descripcion":"NO"}
        ]
    });
    
    comboMasivo = Ext.create('Ext.form.ComboBox', {
        id:'comboMasivo',
        store: comboMasivoStore,
        displayField: 'descripcion',
        valueField: 'codigo',
        height:30,
        width:200,
        border:0,
        margin:0,
        fieldLabel: 'Masivo',
        //labelStyle: 'width:600px',
        queryMode: "remote",
        emptyText: '',
        hidden: true,
        editable: false,
        listeners: {
            select: function(combo, records, eOpts) {
                
                var cmbVisible = records[0].get('descripcion');
                
                if (cmbVisible == 'SI'){
                    setVisibleMasivo(true);
                }else{
                    setVisibleMasivo(false);
                }
            }
        }
    });
    Ext.getCmp('comboMasivo').value = "NO";
    Ext.getCmp('comboMasivo').setRawValue('NO');
    /*** combo masivo ***/


    /*** combo hipotesis ***/
    var objStoreArbolHipotesisIndisponibilidad = 
	{ 
        total: 'total',
		proxy: {
            type  : 'ajax',
            method: 'post',
			url   : url_admiHipotesisArbolGrid,
			reader: {
				type          : 'json',
				totalProperty : 'total',
				root          : 'encontrados'
			},
			extraParams: {
					nombre: '',
					estado: 'Activo',
					caso  : data.id_caso,
					start : 0,
					limit : 999
			}
		},
		fields:
		[
		    {name:'id_hipotesis', mapping:'id_hipotesis'},
			{name:'nombre_hipotesis', mapping:'nombre_hipotesis'},
			{name:'descripcion_hipotesis', mapping:'descripcion_hipotesis'},
		]
    };
    
    storeArbolHipotesisNivel1Indisponibilidad = new Ext.data.Store(objStoreArbolHipotesisIndisponibilidad);
	storeArbolHipotesisNivel1Indisponibilidad.proxy.extraParams = {padreHipotesis: '0',estado:'Activo'};
	storeArbolHipotesisNivel2Indisponibilidad = new Ext.data.Store(objStoreArbolHipotesisIndisponibilidad);
	storeArbolHipotesisNivel3Indisponibilidad = new Ext.data.Store(objStoreArbolHipotesisIndisponibilidad);

    comboArbolHipotesisNivel1Indisponibilidad = Ext.create('Ext.form.ComboBox', {
	   id:'comboArbolHipotesisNivel1Indisponibilidad',
	   store: storeArbolHipotesisNivel1Indisponibilidad,
	   displayField: 'nombre_hipotesis',
	   valueField: 'id_hipotesis',
       height:30,
       width:390,
	   border:0,
	   margin:0,
	   fieldLabel: 'Nivel1:',	
       queryMode: "remote",
       emptyText: '',
       listConfig: {
		getInnerTpl: function() {
			return '<div data-qtip="{descripcion_hipotesis}">{nombre_hipotesis} </div>';
		}
	   },
	   listeners:{
		   select: function(combo)
		   {
			Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').value = "";
			Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').setRawValue("");
		    Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').reset();
			Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').setDisabled(false);
			Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').value = "";
			Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setRawValue("");
		    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').reset();
            Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setDisabled(true);
            
            storeArbolHipotesisNivel2Indisponibilidad.proxy.extraParams = {padreHipotesis: combo.getValue(), estado:'Activo'};
            storeArbolHipotesisNivel2Indisponibilidad.load();
            
           },
           change: function(combo, records, eOpts) {

                //if(intHipotesisId3 > 0){
                    Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').value = "";
                    Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').setRawValue("");
                    Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').reset();
                    Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').setDisabled(false);
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').value = "";
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setRawValue("");
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').reset();
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setDisabled(true);
                    
                    storeArbolHipotesisNivel2Indisponibilidad.proxy.extraParams = {padreHipotesis: combo.getValue(), estado:'Activo'};
                    storeArbolHipotesisNivel2Indisponibilidad.load();
                    Ext.getCmp('comboArbolHipotesisNivel2Indisponibilidad').setValue(intHipotesisId2);
                //}
           }
       },
       forceSelection: true
    });
    comboArbolHipotesisNivel2Indisponibilidad = Ext.create('Ext.form.ComboBox', {
		id:'comboArbolHipotesisNivel2Indisponibilidad',
		store: storeArbolHipotesisNivel2Indisponibilidad,
		displayField: 'nombre_hipotesis',
		valueField: 'id_hipotesis',
        height:30,
        width:390,
		border:0,
		margin:0,
		fieldLabel: 'Nivel2:',	
		queryMode: "remote",
        emptyText: '',
        disabled: true,
		listConfig: {
			getInnerTpl: function() {
				return '<div data-qtip="{descripcion_hipotesis}">{nombre_hipotesis} </div>';
			}
	    },
		listeners:{
			select: function(combo)
			{
			 Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').value = "";
			 Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setRawValue("");
			 Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').reset();
			 Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setDisabled(false);
			 storeArbolHipotesisNivel3Indisponibilidad.proxy.extraParams = {padreHipotesis: combo.getValue(),estado:'Activo'};
             storeArbolHipotesisNivel3Indisponibilidad.load();
             
			},
            change: function(combo, records, eOpts) {

                //if(intHipotesisId3 > 0){
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').value = "";
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setRawValue("");
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').reset();
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setDisabled(false);
                
                    storeArbolHipotesisNivel3Indisponibilidad.proxy.extraParams = {padreHipotesis: combo.getValue(), estado:'Activo'};
                    storeArbolHipotesisNivel3Indisponibilidad.load();
                    Ext.getCmp('comboArbolHipotesisNivel3Indisponibilidad').setValue(intHipotesisId3);

                    /*intHipotesisId1 = 0;
                    strHipotesisNombre1 = '';

                    intHipotesisId2 = 0;
                    strHipotesisNombre2 = '';

                    intHipotesisId3 = 0;
                    strHipotesisNombre3 = '';*/
                //}

            }
        },
        forceSelection: true
	 });
	 comboArbolHipotesisNivel3Indisponibilidad = Ext.create('Ext.form.ComboBox', {
		id:'comboArbolHipotesisNivel3Indisponibilidad',
		store: storeArbolHipotesisNivel3Indisponibilidad,
		displayField: 'nombre_hipotesis',
		valueField: 'id_hipotesis',
        height:30,
        width:390,
		border:0,
		margin:0,
		fieldLabel: 'Nivel3:',	
		queryMode: "remote",
        emptyText: '',
		disabled: true,
		listConfig: {
            getInnerTpl: function() {
				return '<div data-qtip="{descripcion_hipotesis}">{nombre_hipotesis} </div>';
			}
        },
        listeners: 
        {
            afterRender: function(combo) {

                //if(intHipotesisId3 > 0){
                    storeArbolHipotesisNivel1Indisponibilidad.load(function() {

                        Ext.getCmp('comboArbolHipotesisNivel1Indisponibilidad').setValue(intHipotesisId1);
                        
                    });
                //}
            }
        },
        forceSelection: true
     });
     /*** combo hipotesis ***/



    btnCerrar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            Ext.getCmp('winIndisponibilidadCaso').setVisible(false);
        }
    });

    btnLimpiar = Ext.create('Ext.Button', {
        text: 'Limpiar',
        cls: 'x-btn-rigth',
        handler: function() {

            Ext.getCmp('oltSeleccionados').setValue('');
            
            storePuerto.removeAll();
            storePuerto.proxy.extraParams = {};
            storePuerto.load();
            comboPuertoC.setValue('');

            storeCaja.removeAll();
            storeCaja.proxy.extraParams = {};
            storeCaja.load();
            comboCaja.setValue('');

            storeSplitter.removeAll();
            storeSplitter.proxy.extraParams = {};
            storeSplitter.load();
            comboSplitter.setValue('');

        }
    });


    formPanelIndisponibilidad = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        height: 615,
        width: 440,
        layout: 'fit',
        autoScroll: true,
        fieldDefaults: {
        labelAlign: 'left',
            msgTarget: 'side'
        },
        items:
        [
        {
            xtype: 'fieldset',
            title: 'Información',
            defaultType: 'textfield',
            items:
            [
                comboArbolHipotesisNivel1Indisponibilidad,
                comboArbolHipotesisNivel2Indisponibilidad,
                comboArbolHipotesisNivel3Indisponibilidad,
                comboIndisponibilidad,
                {
                    xtype: 'numberfield',
                    fieldLabel: 'Tiempo de afectación:',
                    id: 'tiempoAfectacion',
                    name: 'tiempoAfectacion',
                    hidden: true,
                    width: 200,
                    minValue: 0,
                    allowNegative: false,
                    allowPureDecimal: true
                    //value: data.fechaEjecucion
                },
                actualizarTiempoAfectacion,
                comboMasivo,
                comboOlt,
                {
                    xtype: 'textfield',
                    fieldLabel: '',
                    id: 'oltSeleccionados',
                    name: 'oltSeleccionados',
                    hidden: true,
                    disabled : true,
                    width: 390,
                    listeners : {
                        change : function (txt, newValue,oldValue){
                            
                            nombreOlt = Ext.getCmp('oltSeleccionados').getValue();
                            storePuerto.removeAll();
                            storeCaja.removeAll();
                            storeSplitter.removeAll();

                            // activa/desactiva combos puerto, caja, splitter
                            validarSeleccionOlt(nombreOlt, storePuerto);
                        }
                    }
                },
                /*{
                    xtype: 'textfield',
                    fieldLabel: 'Olt Value',
                    id: 'oltValue',
                    name: 'oltValue',
                    hidden: true,
                    width: 390
                    
                },*/
                comboPuertoC,
                comboCaja,
                comboSplitter,
                {
                    xtype: 'numberfield',
                    fieldLabel: 'Clientes afectados',
                    id: 'clientesAfectados',
                    name: 'clientesAfectados',
                    hidden: true,
                    width: 200,
                    minValue: 0,
                    allowNegative: false,
                    allowPureDecimal: true
                    //value:data.duracionMinutos
                },
                comboResponsable,
                {
                    xtype: 'textarea',
                    fieldLabel: 'Observaciones:',
                    id: 'observaciones',
                    name: 'observaciones',
                    maxLength: 500,
                    enforceMaxLength: true,
                    enableKeyEvents: true,
                    rows: 5,
                    cols: 160,
                    hidden: true
                }
            ]
        }
        ]
    });
    
    winIndisponibilidadCaso = Ext.create('Ext.window.Window', {
        id: 'winIndisponibilidadCaso',
        title: 'Indisponibilidad',
        modal: true,
        width: 440,
        height: 620,
        resizable: true,
        layout: 'fit',
        items: [formPanelIndisponibilidad],
        buttonAlign: 'center',
        buttons:[btnLimpiar, btnCerrar],
        closable: false
    }).show();

    Ext.getCmp('winIndisponibilidadCaso').setVisible(false);

}


function setVisibleIndisponibilidad(boolean){

    Ext.getCmp('tiempoAfectacion').setVisible(boolean);
    Ext.getCmp('comboMasivo').setVisible(boolean);
    Ext.getCmp('actualizarTiempoAfectacion').setVisible(boolean);

    if (!boolean){
        setVisibleMasivo(boolean);
        //Ext.getCmp('comboMotivoFinaliza').value = "NO";
        Ext.getCmp('comboMasivo').setRawValue("NO");
    }

    /*Ext.getCmp('comboMotivoFinaliza').value = "";
    Ext.getCmp('comboMotivoFinaliza').setRawValue("");
    Ext.getCmp('comboMotivoFinaliza').reset();*/
}

function setVisibleMasivo(boolean){

    Ext.getCmp('clientesAfectados').setVisible(boolean);
    Ext.getCmp('comboResponsable').setVisible(boolean);
    Ext.getCmp('observaciones').setVisible(boolean);
    Ext.getCmp('comboOlt').setVisible(boolean);
    //Ext.getCmp('oltValue').setVisible(boolean);
    Ext.getCmp('oltSeleccionados').setVisible(boolean);
    Ext.getCmp('comboPuertoC').setVisible(boolean);
    Ext.getCmp('comboCaja').setVisible(boolean);
    Ext.getCmp('comboSplitter').setVisible(boolean);

}


function cerrarCasoTN(data,abd)
{
    var id_caso          = data.id_caso;
    var numero_caso     = data.numero_caso;
    var fecha_apertura  = data.fecha_apertura;
    var hora_apertura   = data.hora_apertura;
    var tieneTareas      = data.tiene_tareas;
    var hipotesisIniciales = data.hipotesisIniciales;
    var tipoAfectacion   = data.afectacion;
    var boolPermisoVerSeguimientosCerrarCaso = data.boolPermisoVerSeguimientosCerrarCaso;
    var tiempoTotalCaso   = data.tiempoTotalCaso;
    var nuevoEsquema = data.nuevoEsquema;
    var boolDetTarea = comparaFechas(fecha_apertura, (typeof fechaDetTareas === 'undefined')? null : fechaDetTareas);
    
	var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });		   	  	  	    	 	

    storeDetallesTareasCerrarCasoTN = new Ext.data.JsonStore(
    {
        pageSize: 200,
        autoLoad: true,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : url_detalles_tareas_TN,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                id_caso: id_caso
            }
        },
        fields:
        [
            {name:'id_detalle', mapping:'id_detalle'},
            {name:'id_tarea', mapping:'id_tarea'},
            {name:'nombre_tarea', mapping:'nombre_tarea'},
            {name:'estado', mapping:'estado'},
            {name:'asignado_nombre', mapping:'asignado_nombre'},
            {name:'esSolucion', mapping:'esSolucion'},
            {name:'es_solucion_TN', mapping:'es_solucion_TN'},
            {name:'hereda_version_final_TN', mapping:'hereda_version_final_TN'},
            {name:'observacion_detalle', mapping:'observacion_detalle'},
            {name:'action1', mapping:'action1'},
            {name:'numero_tarea', mapping:'numero_tarea'},
            
        ]                
    });
                
    cellEditingTareasCerrarCasoTN = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            edit: function() {
                gridDetallesTareasCerrarCasoTN.getView().refresh();
            }
        }
    });    

    let plugins = [];
    let esTn = (typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim() == 'TN');

    if (esTn && boolDetTarea) {

        cellExpandingDetalleTarea = {
            ptype: 'rowexpander',
            pluginId: 'rowexpanderDetallesTarea',
            selectRowOnExpand: true,
            rowBodyTpl: [
                '</div><div id="infoDetalleTarea-{id_detalle}" ></div>'
            ],
            toggleRow: function(rowIdx)
            {
                var rowNode = this.view.getNode(rowIdx),
                row = Ext.get(rowNode),
                nextBd = Ext.get(row).down(this.rowBodyTrSelector),
                hiddenCls = this.rowBodyHiddenCls,
                record = this.view.getRecord(rowNode);
                var idDetalle = record.get('id_detalle'),
                targetId = 'infoDetalleTarea-' + idDetalle

                if (row.hasCls(this.rowCollapsedCls))
                {
                    row.removeCls(this.rowCollapsedCls)
                    this.recordsExpanded[record.internalId] = true
                    this.view.fireEvent('expandbody', rowNode, record, nextBd.dom)

                    if (rowNode.gridDetalleTarea) 
                    {
                        nextBd.removeCls(hiddenCls)
                    } 
                    else 
                    {
                        nextBd.removeCls(hiddenCls)
                        Ext.define('detalleTarea', {
                            extend: 'Ext.data.Model',
                            fields: [
                                { name: 'idDetalleHist', type: 'int' },
                                { name: 'motivoFinTarea', type: 'string' },
                                { name: 'nombreTarea', type: 'string' },
                                { name: 'nombreDepartamento', type: 'string' },
                                { name: 'esSolucion', mapping:'esSolucion' },
                                { name: 'es_solucion_TN_det', mapping:'es_solucion_TN_det' },
                                { name: 'hereda_version_final_TN_det', mapping:'hereda_version_final_TN_det' },
                                { name: 'detalleId', mapping:'detalleId' },
                            ],
                        })

                        var storeDetalleTarea = Ext.create(
                            'Ext.data.JsonStore',
                            {
                                model: 'detalleTarea',
                                pageSize: 6,
                                autoLoad: true,
                                proxy: {
                                    timeout: 400000,
                                    type: 'ajax',
                                    url: url_getDetalleTarea,
                                    reader: {
                                        type: 'json',
                                        root: 'servicios',
                                        totalProperty: 'total',
                                    },
                                    actionMethods: {
                                        create: 'POST',
                                        read: 'POST',
                                        update: 'POST',
                                        destroy: 'POST',
                                    },
                                    extraParams: {
                                        idDetalle: idDetalle,
                                    },
                                    simpleSortMode: true,
                                },
                            },
                        )

                        gridDetalleTarea = Ext.create('Ext.grid.Panel',{
                            id: 'gridDetalleTarea-' + idDetalle,
                            name: 'gridDetalleTarea-' + idDetalle,
                            collapsible: false,
                            autoScroll: true,
                            title: 'Detalle Tarea',
                            renderTo: targetId,
                            store: storeDetalleTarea,
                            cls: (esTn && boolDetTarea) ? 'custom-grid-detail-tarea' : '',
                            dockedItems: [
                                {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        { xtype: 'tbfill' },
                                    ],
                                },
                            ],
                            viewConfig: {
                                emptyText: 'No hay datos para mostrar',
                            },
                            listeners: {
                                itemdblclick: function ( view, record, item, index, eventobj, obj) {
                                    
                                    var position = view.getPositionByEvent(eventobj),
                                    data = record.data,
                                    value = data[this.columns[position.column].dataIndex]

                                    Ext.Msg.show({
                                        title: 'Copiar texto?',
                                        msg:
                                        'Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>' +
                                        value +
                                        '</b>',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.Msg.INFORMATION,
                                    })
                                }
                            },
                            columns: [
                                new Ext.grid.RowNumberer(),
                                {
                                    id: 'idDetalleTarea-' + idDetalle,
                                    header: 'idDetalleTarea',
                                    dataIndex: 'idDetalleTarea',
                                    hidden: true,
                                    hideable: false,
                                },
                                {
                                    id: 'idDetalleHist-' + idDetalle,
                                    dataIndex: 'idDetalleHist',
                                    hidden: true,
                                    hideable: false,
                                },
                                // {
                                //     id: 'detalleId-' + idDetalle,
                                //     header: 'detalleId',
                                //     dataIndex: 'detalleId',
                                //     //hidden: true,
                                //     //hideable: false,
                                // },
                                {
                                    text: 'Nombre Tarea',
                                    width: 250,
                                    dataIndex: 'nombreTarea',
                                    id: 'nombreTarea-' + idDetalle,
                                },
                                {
                                    text: 'Motivo',
                                    width: 250,
                                    dataIndex: 'motivoFinTarea',
                                    id: 'motivoFinTarea-' + idDetalle,
                                },
                                {
                                    text: 'Departamento',
                                    width: 130,
                                    dataIndex: 'nombreDepartamento',
                                    id: 'nombreDepartamento-' + idDetalle,
                                },
                                {
                                    header: 'Es solución', 
                                    width: 100,
                                    align: 'center',
                                    renderer: function(value, metaData, record, rowIdx, colIdx, store) {
                                        return "<input type='checkbox' name = 'inputEsSolucionTNDet' " 
                                                + (record.data.es_solucion_TN_det==1 ? "checked='checked'" : "") 
                                                + " onchange='setEsSolucionParcialDet("+rowIdx+"," + idDetalle + ");'>";
                                    }

                                },
                                {
                                    header: 'Hereda Versión Final', 
                                    width: 123,
                                    align: 'center',
                                    renderer: function(value, metaData, record, rowIdx, colIdx, store) {
                                        return "<input type='checkbox' name='inputHeredaVersionFinalTNDet' "
                                                +(record.data.es_solucion_TN_det==1 ? "" : "disabled='true'") 
                                                +(record.data.hereda_version_final_TN_det==1 ? "checked='checked'" : "") 
                                                +" onchange='setHeredaVersionFinalDet("+rowIdx+"," + idDetalle + ");'>";
                                    }

                                },
                            ]
                        });
                        rowNode.gridDetalleTarea = gridDetalleTarea
                    }
                }
                else
                {
                    row.addCls(this.rowCollapsedCls)
                    nextBd.addCls(this.rowBodyHiddenCls)
                    this.recordsExpanded[record.internalId] = false
                    this.view.fireEvent('collapsebody', rowNode, record, nextBd.dom)
                }

            }
        };

        plugins = [
            cellEditingTareasCerrarCasoTN,
            cellExpandingDetalleTarea
        ];
    }else{
        plugins = [
            cellEditingTareasCerrarCasoTN
        ]
    }

    storeSoluciones = new Ext.data.JsonStore(
    {
        pageSize: 200,
        autoLoad: true,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : ulr_revisar_soluciones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                id_caso : id_caso
            }
        },
        fields:
        [
            {name:'idDetalle', mapping:'idDetalle'},
            {name:'nombreTarea', mapping:'nombreTarea'},
            {name:'esSolucion', mapping:'esSolucion'},
            {name:'idDetalleHist', mapping:'idDetalleHist'},
            
        ]                
    });
    

    gridDetallesTareasCerrarCasoTN = Ext.create('Ext.grid.Panel', {
        width: 1250,
        height: 400,
        sortableColumns:false,
        store: storeDetallesTareasCerrarCasoTN,
        plugins: plugins,
        cls: (esTn && boolDetTarea) ? 'custom-grid-detail' : '',
        viewConfig: { enableTextSelection: true }, 
            id:'gridTareasCerrarCaso',
            enableColumnResize :false,
            loadMask: true,
            frame:true,
            listeners: 
            {
                viewready: function (grid)
                {
                    var view = grid.view;

                    grid.mon(view,
                    {
                        uievent: function (type, view, cell, recordIndex, cellIndex, e)
                        {
                            grid.cellIndex   = cellIndex;
                            grid.recordIndex = recordIndex;
                        }
                    });

                    grid.tip = Ext.create('Ext.tip.ToolTip',
                    {
                        id: 'tooltip1',
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        renderTo: Ext.getBody(),
                        listeners:
                        {
                            beforeshow: function(tip)
                            {
                                var trigger, parent, columnDataIndex, columnText;
                                if (esTn && boolDetTarea) {
                                    trigger         = tip.triggerElement;
                                    parent          = tip.triggerElement.parentElement;
                                    columnDataIndex = view.getHeaderByCell(trigger).dataIndex;
                                    columnText = (view.getRecord(parent) !== undefined) ? view.getRecord(parent).get(columnDataIndex).toString() : trigger.innerText;
                                    if (columnText && columnText.trim().length != 0)
                                    {
                                        tip.update(columnText);
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                } else {
                                    if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                    {
                                        header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                        if( header.dataIndex != null )
                                        {
                                            trigger         = tip.triggerElement;
                                            parent          = tip.triggerElement.parentElement;
                                            columnDataIndex = view.getHeaderByCell(trigger).dataIndex;
                                            columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                            if (columnText)
                                            {
                                                tip.update(columnText);
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }

                                }


                                
                            }
                        }
                    });
                }
            },
            columns: [
                {
                    id: 'id_punto_grid_avanzada',
                    header: 'id_punto_grid_avanzada',
                    dataIndex: 'id_punto_grid_avanzada',
                    hidden: true,
                    hideable: false,
                },
                {
                    xtype : 'rownumberer',
                    hidden: (esTn && boolDetTarea),
                },
                {
                    id: 'detalle_numero_tarea_TN',
                    header: 'Numero Tarea',
                    hidden: !(esTn && boolDetTarea),
                    dataIndex: 'numero_tarea',
                    width: 80,
                },
                {
                    id: 'detalle_nombre_tarea_TN',
                    header: 'Tarea',
                    dataIndex: 'nombre_tarea',
                    width: (esTn && boolDetTarea) ? 340 : 300,
                },
                {
                    id: 'asignado_nombre_TN',
                    header: 'Asignado',
                    dataIndex: 'asignado_nombre',
                    width:150
                },
                {
                    id: 'observacion_detalle_TN',
                    header: 'Observación',
                    dataIndex: 'observacion_detalle',
                    width: (esTn && boolDetTarea) ? 250 : 150,
                },
                {
                    header: 'Es solución', 
                    width: 100,
                    align: 'center',
                    hidden: (esTn && boolDetTarea),
                    renderer: function(value, metaData, record, rowIdx, colIdx, store) {
                        return "<input type='checkbox' name = 'inputEsSolucionTN' " 
                                + (record.data.es_solucion_TN==1 ? "checked='checked'" : "") 
                                + " onchange='setEsSolucionTN("+rowIdx+");'>";
                    }

                },
                {
                    header: 'Hereda Versión Final',
                    align: 'center',
                    width: 120,
                    hidden: (esTn && boolDetTarea),
                    renderer: function(value, metaData, record, rowIdx, colIdx, store) 
                    {
                        return "<input type='radio' name='inputHeredaVersionFinalTN' "
                                +(record.data.es_solucion_TN==1 ? "" : "disabled='true'") 
                                +(record.data.hereda_version_final_TN==1 ? "checked='checked'" : "") 
                                +" onchange='setHeredaVersionFinalTN("+rowIdx+");'>";
                    }
                },
                {
                    header: 'Acciones',
                    xtype: 'actioncolumn',
                    width: 60,
                    sortable: false,
                    items:
                        [
                            {
                                getClass: function(v, meta, rec) {
                                    if (!boolPermisoVerSeguimientosCerrarCaso) {
                                        rec.data.action1 = "icon-invisible";
                                    }

                                    if (rec.get('action1') == "icon-invisible")
                                        this.items[0].tooltip = '';
                                    else
                                        this.items[0].tooltip = 'Ver Seguimiento';

                                    return rec.get('action1');
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storeDetallesTareasCerrarCasoTN.getAt(rowIndex);

                                    if (!boolPermisoVerSeguimientosCerrarCaso) {
                                        rec.data.action1 = "icon-invisible";
                                    }

                                    if (rec.get('action1') != "icon-invisible")
                                        verSeguimientoTareaCerrarCasoTN(rec.data.id_detalle);
                                    else
                                        Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                            }
                        ]
                }
                
		],
        buttons:
        [
            {
                text: 'Guardar',
                formBind: true,
                handler: function()
                {
                    // valida empresa 10
                    var boolValidaTareasCerrarCaso;
                    if (esTn && boolDetTarea) {
                        boolValidaTareasCerrarCaso = validarTareasCerrarCasoTNDet(id_caso);
                    }else{
                        boolValidaTareasCerrarCaso = validarTareasCerrarCasoTN();
                    }
                    
                    if (boolValidaTareasCerrarCaso)
                    {
                        var arrayDetalleTareasTN = obtenerTareasCerrarCasoTN();
                        var jsonTareasCerrarCaso = arrayDetalleTareasTN['tareas'];
                        var observacionVersionFinalDet = arrayDetalleTareasTN['observacion'];
                        var solucionesFinal = [];

                        if (esTn && boolDetTarea) {
                            solucionesFinal = solucionesWS;
                        }

                        winCerrarCasoTN.destroy();
                        
                        var conn = new Ext.data.Connection({
                            listeners: {
                                'beforerequest': {
                                    fn: function(con, opt) {
                                        Ext.get(document.body).mask('Actualizando las tareas...');
                                    },
                                    scope: this
                                },
                                'requestcomplete': {
                                    fn: function(con, res, opt) {
                                        Ext.get(document.body).unmask();
                                    },
                                    scope: this
                                },
                                'requestexception': {
                                    fn: function(con, res, opt) {
                                        Ext.get(document.body).unmask();
                                    },
                                    scope: this
                                }
                            }
                        });

                        conn.request({
                            method: 'POST',                                                                                                                                                                          
                            url:url_guardar_tareas_solucion_TN,
                            params:
                            {
                                id_caso             : id_caso,
                                tareasCerrarCasoTN  : jsonTareasCerrarCaso
                            },
                            success: function(response) 
                            {
                                var json = Ext.JSON.decode(response.responseText);                                                                                                

                                if (json.success)
                                {                              
                                    var fechaFinal              = json.fechaFinal;
                                    var horaFinal               = json.horaFinal;
                                    var observacionVersionFinal;
                                    if (esTn && boolDetTarea) {
                                        observacionVersionFinal = observacionVersionFinalDet;
                                    }else{
                                        observacionVersionFinal = json.observacionVersionFinal;
                                    }
                                    
                                    $('#fechaFin').val(fechaFinal);
                                    $('#horaFin').val(horaFinal);

                                    var connCerrarCasoGeneral = new Ext.data.Connection({
                                        listeners: {
                                            'beforerequest': {
                                                fn: function(con, opt) {
                                                    Ext.get(document.body).mask('Obteniendo Fecha y Hora...');
                                                },
                                                scope: this
                                            },
                                            'requestcomplete': {
                                                fn: function(con, res, opt) {
                                                    Ext.get(document.body).unmask();
                                                },
                                                scope: this
                                            },
                                            'requestexception': {
                                                fn: function(con, res, opt) {
                                                    Ext.get(document.body).unmask();
                                                },
                                                scope: this
                                            }
                                        }
                                    });
                                    
                                    connCerrarCasoGeneral.request({
                                        method: 'POST',                                                                                                                                                                          
                                        url:url_obtenerFechaServer,
                                        success: function(response) 
                                        {
                                            var json = Ext.JSON.decode(response.responseText);                                                                                                

                                            if (json.success)
                                            {                              
                                                var data = {
                                                    id_caso       : id_caso,
                                                    numero_caso   : numero_caso,
                                                    fecha_apertura: fecha_apertura,
                                                    hora_apertura : hora_apertura,                    
                                                    tiene_tareas  : tieneTareas,
                                                    fecha_final   : fechaFinal,
                                                    hora_final    : horaFinal,
                                                    hipotesisIniciales : hipotesisIniciales,
                                                    fechaActual   : json.fechaActual,
                                                    horaActual    : json.horaActual,
                                                    observacionVersionFinal : observacionVersionFinal,
                                                    afectacion : tipoAfectacion,
                                                    casoTN        : 1,
                                                    tiempoTotalCaso : tiempoTotalCaso,
                                                    nuevoEsquema    : nuevoEsquema,
                                                    solucionesFinal : solucionesFinal
                                                };
                                                cerrarCaso(data);
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Alerta ', json.error);
                                            }
                                        }
                                    }); 
                                }
                                else
                                {
                                    Ext.Msg.alert('Alerta ', json.mensaje);
                                }
                            }
                        });                      
                    }
                }
            }, 
            {
                text: 'Cancelar',
                handler: function() {
                    if (typeof gridDetallesTareasCerrarCasoTN !== 'undefined') {
                        gridDetallesTareasCerrarCasoTN.tip.destroy();
                    }
                    winCerrarCasoTN.destroy();
                }
            }
        ]

    });
    
    
    winCerrarCasoTN = Ext.create('Ext.window.Window', {
        title: 'Detalle de Tareas',
        modal: true,
        closable: false,
        width: 980,
        layout: 'fit',
        items: [gridDetallesTareasCerrarCasoTN]
    }).show();

}

function setEsSolucionTN(rowIdx)
{
    if(gridDetallesTareasCerrarCasoTN.getStore().data.items[rowIdx].data.es_solucion_TN==1)
    {
        gridDetallesTareasCerrarCasoTN.getStore().data.items[rowIdx].set('es_solucion_TN',0);
        gridDetallesTareasCerrarCasoTN.getStore().data.items[rowIdx].set('hereda_version_final_TN',0);
    }
    else
    {
        gridDetallesTareasCerrarCasoTN.getStore().data.items[rowIdx].set('es_solucion_TN',1);
    }
}

function setEsSolucionParcialDet(rowIdx, idDetalle)
{
    if(Ext.getCmp('gridDetalleTarea-' + idDetalle).getStore().data.items[rowIdx].data.es_solucion_TN_det==1)
    {
        Ext.getCmp('gridDetalleTarea-' + idDetalle).getStore().data.items[rowIdx].set('es_solucion_TN_det',0);
        Ext.getCmp('gridDetalleTarea-' + idDetalle).getStore().data.items[rowIdx].set('hereda_version_final_TN_det',0);
    }
    else
    {
        Ext.getCmp('gridDetalleTarea-' + idDetalle).getStore().data.items[rowIdx].set('es_solucion_TN_det',1);
    }
}

function setHeredaVersionFinalTN(rowIdx)
{
    for(index in gridDetallesTareasCerrarCasoTN.getStore().data.items)
    {
        if(gridDetallesTareasCerrarCasoTN.getStore().data.items[index].data.hereda_version_final_TN==1)
        {
            gridDetallesTareasCerrarCasoTN.getStore().data.items[index].set('hereda_version_final_TN',0);   
        }
    }
    gridDetallesTareasCerrarCasoTN.getStore().data.items[rowIdx].set('hereda_version_final_TN',1);
}

function setHeredaVersionFinalDet(rowIdx, idDetalle)
{
    let item = Ext.getCmp('gridDetalleTarea-' + idDetalle).getStore().data.items[rowIdx];
    if (item.data.hereda_version_final_TN_det==1){
        item.set('hereda_version_final_TN_det', 0)
    }else{
        item.set('hereda_version_final_TN_det', 1)
    }
}

function validarTareasCerrarCasoTN()
{
    var storeValida = Ext.getCmp("gridTareasCerrarCaso").getStore();
    if(storeValida.getCount() > 0)
	{
		var contEsSolucion  = 0;
        var contHeredaVersionFinal = 0;
		for(var i = 0; i < storeValida.getCount(); i++)
		{
			var es_solucion = storeValida.getAt(i).data.es_solucion_TN;
			var hereda_version_final = storeValida.getAt(i).data.hereda_version_final_TN;		
			if(es_solucion==1){
				contEsSolucion++;
			}	
			if(hereda_version_final==1){
				contHeredaVersionFinal++;
            }	  
		}
        console.log('1->'+contEsSolucion);
        if(contEsSolucion==0)
        {
            Ext.Msg.alert("Alerta","Debe seleccionar al menos una tarea como solución.");
            return false;
        }
        
        if(contHeredaVersionFinal==0)
        {
            Ext.Msg.alert("Alerta","Debe seleccionar al menos una tarea que herede la versión final.");
            return false;
        }
		
		return true;						
	}
}

function validarTareasCerrarCasoTNDet(idCaso)
{
    let gridTareas = Ext.getCmp("gridTareasCerrarCaso").getStore();
    let gridDetalleTareas;
    let idDetalle;
    let contEsSolucion = 0,
        contHeredaVersionFinal = 0;
    let es_solucion,
        hereda_version_final,
        detalleId,
        nombreTarea;

    solucionesWS = [];
    storeSoluciones.data.items.forEach(element => {
        if (element.data.esSolucion == 'S') {
            solucionesWS.push(element.data);    
        }
    });

    let ban = false;
    if(gridTareas.getCount() > 0) {
        for (let x = 0; x < gridTareas.getCount(); x++) {
            idDetalle = gridTareas.data.items[x].data.id_detalle
            if (Ext.getCmp("gridDetalleTarea-" + idDetalle) !== undefined) {
                gridDetalleTareas = Ext.getCmp("gridDetalleTarea-" + idDetalle).getStore();
                if (gridDetalleTareas.getCount() > 0) {
                    for (let y = 0; y < gridDetalleTareas.getCount(); y++) {
                        es_solucion = gridDetalleTareas.data.items[y].data.es_solucion_TN_det;
                        hereda_version_final = gridDetalleTareas.data.items[y].data.hereda_version_final_TN_det;
                        detalleId = gridDetalleTareas.data.items[y].data.detalleId;
                        nombreTarea = gridDetalleTareas.data.items[y].data.nombreTarea;
                        idDetalleHist = gridDetalleTareas.data.items[y].data.idDetalleHist;
                        ban = false;
                        solucionesWS.every((item,index,obj) => {
                            if (item.idDetalle == detalleId && item.nombreTarea == nombreTarea && item.idDetalleHist == idDetalleHist) {
                                ban = true;
                                if(es_solucion!=1){
                                    obj.splice(index, 1);
                                    return false;
                                }
                            }
                            return true;
                        })

                        if (!ban && es_solucion==1) {
                            solucionesWS.push({
                                'idDetalle' : detalleId,
                                'nombreTarea' : nombreTarea,
                                'idDetalleHist': idDetalleHist
                            });
                        }

                        if(hereda_version_final==1){
                            contHeredaVersionFinal++;
                        }	  
                    }
                }    
            }
        }
        if(solucionesWS.length == 0) {
            Ext.Msg.alert("Alerta","Debe seleccionar al menos una tarea como solución.");
            return false;
        }
        if(contHeredaVersionFinal==0) {
            Ext.Msg.alert("Alerta","Debe seleccionar al menos una tarea que herede la versión final.");
            return false;
        }
		return true;						
	}
    return false;
}

function obtenerTareasCerrarCasoTN()
{
    var array = new Object();
    array['total'] =  gridDetallesTareasCerrarCasoTN.getStore().getCount();
    array['tareas'] = new Array();
    var array_data = new Array();
    let array_data_tarea = new Array();
    let idDetalle;
    let array_data_detalle_tarea;
    let gridDetalleTareas;
    let observacionFinalDet = "";
    for(var i=0; i < gridDetallesTareasCerrarCasoTN.getStore().getCount(); i++)
    {
        idDetalle = gridDetallesTareasCerrarCasoTN.getStore().getAt(i).data.id_detalle;
        array_data_detalle_tarea = new Array();
        if (Ext.getCmp("gridDetalleTarea-" + idDetalle) !== undefined) {
            gridDetalleTareas = Ext.getCmp("gridDetalleTarea-" + idDetalle).getStore();
            if (gridDetalleTareas.getCount() > 0) {
                for (let x = 0; x < gridDetalleTareas.getCount(); x++) {
                    array_data_detalle_tarea.push(gridDetalleTareas.getAt(x).data);
                    if (gridDetalleTareas.getAt(x).data.hereda_version_final_TN_det == 1) {
                        observacionFinalDet = observacionFinalDet + gridDetalleTareas.getAt(x).data.motivoFinTarea + ' | ';
                    }
                }
            }
        }
        array_data_tarea = gridDetallesTareasCerrarCasoTN.getStore().getAt(i).data;
        array_data_tarea['detalle'] = array_data_detalle_tarea;
        array_data.push(array_data_tarea);
    }
    array['tareas'] = array_data;
    return {
        'tareas'      : Ext.JSON.encode(array),
        'observacion' : observacionFinalDet
    };
}



function verSeguimientoTareaCerrarCasoTN(id_detalle){
      var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
   
    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		      winSeguimientoTarea.destroy();													
            }
    });
    
	storeSeguimientoTarea = new Ext.data.Store({ 
		//pageSize: 1000,
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : url_ver_seguimiento_tarea,
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				id_detalle: id_detalle			
			}
		},
		fields:
		[
		      {name:'id_detalle', mapping:'id_detalle'},
		      {name:'observacion', mapping:'observacion'},
		      {name:'departamento', mapping:'departamento'},
		      {name:'empleado', mapping:'empleado'},
		      {name:'fecha', mapping:'fecha'}					
		]
	});
	gridSeguimiento = Ext.create('Ext.grid.Panel', {
		id:'gridSeguimiento',
		store: storeSeguimientoTarea,		
		columnLines: true,
		columns: [
			{
			      id: 'observacion',
			      header: 'Observación',
			      dataIndex: 'observacion',
			      width:400,
			      sortable: true						 
			},
			  {
			      id: 'empleado',
			      header: 'Ejecutante',
			      dataIndex: 'empleado',
			      width:80,
			      sortable: true						 
			},
			  {
			      id: 'departamento',
			      header: 'Departamento',
			      dataIndex: 'departamento',
			      width:100,
			      sortable: true						 
			},
			  {
			      id: 'fecha',
			      header: 'Fecha Observación',
			      dataIndex: 'fecha',
			      width:120,
			      sortable: true						 
			}
		],		
		width: 700,
		height: 300,
		listeners:{
            itemdblclick: function( view, record, item, index, eventobj, obj ){
                var position = view.getPositionByEvent(eventobj),
                data = record.data,
                value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title:'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners: {
                        beforeshow: function updateTipBody(tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });

            }                                    
        }
	});
	formPanelSeguimiento = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 300,
			width:700,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				//labelWidth: 140,
				msgTarget: 'side'
			},

			items: [{
				xtype: 'fieldset',				
				defaultType: 'textfield',
				items: [					
					gridSeguimiento
				]
			}]
		 });
	winSeguimientoTarea = Ext.create('Ext.window.Window', {
			title: 'Seguimiento Tareas',
			modal: true,
			width: 750,
			height: 400,
			resizable: true,
			layout: 'fit',
			items: [formPanelSeguimiento],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show();       
}



function obtenerTiempoAfectacionIndisponibilidadCaso(data)
{        

   // Ext.getCmp('tiempoAfectacion').setValue(data.id_tarea);
    
    var strIdDetalle = data.id_caso;

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    }); 
    
    
    conn.request({
        url: url_getTiempoAfectacionIndisponibilidadCaso,
        method: 'post',
        params: 
            { 
                strIdDetalle : strIdDetalle
            },
        success: function(response){			
            Ext.getCmp('tiempoAfectacion').setValue(Ext.decode(response.responseText));
          
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });      
      
}



function obtenerClientesAfectadosIndisponibilidad(pIdPuerto, pIdCaja, pIdSplitter)
{        
    
    var nombreOlt = Ext.getCmp('oltSeleccionados').getValue();
    var reemplazar = /, /gi;
    nombreOlt = nombreOlt.replace(reemplazar, "','");

    var idPuerto = pIdPuerto;
    var idCaja = pIdCaja;
    var idSplitter = pIdSplitter;

    var i;
    var j;

    // parametro puerto vacio, obtengo seleccionados
    if(idPuerto == ''){
        
        for (i = 0; i<comboPuertoC.valueModels.length; i++){

            for (j = 0; j<storePuerto.data.items.length; j++){

                if (comboPuertoC.valueModels[i].data.idInterface == storePuerto.data.items[j].data.idInterface){
                    
                    if (idPuerto == ''){
                        idPuerto = comboPuertoC.valueModels[i].data.idInterface;
                    }else{
                        idPuerto = idPuerto + ', ' + comboPuertoC.valueModels[i].data.idInterface;
                    }
                    break;

                }

            }

        }

    }


    // parametro caja vacio, obtengo seleccionados
    if(idCaja == ''){

        if(comboCaja.valueModels != null){

            for (i = 0; i<comboCaja.valueModels.length; i++){

                for (j = 0; j<storeCaja.data.items.length; j++){

                    if (comboCaja.valueModels[i].data.idCaja == storeCaja.data.items[j].data.idCaja){
                        
                        if (idCaja == ''){
                            idCaja = comboCaja.valueModels[i].data.idCaja;
                        }else{
                            idCaja = idCaja + ', ' + comboCaja.valueModels[i].data.idCaja;
                        }
                        break;

                    }

                }

            }
            
        }

    }


    // parametro splitter vacio, obtengo seleccionados
    if(idSplitter == ''){

        if(comboSplitter.valueModels != null){

            for (i = 0; i<comboSplitter.valueModels.length; i++){

                for (j = 0; j<storeSplitter.data.items.length; j++){

                    if (comboSplitter.valueModels[i].data.idSplitter == storeSplitter.data.items[j].data.idSplitter){
                        
                        if (idSplitter == ''){
                            idSplitter = comboSplitter.valueModels[i].data.idSplitter;
                        }else{
                            idSplitter = idSplitter + ', ' + comboSplitter.valueModels[i].data.idSplitter;
                        }
                        break;

                    }

                }

            }
            
        }
    }




    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    }); 
    
    
    conn.request({
        url: url_getClientesAfectados,
        method: 'post',
        params: 
            { 
                nombreOlt : nombreOlt,
                idPuerto : idPuerto,
                idCaja : idCaja,
                idSplitter : idSplitter,
            },
        success: function(response){			
            Ext.getCmp('clientesAfectados').setValue(Ext.decode(response.responseText));
          
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });      
      
}

function validarSeleccionOlt(obj, storePuerto)
{ 

    // si selecciono mas de 1, se bloquean los demas
    if(obj.indexOf(",") != -1){

        comboPuertoC.setValue("NO APLICA");
        comboPuertoC.setRawValue("NO APLICA");
        comboPuertoC.setDisabled(true);

        comboCaja.setValue("NO APLICA");
        comboCaja.setRawValue("NO APLICA");
        comboCaja.setDisabled(true);

        comboSplitter.setValue("NO APLICA");
        comboSplitter.setRawValue("NO APLICA");
        comboSplitter.setDisabled(true);

    // solo un olt
    }else{

        //comboPuertoC.setValue("");
        //comboPuertoC.setRawValue("Seleccione puerto");
        comboPuertoC.setDisabled(false);

        comboCaja.setValue("");
        comboCaja.setRawValue("");
        comboCaja.setDisabled(false);

        comboSplitter.setValue("");
        comboSplitter.setRawValue("");
        comboSplitter.setDisabled(false);

        if(obj.length > 0){
            storePuerto.proxy.extraParams = {nombreOlt: nombreOlt};
            storePuerto.load({params: {}});
        } 

    }

    obtenerClientesAfectadosIndisponibilidad('', '', '');

}

function obtenerArbolHipotesis(data)
{        

    var intCasoId = data.id_caso;

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    }); 
    
    
    conn.request({
        url: url_getArbolHipotesis,
        method: 'post',
        params: 
            { 
                intCasoId : intCasoId
            },
        success: function(response){		

            var objJson = Ext.JSON.decode(response.responseText);  

            intHipotesisId1 = objJson[0].id_1;
            strHipotesisNombre1 = objJson[0].nombre_1;

            intHipotesisId2 = objJson[0].id_2;
            strHipotesisNombre2 = objJson[0].nombre_2;

            intHipotesisId3 = objJson[0].id_3;
            strHipotesisNombre3 = objJson[0].nombre_3;
          
        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });      
      
}