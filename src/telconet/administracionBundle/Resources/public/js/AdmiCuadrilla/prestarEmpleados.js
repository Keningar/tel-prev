Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var store           = '';
var intItemsPerPage = 15;
var win             = null;
var storeCargos     = null;
var storeJefes      = null;

Ext.onReady(function()
{		
    Ext.tip.QuickTipManager.init();
    
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
    
    var strNombre = new Ext.form.TextField(
    {
        id: 'nombre',
        fieldLabel: 'Nombre',
        xtype: 'textfield',
        width: 350
    });
    
    var strApellido = new Ext.form.TextField(
    {
        id: 'apellido',
        fieldLabel: 'Apellido',
        xtype: 'textfield',
        width: 350
    });
    
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            { name: 'intIdPersonaEmpresaRol',    type: 'string', mapping: 'intIdPersonaEmpresaRol' },
            { name: 'strEmpleado',               type: 'string', mapping: 'strEmpleado' },
            { name: 'strCargo',                  type: 'string', mapping: 'strCargo' },
            { name: 'strCuadrillaAsignada',      type: 'string', mapping: 'strCuadrillaAsignada' },
            { name: 'strEstadoEmpleado',         type: 'string', mapping: 'strEstadoEmpleado' },
            { name: 'intIdPersonaPrestamo',      type: 'string', mapping: 'intIdPersonaPrestamo' },
            { name: 'strNombrePersonaPrestamo',  type: 'string', mapping: 'strNombrePersonaPrestamo' },
            { name: 'strFechaPrestamo',          type: 'string', mapping: 'strFechaPrestamo' }
        ],
        idProperty: 'intIdPersonaEmpresaRol'
    });

    store = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: intItemsPerPage,
        proxy:
        {
            type: 'ajax',
            url: strUrlGrid,
            reader:
            {
                type: 'json',
                root: 'usuarios',
                totalProperty: 'total'
            },
            extraParams:
            {
                strExceptoUsr: intIdPersonaEmpresaRol,
                strsignadosA: intIdPersonaEmpresaRol,
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
        width: 950,
        height: 370,
        renderTo: Ext.get('empleadosAsignados'),
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
                        beforeshow: function updateTipBody(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
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
                width: 320,
                dataIndex: 'strEmpleado'
            },
            {
                text: 'Cargo',
                width: 180,
                dataIndex: 'strCargo'
            },
            {
                text: 'Cuadrilla Asignada',
                width: 185,
                dataIndex: 'strCuadrillaAsignada'
            },
            {
                text: 'Estado',
                width: 160,
                dataIndex: 'strEstadoEmpleado'
            },
            {
                header: 'Acciones',
                align: 'center',
                xtype: 'actioncolumn',
                width: 80,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton    = 'btn-acciones btn-asignar-prestar-cuadrilla';
                            var strEstadoEmpleado = rec.get('strEstadoEmpleado');
                            this.items[0].tooltip = 'Prestar Empleado';
                            
                            if (strEstadoEmpleado != "Disponible")
                            {
                                this.items[0].tooltip = '';
                                strClassButton        = '';
                            }
                            
                            return strClassButton;
                        },
                        tooltip: 'Prestar Empleado',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec               = store.getAt(rowIndex);
                            var strEstadoEmpleado = rec.get('strEstadoEmpleado');
                            
                            if (strEstadoEmpleado == "Disponible")
                            {
                                var arrayParametros                       = [];
                                arrayParametros['accion']                 = 'prestar';
                                arrayParametros['intIdPersonaEmpresaRol'] = rec.get('intIdPersonaEmpresaRol');

                                prestarEmpleado(arrayParametros);
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton    = '';
                            var strEstadoEmpleado = rec.get('strEstadoEmpleado');
                            this.items[1].tooltip = '';
                            
                            if (strEstadoEmpleado == "Es prestamo")
                            {
                                this.items[1].tooltip = 'Devolver Empleado';
                                strClassButton        = 'btn-acciones btn-asignar-devolver-cuadrilla';
                            }
                            
                            return strClassButton;
                        },
                        tooltip: 'Devolver Empleado',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec               = store.getAt(rowIndex);
                            var strEstadoEmpleado = rec.get('strEstadoEmpleado');
                            
                            if (strEstadoEmpleado == "Es prestamo")
                            {
                                var arrayParametros                       = [];
                                arrayParametros['accion']                 = 'devolver';
                                arrayParametros['intIdPersonaEmpresaRol'] = rec.get('intIdPersonaEmpresaRol');
                                arrayParametros['coordinadorPrestado']    = '';

                                cambioCoordinadorEmpleado(arrayParametros);
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton    = '';
                            var strEstadoEmpleado = rec.get('strEstadoEmpleado');
                            this.items[2].tooltip = '';
                            
                            if ( strEstadoEmpleado == "Prestado" || strEstadoEmpleado == "Es prestamo" 
                                 || strEstadoEmpleado == "Se presto cuadrilla" || strEstadoEmpleado == "Prestamo de cuadrilla")
                            {
                                this.items[2].tooltip = 'Ver Información del Empleado Prestado';
                                strClassButton        = 'button-grid-show';
                            }
                            
                            return strClassButton;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec               = store.getAt(rowIndex);
                            var strEstadoEmpleado = rec.get('strEstadoEmpleado');
                            
                            var arrayParametros                           = [];
                                arrayParametros['strEstadoPrestamo']      = rec.get('strEstadoEmpleado');
                                arrayParametros['strCoordinadorPrestamo'] = rec.get('strNombrePersonaPrestamo');
                                arrayParametros['dateFechaPrestamo']      = rec.get('strFechaPrestamo');
                            
                            if (strEstadoEmpleado == "Prestado" || strEstadoEmpleado == "Se presto cuadrilla")
                            {
                                arrayParametros['labelCoordinador'] = 'Coordinador del Préstamo';

                                verInformacionCoordinador(arrayParametros);
                            }
                            else if (strEstadoEmpleado == "Es prestamo" || strEstadoEmpleado == "Prestamo de cuadrilla")
                            {
                                arrayParametros['labelCoordinador'] = 'Coordinador Principal';

                                verInformacionCoordinador(arrayParametros);
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassButton    = '';
                            var strEstadoEmpleado = rec.get('strEstadoEmpleado');
                            this.items[3].tooltip = '';
                            
                            console.log(strNombreDepartamento);

                            if (strEstadoEmpleado == "Prestado" &&
                                strNombreDepartamento == 'Operaciones Urbanas')
                            {
                                this.items[3].tooltip = 'Recuperar Empleado';
                                strClassButton        = 'btn-acciones btn-asignar-devolver-cuadrilla';
                            }
                            
                            return strClassButton;
                        },
                        tooltip: 'Recuperar Empleado',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec               = store.getAt(rowIndex);
                            var strEstadoEmpleado = rec.get('strEstadoEmpleado');
                            
                            if (strEstadoEmpleado == "Prestado")
                            {
                                var arrayParametros                       = [];
                                arrayParametros['accion']                 = 'recuperar';
                                arrayParametros['intIdPersonaEmpresaRol'] = rec.get('intIdPersonaEmpresaRol');
                                arrayParametros['coordinadorPrestado']    = '';

                                cambioCoordinadorEmpleado(arrayParametros);
                            }
                        }
                    },
                ]
            }
        ]
    });

    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 5,
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
        collapsible: true,
        collapsed: true,
        width: 950,
        title: 'Criterios de busqueda',
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
            {
                html: "&nbsp;",
                border: false,
                width: 100
            },
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
                width: 100
            }
        ],
        renderTo: 'criteriosBusqueda'
    });
    
    
    var myMask = new Ext.LoadMask(Ext.getCmp('grid').el,{ msg:"Cargando..." });

    Ext.Ajax.on('beforerequest', myMask.show, myMask);
    Ext.Ajax.on('requestcomplete', myMask.hide, myMask);
    Ext.Ajax.on('requestexception', myMask.hide, myMask);


    function Buscar() 
    {
        if( Ext.getCmp('nombre').getValue() == '' && Ext.getCmp('apellido').getValue() == '' )
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
        
        store.loadData([],false);
        store.currentPage = 1;
        store.load();
    }
    
    
    function prestarEmpleado(arrayParametros)
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
                        strSoloJefes: 'N', 
                        strExceptoUsr: intIdPersonaEmpresaRol,
                        strFiltroCargo: strCargo,
                        strNombreArea: strNombreArea,
                        strAccion: 'prestamo_cuadrilla'
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

        var formPanel = Ext.create('Ext.form.Panel',
        {
            id: 'formCambiarCoordinadorEmpleado',
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
                        var form = Ext.getCmp('formCambiarCoordinadorEmpleado').getForm();

                        if( form.isValid() )
                        {
                            var intIdCoordinador = Ext.getCmp('comboCoordinador').getValue();

                            if ( intIdCoordinador != null && intIdCoordinador != '' )
                            {
                                arrayParametros['coordinadorPrestado'] = intIdCoordinador;

                                cambioCoordinadorEmpleado(arrayParametros);
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
                   title: 'Prestar Empleado',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }
    
    
    function cambioCoordinadorEmpleado(arrayParametros)
    {
        Ext.Msg.confirm('Alerta','Está seguro que desea '+arrayParametros['accion']+' el empleado seleccionado. Desea continuar?', function(btn)
        {
            if(btn=='yes')
            {
                connEsperaAccion.request
                ({
                    url: strUrlCambioCoordinadorEmpleado,
                    method: 'post',
                    dataType: 'json',
                    params:
                    { 
                        empleados : arrayParametros['intIdPersonaEmpresaRol'],
                        accion : arrayParametros['accion'],
                        coordinadorPrestado: arrayParametros['coordinadorPrestado']
                    },
                    success: function(result)
                    {
                        if( "OK" == result.responseText  )
                        {
                            if( arrayParametros['accion'] == "prestar" )
                            {
                                Ext.Msg.alert('Información', 'Se prestó el empleado con éxito');
                            }
                            else if( arrayParametros['accion'] == "devolver" )
                            {
                                Ext.Msg.alert('Información', 'Se devolvió el empleado con éxito');
                            }
                            else
                            {
                                Ext.Msg.alert('Información', 'Se cambio el estado de la cuadrilla con éxito');
                            }

                            store.load();
                        }
                        else
                        {
                            Ext.Msg.alert('Error ', result.responseText);
                        }

                        if ( typeof win != 'undefined' && win != null )
                        {
                            win.destroy();
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
    
    
    function verInformacionCoordinador(arrayParametros)
    {
        var formPanel = Ext.create('Ext.form.Panel',
        {
            id: 'formInfoCoordinadorPrestado',
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
                            id: 'strEstadoPrestamo',
                            name: 'strEstadoPrestamo',
                            fieldLabel: '<b>Estado</b>',
                            value: arrayParametros['strEstadoPrestamo']
                        },
                        {
                            xtype: 'displayfield',
                            id: 'strCoordinadorPrestamo',
                            name: 'strCoordinadorPrestamo',
                            fieldLabel: '<b>'+arrayParametros['labelCoordinador']+'</b>',
                            value: arrayParametros['strCoordinadorPrestamo']
                        },
                        {
                            xtype: 'displayfield',
                            id: 'dateFechaPrestamo',
                            name: 'dateFechaPrestamo',
                            fieldLabel: '<b>Fecha del Préstamo</b>',
                            value: arrayParametros['dateFechaPrestamo']
                        },
                    ]
                }
            ],
            buttons:
            [
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
                   title: 'Información del Empleado Prestado',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }
});