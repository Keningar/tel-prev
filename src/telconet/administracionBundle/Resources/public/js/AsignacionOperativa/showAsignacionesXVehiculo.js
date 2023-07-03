var storeHistorialAV = null;
var storeHistorialAP = null;
var win                        = null;

Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var connEsperaAccion = new Ext.data.Connection
    ({
	listeners:
        {
            'beforerequest': 
            {
                fn: function (con, opt)
                {						
                    Ext.MessageBox.show
                    ({
                        msg: 'Grabando los datos, Por favor espere!!',
                        progressText: 'Saving...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200}
                    });
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': 
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
	}
    });


Ext.onReady(function()
{
    var dateActual=new Date();
    var firstDay = new Date(dateActual.getFullYear(), dateActual.getMonth(), 1);
    var lastDay=new Date(dateActual.getFullYear(),dateActual.getMonth()+1,0);   
    
    
    DTFechaDesdeAV = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'fechaDesdeAsignacionAV',
            name:'fechaDesdeAsignacionAV',
            fieldLabel: '<b>Desde</b>',
            editable: false,
            format: 'd/m/Y',
            emptyText: "Seleccione",
            labelWidth: 140,
            value: firstDay,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasFiltro(cmp,1);
                }
            }
     });

    DTFechaHastaAV = new Ext.form.DateField({
        xtype: 'datefield',
        id: 'fechaHastaAsignacionAV',
        name:'fechaHastaAsignacionAV',
        editable: false,
        fieldLabel: '<b>Hasta</b>',
        format: 'd/m/Y',
        emptyText: "Seleccione",
        labelWidth: 110,
        value:lastDay,
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarFechasFiltro(cmp,1);
            }
        }
    });
    
    storeHistorialAV = new Ext.data.Store({ 
                    id:'verHistorialAsignacionVehicularYProvisionalStore',
                    total: 'total',
                    pageSize: 20,
                    autoLoad: true,
                    proxy: {
                            type: 'ajax',                
                            url: strUrlShowHistorialAsignacionVehicularXVehiculo,
                            reader: {
                                type: 'json', 
                                totalProperty: 'total', 
                                root: 'encontrados'
                            },
                            extraParams:
                            {
                                idElemento: intIdElemento,
                                fechaDesde: Ext.getCmp('fechaDesdeAsignacionAV').getSubmitValue(),
                                fechaHasta: Ext.getCmp('fechaHastaAsignacionAV').getSubmitValue(),
                                errorFechas: 0
                            }
                    },
                    fields:
                    [
                    
                        {name:'strFechaInicioAsignacionVehicularHisto',                 mapping:'strFechaInicioAsignacionVehicularHisto'},
                        {name:'strFechaFinAsignacionVehicularHisto',                    mapping:'strFechaFinAsignacionVehicularHisto'},
                        {name:'strHoraInicioAsignacionVehicularHisto',                  mapping:'strHoraInicioAsignacionVehicularHisto'},
                        {name:'strHoraFinAsignacionVehicularHisto',                     mapping:'strHoraFinAsignacionVehicularHisto'},
                        {name:'strHorasAsignacionVehicularHisto',                       mapping:'strHorasAsignacionVehicularHisto'},
                        {name:'strCuadrillaAsignacionVehicularHisto',                   mapping:'strCuadrillaAsignacionVehicularHisto'},
                        {name:'intIdPersonaEmpresaRolChoferAsignacionVehicularHisto',   mapping:'intIdPersonaEmpresaRolChoferAsignacionVehicularHisto'},
                        {name:'intIdPersonaChoferAsignacionVehicularHisto',             mapping:'intIdPersonaChoferAsignacionVehicularHisto'},
                        {name:'strNombresChoferAsignacionVehicularHisto',      mapping:'strNombresChoferAsignacionVehicularHisto'},
                        {name:'strApellidosChoferAsignacionVehicularHisto',      mapping:'strApellidosChoferAsignacionVehicularHisto'},
                        {name:'strIdentificacionChoferAsignacionVehicularHisto',      mapping:'strIdentificacionChoferAsignacionVehicularHisto'},
                        {name:'strEstadoAsignacionVehicularHisto',                      mapping:'strEstadoAsignacionVehicularHisto'},
                        
                    ]
    });
    
    
    var gridHistorialAV = Ext.create('Ext.grid.Panel', {
        id: 'gridHistorialAsignacionVehicular',
        store: storeHistorialAV,
        timeout: 60000,
        width: 580,
        height: 510,
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
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex,
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
                });
            }
        },
        columns:
            [
                {
                    header: 'Fecha Inicio',
                    dataIndex: 'strFechaInicioAsignacionVehicularHisto',
                    width: 75
                },
                {
                    header: 'Fecha Fin',
                    dataIndex: 'strFechaFinAsignacionVehicularHisto',
                    width: 75
                },
                {
                    header: 'Hora Inicio',
                    dataIndex: 'strHoraInicioAsignacionVehicularHisto',
                    width: 75
                },
                {
                    header: 'Hora Fin',
                    dataIndex: 'strHoraFinAsignacionVehicularHisto',
                    width: 75
                },
                {
                    header: 'Cuadrilla',
                    dataIndex: 'strCuadrillaAsignacionVehicularHisto',
                    width: 150
                },
                {
                    header: 'Apellidos Chofer Predefinido',
                    dataIndex: 'strApellidosChoferAsignacionVehicularHisto',
                    width: 150
                },
                {
                    header: 'Nombres Chofer Predefinido',
                    dataIndex: 'strNombresChoferAsignacionVehicularHisto',
                    width: 150
                },
                {
                    header: 'Identificación Chofer Predefinido',
                    dataIndex: 'strIdentificacionChoferAsignacionVehicularHisto',
                    width: 170
                },
                {
                    header: 'Estado',
                    dataIndex: 'strEstadoAsignacionVehicularHisto',
                    width: 100
                }
                
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeHistorialAV,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
            }),
            renderTo: 'gridAsignacionesVehiculares',
            title: 'Asignaciones Vehiculares a Cuadrillas',
        });
        
    var filterPanelAsignacionVehicular = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'vbox',
            columns: 2,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 580,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() 
                {
                    buscarAsignacionVehicular();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() 
                {
                    limpiarAsignacionVehicular();
                }
            }
        ],
        items: 
        [
        
            {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {
                            width: 270,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                DTFechaDesdeAV

                            ]
                        },
                        {
                            width:50,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {
                            width: 250,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                DTFechaHastaAV

                            ]
                        }
                    ]
                },
                
                
                
                
                {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {
                            width: 270,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXIdentificacionChoferAV',
                                    fieldLabel: '<b>Identificación Chofer</b>',
                                    labelWidth:140,
                                    value: ''
                                }

                            ]
                        },
                        {
                            width:50,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {
                            width: 250,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }

                            ]
                        }
                        
                    ]
                },
                
                
                
                
                {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {
                            width: 270,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXNombresChoferAV',
                                    fieldLabel: '<b>Nombres Chofer</b>',
                                    labelWidth:140,
                                    value: ''
                                }

                            ]
                        },
                        {
                            width:50,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {
                            width: 250,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXApellidosChoferAV',
                                    fieldLabel: '<b>Apellidos Chofer</b>',
                                    labelWidth:110,
                                    value: ''
                                }

                            ]
                        }
                        
                    ]
                }
        ],
        renderTo: 'filtroAsignacionesVehiculares'
    });
    
    
    
    
    
        
    DTFechaDesdeAP = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'fechaDesdeAsignacionAP',
            name:'fechaDesdeAsignacionAP',
            fieldLabel: '<b>Desde</b>',
            editable: false,
            format: 'd/m/Y',
            emptyText: "Seleccione",
            labelWidth: 140,
            value: firstDay,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasFiltro(cmp,2);
                }
            }
     });

    DTFechaHastaAP = new Ext.form.DateField({
        xtype: 'datefield',
        id: 'fechaHastaAsignacionAP',
        name:'fechaHastaAsignacionAP',
        editable: false,
        fieldLabel: '<b>Hasta</b>',
        format: 'd/m/Y',
        emptyText: "Seleccione",
        labelWidth: 110,
        value: lastDay,
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarFechasFiltro(cmp,2);
            }
        }
    });     
    
    storeHistorialAP = new Ext.data.Store({ 
                    id:'verHistorialAsignacionProvisionalStore',
                    total: 'total',
                    pageSize: 20,
                    autoLoad: true,
                    proxy: {
                            type: 'ajax',                
                            url: strUrlShowHistorialAsignacionProvisionalXVehiculo,
                            reader: {
                                type: 'json', 
                                totalProperty: 'total', 
                                root: 'encontrados'
                            },
                            extraParams:
                            {
                                idElemento: intIdElemento,
                                fechaDesde: Ext.getCmp('fechaDesdeAsignacionAP').getSubmitValue(),
                                fechaHasta: Ext.getCmp('fechaHastaAsignacionAP').getSubmitValue(),
                                errorFechas: 0
                            }
                    },
                    fields:
                    [
                        {name:'strFechaInicioAsignacionProvisionalHisto',        mapping:'strFechaInicioAsignacionProvisionalHisto'},
                        {name:'strFechaFinAsignacionProvisionalHisto',        mapping:'strFechaFinAsignacionProvisionalHisto'},
                        
                        {name:'strHoraInicioAsignacionProvisionalHisto',         mapping:'strHoraInicioAsignacionProvisionalHisto'},
                        {name:'strHoraFinAsignacionProvisionalHisto',         mapping:'strHoraFinAsignacionProvisionalHisto'},
                        
                        {name:'strObservacionProvisionalHisto',              mapping:'strObservacionProvisionalHisto'},
                        {name:'strMotivoProvisionalHisto',                   mapping:'strMotivoProvisionalHisto'},
                        {name:'strEstadoProvisionalHisto',                   mapping:'strEstadoProvisionalHisto'},

                        {name:'personaIdChoferProvisionalHisto',             mapping:'personaIdChoferProvisionalHisto'},
                        //{name:'strNombresApellidosChoferProvisionalHisto',   mapping:'strNombresApellidosChoferProvisionalHisto'},
                        {name:'strNombresChoferProvisionalHisto',   mapping:'strNombresChoferProvisionalHisto'},
                        {name:'strApellidosChoferProvisionalHisto',   mapping:'strApellidosChoferProvisionalHisto'},
                        {name:'strIdentificacionChoferProvisionalHisto',   mapping:'strIdentificacionChoferProvisionalHisto'},
                        
                        
                        {name:'personaEmpresaRolIdChoferProvisionalHisto',   mapping:'personaEmpresaRolIdChoferProvisionalHisto'},
                        {name:'idCuadrillaProvisionalHisto',                 mapping:'idCuadrillaProvisionalHisto'},
                        {name:'nombreCuadrillaProvisionalHisto',             mapping:'nombreCuadrillaProvisionalHisto'}

                    ]
    });
    
    
    var gridHistorialAP = Ext.create('Ext.grid.Panel', {
        id: 'gridHistorialAsignacionProvisional',
        store: storeHistorialAP,
        timeout: 60000,
        width: 580,
        height: 510,
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
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex,
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
                });
            }
        },
        columns:
            [
                {
                    header: 'Fecha Inicio',
                    dataIndex: 'strFechaInicioAsignacionProvisionalHisto',
                    width: 75
                },
                {
                    header: 'Fecha Fin',
                    dataIndex: 'strFechaFinAsignacionProvisionalHisto',
                    width: 75
                },
                {
                    header: 'Hora Inicio',
                    dataIndex: 'strHoraInicioAsignacionProvisionalHisto',
                    width: 75
                },
                {
                    header: 'Hora Fin',
                    dataIndex: 'strHoraFinAsignacionProvisionalHisto',
                    width: 75
                },
                {
                    header: 'Cuadrilla',
                    dataIndex: 'nombreCuadrillaProvisionalHisto',
                    width: 200
                },
                {
                    header: 'Apellidos Chofer Asignado',
                    dataIndex: 'strApellidosChoferProvisionalHisto',
                    width: 150
                },
                {
                    header: 'Nombres Chofer Asignado',
                    dataIndex: 'strNombresChoferProvisionalHisto',
                    width: 150
                },
                {
                    header: 'Identificacion Chofer Asignado',
                    dataIndex: 'strIdentificacionChoferProvisionalHisto',
                    width: 160
                },
                {
                    header: 'Motivo',
                    dataIndex: 'strMotivoProvisionalHisto',
                    width: 100
                },
                {
                    header: 'Estado',
                    dataIndex: 'strEstadoProvisionalHisto',
                    width: 100
                }
                
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeHistorialAP,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
            }),
            title: 'Asignaciones Provisionales de Choferes',
            renderTo: 'gridAsignacionesProvisionales'
        });
        
        

    
    
    var filterPanelAsignacionProvisional = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'vbox',
            columns: 2,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 580,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() 
                {
                    buscarAsignacionProvisional();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() 
                {
                    limpiarAsignacionProvisional();
                }
            }
        ],
        items: 
        [
            {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {
                            width: 270,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                DTFechaDesdeAP

                            ]
                        },
                        {
                            width:50,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {
                            width: 250,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                DTFechaHastaAP

                            ]
                        }
                    ]
                },
                
                
                
                
                {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {
                            width: 270,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXIdentificacionChoferProvisional',
                                    fieldLabel: '<b>Identificación Chofer</b>',
                                    labelWidth:140,
                                    value: ''
                                }

                            ]
                        },
                        {
                            width:50,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {
                            width: 250,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }

                            ]
                        }
                        
                    ]
                },
                
                
                
                
                {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {
                            width: 270,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXNombresChoferProvisional',
                                    fieldLabel: '<b>Nombres Chofer</b>',
                                    labelWidth:140,
                                    value: ''
                                }

                            ]
                        },
                        {
                            width:50,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {
                            width: 250,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXApellidosChoferProvisional',
                                    fieldLabel: '<b>Apellidos Chofer</b>',
                                    labelWidth:110,
                                    value: ''
                                }

                            ]
                        },
                        
                    ]
                }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        /*
            {
                layout: 'table',
                border: false,
                items: 
                [
                    {
                        width: 220,
                        layout: 'form',
                        border: false,
                        labelWidth:50,
                        items: 
                        [
                            DTFechaDesdeAP

                        ]
                    },
                    {
                        width:100,
                        layout: 'form',
                        border: false,
                        items: 
                        [
                            {
                                xtype: 'displayfield'
                            }
                        ]
                    },
                    {
                        width: 220,
                        layout: 'form',
                        border: false,
                        labelWidth:50,
                        items: 
                        [
                            DTFechaHastaAP

                        ]
                    }
                ]
            }*/
        ],
        renderTo: 'filtroAsignacionesProvisionales'
    });



});


