var store = null;
var grid  = null;
var win   = null;
var winMotivo   = null;
var storeChoferes=null;
var storeZonas=null;
var storeTareas=null;
var storeDepartamentos=null;
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
            {name: 'id', type:'string', convert: function(value,record) 
                {
                    return record.get('intIdElemento') + '-' + record.get('intIdDetalleSolicitudPredefinida');
                }
            },
            {name:'intIdElemento',                          mapping:'intIdElemento'},
            {name:'strNombreElemento',                      mapping:'strNombreElemento'},
            {name:'strNombreModeloElemento',                mapping:'strNombreModeloElemento'},
            {name:'strDISCO',                               mapping:'strDISCO'},
            {name:'strFechaDesdeAsignacionPredefinida',     mapping:'strFechaDesdeAsignacionPredefinida'},

            {name:'intIdDetalleSolicitudPredefinida',       mapping:'intIdDetalleSolicitudPredefinida'},

            {name:'strFechaDesdeAsignacionPredefinida',     mapping:'strFechaDesdeAsignacionPredefinida'},
            
            {name:'strHoraDesdeAsignacionPredefinida',      mapping:'strHoraDesdeAsignacionPredefinida'},
            {name:'strHoraHastaAsignacionPredefinida',      mapping:'strHoraHastaAsignacionPredefinida'},
            
            {name:'intIdZonaPredefinida',                   mapping:'intIdZonaPredefinida'},
            {name:'strZonaPredefinida',                     mapping:'strZonaPredefinida'},
            
            {name:'intIdTareaPredefinida',                  mapping:'intIdTareaPredefinida'},
            {name:'strTareaPredefinida',                    mapping:'strTareaPredefinida'},

            {name:'intIdDepartamentoPredefinido',           mapping:'intIdDepartamentoPredefinido'},
            {name:'strDepartamentoPredefinido',             mapping:'strDepartamentoPredefinido'},

            {name:'intIdPerChoferPredefinido',              mapping:'intIdPerChoferPredefinido'},
            {name:'intIdPersonaChoferPredefinido',          mapping:'intIdPersonaChoferPredefinido'},
            {name:'strIdentificacionChoferPredefinido',     mapping:'strIdentificacionChoferPredefinido'},
            {name:'strNombresChoferPredefinido',            mapping:'strNombresChoferPredefinido'},
            {name:'strApellidosChoferPredefinido',          mapping:'strApellidosChoferPredefinido'}
        ],
        idProperty: 'id'
    });
	
    store = new Ext.data.Store
    ({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : strUrlGridAsignacionVehicularPredefinida,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        autoLoad: true
    });

	


    grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: '100%',
        height: 410,
        store: store,
        plugins: [{ ptype : 'pagingselectpersist' }],
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
        dockedItems: 
        [ 
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: 
                [                    
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_exportar_pdf',
                        text: 'Exportar',
                        scope: this,
                        handler: function()
                        {
                            var permiso = $("#ROLE_342-7");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso)
                            { 
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                            else
                            {
                                var idModeloMedioTransporte = Ext.getCmp('cmbModeloMedioTransporte').value;
    
                                if( idModeloMedioTransporte == "Todos" )
                                {
                                    idModeloMedioTransporte = "";
                                }

                                var idHorarioPredefinido    = Ext.getCmp('cmbHorarioAsignacionPredefinida').value;

                                if( idHorarioPredefinido == "Todos" )
                                {
                                    idHorarioPredefinido    = "";
                                }
                                document.getElementById("idModeloMedioTransporte").value= idModeloMedioTransporte;
                                document.getElementById("idHorarioPredefinido").value   = idHorarioPredefinido;
                                document.getElementById("formAsignacionesPredefinidas").submit();
                            }
                        }
                    }
                ]
            }
        ],
        columns:
        [
            {
                id: 'intIdElemento',
                header: 'intIdElemento',
                dataIndex: 'intIdElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'strDISCO',
                header: 'Disco',
                dataIndex: 'strDISCO',
                width: 60,
                sortable: true
            },
            {
                id: 'strNombreElemento',
                header: 'Placa',
                dataIndex: 'strNombreElemento',
                width: 100,
                sortable: true
            },
            {
                id: 'strNombreModeloElemento',
                header: 'Modelo Elemento',
                dataIndex: 'strNombreModeloElemento',
                width: 210,
                sortable: true
            },
            {
                id: 'intIdDetalleSolicitudPredefinida',
                header: 'intIdDetalleSolicitudPredefinida',
                dataIndex: 'intIdDetalleSolicitudPredefinida',
                hidden: true,
                hideable: false
            },
            {
                id: 'intIdPerChoferPredefinido',
                header: 'intIdPerChoferPredefinido',
                dataIndex: 'intIdPerChoferPredefinido',
                hidden: true,
                hideable: false
            },
            {
                id: 'intIdPersonaChoferPredefinido',
                header: 'intIdPersonaChoferPredefinido',
                dataIndex: 'intIdPersonaChoferPredefinido',
                hidden: true,
                hideable: false
            },
            {
                id: 'strFechaDesdeAsignacionPredefinida',
                header: "<p style='text-align:center;line-height:15px;'>Fecha<br>Asignación Predefinida</p>",
                dataIndex: 'strFechaDesdeAsignacionPredefinida',
                width: 150,
                sortable: true
            },
            {
                id: 'strHoraDesdeAsignacionPredefinida',
                header: "<p style='text-align:center;line-height:15px;'>Desde</p>",
                dataIndex: 'strHoraDesdeAsignacionPredefinida',
                width: 80 
            },
            {
                id: 'strHoraHastaAsignacionPredefinida',
                header: "<p style='text-align:center;line-height:15px;'>Hasta</p>",
                dataIndex: 'strHoraHastaAsignacionPredefinida',
                width: 80 
            },
            {
                id: 'strZonaPredefinida',
                header: 'Zona Predefinida',
                dataIndex: 'strZonaPredefinida',
                width: 200,
                sortable: true
            },
            {
                id: 'strTareaPredefinida',
                header: 'Tarea Predefinida',
                dataIndex: 'strTareaPredefinida',
                width: 100,
                sortable: true
            },
            {
                id: 'strDepartamentoPredefinido',
                header: 'Departamento Predefinido',
                dataIndex: 'strDepartamentoPredefinido',
                width: 200,
                sortable: true
            },
            
            {
                id: 'strApellidosChoferPredefinido',
                header: "<p style='text-align:center;line-height:15px;'>Apellidos<br>Chofer Predefinido</p>",
                dataIndex: 'strApellidosChoferPredefinido',
                width: 200,
                sortable: true
            },
            {
                id: 'strNombresChoferPredefinido',
                header: "<p style='text-align:center;line-height:15px;'>Nombres<br>Chofer Predefinido</p>",
                dataIndex: 'strNombresChoferPredefinido',
                width: 200,
                sortable: true
            },
            {
                id: 'strIdentificacionChoferPredefinido',
                header: "<p style='text-align:center;line-height:15px;'>Identificación<br>Chofer Predefinido</p>",
                dataIndex: 'strIdentificacionChoferPredefinido',
                width: 200,
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
                            var strClassButton = 'btn-asignar-chofer';
                            var permiso = $("#ROLE_342-3777");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }
                            
                            this.items[0].tooltip = 'Crear Asignacion Vehicular Predefinida';

                            if (strClassButton == "")
                            {
                                this.items[0].tooltip = ''; 
                            }   
                            else
                            {
                                this.items[0].tooltip = 'Nueva Asignacion Vehicular Predefinida';
                            }
                            return strClassButton;

                        },
                        tooltip: 'Nueva Asignacion Vehicular Predefinida',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var strClassButton = 'btn-asignar-chofer';
                            var permiso = $("#ROLE_342-3777");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton != "")
                            {
                                
                                    var arrayParametros             = [];
                                    arrayParametros['idElemento']   = rec.get('intIdElemento');
                                    arrayParametros['placa']        = rec.get('strNombreElemento');
                                    arrayParametros['modelo']       = rec.get('strNombreModeloElemento');
                                    arrayParametros['disco']        = rec.get('strDISCO');
                                    
                                    nuevaAsignacionVehicularPredefinida(arrayParametros);
                            } 
                        }
                    },                    
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'button-grid-edit';
                            var permiso = $("#ROLE_342-3797");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "")
                            {
                               this.items[1].tooltip = ''; 
                            }   
                            else
                            {
                                if(rec.get('intIdDetalleSolicitudPredefinida') == "") 
                                {
                                    strClassButton        = '';
                                    this.items[1].tooltip = '';
                                }
                                else 
                                {
                                    this.items[1].tooltip = 'Cambio de Chofer Predefinido'; 
                                }
                            }
                            return strClassButton;

                        },
                        tooltip: 'Cambio de Chofer Predefinido',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var strClassButton = 'button-grid-edit';
                            var permiso = $("#ROLE_342-3797");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }
                            if (strClassButton != "")
                            {
                                if(rec.get('intIdDetalleSolicitudPredefinida') != "") 
                                {
                                    var arrayParametros                 = [];
                                    arrayParametros['idElemento']       = rec.get('intIdElemento');
                                    arrayParametros['placa']            = rec.get('strNombreElemento');
                                    arrayParametros['modelo']           = rec.get('strNombreModeloElemento');
                                    arrayParametros['disco']            = rec.get('strDISCO');
                                    arrayParametros['detalleSolicitud'] = rec.get('intIdDetalleSolicitudPredefinida');
                                    
                                    arrayParametros['idZonaPredefinida']            = rec.get('intIdZonaPredefinida');
                                    arrayParametros['idTareaPredefinida']           = rec.get('intIdTareaPredefinida');
                                    
                                    if(arrayParametros['idZonaPredefinida']!='')
                                    {
                                        arrayParametros['tipoAsignacion']='ZONA';   
                                    }
                                    else
                                    {
                                        arrayParametros['tipoAsignacion']='TAREA';
                                    }
                                    arrayParametros['zonaPredefinida']                  = rec.get('strZonaPredefinida');
                                    arrayParametros['tareaPredefinida']                 = rec.get('strTareaPredefinida');
                                    arrayParametros['idDepartamentoPredefinido']        = rec.get('intIdDepartamentoPredefinido');
                                    arrayParametros['departamentoPredefinido']          = rec.get('strDepartamentoPredefinido');
                                    arrayParametros['nombresChoferPredefinido']         = rec.get('strNombresChoferPredefinido');
                                    arrayParametros['apellidosChoferPredefinido']       = rec.get('strApellidosChoferPredefinido');
                                    arrayParametros['horaDesdeAsignacionPredefinida']   = rec.get('strHoraDesdeAsignacionPredefinida');
                                    arrayParametros['horaHastaAsignacionPredefinida']   = rec.get('strHoraHastaAsignacionPredefinida');
                                    arrayParametros['detalleSolicitud']                 = rec.get('intIdDetalleSolicitudPredefinida');
                                    arrayParametros['horaInicioPredefinida']            = rec.get('strHoraDesdeAsignacionPredefinida');
                                    arrayParametros['horaFinPredefinida']               = rec.get('strHoraHastaAsignacionPredefinida');
                                    editarAsignacionVehicularPredefinida(arrayParametros);
                                }   
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'btn-eliminar-chofer';
                            var permiso = $("#ROLE_342-3778");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "")
                            {
                               this.items[2].tooltip = ''; 
                            }   
                            else
                            {
                                if(rec.get('intIdDetalleSolicitudPredefinida') == "") 
                                {
                                    strClassButton        = '';
                                    this.items[2].tooltip = '';
                                }
                                else 
                                {
                                    this.items[2].tooltip = 'Eliminar Asignación Vehicular Predefinida'; 
                                }

                            }

                            return strClassButton;
                        },
                        tooltip: 'Eliminar Asignación Vehicular Predefinida',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var strClassButton = 'btn-acciones btn-eliminar-chofer';
                            var permiso = $("#ROLE_342-3778");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }
                            if (strClassButton != "")
                            {
                                if(rec.get('intIdDetalleSolicitudPredefinida') != "") 
                                {
                                    var arrayParametros                 = [];
                                    
                                    arrayParametros['idElemento']       = rec.get('intIdElemento');
                                    arrayParametros['placa']            = rec.get('strNombreElemento');
                                    arrayParametros['modelo']           = rec.get('strNombreModeloElemento');
                                    arrayParametros['disco']            = rec.get('strDISCO');
                                    arrayParametros['detalleSolicitud'] = rec.get('intIdDetalleSolicitudPredefinida');
                                    
                                    arrayParametros['zonaPredefinida']              = rec.get('strZonaPredefinida');
                                    arrayParametros['tareaPredefinida']             = rec.get('strTareaPredefinida');
                                    arrayParametros['departamentoPredefinido']      = rec.get('strDepartamentoPredefinido');
                                    arrayParametros['nombresChoferPredefinido']     = rec.get('strNombresChoferPredefinido');
                                    arrayParametros['apellidosChoferPredefinido']   = rec.get('strApellidosChoferPredefinido');

                                    eliminarAsignacionVehicularPredefinida(arrayParametros);
                                }   
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
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
    var storeModelosMedioTransporte = new Ext.data.Store
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
                     store.insert(0,[{ strIdentificacion: 'Todos', strDescripcion: 'Todos' }]);
                }      
            }
        });
        
    var storeHorariosAsignacionPredefinida = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetHorariosAsignacionVehicularPredefinida,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name:'idParametroDet',         mapping:'idParametroDet'},
                {name:'valor1',                 mapping:'valor1'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                    store.insert(0,[{ idParametroDet: 'Todos', valor1: 'Todos' }]);
                }      
            }
        });
        
    var filterPanel = Ext.create('Ext.panel.Panel', 
        {
            bodyPadding: 7, 
            border:false,
            buttonAlign: 'center',
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
            width: '100%',
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
                
                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'strPlaca',
                    name: 'strPlaca',
                    fieldLabel: 'Placa',
                    value: '',
                    width: '300',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strPlaca-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:300},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Modelo Medio Transporte',
                    id: 'cmbModeloMedioTransporte',
                    name: 'cmbModeloMedioTransporte',
                    store: storeModelosMedioTransporte,
                    displayField: 'strDescripcion',
                    valueField: 'strIdentificacion',
                    queryMode: 'remote',
                    emptyText: 'Seleccione',
                    forceSelection: true
                },
                {html:"&nbsp;",border:false,width:100},
                
                
                
                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'strNumDisco',
                    name: 'strNumDisco',
                    fieldLabel: 'Disco',
                    value: '',
                    width: '250',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strNumDisco-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:300},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Horario',
                    id: 'cmbHorarioAsignacionPredefinida',
                    name: 'cmbHorarioAsignacionPredefinida',
                    store: storeHorariosAsignacionPredefinida,
                    displayField: 'valor1',
                    valueField: 'idParametroDet',
                    queryMode: 'remote',
                    emptyText: 'Seleccione',
                    forceSelection: true,
                    onFocus: function() {
                        var me = this;
                        if (!me.isExpanded) 
                        {
                            me.expand();
                        }
                        me.getPicker().focus();
                    }
                },
                {html:"&nbsp;",border:false,width:100},
                
                
                
                
                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'strBusqIdentificacionChoferPredefinido',
                    name: 'strBusqIdentificacionChoferPredefinido',
                    fieldLabel: 'Identificación Chofer Predefinido',
                    value: '',
                    width: '250',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strBusqIdentificacionChoferPredefinido-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:150},
                {html:"&nbsp;",border:false,width:150},
                {html:"&nbsp;",border:false,width:100},
                
                
                
                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'strBusqNombresChoferPredefinido',
                    name: 'strBusqNombresChoferPredefinido',
                    fieldLabel: 'Nombres Chofer Predefinido',
                    value: '',
                    width: '250',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strBusqNombresChoferPredefinido-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:150},
                {
                    xtype: 'textfield',
                    id: 'strBusqApellidosChoferPredefinido',
                    name: 'strBusqApellidosChoferPredefinido',
                    fieldLabel: 'Apellidos Chofer Predefinido',
                    value: '',
                    width: '250',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strBusqApellidosChoferPredefinido-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:100},
            ],	
            renderTo: 'filtro'
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
    
    var cmbHorarioAsignacionPredefinida = Ext.getCmp('cmbHorarioAsignacionPredefinida').value;
    
    if( cmbHorarioAsignacionPredefinida == "Todos" )
    {
        cmbHorarioAsignacionPredefinida = "";
    }
    
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.placa                              = Ext.getCmp('strPlaca').value;
    store.getProxy().extraParams.disco                              = Ext.getCmp('strNumDisco').value;
    store.getProxy().extraParams.modeloMedioTransporte              = cmbModeloMedioTransporte;
    store.getProxy().extraParams.horarioAsignacionPredefinida       = cmbHorarioAsignacionPredefinida;
    
    
    store.getProxy().extraParams.identificacionChoferPredefinido    = Ext.getCmp('strBusqIdentificacionChoferPredefinido').value;
    store.getProxy().extraParams.nombresChoferPredefinido           = Ext.getCmp('strBusqNombresChoferPredefinido').value;
    store.getProxy().extraParams.apellidosChoferPredefinido         = Ext.getCmp('strBusqApellidosChoferPredefinido').value;
    store.load();
}


