var store = null;
var grid  = null;
var win   = null;
var storeModelosMedioTransporte=null;
var storeMediosTransporte=null;
var storeHistorial=null;
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
            {name:'strZona',                        mapping:'strZona'},
            {name:'strTarea',                       mapping:'strTarea'},
            {name:'strDepartamento',                mapping:'strDepartamento'},
            {name:'strEstado',                      mapping:'strEstado'},
            {name:'strTurnoInicio',                 mapping:'strTurnoInicio'},
            {name:'strTurnoFin',                    mapping:'strTurnoFin'},
            {name:'strActivoAsignado',              mapping:'strActivoAsignado'},
            
            {name:'strAsignacionFechaInicio',       mapping:'strAsignacionFechaInicio'},
            {name:'strAsignacionFechaFin',          mapping:'strAsignacionFechaFin'},
            {name:'strAsignacionHoraInicio',        mapping:'strAsignacionHoraInicio'},
            {name:'strAsignacionHoraFin',           mapping:'strAsignacionHoraFin'},
            
            {name:'strModeloAsignado',              mapping:'strModeloAsignado'},
            {name:'intIdDetAsignacionVehicular',    mapping:'intIdDetAsignacionVehicular'},
            {name:'intIdActivoAsignado',            mapping:'intIdActivoAsignado'},
            {name:'strTipoActivoAsignado',          mapping:'strTipoActivoAsignado'},
            {name:'intIdPersonaEmpresaRolChofer',   mapping:'intIdPersonaEmpresaRolChofer'},  
            {name:'intIdPersonaChofer',             mapping:'intIdPersonaChofer'},  
            {name:'strNombresChofer',               mapping:'strNombresChofer'},
            {name:'strApellidosChofer',             mapping:'strApellidosChofer'},
            {name:'strIdentificacionChofer',        mapping:'strIdentificacionChofer'}
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

    var pluginExpanded = true;
	

    grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: '100%',
        height: 400,
        store: store,
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
              id: 'intIdCuadrilla',
              header: 'intIdCuadrilla',
              dataIndex: 'intIdCuadrilla',
              hidden: true,
              hideable: false
            },
            {
              id: 'intIdDetAsignacionVehicular',
              header: 'intIdDetAsignacionVehicular',
              dataIndex: 'intIdDetAsignacionVehicular',
              hidden: true,
              hideable: false
            },
            {
              id: 'intIdPersonaEmpresaRolChofer',
              header: 'intIdPersonaEmpresaRolChofer',
              dataIndex: 'intIdPersonaEmpresaRolChofer',
              width:40,
              hidden: true,
              hideable: false
            }, 
            {
              id: 'intIdPersonaChofer',
              header: 'intIdPersonaChofer',
              dataIndex: 'intIdPersonaChofer',
              width:10,
              hidden: true,
              hideable: false
            },
            
            {
              id: 'strCodigo',
              header: 'Código',
              dataIndex: 'strCodigo',
              width: 80,
              sortable: true,
            },
            {
              id: 'strNombreCuadrilla',
              header: 'Nombre',
              dataIndex: 'strNombreCuadrilla',
              width: 150,
              sortable: true
            },
            {
              id: 'strDepartamento',
              header: 'Departamento',
              dataIndex: 'strDepartamento',
              width: 200,
              sortable: true
            },
            {
              id: 'strZona',
              header: 'Zona',
              dataIndex: 'strZona',
              width: 100,
              sortable: true
            },
            {
                id: 'strTarea',
                header: 'Tarea',
                dataIndex: 'strTarea',
                width: 100,
                sortable: true
            },
            {
              header: "<p style='text-align:center;'>Turno<br>Inicio</p>",
              dataIndex: 'strTurnoInicio',
              width: 50,
              sortable: true
            },
            {
              header: "<p style='text-align:center;'>Turno<br>Fin</p>",
              dataIndex: 'strTurnoFin',
              width: 50,
              sortable: true
            },
            {
              header: "<p style='text-align:center;'>Estado<br>Cuadrilla</p>",
              dataIndex: 'strEstado',
              width: 89,
              sortable: true
            },
            {
              header: "<p style='text-align:center;'>Vehículo<br>Asignado</p>",
              dataIndex: 'strActivoAsignado',
              width: 100,
              sortable: true
            },
            {
              header: 'Modelo',
              dataIndex: 'strModeloAsignado',
              hidden: true
            },
            {
              id: 'strApellidosChofer',
              header: "<p style='text-align:center;'>Apellidos<br/>Chofer Predefinido</p>",
              dataIndex: 'strApellidosChofer',
              width: 150,
              sortable: true
            },
            {
              id: 'strNombresChofer',
              header: "<p style='text-align:center;'>Nombres<br/>Chofer Predefinido</p>",
              dataIndex: 'strNombresChofer',
              width: 150,
              sortable: true
            },
            {
              header: "<p style='text-align:center;'>Fecha Inicio<br>Asignación</p>",
              dataIndex: 'strAsignacionFechaInicio',
              width: 75,
              sortable: true
            },
            {
              header: "<p style='text-align:center;'>Hora Inicio<br>Asignación</p>",
              dataIndex: 'strAsignacionHoraInicio',
              width: 75,
              sortable: true
            },
            {
              header: "<p style='text-align:center;'>Hora Fin<br>Asignación</p>",
              dataIndex: 'strAsignacionHoraFin',
              width: 75,
              sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 150,
                items: 
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'btn-acciones button-grid-show';
                            var permiso = $("#ROLE_340-3617");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                            if(!boolPermiso){ strClassButton = "icon-invisible"; }
                            
                            if (strClassButton == "icon-invisible")
                            {
                                this.items[0].tooltip = ''; 
                            }   
                            else
                            {
                                this.items[0].tooltip = 'Ver Historial de Asignaciones Por Cuadrilla';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Ver Historial de Asignaciones Por Cuadrilla',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
							var strClassButton = 'btn-acciones button-grid-show';		
                            var permiso = $("#ROLE_340-3617");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = "icon-invisible"; }

                            if(strClassButton!="icon-invisible")
                            {
                                var rec = store.getAt(rowIndex);
                                var arrayParametros                                 = [];
                                arrayParametros['cuadrilla']                        = rec.get('intIdCuadrilla');
                                arrayParametros['nombreCuadrilla']                  = rec.get('strNombreCuadrilla');
                                arrayParametros['activoAsignado']                   = rec.get('strActivoAsignado');
                                arrayParametros['strModeloAsignado']                = rec.get('strModeloAsignado');
                                arrayParametros['intIdDetAsignacionVehicular']      = rec.get('intIdDetAsignacionVehicular');
                                arrayParametros['intIdActivoAsignado']              = rec.get('intIdActivoAsignado');
                                arrayParametros['intIdPersonaEmpresaRolChofer']     = rec.get('intIdPersonaEmpresaRolChofer');
                                arrayParametros['intIdPersonaChofer']               = rec.get('intIdPersonaChofer');
                                arrayParametros['strNombresChofer']                 = rec.get('strNombresChofer');
                                arrayParametros['strApellidosChofer']               = rec.get('strApellidosChofer');
                                arrayParametros['strIdentificacionChofer']          = rec.get('strIdentificacionChofer');
                                arrayParametros['strTurnoInicio']                   = rec.get('strTurnoInicio');
                                arrayParametros['strTurnoFin']                      = rec.get('strTurnoFin');

                                verHistorialAsignacionVehicularXCuadrilla(arrayParametros);
                                
                            }
                            else
                            {
                                Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                            }
                        }
                    }
                    
                    
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
    
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    
    var storeDepartamentos = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlDepartamentosAsignacionVehicular,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'strValue',  mapping: 'strValue'},
            {name: 'strNombre', mapping: 'strNombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
        
        
    var filterPanel = Ext.create('Ext.panel.Panel', 
        {
            bodyPadding: 7, 
            border:false,
            buttonAlign: 'center',
            width:'100%',
            layout: 
            {
                type:'table',
                columns: 5,
                align: 'left'
            },
            bodyStyle: 
            {
                background: '#fff'
            },   
            collapsible : true,
            collapsed: true,
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
                {html:"&nbsp;",border:false,width:200},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Departamento de Cuadrilla:',
                    id: 'cmbDepartamento',
                    name: 'cmbDepartamento',
                    store: storeDepartamentos,
                    displayField: 'strNombre',
                    valueField: 'strValue',
                    queryMode: 'remote',
                    emptyText: 'Todos',
                    forceSelection: true
                },
                {html:"&nbsp;",border:false,width:250},
                {
                    xtype: 'textfield',
                    id: 'txtNombre',
                    fieldLabel: 'Nombre de Cuadrilla',
                    value: '',
                    width: '325'
                },
                
                {html:"&nbsp;",border:false,width:200},

                {html:"&nbsp;",border:false,width:200},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Estado de Cuadrilla',
                    id: 'cmbEstado',
                    name: 'cmbEstado',
                    value:'',
                    store: [
                        ['','Todos'],
                        ['Activo','Activo'],
                        ['Eliminado','Eliminado']
                    ],
                },
                {html:"&nbsp;",border:false,width:250},
                {html:"&nbsp;",border:false,width:200},
                {html:"&nbsp;",border:false,width:200},
                
                
                
                {html:"&nbsp;",border:false,width:200},
                {
                    xtype: 'textfield',
                    id: 'strBuscarXIdentificacionChoferAV',
                    fieldLabel: 'Identificación Chofer',
                    labelWidth:140,
                    value: ''
                },
                {html:"&nbsp;",border:false,width:250},
                {html:"&nbsp;",border:false,width:200},
                {html:"&nbsp;",border:false,width:200},
                
                
                {html:"&nbsp;",border:false,width:200},
                {
                    xtype: 'textfield',
                    id: 'strBuscarXNombresChoferAV',
                    fieldLabel: 'Nombres Chofer',
                    labelWidth:140,
                    value: ''
                },
                {html:"&nbsp;",border:false,width:250},
                {
                    xtype: 'textfield',
                    id: 'strBuscarXApellidosChoferAV',
                    fieldLabel: 'Apellidos Chofer',
                    labelWidth:110,
                    value: ''
                },
                {html:"&nbsp;",border:false,width:200},
                
                
                
            ],	
            renderTo: 'filtro'
        }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar()
{
    store.getProxy().extraParams.departamento = Ext.getCmp('cmbDepartamento').value;
    
    store.getProxy().extraParams.estado = Ext.getCmp('cmbEstado').value;
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.departamento   = Ext.getCmp('cmbDepartamento').value;
    
    store.getProxy().extraParams.nombresChofer   = Ext.getCmp('strBuscarXNombresChoferAV').value;
    store.getProxy().extraParams.apellidosChofer = Ext.getCmp('strBuscarXApellidosChoferAV').value;
    store.getProxy().extraParams.identificacionChofer = Ext.getCmp('strBuscarXIdentificacionChoferAV').value;
    
    
    store.load();
}


