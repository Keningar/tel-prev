/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
/**
* 
* exportarExcel
* 
* Función que permite exportar los registros de busqueda en un excel.
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 10-03-2019
*
*/
function exportarExcel(){             			      
      $('#hestadoIncidencia').val( Ext.getCmp('cmb_estadoIncidencia').value ? Ext.getCmp('cmb_estadoIncidencia').value : '' );
      document.forms[0].submit();	                
}

var storeOpcionesRespuesta = new Ext.data.Store({ 
    total: 'total',
    proxy: {
        type: 'ajax',
        url : url_buscar_opc_seguimiento,
        reader: {
            type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        }
    },
    fields:
    [
        {name:'seguimientoCombo', mapping:'seguimientos'}
    ],
    autoLoad: false
});

/**
* 
* buscarRegistroIncidenciasPorParametros
* 
* Obtiene los registros de incidencias enviadas por ECUCERT con el caso asociado.
* Los parametros de filtro son:
* 
* @param   feEmisionDesde - Fecha de Emisión con el intervalo de inicio 
*           feEmisionHasta - Fecha de Emisión con el interbalo de fin 
*           numeroCaso     - Número de Caso 
*           estadoInci     - Estado de la incidencia
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 10-03-2019
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 11-08-2019 -  Se agrega filtro por login del cliente
* @since 1.0
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.2 08-04-2020 - Se agrega el ipController, puerto, tipo de usuario
* y número de tarea para el filtro
* @since 1.1
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.3 21-04-2020 - Se omite la doble busqueda con el load
* @since 1.2
*
*/ 
function buscarRegistroIncidenciasPorParametros() 
{  
    var subEstadoInci   = Ext.getCmp('cmb_subEstadoInci').value;
    var feDesde         = Ext.getCmp('feEmisionDesde').value;
    var feHasta         = Ext.getCmp('feEmisionHasta').value; 
    var noCaso          = Ext.getCmp('numeroCaso').value; 
    var noTicket        = Ext.getCmp('noTicket').value;
    var ipAddressFil    = Ext.getCmp('ipAddressFil').value;
    var estadoInci      = Ext.getCmp('cmb_estadoIncidencia').value;
    var prioridadInci   = Ext.getCmp('cmb_prioridadIncidencia').value;
    var estadoGestion   = Ext.getCmp('cmb_estadoGestionIncidencia').value;
    var notificaInci    = Ext.getCmp('cmb_notificarIncidencia').value;
    var categoria       = Ext.getCmp('cmb_categoria').value;
    var canton          = Ext.getCmp('cmb_canton').value;
    var tipoEvento      = Ext.getCmp('cmb_tipoEvento').value;
    var login           = Ext.getCmp('cmb_Login').value;
    var ipControllerFil = Ext.getCmp('ipControllerFil').value;
    var numeroTareaFil  = Ext.getCmp('numeroTareaFil').value;
    var puertoControl   = Ext.getCmp('puertoControllerFil').value;
    var tipoCliente     = Ext.getCmp('cmb_tipoCliente').value;
    
    var store           = Ext.getStore('store');
    var pagingToolbar   = Ext.getCmp('pagingToolbar');
    store.removeAll();

    if( (typeof feDesde       !== "undefined" && feDesde !="") ||
        (typeof feHasta       !== "undefined" && feHasta !="") ||
        noCaso.replace(/ /g, "")         != "" ||
        noTicket.replace(/ /g, "")       != "" ||
        ipAddressFil.replace(/ /g, "")   != "" ||
        ipControllerFil.replace(/ /g, "")!= "" ||
        numeroTareaFil.replace(/ /g, "") != "" ||
        puertoControl.replace(/ /g, "")  != "" ||
        (subEstadoInci != null && subEstadoInci != "") || 
        (estadoInci    != null && estadoInci    != "") ||
        (prioridadInci != null && prioridadInci != "") ||
        (estadoGestion != null && estadoGestion != "") ||
        (notificaInci  != null && notificaInci  != "") ||
        (categoria     != null && categoria     != "") ||
        (canton        != null && canton        != "") ||
        (tipoEvento    != null && tipoEvento    != "") ||
        (login         != null && login         != "") ||
        (tipoCliente   != null && tipoCliente   != "")
        )
    {
        store.getProxy().extraParams.feEmisionDesde    =     feDesde;
        store.getProxy().extraParams.feEmisionHasta    =     feHasta;     
        store.getProxy().extraParams.numeroCaso        =     noCaso; 
        store.getProxy().extraParams.estadoInci        =     estadoInci; 
        store.getProxy().extraParams.noTicket          =     noTicket; 
        store.getProxy().extraParams.ipAddressFil      =     ipAddressFil; 
        store.getProxy().extraParams.subEstadoInci     =     subEstadoInci; 
        store.getProxy().extraParams.prioridadInci     =     prioridadInci;
        store.getProxy().extraParams.estadoGestion     =     estadoGestion; 
        store.getProxy().extraParams.notificaInci      =     notificaInci;
        store.getProxy().extraParams.categoria         =     categoria; 
        store.getProxy().extraParams.canton            =     canton;
        store.getProxy().extraParams.tipoEvento        =     tipoEvento;
        store.getProxy().extraParams.login             =      login;
        store.getProxy().extraParams.ipControllerFil   =     ipControllerFil; 
        store.getProxy().extraParams.numeroTareaFil    =     numeroTareaFil;
        store.getProxy().extraParams.puertoControl     =     puertoControl;
        store.getProxy().extraParams.tipoCliente       =     tipoCliente;
        pagingToolbar.moveFirst();
    }
    else
    {
        Ext.Msg.show({
            title: 'Advertencia',
            msg: 'Ingrese algún parámetro de búsqueda',
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.ERROR
        });
    }
    
       
}