function limpiar()
{
    Ext.getCmp('strPlaca').value="";
    Ext.getCmp('strPlaca').setRawValue("");
    
    Ext.getCmp('strNumDisco').value="";
    Ext.getCmp('strNumDisco').setRawValue("");
    
    Ext.getCmp('cmbModeloMedioTransporte').value = null;
    Ext.getCmp('cmbModeloMedioTransporte').setRawValue(null);
    
    Ext.getCmp('cmbHorarioAsignacionPredefinida').value = null;
    Ext.getCmp('cmbHorarioAsignacionPredefinida').setRawValue(null);
    
    Ext.getCmp('strBusqIdentificacionChoferPredefinido').value="";
    Ext.getCmp('strBusqIdentificacionChoferPredefinido').setRawValue("");
    
    Ext.getCmp('strBusqNombresChoferPredefinido').value="";
    Ext.getCmp('strBusqNombresChoferPredefinido').setRawValue("");
    
    Ext.getCmp('strBusqApellidosChoferPredefinido').value="";
    Ext.getCmp('strBusqApellidosChoferPredefinido').setRawValue("");
    
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.placa                              = Ext.getCmp('strPlaca').value;
    store.getProxy().extraParams.disco                              = Ext.getCmp('strNumDisco').value;
    store.getProxy().extraParams.modeloMedioTransporte              = Ext.getCmp('cmbModeloMedioTransporte').value;
    store.getProxy().extraParams.horarioAsignacionPredefinida       = Ext.getCmp('cmbHorarioAsignacionPredefinida').value;
    
    store.getProxy().extraParams.identificacionChoferPredefinido    = Ext.getCmp('strBusqIdentificacionChoferPredefinido').value;
    store.getProxy().extraParams.nombresChoferPredefinido           = Ext.getCmp('strBusqNombresChoferPredefinido').value;
    store.getProxy().extraParams.apellidosChoferPredefinido         = Ext.getCmp('strBusqApellidosChoferPredefinido').value;
    
    store.load();
}



