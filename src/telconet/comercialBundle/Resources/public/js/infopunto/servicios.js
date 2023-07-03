Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

let objFieldStyle = {
    'backgroundColor': '#F0F2F2',
    'backgrodunImage': 'none'
};

var itemsPerPage                = 10;
var store                       = '';
var estado_id                   = '';
var area_id                     = '';
var login_id                    = '';
var tipo_asignacion             = '';
var pto_sucursal                = '';
var boolHiddenFechaRenovacion   = true;
var intContadorInicial          = 0;
var listView;
var idClienteSucursalSesion;
var storePersonalComisionista;
var gridPlantillaComisionista;
var windowsPlantillaComisionista;
var winMotivoEliminarServicio;
var jsonDatos = null;
var strEmpresaPermitida       = 'TN';
var intIdComisionistaSelected = 0;
var objValidarSolicitudes     = new Ext.data.Connection
var registro = null;
({
    listeners:
    {
        'beforerequest': 
        {
            fn: function (con, opt)
            {						
                Ext.MessageBox.show
                ({
                   msg: 'Validando la información...',
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

var connCoordinar = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});

var connActCaracteristicas = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        }
    }
});

var connDescPresentaFactura = new Ext.data.Connection({
	listeners: {
		'beforerequest': {
			fn: function (con, opt) {						
				Ext.MessageBox.show({
				   msg: 'Grabando los datos, Por favor espere!!',
				   progressText: 'Guardando...',
				   width:300,
				   wait:true,
				   waitConfig: {interval:200}
				});				
			},
			scope: this
		}
	}
});

Ext.onReady(function() {
    strFlagActivaSimu = document.getElementById("strFlagActivacionSim").value;
    
    storeMotivosAnulacion = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_motivo_anulacion,
            timeout: 120000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'ACTIVE'
            }
        },
        fields:
            [
                {name: 'id_motivo', mapping: 'id_motivo'},
                {name: 'nombre_motivo', mapping: 'nombre_motivo'}
            ],
        autoLoad: true
    });
    
     //Store que nos devuelve una lista de los motivos para eliminación  de una orden de servicio.
    storeMotivosEliminacion = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_motivo_eliminacion,
            timeout: 120000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'ACTIVE'
            }
        },
        fields:
            [
                {name: 'id_motivo', mapping: 'id_motivo'},
                {name: 'nombre_motivo', mapping: 'nombre_motivo'}
            ],
        autoLoad: true
    });

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });


    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelEstado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idestado', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });

    var estado_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url: url_cliente_lista_estados,
            reader: {
                type: 'json',
                root: 'estados'
            }
        }
    });

    var estado_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: estado_store,
        labelAlign: 'left',
        id: 'idestado',
        name: 'idestado',
        valueField: 'descripcion',
        displayField: 'descripcion',
        fieldLabel: 'Estado',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        queryMode: 'local',
        allowBlank: true,
        listeners: {
            select:
                function(e) {
                    estado_id = Ext.getCmp('idestado').getValue();        
                },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function() {
                    estado_id = '';
                    estado_store.removeAll();
                    estado_store.load();
                }
            }
        }
    });

    TFNombre = new Ext.form.TextField({
        id: 'nombre',
        fieldLabel: 'Nombre',
        xtype: 'textfield'
    });


    Ext.define('ListaDetalleModel',
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'idServicio',                    type: 'int'},
            {name: 'tipo',                          type: 'string'},
            {name: 'idPunto',                       type: 'string'},
            {name: 'descripcionPunto',              type: 'string'},
            {name: 'descripcionPresentaFactura',    type: 'string'},
            {name: 'loginPadreFact',                type: 'string'},
            {name: 'idProducto',                    type: 'string'},
            {name: 'descripcionProducto',           type: 'string'},
            {name: 'nombreTecnicoProducto',         type: 'string'},
            {name: 'strPrefijoEmpresa',             type: 'string'},
            {name: 'enlaceDatos',                   type: 'string'},
            {name: 'cantidad',                      type: 'string'},
            {name: 'fechaCreacion',                 type: 'string'},
            {name: 'precioVenta',                   type: 'string'},
            {name: 'valorDescuento',                type: 'string'},
            {name: 'descuento',                     type: 'string'},
            {name: 'descuentoUnitario',             type: 'string'},
            {name: 'porcentajeDescuento',           type: 'string'},
            {name: 'estado',                        type: 'string'},
            {name: 'linkVer',                       type: 'string'},
            {name: 'linkEditar',                    type: 'string'},
            {name: 'linkEliminar',                  type: 'string'},
            {name: 'linkFactibilidad',              type: 'string'},
            {name: 'tipoOrden',                     type: 'string'},
            {name: 'esVenta',                       type: 'string'},
            {name: 'ultimaMilla',                   type: 'string'},
            {name: 'tipoMedio',                     type: 'string'},
            {name: 'frecuenciaProducto',            type: 'string'},
            {name: 'mesesRestantes',                type: 'string'},
            {name: 'banderaAnulacionOrdenTrabajo',  type: 'string'},
            {name: 'id_factibilidad',               type: 'string'},
            {name: 'tercializadora',                type: 'string'},
            {name: 'cliente',                       type: 'string'},
            {name: 'login2',                        type: 'string'},
            {name: 'ciudad',                        type: 'string'},
            {name: 'direccion',                     type: 'string'},
            {name: 'nombreSector',                  type: 'string'},
            {name: 'esRecontratacion',              type: 'string'},
            {name: 'producto',                      type: 'string'},
            {name: 'tipo_orden',                    type: 'string'},
            {name: 'tipoOrdenServicio',             type: 'string'},
            {name: 'telefonos',                     type: 'string'},
            {name: 'observacion',                   type: 'string'},
            {name: 'linkVerCliente',                type: 'string'},
            {name: 'strFechaRenovacionServicio',    type: 'string'},
            {name: 'boolMostrarAccionRenovacion',   type: 'boolean'},
            {name: 'esConcentrador',                type: 'string'},
            {name: 'loginAux',                      type: 'string'},
            {name: 'tipoEnlace',                    type: 'string'},
            {name: 'formaEnlace',                   type: 'string'},
            {name: 'precioInstalacion',             type: 'string'},
            {name: 'precioTotal',                   type: 'string'},
            {name: 'anexoTecnico',                  type: 'string'},
            {name: 'backup',                        type: 'string'},
            {name: 'nombre_vendedor',               type: 'string'},
            {name: 'strClasificacion',              type: 'string'},
            {name: 'strReqEnlaceDatos',             type: 'string'},
            {name: 'strRequiereBackup',             type: 'string'},
            {name: 'strTieneBackup',                type: 'string'},
            {name: 'boolMostrarCambioTipoMedio',    type: 'boolean'},
            {name: 'boolTieneComisionistas',        type: 'boolean'},
            {name: 'strContinuaFlujoNormal',        type: 'string'},
            {name: 'strSolucion',                   type: 'string'},
            {name: 'tieneOpcionMigracionFact',      type: 'string'},
            {name: 'esSolucion',                    type: 'string'},
            {name: 'tipoEsquema',                   type: 'int'},
            {name: 'strFlagActivacion',             type: 'string'},
            {name: 'strFlagActiSimul',              type: 'string'},
            {name: 'nombreProducto',                type: 'string'},
            {name: 'strOpcionEliminarServicio',     type: 'string'},
            {name: 'boolMostrarBotonOSA',           type: 'boolean'},
            {name: 'idPersona',                     type: 'int'},
            {name: 'latitud',                       type: 'string'},
            {name: 'longitud',                      type: 'string'},
            {name: 'intIdPuntoCobertura',           type: 'int'},
            {name: 'intIdCanton',                   type: 'int'},
            {name: 'intIdParroquia',                type: 'int'},
            {name: 'intIdSector',                   type: 'int'},
            {name: 'intElementoEdificioId',         type: 'int'},
            {name: 'strElementoEdificio',           type: 'string'},
            {name: 'strDependeDeEdificio',          type: 'string'},
            {name: 'booleanRelCamMascarilla',       type: 'boolean'},
            {name: 'strTipoRed',                    type: 'string'},
            {name: 'strEditaDatosGeograficos',      type: 'string'},
            {name: 'strMetraje',                    type: 'string'},
            {name: 'strModulo',                     type: 'string'},
            {name: 'intPrecioFibra',                type: 'int'},
            {name: 'intMetrosDeDistancia',          type: 'int'},
            {name: 'strSolExcedenteMaterial',       type: 'string'},
            {name: 'intIdFactibilidad',             type: 'int'},
            {name: 'floatValorCaractOCivil',        type: 'float'},
            {name: 'floatValorCaractOtrosMateriales',type: 'float'},
            {name: 'floatValorCaractCancPorCli',    type: 'float'},
            {name: 'floatValorCaractAsumeCli',      type: 'float'},
            {name: 'floatValorCaractAsumeEmpresa',  type: 'float'},
            {name: 'strEvidencia',                  type: 'string'},
            {name: 'arrayParametrosDet',            type: 'string'},
            {name: 'idPersonaRol',                  type: 'int'},
            {name: 'strUuidPaquete',                type: 'string'},
            {name: 'intPersonaEmpresaRolId',        type: 'int'},
            {name: 'strValorProductoPaqHoras',      type: 'string'},
            {name: 'strValorProductoPaqHorasRec',   type: 'string'},
            {name: 'boolEsReplica',                 type: 'boolean'}
        ]
    });

    store = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: 
        {
            type: 'ajax',
            url: url_grid,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                root: 'servicios',
                totalProperty: 'total'
            },
           extraParams: {fechaDesde: '', fechaHasta: '', estado: '', nombre: '', plan: '', producto: '', strFlagActivacion: ''},
            simpleSortMode: true
        },
        listeners: 
        {
            beforeload: function(store) 
            {
                store.getProxy().extraParams.fechaDesde             = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta             = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.producto               = Ext.getCmp('sltProducto').getValue();
                store.getProxy().extraParams.plan                   = Ext.getCmp('sltPlanes').getValue();
                store.getProxy().extraParams.estado                 = Ext.getCmp('sltEstado').getValue();
                store.getProxy().extraParams.nombre                 = Ext.getCmp('nombre').getValue();
                store.getProxy().extraParams.strFlagActivacion      = Ext.getCmp('strFlagActivacion').getValue();
            },
            load: function(store) 
            {
                store.each(function(record) 
                {
                    if(record.data.strFechaRenovacionServicio != '')
                    {
                        boolHiddenFechaRenovacion = false;
                    }
                });
                    
                    
                if( !boolHiddenFechaRenovacion && intContadorInicial == 0 )
                {
                    intContadorInicial = intContadorInicial + 1; 
                    
                    var column = Ext.create('Ext.grid.column.Column', 
                    {
                        text: 'Fecha<br>Renovación',
                        dataIndex: 'strFechaRenovacionServicio',
                        hidden: boolHiddenFechaRenovacion,
                        hideable: false,
                        align: 'center',
                        width: 80
                    });
                    listView.headerCt.insert((listView.columns.length - 2), column);
                    listView.getView().refresh();
                }
            }
        }
    });

    Ext.define('modelPlanes', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idPlan', type: 'int'},
            {name: 'nombrePlan', type: 'string'}
        ]
    });

    var storePlanes = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelPlanes",
        proxy: {
            type: 'ajax',
            url: strUrlGetPlanesPorEstado,
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        }
    });
    
    Ext.define('modelProductos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idProducto', type: 'int'},
            {name: 'descripcionProducto', type: 'string'}
        ]
    });

    var storeProductos = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelProductos",
        proxy: {
            type: 'ajax',
            url: strUrlGetProductosPorEstado,
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        }
    });
    var filterPanelServ = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 3,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: '100%',
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    id: 'buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscarServicios();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],                
                items: [
                        DTFechaDesde,
                        DTFechaHasta,
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltEstado',
                            value:'Todos',
                            store: [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Anulado','Anulado'],
                                ['Asignada','Asignada'],
                                ['AsignadoTarea','AsignadoTarea'],
                                ['Cancel','Cancel'],                            
                                ['Cancel-SinEje','Cancel-SinEje'],
                                ['Eliminado','Eliminado'],
                                ['EnPruebas','EnPruebas'],
                                ['EnVerificacion','EnVerificacion'],
                                ['Factible','Factible'],
                                ['FactibilidadEnProceso','FactibilidadEnProceso'],
                                ['Factibilidad-anticipada','Factibilidad-anticipada'],
                                ['Inactivo','Inactivo'],
                                ['In-Corte-SinEje','In-Corte-SinEje'],
                                ['In-Corte','In-Corte'],
                                ['In-Temp-SinEje','In-Temp-SinEje'],
                                ['In-Temp','In-Temp'],                            
                                ['Pendiente','Pendiente'],
                                ['Planificada','Planificada'],
                                ['Pre-Servicio','Pre-Servicio'],
                                ['PreFactibilidad','PreFactibilidad'],
                                ['PrePlanificada','PrePlanificada'],
                                ['Replanificada','Replanificada'],
                                ['Reubicado','Reubicado'],
                                ['Trasladado','Trasladado']
                            ],
                            width: '30%'
                        },
                        {
                            xtype: 'combobox',
                            id: 'sltProducto',
                            fieldLabel: 'Producto',
                            store: storeProductos,
                            displayField: 'descripcionProducto',
                            valueField: 'idProducto',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'remote',
                            width: '40%',
                            triggerAction: 'all',
                            selectOnFocus: true,
                            lastQuery: '',
                            mode: 'local',
                            allowBlank: true,
                            listeners: {
                                select:
                                    function(e) {
                                        idProducto = Ext.getCmp('sltProducto').getValue();
                                    },
                                click: {
                                    element: 'el',
                                    fn: function() {
                                        idProducto = '';
                                        storeProductos.removeAll();
                                        storeProductos.load();
                                    }
                                }
                            }   
                        },
                        {
                            xtype: 'combobox',
                            id: 'sltPlanes',
                            fieldLabel: 'Plan',
                            store: storePlanes,
                            displayField: 'nombrePlan',
                            valueField: 'idPlan',
                            loadingText: 'Buscando ...',
                            queryMode: 'remote',
                            listClass: 'x-combo-list-small',
                            width: '40%',
                            triggerAction: 'all',
                            selectOnFocus: true,
                            lastQuery: '',
                            mode: 'local',
                            allowBlank: true,
                            listeners: {
                                select:
                                    function(e) {
                                        idPlan = Ext.getCmp('sltPlanes').getValue();
                                    },
                                click: {
                                    element: 'el',
                                    fn: function() {
                                        idPlan = '';
                                        storePlanes.removeAll();
                                        storePlanes.load();
                                    }
                                }
                            }                            
                        },
                        {
                            xtype: 'hidden',
                            id: 'strFlagActivacion',
                            name: 'strFlagActivacion',
                            displayField: '',
                            value: strFlagActivaSimu,
                            width: '40%',
                            readOnly: true
                        },
                        { width: '10%',border:false},
                        ],
        renderTo: 'filtro_servicios'
    });   
    
    store.load({params: {start: 0, limit: 10}});
 
    listView = Ext.create('Ext.grid.Panel', 
    {
        width: 1040,
        height: 490,
        collapsible: false,
        title: '',
        layout:'fit',
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    {xtype: 'tbfill'}
                ]}],
        renderTo: Ext.get('lista_servicios'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando servicios {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar',
            stripeRows: true,
            enableTextSelection: true
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
                    autoHide: false,
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
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                        
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
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });
                
                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                text: 'Orden',
                width: 45,
                dataIndex: 'tipoOrden'
            },
            {
                text: 'Tipo Red',
                width: 50,
                dataIndex: 'strTipoRed'
            },
            {
                text: 'UM',
                width: 40,
                dataIndex: 'ultimaMilla',
                align: 'center'
            },
            {
                text: 'Vendedor',
                width: 130,
                dataIndex: 'nombre_vendedor',
                align: 'center'
            },
            {
                text: 'Cant.',
                width: 35,
                dataIndex: 'cantidad',
                align: 'center'
            },
            {
                text: 'Producto / Plan',
                width: 140,
                dataIndex: 'descripcionProducto'
            },
            {
                text: 'Precio Inst.',
                dataIndex: 'precioInstalacion',
                align: 'right',
                width: 62
            },
            {
                text: 'Dcto. Total',
                dataIndex: 'descuento',
                align: 'right',
                width: 55
            },
            {
                text: 'Dcto. Unitario',
                dataIndex: 'descuentoUnitario',
                align: 'right',
                width: 75
            },            
            {
                text: 'P.V.P.',
                dataIndex: 'precioVenta',
                align: 'right',
                width: 60
            },
            {
                text: 'P. Total',
                dataIndex: 'precioTotal',
                align: 'right',
                width: 70
            },
            {
                text: 'Venta',
                dataIndex: 'esVenta',
                align: 'center',
                width: 40
            },
            {
                text: 'Frec.',
                dataIndex: 'frecuenciaProducto',
                align: 'center',
                width: 35
            },
            {
                text: 'Creación',
                dataIndex: 'fechaCreacion',
                align: 'right',
                width: 67,
                renderer: function(value, metaData, record, colIndex, store, view) 
                {
                    metaData.tdAttr = 'data-qtip="' + value + '"';
                    return value;
                }
            },
            {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'center',
                width: 63
            },
            {
                text: 'Acciones',
                width: 135,
                renderer: renderAcciones
            }
        ]
    });

    if (prefijoEmpresa == strEmpresaPermitida)
    {
        listView.headerCt.insert(5,
            {
                text: 'Descripción Factura',
                width: 150,
                dataIndex: 'descripcionPresentaFactura'
            }
        );
        listView.headerCt.insert(6,
            {
                text: 'Login Aux',
                width: 60,
                dataIndex: 'loginAux'
            }
        );
        listView.headerCt.insert(7,
            {
                text: 'Padre Fact.',
                width: 70,
                dataIndex: 'loginPadreFact'
            }
        );
        listView.headerCt.insert(8,
            {
                text: 'Tipo Enlace',
                width: 70,
                dataIndex: 'tipoEnlace'
            });
        listView.headerCt.insert(9,
            {
                width: 50,
                dataIndex: 'backup',
                hidden: true
            });
        listView.headerCt.insert(10,
            {
                text: 'Grupo/Solución',
                width: 100,
                dataIndex: 'strSolucion'
            });
        listView.headerCt.insert(16,
            {
                text: 'Meses',
                width: 43,
                dataIndex: 'mesesRestantes'
            });

        listView.getView().refresh();
    }
    else if (prefijoEmpresa == 'MD' ||prefijoEmpresa == 'EN'  )
    {
        listView.headerCt.items.items[4].setWidth(250);
    }

    /**
     * Documentación para el método 'renderAcciones'.
     *
     * Funcion utilizada para definir las acciones existenes en el grid de servicios de un punto en sesion.
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 10-08-2016   Se agrega accion para actualizar id del plan de un servicio
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 20-02-2022   Se agrega tipo de orden de servicio en la función "SolicitarFactibilidad"
     * 
     * @since 1.0
     */
    function renderAcciones(value, p, record)
    {
        var arrayBotones = [];
        var permiso      = null;
        var boolPermiso  = false;
        var rec          = record.data;
                
        arrayBotones.push('<a href="#" onClick="verLogsServicio(' + rec.idServicio + 
                           ')" title="Ver Historial Servicio" class="button-grid-verLogsServicio"/>');
        
        permiso     = $("#ROLE_13-2597");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        if (boolPermiso && (rec.estado == 'Activo' || rec.estado == 'In-Corte'))
        {
            arrayBotones.push('<a href="#" onClick="verCaracteristicasServicio(\''  + rec.idServicio + '\',\'' + consultarLoginCam(rec.idPersonaRol,rec.idProducto) +
            '\')" title="Ver Caracteristicas del Servicio" class="button-grid-editarCaracteristicas"/>');                                  
        }
        if(rec.nombreProducto.includes('SEGURIDAD ELECTRONICA   VIDEO VIGILANCIA')){
            if ((rec.estado == 'Activo') && 
            consultarInformacionSerie(rec.idServicio) == false && 
            consultarLoginCam(rec.idPersonaRol,rec.idProducto) == true)
            {
                arrayBotones.push('<a href="#" onClick="agregarCarac(\''  + rec.idServicio + '\',\'' + rec.idProducto + '\',\'' + rec.idPersonaRol +
                '\',\'' + rec.idPunto + '\')" title="Ingresar Información de Cámara" class="button-grid-ingresoCamara"/>');        
            }
        }
        //Opcion para cambiar el Id del Plan de un servicio que se encuentre en estado AsignadoTarea ó Activo
        if (puedeActualizarIdPlan && (rec.estado == 'AsignadoTarea' || rec.estado == 'Activo') && rec.tipo == 'plan')
        {
            arrayBotones.push('<a href="#" onClick="cambiarPlanActivoEquivalente(' + rec.idServicio + 
                               ')" title="Cambiar a Plan Activo Equivalente" class="button-grid-actualizarIdPlan"/>');
        }
        
        if(rec.strOpcionEliminarServicio == 'SI' && (rec.estado == 'Pendiente'  || rec.estado == 'PreFactibilidad' ||
           rec.estado == 'Factible'  || rec.estado == 'PrePlanificada' || rec.estado == 'PreAsignacionInfoTecnica' || 
           rec.estado == 'Rechazado' || rec.estado == 'Pre-servicio')  && rec.esSolucion == 'S')
        {
            arrayBotones.push('<a href="#" onClick="eliminarServicio(\'' + rec.idServicio + '\',\'' + rec.id_factibilidad + 
                               '\',\'' + rec.esSolucion + '\')" title="Eliminar Servicio" class="button-grid-eliminarServicio"/>');
        }
        
        if ( rec.strOpcionEliminarServicio == 'SI' && (rec.estado == 'Pendiente'  || rec.estado == 'PreFactibilidad' ||
             rec.estado == 'Factible'  || rec.estado == 'PrePlanificada' || rec.estado == 'PreAsignacionInfoTecnica' || rec.estado == 'Rechazado')
             && rec.esSolucion !== 'S')
        {
            arrayBotones.push('<a href="#" onClick="showMotivoEliminarServicio(\'' + rec.idServicio + '\',\'' + rec.id_factibilidad + 
                               '\',\'' + rec.esSolucion + '\')" title="Eliminar Servicio" class="button-grid-eliminarServicio"/>');
        }
        if(rec.estado == 'Pre-servicio' && rec.esSolucion !== 'S')
        {
            arrayBotones.push('<a href="#" onClick="eliminarServicio(\'' + rec.idServicio + '\',\'' + rec.id_factibilidad + 
                               '\',\'' + rec.esSolucion + '\')" title="Eliminar Servicio" class="button-grid-eliminarServicio"/>');
        }
        //OPCIÓN PARA INGRESAR CÓDIGO DE PROMOCIONES.

        if (prefijoEmpresa === 'MD' || prefijoEmpresa === 'EN' )
        {
            if (rec.idServicio !== '' && rec.idProducto !== '' && rec.tipo !== ''&& puedeIngresarCodigosPromocionales 
                && rec.estado === 'Activo' && parseInt(rec.frecuenciaProducto) > 0 && rec.esVenta === 'SI' && parseInt(rec.cantidad) > 0)
            {
                var strValidacionPresenta = ingresoCodigoPromociones(rec.idServicio,rec.idProducto,rec.tipo,'Grid');

                if (strValidacionPresenta === 'S')
                {
                    arrayBotones.push('<a href="#" onClick="ingresoCodigoPromociones(\'' + rec.idServicio + '\',\'' + rec.idProducto + 
                                      '\',\'' + rec.tipo + '\',\'' + 'Codigo' + '\')" title="Ingresar Código Promociones" class="button-grid-poolRecursos"/>');           
                }
            }
        }
        //CAMBIAR FRECUENCIA FACTURACIÓN
        permiso     = $("#ROLE_151-4337");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        
        if (boolPermiso && (prefijoEmpresa == 'TN'))
        {
            if ( (rec.nombreProducto !== rec.strValorProductoPaqHorasRec) && (rec.nombreProducto !== rec.strValorProductoPaqHoras) )
            {
                arrayBotones.push('<a href="#" onClick="cambiarFrecuenciaFacturacion(' + rec.idServicio + ',' + rec.frecuenciaProducto + 
                                ')" title="Cambiar Frecuencia de Facturación" class="button-grid-cambiar-frec-fact"/>');
            }
        }
        
        //Verificar Cuarto de Ti para Housing ( BOC )
        if(rec.descripcionProducto.includes('Alquiler de Espacio') && rec.estado !== 'Cancel')
        {
            arrayBotones.push('<a href="#" onClick="verInformacionGeneralCuartoTi('+ rec.idServicio +
                              ')" title="Consultar Cuarto TI" class="button-grid-verCuartoTi-com"/>');
        }
        
        //Verificar información de Pool de recursos ingresado
        if(rec.descripcionProducto.includes('CLOUD IAAS POOL RECURSOS') && rec.estado !== 'Cancel')
        {
            arrayBotones.push('<a href="#" onClick="verResumenRecursosContratados('+ rec.idServicio+
                              ')" title="Consultar Recursos Contratados" class="button-grid-poolRecursos"/>');
        }

        //ASOCIAR SERVICIO MASCARILLA A CAMARAS
        permiso     = $("#ROLE_151-8177");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        if (boolPermiso && prefijoEmpresa == 'TN' && rec.nombreTecnicoProducto == 'SERVICIOS-CAMARA-SAFECITY' && !rec.booleanRelCamMascarilla
            && rec.estado == 'Pendiente')
        {
            arrayBotones.push('<a href="#" onClick="asociarMascarillaACamara(' + rec.idServicio +
                               ')" title="Asociar el servicio a la CAMARA" class="button-grid-asociar-mascarilla-camara"/>');
        }

        if (rec.linkFactibilidad == "si" && !(rec.nombreProducto == 'INTERNET WIFI' && rec.estado == 'Rechazada'))
        {
            if  (rec.idProducto == '237') 
            {
                if (rec.strFlagActivacion == 'N' && rec.strFlagActiSimul !== 'S')
                {
                    arrayBotones.push('<a href="#" onClick="SolicitarFactibilidad(' + rec.idServicio + ','
                                                                                    + rec.idProducto + ',\''
                                                                                    + rec.descripcionPresentaFactura +'\',\''
                                                                                    + rec.strContinuaFlujoNormal+ '\',\''
                                                                                    + rec.tieneOpcionMigracionFact+ '\',\''
                    + rec.nombreTecnicoProducto+ '\',\''+ rec.tipoOrdenServicio +'\')" \n\ title="Solicitar Factibilidad" class="button-grid-solicitarFactibilidad"/>');
                }
                else
                {
                    if (rec.strFlagActiSimul == 'S' && rec.strFlagActivacion == 'S')
                    {
                        arrayBotones.push('<a href="#" onClick="SolicitarFactibilidad(' + rec.idServicio + ','
                                                                                    + rec.idProducto + ',\''
                                                                                    + rec.descripcionPresentaFactura +'\',\''
                                                                                    + rec.strContinuaFlujoNormal+ '\',\''
                                                                                    + rec.tieneOpcionMigracionFact+ '\',\''
                    + rec.nombreTecnicoProducto+ '\',\''+ rec.tipoOrdenServicio +'\')" \n\ title="Solicitar Factibilidad" class="button-grid-solicitarFactibilidad"/>');
                    }
                }
            }
            else
            {
                arrayBotones.push('<a href="#" onClick="SolicitarFactibilidad(' + rec.idServicio + ','
                                                                                    + rec.idProducto + ',\''
                                                                                    + rec.descripcionPresentaFactura +'\',\''
                                                                                    + rec.strContinuaFlujoNormal+ '\',\''
                                                                                    + rec.tieneOpcionMigracionFact+ '\',\''
                + rec.nombreTecnicoProducto+ '\',\''+ rec.tipoOrdenServicio +'\')" \n\ title="Solicitar Factibilidad" class="button-grid-solicitarFactibilidad"/>');
            } //tipo orden rec.tipoOrden =='Traslado
                
        }

        permiso     = $("#ROLE_415-6037");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);        
        if (boolPermiso && prefijoEmpresa  == 'TN' && rec.nombreTecnicoProducto == 'TELEFONIA_NETVOICE' &&  rec.estado == 'Pendiente')
        {
                arrayBotones.push('<a href="#" onClick="solicitarFactibilidadTelefonia('  + rec.idServicio + ',\''+
                                   rec.estado+ '\')" \n\ title="Solicitar Factibilidad" class="button-grid-solicitarFactibilidad"/>');
        } 
        if (prefijoEmpresa == 'TN' && rec.nombreTecnicoProducto == 'TELEFONIA_NETVOICE' && rec.estado != 'Pendiente')
        {
                arrayBotones.push('<a href="#" onClick="verLineasTelefonicas('  + rec.idServicio + ',\'' + rec.estado +
                                    '\')" \n\  title="Ver Líneas y canales " class="button-grid-telefonia"/>');
        }
       
        //Si el enlace es PRINCIPAL , el producto puede tener BACKUP y ademas no tiene aun enlazado alguno, se mostrara el boton
        if (rec.strRequiereBackup === 'SI' && (rec.strPrefijoEmpresa === 'TN' || rec.strPrefijoEmpresa === 'TNP') )  
        {
            arrayBotones.push('<a href="#" onClick="crearServicioBackup('+rec.idServicio+',\''+rec.ultimaMilla+'\')" title="Crear Servicio BACKUP" \n\
                class="button-grid-crearBackup"/>'); 
                
        }
        var validaEnlace = (rec.tipoEnlace !== null) ? rec.tipoEnlace.substring(0, 9):rec.tipoEnlace;
        if (rec.strClasificacion ==="DATOS"
             &&
             rec.esConcentrador === "NO"
             &&
             rec.strReqEnlaceDatos === "SI"
             &&
             validaEnlace === "PRINCIPAL"
             &&
             puedeEnlazarDatos)
        {
            arrayBotones.push('<a href="#" onClick="enlazar(' + rec.idPunto + ',' + rec.idServicio + ',\'' + rec.estado + 
                               '\')" title="Definir Concentrador" class="button-grid-enlazarConcentrador"/>');
        }                
        
        permiso     = $("#ROLE_9-3937");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        if (boolPermiso && rec.estado == 'Activo' && rec.strFechaRenovacionServicio != '' && rec.boolMostrarAccionRenovacion == true)
        {
            arrayBotones.push('<a href="javascript:" onClick="renovarPlan(' + rec.idServicio + ', \'' + rec.strFechaRenovacionServicio + 
                              '\')" title="Renovar Plan" class="btn-acciones btn-renovar-plan"/>');
        }

        //se agrega boton de anulacion de ordenes de trabajo
        permiso     = $("#ROLE_13-225");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        if (boolPermiso && rec.banderaAnulacionOrdenTrabajo == "si") 
        {
            arrayBotones.push('<a href="#" onClick="showAnularOrden_Coordinar(' + 
                               rec.id_factibilidad + ',' +
                               rec.idPunto + ',\'' +
                               rec.linkVerCliente + '\',\'' +
                               rec.tercializadora + '\',\'' +
                               rec.cliente + '\',\'' +
                               rec.login2 + '\',\'' +
                               rec.ciudad + '\',\'' +
                               getCleanedString(rec.direccion) + '\',\'' +
                               rec.nombreSector + '\',\'' +
                               rec.esRecontratacion + '\',\'' +
                               rec.producto + '\',\'' +
                               rec.tipo_orden + '\',\'' +
                               rec.telefonos + '\',\'' +
                               getCleanedString(rec.observacion)+ '\',\'' +
                               rec.esSolucion + '\',\'' +
                               rec.idServicio + '\')" title="Anular Orden de Trabajo" class="button-grid-BigDelete"/>');
        }
       
        // EDICION DE DESCRIPCION PRESENTA FACTURA
        permiso     = $("#ROLE_13-4357");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        if (boolPermiso) 
        {           
            arrayBotones.push('<a href="#" onClick="edicionDescPresentaFactura(' + rec.idServicio + ', \'' + rec.descripcionPresentaFactura + 
                              '\')" title="Editar Descripcion Presenta Factura" class="button-grid-cambiar-desc-fact"/>');
        }
        //CAMBIAR FRECUENCIA FACTURACIÓN
        permiso     = $("#ROLE_415-6038");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);        
        if (boolPermiso && rec.nombreTecnicoProducto == 'TELEFONIA_NETVOICE' && rec.estado == 'Activo')
        {
                arrayBotones.push('<a href="#" onClick="cancelarLineasNetvoice(' + rec.idServicio +
                                  ')" \n\ title="Cancelar Líneas Telefónicas " class="button-grid-BigDelete"/>');
        }         
        
        //FIN ENLACE DE DATOS

        //CAMBIAR FRECUENCIA FACTURACIÓN
        var permiso = $("#ROLE_151-4337");
        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
        
        if (rec.anexoTecnico==="SI")
        {
            arrayBotones.push('<a href="#" onClick="cargarArchivo(' + rec.idPunto + ',' + rec.idServicio + 
                               ')" title="Subir Anexo Tecnico" class="button-grid-agregarArchivo"/>');
               
            arrayBotones.push('<a href="#" onClick="verDocumento(' + rec.idServicio + 
                              ')" title="Ver Anexo Tecnico" class="button-grid-verAnexoTecnico"/>');
        }

        permiso     = $("#ROLE_13-7837");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

        if (boolPermiso)
        {
            if (rec.nombreTecnicoProducto === "NETWIFI")
            {
                if(rec.estado == 'PreAsignacionInfoTecnica')
                {
                    arrayBotones.push('<a href="#" onClick="subirArchivoAnexo('+ rec.idServicio +')" \n\
                                       title="Subir mapa de recorrido del cliente" class="button-grid-agregarArchivo"/>');
                }

                arrayBotones.push('<a href="#" onClick="verDocumento(' + rec.idServicio +
                                  ')" title="Ver documento anexo" class="button-grid-verAnexoTecnico"/>');
            }
        }

        /*VER ARCHIVO INSPECCION WIFI ALQUILER EQUIPOS*/
        permiso     = $("#ROLE_13-6617");
        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);     
        if (boolPermiso && rec.nombreProducto == 'WIFI Alquiler Equipos')
        {
            arrayBotones.push(`<a href="#" onClick="verArchivoInspeccion(${rec.idServicio})" 
                                style="font-size: medium"
                                title="Ver Informe de Inspección" 
                                class="button-grid-telcos search-file"/>`);
        }

        //VER/EDITAR PLANTILLA DE COMISIONISTAS
        if( rec.boolTieneComisionistas == true )
        {
            arrayBotones.push('<a href="javascript:" onClick="editarPlantillaComisionista(' + rec.idServicio + ')" ' +
                              'title="Ver/Editar Plantilla Comisionista" class="button-grid-editarPlantillaComisionista"/>');
        }//( rec.boolTieneComisionistas == true )
        
        //CAMBIO DE TIPO MEDIO
        if (rec.estado == 'Activo' && prefijoEmpresa == 'TN' && (rec.strClasificacion ==="DATOS" || rec.strClasificacion ==="INTERNET")
            && rec.ultimaMilla !== 'FO' && !rec.boolMostrarCambioTipoMedio && rec.ultimaMilla !== 'FTTx')
        {
            var idBackup = '';
            if(rec.backup !== null && rec.backup !== '')
            {
                idBackup = rec.backup;
            }
            else
            {
                idBackup = null;
            }
            permiso     = $("#ROLE_151-5677");
            boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            if (boolPermiso) 
            {
                arrayBotones.push('<a href="#" onClick="cambiarTipoMedio('+
                                rec.idServicio+','+
                                idBackup+ ',\'' +
                                rec.ultimaMilla+ '\',\'' +
                                rec.tipoMedio+ '\',\'' +
                                rec.formaEnlace+ '\',\'' +
                                $("#floatLatitud").val()+','+$("#floatLongitud").val()+ '\',\'' +
                                $("#strInformacionCliente").val()+'\')" '+
                               'title="Cambio de tipo medio" class="button-grid-cambiarEstado"/>');
            }
        }
        
        //BOTÓN PARA EL REINGRESO DE LA ORDEN DE SERVICIO AUTOMÁTICA.
        if ((prefijoEmpresa === 'MD' || prefijoEmpresa === 'EN' ) && rec.boolMostrarBotonOSA)
        {
            if (rec.tipoOrden =='Nueva' && permisoReingresoOrdenServicioNuevo) 
            {
                jsonDatos = rec;
                arrayBotones.push("<a href='#' onClick='reingresoAutomatico()' "+
                                  "title='Reingreso de Orden de Servicio Nueva' class='button-grid-reingreso'/>");
            }
            else if (rec.tipoOrden =='Traslado' && permisoReingresoOrdenServicioTraslado) 
            {
                jsonDatos = rec;
                arrayBotones.push("<a href='#' onClick='reingresoAutomatico()' "+
                                  "title='Reingreso de Orden de Servicio traslado' class='button-grid-reingreso'/> <br>");
            }
        }

        // MUESTRA LOS BOTONES PARA CARGAR Y/O VER ARCHIVOS DE EVIDENCIAS, Y EL BOTÓN DEL VALIDADOR EXCEDENTES DE MATERIALES   
        if ( (prefijoEmpresa === 'TN' ) 
            && (rec.estado == 'PrePlanificada' || rec.estado == 'Factible' || rec.estado == 'Detenido'|| rec.estado == 'Replanificada') 
            && (rec.strMetraje)
            && (rec.arrayParametrosDet)
            )
        {

            if((rec.strMetraje > rec.intMetrosDeDistancia) || ((rec.floatValorCaractOCivil+rec.floatValorCaractOtrosMateriales) > 0))
            {
                arrayBotones.push('<a href="javascript:" onClick="subirMultipleAdjuntosEvidencias(' + rec.idServicio + ')" ' +
                                'title="Subir Archivos de Evidencia para Excedente de Materiales" class="button-grid-agregarArchivo"/>');
            }
    
                arrayBotones.push('<a href="javascript:" onClick="verDocumento(' + rec.idServicio + ')" ' +
                                'title="Consulta Archivos de Evidencia para Excedente de Materiales" class="button-grid-show"/>');
     
                arrayBotones.push('<a href="javascript:" onClick="validadorExcedenteMaterial(' + '\'' + rec.strMetraje + '\',\'' +
                                rec.strModulo + '\',\'' +
                                rec.intPrecioFibra + '\',\'' +
                                rec.intMetrosDeDistancia + '\',\'' +
                                rec.strSolExcedenteMaterial + '\',\'' +
                                rec.idServicio + '\',\'' +
                                rec.intIdFactibilidad + '\',\'' +
                                rec.floatValorCaractOCivil + '\',\'' + 
                                rec.floatValorCaractOtrosMateriales + '\',\'' +
                                rec.floatValorCaractCancPorCli + '\',\''+
                                rec.floatValorCaractAsumeCli + '\',\''+
                                rec.floatValorCaractAsumeEmpresa + '\',\''+
                                rec.strEvidencia + '\',\''
                                +'\')" title="Validador de Excedente de Materiales" class="button-grid-validador"/>');
        }

        //BOTÓN PARA CONFIGURAR LOGINES Y SERVICIOS
        //permiso = $("");
        
        if ((rec.nombreProducto === rec.strValorProductoPaqHorasRec) || (rec.nombreProducto === rec.strValorProductoPaqHoras) 
                && (prefijoEmpresa == 'TN') 
                && ((rec.estado == "Activo") || (rec.estado == "Pendiente"))
                //&& ( (rec.estado !== "Eliminado") || (rec.estado !== "Cancelado") || (rec.estado !== "Pendiente") ) 
            )
        {
            registro = rec;
            //if(rec.estado == "Activo")
            //{
                arrayBotones.push('<a href="javascript:" onClick="verDetallePaqueteHorasSoporte(' + '\'' + rec.strUuidPaquete + '\',\'' +
                                                    rec.idServicio + '\',\'' +
                                                    rec.intPersonaEmpresaRolId + '\',\'' +
                                                    rec.nombreProducto + '\',\'' +
                                                    rec.strValorProductoPaqHorasRec + '\',\''
                                                    +'\')" title="Detalle de Paquete de Horas de Soporte" class="button-grid-detalle-hsop"/>');
            //}
            if (rec.nombreProducto === registro.strValorProductoPaqHoras)
            {
                registro = rec;
                if ((!rec.boolEsReplica)  )
                {
                    arrayBotones.push('<a href="#" onClick="configurarLoginServicio()" ' +
                                        'title="Configuracion de logines y servicios" class="button-grid-Tuerca"/>');
                    if(rec.estado == "Activo")
                    {
                    //Recarga del Paquete de HORAS DE SOPORTE
                        arrayBotones.push('<a href="#" onClick="recargaDePaqueteDeHoras()" ' +
                                        'title="Recarga de horas de soporte" class="button-grid-recargapaquete"/>');
                    }
                }
            }           
        }
        // Se define la tabla en base a la cantidad de botones.
        var acciones = '<table><tr height="30px"><td>';
        var idx      = 1;
        
        Ext.Array.each(arrayBotones, function(rec)
        {
            acciones += rec;
            // máximo 4 botones por fila.
            if (idx % 4 == 0)
            {
                acciones += '</td></tr><tr height="30px"><td>'; // Divisor de Línea.
            }
            
            idx++;
        });
        
        acciones += '</td></tr></table>'; // Divisor de Línea.
        
        return acciones;
    }

});
    /**
     * verDetallePaqueteHorasSoporte
     *
     * Función encargada de presentar todos los datos de un paquete de soporte de horas
     * 
     *
     * @return json con resultado del proceso
     *
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 21-11-2022
     *
     *
     */ 
     function verDetallePaqueteHorasSoporte(uuidPaquete, idServicio, personaEmpresaRolId, nombreProducto ,valorProductoPaqHorasRec){

        var strUuIdPaquete              = uuidPaquete;
        var intIdServicio               = idServicio;
        var intPersonaEmpresaRolId      = personaEmpresaRolId;
        var strNombreProducto           = nombreProducto;
        var strValorProductoPaqHorasRec = valorProductoPaqHorasRec;

        //Grid posterior
        storeDatosTecnicosServicios = new Ext.data.Store({
        total: 'total',
        //autoLoad:true,
        proxy: {
            type: 'ajax',
            url: urlAjaxGetHorasSoporte,
            extraParams: {
                uuid_paquete            : strUuIdPaquete,
                persona_empresa_rol_id  : intPersonaEmpresaRolId,
                servicio_paquete_id     : intIdServicio
            },
            reader: {
                type: 'json',
                // totalProperty: 'total',
                root: 'servicios'
            }
        },
        fields:
            [
                //name: es el nombre del campo y mapping es lo que viene del store
                {name:'login_punto',                mapping: 'login_punto'},
                {name:'producto',                   mapping: 'producto'},
                {name:'permite_activar_paquete',    mapping: 'permite_activar_paquete'},
                {name:'login_auxiliar',             mapping: 'login_auxiliar'},
                {name:'usuario_creacion',           mapping: 'usuario_creacion'},
                {name:'fecha_creacion',             mapping: 'fecha_creacion'},
            ]
        });
        storeDatosTecnicosServicios.load();
        Ext.onReady(function() {
    
            Ext.define('User', {
                extend: 'Ext.data.Model',
                fields: [{
                    name: 'value',
                    type: 'string'
                }, {
                    name: 'tag',
                    type: 'string'
                }]
            });
        
            //Grid posterior
            gridHorasSoporte = Ext.create('Ext.grid.Panel', {
                width  : 952,
                height : 400,
                store : storeDatosTecnicosServicios,
                columns : [ 
                    {
                        header : 'Login punto',
                        dataIndex : 'login_punto',
                        sortable: true,
                        width : '18%'
                    },
                    {
                        header : 'Login auxiliar',
                        dataIndex : 'login_auxiliar',
                        sortable: true,
                        width : '18%'
                    },
                    {
                        header : 'Producto',
                        dataIndex : 'producto',
                        sortable: true,
                        width : '28%'
                    },
                    {
                        header : 'Activa paquete de horas de soporte',
                        dataIndex : 'permite_activar_paquete',
                        sortable: true,
                        width : '15%'
                    },
                    {
                        header : 'Usuario creación',
                        dataIndex : 'usuario_creacion',
                        sortable: true
                    },
                    {
                        header : 'Fecha creación',
                        dataIndex : 'fecha_creacion',
                        sortable: true,
                        width : '10%'
                    }
                ],
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeDatosTecnicosServicios,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos para mostrar"
                }),
                multiSelect: false,
                viewConfig: {
                    emptyText: 'No hay datos para mostrar'
                },
                listeners:{
                        itemdblclick: function( view, record, item, index, eventobj, obj ){
                            var position = view.getPositionByEvent(eventobj),
                            data = record.data,
                            value = data[this.columns[position.column].dataIndex];
                            Ext.Msg.show({
                                title:'Copiar texto?',
                                msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                buttons: Ext.Msg.OK,
                                icon: Ext.Msg.INFORMATION
                            });
                        }
                }
            //})
        });
    });
    
        Ext.Ajax.request({
            url: urlAjaxGetHorasSoporte,
            method: 'post',
            timeout: 400000,
            params: { 
                uuid_paquete            : strUuIdPaquete,
                persona_empresa_rol_id  : intPersonaEmpresaRolId,
                servicio_paquete_id     : intIdServicio
            },
            type: 'ajax',
    
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            success: function(response){     
                var json = Ext.JSON.decode(response.responseText);
                var status = json.status
                var mensaje = json.mensaje
                if (status==500) {
                    window.alert(mensaje);
                }
    
                //Grid superior
                var formPanel = Ext.create('Ext.panel.Panel', {
                    title: 'Informaciòn en general del paquete de soporte',
                    width: 950,
                    height: 390,
                    renderTo: Ext.getBody(),
                    layout: {
                        type: 'vbox',       // Arrange child items vertically
                        align: 'stretch',    // Each takes up full width
                        padding: 5
                    },
                    items: [
                            // Cuadrícula de resultados 
                            {
                                bodyPadding: 3,
                                waitMsgTarget: true,
                                layout: {
                                    type: 'hbox',
                                    width: 280,
                                    align: 'stretch'
                                },
                                store : json,
                                items: [
                                            {
                                                xtype: 'fieldcontainer',
                                                flex: 2,
                                                height: 140,
                                                items: [
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Login principal</b>',
                                                        id : 'login',
                                                        dataIndex : 'login',
                                                        readOnly: true,
                                                        fieldStyle: 'background:none',
                                                        value: json.login_punto
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Login auxiliar</b>',
                                                        id: 'login_auxiliar',
                                                        emptyText: 'Login aux',
                                                        readOnly: true,
                                                        value: json.login_auxiliar
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Minutos contratados</b>',
                                                        id: 'minutos_acumulados',
                                                        readOnly: true,
                                                        value: strNombreProducto === strValorProductoPaqHorasRec ? 
                                                                json.minutos_contratados : json.minutos_totales
                                                        //Si el producto es recarga muestra el valor de recarga
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Minutos restantes</b>',
                                                        id: 'minutos_vigentes',
                                                        readOnly: true,
                                                        value: strNombreProducto === strValorProductoPaqHorasRec ? 
                                                                json.minutos_restantes : json.minutos_vigentes
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'fieldcontainer',
                                                flex: 2,
                                                height: 140,
                                                items: [
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Acumulación</b>',
                                                        id: 'acumula_tiempo',
                                                        readOnly: true,
                                                        value: json.acumula_tiempo
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Forma de Soporte</b>',
                                                        id: 'forma_de_soporte',
                                                        readOnly: true,
                                                        value: json.forma_de_soporte
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Acceso soporte</b>',
                                                        id: 'acceso_de_soporte',
                                                        readOnly: true,
                                                        value: json.acceso_de_soporte
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Embebido</b>',
                                                        id: 'embebido',
                                                        readOnly: true,
                                                        value: json.embebido
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'fieldcontainer',
                                                flex: 2,
                                                height: 140,
                                                items: [
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Fecha de contratación</b>',
                                                        id: 'feInicio',
                                                        readOnly: true,
                                                        value: json.fecha_inicio,
                                                        format: 'd/m/Y'
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: '<b>Fecha de expiración</b>',
                                                        id: 'feFin',
                                                        readOnly: true,
                                                        value: json.fecha_fin,
                                                        format: 'd/m/Y'
                                                    }
                                                ]
                                            }
                                        ]
                            }, 
                            {
                                xtype: 'splitter'   // Un divisor entre los dos elementos secundarios
                            }, 
                            { 
                                // Panel de detalles especificado como un objeto de configuración (ningún tipo predeterminado es 'panel').
                                title: 'Informaciòn de login y productos',
                                bodyPadding: 2,
                                items: [ gridHorasSoporte ], 
                                flex: 2             
                            }
                        ],
                        buttons: [
                            {
                                text: 'Cerrar',
                                handler: function(){ win.destroy();  }
                            }
                        ]
                });
    
                // presenta ambos grid
                var win = Ext.create('Ext.window.Window', {
                    title : 'DETALLE DE '+ strNombreProducto,
                    modal : true,
                    width : 1000,
                    height : 700,
                    resizable : false,
                    layout : 'fit',
                    items : [ formPanel ],
                    buttonAlign : 'center'
                }).show();
    
            },
            failure: function(result)
            {
                var mensajeText ='No hay data que mostrar';
                Ext.Msg.alert('Error ','Error: ' + result.statusText + mensajeText);
            }
    
        }); 
   
    }


    /**
     * configurarLoginServicio
     *
     * Función encargada de registrar los logines asociados a un paquete de soporte.
     *
     *
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0 21-11-2022
     *
     *
     */
    function configurarLoginServicio()
    {
        var idServicio = registro.idServicio;
        var cliente = strCliente;
        //var nombre = registro.loginPadreFact;
        var nombre = strLogin;
        var rol = strRol;
        var idCliente = registro.idPersona;
        var esPadre = strEsPadreFacturacion;
        var idPunto = parseInt(registro.idPunto);

        const uri = `/soporte/gestionPaqueteSoporte/${idServicio}/configurarLoginServicio?`
                                                                +`cliente=${cliente}&`
                                                                +`nombre=${nombre}&`
                                                                +`rol=${rol}&`
                                                                +`idCliente=${idCliente}&`
                                                                +`esPadre=${esPadre}&`
                                                                +`idPunto=${idPunto}`;

        window.location.href = uri;

    }

    function recargaDePaqueteDeHoras()
    {
        console.log(registro);
        var strUuidPaquete = registro.strUuidPaquete;
        var strTipoPaquete = 'recarga';
        let ventana = window.open('/comercial/documentos/servicios/Cliente/new');
        ventana.addEventListener('DOMContentLoaded',function()
         {
             console.log ('ventana de soporte abierta')      
             ventana.document.getElementById('strUuidPaquete').value = strUuidPaquete
             ventana.document.getElementById('strTipoPaquete').value = strTipoPaquete
         }
        )
    }

    /**
     * subirMultipleAdjuntosEvidencias
     *
     * Función encargada de procesar el o los archivos que el usuario desea subir para la evidencia en lo que respecta la negociación
     * de exdedente de materiales
     *
     * @return json con resultado del proceso
     *
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 23-09-2021
     *
     *
     */ 
 
    function subirMultipleAdjuntosEvidencias(idServicio)
    {
        numArchivosSubidos = 0;
        var intIdServicio         = idServicio;

        var panelMultiupload = Ext.create('widget.multiupload', { fileslist: [] });
        var formPanel = Ext.create('Ext.form.Panel',
            {
                width: 500,
                frame: true,
                bodyPadding: '10 10 0',
                defaults: {
                    anchor: '100%',
                    allowBlank: false,
                    msgTarget: 'side',
                    labelWidth: 50
                },
                items: [panelMultiupload],
                buttons: [{
                    text: 'Subir',
                    handler: function () {
                        var form = this.up('form').getForm();
                        if (form.isValid()) {
                            if (numArchivosSubidos > 0) {
                                form.submit({
                                    url: url_CargaDeArchivosMultiples,
                                    params: {
                                        intIdServicio: intIdServicio,
                                        subirEnMsNfs: 'S',
                                        txt_copagos: Ext.getCmp("txt_copagos").getValue()  == '' ? 'NO' : Ext.getCmp("txt_copagos").getValue()
                                    },
                                    waitMsg: 'Procesando Archivo...',
                                    success: function (fp, o) {
                                        Ext.Msg.alert("Mensaje", o.result.respuesta, function (btn) {
                                            if (btn == 'ok') {
                                                numArchivosSubidos = 0;
                                                win.destroy();
                                                store.load();

                                            }
                                        });
                                    },
                                    failure: function (fp, o) {
                                        Ext.Msg.alert("Alerta", o.result.respuesta);
                                    }
                                });
                            }
                            else {
                                Ext.Msg.alert("Mensaje", "No existen archivos para subir", function (btn) {
                                    if (btn == 'ok') {
                                        numArchivosSubidos = 0;
                                        win.destroy();
                                        store.load();
                                    }
                                });
                            }

                        }
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function () {
                        numArchivosSubidos = 0;
                        win.destroy();
                    }
                }]
            });

        var win = Ext.create('Ext.window.Window', {
            title: 'Subir Archivos de Evidencia para Excedente de Materiales',
            modal: true,
            width: 500,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
    }

    /*Función mejorada que muestra la pantalla del Validador de Excedente de Materiales    */

    function validadorExcedenteMaterial(strMetraje,strModulo,intPrecioFibra,intMetrosDeDistancia,strSolExcedenteMaterial,idServicio,
                                    intIdFactibilidad,floatValorCaractOCivil, floatValorCaractOtrosMateriales, floatValorCaractCancPorCli, 
                                    floatValorCaractAsumeCli,floatValorCaractAsumeEmpresa, strEvidencia)
    {
    var strValorMetraje               = strMetraje;
    var floatSubtotalOtrosClientes    = parseFloat(floatValorCaractOCivil) + parseFloat(floatValorCaractOtrosMateriales);
    var intIdServicio                 = idServicio;    
    var strBotonModulo                = '';
    var winValidadorExcedente         = "";
    var resultado1                    = 0;
    var PrecioObraCivil               = 0;
    var PrecioOtrosMate               = 0;
    var suma                          = 0;

    if (!strValorMetraje)
    {
       Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos, no existe metraje');
    }

    if(strModulo=='PLANIFICACION')
    {
        strBotonModulo = 'Enviar a comercial';
    }
    else if(strModulo=='COMERCIAL')
    {
        strBotonModulo = 'Validar';
    }
    
    var formPanelCreacionTarea = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        bodyStyle: "background: white; padding: 15px; border: 0px none;",
        height: 600,
        width: 350,
        frame: true,
        items: [

            //Resumen del cliente (muestra el numero de la solicitud y el estado)
            { width: '10%', border: false },
            {
                xtype: 'label',
                forId: 'lbl_InfoSolExcedente',
                style: "font-weight:bold; color:blue;",
                text: strSolExcedenteMaterial + '\n ',
                margin: '0 0 10 0'
            },
            { width: '10%', border: false },
            {
                xtype: 'label',
                forId: 'lbl_InfoSolExcedente',
                style: "font-weight:bold; color:blue;font-size:11px",
                text:  strEvidencia + '\n ',
                margin: '0 0 10 0',
                hidden: strEvidencia != null ? false : true,
            },
            //-------------------PROYECTOS/CLIENTES EXCEPCIÒN-------------  
            { width: '10%', border: false },
            {
                xtype: 'panel',
                border: false,
                frame: true,
                hidden:true,
                layout: { type: 'hbox', align: 'stretch' },
                items: [
                    {
                        xtype: 'label',
                        forId: 'lbl_clientes_excepcion',
                        text: 'PROYECTOS/CLIENTES EXCEPCIÒN :',
                        margin: '0 0 0 15'
                    }
                ]
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Modulo:',
                name: 'txt_Modulo',
                id: 'txt_Modulo',
                value: strModulo,
                allowBlank: false,
                readOnly: true,                
                style: "width:75%",
                hidden:true
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Valor Predeterminado (metros):',
                name: 'txt_ValorPredeterminado',
                id: 'txt_ValorPredeterminado',
                value: '0',
                allowBlank: false,
                readOnly: true,
                style: "width:75%",
                hidden : true
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Metraje Inspecciòn FO:',
                name: 'txt_MetrajeInpeccion',
                id: 'txt_MetrajeInpeccion',
                value: '0',
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                hidden : true
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Diferencia de FO (metros):',
                name: 'txt_DiferenciaDeFibra',
                id: 'txt_DiferenciaDeFibra',
                value: '0',
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                hidden : true
            },
            {
                xtype: 'textfield',
                fieldLabel: '<b> Sub Total: </b>',
                name: 'txt_SubTotalProyectos',
                id: 'txt_SubTotalProyectos',
                value: '0',
                allowBlank: false,
                readOnly: true,                
                style: "width:75%",
                hidden : true
            },
            // -----------OTROS CLIENTES-------------    
            { width: '10%', border: false },
            {
                xtype: 'panel',
                border: false,
                frame: true,
                layout: { type: 'hbox', align: 'stretch' },
                items: [
                    {
                        xtype: 'label',
                        forId: 'lbl_OtrosClientes',
                        text: 'OTROS CLIENTES :',
                        margin: '0 0 0 15'
                    }
                ]
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Fibra (metros)',
                name: 'txt_FibraMetros',
                id: 'txt_FibraMetros',
                value: parseFloat(strValorMetraje),
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                listeners: {
                    change: {
                        element: 'el',
                        fn: function () {
                            Ext.getCmp('txt_PrecioFibra').setValue
                            (
                                //le da el valor a al precio de fibra
                                parseFloat(resultado1 = Ext.getCmp("txt_FibraMetros").getValue() > parseFloat(intMetrosDeDistancia) ?
                                    (Ext.getCmp("txt_FibraMetros").getValue() - parseFloat(intMetrosDeDistancia)) * parseFloat(intPrecioFibra) : 0)
                            )
                            
                            parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                            parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                            parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate))
                            parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma))
                            parseFloat(Ext.getCmp('txt_Total').setValue(suma))

                            //Resetear los copago
                            parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                        }
                    }
                }
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Precio Fibra',
                name: 'txt_PrecioFibra',
                id: 'txt_PrecioFibra',
                value: (parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia)? (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia)) * parseFloat(intPrecioFibra) : 0),
                allowBlank: false,
                readOnly: true,
                style: "width:75%"
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Precio Obra Civil',
                name: 'txt_PrecioObraCivil',
                id: 'txt_PrecioObraCivil',
                value: parseFloat(floatValorCaractOCivil),
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                listeners: {
                    change: {
                        element: 'el',
                        fn: function () {
                            parseFloat(resultado1 = Ext.getCmp("txt_PrecioFibra").value);
                            parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                            parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                            parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate))
                            parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma))
                            parseFloat(Ext.getCmp('txt_Total').setValue(suma))

                            //Resetear los copago
                            parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                        }
                    }
                }
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Precio Otros Materiales',
                name: 'txt_PrecioOtrosMate',
                id: 'txt_PrecioOtrosMate',
                value: parseFloat(floatValorCaractOtrosMateriales),
                allowBlank: false,
                readOnly: false,
                maskRe: /[0-9.]/,
                style: "width:75%",
                listeners: {
                    change: {
                        element: 'el',
                        fn: function () {
                            parseFloat(resultado1 = Ext.getCmp("txt_PrecioFibra").value);
                            parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                            parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                            parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate))
                            parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma))
                            parseFloat(Ext.getCmp('txt_Total').setValue(suma))

                            //Resetear los copago
                            parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                            parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                        }
                    }
                }
            },
            {
                xtype: 'numberfield',
                fieldLabel: '<b> Sub Total: </b>',
                name: 'txt_SubTotalOtrosClientes',
                id: 'txt_SubTotalOtrosClientes',
                allowBlank: false,
                value: (parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia) ?
                             ( (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia) ) * parseFloat(intPrecioFibra) 
                                     + parseFloat(floatSubtotalOtrosClientes)   )
                                  : floatSubtotalOtrosClientes),
                readOnly: true,
                style: "width:75%",
                maskRe: /[0-9.]/
            },  
            // -----------COPAGOS-------------    
            { width: '10%', border: false },
            {
                xtype: 'panel',
                border: false,
                frame: true,
                layout: { type: 'hbox', align: 'stretch' },
                items: [
                    {
                        xtype: 'label',
                        forId: 'lbl_copagos',
                        text: 'COPAGOS :',
                        margin: '0 0 0 15'
                    }
                ]
            },
            {
                xtype: 'textfield',
                fieldLabel: '% Cancelado por el cliente:',
                name: 'txt_CanceladoPorCliente',
                id: 'txt_CanceladoPorCliente',
                value: floatValorCaractCancPorCli,
                allowBlank: false,
                readOnly: false,
                allowNegative: false,
                style: "width:75%",
                maxValue: 100,
                minValue:1,
                maxLength: 4,
                enforceMaxLength : true,
                limit:100,
                maskRe: /[0-9.]/,
                listeners: {
                    change: {
                        element: 'el',
                        fn: function () {

                            parseFloat(SubTotalOtrosClientes = Ext.getCmp("txt_SubTotalOtrosClientes").value);
                            parseFloat(PorcentajeCanceladoPorCliente = Ext.getCmp('txt_CanceladoPorCliente').value);
                            parseFloat(AsumeCliente = Ext.getCmp('txt_AsumeCliente').value);
                            parseFloat(CalculoAsumeCliente = ((SubTotalOtrosClientes * PorcentajeCanceladoPorCliente) / 100));
                            parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(CalculoAsumeCliente));
                            parseFloat(CalculoAsumeEmpresa = SubTotalOtrosClientes - CalculoAsumeCliente);
                            parseFloat(Ext.getCmp('txt_Total').setValue(CalculoAsumeEmpresa));
                            parseFloat((Ext.getCmp('txt_AsumeEmpresa').setValue(CalculoAsumeEmpresa)) )
                        }
                    }
                }
                
            },
            {
                xtype: 'numberfield',
                fieldLabel: 'Asume el cliente:',
                name: 'txt_AsumeCliente',
                id: 'txt_AsumeCliente',
                value: parseFloat(floatValorCaractAsumeCli),
                allowBlank: false,
                readOnly: true,
                style: "width:75%",
                minValue: 0, //previene numeros negativos
                allowNegative: false,
                maskRe: /[0-9.]/
            },
            {
                xtype: 'numberfield',
                fieldLabel: 'Asume la empresa:',
                name: 'txt_AsumeEmpresa',
                id: 'txt_AsumeEmpresa',
                value: parseFloat(floatValorCaractAsumeEmpresa),
                allowBlank: false,
                readOnly: true,
                allowNegative: false,
                style: "width:75%",
                minValue: 0, //previene numeros negativos
                maskRe: /[0-9.]/
            },
            {
                xtype: 'numberfield',
                fieldLabel: '<b>Total:</b>',
                name: 'txt_Total',
                id: 'txt_Total',
                value: (floatValorCaractCancPorCli >0 ? floatValorCaractAsumeEmpresa:
                    parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia) ?
                             ( (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia) ) * parseFloat(intPrecioFibra) 
                                     + parseFloat(floatSubtotalOtrosClientes)   )
                                  : floatSubtotalOtrosClientes),
                allowBlank: false,
                readOnly: true,
                style: "width:75%",
                maskRe: /[0-9.]/
            },        
            // -----------OBSERVACIÒN-------------
            { width: '10%', border: false },
            {
                xtype: 'label',
                forId: 'lbl_observacion',
                text: 'Observación :',
                margin: '0 0 0 15'
            },
            {
                xtype: 'textareafield',
                hideLabel: true,
                name: 'txt_Observacion',
                id: 'txt_Observacion',
                value: "",
                width: 315,
                heigth: 200,
                readOnly: false
            },
            { width: '10%', border: false },
        ],
        buttons: [
            {
                text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;' + strBotonModulo,
                handler: function () {
                    $.ajax({
                        url: urlValidadorMaterial,
                        type: "POST",
                        timeout: 600000,
                        data:
                        {
                            intIdServicio:        intIdServicio,
                            intMetrosDeDistancia: intMetrosDeDistancia,
                            intPrecioFibra:       intPrecioFibra,
                            //-------------------PROYECTOS/CLIENTES EXCEPCIÒN------------- 
                            valorPredeterminado: Ext.getCmp("txt_ValorPredeterminado").getValue() == '' ? 0 : Ext.getCmp("txt_ValorPredeterminado").getValue(),
                            metrajeInpeccion: Ext.getCmp("txt_MetrajeInpeccion").getValue() == '' ? 0 : Ext.getCmp("txt_MetrajeInpeccion").getValue(),
                            diferenciaDeFibra: Ext.getCmp("txt_DiferenciaDeFibra").getValue() == '' ? 0 : Ext.getCmp("txt_DiferenciaDeFibra").getValue(),
                            subTotalProyectos: Ext.getCmp("txt_SubTotalProyectos").getValue() == '' ? 0 : Ext.getCmp("txt_SubTotalProyectos").getValue(),
                            // -----------OTROS CLIENTES-------------
                            metrosFibra: Ext.getCmp("txt_FibraMetros").getValue() == '' ? 0 : Ext.getCmp("txt_FibraMetros").getValue(),
                            precioFibra: Ext.getCmp("txt_PrecioFibra").getValue(),
                            precioObraCivil: Ext.getCmp("txt_PrecioObraCivil").getValue() == '' ? 0 : Ext.getCmp("txt_PrecioObraCivil").getValue(),
                            precioOtrosMate: Ext.getCmp("txt_PrecioOtrosMate").getValue() == '' ? 0 : Ext.getCmp("txt_PrecioOtrosMate").getValue(),
                            subTotalOtrosClientes: Ext.getCmp("txt_SubTotalOtrosClientes").getValue() == '' ? 0 : Ext.getCmp("txt_SubTotalOtrosClientes").getValue(),
                             // -----------COPAGOS-------------    
                            canceladoPorCliente: Ext.getCmp("txt_CanceladoPorCliente").getValue() == '' ? 0 : Ext.getCmp("txt_CanceladoPorCliente").getValue(),
                            asumeCliente: Ext.getCmp("txt_AsumeCliente").getValue() == '' ? 0 : Ext.getCmp("txt_AsumeCliente").getValue(),
                            asumeEmpresa: Ext.getCmp("txt_AsumeEmpresa").getValue() == '' ? 0 : Ext.getCmp("txt_AsumeEmpresa").getValue(),
                            observacion:  Ext.getCmp("txt_Observacion").getValue()  == '' ? '' : Ext.getCmp("txt_Observacion").getValue(),

                            //------ COMPROBAR DE QUE MODULO ENVÍA EL FORMULARIO
                            modulo:       Ext.getCmp("txt_Modulo").getValue()  == '' ? 0 : Ext.getCmp("txt_Modulo").getValue(),
                            
                            detalleSolId: intIdFactibilidad,
                            strEvidencia: strEvidencia,
                            totalPagar: Ext.getCmp("txt_Total").getValue(),
                        },
                        beforeSend: function () {
                            Ext.get(winValidadorExcedente.getId()).mask('Validando datos...');
                        },
                        complete: function () {
                            Ext.get(winValidadorExcedente.getId()).unmask();
                        },
                        success: function (data) {
                            Ext.Msg.alert('Mensaje', data.mensaje, function (btn) {
                                if (btn == 'ok') {
                                    winValidadorExcedente.close();
                                    store.load();
                                }
                            });

                        },
                        failure: function (result) {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            },
            {
                text: '<i class="fa fa-reply-all" aria-hidden="true"></i>&nbsp;Limpiar Copago',
                handler: function () {
                    parseFloat((Ext.getCmp('txt_CanceladoPorCliente').setValue(0)));
                    parseFloat((Ext.getCmp('txt_AsumeCliente').setValue(0)));
                    parseFloat((Ext.getCmp('txt_AsumeEmpresa').setValue(0)));
                    parseFloat((Ext.getCmp('txt_Total').setValue(Ext.getCmp("txt_SubTotalOtrosClientes").getValue())));
                }
            },
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function () {
                    winValidadorExcedente.close();
                    winValidadorExcedente.destroy();
                }
            },
        ]
    });

        winValidadorExcedente = Ext.widget('window', {
            title: 'Validador de Excedente de Material '+ strModulo,
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: false,
            items: [formPanelCreacionTarea]
        });
        winValidadorExcedente.show();
    
}


