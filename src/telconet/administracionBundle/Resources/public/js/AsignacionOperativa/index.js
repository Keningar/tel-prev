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
                    return record.get('intIdElemento') + '-' +record.get('intIdPersonaChoferPredefinido') + '-' + record.get('intIdCuadrilla') 
                        + '-' + record.get('intIdPerDetalleChofer');
                }
            },
            {name:'intIdElemento',                      mapping:'intIdElemento'},
            {name:'strNombreElemento',                  mapping:'strNombreElemento'},
            {name:'intIdModeloElemento',                mapping:'intIdModeloElemento'},
            {name:'strNombreModeloElemento',            mapping:'strNombreModeloElemento'},
            {name:'strDetalleElementoDISCO',            mapping:'strDetalleElementoDISCO'},
            {name:'intIdCuadrilla',                     mapping:'intIdCuadrilla'},
            {name:'strTurnoInicioCuadrilla',            mapping:'strTurnoInicioCuadrilla'},
            {name:'strTurnoFinCuadrilla',               mapping:'strTurnoFinCuadrilla'},
            {name:'strNombreCuadrilla',                 mapping:'strNombreCuadrilla'},
			
			//Predefinido
            {name:'strHoraInicioChoferPredefinido',     mapping:'strHoraInicioAPredefinido'},
            {name:'strHoraFinChoferPredefinido',        mapping:'strHoraFinAPredefinido'},
            
            {name:'intIdSolicitudChoferPredefinido',    mapping:'intIdSolicitudChoferPredefinido'},
			{name:'intIdPerChoferPredefinido',			mapping:'intIdPerChoferPredefinido'},
			{name:'intIdPersonaChoferPredefinido',		mapping:'intIdPersonaChoferPredefinido'},
			{name:'strIdentificacionChoferPredefinido',	mapping:'strIdentificacionChoferPredefinido'},
			{name:'strNombresChoferPredefinido',		mapping:'strNombresChoferPredefinido'},
			{name:'strApellidosChoferPredefinido',		mapping:'strApellidosChoferPredefinido'},
			//Fin de Predefinido

            
            {name:'strDepartamentoAsignacionProvisional',          mapping:'strDepartamentoAsignacionProvisional'},
            {name:'strZonaAsignacionProvisional',                  mapping:'strZonaAsignacionProvisional'},

            {name:'strAsignacionVehicularFechaInicio',  mapping:'strAsignacionVehicularFechaInicio'},
            {name:'strAsignacionVehicularHoraInicio',   mapping:'strAsignacionVehicularHoraInicio'},
            {name:'strAsignacionVehicularHoraFin',      mapping:'strAsignacionVehicularHoraFin'},
			

			//Provisional
            {name:'asignacionProvisionalXCuadrilla',    mapping: 'asignacionProvisionalXCuadrilla'},
            {name:'intIdPerDetalleChofer',              mapping:'intIdPerDetalleChofer'},
            {name:'intIdPersonaDetalleChofer',          mapping:'intIdPersonaDetalleChofer'},
            {name:'strDetalleNombresChofer',            mapping:'strDetalleNombresChofer'},
            {name:'strDetalleApellidosChofer',          mapping:'strDetalleApellidosChofer'},
            {name:'strFechaInicioAsignacionProvisional',mapping:'strFechaInicioAsignacionProvisional'},
            {name:'strFechaFinAsignacionProvisional',   mapping:'strFechaFinAsignacionProvisional'},
            {name:'strHoraInicioAsignacionProvisional', mapping:'strHoraInicioAsignacionProvisional'},
            {name:'strHoraFinAsignacionProvisional',    mapping:'strHoraFinAsignacionProvisional'},
			//Fin de Provisional
		
            {name:'strDetalleIdentificacionChofer',     mapping:'strDetalleIdentificacionChofer'}

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
            url : strUrlGridAsignacionOperativa,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                nombre: ''
            }
        },
        autoLoad: true
    });

    var pluginExpanded = true;

    grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: '100%',
        height: 450,
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
                        iconCls: 'icon_search',
                        text: 'Ver Asignaciones Provisionales de Choferes',
                        scope: this,
                        handler: function()
                        { 
                            
                            var permiso = $("#ROLE_328-3658");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso)
                            { 
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                            else
                            {
                                window.location="showHistorialAsignacionProvisionalChofer";
                            }
                            
                        }
                    }
                ]
            }
        ], 
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
                id: 'intIdElemento',
                header: 'intIdElemento',
                dataIndex: 'intIdElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'intIdModeloElemento',
                header: 'intIdModeloElemento',
                dataIndex: 'intIdModeloElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'intIdCuadrilla',
                header: 'intIdCuadrilla',
                dataIndex: 'intIdCuadrilla',
                hidden: true,
                hideable: false
            },
            {
                id: 'intIdPerDetalleChofer',
                header: 'intIdPerDetalleChofer',
                dataIndex: 'intIdPerDetalleChofer',
                hidden: true,
                hideable: false
            },
            {
                id: 'strDetalleElementoDISCO',
                header: 'Disco',
                dataIndex: 'strDetalleElementoDISCO',
                width: 80,
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
                header: 'Modelo',
                dataIndex: 'strNombreModeloElemento',
                width: 100,
                sortable: true
            },
            {
                id: 'intIdSolicitudChoferPredefinido',
                header: 'intIdSolicitudChoferPredefinido',
                dataIndex: 'intIdSolicitudChoferPredefinido',
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
                id: 'strHoraInicioChoferPredefinido',
                header: "<p style='text-align:center;line-height:15px;'>Hora Inicio<br> Chofer Predefinido</p>",
                dataIndex: 'strHoraInicioChoferPredefinido',
                width: 100,
                sortable: true
            },
            {
                id: 'strHoraFinChoferPredefinido',
                header: "<p style='text-align:center;line-height:15px;'>Hora Fin<br> Chofer Predefinido</p>",
                dataIndex: 'strHoraFinChoferPredefinido',
                width: 100,
                sortable: true
            },
            {
                id: 'strApellidosChoferPredefinido',
                header: "<p style='text-align:center;line-height:15px;'>Apellidos<br>Chofer Predefinido</p>",
                dataIndex: 'strApellidosChoferPredefinido',
                width: 120,
                sortable: true
            },
            {
                id: 'strNombresChoferPredefinido',
                header: "<p style='text-align:center;line-height:15px;'>Nombres<br>Chofer Predefinido</p>",
                dataIndex: 'strNombresChoferPredefinido',
                width: 120,
                sortable: true
            },
            
            {
                id: 'strIdentificacionChoferPredefinido',
                header: "<p style='text-align:center;line-height:15px;'>Identificación<br>Chofer Predefinido</p>",
                dataIndex: 'strIdentificacionChoferPredefinido',
                width: 100,
                sortable: true
            },
            {
                id: 'strNombreCuadrilla',
                header: "<p style='text-align:center;line-height:15px;'>Asignado a<br> la cuadrilla</p> ",
                dataIndex: 'strNombreCuadrilla',
                width: 100,
                sortable: true
            },
            {
                id: 'strAsignacionVehicularFechaInicio',
                header: "<p style='text-align:center;line-height:15px;'>Fecha Inicio<br>Asignación<br>a la Cuadrilla</p>",
                dataIndex: 'strAsignacionVehicularFechaInicio',
                width:100
            },
            {
                id: 'strAsignacionVehicularHoraInicio',
                header: "<p style='text-align:center;line-height:15px;'>Hora Inicio<br>Asignación<br>a la Cuadrilla</p>",
                dataIndex: 'strAsignacionVehicularHoraInicio',
                width:100
            },
            {
                id: 'strAsignacionVehicularHoraFin',
                header: "<p style='text-align:center;line-height:15px;'>Hora Fin<br>Asignación<br>a la Cuadrilla</p>",
                dataIndex: 'strAsignacionVehicularHoraFin',
                width:100
            },
            {
                id: 'strTurnoInicioCuadrilla',
                header: 'strTurnoInicioCuadrilla',
                dataIndex: 'strTurnoInicioCuadrilla',
                hidden: true,
                hideable: false
            },
            {
                id: 'strTurnoFinCuadrilla',
                header: 'strTurnoFinCuadrilla',
                dataIndex: 'strTurnoFinCuadrilla',
                width: 140,
                hidden: true,
                hideable: false
            },
            {
                id: 'asignacionProvisionalXCuadrilla',
                header: "<p style='text-align:center;line-height:15px;'>Asignación <br>Chofer Provisional<br>Por Cuadrilla</p>",
                dataIndex: 'asignacionProvisionalXCuadrilla',
                hidden:true
            },
            {
                id: 'strDetalleApellidosChofer',
                header: "<p style='text-align:center;line-height:15px;'>Apellidos<br>Chofer Provisional</p>",
                dataIndex: 'strDetalleApellidosChofer',
                width: 120,
                sortable: true
            },
            {
                id: 'strDetalleNombresChofer',
                header: "<p style='text-align:center;line-height:15px;'>Nombres<br>Chofer Provisional</p>",
                dataIndex: 'strDetalleNombresChofer',
                width: 120,
                sortable: true
            },
            {
                id: 'strDetalleIdentificacionChofer',
                header: "<p style='text-align:center;line-height:15px;'>Identificación<br>Chofer Provisional</p>",
                dataIndex: 'strDetalleIdentificacionChofer',
                width: 100,
                sortable: true
            },
            {
                id: 'strDepartamentoAsignacionProvisional',
                header: "<p style='text-align:center;line-height:15px;'>Departamento<br>Asignación<br>Chofer Provisional</p>",
                dataIndex: 'strDepartamentoAsignacionProvisional',
                width:150
            },
            {
                id: 'strZonaAsignacionProvisional',
                header: "<p style='text-align:center;line-height:15px;'>Zona<br>Asignación<br>Chofer Provisional</p>",
                dataIndex: 'strZonaAsignacionProvisional',
                width:150
            },
            {
                id: 'strTareaAsignacionProvisional',
                header: "<p style='text-align:center;line-height:15px;'>Tarea<br>Asignación<br>Chofer Provisional</p>",
                dataIndex: 'strTareaAsignacionProvisional',
                width:150
            },
            {
                id: 'strFechaInicioAsignacionProvisional',
                header: "<p style='text-align:center;line-height:15px;'>Fecha Inicio<br>Asignación<br>Chofer Provisional</p>",
                dataIndex: 'strFechaInicioAsignacionProvisional',
                width:100
            },
            {
                id: 'strFechaFinAsignacionProvisional',
                header: "<p style='text-align:center;line-height:15px;'>Fecha Fin<br>Asignación<br>Chofer Provisional</p>",
                dataIndex: 'strFechaFinAsignacionProvisional',
                width:100
            },
            {
                id: 'strHoraInicioAsignacionProvisional',
                header: "<p style='text-align:center;line-height:15px;'>Hora Inicio<br>Asignación<br>Chofer Provisional</p>",
                dataIndex: 'strHoraInicioAsignacionProvisional',
                width:100
            },
            {
                id: 'strHoraFinAsignacionProvisional',
                header: "<p style='text-align:center;line-height:15px;'>Hora Fin<br>Asignación<br>Chofer Provisional</p>",
                dataIndex: 'strHoraFinAsignacionProvisional',
                width:100
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
                            var strClassButton = 'btn-acciones btn-asignar-chofer';
                            var permiso = $("#ROLE_328-3659");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "")
                            {
                                this.items[0].tooltip = ''; 
                            }   
                            else
                            {
                                if(rec.get('intIdPerDetalleChofer') != "") 
                                {
                                    strClassButton        = '';
                                    this.items[0].tooltip = '';
                                }
                                else 
                                {
                                    this.items[0].tooltip = 'Asignar Chofer Provisional';
                                }

                            }
                            return strClassButton;

                        },
                        tooltip: 'Asignar Chofer Provisional',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var strClassButton = 'btn-acciones btn-asignar-chofer';
                            var permiso = $("#ROLE_328-3659");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton != "")
                            {
                                //Si no hay asignado ya un chofer provisionalmente
                                if(rec.get('intIdPerDetalleChofer') == "") 
                                {
                                    var arrayParametros                             = [];
                                    arrayParametros['idElemento']                   = rec.get('intIdElemento');
                                    arrayParametros['idModeloElemento']             = rec.get('intIdModeloElemento');
                                    arrayParametros['placa']                        = rec.get('strNombreElemento');
                                    arrayParametros['modelo']                       = rec.get('strNombreModeloElemento');
                                    arrayParametros['disco']                        = rec.get('strDetalleElementoDISCO');
                                    arrayParametros['idCuadrilla']                  = rec.get('intIdCuadrilla');
                                    arrayParametros['strNombreCuadrilla']           = rec.get('strNombreCuadrilla');
                                    arrayParametros['strTurnoInicioCuadrilla']      = rec.get('strTurnoInicioCuadrilla');
                                    arrayParametros['strTurnoFinCuadrilla']         = rec.get('strTurnoFinCuadrilla');

                                    arrayParametros['strAsignacionVehicularFechaInicio'] = rec.get('strAsignacionVehicularFechaInicio');
                                    
                                    arrayParametros['strHoraInicioChoferPredefinido']   = rec.get('strHoraInicioChoferPredefinido');
                                    arrayParametros['strHoraFinChoferPredefinido']      = rec.get('strHoraFinChoferPredefinido');
                                    
                                    arrayParametros['choferPredefinido']                = rec.get('strNombresChoferPredefinido')+' '
                                                                                            +rec.get('strApellidosChoferPredefinido');
                                    arrayParametros['idPerChoferAsignadoXVehiculo']     = rec.get('intIdPerDetalleChofer');
                                    arrayParametros['intIdPerChoferPredefinido']        = rec.get('intIdPerChoferPredefinido');
                                    arrayParametros['intIdSolicitudChoferPredefinido']  = rec.get('intIdSolicitudChoferPredefinido');
                                    
                                    if(arrayParametros['intIdSolicitudChoferPredefinido']!="")
                                    {
                                        if(arrayParametros['idCuadrilla']!='')
                                        {
                                            arrayParametros['tipoAsignacion']               = 'CUADRILLA';
                                        }
                                        else
                                        {
                                            /* Si tiene chofer predefinido y no tiene cuadrilla asignada,
                                             * los horarios permitidos estarán de acuerdo al horario de la asignación predefinida
                                             * Se debería pedir motivo
                                             */
                                            arrayParametros['tipoAsignacion']               = 'EMPLEADO';
                                        }
                                    }
                                    else
                                    {
                                        /* Si no hay cuadrilla asociada y tampoco hay asignación predefinida,
                                         * la asignación provisional es directa al vehículo
                                         * los horarios estarán abiertos 
                                         * No se pide un motivo
                                         */
                                        arrayParametros['tipoAsignacion']               = 'VEHICULO';
                                    }
                                    asignacionChoferProvisionalAlVehiculo(arrayParametros);
                                }
                            } 
                        }

                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton = 'btn-acciones btn-eliminar-chofer';
                            var permiso = $("#ROLE_328-3537");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }

                            if (strClassButton == "")
                            {
                               this.items[1].tooltip = ''; 
                            }   
                            else
                            {
                                if(rec.get('intIdPerDetalleChofer') == "") 
                                {
                                    strClassButton        = '';
                                    this.items[1].tooltip = '';
                                }
                                else 
                                {
                                    this.items[1].tooltip = 'Desvincular Chofer Provisional del Vehículo'; 
                                }

                            }

                            return strClassButton;
                        },
                        tooltip: 'Desvincular Chofer Provisional del Vehículo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var strClassButton = 'btn-acciones btn-eliminar-chofer';
                            var permiso = $("#ROLE_328-3537");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = ""; }
                            if (strClassButton != "")
                            {
                                if(rec.get('intIdPerDetalleChofer') != "") 
                                {
                                    var arrayParametros = [];
                                    arrayParametros['idElemento']                       = rec.get('intIdElemento');
                                    arrayParametros['placa']                            = rec.get('strNombreElemento');
                                    arrayParametros['idPerChoferAsignadoXVehiculo']     = rec.get('intIdPerDetalleChofer');
                                    arrayParametros['strDetalleNombresChofer']          = rec.get('strDetalleNombresChofer');
                                    arrayParametros['strDetalleApellidosChofer']        = rec.get('strDetalleApellidosChofer');
                                    arrayParametros['idCuadrilla']                      = rec.get('intIdCuadrilla');
                                    arrayParametros['nombreCuadrilla']                  = rec.get('strNombreCuadrilla');
                                    arrayParametros['asignacionProvisionalXCuadrilla']  = rec.get('asignacionProvisionalXCuadrilla');
                                    
                                    if(arrayParametros['idCuadrilla']!='')
                                    {
                                        if(arrayParametros['asignacionProvisionalXCuadrilla']=="SI")
                                        {
                                            arrayParametros['tipoAsignacion']               = 'CUADRILLA';
                                        }
                                        else
                                        {
                                            arrayParametros['tipoAsignacion']               = 'EMPLEADO';
                                        }
                                        
                                    }
                                    else
                                    {
                                        //Si no hay cuadrilla asociada, se permite escoger fechas y horas para la asignación
                                        //No se pide un motivo
                                        arrayParametros['tipoAsignacion']               = 'EMPLEADO';
                                    }
                                    desvincularChoferDelVehiculo(arrayParametros);
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
                            var strClassButton = 'btn-acciones button-grid-show';
                            var permiso = $("#ROLE_328-3657");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
                            if(!boolPermiso){ strClassButton = "icon-invisible"; }

                            if (strClassButton == "icon-invisible")
                            {
                                this.items[2].tooltip = ''; 
                            }   
                            else
                            {
                                this.items[2].tooltip = 'Ver Historial de Asignaciones Por Vehículo';
                            }

                            return strClassButton;
                        },
                        tooltip: 'Ver Historial de Asignaciones Por Vehículo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            var strClassButton = 'btn-acciones button-grid-show';		
                            var permiso = $("#ROLE_328-3657");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso){ strClassButton = "icon-invisible"; }

                            if(strClassButton!="icon-invisible")
                            {
                                /*if(rec.get('intIdActivoAsignado') != "") 
                                {*/
                                    var rec = store.getAt(rowIndex);
                                    var arrayParametros                                 = [];

                                    arrayParametros['idElemento']                   = rec.get('intIdElemento');
                                    arrayParametros['idModeloElemento']             = rec.get('intIdModeloElemento');
                                    arrayParametros['placa']                        = rec.get('strNombreElemento');
                                    arrayParametros['modelo']                       = rec.get('strNombreModeloElemento');
                                    window.location=arrayParametros['idElemento']+"/showHistorialAsignacionesXVehiculo";
                                //}

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
                {html:"&nbsp;",border:false,width:150},
                {
                    xtype: 'textfield',
                    id: 'strPlaca',
                    fieldLabel: 'Placa',
                    value: '',
                    width: '250',
                    enableKeyEvents: true,
                    listeners:
                    {
                        keyup: function(form, e)
                        {
                            convertirTextoEnMayusculas('strPlaca-inputEl');
                        }
                    }
                },
                {html:"&nbsp;",border:false,width:100},

                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'strNumDisco',
                    fieldLabel: 'Disco',
                    value: '',
                    width: '250'
                },
                {html:"&nbsp;",border:false,width:150},
                {html:"&nbsp;",border:false,width:250},
                {html:"&nbsp;",border:false,width:100},


                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'txtNombreCuadrilla',
                    fieldLabel: 'Nombre de Cuadrilla',
                    value: '',
                    width: '250'
                },
                {html:"&nbsp;",border:false,width:150},
                {html:"&nbsp;",border:false,width:250},
                {html:"&nbsp;",border:false,width:100},

                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'txtNombres',
                    fieldLabel: 'Nombres del Chofer Predefinido',
                    value: '',
                    width: '325'
                },
                {html:"&nbsp;",border:false,width:150},
                {
                    xtype: 'textfield',
                    id: 'txtIdentificacion',
                    fieldLabel: 'Identificacion del Chofer Predefinido',
                    value: '',
                    width: '325'
                },
                {html:"&nbsp;",border:false,width:100},


                {html:"&nbsp;",border:false,width:100},
                {
                    xtype: 'textfield',
                    id: 'txtApellidos',
                    fieldLabel: 'Apellidos del Chofer Predefinido',
                    value: '',
                    width: '325'
                },
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
    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();

    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.placa                 = Ext.getCmp('strPlaca').value;
    store.getProxy().extraParams.disco                 = Ext.getCmp('strNumDisco').value;
    store.getProxy().extraParams.modeloMedioTransporte = cmbModeloMedioTransporte;

    store.getProxy().extraParams.nombreCuadrilla= Ext.getCmp('txtNombreCuadrilla').value;
    store.getProxy().extraParams.nombres        = Ext.getCmp('txtNombres').value;
    store.getProxy().extraParams.apellidos      = Ext.getCmp('txtApellidos').value;
    store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
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

    grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();

    Ext.getCmp('txtNombreCuadrilla').value="";
    Ext.getCmp('txtNombreCuadrilla').setRawValue("");
    Ext.getCmp('txtNombres').value="";
    Ext.getCmp('txtNombres').setRawValue("");
    Ext.getCmp('txtApellidos').value="";
    Ext.getCmp('txtApellidos').setRawValue("");
    Ext.getCmp('txtIdentificacion').value="";
    Ext.getCmp('txtIdentificacion').setRawValue("");

    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.placa                  = Ext.getCmp('strPlaca').value;
    store.getProxy().extraParams.disco                  = Ext.getCmp('strNumDisco').value;
    store.getProxy().extraParams.modeloMedioTransporte  = Ext.getCmp('cmbModeloMedioTransporte').value;

    store.getProxy().extraParams.nombreCuadrilla= Ext.getCmp('txtNombreCuadrilla').value;
    store.getProxy().extraParams.nombres        = Ext.getCmp('txtNombres').value;
    store.getProxy().extraParams.apellidos      = Ext.getCmp('txtApellidos').value;
    store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;

    store.load();
}