function nuevaAsignacionVehicularPredefinida(arrayParametros)
{
    var filterPanelChoferes     = '';
    var TFNombresChofer         = '';
    var TFApellidosChofer       = '';
    var TFIdentificacionChofer  = '';
    
    storeTareas = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlTareasAsignacionVehicularPredefinida,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdTareaPredefinida',     mapping: 'id_tarea'},
            {name: 'strNombreTareaPredefinida', mapping: 'nombre_tarea'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    
    storeZonas = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlZonasAsignacionVehicularPredefinida,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdZonaPredefinida',     mapping: 'strValue'},
            {name: 'strNombreZonaPredefinida', mapping: 'strNombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    storeDepartamentos = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlDepartamentosAsignacionVehicularPredefinida,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        
        fields:
        [
            {name: 'intIdDepartamentoPredefinido',     mapping: 'strValue'},
            {name: 'strNombreDepartamentoPredefinido', mapping: 'strNombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    
    TFNombresChofer = new Ext.form.TextField({
        name: 'txtNombresChoferDisponible',
        fieldLabel: 'Nombres',
        xtype: 'textfield',
        labelWidth: 50
    });

    TFApellidosChofer = new Ext.form.TextField({
        name: 'txtApellidosChoferDisponible',
        fieldLabel: 'Apellidos',
        xtype: 'textfield',
        labelWidth: 50
    });

    TFIdentificacionChofer = new Ext.form.TextField({
        name: 'txtIdentificacionChoferDisponible',
        fieldLabel: 'Identificación',
        xtype: 'textfield',
        labelWidth: 70
    }); 

    storeChoferes = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 5,
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: strUrlGetChoferesPredefinidosDisponibles,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                identificacionChoferDisponible:'',
                nombresChoferDisponible:'',
                apellidosChoferDisponible:''
            }
        },
        fields: [
            {name:'idPersonaEmpresaRolChofer', type: 'int'},
            {name:'idPersonaChofer', type: 'int'},
            {name:'identificacionChofer', type: 'string'},
            {name:'nombresChofer', type: 'string'},
            {name:'apellidosChofer', type: 'string'}
        ]
    });
    var listViewChoferes='';
    
    listViewChoferes = Ext.create('Ext.grid.Panel', {
        width:520,
        height:200,
        collapsible:false,
        title: '',
        store: storeChoferes,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeChoferes,
                    displayInfo: true,
                    displayMsg: 'Mostrando Choferes {0} - {1} of {2}',
                    emptyMsg: "No hay datos para mostrar"
        }),

        columns: 
        [
            new Ext.grid.RowNumberer(),  
            {
                text: 'idPersonaEmpresaRolChofer',
                dataIndex: 'idPersonaEmpresaRolChofer',
                hidden: true,
                hideable: false
            },
            {
                text: 'idPersonaChofer',
                dataIndex: 'idPersonaChofer',
                hidden: true,
                hideable: false
            },
            {
                text: 'Apellidos',
                dataIndex: 'apellidosChofer',
                width: 200			
            },
            {
                text: 'Nombres',
                dataIndex: 'nombresChofer',
                width: 200			
            },
            {
                text: 'Identificación',
                width: 100,
                dataIndex: 'identificacionChofer'
            }
            
        ],
        listeners: 
        {
            itemdblclick:
            {
                fn: function( view, rec, node, index, e, options )
                {
                    
                    var intIdZonaPredefinida = Ext.getCmp('cmbZonaPredefinida').getValue();
                    var intIdTareaPredefinida = Ext.getCmp('cmbTareaPredefinida').getValue();
                    var intIdDepartamentoPredefinido = Ext.getCmp('cmbDepartamentoPredefinido').getValue();
                    var boolAsignarZonaTarea = false;
                    var boolAsignarDepartamento = false;
                    var objExtraParams = storeChoferes.proxy.extraParams;
                    if(!intIdDepartamentoPredefinido)
                    {
                        Ext.Msg.alert('Atenci\xf3n', 'Por favor seleccione un Departamento predefinido');
                    }
                    else
                    {
                        boolAsignarDepartamento=true;
                    }
                    
                    var strTipoAsignacion = Ext.getCmp('strTipoAsignacion').getValue();
                    if(strTipoAsignacion=='ZONA')
                    {
                        if(!intIdZonaPredefinida)
                        {
                            Ext.Msg.alert('Atenci\xf3n', 'Por favor seleccione una Zona Predefinida');
                        }
                        else
                        {
                            boolAsignarZonaTarea=true;
                        }
                    }
                    else if(strTipoAsignacion=='TAREA')
                    {
                        if(!intIdTareaPredefinida)
                        {
                            Ext.Msg.alert('Atenci\xf3n', 'Por favor seleccione una Tarea Predefinida');
                        }
                        else
                        {
                            boolAsignarZonaTarea=true;
                        }
                    }
                    
                    
                    if(boolAsignarDepartamento && boolAsignarZonaTarea)
                    {
                        var strMensaje="Se realizará la asignación predefinida para ";
                            strMensaje+="el vehículo con placa "+arrayParametros["placa"];
                            strMensaje+=". Desea continuar? ";
                        Ext.Msg.confirm('Alerta',strMensaje, function(btn)
                        {
                            if(btn=='yes')
                            {
                                Ext.MessageBox.hide();
                                connEsperaAccion.request
                                ({
                                    url: strUrlCrearAsignacionVehicularPredefinida,
                                    method: 'post',
                                    dataType: 'json',
                                    params:
                                    { 
                                        strTipoAsignacion                       : strTipoAsignacion,
                                        idElementoVehiculo                      : arrayParametros['idElemento'],
                                        idZonaPredefinida                       : intIdZonaPredefinida,
                                        idTareaPredefinida                      : intIdTareaPredefinida,
                                        idDepartamentoPredefinido               : intIdDepartamentoPredefinido,
                                        idPerChoferPredefinido                  : rec.data.idPersonaEmpresaRolChofer,
                                        strHoraDesdeAsignacionPredefinida       : objExtraParams.strHoraDesdeAsignacionPredefinida,
                                        strHoraHastaAsignacionPredefinida       : objExtraParams.strHoraHastaAsignacionPredefinida
                                    },
                                    success: function(result)
                                    {
                                        var strResult = result.responseText;
                                        var strMensajeChofer='';
                                        Ext.Msg.alert('Información',result.responseText);
                                        if ( typeof win != 'undefined' && win != null )
                                        {
                                            win.destroy();
                                        }

                                        if( strResult=="OK" )
                                        {
                                            strMensajeChofer+='Se asignó el chofer al vehículo con éxito.';

                                           Ext.Msg.alert('Información ', strMensajeChofer);
                                           store.load();
                                        }
                                        else
                                        {
                                            strMensajeChofer+=strResult;
                                            Ext.Msg.alert('Error ', strMensajeChofer);

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
                    
                }
            }
        }
    });
    
    filterPanelChoferes = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout:{
                type:'table',
                columns: 5,
                align: 'left'
        },
        bodyStyle: {
                background: '#fff'
        },                     
        defaults: {
                bodyStyle: 'padding:10px'
        },
        collapsible : true,
        collapsed: true,
        width: 520,
        title: 'Criterios de búsqueda',

        buttons: [                   
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function()
                        { 
                            buscarChoferes();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function()
                        { 
                            limpiarChoferes();
                        }
                    }
                ],                
        items: [
                    TFIdentificacionChofer,
                    { width: '5%',border:false},
                    TFNombresChofer,
                    { width: '5%',border:false},
                    TFApellidosChofer
                ]	
             });
             
    DTHoraDesde = new Ext.form.TimeField({
        xtype: 'timefield',
        id: 'horaDesdeAsignacionPredefinida',
        name:'horaDesdeAsignacionPredefinida',
        fieldLabel: '<b>Desde</b>',
        editable: false,
        format: 'H:i',
        minValue: '00:00',
        maxValue: '24:00',
        value:"",
        emptyText: "Seleccione",
        labelWidth: 50,
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarHoras(cmp,arrayParametros['idElemento']);
            }
        }
    });

    DTHoraHasta = new Ext.form.TimeField({
        xtype: 'timefield',
        id: 'horaHastaAsignacionPredefinida',
        name:'horaHastaAsignacionPredefinida',
        editable: false,
        fieldLabel: '<b>Hasta</b>',
        format: 'H:i',
        minValue: '00:00',
        maxValue: '24:00',
        value:"",
        anchor:'100%',
        emptyText: "Seleccione",
        labelWidth: 50,
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarHoras(cmp,arrayParametros['idElemento']);
            }
        }
    });
        
    var formAsignacionPredefinida = Ext.create('Ext.form.Panel', {
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
                       title: 'Datos de la Asignación Vehicular Predefinida',                       
                       width: '100%',
                       
                       items: 
                       [
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Departamento Predefinido</b>',
                                id: 'cmbDepartamentoPredefinido',
                                name: 'cmbDepartamentoPredefinido',
                                store: storeDepartamentos,
                                displayField: 'strNombreDepartamentoPredefinido',
                                valueField: 'intIdDepartamentoPredefinido',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200,
                                listeners: {

                                    change: function(cmp, newValue, oldValue) {
                                        var objExtraParams = storeChoferes.proxy.extraParams;
                                        if(isNaN(cmp.getValue()))
                                        {
                                            objExtraParams.departamentoPredefinido = '';
                                        }
                                        else
                                        {
                                            objExtraParams.departamentoPredefinido = cmp.getValue();
                                        }
                                    }
                                }
                            },
                            {
                               xtype: 'displayfield',
                               id: 'strTipoAsignacion',
                               name: 'strTipoAsignacion',
                               value: 'ZONA',
                               hidden: true
                            },
                            {
                                xtype: 'radiogroup',
                                fieldLabel: '<b>Asignación Por</b>',
                                labelWidth: 200,
                                items: [
                                    {boxLabel: 'Zona', name: 'tipoAsignacion', inputValue: 'ZONA', checked: true},
                                    {boxLabel: 'Tarea', name: 'tipoAsignacion', inputValue: 'TAREA'}
                                ],
                                listeners: {
                                    change: function(field, newValue, oldValue) {
                                        var value = newValue.tipoAsignacion;
                                        if (Ext.isArray(value)) {
                                            return;
                                        }
                                        Ext.getCmp('strTipoAsignacion').setValue(value);
                                        if(value == 'ZONA')
                                        {
                                            Ext.getCmp('cmbTareaPredefinida').setValue("");
                                            Ext.getCmp('cmbTareaPredefinida').getEl().toggle();
                                            Ext.getCmp('cmbTareaPredefinida').getEl().hide();

                                            Ext.getCmp('cmbZonaPredefinida').setValue("");
                                            Ext.getCmp('cmbZonaPredefinida').getEl().toggle();
                                            Ext.getCmp('cmbZonaPredefinida').getEl().show();
                                        }
                                        else if(value=='TAREA')
                                        {
                                            Ext.getCmp('cmbZonaPredefinida').setValue("");
                                            Ext.getCmp('cmbZonaPredefinida').getEl().toggle();
                                            Ext.getCmp('cmbZonaPredefinida').getEl().hide();

                                            Ext.getCmp('cmbTareaPredefinida').setValue("");
                                            Ext.getCmp('cmbTareaPredefinida').getEl().toggle();
                                            Ext.getCmp('cmbTareaPredefinida').getEl().show();
                                        }

                                    }
                                }
                            },
                            
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Zona Predefinida</b>',
                                id: 'cmbZonaPredefinida',
                                name: 'cmbZonaPredefinida',
                                store: storeZonas,
                                displayField: 'strNombreZonaPredefinida',
                                valueField: 'intIdZonaPredefinida',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Tarea Predefinida</b>',
                                id: 'cmbTareaPredefinida',
                                name: 'cmbTareaPredefinida',
                                store: storeTareas,
                                displayField: 'strNombreTareaPredefinida',
                                valueField: 'intIdTareaPredefinida',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200,
                                hidden:true
                            },
                            
                            {
                                height: 10,
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
                                xtype: 'displayfield',
                                id: 'strNombreModeloElementoAsignacionVehicularPredefinida',
                                name: 'strNombreModeloElementoAsignacionVehicularPredefinida',
                                fieldLabel: '<b>Modelo</b>',
                                value: arrayParametros['modelo'],
                                width: '100%',
                                labelWidth: 120
                            },
                            
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
                                            {
                                                xtype: 'displayfield',
                                                id: 'strPlacaElementoAsignacionVehicularPredefinida',
                                                name: 'strPlacaElementoAsignacionVehicularPredefinida',
                                                fieldLabel: '<b>Placa</b>',
                                                value: arrayParametros['placa'],
                                                width: '100%',
                                                labelWidth: 120
                                            }

                                        ]
                                    },
                                    {
                                        width: 100,
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
                                        items: 
                                        [
                                            {
                                                xtype: 'displayfield',
                                                id: 'strDiscoElementoAsignacionVehicularPredefinida',
                                                name: 'strDiscoElementoAsignacionVehicularPredefinida',
                                                fieldLabel: '<b>Disco</b>',
                                                value: arrayParametros['disco'],
                                                width: '100%',
                                                labelWidth: 80
                                            }

                                        ]
                                    }
                                ]
                            }
                       ]
                   },
                   {
                       xtype: 'fieldset',
                       title: 'Horario',                       
                       width: '100%',
                       items: 
                       [
                           {
                                layout: 'table',
                                border: false,
                                items: 
                                [
                                    DTHoraDesde,
                                    {
                                        width: 100,
                                        layout: 'form',
                                        border: false,
                                        items: 
                                        [
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    DTHoraHasta
                                ]
                            }
                       ]
                   },
                   {
                       xtype: 'fieldset',
                       title: 'Choferes Disponibles',                       
                       width: '100%',
                       items: 
                       [
                           filterPanelChoferes,
                           listViewChoferes
                       ]
                   }

               ]
           }

        ]
    });

    win = Ext.create('Ext.window.Window',
    {
      title: 'Asignación Vehicular Predefinida',
      modal: true,
      width: 600,
      closable: true,
      layout: 'fit',
      floating: true,
      shadow: true,
      shadowOffset:20,
      resizable:true,
      items: [formAsignacionPredefinida]
    }).show();
}



