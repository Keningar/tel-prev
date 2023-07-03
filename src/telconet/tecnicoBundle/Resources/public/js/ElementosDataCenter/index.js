
var arrayDataCenter = Ext.JSON.decode(arrayDataCenters);
var storeDataCenter = null;

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    
    var storeTipoElementos = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,        
        proxy: {
            type: 'ajax',
            url : url_ajaxGetTiposElementosDataCenter,
            reader: {
                type: 'json'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            }
        },
        fields:
        [
            {name:'tipo', mapping:'tipo'}
        ]
    });
    
    //Se agrega a un store la información de los datacenters
    storeDataCenter = new Ext.data.Store({
        fields: ['id','nombreElemento'],
        data: arrayDataCenter.arrayRegistros
    });
                                           
    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',            
            url: url_ajaxGetElementosDataCenter,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: 
            {
                tipo      : 'Todos',
                nombre    : '',
                dataCenter: 'Todos'                
            }
        },
        fields:
            [
                {name: 'idElemento',           mapping: 'idElemento'},
                {name: 'nombreElemento',       mapping: 'nombreElemento'},
                {name: 'tipoElemento',         mapping: 'tipoElemento'},
                {name: 'ubicacion',            mapping: 'ubicacion'},
                {name: 'nombreElementoPadre',  mapping: 'nombreElementoPadre'},
                {name: 'tipoElementoPadre',    mapping: 'tipoElementoPadre'},
                {name: 'tipo',                 mapping: 'tipo'}
            ]
    });
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })
    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1000,
        height: 400,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        iconCls: 'icon-grid',
        viewConfig: { enableTextSelection: true },
        dockedItems: 
        [ 
            {
                xtype: 'toolbar',
                dock: 'top',
                hidden:!boolPermisoEditarElementoDCVirtual,
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_add',
                        text: 'Agregar Elemento Virtual',
                        itemId: 'agregarElementoVirtual',
                        scope: this,
                        handler: function () 
                        {
                            agregarElementoVirtual();
                        }
                    }
                ]
            }
        ],                  
        columns:[
                {
                  id: 'idElemento',
                  header: 'idElemento',
                  dataIndex: 'idElemento',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'tipoElemento',
                  header: 'Tipo',
                  dataIndex: 'tipoElemento', 
                  width: 100
                },
                {
                  id: 'nombreElemento',
                  header: 'Nombre',
                  dataIndex: 'nombreElemento',
                  width: 250,
                  sortable: true
                },
                {
                  header: 'Ubicación',
                  dataIndex: 'ubicacion',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Elemento Padre',
                  dataIndex: 'nombreElementoPadre',
                  width: 250,
                  sortable: true
                },
                {
                  header: 'Tipo Elemento Padre',
                  dataIndex: 'tipoElementoPadre',
                  width: 120,
                  sortable: true
                },
                {
                  header: 'Clasificación',
                  dataIndex: 'tipo',
                  width: 90,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 70,
                    items: 
                    [
                        {
                            getClass: function(v, meta, rec) 
                            {                                
                                if(rec.get('tipo') === 'FISICO' && boolPermisoEditarElementoDCActivo)
                                {
                                    return 'button-grid-edit';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Editar Detalle Elemento',
                            handler: function(grid, rowIndex, colIndex) 
                            {
                                var rec = store.getAt(rowIndex);
                                editarElementoDC(rec.get('idElemento'),'5k');
                            }
                        }
                    ]
                }
            ],
        renderTo: 'grid'
    });
    
    Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7, 
        border: false,        
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 7,
            align: 'center'
        },
        bodyStyle: {
            background: '#fff'
        },

        collapsible: true,
        collapsed: false,
        width: 1000,
        title: 'Criterios de búsqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function () {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function () {
                    limpiar();
                }
            }

        ],
        items: [
            {width: '10%', border: false},
            {width: '10%', border: false},                        
            {width: '10%', border: false},
            {
                xtype: 'combobox',
                fieldLabel: '<b>Tipo</b>',
                id: 'cmbTipo',
                value: 'Todos',
                displayField:    'tipo',                                                
                valueField:      'tipo',
                store: storeTipoElementos
            },
            {width: '20%', border: false},
            {
                id: 'txtNombre',
                fieldLabel: '<b>Nombre</b>',
                xtype: 'textfield',
                value: '',
                width: '200px'
            },
            {width: '10%', border: false},

            //-------------------------------------            
            {width: '10%', border: false},
            {width: '10%', border: false}, 
            {width: '10%', border: false},
            {
                xtype: 'combobox',
                id: 'cmbUbicacion',
                fieldLabel: '<b>Data Center</b>',
                displayField:    'nombreElemento',                                                
                valueField:      'id',
                store: storeDataCenter,
                value: 'Todos'
            },
            {width: '20%', border: false},
            {width: '20%', border: false},
            {width: '10%', border: false}
            //---------------------------------------
        ],
        renderTo: 'filtro'
    });         
    
});