function convertirTextoEnMayusculas(idTexto)
{
    var strTexto      = document.getElementById(idTexto).value;
    var strMayusculas = strTexto.toUpperCase(); 

    document.getElementById(idTexto).value = strMayusculas;
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
        //storeChoferes.getProxy().extraParams.idPerChoferAsignadoXVehiculo   = arrayParametros['idPerChoferAsignadoXVehiculo'];
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

    //grid.getPlugin('pagingSelectionPersistence').clearPersistedSelection();


    storeChoferes.currentPage                          = 1;

    storeChoferes.getProxy().extraParams.identificacionChoferDisponible   = 
        Ext.ComponentQuery.query('textfield[name=txtIdentificacionChoferDisponible]')[0].value;  
    storeChoferes.getProxy().extraParams.nombresChoferDisponible          = 
        Ext.ComponentQuery.query('textfield[name=txtNombresChoferDisponible]')[0].value;
    storeChoferes.getProxy().extraParams.apellidosChoferDisponible        = 
        Ext.ComponentQuery.query('textfield[name=txtApellidosChoferDisponible]')[0].value;
    storeChoferes.load();

}

function validarFechasYHoras(cmp,idElemento,tipoAsignacion)
{
    var fieldFechaDesdeAsignacion=Ext.getCmp('fechaDesdeAsignacion');
    var valFechaDesdeAsignacion=fieldFechaDesdeAsignacion.getSubmitValue();

    var fieldFechaHastaAsignacion=Ext.getCmp('fechaHastaAsignacion');
    var valFechaHastaAsignacion=fieldFechaHastaAsignacion.getSubmitValue();

    var fieldHoraDesdeAsignacion = Ext.getCmp('horaInicioAsignacion');
    var valueHoraDesdeAsignacion = fieldHoraDesdeAsignacion.getValue();
    var formattedValueHoraDesdeAsignacion = '';

    var fieldHoraHastaAsignacion = Ext.getCmp('horaFinAsignacion');
    var valueHoraHastaAsignacion = fieldHoraHastaAsignacion.getValue();
    var formattedValueHoraHastaAsignacion = '';

    var boolOKFechas= true;
    var boolOKHoras = true;
    var boolCamposLLenos=false;
    var strMensaje  = '';

    if(valFechaDesdeAsignacion && valFechaHastaAsignacion)
    {
        var valCompFechaDesdeAsignacion = Ext.Date.parse(valFechaDesdeAsignacion, "d/m/Y");
        var valCompFechaHastaAsignacion = Ext.Date.parse(valFechaHastaAsignacion, "d/m/Y");

        if(valCompFechaDesdeAsignacion>valCompFechaHastaAsignacion)
        {
            boolOKFechas=false;
            strMensaje='La Fecha Desde '+ valFechaDesdeAsignacion +' no puede ser mayor a la Fecha Hasta '+valFechaHastaAsignacion;
            Ext.Msg.alert('Atenci\xf3n', strMensaje); 
        }
    }


    
    if(valFechaDesdeAsignacion && valFechaHastaAsignacion && valueHoraDesdeAsignacion && valueHoraHastaAsignacion )
    {
        boolCamposLLenos=true;
    }

    formattedValueHoraDesdeAsignacion = Ext.Date.format(valueHoraDesdeAsignacion, 'H:i');

    formattedValueHoraHastaAsignacion = Ext.Date.format(valueHoraHastaAsignacion, 'H:i');

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


    if(boolOKFechas && boolOKHoras && boolCamposLLenos)
    {
        if(tipoAsignacion=="CUADRILLA" || tipoAsignacion=="EMPLEADO")
        {
            var objExtraParams = storeChoferes.proxy.extraParams;
            objExtraParams.errorFechasHoras          = 0;
            objExtraParams.strFechaDesdeAsignacion   = valFechaDesdeAsignacion;
            objExtraParams.strFechaHastaAsignacion   = valFechaHastaAsignacion;
            objExtraParams.strHoraDesdeAsignacion    = formattedValueHoraDesdeAsignacion;
            objExtraParams.strHoraHastaAsignacion    = formattedValueHoraHastaAsignacion;
            //objExtraParams.idElemento                = Ext.getCmp('fechaHastaAsignacion');

            limpiarChoferes();
        }
        else
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
                        var objExtraParams = storeChoferes.proxy.extraParams;
                        objExtraParams.errorFechasHoras          = 0;
                        objExtraParams.strFechaDesdeAsignacion   = valFechaDesdeAsignacion;
                        objExtraParams.strFechaHastaAsignacion   = valFechaHastaAsignacion;
                        objExtraParams.strHoraDesdeAsignacion    = formattedValueHoraDesdeAsignacion;
                        objExtraParams.strHoraHastaAsignacion    = formattedValueHoraHastaAsignacion;

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
                           msg: text+" o realice la asignación provisional por el horario de asignación predefinida"
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
        
        
        

    }
    else if(!boolOKFechas || !boolOKHoras )
    {
        cmp.value = "";
        cmp.setRawValue("");
        var objExtraParams = storeChoferes.proxy.extraParams;
        objExtraParams.errorFechasHoras=1;
        storeChoferes.load();
    }
}


