Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

            Ext.onReady(function(){
                
            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField(
                {
                    id          :'fechaDesde',
                    fieldLabel  : 'Desde',
                    labelAlign  : 'left',
                    xtype       : 'datefield',
                    format      : 'Y-m-d',
                    width       :  325
                });
            DTFechaHasta = new Ext.form.DateField(
                {
                    id          : 'fechaHasta',
                    fieldLabel  : 'Hasta',
                    labelAlign  : 'left',
                    xtype       : 'datefield',
                    format      : 'Y-m-d',
                    width       : 325
                });
                

            Ext.define('modelEstado', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idestado', type: 'string'},
                    {name: 'codigo',  type: 'string'},
                    {name: 'descripcion',  type: 'string'}                    
                ]
            });			
            var estado_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelEstado",
		    proxy: {
		        type: 'ajax',
		        url : url_estado,
		        reader: {
		            type: 'json',
		            root: 'estados'
                        }
                    }
            });	
            TFNumero = new Ext.form.TextField({
                id: 'numContrato',
                fieldLabel: 'Numero de Contrato',
                xtype: 'textfield'
            });            
            var estado_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: estado_store,
                labelAlign : 'left',
                id:'idestado',
                name: 'idestado',
                valueField:'descripcion',
                displayField:'descripcion',
                fieldLabel: 'Estado',
                width: 325,
                triggerAction: 'all',
                selectOnFocus:true,
                lastQuery: '',
                mode: 'local',
                allowBlank: false,	
					
                listeners: {
                                select:
                                function(e) {
                                    estado_id = Ext.getCmp('idestado').getValue();
                                },
                                click: {
                                    element: 'el', //bind to the underlying el property on the panel
                                    fn: function(){ 
                                        estado_store.removeAll();
                                        estado_store.load();
                                    }
                                }			
                            }
                    });

                Ext.define('modelFormaPago', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'idformapago', type: 'string'},
                        {name: 'codigoformapago',  type: 'string'},
                        {name: 'descripcionformapago',  type: 'string'}                    
                    ]
                });	
                var forma_pago_store = Ext.create('Ext.data.Store', {
                autoLoad: false,
                model: "modelFormaPago",
                proxy: {
                    type: 'ajax',
                    url : url_forma_pago,
                    reader: {
                        type: 'json',
                        root: 'formas_pago'
                            }
                        }
                });	                
                var forma_pago_cmb = new Ext.form.ComboBox({
                    xtype: 'combobox',
                    store: forma_pago_store,
                    labelAlign : 'left',
                    id:'idformapago',
                    name: 'idformapago',
                    valueField:'idformapago',
                    displayField:'descripcionformapago',
                    fieldLabel: 'Forma de Pago',
                    width: 325,
                    triggerAction: 'all',
                    selectOnFocus:true,
                    lastQuery: '',
                    mode: 'local',
                    allowBlank: false,	

                    listeners: {
                                    select:
                                    function(e) {
                                        forma_pago_id = Ext.getCmp('idformapago').getValue();
                                    },
                                    click: {
                                        element: 'el', //bind to the underlying el property on the panel
                                        fn: function(){ 
                                            forma_pago_store.removeAll();
                                            forma_pago_store.load();
                                        }
                                    }			
                                }
                        });                
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'Numerocontrato', type: 'string'},
                            {name:'Numerocontratoemppub', type: 'string'},
                            {name:'Valorcontrato', type: 'string'},
                            {name:'Valoranticipo', type: 'string'},
                            {name:'Valorgarantia', type: 'string'},
                            {name:'Fefincontrato', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'linkVer', type: 'string'},
                            {name:'linkEditar', type: 'string'},
                            {name:'linkEliminar', type: 'string'},
                            {name:'esFisico', type: 'string'},
                            {name:'cliente', type: 'string'}
                            ]
                }); 


   store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        timeout: 900000,
        proxy: {
            type: 'ajax',
            url: url_store,
            reader: {
                type: 'json',
                root: 'tickets',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: '', estado: '',formaPago:'',numContrato:''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.fechaDesde   = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta   = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.estado       = Ext.getCmp('idestado').getValue();
                store.getProxy().extraParams.formaPago    = Ext.getCmp('idformapago').getValue();
                store.getProxy().extraParams.numContrato  = Ext.getCmp('numContrato').getValue();
            },
            load: function(store) {
                store.each(function(record) {
                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                });
            }
        }
    });

                store.load({params: {start: 0, limit: 10}});    



                 var sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                        selectionchange: function(selectionModel, selected, options){
                            arregloSeleccionados= new Array();
                            Ext.each(selected, function(record){
                                    //arregloSeleccionados.push(record.data.idOsDet);
                    });			
                            //console.log(arregloSeleccionados);

                        }
                    }
                });


                var listView = Ext.create('Ext.grid.Panel', {
                    width:870,
                    height:275,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        //tbfill -> alinea los items siguientes a la derecha
                                        { xtype: 'tbfill' },
                                        /*{
                                        iconCls: 'icon_add',
                                        text: 'Add',    
                                        scope: this,
                                        handler: function(){}
                                    }*/]}],                    
                    renderTo: Ext.get('lista_prospectos'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando prospectos {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
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
                            }
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Numero de contrato',
                        width: 110,
                        dataIndex: 'Numerocontrato'
                    },{
                        text: 'Numero emp. publica',
                        width: 110,
                        dataIndex: 'Numerocontratoemppub'
                    },{
                        text: 'Cliente',
                        width: 130,
                        dataIndex: 'cliente'
                    },{
                        text: 'Valor',
                        dataIndex: 'Valorcontrato',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'Anticipo',
                        dataIndex: 'Valoranticipo',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'Garantia',
                        dataIndex: 'Valorgarantia',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'Fecha fin contrato',
                        dataIndex: 'Fefincontrato',
                        align: 'right',
                        flex: 100,
                                    renderer: function(value,metaData,record,colIndex,store,view) {
                                    metaData.tdAttr = 'data-qtip="' + value+'"';
                                    return value;
                                    }			
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 75
                    },{
                        text: 'Acciones',
                        width: 90,
                        renderer: renderAcciones,
                    }]
                });            


            function renderAcciones(value, p, record) {
                    var iconos='';
                    var estadoIncidencia=true;
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';
					if(record.estado=='Pendiente'){
						iconos=iconos+'<b><a href="'+record.data.linkEditar+'" onClick="" title="Editar" class="button-grid-edit"></a></b>';	
						iconos=iconos+'<b><a href="#" onClick="eliminar(\''+record.data.linkEliminar+'\')" title="Eliminar" class="button-grid-delete"></a></b>';
					}
                    if(record.data.esFisico === 'S' && record.data.estado === 'Pendiente'){
                        iconos=iconos+'<b><a href="#" onClick="reenviarDocumento(\''+record.data.Numerocontrato+'\',\''+''+'\')" title="Reenviar documento" class="button-grid-agregarCorreo"></a></b>';
                        iconos=iconos+'<b><a href="#" onClick="notificarAutorizar(\''+record.data.Numerocontrato+'\',\''+''+'\')" title="Notificar autorizar contrato" class="button-grid-habilitar"></a></b>';
                    }
                    return Ext.String.format(
                                    iconos,
                        value,
                        '1',
                                    'nada'
                    );
            }

            var filterPanel = Ext.create('Ext.panel.Panel', {
                    bodyPadding: 7, // Don't want content to crunch against the borders
                    border: false,
                    buttonAlign: 'center',
                    layout: 
                    {
                        type: 'table',
                        columns: 4,
                        align: 'left'
                    },
                    bodyStyle: 
                    {
                        background: '#fff'
                    },
                    defaults: 
                    {
                        // applied to each contained panel
                        bodyStyle: 'padding:10px'
                    },
                    collapsible: true,
                    collapsed: true,
                    width: 870,
                    title: 'Criterios de busqueda',
                
                    buttons: [
                        
                        {
                            text: 'Buscar',
                            //xtype: 'button',
                            iconCls: "icon_search",
                            handler: Buscar,
                        },
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: function(){ limpiar();}
                        }
                        
                        ],                

                        items: 
                        [
                            DTFechaDesde,
                            {html: "&nbsp;", border: false, width: 50},
                            DTFechaHasta,
                            {html: "&nbsp;", border: false, width: 50},
                            estado_cmb,
                            {html: "&nbsp;", border: false, width: 50},
                            forma_pago_cmb,
                            {html: "&nbsp;", border: false, width: 50},
                            TFNumero,
                            {html: "&nbsp;", border: false, width: 50},                            
                        ],	
                renderTo: 'filtro_prospectos'
            }); 
            
});