function buscar()
{
    store.load({params: 
    {
        tipo:       Ext.getCmp('cmbTipo').value,
        dataCenter: Ext.getCmp('cmbUbicacion').value,
        nombre:     Ext.getCmp('txtNombre').value        
    }});
}

function limpiar()
{
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");   
    Ext.getCmp('cmbUbicacion').value="Todos";
    Ext.getCmp('cmbUbicacion').setRawValue("Todos");    
    Ext.getCmp('cmbTipo').value="Todos";
    Ext.getCmp('cmbTipo').setRawValue("Todos");     
    
    store.clearData();
    store.removeAll();
}  

function editarElementoDC(idElemento,tipo)
{
    if(tipo === '5k')
    {
        Ext.get(Ext.get(document.body)).mask('Cargando Información del Switch...');
    }
    else
    {
        Ext.get(Ext.get('panel')).mask('Cargando Información del Switch...');
    }
    
    Ext.Ajax.request({
        url: url_ajaxGetInformacionPuertosDataCenter,
        method: 'post',
        timeout: 1000000,
        params: 
        { 
            idElemento:    idElemento
        },
        success: function(response)
        {            
            var jsonInformacion = Ext.JSON.decode(response.responseText);

            if(tipo === '5k')
            {
                Ext.get(Ext.get(document.body)).unmask();
                obtenerInformacionPuertos(jsonInformacion);
            }
            else
            {
                Ext.get(Ext.get('panel')).unmask();
                obtenerInformacionPuertosNexus2k(jsonInformacion);
            }            
        },
        failure:function()
        {
            if(tipo === '5k')
            {
                Ext.get(Ext.get(document.body)).unmask();
            }
            else
            {
                Ext.get(Ext.get('panel')).unmask();
            }            
        }
    });
}

