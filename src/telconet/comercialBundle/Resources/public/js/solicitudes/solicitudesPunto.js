Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

/* Se modifica la cantidad de items por pagina.
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 16-05-2022
 */
var itemsPerPage            = 10;
var store                   = '';
var motivo_id               = '';
var tipo_asignacion         = '';
var pto_sucursal            = '';
var idClienteSucursalSesion = '';



Ext.onReady(function()
{
    Ext.define('modelMotivo', 
    {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idMotivo',           type: 'string'},
            {name: 'descripcion',        type: 'string'},
            {name: 'idRelacionSistema',  type: 'string'}                 
        ]
    });
                
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: [{name:'id', type: 'string'},
                {name:'servicio', type: 'string'},
                {name:'motivo', type: 'string'},
                {name:'descuento', type: 'string'},
                {name:'observacion', type: 'string'},
                {name:'feCreacion', type: 'string'},
                {name:'usrCreacion', type: 'string'},
                {name:'linkVer', type: 'string'},
                {name:'descripcionSolicitud', type:'string'},
                {name:'estado', type:'string'},
                {name:'muestraBotonAnular', type: 'string'},
                {name:'muestraBotonFinalizar', type: 'string'},
                {name:'cantidadMateriales', type: 'string'}
                ]
    }); 


    store = Ext.create('Ext.data.JsonStore', 
    {
        model   : 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: 
        {
            type: 'ajax',
            url : url_store,
            reader: 
            {
                type         : 'json',
                root         : 'solicitudes',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },
        listeners: 
        {
            load: function(store)
            {
                store.each(function(record) {});
            }
        }
    });

    store.load({params: {start: 0, limit: 10}});    


    var listView = Ext.create('Ext.grid.Panel', 
    {
        width      :1000,
        height     :275,
        collapsible:false,
        title      : '',                 
        renderTo   : Ext.get('lista_solicitudes'),
        bbar       : Ext.create('Ext.PagingToolbar', 
        {
            store      : store,
            displayInfo: true,
            displayMsg : 'Mostrando solicitudes {0} - {1} of {2}',
            emptyMsg   : "No hay datos para mostrar"
        }),	
        store       : store,
        multiSelect : false,
        viewConfig  : 
        {
            emptyText: 'No hay datos para mostrar'
        },
        columns: [
        {
            text     : 'Solicitud',
            width    : 160,
            dataIndex: 'descripcionSolicitud'
        },                        
        {
            text     : 'Servicio',
            width    : 160,
            dataIndex: 'servicio'
        },{
            text     : 'Motivo',
            width    : 160,
            dataIndex: 'motivo'
        },{
            text     : 'Descuento',
            dataIndex: 'descuento',
            align    : 'right',
            width    : 60			
        },{
            text     : 'Observacion Solicitud',
            dataIndex: 'observacion',
            align    : 'right',
            width    : 150		
        },{
            text     : 'Fecha Creacion',
            dataIndex: 'feCreacion',
            align    : 'right',
            width    : 95			
        },{
            text     : 'Usuario Creacion',
            dataIndex: 'usrCreacion',
            align    : 'right',
            flex     : 40			
        },{
            text     : 'Estado',
            dataIndex: 'estado',
            align    : 'right',
            flex     : 45			
        },{
            text     : 'Acciones',
            width    : 70,
            renderer : renderAcciones
        }],
        listeners:
        {
            itemdblclick: function( view, record, item, index, eventobj, obj )
            {
                var position = view.getPositionByEvent(eventobj),
                data         = record.data,
                value        = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  :'Copiar texto?',
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon   : Ext.Msg.INFORMATION
                });
            },
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
                    target    : view.el,
                    delegate  : '.x-grid-cell',
                    trackMouse: true,
                    renderTo  : Ext.getBody(),
                    listeners: 
                    {
                        beforeshow: function updateTipBody(tip) 
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) 
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }                                    
        }                
    });            


    function renderAcciones(value, p, record) 
    {
        var iconos='';	
        if (record.data.muestraBotonAnular==='S' && puedeAnularSolicitudes)
        {    
            iconos+='<b><a href="#" onClick="anulacionSolicitud('+record.data.id+')" title="Anular" class="button-grid-delete" ></a></b>';
        } 
        if (record.data.muestraBotonFinalizar==='S' && puedeFinalizarSolicitudes)
        {    
            iconos+='<b><a href="#" onClick="finalizarSolicitud('+record.data.id+')" title="Finalizar" class="button-grid-BigDelete" ></a></b>';
        }        
        if (record.data.descripcionSolicitud==='SOLICITUD PLANIFICACION')
        {    
            iconos+='<b><a href="#" onClick="verMaterialesPunto('+record.data.id+','+record.data.cantidadMateriales+')" title="Ver Materiales" \n\
                     class="button-grid-show" ></a></b>';
        }
        return Ext.String.format
        (
            iconos,
            value,
            '1',
            'nada'
        );
    }
});


