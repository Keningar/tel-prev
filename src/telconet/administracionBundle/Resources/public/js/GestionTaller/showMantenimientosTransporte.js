
var storeMantenimientosVehiculo=null;
Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{ 
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', 
    {
        extend: 'Ext.data.Model',
        fields:
        [	
            {name:'idMantenimientoElemento',        mapping:'idMantenimientoElemento'},
            {name:'tipoMantenimiento',              mapping:'tipoMantenimiento'},
            {name:'numeroOrdenTrabajoMantenimiento',mapping:'numeroOrdenTrabajo'},
            {name:'kmActual',                       mapping:'kmActual'},
            {name:'valorTotal',                     mapping:'valorTotal'},
            {name:'fechaInicio',                    mapping:'fechaInicio'},
            {name:'fechaFin',                       mapping:'fechaFin'},
            {name:'fechaCreacion',                  mapping:'fechaCreacion'},
            {name:'usrCreacion',                    mapping:'usrCreacion'},
            {name:'estado',                         mapping:'estado'}
        ],
        idProperty: 'idMantenimientoElemento'
    });
    
    storeMantenimientosTransporte = new Ext.data.Store
    ({ 
        pageSize: 5,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : strUrlGetMantenimientosTransporte,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                nombre: '',
                estado: ''
            }
        },
        autoLoad: true
    });
    
    

    var grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: '100%',
        height: 250,
        store: storeMantenimientosTransporte,
        plugins: 
        [
            {ptype : 'pagingselectpersist'}
        ],
        viewConfig: 
        {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
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
              id: 'tipoMantenimiento',
              header: 'Tipo Mantenimiento',
              dataIndex: 'tipoMantenimiento',
              width: 110,
              sortable: true
            },
            {
              id: 'numeroOrdenTrabajoMantenimiento',
              header: 'Número Orden Trabajo',
              dataIndex: 'numeroOrdenTrabajoMantenimiento',
              width: 130,
              sortable: true
            },
            
            {
              id: 'kmActual',
              header: 'kmActual',
              dataIndex: 'kmActual',
              width: 80,
              sortable: true
            },
            {
              id: 'fechaInicio',
              header: 'Fecha Inicio',
              dataIndex: 'fechaInicio',
              width: 80,
              sortable: true
            },
            {
              id: 'fechaFin',
              header: 'Fecha Fin',
              dataIndex: 'fechaFin',
              width: 80,
              sortable: true
            },
            {
              id: 'valorTotal',
              header: 'Valor Total',
              dataIndex: 'valorTotal',
              width: 80,
              sortable: true
            },
            {
              header: 'Estado',
              dataIndex: 'estado',
              width: 80,
              sortable: true
            },
            {
              id: 'fechaCreacion',
              header: 'Fecha Creación',
              dataIndex: 'fechaCreacion',
              width: 100,
              sortable: true
            },
            {
              id: 'usrCreacion',
              header: 'Usr. Creación',
              dataIndex: 'usrCreacion',
              width: 80,
              sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 100,
                items: 
                [
                    
                     {
						getClass: function(v, meta, rec) {	
							var actionClass = 'button-grid-show';
							if(!boolPermisoVerValoresCategoriasMantenimientoTransporte){ actionClass = "icon-invisible"; }
	
							if (actionClass == "icon-invisible") 
								this.items[0].tooltip = '';
							else 
								this.items[0].tooltip = 'Ver Detalle';
							
							return actionClass;
						},
						handler: function(grid, rowIndex, colIndex) {
							var actionClass = 'button-grid-show';
                            var rec = storeMantenimientosTransporte.getAt(rowIndex);
							if(!boolPermisoVerValoresCategoriasMantenimientoTransporte){ actionClass= "icon-invisible"; }
						
							if(actionClass !="icon-invisible")
							{
								verDetallesMantenimientoTransporte(rec.get('idMantenimientoElemento'));
							}
							else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                            }
															
						}
					},
                    {
						getClass: function(v, meta, rec) {	
							var actionClass = 'button-grid-pdf';
							if(!boolPermisoAdjuntosMantenimientosTransporte){ actionClass = "icon-invisible"; }
	
							if (actionClass == "icon-invisible") 
								this.items[1].tooltip = '';
							else 
								this.items[1].tooltip = 'Ver Adjuntos';
							
							return actionClass;
						},
						handler: function(grid, rowIndex, colIndex) {
							var actionClass = 'button-grid-pdf';
                            var rec = storeMantenimientosTransporte.getAt(rowIndex);
							if(!boolPermisoAdjuntosMantenimientosTransporte){ actionClass= "icon-invisible"; }
						
							if(actionClass !="icon-invisible")
							{
								var url_verAdjuntosMantenimientoTransporte="../"+rec.get('idMantenimientoElemento')+"/getAdjuntosMantenimientoTransporte";
                                verAdjuntosMantenimientoTransporte(url_verAdjuntosMantenimientoTransporte);
							}
							else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                            }
															
						}
					}
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: storeMantenimientosTransporte,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'bloqueListadoMantenimientos'
    });
});