function obtenerInformacionPuertos(jsonInformacion)
{
    var storeInformacionInterfaces =  cargarStoreGridPuertos(jsonInformacion);     
    
    var gridPuertos = Ext.create('Ext.grid.Panel', {
        width: 670,
        height: 200,
        id: 'gridPuertos',
        store: cargarStoreGridPuertos(jsonInformacion),
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'idInterface',
                header: 'idInterface',
                dataIndex: 'idInterface',
                hidden: true,
                hideable: false
            },
            {
                id: 'idServicio',
                header: 'idServicio',
                dataIndex: 'idServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'idElementoInt',
                header: 'idElementoInt',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreInterface',
                header: 'Nombre Interface',
                dataIndex: 'nombreInterface',
                align: 'center',
                width: 150
            },
            {
                id: 'estadoInterface',
                header: 'Estado Interfaz',
                dataIndex: 'estadoInterface',
                width: 90,
                align: 'center',
                sortable: true,
                renderer: function (value)
                {
                    if (value === 'connected')
                    {
                        return '<span style="color:blue">' + value + '</span>';
                    } 
                    else
                    {
                        return '<span style="color:green">' + value + '</span>';
                    }
                }
            },
            {
                id: 'login',
                header: 'Login Conectado',
                dataIndex: 'login',
                width: 120,
                sortable: true,
                align: 'center',
                renderer: function (value)
                {
                    if (value === 'migrado')
                    {
                        return '<span style="font-style: italic;color:gray">' + value + '</span>';
                    } 
                    else
                    {
                        return value;
                    }
                }
            },
            {
                id: 'loginAux',
                header: 'Login Aux',
                dataIndex: 'loginAux',
                width: 120,
                sortable: true,
                align: 'center',
                renderer: function (value)
                {
                    if (value === 'migrado')
                    {
                        return '<span style="font-style: italic;color:gray">' + value + '</span>';
                    } 
                    else
                    {
                        return value;
                    }
                }
            },
            {
                id: 'estadoServicio',
                header: 'Estado Servicio',
                dataIndex: 'estadoServicio',
                width: 90,
                sortable: true,
                align: 'center',
                renderer: function (value)
                {
                    if (value === 'migrado')
                    {
                        return '<span style="font-style: italic;color:gray">' + value + '</span>';
                    } 
                    else
                    {
                        return value;
                    }
                }
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 70,
                items:
                    [
                        {
                            getClass: function (v, meta, rec)
                            {
                                if (rec.get('estadoInterface') === 'connected' && rec.get('login')==='migrado')
                                {
                                    return 'button-grid-desconectarInterface';
                                } 
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Liberar',
                            handler: function (grid, rowIndex, colIndex)
                            {
                                var rec = storeInformacionInterfaces.getAt(rowIndex);
                                actualizarPuerto(rec.get('idInterface'), 'ocupar',rec.get('idElemento'),'');
                            }
                        },
                        {
                            getClass: function (v, meta, rec)
                            {
                                if (rec.get('estadoInterface') === 'not connect')
                                {
                                    return 'button-grid-conectarInterface';
                                } 
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Ocupar',
                            handler: function (grid, rowIndex, colIndex)
                            {
                                var rec = storeInformacionInterfaces.getAt(rowIndex);
                                actualizarPuerto(rec.get('idInterface'), 'conectar',rec.get('idElemento'),'');
                            }
                        }
                    ]
            }
        ]
    });
    
    var storeSwitches2k = cargarStoreGridSwitches(jsonInformacion);
    
    var gridNexus2k = Ext.create('Ext.grid.Panel', {
        width: 400,        
        height:200,
        store: storeSwitches2k,
        loadMask: true,
        frame: false,
        columns:[
                {
                  id: 'idElemento2k',
                  header: 'idElemento',
                  dataIndex: 'idElemento',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombreElemento2k',
                  header: 'Nombre Switch 2k',
                  dataIndex: 'nombreElemento', 
                  width: 150
                },                 
                {
                  id: 'estadoElemento2k',
                  header: 'Estado',
                  dataIndex: 'estadoElemento',
                  width: 90,
                  sortable: true
                },                
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 90,
                    items: 
                    [
                        {
                            getClass: function(v, meta, rec) 
                            {                                
                               return 'button-grid-show';
                            },
                            tooltip: 'Consultar Puertos',
                            handler: function(grid, rowIndex, colIndex) 
                            {
                                var rec = storeSwitches2k.getAt(rowIndex);
                                editarElementoDC(rec.get('idElemento'),'2k');
                            }
                        }                        
                    ]
                }
            ]
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true, 
        id:'panel',
        width:750,
        layout: {
            type: 'table',
            columns: 1
        },
        items: 
        [
            {
                id: 'containerPuertosSwitch',
                width:700,
                xtype: 'fieldset',
                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Puertos Giga</b>',
                defaultType: 'textfield',
                items: [
                {
                    xtype: 'container',
                    layout: {
                        type: 'table',
                        columns: 1,
                        align: 'stretch'
                    },
                    items: 
                    [
                        gridPuertos
                    ]
                }]
            },
            
            {
                id: 'containerNexus2k',
                xtype: 'fieldset',
                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Nexus 2k Relacionados</b>',
                defaultType: 'textfield',
                items: [
                {
                    xtype: 'container',
                    layout: {
                        type: 'table',
                        columns: 1,
                        align: 'stretch'
                    },
                    items: 
                    [
                        gridNexus2k
                    ]
                }]
            }            
        ],
        buttons:
            [
                {
                    text: 'Cerrar',
                    handler: function ()
                    {
                        win.destroy();
                    }
                }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Información de Puertos del Switch',
        id:'winPuertosSwitch',
        modal: true,
        width: 750,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function obtenerInformacionPuertosNexus2k(jsonInformacion)
{
    var storeInformacionInterfaces =  cargarStoreGridPuertos(jsonInformacion);     
    
    var gridPuertosNexus2k = Ext.create('Ext.grid.Panel', {
        width: 670,
        height: 200,
        id: 'gridPuertos2k',
        store: cargarStoreGridPuertos(jsonInformacion),
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'idInterface2k',                
                dataIndex: 'idInterface',
                hidden: true,
                hideable: false
            },
            {
                id: 'idServicio2k',                
                dataIndex: 'idServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'idElementoInt2k',                
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreInterface2k',
                header: 'Nombre Interface',
                dataIndex: 'nombreInterface',
                align: 'center',
                width: 150
            },
            {
                id: 'estadoInterface2k',
                header: 'Estado Interfaz',
                dataIndex: 'estadoInterface',
                width: 90,
                align: 'center',
                sortable: true,
                renderer: function (value)
                {
                    if (value === 'connected')
                    {
                        return '<span style="color:blue">' + value + '</span>';
                    } 
                    else
                    {
                        return '<span style="color:green">' + value + '</span>';
                    }
                }
            },
            {
                id: 'login2k',
                header: 'Login Conectado',
                dataIndex: 'login',
                width: 120,
                sortable: true,
                align: 'center',
                renderer: function (value)
                {
                    if (value === 'migrado')
                    {
                        return '<span style="font-style: italic;color:gray">' + value + '</span>';
                    } 
                    else
                    {
                        return value;
                    }
                }
            },
            {
                id: 'loginAux2k',
                header: 'Login Aux',
                dataIndex: 'loginAux',
                width: 120,
                sortable: true,
                align: 'center',
                renderer: function (value)
                {
                    if (value === 'migrado')
                    {
                        return '<span style="font-style: italic;color:gray">' + value + '</span>';
                    } 
                    else
                    {
                        return value;
                    }
                }
            },
            {
                id: 'estadoServicio2k',
                header: 'Estado Servicio',
                dataIndex: 'estadoServicio',
                width: 90,
                sortable: true,
                align: 'center',
                renderer: function (value)
                {
                    if (value === 'migrado')
                    {
                        return '<span style="font-style: italic;color:gray">' + value + '</span>';
                    } 
                    else
                    {
                        return value;
                    }
                }
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 70,
                items:
                    [
                        {
                            getClass: function (v, meta, rec)
                            {
                                if (rec.get('estadoInterface') === 'connected' && rec.get('login')==='migrado')
                                {
                                    return 'button-grid-desconectarInterface';
                                } 
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Liberar',
                            handler: function (grid, rowIndex, colIndex)
                            {
                                var rec = storeInformacionInterfaces.getAt(rowIndex);
                                actualizarPuerto(rec.get('idInterface'), 'ocupar',rec.get('idElemento'),'2k');
                            }
                        },
                        {
                            getClass: function (v, meta, rec)
                            {
                                if (rec.get('estadoInterface') === 'not connect')
                                {
                                    return 'button-grid-conectarInterface';
                                } 
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Ocupar',
                            handler: function (grid, rowIndex, colIndex)
                            {
                                var rec = storeInformacionInterfaces.getAt(rowIndex);
                                actualizarPuerto(rec.get('idInterface'), 'conectar',rec.get('idElemento'),'2k');
                            }
                        }
                    ]
            }
        ]
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,                
        width:750,
        layout: {
            type: 'table',
            columns: 1
        },
        items: 
        [
            {
                id: 'containerPuertosNexus',
                width:700,
                xtype: 'fieldset',
                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Puertos Nexus</b>',
                defaultType: 'textfield',
                items: [
                {
                    xtype: 'container',
                    layout: {
                        type: 'table',
                        columns: 1,
                        align: 'stretch'
                    },
                    items: 
                    [
                        gridPuertosNexus2k
                    ]
                }]
            }        
        ],
        buttons:
            [
                {
                    text: 'Cerrar',
                    handler: function ()
                    {
                        win.destroy();
                    }
                }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Información de Puertos del Switch 2k',
        id:'winPuertosSwitch2k',
        modal: true,
        width: 750,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function actualizarPuerto(idInterface, accion, idElemento,nexus)
{
    Ext.get('gridPuertos'+nexus).mask('Actualizando puerto...');
    
    Ext.Ajax.request({
        url: url_ajaxActualizarPuertosDataCenter,
        method: 'post',
        timeout: 1000000,
        params: 
        { 
            idInterface:    idInterface,
            accion:         accion            
        },
        success: function(response)
        {
            Ext.Msg.alert('Mensaje',response.responseText, function(btn){
                if(btn=='ok')
                {                    
                    //se cargará nuevamente 
                    Ext.Ajax.request({
                        url: url_ajaxGetInformacionPuertosDataCenter,
                        method: 'post',
                        timeout: 1000000,
                        params: 
                        { 
                            idElemento:    idElemento
                        },
                        success: function(response)
                        {                                                        
                            var jsonInformacion = Ext.JSON.decode(response.responseText);
                            
                            Ext.get('gridPuertos'+nexus).unmask();
                            Ext.getCmp('gridPuertos'+nexus).bindStore(cargarStoreGridPuertos(jsonInformacion));
                        },
                        failure:function()
                        {
                            Ext.get('gridPuertos'+nexus).unmask();
                        }
                    });
                }
            });
        },
        failure:function()
        {
            Ext.get(Ext.get('gridPuertos')).unmask();
        }
    });
}

function cargarStoreGridPuertos(jsonInformacion)
{
    //Se crea dinamicamente el store del grid
    Ext.define('informacionInterfacesModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idInterface',     type: 'integer'},
            {name: 'idElemento',      type: 'integer'},
            {name: 'idServicio',      type: 'integer'},
            {name: 'nombreInterface', type: 'string'},
            {name: 'estadoInterface', type: 'string'},
            {name: 'loginAux',        type: 'string'},
            {name: 'estadoServicio',  type: 'string'},
            {name: 'login',           type: 'string'}           
        ]
    });   
        
    var storeInformacionInterfaces = Ext.create('Ext.data.Store', {
        pageSize: 10,
        autoDestroy: true,
        model: 'informacionInterfacesModel',
        proxy: {
            type: 'memory'
        }
    });
    
    Ext.each(jsonInformacion.interfaces,function(json){
        var recordParamDet = Ext.create('informacionInterfacesModel', {
            idInterface     : json.idInterface,
            idElemento      : json.idElemento,
            idServicio      : json.idServicio,
            nombreInterface : json.nombreInterface,
            estadoInterface : json.estadoInterface,
            loginAux        : json.loginAux,
            estadoServicio  : json.estadoServicio,
            login           : json.login            
        });
    
        storeInformacionInterfaces.insert(0, recordParamDet);
    });     
    
    return storeInformacionInterfaces;
}

function cargarStoreGridSwitches(jsonInformacion)
{
    //Se crea dinamicamente el store del grid
    Ext.define('informacionSwitchesModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idElemento',     type: 'integer'},
            {name: 'nombreElemento', type: 'string'},
            {name: 'estadoElemento', type: 'string'}                    
        ]
    });   
        
    var storeInformacionSwitches = Ext.create('Ext.data.Store', {
        pageSize: 10,
        autoDestroy: true,
        model: 'informacionSwitchesModel',
        proxy: {
            type: 'memory'
        }
    });
    
    Ext.each(jsonInformacion.switches,function(json){
        var recordParamDet = Ext.create('informacionSwitchesModel', {
            idElemento     : json.idElemento,
            nombreElemento : json.nombreElemento,
            estadoElemento : json.estado                    
        });
    
        storeInformacionSwitches.insert(0, recordParamDet);
    });     
    
    return storeInformacionSwitches;
}
