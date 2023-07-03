
Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.require('Ext.tab.*');

var cliente;
var totalPuntos = 0;
var checkTotal  = false;

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    storeClientes = new Ext.data.Store({
        total    : 'total',
        pageSize :  400,
        proxy: {
            type : 'ajax',
            url  :  url_gridClientes,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idPunto'   , mapping: 'idPunto'},
                {name: 'idServicio', mapping: 'idServicio'},
                {name: 'login'     , mapping: 'login'},
                {name: 'estado'    , mapping: 'estado'},
                {name: 'oficina'   , mapping: 'nombreOficina'},
                {name: 'nombres'   , mapping: 'nombres'},
                {name: 'calculoIni', mapping: 'calculoIni'}
            ],
        autoLoad: false,
        listeners:
        {
            load: function(sender)
            {
                var boolExiste = (typeof sender.getProxy().getReader().rawData === 'undefined') ? false :
                                 (typeof sender.getProxy().getReader().rawData.total === 'undefined') ? false : true;
                if (boolExiste) {
                    totalPuntos = sender.getProxy().getReader().rawData.total;
                }
            }
        }
    });

    storeProductos = new Ext.data.Store({
        total: 'total',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: url_getServicios,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: ''
            }
        },
        fields:
            [
                {name: 'id_producto', mapping: 'id_producto'},
                {name: 'producto', mapping: 'producto'}
            ]
    });

    comboProductos = Ext.create('Ext.form.ComboBox', {
        id: 'cmb_productos',
        store: storeProductos,
        displayField: 'producto',
        valueField: 'id_producto',
        fieldLabel: 'Servicios',
        height: 30,
        width: 400,
        queryMode: "remote"
    });

    storeOficinas = new Ext.data.Store({
        total: 'total',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: url_getOficinas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: ''
            }
        },
        fields:
            [
                {name: 'id_oficina_grupo', mapping: 'id_oficina_grupo'},
                {name: 'nombre_oficina', mapping: 'nombre_oficina'}
            ]
    });

    comboOficinas = Ext.create('Ext.form.ComboBox', {
        id: 'cmb_oficinas',
        store: storeOficinas,
        displayField: 'nombre_oficina',
        valueField: 'id_oficina_grupo',
        fieldLabel: 'Oficina Cobertura',
        height: 30,
        width: 400,
        queryMode: "remote"
    });

    Ext.define('My.selection.CheckboxModel', {
        extend: 'Ext.selection.CheckboxModel',
        onHeaderClick: function (headerCt, header, e) {
            if (header.isCheckerHd) {
                e.stopEvent();
                var me = this, isChecked = header.el.hasCls(Ext.baseCSSPrefix + 'grid-hd-checker-on');
                me.preventFocus = true;
                if (isChecked) {
                    me.deselectAll();
                    me.fireEvent('deselectall', me);
                } else {
                    me.selectAll();
                    me.fireEvent('selectall', me);
                }
                delete me.preventFocus;
            }
        }
    });

    sm = Ext.create('My.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
            selectall : function () {checkTotal = true;},
            deselectall: function () {checkTotal = false;}
        }
    });

    grid = Ext.create('Ext.grid.Panel', {
        width: 1000,
        height: 300,
        store: storeClientes,
        selModel: sm,
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {emptyText: 'No hay datos para mostrar'},
        loadMask: true,
        frame: false,
        dockedItems:
            [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '<-',
                    items:
                        [
                            {
                                iconCls: 'icon_calculador',
                                text: 'Calcular SLA',
                                scope: this,
                                handler: function() {
                                    var registros = storeClientes.getAt(0);
                                    calcularSla(registros.data.calculoIni);
                                }
                            }
                        ]
                }
            ],
        columns:
            [
                {
                    header   : '<i align="center" class="fa fa-hashtag" aria-hidden="true"></i>',
                    xtype    : 'rownumberer',
                    align    : 'center',
                    width    :  31,
                    sortable :  false,
                    hideable :  false
                },
                {
                    id: 'idPunto',
                    header: 'idPunto',
                    dataIndex: 'idPunto',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'idServicio',
                    header: 'idServicio',
                    dataIndex: 'idServicio',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'login',
                    header: 'Login Pto. Sucursal',
                    dataIndex: 'login',
                    width: 200,
                    sortable: true
                },
                {
                    id: 'nombres',
                    header: 'Nombre/Razon Social',
                    dataIndex: 'nombres',
                    width: 320,
                    sortable: true
                },
                {
                    id: 'oficina',
                    header: 'Oficina Pto. Sucursal',
                    dataIndex: 'oficina',
                    width: 200,
                    sortable: true
                },
                {
                    id: 'estado',
                    header: 'Estado',
                    dataIndex: 'estado',
                    width: 100
                },
                {
                    header: 'Acciones',
                    xtype: 'actioncolumn',
                    width: 100,
                    sortable: false,
                    items:
                        [
                            {
                                getClass: function(v, meta, rec) {
                                    return 'button-grid-show';
                                },
                                tooltip: 'Ver Servicios Afectados',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storeClientes.getAt(rowIndex);
                                    verServiciosAfectados(rec.data.idServicio, rec.data.idPunto);
                                }

                            }
                        ]
                }
            ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeClientes,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });

    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders        
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 1000,
        title: 'Criterios de busqueda',
        buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function() {
                        buscar();
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
            [
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype: 'textfield',
                    id: 'txtRazonSocial',
                    fieldLabel: 'Razón Social',
                    value: '',
                    width: 400
                },
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype : 'textfield',
                    id : 'txtIdentificacion',
                    fieldLabel: 'Identificación',
                    value: '',
                    width: 400
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype: 'textfield',
                    id: 'txtNombres',
                    fieldLabel: 'Nombres',
                    value: '',
                    width: 400
                },
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype: 'textfield',
                    id: 'txtApellidos',
                    fieldLabel: 'Apellidos',
                    value: '',
                    width: 400
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 50},
                comboProductos,
                {html: "&nbsp;", border: false, width: 50},
                comboOficinas,
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Estado Punto',
                    id: 'sltEstado',
                    value: 'Todos',
                    store: [
                        ['Todos', 'Todos'],
                        ['Activo', 'Activo'],
                        ['In-Corte', 'In-Corte'],
                        ['Cancelado', 'Cancelado']
                    ],
                    width: 400
                }
            ],
        renderTo: 'filtro'
    });
       
});

