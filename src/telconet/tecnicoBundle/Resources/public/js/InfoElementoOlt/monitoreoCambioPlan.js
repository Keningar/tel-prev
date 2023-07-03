
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();         

    store = new Ext.data.Store({
        pageSize: 100,
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: urlGridMonitoreo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: ''               
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreOlt', mapping: 'nombreOlt'},
                {name: 'totalClientes', mapping: 'totalClientes'},
                {name: 'totalAProcesar', mapping: 'totalAProcesar'},
                {name: 'totalNoConfigurados', mapping: 'totalNoConfigurados'},
                {name: 'estadoMigracion', mapping: 'estadoMigracion'},
                {name: 'fechaInicio', mapping: 'fechaInicio'},
                {name: 'fechaFin', mapping: 'fechaFin'}                
            ]
    });
    
    grid = Ext.create('Ext.grid.Panel', {
        id:'grid',
        width: 950,
        height: 350,
        store: store,        
        loadMask: true,  
        frame:true,
        viewConfig: {emptyText: 'No hay datos para mostrar'},        
        columns: [
            {
                xtype: 'rownumberer',
                width: 30
            },
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Olt',
                dataIndex: 'nombreOlt',
                width: 200,
                sortable: true
            },
            {
                header: 'Total Clientes',
                dataIndex: 'totalClientes',
                width: 80,
                align: 'center'
            },
            {
                header: 'Procesados',
                dataIndex: 'totalAProcesar',
                width: 80,
                align: 'center'
            },
            {
                header: 'Sin Configurar',
                dataIndex: 'totalNoConfigurados',
                width: 100,
                align: 'center'
            },
            {
                id: 'procesando',
                header: '',
                dataIndex: 'procesando',
                width: 20,
                renderer: renderAccionEjecutando
            },
            {
                header: 'Estado',
                dataIndex: 'estadoMigracion',
                width: 90,
                align: 'center'
            },            
            {
                header: 'Fecha Inicio',
                dataIndex: 'fechaInicio',
                width: 120,
                align: 'center'
            },
            {
                header: 'Fecha Fin',
                dataIndex: 'fechaFin',
                width: 120,
                align: 'center'
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 65,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            
                            if(rec.get('totalNoConfigurados')===0)
                            {
                                return 'button-grid-invisible';
                            }
                            else
                            {
                                return 'button-grid-show';
                            }
                            
                        },
                        tooltip: 'Ver Clientes Sin Configurar',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            verNoConfigurados(rec.get('idElemento'));                                                       
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-logs';
                        },
                        tooltip: 'Ver Historial',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            verHistorial(rec.get('idElemento'),rec.get('nombreOlt'));                            
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 950,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }

        ],
        items: [
            {width: '10%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                width: '200px'
            },
            {width: '20%', border: false},
            {width: '10%', border: false}
        ],
        renderTo: 'filtro'
    });
    
    setInterval('ejecutarCadaTiempo()',150000);
});

function buscar() {
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;   
    store.load();
}

function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");
    store.load({params: {
            nombreElemento: Ext.getCmp('txtNombre').value
        }});
}