function buscarAsignacionProvisional()
{
    storeHistorialAP.loadData([],false);
    storeHistorialAP.currentPage = 1;
    storeHistorialAP.getProxy().extraParams.fechaDesde          = Ext.getCmp('fechaDesdeAsignacionAP').getSubmitValue();
    storeHistorialAP.getProxy().extraParams.fechaHasta          = Ext.getCmp('fechaHastaAsignacionAP').getSubmitValue();
    
    storeHistorialAP.getProxy().extraParams.identificacionChoferProvisional = Ext.getCmp('strBuscarXIdentificacionChoferProvisional').getValue();
    storeHistorialAP.getProxy().extraParams.nombresChoferProvisional        = Ext.getCmp('strBuscarXNombresChoferProvisional').getValue();
    storeHistorialAP.getProxy().extraParams.apellidosChoferProvisional      = Ext.getCmp('strBuscarXApellidosChoferProvisional').getValue();
    
    
    
    storeHistorialAP.load();
}


function limpiarAsignacionProvisional()
{
    var dateActual=new Date();
    var firstDay = new Date(dateActual.getFullYear(), dateActual.getMonth(), 1);
    var lastDay=new Date(dateActual.getFullYear(),dateActual.getMonth()+1,0); 
    
    Ext.getCmp('fechaDesdeAsignacionAP').setValue(firstDay);
    Ext.getCmp('fechaHastaAsignacionAP').setValue(lastDay);
    
    storeHistorialAP.loadData([],false);
    storeHistorialAP.currentPage = 1;
    storeHistorialAP.getProxy().extraParams.fechaDesde          = Ext.getCmp('fechaDesdeAsignacionAP').getSubmitValue();
    storeHistorialAP.getProxy().extraParams.fechaHasta          = Ext.getCmp('fechaHastaAsignacionAP').getSubmitValue();
    
    Ext.getCmp('strBuscarXIdentificacionChoferProvisional').value="";
    Ext.getCmp('strBuscarXIdentificacionChoferProvisional').setRawValue("");
    Ext.getCmp('strBuscarXNombresChoferProvisional').value="";
    Ext.getCmp('strBuscarXNombresChoferProvisional').setRawValue("");
    Ext.getCmp('strBuscarXApellidosChoferProvisional').value="";
    Ext.getCmp('strBuscarXApellidosChoferProvisional').setRawValue("");
    
    storeHistorialAP.getProxy().extraParams.identificacionChoferProvisional = Ext.getCmp('strBuscarXIdentificacionChoferProvisional').getValue();
    storeHistorialAP.getProxy().extraParams.nombresChoferProvisional        = Ext.getCmp('strBuscarXNombresChoferProvisional').getValue();
    storeHistorialAP.getProxy().extraParams.apellidosChoferProvisional      = Ext.getCmp('strBuscarXApellidosChoferProvisional').getValue();
    
    
    
    storeHistorialAP.load();
}