var connFact = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});

var connValidaTrasladoMd = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Validando tipo de traslado, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

//Función que permite el reingreso automático de la orden de servicio
//rechazada o anulada.
/*
 * @author Alex Gómez <algomez@telconet.ec>
 * @version 1.1 30-09-2022   Se agrega combo para permitir reingreso de servicios adicionales
 */
function reingresoAutomatico() {

    if (jsonDatos === null || Ext.isEmpty(jsonDatos)) {
        mensajeWarning('Error al obtener los datos, por favor intente de nuevo y<br/>'+
                       'si el problema persiste comuniquese con Sistemas.');
        return;
    }

    var objData  = '';
    var message  = '';
    var status   = true;
    var load     = false;

    var dataJson = {'intIdServicio' : jsonDatos.idServicio,
                    'intIdPersona'  : jsonDatos.idPersona,
                    'soloValidar'   : 'SI'};

    //Proceso para validar si la ventana de reingreso de orden de servicio
    //se debe habilitar
    Ext.get(document.body).mask('Procesando...');
    Ext.Ajax.request({
        url    : urlAjaxReingresoOrdenServicio,
        method : 'post',
        timeout: 900000,
        async  : false,
        params : {
            'datos' : Ext.JSON.encode(dataJson)
        },
        success: function (response) {
            Ext.get(document.body).unmask();
            objData = Ext.JSON.decode(response.responseText);
            message = typeof objData.message === 'undefined' ? '' : objData.message;
            status  = typeof objData.status === 'undefined' ? false : objData.status;
        },
        failure: function (result) {
            Ext.get(document.body).unmask();
            message = 'Error al intentar validar el proceso. ('+result.statusText+')';
            status  = false;
        }
    });

    if (!status)
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: message,
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR,
            closable: false,
            multiline: false,
            buttonText: {ok: 'Cerrar'},
            fn: function (buttonValue) {
                if (buttonValue === "ok") {
                    store.load();
                }
            }
        });

        return;
    }

    Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto'}        ,
            {name: 'valor'                 , type: 'string'}
        ]
    });

    Ext.define('FormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id'         , type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });

    var storePersonaFormasContacto = new Ext.data.Store({
        model       : 'PersonaFormasContactoModel',
        autoDestroy : true,
        autoLoad    : true,
        proxy : {
            type   : 'ajax',
            method : 'post',
            url    : url_ajaxFormasContactoPuntoPorTipo,
            simpleSortMode : true,
            reader : {
                type : 'json',
                root : 'personaFormasContacto',
                totalProperty : 'total'
            },
            extraParams : {
                puntoId : puntoId
            }
        }
    });

    var storeFormasContacto = new Ext.data.Store({
        model       : 'FormasContactoModel',
        autoDestroy : true,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    : url_formasContactoPorCodigoAjax,
            reader : {
                type : 'json',
                root : 'formasContacto'
            }
        }
    });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit : 2
    });

    var gridFormaContacto = Ext.create('Ext.grid.Panel', {
        id      : 'gridFormaContacto',
        store   : storePersonaFormasContacto,
        width   : 600,
        height  : 300,
        columns : [
            {
                text      : '<b>Forma Contacto</b>',
                dataIndex : 'formaContacto',
                width     : 200,
                editor    : new Ext.form.field.ComboBox({
                    id            : 'id',
                    name          : 'formaContacto',
                    valueField    : 'descripcion',
                    displayField  : 'descripcion',
                    triggerAction : 'all',
                    store         : storeFormasContacto,
                    listClass     : 'x-combo-list-small',
                    lazyRender    : true,
                    selectOnTab   : true,
                    typeAhead     : true
                })
            }, {
                text      : '<b>Valor</b>',
                dataIndex : 'valor',
                width     :  350,
                align     : 'right',
                editor    : {
                    width      : '80%',
                    xtype      : 'textfield',
                    fieldStyle : 'text-transform: lowercase',
                    allowBlank : false,
                    listeners  : {
                        blur: function(field, e) {
                            field.setValue(field.getValue().toLowerCase());
                        }
                    }
                }
            },{
                xtype    : 'actioncolumn',
                width    : 45,
                sortable : false,
                items    : [{
                    iconCls : "button-grid-quitarRed",
                    tooltip : 'Borrar Forma Contacto',
                    handler: function(grid, rowIndex, colIndex) {
                        storePersonaFormasContacto.removeAt(rowIndex);
                    }
                }]
            }
        ],
        selModel: {
            selType: 'cellmodel'
        },
        tbar: [{
            text : '<label style="color:blue;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>'+
                    '&nbsp;<b>Agregar</b>',
            handler : function(){
                var r = Ext.create('PersonaFormasContactoModel', {
                    idPersonaFormaContacto: '',
                    formaContacto: '',
                    valor: ''
                });
                storePersonaFormasContacto.insert(0, r);
                cellEditing.startEditByPosition({row: 0, column: 0});
            }
        }],
        plugins: [cellEditing]
    });

    //Mensaje Informativo
    var infomessage = Ext.create('Ext.Component',{
       html : "<div class='infomessage'>"+
                "Todo n&uacutemero de tel&eacute;fono debe iniciar con el c&oacute;digo de area correspondiente"+
              "</div>"+
              "<div class='infomessage'>"+
                "Todo n&uacutemero de tel&eacute;fono internacional debe tener entre 7 y 15 d&iacute;gitos"+
              "</div>",
       width : 600
    });

    //Model
    Ext.define('ListModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', type:'int'},
            {name:'nombre', type:'string'}
        ]
    });

    //==== Inicio - Punto de cobertura
    var storePtosCobertura = new Ext.data.Store({
        model    : 'ListModel',
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    : url_puntoscobertura,
            reader : {
                type: 'json',
                root: 'jurisdicciones'
            }
        },
        listeners : {
            beforeload : function() {
                load = true;
            },
            load: function(store) {

                Ext.get('formPanelDependeEdificio').unmask();

                var comboPtosCobertura  = Ext.getCmp("comboPtosCobertura");
                var intIdPuntoCobertura = typeof jsonDatos.intIdPuntoCobertura === 'undefined' ? ''
                                               : jsonDatos.intIdPuntoCobertura;

                if (comboPtosCobertura && !Ext.isEmpty(intIdPuntoCobertura)
                        && store.findRecord('id', intIdPuntoCobertura)
                        && comboPtosCobertura.getValue() !== intIdPuntoCobertura)
                {
                    Ext.get('formPanelDependeEdificio').mask('Cargando datos...');

                    comboPtosCobertura.setValue(intIdPuntoCobertura);
                    storeCantones.proxy.extraParams = {'idjurisdiccion': intIdPuntoCobertura};
                    storeCantones.load();
                }
            }
        }
    });

    var comboPtosCobertura = new Ext.form.ComboBox({
        xtype        : 'combobox',
        store        :  storePtosCobertura,
        id           : 'comboPtosCobertura',
        name         : 'comboPtosCobertura',
        valueField   : 'id',
        displayField : 'nombre',
        fieldLabel   : '<b>Ptos. Cobertura</b>',
        emptyText    : 'Seleccione..',
        queryMode    : 'remote',
        width        :  500,
        listeners    : {
            select : {
                fn: function(combo) {
                    if (Ext.getCmp("combo_cantones")) {
                        Ext.get('formPanelDependeEdificio').mask('Cargando datos...');
                        Ext.getCmp("combo_cantones").setValue(null);
                        Ext.getCmp("combo_cantones").setRawValue(null);
                        Ext.getCmp("comboParroquias").setValue('');
                        Ext.getCmp("comboParroquias").setRawValue('');
                        Ext.getCmp("comboParroquias").setDisabled(true);
                        Ext.getCmp("comboSectores").setValue('');
                        Ext.getCmp("comboSectores").setRawValue('');
                        Ext.getCmp("comboSectores").setDisabled(true);
                        storeCantones.removeAll();
                        storeCantones.proxy.extraParams = {'idjurisdiccion' : combo.getValue()};
                        storeCantones.load();                        
                    }
                }
            }
        }
    });
    //==== Fin - Punto de cobertura

    //==== Inicio - Cantones -combo_cantones
    var storeCantones = new Ext.data.Store({
        model : 'ListModel',
        proxy : {
            type   : 'ajax',
            method : 'post',
            url    : url_cantones,
            reader : {
                type : 'json',
                root : 'cantones'
            }
        },
        listeners : {

            load: function(store) {

                Ext.get('formPanelDependeEdificio').unmask();

                var combo_cantones = Ext.getCmp("combo_cantones");
                var intIdCanton   = typeof jsonDatos.intIdCanton === 'undefined' ? ''
                                         : jsonDatos.intIdCanton;

                if (combo_cantones && !Ext.isEmpty(intIdCanton)
                        && store.findRecord('id', intIdCanton)
                        && combo_cantones.getValue() !== intIdCanton)
                {
                    Ext.get('formPanelDependeEdificio').mask('Cargando datos...');

                    combo_cantones.setValue(intIdCanton);
                    Ext.getCmp("combo_cantones").setDisabled(false);
                    storeParroquia.proxy.extraParams = {'idcanton' : intIdCanton};
                    storeParroquia.load();
                }
            }
        }
    });

    combo_cantones = new Ext.form.ComboBox({
        xtype        : 'combobox',
        store        :  storeCantones,
        id           : 'combo_cantones',
        name         : 'combo_cantones',
        valueField   : 'id',
        displayField : 'nombre',
        fieldLabel   : '<b>Cantón</b>',
        emptyText    : 'Seleccione..',
        queryMode    : 'remote',
        width        :  500,
        listeners    : {
            select : {
                fn: function(combo) {
                    if (Ext.getCmp("comboParroquias")) {
                        Ext.get('formPanelDependeEdificio').mask('Cargando datos...');
                        Ext.getCmp("comboParroquias").setValue('');
                        Ext.getCmp("comboParroquias").setRawValue('');
                        Ext.getCmp("comboParroquias").setDisabled(false);
                        Ext.getCmp("comboSectores").setValue('');
                        Ext.getCmp("comboSectores").setRawValue('');
                        Ext.getCmp("comboSectores").setDisabled(true);
                        storeParroquia.removeAll();
                        storeParroquia.proxy.extraParams = {'idcanton' : combo.getValue()};
                        storeParroquia.load();
                        generaLogin();
                    }
                }
            }
        }
    });
    //==== Fin - Cantones

    //==== Inicio - Parroquias
    var storeParroquia = new Ext.data.Store({
        model : 'ListModel',
        proxy : {
            type   : 'ajax',
            method : 'post',
            url    : url_lista_parroquias,
            reader : {
                type : 'json',
                root : 'parroquias'
            }
        },
        listeners : {

            load: function(store) {

                Ext.get('formPanelDependeEdificio').unmask();

                var comboParroquias = Ext.getCmp("comboParroquias");
                var intIdParroquia  = typeof jsonDatos.intIdParroquia === 'undefined' ? ''
                                           : jsonDatos.intIdParroquia;

                if (comboParroquias && !Ext.isEmpty(intIdParroquia)
                        && store.findRecord('id', intIdParroquia)
                        && comboParroquias.getValue() !== intIdParroquia)
                {
                    Ext.get('formPanelDependeEdificio').mask('Cargando datos...');

                    comboParroquias.setValue(intIdParroquia);
                    Ext.getCmp("comboParroquias").setDisabled(false);
                    storeSectores.proxy.extraParams = {'idparroquia' : intIdParroquia};
                    storeSectores.load();
                }
            }
        }
    });

    var comboParroquias = new Ext.form.ComboBox({
        xtype        : 'combobox',
        store        :  storeParroquia,
        id           : 'comboParroquias',
        name         : 'comboParroquias',
        valueField   : 'id',
        displayField : 'nombre',
        fieldLabel   : '<b>Parroquia</b>',
        emptyText    : 'Seleccione..',
        queryMode    : 'remote',
        width        :  500,
        listeners    : {
            select : {
                fn: function(combo) {
                    if (Ext.getCmp("comboSectores")) {
                        Ext.get('formPanelDependeEdificio').mask('Cargando datos...');
                        Ext.getCmp("comboSectores").setValue('');
                        Ext.getCmp("comboSectores").setRawValue('');
                        Ext.getCmp("comboSectores").setDisabled(false);
                        storeSectores.removeAll();
                        storeSectores.proxy.extraParams = {'idparroquia' : combo.getValue()};
                        storeSectores.load();
                    }
                }
            }
        }
    });
    //==== FIN - Parroquias

    //==== Inicio - Sectores
    var storeSectores = new Ext.data.Store({
        model : 'ListModel',
        proxy : {
            type   : 'ajax',
            method : 'post',
            url    : url_lista_sectores,
            reader : {
                type : 'json',
                root : 'sectores'
            }
        },
        listeners : {

            load: function(store) {

                Ext.get('formPanelDependeEdificio').unmask();

                var comboSectores = Ext.getCmp("comboSectores");
                var intIdSector   = typeof jsonDatos.intIdSector === 'undefined' ? ''
                                         : jsonDatos.intIdSector;

                if (comboSectores && !Ext.isEmpty(intIdSector)
                        && store.findRecord('id', intIdSector)
                        && comboSectores.getValue() !== intIdSector)
                {
                    Ext.getCmp("comboSectores").setDisabled(false);
                    comboSectores.setValue(intIdSector);
                    
                    if (jsonDatos.strEditaDatosGeograficos != 'S')
                    {
                        Ext.getCmp('comboPtosCobertura').disable();
                        Ext.getCmp('combo_cantones').disable();
                        Ext.getCmp('comboParroquias').disable();
                        Ext.getCmp('comboSectores').disable();
                        Ext.getCmp("login_nuevo").setReadOnly(true);  
                        Ext.getCmp('login_nuevo').disable();
                    }
                }
            }
        }
    });

    var comboSectores = new Ext.form.ComboBox({
        xtype        : 'combobox',
        store        :  storeSectores,
        id           : 'comboSectores',
        name         : 'comboSectores',
        valueField   : 'id',
        displayField : 'nombre',
        fieldLabel   : '<b>Sector</b>',
        emptyText    : 'Seleccione..',
        queryMode    : 'remote',
        width        :  500
    });
    //==== Fin - Sectores
   
    function generaLogin() {

        var cantonId = combo_cantones.getValue();
       
        $.ajax({
            type: "POST",
            data: "idCanton=" + cantonId + "&idCliente=" + clienteId + "&tipoNegocio=" + tipoNegocioId,
            url: url_genera_login,
            beforeSend: function () {
                $('#img-valida-login').attr("src", url_img_loader);
            },
            success: function (msg) {
                if (msg != '') {                   
                    Ext.getCmp('login_nuevo').setValue(msg);                    
                    Ext.getCmp("login_nuevo").setReadOnly(true);                                      
                    $('#img-valida-login').attr("title", "login correcto");
                    $('#img-valida-login').attr("src", url_img_check);                   
                    validaLogin();
                }
                else
                {
                    alert("Error: No se pudo generar el login ingresado.");
                }
            }
        });
    }			

    function validaLogin() {
        currentLogin = Ext.getCmp('login_nuevo').getValue();      
        if (currentLogin != "")
        {
            $.ajax({
                type: "POST",
                data: "login=" + currentLogin,
                url: url_valida_login,
                beforeSend: function () {
                    $('#img-valida-login').attr("src", url_img_loader);
                },
                success: function (msg) {
                    if (msg != '') {
                        if (msg == "no") {
                            flagLoginCorrecto = 1;
                            $('#img-valida-login').attr("title", "login correcto");
                            $('#img-valida-login').attr("src", url_img_check);
                        }
                        if (msg == "si") {
                            flagLoginCorrecto = 0;
                            $('#img-valida-login').attr("title", "login incorrecto");
                            $('#img-valida-login').attr("src", url_img_delete);
                            $('#login_nuevo').focus();
                            alert("Login ya existente. Favor Corregir");
                        }

                    } else
                    {
                        alert("Error: No se pudo validar el login ingresado.");
                    }
                }
            });
        } else
        {
            flagLoginCorrecto = 0;
            $('#img-valida-login').attr("title", "login incorrecto");
            $('#img-valida-login').attr("src", url_img_delete);
            $('#login_nuevo').focus();
        }
    }

    //Panel de depende de Edificio
    var formPanelDependeEdificio = Ext.create('Ext.form.Panel',{
        id            : 'formPanelDependeEdificio',
        title         : 'Datos Adicionales',
        bodyPadding   :  07,
        waitMsgTarget : true,
        border        : false,
        frame         : false,
        fieldDefaults : {
            labelAlign : 'left',
            labelWidth :  200,
            anchor     : '100%',
            msgTarget  : 'side'
        },
        items :
        [
            {
                xtype : 'fieldset',
                title : '&nbsp;<label style="color:blue;">'+
                            '<i class="fa fa-tag" aria-hidden="true"></i></label>'+
                        '&nbsp;<b>Información del Punto</b>',
                items :
                [
                    comboPtosCobertura,
                    combo_cantones,
                    comboParroquias,
                    comboSectores,
                    
                    {
                        xtype  : 'panel',
                        border : false,
                        layout : {type: 'hbox', align: 'stretch'},
                        items  : [
                                    {
                                        xtype: 'textfield',
                                        id: 'login_nuevo',
                                        name: 'login_nuevo',
                                        fieldLabel: '<b>Nuevo Login</b>',
                                        displayField: '',
                                        value: '',
                                        valueField: '',
                                        maxLength: 250,
                                        width: 400
                                    },
                                    {
                                        xtype: 'image',
                                        id: 'img-valida-login',
                                        name: 'img-valida-login',
                                        src: url_img_delete,
                                        flex: 1
                                    }
                                 ]
                    },                        
                    {
                        xtype      : 'combobox',
                        id         : 'comboDependeEdificio',
                        name       : 'comboDependeEdificio',
                        fieldLabel : '<b>Depende de Edificio:</b>',
                        width      :  270,
                        value      :  Ext.isEmpty(jsonDatos.strDependeDeEdificio) ? 'N' : jsonDatos.strDependeDeEdificio,
                        store      : [['N','No'],['S','Si']],
                        listeners  : {
                            select : function(combo) {
                                if (combo.value === 'S') {
                                    Ext.getCmp("dependeEdificioDesc").setVisible(true);
                                    Ext.getCmp("btnBuscarEdificio").setVisible(true);
                                } else {
                                    Ext.getCmp("dependeEdificioDesc").setVisible(false);
                                    Ext.getCmp("btnBuscarEdificio").setVisible(false);
                                }
                            }
                        }
                    },
                    {
                        xtype  : 'panel',
                        border : false,
                        layout : {type: 'hbox', align: 'stretch'},
                        items  : [
                            {
                                xtype     : 'textfield',
                                id        : 'dependeEdificioDesc',
                                name      : 'dependeEdificioDesc',
                                fieldLabel: '<b>Edificio Padre:</b>',
                                readOnly  : true,
                                hidden    : jsonDatos.strDependeDeEdificio !== 'S',
                                value     : jsonDatos.strElementoEdificio,
                                width     : 480
                            },
                            {
                                xtype       : 'button',
                                id          : 'btnBuscarEdificio',
                                name        : 'btnBuscarEdificio',
                                text        : '<i class="fa fa-search" aria-hidden="true"></i>',
                                tooltipType : 'title',
                                tooltip     : 'Buscar Edificio',
                                hidden      : jsonDatos.strDependeDeEdificio !== 'S',
                                handler : function() {
                                    showEdificios();
                                }
                            }
                        ]
                    },
                    {
                        xtype     : 'textfield',
                        id        : 'dependeEdificioId',
                        name      : 'dependeEdificioId',
                        readOnly  : true,
                        hidden    : true,
                        value     : jsonDatos.intElementoEdificioId
                    },
                    {
                        xtype     : 'textfield',
                        id        : 'nuevoNodoCliente',
                        name      : 'nuevoNodoCliente',
                        readOnly  : true,
                        hidden    : true
                    },
                    {
                        xtype     : 'textfield',
                        id        : 'tipoEdificio',
                        name      : 'tipoEdificio',
                        readOnly  : true,
                        hidden    : true
                    },
                    {
                        xtype      : 'combobox',
                        id         : 'comboRSAdicionales',
                        name       : 'comboRSAdicionales',
                        fieldLabel : '<b>Reingreso de Servicios Adicionales:</b>',
                        width      :  270,
                        value      :  'N',
                        store      : [['N','No'],['S','Si']]
                    }
                ]
            }
        ]
    });

    //Panel de formas de contactos
    var formPanelFormaContacto = Ext.create('Ext.form.Panel',{
        id          : 'panelFormaContacto',
        title       : 'Formas de contacto',
        bodyPadding : 10,
        frame       : false,
        items       : [gridFormaContacto,infomessage]
    });

    //Panel del mapa
    var formPanelMapa = Ext.create('Ext.form.Panel', {
        id          : 'formPanelMapa',
        title       : 'Coordenadas del punto',
        BodyPadding : 10,
        frame       : false,
        items       :
        [
            {html: "<div id='mapa' style='width:620px; height:340px'></div>"},
            {
                layout: {
                    type    : 'table',
                    tdAttrs : {style: 'padding: 5px;'},
                    columns : 5,
                    align   : 'stretch'
                },
                items:
                [
                    {width: '10%', border: false},
                    {
                        xtype : 'label',
                        cls   : 'label-coordenada',
                        text  : 'Coordenada Anterior',
                        width : 280
                    },
                    { width: '15%', border: false},
                    { width: '15%', border: false},
                    { width:  10  , border: false},
                    { width: '10%', border: false},
                    {
                        xtype     : 'textfield',
                        id        : 'latitudActual',
                        name      : 'latitudActual',
                        fieldLabel: 'Latitud',
                        readOnly  : true,
                        value     : jsonDatos.latitud,
                        width     : 280
                    },
                    { width: '10%', border: false},
                    {
                        xtype      : 'textfield',
                        id         : 'longitudActual',
                        name       : 'longitudActual',
                        fieldLabel : 'Longitud',
                        readOnly   : true,
                        value      : jsonDatos.longitud,
                        width      : 280
                    },
                    { width :  10  , border: false},
                    { width : '10%', border: false},
                    {
                        xtype      : 'label',
                        cls        : 'label-coordenada',
                        fieldStyle : "font-weight: bold",
                        text       : 'Coordenada Actual',
                        width      : 280
                    },
                    { width: '15%', border: false},
                    { width: '15%', border: false},
                    { width:  10  , border: false},
                    { width: '10%', border: false},
                    {
                        xtype       : 'textfield',
                        hideTrigger : true,
                        id          : 'latitudSugerida',
                        name        : 'latitudSugerida',
                        fieldLabel  : 'Latitud',
                        value       : jsonDatos.latitud,
                        readOnly    : true,
                        width       : 280
                    },
                    { width: '10%', border: false},
                    {
                        xtype       : 'textfield',
                        hideTrigger : true,
                        id          : 'longitudSugerida',
                        name        : 'longitudSugerida',
                        fieldLabel  : 'Longitud',
                        value       : jsonDatos.longitud,
                        readOnly    : true,
                        width       : 280
                    },
                    { width: 10, border: false}
                ]
            }
        ]
    });

    var tabs = new Ext.TabPanel({
        xtype      :'tabpanel',
        activeTab  : 0,
        autoScroll : false,
        items      : [formPanelDependeEdificio,formPanelMapa,formPanelFormaContacto],
        listeners  : {
            tabchange: function(objPanel,objPanelActual) {
                if (objPanelActual.id === 'formPanelDependeEdificio' && !load) {
                    Ext.get('formPanelDependeEdificio').mask('Cargando datos...');
                    storePtosCobertura.load();
                }
               if (objPanelActual.id === 'formPanelMapa') {                   
                    mostrarMapa(jsonDatos.latitud, jsonDatos.longitud);
                }
            },           
        },
        layoutOnTabChange : true
    });

    var winVerificarDatos = Ext.widget('window', {
        id          : 'winVerificarDatos',
        title       : 'Verificación de Datos',
        layout      : 'fit',
        modal       : true,
        resizable   : false,
        closable    : false,
        items       : [tabs],
        buttonAlign : 'right',
        buttons     : [
            {
                height : 25,
                text   : '<label style="color:green;"><i class="fa fa-check-circle" aria-hidden="true"></i></label>'+
                         '&nbsp;<b>Continuar</b>',
                handler: function()
                {                    
                    var form     = formPanelMapa.getForm();
                    var latitud  = null;
                    var longitud = null;

                    if (form.isValid()) {

                        var array_data    = new Array();
                        var valoresVacios = false;
                        var variable      = '';

                        for (var i = 0; i < gridFormaContacto.getStore().getCount(); i++)
                        {
                            variable = gridFormaContacto.getStore().getAt(i).data;

                            for (var key in variable)
                            {
                                var valor = variable[key];

                                if (key === 'valor' && valor === '') {
                                    valoresVacios = true;
                                } else {
                                    array_data.push(valor);
                                }
                            }
                        }

                        if (valoresVacios) {
                            mensajeWarning('Hay formas de contacto que tienen valor vacio, por favor corregir.');
                            return;
                        }

                        if (Ext.isEmpty(Ext.getCmp('comboPtosCobertura').value)) {
                            mensajeWarning('Seleccione el punto de cobertura.');
                            return;
                        }

                        if (Ext.isEmpty(Ext.getCmp('combo_cantones').value)) {
                            mensajeWarning('Seleccione el cantón.');
                            return;
                        }

                        if (Ext.isEmpty(Ext.getCmp('comboParroquias').value)) {
                            mensajeWarning('Seleccione la Parroquia.');
                            return;
                        }

                        if (Ext.isEmpty(Ext.getCmp('comboSectores').value)) {
                            mensajeWarning('Seleccione el sector.');
                            return;
                        }

                        if (Ext.getCmp('comboDependeEdificio').value === 'S'
                                && Ext.isEmpty(Ext.getCmp('dependeEdificioDesc').value)) {
                            mensajeWarning('Ingrese el Edificio Padre.');
                            return;
                        }

                        if (Ext.isEmpty(Ext.getCmp('latitudSugerida').value)
                                || Ext.isEmpty(Ext.getCmp('longitudSugerida').value))
                        {
                            mensajeWarning('Ni la Latitud ni la Longidud de las coordenadas actuales<br/>'+
                                           'pueden ser nulos');
                            return;
                        }

                        if ((Ext.getCmp('latitudSugerida').value !== Ext.getCmp('latitudActual').value)
                                || Ext.getCmp('longitudSugerida').value !== Ext.getCmp('longitudActual').value)
                        {
                            latitud  = Ext.getCmp('latitudSugerida').value;
                            longitud = Ext.getCmp('longitudSugerida').value;
                        }

                        //Data a enviar al controlador
                        dataJson = {'intIdPersona'          : jsonDatos.idPersona,
                                    'intIdPunto'            : jsonDatos.idPunto,
                                    'intIdServicio'         : jsonDatos.idServicio,
                                    'strEstado'             : jsonDatos.estado,
                                    'strTipoOrden'          : jsonDatos.tipoOrden,
                                    'strFlujoCompleto'      : 'completo',
                                    'strLatitud'            : latitud,
                                    'strLongitud'           : longitud,
                                    'intElementoEdificioId' : Ext.getCmp('dependeEdificioId').value,
                                    'strElementoEdificio'   : Ext.getCmp('dependeEdificioDesc').value,
                                    'strDependeDeEdificio'  : Ext.getCmp('comboDependeEdificio').value,
                                    'strNuevoNodoCliente'   : Ext.getCmp('nuevoNodoCliente').value,
                                    'intTipoEdificio'       : Ext.getCmp('tipoEdificio').value,
                                    'intPuntoCobertura'     : Ext.getCmp('comboPtosCobertura').value,
                                    'intCanton'             : Ext.getCmp('combo_cantones').value,
                                    'intParroquia'          : Ext.getCmp('comboParroquias').value,
                                    'intSector'             : Ext.getCmp('comboSectores').value,
                                    'strLoginNuevo'         : Ext.getCmp('login_nuevo').value,
                                    'strRSAdicionales'      : Ext.getCmp('comboRSAdicionales').value,
                                    'strFormasContactos'    : array_data.toString()};

                        Ext.MessageBox.show({
                            title      : "Mensaje",
                            msg        : '¿Está seguro de continuar con el proceso?',
                            closable   : false,
                            multiline  : false,
                            icon       : Ext.Msg.QUESTION,
                            buttons    : Ext.Msg.YESNO,
                            buttonText : {yes: 'Si', no: 'No'},
                            fn: function (buttonValue)
                            {
                                if (buttonValue === "yes") {

                                    Ext.MessageBox.wait('Ejecutando proceso...');
                                    Ext.Ajax.request({
                                        url     : urlAjaxReingresoOrdenServicio,
                                        method  : 'post',
                                        timeout : 900000,
                                        params  : {
                                            'datos' : Ext.JSON.encode(dataJson)
                                        },
                                        success: function (response) {

                                            objData = Ext.JSON.decode(response.responseText);
                                            message = objData.message;
                                            status  = typeof objData.status === 'undefined' ? false : objData.status;

                                            Ext.MessageBox.show({
                                                title      : status ? 'Mensaje' : 'Error',
                                                msg        : message,
                                                buttons    : Ext.MessageBox.OK,
                                                icon       : status ? Ext.MessageBox.INFO : Ext.MessageBox.ERROR,
                                                closable   : false,
                                                multiline  : false,
                                                buttonText : {ok: 'Cerrar'},
                                                fn: function (buttonValue) {
                                                    if (buttonValue === "ok") {
                                                        winVerificarDatos.close();
                                                        winVerificarDatos.destroy();
                                                        store.load();
                                                    }
                                                }
                                            });
                                        },
                                        failure: function (result) {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            },
            {
                height : 25,
                text   : '<label style="color:red;"><i class="fa fa-times-circle" aria-hidden="true"></i></label>'+
                         '&nbsp;<b>Cerrar</b>',
                handler: function() {
                    winVerificarDatos.close();
                    winVerificarDatos.destroy();
                }
            }
        ]
    }).show();
    storePtosCobertura.load();
    mostrarMapa(jsonDatos.latitud, jsonDatos.longitud);
}

function mensajeWarning(mensaje) {
        Ext.MessageBox.show({
        title      : 'Alerta',
        msg        : mensaje,
        buttons    : Ext.MessageBox.OK,
        icon       : Ext.MessageBox.WARNING,
        closable   : false,
        multiline  : false,
        buttonText : {ok: 'Ok'}
    });
}

function editarPlantillaComisionista( intIdServicio )
{
    //Crea el plugin para la edición de filas del grid gridParametrosDet
    var rowEditingPlantillaComisionista         = null;
    var permisoEditarPlantillaComisionistas     = $("#ROLE_9-5317");
    var boolPermisoEditarPlantillaComisionistas = (typeof permisoEditarPlantillaComisionistas === 'undefined') ? false 
                                                   : (permisoEditarPlantillaComisionistas.val() == 1 ? true : false);

    var rowEditingPlantillaComisionista = Ext.create('Ext.grid.plugin.RowEditing',
    {
        pluginId: 'rowEditing',
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners:
        {
            beforeedit: function(editor, context)
            {
                if( boolPermisoEditarPlantillaComisionistas )
                {
                    intIdComisionistaSelected   = 0;
                    var boolEditarComisionistas = context.record.getData().boolEditarComisionistas;
                    
                    if( boolEditarComisionistas )
                    {
                        var intIdPersonaEmpresaRol  = context.record.getData().intIdPersonaEmpresaRol;
                        var strTipoComisionista     = context.record.getData().strTipoComisionista;
                        var strGrupoProducto        = context.record.getData().strGrupoProducto;
                        var intIdCargo              = context.record.getData().intIdCargo;

                        if( strTipoComisionista == "SUBGERENTE" )
                        {
                            gridPlantillaComisionista.getPlugin('rowEditing').editor.form.findField('strPersonaComisionista').disable();
                        }
                        else
                        {
                            gridPlantillaComisionista.getPlugin('rowEditing').editor.form.findField('strPersonaComisionista').enable();
                            storePersonalComisionista.getProxy().extraParams.intIdPersonaEmpresaRol = intIdPersonaEmpresaRol;
                            storePersonalComisionista.getProxy().extraParams.strTipoComisionista    = strTipoComisionista;
                            storePersonalComisionista.getProxy().extraParams.strGrupoProducto       = strGrupoProducto;
                            storePersonalComisionista.getProxy().extraParams.intIdCargo             = intIdCargo;
                        }//( strTipoComisionista == "SUBGERENTE" )
                    }
                    else
                    {
                        return false;
                    }//( boolEditarComisionistas )
                }
                else
                {
                    return false;
                }
            },//beforeedit
            afteredit: function(roweditor, changes, record, rowIndex)
            {
                //Valida que los nuevos campos a ingresar no sean nulos.
                if( !Ext.isEmpty(changes.newValues.strPersonaComisionista) && !Ext.isEmpty(changes.newValues.floatComisionVenta) )
                {
                    var objStore                    = Ext.getCmp('gridPlantillaComisionista').getStore();
                    var recStore                    = objStore.getAt(changes.rowIdx);
                    var floatTotalComisionVenta     = 0;
                    var strTipoComisionista         = recStore.get('strTipoComisionista');
                    var intIdServicioSelected       = recStore.get('intIdServicio');
                    var strRolComisionista          = recStore.get('strRolComisionista');
                    var strPersonaComisionistaNew   = changes.newValues.strPersonaComisionista;
                    var strPersonaComisionistaOld   = changes.originalValues.strPersonaComisionista;
                    var floatComisionVentaNew       = changes.newValues.floatComisionVenta;
                    var floatComisionVentaOld       = changes.originalValues.floatComisionVenta;
                    var intIdPersonaComisionistaNew = 0;
                    var intIdPersonaComisionistaOld = recStore.get('intIdPersonaEmpresaRol');
                    var intIdServicioComision       = recStore.get('intIdServicioComision');
                    var strNombreSolicitud          = "";
                    var floatComisionVentaSelected  = 0;
                    var strCaracteristicaSolicitud  = "";

                    objStore.each(function (record) 
                    {
                        if( !Ext.isEmpty(record.data.floatComisionVenta) )
                        {
                            floatTotalComisionVenta += record.data.floatComisionVenta;
                        }
                    });

                    floatTotalComisionVenta = ( Math.round(floatTotalComisionVenta * 100)/100 );

                    recStore.set('strPersonaComisionista', changes.originalValues.strPersonaComisionista);
                    recStore.set('floatComisionVenta',     changes.originalValues.floatComisionVenta);

                    if( floatTotalComisionVenta > floatValorMaxComision)
                    {
                        Ext.Msg.alert('Atención', 'La sumatoria de las comisiones ingresadas no debe sobrepasar el valor máximo permitido.<br/>' +
                                                  '<b>Valor Máximo permitido:</b> ' + floatValorMaxComision);
                    }
                    else
                    {
                        recStore.set('strPersonaComisionista', changes.originalValues.strPersonaComisionista);
                        recStore.set('floatComisionVenta',     changes.originalValues.floatComisionVenta);

                        gridPlantillaComisionista.getStore().rejectChanges();

                        if( strPersonaComisionistaNew !== strPersonaComisionistaOld )
                        {
                            strNombreSolicitud          += "SOLICITUD CAMBIO PERSONAL PLANTILLA|";
                            strCaracteristicaSolicitud  += "CAMBIO_" + strTipoComisionista + "|";
                            intIdPersonaComisionistaNew = intIdComisionistaSelected;
                        }//( strPersonaComisionistaNew !== strPersonaComisionistaOld )

                        if( floatComisionVentaNew != floatComisionVentaOld )
                        {
                            strNombreSolicitud         += "SOLICITUD CAMBIO COMISION";
                            strCaracteristicaSolicitud += "CAMBIO_COMISION";
                            floatComisionVentaSelected = floatComisionVentaNew;
                        }//( floatComisionVentaNew != floatComisionVentaOld )

                        if( intIdPersonaComisionistaNew > 0 || floatComisionVentaSelected > 0 )
                        {
                            objValidarSolicitudes.request
                            ({
                                url: strUrlValidarSolicitudes,
                                method: 'post',
                                timeout: 9000000,
                                params:
                                {
                                    strServiciosSelected: intIdServicioSelected,
                                    strNombreSolicitud: strNombreSolicitud,
                                    strCaracteristicaSolicitud: strCaracteristicaSolicitud,
                                    intIdServicioComision: intIdServicioComision
                                },
                                success: function(response)
                                {
                                    if( response.responseText == "OK" )
                                    {
                                        var strMensajeAlerta = "Se crearán las correspondientes solicitudes para la aprobación del cambio de ";

                                        if( intIdPersonaComisionistaNew > 0 && floatComisionVentaSelected > 0 )
                                        {
                                            strMensajeAlerta += "<b>" + strRolComisionista + "</b> y cambio de <b>COMISIÓN</b>.";
                                        }//( intIdPersonaComisionistaNew > 0 && floatComisionVentaSelected > 0 )
                                        else if( intIdPersonaComisionistaNew > 0 )
                                        {
                                            strMensajeAlerta += "<b>" + strRolComisionista + "</b>.";
                                        }//( intIdPersonaComisionistaNew > 0 )
                                        else
                                        {
                                            strMensajeAlerta += "<b>COMISIÓN</b>.";
                                        }

                                        var messagebox = Ext.MessageBox.show
                                        ({
                                           title: 'Alerta',
                                            width: 400,
                                            cls: 'msg_floaitng',
                                            buttons: Ext.MessageBox.YESNO,
                                            msg: strMensajeAlerta + ' Desea continuar?',
                                            fn: function(answer)
                                            {
                                                if( answer === "yes" )
                                                {
                                                    Ext.MessageBox.wait("Procesando la información...");

                                                    Ext.Ajax.request
                                                    ({
                                                        timeout: 9000000,
                                                        url: strUrlCrearSolicitudesComisionistas,
                                                        params:
                                                        {
                                                            intIdPersonaComisionistaNew: intIdPersonaComisionistaNew,
                                                            intIdPersonaComisionistaOld:intIdPersonaComisionistaOld,
                                                            strPersonaComisionistaNew: strPersonaComisionistaNew,
                                                            strPersonaComisionistaOld: strPersonaComisionistaOld,
                                                            intIdServicioComision: intIdServicioComision,
                                                            intIdServicioSelected: intIdServicioSelected,
                                                            floatComisionVentaNew: floatComisionVentaNew,
                                                            floatComisionVentaOld: floatComisionVentaOld,
                                                            strTipoComisionista: strTipoComisionista,
                                                            strNombreSolicitud: strNombreSolicitud
                                                        },
                                                        method: 'get',
                                                        success: function(response) 
                                                        {                
                                                            var mensajeRespuesta = response.responseText;

                                                            if( "S" == mensajeRespuesta )
                                                            {
                                                                Ext.MessageBox.hide();
                                                                store.add(record);
                                                                calculaTotal();
                                                                limpia();
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Atención', mensajeRespuesta);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.Msg.alert('Error ', 'Error al procesar la edición de la plantilla de ' + 
                                                                                    'comisionistas.');
                                                        }
                                                    });
                                                }//( answer === "yes" )
                                            },
                                            icon: Ext.MessageBox.INFO
                                        });

                                        Ext.Function.defer(function ()
                                        {
                                            messagebox.zIndexManager.bringToFront(messagebox);
                                        },100);
                                    }
                                    else
                                    {
                                        var objMensajeAlert = Ext.Msg.alert('Atención', response.responseText);

                                        Ext.Function.defer(function ()
                                        {
                                            objMensajeAlert.zIndexManager.bringToFront(objMensajeAlert);
                                        },100);
                                    }//( response.responseText == "OK" )
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ', 'Se presentaron errores al validar las solicitudes creadas del servicio seleccionado.');
                                }
                            });
                        }//( intIdPersonaComisionistaNew > 0 || floatComisionVentaNew > 0 )
                    }//( floatTotalComisionVenta > floatValorMaxComision)
                }
                else
                {
                    Ext.Msg.alert('Atención', 'No deben ingresar registros vacíos');
                }//( !Ext.isEmpty(changes.newValues.strPersonaComisionista) && !Ext.isEmpty(changes.newValues.floatComisionVenta) )
            }//afteredit
        }//listeners:
    });//rowEditingPlantillaComisionista
        
                            
                            
    //Define un modelo para el store storePlantillaComisionista
    Ext.define('ListaPlantillaComisionistaModel',
    {
        extend: 'Ext.data.Model',
        fields:
        [
            {name: 'intIdCargo',                 type: 'int'},
            {name: 'intIdServicio',              type: 'int'},
            {name: 'intIdServicioComision',      type: 'int'},
            {name: 'strTipoComisionista',        type: 'string'},
            {name: 'strPersonaComisionista',     type: 'string'},
            {name: 'strRolComisionista',         type: 'string'},
            {name: 'intIdPersonaEmpresaRol',     type: 'int'},
            {name: 'floatComisionVenta',         type: 'float'},
            {name: 'floatComisionMantenimiento', type: 'float'},
            {name: 'strGrupoProducto',           type: 'string'},
            {name: 'boolEditarComisionistas',    type: 'boolean'}
        ]
    });
                            
                            
    //Store que contiene la información de la plantilla de comisionista
    var storePlantillaComisionista = Ext.create('Ext.data.Store',
    {
        pageSize: 100,
        model: 'ListaPlantillaComisionistaModel',
        collapsible: false,
        autoScroll: true,
        autoLoad: true,
        proxy:
        {
            type: 'ajax',
            url: strUrlGetPlantillaComisionista,
            timeout: 900000,
            reader:
            {
                type: 'json',
                root: 'arrayResultados',
                totalProperty: 'intTotal'
            },
            extraParams:
            {
                intIdServicio: intIdServicio
            },
            simpleSortMode: true
        },
        listeners:
        {
            load: function(store)
            {
                if( !Ext.isEmpty(store.getProxy().getReader().rawData.strMensajeError) )
                {
                    Ext.Msg.alert('Error', store.getProxy().getReader().rawData.strMensajeError);
                }
            }
        }
    });
    
    
    //Define un modelo para el store storePersonalComisionista
    Ext.define('ListaPersonalComisionistaModel',
    {
        extend: 'Ext.data.Model',
        fields:
        [
            {name: 'intIdPersonaEmpresaRol', type: 'int'},
            {name: 'strEmpleado',            type: 'string'}
        ]
    });
    
    //Store que contiene la información de la plantilla de comisionista
    storePersonalComisionista = Ext.create('Ext.data.Store',
    {
        pageSize: 100,
        model: 'ListaPersonalComisionistaModel',
        collapsible: false,
        autoScroll: true,
        proxy:
        {
            type: 'ajax',
            url: strUrlGetPersonalComisionista,
            timeout: 900000,
            reader:
            {
                type: 'json',
                root: 'arrayResultados',
                totalProperty: 'intTotal'
            },
            simpleSortMode: true
        },
        listeners:
        {
            load: function(store)
            {
                if( !Ext.isEmpty(store.getProxy().getReader().rawData.strMensajeError) )
                {
                    Ext.Msg.alert('Error', store.getProxy().getReader().rawData.strMensajeError);
                }
            }
        }
    });


    //Grid que contiene la información de la plantilla de comisionistas
    gridPlantillaComisionista = Ext.create('Ext.grid.Panel',
    {
        title: 'Grid Plantilla de Comisionistas',
        store: storePlantillaComisionista,
        id: 'gridPlantillaComisionista',
        cls: 'custom-grid',
        autoScroll: false,
        plugins: [rowEditingPlantillaComisionista],
        columns:
        [
            {
                header: "boolEditarComisionistas",
                dataIndex: "boolEditarComisionistas",
                hidden: true,
            },
            {
                header: "intIdServicio", 
                dataIndex: 'intIdServicio', 
                hidden: true
            },
            {
                header: "intIdServicioComision", 
                dataIndex: 'intIdServicioComision', 
                hidden: true
            },
            {
                header: "intIdPersonaEmpresaRol", 
                dataIndex: 'intIdPersonaEmpresaRol', 
                hidden: true
            },
            {
                header: "strTipoComisionista", 
                dataIndex: 'strTipoComisionista', 
                hidden: true
            },
            {
                header: 'Rol Comisionista',
                dataIndex: 'strRolComisionista',
                width: 170
            },
            {
                header: 'Persona Comisionista ',
                dataIndex: 'strPersonaComisionista',
                width: 265,
                editor:
                {
                    xtype: 'combobox',
                    allowBlank: false,
                    fieldLabel: '',
                    id: 'cmbPersonaComisionista',
                    name: 'cmbPersonaComisionista',
                    store: storePersonalComisionista,
                    queryMode: 'remote',
                    displayField: 'strEmpleado',
                    valueField: 'strEmpleado',
                    triggerAction: 'all',
                    editable: false,
                    listeners: 
                    {
                        expand: function()
                        {
                            storePersonalComisionista.load();
                        },
                        select: function(combo, record, index)
                        {
                            intIdComisionistaSelected = record[0].data.intIdPersonaEmpresaRol;
                        }  // select
                    }  // listener
                }
            },
            {
                header: 'Comisión Venta<br/>(%)',
                dataIndex: 'floatComisionVenta',
                width: 108,
                align: 'right',
                editor:
                {
                    allowBlank: false,
                    regex: /^\d+(\.\d{1,2})?$/
                }
            }
        ],
        height: 250,
        width: 546
    });


    //Panel que contiene el grid de la plantilla de comisionista que se podrá editar
    var panelPlantillaComisionista = new Ext.Panel
    ({
        width: '100%',
        height: '100%',
        items:[gridPlantillaComisionista]
    });


    //Ventana que muestra la plantilla de comisionista
    windowsPlantillaComisionista = Ext.widget('window',
    {
        title: 'Plantilla de Comisionistas',
        height: 267,
        width: 560,
        resizable: false,
        modal: true,
        layout:
        {
            align: 'stretch',
            type: 'hbox'
        },
        items: [panelPlantillaComisionista]
    }).show();
}


/**
 * Documentación para el método 'edicionDescPresentaFactura'.
 *
 * Envia mediante post el id del servicio y la descripcion del servicio a presentarse en la Factura
 * al controlador que realiza la actualizacion.
 * 
 * @param integer    idServicio           Obtiene el IdServicio del cliente
 * @param string     descPresentaFactura  Obtiene descripcionPresentaFactura del servicio
 *
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.0 04-07-2016 
 * 
 * @author Kenth Encalada <kencalada@telconet.ec>
 * @version 1.1 23-06-2023
 */

function edicionDescPresentaFactura(idServicio,descPresentaFactura)
{
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 90,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',                    
                    columns: 1
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 250
                            },
                        items:
                            [                       
                                {
                                    xtype: 'textfield',
                                    id: 'descripcionPresentaFactura',
                                    name: 'descripcionPresentaFactura',
                                    fieldLabel: 'Descripcion Factura',
                                    displayField: '',
                                    value: descPresentaFactura,
                                    valueField: '',
                                    maxLength: 250,
                                    width: 250
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Grabar',
                        formBind: true,
                        handler: function()
                        {
                            var valor = Ext.getCmp('descripcionPresentaFactura').value;
                            if (valor == "")
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese la descripcion del servicio a presentarse en la Factura!");
                            }
                            else
                            {
                                const patronCaracteresEspeciales = /[\'^£$%&*()}{@#~?><>,|=+¬\/"]/gi;
                                const fnSendRequest = () => {
                                connDescPresentaFactura.request
                                ({
                                    url: url_editarDescPresentaFactAjax,
                                    method: 'post',
                                    waitMsg: 'Esperando Respuesta',
                                    timeout: 400000,
                                    params:
                                        {
                                            idServicio: idServicio,
                                            descripcionPresentaFactura: Ext.getCmp('descripcionPresentaFactura').value.replace(patronCaracteresEspeciales, '').trim()
                                        },
                                    success: function(response)
                                    {
                                        var respuesta = response.responseText;

                                        if (respuesta == "OK")
                                        {
                                            Ext.Msg.alert('MENSAJE ', 'Se actualizo la información correctamente.');
                                            store.load({params: {start: 0, limit: 10}});
                                            
                                        }
                                        else if(respuesta == 'ERROR WIFI')
                                        {                                            
                                            Ext.Msg.alert('Error',
                                            'No se puede editar la descripción del servicio, debido a que es un servicio Concentrador Wifi!');
                                        }
                                        else if(respuesta == 'ERROR_CANAL_TELEFONIA')
                                        {                                            
                                            Ext.Msg.alert('Error',
                                            'No se puede editar la descripción del servicio, debido a que es un servicio Canal Telefonia!');
                                        } 
                                        else
                                        {                                            
                                            Ext.Msg.alert('Error',' Se presentaron problemas al actualizar la información,' +
                                                ' favor notificar a Sistemas ');
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                                
                                winDescPresentaFactura.destroy();
                                }
                                if (prefijoEmpresa == 'TN' && (patronCaracteresEspeciales).test(valor))
                                {
                                    Ext.Msg.alert(
                                        'Alerta!', 
                                        'La descripcion de la factura contenía caracteres inválidos, por lo que se procederá con el ajuste del campo de texto.',
                                        function (btn) {
                                            fnSendRequest();
                                        });
                                } 
                                else 
                                {
                                    fnSendRequest();
                                }
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winDescPresentaFactura.destroy();
                        }
                    }
                ]
        });

    var winDescPresentaFactura = Ext.create('Ext.window.Window',
        {
            title: 'Editar Descripcion que se Presenta en la Factura',
            modal: true,
            width: 320,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function eliminarServicio(id,solicitudId,esSolucion)
{  
    var backup      = null;
    var solicitudBU = '';
    var msg         = '';
    
    if(prefijoEmpresa == 'TN')
    {
        store.each(function(record)
        {
            if (typeof record !== typeof undefined)
            {
                if (parseInt(record.get('idServicio')) === parseInt(id))
                {
                    backup      = record.get('backup');
                    solicitudBU = record.get('id_factibilidad');
                    if(backup == '' || typeof backup == typeof undefined || isNaN(backup))
                    {
                        backup = null;
                    }
                    else if(typeof backup == typeof undefined)
                    {
                        solicitudBU = '';
                    }
                }
            }
        });

        if(backup)
        {
            msg = ' Principal y su Servicio BackUp';
        }
    }
    
    
    
     connFact.request({
        url: urlGetProcesoMasivoNC,
        method: 'post',
        timeout: 400000,
        success: function(response){
       
            if (response.responseText === 'OK')
            {
                
                 Ext.Msg.confirm('Alerta', 'Se eliminará el Servicio' + msg + '. Desea continuar?', function(btn) 
                    {
                        if (btn == 'yes') 
                        {
                            if(prefijoEmpresa == 'TN' && backup)
                            {
                                Ext.MessageBox.wait("Eliminando Servicio BackUp...");
                                Ext.Ajax.request
                                    ({
                                        url: url_servicio_delete_ajax,
                                        params:
                                            {
                                                idservicio: backup,
                                                id_solicitud: solicitudBU
                                            },
                                        method: 'get',
                                        success: function(response)
                                        {
                                            Ext.MessageBox.hide();

                                            var text = response.responseText;
                                            Ext.MessageBox.wait(text + "<br/><br/>Eliminando Servicio Principal...");
                                            deleteService(id, solicitudId);
                                        },
                                        failure: function(result)
                                        {
                                            Ext.MessageBox.hide();
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                            }
                            else
                            {
                                //Verificar si es solución o no
                                if(!Ext.isEmpty(esSolucion) && esSolucion === 'S')
                                {
                                    $.ajax({
                                        type   : "POST",
                                        url    : urlEliminaServicioSolucion,
                                        timeout: 900000,
                                        data   : 
                                        {
                                          'idServicio'     : id,
                                          'idSolicitud'    : solicitudId,
                                          'idPunto'        : intIdPunto
                                        },
                                        beforeSend: function() 
                                        {            
                                            Ext.MessageBox.show({
                                                   msg: 'Eliminado Servicio de la Solución',
                                                   progressText: 'Eliminando...',
                                                   width:300,
                                                   wait:true,
                                                   waitConfig: {interval:200}
                                                });                     
                                        },
                                        success: function(data)
                                        {                                     
                                            if(data.status === 'OK')
                                            {
                                                var html = '';

                                                if((data.arrayServiciosEliminados)&&(data.arrayServiciosEliminados.length > 0))
                                                {
                                                    html += '<br><br>Los siguientes Servicios fueron eliminados por la acción realizada:';
                                                    html += '<br><ul>';
                                                    $.each(data.arrayServiciosEliminados, function(i, item)
                                                    {
                                                        html += '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp'+item+'</li>';
                                                    });
                                                    html += '</ul>';
                                                }
                                                if((data.arrayServiciosLigados)&&(data.arrayServiciosLigados.length > 0))
                                                {
                                                    html += '<br><br>Por la acción realizada Los siguientes servicios fueron desenlazados pero no eliminados:';
                                                    html += '<br><ul>';
                                                    $.each(data.arrayServiciosLigados, function(i, item)
                                                    {
                                                        html += '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp'+item+'</li>';
                                                    });
                                                    html += '</ul>';
                                                }

                                                Ext.Msg.alert('Mensaje', "Servicio Eliminado correctamente"+html, function(btn) {
                                                    if (btn == 'ok') 
                                                    {
                                                        store.load();
                                                    }
                                                }); 
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error', data.mensaje);
                                            }
                                        }
                                    });
                                }
                                else
                                {
                                    Ext.MessageBox.wait("Eliminando Servicio...");
                                    deleteService(id, solicitudId);
                                }
                            }
                        }
                    });
                
            }
            else 
            {
                Ext.Msg.alert('Atención', 'Se encuentra en proceso la Aprobación masiva de NC, espere unos minutos por favor y vuelva a intentar.');
            }
           
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
    
   
}

function deleteService(id, solicitud)
{
    Ext.Ajax.request
        ({
            url: url_servicio_delete_ajax,
            params:
                {
                    idservicio: id,
                    id_solicitud: solicitud
                },
            method: 'get',
            success: function(response)
            {
                Ext.MessageBox.hide();

                var text = response.responseText;
                Ext.Msg.alert('Atención', text);
                store.load();
            },
            failure: function(result)
            {
                Ext.MessageBox.hide();

                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
            }
        });
}

/*
 * @param {int} id
 * @param {string} solicitud
 * @param {string} observacion
 * @param {int} motivoId
 * @returns Action
 * 
 * @author Josselhin Moreira <kjmoreira@telconet.ec>
 * @version 1.0 2-04-2018 
 * Se agrega funcionalidad para generar factibilidad usando última milla de servicio existente
 * 
 */
function deleteServices(id, solicitud,observacion,motivoId)
{
    $.ajax
        ({
            Type: 'POST',
            url: url_servicio_delete_ajax,
            data:
                {
                    idservicio: id,
                    id_solicitud: solicitud,
                    observacion: observacion,
                    id_motivo: motivoId
                },
            success: function(data)
            {
                cierraVentanaMotivosEliminarServicio();
                Ext.Msg.alert('Mensaje', data);
                store.load();
            },
            failure: function(result)
            {
                cierraVentanaMotivosEliminarServicio();
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                store.load();
            }
        });
}

/*
 * Documentación para el método 'SolicitarFactibilidad'.
 *
 * Método utilizado para invocar el routing que solicita la factibilidad del servicio.
 *
 * @param  id_servicio integer PK del servicio.
 * @param  idProducto  integer PK del producto.
 *
 * @return Action      Alert   Mensaje de confirmación de éxito o fracaso del método.
 *
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.4 20-02-2022 Se agrega nuevo parámetro tipoOrden para utilizar en nuevas validaciones de proyecto TRASLADO MD DIFERENTE TECNOLOGÍA
 * 
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.3 13-03-2019 Se elimina parámetro 'migrarFactibilidad' ya que no esta siendo utilizado.
 *
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.2 14-07-2016 Se agrega funcionalidad para generar factibilidad usando última milla de servicio existente
 *
 * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
 * @version 1.1 07-07-2016
 * @since   1.0
 * Se elimina el parámetro "descripcion" debido a que no se estaba utilizando dentro del lógica de la función.
 */
function SolicitarFactibilidad(id_servicio,idProducto, descripcionPresenta, continuaFlujo ,migrarFactibilidad,nombreTecnico, tipoOrden)
{
    if(migrarFactibilidad === 'SI' && (nombreTecnico === 'L3MPLS' || nombreTecnico === 'INTERNET' || nombreTecnico === 'OTROS'))
    {
        Ext.Msg.confirm('Información', 'Desea migrar Factibilidad desde otro Cliente?', function(btn) {
            if (btn == 'yes')
            {
                migrarFactibilidadExterna(id_servicio);
            }
            else if(btn == 'no')
            {
                generarFactibilidad(id_servicio,idProducto,continuaFlujo,tipoOrden);
            }            
        });
    }
    else if(descripcionPresenta == 'CANAL TELEFONIA')
    {
        usarMismaUM(id_servicio);
    }
    else
    {
        generarFactibilidad(id_servicio,idProducto,continuaFlujo,tipoOrden);
    }
}


function asociarMascarillaACamara(id_servicio)
{
    asignarServicioACamara(id_servicio);
}


function usarMismaUM(id_servicio)
{
    var ventana = Ext.getCmp('windowServiciosUM');
    if (ventana != null) {
        ventana.close();
        ventana.destroy();
    }

    //Define un modelo para el store storeServiciosUM
    Ext.define('modelListaServiciosUM', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdServicio', type: 'int'},
            {name: 'strLoginAux', type: 'string'},
            {name: 'strDescProducto', type: 'string'},
            {name: 'strDescFactura', type: 'string'},
            {name: 'strUltimaMilla', type: 'string'},
            {name: 'strEstado', type: 'string'}
        ]
    });

    //Store que realiza la petición ajax para el grid: gridListaServiciosUM
    var storeServiciosUM = "";
    storeServiciosUM = Ext.create('Ext.data.Store', {
        pageSize: this.intPageSize,
        model: 'modelListaServiciosUM',
        autoLoad: true,
        proxy: {
            timeout: 60000,
            type: 'ajax',
            url: "../../getServiciosUM",
            timeout: this.timeout,
            reader: {
                type: 'json',
                root: 'jsonServiciosUM',
                totalProperty: 'total'
            },
            extraParams: {
                intIdServicio: id_servicio
            },
            simpleSortMode: true
        }
    });

    var chkBoxModelServicios = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function (selectionModel, selected, options) {
                Ext.each(selected, function (rec) {});
                gridListaServiciosUM.down('#btnFactibilidadServicios').setDisabled(selected.length == 0);
            }
        }
    });

    var toolbarServicios = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'bottom',
        align: '->',
        items:
            [{xtype: 'tbfill'},
                {
                    xtype: 'button',
                    cls: 'scm-button',
                    id: "btnFactibilidadServicios",
                    iconCls: "icon_anadir",
                    text: 'Generar Factibilidad',
                    disabled: true,
                    scope: this,
                    handler: function () {

                        var intServicioIdSeleccionado;
                        //Valida que haya seleccionado servicios por punto, caso contrario muestra un mensaje de alerta
                        if (1 === chkBoxModelServicios.getSelection().length)
                        {
                            //Itera los chkBox y concatena los ID Servicios en un solo string strIdServicios
                            for (var intForIndex = 0; intForIndex < chkBoxModelServicios.getSelection().length; intForIndex++) {
                                intServicioIdSeleccionado = chkBoxModelServicios.getSelection()[intForIndex].data['intIdServicio'];
                            }

                            connFact.request({
                                url: "../../generarFactibilidadUM",
                                method: 'post',
                                timeout: 400000,
                                params: {idServicioOrigen: id_servicio, idServicioUm: intServicioIdSeleccionado},
                                success: function (response) {
                                    var text = response.responseText;
                                    Ext.Msg.alert('Alerta', text);
                                    winServiciosUM.close();
                                    winServiciosUM.destroy();
                                    store.load();
                                },
                                failure: function (response) {
                                    var text = response.responseText;
                                    Ext.Msg.alert('Alerta', text);
                                    winServiciosUM.close();
                                    winServiciosUM.destroy();
                                    store.load();
                                }
                            });

                        } else {
                            Ext.Msg.alert('Alerta', 'Debe seleccionar sólo una orden de servicio.');
                        }
                    }
                }
            ]
    });

    //Crea el grid que muestra la información obtenida desde el controlador  del listado de servicios.
    var gridListaServiciosUM = Ext.create('Ext.grid.Panel', {
        store: storeServiciosUM,
        id: 'gridListaServiciosUM',
        selModel: chkBoxModelServicios,
        dockedItems: [toolbarServicios],
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'intIdServicioH',
                header: 'IdServicio',
                dataIndex: 'intIdServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'strLoginAuxH',
                header: 'Login Aux',
                dataIndex: 'strLoginAux',
                width: 210
            },
            {
                id: 'strProductoH',
                header: 'Producto',
                dataIndex: 'strDescProducto',
                width: 210
            },
            {
                id: 'strDescripcionH',
                header: 'Descripcion',
                dataIndex: 'strDescFactura',
                width: 210
            },
            {
                id: 'strDescripcionUm',
                header: 'Ultima MIlla',
                dataIndex: 'strUltimaMilla',
                width: 210
            },
            {
                id: 'strEstadoH',
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 230
            }
        ],
        height: 400,
        width: 980,
        listeners: {

        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeServiciosUM,
            displayInfo: true
        })
    });

    var winServiciosUM = Ext.widget('window', {
        id: 'windowServiciosUM',
        title: 'Seleccionar Ultima Milla: ',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: true,
        items: [gridListaServiciosUM]
    });
    winServiciosUM.show();


}

/* 
 * @version 1.0 No documentada
 * 
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.1 06-02-2019 Se válida que no se duplique una solicitud de factibilidad, que se encuentre en estado: Factible o Prefactibilidad
 * @since 1.0
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.2 20-03-2019 Se válida que parámetro 'migrarFactibilidad' no se esta utilizando,
 * y se procede a eliminarlo.
 * 
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.3 10-01-2022 Se agrega parámetro tipoOrden que se utilizará para factibilidades en traslados con diferente teconlogías en MD
 */

function generarFactibilidad(id_servicio,idProducto,continuaFlujo,tipoOrden)
{
    if(continuaFlujo === 'SI')
    {
        connFact.request({
            url: "../../solicitarFactibilidadGeneral",
            method: 'post',
            timeout: 400000,
            params: {id: id_servicio, idProducto: idProducto},
            success: function(response) {
                var text = response.responseText;
                if("factibilidadUM"===text)
                {//Valida si el producto es 261 (INTERNET WIFI), si es asi, lo envia a solicitar factibilidad.
                  if (idProducto == 261) {
                  connFact.request({
                               url: "../../solicitarFactibilidadAjax",
                               method: 'post',
                               timeout: 400000,
                               params: {id: id_servicio, idProducto: idProducto},
                               success: function(response) {
                                   var text = response.responseText;
                                   Ext.Msg.alert('Mensaje', text, function(btn) {
                                       if (btn == 'ok') {
                                           store.load();
                                       }
                                   });
                               },
                               failure: function(result) {
                                   Ext.Msg.alert('Alerta', result.responseText);
                                   store.load();
                               }
                           }); 
                  }else
                  {
                    Ext.Msg.confirm('Alerta', 'Desea Utilizar la misma Ultima Milla?', function(btn) {
                        if (btn == 'yes') 
                        {
                            
                            usarMismaUM(id_servicio);

                        }
                        else
                        {
                            connFact.request({
                               url: "../../solicitarFactibilidadAjax",
                               method: 'post',
                               timeout: 400000,
                               params: {id: id_servicio, idProducto: idProducto},
                               success: function(response) {
                                   var text = response.responseText;

                                   Ext.Msg.alert('Mensaje', text, function(btn) {
                                       if (btn == 'ok') {
                                           store.load();
                                       }
                                   });
                               },
                               failure: function(result) {
                                   Ext.Msg.alert('Alerta', result.responseText);
                                   store.load();
                               }
                           });                         

                        }
                    });
                  }
                    
                }
                else if("factibilidadREPETIDA"===text)
                {
                     Ext.Msg.alert('Alerta', 'Ya existe una solicitud de factibilidad ingresada, por favor verificar.');
                }
                else
                {
                    connFact.request({
                        url: "../../solicitarFactibilidadAjax",
                        method: 'post',
                        timeout: 400000,
                        params: {id: id_servicio, idProducto: idProducto},
                        success: function(response) {
                            var text = response.responseText;
                            if ((prefijoEmpresa === 'MD' || prefijoEmpresa === 'EN' ) && tipoOrden === 'T') {
                                connValidaTrasladoMd.request({
                                    url: urlValidaTrasladoMd,
                                    method: 'post',
                                    timeout: 400000,
                                    params: {idServicio: id_servicio},
                                    success: function(responseValida) {
                                        var datosValidaTrasladoMd = Ext.JSON.decode(responseValida.responseText);
                                        if (datosValidaTrasladoMd.strStatus == "OK")
                                        {
                                            if (datosValidaTrasladoMd.strDiferenteTecnologia == "DIFERENTE TECNOLOGIA FACTIBILIDAD")
                                            {
                                                Ext.Msg.confirm({
                                                    title:'Alerta',
                                                    msg: 'Está realizando un traslado en diferente tecnología, desea registrar la entrega de algún equipo?',
                                                    buttons: Ext.Msg.YESNO,
                                                    icon: Ext.MessageBox.QUESTION,
                                                    buttonText: {
                                                        yes: 'SI', no: 'NO'
                                                    },
                                                    fn: function(btn){
                                                        if(btn=='yes'){
                                                            showRegistroEquiposEntregados(id_servicio);
                                                        }
                                                        else
                                                        {
                                                            store.load();
                                                        }
                                                    }
                                                });
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Mensaje', text, function(btn) {
                                                    if (btn == 'ok') {
                                                        store.load();
                                                    }
                                                });
                                            }
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Mensaje', datosValidaTrasladoMd.strMensaje, function(btn) {
                                                if (btn == 'ok') {
                                                    store.load();
                                                }
                                            });
                                        }
                                    },
                                    failure: function(result) {
                                        Ext.Msg.alert('Alerta', result.responseText);
                                        store.load();
                                    }
                                });
                            } else {
                                Ext.Msg.alert('Mensaje', text, function(btn) {
                                    if (btn == 'ok') {
                                        store.load();
                                    }
                                });
                            }
                        },
                        failure: function(result) {
                            Ext.Msg.alert('Alerta', result.responseText);
                            store.load();
                        }
                    });                              
                }

            },
            failure: function(result) {
                Ext.Msg.alert('Alerta', result.responseText);
                store.load();
            }
        });
    }
    else
    {
        Ext.Msg.alert('Alerta', 'Favor ingrese el anexo Técnico y/o Comercial requerido para el servicio');
    }
}

function migrarFactibilidadExterna(idServicio)
{
    var razonSocial   = '';
    
    var storeClientes = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: urlGetInfoClientesParaMigracion,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                accion        : 'clientes',
                personaRol    : '',
                oficina       : ''
            }
        },
        fields:
            [
                {name: 'idPersonaRol',  mapping: 'idPersonaRol'},
                {name: 'razonSocial',   mapping: 'razonSocial'}
            ]
    });
    
    var storePuntos = new Ext.data.Store({
        pageSize: 1000,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: urlGetInfoClientesParaMigracion,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idPunto',         mapping: 'idPunto'},
                {name: 'login',           mapping: 'login'},
                {name: 'nombrePunto',     mapping: 'nombrePunto'},
                {name: 'nombreOficina',   mapping: 'nombreOficina'}
            ]
    });
    
    var storeOficinas = new Ext.data.Store({
        total: 'total',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: url_getOficinas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'id_oficina_grupo', mapping: 'id_oficina_grupo'},
                {name: 'nombre_oficina',   mapping: 'nombre_oficina'}
            ]
    });
    
    var gridPuntos = Ext.create('Ext.grid.Panel', {
        width: 550,
        height: 230,
        store: storePuntos,
        loadMask: true,
        frame: false,
        columns: [
            {
                header: '<i align="center" class="fa fa-hashtag" aria-hidden="true"></i>',
				xtype : 'rownumberer',
				width : 25
			},
            {
                id: 'idPunto',
                header: 'idPunto',
                dataIndex: 'idPunto',
                hidden: true,
                hideable: false
            },            
            {
                id: 'login',
                header: '<b>Login</b>',
                dataIndex: 'login',
                width: 150,
                sortable: true
            },
            {
                id: 'nombrePunto',
                header: '<b>Nombre Punto</b>',
                dataIndex: 'nombrePunto',
                width: 180,
                sortable: true
            },
            {
                id: 'nombreOficina',
                header: '<b>Nombre Oficina</b>',
                dataIndex: 'nombreOficina',
                width: 130,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                sortable: false,
                header: '<i align="center" class="fa fa-cogs" aria-hidden="true"></i>',
                width: 35,
                items: [
                    {
                        getClass: function(v, meta, rec)
                        {
                            return "button-grid-show";
                        },
                        tooltip: 'Mostrar Servicios',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var record = grid.getStore().getAt(rowIndex);

                            mostrarServiciosPuntosMigrar(record,razonSocial,idServicio);
                        }
                    }
                ]
            }
        ],
        listeners: 
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> \n\
                          <i class='fa fa-arrow-right' aria-hidden='true'></i> <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
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
                    autoHide: false,
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
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

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
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;
                    
                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        }
    });
    
    //Filtros para los puntos---------------------------------------------------------------------
    
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders        
        border:false,        
        buttonAlign: 'center',
        layout: {
            type:'table',
            columns: 2
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : false,
        collapsed: false,
        width: 550,
        title: 'Criterios de busqueda',
        buttons: 
		[
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function()
                { 
                    var idPersonaRol    = Ext.getCmp("cmbClientesMigracion").getValue();
                    var txtLoginMigrar  = Ext.getCmp("txtLoginMigrar").getValue();
                    var idOficinaMigrar = Ext.getCmp("cmbOficinaMigrar").getValue();
                    
                    storePuntos.proxy.extraParams = {
                                                        accion    :'puntos',
                                                        personaRol: idPersonaRol,
                                                        oficina   : idOficinaMigrar,
                                                        login     : txtLoginMigrar,
                                                        razonSocial: razonSocial,
                                                        idServicio : idServicio
                                                    };
                    storePuntos.load();
                }
            }
		],                
		items: 
		[
			{html:"&nbsp;",border:false,width:50},		
			{
				xtype: 'textfield',
				id: 'txtLoginMigrar',
				name: 'txtLoginMigrar',
				fieldLabel: '<b>Login</b>',
				value: '',
				width: 350
			},
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'combobox',
                fieldLabel: '<b>Oficina</b>',
                name: 'cmbOficinaMigrar',
                id: 'cmbOficinaMigrar',
                store:storeOficinas,
                displayField: 'nombre_oficina',
                valueField: 'id_oficina_grupo',
                width:350,
                readOnly: false
			}
		]
    }); 
    
    //----------------------------------------------------------------------------
    
    var formPanelClientesExternos = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        width: 600,
        height: 500,
        bodyStyle: "background: white; padding: 5px; border: 0px none;",
        frame: true,
        items:
            [
                {
                    xtype: 'fieldset',
                    title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Clientes Externos de donde se relizará la Migración</b>',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 1,
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            fieldLabel: '<i class="fa fa-user-circle" aria-hidden="true"></i>&nbsp;<b>Clientes</b>',
                            name: 'cmbClientesMigracion',
                            id: 'cmbClientesMigracion',
                            store:storeClientes,
                            displayField: 'razonSocial',
                            valueField: 'idPersonaRol',
                            width:400,
                            readOnly: false,
                            listeners: 
                                {
                                    select: function(combo) 
                                    {
                                        razonSocial = combo.getRawValue();
                                        
                                        storePuntos.proxy.extraParams = {
                                                                          accion    :'puntos',
                                                                          personaRol: combo.getValue(),
                                                                          razonSocial: razonSocial,
                                                                          idServicio : idServicio
                                                                        };
                                        storePuntos.load();
                                    }
                                }
                        }
                    ]
                },
                //Puntos
                {
                    xtype: 'fieldset',
                    title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Puntos Activos del Cliente</b>',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 1,
                        pack: 'center'
                    },
                    items: 
                    [                  
                        filterPanel,
                        gridPuntos
                    ]
                }
            ],
        buttons: [
            {
                text: '<i class="fa fa-window-close" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function()
                {
                    winClientesExternos.close();
                    winClientesExternos.destroy();
                }
            }
        ]});

    winClientesExternos = Ext.widget('window', {
        id: 'winClientesExternos',
        title: 'Migración de datos de Factibilidad',
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: true,
        width: 'auto',
        height : 600,
        items: [formPanelClientesExternos]
    });

    winClientesExternos.show();
}


