
var globalData    = null;
var dataAdicional = {};
var windowAdministracion;
var windowCrearRm = null;
var prefixMask;
var prefixType;
var protocolos;
var tipos;
var storeSubredes;
var storeRutasBgp;
var formPanelShowRun;
var storeRouteMap;
var esAddDeleteRm  = false;
var boolEsDatos    = true;
var routemapseleccionado = '';

function adminstrarEnrutamiento(data)
{
    globalData = data;
    
    if(data.descripcionProducto === 'INTMPLS' || data.descripcionProducto === 'INTERNET SDWAN')
    {
        boolEsDatos = false;
    }
    else
    {
        boolEsDatos = true;
    }
    
    Ext.define('infoSubredes', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idPrefix', mapping: 'idPrefix'},
            {name: 'prefixIp', mapping: 'prefixIp'},
            {name: 'prefixMask', mapping: 'prefixMask'},
            {name: 'tipo', mapping: 'tipo'},
            {name: 'valor', mapping: 'valor'},
            {name: 'seq', mapping: 'seq'}
        ]
    });

    storeSubredes = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            autoLoad: false,
            model: 'infoSubredes'
        });
    
    Ext.get(gridServicios.getId()).mask('Obteniendo Información...');
    Ext.Ajax.request({
        url: urlGetInformacionEnrutamiento,
        method: 'post',
        timeout: 400000,
        params: {
            nombreProducto : data.descripcionProducto
        },
        success: function(response)
        {
            Ext.get(gridServicios.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);

            prefixMask = json.prefixMask;
            prefixType = json.prefixType;
            protocolos = json.protocolos;
            tipos      = json.tipos;
                       
            dataAdicional   = 
            {
                vrf           : data.vrf,
                pe            : data.elementoPadre,
                asPrivado     : data.asPrivado,
                protocolo     : 'BGP',
                claseServicio : data.nombreProducto,
                descripcion   : data.descripcionProducto,
                loginAux      : data.loginAux,
                ipBgp         : data.ipServicio,
                idServicio    : data.idServicio
                
            };
            
            dataAdicional   = Ext.JSON.encode(dataAdicional);
            
            arrayProtocolos = protocolos.split("|");
            arrayProtocolos = arrayProtocolos.filter(function(e){return e;});
            arrayTipos      = tipos.split("|");
            arrayTipos      = arrayTipos.filter(function(e){return e;})
            
            //Obtener los routeMap ligados al Servicio
            storeRouteMap = new Ext.data.Store({
                proxy: {
                    type: 'ajax',
                    url : urlGetPrefixRouteMap,
                    extraParams: {
                        opcion    : 'RM',
                        routeMap  : '',
                        idServicio: data.idServicio
                    },
                    reader: {
                        type: 'json',
                        root: 'encontrados'
                    }
                },
                fields:
                    [
                      {name:'routeMap',   mapping:'routeMap'},
                      {name:'prefixIp',   mapping:'prefixIp'},
                      {name:'prefixMask', mapping:'prefixMask'},
                      {name:'tipo',       mapping:'tipo'},
                      {name:'valor',      mapping:'valor'},
                      {name:'seq',        mapping:'seq'}
                    ],
                autoLoad : true
            });                       
            
            //Configuracion del Pe
            formPanelShowRun = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                title:'Configuración del Pe',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    columns: 1
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },

                items: [
                    {
                        xtype: 'panel',
                        id: 'panelShowRun',
                        autoScroll: true,
                        layout: 'fit',
                        height: 2000,
                        width: 550
                    }
                ],//cierre items
                width: 600,
                height: 450,
                autoScroll: true,
                frame: true
            });
            
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
                    columns: 3
                },
                items: [
                    //******************************************************
                    //             CONFIGURACION DEL EQUIPO PE
                    //******************************************************
                    formPanelShowRun,
                    //**********************************************************
                    //                      ADMINISTRACION BGP
                    //**********************************************************
                    {width: '40%', border: true},
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 1,
                            align: 'stretch'
                        },
                        items:
                            [
                                //************************************************************************
                                //                    CONFIGURACION DE ENRUTAMIENTO
                                //************************************************************************
                                {
                                    xtype: 'fieldset',
                                    title: '<b>Configuración de Enrutamiento<b>',
                                    defaults: {
                                        width: 750
                                    },
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 5,
                                                align: 'stretch'
                                            },
                                            items:
                                                [
                                                    {
                                                        xtype: 'container',
                                                        layout: {
                                                            type: 'table',
                                                            columns: 5,
                                                            align: 'stretch'
                                                        },
                                                        items: [
                                                            //---------------------------------------------
                                                            {width: '10%', border: false},
                                                            {
                                                                xtype: 'textfield',
                                                                name: 'tipoEnrutamiento',
                                                                fieldLabel: 'Tipo Enrutamiento',
                                                                displayField: data.protocolo,
                                                                value: data.protocolo,
                                                                readOnly: true,
                                                                labelStyle: 'font-weight:bold',
                                                                width: '50%'
                                                            },
                                                            {width: '10%', border: false},
                                                            {
                                                                xtype: 'textfield',
                                                                name: 'asPrivado',
                                                                fieldLabel: 'AS Privado',
                                                                displayField: data.asPrivado,
                                                                value: data.asPrivado,
                                                                readOnly: true,
                                                                labelStyle: 'font-weight:bold',
                                                                width: '50%'
                                                            },
                                                            {width: '0%', border: false},
                                                            //---------------------------------------------

                                                            {width: '10%', border: false},
                                                            {
                                                                xtype: 'textfield',
                                                                name: 'neighbor',
                                                                fieldLabel: 'Neighbor',
                                                                displayField: data.ipServicio,
                                                                value: data.ipServicio,
                                                                readOnly: true,
                                                                labelStyle: 'font-weight:bold',
                                                                width: '50%'
                                                            },
                                                            {width: '10%', border: false},
                                                            {
                                                                xtype: 'textfield',
                                                                name: 'vlan',
                                                                fieldLabel: 'Vlan',
                                                                displayField: data.vlan,
                                                                value: data.vlan,
                                                                readOnly: true,
                                                                labelStyle: 'font-weight:bold',
                                                                width: '50%'
                                                            },
                                                            {width: '0%', border: false}
                                                        ]
                                                    }
                                                ]
                                        }
                                    ]
                                }
                                ,
                                //************************************************************************
                                //                    OPCIONES DE CONFIGURACION
                                //************************************************************************
                                {
                                    xtype: 'fieldset',
                                    title: '<b>Opciones de Configuración<b>',
                                    defaultType: 'textfield',
                                    defaults: {
                                        width: 750,
                                        height:290
                                    },
                                    items: [
                                        {
                                            xtype: 'tabpanel',
                                            activeTab: 0,
                                            id:'configTab',
                                            itemId: 'tabPanel',
                                            defaults: {
                                                width: 750,
                                                height:285
                                            },
                                            items:
                                                [
                                                    //------------- As-Override --------------------------------------
                                                    {
                                                        xtype: 'panel',
                                                        tbar: ['->', {
                                                                text: getButtons('+'),
                                                                handler: function()
                                                                {
                                                                    if (!Ext.isEmpty(Ext.getCmp('rgOverride').getValue().rbAs))
                                                                    {
                                                                        BGP.override();
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe escoger una opción \n\
                                                                                                      para configurar As-Override");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            }
                                                        ],
                                                        labelWidth: 120,
                                                        title: "As-Override",
                                                        defaults: {
                                                            height:285
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'fieldset',
                                                                title: '<b>Agregar/Eliminar As-Override<b>',
                                                                items: [
                                                                    {
                                                                        xtype: 'container',
                                                                        layout: {
                                                                            type: 'table',
                                                                            columns: 5,
                                                                            align: 'stretch'
                                                                        },
                                                                        items: [
                                                                            {
                                                                                id: 'rgOverride',
                                                                                xtype: 'radiogroup',
                                                                                fieldLabel: '<b>Comandos<b>',
                                                                                columns: 1,
                                                                                width: '100%',
                                                                                items: [
                                                                                    {
                                                                                        boxLabel: '<span>neighbor ' + data.ipServicio + ' \n\
                                                                                                  as-override</span>',
                                                                                        id: 'rbAgregarAs',
                                                                                        name: 'rbAs',
                                                                                        inputValue: "agregar"
                                                                                    },
                                                                                    {
                                                                                        boxLabel: '<span>no neighbor ' + data.ipServicio + ' \n\
                                                                                                  as-override</span>',
                                                                                        id: 'rbEliminarAs',
                                                                                        name: 'rbAs',
                                                                                        inputValue: "eliminar"
                                                                                    }
                                                                                ]//items
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false}
                                                                        ]
                                                                    }
                                                                ]
                                                            }
                                                        ]
                                                    },
                                                    //------------- Default Originate -------------------------------
                                                    {
                                                        xtype: 'panel',
                                                        tbar: ['->', {
                                                                text: getButtons('+'),
                                                                handler: function()
                                                                {
                                                                    if (!Ext.isEmpty(Ext.getCmp('rgOriginate').getValue().rbOri))
                                                                    {
                                                                        BGP.originate();
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe escoger una opción para \n\
                                                                                                     configurar Default Originate");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            }
                                                        ],
                                                        labelWidth: 120,
                                                        title: "Default Originate",
                                                        defaults: {
                                                            height:285
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'fieldset',
                                                                title: '<b>Agregar/Eliminar Default Originate<b>',
                                                                items: [
                                                                    {
                                                                        xtype: 'container',
                                                                        layout: {
                                                                            type: 'table',
                                                                            columns: 5,
                                                                            align: 'stretch'
                                                                        },
                                                                        items: [
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                id: 'rgOriginate',
                                                                                xtype: 'radiogroup',
                                                                                fieldLabel: '<b>Comandos<b>',
                                                                                columns: 1,
                                                                                width: '100%',
                                                                                items: [
                                                                                    {
                                                                                        boxLabel: '<span>default-information originate</span>',
                                                                                        id: 'rbAgregarOri',
                                                                                        name: 'rbOri',
                                                                                        inputValue: "agregar"
                                                                                    },
                                                                                    {
                                                                                        boxLabel: '<span>no default-information originate</span>',
                                                                                        id: 'rbEliminarOri',
                                                                                        name: 'rbOri',
                                                                                        inputValue: "eliminar"
                                                                                    }
                                                                                ]//items
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false}
                                                                        ]
                                                                    }
                                                                ]
                                                            }
                                                        ]
                                                    },
                                                    //----------------------- ShutDown Bgp ------------------------
                                                    {
                                                        xtype: 'panel',
                                                        tbar: ['->', {
                                                                text: getButtons('+'),
                                                                handler: function()
                                                                {
                                                                    if (!Ext.isEmpty(Ext.getCmp('rgShut').getValue().rbSd))
                                                                    {
                                                                        BGP.shutdown();
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe escoger una opción para configurar \n\
                                                                                         Shut Down de RouteMap");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            }
                                                        ],
                                                        labelWidth: 120,
                                                        title: "ShutDown",
                                                        defaults: {
                                                            height:285
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'fieldset',
                                                                title: '<b>Ejecutar Shutdown<b>',
                                                                items: [
                                                                    {
                                                                        xtype: 'container',
                                                                        layout: {
                                                                            type: 'table',
                                                                            columns: 5,
                                                                            align: 'stretch'
                                                                        },
                                                                        items: [
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                id: 'rgShut',
                                                                                xtype: 'radiogroup',
                                                                                fieldLabel: '<b>Comandos<b>',
                                                                                columns: 1,
                                                                                width: '100%',
                                                                                items: [
                                                                                    {
                                                                                        boxLabel: '<span>SI</span>',
                                                                                        id: 'rbShutDownOn',
                                                                                        name: 'rbSd',
                                                                                        inputValue: "agregar"
                                                                                    },
                                                                                    {
                                                                                        boxLabel: '<span>NO</span>',
                                                                                        id: 'rbShutDownOff',
                                                                                        name: 'rbSd',
                                                                                        inputValue: "eliminar"
                                                                                    }
                                                                                ]//items
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false}
                                                                        ]
                                                                    }
                                                                ]
                                                            }
                                                        ]
                                                    },
                                                    //------------- Cambiar Weight ----------------------------------
                                                    {
                                                        xtype: 'panel',
                                                        tbar: ['->', {
                                                                text: getButtons('+'),
                                                                handler: function()
                                                                {
                                                                    var weight = Ext.getCmp('weightText').value;
                                                                    if (!Ext.isEmpty(weight))
                                                                    {
                                                                        if(weight < 1)
                                                                        {
                                                                            Ext.Msg.alert("Advertencia", "Valor debe ser mayor o igual a 1");
                                                                        }
                                                                        else
                                                                        {
                                                                            BGP.weight('agregar');
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe ingresar el Weight a ser agregado");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            },
                                                            {
                                                                text: getButtons('-'),
                                                                handler: function()
                                                                {
                                                                    var weight = Ext.getCmp('weightText').value;
                                                                    if (!Ext.isEmpty(weight))
                                                                    {
                                                                        if(weight < 1)
                                                                        {
                                                                            Ext.Msg.alert("Advertencia", "Valor debe ser mayor o igual a 1");
                                                                        }
                                                                        else
                                                                        {
                                                                            BGP.weight('eliminar');
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe ingresar el Weight a ser eliminado");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            }
                                                        ],
                                                        labelWidth: 120,
                                                        title: "Weight",
                                                        defaults: {
                                                            height:285
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'fieldset',
                                                                title: '<b>Cambiar Weight<b>',
                                                                items: [
                                                                    {
                                                                        xtype: 'container',
                                                                        layout: {
                                                                            type: 'table',
                                                                            columns: 5,
                                                                            align: 'stretch'
                                                                        },
                                                                        items: [
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'numberfield',
                                                                                fieldLabel: 'Weight:',
                                                                                id: 'weightText',
                                                                                labelStyle: 'font-weight:bold',
                                                                                hideTrigger: true,
                                                                                useThousandSeparator: true,
                                                                                emptyText: 'Ingrese el valor del Weight'
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false}
                                                                        ]
                                                                    }
                                                                ]
                                                            }
                                                        ]
                                                    },
                                                    //------------- Clear -------------------------------------------
                                                    {
                                                        xtype: 'panel',
                                                        tbar: ['->', {
                                                                text: getButtons('+'),
                                                                handler: function()
                                                                {
                                                                    if (!Ext.isEmpty(Ext.getCmp('cmbTipoClear').getValue()))
                                                                    {
                                                                        BGP.clear();
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe Seleccionar una opción para configurar");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            }
                                                        ],
                                                        labelWidth: 120,
                                                        title: "Clear",
                                                        defaults: {
                                                            height:285
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'fieldset',
                                                                title: '<b>Ejecutar Opciones de Clear<b>',
                                                                items: [
                                                                    {
                                                                        xtype: 'container',
                                                                        layout: {
                                                                            type: 'table',
                                                                            columns: 5,
                                                                            align: 'stretch'
                                                                        },
                                                                        items: [
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'combobox',
                                                                                fieldLabel: 'Tipo:',
                                                                                id: 'cmbTipoClear',
                                                                                name: 'cmbTipoClear',
                                                                                store: [
                                                                                    ['clear-total', 'CLEAR TOTAL'],
                                                                                    ['clear-in', 'CLEAR DE ENTRADA'],
                                                                                    ['clear-out', 'CLEAR DE SALIDA']
                                                                                ],
                                                                                emptyText: 'Seleccione',
                                                                                editable: false,
                                                                                width: 300,
                                                                                labelStyle: 'font-weight:bold'
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '0%', border: false}
                                                                        ]
                                                                    }
                                                                ]
                                                            }
                                                        ]
                                                    },
                                                    //------------- RouteMap - Creación -----------------------------
                                                    {
                                                        xtype: 'panel',
                                                        id:'tabRmCrear',
                                                        tbar: ['->', {
                                                                text: getButtons('+'),
                                                                handler: function()
                                                                {
                                                                    var selector = Ext.getCmp('rgRouteMap').getValue().rbrm;

                                                                    if (selector === 'creados')
                                                                    {
                                                                        var existenteRouteMap = Ext.getCmp('cmbRouteMap').value;

                                                                        if (!Ext.isEmpty(existenteRouteMap))
                                                                        {
                                                                            routeMapNew();
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.Msg.alert("Advertencia", "Debe seleccionar una Route-Map existente");
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        var nuevoRouteMap = Ext.getCmp('routeMapNuevoText').value;

                                                                        if (!Ext.isEmpty(nuevoRouteMap))
                                                                        {
                                                                            routeMapNew();
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.Msg.alert("Advertencia", "Debe ingresar el nombre de la \n\
                                                                                                         Route-Map a ser creada");
                                                                        }
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            },
                                                            {
                                                                text: getButtons('-'),
                                                                hidden:!boolEsDatos,
                                                                handler: function()
                                                                {
                                                                    var selector = Ext.getCmp('rgRouteMap').getValue().rbrm;

                                                                    if (selector === 'creados')
                                                                    {
                                                                        var existenteRouteMap = Ext.getCmp('cmbRouteMap').value;

                                                                        if (!Ext.isEmpty(existenteRouteMap))
                                                                        {
                                                                            BGP.crearEliminarRouteMap(existenteRouteMap, 'eliminar');
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.Msg.alert("Advertencia", "Para eliminar debe \n\
                                                                                                         seleccionar una Route-Map existente");
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        var nuevoRouteMap = Ext.getCmp('routeMapNuevoText').value;

                                                                        if (!Ext.isEmpty(nuevoRouteMap))
                                                                        {
                                                                            BGP.crearEliminarRouteMap(nuevoRouteMap, 'eliminar');
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.Msg.alert("Advertencia", "Debe ingresar el nombre de la \n\
                                                                                                         Route-Map a ser eliminada");
                                                                        }
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            }
                                                        ],
                                                        labelWidth: 120,
                                                        title: "RouteMap (Crear)",
                                                        defaults: {
                                                            height:285
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'fieldset',
                                                                title: "<b>RouteMap - Creación<b>",
                                                                items: [
                                                                    {
                                                                        xtype: 'container',
                                                                        layout: {
                                                                            type: 'table',
                                                                            columns: 5,
                                                                            align: 'stretch'
                                                                        },
                                                                        items: [
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                id: 'rgRouteMap',
                                                                                xtype: 'radiogroup',
                                                                                columns: 1,
                                                                                width: '100%',
                                                                                value: 'creados',
                                                                                items: [
                                                                                    {
                                                                                        boxLabel: '<span>RouteMap Creados</span>',
                                                                                        id: 'rbrmCreados',
                                                                                        name: 'rbrm',
                                                                                        inputValue: "creados",
                                                                                        listeners:
                                                                                            {
                                                                                                change: function(cb, nv, ov)
                                                                                                {
                                                                                                    if (nv)
                                                                                                    {
                                                                                                        $('#routeMapExistente').show();
                                                                                                        $('#routeMapNuevo').hide();
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                    },
                                                                                    {
                                                                                        boxLabel: '<span>RouteMap Nuevo</span>',
                                                                                        id: 'rbrmNuevos',
                                                                                        name: 'rbrm',
                                                                                        inputValue: "nuevos",
                                                                                        hidden:!boolEsDatos,
                                                                                        listeners:
                                                                                            {
                                                                                                change: function(cb, nv, ov)
                                                                                                {
                                                                                                    if (nv)
                                                                                                    {
                                                                                                        $('#routeMapNuevo').show();
                                                                                                        $('#routeMapExistente').hide();
                                                                                                        routemapseleccionado = '';
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                    }
                                                                                ]//items
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '0%', border: false},
                                                                            //-----------------------------------------
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'fieldset',
                                                                                title: '<b>RoutMap Creados<b>',
                                                                                defaultType: 'textfield',
                                                                                id: 'routeMapExistente',
                                                                                visible: false,
                                                                                defaults: {
                                                                                    width: 580
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
                                                                                            {width: '10%', border: false},
                                                                                            {
                                                                                                xtype: 'combobox',
                                                                                                fieldLabel: 'RoutMap Creados:',
                                                                                                id: 'cmbRouteMap',
                                                                                                name: 'cmbRouteMap',
                                                                                                store: storeRouteMap,
                                                                                                emptyText: 'Seleccione una RouteMap',
                                                                                                displayField: 'routeMap',
                                                                                                valueField: 'routeMap',
                                                                                                editable: false,
                                                                                                queryMode: 'remote',
                                                                                                labelStyle: 'font-weight:bold',
                                                                                                width: 400,
                                                                                                listeners: {
                                                                                                    change: function(combo)
                                                                                                    {
                                                                                                        if (!Ext.isEmpty(combo.getValue())) 
                                                                                                        {
                                                                                                            routemapseleccionado = combo.getValue();
                                                                                                        }
                                                                                                    }                                                                                                  
                                                                                                }
                                                                                            },
                                                                                            {width: '10%', border: false},
                                                                                            {width: '10%', border: false},
                                                                                            {width: '0%', border: false}
                                                                                        ]
                                                                                    }
                                                                                ]
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '0%', border: false},
                                                                            //-----------------------------------------
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'fieldset',
                                                                                title: '<b>RoutMap Nuevo<b>',
                                                                                defaultType: 'textfield',
                                                                                id: 'routeMapNuevo',
                                                                                defaults: {
                                                                                    width: 580
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
                                                                                            {width: '10%', border: false},
                                                                                            {
                                                                                                xtype: 'textfield',
                                                                                                fieldLabel: 'RoutMap:',
                                                                                                id: 'routeMapNuevoText',
                                                                                                labelStyle: 'font-weight:bold',
                                                                                                width: 400,
                                                                                                maskRe: /([A-Za-z0-9\_\-]+)/i
                                                                                            },
                                                                                            {width: '10%', border: false},
                                                                                            {width: '10%', border: false},
                                                                                            {width: '0%', border: false}
                                                                                        ]
                                                                                    }
                                                                                ]
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '0%', border: false}
                                                                        ]
                                                                    }
                                                                ],
                                                                listeners:
                                                                    {
                                                                        render: function()
                                                                        {
                                                                            $('#routeMapExistente').hide();
                                                                            $('#routeMapNuevo').hide();
                                                                        }
                                                                    }
                                                            }
                                                        ]
                                                    },
                                                    //------------- RouteMap - Agregar a un Neighbor ---------------
                                                    {
                                                        xtype: 'panel',    
                                                        id:'tabRmNeighbor',
                                                        tbar: ['->', {
                                                                text: getButtons('+'),
                                                                handler: function()
                                                                {
                                                                    var tipoN = Ext.getCmp('cmbTipoNeighbor').getValue();
                                                                    var RmN = Ext.getCmp('cmbRouteMapNeighbor').getValue();

                                                                    if (!Ext.isEmpty(tipoN) && !Ext.isEmpty(RmN))
                                                                    {
                                                                        BGP.agregarEliminarNeighbor('agregar');
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe escoger una opción para configurar");
                                                                    }

                                                                },
                                                                iconCls: ''
                                                            },
                                                            {
                                                                text: getButtons('-'),
                                                                handler: function()
                                                                {
                                                                    var tipoN = Ext.getCmp('cmbTipoNeighbor').getValue();
                                                                    var RmN = Ext.getCmp('cmbRouteMapNeighbor').getValue();

                                                                    if (!Ext.isEmpty(tipoN) && !Ext.isEmpty(RmN))
                                                                    {
                                                                        BGP.agregarEliminarNeighbor('eliminar');
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe escoger una opción para configurar");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            }
                                                        ],
                                                        labelWidth: 120,
                                                        title: "RouteMap (Neighbor)",
                                                        defaults: {
                                                            height:285
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'fieldset',
                                                                title: "RouteMap Agregar a un Neighbor",
                                                                items: [
                                                                    {
                                                                        xtype: 'container',
                                                                        layout: {
                                                                            type: 'table',
                                                                            columns: 5,
                                                                            align: 'stretch'
                                                                        },
                                                                        items: [
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'combobox',
                                                                                fieldLabel: 'Tipo:',
                                                                                id: 'cmbTipoNeighbor',
                                                                                name: 'cmbTipoNeighbor',
                                                                                store: [
                                                                                    ['IN', 'IN'],
                                                                                    ['OUT', 'OUT']
                                                                                ],
                                                                                emptyText: 'Seleccione',
                                                                                editable: false,
                                                                                labelStyle: 'font-weight:bold',
                                                                                width: '25%'
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'combobox',
                                                                                fieldLabel: 'RoutMap:',
                                                                                id: 'cmbRouteMapNeighbor',
                                                                                name: 'cmbRouteMapNeighbor',
                                                                                store: storeRouteMap,
                                                                                displayField: 'routeMap',
                                                                                valueField: 'routeMap',
                                                                                emptyText: 'Seleccione una RouteMap',
                                                                                editable: false,
                                                                                labelStyle: 'font-weight:bold',
                                                                                width: '25%'
                                                                            },
                                                                            {width: '10%', border: false}
                                                                        ]
                                                                    }
                                                                ]
                                                            }
                                                        ]
                                                    },
                                                    //-------------- Redistribucion protocolos BGP -------------------
                                                    {
                                                        xtype: 'panel',
                                                        id:'tabRmRedistribuir',
                                                        tbar: ['->', {
                                                                text: getButtons('+'),
                                                                handler: function()
                                                                {
                                                                    var protocolo = Ext.getCmp('cmbTipoProtocolo').value;
                                                                    var configuracion = Ext.getCmp('cmbTipoConfiguracion').value;

                                                                    if (!Ext.isEmpty(protocolo) && !Ext.isEmpty(configuracion))
                                                                    {
                                                                        BGP.agregarEliminarRedistribucion('agregar');
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe seleccionar todas las opciones");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            },
                                                            {
                                                                text: getButtons('-'),
                                                                handler: function()
                                                                {
                                                                    var protocolo = Ext.getCmp('cmbTipoProtocolo').value;
                                                                    var configuracion = Ext.getCmp('cmbTipoConfiguracion').value;

                                                                    if (!Ext.isEmpty(protocolo) && !Ext.isEmpty(configuracion))
                                                                    {
                                                                        BGP.agregarEliminarRedistribucion('eliminar');
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert("Advertencia", "Debe seleccionar todas las opciones");
                                                                    }
                                                                },
                                                                iconCls: ''
                                                            }
                                                        ],
                                                        labelWidth: 120,
                                                        height: 155,
                                                        title: "Redistribución BGP",
                                                        defaults: {
                                                            height:285
                                                        },
                                                        items: [
                                                            {
                                                                xtype: 'fieldset',
                                                                title: "<b>Redistribución de otros protocolos hacia BGP<b>",
                                                                items: [
                                                                    {
                                                                        xtype: 'container',
                                                                        layout: {
                                                                            type: 'table',
                                                                            columns: 5,
                                                                            align: 'stretch'
                                                                        },
                                                                        items: [
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'combobox',
                                                                                fieldLabel: 'Protocolo:',
                                                                                id: 'cmbTipoProtocolo',
                                                                                name: 'cmbTipoProtocolo',
                                                                                store: arrayProtocolos,
                                                                                emptyText: 'Seleccione',
                                                                                editable: false,
                                                                                labelStyle: 'font-weight:bold',
                                                                                width: '25%'
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'combobox',
                                                                                fieldLabel: 'Configuración:',
                                                                                id: 'cmbTipoConfiguracion',
                                                                                name: 'cmbTipoConfiguracion',
                                                                                store: arrayTipos,
                                                                                emptyText: 'Seleccione un tipo',
                                                                                editable: false,
                                                                                labelStyle: 'font-weight:bold',
                                                                                width: '25%',
                                                                                listeners:
                                                                                    {
                                                                                        change: function(combo)
                                                                                        {
                                                                                            if (combo.getValue() === 'METRIC')
                                                                                            {
                                                                                                $('#metricBgpText').show();
                                                                                                $('#cmbRouteMapBgp').hide();
                                                                                                $('#cbmatch').hide();
                                                                                            }
                                                                                            if (combo.getValue() === 'ROUTE-MAP')
                                                                                            {
                                                                                                $('#metricBgpText').hide();
                                                                                                $('#cmbRouteMapBgp').show();
                                                                                                $('#cbmatch').hide();
                                                                                            }
                                                                                            if (combo.getValue() === 'STANDARD')
                                                                                            {
                                                                                                $('#metricBgpText').hide();
                                                                                                $('#cmbRouteMapBgp').hide();
                                                                                                $('#cbmatch').hide();
                                                                                            }
                                                                                            if (combo.getValue() === 'MATCH')
                                                                                            {
                                                                                                $('#cbmatch').show();
                                                                                                $('#metricBgpText').hide();
                                                                                                $('#cmbRouteMapBgp').hide();
                                                                                            }
                                                                                        }
                                                                                    }
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            //-----------------------------------------
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'combobox',
                                                                                fieldLabel: 'RoutMap Creados:',
                                                                                id: 'cmbRouteMapBgp',
                                                                                name: 'cmbRouteMapBgp',
                                                                                store: storeRouteMap,
                                                                                displayField: 'routeMap',
                                                                                valueField: 'routeMap',
                                                                                emptyText: 'Seleccione un RouteMap',
                                                                                editable: false,
                                                                                labelStyle: 'font-weight:bold',
                                                                                width: 400
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            //--------------------------------------------
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                xtype: 'textfield',
                                                                                fieldLabel: 'Metric:',
                                                                                id: 'metricBgpText',
                                                                                name: 'metricBgpText',
                                                                                labelStyle: 'font-weight:bold',
                                                                                hideTrigger: true,
                                                                                value: '0',
                                                                                maskRe: /([0-9]+)/i,
                                                                                useThousandSeparator: true,
                                                                                emptyText: 'Ingrese el valor del Metric',
                                                                                width: 400
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            //--------------------------------------------
                                                                            {width: '10%', border: false},
                                                                            {
                                                                                id: 'cbmatch',
                                                                                xtype: 'checkboxgroup',
                                                                                fieldLabel: '<b>Seleccione<b>',
                                                                                columns: 1,
                                                                                vertical: true,
                                                                                items: [
                                                                                    {boxLabel: 'INTERNAL', name: 'rb', inputValue: 'internal'},
                                                                                    {boxLabel: 'EXTERNAL 1', name: 'rb', inputValue: 'external1'},
                                                                                    {boxLabel: 'EXTERNAL 2', name: 'rb', inputValue: 'external2'}
                                                                                ]
                                                                            },
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false},
                                                                            {width: '10%', border: false}
                                                                        ]
                                                                    }
                                                                ],
                                                                listeners:
                                                                    {
                                                                        render: function()
                                                                        {
                                                                            $('#metricBgpText').hide();
                                                                            $('#cmbRouteMapBgp').hide();
                                                                            $('#cbmatch').hide();
                                                                        }
                                                                    }
                                                            }
                                                        ]
                                                    }
                                                ]
                                        }
                                    ]
                                }
                            ]
                    }
                ],
                buttons: [
                    {
                        text: 'Cerrar',
                        handler: function() {
                            windowAdministracion.destroy();
                        }
                    }]
            });

            windowAdministracion = Ext.create('Ext.window.Window', {
                title: 'Administración de Enrutamiento BGP',
                modal: true,
                width: 1400,
                closable: true,
                layout: 'fit',
                items: [formPanel],
                listeners: {
                afterrender: function(el, eOpts){
                    getShowRunConfigurationPe(data.idServicio);
                    var tabPanel = Ext.getCmp('configTab');
                    
                    if(!boolEsDatos)
                    {
                        //Esconder los tabs de acuerdo al nombre tecnico
                        tabPanel.child('#tabRmNeighbor').tab.hide();
                        tabPanel.child('#tabRmRedistribuir').tab.hide();                        
                    }
                    else
                    {
                        tabPanel.child('#tabRmCrear').tab.hide();
                    }
                }
            }
            }).show();
        },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}

BGP = {
        
    parametros      : "",
    opcionEjecucion : "",
    
    override: function()
    {
        esAddDeleteRm        = false;
        var rgSelection      = Ext.getCmp('rgOverride').getValue();
        this.opcionEjecucion = 'override';
        this.parametros      = {opcion        : this.opcionEjecucion,
                                info          : '',
                                tipo          : rgSelection.rbAs,
                                dataAdicional : dataAdicional
                               };
        this.ajaxRequest();                               
    }
    ,
    
    originate : function()
    {
        esAddDeleteRm        = false;
        var rgSelection      = Ext.getCmp('rgOriginate').getValue();
        this.opcionEjecucion = 'originate';
        this.parametros      = {opcion        : this.opcionEjecucion,
                                info          : '',
                                tipo          : rgSelection.rbOri,
                                dataAdicional : dataAdicional
                               };
        this.ajaxRequest();  
    }
    ,
    
    weight : function(tipo)
    {
        esAddDeleteRm        = false;
        this.opcionEjecucion = 'weight';
        this.parametros      = {opcion        : this.opcionEjecucion,
                                info          : Ext.getCmp('weightText').value,
                                tipo          : tipo,
                                dataAdicional : dataAdicional
                               };
        this.ajaxRequest();
    }
    ,
    
    clear : function()
    {
        esAddDeleteRm        = false;
        var cmbClear         = Ext.getCmp('cmbTipoClear').getValue();
        this.opcionEjecucion = 'clear';
        this.parametros      = {opcion        : this.opcionEjecucion,
                                info          : cmbClear,
                                tipo          : 'agregar',
                                dataAdicional : dataAdicional
                               };
        this.ajaxRequest();
    }
    ,
    
    shutdown : function()
    {
        esAddDeleteRm        = false;
        var rgSelection      = Ext.getCmp('rgShut').getValue();
        this.opcionEjecucion = 'shutdown';
        this.parametros      = {opcion        : this.opcionEjecucion,
                                info          : '',
                                tipo          : rgSelection.rbSd,
                                dataAdicional : dataAdicional
                               };
        this.ajaxRequest(); 
    }
    ,
    
    crearEliminarRouteMap : function(infoCrearRouteMap , tipo)
    {
        esAddDeleteRm        = true;
        var tipoCreacionRm   = '';
        var tipoRouteMa      = Ext.getCmp('rgRouteMap').getValue().rbrm;           //nuevos, creados
        var routeMap;
        
        if(tipoRouteMa === 'nuevos')
        {
            routeMap = Ext.getCmp('routeMapNuevoText').value;
        }
        else
        {
            if(tipo === 'agregar')
            {
                tipoCreacionRm  = Ext.getCmp('rgRouteMapAdd').getValue().rbTipoRoute; //asPrivado, subredes
            }
            routeMap        = Ext.getCmp('cmbRouteMap').value;
        }
        
        var datos =
            {
                tipoRoute    : tipoCreacionRm,
                tipoCreacion : tipoRouteMa,
                routeMap     : routeMap, //Informacion de asPrivado o subredes a agregar
                infoAdicional: infoCrearRouteMap
            };
        
        this.opcionEjecucion = 'crearEliminarRouteMap';
        this.parametros      = {opcion        : this.opcionEjecucion,
                                info          : Ext.JSON.encode(datos),
                                tipo          : tipo,
                                dataAdicional : dataAdicional
                               };
                                       
        this.ajaxRequest(); 
    }
    ,
    
    agregarEliminarNeighbor : function(tipo)
    {
        esAddDeleteRm    = false;
        var routeMapType = Ext.getCmp('cmbTipoNeighbor').value;
        var routeMap     = Ext.getCmp('cmbRouteMapNeighbor').value;
        var datos = 
            {
                routemap_name  : routeMap,
                routemap_type  : routeMapType
            };
        this.opcionEjecucion = 'agregarEliminarNeighbor';
        this.parametros      = {opcion        : this.opcionEjecucion,
                                info          : Ext.JSON.encode(datos),
                                tipo          : tipo,
                                dataAdicional : dataAdicional
                               };
        this.ajaxRequest();
    }
    ,
    
    agregarEliminarRedistribucion: function(tipo)
    {
        esAddDeleteRm        = false;
        var protocolo        = Ext.getCmp('cmbTipoProtocolo').value;
        var configuracion    = Ext.getCmp('cmbTipoConfiguracion').value;
        var routeMap         = Ext.getCmp('cmbRouteMapBgp').value;
        var metric           = Ext.getCmp('metricBgpText').value;
        var matches          = Ext.getCmp('cbmatch').getValue().rb;
        
        if(configuracion === 'MATCH')
        {
            if(Ext.isEmpty(matches))
            {
                Ext.Msg.alert('Advertencia', 'Debe escoger al menos una opción');
                return;
            }
        }
        
        if(configuracion === 'METRIC')
        {
            if(Ext.isEmpty(metric) || metric == 0)
            {
                Ext.Msg.alert('Advertencia', 'Debe ingresar la metrica para continuar');
                return;
            }
            if(Ext.isEmpty(metric) && metric < 1)
            {
                Ext.Msg.alert('Advertencia', 'El valor de la métrica debe ser mayor o igual que 1');
                return;
            }
        }

        if(isArray( matches ))
        {
            matches = matches.join("-");
        }
        
        var datos = 
            {
                redistribute_bgp_protocolo  : protocolo,
                redistribute_bgp_type       : configuracion,
                redistribute_bgp_metric     : metric,
                redistribute_bgp_routemap   : routeMap,
                redistribute_bgp_match      : matches
            };
            
        this.opcionEjecucion = 'agregarEliminarRedistribucion';
        this.parametros      = {opcion        : this.opcionEjecucion,
                                info          : Ext.JSON.encode(datos),
                                tipo          : tipo,
                                dataAdicional : dataAdicional
                               };
        this.ajaxRequest();
    }
    ,
        
    ajaxRequest: function()
    {
        var component = Ext.get(windowAdministracion.getId());
        component.mask('Ejecutando Script para <b>'+this.opcionEjecucion+'</b>');
       
        Ext.Ajax.request({
            url    : urlAdministrarEnrutamientoBgp,
            method : 'post',
            timeout: 400000,
            params : this.parametros,
            success: function(response) 
            {
                component.unmask();
                
                var json = Ext.JSON.decode(response.responseText);
                
                if(json.status === 'OK')
                {
                    Ext.Msg.alert('Mensaje', json.mensaje, function(btn) 
                    {
                        if (btn == 'ok')
                        {
                            getShowRunConfigurationPe(globalData.idServicio);

                            if(esAddDeleteRm)
                            {
                                if(windowCrearRm!==null)
                                {
                                    windowCrearRm.destroy();
                                }
                                
                                Ext.getCmp('cmbRouteMap').setValue("");
                                storeRouteMap.clearData();
                                storeRouteMap.removeAll();
                                storeRouteMap.load();
                                Ext.getCmp('routeMapNuevoText').setValue("");
                                
                                windowCrearRm = null;
                            }
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Alerta', json.mensaje, function(btn) 
                    {
                        if (btn == 'ok')
                        {
                            if(esAddDeleteRm)
                            {
                                if(windowCrearRm!==null)
                                {
                                    windowCrearRm.show();
                                }
                            }
                        }
                    });
                } 
            },
            failure: function(result)
            {
                component.unmask();
                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
            }
        });
    }
};

/*
 * Funcion para crear una nueva route map
 */
function routeMapNew()
{
    storeRutasBgp = new Ext.data.Store({  
        pageSize: 5,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url : mostrarRutas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: globalData.idServicio,
                origen  : 'AdminBGP'
            }
        },
        fields:
            [
              {name:'id',           mapping:'idRutaElemento'},
              {name:'nombreRuta',   mapping:'nombreRutaElemento'},
              {name:'ip',           mapping:'ip'},
              {name:'subred',       mapping:'subred'},
              {name:'mascara',      mapping:'mascara'},
              {name:'tipo',         mapping:'tipo'},
              {name:'feCreacion',   mapping:'feCreacion'}
            ]
    });
    
    var gridSubredes;
    if(!Ext.isEmpty(routemapseleccionado))    
    {
        var component = Ext.get(windowAdministracion.getId());
        storeSubredes = new Ext.data.Store({
            proxy: {
                type: 'ajax',
                url: urlGetPrefixRouteMap,
                extraParams: {
                    opcion: 'RM-P',
                    routeMap: routemapseleccionado,
                    idServicio: globalData.idServicio
                },
                reader: {
                    type: 'json',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'idPrefix',  mapping: 'idPrefix'},
                    {name: 'prefixIp',  mapping: 'prefixIp'},
                    {name: 'prefixMask',mapping: 'prefixMask'},
                    {name: 'tipo',      mapping: 'tipo'},
                    {name: 'valor',     mapping: 'valor'},
                    {name: 'seq',       mapping: 'seq'}
                ],
            autoLoad: true,
            listeners:{
                beforeload:function()
                {                    
                    component.mask('Obteniendo prefijos existentes...');
                },
                load:function()
                {
                    component.unmask();
                    gridSubredes        = getPrefixGrid();
                    showWindow(gridSubredes);
                }
            }
        });
    }
    else
    {
        Ext.define('infoSubredes', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idPrefix',  mapping: 'idPrefix'},
                {name: 'prefixIp',  mapping: 'prefixIp'},
                {name: 'prefixMask',mapping: 'prefixMask'},
                {name: 'tipo',      mapping: 'tipo'},
                {name: 'valor',     mapping: 'valor'},
                {name: 'seq',       mapping: 'seq'}
            ]
        });

        storeSubredes = Ext.create('Ext.data.Store',
            {
                autoDestroy: true,
                autoLoad: false,
                model: 'infoSubredes'            
            });
            
        gridSubredes        = getPrefixGrid();
        showWindow(gridSubredes);
    }
}

function showWindow(gridSubredes)
{
    var boolEsSubredChecked = true;
    
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
            columns: 1
        },
        items: [
            {
                xtype: 'container',
                layout: {
                    type: 'table',
                    columns: 3,
                    align: 'stretch'
                },
                items: [
                    {width: '10%', border: false},
                    {
                        id:'rgRouteMapAdd',
                        xtype: 'radiogroup',
                        fieldLabel: '<b>Tipo de Route<b>',
                        columns: 1,
                        width: '100%',
                        items: [
                            {
                                boxLabel: '<span>Subredes</span>',
                                id: 'rbSubredes',
                                name: 'rbTipoRoute',
                                inputValue: "subredes",
                                listeners:
                                   {
                                        change: function(cb, nv, ov)
                                        {
                                            if (nv)
                                            {                                                
                                                Ext.getCmp('subredesRouteMap').show();
                                                Ext.getCmp('asPrivado').hide();
                                                boolEsSubredChecked = true;
                                            }
                                        }
                                    }
                            },
                            {
                                boxLabel: '<span>As Privado</span>',
                                id: 'rbAsPrivado',
                                name: 'rbTipoRoute',
                                inputValue: "asPrivado",
                                listeners:
                                   {
                                        change: function(cb, nv, ov)
                                        {
                                            if (nv)
                                            {
                                                Ext.getCmp('subredesRouteMap').hide();
                                                Ext.getCmp('asPrivado').show();
                                                boolEsSubredChecked = false;
                                            }
                                        }
                                    }
                            }
                        ]
                    },
                    {width: '10%', border: false},
                    
                    //---------------- opciones -------------------
                    {width: '10%', border: false},
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 3,
                            align: 'stretch'
                        },
                        items: [
                            {width: '10%', border: false},
                            {
                                xtype: 'fieldset',
                                title: "Subredes",
                                id: 'subredesRouteMap',
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 3,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {width: '10%', border: false},
                                            gridSubredes,
                                            {width: '10%', border: false}
                                        ]
                                    }
                                ]
                            },
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {
                                xtype: 'fieldset',
                                title: "AsPrivado",
                                id: 'asPrivado',
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 3,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'AS Privado',
                                                displayField: globalData.asPrivado,
                                                value: globalData.asPrivado,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '50%'
                                            },
                                            {width: '10%', border: false}
                                        ]
                                    }
                                ]
                            },
                            {width: '10%', border: false}
                        ]
                    }
                ]
            }
        ],
        buttons: [
            {
                text: 'Ejecutar',
                iconCls: 'icon_ejecutarScript',
                handler: function() 
                {
                    var selector = Ext.getCmp('rgRouteMapAdd').getValue().rbTipoRoute;
                    
                    if(!Ext.isEmpty(selector))
                    {
                        var info;
                    
                        if(boolEsSubredChecked)
                        {
                            info = getInfoGrid(gridSubredes);
                        }
                        else
                        {
                            info = globalData.asPrivado;
                            
                            if(Ext.isEmpty(info))
                            {
                                Ext.Msg.alert('Advertencia', 'No existe información de As Privado para crear la routemap');
                                return;
                            }
                        }
                        
                        windowCrearRm.hide();

                        //Enviar informacion a ejecutar
                        BGP.crearEliminarRouteMap(info,'agregar');
                    }
                    else
                    {
                        Ext.Msg.alert('Advertencia', 'Debe escoger una opción para poder continuar');
                        return;
                    }
                }
            },
            {
                text: 'Cerrar',
                iconCls: 'icon_cerrar',
                handler: function() {                    
                    windowCrearRm.close();
                }
            }]
    });
    
    windowCrearRm = Ext.create('Ext.window.Window', {
        id:'winRm',
        title: '<b>Crear Nuevo RouteMap<b>',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    
    Ext.getCmp('subredesRouteMap').hide();
    Ext.getCmp('asPrivado').hide();
}

function getShowRunConfigurationPe(idServicio)
{
    //Obtener la configuracion del PE    
    Ext.get(formPanelShowRun.getId()).mask("Obteniendo Configuración del Pe...");
    Ext.Ajax.request({
        url: verConfiguracionOlt,
        method: 'post',
        timeout: 400000,
        params: 
        {
            idServicio           : idServicio,
            requiereInfoCompleta : 'N'
        },
        success: function(response)
        {
            Ext.get(formPanelShowRun.getId()).unmask();
            var datos = response.responseText;
            Ext.getCmp('panelShowRun').el.update(datos);
        }
    });
}