function buscar()
{
    razonSocial    = Ext.getCmp('txtRazonSocial').value;
    nombres        = Ext.getCmp('txtNombres').value;
    apellidos      = Ext.getCmp('txtApellidos').value;
    identificacion = Ext.getCmp('txtIdentificacion').value;
    estado         = Ext.getCmp('sltEstado').value;
    producto       = Ext.getCmp('cmb_productos').value;
    oficina        = Ext.getCmp('cmb_oficinas').value;

    if (razonSocial === "" && nombres === "" && apellidos === "" && identificacion === "")
    {
        Ext.Msg.alert('Alerta ',"Debe escribir la Razón Social, Identificación, Nombres o Apellidos del Cliente.");
        return false;
    }
    
    storeClientes.removeAll();
    storeClientes.getProxy().extraParams.razonSocial    = razonSocial;
    storeClientes.getProxy().extraParams.nombres        = nombres;
    storeClientes.getProxy().extraParams.apellidos      = apellidos;
    storeClientes.getProxy().extraParams.identificacion = identificacion;
    storeClientes.getProxy().extraParams.estado         = estado;
    storeClientes.getProxy().extraParams.producto       = producto;
    storeClientes.getProxy().extraParams.oficina        = oficina;
    storeClientes.load();
}

function limpiar()
{
    Ext.getCmp('txtRazonSocial').value = "";
    Ext.getCmp('txtNombres').value     = "";
    Ext.getCmp('txtApellidos').value   = "";
    Ext.getCmp('sltEstado').value      = "Todos";
    Ext.getCmp('cmb_productos').value  = "";
    Ext.getCmp('cmb_oficinas').value   = "";
    Ext.getCmp('txtRazonSocial').setRawValue("");
    Ext.getCmp('txtNombres').setRawValue("");
    Ext.getCmp('txtApellidos').setRawValue("");
    Ext.getCmp('sltEstado').setRawValue("Todos");
    Ext.getCmp('cmb_productos').setRawValue("");
    Ext.getCmp('cmb_oficinas').setRawValue("");    
    Ext.getCmp('txtIdentificacion').value = "";
    Ext.getCmp('txtIdentificacion').setRawValue("");
    
    storeClientes.removeAll();
    storeClientes.getProxy().extraParams.razonSocial  = "";
    storeClientes.getProxy().extraParams.nombres      = "";
    storeClientes.getProxy().extraParams.apellidos    = "";
    storeClientes.getProxy().extraParams.estado       = "";
    storeClientes.getProxy().extraParams.producto     = "";
    storeClientes.getProxy().extraParams.oficina      = "";
    
    grid.getStore().removeAll(); 
}