function buscarAsignacionVehicular()
{
    storeHistorialAV.loadData([],false);
    storeHistorialAV.currentPage = 1;
    storeHistorialAV.getProxy().extraParams.fechaDesde          = Ext.getCmp('fechaDesdeAsignacionAV').getSubmitValue();
    storeHistorialAV.getProxy().extraParams.fechaHasta          = Ext.getCmp('fechaHastaAsignacionAV').getSubmitValue();
    
    
    storeHistorialAV.getProxy().extraParams.identificacionChoferAV = Ext.getCmp('strBuscarXIdentificacionChoferAV').getValue();
    storeHistorialAV.getProxy().extraParams.nombresChoferAV        = Ext.getCmp('strBuscarXNombresChoferAV').getValue();
    storeHistorialAV.getProxy().extraParams.apellidosChoferAV      = Ext.getCmp('strBuscarXApellidosChoferAV').getValue();
    
    
    storeHistorialAV.load();
}


function limpiarAsignacionVehicular()
{
    var dateActual=new Date();
    var firstDay = new Date(dateActual.getFullYear(), dateActual.getMonth(), 1);
    var lastDay=new Date(dateActual.getFullYear(),dateActual.getMonth()+1,0); 
    
    Ext.getCmp('fechaDesdeAsignacionAV').setValue(firstDay);
    Ext.getCmp('fechaHastaAsignacionAV').setValue(lastDay);
    
    storeHistorialAV.loadData([],false);
    storeHistorialAV.currentPage = 1;
    storeHistorialAV.getProxy().extraParams.fechaDesde          = Ext.getCmp('fechaDesdeAsignacionAV').getSubmitValue();
    storeHistorialAV.getProxy().extraParams.fechaHasta          = Ext.getCmp('fechaHastaAsignacionAV').getSubmitValue();
    
    
    Ext.getCmp('strBuscarXIdentificacionChoferAV').value="";
    Ext.getCmp('strBuscarXIdentificacionChoferAV').setRawValue("");
    Ext.getCmp('strBuscarXNombresChoferAV').value="";
    Ext.getCmp('strBuscarXNombresChoferAV').setRawValue("");
    Ext.getCmp('strBuscarXApellidosChoferAV').value="";
    Ext.getCmp('strBuscarXApellidosChoferAV').setRawValue("");
    
    storeHistorialAV.getProxy().extraParams.identificacionChoferAV = Ext.getCmp('strBuscarXIdentificacionChoferAV').getValue();
    storeHistorialAV.getProxy().extraParams.nombresChoferAV        = Ext.getCmp('strBuscarXNombresChoferAV').getValue();
    storeHistorialAV.getProxy().extraParams.apellidosChoferAV      = Ext.getCmp('strBuscarXApellidosChoferAV').getValue();
    
    
    
    storeHistorialAV.load();
}