function asignacionChoferProvisionalAlVehiculo(arrayParametros)
{
    storeTareas = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlTareasAsignacionProvisionalChofer,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdTareaProvisionalChofer',     mapping: 'id_tarea'},
            {name: 'strNombreTareaProvisionalChofer', mapping: 'nombre_tarea'}
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
            url: strUrlZonasAsignacionProvisionalChofer,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdZonaProvisionalChofer',     mapping: 'strValue'},
            {name: 'strNombreZonaProvisionalChofer', mapping: 'strNombre'}
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
            url: strUrlDepartamentosAsignacionProvisionalChofer,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        
        fields:
        [
            {name: 'intIdDepartamentoProvisionalChofer',     mapping: 'strValue'},
            {name: 'strNombreDepartamentoProvisionalChofer', mapping: 'strNombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    
	var idCuadrilla=arrayParametros['idCuadrilla'];

    var turnoHoraCuadrillaInicio=arrayParametros['strTurnoInicioCuadrilla'];
    var turnoHoraCuadrillaFin=arrayParametros['strTurnoFinCuadrilla'];
    var boolAsignarChofer=false;
    
    var idPerChoferPredefinido      = arrayParametros['intIdPerChoferPredefinido'];
    var horaInicioChoferPredefinido = arrayParametros['strHoraInicioChoferPredefinido'];
    var horaFinChoferPredefinido    = arrayParametros['strHoraFinChoferPredefinido'];

    //Reemplazar provisionalmente chofer que pertenece a una cuadrilla
    if(arrayParametros['tipoAsignacion']=='CUADRILLA')
    {
        if(idCuadrilla!="")
        {
            if(turnoHoraCuadrillaInicio!="" && turnoHoraCuadrillaInicio!="")
            {
                boolAsignarChofer=true;
            }
            else
            {
                Ext.Msg.alert('Atenci\xf3n', 'La cuadrilla no cuenta con un horario');
            }
        }
        else
        {
            Ext.Msg.alert('Atenci\xf3n', 'No hay cuadrilla asociada a este vehículo');
        }
    }
    else if(arrayParametros['tipoAsignacion']=='EMPLEADO')
    {
        if(idPerChoferPredefinido!="")
        {
            if(horaInicioChoferPredefinido!="" && horaFinChoferPredefinido!="")
            {
                boolAsignarChofer=true;
            }
            else
            {
                Ext.Msg.alert('Atenci\xf3n', 'La asignación predefinida no cuenta con horario');
            }
        }
        else
        {
            Ext.Msg.alert('Atenci\xf3n', 'No hay chofer predefinido asociado al vehículo');
        }
    }
    else
    {
        boolAsignarChofer=true;
    }



    if(boolAsignarChofer)
    {
        var minValueFecha='';
        var maxValueFecha='';
        var valueFechaInicioAsignacion='';
        var valueFechaFinAsignacion='';


        var minValueHora='';
        var maxValueHora='';
        var valueHoraInicioAsignacion='';
        var valueHoraFinAsignacion='';
        var loadChoferes=false;
        
        var storeMotivos = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlMotivosAsignacionProvisional,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:
                {
                    strAccion: 'asignarChoferAVehiculo', 
                    strModulo: 'asignacion_operativa'
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

        fieldsetMotivoObservacion = new Ext.form.FieldSet(
        {
            xtype: 'fieldset',
            title: 'Motivo de Asignación Provisional',
            width: '100%',
            items: 
            [
                {
                    xtype: 'displayfield',
                    id: 'strChoferPredefinidoAReemplazar',
                    name: 'strChoferPredefinidoAReemplazar',
                    fieldLabel: '<b>Chofer a reemplazar </b>',
                    value: arrayParametros['choferPredefinido'],
                    labelWidth: 150
                },
                {
                    xtype: 'combobox',
                    fieldLabel: '<b>Motivo </b>',
                    id: 'cmbMotivo',
                    name: 'cmbMotivo',
                    store: storeMotivos,
                    displayField: 'strMotivo',
                    valueField: 'intIdMotivo',
                    queryMode: 'remote',
                    emptyText: 'Seleccione',
                    forceSelection: true,
                    labelWidth: 150
                },
                {
                    xtype: 'textareafield',
                    id: 'strObservacionAsignacion',
                    name: 'strObservacionAsignacion',
                    fieldLabel: '<b>Observación </b>',
                    value: '',
                    width:350,
                    rows:2,
                    labelWidth: 150
                }

            ]
        });
        
        
        var dateActual  = new Date();
        var lastDay     = new Date(dateActual.getFullYear(),dateActual.getMonth()+1,0);

        if(arrayParametros['tipoAsignacion']=='CUADRILLA')
        {
            if(arrayParametros['strAsignacionVehicularFechaInicio']!="")
            {
                minValueFecha=arrayParametros['strAsignacionVehicularFechaInicio'];
                maxValueFecha='';
                valueFechaInicioAsignacion=arrayParametros['strAsignacionVehicularFechaInicio'];
                valueFechaFinAsignacion=new Date();
            }
            else
            {
                minValueFecha='';
                maxValueFecha='';
                valueFechaInicioAsignacion='';
                valueFechaFinAsignacion='';
            }

             minValueHora=turnoHoraCuadrillaInicio;
             maxValueHora=turnoHoraCuadrillaFin;
             valueHoraInicioAsignacion=turnoHoraCuadrillaInicio;
             valueHoraFinAsignacion=turnoHoraCuadrillaFin;
             loadChoferes=true;

        }
        else if(arrayParametros['tipoAsignacion']=='EMPLEADO')
        {
            
            minValueFecha=dateActual;
            maxValueFecha='';
            valueFechaInicioAsignacion=dateActual;
            valueFechaFinAsignacion=lastDay;
            
            minValueHora=horaInicioChoferPredefinido;
            maxValueHora=horaFinChoferPredefinido;
            valueHoraInicioAsignacion=horaInicioChoferPredefinido;
            valueHoraFinAsignacion=horaFinChoferPredefinido;
            loadChoferes=true;
        }
        else
        {
            minValueFecha=dateActual;
            maxValueFecha='';
            valueFechaInicioAsignacion=dateActual;
            valueFechaFinAsignacion=lastDay;
            
            minValueHora='00:00';
            maxValueHora='24:00';
            valueHoraInicioAsignacion='';
            valueHoraFinAsignacion='';
        }
        
        DTFechaDesde = new Ext.form.DateField({
                xtype: 'datefield',
                id: 'fechaDesdeAsignacion',
                name:'fechaDesdeAsignacion',
                fieldLabel: '<b>Desde</b>',
                editable: false,
                format: 'd/m/Y',
                minValue: minValueFecha,
                maxValue: maxValueFecha,
                value:valueFechaInicioAsignacion,
                emptyText: "Seleccione",
                labelWidth: 70,
                listeners: {
                    select: function(cmp, newValue, oldValue) {
                        console.log(arrayParametros['idElemento']);
                        validarFechasYHoras(cmp,arrayParametros['idElemento'],arrayParametros['tipoAsignacion']);
                    }
                }
         });

        DTFechaHasta = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'fechaHastaAsignacion',
            name:'fechaHastaAsignacion',
            editable: false,
            fieldLabel: '<b>Hasta</b>',
            format: 'd/m/Y',
            minValue: minValueFecha,
            maxValue: maxValueFecha,
            value:valueFechaFinAsignacion,
            anchor:'100%',
            emptyText: "Seleccione",
            labelWidth: 70,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasYHoras(cmp,arrayParametros['idElemento'],arrayParametros['tipoAsignacion']);
                }
            }
        });  

        DTHoraDesde = new Ext.form.TimeField({
            xtype: 'timefield',
            id: 'horaInicioAsignacion',
            name:'horaInicioAsignacion',
            fieldLabel: '<b>Hora Inicio</b>',
            editable: false,
            minValue: minValueHora,
            maxValue: maxValueHora,
            value: valueHoraInicioAsignacion,
            format: 'H:i',
            emptyText: "Seleccione",
            increment: 15,
            labelWidth: 70,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasYHoras(cmp,arrayParametros['idElemento'],arrayParametros['tipoAsignacion']);
                }
            }
        });


        DTHoraHasta = new Ext.form.TimeField({
            xtype: 'timefield',
            id: 'horaFinAsignacion',
            name: 'horaFinAsignacion',
            editable: false,
            fieldLabel: '<b>Hora Fin</b>',
            minValue: minValueHora,
            maxValue: maxValueHora,
            value: valueHoraFinAsignacion,
            format: 'H:i',
            emptyText: "Seleccione",
            labelWidth: 70,
            increment: 15,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasYHoras(cmp,arrayParametros['idElemento'],arrayParametros['tipoAsignacion']);
                }
            }
        });


        var filterPanelChoferes     = '';
        var TFNombresChofer         = '';
        var TFApellidosChofer       = '';
        var TFIdentificacionChofer  = '';


        var listViewChoferes='';

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
                url: strUrlGetChoferesDisponibles,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    identificacionChoferDisponible:'',
                    nombresChoferDisponible:'',
                    apellidosChoferDisponible:'',
                    idPerChoferAsignadoXVehiculo:'',
                    errorFechasHoras:1
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
                        //El motivo será obligatorio para todos los casos
                        var intIdMotivo = Ext.getCmp('cmbMotivo').getValue();
                        
                        var intIdZona = Ext.getCmp('cmbZonaProvisionalChofer').getValue();
                        var intIdTarea = Ext.getCmp('cmbTareaProvisionalChofer').getValue();
                        var intIdDepartamento = Ext.getCmp('cmbDepartamentoProvisionalChofer').getValue();
                        var strObservacionAsignacion = Ext.getCmp('strObservacionAsignacion').getValue();
                        var idCuadrilla = arrayParametros['idCuadrilla'];
                        var boolAsignarZonaTarea = false;
                        var boolAsignarDepartamento = false;
                        var boolAsignarMotivo       = false;

                        if(!intIdDepartamento)
                        {
                            Ext.Msg.alert('Atenci\xf3n', 'Por favor seleccione un Departamento');
                            return false;
                        }
                        else
                        {
                            boolAsignarDepartamento=true;
                        }
                        

                        var strTipoAsignacionZonaTarea = Ext.getCmp('strTipoAsignacionZonaTarea').getValue();
                        if(strTipoAsignacionZonaTarea=='ZONA')
                        {
                            if(!intIdZona)
                            {
                                Ext.Msg.alert('Atenci\xf3n', 'Por favor seleccione una Zona');
                                return false;
                            }
                            else
                            {
                                boolAsignarZonaTarea=true;
                            }
                        }
                        else if(strTipoAsignacionZonaTarea=='TAREA')
                        {
                            if(!intIdTarea)
                            {
                                Ext.Msg.alert('Atenci\xf3n', 'Por favor seleccione una Tarea');
                                return false;
                            }
                            else
                            {
                                boolAsignarZonaTarea=true;
                            }
                        }
                        
                        if(!intIdMotivo)
                        {
                            Ext.Msg.alert('Atenci\xf3n', 'Por favor seleccione un Motivo');
                            return false;
                        }
                        else
                        {
                            boolAsignarMotivo=true;
                        }
                        
                        if(boolAsignarDepartamento && boolAsignarZonaTarea && boolAsignarMotivo)
                        {
                            var objExtraParams = storeChoferes.proxy.extraParams;

                            var strMensaje="Se asignará el chofer "+rec.data.nombresChofer+" "+rec.data.apellidosChofer;
                            strMensaje+=" al vehículo con placa "+arrayParametros["placa"];
                            Ext.Msg.confirm('Alerta',strMensaje, function(btn)
                            {
                                if(btn=='yes')
                                {
                                    connEsperaAccion.request
                                    ({
                                        url: strUrlAsignacionProvisionalChoferVehiculo,
                                        method: 'post',
                                        dataType: 'json',
                                        params:
                                        { 
                                            idElementoVehiculo              : arrayParametros['idElemento'],
                                            idPersonaEmpresaRolChofer       : rec.data.idPersonaEmpresaRolChofer,
                                            intIdMotivo                     : intIdMotivo,
                                            intIdCuadrilla                  : idCuadrilla,
                                            strNombreCuadrilla              : arrayParametros['strNombreCuadrilla'],
                                            strFechaDesdeAsignacion         : objExtraParams.strFechaDesdeAsignacion,
                                            strFechaHastaAsignacion         : objExtraParams.strFechaHastaAsignacion,
                                            strHoraDesdeAsignacion          : objExtraParams.strHoraDesdeAsignacion,
                                            strHoraHastaAsignacion          : objExtraParams.strHoraHastaAsignacion,
                                            strTipoAsignacion               : arrayParametros['tipoAsignacion'],
                                            strObservacionAsignacion        : strObservacionAsignacion,
                                            intIdSolicitudChoferPredefinido : arrayParametros['intIdSolicitudChoferPredefinido'],
                                            intIdPerChoferPredefinido       : arrayParametros['intIdPerChoferPredefinido'],
                                            intIdPerChoferCuadrilla         : arrayParametros['intIdPerChoferPredefinido'],
                                            intIdDepartamento               : intIdDepartamento,
                                            intIdZona                       : intIdZona,
                                            strTipoAsignacionZonaTarea      : strTipoAsignacionZonaTarea
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


        var formAsignacionProvisional = Ext.create('Ext.form.Panel', {
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
                       title: 'Datos del Vehículo',                       
                       width: '100%',

                       items: 
                       [
                           
                           {
                                xtype: 'combobox',
                                fieldLabel: '<b>Departamento</b>',
                                id: 'cmbDepartamentoProvisionalChofer',
                                name: 'cmbDepartamentoProvisionalChofer',
                                store: storeDepartamentos,
                                displayField: 'strNombreDepartamentoProvisionalChofer',
                                valueField: 'intIdDepartamentoProvisionalChofer',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200
                            },
                            {
                               xtype: 'displayfield',
                               id: 'strTipoAsignacionZonaTarea',
                               name: 'strTipoAsignacionZonaTarea',
                               value: 'ZONA',
                               hidden: true
                            },
                            {
                                xtype: 'radiogroup',
                                fieldLabel: '<b>Asignación Por</b>',
                                labelWidth: 200,
                                items: [
                                    {boxLabel: 'Zona', name: 'tipoAsignacionZonaTarea', inputValue: 'ZONA', checked: true},
                                    {boxLabel: 'Tarea', name: 'tipoAsignacionZonaTarea', inputValue: 'TAREA'}
                                ],
                                listeners: {
                                    change: function(field, newValue, oldValue) {
                                        var value = newValue.tipoAsignacionZonaTarea;
                                        if (Ext.isArray(value)) {
                                            return;
                                        }
                                        Ext.getCmp('strTipoAsignacionZonaTarea').setValue(value);
                                        if(value == 'ZONA')
                                        {
                                            Ext.getCmp('cmbTareaProvisionalChofer').setValue("");
                                            Ext.getCmp('cmbTareaProvisionalChofer').getEl().toggle();
                                            Ext.getCmp('cmbTareaProvisionalChofer').getEl().hide();

                                            Ext.getCmp('cmbZonaProvisionalChofer').setValue("");
                                            Ext.getCmp('cmbZonaProvisionalChofer').getEl().toggle();
                                            Ext.getCmp('cmbZonaProvisionalChofer').getEl().show();
                                        }
                                        else if(value=='TAREA')
                                        {
                                            Ext.getCmp('cmbZonaProvisionalChofer').setValue("");
                                            Ext.getCmp('cmbZonaProvisionalChofer').getEl().toggle();
                                            Ext.getCmp('cmbZonaProvisionalChofer').getEl().hide();

                                            Ext.getCmp('cmbTareaProvisionalChofer').setValue("");
                                            Ext.getCmp('cmbTareaProvisionalChofer').getEl().toggle();
                                            Ext.getCmp('cmbTareaProvisionalChofer').getEl().show();
                                        }

                                    }
                                }
                            },
                            
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Zona</b>',
                                id: 'cmbZonaProvisionalChofer',
                                name: 'cmbZonaProvisionalChofer',
                                store: storeZonas,
                                displayField: 'strNombreZonaProvisionalChofer',
                                valueField: 'intIdZonaProvisionalChofer',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: '<b>Tarea</b>',
                                id: 'cmbTareaProvisionalChofer',
                                name: 'cmbTareaProvisionalChofer',
                                store: storeTareas,
                                displayField: 'strNombreTareaProvisionalChofer',
                                valueField: 'intIdTareaProvisionalChofer',
                                queryMode: 'remote',
                                emptyText: 'Seleccione',
                                forceSelection: true,
                                labelWidth: 200,
                                hidden:true
                            },
                            {
                               xtype: 'displayfield',
                               id: 'strNombreModeloElementoAsignacionProvisional',
                               name: 'strNombreModeloElementoAsignacionProvisional',
                               fieldLabel: '<b>Modelo</b>',
                               value: arrayParametros['modelo'],
                               width: '100%'
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
                                                id: 'strPlacaElementoAsignacionProvisional',
                                                name: 'strPlacaElementoAsignacionProvisional',
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
                                                id: 'strDiscoElementoAsignacionProvisional',
                                                name: 'strDiscoElementoAsignacionProvisional',
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
                   fieldsetMotivoObservacion,
                   {
                       xtype: 'fieldset',
                       title: 'Horario',
                       collapsed: false,
                       collapsible: false,
                       width: 550,
                       items: 
                       [
                           {
                               layout: 'table',
                               border: false,
                               items: 
                               [
                                   {
                                       width: 240,
                                       layout: 'form',
                                       border: false,
                                       labelWidth:50,
                                       items: 
                                       [
                                           DTFechaDesde,
                                           DTHoraDesde
                                       ]
                                   },
                                   {
                                       width: 40,
                                       layout: 'form',
                                       border: false,
                                       items: 
                                       [
                                           {
                                               xtype: 'displayfield'
                                           },
                                           {
                                               xtype: 'displayfield'
                                           }
                                       ]
                                   },
                                   {
                                       width: 240,
                                       layout: 'form',
                                       border: false,
                                       labelWidth:50,
                                       items: 
                                       [
                                           DTFechaHasta,
                                           DTHoraHasta
                                       ]
                                   }
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
          title: 'Asignación Chofer Provisional ',
          modal: true,
          width: 600,
          closable: true,
          layout: 'fit',
          floating: true,
          shadow: true,
          shadowOffset:20,
          resizable:true,
          items: [formAsignacionProvisional]
        }).show();

        if(loadChoferes)
        {
            validarFechasYHoras(null,arrayParametros['idElemento'],arrayParametros['tipoAsignacion']);
        }

    }
}

function desvincularChoferDelVehiculo(arrayParametros)
{
    var storeMotivosEliminarAPChofer = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlMotivosAsignacionProvisional,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                strModulo: 'asignacion_operativa', 
                strAccion: 'eliminarAsignacionVehiculoChofer'
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
    
    
    
    var formPanelEliminarAPChofer = Ext.create('Ext.form.Panel',
    {
        id: 'formEliminarAPChofer',
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
                        fieldLabel: '<b>Motivo de Eliminación de Asignación Chofer Provisional</b>',
                        id: 'cmbMotivoEliminacionAPChofer',
                        name: 'cmbMotivoEliminacionAPChofer',
                        store: storeMotivosEliminarAPChofer,
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
                    var form = Ext.getCmp('formEliminarAPChofer').getForm();

                    if( form.isValid() )
                    {
                        var intIdMotivoEliminarAPChofer = Ext.getCmp('cmbMotivoEliminacionAPChofer').getValue();

                        if ( intIdMotivoEliminarAPChofer != null && intIdMotivoEliminarAPChofer != '' )
                        {
                            var strMensaje = 'Se eliminará la asociación entre el vehículo con placa '+arrayParametros['placa'];
                                strMensaje+= ' y el chofer '+arrayParametros["strDetalleNombresChofer"]+' '+arrayParametros["strDetalleApellidosChofer"];
                                strMensaje += '. Desea continuar?';   
                            Ext.Msg.confirm('Alerta',strMensaje, function(btn)
                            {
                                if(btn=='yes')
                                {
                                    connEsperaAccion.request
                                    ({
                                        url: strUrlEliminarAsignacionProvisionalChoferVehiculo,
                                        method: 'post',
                                        dataType: 'json',
                                        params:
                                        { 
                                            intIdMotivoEliminacionAPChofer  : intIdMotivoEliminarAPChofer,
                                            idElementoVehiculo              : arrayParametros['idElemento'] ,
                                            idPerChoferAsignadoXVehiculo    : arrayParametros['idPerChoferAsignadoXVehiculo'],
                                            strTipoAsignacion               : arrayParametros['tipoAsignacion'],
                                            intIdCuadrilla                  : arrayParametros['idCuadrilla'],
                                            nombreCuadrilla                 : arrayParametros['nombreCuadrilla']

                                        },
                                        success: function(result)
                                        {
                                            
                                            var strResult = result.responseText;
                                            var strMensajeChofer='';
                                            Ext.Msg.alert('Información',result.responseText);
                                            if ( typeof winMotivo != 'undefined' && winMotivo != null )
                                            {
                                                winMotivo.destroy();
                                            }
                                            
                                            
                                            if ( typeof win != 'undefined' && win != null )
                                            {
                                                win.destroy();
                                            }

                                            if( strResult=="OK" )
                                            {
                                                var strMensajeEliminarAsignacion = 'Se eliminó la asociación entre el vehículo con placa '+arrayParametros['placa'];
                                                    strMensajeEliminarAsignacion+= ' y el chofer '+arrayParametros["strDetalleNombresChofer"]+' '
                                                                                    +arrayParametros["strDetalleApellidosChofer"];

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
                            });
                            
                            
                            
                            
                        }
                        else
                        {
                            Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un motivo por la eliminación de la Asignación Provisional del Chofer');
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
         title: 'Eliminar Asignación Chofer Provisional',
         modal: true,
         width: 350,
         closable: true,
         layout: 'fit',
         items: [formPanelEliminarAPChofer]
    }).show();
    
}