function limpiar()
{
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('strBuscarXNombresChoferAV').value           ="";
    Ext.getCmp('strBuscarXNombresChoferAV').setRawValue("");
    Ext.getCmp('strBuscarXApellidosChoferAV').value         ="";
    Ext.getCmp('strBuscarXApellidosChoferAV').setRawValue("");
    Ext.getCmp('strBuscarXIdentificacionChoferAV').value    ="";
    Ext.getCmp('strBuscarXIdentificacionChoferAV').setRawValue("");
    
    Ext.getCmp('cmbEstado').setValue("");
    Ext.getCmp('cmbEstado').setRawValue("Todos");
    
    Ext.getCmp('cmbDepartamento').setValue("");
    Ext.getCmp('cmbDepartamento').setRawValue("Todos");
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.nombre         = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.nombresChofer  = Ext.getCmp('strBuscarXNombresChoferAV').value;
    store.getProxy().extraParams.apellidosChofer= Ext.getCmp('strBuscarXApellidosChoferAV').value;
    store.getProxy().extraParams.identificacionChofer= Ext.getCmp('strBuscarXIdentificacionChoferAV').value;
    store.getProxy().extraParams.estado         = "";
    store.getProxy().extraParams.departamento   = "";
    store.load();
}



function limpiarCombosTransporte()
{
    storeModelosMedioTransporte.removeAll();
    storeMediosTransporte.removeAll();
    Ext.getCmp('cmbModeloMedioTransporte').reset();
    storeModelosMedioTransporte.load([], false);
    Ext.getCmp('cmbMedioTransporte').reset();
    Ext.getCmp('cmbMedioTransporte').setDisabled(true);
}