/**
* 
* limpiarRegistroDeFiltro
* 
* Limpia los registros de filtro para poder realizar una nueva busqueda por los parámetros del filtro
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 10-03-2019
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 08-04-2020 - Se limpian los filtros de ipController, puerto, tipo de usuario
* y número de tarea
* @since 1.0
*
*/
function limpiarRegistroDeFiltro() 
{
    Ext.getCmp('noTicket').value = "";
    Ext.getCmp('noTicket').setRawValue("");
    Ext.getCmp('feEmisionDesde').value = "";
    Ext.getCmp('feEmisionDesde').setRawValue("");
    Ext.getCmp('feEmisionHasta').value = "";
    Ext.getCmp('feEmisionHasta').setRawValue("");
    Ext.getCmp('ipAddressFil').value = ""; 
    Ext.getCmp('ipAddressFil').setRawValue("");
    Ext.getCmp('numeroCaso').value = ""; 
    Ext.getCmp('numeroCaso').setRawValue("");
    Ext.getCmp('cmb_estadoIncidencia').value = "";
    Ext.getCmp('cmb_estadoIncidencia').setRawValue("");
    Ext.getCmp('cmb_subEstadoInci').value = "";
    Ext.getCmp('cmb_subEstadoInci').setRawValue("");
    Ext.getCmp('cmb_prioridadIncidencia').value = "";
    Ext.getCmp('cmb_prioridadIncidencia').setRawValue("");
    Ext.getCmp('cmb_estadoGestionIncidencia').value = "";
    Ext.getCmp('cmb_estadoGestionIncidencia').setRawValue("");
    Ext.getCmp('cmb_notificarIncidencia').value = "";
    Ext.getCmp('cmb_notificarIncidencia').setRawValue("");
    Ext.getCmp('cmb_categoria').value = "";
    Ext.getCmp('cmb_categoria').setRawValue("");
    Ext.getCmp('cmb_canton').value = "";
    Ext.getCmp('cmb_canton').setRawValue("");
    Ext.getCmp('cmb_tipoEvento').value = "";
    Ext.getCmp('cmb_tipoEvento').setRawValue("");
    Ext.getCmp('cmb_Login').value = "";
    Ext.getCmp('cmb_Login').setRawValue("");
    Ext.getCmp('ipControllerFil').value = "";
    Ext.getCmp('ipControllerFil').setRawValue("");
    Ext.getCmp('numeroTareaFil').value = "";
    Ext.getCmp('numeroTareaFil').setRawValue("");
    Ext.getCmp('puertoControllerFil').value = "";
    Ext.getCmp('puertoControllerFil').setRawValue("");
    Ext.getCmp('cmb_tipoCliente').value = "";
    Ext.getCmp('cmb_tipoCliente').setRawValue("");
    
    var store = Ext.getStore('store');
    var grid  = Ext.getCmp("grid");
    store.removeAll();
    store.getProxy().extraParams.feEmisionDesde    =     "";
    store.getProxy().extraParams.feEmisionHasta    =     "";     
    store.getProxy().extraParams.numeroCaso        =     ""; 
    store.getProxy().extraParams.estadoInci        =     ""; 
    store.getProxy().extraParams.noTicket          =     ""; 
    store.getProxy().extraParams.ipAddressFil      =     ""; 
    store.getProxy().extraParams.subEstadoInci     =     ""; 
    store.getProxy().extraParams.prioridadInci     =     "";
    store.getProxy().extraParams.estadoGestion     =     ""; 
    store.getProxy().extraParams.notificaInci      =     "";
    store.getProxy().extraParams.categoria         =     ""; 
    store.getProxy().extraParams.canton            =     "";
    store.getProxy().extraParams.tipoEvento        =     "";
    store.getProxy().extraParams.login             =     "";
    store.getProxy().extraParams.ipControllerFil   =     ""; 
    store.getProxy().extraParams.numeroTareaFil    =     "";
    store.getProxy().extraParams.puertoControl     =     "";
    store.getProxy().extraParams.tipoCliente       =     "";
    grid.getStore().removeAll();  
}