function asignarServicioACamara(id_servicio)
{
    var ventana = Ext.getCmp('windowCamarasSafecity');
    if (ventana != null) {
        ventana.close();
        ventana.destroy();
    }

    //Define un modelo para el store storeListaCamaras
    Ext.define('modelListaCamaras', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdServicio', type: 'int'},
            {name: 'strLoginAux', type: 'string'},
            {name: 'strDescProducto', type: 'string'},
            {name: 'strEstado', type: 'string'}
        ]
    });
   

    //Store que realiza la petición ajax para el grid: gridListaServiciosUM
    var storeListaCamaras = "";
    storeListaCamaras = Ext.create('Ext.data.Store', {
        pageSize: this.intPageSize,
        model: 'modelListaCamaras',
        autoLoad: true,
        proxy: {
            timeout: 60000,
            type: 'ajax',
            url: urlGetCamarasSafecity,
            reader: {
                type: 'json',
                root: 'jsonServiciosCAMARAS',
                totalProperty: 'total'
            },
            extraParams: {
                intIdServicio: id_servicio
            },
            simpleSortMode: true
        }
    });

    var chkBoxModelServicios = new Ext.selection.CheckboxModel({
            mode: 'SINGLE',
            checkOnly: 'true',
        listeners: {
            selectionchange: function (selectionModel, selected, options) {
                Ext.each(selected, function (rec) {});
                gridListaCamaras.down('#btnAsociarMascarillasCamaras').setDisabled(selected.length == 0);
            }
        }
    });

    var toolbarServicios = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'bottom',
        align: '->',
        items:
            [{xtype: 'tbfill'},
                {
                    xtype: 'button',
                    cls: 'scm-button',
                    id: "btnAsociarMascarillasCamaras",
                    iconCls: "icon_anadir",
                    text: 'Asociar',
                    disabled: true,
                    scope: this,
                    handler: function () {
                        var intServicioIdSeleccionado;
                        //Valida que haya seleccionado servicios por punto, caso contrario muestra un mensaje de alerta
                        if (1 === chkBoxModelServicios.getSelection().length)
                        {
                            //Itera los chkBox y concatena los ID Servicios en un solo string strIdServicios
                            for (var intForIndex = 0; intForIndex < chkBoxModelServicios.getSelection().length; intForIndex++) {
                                intServicioIdSeleccionado = chkBoxModelServicios.getSelection()[intForIndex].data['intIdServicio'];
                            }

                            Ext.Msg.confirm('Información', 'Está seguro que desea asociar la mascarilla a este servicio?', function(btn) {
                                if (btn == 'yes'){
                                    connFact.request({
                                        url: urlAsociarMascarillaConCamara,
                                        method: 'post',
                                        timeout: 400000,
                                        params: {idServicioOrigen: id_servicio, idServicioCamara: intServicioIdSeleccionado},
                                        success: function (response) {

                                            var obj           = Ext.JSON.decode(response.responseText);
                                            var mensajeAlerta = "";

                                            if(obj.status == "OK")
                                            {
                                                mensajeAlerta = Ext.Msg.alert('Mensaje',obj.mensaje);
                                            }
                                            else
                                            {
                                                mensajeAlerta = Ext.Msg.alert('Error',obj.mensaje);
                                            }

                                            Ext.defer(function() {
                                                mensajeAlerta.toFront();
                                            }, 50);

                                            winServiciosCamaras.close();
                                            winServiciosCamaras.destroy();
                                            store.load();                                   

                                        },
                                        failure: function (response) {

                                            var obj           = Ext.JSON.decode(response.responseText);
                                            var mensajeAlerta = Ext.Msg.alert('Error',obj.mensaje);

                                            Ext.defer(function() {
                                                mensajeAlerta.toFront();
                                            }, 50);

                                            winServiciosCamaras.close();
                                            winServiciosCamaras.destroy();
                                            store.load();
                                        }
                                    });
                                }
                            });

                        } else {
                            Ext.Msg.alert('Alerta', 'Debe seleccionar sólo una camara');
                        }
                    }
                }
            ]
    });

    //Crea el grid que muestra la información obtenida desde el controlador  del listado de servicios.
    var gridListaCamaras = Ext.create('Ext.grid.Panel', {
        store: storeListaCamaras,
        id: 'gridListaCamaras',
        selModel: chkBoxModelServicios,
        dockedItems: [toolbarServicios],
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'intIdServicioH',
                header: 'IdServicio',
                dataIndex: 'intIdServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'strLoginAuxH',
                header: 'Login Aux',
                dataIndex: 'strLoginAux',
                width: 210
            },
            {
                id: 'strDescripcionH',
                header: 'Descripcion',
                dataIndex: 'strDescProducto',
                width: 210
            },
            {
                id: 'strEstadoH',
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 230
            }
        ],
        height: 300,
        width: 680,
        listeners: {

        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeListaCamaras,
            displayInfo: true
        })
    });

    var winServiciosCamaras = Ext.widget('window', {
        id: 'windowCamarasSafecity',
        title: 'Asignar el servicio Mascarilla a la Camara',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: true,
        items: [gridListaCamaras]
    });
    winServiciosCamaras.show();
}