function convertirTextoEnMayusculas(idTexto)
{
    var strTexto      = document.getElementById(idTexto).value;
    var strMayusculas = strTexto.toUpperCase(); 
    
    document.getElementById(idTexto).value = strMayusculas;
}


function verHistorialAsignacionVehicularXCuadrilla(arrayParametros)
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
            labelWidth: 70,
            value: firstDay,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasFiltro(cmp);
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
        labelWidth: 70,
        value:lastDay,
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarFechasFiltro(cmp);
            }
        }
    });
    storeHistorial = new Ext.data.Store({ 
                    id:'verHistorialAsignacionVehicularStore',
                    total: 'total',
                    pageSize: 5,
                    autoLoad: true,
                    proxy: {
                            type: 'ajax',                
                            url: strUrlShowHistorialAsignacionVehicularXCuadrilla,
                            reader: {
                                type: 'json', 
                                totalProperty: 'total', 
                                root: 'encontrados'
                            },
                            extraParams:
                            {
                                idCuadrilla:arrayParametros['cuadrilla'],
                                fechaDesde: Ext.getCmp('fechaDesdeAsignacionAV').getSubmitValue(),
                                fechaHasta: Ext.getCmp('fechaHastaAsignacionAV').getSubmitValue(),
                                errorFechas: 0
                            }
                    },
                    fields:
                    [
                        {name:'strFechaInicioHisto',    mapping:'strFechaInicioHisto'},
                        {name:'strFechaFinHisto',       mapping:'strFechaFinHisto'},
                        {name:'strHoraInicioHisto',     mapping:'strHoraInicioHisto'},
                        {name:'strHoraFinHisto',        mapping:'strHoraFinHisto'},
                        {name:'strPlacaHisto',          mapping:'strPlacaHisto'},
                        {name:'strEstadoHisto',         mapping:'strEstadoHisto'}
                    ]
    });
    
    
      
    
    
        
    var filterPanelAsignacionVehicular = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 2,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 520,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() 
                {
                    buscarAsignacionVehicularXCuadrilla();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() 
                {
                    limpiarAsignacionVehicularXCuadrilla();
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
                        width: 200,
                        layout: 'form',
                        border: false,
                        labelWidth:50,
                        items: 
                        [
                            DTFechaDesdeAV

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

                        layout: 'form',
                        border: false,
                        labelWidth:50,
                        items: 
                        [
                            DTFechaHastaAV

                        ]
                    }
                ]
            }
        ]
    });
    
    var gridHistorial = Ext.create('Ext.grid.Panel', {
        id: 'gridHistorialAsignacionVehicular',
        store: storeHistorial,
        timeout: 60000,
        width: 520,
        columns:
            [
                {
                    header: 'Fecha Inicio',
                    dataIndex: 'strFechaInicioHisto',
                    width: 75
                },
                {
                    header: 'Fecha Fin',
                    dataIndex: 'strFechaFinHisto',
                    width: 75
                },
                {
                    header: 'Hora Inicio',
                    dataIndex: 'strHoraInicioHisto',
                    width: 75
                },
                {
                    header: 'Hora Fin',
                    dataIndex: 'strHoraFinHisto',
                    width: 75
                },
                {
                    header: 'Vehículo',
                    dataIndex: 'strPlacaHisto',
                    width: 100
                },
                {
                    header: 'Estado',
                    dataIndex: 'strEstadoHisto',
                    width: 100
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeHistorial,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
            })
        });
        var strTurno='';
        if(arrayParametros['strTurnoInicio']!='' && arrayParametros['strTurnoFin']!='' )
        {
            strTurno+=arrayParametros['strTurnoInicio'] +' - '+arrayParametros['strTurnoFin'];
        }
        var choferCuadrilla='';
        if(arrayParametros['intIdPersonaEmpresaRolChofer']!='')
        {
            choferCuadrilla+=arrayParametros['strNombresChofer']+' '+arrayParametros['strApellidosChofer'];
        }
        
        var formInfoHistorial = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        fieldDefaults: {
           labelAlign: 'left',
           msgTarget: 'side'
        },
        defaults: {
           margins: '0 0 10 0'
        },
        items: [
           {
               xtype: 'fieldset',
               title: '',
               defaultType: 'textfield',
               width: '100%',
               items:
               [
                    {
                       xtype: 'fieldset',
                       title: 'Datos Generales',                       
                       width: '100%',
                       items: 
                       [
                           {
                               xtype: 'displayfield',
                               id: 'strNombreCuadrillaHisto',
                               name: 'strNombreCuadrillaHisto',
                               fieldLabel: '<b>Nombre</b>',
                               value: arrayParametros['nombreCuadrilla'],
                               width: '100%'
                           },
                           {
                               xtype: 'displayfield',
                               id: 'strTurnoCuadrillaHisto',
                               name: 'strTurnoCuadrillaHisto',
                               fieldLabel: '<b>Turno</b>',
                               value: strTurno,
                               width: '100%'
                           },
                           {
                               xtype: 'displayfield',
                               id: 'strNombresChoferHisto',
                               name: 'strNombresChoferHisto',
                               fieldLabel: '<b>Chofer</b>',
                               value: choferCuadrilla,
                               width: '100%'
                           },
                           {
                               xtype: 'displayfield',
                               id: 'strVehiculoHisto',
                               name: 'strVehiculoHisto',
                               fieldLabel: '<b>Placa del Vehículo Actual</b>',
                               value: arrayParametros['activoAsignado'],
                               width: '100%'
                           }
                       ]
                   },
                   {
                       xtype: 'fieldset',
                       title: 'Historial de Asignaciones',                       
                       width: '100%',
                       items: 
                       [
                            filterPanelAsignacionVehicular,
                            gridHistorial
                       ]
                   }

               ]
           }
        ]
        });
        
        var win = Ext.create('Ext.window.Window', {
            title: 'Historial de Asignaciones Vehiculares',
            resizable: true,
            width: 600,
            modal: true,
            layout:{
                    type:'fit',
                    align:'stretch',
                    pack:'start'
            },
            floating: true,
            shadow: true,
            shadowOffset:20,
            items: [formInfoHistorial] 
        });
        win.show();
}




