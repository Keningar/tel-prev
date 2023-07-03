var store = null;
var grid  = null;
var win   = null;

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
    Ext.tip.QuickTipManager.init();
    var conn = new Ext.data.Connection({
      listeners: {
          'beforerequest': {
              fn: function (con, opt) {
                  Ext.get(document.body).mask('Loading...');
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
   
    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
              winOrdenesTrabajo.destroy();													
            }
    });
    
	storeOrdenesTrabajo = new Ext.data.Store({
        pageSize: 5,
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : strUrlGetOrdenesTrabajoVehiculo,
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
		},
		fields:
		[
            {name:'idOrdenTrabajo', mapping:'idOrdenTrabajo'},
            {name:'numeroOrdenTrabajo', mapping:'numeroOrdenTrabajo'},
            {name:'kilometraje', mapping:'kilometraje'},
            {name:'tipoMantenimiento', mapping:'tipoMantenimiento'},
            {name:'idDocumentoRelacionOT', mapping:'idDocumentoRelacion'},
            {name:'idDocumentoOT', mapping:'idDocumentoOT'},
            {name:'ubicacionLogicaDocOT', mapping:'ubicacionLogicaDocumento'},
            {name:'ubicacionFisicaDocOT', mapping:'ubicacionFisicaDocumento'},
            {name:'feInicioOT', mapping:'feInicio'},
            {name:'feFinOT', mapping:'feFin'},
            {name:'feCreacionOT', mapping:'feCreacion'},
            {name:'usrCreacion', mapping:'usrCreacion'},
            {name:'linkVerDocOT', mapping: 'linkVerDocumento'}			
		]
	});
    
    var toolbar = Ext.create('Ext.toolbar.Toolbar', 
    {
        dock: 'top',
        align: '->',
        items   : 
        [ 
            { xtype: 'tbfill' }
        ]
    });
    
    gridOrdenesTrabajo = Ext.create('Ext.grid.Panel', {
		id:'gridOrdenesTrabajo',
		store: storeOrdenesTrabajo,
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
        dockedItems: [ toolbar ], 
		columnLines: true,
        width: 1200,
		height: 250,
		columns: [
            {
                id: 'idOrdenTrabajo',
                header: 'idOrdenTrabajo',
                dataIndex: 'idOrdenTrabajo',
                hidden: true,
                hideable: false
            },
            {
                id: 'numeroOrdenTrabajo',
                header: 'Número Orden Trabajo',
                dataIndex: 'numeroOrdenTrabajo',
                width:120,
                hideable: false
            },
            {
                id: 'tipoMantenimiento',
                header: 'Tipo Mantenimiento',
                dataIndex: 'tipoMantenimiento',
                width:120,
                sortable: true						 
            },
            {
                id: 'kilometraje',
                header: 'Kilometraje',
                dataIndex: 'kilometraje',
                width:100,
                sortable: true						 
            },
            {
                id: 'ubicacionLogicaDocOT',
                header: 'Archivo',
                dataIndex: 'ubicacionLogicaDocOT',
                width:290,
                sortable: true						 
            },
            {
                id: 'feInicioOT',
                header: 'Fecha Inicio',
                dataIndex: 'feInicioOT',
                width:120,
                sortable: true						 
            },
            {
                id: 'feFinOT',
                header: 'Fecha Fin',
                dataIndex: 'feFinOT',
                width:120,
                sortable: true						 
            },
            {
                id: 'usrCreacion',
                header: 'usr Creación',
                dataIndex: 'usrCreacion',
                width:100,
                sortable: true						 
            },
            {
                id: 'feCreacionOT',
                header: 'Fecha Creación',
                dataIndex: 'feCreacionOT',
                width:120,
                sortable: true						 
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 100,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-pdf';
                            if (!boolPermisoVerOrdenTrabajo)
                            {                               
                                strClassButton = '';
                            }
                            return strClassButton;
                        },
                        tooltip: 'Ver Orden de Trabajo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec         = storeOrdenesTrabajo.getAt(rowIndex);
                            verArchivoDigital(rec);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            
                            var strClassButton = 'button-grid-agregarMantenimiento';
                                                        
                            if (!boolPermisoNuevoMantenimiento)
                            {                               
                                strClassButton = '';
                            }
                            return strClassButton;
                            
                        },
                        tooltip: 'Nuevo Mantenimiento',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = storeOrdenesTrabajo.getAt(rowIndex);
                            window.location = "../" + rec.get('idOrdenTrabajo') + "/newMantenimientoTransporte";
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-verMantenimientosTransporte';
                                                        
                            if (!boolPermisoVerMantenimientosXOrdenTrabajo)
                            {                               
                                strClassButton = '';
                            }
                            return strClassButton;
                            
                        },
                        tooltip: 'Ver Mantenimientos Por Orden de Trabajo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = storeOrdenesTrabajo.getAt(rowIndex);
                            window.location = "../" + rec.get('idOrdenTrabajo') + "/showMantenimientosXOrdenTrabajoTransporte";
                        }
                    }
                ]
            }
            
        ],
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: storeOrdenesTrabajo,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid_ordenes_trabajo_vehiculo'
		
	});
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
function buscar()
{
    var cmbModeloMedioTransporte = Ext.getCmp('cmbModeloMedioTransporte').value;
    
    if( cmbModeloMedioTransporte == "Todos" )
    {
        cmbModeloMedioTransporte = "";
    }
    
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.placa                 = Ext.getCmp('strPlaca').value;
    store.getProxy().extraParams.chasis                = Ext.getCmp('strNumChasis').value;
    store.getProxy().extraParams.disco                 = Ext.getCmp('strNumDisco').value;
    store.getProxy().extraParams.motor                 = Ext.getCmp('strNumMotor').value;
    store.getProxy().extraParams.modeloMedioTransporte = cmbModeloMedioTransporte;
    
    store.load();
}


function limpiar()
{
    Ext.getCmp('strPlaca').value="";
    Ext.getCmp('strPlaca').setRawValue("");
    
    Ext.getCmp('strNumMotor').value="";
    Ext.getCmp('strNumMotor').setRawValue("");
    
    Ext.getCmp('strNumChasis').value="";
    Ext.getCmp('strNumChasis').setRawValue("");
    
    Ext.getCmp('strNumDisco').value="";
    Ext.getCmp('strNumDisco').setRawValue("");
    
    Ext.getCmp('cmbModeloMedioTransporte').value = null;
    Ext.getCmp('cmbModeloMedioTransporte').setRawValue(null);
    
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.placa                  = Ext.getCmp('strPlaca').value;
    store.getProxy().extraParams.motor                  = Ext.getCmp('strNumMotor').value;
    store.getProxy().extraParams.chasis                 = Ext.getCmp('strNumChasis').value;
    store.getProxy().extraParams.disco                  = Ext.getCmp('strNumDisco').value;
    store.getProxy().extraParams.modeloMedioTransporte  = Ext.getCmp('cmbModeloMedioTransporte').value;
    store.load();
}

function verArchivoDigital(rec)
{
    var rutaFisica = rec.get('ubicacionFisicaDocOT');
    var posicion = rutaFisica.indexOf('/public');
    window.open(rutaFisica.substring(posicion,rutaFisica.length));
}

function convertirTextoEnMayusculas(idTexto)
{
    var strTexto      = document.getElementById(idTexto).value;
    var strMayusculas = strTexto.toUpperCase(); 
    
    document.getElementById(idTexto).value = strMayusculas;
}