function verServiciosAfectados(idServicio,idPunto)
{    
    btncancelar2 = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {
			winServicioAfectados.destroy();
		}
    });
    
    storeServiciosAfectados = new Ext.data.Store({ 
		total: 'total',
		autoLoad:true,
		proxy: {
			type: 'ajax',			
			url:url_getServAfectados,
			reader: {
			    type: 'json',
			    totalProperty: 'total',
			    root: 'encontrados'
			},
			extraParams: {
			    idPunto: idPunto,
                idServicio: idServicio
                
			}
		},
		fields:
		[
			{name:'idServicioAfectado', mapping:'idServicio'},
	        {name:'nombreProducto', mapping:'nombreProducto'},
            {name:'estadoAfectado', mapping:'estado'}
		]        
	});
    
    gridServiciosAfectados = Ext.create('Ext.grid.Panel', {
        id: 'gridServiciosAfectados',
        store: storeServiciosAfectados,
        columnLines: true,
        viewConfig: {emptyText: 'Servicios In-Corte o Cancelados'},
        columns: [
            {
                id: 'idServicioAfectado',
                header: 'idServicio',
                dataIndex: 'idServicioAfectado',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreProducto',
                header: 'Producto/Servicio',
                dataIndex: 'nombreProducto',
                width: 230,
                sortable: true
            },
            {
                id: 'estadoAfectado',
                header: 'Estado',
                dataIndex: 'estadoAfectado',
                width: 90,
                sortable: true
            }           
        ],
        width: 300,
        height: 280
    });
    
    formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 280,
		width: 350,
		layout: 'fit',
		fieldDefaults: {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		items: 
		[
            gridServiciosAfectados
		]
	 });  
    
    winServicioAfectados = Ext.create('Ext.window.Window', {
		title: 'Servicios Afectados',
		modal: true,
		width: 350,
		height: 220,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btncancelar2]
    }).show(); 
}