/**
* 
* agregarSeguimiento
* 
* Opción para guardar los seguimientos de la tarea, que fue creada en base a la incidencia reportada por ECUCERT.
* Los parametros de filtro son:
* 
* @param   id_caso         - Id del caso
*           id_detalle      - Id del detalle de la tarea 
*           nombre_tarea    - Nombre de la tarea 
*           registroInterno - Bandera que si el seguimiento si es interno o no
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 10-03-2019
+
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 23-08-2019 - Se agrega el campo para seleccionar el tipo de seguimiento para ECUCERT.
* @since 1.0
*/
function agregarSeguimiento(id_caso,nombre_tarea,id_detalle,registroInterno,boolCambioEstadoGes,rowIndex)
{
    var mostrarIngresoRegistro = true;
    if(registroInterno == "S")
    {
        mostrarIngresoRegistro = false;
    }
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
    var btnguardar2 = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                var valorSeguimiento      = Ext.getCmp('seguimiento').value;
                var registroInterno       = Ext.getCmp('seguimientoInterno').value;
                var seguimientoEcucert    = Ext.getCmp('cmb_seguimientoCombo').value;
                var compararVariableVacia = "undefined";
                if(seguimientoEcucert != null && seguimientoEcucert!="")
                {
                    if(valorSeguimiento != null && valorSeguimiento!="" && typeof valorSeguimiento !== compararVariableVacia)
                    {
                        var seguientoIngresado  = "".concat(seguimientoEcucert,': ',valorSeguimiento);
                        winSeguimiento.destroy();
                        conn.request({
                            method: 'POST',
                            params :{
                                id_caso: id_caso,
                                id_detalle: id_detalle,
                                seguimiento: seguientoIngresado,
                                registroInterno: registroInterno
                            },
                            url: 'ingresarSeguimiento',
                            success: function(response){
                                var json = Ext.JSON.decode(response.responseText);
                                if(json.mensaje != "cerrada")
                                {
                                    Ext.Msg.show({
                                        title: 'Mensaje',
                                        msg: 'Se ingresó el seguimiento',
                                        buttons: Ext.Msg.OK,
                                        fn: function(buttonId) 
                                            {
                                                if (buttonId === "ok")
                                                {
                                                    if(boolCambioEstadoGes)
                                                    {
                                                        CambioValidacionEstadoGestion(rowIndex);
                                                    }
                                                }
                                            }
                                            });
                                }
                                else
                                {
                                    Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consúltela nuevamente");
                                }
                            },
                            failure: function(rec, op) {
                                var json = Ext.JSON.decode(op.response.responseText);
                                Ext.Msg.alert('Alerta ',json.mensaje);
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta ','Ingrese un seguimiento');
                    }
                }
                else{
                    Ext.Msg.alert('Alerta ','Seleccione un tipo de seguimiento');
                }
            }
    });
    var btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winSeguimiento.destroy();
            }
    });        
     var formPanel2 = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            height: 200,
            width: 500,
            layout: 'fit',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 140,
                msgTarget: 'side'
            },

            items: [{
                xtype: 'fieldset',
                title: 'Información',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Tarea:',
                        id: 'tareaCaso',
                        name: 'tareaCaso',
                        value: nombre_tarea
                    },
                    {
                        xtype: 'combobox',
                        id: 'cmb_seguimientoCombo',
                        fieldLabel: 'Tipo Seguimiento:',
                        store: storeOpcionesRespuesta,
                        name: 'cmb_seguimientoCombo',
                        emptyText: 'Seleccione Seguimiento ECUCERT',
                        displayField: 'seguimientoCombo',
                        valueField: 'seguimientoCombo',
                        border:0,
                        marginTop:0,
                        queryMode: "remote",
                        editable: false,
                        height:30,
                        width: 615
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Seguimiento:',
                        id: 'seguimiento',
                        name: 'seguimiento',
                        rows: 7,
                        cols: 70
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Registro Interno',
                        id: 'seguimientoInterno',
                        hidden: mostrarIngresoRegistro,
                        value: 'N',
                        store: [
                            ['N', 'No'],
                            ['S', 'Si']
                        ],
                        width: 400
                    }
                ]
            }]
         });
    var winSeguimiento = Ext.create('Ext.window.Window', {
            title: 'Ingresar Seguimiento',
            modal: true,
            width: 660,
            height: 300,
            resizable: false,
            layout: 'fit',
            items: [formPanel2],
            buttonAlign: 'center',
            buttons:[btnguardar2,btncancelar2]
    }).show();    
}

