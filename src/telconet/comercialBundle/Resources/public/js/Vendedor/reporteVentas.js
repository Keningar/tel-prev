Ext.onReady(function() 
{
    var modelVentas = Ext.define('VentasModel',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                { name: 'intIdServicio',         mapping: 'intIdServicio' },
                { name: 'strLoginPunto',         mapping: 'strLoginPunto' },
                { name: 'strEstadoServicio',     mapping: 'strEstadoServicio' },
                { name: 'strDireccion',          mapping: 'strDireccion' },
                { name: 'strPlan',               mapping: 'strPlan' },
                { name: 'strNombreJurisdiccion', mapping: 'strNombreJurisdiccion' },
                { name: 'strSector',             mapping: 'strSector' },
                { name: 'strCliente',            mapping: 'strCliente' },
                { name: 'strEmpresa',            mapping: 'strEmpresa' },
                { name: 'strIdentificacion',     mapping: 'strIdentificacion' },
                { name: 'strVendedor',           mapping: 'strVendedor' },
                { name: 'strUsuarioVendedor',    mapping: 'strUsuarioVendedor' },
                { name: 'strFechaAprobacion',    mapping: 'strFechaAprobacion' },
                { name: 'strFechaCreacionPunto', mapping: 'strFechaCreacionPunto' },
                { name: 'strFechaActivacion',    mapping: 'strFechaActivacion' },
                { name: 'strCoordenadas',        mapping: 'strCoordenadas' },
                { name: 'strPrecioVenta',        mapping: 'strPrecioVenta' },
                { name: 'strMotivoRechazo',      mapping: 'strMotivoRechazo' },
                { name: 'strObservacion',        mapping: 'strObservacion' }
            ]
        });
        
        
    var storeReporteVentas = new Ext.data.Store
        ({
            pageSize: 40,
            total: 'total',
            model: modelVentas,
            proxy: 
            {
                type: 'ajax',
                url: strUrlGetVentas,
                timeout: 900000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
        });

    var gridReporteVentas = Ext.create('Ext.grid.Panel',
        {
            width: 1150,
            height: 510,
            store: storeReporteVentas,
            loadMask: true,
            iconCls: 'icon-grid',
            layout:'fit',
            viewConfig: 
            {
                emptyText: 'No hay datos para mostrar',
                enableTextSelection: true,
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
            layoutConfig: 
            {
                align: 'middle'
            },
            columns: 
            [            
                new Ext.grid.RowNumberer(),
                {
                    header: 'ServicioId',
                    dataIndex: 'intIdServicio',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Login',
                    dataIndex: 'strLoginPunto',
                    width: 100,
                    sortable: true
                },
                {
                    header: 'Estado<br/>Servicio',
                    dataIndex: 'strEstadoServicio',
                    width: 80,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Plan',
                    dataIndex: 'strPlan',
                    width: 150,
                    sortable: true
                },
                {
                    header: 'Identificación',
                    dataIndex: 'strIdentificacion',
                    width: 100,
                    sortable: true
                },
                {
                    header: 'Nombre Cliente',
                    dataIndex: 'strCliente',
                    width: 200,
                    sortable: true
                },
                {
                    header: 'Empresa',
                    dataIndex: 'strEmpresa',
                    width: 100,
                    sortable: true
                },
                {
                    header: 'Nombre<br/>Jurisdicción',
                    dataIndex: 'strNombreJurisdiccion',
                    width: 100,
                    sortable: true
                },
                {
                    header: 'Sector',
                    dataIndex: 'strSector',
                    width: 100,
                    sortable: true
                },
                {
                    header: 'Dirección',
                    dataIndex: 'strDireccion',
                    width: 150,
                    sortable: true
                },
                {
                    header: 'Fecha<br/>Aprobación',
                    dataIndex: 'strFechaAprobacion',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Fecha<br/>Creación Punto',
                    dataIndex: 'strFechaCreacionPunto',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Fecha<br/>Activación',
                    dataIndex: 'strFechaActivacion',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Motivo<br/>Rechazo',
                    dataIndex: 'strMotivoRechazo',
                    width: 120,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Observaciones',
                    dataIndex: 'strObservacion',
                    width: 250,
                    align: 'center',
                    sortable: true
                }
            ],
            title: 'Reporte de Ventas del Vendedor',
            bbar: Ext.create('Ext.PagingToolbar', 
            {
                store: storeReporteVentas,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'gridReporteVentas'
        });
        
    
    
    /*
     * Filtros de Búsqueda
     */
    //Combo Login del Punto
    var storeCmbLoginPunto = new Ext.data.Store
        ({ 
            total: 'total',
            proxy:
            {
                timeout: 400000,
                type: 'ajax',
                url : strUrlGetPuntosClientes,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name:'id_cliente', mapping:'id_cliente'},
                {name:'cliente',    mapping:'cliente'}
            ],
            listeners:
            {
                load: function(store, records)
                {
                     store.insert
                     (0, 
                        [{
                            cliente: '&nbsp;',
                            id_cliente: null
                        }]
                    );

                    Ext.getCmp('cmbLoginPunto').queryMode = 'remote';
                }      
            }
        });
    
    var comboLoginPunto = Ext.create('Ext.form.ComboBox',
        {
            id: 'cmbLoginPunto',
            name: 'cmbLoginPunto',
            fieldLabel: 'Login del Punto',
            emptyText: "Digite",
            labelWidth: 110,
            labelPad: 10,
            labelAlign : 'right',
            width: 350,
            displayField: 'cliente',
            valueField: 'id_cliente',
            store: storeCmbLoginPunto,
            queryMode: "remote",
            listClass: 'x-combo-list-small'
        });
    //Fin Combo Login del Punto
    
    
    //Estados del Servicio
    var storeEstadosServicios = new Ext.data.Store
        ({ 
            total: 'total',
            proxy: 
            {
                timeout: 400000,                
                type: 'ajax',
                url : strUrlGetEstadosSerrvicios,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name:'estado_servicio', mapping:'estado_servicio'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0, [{ estado_servicio: '&nbsp;' }]);
                 
                     Ext.getCmp('cmbEstadoServicio').queryMode = 'local';
                }      
            }
        });
    //Fin Estados del Servicio
    
    
    //Planes
    Ext.define('modelPlanes', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'intIdPlan',     type: 'integer'},
            {name: 'strNombrePlan', type: 'string'}
        ]
    });
    
    var storePlanes = Ext.create('Ext.data.Store', 
        {
            model: "modelPlanes",
            proxy: 
            {
                type: 'ajax',
                url: urlGetPlanes,
                timeout: 120000,
                reader:
                {
                    type: 'json',
                    root: 'jsonPlanes'
                }
            },
            autoLoad: true
        });
        
    var cmbPlanes = new Ext.form.ComboBox
        ({
            xtype: 'combobox',
            store: storePlanes,
            id: 'cmbPlanes',
            name: 'cmbPlanes',
            valueField: 'intIdPlan',
            displayField: 'strNombrePlan',
            fieldLabel: 'Plan',
            width: 350,
            labelAlign : 'right',
            labelWidth: 110,
            labelPad: 10,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: true
        });
    //Fin Planes
    
    
    //Combo Empresas
    var storeEmpresas = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetEmpresas,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:
                {
                    app: 'TELCOS'                    
                }
            },
            fields:
            [
                {name: 'opcion',    mapping: 'nombre_empresa'},
                {name: 'valor',     mapping: 'prefijo'}
            ]
        });
    
    
    var comboEmpresa;
    
    if(boolAutoloadJurisdiccion)
    {
        comboEmpresa = {width: 350, border: false};
    }
    else
    {
        comboEmpresa = Ext.create('Ext.form.ComboBox',
        {
            id:'cmbEmpresa',
            store: storeEmpresas,
            displayField: 'opcion',
            valueField: 'valor',
            fieldLabel: 'Empresa',
            width: 350,
            labelAlign : 'right',
            labelWidth: 110,
            labelPad: 10,
            queryMode: "remote",
            emptyText: '',
            listeners:
            {
                select: function(combo)
                {
                    Ext.getCmp('cmbSector').reset();
                    Ext.getCmp('cmbSector').setDisabled(true);
                    
                    Ext.getCmp('cmbJurisdiccion').reset();
                    Ext.getCmp('cmbJurisdiccion').setDisabled(false);

                    var objExtraParams = storeJurisdicciones.proxy.extraParams;

                    objExtraParams.empresa = Ext.getCmp('cmbEmpresa').getValue();

                    storeJurisdicciones.load();
                }
            }
        });
    }
    //Fin Combo Empresas
    
    
    //Combo Jurisdicciones
    var storeJurisdicciones = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetJurisdicciones,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:
                {
                    empresa: strPrefijoEmpresa
                }
            },
            fields:
            [
                {name: 'opcion', mapping: 'strNombreJurisdiccion'},
                {name: 'valor',  mapping: 'intIdJurisdiccion'}
            ],
            autoLoad: boolAutoloadJurisdiccion
        });
    
    var comboJurisdiccion = Ext.create('Ext.form.ComboBox',
        {
            id:'cmbJurisdiccion',
            store: storeJurisdicciones,
            displayField: 'opcion',
            valueField: 'valor',
            fieldLabel: 'Jurisdicción',
            width: 350,
            labelAlign : 'right',
            labelWidth: 110,
            labelPad: 10,
            queryMode: "remote",
            emptyText: '',
            disabled: boolFitroEmpresa,
            listeners:
            {
                select: function(combo)
                {
                    Ext.getCmp('cmbSector').reset();
                    Ext.getCmp('cmbSector').setDisabled(false);

                    var objExtraParams = storeSectores.proxy.extraParams;

                    objExtraParams.jurisdiccion = Ext.getCmp('cmbJurisdiccion').getValue();
                    
                    if(boolAutoloadJurisdiccion === false)
                    {
                        objExtraParams.empresa = Ext.getCmp('cmbEmpresa').getValue();
                    }
                    else
                    {
                        objExtraParams.empresa = strPrefijoEmpresa;
                    }

                    storeSectores.load();
                }
            }
        });
    //Fin Combo Jurisdicciones
    
    
    //Combo Sector
    var storeSectores = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetSectores,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name: 'opcion', mapping: 'strNombreSector'},
                {name: 'valor',  mapping: 'intIdSector'}
            ]
        });
    
    var comboSector = Ext.create('Ext.form.ComboBox',
        {
            id:'cmbSector',
            store: storeSectores,
            displayField: 'opcion',
            valueField: 'valor',
            fieldLabel: 'Sector',
            width: 350,
            labelAlign : 'right',
            labelWidth: 110,
            labelPad: 10,
            queryMode: "remote",
            emptyText: '',
            disabled: true
        });
    //Fin Combo Sector
    
    
    var filterReporteVentasSupervisor = Ext.create('Ext.panel.Panel',
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
            collapsible: true,
            collapsed: false,
            width: 1150,
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
                {width: 150, border: false},
                comboLoginPunto,
                {width: 100, border: false},
                {
                    xtype: 'textfield',
                    id: 'txtIdentificacionCliente',
                    fieldLabel: 'Identificación Cliente',
                    value: '',
                    labelWidth: 110,
                    labelPad: 10,
                    labelAlign : 'right',
                    width: 350,
                    maxLength: 13,
                    hideTrigger:true
                },
                {width: 150, border: false},
                {width: 150, border: false},
                {
                    xtype: 'combobox',
                    id: 'cmbEstadoServicio',
                    name: 'cmbEstadoServicio',
                    fieldLabel: 'Estado del Servicio',
                    labelWidth: 110,
                    labelPad: 10,
                    labelAlign : 'right',
                    emptyText: "Seleccione",
                    width:350,
                    triggerAction: 'all',
                    displayField:'estado_servicio',
                    valueField: 'estado_servicio',
                    selectOnTab: true,
                    store: storeEstadosServicios,              
                    lazyRender: true,
                    queryMode: "remote",
                    listClass: 'x-combo-list-small',
                    listeners:
                    {
                        select:
                        {
                            fn:function(comp, record, index)
                            {
                                if (comp.getValue() === "" || comp.getValue() === "&nbsp;")
                                {
                                    comp.setValue(null);
                                }
                            }
                        }
                     }
                },
                {width: 100, border: false},
                {
                    xtype: 'textfield',
                    id: 'txtNombreCliente',
                    fieldLabel: 'Nombre Cliente',
                    value: '',
                    labelWidth: 110,
                    labelPad: 10,
                    labelAlign : 'right',
                    width: 350
                },
                {width: 150, border: false},
                {width: 150, border: false},
                cmbPlanes,
                {width: 100, border: false},
                {
                    xtype: 'textfield',
                    id: 'txtApellidoCliente',
                    fieldLabel: 'Apellido Cliente',
                    value: '',
                    labelWidth: 110,
                    labelPad: 10,
                    labelAlign : 'right',
                    width: 350
                },
                {width: 150, border: false},
                {width: 150, border: false, height: 20},
                {width: 350, border: false, height: 20},
                {width: 100, border: false, height: 20},
                {width: 350, border: false, height: 20},
                {width: 150, border: false, height: 20},
                {width: 150, border: false},
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Fecha Aprobación',
                    labelWidth: 110,
                    labelPad: 10,
                    labelAlign : 'right',
                    items: 
                    [
                        {
                            xtype: 'datefield',
                            width: 230,
                            id: 'feAprobacionDesde',
                            name: 'feAprobacionDesde',
                            fieldLabel: 'Desde:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 230,
                            id: 'feAprobacionHasta',
                            name: 'feAprobacionHasta',
                            fieldLabel: 'Hasta:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {width: 100, border: false},
                comboEmpresa,
                {width: 150, border: false},
                {width: 150, border: false},
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Fecha Creación Punto',
                    labelWidth: 110,
                    labelPad: 10,
                    labelAlign : 'right',
                    items: 
                    [
                        {
                            xtype: 'datefield',
                            width: 230,
                            id: 'feCreacionPuntoDesde',
                            name: 'feCreacionPuntoDesde',
                            fieldLabel: 'Desde:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 230,
                            id: 'feCreacionPuntoHasta',
                            name: 'feCreacionPuntoHasta',
                            fieldLabel: 'Hasta:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {width: 100, border: false},
                comboJurisdiccion,
                {width: 150, border: false},
                {width: 150, border: false},
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Fecha Activación',
                    labelWidth: 110,
                    labelPad: 10,
                    labelAlign : 'right',
                    items: 
                    [
                        {
                            xtype: 'datefield',
                            width: 230,
                            id: 'feActivacionDesde',
                            name: 'feActivacionDesde',
                            fieldLabel: 'Desde:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 230,
                            id: 'feActivacionHasta',
                            name: 'feActivacionHasta',
                            fieldLabel: 'Hasta:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {width: 100, border: false},
                comboSector,
                {width: 150, border: false},
                {width: 150, border: false, height: 20},
                {width: 350, border: false, height: 20},
                {width: 100, border: false, height: 20},
                {width: 350, border: false, height: 20},
                {width: 150, border: false, height: 20},
                {width: 150, border: false},
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Fecha Planificación',
                    labelWidth: 110,
                    labelPad: 10,
                    labelAlign : 'right',
                    items: 
                    [
                        {
                            xtype: 'datefield',
                            width: 230,
                            id: 'fePlanificacionDesde',
                            name: 'fePlanificacionDesde',
                            fieldLabel: 'Desde:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 230,
                            id: 'fePlanificacionHasta',
                            name: 'fePlanificacionHasta',
                            fieldLabel: 'Hasta:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {width: 100, border: false},
                {width: 350, border: false},
                {width: 150, border: false},
                {width: 150, border: false, height: 20},
                {width: 350, border: false, height: 20},
                {width: 100, border: false, height: 20},
                {width: 350, border: false, height: 20},
                {width: 150, border: false, height: 20},
                {width: 150, border: false}
            ],
            renderTo: 'filtros'
        });    


    function buscar() 
    {
        var boolContinuar = true;
        
        if(boolAutoloadJurisdiccion === false)
        {
            if( Ext.getCmp('cmbEmpresa').getValue() == null && Ext.getCmp('cmbJurisdiccion').getValue() == null )
            {
                boolContinuar = false;
            }
        }
        else
        {
            if( Ext.getCmp('cmbJurisdiccion').getValue() == null )
            {
                boolContinuar = false;
            }
        }
        
        if( Ext.getCmp('cmbLoginPunto').getValue()            != null ||
            Ext.getCmp('cmbEstadoServicio').getValue()        != null ||
            Ext.getCmp('txtNombreCliente').getValue()         != ''   ||
            Ext.getCmp('txtApellidoCliente').getValue()       != ''   ||
            Ext.getCmp('cmbPlanes').getValue()                != null ||
            Ext.getCmp('cmbSector').getValue()                != null ||
            Ext.getCmp('txtIdentificacionCliente').getValue() != ''   ||
            Ext.getCmp('feAprobacionDesde').getValue()        != null ||
            Ext.getCmp('feAprobacionHasta').getValue()        != null ||
            Ext.getCmp('feCreacionPuntoDesde').getValue()     != null ||
            Ext.getCmp('feCreacionPuntoHasta').getValue()     != null ||
            Ext.getCmp('feActivacionDesde').getValue()        != null ||
            Ext.getCmp('feActivacionHasta').getValue()        != null ||
            Ext.getCmp('fePlanificacionDesde').getValue()     != null ||
            Ext.getCmp('fePlanificacionHasta').getValue()     != null ||
            boolContinuar
          )
        {
            boolContinuar = true;
            
            if( Ext.getCmp('fePlanificacionDesde').getValue() != null || Ext.getCmp('fePlanificacionHasta').getValue() != null )
            {
                if( Ext.getCmp('fePlanificacionDesde').getValue() > Ext.getCmp('fePlanificacionHasta').getValue() )
                {
                    boolContinuar = false;
                    
                    Ext.Msg.show
                    ({
                        title:'Error en Busqueda',
                        msg: 'La Fecha de Planificación Inicial debe ser menor o igual a la Fecha de Planificación Final.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            
            if( Ext.getCmp('feAprobacionDesde').getValue() != null || Ext.getCmp('feAprobacionHasta').getValue() != null )
            {
                if( Ext.getCmp('feAprobacionDesde').getValue() > Ext.getCmp('feAprobacionHasta').getValue() )
                {
                    boolContinuar = false;
                    
                    Ext.Msg.show
                    ({
                        title:'Error en Busqueda',
                        msg: 'La Fecha de Aprobación Inicial debe ser menor o igual a la Fecha de Aprobación Final.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            
            if( Ext.getCmp('feCreacionPuntoDesde').getValue() != '' || Ext.getCmp('feCreacionPuntoHasta').getValue() != '' )
            {
                if( Ext.getCmp('feCreacionPuntoDesde').getValue() > Ext.getCmp('feCreacionPuntoHasta').getValue() )
                {
                    boolContinuar = false;
                    
                    Ext.Msg.show
                    ({
                        title:'Error en Busqueda',
                        msg: 'La Fecha de Creación de Punto Inicial debe ser menor o igual a la Fecha de Creación de Punto Final.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            
            if( Ext.getCmp('feActivacionDesde').getValue() != '' || Ext.getCmp('feActivacionHasta').getValue() != '' )
            {
                if( Ext.getCmp('feActivacionDesde').getValue() > Ext.getCmp('feActivacionHasta').getValue() )
                {
                    boolContinuar = false;
                    
                    Ext.Msg.show
                    ({
                        title:'Error en Busqueda',
                        msg: 'La Fecha de Activación Inicial debe ser menor o igual a la Fecha de Activación Final.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            
            if( boolContinuar )
            {
                storeReporteVentas.loadData([],false);
                cargarFiltrosBusquedaAlStore();
                storeReporteVentas.currentPage = 1;
                storeReporteVentas.load();
            }
        }
        else
        {
            Ext.Msg.show
            ({
                title:'Error en Busqueda',
                msg: 'Debe escoger al menos un criterio de búsqueda.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }


    function limpiar() 
    {
        Ext.getCmp('cmbPlanes').value = null;
        Ext.getCmp('cmbPlanes').setRawValue(null);
        Ext.getCmp('cmbSector').value = null;
        Ext.getCmp('cmbSector').setRawValue(null);
        Ext.getCmp('cmbLoginPunto').value = null;
        Ext.getCmp('cmbLoginPunto').setRawValue(null);
        Ext.getCmp('cmbJurisdiccion').value = null;
        Ext.getCmp('cmbJurisdiccion').setRawValue(null);
        Ext.getCmp('txtNombreCliente').value = "";
        Ext.getCmp('txtNombreCliente').setRawValue("");
        Ext.getCmp('feAprobacionDesde').value = null;
        Ext.getCmp('feAprobacionDesde').setRawValue(null);
        Ext.getCmp('feAprobacionHasta').value = null;
        Ext.getCmp('feAprobacionHasta').setRawValue(null);
        Ext.getCmp('feActivacionDesde').value = null;
        Ext.getCmp('feActivacionDesde').setRawValue(null);
        Ext.getCmp('feActivacionHasta').value = null;
        Ext.getCmp('feActivacionHasta').setRawValue(null);
        Ext.getCmp('cmbEstadoServicio').value = null;
        Ext.getCmp('cmbEstadoServicio').setRawValue(null);
        Ext.getCmp('txtApellidoCliente').value = "";
        Ext.getCmp('txtApellidoCliente').setRawValue("");
        Ext.getCmp('feCreacionPuntoDesde').value = null;
        Ext.getCmp('feCreacionPuntoDesde').setRawValue(null);
        Ext.getCmp('feCreacionPuntoHasta').value = null;
        Ext.getCmp('feCreacionPuntoHasta').setRawValue(null);
        Ext.getCmp('fePlanificacionDesde').value = null;
        Ext.getCmp('fePlanificacionDesde').setRawValue(null);
        Ext.getCmp('fePlanificacionHasta').value = null;
        Ext.getCmp('fePlanificacionHasta').setRawValue(null);
        Ext.getCmp('txtIdentificacionCliente').value = "";
        Ext.getCmp('txtIdentificacionCliente').setRawValue("");

        if(boolAutoloadJurisdiccion === false)
        {
            Ext.getCmp('cmbEmpresa').value = null;
            Ext.getCmp('cmbEmpresa').setRawValue(null);
            Ext.getCmp('cmbJurisdiccion').setDisabled(true);
        }
        
        Ext.getCmp('cmbSector').reset();
        Ext.getCmp('cmbSector').setDisabled(true);
        
        Ext.getCmp('cmbJurisdiccion').reset();

        storeReporteVentas.loadData([],false);
    }
    
    
    function cargarFiltrosBusquedaAlStore()
    {
        storeReporteVentas.getProxy().extraParams.idPunto         = Ext.getCmp('cmbLoginPunto').value;
        storeReporteVentas.getProxy().extraParams.estadoServicio  = Ext.getCmp('cmbEstadoServicio').value;
        storeReporteVentas.getProxy().extraParams.nombreCliente   = Ext.getCmp('txtNombreCliente').value;
        storeReporteVentas.getProxy().extraParams.apellidoCliente = Ext.getCmp('txtApellidoCliente').value;
        storeReporteVentas.getProxy().extraParams.idPlan          = Ext.getCmp('cmbPlanes').value;
        
        if(boolAutoloadJurisdiccion)
        {
            storeReporteVentas.getProxy().extraParams.prefijoEmpresa = strPrefijoEmpresa;
        }
        else
        {
            storeReporteVentas.getProxy().extraParams.prefijoEmpresa = Ext.getCmp('cmbEmpresa').value;
        }
        
        storeReporteVentas.getProxy().extraParams.idJurisdiccion        = Ext.getCmp('cmbJurisdiccion').value;
        storeReporteVentas.getProxy().extraParams.idSector              = Ext.getCmp('cmbSector').value;
        storeReporteVentas.getProxy().extraParams.identificacionCliente = Ext.getCmp('txtIdentificacionCliente').value;
        storeReporteVentas.getProxy().extraParams.feAprobacionDesde     = Ext.getCmp('feAprobacionDesde').value;
        storeReporteVentas.getProxy().extraParams.feAprobacionHasta     = Ext.getCmp('feAprobacionHasta').value;
        storeReporteVentas.getProxy().extraParams.feCreacionPuntoDesde  = Ext.getCmp('feCreacionPuntoDesde').value;
        storeReporteVentas.getProxy().extraParams.feCreacionPuntoHasta  = Ext.getCmp('feCreacionPuntoHasta').value;
        storeReporteVentas.getProxy().extraParams.feActivacionDesde     = Ext.getCmp('feActivacionDesde').value;
        storeReporteVentas.getProxy().extraParams.feActivacionHasta     = Ext.getCmp('feActivacionHasta').value;
        storeReporteVentas.getProxy().extraParams.fePlanificacionDesde  = Ext.getCmp('fePlanificacionDesde').value;
        storeReporteVentas.getProxy().extraParams.fePlanificacionHasta  = Ext.getCmp('fePlanificacionHasta').value;
    }
    
});