function validarFechasFiltro(cmp,tipoAsignacion)
{
    var strTipoAsignacion="";
    
    if(tipoAsignacion==1)
    {
        strTipoAsignacion="Asignaciones Vehiculares a Cuadrillas";
        var storeHistorialPrincipal=storeHistorialAV;
        var fieldFechaDesdeAsignacion=Ext.getCmp('fechaDesdeAsignacionAV');
        var valFechaDesdeAsignacion=fieldFechaDesdeAsignacion.getSubmitValue();

        var fieldFechaHastaAsignacion=Ext.getCmp('fechaHastaAsignacionAV');
        var valFechaHastaAsignacion=fieldFechaHastaAsignacion.getSubmitValue();
    }
    else
    {
        strTipoAsignacion="Asignaciones Provisionales de Choferes";
        var storeHistorialPrincipal=storeHistorialAP;
        var fieldFechaDesdeAsignacion=Ext.getCmp('fechaDesdeAsignacionAP');
        var valFechaDesdeAsignacion=fieldFechaDesdeAsignacion.getSubmitValue();

        var fieldFechaHastaAsignacion=Ext.getCmp('fechaHastaAsignacionAP');
        var valFechaHastaAsignacion=fieldFechaHastaAsignacion.getSubmitValue();
    }
    
    

    var boolOKFechas= true;
    var boolCamposLLenos=false;
    var strMensaje  = '';
    
    if(valFechaDesdeAsignacion && valFechaHastaAsignacion)
    {
        var valCompFechaDesdeAsignacion = Ext.Date.parse(valFechaDesdeAsignacion, "d/m/Y");
        var valCompFechaHastaAsignacion = Ext.Date.parse(valFechaHastaAsignacion, "d/m/Y");

        if ((isNaN(fieldFechaDesdeAsignacion.value) || isNaN(fieldFechaHastaAsignacion.value)) || 
            (fieldFechaDesdeAsignacion.value==="" || fieldFechaHastaAsignacion.value==="" ))
        {
            boolOKFechas=false;
            strMensaje= "Los campos de las fechas en las "+strTipoAsignacion+" no pueden estar vacías";
            Ext.Msg.alert('Atenci\xf3n ', strMensaje);
        }
        else if(valCompFechaDesdeAsignacion>valCompFechaHastaAsignacion)
        {
            boolOKFechas=false;
            strMensaje='La Fecha Desde '+ valFechaDesdeAsignacion +' no puede ser mayor a la Fecha Hasta '+valFechaHastaAsignacion+
                        " en las "+strTipoAsignacion;
            Ext.Msg.alert('Atenci\xf3n', strMensaje); 
        }
    }
    
    if(valFechaDesdeAsignacion && valFechaHastaAsignacion )
    {
        boolCamposLLenos=true;
    }


    if(boolOKFechas && boolCamposLLenos)
    {
        var objExtraParams = storeHistorialPrincipal.proxy.extraParams;
        objExtraParams.errorFechas              = 0;
        objExtraParams.fechaDesde  = valFechaDesdeAsignacion;
        objExtraParams.fechaHasta  = valFechaHastaAsignacion;

    }
    else if(!boolOKFechas )
    {
        cmp.value = "";
        cmp.setRawValue("");
        var objExtraParams = storeHistorialPrincipal.proxy.extraParams;
        objExtraParams.errorFechas=1;
        storeHistorialPrincipal.load();

    }
}