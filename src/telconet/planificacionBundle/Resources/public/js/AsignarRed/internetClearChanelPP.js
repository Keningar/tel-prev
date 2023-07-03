/**
 * Funcion que sirve para mostrar la pantalla para la asignacion 
 * de recursos de red para Producto Clear Channel Punto a Punto
 * 
 * @author Josue Valencia <ajvalencia@telconet.ec>
 * @version 1.0 26-10-2022
 * 
 * */

 var storeVlansDisponibles  = null;

 function showRecursoRedClearChannel(data)
 {
     Ext.get(grid.getId()).mask('Consultando Datos...');
     
         Ext.Ajax.request({ 
             url: getDatosFactibilidad,
             method: 'post',
             timeout: 400000,
             params: { 
                 idServicio:  data.get('id_servicio'),
                 ultimaMilla: data.get('ultimaMilla'),
                 tipoSolicitud: data.get('descripcionSolicitud'),
                 idSolicitud  : data.get('id_factibilidad'),
                 tipoRed      : data.get('strTipoRed')
             },
             success: function(response){
                 Ext.get(grid.getId()).unmask();
 
                 var json = Ext.JSON.decode(response.responseText);
                 //Activar o desactivar  bandera para ejecución manual de ventana de asignación de recursos de red para provincias
                 //-------------------------------------------------------------------------------------------
                   
                     if(json.status=="OK")
                     {
                         //-------------------------------------------------------------------------------------------
                         
                         var storePeDisponibles = new Ext.data.Store({  
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url : getObtenerPe,
                                reader: {
                                    type: 'json',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                            [
                                {name: 'id', mapping:  'id'},
                                {name: 'valor', mapping: 'valor'}
                            ]
                        });
 
                        var storePeClientes = new Ext.data.Store({  
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url : getObtenerPeCliente,
                                reader: {
                                    type: 'json',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                            [
                                {name: 'id', mapping:  'id'},
                                {name: 'valor', mapping: 'valor'}
                            ]
                        });
 
                        var storeVrfInternet = new Ext.data.Store({
                            pageSize: 100,
                            autoLoad: false,
                            proxy: {
                                type: 'ajax',
                                url: getVrfInternetClearChannel,
                                reader: {
                                    type: 'json',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                            [
                                {name: 'id', mapping:  'id'},
                                {name: 'valor', mapping: 'valor'}
                            ]
                        });
 
                            storeVlansDisponibles = new Ext.data.Store({
                            total: 'total',
                            proxy: {
                                type: 'ajax',
                                url: urlAjaxGetVlansDisponiblesClearCh,
                                timeout: 3000000,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'data'
                                },
                                actionMethods: {
                                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                },
                                extraParams: {
                                    idNombreElemento: '',
                                    idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                                    anillo: '0'
                                }
                            },
                            fields:
                                [
                                    {name: 'id'  , mapping: 'id'},
                                    {name: 'vlan', mapping: 'vlan'}
                                ]
                        });
                        
 
                        var storeProtocolosEnrutamiento = new Ext.data.Store({
                            proxy: {
                                type: 'ajax',
                                url: urlAjaxGetProtocolosEnrutamiento,
                                timeout: 600000,
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                },                            
                            },
                            fields:
                                [
                                    {name: 'descripcion', mapping: 'descripcion'}
                                ]
                        });
 
                        storeMascaras = new Ext.data.Store({
                            fields: ['display','value'],
                            data: [
                                {
                                    "display": "/24",
                                    "value": "255.255.255.0"
                                },
                                {
                                    "display": "/25",
                                    "value": "255.255.255.128"
                                },
                                {
                                    "display": "/26",
                                    "value": "255.255.255.192"
                                },
                                {
                                    "display": "/27",
                                    "value": "255.255.255.224"
                                },
                                {
                                    "display": "/28",
                                    "value": "255.255.255.240"
                                },
                                {
                                    "display": "/29",
                                    "value": "255.255.255.248"
                                },
                                {
                                    "display": "/30",
                                    "value": "255.255.255.252"
                                },
                                {
                                    "display": "/31",
                                    "value": "255.255.255.254"
                                }
                            ]
                        });
                         
                         var storeSubredDisponibles = new Ext.data.Store({
                            pageSize: 100,
                            autoLoad: false,
                            proxy: {
                                type: 'ajax',
                                url: urlObtenerSubredesElemento,
                                reader: {
                                    type: 'json',
                                    root: 'encontrados'
                                },
                                actionMethods: {
                                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                },
                                extraParams: {
                                    idElemento: '',
                                    anillo: '',
                                    tipo: '',
                                    uso: ''
                                }
                            },
                            fields:
                                    [
                                        {name: 'idSubred', mapping: 'idSubred'},
                                        {name: 'subred', mapping: 'subred'}
                                    ]
 
                                    
                        });
                        var htmlButtonVlanWan = '<div class="content-vlans" id="content-vlan-clearchannel" onclick="reservarVlansClearChannel(\'wan\',\'dedicado\')" \n\
                                              style="cursor:pointer;" title="Resevar VLAN WAN">\n\
                                              <i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>\n\
                                         </div>';
 
                        var objHtmlButtonWan = Ext.create('Ext.Component', {
                        html: htmlButtonVlanWan,
                        id: 'btnVlan',
                        padding: 1,
                        layout: 'anchor',
                        hidden: false,
                        });
                         //-------------------------------------------------------------------------------------------
 
                         var formPanel = Ext.create('Ext.form.Panel', {
                             bodyPadding: 2,
                             waitMsgTarget: true,
                             fieldDefaults: {
                                 labelAlign: 'left',
                                 labelWidth: 85, 
                                 msgTarget: 'side',
                                 bodyStyle: 'padding:20px'
                             },
                             layout: {
                                 type: 'table',
                                 // The total column count must be specified here
                                 columns: 3
                             },
                             defaults: {
                                 // applied to each contained panel
                                 bodyStyle: 'padding:20px'
                             },
                             items: [
                                 
                                 //informacion del servicio
                                 {
                                     colspan: 2,
                                     rowspan:2,
                                     xtype: 'panel',
                                     title: 'Información del Servicio',
                                     defaults: { 
                                         height: 100
                                     },
                                     items: [
 
                                     {
                                             xtype: 'container',
                                             layout: {
                                                 type: 'table',
                                                 columns: 5,
                                                 align: 'stretch'
                                             },
                                             items: [
                                                 {   width: '10%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'tipoOrden',
                                                     fieldLabel: 'Tipo Orden',
                                                     displayField: data.get("tipo_orden"),
                                                     value: data.get("tipo_orden"),
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '15%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'producto',
                                                     fieldLabel: 'Producto',
                                                     displayField: data.get("producto"),
                                                     value: data.get("producto"),
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '10%', border: false},
 
                                                 //---------------------------------------------
 
                                                 { width: '10%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'capacidad1',
                                                     fieldLabel: 'Capacidad1',
                                                     displayField: json.capacidad1,
                                                     value: json.capacidad1,
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '15%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'capacidad2',
                                                     fieldLabel: 'Capacidad2',
                                                     displayField: json.capacidad2,
                                                     value: json.capacidad2,
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '10%', border: false},
 
                                                 //---------------------------------------------
                                                 
                                                 { width: '10%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'tipoEnlace',
                                                     fieldLabel: 'Tipo Enlace',
                                                     displayField: data.get('tipo_enlace'),
                                                     value: data.get('tipo_enlace'),
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '15%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'tipoRed',
                                                     fieldLabel: 'Tipo Red',
                                                     displayField: data.get('tipoRedServicio'),
                                                     value: data.get('tipoRedServicio'),
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '15%', border: false},
                                                 { width: '15%', border: false},
                                                 { width: '10%', border: false}
                                                 
                                             ]
                                         }
 
                                     ]
                                 },//cierre de informacion del servicio
                                 
                                 //informacion del cliente
                                 {
                                     colspan: 2,
                                     rowspan:2,
                                     xtype: 'panel',
                                     title: 'Información del Cliente',
                                     defaults: { 
                                         height: 100
                                     },
                                     items: [
 
                                         {
                                             xtype: 'container',
                                             layout: {
                                                 type: 'table',
                                                 columns: 5,
                                                 align: 'stretch'
                                             },
                                             items: [
 
                                                 { width: '10%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'cliente',
                                                     fieldLabel: 'Cliente',
                                                     displayField: data.get('cliente'),
                                                     value: data.get('cliente'),
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '15%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'login',
                                                     fieldLabel: 'Login',
                                                     displayField: data.get('login2'),
                                                     value: data.get('login2'),
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '10%', border: false},
 
                                                 //---------------------------------------------
 
                                                 { width: '10%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'ciudad',
                                                     fieldLabel: 'Ciudad',
                                                     displayField: data.get('ciudad'),
                                                     value: data.get('ciudad'),
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '15%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'direccion',
                                                     fieldLabel: 'Direccion',
                                                     displayField: data.get('direccion'),
                                                     value: data.get('direccion'),
                                                     readOnly: true,
                                                     width: '30%'
                                                 },
                                                 { width: '10%', border: false},
 
                                                 //---------------------------------------------
 
                                                 { width: '10%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'sector',
                                                     fieldLabel: 'Sector',
                                                     displayField: data.get('nombreSector'),
                                                     value: data.get('nombreSector'),
                                                     readOnly: true,
                                                     width: '35%'
                                                 },
                                                 { width: '15%', border: false},
                                                 {
                                                     xtype: 'textfield',
                                                     name: 'esRecontratacion',
                                                     fieldLabel: 'Es Recontratacion',
                                                     displayField: data.get("esRecontratacion"),
                                                     value: data.get("esRecontratacion"),
                                                     readOnly: true,
                                                     width: '35%'
                                                 },
                                                 { width: '10%', border: false}
                                                 
                                                 //---------------------------------------------
 
                                             ]
                                         }
 
                                     ]
                                 },//cierre de la informacion del cliente
                                                         
                                 //informacion de los elementos del cliente
                                 {
                                    colspan: 5,
                                    rowspan:5,
                                     xtype: 'panel',
                                     title: 'Ingreso Información Técnica',
                                     items: [                       
                                         //informacion del elemento backbone y distribucion
                                         {
                                            xtype: 'fieldset',
                                            title: 'Elemento Padre',
                                            style: "font-weight:bold; margin-bottom: 15px;",
                                            defaults: {
                                                width: '350px'
                                            },
                                            layout: {
                                                type: 'table',
                                                columns: 3,
                                                align: 'stretch'
                                            },
                                            items: [
                                                
                                                {
                                                    xtype: 'radio',
                                                    x: 0,
                                                    y: 0,
                                                    boxLabel: 'PE',
                                                    name: 'pe_tn',
                                                    inputValue: 'PE',
                                                    id: 'pe_tn',
                                                    checked: true,
                                                    //text: 'Smaller Size',
                                                    handler: function() {
                                                        var radio1 = Ext.getCmp('pe_tn');
                                                        var radio2 = Ext.getCmp('pe_otros');
                                                        
                                                        if (radio1.getValue()) 
                                                        {
                                                            radio2.setValue(false);
                                                            Ext.getCmp('btnVlan').setDisabled(false);
                                                            Ext.getCmp('cbxPE').setVisible(true);
                                                            Ext.getCmp('cbxPEOther').setVisible(false);       
                                                            Ext.getCmp('cbxPEOther').reset();
                                                            Ext.getCmp('txtVlan').reset();
                                                            Ext.getCmp('txtVrf').reset();
                                                            Ext.getCmp('txtMask').reset();
                                                            Ext.getCmp('txtProtcol').reset();
                                                            Ext.getCmp('txtSubred').reset();

                                                            Ext.getCmp('cbxSubred').setVisible(true);
                                                            Ext.getCmp('cbxVrf').setVisible(true);
                                                            Ext.getCmp('txtVrf').setVisible(false);
                                                            Ext.getCmp('cbxVlan').setVisible(true);
                                                            Ext.getCmp('txtVlan').setVisible(false);     
                                                            Ext.getCmp('cbxProtcol').setVisible(true);
                                                            Ext.getCmp('txtProtcol').setVisible(false);
                                                            Ext.getCmp('cbxMask').setVisible(true);
                                                            Ext.getCmp('txtMask').setVisible(false);
                                                            Ext.getCmp('cbxSubred').setVisible(true);
                                                            Ext.getCmp('txtSubred').setVisible(false);
                                                            return;
                                                        }
 
                                                    }
                                                },
                                                
                                                {
                                                    xtype: 'radio',
                                                    x: 0,
                                                    y: 0,
                                                    boxLabel: 'Otros PE',
                                                    name: 'pe_otros',
                                                    inputValue: 'Otros PE',
                                                    id: 'pe_otros',
                                                    checked: false,
                                                    //text: 'Smaller Size',
                                                    handler: function() {
                                                        var radio1 = Ext.getCmp('pe_tn');
                                                        var radio2 = Ext.getCmp('pe_otros');
                                                        
                                                        if (radio2.getValue()) 
                                                        {
                                                            radio1.setValue(false);
                                                            Ext.getCmp('btnVlan').setDisabled(true);
                                                            Ext.getCmp('cbxPE').setVisible(false);
                                                            Ext.getCmp('cbxPE').reset();
                                                            Ext.getCmp('cbxVlan').reset();
                                                            Ext.getCmp('cbxVrf').reset();
                                                            Ext.getCmp('cbxMask').reset();
                                                            Ext.getCmp('cbxProtcol').reset();
                                                            Ext.getCmp('cbxSubred').reset();
                                                            Ext.getCmp('cbxPEOther').setVisible(true);      
                                                            Ext.getCmp('cbxVrf').setVisible(false);
                                                            Ext.getCmp('txtVrf').setVisible(true);
                                                            Ext.getCmp('cbxVlan').setVisible(false);
                                                            Ext.getCmp('txtVlan').setVisible(true);     
                                                            Ext.getCmp('cbxProtcol').setVisible(false);
                                                            Ext.getCmp('txtProtcol').setVisible(true);
                                                            Ext.getCmp('cbxMask').setVisible(false);
                                                            Ext.getCmp('txtMask').setVisible(true);
                                                            Ext.getCmp('cbxSubred').setVisible(false);
                                                            Ext.getCmp('txtSubred').setVisible(true);
                                                            return;
                                                        }
 
                                                    }
                                                },
                                                objHtmlButtonWan,
                                                {
                                                    id: 'cbxPE',
                                                    name: 'cbxPE',
                                                    xtype: 'combobox',
                                                    queryMode: 'local',
                                                    fieldLabel: 'Seleccione PE',
                                                    displayField: 'id',
                                                    valueField: 'valor',
                                                    store: storePeDisponibles,
                                                    editable: false,
                                                    width: '20%',
                                                    labelAlign: 'top',
                                                    listeners: {
                                                        select: function(combo){
                                                            Ext.getCmp('cbxVrf').reset();
                                                            Ext.getCmp('cbxVlan').reset();
                                                            Ext.getCmp('cbxProtcol').reset();
                                                            Ext.getCmp('cbxMask').reset();
                                                            Ext.getCmp('cbxVrf').setDisabled(true);
                                                            Ext.getCmp('cbxVlan').setDisabled(true);
                                                            Ext.getCmp('cbxProtcol').setDisabled(true);
                                                            Ext.getCmp('cbxMask').setDisabled(true);
                                                            
                                                            Ext.getCmp('cbxVlan').value = "loading..";
                                                            Ext.getCmp('cbxVlan').setRawValue("loading..");
                                                            storeVlansDisponibles.proxy.extraParams = {
                                                                idNombreElemento: Ext.getCmp('cbxPE').getValue(),
                                                                idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                                                                anillo: '0'
                                                            };
                                                            storeVlansDisponibles.load({callback: function () {
                                                                Ext.getCmp('cbxVlan').setDisabled(false);
                                                            }});
                                                        }
                                                    }
                                                },                                               
                                                {
                                                    id: 'cbxPEOther',
                                                    name: 'cbxPEOther',
                                                    xtype: 'combobox',
                                                    queryMode: 'local',
                                                    fieldLabel: 'Seleccione PE',
                                                    displayField: 'id',
                                                    valueField: 'valor',
                                                    store: storePeClientes,
                                                    editable: false,
                                                    hidden:true,
                                                    width: '20%',
                                                    labelAlign: 'top'
                                                },
                                                {
                                                    id: 'cbxVrf',
                                                    name: 'cbxVrf',
                                                    xtype: 'combobox',
                                                    fieldLabel: 'VRF',
                                                    queryMode: 'local',
                                                    displayField: 'valor',
                                                    valueField: 'id',
                                                    store: storeVrfInternet,
                                                    editable: false,
                                                    hidden:false,
                                                    width: '20%',
                                                    labelAlign: 'top'
 
                                                },
                                                {
                                                    id: 'txtVrf',
                                                    name: 'txtxVrf',
                                                    xtype: 'textfield',
                                                    fieldLabel: 'VRF',
                                                    queryMode: 'local',
                                                    editable: false,
                                                    width: '20%',
                                                    hidden:true,
                                                    labelAlign: 'top',
                                                    disabled: true
                                                },
                                                {
                                                    id: 'cbxVlan',
                                                    name: 'cbxVlan',
                                                    xtype: 'combobox',
                                                    fieldLabel: 'VLAN',
                                                    queryMode: 'local',
                                                    displayField: 'vlan',
                                                    valueField: 'id',
                                                    store: storeVlansDisponibles,
                                                    width: '20%',
                                                    editable: false,
                                                    hidden: false,
                                                    labelAlign: 'top',
                                                    listeners: {
                                                        select: function(combo){
                                                            Ext.getCmp('cbxProtcol').reset();
                                                            Ext.getCmp('cbxProtcol').setDisabled(true);
                                                            Ext.getCmp('cbxMask').reset();
                                                            Ext.getCmp('cbxMask').setDisabled(true);
                                                            Ext.getCmp('cbxVrf').reset();
                                                            Ext.getCmp('cbxVrf').setDisabled(true);
                                                            Ext.getCmp('cbxVrf').value = "loading..";
                                                            Ext.getCmp('cbxVrf').setRawValue("loading..");
                                                            
                                                            storeVrfInternet.load({callback: function (response) {
                                                                
                                                                Ext.getCmp('cbxVrf').setRawValue("Seleccione...");
                                                                Ext.getCmp('cbxVrf').setDisabled(false);
                                                                storeProtocolosEnrutamiento.load({callback: function () {
                                                                    Ext.getCmp('cbxProtcol').setRawValue("Seleccione...");
                                                                    Ext.getCmp('cbxMask').setRawValue("Seleccione...");
                                                                    Ext.getCmp('cbxProtcol').setDisabled(false);
                                                                    Ext.getCmp('cbxMask').setDisabled(false);
                                                                }});
                                                             }});
                                                           
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype: 'numberfield',
                                                    id: 'txtVlan',
                                                    name: 'txtVlan',
                                                    fieldLabel: 'VLAN',
                                                    queryMode: 'local',
                                                    editable: true,
                                                    hidden: true,
                                                    width: '20%',
                                                    labelAlign: 'top',
                                                },
                                                {
                                                    id: 'cbxProtcol',
                                                    name: 'cbxProtcol',
                                                    xtype: 'combobox',
                                                    fieldLabel: 'Protocolo',
                                                    queryMode: 'local',
                                                    displayField:   'descripcion',
                                                    valueField:     'descripcion',
                                                    store:          storeProtocolosEnrutamiento,
                                                    editable: false,
                                                    hidden:false,
                                                    width: '20%',
                                                    labelAlign: 'top'
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'txtProtcol',
                                                    name: 'txtProtcol',
                                                    fieldLabel: 'Protocolo',
                                                    queryMode: 'local',
                                                    editable: true,
                                                    hidden:true,
                                                    width: '20%',
                                                    labelAlign: 'top'
                                                },
                                                {
                                                    id: 'cbxMask',
                                                    name: 'cbxMask',
                                                    xtype: 'combobox',
                                                    fieldLabel: 'Mascara',
                                                    queryMode: 'local',
                                                    store: storeMascaras,
                                                    displayField:   'display',
                                                    valueField:     'value',
                                                    editable: false,
                                                    hidden: false,
                                                    width: '20%',
                                                    labelAlign: 'top',
                                                    listeners: {
                                                        select: function(combo){
                                                            Ext.getCmp('cbxSubred').reset();
                                                            storeSubredDisponibles.proxy.extraParams = {
                                                                idElemento: Ext.getCmp('cbxPE').getValue(),
                                                                anillo: '',
                                                                tipo: 'WAN',
                                                                uso: 'CLEAR CHANNEL',
                                                                mascara: combo.getValue()
                                                            };
                                                            storeSubredDisponibles.load({callback: function () {
                                                                Ext.getCmp('cbxSubred').setDisabled(false);
                                                            }});
                                                            
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype: 'combobox',
                                                    id: 'txtMask',
                                                    name: 'txtMask',
                                                    fieldLabel: 'Mascara',
                                                    queryMode: 'local',
                                                    store: storeMascaras,
                                                    displayField:   'display',
                                                    valueField:     'value',
                                                    editable: true,
                                                    hidden:true,
                                                    width: '20%',
                                                    labelAlign: 'top'
                                                }
 
                                            ]
                                        },
 
 
                                        {
                                            xtype: 'fieldset',
                                            title: 'Asignación Subred',
                                            defaultType: 'textfield',
                                            style: "font-weight:bold; margin-bottom: 15px;",
                                            defaults: {
                                                width: '350px'
                                            },
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    title: '* WAN',
                                                    defaultType: 'textfield',
                                                    style: "font-weight:bold; margin-bottom: 15px;",
                                                    defaults: {
                                                        width: '350px'
                                                    },
                                                    items: [
                                                        {
                                                            id: 'cbxSubred',
                                                            name: 'cbxSubred',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'Subred',
                                                            queryMode: 'local',
                                                            displayField: 'subred',
                                                            valueField: 'idSubred',
                                                            editable: false,
                                                            hidden:false,
                                                            width: '20%',
                                                            store: storeSubredDisponibles,
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            id: 'txtSubred',
                                                            name: 'txtxSubred',
                                                            xtype: 'textfield',
                                                            fieldLabel: 'Subred',
                                                            queryMode: 'local',
                                                            displayField: 'valor',
                                                            editable: true,
                                                            width: '20%',
                                                            hidden:true,
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            id: 'txtEmprRol',
                                                            xtype: 'textfield',
                                                            name: 'txtEmprRol',
                                                            fieldLabel: 'idEmpresa',
                                                            displayField: data.get('id_persona_empresa_rol'),
                                                            value: data.get('id_persona_empresa_rol'),
                                                            readOnly: true,
                                                            hidden:true,
                                                            width: '30%'
                                                        },
 
                                                    ]
                                                }
                                            ]
                                        }
 
 
                                     ]
 
                                 },//cierre informacion de los elementos del cliente
                             ],
                             buttons: 
                             [{
                                 text: 'Grabar',
                                 formBind: true,
                                 handler: function(){
                                     var interfaceElemento   = "";
                                     var tipoIngreso         = "";
                                     var idElementoPadre     = "";
                                     var vlan                = "";
                                     var vrf                 = "";
                                     var protocolo           = "";
                                     var mascaraPublica      = "";
                                     var ipPublica           = "";
                                     var validacion=true;
                                     var ms_error="";
 
                                     if (Ext.getCmp('pe_tn').getValue())
                                     {
                                        tipoIngreso         = "T";
                                        idElementoPadre     = Ext.getCmp('cbxPE').getValue();
                                        vlan                = Ext.getCmp('cbxVlan').getValue();
                                        vrf                 = Ext.getCmp('cbxVrf').getValue();
                                        protocolo           = Ext.getCmp('cbxProtcol').getValue();
                                        mascaraPublica      = Ext.getCmp('cbxMask').getValue();
                                        ipPublica           = Ext.getCmp('cbxSubred').getValue();
                                     }
                                     else
                                     {
                                        tipoIngreso         = "C";
                                        idElementoPadre     = Ext.getCmp('cbxPEOther').getValue();
                                        vlan                = Ext.getCmp('txtVlan').getValue();
                                        vrf                 = Ext.getCmp('txtVrf').getValue();
                                        protocolo           = Ext.getCmp('txtProtcol').getValue();
                                        mascaraPublica      = Ext.getCmp('txtMask').getValue();
                                        ipPublica           = Ext.getCmp('txtSubred').getValue();
 
                                        // Patron para validar la ip
                                        const patronIp=new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/gm);
                                        if (ipPublica.search(patronIp)!=0&&ipPublica != "") {
 
                                            // Ip no es correcta
                                            validacion=false;
                                            ms_error=ms_error+"<br/>La ip ingresada no cumple con el formato correcto";
                                        } 
                                    }
                                     
                                    if(idElementoPadre == null || vlan == null || protocolo == null || mascaraPublica == null || ipPublica == null 
                                        || idElementoPadre == "" || vlan == "" || protocolo == "" || mascaraPublica == "" || ipPublica == "" 
                                        || ipPublica =="Seleccione..." || vlan =="Seleccione..." || protocolo =="Seleccione..." || mascaraPublica =="Seleccione...")
                                    {
                                    
                                        validacion=false;
                                        ms_error=ms_error+"Debe llenar todos los campos obligatorios";
                                        
                                    }
 
                                     if(validacion)
                                     {
                                         Ext.get(formPanel.getId()).mask('Guardando datos!');                                    
                                         Ext.Ajax.request({
                                             url: asignarRecursosInternetClearChannel,
                                             method: 'post',
                                             timeout: 100000,
                                             params: {
                                                 tipoIngreso:            tipoIngreso,
                                                 idServicio:             data.get('id_servicio'),
                                                 idDetalleSolicitud:     data.get('id_factibilidad'),
                                                 tipoSolicitud:          data.get('descripcionSolicitud'),
                                                 idElementoPadre:        idElementoPadre,
                                                 vlan:                   vlan,
                                                 vrf:                    vrf,
                                                 mascaraPublica:         mascaraPublica,
                                                 protocolo:              protocolo,
                                                 idSubred:               ipPublica
                                             },
                                             success: function(response){
                                                 Ext.get(formPanel.getId()).unmask();
                                                 var strEstado= response.responseText[11] + response.responseText[12] ;
                                                 if(strEstado == "OK")
                                                 {
                                                    Ext.Msg.alert('Mensaje','Se Asignaron los Recursos de Red!', function(btn){
                                                        if(btn=='ok')
                                                        {
                                                            win.destroy();
                                                            store.load();
                                                        }
                                                    });
                                                 }
                                                 else if(strEstado == "ER")
                                                 {
                                                    Ext.Msg.alert('Mensaje ',
                                                    '<b>Observaci&oacute;n :</b> No Existen IPs disponibles para la Subred P&uacute;blica' );
                                                 }
                                                 else{
                                                     Ext.Msg.alert('Mensaje ',response.responseText );
                                                 }
                                             },
                                             failure: function(result)
                                             {
                                                 Ext.get(formPanel.getId()).unmask();
                                                 Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                             }
                                         });
 
                                     }
                                     else
                                     {
                                         Ext.Msg.alert("Validacion",ms_error);
                                     }
                                 }//handler
                             },
                             {
                                 text: 'Cancelar',
                                 handler: function()
                                 {
                                     win.destroy();
                                 }
                             }]
                         });
 
                         var win = Ext.create('Ext.window.Window', {
                             title: 'Asignar Recurso de Red - CLEAR CHANNEL PUNTO A PUNTO',
                             modal: true,
                             id:'winRecursosRed',
                             width: 1100,
                             closable: true,
                             layout: 'fit',
                             items: [formPanel]
                         }).show();
                         storePeDisponibles.load({});
                         storePeClientes.load({});
                         storeProtocolosEnrutamiento.load({});
                         storeSubredDisponibles.on('load', function()
                                {
                                    Ext.getCmp('cbxSubred').setRawValue("Seleccione...");
                                });
                         
                        
                     }// if(json.status=="OK")
                     else
                     {
                         Ext.MessageBox.show({
                             title: 'Error',
                             msg: json.msg,
                             buttons: Ext.MessageBox.OK,
                             icon: Ext.MessageBox.ERROR
                         });   
                     }
                 
                 //-------------------------------------------------------------------------------------------
                     
             },//cierre response
             failure: function(result) {
                 Ext.MessageBox.show({
                     title: 'Error',
                     msg: result.responseText,
                     buttons: Ext.MessageBox.OK,
                     icon: Ext.MessageBox.ERROR
                 });
             }
     });   
 }
 
 function reservarVlansClearChannel(tipo,recurso)
 {
     var content = 'content-vlan-clearchannel';
     
     $("#"+content).find("i").remove();
     $("#"+content).prepend('<i class="fa fa-toggle-on fa-2x" aria-hidden="true"></i>');
     
     Ext.get(Ext.get('winRecursosRed')).mask('Consultando Información para reserva de Vlan...');
     
     Ext.Ajax.request({
         url: urlAjaxGetInformacionVlansCH,
         method: 'post',
         params: {
             tipoVlan       : tipo.toUpperCase()
         },
         success: function(response) 
         {
             Ext.get(Ext.get('winRecursosRed')).unmask();
             
             var json = Ext.decode(response.responseText);
             
             var min = json.minRango;
             var max = json.maxRango;
 
             var formPanelReservaLans = Ext.create('Ext.form.Panel', {
                 buttonAlign: 'center',
                 BodyPadding: 10,
                 bodyStyle: "background: white; padding: 5px; border: 0px none;",
                 frame: true,
                 items: [
                     {
                         xtype: 'fieldset',
                         title: '',
                         layout: {
                             tdAttrs: {style: 'padding: 5px;'},
                             type: 'table',
                             columns: 2,
                             pack: 'center'
                         },
                         items: [
                             {
                                 xtype: 'fieldset',
                                 title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Información Vlan</b>',
                                 defaultType: 'textfield',
                                 style: "font-weight:bold; margin-bottom: 5px;",
                                 layout: 'anchor',
                                 defaults: {
                                     width: '350px'
                                 },
                                 items: [
                                     {
                                         xtype: 'textfield',
                                         fieldLabel: '<b>Tipo Vlan a reservar</b>',
                                         value: tipo.toUpperCase(),
                                         width: 250,
                                         readOnly: true
                                     },
                                     {
                                         xtype: 'textfield',
                                         fieldLabel: '<b>Rango Definido</b>',
                                         value: min + "-" + max,
                                         width: 250,
                                         readOnly: true
                                     },
                                     {
                                         xtype: 'numberfield',
                                         id: 'vlanSugerida',
                                         name: 'vlanSugerida',
                                         fieldLabel: '<b>Sugerir Vlan</b>',
                                         minValue: min,
                                         maxValue:max,
                                         width: 250
                                     }
                                 ]
                             }
                         ]
                     }
                 ],
                 buttons: [
                     {
                         text: '<i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;<b>Guardar/Sugerir Vlan</b>',
                         handler: function()
                         {
                             var strVlan = Ext.getCmp('vlanSugerida').getValue();
                             var intIdElemento = Ext.getCmp('cbxPE').getValue();
                             var intIdPersonaEmpresaRol = Ext.getCmp('txtEmprRol').getValue();
                             if(!Ext.isEmpty(strVlan) && (strVlan < min || strVlan > max))
                             {
                                 Ext.Msg.alert('Alerta','La Vlan ingresada no se encuentra dentro del Rango permitido');
                             }
                             else
                             {
                                 Ext.get(Ext.get('winReservaVlans')).mask('Reservando Vlan en el Cliente...');
                                 
                                 Ext.Ajax.request({
                                     url: urlAjaxGuardarVlanCH,
                                     method: 'post',
                                     params: 
                                     {
                                         tipoVlan            : 'WAN',
                                         vlanSugerida        : strVlan,
                                         idPersonaEmpresaRol : intIdPersonaEmpresaRol,
                                         idPe                : intIdElemento
                                     },
                                     success: function(response) 
                                     {
                                         Ext.get(Ext.get('winReservaVlans')).unmask();
 
                                         var json = Ext.decode(response.responseText);
                                         
                                         Ext.Msg.alert('Mensaje', json.mensaje);
                                         
                                         if(tipo.toUpperCase() === 'WAN')
                                         {
                                            storeVlansDisponibles.proxy.extraParams = {
                                                idNombreElemento: intIdElemento,
                                                idPersonaEmpresaRol : intIdPersonaEmpresaRol,
                                                anillo: '0'
                                        };
                                        storeVlansDisponibles.load({callback: function () {
                                            Ext.getCmp('cbxVlan').setDisabled(false);
                                        }});
                                         }
                                         
                                         winReservaVlans.close();
                                         winReservaVlans.destroy();
                                         $("#" + content).find("i").remove();
                                         $("#" + content).prepend('<i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>');
                                     },
                                     failure: function(result)
                                     {
                                         Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                     }
                                 });
                             }
                         }
                     },
                     {
                         text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;<b>Cerrar</b>',
                         handler: function()
                         {
                             winReservaVlans.close();
                             winReservaVlans.destroy();
                             $("#" + content).find("i").remove();
                             $("#" + content).prepend('<i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>');
                         }
                     }
                 ]
             });
 
             var winReservaVlans = Ext.widget('window', {
                 title: '<b>Reservación de VLANS</b>',
                 layout: 'fit',
                 id:'winReservaVlans',
                 resizable: false,
                 modal: true,
                 closable: false,
                 items: [formPanelReservaLans]
             });
 
             winReservaVlans.show();
         },
         failure: function(result)
         {
             Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
         }
     });
 }