function mostrarServiciosPuntosMigrar(rec,razonSocial,idServicio)
{
    var storeServiciosMigrar = new Ext.data.Store({
        pageSize: 1000,
        total: 'total',
        autoLoad:true,
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: urlGetInfoClientesParaMigracion,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                accion      : 'servicios',
                idPunto     : rec.get('idPunto'),
                razonSocial : razonSocial,
                idServicio  : idServicio
            }
        },
        fields:
            [
                {name: 'idServicio',      mapping: 'idServicio'},
                {name: 'nombreProducto',  mapping: 'nombreProducto'},
                {name: 'loginAux',        mapping: 'loginAux'},
                {name: 'capacidad1',      mapping: 'capacidad1'},
                {name: 'capacidad2',      mapping: 'capacidad2'}
            ]
    });
    
    var gridServiciosMigrar = Ext.create('Ext.grid.Panel', {
        width: 450,
        height: 150,
        id:'gridServiciosMigrar',
        store: storeServiciosMigrar,
        loadMask: true,
        frame: false,
        columns: [            
            {
                id: 'idServicio',
                header: 'idServicio',
                dataIndex: 'idServicio',
                hidden: true,
                hideable: false
            },            
            {
                id: 'nombreProducto',
                header: '<b>Producto</b>',
                dataIndex: 'nombreProducto',
                width: 80,
                sortable: true
            },
            {
                id: 'loginAux',
                header: '<b>Login Aux</b>',
                dataIndex: 'loginAux',
                width: 180,
                sortable: true
            },
            {
                id: 'capacidad1',
                header: '<b>Capacidad 1</b>',
                dataIndex: 'capacidad1',
                width: 75,
                sortable: true
            },
            {
                id: 'capacidad2',
                header: '<b>Capacidad 2</b>',
                dataIndex: 'capacidad2',
                width: 75,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                sortable: false,
                header: '<i align="center" class="fa fa-cogs" aria-hidden="true"></i>',
                width: 35,
                items: [
                    {
                        getClass: function(v, meta, rec)
                        {
                            return "button-grid-seleccionar";
                        },
                        tooltip: 'Clonar Datos del Servicio',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            Ext.Msg.confirm('Alerta', 'Desea clonar la información de este Servicio?', function(btn) {
                                if (btn == 'yes') 
                                {
                                    var record = grid.getStore().getAt(rowIndex);
                            
                                    Ext.get('winServiciosMigrar').mask("Clonando información del Servicio...");

                                    Ext.Ajax.request
                                        ({
                                            url: urlClonarInformacionServicio,
                                            method: 'post',
                                            timeout: 600000,
                                            params:
                                            {
                                                idServicio:         idServicio,
                                                idServicioAnterior: record.get('idServicio')
                                            },
                                            success: function(response)
                                            {
                                                Ext.get('winServiciosMigrar').unmask();

                                                var objJson = Ext.JSON.decode(response.responseText);
                                                
                                                Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                                                    if (btn == 'ok') 
                                                    {
                                                        if(objJson.status === 'OK')
                                                        {
                                                            winServiciosMigrar.close();
                                                            winServiciosMigrar.destroy();
                                                            winClientesExternos.close();
                                                            winClientesExternos.destroy();
                                                            store.load();
                                                        }
                                                    }
                                                });
                                            },
                                            failure: function(result)
                                            {
                                                Ext.MessageBox.hide();
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                }
                            });
                        }
                    }
                ]
            }
        ]
    });
    
    var formPanelServiciosMigrar = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        width: 500,
        height: 250,
        bodyStyle: "background: white; padding: 5px; border: 0px none;",
        frame: true,
        items:
            [
                {
                    xtype: 'fieldset',
                    title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;\n\
                           <b>Servicios del punto <label style="color:green;">'+rec.get('login')+'</label> a ser migrados</b>',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 1,
                        pack: 'center'
                    },
                    items: [
                        gridServiciosMigrar
                    ]
                }
            ],
        buttons: [
            {
                text: '<i class="fa fa-window-close" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function()
                {
                    winServiciosMigrar.close();
                    winServiciosMigrar.destroy();
                }
            }
        ]});

    var winServiciosMigrar = Ext.widget('window', {
        id: 'winServiciosMigrar',
        title: 'Migración de datos de Factibilidad',
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: true,
        width: 'auto',
        height : 300,
        items: [formPanelServiciosMigrar]
    });

    winServiciosMigrar.show();
}