function buscarAsignacionVehicularXCuadrilla()
{
    if(Ext.ComponentQuery.query('textfield[name=fechaDesdeAsignacionAV]')[0].value!="" 
            || Ext.ComponentQuery.query('textfield[name=fechaHastaAsignacionAV]')[0].value!="")
    {
        storeHistorial.loadData([],false);
        storeHistorial.currentPage = 1;

        storeHistorial.getProxy().extraParams.strFechaDesdeAsignacion   = 
        Ext.ComponentQuery.query('textfield[name=fechaDesdeAsignacionAV]')[0].getSubmitValue();
        storeHistorial.getProxy().extraParams.strFechaHastaAsignacion          =
        Ext.ComponentQuery.query('textfield[name=fechaHastaAsignacionAV]')[0].getSubmitValue();
        storeHistorial.getProxy().extraParams.errorFechas        = 0;
        storeHistorial.load({params: {start: 0, limit: 5}});
    }
}    

function limpiarAsignacionVehicularXCuadrilla()
{
    var dateActual=new Date();
    var firstDay = new Date(dateActual.getFullYear(), dateActual.getMonth(), 1);
    var lastDay=new Date(dateActual.getFullYear(),dateActual.getMonth()+1,0);  

    Ext.getCmp('fechaDesdeAsignacionAV').setValue(firstDay);
    Ext.getCmp('fechaHastaAsignacionAV').setValue(lastDay);

    storeHistorial.loadData([],false);
    storeHistorial.currentPage  = 1;
    storeHistorial.getProxy().extraParams.errorFechas               = 0 ;
    storeHistorial.getProxy().extraParams.strFechaDesdeAsignacion   = Ext.getCmp('fechaDesdeAsignacionAV').getSubmitValue();
    storeHistorial.getProxy().extraParams.strFechaHastaAsignacion   = Ext.getCmp('fechaHastaAsignacionAV').getSubmitValue();
    
    storeHistorial.load();
    
}

