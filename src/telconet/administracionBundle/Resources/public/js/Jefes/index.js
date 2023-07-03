Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var store              = '';
var intItemsPerPage    = 10;
var win                = null;
var storeCargos        = null;
var storeJefes         = null;
var boolOcultarColumna = false;
var intWidthNombres    = 230;

Ext.onReady(function()
{
    if( !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "TN" )
    {
        boolOcultarColumna = true;
        intWidthNombres    = 370;
    }
    
    Ext.tip.QuickTipManager.init();
    
    var strNombre = new Ext.form.TextField(
    {
        id: 'nombre',
        fieldLabel: 'Nombre',
        xtype: 'textfield'
    });
    
    var strApellido = new Ext.form.TextField(
    {
        id: 'apellido',
        fieldLabel: 'Apellido',
        xtype: 'textfield'
    });
    
    storeCargosNoVisibles = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetCargos,
            timeout: 900000,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                strNombreArea: strNombreArea
            }
        },
        fields:
        [
            {name: 'intIdCargo',     mapping: 'intIdCargo'},
            {name: 'strNombreCargo', mapping: 'strNombreCargo'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombreCargo: 'Todos', intIdCargo: ''}]);
            }      
        }
    });
    
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {
                name: 'intIdPersonaEmpresaRol', 
                type: 'string', 
                mapping: 'intIdPersonaEmpresaRol'
            },
            {
                name: 'strEmpleado',
                type: 'string', 
                mapping: 'strEmpleado'
            },
            {
                name: 'strReportaA',
                type: 'string', 
                mapping: 'strReportaA'
            },
            {
                name: 'strIdReportaA',
                type: 'string', 
                mapping: 'strIdReportaA'
            },
            {
                name: 'strCargo',
                type: 'string', 
                mapping: 'strCargo'
            },
            {
                name: 'strCargoNaf',
                type: 'string', 
                mapping: 'strCargoNaf'
            },
            {
                name: 'strMetaBruta',
                type: 'string', 
                mapping: 'strMetaBruta'
            },
            {
                name: 'strMetaActiva',
                type: 'string', 
                mapping: 'strMetaActiva'
            },
            {
                name: 'intMetaActivaValor',
                type: 'string', 
                mapping: 'intMetaActivaValor'
            },
            {
                name: 'intTotalEmpleadosAsignados',
                type: 'string',
                mapping: 'intTotalEmpleadosAsignados'
            },
            {
                name: 'boolEsJefe',
                type: 'string',
                mapping: 'boolEsJefe'
            },
            {
                name: 'boolEsSupervisor',
                type: 'string',
                mapping: 'boolEsSupervisor'
            },
            {
                name: 'intIdCargoTelcos',
                type: 'int',
                mapping: 'intIdCargoTelcos'
            },
            {
                name: 'intTotalCargosJefes',
                type: 'int',
                mapping: 'intTotalCargosJefes'
            },
            {
                name: 'boolHabilitarComoJefe',
                type: 'string',
                mapping: 'boolHabilitarComoJefe'
            },
            {
                name: 'strEsAsistente',
                type: 'string',
                mapping: 'strEsAsistente'
            },
            {
                name: 'boolSoloAsis',
                type: 'string',
                mapping: 'boolSoloAsis'
            }
        ],
        idProperty: 'login'
    });

    store = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: intItemsPerPage,
        proxy:
        {
            type: 'ajax',
            url: strUrlGrid,
            timeout: 900000,
            reader:
            {
                type: 'json',
                root: 'usuarios',
                totalProperty: 'total'
            },
            extraParams:
            {
                nombre: '', 
                apellido: '',
                strNombreArea: strNombreArea
            },
            simpleSortMode: true
        },
        listeners:
        {
            beforeload: function(store) 
            {
                store.getProxy().extraParams.nombre   = Ext.getCmp('nombre').getValue();
                store.getProxy().extraParams.apellido = Ext.getCmp('apellido').getValue();
                
                var strCargo =  Ext.getCmp('cmbCargo').getValue();
        
                if(strCargo == 'Todos')
                {
                    store.getProxy().extraParams.cargo = '';
                }
                else
                {
                    store.getProxy().extraParams.cargo = strCargo;
                }
            },
            load: function(store) 
            {
                store.each(function(record) {});
            }
        },
        autoLoad: true
    });

    listView = Ext.create('Ext.grid.Panel', 
    {
        id: 'grid',
        width: 1180,
        autoheight: true,
        renderTo: Ext.get('jefes'),
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando usuarios {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar',
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
            new Ext.grid.RowNumberer(),
            {
                id: 'intIdPersonaEmpresaRol',
                header: 'intIdPersonaEmpresaRol',
                dataIndex: 'intIdPersonaEmpresaRol',
                hidden: true,
                hideable: false
            }, 
            {
                text: 'Nombres y Apellidos',
                width: intWidthNombres,
                dataIndex: 'strEmpleado'
            },
            {
                text: 'Reporta A',
                width: 230,
                dataIndex: 'strReportaA'
            },
            {
                text: 'Cargo NAF',
                width: 150,
                dataIndex: 'strCargoNaf'
            },
            {
                text: 'Cargo Telcos',
                width: 100,
                dataIndex: 'strCargo'
            },
            {
                text: '# Empleados Asignados',
                width: 130,
                align: 'center',
                dataIndex: 'intTotalEmpleadosAsignados'
            },
            {
                text: 'Meta Bruta',
                width: 70,
                dataIndex: 'strMetaBruta',
                align: 'center',
                hidden: boolOcultarColumna
            },
            {
                text: 'Meta Activa',
                width: 70,
                dataIndex: 'intMetaActivaValor',
                align: 'center',
                hidden: boolOcultarColumna
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 175,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassA             = "btn-acciones btn-habilitar-jefe";
                            var boolEsJefe            = rec.data.boolEsJefe;
                            var boolSoloAsis          = rec.data.boolSoloAsis;
                            var boolHabilitarComoJefe = rec.data.boolHabilitarComoJefe;

                            if( !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "MD" )
                            {
                                if( boolEsJefe == 'S' )
                                {
                                    strClassA = 'icon-invisible';
                                }
                            }
                            else if( rec.data.intTotalCargosJefes == 0 || boolHabilitarComoJefe == 'N' || boolSoloAsis == 'S' )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[0].tooltip = '';
                            }
                            else
                            {
                                this.items[0].tooltip = 'Habilitar como Jefe';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                   = store.getAt(rowIndex);
                            var strClassA             = "btn-acciones btn-habilitar-jefe";
                            var boolEsJefe            = rec.data.boolEsJefe;
                            var boolHabilitarComoJefe = rec.data.boolHabilitarComoJefe;
                            var strCargosJefes        = '';
                            
                            if( !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "MD" )
                            {
                                if( boolEsJefe == 'S' )
                                {
                                    strClassA = 'icon-invisible';
                                }
                            }
                            else if( rec.data.intTotalCargosJefes == 0 || boolHabilitarComoJefe == 'N' )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA != "icon-invisible")
                            {
                                if( !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "TN" )
                                {
                                    strCargosJefes = "ES_JEFE";
                                }
                                
                                habilitarJefe(rec.data.intIdPersonaEmpresaRol, rec.data.strCargo, strCargosJefes);
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
                            var strClassA = "btn-acciones btn-asignar-jefe";
                            var boolSoloAsis = rec.data.boolSoloAsis;
                            if (strClassA == "icon-invisible" || boolSoloAsis == 'S' )
                            {
                                this.items[1].tooltip = '';
                                strClassA = 'icon-invisible';
                            }
                            else
                            {
                                this.items[1].tooltip = 'Asignar/Quitar Jefe';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec       = store.getAt(rowIndex);
                            var strClassA = "btn-acciones btn-asignar-jefe";
                            var strCargo  = rec.data.strCargo;
                            
                            if (strClassA != "icon-invisible")
                            {
                                var intIdPersonaEmpresaRol = rec.data.intIdPersonaEmpresaRol;
                                var strIdReportaA          = rec.data.strIdReportaA;
                                
                                var arrayParametros                           = [];
                                    arrayParametros['intIdPersonaEmpresaRol'] = intIdPersonaEmpresaRol;
                                    arrayParametros['strIdReportaA']          = strIdReportaA;
                                    arrayParametros['strCargo']               = strCargo;
                                    
                                asignarJefe( arrayParametros );
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
                            var strClassA = "btn-acciones btn-asignar-meta";

                            if( !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "TN" )
                            {
                                strClassA = "icon-invisible";
                            }
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[2].tooltip = '';
                            }
                            else
                            {
                                this.items[2].tooltip = 'Asignar Meta';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                    = store.getAt(rowIndex);
                            var strClassA              = "btn-acciones btn-asignar-meta";
                            var strMetaBruta           = rec.data.strMetaBruta;
                            var strMetaActiva          = rec.data.strMetaActiva;
                            var intIdPersonaEmpresaRol = rec.data.intIdPersonaEmpresaRol;

                            if( !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "TN" )
                            {
                                strClassA = "icon-invisible";
                            }
                            
                            if (strClassA != "icon-invisible")
                            {
                                var arrayParametros                             = [];
                                    arrayParametros['intIdPersonalEmpresaRol']  = intIdPersonaEmpresaRol;
                                    arrayParametros['strMetaBruta']             = strMetaBruta;
                                    arrayParametros['strMetaActiva']            = strMetaActiva;
                                    arrayParametros['valor']                    = strMetaBruta+'|'+strMetaActiva;
                                    arrayParametros['accion']                   = 'Guardar';
                                    arrayParametros['store']                    = store;
                                    
                                asignarMeta( arrayParametros );
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
                            var strClassA              = "btn-acciones btn-deshabilitar-jefe";
                            var strCargo               = rec.data.strCargo;
                            var intEmpleadosAsignados  = rec.data.intTotalEmpleadosAsignados;
                            var boolEsSupervisor       = rec.data.boolEsSupervisor;
                            var boolSoloAsis           = rec.data.boolSoloAsis;
                            if( strCargo == 'Empleado' || strCargo == 'Vendedor' || strCargo == 'Jefe' || intEmpleadosAsignados > 0 
                                || boolEsSupervisor == 'S' || boolSoloAsis == 'S' )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[3].tooltip = '';
                            }
                            else
                            {
                                this.items[3].tooltip = 'Deshabilitar como:<br>'+strCargo;
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                    = store.getAt(rowIndex);
                            var strClassA              = "btn-acciones btn-deshabilitar-jefe";
                            var strCargo               = rec.data.strCargo;
                            var intIdPersonaEmpresaRol = rec.data.intIdPersonaEmpresaRol;
                            var intEmpleadosAsignados  = rec.data.intTotalEmpleadosAsignados;
                            var boolEsSupervisor       = rec.data.boolEsSupervisor;
                            
                            if( strCargo == 'Empleado' || strCargo == 'Vendedor' || strCargo == 'Jefe' || intEmpleadosAsignados > 0
                                || boolEsSupervisor == 'S' )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA != "icon-invisible")
                            {
                                var arrayParametros                             = [];
                                    arrayParametros['tipo']                     = 'Cargo';
                                    arrayParametros['intIdPersonalEmpresaRol']  = intIdPersonaEmpresaRol;
                                    arrayParametros['valor']                    = '';
                                    arrayParametros['caracteristica']           = strCaracteristicaCargo;
                                    arrayParametros['accion']                   = 'Eliminar';
                                    arrayParametros['store']                    = store;
                                    arrayParametros['cargoEmpleado']            = strCargo;
                                
                                deshabilitarComoJefe(arrayParametros);
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
                            var strClassA              = "btn-acciones btn-cambiar-asignacion-jefe";
                            var strCargo               = rec.data.strCargo;
                            var intEmpleadosAsignados  = rec.data.intTotalEmpleadosAsignados;
                            var boolSoloAsis           = rec.data.boolSoloAsis;
                            if( strCargo == 'Empleado' || strCargo == 'Vendedor' || intEmpleadosAsignados == 0 || boolSoloAsis == 'S' )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[4].tooltip = '';
                            }
                            else
                            {
                                this.items[4].tooltip = 'Cambiar de '+strCargo+' a<br>los empleados asignados';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                     = store.getAt(rowIndex);
                            var strClassA               = "btn-acciones btn-cambiar-asignacion-jefe";
                            var strCargo                = rec.data.strCargo;
                            var intEmpleadosAsignados   = rec.data.intTotalEmpleadosAsignados;
                            
                            if( strCargo == 'Empleado' || strCargo == 'Vendedor' || intEmpleadosAsignados == 0 )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA != "icon-invisible")
                            {
                                var intIdPersonaEmpresaRol = rec.data.intIdPersonaEmpresaRol;
                                var strEmpleado            = rec.data.strEmpleado;
                                
                                var arrayParametros                           = [];
                                    arrayParametros['intIdPersonaEmpresaRol'] = intIdPersonaEmpresaRol;
                                    arrayParametros['strIdReportaA']          = intIdPersonaEmpresaRol;
                                    arrayParametros['strCargo']               = strCargo;
                                    arrayParametros['jefeActual']             = strEmpleado;
                                
                                cambiarJefeAsignado( arrayParametros );
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
                            var strClassA     = "btn-acciones btn-asignar-empleados";
                            var boolEsJefe    = rec.data.boolEsJefe;
                            var boolSoloAsis  = rec.data.boolSoloAsis;
                            if( boolEsJefe == 'N' || boolSoloAsis == 'S' )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[5].tooltip = '';
                            }
                            else
                            {
                                this.items[5].tooltip = 'Asignar Empleados';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec       = store.getAt(rowIndex);
                            var strClassA = "btn-acciones btn-asignar-empleados";
                            var boolEsJefe  = rec.data.boolEsJefe;
                            
                            if( boolEsJefe == 'N' )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA != "icon-invisible")
                            {
                                mostrarVistaAsignarEmpleados(rec);
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
                            var strClassA   = "btn-acciones btn-asignar-vendedor";
                            var boolEsAsist = rec.data.strEsAsistente;

                            if(boolPermiso == 'S' && boolEsAsist == 'S' )
                            { 
                                this.items[6].tooltip = 'Asignar Vendedores';
                            }                           
                            else
                            {
                                strClassA = 'icon-invisible';
                                this.items[6].tooltip = '';                                                                
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var strClassA   = "btn-acciones btn-asignar-vendedor";
                            var rec         = store.getAt(rowIndex);
                            var boolEsAsist = rec.data.strEsAsistente;
                            
                            if(boolPermiso == 'S' && boolEsAsist == 'S' )
                            {                                
                                mostrarVistaAsignarEmpleados(rec);
                            }
                            else
                            {
                                strClassA = 'icon-invisible';
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');                                                                
                            }
                            
                        }
                    }
                ]
            }
        ]
    });

    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        collapsible: true,
        collapsed: true,
        width: 1180,
        title: 'Criterios de busqueda',
        layout: 
        {
            type: 'table',
            columns: 6,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        defaults: 
        {
            bodyStyle: 'padding:10px'
        },
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: Buscar
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: Limpiar
            }

        ],
        items: 
        [
            strNombre,
            {
                html: "&nbsp;",
                border: false,
                width: 50
            },
            strApellido,
            {
                html: "&nbsp;",
                border: false,
                width: 50
            },
            {
                xtype: 'combobox',
                fieldLabel: 'Cargos:',
                id: 'cmbCargo',
                name: 'cmbCargo',
                store: storeCargosNoVisibles,
                displayField: 'strNombreCargo',
                valueField: 'strNombreCargo',
                queryMode: 'remote',
                emptyText: 'Seleccione',
                forceSelection: true
            },
            {
                html: "&nbsp;",
                border: false,
                width: 50
            },
        ],
        renderTo: 'criteriosBusqueda'
    });
    
    
    var myMask = new Ext.LoadMask
        (
           Ext.getCmp('grid').el,
           {
               msg:"Cargando..."
           }
        );

    Ext.Ajax.on('beforerequest', myMask.show, myMask);
    Ext.Ajax.on('requestcomplete', myMask.hide, myMask);
    Ext.Ajax.on('requestexception', myMask.hide, myMask);


    function Buscar() 
    {
        if( Ext.getCmp('nombre').getValue() == '' && Ext.getCmp('apellido').getValue() == '' && Ext.getCmp('cmbCargo').getValue() == null )
        {
            Ext.Msg.alert('Error', 'No se puede realizar la búsqueda porque los campos Nombres y Apellidos están vacíos.');
        }
        else
        {
            store.loadData([],false);
            store.currentPage = 1;
            store.load();
        }
    }

    function Limpiar()
    {
        Ext.getCmp('nombre').setValue('');
        Ext.getCmp('apellido').setValue('');
        Ext.getCmp('cmbCargo').setValue('');
        
        store.loadData([],false);
        store.currentPage = 1;
        store.load();
    }
    
    function habilitarJefe(intIdPersonaEmpresaRol, strCargo, strCargosJefes)
    {
        var strCampoValorComboCargos = 'strNombreCargo';
        
        if( strPrefijoEmpresa == "TN" )
        {
            strCampoValorComboCargos = 'intIdCargo';
        }
        
        storeCargosVisibles = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetCargos,
                timeout: 900000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:
                {
                    strEsVisible: 'SI',
                    strNombreArea: strNombreArea,
                    strCargo: strCargo,
                    strCargosJefes: strCargosJefes
                }
            },
            fields:
            [
                {name: 'intIdCargo',     mapping: 'intIdCargo'},
                {name: 'strNombreCargo', mapping: 'strNombreCargo'}
            ]
        });
        
        
        var formPanel = Ext.create('Ext.form.Panel',
            {
                id: 'formHabilitarJefe',
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
                                xtype: 'displayfield',
                                fieldLabel: 'Cargo Actual:',
                                name: 'cargoActual',
                                value: strCargo
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Cargos:',
                                id: 'comboCargos',
                                name: 'comboCargos',
                                store: storeCargosVisibles,
                                displayField: 'strNombreCargo',
                                valueField: strCampoValorComboCargos,
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
                        text: 'Habilitar',
                        type: 'submit',
                        handler: function()
                        {
                            var form = Ext.getCmp('formHabilitarJefe').getForm();

                            if( form.isValid() )
                            {
                                var strNombreCargo = Ext.getCmp('comboCargos').getValue();

                                if ( strNombreCargo != null && strNombreCargo != '' )
                                {
                                    var arrayParametros                             = [];
                                        arrayParametros['intIdPersonalEmpresaRol']  = intIdPersonaEmpresaRol;
                                        arrayParametros['valor']                    = strNombreCargo;
                                        arrayParametros['caracteristica']           = strCaracteristicaCargo;
                                        arrayParametros['accion']                   = 'Guardar';
                                        arrayParametros['store']                    = store;
                                        
                                    ajaxAsignarCaracteristica(arrayParametros);
                                }
                                else
                                {
                                    Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un Cargo');
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
                   title: 'Habilitar como Jefe',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }
    
    
    function crearStoreJefes( arrayParametros )
    {
        storeJefes = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetJefes,
                timeout: 900000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'usuarios'
                },
                extraParams:
                {
                    strSoloJefes: 'S',
                    strExceptoUsr: arrayParametros['intIdPersonaEmpresaRol']+'|'+arrayParametros['strIdReportaA'],
                    strCargo: arrayParametros['strCargo'],
                    strNombreArea: strNombreArea
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
                            strEmpleado: 'Sin Asignación',
                            intIdPersonaEmpresaRol: null
                        }
                     ]);
                }      
            }
        });
    }
    
    
    function asignarJefe( arrayParametros )
    {
        var intIdReportaA = arrayParametros['strIdReportaA'];
        
        crearStoreJefes(arrayParametros);
            
        var formPanel = Ext.create('Ext.form.Panel',
            {
                id: 'formAsignarJefe',
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
                                fieldLabel: 'Jefe:',
                                id: 'comboJefe',
                                name: 'comboJefe',
                                store: storeJefes,
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
                        text: 'Asignar/Quitar',
                        type: 'submit',
                        handler: function()
                        {
                            var form = Ext.getCmp('formAsignarJefe').getForm();

                            if( form.isValid() )
                            {
                                var intIdJefe              = Ext.getCmp('comboJefe').getValue();
                                var strIdPersonaEmpresaRol = arrayParametros['intIdPersonaEmpresaRol'];
                                
                                if( !Ext.isEmpty(intIdReportaA) || !Ext.isEmpty(intIdJefe) )
                                {
                                    if( strIdPersonaEmpresaRol != null && strIdPersonaEmpresaRol != '' )
                                    {
                                        Ext.Ajax.request
                                        ({
                                            url: strUrlCambioJefe,
                                            method: 'post',
                                            dataType: 'json',
                                            params:
                                            { 
                                                intIdJefe: intIdJefe,
                                                strIdPersonaEmpresaRol: strIdPersonaEmpresaRol
                                            },
                                            success: function(response)
                                            {
                                                if( response.responseText == 'OK')
                                                {
                                                    if( Ext.isEmpty(intIdJefe) )
                                                    {
                                                        Ext.Msg.alert('Información', 'Se desvinculó el Jefe asignado con éxito');
                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.alert('Información', 'Se ha asignado a un nuevo Jefe con éxito');
                                                    }

                                                    win.destroy();
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Error', 'Hubo un problema al asignar el jefe'); 
                                                }

                                                store.load();
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error',result.responseText); 
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un Jefe');
                                    }//( strIdPersonaEmpresaRol != null && strIdPersonaEmpresaRol != '' )
                                }
                                else
                                {
                                    Ext.Msg.alert('Atenci\xf3n', 'El empleado seleccionado no tiene Jefe asignado.');
                                }//( !Ext.isEmpty(intIdReportaA) || !Ext.isEmpty(intIdJefe) )
                            }//( form.isValid() )
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
                   title: 'Asignar/Quitar Jefe',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }
    
    
    function cambiarJefeAsignado( arrayParametros )
    {
        crearStoreJefes( arrayParametros );
        
        var formPanel = Ext.create('Ext.form.Panel',
            {
                id: 'formAsignarJefe',
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
                                xtype: 'displayfield',
                                id: 'strJefeActual',
                                name: 'strJefeActual',
                                fieldLabel: 'Jefe Actual',
                                value: arrayParametros['jefeActual']
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Jefe Nuevo',
                                id: 'comboJefe',
                                name: 'comboJefe',
                                store: storeJefes,
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
                            var form = Ext.getCmp('formAsignarJefe').getForm();

                            if( form.isValid() )
                            {
                                var intIdJefe              = Ext.getCmp('comboJefe').getValue();
                                var strIdPersonaEmpresaRol = arrayParametros['intIdPersonaEmpresaRol'];

                                if ( strIdPersonaEmpresaRol != null && strIdPersonaEmpresaRol != '' )
                                {
                                    Ext.Ajax.request
                                    ({
                                        url: strUrlCambioJefe,
                                        method: 'post',
                                        dataType: 'json',
                                        params:
                                        { 
                                            intIdJefe: intIdJefe,
                                            strIdPersonaEmpresaRol: strIdPersonaEmpresaRol,
                                            strAccion: 'cambioJefeEmpleadosAsignados',
                                            strNombreArea: strNombreArea
                                        },
                                        success: function(response)
                                        {
                                            if( response.responseText == 'OK')
                                            {
                                                Ext.Msg.alert('Información', 'Se ha asignado a un nuevo Jefe con éxito');
                                                win.destroy();
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error', 'Hubo un problema al asignar el jefe'); 
                                            }
                                            
                                            store.load();
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error',result.responseText); 
                                        }
                                    });
                                }
                                else
                                {
                                    Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un Jefe');
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
                   title: 'Asignar Jefe',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }
    
    
    function deshabilitarComoJefe( arrayParametros )
    {
        Ext.Msg.confirm('Alerta', 'Está seguro que desea deshabilitar como '+arrayParametros["cargoEmpleado"]+'?', function(btn) 
        {
            if (btn == 'yes')
            {
                ajaxAsignarCaracteristica(arrayParametros);
            }
        });
    }
    
    
    function mostrarVistaAsignarEmpleados(rec)
    {
        $('#itemStrCargo').val( rec.data.strCargo );
        $('#itemStrCargoNaf').val( rec.data.strCargoNaf );
        $('#itemStrMetaBruta').val( rec.data.strMetaBruta );
        $('#itemStrMetaActiva').val( rec.data.strMetaActiva );
        $('#itemIntIdCargoTelcos').val( rec.data.intIdCargoTelcos );
        $('#itemIntMetaActivaValor').val( rec.data.intMetaActivaValor );
        $('#itemIntIdPersonaEmpresaRol').val( rec.data.intIdPersonaEmpresaRol );
        $('#itemStrEsAsistente').val( rec.data.strEsAsistente );

        document.forms[0].submit();		
    }
});