function calcularSla(calculoInicial)
{
    var param         = '';
    var parametrosSLA = 'S';

    if (sm.getSelection().length > 0)
    {
        cliente = sm.getSelection()[0].data.nombres;

        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            idPunto = sm.getSelection()[i].data.idPunto;
            idServicio = sm.getSelection()[i].data.idServicio === null ? 0 : sm.getSelection()[i].data.idServicio;

            param = param + idPunto + "-" + idServicio;
            if (i < (sm.getSelection().length - 1))
            {
                param = param + '|';
            }
        }

        if(sm.getSelection().length <= 500)
        {
            parametrosSLA = param;
        }

        btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winCalculoSla.destroy();
            }
        });

        var string_html = "<table width='100%' border='0' >";
        string_html += "    <tr>";
        string_html += "        <td colspan='6'>";
        string_html = "			<table width='100%' border='0' >";
        string_html += "                <tr style='height:150px'>";
        string_html += "                    <td colspan='4'><div id='filtroSla'></div></td>";
        string_html += "                </tr>";
        string_html += "                <tr style='height:270px'>\n\
                                              <td>\n\
                                                  <div id='sla-tabs-resultado'>\n\
                                                  </div>\n\
                                              </td>   ";
        string_html += "                </tr>";
        string_html += "            </table>";
        string_html += "        </td>";
        string_html += "    </tr>";
        string_html += "</table>";

        secciones = Ext.create('Ext.Component', {
            html: string_html,
            padding: 1,
            layout: 'anchor',
            style: {border: '0'}
        });


        formPanelSla = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            height: 800,
            width: 500,
            layout: 'fit',
            fieldDefaults: {
                labelAlign: 'left',
                msgTarget: 'side'
            },
            items:
                [
                    secciones
                ]
        });

        //Se realiza el calculo del SLA con los clientes seleccionados
        winCalculoSla = Ext.create('Ext.window.Window', {
            title: 'Cálculo de SLA',
            modal: true,
            width: 1100,
            height: 500,
            resizable: false,
            layout: 'fit',
            items: [formPanelSla],
            buttonAlign: 'center',
            buttons: [btncancelar2]
        }).show();

        //Filtros para calculo de Sla
        filterPanel = Ext.create('Ext.panel.Panel', {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {type: 'table',
                columns: 5,
                align: 'left'
            },
            bodyStyle: {
                background: '#fff'
            },
            buttons:
                [
                    {
                        text: 'Calcular',
                        iconCls: "icon_calculador",
                        handler: function() {
                            //Calcular SLa mostrar tickets con incidencia                                                         
                            if(validador())
                            {
                                rangoDesde= Ext.getCmp('feSolicitadaDesde').value;
                                rangoHasta= Ext.getCmp('feSolicitadaHasta').value; 
                                
                                storeCasosPorPunto.getProxy().extraParams.rangoDesde = rangoDesde;
                                storeCasosPorPunto.getProxy().extraParams.rangoHasta = rangoHasta;
                                storeCasosPorPunto.getProxy().extraParams.params     = parametrosSLA+"|";
                                storeCasosPorPunto.getProxy().extraParams.razonSocial= razonSocial;
                                storeCasosPorPunto.getProxy().extraParams.nombres    = nombres;
                                storeCasosPorPunto.getProxy().extraParams.apellidos  = apellidos;
                                storeCasosPorPunto.getProxy().extraParams.estado     = estado;
                                storeCasosPorPunto.getProxy().extraParams.producto   = producto;
                                storeCasosPorPunto.getProxy().extraParams.oficina    = oficina;
                                storeCasosPorPunto.getProxy().extraParams.calculoIni = calculoInicial;
                                storeCasosPorPunto.getProxy().extraParams.calculoFin = sm.getSelection().length;
                                storeCasosPorPunto.getProxy().extraParams.accion     = "casos";
                                storeCasosPorPunto.load();
                                
                                storeDisponibilidadPunto.getProxy().extraParams.rangoDesde = rangoDesde;
                                storeDisponibilidadPunto.getProxy().extraParams.rangoHasta = rangoHasta;
                                storeDisponibilidadPunto.getProxy().extraParams.params     = parametrosSLA+"|";
                                storeDisponibilidadPunto.getProxy().extraParams.razonSocial= razonSocial;
                                storeDisponibilidadPunto.getProxy().extraParams.nombres    = nombres;
                                storeDisponibilidadPunto.getProxy().extraParams.apellidos  = apellidos;
                                storeDisponibilidadPunto.getProxy().extraParams.estado     = estado;
                                storeDisponibilidadPunto.getProxy().extraParams.producto   = producto;
                                storeDisponibilidadPunto.getProxy().extraParams.oficina    = oficina;
                                storeDisponibilidadPunto.getProxy().extraParams.calculoIni = calculoInicial;
                                storeDisponibilidadPunto.getProxy().extraParams.calculoFin = sm.getSelection().length;
                                storeDisponibilidadPunto.getProxy().extraParams.accion     = "disponibilidad";
                                storeDisponibilidadPunto.load();
                                
                                storeResumenDisponibilidad.getProxy().extraParams.rangoDesde = rangoDesde;
                                storeResumenDisponibilidad.getProxy().extraParams.rangoHasta = rangoHasta;
                                storeResumenDisponibilidad.getProxy().extraParams.params     = parametrosSLA+"|";
                                storeResumenDisponibilidad.getProxy().extraParams.razonSocial= razonSocial;
                                storeResumenDisponibilidad.getProxy().extraParams.nombres    = nombres;
                                storeResumenDisponibilidad.getProxy().extraParams.apellidos  = apellidos;
                                storeResumenDisponibilidad.getProxy().extraParams.estado     = estado;
                                storeResumenDisponibilidad.getProxy().extraParams.producto   = producto;
                                storeResumenDisponibilidad.getProxy().extraParams.oficina    = oficina;
                                storeResumenDisponibilidad.getProxy().extraParams.calculoIni = calculoInicial;
                                storeResumenDisponibilidad.getProxy().extraParams.calculoFin = sm.getSelection().length;
                                storeResumenDisponibilidad.getProxy().extraParams.accion     = "resumen";
                                storeResumenDisponibilidad.load();
                            }
                        }
                    },
                    {
                        text: 'Descargar SLA Consolidado',
                        iconCls: "icon_exportar",
                        handler: function() {
                            if (validador())
                            {
                                descargarSla('consolidado');
                            }
                        }
                    },
                    {
                        text: 'Descargar SLA Detallado',
                        iconCls: "icon_exportar",
                        handler: function() {
                            if (validador())
                            {
                                descargarSla('detallado');
                            }
                        }
                    }
                ],
            items:
                [
                    {html: "&nbsp;", border: false, width: 50},
                    {
                        xtype: 'fieldcontainer',
                        fieldLabel: 'Rango de fechas',
                        items: [
                            {
                                xtype: 'datefield',
                                width: 290,
                                id: 'feSolicitadaDesde',
                                name: 'feSolicitadaDesde',
                                fieldLabel: 'Desde:',
                                format: 'Y-m-d',
                                editable: false
                            },
                            {
                                xtype: 'datefield',
                                width: 290,
                                id: 'feSolicitadaHasta',
                                name: 'feSolicitadaHasta',
                                fieldLabel: 'Hasta:',
                                format: 'Y-m-d',
                                editable: false
                            }
                        ]
                    },
                    {html: "&nbsp;", border: false, width: 50},
                    {
                        xtype: 'fieldcontainer',
                        defaultType: 'checkboxfield',
                        items:
                            [
                                {
                                    boxLabel: 'Ver Version Oficial en Reporte',
                                    name: 'checkbox',
                                    inputValue: 'N',
                                    id: 'checkbox'
                                },
                                {
                                    id         : 'generacionTotal',
                                    name       : 'generacionTotal',
                                    boxLabel   : '<b>Descargar la cantidad total de '+totalPuntos+' punto(s)</b>',
                                    inputValue : 'N',
                                    checked    :  checkTotal
                                }
                            ]
                    }
                ],
            renderTo: 'filtroSla'
        });

        storeCasosPorPunto = new Ext.data.Store({
            total: 'total',
            pageSize: 15,
            autoLoad: false,
            proxy: {
                type: 'ajax',
                timeout: 1200000,
                url: url_infoSla,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'idCaso', mapping: 'idCaso'},
                    {name: 'numeroCaso', mapping: 'numeroCaso'},
                    {name: 'fechaIncidencia', mapping: 'fechaIncidencia'},
                    {name: 'tituloInicial', mapping: 'tituloInicial'},
                    {name: 'loginCaso', mapping: 'loginCaso'},
                    {name: 'estadoCaso', mapping: 'estadoCaso'},
                    {name: 'tiempoSolucion', mapping: 'tiempoSolucion'},
                    {name: 'servicioAfectado', mapping: 'servicioAfectado'},
                    {name: 'nombrePunto', mapping: 'nombrePunto'},
                    {name: 'disponibilidad', mapping: 'disponibilidad'}
                ]
        });

        storeDisponibilidadPunto = new Ext.data.Store({
            total: 'total',
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                timeout: 1200000,
                url: url_infoSla,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'puntoDisponibilidad', mapping: 'puntoDisponibilidad'},
                    {name: 'loginDisponibilidad', mapping: 'loginDisponibilidad'},
                    {name: 'porcentajeDisponibilidad', mapping: 'porcentajeDisponibilidad'},
                    {name: 'minutosTotalDisponibilidad', mapping: 'minutosTotalDisponibilidad'}
                ]
        });

        storeResumenDisponibilidad = new Ext.data.Store({
            total: 'total',
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                timeout: 1200000,
                url: url_infoSla,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'rango', mapping: 'rango'},
                    {name: 'tiempo', mapping: 'tiempo'},
                    {name: 'totalPuntos', mapping: 'totalPuntos'},
                    {name: 'perdida', mapping: 'perdida'},
                    {name: 'disponibilidad', mapping: 'disponibilidad'}
                ]
        });

        gridCasosPorPunto = Ext.create('Ext.grid.Panel', {
            id: 'gridCasosPorPunto',
            store: storeCasosPorPunto,
            columnLines: true,
            title: 'Listado de Casos por Punto',
            viewConfig: {emptyText: 'No tiene casos relacionados en el rango de fechas'},
            columns: [
                {
                    id: 'numeroCaso',
                    header: 'Numero Caso',
                    dataIndex: 'numeroCaso',
                    width: 90,
                    sortable: true
                },
                {
                    id: 'fechaIncidencia',
                    header: 'Fecha Incidencia',
                    dataIndex: 'fechaIncidencia',
                    width: 120,
                    sortable: true
                },
                {
                    id: 'nombrePunto',
                    header: 'Nombre/Razon Social',
                    dataIndex: 'nombrePunto',
                    width: 230,
                    sortable: true
                },
                {
                    id: 'tituloInicial',
                    header: 'Version Inicial',
                    dataIndex: 'tituloInicial',
                    width: 300,
                    sortable: true
                },
                {
                    id: 'loginCaso',
                    header: 'Login',
                    dataIndex: 'loginCaso',
                    width: 190,
                    sortable: true
                },
                {
                    id: 'tiempoSolucion',
                    header: 'Tiempo',
                    dataIndex: 'tiempoSolucion',
                    width: 50,
                    align: 'right',
                    sortable: true
                },
                {
                    id: 'estadoCaso',
                    header: 'Estado Caso',
                    dataIndex: 'estadoCaso',
                    width: 90,
                    sortable: true
                }
            ],
            width: 975,
            height: 250,
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeCasosPorPunto,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            listeners: {
                itemdblclick: function(view, record, item, index, eventobj, obj) {
                    var position = view.getPositionByEvent(eventobj),
                        data = record.data,
                        value = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show({
                        title: 'Copiar texto?',
                        msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFORMATION
                    });
                },
                viewready: function(grid) {
                    var view = grid.view;

                    // record the current cellIndex
                    grid.mon(view, {
                        uievent: function(type, view, cell, recordIndex, cellIndex, e) {
                            grid.cellIndex = cellIndex;
                            grid.recordIndex = recordIndex;
                        }
                    });

                    grid.tip = Ext.create('Ext.tip.ToolTip', {
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        renderTo: Ext.getBody(),
                        listeners: {
                            beforeshow: function updateTipBody(tip) {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                }
                            }
                        }
                    });

                }
            }
        });

        gridDisponibilidadPunto = Ext.create('Ext.grid.Panel', {
            id: 'gridDisponibilidadPunto',
            store: storeDisponibilidadPunto,
            columnLines: true,
            title: 'Listado de Disponibilidad',
            viewConfig: {emptyText: 'No tiene casos relacionados en el rango de fechas'},
            columns: [
                {
                    id: 'puntoDisponibilidad',
                    header: 'Nombre/Razon Social',
                    dataIndex: 'puntoDisponibilidad',
                    width: 230,
                    sortable: true
                },
                {
                    id: 'loginDisponibilidad',
                    header: 'Login Pto. Sucursal',
                    dataIndex: 'loginDisponibilidad',
                    width: 200,
                    sortable: true
                },
                {
                    id: 'porcentajeDisponibilidad',
                    header: 'Porcentaje de Disponibilidad',
                    dataIndex: 'porcentajeDisponibilidad',
                    align: 'right',
                    width: 150,
                    sortable: true
                },
                {
                    id: 'minutosTotalDisponibilidad',
                    header: 'Minutos Total de Casos',
                    dataIndex: 'minutosTotalDisponibilidad',
                    align: 'right',
                    width: 150,
                    sortable: true
                }
            ],
            width: 975,
            height: 250,
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeDisponibilidadPunto,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            })
        });

        gridResumenPorcentajes = Ext.create('Ext.grid.Panel', {
            id: 'gridResumenPorcentajes',
            store: storeResumenDisponibilidad,
            columnLines: true,
            title: 'Resumen Porcentaje y Tiempos Totales',
            viewConfig: {emptyText: 'No tiene casos relacionados en el rango de fechas'},
            columns: [
                {
                    id: 'rango',
                    header: 'Tiempo de Rango de Fechas (Min.)',
                    dataIndex: 'rango',
                    align: 'right',
                    width: 200,
                    sortable: true
                },
                {
                    id: 'tiempo',
                    header: 'Tiempo Total de Casos',
                    dataIndex: 'tiempo',
                    align: 'right',
                    width: 150,
                    sortable: true
                },
                {
                    id: 'perdida',
                    header: 'Porcentaje de tiempos de Casos',
                    dataIndex: 'perdida',
                    align: 'right',
                    width: 200,
                    sortable: true
                },
                {
                    id: 'disponibilidad',
                    header: 'Porcentaje de Disponibilidad Clientes',
                    dataIndex: 'disponibilidad',
                    align: 'right',
                    width: 200,
                    sortable: true
                },
                {
                    id: 'totalPuntos',
                    header: 'Numero de Puntos',
                    dataIndex: 'totalPuntos',
                    align: 'right',
                    width: 120,
                    sortable: true
                }
            ],
            width: 975,
            height: 250
        });

        new Ext.TabPanel({
            height: 250,
            renderTo: 'sla-tabs-resultado',
            activeTab: 0,
            plain: true,
            autoRender: true,
            autoShow: true,
            items: [
                gridCasosPorPunto,
                gridDisponibilidadPunto,
                gridResumenPorcentajes
            ]
        });


    }
    else
    {
        Ext.Msg.alert('Alerta', 'Debe escoger al menos un punto para el cálculo del Sla');
    }
}

