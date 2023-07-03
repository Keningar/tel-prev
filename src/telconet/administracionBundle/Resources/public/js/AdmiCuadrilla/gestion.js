var store = null;
var selModel = null;
var grid  = null;
var win   = null;
var boolEsOPU = strNombreDepartamento == 'Operaciones Urbanas' ? true : false;

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
         
    Ext.define('ModelStore', 
    {
        extend: 'Ext.data.Model',
        fields:
        [				
            {name:'intIdCuadrilla',                 mapping:'intIdCuadrilla'},
            {name:'strCodigo',                      mapping:'strCodigo'},
            {name:'strNombreCuadrilla',             mapping:'strNombreCuadrilla'},
            {name:'intIdZona',                      mapping:'intIdZona'},
            {name:'strEsHal',                       mapping:'strEsHal'},
            {name:'strZona',                        mapping:'strZona'},
            {name:'intIdTarea',                     mapping:'intIdTarea'},
            {name:'strTarea',                       mapping:'strTarea'},
            {name:'intIdDepartamento',              mapping:'intIdDepartamento'},
            {name:'strDepartamento',                mapping:'strDepartamento'},
            {name:'strEstado',                      mapping:'strEstado'},
            {name:'strFechaInicio',                 mapping:'strFechaInicio'},
            {name:'strFechaFin',                    mapping:'strFechaFin'},
            {name:'strTurnoInicio',                 mapping:'strTurnoInicio'},
            {name:'strTurnoFin',                    mapping:'strTurnoFin'},
            {name:'strUrlVer',                      mapping:'strUrlVer'},
            {name:'strUrlEditar',                   mapping:'strUrlEditar'},
            {name:'coordinadorPrincipalId',         mapping:'coordinadorPrincipalId'},
            {name:'coordinadorPrestadoId',          mapping:'coordinadorPrestadoId'},
            {name:'intIdDetAsignacionVehicular',    mapping:'intIdDetAsignacionVehicular'},
            {name:'strDISCOVehiculo',               mapping:'strDISCOVehiculo'},
            {name:'strActivoAsignado',              mapping:'strActivoAsignado'},
            {name:'intIdActivoAsignado',            mapping:'intIdActivoAsignado'},
            {name:'strTipoActivoAsignado',          mapping:'strTipoActivoAsignado'},
            {name:'strEstaLibre',                   mapping:'strEstaLibre'},
            {name:'strEsSatelite',                  mapping:'strEsSatelite'},
            {name:'boolDepConfigHE',                mapping:'boolDepConfigHE'}
        ],
        idProperty: 'intIdCuadrilla'
    });
	
    store = new Ext.data.Store
    ({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : strUrlGridCuadrillas,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'cuadrillas'
            },
            extraParams:
            {
                nombre: '',
                estado: '',
                esGestion: 'SI'
            }
        },
        autoLoad: true
    });
	
    var permiso      = $("#ROLE_170-8");
    var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	

    var strPermiso      = $("#ROLE_170-9");
    var boolPermiso2 = (typeof strPermiso === 'undefined') ? false : (strPermiso.val() == 1 ? true : false);	

    var definirComoHal = "";
    var definirComoSatelite = "";

    if(boolPermiso1 && boolPermiso2)
    {
        selModel = Ext.create('Ext.selection.CheckboxModel', 
        {
            renderer: function(value, metaData, record, rowIndex, colIndex, store, view)
            {
                if (record.get('strEstado') == 'Activo' || record.get('strEstado') == 'Prestado')
                {
                    return '<div class="'+Ext.baseCSSPrefix+'grid-row-checker">&#160;</div>';
                }
            }
        });

        definirComoHal = Ext.create('Ext.button.Button',
        {
            iconCls: 'icon_asignarHal',
            text: 'HAL',
            itemId: 'asignarAjax',
            scope: this,
            handler: function()
            {
                asignarHal();
            }
        });

        definirComoSatelite = Ext.create('Ext.button.Button',
            {
                iconCls: 'icon_asignarHal',
                text: 'Satélite',
                itemId: 'asignarSatelite',
                scope: this,
                handler: function()
                {
                    asignarSatelite();
                }
            });
    }

    var toolbar = Ext.create('Ext.toolbar.Toolbar', {});

    if (boolEsOPU)
    {
        toolbar = Ext.create('Ext.toolbar.Toolbar', 
        {
            dock: 'top',
            align: '->',
            items   : 
            [ 
                { xtype: 'tbfill' },
                    definirComoHal,
                    definirComoSatelite
            ]
        });
    }

    grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: 950,
        height: 400,
        store: store,
        selModel: selModel,
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

                                if( header.dataIndex != null && header.dataIndex != '' )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
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
        columns:
        [
            {
              id: 'intIdCuadrilla',
              header: 'intIdCuadrilla',
              dataIndex: 'intIdCuadrilla',
              hidden: true,
              hideable: false
            },
            {
              id: 'strCodigo',
              header: 'Código',
              dataIndex: 'strCodigo',
              width: 80,
              sortable: true
            },
            {
              id: 'strNombreCuadrilla',
              header: 'Nombre',
              dataIndex: 'strNombreCuadrilla',
              width: 150,
              sortable: true
            },
            {
              id: 'intIdZona',
              header: 'intIdZona',
              dataIndex: 'intIdZona',
              hidden: true,
              hideable: false
            },
            {
              id: 'strZona',
              header: 'Zona',
              dataIndex: 'strZona',
              width: 100,
              sortable: true
            },
            {
              id: 'strEsHal',
              header: 'HAL',
              dataIndex: 'strEsHal',
              width: 40,
              sortable: true,
              renderer  : function (value, metaData, record, rowIndex, colIndex, store) {
                    var esHal = record.data.strEsHal.toUpperCase();
                    if( esHal === "S") {
                        esHal = "<b style='color:green;'>SI</b>";
                    } else {
                        esHal = "NO";
                    }
                    return esHal;
              }
            },
            {
                        id: 'strEsSatelite',
                        header: 'SATELITE',
                        dataIndex: 'strEsSatelite',
                        width: 60,
                        sortable: true,
                        renderer  : function (value, metaData, record, rowIndex, colIndex, store) {
                            var esSatelite = record.data.strEsSatelite.toUpperCase();
                            if( esSatelite === "S") {
                                esSatelite = "<b style='color:green;'>SI</b>";
                            } else {
                                esSatelite = "NO";
                            }
                            return esSatelite;
                        }
                    },
                    {
              id: 'intIdTarea',
              header: 'intIdTarea',
              dataIndex: 'intIdTarea',
              hidden: true,
              hideable: false
            },
            {
              id: 'strTarea',
              header: 'Tarea',
              dataIndex: 'strTarea',
              width: 100,
              sortable: true
            },
            {
              id: 'intIdDepartamento',
              header: 'intIdDepartamento',
              dataIndex: 'intIdDepartamento',
              hidden: true,
              hideable: false
            },
            {
              id: 'strDepartamento',
              header: 'Departamento',
              dataIndex: 'strDepartamento',
              width: 140,
              sortable: true
            },
            {
              header: "<p style='text-align:center;line-height:15px;'>Disco<br>Vehículo Asignado</p>",
              dataIndex: 'strDISCOVehiculo',
              width: 100,
              sortable: true
            },
            {
              header: "<p style='text-align:center;line-height:15px;'>Placa<br>Vehículo Asignado</p>",
              dataIndex: 'strActivoAsignado',
              width: 100,
              sortable: true
            },
            {
              header: 'Estado',
              dataIndex: 'strEstado',
              width: 89,
              sortable: true
            },
            {
              header: 'Libre',
              dataIndex: 'strEstaLibre',
              width: 50,
              sortable: true
            },
            {
                header: "<p style='text-align:center;line-height:15px;'>Fecha Inicio</p>",
                dataIndex: 'strFechaInicio',
                width: 90,
                sortable: true
            },
            {
                header: "<p style='text-align:center;line-height:15px;'>Fecha Fin</p>",
                dataIndex: 'strFechaFin',
                width: 90,
                sortable: true
            },
            {
              header: 'Hora<br/>Inicio',
              dataIndex: 'strTurnoInicio',
              width: 50,
              sortable: true
            },
            {
              header: 'Hora<br/>Fin',
              dataIndex: 'strTurnoFin',
              width: 50,
              sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 200,
                items: 
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-show';

                            if(rec.get('strUrlVer') == "") 
                            {
                                strClassButton        = '';
                                this.items[0].tooltip = '';
                            }
                            else 
                            {
                                this.items[0].tooltip = 'Ver Cuadrilla';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Ver Cuadrilla',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec       = store.getAt(rowIndex);
                            var strUrlVer = rec.get('strUrlVer');

                            if(strUrlVer != "")
                            {
                                window.location = strUrlVer;
                            }
                        }
                    },

                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-edit';

                            if(rec.get('strUrlEditar') == "") 
                            {
                                strClassButton        = '';
                                this.items[1].tooltip = '';
                            }
                            else 
                            {
                                this.items[1].tooltip = 'Editar Cuadrilla';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Editar Cuadrilla',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec          = store.getAt(rowIndex);
                            var strUrlEditar = rec.get('strUrlEditar');

                            if(strUrlEditar != "")
                            {
                                window.location = strUrlEditar;
                            }
                        }
                    },

                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'btn-acciones btn-asignar-devolver-cuadrilla';

                            if( rec.get('strEstado') != "Prestado" ) 
                            {
                                strClassButton        = '';
                                this.items[2].tooltip = '';
                            }
                            else 
                            {
                                this.items[2].tooltip = 'Recuperar Cuadrilla';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Recuperar Cuadrilla',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            if(rec.get('strEstado') == "Prestado")
                            {
                                var arrayParametros                        = [];
                                    arrayParametros['cuadrillas']           = rec.get('intIdCuadrilla');
                                    arrayParametros['accion']              = 'recuperar';
                                    arrayParametros['coordinadorPrestado'] = '';

                                verificarPlanificacion(arrayParametros);
                            }
                        }
                    },

                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'btn-acciones btn-asignar-prestar-cuadrilla';

                            if( rec.get('strEstado') != "Activo" || !boolEsOPU) 
                            {
                                strClassButton        = '';
                                this.items[3].tooltip = '';
                            }
                            else 
                            {
                                this.items[3].tooltip = 'Prestar Cuadrilla';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Prestar Cuadrilla',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            if(rec.get('strEstado') == "Activo")
                            {
                                var arrayParametros              = [];
                                    arrayParametros['cuadrillas'] = rec.get('intIdCuadrilla');
                                    arrayParametros['fechaTurnoInicio'] = rec.get('strFechaInicio');
                                    arrayParametros['fechaTurnoFin'] = rec.get('strFechaFin');
                                    arrayParametros['horaTurnoInicio'] = rec.get('strTurnoInicio');
                                    arrayParametros['horaTurnoFin'] = rec.get('strTurnoFin');
                                    arrayParametros['boolDepConfigHE'] = rec.get('boolDepConfigHE');
                                    arrayParametros['accion']    = 'prestar';

                                prestarCuadrilla(arrayParametros);
                            }
                        }
                    },
                    
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'btn-acciones btn-asignar-vehiculo';
                            var permiso = $("#ROLE_170-3137");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                            if(!boolPermiso){ strClassButton = "icon-invisible"; }

                            if (strClassButton == "icon-invisible")
                            {
                                this.items[0].tooltip = ''; 
                            }   
                            else
                            {
                                if(rec.get('strEstado') != "Activo" || rec.get('intIdDetAsignacionVehicular')!='' && boolEsOPU) 
                                {
                                    strClassButton        = 'icon-invisible';
                                    this.items[0].tooltip = '';
                                }
                                else
                                {
                                    this.items[0].tooltip = 'Nueva Asignación de Vehículo';
                                    
                                }

                            }

                            return strClassButton;
                        },
                        tooltip: 'Nueva Asignación de Vehículo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
							var strClassButton = 'btn-acciones btn-asignar-vehiculo';		
                            var permiso = $("#ROLE_170-3137");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = "icon-invisible"; }

                            if(strClassButton!="icon-invisible")
                            {
                                if(rec.get('strEstado') == "Activo" && rec.get('intIdDetAsignacionVehicular')=='')
                                {

                                    rec = store.getAt(rowIndex);
                                    var arrayParametros                                 = [];
                                    arrayParametros['cuadrilla']                        = rec.get('intIdCuadrilla');
                                    arrayParametros['nombreCuadrilla']                  = rec.get('strNombreCuadrilla');
                                    arrayParametros['idZonaCuadrilla']                  = rec.get('intIdZona');
                                    arrayParametros['zonaCuadrilla']                    = rec.get('strZona');
                                    arrayParametros['idTareaCuadrilla']                 = rec.get('intIdTarea');
                                    arrayParametros['tareaCuadrilla']                   = rec.get('strTarea');
                                    arrayParametros['idDepartamentoCuadrilla']          = rec.get('intIdDepartamento');
                                    arrayParametros['departamentoCuadrilla']            = rec.get('strDepartamento');
                                    arrayParametros['strTurnoInicio']                   = rec.get('strTurnoInicio');
                                    arrayParametros['strTurnoFin']                      = rec.get('strTurnoFin');
                                    asignarMedioTransporte(arrayParametros);
                                }
                                
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                            }
                        }
                    },

                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'btn-acciones btn-eliminar-vehiculo';
                            var permiso = $("#ROLE_170-3597");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                            if(!boolPermiso){ strClassButton = "icon-invisible"; }
                            if (strClassButton == "icon-invisible")
                            {
                               this.items[1].tooltip = ''; 
                            }   
                            else
                            {
                                
                                if(rec.get('intIdDetAsignacionVehicular')!='' && boolEsOPU)
                                {
                                    this.items[1].tooltip = 'Eliminar Asignación de Vehículo';
                                }
                                else
                                {
                                    strClassButton        = 'icon-invisible';
                                    this.items[1].tooltip = '';
                                }
                            }
                            

                            return strClassButton;
                        },
                        tooltip: 'Eliminar Asignación de Vehículo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
							var strClassButton = 'btn-acciones btn-eliminar-vehiculo';		
                            var permiso = $("#ROLE_170-3597");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = "icon-invisible"; }

                            if(strClassButton!="icon-invisible")
                            {
                                if(rec.get('intIdDetAsignacionVehicular')!='')
                                {
                                    rec = store.getAt(rowIndex);
                                    var arrayParametros                                     = [];
                                        arrayParametros['elemento']                         = rec.get('intIdActivoAsignado');
                                        arrayParametros['placa']                            = rec.get('strActivoAsignado');
                                        arrayParametros['cuadrilla']                        = rec.get('intIdCuadrilla');
                                        arrayParametros['nombreCuadrilla']                  = rec.get('strNombreCuadrilla');
                                        arrayParametros['intIdActivoAsignado']              = rec.get('intIdActivoAsignado');
                                        arrayParametros['activoAsignado']                   = rec.get('strActivoAsignado');
                                        
                                        eliminarAsignacionVehicular(arrayParametros);
                                }
                                
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-liberarCuadrilla';
                            var permiso = $("#ROLE_170-4957");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "")
                            {
                                this.items[5].tooltip = '';
                            }   
                            else
                            {
                                if((rec.get('strEstado')=='Activo' || rec.get('strEstado')=='Prestado') 
                                    && rec.get('strEstaLibre')!='SI' && boolEsOPU)
                                {
                                    this.items[5].tooltip = 'Dejar Cuadrilla Libre';
                                }
                                else
                                {
                                    strClassButton          = "";
                                    this.items[5].tooltip   = '';
                                }

                            }
                            return strClassButton;
                        },
                        tooltip: 'Dejar Cuadrilla Libre',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var strClassButton = 'button-grid-liberarCuadrilla';
                            var permiso = $("#ROLE_170-4957");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton != "")
                            {
                                var rec = store.getAt(rowIndex);
                                if((rec.get('strEstado')=='Activo' || rec.get('strEstado')=='Prestado') && rec.get('strEstaLibre')!='SI')
                                {
                                    var arrayParametros = [];
                                    arrayParametros['intIdCuadrilla']                       = rec.get('intIdCuadrilla');
                                    arrayParametros['strNombreCuadrilla']                   = rec.get('strNombreCuadrilla');
                                    arrayParametros['strEstadoAccionARealizarCuadrilla']    = 'LIBRE';
                                    administrarCuadrillaLibre(arrayParametros);
                                    
                                }
                            }
                        }
                    },
                                 
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });

    var storeZonasFilter = new Ext.data.Store({
        total    : 'total',
        pageSize : 200,
        autoLoad : true,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    : strUrlZonas,
            reader: {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
        [
            { name:'idZona'     , mapping:'idZona'  },
            { name:'nombreZona' , mapping:'nombreZona' }
        ]
    });

    new Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7, 
        border:false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: 
        {
            background: '#fff'
        },   
        collapsible : true,
        collapsed: true,
        width: 950,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function()
                { 
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                { 
                    limpiar();
                }
            }
        ],                
        items: 
        [
            { width: '5%',border:false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                width: 340
            },
            { width: '15%',border:false},
            {
                xtype: 'combobox',
                fieldLabel: 'Zona:',
                id: 'cmbZona',
                name: 'cmbZona',
                store: storeZonasFilter,
                displayField: 'nombreZona',
                valueField: 'idZona',
                emptyText: 'Seleccione la Zona a consultar',
                forceSelection: true,
                width: 340
            },
            
        ],	

        renderTo: 'filtro'
    }); 

});