/**
 * Documentación para funcion 'verMaterialesPunto'.
 * Metodo que permite vizualizar los materiales usados por Solicitud
 *
 * @author rcabrera@telconet.ec
 * @version 1.0 17-09-2015
 * 
 * @param string  idSolicitud
 * @param string  cantidadMateriales
 *
 */
function verMaterialesPunto(idSolicitud,cantidadMateriales){
    
    if(cantidadMateriales > 0){
    
        var storeMaterialesPunto = new Ext.data.Store({  
            pageSize: 1000,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url : urlGetMaterialesPunto,
                reader: {
                    type         : 'json',
                    totalProperty: 'total',
                    root         : 'encontrados'
                },
                extraParams: {
                    idSolicitud: idSolicitud
                }
            },
            fields:
                [
                  {name:'cantidad', mapping:'cantidad'},
                  {name:'codigoMaterial', mapping:'codigoMaterial'},
                  {name:'descripcion', mapping:'descripcion'},
                  {name:'unidad', mapping:'unidad'}
                ]
        });

        Ext.define('MaterialesPunto', {
            extend: 'Ext.data.Model',
            fields: [
                  {name:'cantidad', mapping:'cantidad'},
                  {name:'codigoMaterial', mapping:'codigoMaterial'},
                  {name:'descripcion', mapping:'descripcion'},
                  {name:'unidad', mapping:'unidad'}
            ]
        });

        //grid de materiales por solicitud
        gridMaterialesPunto = Ext.create('Ext.grid.Panel', {
            id:'gridMaterialesPunto',
            store: storeMaterialesPunto,
            columnLines: true,
            columns: [{
                header   : 'Cantidad',
                dataIndex: 'cantidad',
                width    : 100
            },
            {
                header   : 'Codigo del Material',
                dataIndex: 'codigoMaterial',
                width    : 150
            },
            {
                header   : 'Descripcion del Material',
                dataIndex: 'descripcion',
                width    : 450
            },
            {
                header   : 'Unidad de Medida',
                dataIndex: 'unidad',
                width    : 120
            }],
            viewConfig:{
                stripeRows:true,
                enableTextSelection: true
            },
            frame : true,
            height: 300
        });

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
                    width: 850
                },
                items: [

                    gridMaterialesPunto

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
            title   : 'Materiales de Instalacion',
            modal   : true,
            width   : 900,
            closable: true,
            layout  : 'fit',
            items   : [formPanel]
        }).show();
        
    }else{ 
        
        Ext.Msg.show({
        title  :'Mensaje',
        msg    : 'No existen materiales disponibles',
        buttons: Ext.Msg.OK,
        animEl : 'elId',
        });	  
        
    }    
}


/**
 * Documentación para funcion 'anulacionSolicitud'.
 * Metodo que anula la solicitud seleccionada del punto
 *
 * @author amontero@telconet.ec
 * @version 1.0 08-07-2015
 */