/**
* 
* verSeguimientoTarea
* 
* Opción para ver seguimientos de la tarea, que fue creada en base a la incidencia reportada por ECUCERT
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 10-03-2019
*
*/
function verSeguimientoTarea(id_detalle){
    var btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		      winSeguimientoTarea.destroy();													
            }
    });
	var storeSeguimientoTarea = new Ext.data.Store({ 
		//pageSize: 1000,
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : 'verSeguimientoTarea',
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
	var gridSeguimiento = Ext.create('Ext.grid.Panel', {
		id:'gridSeguimiento',
		store: storeSeguimientoTarea,		
		columnLines: true,
		columns: [
			{
			      id: 'observacion',
			      header: 'Observacion',
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
			      header: 'Fecha Observacion',
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
                                    msg: "Para poder copiar el contenido debe seleccionar y presionar Ctrl + C: <br> -&gt; <b>" + value + "</b>",
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
                                        beforeshow: function (tip) {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                            }
                                        }
                                    }
                                });

                            }                                    
                    }
	});
	var formPanelSeguimiento = Ext.create('Ext.form.Panel', {
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
	var winSeguimientoTarea = Ext.create('Ext.window.Window', {
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

/**
* 
* validarVulnerabilidad
* 
* Permite guardar seguimientos asociados a la tarea.
* Los parametros de filtro son:
* 
* @param   feEmisionDesde - Fecha de Emisión con el intervalo de inicio 
*           feEmisionHasta - Fecha de Emisión con el interbalo de fin 
*           numeroCaso     - Número de Caso 
*           estadoInci     - Estado de la incidencia
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 10-03-2019
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 04-05-2020 - Se agrega el parámetro puerto para validar
*                           la vulnerabilidad de la IP
* @since 1.0
*
*/
function validarVulnerabilidad(rowIndex)
{
    var store       = Ext.getStore('store');
    var rec         = store.getAt(rowIndex);
    var numeroCaso  = rec.data.numero_caso;
    var id_detalle  = rec.data.idDetalle;
    var connValidarVulnerabilidad = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Validando vulnerabilidad..');
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
    connValidarVulnerabilidad.request({
        url: './verificarVulnerabilidadEcucert',
        method: 'post',
        params:
            {
                idDetalleIncidencia: rec.data.idDetalleIncidencia,
                categoria: rec.data.categoria,
                subcategoria: rec.data.subCategoria,
                ip: rec.data.ipAddress,
                puerto: rec.data.puertocontroller,
                ticketNo: rec.data.ticket_No,
                intDetalleId: id_detalle
            },
        success: function(response) {
            var respuestaVulnerabilidad = response;
            var respuesta = JSON.parse(respuestaVulnerabilidad.responseText).estado;
            
            if(respuesta.includes("ERROR"))
            {
                Ext.Msg.show({
                    title: 'Error',
                    msg: respuesta,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
            else if (respuesta == "No Vulnerable" && numeroCaso != null )
            {
                rec.set('estado_incidente',respuesta);
                rec.data.estado_incidente = respuesta;
                cerrarTarea(rec.data.casoId,rec.data.idDetalle,rec.data.fechaSol,rec.data.horaSol,rec.data.idTarea,rec.data.idDetalleIncidencia);
            }
            else if (respuesta == "No Vulnerable" && numeroCaso == null )
            {
                var estadoCNA = 'Estado actual: '+respuesta+'. No tiene Caso asociado';
                rec.set('estado_incidente',respuesta);
                rec.data.estado_incidente = respuesta;
                Ext.Msg.show({
                    title: 'Información',
                    msg: estadoCNA,
                    buttons: Ext.Msg.OK,
                    fn: function(buttonId) 
                        {
                            if (buttonId === "ok")
                            {
                                store.load();
                            }
                        }
                });
            }
            else
            {
                var estado = 'Estado actual: '+respuesta;
                rec.set('estado_incidente',respuesta);
                rec.data.estado_incidente = respuesta;
                Ext.Msg.show({
                    title: 'Información',
                    msg: estado,
                    buttons: Ext.Msg.OK
                });
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
}

/**
* 
* cerrarTarea
* 
* Permite cerrar el caso y la tarea asociada al caso.
* Los parametros de filtro son:
* 
* @param   casoId          - Id del caso
*           idDetalle       - idDetalle de la tarea
*           fechaSol        - Fecha que se creo la incidencia
*           horaSol         - Hora que se creo la incidencia
*           idTarea         - id de la tarea
*           IncidenciaDetId - Id del detalle de la incidencia
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 13-03-2019
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 26-08-2019 - Se modifica el mensaje si se cierra el caso o la tarea
* @since 1.0
*
*/
function cerrarTarea(casoId,idDetalle,fechaSol,horaSol,idTarea,IncidenciaDetId)
{
    var store   = Ext.getStore('store');
    var mensaje = '';
    if(casoId != "" && casoId != null)
    {
        mensaje = ' ¿Desea cerrar el Caso asociado al Ticket ECUCERT? ';
    }
    else
    {
        mensaje = ' ¿Desea cerrar la Tarea asociada al Ticket ECUCERT? ';
    }
    var formPanelCerrarCaso = Ext.create('Ext.form.Panel', {
        bodyPadding: 10,
        waitMsgTarget: true,
        height: 100,
        width: 360,
        layout: 'fit',
        fieldDefaults: {
            labelAlign: 'center',
            msgTarget: 'side'
        },
        items: [{
                xtype: 'displayfield',
                value: mensaje,
        }]
    });  
    var btncancelar = Ext.create('Ext.Button', {
            text: 'No',
            cls: 'x-btn-rigth',
            handler: function() {
		        winCerrarCaso.destroy();													
            }
    }); 
    var btnaceptar = Ext.create('Ext.Button', {
            text: 'Si',
            cls: 'x-btn-left',
            handler: function() {
                winCerrarCaso.destroy();
                var connCerrarCaso = new Ext.data.Connection({
                listeners: {
                    'beforerequest': {
                        fn: function(con, opt) {
                            Ext.get(document.body).mask('Cerrando Caso..');
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
            connCerrarCaso.request({
                url: './cerrarCasosEcucert',
                method: 'post',
                params:
                    {
                        id_caso: casoId,
                        tituloFinalHipotesis: null,
                        id_detalle: idDetalle,
                        versionFinal: null,
                        tiempo_total_caso: '100',
                        tarea_id: idTarea,
                        fechaSol: fechaSol,
                        horaSol: horaSol,
                        IncidenciaDetId: IncidenciaDetId
                    },
                success: function(response) {
                    var respuesta = JSON.parse(response.responseText).estado;
                    Ext.Msg.show({
                        title: 'Mensaje',
                        msg: respuesta,
                        buttons: Ext.Msg.OK,
                        fn: function(buttonId) 
                            {
                                if (buttonId === "ok")
                                {
                                    store.load();
                                }
                            }
                    });
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
    });   
    var winCerrarCaso = Ext.create('Ext.window.Window', {
			title: 'Cerrar Caso',
			modal: true,
			width: 380,
			height: 100,
			resizable: true,
			layout: 'fit',
			items: [formPanelCerrarCaso],
			buttonAlign: 'center',
			buttons:[btnaceptar,btncancelar]
	}).show();  
}
    
/* contenerIp
* 
* Permite cerrar el caso y la tarea asociada al caso.
* Los parametros de filtro son:
* 
* @param  array [
*   categoria
*   subCategoria
*   ipAddress
* ]
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 13-03-2019
*
*/
function contenerIp(rowIndex)
{
    var store = Ext.getStore('store');
    var rec = store.getAt(rowIndex);
    var connContenerIp = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Conteniendo Ip..');
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
    connContenerIp.request({
        url: './contenerIp',
        method: 'post',
        params:
            {
                categoria: rec.data.categoria,
                subcategoria: rec.data.subCategoria,
                ip: rec.data.ipAddress
            },
        success: function(response) {
            var respuesta = JSON.parse(response.responseText).estado;
            Ext.Msg.show({
                title: 'Mensaje',
                msg: respuesta,
                buttons: Ext.Msg.OK
            });
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

/* CambiarEstadoGestion
* 
* Permite cambiar de estado de Gestión de la IP asociado al ticket
* Los parametros de filtro son:
* 
* @param  array [
    idIncidenciaDetalle
* ]
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 11-08-2019
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 20-08-2019 - Se obtiene los parámetros de estado de gestión
* @since 1.0
*
*/
function CambiarEstadoGestion(rowIndex)
{
    var store           = Ext.getStore('store');
    var rec             = store.getAt(rowIndex);
    var estadoGestiSig  = rec.data.siguienteEstadoGestion;
    var estadoGestAct   = rec.data.estadoIncEcucert;
    var alturaCasilla   = 100;
    var mensajeCambioGe = '¿Desea cambiar de estado '.concat(estadoGestAct,' a estado ',estadoGestiSig,' ? ');
    if (rec.data.idComunicacion != "" && rec.data.idComunicacion != null && rec.data.estadoTarea!='Finalizada')
    {
        mensajeCambioGe = mensajeCambioGe+" <br> &nbsp  Primero ingrese el motivo del cambio de estado";
        alturaCasilla   = 120;
    }

    var formPanelCambioEstadoGestion = Ext.create('Ext.form.Panel', {
        bodyPadding: 10,
        waitMsgTarget: true,
        height: alturaCasilla,
        width: 370,
        layout: 'fit',
        fieldDefaults: {
            labelAlign: 'center',
            msgTarget: 'side'
        },
        items: [{
                xtype: 'displayfield',
                value: mensajeCambioGe,
        }]
    });  
    var btncancelar = Ext.create('Ext.Button', {
            text: 'No',
            cls: 'x-btn-rigth',
            handler: function() {
		        winCambioEstadoGestion.destroy();													
            }
    }); 
    var btnaceptar = Ext.create('Ext.Button', {
        text: 'Si',
        cls: 'x-btn-left',
        handler: function() {
            winCambioEstadoGestion.destroy();
            if (rec.data.idComunicacion == "" || rec.data.idComunicacion == null || rec.data.estadoTarea=='Finalizada')
            {
                CambioValidacionEstadoGestion(rowIndex);
            }
            else
            {
                agregarSeguimiento(rec.data.casoId, rec.data.nombretarea, rec.data.idDetalle,rec.data.seguimientoInterno,true,rowIndex); 
            }											
        }
    });
    var winCambioEstadoGestion = Ext.create('Ext.window.Window', {
        title: 'Cambio de estado',
        modal: true,
        width: 380,
        height: alturaCasilla,
        resizable: true,
        layout: 'fit',
        items: [formPanelCambioEstadoGestion],
        buttonAlign: 'center',
        buttons:[btnaceptar,btncancelar]
    }).show(); 

}

/* revisarNotificaciones
* 
* Permite revisar si se ha realizado notificaciones al cliente caso contrario reenvía la notificación
* Los parametros de filtro son:
* 
* @param array [
*   idDetalleIncidencia
* ]  
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 13-03-2019
*
*/
function revisarNotificaciones(idDetalleIncidencia)
{
    var btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		      winNotificacion.destroy();													
            }
    });
	var storeNotificaciones = new Ext.data.Store({ 
		//pageSize: 1000,
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : 'verNotificaciones',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				idDetalleIncidencia: idDetalleIncidencia			
			}
		},
		fields:
		[
                {name:'correo', mapping:'correo'},
                {name:'fecha', mapping:'fecha'}
		]
	});
	var gridNotificaciones = Ext.create('Ext.grid.Panel', {
		id:'gridNotificaciones',
		store: storeNotificaciones,		
		columnLines: true,
		columns: [
		    {
			      id: 'correo',
			      header: 'Correo',
			      dataIndex: 'correo',
			      width:330,
			      sortable: true						 
			},
			  {
			      id: 'fecha',
			      header: 'Fecha Envío',
			      dataIndex: 'fecha',
			      width:140,
			      sortable: true						 
			}
		],		
		width: 490,
		height: 300,
		listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido debe seleccionar y presionar Ctrl + C: <br> -&gt; <b>" + value + "</b>",
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
                                        beforeshow: function (tip) {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                            }
                                        }
                                    }
                                });

                            }                                    
                    }
	});
	var formPanelNotificaciones = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 300,
			width:520,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				msgTarget: 'side'
			},

			items: [{
				xtype: 'fieldset',				
				defaultType: 'textfield',
				items: [					
					gridNotificaciones
				]
			}]
		 });
	var winNotificacion = Ext.create('Ext.window.Window', {
			title: 'Detalle de notificaciones de correo',
			modal: true,
			width: 520,
			height: 400,
			resizable: true,
			layout: 'fit',
			items: [formPanelNotificaciones],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show(); 
}

/**
* 
* notificarCliente
* 
* Permite notificar al cliente sino ha sido notificado de la incidencia.
* Los parametros de filtro son:
* 
* @param   casoId                - Id del caso
*           idDetalleIncidencia   - Id del de detalle de la incidencia
*           idPunto               - Id del punto del cliente
*           loginCliente          - Login del cliente
*           personaEmpresaRolId   - Id persona empresa Rol
*           categoria             - Categoria de la incidencia ECUCERT
*           subCategoria          - SubCategoria de la incidencia ECUCERT
*           tipoEvento            - Tipo de Evento de la incidencia
*           ip                    - Ip que esta causando la incidencia
*           puerto                - Puerto detectado en la incidencia
*           ipDestino             - Ip de destino
*           ticket                - ticket de la incidencia enviada por ECUCERT
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 2103-2019
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 2103-2019 - Se correción del campo IP por IPAddress 
* @since 1.0
*
*/
function notificarCliente(rowIndex)
{
    var store               = Ext.getStore('store');
    var rec                 = store.getAt(rowIndex);
    var idDetalleIncidencia = rec.data.idDetalleIncidencia;
    var idPunto             = rec.data.idPunto;
    var loginCliente        = rec.data.loginCliente;
    var casoId              = rec.data.casoId;
    var personaEmpresaRolId = rec.data.personaEmpresaRolId;
    var categoria           = rec.data.categoria;
    var subCategoria        = rec.data.subCategoria;
    var tipoEvento          = rec.data.tipoEvento;
    var ip                  = rec.data.ipAddress;
    var puerto              = rec.data.puerto;
    var ipDestino           = rec.data.ipDestino;
    var ticket              = rec.data.ticket;
    
    var formPanelNotificarClie = Ext.create('Ext.form.Panel', {
        bodyPadding: 10,
        waitMsgTarget: true,
        height: 150,
        width: 290,
        layout: 'fit',
        fieldDefaults: {
            labelAlign: 'center',
            msgTarget: 'side'
        },
        items: [{
                xtype: 'displayfield',
                value: 'No se ha notificado al cliente, ¿Desea enviar la notificación?',
        }]
    }); 
    var btncancelar = Ext.create('Ext.Button', {
            text: 'No',
            cls: 'x-btn-rigth',
            handler: function() {
		        winNotificarClie.destroy();													
            }
    });  
    var btnaceptar = Ext.create('Ext.Button', {
            text: 'Si',
            cls: 'x-btn-left',
            handler: function() {
                winNotificarClie.destroy();
                var connNotificarCliente = new Ext.data.Connection({
                listeners: {
                    'beforerequest': {
                        fn: function(con, opt) {
                            Ext.get(document.body).mask('Enviando correo..');
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
            connNotificarCliente.request({
                url: './enviarCorreoCliente',
                method: 'post',
                params:
                    {
                        idDetalleIncidencia:    idDetalleIncidencia,
                        idPunto:                idPunto,
                        loginCliente:           loginCliente,
                        casoId:                 casoId,
                        personaEmpresaRolId:    personaEmpresaRolId,
                        subCategoria:           subCategoria,
                        categoria:              categoria,
                        tipoEvento:             tipoEvento,
                        ip:                     ip,
                        puerto:                 puerto,
                        ipDestino:              ipDestino,
                        ticket:                 ticket
                    },
                success: function(response) {
                    var strRespuesta = JSON.parse(response.responseText).estado;
                    if(strRespuesta.includes("Enviado"))
                    {
                        Ext.Msg.show({
                            title: 'Mensaje',
                            msg: "Se envió correctamente la notificación al cliente",
                            buttons: Ext.Msg.OK
                        });
                    }
                    else
                    {
                        Ext.Msg.show({
                            title: 'Mensaje',
                            msg: strRespuesta,
                            buttons: Ext.Msg.OK
                        });
                    }
                    
                    if(!strRespuesta.includes("ERROR")){
                        store.load();
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
        }
    });  
    var winNotificarClie = Ext.create('Ext.window.Window', {
			title: 'Notificación',
			modal: true,
			width: 310,
			height: 150,
			resizable: true,
			layout: 'fit',
			items: [formPanelNotificarClie],
			buttonAlign: 'center',
			buttons:[btnaceptar,btncancelar]
	}).show(); 
}