function validador()
{
    rangoDesde = Ext.getCmp('feSolicitadaDesde').value;
    rangoHasta = Ext.getCmp('feSolicitadaHasta').value;

    if (isNaN(rangoDesde) || isNaN(rangoHasta))
    {
        Ext.Msg.alert('Alerta', 'Debe escoger el rango de fechas completo a calcular el Sla');
        return false;
    }

    return true;
}

function descargarSla(tipo)
{
    var param          = '';
    var parametrosSLA  = 'S';
    var registros      = storeClientes.getAt(0);
    var calculoInicial = registros.data.calculoIni;

    if (sm.getSelection().length < 1) {
        Ext.Msg.alert('Alerta', 'Debe seleccionar al menos un punto para el cálculo del Sla.');
        return;
    }

    for (var i = 0; i < sm.getSelection().length; ++i)
    {
        idPunto = sm.getSelection()[i].data.idPunto;
        idServicio = sm.getSelection()[i].data.idServicio === null ? 0 : sm.getSelection()[i].data.idServicio;

        param = param + idPunto + "-" + idServicio;

        if (i < (sm.getSelection().length - 1))
        {
            param = param + '|';
        }

        if(sm.getSelection().length <= 500)
        {
            parametrosSLA = param;
        }
    }

    rangoDesde = Ext.getCmp('feSolicitadaDesde').value;
    rangoHasta = Ext.getCmp('feSolicitadaHasta').value;
    rangoDesde = convert(rangoDesde);
    rangoHasta = convert(rangoHasta);

    $.ajax({
        type : "POST",
        url  : url_reporteSla,
        data : {
            'rangoDesde'     : rangoDesde,
            'rangoHasta'     : rangoHasta,
            'cliente'        : cliente,
            'versionOficial' : Ext.getCmp('checkbox').checked,
            'generacionTotal': Ext.getCmp('generacionTotal').checked,
            'params'         : parametrosSLA,
            'tipo'           : tipo,
            'razonSocial'    : razonSocial,
            'nombres'        : nombres,
            'apellidos'      : apellidos,
            'identificacion' : identificacion,
            'estado'         : estado,
            'producto'       : producto,
            'oficina'        : oficina,
            'calculoIni'     : calculoInicial,
            'calculoFin'     : sm.getSelection().length
        },
        beforeSend: function() {
            Ext.MessageBox.show({
                msg   :'Ejecutando Reporte..',
                width : 200,
                wait  : true,
                waitConfig: {interval: 200}
            });
        },
        success: function(data) {
            Ext.MessageBox.show({
                title     : data.status ? 'Mensaje' : 'Error',
                msg       : data.message,
                icon      : data.status ? Ext.MessageBox.INFO : Ext.MessageBox.ERROR,
                closable  : false,
                multiline : false,
                buttons   : Ext.MessageBox.OK,
                buttonText: { ok:'Cerrar'}
            });
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            Ext.Msg.alert('Error', "status: " + textStatus + ', Error: ' + errorThrown);
        }
    });

    function convert(str)
    {
        var date = new Date(str),mnth = ("0" + (date.getMonth() + 1)).slice(-2),day  = ("0" + date.getDate()).slice(-2);

        return [date.getFullYear(), mnth, day].join("-");
    }

}