function anulacionSolicitud(idSolicitud)
{
	winAnulaAnticipo="";
	if(!winAnulaAnticipo) 
    {
	    Ext.define('modelMotivos', 
        {
            extend: 'Ext.data.Model',
            fields: 
            [
                {name: 'idMotivo'   ,  type: 'string'},
                {name: 'descripcion',  type: 'string'}                
            ]
	    });         
	    
	    var motivos_store = Ext.create('Ext.data.Store', 
        {
            autoLoad: false,
            model   : "modelMotivos",
            proxy: 
            {
                type: 'ajax',	
                url : urlListaMotivosAnulacion,
                reader: 
                {
                    type: 'json',
                    root: 'motivos'
                }           
            }
	    }); 
	    
	    var motivos_cmb = new Ext.form.ComboBox(
        {
            xtype         : 'combobox',
            store         : motivos_store,
            labelAlign    : 'left',            
            name          : 'idMotivo',
            id            : 'idMotivo',
            valueField    : 'idMotivo',
            displayField  : 'descripcion',
            fieldLabel    : 'Motivo Anulación',
            width         : 325,
            triggerAction : 'all',
            mode          : 'local',
            allowBlank    : true,   
            listeners: 
            {
                select:
                function(e) 
                {
                },
                click: 
                {
                element: 'el',
                fn: function(){ }
                }           
            }
	    });
	    
	    var formAnulaAnticipo = Ext.widget('form', 
        {
            layout: 
            {
                type  : 'vbox',
                align : 'stretch'
            },
            border      : false,
            bodyPadding : 10,
            fieldDefaults: 
            {
                labelAlign : 'top',
                labelWidth : 130,
                labelStyle : 'font-weight:bold'
            },
            defaults: 
            {
                margins : '0 0 10 0'
            },
            items: 
            [
                motivos_cmb,
                {
                    xtype      : 'textarea',
                    fieldLabel : 'Escriba Observación:',
                    labelAlign : 'top',
                    name       : 'txtObservacion',
                    id         : 'txtObservacion',
                    value      : '',
                    allowBlank : false
                }
            ],
            buttons: 
            [{
                text    : 'Cancel',
                handler : function() 
                {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }, 
            {
                text: 'Grabar',
                name: 'grabar',
                handler: function() 
                {
                    var respuesta='';
                    if(Ext.getCmp('idMotivo').value != null && Ext.getCmp('txtObservacion').value != '')
                    {           
                        Ext.Ajax.request({
                            url: urlAnularSolicitudPunto,
                            method: 'post',
                            params: { idMotivo    : Ext.getCmp('idMotivo').value, 
                                      observacion : Ext.getCmp('txtObservacion').value, 
                                      idSolicitud : idSolicitud },
                            success: function(response)
                            {

                                respuesta = Ext.decode(response.responseText);

                                store.load({params: {start: 0, limit: 10}});

                                if(respuesta.respuestaAnular === 'OK')
                                {
                                    Ext.Msg.alert('Success', 'Se realizo la anulacion');
                                }
                                else
                                {
                                    Ext.Msg.alert('Alert', 'No se Realizo la anulación (' + respuesta.respuestaAnular+')');
                                }
                            },
                            failure: function(respuesta)
                            {
                                Ext.Msg.alert('Error ','Error: ' + respuesta.respuestaAnular);
                            }
                        });
                        this.up('window').destroy();
                        winAnulaAnticipo.close();
                    }
                    else
                    {
                        Ext.Msg.alert('Alert', 'Debe seleccionar un motivo de anulación y observación.');
                    }    
                }
            }]
	    });
	    
	    winAnulaAnticipo = Ext.widget('window', 
        {
            title       : 'Anulación de Solicitud',
            closeAction : 'hide',
            closable    : false,
            width       : 350,
            height      : 250,
            minHeight   : 200,
            layout      : 'fit',
            resizable   : true,
            modal       : true,
            items       : formAnulaAnticipo
	    });

        winAnulaAnticipo.show();
	}
}



/**
 * Documentación para funcion 'finalizarSolicitud'.
 * Metodo que finaliza la solicitud seleccionada del punto
 *
 * @author amontero@telconet.ec
 * @version 1.0 17-02-2016
 */
function finalizarSolicitud(idSolicitud)
{
	winAnulaAnticipo="";
	if(!winAnulaAnticipo) 
    {
	    Ext.define('modelMotivos', 
        {
            extend: 'Ext.data.Model',
            fields: 
            [
                {name: 'idMotivo'   ,  type: 'string'},
                {name: 'descripcion',  type: 'string'}                
            ]
	    });         
	    
	    var motivos_store = Ext.create('Ext.data.Store', 
        {
            autoLoad: false,
            model   : "modelMotivos",
            proxy: 
            {
                type: 'ajax',	
                url : urlListaMotivosFinalizar,
                reader: 
                {
                    type: 'json',
                    root: 'motivos'
                }           
            }
	    }); 
	    
	    var motivos_cmb = new Ext.form.ComboBox(
        {
            xtype         : 'combobox',
            store         : motivos_store,
            labelAlign    : 'left',            
            name          : 'idMotivo',
            id            : 'idMotivo',
            valueField    : 'idMotivo',
            displayField  : 'descripcion',
            fieldLabel    : 'Motivo Finalizar',
            width         : 325,
            triggerAction : 'all',
            mode          : 'local',
            allowBlank    : true,   
            listeners: 
            {
                select:
                function(e) 
                {
                },
                click: 
                {
                element: 'el',
                fn: function(){ }
                }           
            }
	    });
	    
	    var formAnulaAnticipo = Ext.widget('form', 
        {
            layout: 
            {
                type  : 'vbox',
                align : 'stretch'
            },
            border      : false,
            bodyPadding : 10,
            fieldDefaults: 
            {
                labelAlign : 'top',
                labelWidth : 130,
                labelStyle : 'font-weight:bold'
            },
            defaults: 
            {
                margins : '0 0 10 0'
            },
            items: 
            [
                motivos_cmb,
                {
                    xtype      : 'textarea',
                    fieldLabel : 'Escriba Observación:',
                    labelAlign : 'top',
                    name       : 'txtObservacion',
                    id         : 'txtObservacion',
                    value      : '',
                    allowBlank : false
                }
            ],
            buttons: 
            [{
                text    : 'Cancel',
                handler : function() 
                {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }, 
            {
                text: 'Grabar',
                name: 'grabar',
                handler: function() 
                {
                    var respuesta='';
                    if(Ext.getCmp('idMotivo').value != null && Ext.getCmp('txtObservacion').value != '')
                    {           
                        Ext.Ajax.request({
                            url: urlFinalizarSolicitudPunto,
                            method: 'post',
                            params: { idMotivo    : Ext.getCmp('idMotivo').value, 
                                      observacion : Ext.getCmp('txtObservacion').value, 
                                      idSolicitud : idSolicitud },
                            success: function(response)
                            {

                                respuesta = Ext.decode(response.responseText);

                                store.load({params: {start: 0, limit: 10}});

                                if(respuesta.respuestaAnular === 'OK')
                                {
                                    Ext.Msg.alert('Success', 'Se finalizo la solicitud correctamente.');
                                }
                                else
                                {
                                    Ext.Msg.alert('Alert', 'No se Realizo la finalización (' + respuesta.respuestaAnular+')');
                                }
                            },
                            failure: function(respuesta)
                            {
                                Ext.Msg.alert('Error ','Error: ' + respuesta.respuestaAnular);
                            }
                        });
                        this.up('window').destroy();
                        winAnulaAnticipo.close();
                    }
                    else
                    {
                        Ext.Msg.alert('Alert', 'Debe seleccionar un motivo de finalización y observación.');
                    }    
                }
            }]
	    });
	    
	    winAnulaAnticipo = Ext.widget('window', 
        {
            title       : 'Finalizar Solicitud',
            closeAction : 'hide',
            closable    : false,
            width       : 350,
            height      : 250,
            minHeight   : 200,
            layout      : 'fit',
            resizable   : true,
            modal       : true,
            items       : formAnulaAnticipo
	    });

        winAnulaAnticipo.show();
	}
}