function Buscar(){
		if  (( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
		{
			if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
			{
			   Ext.Msg.show({
			   title:'Error en Busqueda',
			   msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.ERROR
				});		 

			}
			else
			{
				store.load({params: {start: 0, limit: 10}});
			}
		}
		else
		{

			   Ext.Msg.show({
			   title:'Error en Busqueda',
			   msg: 'Por Favor Ingrese criterios de fecha.',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.ERROR
				});
		}
	}

function eliminar(direccion)
{
    //alert(direccion);
    
    Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
        if(btn=='yes'){
            Ext.Ajax.request({
                url: direccion,
                method: 'post',
                success: function(response){
                    var text = response.responseText;
                    store.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        }
    });
}

function limpiar(){
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");
    Ext.getCmp('idestado').setRawValue("");
    Ext.getCmp('idformapago').setRawValue("");
    Ext.getCmp('numContrato').setRawValue("");
    
}

/**
 * Documentación para el método 'reenviarDocumento'.
 *
 * Método para reenviar el documento del contrato al cliente.
 *
 * @param arrayCorreo    array   Array de correo de clientes.
 *
 * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
 * @version 1.0 04-09-2022
 */
 function reenviarDocumento(numeroContrato, numeroAdendum){

    var connReenviarCorreo = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {						
                    Ext.MessageBox.show({
                        msg: 'Reenviando los documentos de contrato al cliente, Por favor espere!!',
                        progressText: 'Reenviando correo...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200}
                    });				
                },
                scope: this
            }
        }
    });

    storeCargosVisibles = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetCorreosCliente,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: ''
            },
            extraParams:
            {
                numeroContrato: numeroContrato,
                numeroAdendum: numeroAdendum
            },
        },
        fields:
        [
            {name: 'valor', mapping: 'valor'}
        ]
    });

    var formPanel = Ext.create('Ext.form.Panel',
         {
             bodyPadding: 2,
             waitMsgTarget: true,
             fieldDefaults:
                 {
                     labelAlign: 'left',
                     labelWidth: 90,
                     msgTarget: 'side'
                 },
             layout:
                 {
                     type: 'table',                    
                     columns: 1
                 },
             items:
                 [
                     {
                         xtype: 'fieldset',
                         title: '',
                         defaultType: 'textfield',
                         defaults:
                             {
                                 width: 250
                             },
                         items:
                             [                       
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Seleccione correo:',
                                    id: 'comboCorreoEmpl',
                                    name: 'comboCorreoEmpl',
                                    store: storeCargosVisibles,
                                    displayField: 'valor',
                                    valueField: 'valor',
                                    queryMode: 'remote',
                                    emptyText: 'Seleccione',
                                    forceSelection: true
                                }
                             ]
                     }
                 ],
             buttons:
                 [
                     {
                         text: 'Reenviar',
                         formBind: true,
                         handler: function()
                         {
                             var valor = Ext.getCmp('comboCorreoEmpl').value;
                             if (valor == "")
                             {
                                 Ext.Msg.alert("Alerta", "Favor ingrese la descripcion del servicio a presentarse en la Factura!");
                             }
                             else
                             {
                                 connReenviarCorreo.request
                                 ({
                                     url: urlReenviarDocumentoContrato,
                                     method: 'post',
                                     waitMsg: 'Esperando Respuesta',
                                     params:
                                         {
                                            numeroContrato: numeroContrato,
                                            correoCliente: valor,
                                            tipoContrato: 'C'
                                         },
                                     success: function(response)
                                     {
                                         var respuesta = Ext.JSON.decode(response.responseText);
 
                                         if (respuesta.strStatus == "OK")
                                         {
                                             Ext.Msg.alert('MENSAJE ', 'Se reenvió el documento de contrato al siguiente correo: '+valor);
                                         }
                                         else
                                         {                                            
                                             Ext.Msg.alert('Error', respuesta.strMensaje);
                                         }
                                     },
                                     failure: function(result)
                                     {
                                         Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                     }
                                 });
                                 winReenviarDoc.destroy();
                             }
                         }
                     },
                     {
                         text: 'Cancelar',
                         handler: function()
                         {
                             winReenviarDoc.destroy();
                         }
                     }
                 ]
         });
 
     var winReenviarDoc = Ext.create('Ext.window.Window',
         {
             title: 'Reenviar el documento al cliente',
             modal: true,
             width: 320,
             closable: true,
             layout: 'fit',
             items: [formPanel]
         }).show();
}

