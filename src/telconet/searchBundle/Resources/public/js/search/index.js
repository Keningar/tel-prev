
var winBusquedaAvanzada = "";

function showBusquedaAvanzada(searchLogin){
//     Ext.MessageBox.wait("Cargando...");
    
    if(!winBusquedaAvanzada)
    {
        Ext.tip.QuickTipManager.init();
        
            /* DATOS CLIENTE */
            storeFormasContactoBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetFormasContacto',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'forma_contacto', mapping:'forma_contacto'},
                            {name:'id_forma_contacto', mapping:'id_forma_contacto'}
                        ],
                 listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             forma_contacto: '&nbsp;',
                             id_forma_contacto: null
                         }]);
                         Ext.getCmp('forma_contacto_cliente_avanzada').queryMode = 'local';
                    }      
                }, 
                //autoLoad: true
            });
            
            storeOFicinasBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetOficinas',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'oficina', mapping:'oficina'},
                            {name:'id_oficina', mapping:'id_oficina'}
                        ],
                 listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             oficina: '&nbsp;',
                             id_oficina: null
                         }]);
                         Ext.getCmp('id_oficina_cliente_avanzada').queryMode = 'local';
                    }      
                }, 
                //autoLoad: true
            });
            
            panelDatosClienteBusquedaAvanzada = Ext.create('Ext.Panel', {
                autoScroll: true,
                defaults :{
                    autoScroll: true
                },
                //frame:true,
                border:false,
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Datos Cliente',
//                         defaultType: 'textfield',
//                        style: "font-weight:bold; margin-bottom: 15px;",
//                        layout: 'anchor',
//                         defaults: {
//                             width: '400px'
//                         },
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Identificacion *',
                                style: 'color: red',
                                name: 'identificacion_cliente_avanzada',
                                id: 'identificacion_cliente_avanzada',
				width: 350
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Nombre(s) *',
                                style: 'color: red',
                                name: 'nombres_cliente_avanzada',
                                id: 'nombres_cliente_avanzada',
				width: 350
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Apellido(s) *',
                                  style: 'color: red',
                                name: 'apellidos_cliente_avanzada',
                                id: 'apellidos_cliente_avanzada',
				width: 350
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Razon Social *',
                                style: 'color: red',
                                name: 'razon_social_cliente_avanzada',
                                id: 'razon_social_cliente_avanzada',
				width: 350
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Representante Legal',
                                name: 'representante_legal_cliente_avanzada',
                                id: 'representante_legal_cliente_avanzada',
				width: 350
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Direccion *',
                                style: 'color: red',
                                name: 'direccion_cliente_avanzada',
                                id: 'direccion_cliente_avanzada',
				width: 350
                            },
                            {
                                  xtype: 'combobox',
                                  id: 'id_oficina_cliente_avanzada',
                                  name: 'id_oficina_cliente_avanzada',
                                  fieldLabel: 'Oficina',
                                  emptyText: "Seleccione",
                                  width: 350,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'oficina',
                                  valueField: 'id_oficina',
                                  selectOnTab: true,
                                  store: storeOFicinasBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                            },
                            {
                                 xtype: 'panel',
                                 border:false,
                                 //frame:true,
//                                 layout: { type: 'hbox', align: 'stretch' },
                                 items: [
                                    {
                                          xtype: 'combobox',
                                          id: 'forma_contacto_cliente_avanzada',
                                          name: 'forma_contacto_cliente_avanzada',
                                          fieldLabel: 'Forma Contacto',
                                          emptyText: "Seleccione",
                                          width:350,
                                          editable: false,
        //                                  typeAhead: true,
                                          triggerAction: 'all',
                                          displayField:'forma_contacto',
                                          valueField: 'id_forma_contacto',
                                          selectOnTab: true,
                                          store: storeFormasContactoBusquedaAvanzada,              
                                          lazyRender: true,
                                          queryMode: "remote",
                                          listClass: 'x-combo-list-small',
                                          listeners:{
                                                select:{
                                                    fn:function(comp, record, index) {
                                                        if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;"){
                                                           comp.setValue(null);
                                                           Ext.getCmp('valor_forma_contacto_cliente_avanzada').setVisible(false);
                                                           Ext.getCmp('valor_forma_contacto_cliente_avanzada').setValue(null);
                                                        }else{   
                                                            Ext.getCmp('valor_forma_contacto_cliente_avanzada').setVisible(true);
                                                        }
                                                    }
                                                }
                                           }
                                      },
//                                      {
//                                            html:"&nbsp;",
//                                            border:false,
//                                            width:20
//                                      },
                                      {
                                        xtype: 'textfield',
//                                        fieldLabel: ' ',
                                        style: "margin-left: 25.8%;",
                                        width:246,
                                        name: 'valor_forma_contacto_cliente_avanzada',
                                        id: 'valor_forma_contacto_cliente_avanzada',
                                        hidden: true
                                      }
                                 ]
                            }
                        ]
                    }
                ]
          });
          
          /* FIN DATOS CLIENTE*/
          
          /* DATOS PUNTO */
          storeTiposNegocioBusquedaAvanzada = new Ext.data.Store({ 
            total: 'total',
            proxy: {
                timeout: 400000,                type: 'ajax',
                url : '/search/ajaxGetTiposNegocio',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                    [
                        {name:'tipo_negocio', mapping:'tipo_negocio'},
                        {name:'id_tipo_negocio', mapping:'id_tipo_negocio'}
                    ],
             listeners: {
                load: function(store, records) {
                     store.insert(0, [{
                         tipo_negocio: '&nbsp;',
                         id_tipo_negocio: null
                     }]);
                     Ext.getCmp('tipo_negocio_punto_avanzada').queryMode = 'local';
                }      
            }, 
            //autoLoad: true
          });

          storeTiposUbicacionBusquedaAvanzada = new Ext.data.Store({ 
            total: 'total',
            proxy: {
                timeout: 400000,                type: 'ajax',
                url : '/search/ajaxGetTiposUbicacion',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                    [
                        {name:'tipo_ubicacion', mapping:'tipo_ubicacion'},
                        {name:'id_tipo_ubicacion', mapping:'id_tipo_ubicacion'}
                    ],
             listeners: {
                load: function(store, records) {
                     store.insert(0, [{
                         tipo_ubicacion: '&nbsp;',
                         id_tipo_ubicacion: null
                     }]);
                     Ext.getCmp('tipo_ubicacion_punto_avanzada').queryMode = 'local';
                }      
            }, 
            //autoLoad: true
          });
            
          storeVendedoresBusquedaAvanzada = new Ext.data.Store({ 
            total: 'total',
            proxy: {
                timeout: 600000,                type: 'ajax',
                url : '/search/ajaxGetVendedores',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                    [
                        {name:'vendedor', mapping:'vendedor'},
                        {name:'id_vendedor', mapping:'id_vendedor'}
                    ],
             listeners: {
                load: function(store, records) {
                     store.insert(0, [{
                         vendedor: '&nbsp;',
                         id_vendedor: null
                     }]);
                     Ext.getCmp('vendedor_punto_avanzada').queryMode = 'local';
                }      
            }, 
            //autoLoad: true
          });
          
          storeEstadosPuntoBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                pageSize: 50,
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetEstadosPunto',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'estado_punto', mapping:'estado_punto'}
                        ],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             estado_punto: '&nbsp;'
                         }]);
                         Ext.getCmp('estado_punto_avanzada').queryMode = 'local';
                    }      
                },        
                //autoLoad: true
          });
        
          panelDatosPuntoBusquedaAvanzada = Ext.create('Ext.Panel', {
              autoScroll: true,
              defaults :{
                      autoScroll: true
              },
              //frame:true,
              border:false,
              items: [
                      {
                          xtype: 'fieldset',
                          title: 'Datos Del Punto',
//                           defaultType: 'textfield',
//                           style: "font-weight:bold;",
//                          layout: 'anchor',
//                           defaults: {
//                               width: '350px'
//                           },
                          items: [
                              { 
                                  xtype: 'textfield',
                                  fieldLabel: 'Login *',
                                  style: 'color: red',
                                  name: 'login_punto_avanzada',
                                  id: 'login_punto_avanzada',
				  width: 350
                              },
                              { 
                                  xtype: 'textfield',
                                  fieldLabel: 'Nombre Punto *',
                                  style: 'color: red',
                                  name: 'nombre_punto_avanzada',
                                  id: 'nombre_punto_avanzada',
				  width: 350
                              },
                              {
                                  xtype: 'textfield',
                                  fieldLabel: 'Descripcion',
                                  name: 'descripcion_punto_avanzada',
                                  id: 'descripcion_punto_avanzada',
				  width: 350
                              },
                              {
                                  xtype: 'textfield',
                                  fieldLabel: 'Direccion *',
                                  style: 'color: red',
                                  name: 'direccion_punto_avanzada',
                                  id: 'direccion_punto_avanzada',
				  width: 350
                              },
                              {
                                  xtype: 'textfield',
                                  fieldLabel: 'Ciudad',
                                  name: 'ciudad_punto_avanzada',
                                  id: 'ciudad_punto_avanzada',
				  width: 350
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'tipo_negocio_punto_avanzada',
                                  name: 'tipo_negocio_punto_avanzada',
                                  fieldLabel: 'Tipo Negocio',
//                                  typeAhead: true,
                                  editable: false,
                                  emptyText: "Seleccione",
                                  triggerAction: 'all',
                                  displayField:'tipo_negocio',
                                  valueField: 'id_tipo_negocio',
                                  selectOnTab: true,
                                  store: storeTiposNegocioBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  width: 350,
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'tipo_ubicacion_punto_avanzada',
                                  name: 'tipo_ubicacion_punto_avanzada',
                                  fieldLabel: 'Tipo Ubicacion',
//                                  typeAhead: true,
                                  editable: false,
                                  emptyText: "Seleccione",
                                  triggerAction: 'all',
                                  displayField:'tipo_ubicacion',
                                  valueField: 'id_tipo_ubicacion',
                                  selectOnTab: true,
                                  store: storeTiposUbicacionBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  width: 350,
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'vendedor_punto_avanzada',
                                  name: 'vendedor_punto_avanzada',
                                  fieldLabel: 'Vendedor',
//                                  typeAhead: true,
                                  editable: false,
                                  emptyText: "Seleccione",
                                  triggerAction: 'all',
                                  displayField:'vendedor',
                                  valueField: 'id_vendedor',
                                  selectOnTab: true,
                                  store: storeVendedoresBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  width: 350,
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'estado_punto_avanzada',
                                  name: 'estado_punto_avanzada',
                                  fieldLabel: 'Estado',
//                                  typeAhead: true,
                                  editable: false,
                                  emptyText: "Seleccione",
                                  triggerAction: 'all',
                                  displayField:'estado_punto',
                                  valueField: 'estado_punto',
                                  selectOnTab: true,
                                  store: storeEstadosPuntoBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  width: 350,
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              }
                          ]
                      }
                ]
            });
            /* FIN DATOS PUNTO*/
        
            /* TAB MODULO COMERCIAL */
            
            // DOCUMENTOS
            storeTiposDocumentosComercialesBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetTiposDocumentosComerciales',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'tipo_documento', mapping:'tipo_documento'},
                            {name:'id_tipo_documento', mapping:'id_tipo_documento'}
                        ],
                listeners: {
                        load: function(store, records) {
                             store.insert(0, [{
                                 tipo_documento: '&nbsp;',
                                 id_tipo_documento: null
                             }]);
                             Ext.getCmp('tipo_documento_comercial_avanzada').queryMode = 'local';
                        }      
                }, 
                //autoLoad: true
            });
            
            storeFormasPagosContratoBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetFormasPagosContrato',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'forma_pago', mapping:'forma_pago'},
                            {name:'id_forma_pago', mapping:'id_forma_pago'}
                        ],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             forma_pago: '&nbsp;',
                             id_forma_pago: null
                         }]);
                         Ext.getCmp('forma_pago_comercial_avanzada').queryMode = 'local';
                    }      
                }, 
                //autoLoad: true
            });
            
            storeEstadosTiposDocumentoComercialBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetEstadosTiposDocumentosComerciales',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'estado_documento', mapping:'estado_documento'}
                        ],
                listeners: {
                        load: function(store, records) {
                             store.insert(0, [{
                                 estado_documento: '&nbsp;'
                             }]);
                             Ext.getCmp('estado_tipo_documento_comercial_avanzada').queryMode = 'local';
                        }      
                }, 
                //autoLoad: true
            });
            
            // FIN DOCUMENTOS
            
            // SERVICIOS
            Ext.define('modelListadoServiciosBusquedaAvanzada', {
                    extend: 'Ext.data.Model',
                    fields: [
                            {name: 'servicio_por', type: 'string'}
                    ]
            });
            
            storeListadoServiciosPorBusquedaAvanzada = new Ext.data.Store({ 
                    model: 'modelListadoServiciosBusquedaAvanzada',
                    data : [
                            {servicio_por:'&nbsp;' },
                            {servicio_por:'Catalogo' },
                            {servicio_por:'Portafolio' }
                    ]
            });
            
            storeListadoServiciosBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetListadoServiciosPor',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                [
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'servicio', mapping:'servicio'}
                ],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             servicio: '&nbsp;',
                             id_servicio: null
                         }]);
                    }      
                },         
            });
            
            storeEstadosServicioBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetEstadoServicios',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'estado_servicio', mapping:'estado_servicio'}
                        ],
                 listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             estado_servicio: '&nbsp;',
                         }]);
                         Ext.getCmp('estado_servicio_comercial_avanzada').queryMode = 'local';
                    }      
                }, 
                //autoLoad: true
            });
            // FIN SERVICIOS
        
            tabComercialBusquedaAvanzada = Ext.create('Ext.Panel', {
              title: 'Comercial',
	      width: 725,
	      height: 503,
              autoScroll: true,
              defaults :{
                      autoScroll: true
              },
              //frame:true,
              border:false,
              items: [
                      {
                          xtype: 'fieldset',
                          title: 'Documentos',
			  autoScroll: true,
//                           defaultType: 'textfield',
//                           style: "font-weight:bold;",
//                          layout: 'anchor',
//                           defaults: {
//                               width: '350px'
//                           },
                          items: [
                              {
                                  xtype: 'combobox',
                                  id: 'tipo_documento_comercial_avanzada',
                                  name: 'tipo_documento_comercial_avanzada',
                                  fieldLabel: 'Tipo Documento *',
                                  style: 'color: red',
                                  emptyText: "Seleccione",
                                  width:350,
                                  editable: false,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'tipo_documento',
                                  valueField: 'id_tipo_documento',
                                  selectOnTab: true,
                                  store: storeTiposDocumentosComercialesBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              { 
                                  xtype: 'textfield',
                                  fieldLabel: 'Numero Documento *',
                                  style: 'color: red',
                                  name: 'numero_documento_comercial_avanzada',
                                  id: 'numero_documento_comercial_avanzada',
                                  width:350,
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'forma_pago_comercial_avanzada',
                                  name: 'forma_pago_comercial_avanzada',
                                  fieldLabel: 'Forma Pago',
                                  emptyText: "Seleccione",
                                  width:350,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'forma_pago',
                                  valueField: 'id_forma_pago',
                                  selectOnTab: true,
                                  store: storeFormasPagosContratoBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'estado_tipo_documento_comercial_avanzada',
                                  name: 'estado_tipo_documento_comercial_avanzada',
                                  fieldLabel: 'Estado Documento',
                                  emptyText: "Seleccione",
                                  width:350,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'estado_documento',
                                  valueField: 'estado_documento',
                                  selectOnTab: true,
                                  store: storeEstadosTiposDocumentoComercialBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              {
                                  xtype: 'datefield',
                                  id: 'fecha_aut_documento_comercial_avanzada',
                                  name: 'fecha_aut_documento_comercial_avanzada',
                                  fieldLabel: 'Fecha Autorizacion',
                                  labelAlign : 'left',
                                  format: 'Y-m-d',
                                  width:350,
                                  editable: false
                              },
                              {
                                  xtype: 'datefield',
                                  id: 'fecha_creacion_documento_comercial_avanzada',
                                  name: 'fecha_creacion_documento_comercial_avanzada',
                                  fieldLabel: 'Fecha Creacion',
                                  labelAlign : 'left',
                                  format: 'Y-m-d',
                                  width:350,
                                  editable: false
                              },
                              { 
                                  xtype: 'textfield',
                                  fieldLabel: 'Usuario Creacion(login):',
                                  name: 'usuario_documento_comercial_avanzada',
                                  id: 'usuario_documento_comercial_avanzada',
                                  width:350,
                              },
                          ]
                      },
                      {
                          xtype: 'fieldset',
                          title: 'Servicios',
			  autoScroll: true,
//                           defaultType: 'textfield',
//                           style: "font-weight:bold;",
//                          layout: 'anchor',
//                           defaults: {
//                               width: '350px'
//                           },
                          items: [
                              {
                                  xtype: 'combobox',
                                  id: 'servicios_por_comercial_avanzada',
                                  name: 'servicios_por_comercial_avanzada',
                                  fieldLabel: 'Por:',
                                  emptyText: "Seleccione",
                                  width:350,
                                  editable: false,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'servicio_por',
                                  valueField: 'servicio_por',
                                  selectOnTab: true,
                                  store: storeListadoServiciosPorBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "local",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "&nbsp;"){
                                                   comp.setValue(null);
                                                }else{   
                                                    storeListadoServiciosBusquedaAvanzada.proxy.extraParams = { por : comp.getValue() };
                                                    storeListadoServiciosBusquedaAvanzada.load({params: {}});
                                                }
                                            }
                                        }
                                   }
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'producto_plan_avanzada',
                                  name: 'producto_plan_avanzada',
                                  fieldLabel: 'Listado',
                                  emptyText: "Seleccione Por",
                                  width:350,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'servicio',
                                  valueField: 'id_servicio',
                                  selectOnTab: true,
                                  store: storeListadoServiciosBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "local",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'estado_servicio_comercial_avanzada',
                                  name: 'estado_servicio_comercial_avanzada',
                                  fieldLabel: 'Estado',
                                  emptyText: "Seleccione",
                                  width:350,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'estado_servicio',
                                  valueField: 'estado_servicio',
                                  selectOnTab: true,
                                  store: storeEstadosServicioBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              {
                                 xtype: 'panel',
                                 border:false,
				 autoScroll: true,
                                 //frame:true,
                                 layout: { type: 'hbox', align: 'stretch' },
                                 items: [
                                         {
                                            xtype: 'datefield',
                                            id: 'fecha_cancelacion_desde_avanzada',
                                            name: 'fecha_cancelacion_desde_avanzada',
                                            fieldLabel: 'Fecha Cancelacion Desde',
                                            labelAlign : 'left',
                                            format: 'Y-m-d',
                                            width:325,
                                            editable: false
                                        },
                                        {
                                            html:"&nbsp;",
                                            border:false,
                                            width:20
                                        },
                                        {
                                            xtype: 'datefield',
                                            id: 'fecha_cancelacion_hasta_avanzada',
                                            name: 'fecha_cancelacion_hasta_avanzada',
                                            fieldLabel: 'Fecha Cancelacion Hasta',
                                            labelAlign : 'left',
                                            format: 'Y-m-d',
          //                                  minValue: Ext.getCmp('fecha_cancelacion_desde_avanzada').getDate(),
                                            width:325,
                                            editable: false
                                        },
                                 ]
                              },
                                      {
                                 xtype: 'panel',
                                 border:false,
				 autoScroll: true,
                                 //frame:true,
                                 layout: { type: 'hbox', align: 'stretch' },
                                 items: [
                                         {
                                            xtype: 'datefield',
                                            id: 'fecha_corte_desde_avanzada',
                                            name: 'fecha_corte_desde_avanzada',
                                            fieldLabel: 'Fecha Corte Desde',
                                            labelAlign : 'left',
                                            format: 'Y-m-d',
                                            width:325,
                                            editable: false
                                        },
                                        {
                                            html:"&nbsp;",
                                            border:false,
                                            width:20
                                        },
                                        {
                                            xtype: 'datefield',
                                            id: 'fecha_corte_hasta_avanzada',
                                            name: 'fecha_corte_hasta_avanzada',
                                            fieldLabel: 'Fecha Corte Hasta',
                                            labelAlign : 'left',
                                            format: 'Y-m-d',
          //                                  minValue: Ext.getCmp('fecha_corte_desde_avanzada').getDate(),
                                            width:325,
                                            editable: false
                                        }
                                 ]
                              }
                          ]
                      }
                ]
            });
            
            /* FIN TAB MODULO COMERCIAL */
	    
	    /* TAB MODULO TECNICO */
            
            // BACKBONE
            storeTiposElementosBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetTiposElementos',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'tipo_elemento', mapping:'tipo_elemento'},
                            {name:'id_tipo_elemento', mapping:'id_tipo_elemento'}
                        ],
                listeners: {
                        load: function(store, records) {
                             store.insert(0, [{
                                 tipo_elemento: '&nbsp;',
                                 id_tipo_elemento: null
                             }]);
                             Ext.getCmp('tipo_elemento_tecnico_avanzada').queryMode = 'local';
                        }      
                }, 
                //autoLoad: true
            });
            
            storeModelosElementosBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetModelosElementos',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'modelo_elemento', mapping:'modelo_elemento'},
                            {name:'id_modelo_elemento', mapping:'id_modelo_elemento'}
                        ],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             modelo_elemento: '&nbsp;',
                             id_modelo_elemento: null
                         }]);
                         Ext.getCmp('modelo_elemento_tecnico_avanzada').queryMode = 'local';
                    }      
                }, 
                //autoLoad: true
            });
            
            storeElementosBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetElementos',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'elemento', mapping:'elemento'},
                            {name:'id_elemento', mapping:'id_elemento'}
                        ],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             elemento: '&nbsp;',
                             id_elemento: null
                         }]);
                         Ext.getCmp('elemento_tecnico_avanzada').queryMode = 'remote';
                    }      
                }, 
                //autoLoad: true
            });
	    
	    storeInterfacesElementoBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetInterfacesElemento',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'interface_elemento', mapping:'interface_elemento'},
                            {name:'id_interface_elemento', mapping:'id_interface_elemento'}
                        ],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             interface_elemento: '&nbsp;',
                             id_interface_elemento: null
                         }]);
                         Ext.getCmp('interface_elemento_tecnico_avanzada').queryMode = 'remote';
                    }      
                }, 
                //autoLoad: true
            });
            
            // FIN BACKBONE
            
            // CLIENTE
            
            storeTiposMediosBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetTiposMedios',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                [
                    {name:'tipo_medio', mapping:'tipo_medio'},
                    {name:'id_tipo_medio', mapping:'id_tipo_medio'}
                ],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             tipo_medio: '&nbsp;',
                             id_tipo_medio: null
                         }]);
			 Ext.getCmp('tipo_medio_tecnico_avanzada').queryMode = 'local';
                    }      
                },         
            });
            
            // FIN CLIENTE
        
            tabTecnicoBusquedaAvanzada = Ext.create('Ext.Panel', {
              title: 'Tecnico',
              autoScroll: true,
	      width: 725,
	      height: 503,
              defaults :{
                      autoScroll: true
              },
              //frame:true,
              border:false,
              items: [
                      {
                          xtype: 'fieldset',
                          title: 'Backbone',
//                           defaultType: 'textfield',
//                           style: "font-weight:bold;",
//                          layout: 'anchor',
//                           defaults: {
//                               width: '350px'
//                           },
                          items: [
                              {
                                  xtype: 'combobox',
                                  id: 'tipo_elemento_tecnico_avanzada',
                                  name: 'tipo_elemento_tecnico_avanzada',
                                  fieldLabel: 'Tipo Elemento',
                                  emptyText: "Seleccione",
                                  width:350,
                                  editable: false,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'tipo_elemento',
                                  valueField: 'id_tipo_elemento',
                                  selectOnTab: true,
                                  store: storeTiposElementosBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;")
                                                   comp.setValue(null);
						     
						Ext.getCmp('modelo_elemento_tecnico_avanzada').setValue(null);
						Ext.getCmp('elemento_tecnico_avanzada').setValue(null);
						Ext.getCmp('interface_elemento_tecnico_avanzada').setValue(null);
						
						storeModelosElementosBusquedaAvanzada.proxy.extraParams = { idTipoElemento : comp.getValue() };
						storeModelosElementosBusquedaAvanzada.load({params: {}});

                                            }
                                        }
                                   }
                              },
                              {
                                  xtype: 'combobox',
                                  id: 'modelo_elemento_tecnico_avanzada',
                                  name: 'modelo_elemento_tecnico_avanzada',
                                  fieldLabel: 'Modelo Elemento',
                                  emptyText: "Seleccione",
                                  width:350,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'modelo_elemento',
                                  valueField: 'id_modelo_elemento',
                                  selectOnTab: true,
                                  store: storeModelosElementosBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
						
						Ext.getCmp('elemento_tecnico_avanzada').setValue(null);
						Ext.getCmp('interface_elemento_tecnico_avanzada').setValue(null);
						
						storeElementosBusquedaAvanzada.proxy.extraParams = {  
												      idTipoElemento : Ext.getCmp('tipo_elemento_tecnico_avanzada').getValue(),
												      idModeloElemento : comp.getValue() };
						storeElementosBusquedaAvanzada.load({params: {}});

                                                   
                                            }
                                        }
                                   }
                              },
                              {
                            xtype: 'combobox',
                            id: 'elemento_tecnico_avanzada',
                            name: 'elemento_tecnico_avanzada',
                            fieldLabel: 'Elemento',
                            emptyText: "Digite",
                            width: 350,
                            displayField: 'elemento',
                            valueField: 'id_elemento',
                            store: storeElementosBusquedaAvanzada,
                            queryMode: "remote",
                            listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
						
						Ext.getCmp('interface_elemento_tecnico_avanzada').setValue(null);
						
                            storeInterfacesElementoBusquedaAvanzada.proxy.extraParams = { idElemento : comp.getValue() };
						storeInterfacesElementoBusquedaAvanzada.load({params: {}});
                                            }
                                        }
                                   }
                              },
			      {
                                  xtype: 'combobox',
                                  id: 'interface_elemento_tecnico_avanzada',
                                  name: 'interface_elemento_tecnico_avanzada',
                                  fieldLabel: 'Interface Elemento',
                                  emptyText: "Seleccione",
                                  width:350,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'interface_elemento',
                                  valueField: 'id_interface_elemento',
                                  selectOnTab: true,
                                  store: storeInterfacesElementoBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              }
                          ]
                      },
                      {
                          xtype: 'fieldset',
                          title: 'Cliente',
//                           defaultType: 'textfield',
//                           style: "font-weight:bold;",
//                          layout: 'anchor',
//                           defaults: {
//                               width: '350px'
//                           },
                          items: [
                              {
                                  xtype: 'combobox',
                                  id: 'tipo_medio_tecnico_avanzada',
                                  name: 'tipo_medio_tecnico_avanzada',
                                  fieldLabel: 'Ultima Milla',
                                  emptyText: "Seleccione",
                                  width:350,
                                  editable: false,
//                                  typeAhead: true,
                                  triggerAction: 'all',
                                  displayField:'tipo_medio',
                                  valueField: 'id_tipo_medio',
                                  selectOnTab: true,
                                  store: storeTiposMediosBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;")
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
                              {
                                xtype: 'textfield',
                                fieldLabel: 'Host Cpe',
                                name: 'host_cpe_tecnico_avanzada',
                                id: 'host_cpe_tecnico_avanzada',
				width:350,
			      },
			      {
                                xtype: 'textfield',
                                fieldLabel: 'Serie Cpe',
                                name: 'serie_cpe_tecnico_avanzada',
                                id: 'serie_cpe_tecnico_avanzada',
				width:350,
                              },
			      {
                                xtype: 'textfield',
                                fieldLabel: 'Mac Cpe *',
                                style: 'color: red',
                                name: 'mac_cpe_tecnico_avanzada',
                                id: 'mac_cpe_tecnico_avanzada',
				width:350,
                              },
			      {
                                xtype: 'textfield',
                                fieldLabel: 'Ip *',
                                style: 'color: red',
                                name: 'ip_tecnico_avanzada',
                                id: 'ip_tecnico_avanzada',
				width:350,
                              },
                          ]
                      }
                ]
            });
            
            /* FIN TAB MODULO TECNICO */
            
            /* GRID RESPUESTA BUSQUEDA AVANZADA */
            storeResponseSearch = new Ext.data.Store({ 
	    pageSize: 14,
	    total: 'total',
            loadMask: false,
	    proxy: {
		timeout: 400000,                type: 'ajax',
		url : '/search/ajaxSearch',
		reader: {
		    type: 'json',
		    totalProperty: 'total',
            root: 'encontrados',
            metaProperty: 'myMetaData'
		},
		actionMethods: {
		    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		}
	    },
	    fields:
		[
		    {name:'id_cliente_grid_avanzada', mapping:'id_cliente_grid_avanzada'},
		    {name:'id_punto_grid_avanzada', mapping:'id_punto_grid_avanzada'},
		    {name:'razon_social_cliente_grid_avanzada', mapping:'razon_social_cliente_grid_avanzada'},
		    {name:'nombre_completo_cliente_grid_avanzada', mapping:'nombre_completo_cliente_grid_avanzada'},
		    {name:'login_grid_avanzada', mapping:'login_grid_avanzada'},
                    {name:'nombre_punto_grid_avanzada', mapping:'nombre_punto_grid_avanzada'},
		    {name:'descripcion_punto_grid_avanzada', mapping:'descripcion_punto_grid_avanzada'},
                    {name:'direccion_punto_grid_avanzada', mapping:'direccion_punto_grid_avanzada'},
		    {name:'oficina_grid_avanzada', mapping:'oficina_grid_avanzada'},
		    {name:'estado_grid_avanzada', mapping:'estado_grid_avanzada'},
            {name:'strPtoFacturacion', mapping:'strPtoFacturacion'},
                    {name:'uri_info_comercial', mapping:'uri_info_comercial'},
                    {name:'uri_info_tecnica', mapping:'uri_info_tecnica'},
                    {name:'uri_info_financiera', mapping:'uri_info_financiera'},
                    {name:'uri_info_soporte', mapping:'uri_info_soporte'},
                    {name:'uri_info_contrato', mapping:'uri_info_contrato'},
                    {name:'verResumen', mapping:'verResumen'},
                    {name:'linkVerResumen', mapping:'linkVerResumen'},
                    {name:'arrayRoles', mapping:'arrayRoles'},
                    {name:'strUrlAjaxSetPuntoSession', mapping:'strUrlAjaxSetPuntoSession'}
		],
		listeners: {
		      load: function(records, operation, success) {
                Ext.get('formBusquedaAvanzada').unmask();
                var intCantidad = records.data.length;
                var strMensaje = storeResponseSearch.getProxy().getReader().jsonData.myMetaData;
                var boolContinuar = true;
                if( strMensaje == "Existe por lo menos 1 cliente que cumple con los parámetros ingresados pero no tiene permisos para poder visualizar su información. Por favor dirigirse al módulo Clientes" )
                {
                    Ext.Msg.alert('Mensaje Informativo', strMensaje);
                    boolContinuar = false;
                }
                if (success) 
                 {
                    if( intCantidad > 0)
                    {
                        formBusquedaAvanzada.setVisible(false);
                        panelGridResponseBusquedaAvanzada.setVisible(true);
                    }
                    else
                    {
                        if( boolContinuar )
                        {
                            if(prefijoEmpresaTW === 'MD' && !boolPermisoTwig)
                            {
                                winBusquedaAvanzada.hide();
                            }
                            Ext.Msg.alert('Mensaje','No se encontro resultados con los parametros ingresados o <b>Cliente a Buscar no Contiene creado un Punto.</b>');
                        }
                    }
                }
                else 
                {
                    if( boolContinuar )
                    {
                        Ext.Msg.alert('Error ','Error en la Busqueda realizada. Favor notificar a sistemas.');
                    }
			    }
		      }
		}
	});
		    
		    
	gridResponseBusquedaAvanzada = Ext.create('Ext.grid.Panel', {
                dockedItems: [{
                    xtype: 'pagingtoolbar',
                    store: storeResponseSearch,   // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true,
		    displayMsg: 'Mostrando {0} - {1} de {2}',
		    emptyMsg: "No hay datos que mostrar."
                }],
		height: 510,
                width: 1200,
                loadMask: false,
		store: storeResponseSearch,
		autoScroll: true,
		disableSelection:true,
		viewConfig: {
		    emptyText: 'No hay datos para mostrar',
		    enableTextSelection: true
		},
		//frame:true,
		columns:[
                    {
                        xtype: 'actioncolumn',
                        header: 'Ir',
                        align: 'center',
                        width: 220,
                        items: [
                                {
                                    tooltip: 'Datos del Punto',
                                    getClass: function(v, meta, rec) {                                        
                                        if (rec.data.oficina_grid_avanzada.indexOf("MEGADATOS") !== -1)
                                        {
                                            var arrayRoles  = rec.get('arrayRoles');
                                            var boolPermiso = arrayRoles['verDatosPunto'];
                            
                                            if(!boolPermiso){ 
                                                return 'button-grid-invisible';
                                            }
                                        } 
                                        return 'button-grid-comercial';
                                    },
                                    handler: function(grid, rowIndex, colIndex) {
                                        var rec = storeResponseSearch.getAt(rowIndex);
                                        Ext.MessageBox.wait("Cargando...");										
                                        
                                        Ext.Ajax.request({
                                            url: '/search/ajaxSetPuntoSession',
                                            method: 'post',
                                            params: { 
                                                idPersona: rec.data.id_cliente_grid_avanzada,
                                                idPunto: rec.data.id_punto_grid_avanzada
                                            },
                                            success: function(response){
                                                var text = response.responseText;
                                                
                                                if(text === "OK")
                                                {
                                                    window.location = rec.data.uri_info_comercial; 
                                                }else{
                                                    Ext.MessageBox.hide();
                                                    Ext.Msg.alert('Error',text); 
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.MessageBox.hide();
                                                Ext.Msg.alert('Error',result.responseText);
                                            }
                                        }); 
                                    }
                                },
                                {
                                    getClass: function(v, meta, rec) {
                                        if (rec.data.oficina_grid_avanzada.indexOf("MEGADATOS") !== -1)
                                        {
                                            var arrayRoles  = rec.get('arrayRoles');
                                            var boolPermiso = arrayRoles['verDatosTecnicos'];
                            
                                            if(!boolPermiso)
                                            { 
                                                return 'button-grid-invisible';
                                            }
                                        } 
                                        return 'button-grid-tecnica';
                                                                           
                                    },
                                    tooltip: 'Datos Tecnicos',
                                    handler: function(grid, rowIndex, colIndex) {
                                        var rec = storeResponseSearch.getAt(rowIndex);
                                        Ext.MessageBox.wait("Cargando...");
                                        
                                        Ext.Ajax.request({
                                            url: '/search/ajaxSetPuntoSession',
                                            method: 'post',
                                            params: { 
                                                idPersona: rec.data.id_cliente_grid_avanzada,
                                                idPunto: rec.data.id_punto_grid_avanzada
                                            },
                                            success: function(response){
                                                var text = response.responseText;
                                                
                                                if(text === "OK")
                                                {
                                                    window.location = rec.data.uri_info_tecnica; 
                                                }else{
                                                    Ext.MessageBox.hide();
                                                    Ext.Msg.alert('Error',text); 
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.MessageBox.hide();
                                                Ext.Msg.alert('Error',result.responseText);
                                            }
                                        }); 
                                    }
                                },
                                {
                                    getClass: function(v, meta, rec) {
                                        
                                        if (rec.data.oficina_grid_avanzada.indexOf("MEGADATOS") !== -1)
                                        {
                                            var arrayRoles  = rec.get('arrayRoles');
                                            var boolPermiso = arrayRoles['verEstadoCtaPto'];
                            
                                            if(!boolPermiso){ 
                                                return 'button-grid-invisible';
                                            }
                                        } 
                                        return 'button-grid-financiera';
                                    },
                                    tooltip: 'Facturas',
                                    handler: function(grid, rowIndex, colIndex) {
                                        var rec = storeResponseSearch.getAt(rowIndex);
                                        Ext.MessageBox.wait("Cargando...");
                                        
                                        Ext.Ajax.request({
                                            url: '/search/ajaxSetPuntoSession',
                                            method: 'post',
                                            params: { 
                                                idPersona: rec.data.id_cliente_grid_avanzada,
                                                idPunto: rec.data.id_punto_grid_avanzada
                                            },
                                            success: function(response){
                                                var text = response.responseText;
                                                
                                                if(text === "OK")
                                                {
                                                    window.location = rec.data.uri_info_financiera; 
                                                }else{
                                                    Ext.MessageBox.hide();
                                                    Ext.Msg.alert('Error',text); 
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.MessageBox.hide();
                                                Ext.Msg.alert('Error',result.responseText); 
                                            }
                                        }); 
                                    }
                                },
				
				/*
				 * <arsuarez@telconet.ec> V 1.0 21-07-2014
				 * Busqueda avanzada hacia el modulo de Soporte
				 */
				{
                                    getClass: function(v, meta, rec) {
                                        
                                        if (rec.data.oficina_grid_avanzada.indexOf("MEGADATOS") !== -1)
                                        {
                                            var arrayRoles  = rec.get('arrayRoles');
                                            var boolPermiso = arrayRoles['verCasos'];
                            
                                            if(!boolPermiso){ 
                                                return 'button-grid-invisible';
                                            }
                                        } 
                                        return 'button-grid-soporte';
                                    },
                                    tooltip: 'Casos',
                                    handler: function(grid, rowIndex, colIndex) {
                                        var rec = storeResponseSearch.getAt(rowIndex);
                                        Ext.MessageBox.wait("Cargando...");
                                        
                                        Ext.Ajax.request({
                                            url: '/search/ajaxSetPuntoSession',
                                            method: 'post',
                                            params: { 
                                                idPersona: rec.data.id_cliente_grid_avanzada,
                                                idPunto: rec.data.id_punto_grid_avanzada
                                            },
                                            success: function(response){
                                                var text = response.responseText;
                                                
                                                if(text === "OK")
                                                {
                                                    window.location = rec.data.uri_info_soporte; 
                                                }else{
                                                    Ext.MessageBox.hide();
                                                    Ext.Msg.alert('Error',text); 
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.MessageBox.hide();
                                                Ext.Msg.alert('Error',result.responseText); 
                                            }
                                        }); 
                                    }
                                },
				/*
				 * <efranco@telconet.ec> V 1.0 07-09-2014
				 * Resumen Comercial, Financiero, Cobranzas, IPCCL y AT de un Cliente
				 */
				{
                                    getClass: function(v, meta, rec)
                                    {
                                        var verResumen     = rec.get('verResumen');
                                        if(rec.data.oficina_grid_avanzada.indexOf("MEGADATOS") !== -1)
                                        {
                                            var arrayRoles  = rec.get('arrayRoles');
                                            var boolPermiso = arrayRoles['verResumen'];
                                            if(verResumen == 'N' || !boolPermiso){ 
                                                return 'button-grid-invisible';
                                            }
                                        } 
                                        else 
                                        {
                                            if(verResumen == 'N'){ 
                                                return 'button-grid-invisible';
                                            }
                                        }
                                        return 'button-grid-show without-margin-left cursor-pointer';
                                    },
                                    tooltip: 'Resumen Cliente',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        var rec                       = storeResponseSearch.getAt(rowIndex);
                                        var verResumen                = rec.get('verResumen');
                                        var linkResumen               = rec.get('linkVerResumen');
                                        var strUrlAjaxSetPuntoSession = rec.get('strUrlAjaxSetPuntoSession');
                                        
                                        Ext.MessageBox.wait("Cargando...");
                                        
                                        if( verResumen == 'S')
                                        { 
                                            Ext.Ajax.request
                                            ({
                                                url: strUrlAjaxSetPuntoSession,
                                                method: 'post',
                                                params: 
                                                { 
                                                    idPersona: rec.data.id_cliente_grid_avanzada,
                                                    idPunto: rec.data.id_punto_grid_avanzada
                                                },
                                                success: function(response)
                                                {
                                                    var text = response.responseText;

                                                    if(text === "OK")
                                                    {
                                                        window.location = linkResumen;
                                                    }
                                                    else
                                                    {
                                                        Ext.MessageBox.hide();
                                                        Ext.Msg.alert('Error',text); 
                                                    }
                                                },
                                                failure: function(result)
                                                {
                                                    Ext.MessageBox.hide();
                                                    Ext.Msg.alert('Error',result.responseText);
                                                }
                                            }); 
                                        }
                                    }
                                },
                                //boton adicional para conectar al cliente con el OSS
                                {
                                    tooltip: 'Diagnostico OSS',
                                    getClass: function(v, meta, rec) {
                                        
                                        if (rec.data.oficina_grid_avanzada.indexOf("MEGADATOS") !== -1)
                                        {
                                            var arrayRoles  = rec.get('arrayRoles');
                                            var boolPermiso = arrayRoles['verDiagnosticoOss'];
                            
                                            if(!boolPermiso){ 
                                                return 'button-grid-invisible';
                                            }
 
                                            return 'button-grid-oss';
                                        } 
                                        
                                        
                                    },
                                    handler: function(grid, rowIndex, colIndex) {
                                        var rec = storeResponseSearch.getAt(rowIndex);
                                        Ext.Ajax.request({
                                            method: 'post',
                                            url   :  '/search/ajaxGuardarLog',
                                            params: { 
                                                      strObservacion: 'DIAGNOSTICO OSS'                                            
                                            },
                                            async :  false
                                        });
                                        window.open("https://oss.netlife.net.ec/soporte/busqueda_login/"+rec.data.login_grid_avanzada); 
                                    }
                                },
                                {
                                    tooltip: 'Ver Contrato',
                                    getClass: function(v, meta, rec) 
                                    {
                                        if(rec.data.oficina_grid_avanzada.indexOf("MEGADATOS") !== -1)
                                        {
                                            return 'button-grid-telcos button-grid-contrato';
                                        }                                        
                                        return 'button-grid-invisible';
                                    },
                                    handler: function(grid, rowIndex, colIndex) {
                                       
                                        var rec = storeResponseSearch.getAt(rowIndex);
                                        Ext.MessageBox.wait("Cargando...");										
                                        
                                        Ext.Ajax.request({
                                            url: '/search/ajaxSetPuntoSession',
                                            method: 'post',
                                            params: { 
                                                idPersona: rec.data.id_cliente_grid_avanzada,
                                                idPunto: rec.data.id_punto_grid_avanzada
                                            },
                                            success: function(response){
                                                var text = response.responseText;
                                                
                                                if(text === "OK")
                                                {
                                                    window.location = rec.data.uri_info_contrato; 
                                                }else{
                                                    Ext.MessageBox.hide();
                                                    Ext.Msg.alert('Error',text); 
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.MessageBox.hide();
                                                Ext.Msg.alert('Error',result.responseText);
                                            }
                                        }); 
                                    }
                                },
                            ]
                        },
                        {
			  id: 'uri_info_comercial',
			  header: 'uri_info_comercial',
			  dataIndex: 'uri_info_comercial',
			  hidden: true,
			  hideable: false
			},
                        {
			  id: 'uri_info_tecnica',
			  header: 'uri_info_tecnica',
			  dataIndex: 'uri_info_tecnica',
			  hidden: true,
			  hideable: false
			},
                        {
			  id: 'uri_info_financiera',
			  header: 'uri_info_financiera',
			  dataIndex: 'uri_info_financiera',
			  hidden: true,
			  hideable: false
			},
			 {
			  id: 'uri_info_soporte',
			  header: 'uri_info_soporte',
			  dataIndex: 'uri_info_soporte',
			  hidden: true,
			  hideable: false
			},
                        {
			  id: 'uri_info_contrato',
			  header: 'uri_info_contrato',
			  dataIndex: 'uri_info_contrato',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'id_cliente_grid_avanzada',
			  header: 'id_cliente_grid_avanzada',
			  dataIndex: 'id_cliente_grid_avanzada',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'id_punto_grid_avanzada',
			  header: 'id_punto_grid_avanzada',
			  dataIndex: 'id_punto_grid_avanzada',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'nombre_completo_cliente_grid_avanzada',
			  header: 'Nombre Completo',
			  dataIndex: 'nombre_completo_cliente_grid_avanzada',
			  width: 190,
			  sortable: true
			},
                        {
			  id: 'nombre_punto_grid_avanzada',
			  header: 'Nombre Punto',
			  dataIndex: 'nombre_punto_grid_avanzada',
			  width: 100,
			  sortable: true
			},
			{
			  id: 'razon_social_cliente_grid_avanzada',
			  header: 'Razon Social',
			  dataIndex: 'razon_social_cliente_grid_avanzada',
			  width: 180,
			  sortable: true
			},
			{
			  id: 'login_grid_avanzada',
			  header: 'Login',
			  dataIndex: 'login_grid_avanzada',
			  width: 100,
			  sortable: true
			},
			{
			  id: 'strPtoFacturacion',
			  header: 'Pto.Facturación',
			  dataIndex: 'strPtoFacturacion',
			  width: 140,
              align:'center',
			  sortable: true
			},            
			{
			  id: 'estado_grid_avanzada',
			  header: 'Estado Punto',
			  dataIndex: 'estado_grid_avanzada',
			  width: 85,
			  sortable: true
			},
                        {
			  id: 'direccion_punto_grid_avanzada',
			  header: 'Direccion Punto',
			  dataIndex: 'direccion_punto_grid_avanzada',
			  width: 170,
			  sortable: true
			},
			{
			  id: 'oficina_grid_avanzada',
			  header: 'Oficina',
			  dataIndex: 'oficina_grid_avanzada',
			  width: 140,
			  sortable: true
			}           
		    ],
		    plugins: [{
			ptype: 'rowexpander',
			pluginId: 'rowexpanderBusquedaAvanzada',
			selectRowOnExpand: true,

			// this gives each row a unique identifier based on record's "acct_no"
			rowBodyTpl: [
			    '</div><div id="infoExtraBusquedaAvanzada-{id_punto_grid_avanzada}" ></div>'
			],

			// stick a grid into the rowexpander div whenever it is toggled open
			toggleRow: function(rowIdx) {
			    var rowNode = this.view.getNode(rowIdx),
				row = Ext.get(rowNode),
				nextBd = Ext.get(row).down(this.rowBodyTrSelector),
				hiddenCls = this.rowBodyHiddenCls,
				record = this.view.getRecord(rowNode),
//				grid = this.getCmp(),
				idPunto = record.get('id_punto_grid_avanzada'),
				targetId = 'infoExtraBusquedaAvanzada-' + idPunto;
				
			    if (row.hasCls(this.rowCollapsedCls)) {
                                row.removeCls(this.rowCollapsedCls);
				this.recordsExpanded[record.internalId] = true;
				this.view.fireEvent('expandbody', rowNode, record, nextBd.dom);

				if (rowNode.panelInfoExtraBusquedaAvanzada) {
				     nextBd.removeCls(hiddenCls);
				} else {
                                     nextBd.removeCls(hiddenCls);
                                     
                                     Ext.define('serviciosBusquedaAvanzadaModel', {
                                        extend: 'Ext.data.Model',
                                        fields: [
                                                    {name:'idServicio', type: 'int'},
                                                    {name:'tipo', type: 'string'},
                                                    {name:'idPunto', type: 'string'},
                                                    {name:'descripcionPunto', type: 'string'},
                                                    {name:'idProducto', type: 'string'},
                                                    {name:'descripcionProducto', type: 'string'},
                                                    {name:'cantidad', type: 'string'},
                                                    {name:'fechaCreacion', type: 'string'},
                                                    {name:'precioVenta', type: 'string'},
                                                    {name:'valorDescuento', type: 'string'},
                                                    {name:'descuento', type: 'string'},
                                                    {name:'porcentajeDescuento', type: 'string'},
                                                    {name:'estado', type: 'string'},
                                                    {name:'linkVer', type: 'string'},
                                                    {name:'linkEditar', type: 'string'},
                                                    {name:'linkEliminar', type: 'string'},
                                                    {name:'linkFactibilidad', type: 'string'},
                                                    {name:'tipoOrden', type: 'string'},
                                                    {name:'ultimaMilla', type: 'string'}
                                                ]
                                    }); 

                                    var storeServiciosBusquedaAvanzada = Ext.create('Ext.data.JsonStore', {
                                        model: 'serviciosBusquedaAvanzadaModel',
                                        pageSize: 6,
                                        autoLoad: true,
                                        proxy: {
                                            timeout: 400000,                type: 'ajax',
                                            url: '/search/ajaxGetServiciosPunto',
                                            reader: {
                                                type: 'json',
                                                root: 'servicios',
                                                totalProperty: 'total'
                                            },
                                            actionMethods: {
                                                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                            },
                                            extraParams:{
                                                idPunto:idPunto,
                                                idPersona: record.data.id_cliente_grid_avanzada,
                                            },
                                            simpleSortMode: true
                                        }
                                    });
                                    
                                    var gridServiciosBusquedaAvanzada = Ext.create('Ext.grid.Panel', {
                                        id: "gridServiciosBusquedaAvanzada-"+idPunto,
                                        name: "gridServiciosBusquedaAvanzada-"+idPunto,
                                        collapsible:false,
                                        autoScroll: true,
                                        title: 'Servicios',
                                        store: storeServiciosBusquedaAvanzada,
                                        dockedItems: [ {
                                            xtype: 'toolbar',
                                            dock: 'top',
                                            align: '->',
                                            items: [
                                                    //tbfill -> alinea los items siguientes a la derecha
                                                    { xtype: 'tbfill' }

                                        ]}],
                                        // paging bar on the bottom
                                        bbar: Ext.create('Ext.PagingToolbar', {
                                            store: storeServiciosBusquedaAvanzada,
                                            displayInfo: true,
                                            displayMsg: 'Mostrando servicios {0} - {1} of {2}',
                                            emptyMsg: "No hay datos para mostrar"
                                        }),
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
                                        columns: [
                                            new Ext.grid.RowNumberer(),  
                                            {
                                                id: 'idServicioBusquedaAvanzada-'+idPunto,
                                                header: 'idServicioBusquedaAvanzada',
                                                dataIndex: 'idServicioBusquedaAvanzada',
                                                hidden: true,
                                                hideable: false
                                            },
                                            {
                                                text: 'Tipo Orden',
                                                width: 70,
                                                dataIndex: 'tipoOrden',
                                                id: 'tipoOrdenBusquedaAvanzada-'+idPunto
                                            },
                                            {
                                                text: 'Ultima Milla',
                                                width: 70,
                                                dataIndex: 'ultimaMilla',
                                                id: 'ultimaMillaBusquedaAvanzada-'+idPunto
                                            },                        
                                            {
                                                text: 'Producto / Plan',
                                                flex: 170,
                                                dataIndex: 'descripcionProducto',
                                                id: 'descripcionProductoBusquedaAvanzada-'+idPunto
                                            },{
                                                text: 'Cantidad',
                                                width: 60,
                                                dataIndex: 'cantidad',
                                                id: 'cantidadBusquedaAvanzada-'+idPunto
                                            },{
                                                text: 'P.V.P.',
                                                dataIndex: 'precioVenta',
                                                id: 'precioVentaBusquedaAvanzada-'+idPunto,
                                                align: 'right',
                                                width: 50			
                                            },{
                                                text: 'Descuento',
                                                dataIndex: 'descuento',
                                                id: 'descuentoBusquedaAvanzada-'+idPunto,
                                                align: 'right',
                                                width: 70			
                                            },{
                                                text: 'Fecha Creacion',
                                                dataIndex: 'fechaCreacion',
                                                id: 'fechaCreacionBusquedaAvanzada-'+idPunto,
                                                align: 'right',
                                                width: 100,
                                                renderer: function(value,metaData,record,colIndex,store,view) {
                                                    metaData.tdAttr = 'data-qtip="' + value+'"';
                                                    return value;
                                                }			
                                            },{
                                                text: 'Estado',
                                                dataIndex: 'estado',
                                                id: 'estadoBusquedaAvanzada-'+idPunto,
                                                align: 'right',
                                                width: 70
                                            }
//                                            {
//                                                xtype: 'actioncolumn',
//                                                header: 'consultar',
//                                                align: 'center',
//                                                width: 60,
//                                                items: [
//                                                    {
//                                                        getClass: function(v, meta, rec) {
//                                                            var permiso = $("#ROLE_151-846");
//                                                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
//                                                            boolPermiso = 1;
//                                                            if(!boolPermiso){ 
//                                                                return 'button-grid-invisible';
//                                                            }
//                                                            else{
//                                                                return 'button-grid-show';
//
//                                                            }
//                                                        },
//                                                        tooltip: 'Info Tecnica',
//                                                        handler: function(grid, rowIndex, colIndex) {
//                                                            var rec = storeResponseSearch.getAt(rowIndex);
//                                                            Ext.MessageBox.show({
//                                                                title: 'Error',
//                                                                msg: 'Opcion en construccion',
//                                                                buttons: Ext.MessageBox.OK,
//                                                                icon: Ext.MessageBox.ERROR
//                                                             });
//
//                                                        }
//                                                    }
//                                                ]
//                                            }
                                        ]
                                    });  
        
                                    var panelInfoExtraBusquedaAvanzada = Ext.widget('tabpanel', {
                                            id: 'panelDatosGenerales-' + idPunto,
                                            name: 'panelDatosGenerales-' + idPunto,
                                            autoScroll: true,
                                            renderTo: targetId,
                                            activeTab: 0,
                                            plain: true,
                                            defaults :{
                                                autoScroll: true,
                                                autoHeight: true 
//                                                            bodyPadding: 10
                                            },
                                            items: [
                                                gridServiciosBusquedaAvanzada,
                                                {
                                                    title: 'General',
                                                    loader: {
//                                                                    type: "post",
                                                        url: '/search/ajaxGetDatosGenerales',
                                                        autoLoad: true,
                                                        params: { 
                                                            idPersona: record.data.id_cliente_grid_avanzada,
                                                            idPunto: record.data.id_punto_grid_avanzada
                                                            //containerId: panelInfoExtraBusquedaAvanzada.body.id 
                                                        },
                                                        loadMask: {msg:'Cargando...'}
//                                                                    listeners: {
//                                                                        load: function(){
//                                                                            Ext.get('panelDatosGenerales-'+idPunto+'-body').unmask();
//                                                                        },
//                                                                        beforeload: function(){
//                                                                            Ext.get('panelDatosGenerales-'+idPunto+'-body').mask('Cargando...');
//                                                                        }
//                                                                    }
                                                    }
//                                                                listeners: {
//                                                                    activate: function(tab) {
//                                                                        tab.loader.load();
//                                                                    }
//                                                                }
                                                }
                                            ]
                                        });
                                        
                                        // create the inner grid and render it to the row
//                                        rowNode.grid = panelInfoExtraBusquedaAvanzada;
                                        rowNode.panelInfoExtraBusquedaAvanzada = panelInfoExtraBusquedaAvanzada;
//                                        rowNode.grid.doComponentLayout();
				}

			    } else {
				row.addCls(this.rowCollapsedCls);
				nextBd.addCls(this.rowBodyHiddenCls);
				this.recordsExpanded[record.internalId] = false;
				this.view.fireEvent('collapsebody', rowNode, record, nextBd.dom);
			    }
			}
		    }],
		    title: 'Resultados de la Busqueda',
		    iconCls: 'icon-grid',
//		    bbar: Ext.create('Ext.PagingToolbar', {
//			store: storeResponseSearch,
//			displayInfo: true,
//			displayMsg: 'Mostrando {0} - {1} de {2}',
//			emptyMsg: "No hay datos que mostrar."
//		    })
	    });
            /* FIN GRID RESPUESTA BUSQUEDA AVANZADA*/
            
            /* VENTANA BUSQUEDA AVANZADA*/
            
            tabsModulosBusquedaAvanzada = new Ext.TabPanel({
//                  width: 850,
                  //frame:true,
                  autoScroll: true,
                  activeTab: 0,
                  plain: true,
                  defaults :{
                      autoScroll: true,
//                      autoHeight: true, 
                      bodyPadding: 6
                  },
                  items:[
                       tabComercialBusquedaAvanzada,
		       tabTecnicoBusquedaAvanzada,
//                        {html:' Muy Pronto. Esperelo', title:'Planificación'},
//                        {html:' Muy Pronto. Esperelo', title:'Tecnico'},
//                        {html:' Muy Pronto. Esperelo', title:'Soporte'},
//                        {html:' Muy Pronto. Esperelo', title:'Financiero'}
                  ]            
             }); 

            panelIzqBusquedaAvanzada = Ext.create('Ext.Panel', {
                autoScroll: true,
		width: 410,
                defaults :{
                        autoScroll: true
                },
                //frame:true,
                border:false,
                items: [ panelDatosClienteBusquedaAvanzada , panelDatosPuntoBusquedaAvanzada ]
            });

            panelDerBusquedaAvanzada = Ext.create('Ext.Panel', {
                autoScroll: true,
                defaults :{
                        autoScroll: true
                },
                //frame:true,
                border:false,
                items: [ tabsModulosBusquedaAvanzada ]
            });

            panelDatosBusquedaAvanzada = Ext.create('Ext.Panel',
            {
                autoScroll: true,
                defaults :
                {
                    autoScroll: true,
                    bodyPadding: 12,
                },
                border:false,
                layout: 
                {
                    type: 'table',
                    columns: 2,
                    align: 'left'
                },
                items: 
                [
                    panelIzqBusquedaAvanzada, 
                    Ext.create('Ext.Panel',
                    {
                        autoScroll: true,
                        border:false,
                        style: 'padding:0px!important; margin-top: -30px;',
                        bodyStyle: 'padding: 0px!important;',
                        bodyPadding: 0,
                        items:
                        [ 
                            {
                                html: "<div style='color: red; text-align:right;'>* Se requiere informaci&oacute;n de al menos uno para empezar la "+
                                      "b&uacute;squeda</div>",
                                border: false,
                                height: 15,
                                padding: 0
                            },
                            panelDerBusquedaAvanzada
                        ]
                    })
                ]
            });

            panelGridResponseBusquedaAvanzada = Ext.create('Ext.Panel', {
                //frame:true,
                id: 'panelGridResponseBusquedaAvanzada',
                name: 'panelGridResponseBusquedaAvanzada',
                buttonAlign: 'center',
                hidden: true,
                defaults :{
                    autoScroll: true,
                    autoHeight: true
                },
                items: [
                        gridResponseBusquedaAvanzada
                ],
                buttons:[
                    {
                        text: 'Nueva Busqueda',
                        iconCls: "icon_regresar",
                        handler: function(){
                            Ext.get('panelGridResponseBusquedaAvanzada').mask('Cargando Formulario...');
                            setTimeout(function(){
                                if(prefijoEmpresaTW === 'MD' && !boolPermisoTwig)
                                {
                                    Ext.Msg.alert('Mensaje', 'No cuenta con los permisos suficientes para hacer una busqueda avanzada');
                                    winBusquedaAvanzada.hide();
                                }
                                formBusquedaAvanzada.setVisible(true);
                                panelGridResponseBusquedaAvanzada.setVisible(false);
                                Ext.get('panelGridResponseBusquedaAvanzada').unmask(); 
                            },1000);
                        }
                    },
                    {
                        text: 'Cerrar',
                        iconCls: "icon_cerrar",
                        formBind: true,
                        handler: function(){
                            winBusquedaAvanzada.hide();
                        }
                    }
                ]
            });
  
            formBusquedaAvanzada = Ext.widget('form', {
                //frame:true,
                id: "formBusquedaAvanzada",
                name: "formBusquedaAvanzada",
                buttonAlign: 'center',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                defaults: {
                    margins: '0 0 0 0'
                },
                url: "/search",
                items: [ panelDatosBusquedaAvanzada ],
                buttons:[
                        {
                            text: 'Buscar',
                            formBind: true,
                            iconCls: "icon_search",
                            handler: function(){
                              buscarAvanzada();
                            }
                        },
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: function(){
                                this.up('form').getForm().reset();
                            }
                        },
                        {
                            text: 'Cerrar',
                            iconCls: "icon_cerrar",
                            formBind: true,
                            handler: function(){
                                winBusquedaAvanzada.hide();
                            }
                        }
                ],
                listeners: {
                    afterRender: function(thisForm, options){
                        this.keyNav = Ext.create('Ext.util.KeyNav', this.el, {
                            enter: buscarAvanzada,
                            scope: this
                        });
                    }
                }
            });
            
            /* FIN FORMULARIO BUSQUEDA AVANZADA */
            
            Ext.define('App.model.Window',{
                extend:'Ext.window.Window',
                title:'Busqueda Avanzada'
            });
            
            winBusquedaAvanzada = Ext.create('App.model.Window',{
                //frame:true,
                closable: false,
               width: 1180,
//                height: 590,
                modal: true,
                items:[
                      formBusquedaAvanzada , panelGridResponseBusquedaAvanzada
                ]
             });

        }
        
//         setTimeout(function(){ 
//             Ext.MessageBox.hide(); 
            winBusquedaAvanzada.show();
            winBusquedaAvanzada.center() ;


            if(searchLogin){
                Ext.getCmp('login_punto_avanzada').setValue(searchLogin) ;
                buscarAvanzada();   
            } 
//         },1500);
}

function buscarAvanzada() {

    var tipoElemento      = Ext.getCmp('tipo_elemento_tecnico_avanzada').getRawValue();
    var interfaceElemento = Ext.getCmp('interface_elemento_tecnico_avanzada').getRawValue();
    var modeloElemento    = Ext.getCmp('modelo_elemento_tecnico_avanzada').getRawValue();
    var nombreElemento    = Ext.getCmp('elemento_tecnico_avanzada').getRawValue();
    
    var login            = Ext.getCmp('login_punto_avanzada').getRawValue();
    var nombres          = Ext.getCmp('nombres_cliente_avanzada').getRawValue();
    var apellidos        = Ext.getCmp('apellidos_cliente_avanzada').getRawValue();
    var identificacion   = Ext.getCmp('identificacion_cliente_avanzada').getRawValue();
    var razonSocial      = Ext.getCmp('razon_social_cliente_avanzada').getRawValue();
    var tipo_documento   = Ext.getCmp('tipo_documento_comercial_avanzada').getRawValue();
    var numero_documento = Ext.getCmp('numero_documento_comercial_avanzada').getRawValue();
    var mac_cpe          = Ext.getCmp('mac_cpe_tecnico_avanzada').getRawValue();
    var nombre_punto     = Ext.getCmp('nombre_punto_avanzada').getRawValue();
    var direccion_cliente = Ext.getCmp('direccion_cliente_avanzada').getRawValue();
    var direccion_punto   = Ext.getCmp('direccion_punto_avanzada').getRawValue();
    
    var ip_tecnico_avanzada = Ext.getCmp('ip_tecnico_avanzada').getRawValue();

    if (tipoElemento != '' && modeloElemento == '')
    {
        alert("Debe ingresar el modelo del elemento");
    }
    else if (tipoElemento != '' && nombreElemento == '')
    {
        alert("Debe ingresar el nombre del elemento");
    }
    else if (tipoElemento == 'Olt' && interfaceElemento == '' )
    {
        alert("Debe ingresar la interface del olt");
    }
    else if (login == '' && nombres == '' && apellidos == '' && identificacion == '' && razonSocial == '' &&
             (tipo_documento == '' || numero_documento == '') && mac_cpe == '' && nombre_punto == ''&& 
             direccion_cliente == '' && direccion_punto== '' && ip_tecnico_avanzada=='')
    {
        alert("Debe ingresar al menos uno de estos parametros:\n\t\tLogin\n\t\tIdentificación\n\t\tNombres\n\t\tApellidos\n\t\tRazon Social"+
              "\n\t\tTipo Documento y Número Documento\n\t\tMac Cpe\n\t\tNombre Punto\n\t\tDirección del Cliente\n\t\tDirección del Punto\n\t\tIp del Servicio");
    }
    else
    {
        Ext.get('formBusquedaAvanzada').mask('Buscando...');
        var formData = Ext.getCmp('formBusquedaAvanzada').getValues();

        gridResponseBusquedaAvanzada.store.currentPage = 1;
        gridResponseBusquedaAvanzada.down('pagingtoolbar').onLoad();

        storeResponseSearch.proxy.extraParams = {datos: Ext.JSON.encode(formData)};
        storeResponseSearch.load({params: {}});
    }
}