function buscarChoferes()
{
    if(Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferDisponible]')[0].value!="" 
            || Ext.ComponentQuery.query('textfield[name=txtNombresChoferDisponible]')[0].value!=""
            || Ext.ComponentQuery.query('textfield[name=txtApellidosChoferDisponible]')[0].value!="")
    {
        storeChoferes.loadData([],false);
        storeChoferes.currentPage = 1;

        storeChoferes.getProxy().extraParams.identificacionChoferDisponible   = 
        Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferDisponible]')[0].value;
        storeChoferes.getProxy().extraParams.nombresChoferDisponible          =
        Ext.ComponentQuery.query('textfield[name=txtNombresChoferDisponible]')[0].value;
        storeChoferes.getProxy().extraParams.apellidosChoferDisponible        =
        Ext.ComponentQuery.query('textfield[name=txtApellidosChoferDisponible]')[0].value;
        storeChoferes.load({params: {start: 0, limit: 5}});
    }
    else
    {
        Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor Ingrese el nombre, apellido o una identificación para buscar',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
    }


}

function limpiarChoferes()
{
    Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferDisponible]')[0].value  = "";
    Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferDisponible]')[0].setRawValue("");

    Ext.ComponentQuery.query('textfield[name=txtNombresChoferDisponible]')[0].value = "";
    Ext.ComponentQuery.query('textfield[name=txtNombresChoferDisponible]')[0].setRawValue("");

    Ext.ComponentQuery.query('textfield[name=txtApellidosChoferDisponible]')[0].value = "";
    Ext.ComponentQuery.query('textfield[name=txtApellidosChoferDisponible]')[0].setRawValue("");

    storeChoferes.currentPage   = 1;

    storeChoferes.getProxy().extraParams.identificacionChoferDisponible   = 
        Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferDisponible]')[0].value;  
    storeChoferes.getProxy().extraParams.nombresChoferDisponible          = 
        Ext.ComponentQuery.query('textfield[name=txtNombresChoferDisponible]')[0].value;
    storeChoferes.getProxy().extraParams.apellidosChoferDisponible        = 
        Ext.ComponentQuery.query('textfield[name=txtApellidosChoferDisponible]')[0].value;
    storeChoferes.load();
}