function verHistorial(idElemento,nombreOlt)
{
    var storeHistorial = new Ext.data.Store({
        total: 'total',          
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: urlAccionesMonitoreo,
            extraParams: {idElemento: idElemento,accion:'Historial'},
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [                                
                {name: 'totalClientes', mapping: 'totalClientes'},
                {name: 'totalClientesSi', mapping: 'totalClientesSi'},
                {name: 'totalClientesNo', mapping: 'totalClientesNo'},                
                {name: 'totalNoConfigurados', mapping: 'totalNoConfigurados'},                
                {name: 'fechaInicio', mapping: 'fechaInicio'},
                {name: 'fechaFin', mapping: 'fechaFin'}   
            ]
    });
    
    gridHistorial = Ext.create('Ext.grid.Panel', {
        id: 'gridHistorial',
        store: storeHistorial,
        viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        columnLines: true,
        columns: [
            {
                header: 'Total Clientes',
                dataIndex: 'totalClientes',
                width: 80,
                align: 'center'
            },
            {
                header: 'Total Clientes SI',
                dataIndex: 'totalClientesSi',
                width: 90,
                align: 'center'
            },
            {
                header: 'Total Clientes NO',
                dataIndex: 'totalClientesNo',
                width: 100,
                align: 'center'
            },           
            {
                header: 'Sin Configurar',
                dataIndex: 'totalNoConfigurados',
                width: 100,
                align: 'center'
            },                            
            {
                header: 'Fecha Inicio',
                dataIndex: 'fechaInicio',
                width: 120,
                align: 'center'
            },
            {
                header: 'Fecha Fin',
                dataIndex: 'fechaFin',
                width: 120,
                align: 'center'
            }
        ],
        width: 600,
        height: 250,
        frame: true,        
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeHistorial,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj)
            {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function(grid)
            {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                    {
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

    var winPuertosTarjeta = Ext.create('Ext.window.Window', {
        title: 'Historial de Procesamientos para <b>'+nombreOlt+'</b>',
        modal: true,
        width: 650,
        closable: false,
        layout: 'fit',
        items: [gridHistorial],
        buttons: [{
                    xtype: 'button',
                    text: 'Cerrar',
                    align: 'center',
                    formBind: true,
                    width: '250',
                    height: 25,
                    handler: function() {
                        winPuertosTarjeta.destroy();                        
                    }
                }]
    }).show();
}

function verNoConfigurados(idElemento)
{
    filterPanelLogines = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, 
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 900,
        height:100,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscarCliente(idElemento);
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiarCliente(idElemento);
                }
            }

        ],
        items: [
            {width: '10%', border: false},
            {
                xtype: 'textfield',
                id: 'txtLoginCliente',
                fieldLabel: 'Login',
                value: '',
                width: '200px'
            },
            {width: '20%', border: false},
            {width: '10%', border: false}
        ]
    });
    
    storeGridClientes = new Ext.data.Store({
        total: 'total',
        pageSize: 10,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlAccionesMonitoreo,
            extraParams: {idElemento: idElemento,accion:'Clientes',login:''},
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'login', mapping: 'login'},
                {name: 'estado', mapping: 'estado'},
                {name: 'puerto', mapping: 'puerto'},
                {name: 'observacion', mapping: 'observacion'},
                {name: 'solicitud', mapping: 'solicitud'}
            ]
    });
    
    gridClientesSinConfigurar = Ext.create('Ext.grid.Panel', {
        id: 'gridClientesSinConfigurar',
        store: storeGridClientes,
        columnLines: true,
        columns: [
            {
                id: 'login',
                header: 'Login',
                dataIndex: 'login',
                width: 150,
                hidden: false,
                hideable: false
            },  
            {
                id: 'solicitud',
                header: 'Solicitud',
                dataIndex: 'solicitud',
                width: 80,
                hidden: false,
                hideable: false
            }, 
            {
                id: 'puerto',
                header: 'Puerto',
                dataIndex: 'puerto',
                width: 60,
                hidden: false,
                hideable: false
            },
            {
                id: 'estadoServicio',
                header: 'Estado Servicio',
                dataIndex: 'estado',
                width: 100,
                hidden: false,
                hideable: false
            }, 
            {
                id: 'observacion',
                header: 'Observacion',
                dataIndex: 'observacion',
                width: 460,
                hidden: false,
                hideable: false
            }
        ],
        width: 900,
        height: 350,
        frame: true,
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeGridClientes,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj)
            {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function(grid)
            {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                    {
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

    var winPuertosTarjeta = Ext.create('Ext.window.Window', {
        title: 'Clientes Sin Configurar',
        modal: true,
        width: 920,
        height: 510,
        closable: false,
        items: [filterPanelLogines,gridClientesSinConfigurar],
        buttons: [{
                    xtype: 'button',
                    text: 'Cerrar',
                    align: 'center',
                    formBind: true,
                    width: '250',
                    height: 25,
                    handler: function() {
                        winPuertosTarjeta.destroy();                        
                    }
                }]
    }).show();

}

function buscarCliente(idElemento) {    
    storeGridClientes.getProxy().extraParams.idElemento = idElemento;  
    storeGridClientes.getProxy().extraParams.accion     = 'Clientes';   
    storeGridClientes.getProxy().extraParams.login      = Ext.getCmp('txtLoginCliente').value;  
    storeGridClientes.currentPage = 1;
    storeGridClientes.load();
}

function limpiarCliente(idElemento) {
    Ext.getCmp('txtLoginCliente').value = "";
    Ext.getCmp('txtLoginCliente').setRawValue("");        
    storeGridClientes.load({params: {
            idElemento: idElemento,
            accion    : 'Clientes',
            login     : ''
        }});
}

function ejecutarCadaTiempo()
{
    store.load({params: {start: 0, limit: 100, nombreElemento:''}});
}

function renderAccionEjecutando(value, p, record) 
{
    var iconos='';
    if(record.data.estadoMigracion==='PROCESANDO' || record.data.estadoMigracion === 'PROC. LDAP' || record.data.estadoMigracion === 'CALCULANDO')
    {
        iconos=iconos+iconoEjecutando;                    
    }
    return Ext.String.format(iconos,value);
}