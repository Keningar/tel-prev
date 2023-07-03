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
* reprocesarClientePorIp
* 
* Permite notificar al cliente sino ha sido notificado de la incidencia.
* Los parametros de filtro son:
* 
* @param   ipAddress             - IP de la incidencia
*           idDetalleIncidencia   - Id del de detalle de la incidencia
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 2103-2019
*
*/
function reprocesarClientePorIp(rowIndex)
{
    var store               = Ext.getStore('store');
    var rec                 = store.getAt(rowIndex);
    var ipAddress           = rec.data.ipAddress;
    var idDetalleIncidencia = rec.data.idDetalleIncidencia;
    var noTicket            = rec.data.ticket_No;
    var subCategoria        = rec.data.subCategoria;
    var categoria           = rec.data.categoria;
    var tipoEvento          = rec.data.tipoEvento;
    var feIncidencia        = rec.data.feIncidente;
    var estadoIncidencia    = rec.data.estado_incidente;
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
                value: '¿Desea volver a buscar al cliente que esta asociado a la IP '+ipAddress+' ?',
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
                var connCerrarCaso = new Ext.data.Connection({
                listeners: {
                    'beforerequest': {
                        fn: function(con, opt) {
                            Ext.get(document.body).mask('Reprocesando..');
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
                url: './reprocesarIP',
                method: 'post',
                params:
                    {
                        ipAddress:              ipAddress,
                        idDetalleIncidencia:    idDetalleIncidencia,
                        noTicket:               noTicket,
                        subCategoria:           subCategoria,
                        categoria:              categoria,
                        tipoEvento:             tipoEvento,
                        feIncidencia:           feIncidencia,
                        estadoIncidencia:       estadoIncidencia
                    },
                success: function(response) {
                    var strRespuesta = JSON.parse(response.responseText).estado;
                    Ext.Msg.show({
                        title: 'Mensaje',
                        msg: strRespuesta,
                        buttons: Ext.Msg.OK
                    });
                    if(!strRespuesta.includes("No se pudo procesar")){
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
			title: 'Reproceso',
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


/**
* 
* subirMultipleAdjuntosTarea
* 
* Permite subir archivos a la tarea direcamente en el módulo de ECUCERT:
* 
* @param   id_tarea             - Id detalle de la tarea
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 2103-2019
*
*/

function subirMultipleAdjuntosTarea(rowIndex)
{
    var store               = Ext.getStore('store');
    var rec                 = store.getAt(rowIndex);
    var id_tarea            = rec.data.idDetalle;
    
    var panelMultiupload = Ext.create('widget.multiupload',{ fileslist: [] });
    var formPanel = Ext.create('Ext.form.Panel',
     {
        width: 500,
        frame: true,
        bodyPadding: '10 10 0',
        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },
        items: [panelMultiupload],
        buttons: [{
            text: 'Subir',
            handler: function()
            {
                if(numArchivosSubidos>0)
                {    
                var form = this.up('form').getForm();
                if(form.isValid())
                {
                    form.submit({
                        url: url_multipleFileUpload,
                        params :{
                            IdTarea    : id_tarea,
                            origenTarea: 'S',
                            subirEnMsNfs: 'S'
                        },
                        waitMsg: 'Procesando Archivo...',
                        success: function(fp, o)
                        {
                        Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
                            if(btn=='ok')
                            {
                                win.destroy();
                            }
                        });
                        },
                        failure: function(fp, o) {
                        Ext.Msg.alert("Alerta",o.result.respuesta);
                        }
                    });
                }
                }
                else
                {
                    Ext.Msg.alert("Mensaje", "No existen archivos para subir", function(btn){
                        if(btn=='ok')
                        {
                            numArchivosSubidos=0;
                            win.destroy();
                        }
                    });
                }
                    
                
            }
        },
        {
            text: 'Cancelar',
            handler: function() {
                numArchivosSubidos=0;
                win.destroy();
            }
        }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Archivos Tarea',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    
}

/**
* 
* presentarDocumentosTareas
* 
* Permite ver los archivos subidos a la tarea direcamente en el módulo de ECUCERT:
* 
* @param   id_tarea             - Id detalle de la tarea
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 2103-2019
*
*/

function presentarDocumentosTareas(rowIndex)
{
    var store               = Ext.getStore('store');
    var rec                 = store.getAt(rowIndex);
    var id_tarea            = rec.data.idDetalle;
    var strTareaIncAudMant  = 'N';
    var cantidadDocumentos  = 1;
    var connDocumentosTarea = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.MessageBox.show({
                       msg: 'Consultando documentos, Por favor espere!!',
                       progressText: 'Saving...',
                       width:300,
                       wait:true,
                       waitConfig: {interval:200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });

    connDocumentosTarea.request({
        url: url_verifica_casos,
        method: 'post',
        params:
            {
                idTarea             : id_tarea,
                strTareaIncAudMant  : strTareaIncAudMant
            },
        success: function(response){
            var text           = Ext.decode(response.responseText);
            cantidadDocumentos = text.total;

            if(cantidadDocumentos > 0)
            {
                var storeDocumentosCaso = new Ext.data.Store({
                    pageSize: 1000,
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url : url_documentosCaso,
                        reader: {
                            type         : 'json',
                            totalProperty: 'total',
                            root         : 'encontrados'
                        },
                        extraParams: {
                            idTarea             : id_tarea,
                            strTareaIncAudMant  : strTareaIncAudMant
                        }
                    },
                    fields:
                        [
                            {name:'idDocumento',            mapping:'idDocumento'},
                            {name:'ubicacionLogica',        mapping:'ubicacionLogica'},
                            {name:'feCreacion',             mapping:'feCreacion'},
                            {name:'usrCreacion',            mapping:'usrCreacion'},
                            {name:'linkVerDocumento',       mapping:'linkVerDocumento'},
                            {name:'boolEliminarDocumento',  mapping:'boolEliminarDocumento'}
                        ]
                });

                Ext.define('DocumentosCaso', {
                    extend: 'Ext.data.Model',
                    fields: [
                          {name:'ubicacionLogica',  mapping:'ubicacionLogica'},
                          {name:'feCreacion',       mapping:'feCreacion'},
                          {name:'linkVerDocumento', mapping:'linkVerDocumento'}
                    ]
                });

                //grid de documentos por Caso
                gridDocumentosCaso = Ext.create('Ext.grid.Panel', {
                    id:'gridMaterialesPunto',
                    store: storeDocumentosCaso,
                    columnLines: true,
                    columns: [{
                        header   : 'Nombre Archivo',
                        dataIndex: 'ubicacionLogica',
                        width    : 260
                    },
                    {
                        header   : 'Usr. Creación',
                        dataIndex: 'usrCreacion',
                        width    : 80
                    },
                    {
                        header   : 'Fecha de Carga',
                        dataIndex: 'feCreacion',
                        width    : 120
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 90,
                        items:
                        [
                            {
                                iconCls: 'button-grid-show',
                                tooltip: 'Ver Archivo Digital',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec         = storeDocumentosCaso.getAt(rowIndex);
                                    verArchivoDigital(rec);
                                }
                            },
                            {
                                getClass: function(v, meta, rec) 
                                {
                                    var strClassButton  = 'button-grid-delete';
                                    if(!rec.get('boolEliminarDocumento'))
                                    {
                                        strClassButton = ""; 
                                    }

                                    if (strClassButton == "")
                                    {
                                        this.items[0].tooltip = ''; 
                                    }   
                                    else
                                    {
                                        this.items[0].tooltip = 'Eliminar Archivo Digital';
                                    }
                                    return strClassButton;

                                },
                                tooltip: 'Eliminar Archivo Digital',
                                handler: function(grid, rowIndex, colIndex) 
                                {
                                    var rec                 = storeDocumentosCaso.getAt(rowIndex);
                                    var idDocumento         = rec.get('idDocumento');
                                    var strClassButton      = 'button-grid-delete';
                                    if(!rec.get('boolEliminarDocumento'))
                                    {
                                        strClassButton = ""; 
                                    }

                                    if (strClassButton != "" )
                                    {
                                        eliminarAdjunto(storeDocumentosCaso,idDocumento);
                                            
                                    } 
                                }
                            }
                        ]
                    }
                ],
                    viewConfig:{
                        stripeRows:true,
                        enableTextSelection: true
                    },
                    frame : true,
                    height: 200
                });

                function verArchivoDigital(rec)
                {
                    var rutaFisica = rec.get('linkVerDocumento');
                    var posicion = rutaFisica.indexOf('/public')
                    window.open(rutaFisica.substring(posicion,rutaFisica.length));
                }

                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding  : 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget : 'side'
                    },
                    items: [

                    {
                        xtype      : 'fieldset',
                        title      : '',
                        defaultType: 'textfield',

                        defaults: {
                            width: 550
                        },
                        items: [

                            gridDocumentosCaso

                        ]
                    }
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title   : 'Documentos Cargados',
                    modal   : true,
                    width   : 580,
                    closable: true,
                    layout  : 'fit',
                    items   : [formPanel]
                }).show();
            }else{
                Ext.Msg.show({
                title  :'Mensaje',
                msg    : 'La tarea seleccionada no posee archivos adjuntos.',
                buttons: Ext.Msg.OK,
                animEl : 'elId',
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

/* CambiarEstadoNotificado
* 
* Permite cambiar de estado de Notificación de la IP asociado al ticket
* Los parametros de filtro son:
* 
* @param  array [
    idIncidenciaDetalle - Id detalle incidencia ECUCERT
    idPunto             - Id del punto del cliente     
    loginCliente        - Login del cliente
    casoId              - Caso del cliente
    personaEmpresaRolId - Id Persona empresa rol del cliente
    categoria           - Categoria de la incidencia
    subCategoria        - SubCategoria de la incidencia
    tipoEvento          - Tipo de evento de la incidencia  
    ip                  - Ip reportada por ECUCERT
    puerto              - Puerto de la incidencia
    ipDestino           - Ip destino 
    ticket              - Número de ticket de ECUCERT
    idDetalle           - Id Detalla de la tarea
* ]
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 20-08-2019
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.1 16-04-2020 - Se agrega el parámetro que permite enviar correos adicionales
*                           y agregar los correos enviados al seguimiento de la tarea.
* @since 1.0
*
*/
function CambiarEstadoNotificado(rowIndex)
{
    var store           = Ext.getStore('store');
    var rec             = store.getAt(rowIndex);
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
    var idDetalle           = rec.data.idDetalle;
    var estadoGestion       = rec.data.estadoIncEcucert;
    var banderaLogin2       = false;
    var altoPantallaCorreo  = 450;
    if( loginCliente != null  && loginCliente.replace(/ /g, "")   != "" && loginCliente != "null")
    {
        banderaLogin2      = true;
        altoPantallaCorreo = 400;
    }
    var connCambiarEstado = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Ingresando Correos..');
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

    Ext.define('modeloCorreo', {
        extend: 'Ext.data.Model',
        fields: [
                    {name:'tipoUsuario', type:'string', mapping: 'tipoUsuario'},
                    {name:'correo', type:'string', mapping: 'correo'}
                ]
    });

    var storeMails = Ext.create('Ext.data.Store', {
        name: 'storeEmail',
        id: 'storeEmail',
        model: 'modeloCorreo',
        autoLoad: false,
    });

    var gridIngresoMail = Ext.create('Ext.grid.Panel', {
		id:'gridCorreos',
		store: storeMails,		
        columnLines: true,
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items:
                [
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_add',
                        text: 'Registrar',
                        scope: this,
                        handler: function() 
                        {
                            var formPanelIngresoCorreo = Ext.create('Ext.form.Panel', {
                                bodyPadding: 5,
                                waitMsgTarget: true,
                                height: 50,
                                width: 300,
                                layout: 'fit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 140,
                                    msgTarget: 'side'
                                },
                        
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        title: 'Mail',
                                        defaultType: 'textfield',
                                        items: [
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: 'Tipo Envio',
                                                id: 'tipoEnvio',
                                                name: 'tipoEnvio',
                                                value: 'Punto',
                                                store: [
                                                    ['Punto', 'Punto'],
                                                    ['Personal', 'Personal']
                                                ],
                                                width: 300,
                                                editable: false
                                            },
                                            {
                                                xtype: 'textarea',
                                                fieldLabel: 'Correo Electrónico:',
                                                id: 'correoUsuarioI',
                                                name: 'correoUsuarioI',
                                                rows: 2,
                                                cols: 150
                                            }
                                        ]
                                    }]
                             });
    
                            var btncancelarIngresoCorreo = Ext.create('Ext.Button', {
                                text: 'No',
                                cls: 'x-btn-rigth',
                                handler: function() {
                                    WinIngresoCorreo.destroy();													
                                }
                            }); 
    
                            var btnaceptarIngresoCorreo = Ext.create('Ext.Button', {
                                text: 'Si',
                                cls: 'x-btn-left',
                                handler: function() {
                                    var correoUsuarioText =  formPanelIngresoCorreo.down('textfield[name=correoUsuarioI]').getValue();   
                                    var TipoEnvio =  formPanelIngresoCorreo.down('textfield[name=tipoEnvio]').getValue();   
                                    if(correoUsuarioText != null && correoUsuarioText!= "" && correoUsuarioText.includes("@"))
                                    {
                                        var registro = Ext.create('modeloCorreo', {
                                            tipoUsuario: TipoEnvio,
                                            correo: correoUsuarioText
                                        }); 
                                        storeMails.insert(0, registro);  
                                        WinIngresoCorreo.destroy();	
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Mensaje',"Ingrese un correo válido") ;
                                    }
                                   
                                }
                            });
    
                            var WinIngresoCorreo = Ext.create('Ext.window.Window', {
                                title: 'Ingreso correo',
                                modal: true,
                                width: 500,
                                height: 200,
                                resizable: true,
                                layout: 'fit',
                                items: [formPanelIngresoCorreo],
                                buttonAlign: 'center',
                                buttons:[btnaceptarIngresoCorreo,btncancelarIngresoCorreo]
                            }).show(); 
                            
                        }
                    }
                ]
            }
        ],
		columns: [
			{
			      id: 'tipoUsuario',
			      header: 'Tipo Envío',
			      dataIndex: 'tipoUsuario',
			      width:170,
			      sortable: true						 
			},
			  {
			      id: 'correo',
			      header: 'Correo',
			      dataIndex: 'correo',
			      width:450,
			      sortable: true						 
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 65,
                sortable: false,
                items:
               [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var classButton = "button-grid-delete"
                            this.items[0].tooltip = 'Borrar registro';
                            
                            if (rec.data.esconder == 1)
                            {
                                this.items[0].tooltip = '';
                                return "icon-invisible";
                            }

                            return classButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            storeMails.removeAt(rowIndex); 
                        }
                    }
               ]
            }
		],		
		width: 690,
		height: 280,
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

    var btncancelar = Ext.create('Ext.Button', {
            text: 'Cancelar',
            cls: 'x-btn-rigth',
            handler: function() {
		        winCambioEstadoGestion.destroy();													
            }
    }); 

    var formPanelCambioEstadoGestion = Ext.create('Ext.container.Container', {
        id: 'container',
        width: 700,
        renderTo: document.body,
        layout : 'column',
        items: [
        {
            xtype: 'textarea',
            fieldLabel: 'Login:',
            style: 'margin: 10px;',
            id: 'loginNuevo',
            name: 'loginNuevo',
            rows: 2,
            cols: 150,
            hidden: banderaLogin2
        },
        {
            style: 'margin: 10px;',
            xtype: 'fieldset',				
            defaultType: 'textfield',

            items: [					
                gridIngresoMail
            ]
			
        },
        {
            xtype: 'fieldcontainer',
            defaultType: 'fieldcontainer',
            style: 'margin: 20px;',
            defaults: {
                width: '100%',
                margin:'5'
            }
        }] // this way we can add differnt child elements to the container as container items.
     });
     
    var btnaceptar = Ext.create('Ext.Button', {
        text: 'Guardar',
        cls: 'x-btn-left',
        handler: function() {
            var datar           = new Array();
            var boolCorreo      = false;
            var storeEmail      = Ext.getStore('storeEmail');
            var records         = storeEmail.getRange();
            for (var i = 0; i < records.length; i++) 
            {
                datar.push(records[i].data);
                boolCorreo = true;
            }
            jsonCorreos = Ext.JSON.encode(datar);
            if(boolCorreo)
            {
                connCambiarEstado.request({
                    url: url_cambiar_estado_notificacion,
                    method: 'post',
                    params:
                        {
                            jsonCorreos: jsonCorreos,
                            DetalleIncidenciaId: rec.data.idDetalleIncidencia
                        },
                    success: function(response) {
                        var respuesta = "Correos registrados exitosamente";
                        Ext.Msg.show({
                            title: 'Información',
                            msg: respuesta,
                            buttons: Ext.Msg.OK,
                            fn: function(buttonId) 
                                {
                                    if (buttonId === "ok")
                                    {
                                        winCambioEstadoGestion.destroy();
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
            else
            {
                Ext.Msg.show({
                    title: 'Error',
                    msg: "Ingrese al menos un correo",
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        }
    });

    var btnaceptarEnviar = Ext.create('Ext.Button', {
        text: 'Guardar y Enviar',
        cls: 'x-btn-left',
        handler: function() {
            var datar           = new Array();
            var boolCorreo      = false;
            var boolLogin       = false;
            var storeEmail      = Ext.getStore('storeEmail');
            var strLoginAd      = Ext.getCmp('loginNuevo').value;
            var records         = storeEmail.getRange();
            if(typeof strLoginAd !== "undefined" 
               && strLoginAd != null
               && strLoginAd.length != 0)
            {
                boolLogin = true;
            }
            for (var i = 0; i < records.length; i++) 
            {
                datar.push(records[i].data);
                boolCorreo = true;
            }
            jsonCorreos = Ext.JSON.encode(datar);
            if(boolCorreo)
            {
                if(banderaLogin2 || boolLogin)
                {
                    winCambioEstadoGestion.getEl().mask('Registrando y enviando Correos..');
                    Ext.Ajax.request({
                        url: url_enviar_correo_cliente,
                        method: 'post',
                        params:
                            {
                                jsonCorreos:            jsonCorreos,
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
                                ticket:                 ticket,
                                idDetalle:              idDetalle,
                                estadoGestion:          estadoGestion,
                                loginAdicional:         strLoginAd
                            },
                        success: function(response) {
                            winCambioEstadoGestion.getEl().unmask();
                            var respuesta = "Correos registrados y enviados exitosamente";
                            var mensaje = Ext.Msg.show({
                                title: 'Información',
                                msg: respuesta,
                                buttons: Ext.Msg.OK,
                                fn: function(buttonId) 
                                    {
                                        if (buttonId === "ok")
                                        {
                                            rec.set('estadoNotificacion','Notificado');
                                            rec.data.estadoNotificacion = 'Notificado';
                                            if(estadoGestion !== "Atendido")
                                            {
                                                rec.set('estadoIncEcucert','Analisis');
                                                rec.data.estadoIncEcucert = 'Analisis';
                                            }
                                            mensaje.hide();
                                            winCambioEstadoGestion.destroy();
                                        }
                                    }
                            });
                        },
                        failure: function(result) {
                            winCambioEstadoGestion.getEl().unmask();
                            Ext.Msg.show({
                                title: 'Error',
                                msg: result.statusText,
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    });	
                }
                else
                {
                    Ext.Msg.show({
                        title: 'Error',
                        msg: "Ingrese el login del cliente",
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            else
            {
                Ext.Msg.show({
                    title: 'Error',
                    msg: "Ingrese al menos un correo",
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }										
        }
    });

    var winCambioEstadoGestion = Ext.create('Ext.window.Window', {
        title: 'Registro de Correos',
        modal: true,
        width: 740,
        height: altoPantallaCorreo,
        resizable: true,
        layout: 'fit',
        items: [formPanelCambioEstadoGestion],
        buttonAlign: 'center',
        buttons:[btnaceptar,btnaceptarEnviar,btncancelar]
    }).show(); 

}


/* eliminarAdjunto
* 
* Eliminar adjunto de la tarea
* 
* @param  array [
    idIncidenciaDetalle
* ]
* 
* @return  Json
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 20-08-2019
*
*/
function eliminarAdjunto(storeDocumentosCaso,idDocumento)
{
    Ext.Msg.confirm('Alerta','Se eliminará el documento. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
              Ext.MessageBox.wait("Eliminando Archivo...", 'Por favor espere'); 
              Ext.Ajax.request({
                url: url_eliminar_adjunto,
                method: 'post',
                params: { id:idDocumento },
                success: function(response)
                {
                    Ext.MessageBox.hide();
                    var json = Ext.JSON.decode(response.responseText);                                                                                                

                    if (json.status=="OK")
                    {
                        Ext.MessageBox.show({
                            title: "Información",
                            cls: 'msg_floating',
                            msg: json.message,
                            icon: Ext.MessageBox.INFO,
                            buttons: Ext.Msg.OK,
                            fn: function(buttonId) 
                            {
                                if (buttonId === "ok")
                                {
                                    storeDocumentosCaso.load();
                                }
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.show(
                        {
                           title: 'Error',
                           width: 300,
                           cls: 'msg_floating',
                           icon: Ext.MessageBox.ERROR,
                           msg: json.message
                        });
                    }
                  },
                  failure: function(response)
                  {
                    Ext.MessageBox.hide();
                    var json = Ext.JSON.decode(response.responseText);
                    Ext.Msg.show(
                    {
                       title: 'Error',
                       width: 300,
                       cls: 'msg_floating',
                       icon: Ext.MessageBox.ERROR,
                       msg: json.message
                    });
                  }
              });
        }
    });
}

/* CambioValidacionEstadoGestion
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
* @version 1.0 20-02-2020
*
*/
function CambioValidacionEstadoGestion(rowIndex)
{
    var store           = Ext.getStore('store');
    var rec             = store.getAt(rowIndex);
    var estadoGestiSig  = rec.data.siguienteEstadoGestion;
    var idDetalle       = rec.data.idDetalle;

    var connCambiarEstado = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Cambiando estado de Gestión..');
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
    connCambiarEstado.request({
        url: './cambiarEstadoGestion',
        method: 'post',
        params:
            {
                estado: estadoGestiSig,
                DetalleIncidenciaId: rec.data.idDetalleIncidencia,
                idDetalle: idDetalle
            },
        success: function(response) {
            var respuesta = JSON.parse(response.responseText).estado;
            if(respuesta.includes("Error"))
            {
                Ext.Msg.show({
                    title: 'Error',
                    msg: respuesta,
                    buttons: Ext.Msg.OK
                });
            }
            else
            {
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