function eliminarAsignacionVehicularPredefinida(arrayParametros)
{ 
    var storeMotivosEliminarAVPredefinida = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlMotivosAVPredefinida,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                strAccion: 'eliminarAsignacionVehicularPredefinida', 
                strModulo: 'asignacion_vehicular_predefinida'
            }
        },
        fields:
        [
            {name: 'intIdMotivo', mapping: 'intIdMotivo'},
            {name: 'strMotivo',   mapping: 'strMotivo'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, 
                 [
                    {
                        strMotivo: 'Seleccione',
                        intIdMotivo: ''
                    }
                 ]);
            }      
        }
    });
    
    
    
    var formPanelEliminarAVPredefinida = Ext.create('Ext.form.Panel',
    {
        id: 'formEliminarAVPredefinida',
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
                        fieldLabel: '<b>Motivo de Eliminación de Asignación Vehicular Predefinida</b>',
                        id: 'cmbMotivoEliminacionAVPredefinida',
                        name: 'cmbMotivoEliminacionAVPredefinida',
                        store: storeMotivosEliminarAVPredefinida,
                        displayField: 'strMotivo',
                        valueField: 'intIdMotivo',
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
                text: 'Aceptar',
                type: 'submit',
                handler: function()
                {
                    var form = Ext.getCmp('formEliminarAVPredefinida').getForm();

                    if( form.isValid() )
                    {
                        var intIdMotivoEliminarAVPredefinida = Ext.getCmp('cmbMotivoEliminacionAVPredefinida').getValue();

                        if ( intIdMotivoEliminarAVPredefinida != null && intIdMotivoEliminarAVPredefinida != '' )
                        {
                            
                            var strMensaje = 'Se eliminará la asignación vehicular predefinida para el vehículo con placa '+arrayParametros['placa'];
                                strMensaje += '. Desea continuar?';
                            Ext.Msg.confirm('Alerta',strMensaje, function(btn)
                            {
                                if(btn=='yes')
                                {
                                    Ext.MessageBox.wait("Verificando cuadrillas con esta asignación vehicular predefinida...");
                                    Ext.Ajax.request
                                    ({
                                        url: strUrlVerificarCuadrillasConAsignacionVehicularPredefinida,
                                        method: 'post',
                                        params: 
                                        { 
                                            idDetalleSolicitud  : arrayParametros['detalleSolicitud'],
                                            idElementoVehiculo  : arrayParametros['idElemento']
                                        },
                                        success: function(response)
                                        {
                                            var text = response.responseText;
                                            
                                            if ( typeof winMotivo != 'undefined' && winMotivo != null )
                                            {
                                                winMotivo.destroy();
                                            }
                                            
                                            if(text === "OK")
                                            {
                                                //Ext.Msg.alert('Alert', text);

                                                connEsperaAccion.request
                                                ({
                                                    url: strUrlEliminarAsignacionVehicularPredefinida,
                                                    method: 'post',
                                                    dataType: 'json',
                                                    params:
                                                    { 
                                                        idDetalleSolicitud: arrayParametros['detalleSolicitud'],
                                                        idMotivoEliminacionAVPredefinida   : intIdMotivoEliminarAVPredefinida,
                                                                
                                                    },
                                                    success: function(result)
                                                    {
                                                        var strResult = result.responseText;
                                                        var strMensajeChofer='';
                                                        Ext.Msg.alert('Información',result.responseText);
                                                        if ( typeof win != 'undefined' && win != null )
                                                        {
                                                            win.destroy();
                                                        }

                                                        if( strResult=="OK" )
                                                        {
                                                            var strMensajeEliminarAsignacion = 'Se eliminó la asignación predefinida para el vehículo con placa ';
                                                            strMensajeEliminarAsignacion    += arrayParametros['placa'];

                                                            Ext.Msg.alert('Información ', strMensajeEliminarAsignacion);
                                                            store.load();
                                                        }
                                                        else
                                                        {
                                                            strMensajeChofer+=strResult;
                                                            Ext.Msg.alert('Error ', strMensajeChofer);

                                                        }
                                                    },
                                                    failure: function(result)
                                                    {
                                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                    }
                                                });



                                            }
                                            else
                                            {
                                                var strMensajeError ="No se puede eliminar la asignación vehicular predefinida porque existen cuadrillas que actualmente";
                                                    strMensajeError+=" utilizan el vehículo."
                                                    strMensajeError+="<br/><b>Datos de la Asignación Vehicular Predefinida:</b><br/>";
                                                    strMensajeError+="<b>Disco:</b> "+arrayParametros['disco']+"<br/>";
                                                    strMensajeError+="<b>Placa:</b> "+arrayParametros['placa']+"<br/>";
                                                    if(arrayParametros['zonaPredefinida']!="")
                                                    {
                                                        strMensajeError+="<b>Zona Predefinida:</b> "+arrayParametros['zonaPredefinida']+"<br/>";
                                                    }
                                                    else
                                                    {
                                                        strMensajeError+="<b>Tarea Predefinida:</b> "+arrayParametros['tareaPredefinida']+"<br/>";

                                                    }
                                                    strMensajeError+="<b>Departamento Predefinido:</b> "+arrayParametros['departamentoPredefinido']+"<br/>";
                                                    strMensajeError+="<b>Chofer Predefinido:</b> "+arrayParametros['nombresChoferPredefinido']+" "
                                                    strMensajeError+= arrayParametros['apellidosChoferPredefinido']+"<br/>";
                                                    strMensajeError+="<br/><b>Si desea eliminar por favor solicite la desvinculación del vehículo";
                                                    strMensajeError+=" de las siguientes cuadrillas:</b><br/>";

                                                    strMensajeError+=text;
                                                    Ext.MessageBox.hide();
                                                    Ext.Msg.alert('Error', strMensajeError);

                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.MessageBox.hide();
                                            Ext.Msg.alert('Error',result.responseText);
                                        }
                                    });
                                }
                            });
                            
                            
                            
                            
                        }
                        else
                        {
                            Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un motivo por la eliminación de la Asignación Vehicular Predefinida');
                        }
                    }
                    
                }
            },
            {
                text: 'Cerrar',
                handler: function()
                {
                    winMotivo.destroy();
                }
            }
        ]
    });
    winMotivo = Ext.create('Ext.window.Window',
    {
         title: 'Eliminar Asignación Vehicular Predefinida',
         modal: true,
         width: 350,
         closable: true,
         layout: 'fit',
         items: [formPanelEliminarAVPredefinida]
    }).show();
    
    
}