/**
 * Documentación para el método 'reenviarDocumento'.
 *
 * Método para reenviar el documento del contrato al cliente.
 *
 * @param arrayCorreo    array   Array de correo de clientes.
 *
 * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
 * @version 1.0 04-09-2022
 */
function notificarAutorizar(numeroContrato, numeroAdendum) {
    Ext.Ajax.request({
        url: urlNotificarAutorizar,
        method: 'post',
        params: {
            numeroContrato: numeroContrato,
            numeroAdendum: numeroAdendum
        },
        success: function(response)
        {
            var respuesta = Ext.JSON.decode(response.responseText);
            if (respuesta.strStatus == "OK")
            {
                mostrarCorreosAutorizar(numeroContrato);
            }
            else
            {
                Ext.Msg.alert('Error', respuesta.message);
            }
        },  failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}

/**
 * Documentación para el método 'mostrarCorreosAutorizar'.
 * 
 * Método para mostrar los correos de los clientes que se van a autorizar.
 * 
 * @param numeroContrato    string  Número de contrato.
 * 
 * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
 * @version 1.0 04-09-2022
 */
function mostrarCorreosAutorizar(numeroContrato){
    var conn = new Ext.data.Connection({
        listeners: {
        'beforerequest': {
            fn: function (con, opt) {
            Ext.get(document.body).mask('Consultando Correo...');
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
        url: urlCorreoAutorizar,
        method: 'post',
        params: {
            idContrato: idContrato,
            idAdendum: 0
        },
        success: function(response)
        {
            var respuesta = Ext.JSON.decode(response.responseText);
            if (respuesta.strStatus == "OK")
            {
                llenarCorreosAutorizar(respuesta.arrayCorreo, numeroContrato);
            } else {
                Ext.Msg.alert('Error', respuesta.strMensaje);
            }
        },  failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        } 
    });
}

function llenarCorreosAutorizar(data, numeroContrato){
    var connAutorizar = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {						
                    Ext.MessageBox.show({
                        msg: 'Enviando correo al departamento de contrato para su posterior aprobación, Por favor espere!!',
                        progressText: 'Enviando...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200}
                    });				
                },
                scope: this
            }
        }
    });

    var storeCorreosAutorizar = Ext.create('Ext.data.Store', {
        fields: ['valor'],
        data: data
    });
    var formCorreoContrato = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        width: 350,
        layout: 'anchor',
        defaults: {
            anchor: '100%'
        },
        defaultType: 'textfield',
        items: [
            {
                xtype: 'combobox',
                fieldLabel: 'Correo:',
                name: 'correoContrato',
                id: 'correoContrato',
                store: storeCorreosAutorizar,
                displayField: 'valor',
                valueField: 'valor',
                queryMode: 'local',
                emptyText: 'Seleccione',
                forceSelection: true
            }
        ],
        buttons: [
            {
                text: 'Autorizar',
                formBind: true,
                handler: function()
                {
                    var valor = Ext.getCmp('correoContrato').value;
                    if (valor == "")
                    {
                        Ext.Msg.alert("Alerta", "Favor ingrese el correo del cliente!");
                    }
                    else
                    {
                        connAutorizar.request
                        ({
                            url: urlAutorizarContratoFisico,
                            method: 'post',
                            waitMsg: 'Esperando Respuesta',
                            timeout: 400000,
                            params:
                                {
                                    numeroContrato: numeroContrato,
                                    correo: valor
                                },
                            success: function(response)
                            {
                                var respuesta = Ext.JSON.decode(response.responseText);
                                if (respuesta.strStatus == "OK")
                                {
                                    Ext.Msg.alert('MENSAJE ', 'Se envió el correo al departamento de autorización de contrato.');
                                    store.load({params: {start: 0, limit: 10}});
                                }
                                else
                                {
                                    Ext.Msg.alert('Error', respuesta.message, function(btn) {
                                        if (btn == 'ok') {
                                            mostrarCorreosAutorizar(numeroContrato);
                                        }
                                    });
                                }
                                winVisualizarCorreo.destroy();
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                winVisualizarCorreo.destroy();
                            }
                        });
                    }
                }
            }]
    });

    var winVisualizarCorreo = Ext.create('Ext.window.Window',
         {
             title: 'Seleccionar el correo del departamento de contratos',
             modal: true,
             width: 320,
             closable: true,
             layout: 'fit',
             items: [formCorreoContrato]
         }).show();
}