function verDetallesMantenimientoTransporte(idMantenimientoTransporte)
{    
    Ext.define('ModelDetStore', 
    {
        extend: 'Ext.data.Model',
        fields:
        [	
            {name:'idDetMantenimientoElemento',         mapping:'idDetMantenimientoElemento'},
            {name:'nombreCategoriaMantenimiento',       mapping:'nombreCategoria'},
            {name:'valorTotalCategoria',                mapping:'valorTotalCategoria'},
            {name:'fechaCreacion',                      mapping:'fechaCreacion'},
            {name:'usrCreacion',                        mapping:'usrCreacion'}
            
        ],
        idProperty: 'idDetMantenimientoElemento'
    });
    
    storeDetsMantenimientosTransporte = new Ext.data.Store
    ({ 
        pageSize: 10,
        model: 'ModelDetStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : strUrlGetDetallesMantenimientosTransporte,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                idMantenimientoTransporte: idMantenimientoTransporte
            }
        },
        autoLoad: true
    });
    
    
    var gridDetsMantenimientosTransporte = Ext.create('Ext.grid.Panel', {
        id: 'gridDetsMantenimientosTransporte',
        store: storeDetsMantenimientosTransporte,
        timeout: 60000,
        dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items: [
                            { xtype: 'tbfill' }
                    ]}
        ],                  
        columns:[
                {
                    id: 'idDetMantenimientoElemento',
                    header: 'idDetMantenimientoElemento',
                    dataIndex: 'idDetMantenimientoElemento',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Categoría',
                    dataIndex: 'nombreCategoriaMantenimiento',
                    width: 250
                },
                {
                    header: 'Valor Total',
                    dataIndex: 'valorTotalCategoria',
                    width: 100
                },
                {
                    header: 'Creado por',
                    dataIndex: 'usrCreacion',
                    width: 80,
                    sortable: true
                },
                {
                    header: 'Fecha de Creación',
                    dataIndex: 'fechaCreacion',
                    width: 160,
                    sortable: true
                }
                
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeDetsMantenimientosTransporte,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
            })
        });
        
          
    winDetalleMantenimiento = Ext.create('Ext.window.Window', {
            title: 'Detalle del Mantenimiento del Transporte',
            modal: true,
            width: 600,
            height: 300,
            resizable: false,
            layout: 'fit',
            items: [gridDetsMantenimientosTransporte],
            buttonAlign: 'center'
    }).show(); 
    
}




function verAdjuntosMantenimientoTransporte(url)
{    
    Ext.define('ModelAdjuntosStore', 
    {
        extend: 'Ext.data.Model',
        fields:
        [	
            {name:'id', mapping:'id'},                                      
            {name:'ubicacionLogicaDocumento', mapping:'ubicacionLogicaDocumento'},
            {name:'tipoDocumentoGeneral', mapping:'tipoDocumentoGeneral'},
            {name:'feCreacion', mapping:'feCreacion'},
            {name:'feCaducidad', mapping:'feCaducidad'},
            {name:'usrCreacion', mapping:'usrCreacion'},
            {name:'linkVerDocumento', mapping: 'linkVerDocumento'}
            
        ],
        idProperty: 'idDetMantenimientoElemento'
    });
    
    storeAdjuntosMantenimientosTransporte = new Ext.data.Store
    ({ 
        pageSize: 10,
        model: 'ModelAdjuntosStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : url,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        autoLoad: true
    });
    
    
    var gridAdjuntosMantenimientosTransporte = Ext.create('Ext.grid.Panel', {
        id: 'gridDetsMantenimientosTransporte',
        store: storeAdjuntosMantenimientosTransporte,
        timeout: 60000,
        dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items: [
                            { xtype: 'tbfill' }
                    ]}
        ],                  
        columns:[
                {
                    id: 'id',
                    header: 'id',
                    dataIndex: 'id',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Archivo Digital',
                    dataIndex: 'ubicacionLogicaDocumento',
                    width: 200
                },             
                {
                    header: 'Fecha de Creación',
                    dataIndex: 'feCreacion',
                    width: 160,
                    sortable: true
                },
                {
                    header: 'Creado por',
                    dataIndex: 'usrCreacion',
                    width: 80,
                    sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 165,
                    items: 
                    [

                         {
                            getClass: function(v, meta, rec) {	
                                var actionClass = 'button-grid-show';
                                if(!boolPermisoAdjuntosMantenimientosTransporte){ actionClass = "icon-invisible"; }

                                if (actionClass == "icon-invisible") 
                                    this.items[0].tooltip = '';
                                else 
                                    this.items[0].tooltip = 'Ver';

                                return actionClass;
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var actionClass = 'button-grid-show';
                                var rec = storeAdjuntosMantenimientosTransporte.getAt(rowIndex);
                                if(!boolPermisoAdjuntosMantenimientosTransporte){ actionClass= "icon-invisible"; }

                                if(actionClass !="icon-invisible")
                                {
                                    verArchivoDigital(rec);
                                }
                                else
                                {
                                    Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                                }

                            }
                        }
                    ]
                }
                
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeAdjuntosMantenimientosTransporte,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
            })
        });
    
    winAdjuntosMantenimiento = Ext.create('Ext.window.Window', {
            title: 'Adjuntos del Mantenimiento del Transporte',
            modal: true,
            width: 600,
            height: 300,
            resizable: false,
            layout: 'fit',
            items: [gridAdjuntosMantenimientosTransporte],
            buttonAlign: 'center'
    }).show(); 
    
}


function verArchivoDigital(rec)
{
    var rutaFisica = rec.get('linkVerDocumento');
    var posicion = rutaFisica.indexOf('/public')
    window.open(rutaFisica.substring(posicion,rutaFisica.length));
}