function asignarMedioTransporte(arrayParametros)
{
    if(arrayParametros['strTurnoInicio']!='' && arrayParametros['strTurnoFin']!='')
    {
        var boolHiddenZona=true;
        var boolHiddenTarea=true;
        
        if(arrayParametros['idZonaCuadrilla']!='')
        {
            boolHiddenZona=false;
        }
        if(arrayParametros['idTareaCuadrilla']!='')
        {
            boolHiddenTarea=false;
        }
        
        
        storeModelosMedioTransporte = new Ext.data.Store
            ({
                total: 'total',
                pageSize: 200,
                proxy:
                {
                    type: 'ajax',
                    method: 'post',
                    url: strUrlGetModelosMediosTransporte,
                    reader:
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                [
                    {name: 'strIdentificacion', mapping: 'strIdentificacion'},
                    {name: 'strDescripcion',    mapping: 'strDescripcion'}
                ],
                listeners: 
                {
                    load: function(store, records)
                    {
                         store.insert(0,[{ strIdentificacion: '', strDescripcion: 'Seleccione' }]);
                    }      
                }
            });

            storeMediosTransporte = new Ext.data.Store
            ({
                total: 'total',
                pageSize: 200,
                proxy:
                {
                    type: 'ajax',
                    method: 'post',
                    url: strUrlGetMediosTransporteDisponibles,
                    reader:
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams:
                    {
                        strTipoElemento: 'VEHICULO',
                        idZonaCuadrilla         : arrayParametros['idZonaCuadrilla'],
                        idTareaCuadrilla        : arrayParametros['idTareaCuadrilla'],
                        idDepartamentoCuadrilla : arrayParametros['idDepartamentoCuadrilla']
                    }
                },
                fields:
                [
                    {name: 'intIdDetalleSolicitud',     mapping: 'intIdDetalleSolicitud'},
                    {name: 'intIdElemento',             mapping: 'intIdElemento'},
                    {name: 'strNombreElemento',         mapping: 'strNombreElemento'}
                ],
                listeners: 
                {
                    load: function(store, records)
                    {
                         store.insert(0,[{ intIdDetalleSolicitud: '', strNombreElemento: 'Seleccione' }]);
                    }      
                }
            });

  

        var cmbModeloMedioTransporte = Ext.create('Ext.form.ComboBox',
        {
            id: 'cmbModeloMedioTransporte',
            name: 'cmbModeloMedioTransporte',
            fieldLabel: '<b>Modelo de Vehículo</b>',
            emptyText: "Seleccione",
            triggerAction: 'all',
            selectOnTab: true,
            store: storeModelosMedioTransporte,
            displayField: 'strDescripcion',
            valueField: 'strIdentificacion',             
            lazyRender: true,
            queryMode: "remote",
            listClass: 'x-combo-list-small',
            disabled: false,
            listeners:
            {
                select:
                {
                    fn:function(comp, record, index)
                    {
                        if (comp.getValue() === "" || comp.getRawValue() === "&nbsp;")
                        {
                            comp.setValue(null);

                            Ext.getCmp('cmbMedioTransporte').reset();
                            Ext.getCmp('cmbMedioTransporte').setDisabled(true);
                        }
                        else
                        {
                            Ext.getCmp('cmbMedioTransporte').reset();
                            Ext.getCmp('cmbMedioTransporte').setDisabled(false);
                            
                            Ext.getCmp('choferAsignacion').setValue("");

                            var objExtraParams = storeMediosTransporte.proxy.extraParams;

                            objExtraParams.strModeloElemento = comp.getValue();

                            objExtraParams.strHoraDesdeAsignacion    = arrayParametros["strTurnoInicio"];
                            objExtraParams.strHoraHastaAsignacion    = arrayParametros["strTurnoFin"];


                            storeMediosTransporte.removeAll();
                            storeMediosTransporte.load([], false);
                        }
                    }
                }
            }
        });
        
            
        var cmbMedioTransporte = Ext.create('Ext.form.ComboBox',
        {
            id: 'cmbMedioTransporte',
            name: 'cmbMedioTransporte',
            fieldLabel: '<b>Placa de Vehículo<b/>',
            emptyText: 'Seleccione',
            triggerAction: 'all',
            selectOnTab: true,
            store: storeMediosTransporte,
            displayField: 'strNombreElemento',
            valueField: 'intIdDetalleSolicitud',             
            lazyRender: true,
            queryMode: 'remote',
            listClass: 'x-combo-list-small',
            disabled: true,
            forceSelection:true,
            listeners:
            {
                keyup: function(form, e)
                {
                    convertirTextoEnMayusculas('cmbMedioTransporte-inputEl');
                },
                
                select:
                {
                    fn:function(comp, record, index)
                    {
                        if (comp.getValue() === "" || comp.getRawValue() === "&nbsp;")
                        {
                            Ext.getCmp('choferAsignacion').setValue("");
                            Ext.getCmp('choferAsignacion').getEl().toggle();
                            Ext.getCmp('choferAsignacion').getEl().hide();
                        }
                        else
                        {
                            Ext.Ajax.request
                            ({
                                url: strUrlGetChoferAsignacionPredefinida,
                                method: 'post',
                                params: 
                                { 
                                    idDetalleSolicitud      : comp.getValue(),
                                    idZonaCuadrilla         : arrayParametros['idZonaCuadrilla'],
                                    idTareaCuadrilla        : arrayParametros['idTareaCuadrilla'],
                                    idDepartamentoCuadrilla : arrayParametros['idDepartamentoCuadrilla']
                                },
                                success: function(response)
                                {
                                    var text = response.responseText;
                                    var respuestaChofer=text.split("-");
                                    if(respuestaChofer[0] === "OK")
                                    {
                                        Ext.getCmp('choferAsignacion').setValue(respuestaChofer[1]);
                                        Ext.getCmp('choferAsignacion').getEl().toggle();
                                        Ext.getCmp('choferAsignacion').getEl().show();
                                    }
                                    else
                                    {
                                        Ext.getCmp('choferAsignacion').setValue("");
                                        Ext.getCmp('choferAsignacion').getEl().toggle();
                                        Ext.getCmp('choferAsignacion').getEl().hide();
                                        
                                    }
                                }
                            });
                        }
                        
                    }
                }
            }
        });  
            
        var formPanel = Ext.create('Ext.form.Panel',
        {
            id: 'formAsignarMedioTransporte',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: 
            {
                labelAlign: 'left',
                labelWidth: 150,
                msgTarget: 'side'
            },
            items: 
            [
                {
                    xtype: 'fieldset',
                    title: '',
                    defaultType: 'textfield',
                    width: '100%',
                    items:
                    [
                        {
                            xtype: 'fieldset',
                            title: 'Datos de la cuadrilla',                       
                            width: '100%',
                            items: 
                            [
                                {
                                    xtype: 'displayfield',
                                    id: 'strNombreCuadrillaNuevo',
                                    name: 'strNombreCuadrillaNuevo',
                                    fieldLabel: '<b>Nombre</b>',
                                    value: arrayParametros['nombreCuadrilla']
                                },
                                {
                                    xtype: 'displayfield',
                                    id: 'strZonaCuadrillaNuevo',
                                    name:'strZonaCuadrillaNuevo',
                                    fieldLabel: '<b>Zona</b>',
                                    value:arrayParametros["zonaCuadrilla"],
                                    hidden: boolHiddenZona
                                },
                                {
                                    xtype: 'displayfield',
                                    id: 'strTareaCuadrillaNuevo',
                                    name:'strTareaCuadrillaNuevo',
                                    fieldLabel: '<b>Tarea</b>',
                                    value:arrayParametros["tareaCuadrilla"],
                                    hidden: boolHiddenTarea
                                },
                                {
                                    xtype: 'displayfield',
                                    id: 'strDepartamentoCuadrillaNuevo',
                                    name:'strDepartamentoCuadrillaNuevo',
                                    fieldLabel: '<b>Departamento</b>',
                                    value:arrayParametros["departamentoCuadrilla"]
                                },
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos de la Asignación Vehicular',                       
                            width: '100%',
                            items: 
                            [
                                {
                                    xtype: 'displayfield',
                                    id: 'horaInicioAsignacion',
                                    name:'horaInicioAsignacion',
                                    fieldLabel: '<b>Hora Inicio</b>',
                                    value:arrayParametros["strTurnoInicio"]
                                },
                                {
                                    xtype: 'displayfield',
                                    id: 'horaFinAsignacion',
                                    name:'horaFinAsignacion',
                                    fieldLabel: '<b>Hora Fin</b>',
                                    value:arrayParametros["strTurnoFin"]
                                },
                                cmbModeloMedioTransporte,
                                cmbMedioTransporte,
                                {
                                    xtype: 'displayfield',
                                    id: 'choferAsignacion',
                                    name:'choferAsignacion',
                                    fieldLabel: '<b>Chofer</b>',
                                    hidden:true,
                                    value:''
                                }
                            ]
                        }
                    ]
                }
            ],
            buttons:
            [
                {
                    text: 'Asignar',
                    type: 'submit',
                    handler: function()
                    {
                        var intIdDetalleSolicitud= Ext.getCmp('cmbMedioTransporte').getValue();
                        var form = Ext.getCmp('formAsignarMedioTransporte').getForm();
                        if( form.isValid() )
                        {
                            if ( intIdDetalleSolicitud != null && intIdDetalleSolicitud != "")
                            {
                                arrayParametros['idDetalleSolicitud']               = intIdDetalleSolicitud;
                                arrayParametros['placaMedioTransporteSeleccionado'] = Ext.getCmp('cmbMedioTransporte').getRawValue();

                                Ext.Msg.confirm('Alerta','Está seguro que desea asignar el vehículo seleccionado. Desea continuar?', function(btn)
                                {
                                    if(btn=='yes')
                                    {
                                        connEsperaAccion.request
                                        ({
                                            url: strUrlAsignarMedioTransporte,
                                            method: 'post',
                                            dataType: 'json',
                                            params:
                                            { 
                                                idCuadrilla             : arrayParametros['cuadrilla'],
                                                idZonaCuadrilla         : arrayParametros['idZonaCuadrilla'],
                                                idTareaCuadrilla        : arrayParametros['idTareaCuadrilla'],
                                                idDepartamentoCuadrilla : arrayParametros['idDepartamentoCuadrilla'],
                                                idDetalleSolicitud      : arrayParametros['idDetalleSolicitud'],
                                                strHoraDesdeAsignacion  : arrayParametros["strTurnoInicio"],
                                                strHoraHastaAsignacion  : arrayParametros["strTurnoFin"]
                                                
                                            },
                                            success: function(result)
                                            {
                                                var strResult = result.responseText;
                                                var strMensaje='';
                                                if ( typeof win != 'undefined' && win != null )
                                                {
                                                    win.destroy();
                                                }

                                                if( strResult=="OK" )
                                                {
                                                    strMensaje='Se asigna el vehículo con placa ';
                                                        strMensaje+=arrayParametros['placaMedioTransporteSeleccionado'];
                                                        strMensaje+=' a la cuadrilla ';
                                                        strMensaje+=arrayParametros['nombreCuadrilla'];
                                                    Ext.Msg.alert('Información ', strMensaje);
                                                    store.load();
                                                }
                                                else
                                                {
                                                    strMensaje+=strResult;
                                                    Ext.Msg.alert('Error ', strMensaje);

                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un vehículo');
                            }
                        } 

                    }
                },
                {
                    text: 'Cerrar',
                    handler: function()
                    {
                        win.destroy();
                    }
                }
            ]
        });

        win = Ext.create('Ext.window.Window',
              {
                title: 'Nueva Asignación de Vehículo',
                modal: true,
                width: 450,
                closable: true,
                layout: 'fit',
                floating: true,
                shadow: true,
                shadowOffset:20,
                items: [formPanel]
              }).show();



    }
    else
    {
        Ext.Msg.alert('Atenci\xf3n', 'Esta cuadrilla no tiene horario especificado');
    }
}


function prestarCuadrilla(arrayParametros)
{
    var storeCoordinadores = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetJefes,
                timeout: 9000000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'usuarios'
                },
                extraParams:
                {
                    strExceptoUsr: intIdPersonaEmpresaRol,
                    strFiltroCargo: strCargo,
                    strNombreArea: strNombreArea,
                    strAccion: 'prestamo_cuadrilla',
                    strEsGestion: 'SI',
                    intIdCuadrillaGestion: arrayParametros['cuadrillas']
                }
            },
            fields:
            [
                {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
                {name: 'strEmpleado',            mapping: 'strEmpleado'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0, 
                     [
                        {
                            strEmpleado: 'Seleccione',
                            intIdPersonaEmpresaRol: ''
                        }
                     ]);
                }      
            }
        });
        
          
          if(arrayParametros['boolDepConfigHE'])
          {
              arrayParametros['depConfig'] = 'S';
              var storeTipoHorario = new Ext.data.Store({
                  total: 'total',
                  pageSize: 200,
                  proxy: {
                      type: 'ajax',
                      method: 'post',
                      url: urlConsultarTipoHorario,
                      reader: {
                          type: 'json',
                          totalProperty: 'total',
                          root: 'encontrados'
                      },
                      extraParams: {
                          consulta: 'list_tipoHorarios'
                          }
                  },
                  fields:
                      [
                          {name: 'idTipoHorario', mapping: 'idTipoHorario'},
                          {name: 'nombreTipoHorario', mapping: 'nombreTipoHorario'}
                      ],
                  listeners: {
                          load: function(store, records)
                          {
                               var objTipoHorario = store.data;
                               for (let indice = 0; indice < store.getCount(); indice++) {
                                   if (objTipoHorario.items[indice].data.nombreTipoHorario == "LINEA BASE")
                                   {
                                      store.removeAt(indice, 1);
                                   }
                               }
                          }      
                      },
                  autoLoad: true
              });
              
              var storeDiaSemana = new Ext.data.Store ({
                  total: 'total',
                  pageSize: 200,
                  fields: [
                      {name: 'idDia', type: 'string', mapping: 'idDia'},
                      {name: 'nombreDia', type: 'string', mapping: 'nombreDia'},
                  ],
                  sorters: [{
                      property : 'idDia',
                      direction: 'ASC'
                  }],
                  proxy:
                  {
                      type: 'ajax',
                      method: 'post',
                      url: urlDiasSemana,
                      reader:
                      {
                          type: 'json',
                          totalProperty: 'total',
                          root: 'encontrados'
                      },
                  },
                  listeners: 
                  {
                      load: function(store, records)
                      {
                           store.insert(0, 
                           [
                              {
                                  nombreDia: 'Todos',
                                  idDia:     '',
                              }
                           ]);
                      }      
                  },
                  autoLoad: true
              });
          
              Ext.define('comboSelectedCount', {
                  alias: 'plugin.selectedCount',
                  init: function (combo) {
                      combo.on({
                          select: function (me, records) {
                              var store = combo.getStore(),
                                  diff = records.length != store.count,
                                  newAll = false,
                                  all = false,
                                  newRecords = [];
                              Ext.each(records, function (obj, i, recordsItself) {
                                  if (records[i].data.nombreDia === 'Todos') {
                                      allRecord = records[i];
                                      if (!combo.allSelected) {
                                          combo.select(store.getRange());
                                          combo.allSelected = true;
                                          all = true;
                                          newAll = true;
                                      } else {
                                          all = true;
                                      }
                                  } else {
                                      if (diff && !newAll)
                                          newRecords.push(records[i]);
                                  }
                              });
                              if (combo.allSelected && !all) {
                                  combo.clearValue();
                                  combo.allSelected = false;
                              } else  if (diff && !newAll) {
                                  combo.select(newRecords);
                                  combo.allSelected = false;
                              }
                          }
                      })
                  }
              });
              
              var formPanel1 = Ext.create('Ext.form.Panel',
              {
                  id: 'formCambiarEstadoCuadrilla',
                  bodyPadding: 2,
                  waitMsgTarget: true,
                  fieldDefaults: 
                  {
                      labelAlign: 'left',
                      labelWidth: 89,
                      msgTarget: 'side'
                  },
                  items: 
                  [
                      {
                          xtype: 'fieldset',
                          title: '',
                          defaultType: 'textfield',
                          defaults:
                          {
                              width: 300
                          },
                          items:
                          [
                              {
                                  xtype: 'combobox',
                                  fieldLabel: '<b>Coordinador*:</b>',
                                  id: 'comboCoordinador',
                                  name: 'comboCoordinador',
                                  store: storeCoordinadores,
                                  displayField: 'strEmpleado',
                                  valueField: 'intIdPersonaEmpresaRol',
                                  queryMode: 'remote',
                                  anchor: '75%',
                                  emptyText: 'Seleccione',
                                  forceSelection: true
                              },
                              {
                                  xtype: 'checkbox',
                                  fieldLabel : '¿Asignar mismo horario de cuadrilla?',
                                  id : 'checkAsignarAhora',
                                  name : 'checkAsignarAhora',
                                  anchor: '100%',
                                  checked: false,
                                  hidden: false,
                                  listeners: {
                                      change: function(field, newValue, oldValue, eOpts){
                                         
                                          if(newValue == true)
                                              {
                                              Ext.getCmp('strHoraInicio').disable();
                                              Ext.getCmp('strHoraFin').disable();
                                           
                                              Ext.getCmp('strHoraInicio').setRawValue(arrayParametros['horaTurnoInicio']);
                                              Ext.getCmp('strHoraFin').setRawValue(arrayParametros['horaTurnoFin']);
                                          }
                                          else
                                          {
                                              Ext.getCmp('strHoraInicio').enable(); 
                                              Ext.getCmp('strHoraFin').enable(); 
              
                                              Ext.getCmp('strHoraInicio').setRawValue("");
                                              Ext.getCmp('strHoraFin').setRawValue("");
                                          }
                                      }
                                  }
                              },
                              {
                                  xtype: 'datefield',
                                  id: 'strFechaInicio',
                                  name: 'strFechaInicio',
                                  fieldLabel: '<b>Fecha Inicio:</b>',
                                  editable: false,
                                  format: 'd-m-Y',
                                  value:'',
                                  emptyText: "Seleccione",
                                  //labelWidth: 70,
                                  queryMode: 'remote',
                                  anchor: '50%',
                                  //renderTo:'divFechaInicio',
                                  minValue:new Date(),
                                  listeners: {
                                      select: function(cmp, newValue, oldValue) {
                                          validarFechas(cmp);
                                      }
                                   
                                  },
                              },
                              {
                                  xtype: 'datefield',
                                  id: 'strFechaFin',
                                  name: 'strFechaFin',
                                  fieldLabel: '<b>Fecha Fin*:</b>',
                                  editable: false,
                                  format: 'd-m-Y',
                                  value:'',
                                  emptyText: "Seleccione",
                                  queryMode: 'remote',
                                  anchor: '50%',
                                  /*labelWidth: 70,
                                  renderTo:'divFechaFin',*/
                                  minValue:new Date(),
                                  listeners: {
                                      select: function(cmp, newValue, oldValue) {
                                          validarFechas(cmp);
                                      }
                                  }
                              },
                              {
                                  xtype: 'timefield',
                                  id: 'strHoraInicio',
                                  name:'strHoraInicio',
                                  fieldLabel: '<b>Hora Inicio*:</b>',
                                  editable: false,
                                  minValue: '00:00',
                                  maxValue: '24:00',
                                  format: 'H:i',
                                  value:'',
                                  emptyText: "Seleccione",
                                  increment: 15,
                                  queryMode: 'remote',
                                  forceSelection: true,
                                  anchor: '50%',
                                  listeners: {
                                      select: function(cmp, newValue, oldValue) {
                                          //validarHoras(cmp, arrayParametros);
                                      }
                          
                                  }
                              },
                              {
                                  xtype     : 'timefield',
                                  id        : 'strHoraFin',
                                  name      : 'strHoraFin',
                                  fieldLabel: '<b>Hora Fin*:</b>',
                                  editable  : false,
                                  minValue  : '00:00',
                                  maxValue  : '24:00',
                                  format    : 'H:i',
                                  emptyText : "Seleccione",
                                  increment : 15,
                                  queryMode : 'remote',
                                  anchor    : '50%',
                                  //labelWidth: 70,
                                  value     :'',
                                  //renderTo:'divHoraFinTurno',
                                  listeners : {
                                      select: function(cmp, newValue, oldValue) {
                                          //validarHoras(cmp, arrayParametros);
                                      }
                                  }
                              },
                              {
                                  displayField: 'nombreTipoHorario',
                                  valueField  : 'idTipoHorario',
                                  xtype       : 'combobox',
                                  editable    : false,
                                  fieldLabel  : '<b>Tipo horario*:</b>',
                                  id          : 'cmbTipoHorario1',
                                  anchor      : '50%',
                                  name        : 'cmbTipoHorario1',
                                  emptyText   : "Seleccione",
                                  store       : storeTipoHorario
                              },
                                  //combo dias de la semana labora cuadrilla
                              {
                                  xtype        : 'combobox',
                                  store        :  storeDiaSemana,
                                  id           : 'comboDiaSemana1',
                                  name         : 'comboDiaSemana1',
                                  displayField : 'nombreDia',
                                  valueField   : 'idDia',
                                  fieldLabel   : '<b> Dias Semana </b>',
                                  /*width        :  425,
                                  labelWidth   :  70,*/
                                  anchor       : '50%',
                                  queryMode    : "local",
                                  plugins      : ['selectedCount'],
                                  disabled     : false,
                                  editable     : false,
                                  emptyText    : "Seleccione",
                                  multiSelect  : true,
                                  displayTpl   : '<tpl for="."> {nombreDia} <tpl if="xindex < xcount">, </tpl> </tpl>',
                                  listConfig   : {
                                      itemTpl: '{nombreDia} <div class="uncheckedChkbox"></div>'
                                  },
          
                                  //renderTo: 'divComboDiaSemana'
                              }
          
                          ]
                      }
                  ],
                  buttons:
                  [
                      {
                          text: 'Asignar',
                          type: 'submit',
                          handler: function()
                          {
                              var form = Ext.getCmp('formCambiarEstadoCuadrilla').getForm();
          
                              if( form.isValid() )
                              {
                                  var intIdCoordinador = Ext.getCmp('comboCoordinador').getValue();
                                  var strFechaInicio   = Ext.getCmp('strFechaInicio').getValue();
                                  var strFechaFin      = Ext.getCmp('strFechaFin').getValue();
                                  var strHoraInicio    = Ext.getCmp('strHoraInicio').getValue();
                                  var strHoraFin       = Ext.getCmp('strHoraFin').getValue();
                                  var cmbTipoHorario1  = Ext.getCmp('cmbTipoHorario1').getValue();
                                  var comboDiaSemana1  = Ext.getCmp('comboDiaSemana1').getValue().filter(function(valor) {
                                    return valor !== '';
                                  });
                                  var boolErrorDatos  = false;
                                      
                                  if (comboDiaSemana1 == "" || comboDiaSemana1 == null)
                                  {
                                      boolErrorDatos = true;
                                      Ext.Msg.alert('Error', 'Debe seleccionar un dia de Semana');
                                  }
                                  if (cmbTipoHorario1 == "" || cmbTipoHorario1 == null)
                                  {
                                      boolErrorDatos = true;
                                      Ext.Msg.alert('Error', 'Debe seleccionar un Tipo Horario');
                                  }
                                  var checkBox = document.getElementById("checkAsignarAhora");
                                  if (checkBox.checked != true || tareaDepartamento != 'undefined'){
                                      if (strHoraInicio == "" || strHoraInicio == null)
                                      {
                                          boolErrorDatos = true;
                                          Ext.Msg.alert('Error', 'Debe seleccionar Hora de Inicio');
                                      }
                                      if (strHoraFin == "" || strHoraFin == null)
                                      {
                                          boolErrorDatos = true;
                                          Ext.Msg.alert('Error', 'Debe seleccionar Hora Fin');
                                      }
                                  }
                                  if (strFechaFin == "" || strFechaFin == null)
                                  {
                                      boolErrorDatos = true;
                                      Ext.Msg.alert('Error', 'Debe seleccionar Fecha Fin');
                                  }
                                  if (strFechaInicio == "" || strFechaInicio == null)
                                  {
                                      boolErrorDatos = true;
                                      Ext.Msg.alert('Error', 'Debe seleccionar Fecha Inicio');
                                  }
                                  if (intIdCoordinador == "" || intIdCoordinador == null)
                                  {
                                      boolErrorDatos = true;
                                      Ext.Msg.alert('Error', 'Debe seleccionar al Coordinador');
                                  }
                                  var formattedValueFechaInicio = Ext.Date.format(strFechaInicio, 'd-m-Y');
                                  var formattedValueFechaFin    = Ext.Date.format(strFechaFin, 'd-m-Y');
                                  var formattedValueHoraInicio = Ext.Date.format(strHoraInicio, 'H:i');
                                  var formattedValueHoraFin    = Ext.Date.format(strHoraFin, 'H:i');
          
          
                                  if ( !boolErrorDatos )
                                  {
                                      arrayParametros['coordinadorPrestado'] = intIdCoordinador;
                                      arrayParametros["strFechaInicio"]  = formattedValueFechaInicio;
                                      arrayParametros["strFechaFin"]     = formattedValueFechaFin;
                                      arrayParametros["strHoraInicio"]   = formattedValueHoraInicio;
                                      arrayParametros["strHoraFin"]      = formattedValueHoraFin;
                                      arrayParametros["cmbTipoHorario1"] = cmbTipoHorario1;
                                      arrayParametros["comboDiaSemana1"] = comboDiaSemana1;
                                      enviarTramaPaquete(arrayParametros);
                                      
                                  }
          
                              }
                          }
                      },
                      {
                          text: 'Cerrar',
                          handler: function()
                          {
                              win.destroy();
                          }
                      }
                  ]
              });
          
              win = Ext.create('Ext.window.Window',
                    {
                         title: '<p style= "font-size:12px;">Prestar Cuadrilla</p>',
                         modal: true,
                         width: 500,
                         closable: true,
                         layout: 'fit',
                         items: [formPanel1]
                    }).show();
          }
          else
          {
              var formPanel = Ext.create('Ext.form.Panel',
              {
                  id: 'formCambiarEstadoCuadrilla',
                  bodyPadding: 2,
                  waitMsgTarget: true,
                  fieldDefaults: 
                  {
                      labelAlign: 'left',
                      labelWidth: 85,
                      msgTarget: 'side'
                  },
                  items: 
                  [
                      {
                          xtype: 'fieldset',
                          title: '',
                          defaultType: 'textfield',
                          defaults:
                          {
                              width: 300
                          },
                          items:
                          [
                              {
                                  xtype: 'combobox',
                                  fieldLabel: 'Coordinador:',
                                  id: 'comboCoordinador',
                                  name: 'comboCoordinador',
                                  store: storeCoordinadores,
                                  displayField: 'strEmpleado',
                                  valueField: 'intIdPersonaEmpresaRol',
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
                          text: 'Asignar',
                          type: 'submit',
                          handler: function()
                          {
                              var form = Ext.getCmp('formCambiarEstadoCuadrilla').getForm();
      
                              if( form.isValid() )
                              {
                                  var intIdCoordinador = Ext.getCmp('comboCoordinador').getValue();
      
                                  if ( intIdCoordinador != null && intIdCoordinador != '' )
                                  {
                                      arrayParametros['coordinadorPrestado'] = intIdCoordinador;
                                      
                                      verificarIntegrantesCuadrilla(arrayParametros);
                                  }
                                  else
                                  {
                                      Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un Coordinador');
                                  }
                              }
                          }
                      },
                      {
                          text: 'Cerrar',
                          handler: function()
                          {
                              win.destroy();
                          }
                      }
                  ]
              });
      
              win = Ext.create('Ext.window.Window',
              {
                   title: '<p style= "font-size:12px;">Prestar Cuadrilla</p>',
                   modal: true,
                   width: 500,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
          }
}

function limpiar()
{
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('cmbZona').value = "";
    Ext.getCmp('cmbZona').setRawValue("");
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.idZona = Ext.getCmp('cmbZona').value;
    store.load();
}

function buscar()
{
    store.loadData([],false);
    store.currentPage = 1;

    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.idZona = Ext.getCmp('cmbZona').value;
    store.load();
}

function validarFechas(cmp)
    {
        var fieldFechaDesdeAsignacion = Ext.getCmp('strFechaInicio');
        var valueFechaDesdeAsignacion = fieldFechaDesdeAsignacion.getValue();
        var formattedValueFechaDesdeAsignacion = Ext.Date.format(valueFechaDesdeAsignacion, 'd-m-Y');

        var fieldFechaHastaAsignacion = Ext.getCmp('strFechaFin');
        var valueFechaHastaAsignacion = fieldFechaHastaAsignacion.getValue();
        var formattedValueFechaHastaAsignacion = Ext.Date.format(valueFechaHastaAsignacion, 'd-m-Y');
        var boolOKFechas = true;
        var boolCamposLLenos=false;
        var strMensaje  = '';

        if( valueFechaDesdeAsignacion && valueFechaHastaAsignacion)
        {
            boolCamposLLenos=true;
        }

        if(valueFechaDesdeAsignacion && valueFechaHastaAsignacion)
        {
            if(formattedValueFechaDesdeAsignacion>formattedValueFechaHastaAsignacion)
            {
                boolOKFechas=false;
                strMensaje='La Fecha Inicio '+ formattedValueHoraDesdeAsignacion +' no puede ser mayor a la Fecha Fin '+formattedValueHoraHastaAsignacion;
                Ext.Msg.alert('Atenci\xf3n', strMensaje);
            }
            else if (formattedValueFechaDesdeAsignacion == formattedValueFechaHastaAsignacion)
            {
                Ext.getCmp('comboDiaSemana1').disable();
                Ext.getCmp('comboDiaSemana1').setValue((valueFechaDesdeAsignacion.getDay()+1).toString());
            }
            else
            {
                habilitarCamposAgregar();

                Ext.getCmp('comboDiaSemana1').setValue(null);
            }
            
        }

        if( boolCamposLLenos && boolOKFechas)
        {
            return true;
        }
        else
        {
            if(cmp && boolCamposLLenos)
            {
                cmp.value = "";
                cmp.setRawValue("");
            }

            return false;
        }
    } 