function validarFechasFiltro(cmp)
{
    var fieldFechaDesdeAsignacion=Ext.getCmp('fechaDesdeAsignacionAV');
    var valFechaDesdeAsignacion=fieldFechaDesdeAsignacion.getSubmitValue();

    var fieldFechaHastaAsignacion=Ext.getCmp('fechaHastaAsignacionAV');
    var valFechaHastaAsignacion=fieldFechaHastaAsignacion.getSubmitValue();
    

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
            Ext.Msg.alert('Atenci\xf3n ', 'Los campos de las fechas no pueden estar vacías');
        }
        else if(valCompFechaDesdeAsignacion>valCompFechaHastaAsignacion)
        {
            boolOKFechas=false;
            strMensaje='La Fecha Desde '+ valFechaDesdeAsignacion +' no puede ser mayor a la Fecha Hasta '+valFechaHastaAsignacion;
            Ext.Msg.alert('Atenci\xf3n', strMensaje); 
        }
    }
    
    if(valFechaDesdeAsignacion && valFechaHastaAsignacion )
    {
        boolCamposLLenos=true;
    }


    if(boolOKFechas && boolCamposLLenos)
    {
        var objExtraParams = storeHistorial.proxy.extraParams;
        objExtraParams.errorFechas              = 0;
        objExtraParams.strFechaDesdeAsignacion  = valFechaDesdeAsignacion;
        objExtraParams.strFechaHastaAsignacion  = valFechaHastaAsignacion;

    }
    else if(!boolOKFechas )
    {
        cmp.value = "";
        cmp.setRawValue("");
        var objExtraParams = storeHistorial.proxy.extraParams;
        objExtraParams.errorFechas=1;
        storeHistorial.load();

    }
}