function editarAsignacionVehicularPredefinida(arrayParametros)
{
    var filterPanelChoferes     = '';
    var TFNombresChofer         = '';
    var TFApellidosChofer       = '';
    var TFIdentificacionChofer  = '';
    

    TFNombresChofer = new Ext.form.TextField({
        name: 'txtNombresChoferDisponible',
        fieldLabel: 'Nombres',
        xtype: 'textfield',
        labelWidth: 50
    });

    TFApellidosChofer = new Ext.form.TextField({
        name: 'txtApellidosChoferDisponible',
        fieldLabel: 'Apellidos',
        xtype: 'textfield',
        labelWidth: 50
    });

    TFIdentificacionChofer = new Ext.form.TextField({
        name: 'txtIdentificacionChoferDisponible',
        fieldLabel: 'Identificación',
        xtype: 'textfield',
        labelWidth: 70
    }); 

    storeChoferes = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 5,
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: strUrlGetChoferesPredefinidosDisponibles,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                identificacionChoferDisponible:'',
                nombresChoferDisponible:'',
                apellidosChoferDisponible:'',
                departamentoPredefinido:''
            }
        },
        fields: [
            {name:'idPersonaEmpresaRolChofer', type: 'int'},
            {name:'idPersonaChofer', type: 'int'},
            {name:'identificacionChofer', type: 'string'},
            {name:'nombresChofer', type: 'string'},
            {name:'apellidosChofer', type: 'string'}
        ]
    });
    
    storeChoferes.load();
    var listViewChoferes='';
    
    listViewChoferes = Ext.create('Ext.grid.Panel', {
        width:520,
        height:200,
        collapsible:false,
        title: '',
        store: storeChoferes,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeChoferes,
                    displayInfo: true,
                    displayMsg: 'Mostrando Choferes {0} - {1} of {2}',
                    emptyMsg: "No hay datos para mostrar"
        }),

        columns: 
        [
            new Ext.grid.RowNumberer(),  
            {
                text: 'idPersonaEmpresaRolChofer',
                dataIndex: 'idPersonaEmpresaRolChofer',
                hidden: true,
                hideable: false
            },
            {
                text: 'idPersonaChofer',
                dataIndex: 'idPersonaChofer',
                hidden: true,
                hideable: false
            },
            {
                text: 'Apellidos',
                dataIndex: 'apellidosChofer',
                width: 200			
            },
            {
                text: 'Nombres',
                dataIndex: 'nombresChofer',
                width: 200			
            },
            {
                text: 'Identificación',
                width: 100,
                dataIndex: 'identificacionChofer'
            }
            
        ],
        listeners: 
        {
            itemdblclick:
            {
                fn: function( view, rec, node, index, e, options )
                { 
                    var storeMotivosCambiarChoferAVPredefinida = new Ext.data.Store
                    ({
                        total: 'total',
                        pageSize: 200,
                        proxy:
                        {
                            type: 'ajax',
                            method: 'post',
                            url: strUrlMotivosAVPredefinida,
                            reader:
                            {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'encontrados'
                            },
                            extraParams:
                            {
                                strAccion: 'editarAsignacionVehicularPredefinida', 
                                strModulo: 'asignacion_vehicular_predefinida'
                            }
                        },
                        fields:
                        [
                            {name: 'intIdMotivo', mapping: 'intIdMotivo'},
                            {name: 'strMotivo',   mapping: 'strMotivo'}
                        ],
                        listeners: 
                        {
                            load: function(store, records)
                            {
                                 store.insert(0, 
                                 [
                                    {
                                        strMotivo: 'Seleccione',
                                        intIdMotivo: ''
                                    }
                                 ]);
                            }      
                        }
                    });


                    var formPanelMotivoCambioChoferPredefinido = Ext.create('Ext.form.Panel',
                    {
                        id: 'formMotivoCambiarChoferPredefinido',
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
                                        fieldLabel: '<b>Motivo de Cambio de Chofer Predefinido</b>',
                                        id: 'cmbMotivoCambioChoferPredefinido',
                                        name: 'cmbMotivoCambioChoferPredefinido',
                                        store: storeMotivosCambiarChoferAVPredefinida,
                                        displayField: 'strMotivo',
                                        valueField: 'intIdMotivo',
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
                                text: 'Aceptar',
                                type: 'submit',
                                handler: function()
                                {
                                    var form = Ext.getCmp('formMotivoCambiarChoferPredefinido').getForm();

                                    if( form.isValid() )
                                    {
                                        var intIdMotivoCambioDeChoferPredefinido = Ext.getCmp('cmbMotivoCambioChoferPredefinido').getValue();

                                        if ( intIdMotivoCambioDeChoferPredefinido != null && intIdMotivoCambioDeChoferPredefinido != '' )
                                        {
                                            var strMensaje="Se realizará el cambio del chofer predefinido para ";
                                                strMensaje+="el vehículo con placa "+arrayParametros["placa"];
                                                strMensaje+=". Desea continuar? ";
                                                Ext.Msg.confirm('Alerta',strMensaje, function(btn)
                                                {
                                                    if(btn=='yes')
                                                    {
                                                        connEsperaAccion.request
                                                        ({
                                                            url: strUrlEditarAsignacionVehicularPredefinida,
                                                            method: 'post',
                                                            dataType: 'json',
                                                            params:
                                                            {
                                                                idMotivoCambioDeChoferPredefinido   : intIdMotivoCambioDeChoferPredefinido,
                                                                strTipoAsignacion                   : arrayParametros["tipoAsignacion"],
                                                                idElementoVehiculo                  : arrayParametros['idElemento'],
                                                                idZonaPredefinida                   : arrayParametros['idZonaPredefinida'],
                                                                idTareaPredefinida                  : arrayParametros['idTareaPredefinida'],
                                                                idDepartamentoPredefinido           : arrayParametros['idDepartamentoPredefinido'],
                                                                idDetalleSolicitud                  : arrayParametros['detalleSolicitud'],
                                                                idPerChoferPredefinidoNuevo         : rec.data.idPersonaEmpresaRolChofer,
                                                                strHoraInicioPredefinido            : arrayParametros['horaInicioPredefinida'],
                                                                strHoraFinPredefinido               : arrayParametros['horaFinPredefinida'],
                                                            },
                                                            success: function(result)
                                                            {
                                                                if ( typeof winMotivo != 'undefined' && winMotivo != null )
                                                                {
                                                                    winMotivo.destroy();
                                                                }
                                                                
                                                                var strResult = result.responseText;
                                                                var strMensajeChofer='';
                                                                Ext.Msg.alert('Información',result.responseText);
                                                                if ( typeof win != 'undefined' && win != null )
                                                                {
                                                                    win.destroy();
                                                                }

                                                                if( strResult=="OK" )
                                                                {
                                                                    strMensajeChofer+='Se cambió el chofer predefinido con éxito.';

                                                                   Ext.Msg.alert('Información ', strMensajeChofer);
                                                                   store.load();
                                                                }
                                                                else
                                                                {
                                                                    strMensajeChofer+=strResult;
                                                                    Ext.Msg.alert('Error ', strMensajeChofer);

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
                                            Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un motivo por el Cambio de Chofer Predefinido');
                                        }
                                    }
                                }
                            },
                            {
                                text: 'Cerrar',
                                handler: function()
                                {
                                    winMotivo.destroy();
                                }
                            }
                        ]
                    });
                    
                    winMotivo = Ext.create('Ext.window.Window',
                    {
                         title: 'Motivo De Cambio de Chofer Predefinido',
                         modal: true,
                         width: 350,
                         closable: true,
                         layout: 'fit',
                         items: [formPanelMotivoCambioChoferPredefinido]
                    }).show();
                    
                    
                    
                    
                    
                }
            }
        }
    });
    
    filterPanelChoferes = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout:{
                type:'table',
                columns: 5,
                align: 'left'
        },
        bodyStyle: {
                background: '#fff'
        },                     
        defaults: {
                bodyStyle: 'padding:10px'
        },
        collapsible : true,
        collapsed: true,
        width: 520,
        title: 'Criterios de búsqueda',

        buttons: [                   
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function()
                        { 
                            buscarChoferes();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function()
                        { 
                            limpiarChoferes();
                        }
                    }
                ],                
        items: [
                    TFIdentificacionChofer,
                    { width: '5%',border:false},
                    TFNombresChofer,
                    { width: '5%',border:false},
                    TFApellidosChofer
                ]	
             });
    var boolHiddenZona  = false;
    var boolHiddenTarea = false;
    if(arrayParametros['tipoAsignacion']=='ZONA')
    {
        boolHiddenTarea=true;
    }
    else
    {
        boolHiddenZona=true;
    }
    var formAsignacionPredefinida = Ext.create('Ext.form.Panel', {
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
                       title: 'Datos de la Asignación Vehicular Predefinida',                       
                       width: '100%',
                       
                       items: 
                       [
                            {
                                xtype: 'displayfield',
                                id: 'strNombreModeloElementoAsignacionVehicularPredefinida',
                                name: 'strNombreModeloElementoAsignacionVehicularPredefinida',
                                fieldLabel: '<b>Modelo</b>',
                                value: arrayParametros['modelo'],
                                width: '100%',
                                labelWidth: 200
                            },
                            {
                                xtype: 'displayfield',
                                id: 'strPlacaElementoAsignacionVehicularPredefinida',
                                name: 'strPlacaElementoAsignacionVehicularPredefinida',
                                fieldLabel: '<b>Placa</b>',
                                value: arrayParametros['placa'],
                                width: '100%',
                                labelWidth: 200
                            },
                            {
                               xtype: 'displayfield',
                               id: 'strNombreDepartamentoAsignacionVehicularPredefinido',
                               name: 'strNombreDepartamentoPredefinido',
                               fieldLabel: '<b>Departamento Predefinido</b>',
                               value: arrayParametros['departamentoPredefinido'],
                               width: '100%',
                               labelWidth: 200
                            },
                            {
                               xtype: 'displayfield',
                               id: 'strNombreZonaAsignacionVehicularPredefinida',
                               name: 'strNombreZonaAsignacionVehicularPredefinida',
                               fieldLabel: '<b>Zona Predefinida</b>',
                               value: arrayParametros['zonaPredefinida'],
                               width: '100%',
                               labelWidth: 200,
                               hidden: boolHiddenZona
                            },
                            {
                               xtype: 'displayfield',
                               id: 'strNombreTareaAsignacionVehicularPredefinida',
                               name: 'strNombreTareaAsignacionVehicularPredefinida',
                               fieldLabel: '<b>Tarea Predefinida</b>',
                               value: arrayParametros['tareaPredefinida'],
                               width: '100%',
                               labelWidth: 200,
                               hidden: boolHiddenTarea
                            },
                            {
                                xtype: 'displayfield',
                                id: 'strHoraDesdeAsignacionVehicularPredefinida',
                                name: 'strHoraDesdeAsignacionVehicularPredefinida',
                                fieldLabel: '<b>Desde</b>',
                                value: arrayParametros['horaDesdeAsignacionPredefinida'],
                                width: '100%',
                                labelWidth: 200
                            },
                            {
                                xtype: 'displayfield',
                                id: 'strHoraHastaAsignacionVehicularPredefinida',
                                name: 'strHoraHastaAsignacionVehicularPredefinida',
                                fieldLabel: '<b>Hasta</b>',
                                value: arrayParametros['horaHastaAsignacionPredefinida'],
                                width: '100%',
                                labelWidth: 200
                            },
                            {
                               xtype: 'displayfield',
                               id: 'strChoferAsignacionVehicularPredefinido',
                               name: 'strChoferAsignacionVehicularPredefinido',
                               fieldLabel: '<b>Chofer Predefinido a cambiar</b>',
                               value: arrayParametros['nombresChoferPredefinido']+" "+ arrayParametros['apellidosChoferPredefinido'],
                               width: '100%',
                               labelWidth: 200
                            }
                       ]
                   },
                   {
                       xtype: 'fieldset',
                       title: 'Choferes Disponibles',                       
                       width: '100%',
                       items: 
                       [
                           filterPanelChoferes,
                           listViewChoferes
                       ]
                   }

               ]
           }

        ]
    });

    win = Ext.create('Ext.window.Window',
    {
      title: 'Cambio de Chofer Predefinido',
      modal: true,
      width: 600,
      closable: true,
      layout: 'fit',
      floating: true,
      shadow: true,
      shadowOffset:20,
      resizable:true,
      items: [formAsignacionPredefinida]
    }).show();
}