function verLogsServicio(data)
{
    store.load();
    var storeHistorial = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: '../../../../tecnico/clientes/getHistorialServicio',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data
            }
        },
        fields:
            [
                {name: 'usrCreacion', mapping: 'usrCreacion'},
                {name: 'feCreacion', mapping: 'feCreacion'},
                {name: 'ipCreacion', mapping: 'ipCreacion'},
                {name: 'estado', mapping: 'estado'},
                {name: 'nombreMotivo', mapping: 'nombreMotivo'},
                {name: 'observacion', mapping: 'observacion'},
                {name: 'accion', mapping: 'accion'}
            ]
    });

    Ext.define('HistorialServicio', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'usrCreacion', mapping: 'usrCreacion'},
            {name: 'feCreacion', mapping: 'feCreacion'},
            {name: 'ipCreacion', mapping: 'ipCreacion'},
            {name: 'estado', mapping: 'estado'},
            {name: 'nombreMotivo', mapping: 'nombreMotivo'},
            {name: 'observacion', mapping: 'observacion'},
            {name: 'accion', mapping: 'accion'}
        ]
    });

    //Grid Historial del Servicio
    gridHistorialServicio = Ext.create('Ext.grid.Panel',
        {
            id: 'gridHistorialServicio',
            store: storeHistorial,
            columnLines: true,
            listeners:
                {
                    viewready: function(grid)
                    {
                        var view = grid.view;

                        grid.mon(view,
                            {
                                uievent: function(type, view, cell, recordIndex, cellIndex, e)
                                {
                                    grid.cellIndex = cellIndex;
                                    grid.recordIndex = recordIndex;
                                }
                            });

                        grid.tip = Ext.create('Ext.tip.ToolTip',
                            {
                                target: view.el,
                                delegate: '.x-grid-cell',
                                trackMouse: true,
                                autoHide: false,
                                renderTo: Ext.getBody(),
                                listeners:
                                    {
                                        beforeshow: function(tip)
                                        {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                            {
                                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                                if (header.dataIndex != null)
                                                {
                                                    var trigger = tip.triggerElement,
                                                        parent = tip.triggerElement.parentElement,
                                                        columnTitle = view.getHeaderByCell(trigger).text,
                                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                                    if (view.getRecord(parent).get(columnDataIndex) != null)
                                                    {
                                                        var columnText = view.getRecord(parent).get(columnDataIndex).toString();

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
                                                else
                                                {
                                                    return false;
                                                }
                                            }
                                        }
                                    }
                            });

                        grid.tip.on('show', function()
                        {
                            var timeout;

                            grid.tip.getEl().on('mouseout', function()
                            {
                                timeout = window.setTimeout(function() {
                                    grid.tip.hide();
                                }, 500);
                            });

                            grid.tip.getEl().on('mouseover', function() {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseover', function() {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseout', function()
                            {
                                timeout = window.setTimeout(function() {
                                    grid.tip.hide();
                                }, 500);
                            });
                        });
                    }
                },
            columns:
                [
                    {
                        header: 'Usuario Creacion',
                        dataIndex: 'usrCreacion',
                        width: 100,
                        sortable: true
                    }, {
                        header: 'Fecha Creacion',
                        dataIndex: 'feCreacion',
                        width: 120
                    },
                    {
                        header: 'Ip Creacion',
                        dataIndex: 'ipCreacion',
                        width: 100
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 100
                    },
                    {
                        header: 'Motivo',
                        dataIndex: 'nombreMotivo',
                        width: 130
                    },
                    {
                        header: 'Accion',
                        dataIndex: 'accion',
                        width: 100
                    },
                    {
                        header: 'Observacion',
                        dataIndex: 'observacion',
                        width: 438
                    }
                ],
            viewConfig:
                {
                    stripeRows: true,
                    enableTextSelection: true
                },
            frame: true,
            height: 300
        });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 1100
                },
                items: [
                    gridHistorialServicio

                ]
            }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Historial del Servicio',
        modal: true,
        width: 1150,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

//funcion utilizada para mostrar el popup de anulacion de orden de trabajo
/* 
 * @version 1.0 No documentada
 * 
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.1 18-12-2019 | Se agrega validación para que aparezca mensaje de ejecución de Nc masiva.
 * 
*/
function showAnularOrden_Coordinar(id_factibilidad, id_punto, linkVerCliente, tercializadora, cliente, login2, ciudad, direccion,
    nombreSector, esRecontratacion, producto, tipo_orden, telefonos, observacion, esSolucion, idServicio)
{
    winAnularOrden_Coordinar = "";
    formPanelAnularOrden_Coordinar = "";

    if (!winAnularOrden_Coordinar)
    {
        if (tercializadora) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: tercializadora,
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }

        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });
        
        panelInfoAdicionalSolAnular = Ext.create('Ext.container.Container',
        {
            id:'panelInfoAdicionalSolAnular',
            xtype: 'panel',
            border: false,
            style: "text-align:justify; font-weight:bold;",
            layout: {type: 'vbox', align: 'stretch'},
            listeners:
            {
                afterrender: function(cmp)
                {
                    if(prefijoEmpresa === "MD" || prefijoEmpresa === 'EN')
                    {
                        Ext.MessageBox.wait("Verificando solicitudes simultáneas...");
                        storeSolsSimultaneas = new Ext.data.Store({
                            total: 'intTotal',
                            pageSize: 10000,
                            proxy: {
                                type: 'ajax',
                                url: strUrlGetInfoSolsGestionSimultanea,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'intTotal',
                                    root: 'arrayRegistrosInfoGestionSimultanea'
                                },
                                extraParams: {
                                    intIdSolicitud: id_factibilidad,
                                    strOpcionGestionSimultanea: 'ANULAR'
                                }
                            },
                            fields:
                                [
                                    {name: 'idSolGestionada',           mapping: 'ID_SOL_GESTIONADA'},
                                    {name: 'idServicioSimultaneo',      mapping: 'ID_SERVICIO_SIMULTANEO'},
                                    {name: 'descripServicioSimultaneo', mapping: 'DESCRIP_SERVICIO_SIMULTANEO'},
                                    {name: 'estadoServicioSimultaneo',  mapping: 'ESTADO_SERVICIO_SIMULTANEO'},
                                    {name: 'idSolSimultanea',           mapping: 'ID_SOL_SIMULTANEA'},
                                    {name: 'descripTipoSolSimultanea',  mapping: 'DESCRIP_TIPO_SOL_SIMULTANEA'},
                                    {name: 'estadoSolSimultanea',       mapping: 'ESTADO_SOL_SIMULTANEA'}
                                ]
                        });

                        storeSolsSimultaneas.load({
                            callback: function(records, operation, success) {
                                var numRegistrosGestionSimultanea = storeSolsSimultaneas.getCount();
                                if(numRegistrosGestionSimultanea > 0)
                                {
                                    var strInfoNumSolicitudes = "";
                                    if(numRegistrosGestionSimultanea === 1)
                                    {
                                        strInfoNumSolicitudes = "otra solicitud";
                                    }
                                    else
                                    {
                                        strInfoNumSolicitudes = "otras "+numRegistrosGestionSimultanea+" solicitudes";
                                    }

                                    Ext.getCmp('panelInfoAdicionalSolAnular').add(
                                        {
                                            title: 'Gestión Simultánea',
                                            xtype: 'fieldset',
                                            defaultType: 'textfield',
                                            style: "text-align:justify; padding: 5px; font-weight:bold; margin-bottom: 15px;",
                                            layout: 'anchor',
                                            defaults:
                                                {
                                                    border: false,
                                                    frame: false
                                                },
                                            items: [
                                                {
                                                    xtype: 'panel',
                                                    border: false,
                                                    style: "text-align:justify; font-weight:bold; margin-bottom: 5px;",
                                                    defaults:
                                                    {
                                                        width: '565px',
                                                    },
                                                    layout: {type: 'hbox', align: 'stretch'},
                                                    items: [
                                                        {                                                                                              
                                                            xtype: 'textfield',
                                                            hidden: true,
                                                            id:'tieneGestionSimultanea',
                                                            value:'SI'
                                                        },
                                                        Ext.create('Ext.Component', {
                                                            style: "font-weight:bold; padding: 5px; text-align:justify;",
                                                            html: "<div>Esta solicitud #"+ id_factibilidad 
                                                                  + " anulará de manera simultánea "
                                                                  +strInfoNumSolicitudes+"."+"</div>",
                                                            layout: 'anchor'
                                                        }),
                                                        {
                                                            id: 'btnMasDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ver Detalle',
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('gridSolsSimultaneas').show();
                                                            }
                                                        },
                                                        {
                                                            id: 'btnMenosDetalleSolsSimultaneas',
                                                            xtype: 'button',
                                                            text: 'Ocultar Detalle',
                                                            hidden: true,
                                                            handler: function(){
                                                                Ext.getCmp('btnMenosDetalleSolsSimultaneas').hide();
                                                                Ext.getCmp('btnMasDetalleSolsSimultaneas').show();
                                                                Ext.getCmp('gridSolsSimultaneas').hide();
                                                            }
                                                        }
                                                    ]
                                                },
                                                Ext.create('Ext.grid.Panel', {
                                                    id: 'gridSolsSimultaneas',
                                                    store: storeSolsSimultaneas,
                                                    style: "padding: 5px; text-align:justify; margin-left: auto; margin-right: auto; "
                                                           +"margin-bottom: 10px",
                                                    columnLines: true,
                                                    hidden: true,
                                                    columns: 
                                                    [
                                                        {
                                                            id: 'descripServicioSimultaneo',
                                                            header: 'Plan/Producto',
                                                            dataIndex: 'descripServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoServicioSimultaneo',
                                                            header: 'Estado servicio',
                                                            dataIndex: 'estadoServicioSimultaneo',
                                                            width: 130,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'idSolSimultanea',
                                                            header: '# Solicitud',
                                                            dataIndex: 'idSolSimultanea',
                                                            width: 100,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'descripTipoSolSimultanea',
                                                            header: 'Tipo Solicitud',
                                                            dataIndex: 'descripTipoSolSimultanea',
                                                            width: 180,
                                                            sortable: true
                                                        },
                                                        {
                                                            id: 'estadoSolSimultanea',
                                                            header: 'Estado Solicitud',
                                                            dataIndex: 'estadoSolSimultanea',
                                                            width: 130,
                                                            sortable: true
                                                        }
                                                    ],
                                                    viewConfig: {
                                                        stripeRows: true
                                                    },
                                                    frame: true,
                                                    defaults:
                                                    {
                                                        width: '565px'
                                                    }
                                                })
                                            ]
                                        }
                                    );
                                }
                                else
                                {
                                    Ext.getCmp('panelInfoAdicionalSolAnular').add(
                                    {                                                                                              
                                        xtype: 'textfield',
                                        hidden: true,
                                        id:'tieneGestionSimultanea',
                                        value:'NO'
                                    });
                                }
                                Ext.getCmp('panelInfoAdicionalSolAnular').doLayout();
                                Ext.MessageBox.hide();
                            }
                        });
                        cmp.doLayout();
                    }
                    else
                    {
                        Ext.getCmp('panelInfoAdicionalSolAnular').add(
                            {                                                                                              
                                xtype: 'textfield',
                                hidden: true,
                                id:'tieneGestionSimultanea',
                                value:'NO'
                            },
                        );
                        Ext.getCmp('panelInfoAdicionalSolAnular').doLayout();
                    }
                }
            }
        });
        
        formPanelAnularOrden_Coordinar = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 5,
            bodyStyle: "background: white; padding:5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'panel',
                    frame: false,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            layout: 'anchor',
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: cliente,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: login2,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: ciudad,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: direccion,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: nombreSector,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Es Recontratacion',
                                    name: 'es_recontratacion',
                                    id: 'es_recontratacion',
                                    value: esRecontratacion,
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Punto',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 10px;",
                            defaults: {
                                width: '300px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Servicio',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: producto,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Orden',
                                    name: 'tipo_orden_servicio',
                                    id: 'tipo_orden_servicio',
                                    value: tipo_orden,
                                    allowBlank: false,
                                    readOnly: true
                                }, itemTercerizadora,
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Telefonos',
                                    name: 'telefonos_punto',
                                    id: 'telefonos_punto',
                                    value: telefonos,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observacion',
                                    name: 'observacion_punto',
                                    id: 'observacion_punto',
                                    value: observacion,
                                    allowBlank: false,
                                    readOnly: true
                                },
                            ]
                        }
                    ]
                },
                panelInfoAdicionalSolAnular,
                {
                    xtype: 'fieldset',
                    title: 'Datos de la anulacion',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 10px;",
                    defaults: {
                        width: '500px'
                    },
                    items: [
                        {
                            xtype: 'textarea',
                            fieldLabel: '* Observacion',
                            name: 'info_observacion',
                            id: 'info_observacion',
                            allowBlank: false,
                            labelStyle: "color:red;"
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmbMotivo',
                            fieldLabel: '* Motivo',
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField: 'nombre_motivo',
                            valueField: 'id_motivo',
                            selectOnTab: true,
                            store: storeMotivosAnulacion,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;"
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Anular',
                    handler: function() {
                        var txtObservacion = Ext.getCmp('info_observacion').value;
                        var cmbMotivo = Ext.getCmp('cmbMotivo').value;
                        var boolError = false;
                        var mensajeError = "";
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }
                        
                        connCoordinar.request({
                        url: urlGetProcesoMasivoNC,
                        method: 'post',
                        timeout: 400000,
                        async: false,
                            success: function(response)
                            {

                                    if (response.responseText === 'NO')
                                    {
                                        boolError = true;
                                        mensajeError += "Se encuentra en proceso la Aprobación masiva de NC, espere unos minutos por favor y vuelva a intentar.\n";
                                    }

                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        })
                        
                        
                        if (!boolError)
                        {
                            if(esSolucion === 'S')
                            {
                                $.ajax({
                                    type   : "POST",
                                    url    : urlAnularRechazarSol,
                                    timeout: 900000,
                                    data   : 
                                    {
                                      'idServicio'     : idServicio,
                                      'idSolicitud'    : id_factibilidad,
                                      'idMotivo'       : cmbMotivo, 
                                      'observacion'    : txtObservacion,
                                      'accion'         : 'Anular',
                                      'origen'         : 'coordinacion'
                                    },
                                    beforeSend: function() 
                                    {            
                                        Ext.MessageBox.show({
                                               msg: 'Anulando Servicio de la Solución',
                                               progressText: 'Anulando...',
                                               width:300,
                                               wait:true,
                                               waitConfig: {interval:200}
                                            });                     
                                    },
                                    success: function(data)
                                    {                                     
                                        if(data.status === 'OK')
                                        {
                                            var html = '';

                                            if(data.arrayServiciosEliminados.length > 0)
                                            {
                                                html += '<br><br>Los siguientes Servicios fueron anulados por acción realizada.';
                                                html += '<br><ul>';
                                                $.each(data.arrayServiciosEliminados, function(i, item)
                                                {
                                                    html += '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp'+item+'</li>';
                                                });
                                                html += '</ul>';
                                            }
                                            
                                            var text = "Servicio Anulado correctamente"+html;

                                            Ext.Msg.alert('Mensaje', text, function(btn) 
                                            {
                                                if (btn == 'ok') 
                                                {
                                                    cierraVentanaAnularOrden_Coordinar();
                                                    store.load();
                                                }
                                            });                
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Error', data.mensaje);
                                        }
                                    }
                                });
                            }
                            else
                            {
                                connCoordinar.request({
                                    url: url_anular,
                                    method: 'post',
                                    params: {id: id_factibilidad, id_motivo: cmbMotivo, observacion: txtObservacion},
                                    success: function(response) {
                                        var text = response.responseText;
                                        if (text == "Se anulo la solicitud")
                                        {
                                            var tieneGestionSimultanea = Ext.getCmp('tieneGestionSimultanea').value;
                                            cierraVentanaAnularOrden_Coordinar();
                                            if((prefijoEmpresa == "MD" || prefijoEmpresa === 'EN') && tieneGestionSimultanea === "SI")
                                            {
                                                var objInfoGestionSimultanea = {
                                                    strOpcionGestionSimultanea:         "ANULAR",
                                                    intIdSolGestionada:                 id_factibilidad,
                                                    intIdMotivo:                        cmbMotivo,
                                                    strMensajeEjecucionSolGestionada:   text,
                                                    intIdPuntoAnulacion:                id_punto
                                                };
                                                ejecutaGestionSimultanea(objInfoGestionSimultanea);
                                            }
                                            else
                                            {
                                                anulacionPunto(text,id_punto);
                                            }
                                        }
                                        else {
                                            Ext.Msg.alert('Alerta', 'Error: ' + text);
                                        }
                                    },
                                    failure: function(result) {
                                        Ext.Msg.alert('Alerta', result.responseText);
                                    }
                                });
                            }
                        }
                        else {
                             cierraVentanaAnularOrden_Coordinar();
                             Ext.Msg.alert('Alerta', mensajeError);
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAnularOrden_Coordinar();
                    }
                }
            ]
        });

        winAnularOrden_Coordinar = Ext.widget('window', {
            title: 'Anulacion de Orden de Servicio',
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelAnularOrden_Coordinar]
        });
    }

    winAnularOrden_Coordinar.show();
          
}