function validarHoras(cmp,idElemento)
{
    var fieldHoraDesdeAsignacion = Ext.getCmp('horaDesdeAsignacionPredefinida');
    var valueHoraDesdeAsignacion = fieldHoraDesdeAsignacion.getValue();
    var formattedValueHoraDesdeAsignacion = Ext.Date.format(valueHoraDesdeAsignacion, 'H:i');

    var fieldHoraHastaAsignacion = Ext.getCmp('horaHastaAsignacionPredefinida');
    var valueHoraHastaAsignacion = fieldHoraHastaAsignacion.getValue();
    var formattedValueHoraHastaAsignacion = Ext.Date.format(valueHoraHastaAsignacion, 'H:i');
    var boolOKHoras = true;
    var boolCamposLLenos=false;
    var strMensaje  = '';
    
    if(valueHoraDesdeAsignacion && valueHoraHastaAsignacion )
    {
        boolCamposLLenos=true;
    }

    if(valueHoraDesdeAsignacion && valueHoraHastaAsignacion)
    {
        if(formattedValueHoraDesdeAsignacion==formattedValueHoraHastaAsignacion)
        {
            boolOKHoras=false;
            strMensaje='La Hora Inicio '+ formattedValueHoraDesdeAsignacion +' no puede ser igual a la Hora Fin '+formattedValueHoraHastaAsignacion;
            Ext.Msg.alert('Atenci\xf3n', strMensaje);
        }
        else if(formattedValueHoraDesdeAsignacion>formattedValueHoraHastaAsignacion)
        {
            boolOKHoras=false;
            strMensaje='La Hora Inicio '+ formattedValueHoraDesdeAsignacion +' no puede ser mayor a la Hora Fin '+formattedValueHoraHastaAsignacion;
            Ext.Msg.alert('Atenci\xf3n', strMensaje);
        }
    }

    if( boolOKHoras && boolCamposLLenos)
    {
        Ext.MessageBox.wait("Validando Horario...");
        Ext.Ajax.request
        ({
            url: strUrlValidarHorarioAsignacionPredefinida,
            method: 'post',
            params: 
            { 
                idElemento: idElemento,
                strHoraDesdeAsignacionPredefinida: formattedValueHoraDesdeAsignacion,
                strHoraHastaAsignacionPredefinida: formattedValueHoraHastaAsignacion
            },
            success: function(response)
            {
                var text = response.responseText;

                if(text === "OK")
                {
                    Ext.MessageBox.hide();
                    
                    //Buscar Choferes disponibles para la asignación predefinida de acuerdo al horario 
                    var objExtraParams                                  = storeChoferes.proxy.extraParams;
                    objExtraParams.errorHoras                           = 0;
                    objExtraParams.strHoraDesdeAsignacionPredefinida    = formattedValueHoraDesdeAsignacion;
                    objExtraParams.strHoraHastaAsignacionPredefinida    = formattedValueHoraHastaAsignacion;

                    limpiarChoferes();
                }
                else
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.show(
                    {
                       title: 'Error',
                       width: 300,
                       cls: 'msg_floaitng',
                       msg: "No se puede ingresar la Asignación Predefinida del Chofer.<br>"+text+"."
                    });
                }
            },
            failure: function(result)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error',result.responseText);
            }
        });
        
        
    }
    else
    {
        if(cmp && boolCamposLLenos)
        {
            cmp.value = "";
            cmp.setRawValue("");
            var objExtraParams          = storeChoferes.proxy.extraParams;
            objExtraParams.errorHoras   = 1;
            storeChoferes.load();
        }
    }
}