/*
 * showRegistroEquiposEntregados
 *
 * Función para mostrar pantalla de registro de equipos entregados en traslados MD en diferente tecnología
 *
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.0 11-01-2022
 *
 */
function showRegistroEquiposEntregados(idServicio)
{
    winRegistroEquipos = "";
    formPanelAnularOrden_Coordinar = "";
    if (!winRegistroEquipos)
    {
        Ext.define('estadoEquipo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'value', type: 'string'},
                {name: 'etiqueta', type: 'string'}
            ]
        });
        storeEstadoEquipo = new Ext.data.Store({
            model: 'estadoEquipo',
            data: [
                {value: 'EnOficinaMd'},
                {value: 'NO ENTREGADO'}
            ]
        });

        storeElementosRetirar = new Ext.data.Store({
            pageSize: 100,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlGetElementosRetirarTraslado,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    idServicio: idServicio
                }
            },
            fields:
                [
                    {name: 'idElemento',           mapping: 'idElemento'},
                    {name: 'tipoElemento',         mapping: 'tipoElemento'},
                    {name: 'serieElemento',        mapping: 'serieElemento'},
                    {name: 'estadoElemento',       mapping: 'estadoElemento'},
                    {name: 'observacion',          mapping: 'observacion'},
                    {name: 'idServicioProdCaract', mapping: 'idServicioProdCaract'}
                ]
        });

        var cellEditingElementos = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2
        });

        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* '+
                                      'Debe ingresar el estado y la observación de todos los equipos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        gridElementos = Ext.create('Ext.grid.Panel', {
            id: 'gridElementos',
            store: storeElementosRetirar,
            columnLines: true,
            plugins: [cellEditingElementos],
            columns: [
                {
                    id: 'idElemento',
                    header: 'idElemento',
                    dataIndex: 'idElemento',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'idServicioProdCaract',
                    header: 'idServicioProdCaract',
                    dataIndex: 'idServicioProdCaract',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Tipo Elemento',
                    dataIndex: 'tipoElemento',
                    width: 110,
                    readOnly: true
                },
                {
                    header: 'Serie',
                    dataIndex: 'serieElemento',
                    width: 150,
                    editor: {
                        xtype: 'textfield',
                        valueField: ''
                    },
                    readOnly: true
                },
                {
                    header: 'Estado',
                    dataIndex: 'estadoElemento',
                    width: 140,
                    sortable: true,
                    editor: {
                        queryMode: 'local',
                        editable: false,
                        xtype: 'combobox',
                        displayField: 'value',
                        valueField: 'value',
                        loadingText: 'Buscando...',
                        store: storeEstadoEquipo
                    }
                },
                {
                    header: 'Observación',
                    dataIndex: 'observacion',
                    width: 208,
                    editor: {
                        xtype: 'textfield',
                        valueField: ''
                    }
                },
            ],
            viewConfig: {
                stripeRows: true
            },
            frame: true,
            height: 200,
            title: 'Elementos'
        });

        formPanelRegistroEquipos = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 5,
            bodyStyle: "background: white; padding:5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'fieldset',
                    title: 'Lista de equipos a entregar',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 10px;",
                    defaults: {
                        width: 620
                    },
                    items: [gridElementos
                    ]//cierre del fieldset
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var mensajeError = "";
                        var boolError = false;
                        var datosElementos = getInfoElementos();

                        if (parseInt(datosElementos) == 1) {
                            boolError = true;
                            mensajeError  = "Por favor ingrese el estado y observación de todos los equipos. " +
                                            "En esta pantalla debe ser entregado un equipo como mínimo.";
                        }

                        if (!boolError)
                        {
                            connFact.request({
                                url: urlGrabaEquiposEntregados,
                                method: 'post',
                                timeout: 400000,
                                params: {
                                    idServicio: idServicio,
                                    datosElementos: datosElementos
                                },
                                success: function(responseValida) {
                                    var datosGrabaEquiposMd = Ext.JSON.decode(responseValida.responseText);
                                    if (datosGrabaEquiposMd.strEstado == "OK")
                                    {
                                        Ext.Msg.alert('Mensaje', datosGrabaEquiposMd.strMensaje);
                                        cierraVentanaRegistroEquipos();
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Mensaje', datosGrabaEquiposMd.strMensaje);
                                    }
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Alerta', result.responseText);
                                    store.load();
                                }
                            });
                        }
                        else
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: mensajeError,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRegistroEquipos();
                    }
                }
            ]
        });
        var w = window.innerWidth;
        var h = window.innerHeight;
        winRegistroEquipos = Ext.widget('window', {
            title: 'Registro de equipos entregados',
            layout: 'fit',
            width: 675,
            height: 400,
            resizable: false,
            closabled: false,
            items: [formPanelRegistroEquipos],
            listeners: {
                close: function () {
                    store.load();
                },
                show: function() {
                    this.el.setStyle('left', w -this.getWidth());
                    this.el.setStyle('top', h -this.getHeight());
                }
            },
        });
    }

    winRegistroEquipos.show();
}

/*
 * getInfoElementos
 *
 * Función para obtener elementos del grid de pantalla de entrega de equipos en traslados
 *
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.0 11-01-2022
 *
 */
function getInfoElementos()
{
    var responseElementos = new Object();
    responseElementos['total'] = gridElementos.getStore().getCount();
    responseElementos['elementos'] = new Array();

    var arrayElemento = new Array();
    var entregaEquipos = "NO";
    for (var i = 0; i < gridElementos.getStore().getCount(); i++) {
        if (gridElementos.getStore().getAt(i).data.estadoElemento === "" || gridElementos.getStore().getAt(i).data.observacion === "") {
            return 1;
        }
        else {
            if (gridElementos.getStore().getAt(i).data.estadoElemento === "EnOficinaMd") {
                entregaEquipos = "SI";
            }
            arrayElemento.push(gridElementos.getStore().getAt(i).data);
        }
    }
    if (entregaEquipos === "NO") {
        return 1;
    }
    responseElementos['elementos'] = arrayElemento;
    return Ext.JSON.encode(responseElementos);
}

/*
 * cierraVentanaRegistroEquipos
 *
 * Funcion utilizada para cerrar popup de registro de equipos
 *
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.0 11-01-2022
 *
 */
function cierraVentanaRegistroEquipos() {
    store.load();
    winRegistroEquipos.close();
    winRegistroEquipos.destroy();
}

//funcion utilizada para cerrar popup de anulacion de orden de trabajo
function cierraVentanaAnularOrden_Coordinar() {
    winAnularOrden_Coordinar.close();
    winAnularOrden_Coordinar.destroy();
}

function anulacionPunto(text,id_punto)
{
    Ext.Msg.alert('Mensaje', text, function(btn) 
    {
        if (btn == 'ok') 
        {
            var permiso = $("#ROLE_13-1779");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            
            if (boolPermiso) 
            {
                connCoordinar.request({
                    url   : url_anular_punto,
                    method: 'post',
                    params: 
                    {
                        idPunto: id_punto
                    },
                    success: function(response) 
                    {
                        var text = response.responseText;
                        if (text == "si")
                        {
                            Ext.Msg.confirm('Alerta', 'Desea Anular este punto?', function(btn) {
                                if (btn == 'yes') 
                                {
                                    Ext.Ajax.request({
                                        url: url_anula_punto_ajax,
                                        method: 'post',
                                        params: {idPunto: id_punto},
                                        success: function(response) {
                                            var text = response.responseText;
                                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                                if (btn == 'ok') {
                                                    window.location = linkVerCliente;
                                                }
                                            });


                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            storePuntos.load();
                                        }
                                    });
                                }
                            });
                        }

                        store.load();
                    },
                    failure: function(result) 
                    {
                        Ext.Msg.alert('Alerta', result.responseText);
                    }
                });
            }
            else
            {
                store.load();
            }
        }
    });
}

function verCaracteristicasServicio(data,clienteEm) {
    Ext.tip.QuickTipManager.init();
    var storeCaracteristicasServicio = new Ext.data.Store({
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_getCaracteristicasServicio,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data,
                estado: 'Todos',
                clienteEm: clienteEm
            }
        },
        fields:
            [
                {name: 'idServicioProdCaract',      mapping: 'idServicioProdCaract'},
                {name: 'descripcionProducto',       mapping: 'descripcionProducto'},
                {name: 'descripcionCaracteristica', mapping: 'descripcionCaracteristica'},
                {name: 'valor',                     mapping: 'valor'},
                {name: 'estado',                    mapping: 'estado'},
                {name: 'feCreacion',                mapping: 'feCreacion'},
                {name: 'feUltMod',                  mapping: 'feUltMod'},
                {name: 'mostrarBoton',              mapping: 'mostrarBoton'}
            ]
    });

    
    //grid de usuarios
    gridCaracteristicasServicio = Ext.create('Ext.grid.Panel', {
        id: 'gridCaracteristicasServicio',
        store: storeCaracteristicasServicio,
        columnLines: true,
        columns: [{
                dataIndex: 'idServicioProdCaract',
                width: 100,
                hidden: true
            },
            {
                dataIndex: 'mostrarBoton',
                width: 100,
                hidden: true
            },
            {
                header: 'Descripcion Producto',
                dataIndex: 'descripcionProducto',
                width: 150
            },
            {
                header: 'Descripcion Caracteristicas',
                dataIndex: 'descripcionCaracteristica',
                width: 150
            },
            {
                header: 'Valor',
                dataIndex: 'valor',
                width: 100
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 80
            },
            {
                header: 'Fecha Creacion',
                dataIndex: 'feCreacion',
                width: 120
            },
            {
                header: 'Fecha Modificacion',
                dataIndex: 'feUltMod',
                width: 100
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 100,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_13-2618");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso)
                            {
                                if (rec.data.mostrarBoton == "SI")
                                {
                                    this.items[0].tooltip = 'Actualizar valor de caracteristica';
                                    return 'button-grid-cambioVelocidad';
                                }
                                else
                                {
                                    return "icon-invisible";
                                }
                            }
                            else
                            {
                                return "icon-invisible";
                            }
                        },
                        tooltip: 'Actualizar valor de caracteristica',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = storeCaracteristicasServicio.getAt(rowIndex);
                            actualizarValorCaracteristica(rec.data.idServicioProdCaract,
                                data,
                                rec.data.estado,
                                rec.data.valor,
                                rec.data.descripcionCaracteristica);

                        }
                    }
                ]
            }],
        viewConfig: {
            stripeRows: true
        },
        frame: true,
        height: 300
            //title: 'Historial del Servicio'
    });


    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 2,
            align: 'center'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 800,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar(data);
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }

        ],
        items:
            [{html: "&nbsp;", border: false, width: 250},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Estado',
                    id: 'cmb_estado',
                    value: 'Todos',
                    editable: false,
                    triggerAction: 'all',
                    store: [
                        ['Todos', 'Todos'],
                        ['Activo', 'Activo'],
                        ['Anulado', 'Anulado'],
                        ['Cancel', 'Cancel'],
                        ['Eliminado', 'Eliminado']
                    ]
                }

            ]
    });

    function buscar(data) {
        var boolError = false;
        storeCaracteristicasServicio.getProxy().extraParams.estado = Ext.getCmp('cmb_estado').value;
        storeCaracteristicasServicio.getProxy().extraParams.idServicio = data;
        storeCaracteristicasServicio.load();
    }

    function actualizarValorCaracteristica(idServProdCaract, data, estadoAct, valorAct, caracteristica)
    {
        var formPanel = Ext.create('Ext.form.Panel',
            {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults:
                    {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                layout:
                    {
                        type: 'table',
                        // The total column count must be specified here
                        columns: 2
                    },
                items:
                    [
                        {
                            xtype: 'fieldset',
                            title: '',
                            defaultType: 'textfield',
                            defaults:
                                {
                                    width: 250
                                },
                            items:
                                [
                                    {
                                        xtype: 'textfield',
                                        id: 'valorCaracteristica',
                                        name: 'valorCaracteristica',
                                        fieldLabel: 'Valor Caracteristica',
                                        displayField: '',
                                        value: valorAct,
                                        valueField: '',
                                        maxLength: 1900,
                                        width: '250'
                                    },
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: 'Estado',
                                        id: 'cmb_estado_act',
                                        editable: false,
                                        triggerAction: 'all',
                                        value: estadoAct,
                                        width: '250',
                                        store: [
                                            ['Activo', 'Activo'],
                                            ['Anulado', 'Anulado'],
                                            ['Cancel', 'Cancel'],
                                            ['Eliminado', 'Eliminado']
                                        ]
                                    }

                                ]
                        }
                    ],
                buttons:
                    [
                        {
                            text: 'Grabar',
                            formBind: true,
                            handler: function()
                            {
                                var valor = Ext.getCmp('valorCaracteristica').value;
                                var estado = Ext.getCmp('cmb_estado_act').value;
                                if (valor == "" || estado == "")
                                {
                                    Ext.Msg.alert("Alerta", "Favor ingrese los valores correspondientes!");
                                }
                                else
                                {
                                    connActCaracteristicas.request
                                        ({
                                            url: actualizarCaracteristica,
                                            method: 'post',
                                            waitMsg: 'Esperando Respuesta del Elemento',
                                            timeout: 400000,
                                            params:
                                                {
                                                    idServicioProdCaract: idServProdCaract,
                                                    valor: Ext.getCmp('valorCaracteristica').value,
                                                    estado: Ext.getCmp('cmb_estado_act').value,
                                                    caracteristica: caracteristica
                                                },
                                            success: function(response)
                                            {
                                                var respuesta = response.responseText;

                                                if (respuesta == "Error")
                                                {
                                                    Ext.Msg.alert('Error ', 'Se presentaron problemas al actualizar la infornación,' +
                                                        ' favor notificar a Sistemas');
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('MENSAJE ', 'Se actualizo la información correctamente.');
                                                    buscar(data);
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    winValorCaract.destroy();

                                }

                            }
                        },
                        {
                            text: 'Cancelar',
                            handler: function()
                            {
                                winValorCaract.destroy();
                            }
                        }
                    ]
            });

        var winValorCaract = Ext.create('Ext.window.Window',
            {
                title: 'Actualizar valor de Caracteristica',
                modal: true,
                width: 300,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
    }

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 800
                },
                items: [
                    filterPanel,
                    gridCaracteristicasServicio

                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Caracteristicas del Servicio',
        modal: true,
        width: 850,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}





function agregarCarac(idServicio, idProducto, idPersonaRol, idPunto)
{
 
    storePoste = new Ext.data.Store
    ({ 
        total: 'total',
        pageSize: 200,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetPoste,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'idPoste',      mapping:'idPoste'},
            {name:'nombrePoste',  mapping:'nombrePoste'}
        ],
        autoLoad: true
    });   
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 95,
                msgTarget: 'side',
                bodyStyle: 'padding:20px'
            },
            layout: {
                type: 'table',
                columns: 1
            },
            defaults: {
                bodyStyle: 'padding:20px'
            },
            items: [
                    {
                        xtype: 'fieldset',
                        title: 'Ingreso de cámara',
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [
                        {
                            xtype: 'container',
                            autoWidth: true,
                            layout: {
                                type: 'table',
                                columns: 2
                            },
                            items: [
                            {
                                xtype: 'textfield',
                                id: 'txtSerie',
                                name: 'txtSerie',
                                fieldLabel: 'Serie',
                                value: '',
                                width: '50%',
                                enableKeyEvents: true,
                                listeners:
                                {
                                    blur: function(serie){
                                    
                                            Ext.Ajax.request({
                                                url: url_buscarDatosNaf,
                                                dataType: 'text',
                                                method: 'post',
                                                params: {   
                                                    serieCpe:          serie.getValue(),
                                                    idProducto: idProducto,
                                                    idPersonaRol: idPersonaRol
                                                },
                                                success: function(response){                                                                                
                                                    var respuestajson     = JSON.parse(response.responseText);
                                                    var arrayjson     =  respuestajson["mensaje"].split(",");
                                                    var modelo        =  arrayjson[0];
                                                    var mac        =  arrayjson[3];
                                                    var status        = respuestajson.status;


                                                    if(status=="OK")
                                                    {
                                                            
                                                            Ext.getCmp('txtModelo').value = modelo;
                                                            Ext.getCmp('txtModelo').setRawValue(modelo);
                                                            Ext.getCmp('txtMac').value = mac;
                                                            Ext.getCmp('txtMac').setRawValue(mac);
                                                            Ext.getCmp('btnAgregarCarac').setDisabled(false);

                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.alert('Mensaje ', respuestajson.mensaje);
                                                        
                                                        Ext.getCmp('txtModelo').value = '';
                                                        Ext.getCmp('txtModelo').setRawValue('');
                                                        Ext.getCmp('txtSerie').value = '';
                                                        Ext.getCmp('txtSerie').setRawValue('');
                                                        Ext.getCmp('txtMac').value = '';
                                                        Ext.getCmp('txtMac').setRawValue('');
                                                        Ext.getCmp('btnAgregarCarac').setDisabled(true);
                                                        
                                                    }
                                                },
                                                failure: function(result)
                                                {
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                }
                                            });//Ext.Ajax.request
                                        
                                    },
                                    keyup: function(form, e){
                                        Ext.getCmp('btnAgregarCarac').setDisabled(true);
                                    }
                                }
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Modelo',
                                id: 'txtModelo',
                                name: 'txtModelo',
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                value: '',
                                width: '50%'  
                            },
                            {
                                xtype: 'textfield',
                                id: 'txtMac',
                                name: 'txtMac',
                                fieldLabel: 'Mac',
                                value: '',
                                width: '50%'
                            },
                            {
                                xtype: 'textfield',
                                id: 'txtIdCamara',
                                name: 'txtIdCamara',
                                fieldLabel: 'ID Cámara',
                                value: '',
                                width: '50%'
                            }
                            ]
                        }]
                    
                    }, //cierre del fieldset
                    {
                        xtype: 'fieldset',
                        title: 'Poste de cámara',
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [
                            {
                                xtype: 'container',
                                autoWidth: true,
                                layout: {
                                    type: 'table',
                                    columns: 2
                                },
                                items: [
                                {
                                    xtype: 'textfield',
                                    id: 'textAltura',
                                    name: 'textAltura',
                                    fieldLabel: 'Altura de Poste',
                                    value: '',
                                    width: '50%',
                                    enableKeyEvents: true,
                                    listeners:
                                    {
                                        keypress: function(me, e)
                                        {
                                            validarSoloNumeros(me, e);
                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    store: storePoste,
                                    id: 'idPoste',
                                    name: 'idPoste',
                                    valueField: 'nombrePoste',
                                    displayField: 'nombrePoste',
                                    fieldLabel: 'Tipo de Poste',
                                    width: '50%',
                                    triggerAction: 'all'
                                }
                                ]
                            }],
                    } 
                ],         
            buttons:
                [
                    {
                        text: 'Grabar',
                        id: 'btnAgregarCarac',
                        disabled: true,
                        handler: function()
                        {
                            var serieCamara = Ext.getCmp('txtSerie').value;
                            var macCamara = Ext.getCmp('txtMac').value;
                            var modeloCamara = Ext.getCmp('txtModelo').value;
                            var idCamara = Ext.getCmp('txtIdCamara').value;
                            var alturaPosteCamara = Ext.getCmp('textAltura').value;
                            var tipoPosteCamara = Ext.getCmp('idPoste').value;


                            if (serieCamara == "") 
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese Serie de Cámara");
                            }
                            else if (alturaPosteCamara == "") 
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese Altura de Poste");
                            }
                            else if (tipoPosteCamara == "") 
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese Tipo de Poste");
                            }
                            else
                            {
                                connDescPresentaFactura.request
                                ({
                                    url: url_agregarCarac,
                                    method: 'post',
                                    waitMsg: 'Esperando Respuesta',
                                    timeout: 400000,
                                    params:
                                        {
                                            idServicio: idServicio,
                                            idProducto: idProducto,
                                            serieCamara : serieCamara,
                                            macCamara : macCamara,
                                            modeloCamara: modeloCamara,
                                            idCamara : idCamara,
                                            alturaPosteCamara : alturaPosteCamara,
                                            tipoPosteCamara : tipoPosteCamara,
                                            idPersonaRol : idPersonaRol,
                                            idPunto : idPunto
                                        },
                                    success: function(response)
                                    {
                                        var respuesta = response.responseText;

                                        if (respuesta == "OK")
                                        {
                                            Ext.Msg.alert('MENSAJE ', 'Se actualizo la información correctamente.');
                                            store.load({params: {start: 0, limit: 10}});
                                        }
  
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                                
                                winIngresoCarac.destroy();
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winIngresoCarac.destroy();
                        }
                    }
                ]
        });



    var winIngresoCarac = Ext.create('Ext.window.Window',
        {
            title: 'Ingreso de Cámara PTZ',
            modal: true,
            width: 630,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

/**
 * Documentación para el método 'cambiarPlanActivoEquivalente'.
 *
 * Ejecuta Acción para actualizar el id del plan de un servicio, se recupera un plan con estado Activo con el mismo
 * nombre del plan que registra el servicio.
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 10-08-2016
 * 
 * @param {int} idServicio identificador de servicio seleccionado
 * 
 * @since 1.0
 */
function cambiarPlanActivoEquivalente(idServicio) {
    Ext.Msg.alert('Mensaje','Se intentará recuperar un plan equivalente con estado Activo para actualizar el servicio seleccionado, '+
                            '<br>Desea Continuar?', function(btn){
            if(btn=='ok')
            {
                connFact.request({
                    url: url_actualizaPlanEquivalente,
                    method: 'post',
                    timeout: 400000,
                    params: { 
                        idServicio: idServicio
                    },
                    success: function(response){
                        if(response.responseText == "OK")
                        {
                            Ext.Msg.alert('Mensaje','Se actualizo el plan del servicio!', function(btn){
                                if(btn=='ok'){
                                    store.load();
                                }
                            });
                        }
                        else{
                            Ext.Msg.alert('Mensaje ',response.responseText);
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

function renovarPlan( intIdServicio, strFechaRenovacion )
{
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
                       msg: 'Renovando el plan, Por favor espere!!',
                       progressText: 'Guardando...',
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
    
    
    Ext.Msg.confirm('Alerta', 'Está seguro que desea renovar el siguiente plan?', function(btn)
    {
        if (btn == 'yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlRenovacionPlan,
                params:
                {
                    intIdServicio: intIdServicio, 
                    strFechaRenovacion: strFechaRenovacion
                },
                method: 'post',
                success: function(response)
                {
                    if( "OK" == response.responseText )
                    {
                        Ext.Msg.alert('Información', 'Plan renovado con éxito');
                        store.load();
                    }
                    else
                    {
                        Ext.Msg.alert('Error ', response.responseText);
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}

function cargarArchivo(idPunto, idServicio)
{
    var id_servicio = idServicio;
    var id_punto = idPunto;
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Procesando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    var formPanel = Ext.create('Ext.form.Panel',
        {
            width: 500,
            frame: true,
            bodyPadding: '10 10 0',
            defaults: {
                anchor: '100%',
                allowBlank: false,
                msgTarget: 'side',
                labelWidth: 50
            },
            items: [
                {
                    xtype: 'combobox',
                    fieldLabel: '<b>Tipo Anexo</b>',
                    id: 'cmbModulo',
                    name: 'cmbModulo',
                    value:'TECNICO',
                    store: [					
                        ['TECNICO','TECNICO'],
                        ['COMERCIAL','COMERCIAL']
                    ]
                },
                {
                    xtype: 'filefield',
                    id: 'form-file',
                    name: 'archivo',
                    emptyText: 'Seleccione una Archivo',
                    buttonText: 'Browse',
                    buttonConfig: {
                        iconCls: 'upload-icon'
                    }
                }],
            buttons: [{
                    text: 'Subir',
                    handler: function() {
                        var form = this.up('form').getForm();
                        if (form.isValid())
                        {
                            form.submit({
                                url: url_fileUpload,
                                params: {
                                    idServicio: id_servicio,
                                    idPunto: id_punto,
                                    tipo:Ext.getCmp('cmbModulo').getValue()
                                },
                                waitMsg: 'Procesando Archivo...',
                                success: function(fp, o)
                                {
                                    Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn) {
                                        if (btn == 'ok')
                                        {
                                            store.load({params: {start: 0, limit: 10}});
                                            win.destroy();
                                        }
                                    });
                                },
                                failure: function(fp, o) {
                                    Ext.Msg.alert("Alerta", o.result.respuesta);
                                }
                            });
                        }
                    }
                }, {
                    text: 'Cancelar',
                    handler: function() {
                        win.destroy();
                    }
                }]
        });

    var win = Ext.create('Ext.window.Window', {
        title: 'Cargar Archivo',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function subirArchivoAnexo(idServicio)
{
    var id_servicio = idServicio;

    var formPanel = Ext.create('Ext.form.Panel',
        {
            width: 500,
            height: 80,
            frame: true,
            bodyPadding: '10 10 0',
            defaults: {
                anchor: '100%',
                allowBlank: false,
                msgTarget: 'side',
                labelWidth: 50
            },
            items: [
                {
                    xtype: 'filefield',
                    id: 'form-file',
                    name: 'archivo',
                    emptyText: 'Busque y seleccione un archivo con extensión .pdf',
                    buttonText: 'Seleccionar',
                    accept: ['pdf'],
                    buttonConfig: {
                        iconCls: 'upload-icon'
                    },
                    listeners:
                    {
                        change : function(obj) {

                              var indexofPeriod = obj.getValue().lastIndexOf("."),
                                  uploadedExtension = obj.getValue().substr(indexofPeriod + 1, obj.getValue().length - indexofPeriod);

                              if (!Ext.Array.contains(this.accept, uploadedExtension)){
                                  obj.setActiveError('Por favor sólo suba archivos con las siguientes extensiones :  ' + this.accept.join() + ' !');
                                  Ext.MessageBox.show({
                                      title   : 'Error en el Tipo de Archivo',
                                      msg     : 'Archivo con extensión <b>' + uploadedExtension + '</b> no permitida, intente nuevamente con \n\
                                                 otro archivo.',
                                      buttons : Ext.Msg.OK,
                                      icon    : Ext.Msg.ERROR,
                                      fn: function(buttonId) {
                                      }
                                  });
                                  obj.setRawValue(null);
                                  Ext.getCmp('btnSubir').disable();
                              }
                              else
                              {
                                  Ext.getCmp('btnSubir').enable();
                              }
                          }
                    }
                }],
            buttons: [{
                    text: 'Subir',
                    id: 'btnSubir',
                    disabled: true,
                    handler: function() {
                        var form = this.up('form').getForm();
                        if (form.isValid())
                        {
                            form.submit({
                                url: url_subirDocumentoAnexo,
                                params: {
                                    idServicio: id_servicio
                                },
                                waitMsg: 'Procesando Archivo...',
                                success: function(fp, o)
                                {
                                    Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn) {
                                        if (btn == 'ok')
                                        {
                                            store.load({params: {start: 0, limit: 10}});
                                            win.destroy();
                                        }
                                    });
                                },
                                failure: function(fp, o) {
                                    Ext.Msg.alert("Alerta", o.result.respuesta);
                                }
                            });
                        }
                    }
                }, {
                    text: 'Cancelar',
                    handler: function() {
                        win.destroy();
                    }
                }]
        });

    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Mapa de recorrido del cliente',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function verDocumento(idServicio)
{
    var id_servicio = idServicio;
    var cantidadDocumentos = 1;
    var connDocumentos = new Ext.data.Connection
    ({
        listeners: 
        {
            'beforerequest': 
            {
                fn: function (con, opt) 
                {
                    Ext.MessageBox.show
                    ({
                        msg: 'Consultando documentos, Por favor espere!!',
                        progressText: 'Consultando...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
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

    connDocumentos.request
    ({
        url: url_verifica_documentos,
        method: 'post',
        params:{ idServicio: id_servicio },
        success: function (response)
        {
            var text           = Ext.decode(response.responseText);
            cantidadDocumentos = text.total;

            if (cantidadDocumentos > 0)
            {
                var storeDocumentos = new Ext.data.Store
                ({
                    pageSize: 1000,
                    autoLoad: true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: url_verDocumentos,
                        reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams:
                        {
                            idServicio: id_servicio
                        }
                    },
                    fields:
                    [
                        {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                        {name: 'feCreacion', mapping: 'feCreacion'},
                        {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                        {name: 'idDocumento', mapping: 'idDocumento'}
                    ]
                });

                Ext.define('Documentos', 
                {
                    extend: 'Ext.data.Model',
                    fields:
                    [
                        {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                        {name: 'feCreacion', mapping: 'feCreacion'},
                        {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                        {name: 'idDocumento', mapping: 'idDocumento'}
                    ]
                });

                //grid de documentos
                gridDocumentos = Ext.create('Ext.grid.Panel',
                {
                    id: 'gridMaterialesPunto',
                    store: storeDocumentos,
                    columnLines: true,
                    columns: 
                    [
                        {
                            header: 'Nombre Archivo',
                            dataIndex: 'ubicacionLogica',
                            width: 260
                        },
                        {
                            header: 'Fecha de Carga',
                            dataIndex: 'feCreacion',
                            width: 120
                        },
                        {
                            xtype: 'actioncolumn',
                            header: 'Acciones',
                            width: 100,
                            items:
                            [
                                {
                                    iconCls: 'button-grid-show',
                                    tooltip: 'Ver Archivo Digital',
                                    handler: function (grid, rowIndex, colIndex) 
                                    {
                                        var rec = storeDocumentos.getAt(rowIndex);
                                        verArchivoDigital(rec);
                                    }
                                },
                                {
                                    iconCls: 'button-grid-delete',
                                    tooltip: 'Eliminar Archivo Digital',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        var rec         = storeDocumentos.getAt(rowIndex);
                                        var idDocumento = rec.get('idDocumento');

                                        eliminarAdjunto(storeDocumentos,idDocumento);
                                    }
                                }
                            ]
                        }
                    ],
                    viewConfig:
                    {
                        stripeRows: true,
                        enableTextSelection: true
                    },
                    frame: true,
                    height: 200
                });

                function verArchivoDigital(rec)
                {
                    var idDocumento = rec.get('idDocumento');
                    window.location = url_descargaDocumentos + '?idDocumento=' + idDocumento;
                }

                function eliminarAdjunto(storeDocumentos,idDocumento)
                {
                    Ext.Msg.confirm('Alerta','Se eliminará el documento. Desea continuar?', function(btn)
                    {
                        if(btn=='yes')
                        {
                              Ext.MessageBox.wait("Eliminando Archivo...", 'Por favor espere');
                              Ext.Ajax.request({
                                url: url_eliminar_adjunto,
                                method: 'post',
                                params: { id:idDocumento },
                                success: function(response)
                                {
                                    Ext.MessageBox.hide();
                                    var json = Ext.JSON.decode(response.responseText);

                                    if (json.status=="OK")
                                    {
                                        Ext.MessageBox.show({
                                            title: "Información",
                                            cls: 'msg_floating',
                                            msg: json.message,
                                            icon: Ext.MessageBox.INFO,
                                            buttons: Ext.Msg.OK,
                                            fn: function(buttonId)
                                            {
                                                if (buttonId === "ok")
                                                {
                                                    storeDocumentos.load();
                                                }
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.show(
                                        {
                                           title: 'Error',
                                           width: 300,
                                           cls: 'msg_floating',
                                           icon: Ext.MessageBox.ERROR,
                                           msg: json.message
                                        });
                                    }
                                  },
                                  failure: function(response)
                                  {
                                    Ext.MessageBox.hide();
                                    var json = Ext.JSON.decode(response.responseText);
                                    Ext.Msg.show(
                                    {
                                       title: 'Error',
                                       width: 300,
                                       cls: 'msg_floating',
                                       icon: Ext.MessageBox.ERROR,
                                       msg: json.message
                                    });
                                  }
                              });
                        }
                    });
                }

                var formPanel = Ext.create('Ext.form.Panel',
                {
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
                                width: 510
                            },
                            items: 
                            [
                                gridDocumentos
                            ]
                        }
                    ],
                    buttons: 
                    [{
                        text: 'Cerrar',
                        handler: function ()
                        {
                            win.destroy();
                            store.load();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window',
                {
                    title: 'Documentos Cargados',
                    modal: true,
                    width: 550,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
                
            } 
            else
            {
                Ext.Msg.show
                ({
                    title: 'Mensaje',
                    msg: 'El servicio seleccionado no posee archivos adjuntos.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                });
            }

        },
        failure: function (result)
        {
            Ext.Msg.show
            ({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}

function cambiarFrecuenciaFacturacion(intIdServicio, intFrecuenciaFact)
{

    storeFrecuencias = new Ext.data.Store(
        {
            total: 'total',
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: urlFrecuenciasFacturacion,
                    timeout: 120000,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'id', mapping: 'id'},
                    {name: 'name', mapping: 'name'}
                ]
        });

    storeFrecuencias.on('load', function()
    {
        storeFrecuencias.each(function(record)
        {
            if (typeof record !== typeof undefined)
            {
                if (parseInt(record.get('id')) === parseInt(intFrecuenciaFact))
                {
                    // Se remueve el código "storeFrecuencias.removeAt(itemIndex);" para no excluir la frecuencia actual del servicio.
                    itemIndex = storeFrecuencias.data.indexOf(record);
                }
            }
        });
    });

    var formCambiarFrecFact = Ext.create('Ext.form.Panel',
        {
            id: 'formEditar',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 125,
                    msgTarget: 'side'
                },
            buttons:
                [
                    {
                        text: 'Guardar',
                        handler: function()
                        {
                            var strFrecuencia = Ext.getCmp('cbxFrecuencia').getValue();
                            var strFrecuenciaN = Ext.getCmp('cbxFrecuencia').getRawValue();
                            
                            if (parseInt(strFrecuencia) >= 0)
                            {
                                msg = 'Se actualizará la frecuencia de facturación del servicio a ' + strFrecuenciaN + '. <br> ¿Desea continuar?';

                                Ext.Msg.confirm('Alerta', msg, function(btn)
                                {
                                    if (btn === 'yes')
                                    {
                                        connEsperaAccion2.request
                                            (
                                                {
                                                    url: urlCambiarFrecuenciaFacturacion,
                                                    method: 'POST',
                                                    timeout: 60000,
                                                    params:
                                                        {
                                                            servicio: intIdServicio,
                                                            frecuencia: Ext.getCmp('cbxFrecuencia').getValue()
                                                        },
                                                    success: function(response)
                                                    {
                                                        if (response.statusText == 'OK')
                                                        {
                                                            Ext.Msg.show(
                                                                {
                                                                    title: 'Información',
                                                                    msg: 'Se actualizó la frecuencia exitosamente.',
                                                                    buttons: Ext.Msg.OK,
                                                                    icon: Ext.MessageBox.INFO
                                                                });
                                                            win3.destroy();

                                                            store.currentPage = 1;
                                                            store.load({params: {start: 0, limit: 10}});
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.show(
                                                                {
                                                                    title: 'Error',
                                                                    msg: response.responseText,
                                                                    buttons: Ext.Msg.OK,
                                                                    icon: Ext.MessageBox.ERROR
                                                                });
                                                        }
                                                    },
                                                    failure: function(result)
                                                    {
                                                        Ext.MessageBox.hide();
                                                        Ext.Msg.alert('Error', result.responseText);
                                                    }
                                                }
                                            );
                                    }
                                });

                            }
                            else
                            {
                                Ext.Msg.show({
                                    title: 'Atenci\xf3n',
                                    msg: 'No ha seleccionado la frecuencia',
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            win3.destroy();
                        }
                    }
                ],
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: 400,
                        labelWidth: 40,
                        width: 320,
                        items:
                            [
                                {
                                    fieldLabel: 'Frecuencia',
                                    type: 'combobox',
                                    id: 'cbxFrecuencia',
                                    labelStyle: 'font-weight:bolder;',
                                    width: 250,
                                    xtype: 'combo',
                                    hiddenName: 'rating',
                                    queryMode: 'local',
                                    store: storeFrecuencias,
                                    valueField: 'id',
                                    displayField: 'name',
                                    triggerAction: 'all',
                                    editable: false
                                }
                            ]
                    }
                ]
        });

    Ext.getBody().setStyle('overflow', 'auto');

    win3 = Ext.create('Ext.window.Window',
        {
            title: 'Cambiar Frecuencia de Facturación',
            modal: true,
            width: 320,
            closable: true,
            resizable: false,
            layout: 'fit',
            items: [formCambiarFrecFact],
            listeners:
                {
                    show: function(win)
                    {
                        intBeginTop = document.getElementById('my-tabs').scrollHeight;
                        win.setPosition(win.left, intBeginTop + 250);
                        $('html,body').animate({scrollTop: intBeginTop + 100}, 100);
                    }
                }
        }).show();
}

var connEsperaAccion2 = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Actualizando Frecuencia de Facturación del Servicio.',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });
    /**
    * Función que sirve para reemplazar caractreres especiales en una cadena.
    * @param string cadena
    * @return string cadena
    * @author Edgar Holguín<eholguin@telconet.ec>
    * @version 1.0 11-10-2018
    */    
    function getCleanedString(cadena){
       cadena = cadena.replace(/á/gi,"a");
       cadena = cadena.replace(/é/gi,"e");
       cadena = cadena.replace(/í/gi,"i");
       cadena = cadena.replace(/ó/gi,"o");
       cadena = cadena.replace(/ú/gi,"u");
       cadena = cadena.replace(/ñ/gi,"n");   
       cadena = cadena.replace(/[^a-zA-Z 0-9.]+/g,'');
       return cadena;
    }
 
    /*
     * Función que sirve para enviar al store los datos que se
     * llenaron en los parámetros de busqueda.
     */
    function buscarServicios(){
        if ((Ext.getCmp('fechaDesde').getValue() !== null) && (Ext.getCmp('fechaHasta').getValue() !== null))
        {
            if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
            {
                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });

            }
            else
            {
                store.load({params: {start: 0, limit: 10}});
            }
        }      
        store.getProxy().extraParams.producto              = Ext.getCmp('sltProducto').getValue();
        store.getProxy().extraParams.plan                  = Ext.getCmp('sltPlanes').getValue();
        store.getProxy().extraParams.estado                = Ext.getCmp('sltEstado').getValue();
        store.getProxy().extraParams.fechaDesde            = Ext.getCmp('fechaDesde').getValue();
        store.getProxy().extraParams.fechaHasta            = Ext.getCmp('fechaHasta').getValue();     
        store.getProxy().timeout                    = 999999;
        store.load({params: {start: 0, limit: 10}});
    }

    function limpiar(){
        
        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
        Ext.getCmp('sltProducto').setValue('');
        Ext.getCmp('sltPlanes').setValue('');
        Ext.getCmp('sltEstado').setValue('Todos');
            
    }


function verResumenRecursosContratados(idServicio)
{
    Ext.MessageBox.wait("Consultando Información de Recursos Contratados");
    
    //Grid de Resumen de Recursos
    
    
    Ext.Ajax.request({
        url: urlGetInformacionGeneralHosting,
        method: 'post',
        params: 
        { 
            idServicio      : idServicio,
            tipoInformacion : 'GENERAL'
        },
        success: function(response)
        {
            Ext.MessageBox.hide();
            
            var objJson = Ext.JSON.decode(response.responseText)[0];            
            
            var formPanelResumenHousing = Ext.create('Ext.form.Panel', {
                buttonAlign: 'center',
                BodyPadding: 10,
                width: 620,
                height: 650,
                bodyStyle: "background: white; padding: 5px; border: 0px none;",
                frame: true,
                items:
                    [
                        //PARA LICENCIA
                        {
                            xtype: 'fieldset',
                            title: '<i class="fa fa-sliders" aria-hidden="true"></i>&nbsp;<b>Información de Licenciamiento</b>',
                            defaults: {
                                height: 80,
                                width: 560
                            },
                            layout: {
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items:
                                [
                                    getGridResumen(objJson.arrayDetalleRecursos, 'TIPO LICENCIAMIENTO SERVICE')
                                ]
                        },
                        {
                            xtype: 'fieldset',
                            title: '<i class="fa fa-sliders" aria-hidden="true"></i>&nbsp;<b>Información de Storage</b>',
                            layout: {
                                tdAttrs: {style: 'padding: 5px;'},
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items:
                                [
                                    getGridResumen(objJson.arrayDetalleRecursos, 'DISCO')
                                ]
                        },
                        //Recursos Disponibles
                        {
                            xtype: 'fieldset',
                            title: '<i class="fa fa-sliders" aria-hidden="true"></i>&nbsp;<b>Información de Procesador</b>',
                            defaults: {
                                height: 80,
                                width: 560
                            },
                            layout: {
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items:
                                [
                                    getGridResumen(objJson.arrayDetalleRecursos, 'PROCESADOR')
                                ]
                        },
                        //Creacion de Maquinas Virtuales
                        {
                            xtype: 'fieldset',
                            title: '<i class="fa fa-sliders" aria-hidden="true"></i>&nbsp;<b>Información de Memoria Ram</b>',
                            defaults: {
                                height: 80,
                                width: 560
                            },
                            layout: {
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items:
                                [
                                    getGridResumen(objJson.arrayDetalleRecursos, 'MEMORIA')
                                ]
                        }
                    ],
                buttons: [
                    {
                        text: '<i class="fa fa-window-close" aria-hidden="true"></i>&nbsp;Cerrar',
                        handler: function ()
                        {
                            winResumenPoolRecursos.close();
                            winResumenPoolRecursos.destroy();
                        }
                    }
                ]});

            var winResumenPoolRecursos = Ext.widget('window', {
                id: 'winResumenPoolRecursos',
                title: 'Resumen Recursos Contratados',
                layout: 'fit',
                resizable: true,
                modal: true,
                closable: true,
                width: 'auto',
                items: [formPanelResumenHousing]
            }).show();
        }
    });
}

function getGridResumen(arrayRecursos,tipo)
{
    Ext.define('detalleRecursos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'nombreRecurso', type: 'string'},                        
            {name: 'valor',         type: 'string'}
        ]
    }); 
                
    var storeDetalleRecursos = new Ext.data.Store({
        pageSize: 5,
        autoDestroy: true,
        model: 'detalleRecursos',
        proxy: {
            type: 'memory'
        }
    });
        
    var array  = [];
    var unidad = '';
    
    switch(tipo)
    {
        case 'DISCO':
            array  = arrayRecursos.arrayDetalleDisco;
            unidad = '(GB)';
            break;

        case 'PROCESADOR':
            array  = arrayRecursos.arrayDetalleProcesador;
            unidad = '(Cores)';
            break;            

        case 'TIPO LICENCIAMIENTO SERVICE':
            array  = arrayRecursos.arrayDetalleLicencia;
            unidad = '(Unidad)';
            break;   

        default :
            array  = arrayRecursos.arrayDetalleMemoria;
            unidad = '(GB)';
            break;
    }
    
    $.each(array,function(i , item)
    {
        var recordParamDet = Ext.create('detalleRecursos', {
            nombreRecurso: item.nombreRecurso,                        
            valor        : item.valor
        });

        storeDetalleRecursos.insert(i, recordParamDet);
    });
    
    var gridDetalleRecursos = Ext.create('Ext.grid.Panel', {
        width: 550,        
        height: 100,
        store: storeDetalleRecursos,
        loadMask: true,
        frame: false,
        columns: [
            {                
                header: '<b>Recurso Contratado</b>',
                dataIndex: 'nombreRecurso',
                width: 400
            },
            {                
                header: '<b>Cantidad Contratada</b>',
                dataIndex: 'valor',
                width: 120,
                align:'center',
                renderer: function(val)
                {                    
                    return '<label style="color:#4D793E;"><b>'+val+' '+unidad+'</b></label>';
                }
            }
        ]
    });  
    
    return gridDetalleRecursos;
}

/*
* Función que levanta una ventana modal, para registrar los motivos de la eliminación.
* Continua con la eliminación de la orden de servicio. 
* 
* @author Josselhin Moreira <kjmoreira@telconet.ec>
* @version 1.0 07-03-2019 | Versión Inicial.
* 
* @author Katherine Yager <kyager@telconet.ec>
* @version 1.1 17-12-2019 | Se agrega validación para que aparezca mensaje de ejecución de Nc masiva.
*/
function showMotivoEliminarServicio(id,solicitudId,esSolucion){
    winMotivoEliminarServicio = "";
    formPanelMotivoEliminarServicio = "";
    if (!winMotivoEliminarServicio)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });
        formPanelMotivoEliminarServicio = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 5,
            bodyStyle: "background: white; padding:5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'panel',
                    frame: false,
                    layout: {type: 'hbox', align: 'stretch'}
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos de la Eliminación',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 10px;",
                    defaults: {
                        width: '500px'
                    },
                    items: [
                        {
                            xtype: 'textarea',
                            fieldLabel: '* Observacion',
                            name: 'info_observacion',
                            id: 'info_observacion',
                            allowBlank: false,
                            labelStyle: "color:red;"
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmbMotivo',
                            fieldLabel: '* Motivo',
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField: 'nombre_motivo',
                            valueField: 'id_motivo',
                            selectOnTab: true,
                            store: storeMotivosEliminacion,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;"
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Eliminar',
                    handler: function() {
                        var txtObservacion = Ext.getCmp('info_observacion').value;
                        var cmbMotivo      = Ext.getCmp('cmbMotivo').value;
                        var boolError      = false;
                        var mensajeError   = "";

                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }

                        connFact.request({
                        url: urlGetProcesoMasivoNC,
                        method: 'post',
                        timeout: 400000,
                        async: false,
                            success: function(response)
                            {

                                    if (response.responseText === 'NO')
                                    {
                                        boolError = true;
                                        mensajeError += "Se encuentra en proceso la Aprobación masiva de NC, espere unos minutos por favor y vuelva a intentar.\n";
                                    }

                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        })

                    if (!boolError)
                        {   
                            deleteServices(id,solicitudId,txtObservacion,cmbMotivo);
                        }
                        else
                        {
                            cierraVentanaMotivosEliminarServicio();
                            Ext.Msg.alert('Error ','Error: ' + mensajeError);

                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaMotivosEliminarServicio();
                    }
                }
            ]
        });
        winMotivoEliminarServicio = Ext.widget('window', {
            title: 'Eliminación de Orden de Servicio',
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelMotivoEliminarServicio]
        });
    }
    winMotivoEliminarServicio.show();
}

/**
 * Documentación para el método 'verDocumentoInspeccion'.
 *
 * Método encargado de presentar los archivos de inspeccion para
 * Wifi alquiler de equipos en el grid comercial.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 16-08-2019 | Versión Inicial.
 *
 */
function verArchivoInspeccion(idServicio)
{
    var id_servicio = idServicio;
    var cantidadDocumentos = 1;
    var connDocumentos = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function (con, opt) {
                            Ext.MessageBox.show({
                                msg: 'Consultando documentos, Por favor espere!!',
                                progressText: 'Consultando...',
                                width: 300,
                                wait: true,
                                waitConfig: {interval: 200}
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

    connDocumentos.request({
        url: url_verifica_documentos,
        method: 'post',
        params: {
            idServicio: id_servicio
        },
        success: function (response) {
            var text = Ext.decode(response.responseText);
            cantidadDocumentos = text.total;

            if (cantidadDocumentos > 0)
            {
                var storeDocumentos = new Ext.data.Store
                ({
                    pageSize: 1000,
                    autoLoad: true,
                    proxy:
                        {
                            type: 'ajax',
                            url: url_verDocumentos,
                            reader:
                                {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                },
                            extraParams:
                                {
                                    idServicio: id_servicio,
                                    strNombreDocumento: 'Inspeccion Radio'
                                }
                        },
                    fields:
                        [
                            {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                            {name: 'feCreacion', mapping: 'feCreacion'},
                            {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                            {name: 'idDocumento', mapping: 'idDocumento'}
                        ]
                });

                Ext.define('Documentos',
                    {
                        extend: 'Ext.data.Model',
                        fields:
                            [
                                {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                                {name: 'feCreacion', mapping: 'feCreacion'},
                                {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                                {name: 'idDocumento', mapping: 'idDocumento'}
                            ]
                    });

                //grid de documentos
                gridDocumentos = Ext.create('Ext.grid.Panel',
                    {
                        id: 'gridMaterialesPunto',
                        store: storeDocumentos,
                        columnLines: true,
                        columns:
                            [
                                {
                                    header: 'Nombre Archivo',
                                    dataIndex: 'ubicacionLogica',
                                    width: 328
                                },
                                {
                                    header: 'Fecha de Carga',
                                    dataIndex: 'feCreacion',
                                    align: 'center',
                                    width: 100
                                },
                                {
                                    xtype: 'actioncolumn',
                                    header: 'Acciones',
                                    align: 'center',
                                    width: 70,
                                    items:
                                        [
                                            {
                                                iconCls: 'button-grid-show',
                                                tooltip: 'Ver Archivo Digital',
                                                handler: function (grid, rowIndex, colIndex) {
                                                    var rec = storeDocumentos.getAt(rowIndex);
                                                    verArchivoDigital(rec);
                                                }
                                            }
                                        ]
                                }
                            ],
                        viewConfig:
                            {
                                stripeRows: true,
                                enableTextSelection: true
                            },
                        frame: true,
                        height: 200
                    });

                function verArchivoDigital(rec) {
                    var rutaFisica = rec.get('linkVerDocumento');
                    var posicion = rutaFisica.indexOf('/public')
                    window.open(rutaFisica.substring(posicion,rutaFisica.length));
                }

                var formPanel = Ext.create('Ext.form.Panel',
                    {
                        width: 700,
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
                                            width: 510
                                        },
                                    items:
                                        [
                                            gridDocumentos
                                        ]
                                }
                            ],
                        buttons:
                            [{
                                text: 'Cerrar',
                                handler: function () {
                                    win.destroy();
                                }
                            }]
                    });

                var win = Ext.create('Ext.window.Window',
                    {
                        title: 'Documentos Cargados',
                        modal: true,
                        width: 550,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();

            } else
            {
                Ext.Msg.show({
                    title: 'Mensaje',
                    msg: 'El servicio seleccionado no posee archivos adjuntos.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                });
            }

        },
        failure: function (result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}

 /*
* Función que cierra la ventana modal, para los motivos de la eliminación.
*
* @author Josselhin Moreira <kjmoreira@telconet.ec>
* @version 1.0 07-03-2019 | Versión Inicial.
*/
function cierraVentanaMotivosEliminarServicio() {
    winMotivoEliminarServicio.close();
    winMotivoEliminarServicio.destroy();
}

/**
 * Documentación para el método 'ingresoCodigoPromociones'.
 *
 * Ejecuta Acción para llamar a Modal de ingreso de Códigos Promocionales.
 * 
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 29-10-2020
 * 
 * @param {int} idServicio identificador de servicio seleccionado
 * 
 * @since 1.0
 */
function ingresoCodigoPromociones(idServicio,idProducto, tipoProducto, tipoEnvio) {
   $("#intIdServicio").val(idServicio);
   $("#intIdProducto").val(idProducto);
   $("#strTipoProducto").val(tipoProducto);
   $("#codigoMens").val('');
   $("#codigoBW").val('');
   $('#checkPromoMix').prop('checked',false)
   if (tipoEnvio !== 'Grid')
   {
     $('#modalCodigoPromocion').modal({show: true});
     if (tipoProducto === 'producto')
     {
         $("#promocionMix").hide();
     }
     else
     {
         $("#promocionMix").show();
     }
   }
   $( "#btnGuardarCodigoPromocion" ).prop( "disabled", true );
   $("#codigoMens").attr('disabled','disabled');
   $("#codigoBW").attr('disabled','disabled');
   $( "#messageIdMens" ).remove();
   $( "#messageIdBw" ).remove();
   $("#divSuccesMens").removeClass();
   $("#divSuccesBw").removeClass();
   var srtExisteBw='';
   var srtExisteMens='';
   var srtValidacionGrid='S';
   
   if (tipoProducto === 'producto')
   {
        $("#div-mens").show();
        $("#div-bw").hide();
   }
   else if (tipoProducto === 'plan')
   {
        $("#div-mens").show();
        $("#div-bw").show();
   }
   
    var parametros = {  "intIdServicio"    : idServicio };
    
     $.ajax({
           data: parametros,
           url: urlValidaCaracExist,
           type: 'post',
           async: false,
           success: function (response) {
                srtExisteBw=response.srtExisteBw;
                srtExisteMens=response.strResponseMens;
  
               if (srtExisteMens === '0')
               {
                   if(tipoProducto === 'plan')
                   {
                       $("#checkPromoMix").attr('disabled','disabled');
                   }
                   $("#codigoMens").attr('disabled','disabled');
                   $("#codigoMens").val(response.strResponseValorMens);
                   $("#codigoMensVal").val('S');
               }
               else
               {
                   $("#codigoMens").removeAttr('disabled')
                   $("#codigoMens").val('');
               }
               
                 if (srtExisteBw === '0')
               {                        
                   $("#codigoBW").attr('disabled','disabled');
                   $("#codigoBW").val(response.srtExisteValorBw);
                   $("#codigoBWVal").val('S');
               }
               else
               {
                   $("#codigoBW").removeAttr('disabled')
                   $("#codigoBW").val('');
               }
               
               if (srtExisteMens === '0' && srtExisteBw === '0' && tipoProducto === 'plan')
               {
                   srtValidacionGrid = 'N';
               }
               
               if (srtExisteMens === '0' && tipoProducto === 'producto')
               {
                   srtValidacionGrid = 'N';
               }
               
                if (response.strMensaje !== '')
               {                        
                   $("#codigoMens").attr('disabled','disabled');
                   $("#codigoBW").attr('disabled','disabled');
                   $('#modalCodigoPromocion').modal('toggle');
                   $('#modalMensajes .modal-body').html('Existe un error: ' + response.strMensaje);
                   $('#modalMensajes').modal({show: true});
               }
                 
           },
           failure: function (response) {
               $('#modalCodigoPromocion').modal('toggle');
               $('#modalMensajes .modal-body').html('Existe un error: ' + response);
               $('#modalMensajes').modal({show: true});
           }
       });

     return    srtValidacionGrid;
}

    /**
     * Realiza una verificación que exista data en la caja de texto para el ingreso de códigos por mensualidad.
     *    
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 10-12-2020
     * @since 1.0
     */
    function controlValidaMix(strIdTipoPromo,strTipoPromo)
    {
        var strCodigoPromo = document.getElementById(strIdTipoPromo).value;
        var intCodigoPromo = strCodigoPromo.length;
        if (intCodigoPromo > 0) 
        {
            validaCodigoPromociones(strTipoPromo);
        }
    }

    /**
     * Realiza un bloqueo del botón guardar cuando se escribe en las cajas de texto para el ingreso de códigos
     * promocionales.
     *    
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 10-12-2020
     * @since 1.0
     */
    function controlGuardar(strIdTipoPromo,strTipoPromo)
    {
        var strCodigoPromo = document.getElementById(strIdTipoPromo).value;
        var intCodigoPromo = strCodigoPromo.length;
        if (intCodigoPromo > 0) 
        {
            $("#btnGuardarCodigoPromocion").prop("disabled", true);
        }
        else
        {
            validaCodigoPromociones(strTipoPromo);
        }
    }
    /**
     * Realiza la llamada a la función Ajax que valida los códigos promocionales por Mensualidad y Ancho de Banda.
     *    
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 29-10-2020
     * @since 1.0
     */
    function validaCodigoPromociones(strTipoPromo)
    {
        var promMens         = $("#codigoMens").val();
        var promBw           = $("#codigoBW").val();
        var intIdServicio    = $("#intIdServicio").val();
        var intIdProducto    = $("#intIdProducto").val();
        var strTipoProducto  = $("#strTipoProducto").val();
        var intIdPunto       = '';
        var intIdUltimaMilla = '';
        var strGrupoPromo    = '';
        var strCodigoPromo   = '';

        if (strTipoPromo === 'PROM_MPLA' && $('#checkPromoMix').prop('checked')) {
            strTipoPromo='PROM_MIX';
        }

        if (strTipoProducto === 'producto')
        {
            strTipoPromo='PROM_MPRO';
        }
   
        if(strTipoPromo === 'PROM_MPLA' || strTipoPromo === 'PROM_MPRO' || strTipoPromo === 'PROM_MIX')
        {
            strGrupoPromo='PROM_MENS';
        }
   
        if(strTipoPromo === 'PROM_BW')
        {
            strGrupoPromo='PROM_BW';
        }
   
        if(promMens !== '' && strGrupoPromo === 'PROM_MENS')
        {
             strCodigoPromo     = promMens;
             strCodigoPromo     = strCodigoPromo.trim();
             strCodigoPromo     = strCodigoPromo.toUpperCase();
             $("#codigoMens").val(strCodigoPromo);
        }
   
        if(promBw !== '' && strGrupoPromo === 'PROM_BW')
        {
             strCodigoPromo     = promBw;
             strCodigoPromo     = strCodigoPromo.trim();
             strCodigoPromo     = strCodigoPromo.toUpperCase();
            $("#codigoBW").val(strCodigoPromo);
        }
   

        var intCodigoPromo = strCodigoPromo.length;
        if (intCodigoPromo > 0) 
        {
            var parametros = {
                                "strGrupoPromocion": strGrupoPromo,
                                "strTipoPromocion" : strTipoPromo,
                                "strCodigo"        : strCodigoPromo,
                                "intIdServicio"    : intIdServicio,
                                "intIdPunto"       : intIdPunto,
                                "intIdPlan"        : intIdProducto,
                                "intIdProducto"    : intIdProducto,
                                "intIdUltimaMilla" : intIdUltimaMilla,
                                "strTipoProceso"   : 'EXISTENTE'
                            };

            $.ajax({
                data: parametros,
                url: urlCodigoPromocion,
                type: 'post',
                success: function (response) {
                    var strAplica  = response.strAplica;
                    var strMensaje = response.strMensaje;
                    if (strAplica === 'N')
                    {
                        if (strGrupoPromo === 'PROM_MENS')
                        {
                            $("#messageIdMens").remove();
                            $("#divSuccesMens").removeClass("text-success");
                            $("#strAplicaMens").val(strAplica);
                            $("#strPomocionMens").val('');
                            $("#strIdTipoMens").val('');
                            $("#divSuccesMens").addClass("text-danger");
                            $("#divSuccesMens").append( "<p id='messageIdMens'>"+strMensaje+"</p>" );
                        }
                        else
                        {
                            $("#messageIdBw").remove();
                            $("#divSuccesBw").removeClass("text-success");
                            $("#strAplicaBW").val(strAplica);
                            $("#strPomocionBw").val('');
                            $("#strIdTipoBw").val('');
                            $("#divSuccesBw").addClass("text-danger");
                            $("#divSuccesBw").append( "<p id='messageIdBw'>"+strMensaje+"</p>" );
                        }
                        $("#strServiciosMix").val('');
                        bloqueaBotonGuardar(strTipoPromo);
                    }
                    else
                    {
                        if (strGrupoPromo === 'PROM_MENS')
                        {
                            $("#messageIdMens").remove();
                            $("#divSuccesMens").removeClass("text-danger");
                            $("#strAplicaMens").val(strAplica);
                            $("#strPomocionMens").val(response.strNombrePromocion);
                            $("#strIdTipoMens").val(response.strIdTipoPromocion);
                            $("#divSuccesMens").addClass("text-success");
                            $("#divSuccesMens").append( "<p id='messageIdMens'>"+strMensaje+"</p>" );
                        }
                        else
                        {
                            $("#messageIdBw").remove();
                            $("#divSuccesBw").removeClass("text-danger");
                            $("#strAplicaBW").val(strAplica);
                            $("#strPromocionBw").val(response.strNombrePromocion);
                            $("#strIdTipoBw").val(response.strIdTipoPromocion);
                            $("#divSuccesBw").addClass("text-success");
                            $("#divSuccesBw").append( "<p id='messageIdBw'>"+strMensaje+"</p>" );
                        }
                        if (strTipoPromo === 'PROM_MIX')
                        {
                            $("#strServiciosMix").val(response.strServiciosMix);
                        }
                        bloqueaBotonGuardar(strTipoPromo);
                    }

                },
                failure: function (response) {
                    if (strGrupoPromo === 'PROM_MENS')
                    {
                        $("#messageIdMens").remove();
                        $("#divSuccesMens").removeClass("text-success");
                        $("#strAplicaMens").val(response.strAplica);
                        $("#strPomocionMens").val('');
                        $("#strIdTipoMens").val('');
                        $("#divSuccesMens").addClass("text-danger");
                        $("#divSuccesMens" ).append("<p id='messageIdMens'>Ocurrió un error al validar el código ingresado.</p>");
                    }
                    else
                    {
                        $("#messageIdBw").remove();
                        $("#divSuccesBw").removeClass("text-success");
                        $("#strAplicaBW").val(response.strAplica);
                        $("#strPomocionBw").val('');
                        $("#strIdTipoBw").val('');
                        $("#divSuccesBw").addClass("text-danger");
                        $("#divSuccesMens" ).append("<p id='messageIdMens'>Ocurrió un error al validar el código ingresado.</p>");
                    }
                    $("#strServiciosMix").val('');
                    bloqueaBotonGuardar(strTipoPromo);
                }
            });
        }
        else
        {
            if (strGrupoPromo === 'PROM_MENS')
            {
                $("#messageIdMens").remove();
                $("#codigoMens").val('');
                $("#strAplicaMens").val('');
                $("#strPomocionMens").val('');
                $("#strIdTipoMens").val('');
                $("#divSuccesMens").removeClass("text-success");
                $("#strServiciosMix").val('');
            }
            else
            {
                $("#messageIdBw").remove();
                $("#codigoBW").val('');
                $("#strAplicaBW").val('');
                $("#strPromocionBw").val('');
                $("#strIdTipoBw").val('');
                $("#divSuccesBw").removeClass("text-success");
            }
            bloqueaBotonGuardar(strTipoPromo);
        }
    }
 
    /**
    * Realiza validaciones para habilitar el botón guardar código promocional por tipo de promoción.
    *    
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 26-11-2020
    * @since 1.0
    */
    function bloqueaBotonGuardar(strTipoPromocion)
    {
        var strAplicaMens = $("#strAplicaMens").val();
        var strAplicaBW   = $("#strAplicaBW").val();
        var intAplicaMens = strAplicaMens.length;
        var intAplicaBW   = strAplicaBW.length;

        if (strTipoPromocion === 'PROM_MPRO')
        {
            if (strAplicaMens === 'S')
            {
                $("#btnGuardarCodigoPromocion").prop("disabled", false);
            }
            else
            {
                $("#btnGuardarCodigoPromocion").prop("disabled", true);
            }
        }
        else
        {
            if ((strAplicaMens === 'S' && strAplicaBW === 'S') || ((strAplicaMens === 'S' || strAplicaBW === 'S') && (intAplicaMens === 0 || intAplicaBW === 0)))
            {
                $("#btnGuardarCodigoPromocion").prop("disabled", false);
            }
            else
            {
                $("#btnGuardarCodigoPromocion").prop("disabled", true);
            }
        }
    }


    /**
    * Realiza la llamada a la función Ajax que valida los códigos promocionales por Mensualidad y Ancho de Banda.
    *    
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 29-10-2020
    * @since 1.0
    */
    function guardarCodigoPromociones()
    {
        var promMens         = $("#codigoMens").val();
        var strPomocionMens  = $("#strPomocionMens").val();
        var strIdTipoMens    = $("#strIdTipoMens").val();
        var promBw           = $("#codigoBW").val();
        var strPromocionBw   = $("#strPromocionBw").val();
        var strIdTipoBw      = $("#strIdTipoBw").val();
        var intIdServicio    = $("#intIdServicio").val();
        var intIdProducto    = $("#intIdProducto").val();
        var strServiciosMix  = $("#strServiciosMix").val();
        var strAplicaMens    = $("#strAplicaMens").val();
        var strAplicaBW      = $("#strAplicaBW").val();
        var intPromMens      = promMens.length;
        var intPromBw        = promBw.length;

        if ((intPromMens > 0 && strAplicaMens === 'S' )|| (intPromBw > 0) && strAplicaBW === 'S') 
        {
            var parametros       = {
                "strCodigoMens"    : promMens,
                "strPomocionMens"  : strPomocionMens,
                "strIdTipoMens"    : strIdTipoMens,
                "strCodigoBW"      : promBw,
                "strPromocionBw"   : strPromocionBw,
                "strIdTipoBw"      : strIdTipoBw,
                "intIdServicio"    : intIdServicio,
                "intIdPlan"        : intIdProducto,
                "strTipoProceso"   : 'EXISTENTE',
                "strServiciosMix"  : strServiciosMix
            };

            $.ajax({
                data: parametros,
                url: urlGuardarCodigoPromocion,
                type: 'post',
                success: function (response) {
                    var strPromMens=response.strPromMens;
                    var strPromBW=response.strPromBW;
                    if (strPromMens !== '' || strPromBW !== '')
                    {
                        $('#modalMensajes .modal-body').html('Se guardaron correctamente los códigos promocionales.');
                        $('#modalMensajes').modal({show: true});
                        location.reload();
                    }

                    if (response.strMensaje!=='')
                    {
                        $('#modalMensajes .modal-body').html('Ocurrió un error: ' + response.strMensaje );
                        $('#modalMensajes').modal({show: true});
                    }
                },
                failure: function (response) {
                    $('#modalMensajes .modal-body').html('Existe un error: ' + response);
                    $('#modalMensajes').modal({show: true});
                }
            });
        }
    }

/*
 * 
 * Función que anula simultáneamente las solicitudes asociadas al servicio gestionado
 */
function ejecutaGestionSimultanea(objInfoGestionSimultanea)
{
    Ext.MessageBox.wait("Gestionando solicitudes simultáneas...");
    Ext.Ajax.request({
        url: strUrlEjecutaSolsGestionSimultanea,
        method: 'post',
        timeout: 300000,
        params: objInfoGestionSimultanea,
        success: function(response){
            Ext.MessageBox.hide();
            var objData     = Ext.JSON.decode(response.responseText);
            anulacionPunto(objData.mensaje, objInfoGestionSimultanea.intIdPuntoAnulacion);
        },
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function validarSoloNumeros(me,e)
{
    var charCode = e.getKey();

    if (charCode >= 48 && charCode <= 57)
    {
        me.isValid();
    }
    else if (charCode === 8 || charCode === 46)
    {
        me.isValid();
    }
    else
    {
        e.stopEvent();
    }
}