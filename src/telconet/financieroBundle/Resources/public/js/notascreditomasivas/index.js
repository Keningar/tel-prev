Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
Ext.require('Ext.chart.*');
Ext.require('Ext.layout.container.Fit');
Ext.onReady(function () {

    /*Inicio: Filtros Financiero*/
    var dateFechaDesde = new Ext.form.DateField({
        id: 'dateFechaDesde',
        fieldLabel: 'Fecha Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -1),
        format: 'd-m-Y',
        width: 325
    });
    var dateFechaHasta = new Ext.form.DateField({
        id: 'dateFechaHasta',
        fieldLabel: 'Fecha Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'd-m-Y',
        width: 325
    });

    var txtRangoFacturaDesde = Ext.create('Ext.form.Text',
        {
            id: 'txtRangoFacturaDesde',
            name: 'txtRangoFacturaDesde',
            fieldLabel: '# Factura Desde',
            labelAlign: 'left',
            allowBlank: true,
            width: 325,
            maskRe: /[0-9_]/i,
            regex: /^[0-9_]+$/,
            regexText: 'Solo numeros'
        });

    var txtRangoFacturaHasta = Ext.create('Ext.form.Text',
        {
            id: 'txtRangoFacturaHasta',
            name: 'txtRangoFacturaHasta',
            fieldLabel: '# Factura Hasta',
            labelAlign: 'left',
            allowBlank: true,
            width: 325,
            maskRe: /[0-9_]/i,
            regex: /^[0-9_]+$/,
            regexText: 'Solo numeros'
        });

    //Modelo Oficina
    Ext.define('modelOficinas', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdOficina', type: 'int'},
            {name: 'strNombreOficina', type: 'string'}
        ]
    });

    //Store Oficina
    var storeOficina = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelOficinas",
        proxy: {
            type: 'ajax',
            url: urlGetOficinasByPrefijoEmpresa,
            reader: {
                type: 'json',
                root: 'objDatos'
            }
        }
    });

    //Combo Oficina
    var cbxOficina = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeOficina,
        labelAlign: 'left',
        name: 'cbxOficina',
        id: 'idCbxOficina',
        valueField: 'intIdOficina',
        displayField: 'strNombreOficina',
        fieldLabel: 'Oficina',
        width: 325,
        triggerAction: 'all',
        queryMode: 'local',
        allowBlank: true
    });

    //Modelo Estados
    Ext.define('modelEstados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strEstadoDocumento', type: 'string'}
        ]
    });

    //Store Estados
    var storeEstados = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelEstados",
        proxy: {
            type: 'ajax',
            url: urlGetEstadosDocumentos,
            reader: {
                type: 'json',
                root: 'jsonEstadosByTipoDocumento'
            }
        }
    });

    //Combo Estados
    var cbxEstados = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeEstados,
        labelAlign: 'left',
        name: 'cbxEstados',
        id: 'idCbxEstados',
        valueField: 'strEstadoDocumento',
        displayField: 'strEstadoDocumento',
        fieldLabel: 'Estado',
        width: 325,
        triggerAction: 'all',
        queryMode: 'local',
        allowBlank: true
    });

    //Modelo Productos
    Ext.define('modelProductos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdProducto', type: 'integer'},
            {name: 'strDescripcionProdcuto', type: 'string'}
        ]
    });

    //Store Productos
    var storeProductos = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelProductos",
        proxy: {
            type: 'ajax',
            url: urlGetProductos,
            reader: {
                type: 'json',
                root: 'jsonProductos'
            }
        }
    });

    //Cbo Productos
    var cbxProducto = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeProductos,
        labelAlign: 'left',
        id: 'idCbxProducto',
        name: 'cbxProducto',
        valueField: 'intIdProducto',
        displayField: 'strDescripcionProdcuto',
        fieldLabel: 'Producto',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true
    });

    //Modelo Planes
    Ext.define('modelPlanes', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdPlan', type: 'integer'},
            {name: 'strNombrePlan', type: 'string'}
        ]
    });

    //Store Planes
    var storePlanes = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelPlanes",
        proxy: {
            type: 'ajax',
            url: urlGetPlanes,
            reader: {
                type: 'json',
                root: 'jsonPlanes'
            }
        }
    });

    //Cbo Planes
    var cbxPlan = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storePlanes,
        labelAlign: 'left',
        id: 'idCbxPlanes',
        name: 'cbxPlanes',
        valueField: 'intIdPlan',
        displayField: 'strNombrePlan',
        fieldLabel: 'Plan',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true
    });
    /*Fin: Filtros Financieros*/

    /*Inicio: Filtros Comercial*/
    //Fecha de Solicitud
    var dateFechaSolicitudDesde = new Ext.form.DateField({
        id: 'dateFechaSolicitudDesde',
        fieldLabel: 'Fecha Solicitud',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    //Field Login
    var txtLogin = Ext.create('Ext.form.Text',
        {
            id: 'txtLogin',
            name: 'txtLogin',
            fieldLabel: 'Login',
            labelAlign: 'left',
            allowBlank: true,
            width: 325
        });

    //Modelo modelFormaPago
    Ext.define('modelFormaPago', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idFormaPago', type: 'int'},
            {name: 'descripcionFormaPago', type: 'string'}
        ]
    });

    //Store modelFormaPago
    var storeFormaPago = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelFormaPago",
        proxy: {
            type: 'ajax',
            url: urlGetAdmiFormaPago,
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        }
    });

    //Combo modelFormaPago
    var cbxFormaPago = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeFormaPago,
        labelAlign: 'left',
        name: 'cbxFormaPago',
        id: 'idCbxFormaPago',
        valueField: 'idFormaPago',
        displayField: 'descripcionFormaPago',
        fieldLabel: 'Forma Pago',
        width: 325,
        triggerAction: 'all',
        queryMode: 'local',
        allowBlank: true
    });

    //model Tipo Cuenta
    Ext.define('modelTipoCuenta', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdAdmiTipoCUenta', type: 'int'},
            {name: 'strDescCuenta', type: 'string'}
        ]
    });

    //Store Tipo Cuenta
    var storeTipoCuenta = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelTipoCuenta",
        proxy: {
            type: 'ajax',
            url: urlGetTipoCuenta,
            reader: {
                type: 'json',
                root: 'jsonTipoCuenta'
            }
        }
    });

    //Combo Tipo Cuenta
    var cbxTipoCuenta = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeTipoCuenta,
        labelAlign: 'left',
        id: 'idCbxTipoCuenta',
        name: 'cbxTipoCuenta',
        valueField: 'intIdAdmiTipoCUenta',
        displayField: 'strDescCuenta',
        fieldLabel: 'Tipo Cuenta',
        width: 325,
        triggerAction: 'all',
        queryMode: 'local',
        allowBlank: true
    });

    //model Tipo Solicitud
    Ext.define('modelTipoSolicitud', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdTipoSolcitud', type: 'int'},
            {name: 'strDescTipoSolicitud', type: 'string'}
        ]
    });

    //Store TipoSolicitud
    var storeTipoSolicitud = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelTipoSolicitud",
        proxy: {
            type: 'ajax',
            url: urlGetTipoSolicitud,
            reader: {
                type: 'json',
                root: 'jsonTipoSolicitudes'
            }
        }
    });
    //Combo Tipo Solicitud 
    var cbxTipoSolicitud = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeTipoSolicitud,
        labelAlign: 'left',
        id: 'idCbxTipoSolicitud',
        name: 'cbxTipoSolicitud',
        valueField: 'intIdTipoSolcitud',
        displayField: 'strDescTipoSolicitud',
        fieldLabel: 'Tipo Solicitud',
        width: 325,
        triggerAction: 'all',
        queryMode: 'local',
        allowBlank: true
    });

    //model Estado Tipo Solicitud
    Ext.define('modelEstdTipoSold', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'estado', type: 'string'}
        ]
    });

    //Store EstadoTipoSolicitud
    var storeEstTipoSold = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelEstdTipoSold",
        proxy: {
            type: 'ajax',
            url: urlGetEstadoTipoSolicitud,
            reader: {
                type: 'json',
                root: 'objDatos'
            }
        }
    });

    //Combo Estado Tipo Solicitud 
    var cbxEstTipoSold = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeEstTipoSold,
        labelAlign: 'left',
        id: 'idCbxEstTipoSolicitud',
        name: 'cbxEstTipoSolicitud',
        valueField: 'estado',
        displayField: 'estado',
        fieldLabel: 'Estado Solicitud',
        width: 325,
        triggerAction: 'all',
        queryMode: 'local',
        allowBlank: true
    });

    /*Fin: Filtros Comercial*/
    /*Inicio Filtros Tecnico*/
    storeInterfacesBusq = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlGetInterfacesElemento,
            extraParams: {
                idElemento: ''
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
            ]
    });

    /*Fin Filtros Tecnico*/
    var tabsFiltros = Ext.create('Ext.tab.Panel', {
        id: 'tab_panel',
        width: 950,
        autoScroll: true,
        activeTab: 0,
        defaults: {autoHeight: true},
        plain: true,
        deferredRender: false,
        hideMode: 'offsets',
        frame: false,
        buttonAlign: 'center',
        items: [
            {
                contentEl: 'fieldsTabFinanciero',
                title: 'Financiero',
                id: 'idTabFinanciero',
                autoRender: true,
                autoShow: true,
                closable: false,
                layout: {
                    type: 'table',
                    columns: 3,
                    align: 'left'
                },
                items: [
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    txtRangoFacturaDesde,
                    {html: "&nbsp;", border: false, width: 200},
                    txtRangoFacturaHasta,
                    dateFechaDesde,
                    {html: "&nbsp;", border: false, width: 200},
                    dateFechaHasta,
                    cbxOficina,
                    {html: "&nbsp;", border: false, width: 200},
                    cbxPlan,
                    cbxEstados,
                    {html: "&nbsp;", border: false, width: 200},
                    cbxProducto,
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200}
                ]
            },
            {
                contentEl: 'fieldsTabComercial',
                title: 'Comercial',
                id: 'idTabComercial',
                layout: {
                    type: 'table',
                    columns: 3,
                    align: 'left'
                },
                items: [
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    txtLogin,
                    {html: "&nbsp;", border: false, width: 200},
                    cbxTipoCuenta,
                    cbxFormaPago,
                    {html: "&nbsp;", border: false, width: 200},
                    dateFechaSolicitudDesde,
                    cbxTipoSolicitud,
                    {html: "&nbsp;", border: false, width: 200},
                    cbxEstTipoSold,
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200}
                ],
                closable: false
            },
            {
                contentEl: 'fieldsTabTecnico',
                title: 'Técnico',
                id: 'idTabTecnico',
                layout: {
                    type: 'table',
                    columns: 3,
                    align: 'left'
                },
                items: [
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {xtype: 'hidden',
                        id: 'txtIdElemento',
                        value: '',
                        readOnly: true},
                    {html: '<input width = "100"  type="text" id="txtElemento" class="x-form-field x-form-text" style=" -moz-user-select: text;" readonly>\n\
                                <a href="#" onclick="buscarElementoPanel()">\n\
                                <img src="/public/images/search.png" />\n\
                                </a>', border: false, width: 200},
                    {html: "Para", border: false, width: 200},
                    {
                        xtype: 'combobox',
                        id: 'idCbxInterfacesFiltro',
                        name: 'idCbxInterfacesFiltro',
                        store: storeInterfacesBusq,
                        fieldLabel: 'Interface',
                        displayField: 'nombreInterfaceElemento',
                        valueField: 'idInterfaceElemento',
                        queryMode: 'local',
                        riggerAction: 'all',
                        allowBlank: true,
                        width: '30%'
                    },
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200},
                    {html: "&nbsp;", border: false, width: 200}
                ],
                closable: false
            }
        ]
    });

    var objFilterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
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
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function () {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function () {
                    Limpiar();
                }
            }

        ],
        items: [tabsFiltros],
        renderTo: 'filtroFacturas'
    });


    Ext.define('ListaDocumentosModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdDocumento', type: 'int'},
            {name: 'strTipoDocumento', type: 'string'},
            {name: 'strNumFactura', type: 'string'},
            {name: 'strLogin', type: 'string'},
            {name: 'strCliente', type: 'string'},
            {name: 'strEsAutomatica', type: 'string'},
            {name: 'strElectronica', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strFeEmision', type: 'string'},
            {name: 'strFeCreacion', type: 'string'},
            {name: 'intTotal', type: 'string'},
            {name: 'intSaldoDisponible', type: 'string'}
        ]
    });
});

function buscarElementoPanel() {
    Ext.getCmp('idCbxInterfacesFiltro').value = null;
    Ext.getCmp('idCbxInterfacesFiltro').setRawValue(null);
    storeElementos = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: getElementosPorEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'modeloElemento', mapping: 'modeloElemento'},
                {name: 'ip', mapping: 'ip'},
                {name: 'estado', mapping: 'estado'}
            ]
    });
    var storeTipoElemento = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: getTiposElementosBackbone,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento'},
                {name: 'idTipoElemento', mapping: 'idTipoElemento'}
            ]
    });
    gridElementosBusq = Ext.create('Ext.grid.Panel', {
        width: 530,
        height: 294,
        store: storeElementos,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                header: 'Nombre Elemento',
                dataIndex: 'nombreElemento',
                width: 160,
                sortable: true
            },
            {
                header: 'Modelo',
                dataIndex: 'modeloElemento',
                width: 80,
                sortable: true
            },
            {
                header: 'Ip',
                dataIndex: 'ip',
                width: 100,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 90,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Accion',
                width: 50,
                items: [
                    {
                        getClass: function (v, meta, rec) {
                            if (rec.get('modeloElemento') != "GENERICO") {
                                return 'button-grid-seleccionar';
                            }
                            else {
                                return 'button-grid-invisible';
                            }

                        },
                        tooltip: 'Seleccionar',
                        handler: function (grid, rowIndex, colIndex) {
                            Ext.getCmp('txtIdElemento').setValue(grid.getStore().getAt(rowIndex).data.idElemento);
                            document.getElementById('txtElemento').value = grid.getStore().getAt(rowIndex).data.nombreElemento;
                            storeInterfacesBusq.getProxy().extraParams.idElemento = grid.getStore().getAt(rowIndex).data.idElemento;
                            storeInterfacesBusq.load();
                            win.destroy();
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeElementos,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });
    filterPanelElementosBusq = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 530,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function () {
                    buscarElemento();
                }
            }

        ], //cierre buttons
        items: [
            {width: '10%', border: false}, //inicio
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                width: '30%'
            },
            {width: '20%', border: false}, //medio
            {
                xtype: 'textfield',
                id: 'txtIp',
                fieldLabel: 'Ip',
                value: '',
                width: '30%'
            },
            {width: '10%', border: false}, //final
            //-------------------------------------
            {width: '10%', border: false}, //inicio
            {
                xtype: 'combobox',
                id: 'sltTipoElemento',
                fieldLabel: 'Tipo',
                store: storeTipoElemento,
                displayField: 'nombreTipoElemento',
                valueField: 'idTipoElemento',
                loadingText: 'Buscando ...',
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '30%'
            },
            {width: '20%', border: false}, //medio
            {width: '20%', border: false},
            {width: '10%', border: false}, //final
            //-------------------------------------
        ]//cierre items
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
                    width: 530
                },
                items: [
                    filterPanelElementosBusq,
                    gridElementosBusq
                ]
            }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function () {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Elementos',
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}
/****/
function buscarElemento() {
    if (Ext.getCmp('sltTipoElemento').getRawValue() == "") {
        Ext.Msg.alert('Alert', 'Debe escoger un Tipo de Elemento');
    }
    else {
        storeElementos.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
        storeElementos.getProxy().extraParams.ip = Ext.getCmp('txtIp').value;
        storeElementos.getProxy().extraParams.tipoElemento = Ext.getCmp('sltTipoElemento').getRawValue();
        storeElementos.load();
    }
}

function buscar() {
    //Valida que se elija al menos un filtro del panel de busqueda
    if (/*validacion financiero*/Ext.getCmp('txtRangoFacturaDesde').getValue() !== '' ||
        Ext.getCmp('txtRangoFacturaHasta').getValue()   !== '' ||
        Ext.getCmp('dateFechaDesde').getValue()         !== null ||
        Ext.getCmp('dateFechaHasta').getValue()         !== null ||
        Ext.getCmp('idCbxOficina').getValue()           !== null ||
        Ext.getCmp('idCbxEstados').getValue()           !== null ||
        Ext.getCmp('idCbxProducto').getValue()          !== null ||
        Ext.getCmp('idCbxPlanes').getValue()            !== null ||
        /*validacion comercial*/
        Ext.getCmp('txtLogin').getValue()                   !== '' ||
        Ext.getCmp('dateFechaSolicitudDesde').getValue()    !== null ||
        Ext.getCmp('idCbxFormaPago').getValue()             !== null ||
        Ext.getCmp('idCbxTipoCuenta').getValue()            !== null ||
        Ext.getCmp('idCbxTipoSolicitud').getValue()         !== null ||
        Ext.getCmp('idCbxEstTipoSolicitud').getValue()      !== null ||
        /*validacion tecnico*/
        Ext.getCmp('txtIdElemento').getValue()          !== '' ||
        Ext.getCmp('idCbxInterfacesFiltro').getValue()  !== null) {

        var myGrid = Ext.getCmp('gridDocumentos');
        if (myGrid) {
            gridDocumentos.destroy(true);
        }

        storeDocumentos = Ext.create('Ext.data.Store', {
            pageSize: 500,
            model: 'ListaDocumentosModel',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlGetstoreListaDocumentos,
                timeout: 90000,
                reader: {
                    type: 'json',
                    root: 'jsonListaDocumentos',
                    totalProperty: 'intTotalDocumentos'
                },
                simpleSortMode: true
            },
            listeners: {
                beforeload: function (storeDocumentos) {
                    //financiero parametros
                    storeDocumentos.getProxy().extraParams.strRangoFacturaDesde = Ext.getCmp('txtRangoFacturaDesde').getValue();
                    storeDocumentos.getProxy().extraParams.strRangoFacturaHasta = Ext.getCmp('txtRangoFacturaHasta').getValue();
                    storeDocumentos.getProxy().extraParams.dateFechaDesde       = Ext.getCmp('dateFechaDesde').getValue();
                    storeDocumentos.getProxy().extraParams.dateFechaHasta       = Ext.getCmp('dateFechaHasta').getValue();
                    storeDocumentos.getProxy().extraParams.intIdOficina         = Ext.getCmp('idCbxOficina').getValue();
                    storeDocumentos.getProxy().extraParams.strEstado            = Ext.getCmp('idCbxEstados').getValue();
                    storeDocumentos.getProxy().extraParams.intIdProducto        = Ext.getCmp('idCbxProducto').getValue();
                    storeDocumentos.getProxy().extraParams.intIdPlan            = Ext.getCmp('idCbxPlanes').getValue();
                    //comercial parametros
                    storeDocumentos.getProxy().extraParams.strLogin                 = Ext.getCmp('txtLogin').getValue();
                    storeDocumentos.getProxy().extraParams.dateFechaSolicitudDesde  = Ext.getCmp('dateFechaSolicitudDesde').getValue();
                    storeDocumentos.getProxy().extraParams.intIdFormaPago           = Ext.getCmp('idCbxFormaPago').getValue();
                    storeDocumentos.getProxy().extraParams.intIdTipoCuenta          = Ext.getCmp('idCbxTipoCuenta').getValue();
                    storeDocumentos.getProxy().extraParams.intIdTipoSolicitud       = Ext.getCmp('idCbxTipoSolicitud').getValue();
                    storeDocumentos.getProxy().extraParams.strIdEstTipoSolicitud    = Ext.getCmp('idCbxEstTipoSolicitud').getValue();
                    //tecnico parametros
                    storeDocumentos.getProxy().extraParams.intIdElemento            = Ext.getCmp('txtIdElemento').getValue();
                    storeDocumentos.getProxy().extraParams.intIdInterface           = Ext.getCmp('idCbxInterfacesFiltro').getValue();
                },
                load: function (store) {
                    if (store.getProxy().getReader().rawData.strMensajeError !== '') {
                        Ext.Msg.alert('Error', store.getProxy().getReader().rawData.strMensajeError);
                    }
                }
            }
        });

        chkBoxModel = new Ext.selection.CheckboxModel({
            listeners: {
                selectionchange: function (selectionModel, selected, options) {
                    arregloSeleccionados = new Array();
                    Ext.each(selected, function (rec) {});
                }
            }
        });

        toolbar = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            align: '->',
            items:
                [{xtype: 'tbfill'},
                    {
                        //iconCls: 'icon_aprobar',
                        text: 'Crear Nota de Credito',
                        //itemId: 'clear',
                        scope: this,
                        handler: function () {
                            strIdDocumentos = '';
                            //Valida que haya seleccionado una factura, caso contrario muestra un mensaje de alerta
                            if (chkBoxModel.getSelection().length > 0)
                            {
                                //Itera los chkBox y concatena los ID Facturas en un solo string strIdDocumentos
                                for (var intForIndex = 0; intForIndex < chkBoxModel.getSelection().length; intForIndex++)
                                {
                                    console.log(chkBoxModel.getSelection()[intForIndex]);
                                    strIdDocumentos = strIdDocumentos + chkBoxModel.getSelection()[intForIndex].data.intIdDocumento;
                                    if (intForIndex < (chkBoxModel.getSelection().length - 1))
                                    {
                                        strIdDocumentos = strIdDocumentos + '-';
                                    }
                                }
                                /*Combo Motivos NC*/
                                Ext.define('modelMotivos', {
                                    extend: 'Ext.data.Model',
                                    fields: [
                                        {name: 'intIdMotivo', type: 'int'},
                                        {name: 'strNombreMotivo', type: 'string'}
                                    ]
                                });

                                //Store de motivos
                                storeMotivosNc = Ext.create('Ext.data.Store', {
                                    autoLoad: true,
                                    model: "modelMotivos",
                                    proxy: {
                                        type: 'ajax',
                                        url: urlGetMotivosNc,
                                        reader: {
                                            type: 'json',
                                            root: 'jsonMotivos'
                                        }
                                    }
                                });

                                //Campo fecha si es una nota de credito proporcional por dias
                                dateFechaDesdePro = new Ext.form.DateField({
                                    id: 'dateFechaDesdePro',
                                    fieldLabel: 'Fecha Desde',
                                    labelAlign: 'left',
                                    xtype: 'datefield',
                                    format: 'd-m-Y',
                                    width: 300
                                });

                                //Campo fecha si es una nota de credito proporcional por dias
                                dateFechaHastaPro = new Ext.form.DateField({
                                    id: 'dateFechaHastaPro',
                                    fieldLabel: 'Fecha Hasta',
                                    labelAlign: 'left',
                                    xtype: 'datefield',
                                    format: 'd-m-Y',
                                    width: 300
                                });

                                //Campo porcentaje si es una nota de credito por porcentaje del servicio
                                txtPorcentaje = Ext.create('Ext.form.Text',
                                    {
                                        id: 'txtPorcentaje',
                                        name: 'txtPorcentajes',
                                        fieldLabel: 'Porcentaje',
                                        labelAlign: 'left',
                                        minValue: 1,
                                        maxValue: 100,
                                        width: 200,
                                        maskRe: /[0-9.]/,
                                        regex: /^[0-9]+(?:\.[0-9]+)?$/,
                                        regexText: 'Solo numeros'
                                    });

                                //CheckBox para seleccionar NC Proporcional por dias
                                chkBoxProporcionaleXDias = new Ext.form.Radio({
                                    boxLabel: 'Proporcional por dias',
                                    name: 'grTipoNotaCredito',
                                    inputValue: 'chkBoxProporcionaleXDias',
                                    uncheckedValue: false
                                });

                                //CheckBox para seleccionar NC Porcentaje del servicio
                                chkBoxPorcentajeServicio = new Ext.form.Radio({
                                    boxLabel: 'Porcentaje del servicio',
                                    name: 'grTipoNotaCredito',
                                    inputValue: 'chkBoxPorcentajeServicio',
                                    uncheckedValue: false
                                });

                                //CheckBox para seleccionar NC Valor original
                                chkBoxValorOriginal = new Ext.form.Radio({
                                    boxLabel: 'Valor Original',
                                    name: 'grTipoNotaCredito',
                                    inputValue: 'chkBoxValorOriginal',
                                    uncheckedValue: false
                                });

                                //Radio Button Group que contiene los tipo de notas de credito
                                rdTipoNotaCredito = new Ext.form.RadioGroup({
                                    fieldLabel: 'Tipo Nota de Credito',
                                    columns: 1,
                                    items: [chkBoxProporcionaleXDias, chkBoxPorcentajeServicio, chkBoxValorOriginal],
                                    listeners: {
                                        change: function (field, newValue, oldValue) {
                                            rbValue = newValue.grTipoNotaCredito;
                                            //Cuando selecciona un tipo de Nota de credito se habilita el boton de guardar
                                            Ext.getCmp('idBtnGuardar').setDisabled(false);
                                            //Oculta y muestra las opciones segun el tipo de nota de credito
                                            switch (newValue.grTipoNotaCredito) {
                                                case 'chkBoxProporcionaleXDias':
                                                    Ext.getCmp('idFsPorValorFact').setVisible(false);
                                                    Ext.getCmp('idFsProValorFact').setVisible(true);
                                                    Ext.getCmp('dateFechaDesdePro').setValue('');
                                                    Ext.getCmp('dateFechaHastaPro').setValue('');
                                                    Ext.getCmp('txtPorcentaje').setValue('');
                                                    break;
                                                case 'chkBoxPorcentajeServicio':
                                                    Ext.getCmp('idFsProValorFact').setVisible(false);
                                                    Ext.getCmp('idFsPorValorFact').setVisible(true);
                                                    Ext.getCmp('dateFechaDesdePro').setValue('');
                                                    Ext.getCmp('dateFechaHastaPro').setValue('');
                                                    Ext.getCmp('txtPorcentaje').setValue('');
                                                    break;
                                                default:
                                                    Ext.getCmp('idFsPorValorFact').setVisible(false);
                                                    Ext.getCmp('idFsProValorFact').setVisible(false);
                                                    Ext.getCmp('dateFechaDesdePro').setValue('');
                                                    Ext.getCmp('dateFechaHastaPro').setValue('');
                                                    Ext.getCmp('txtPorcentaje').setValue('');
                                            }
                                        }
                                    }
                                });
                                winCreaNotaCredito = '';
                                formCreaNotaCredito = new Ext.FormPanel({
                                    title: 'Nueva Nota de Credito',
                                    defaults: {xtype: 'textfield'},
                                    bodyStyle: 'padding: 10px',
                                    buttonAlign: 'center',
                                    items: [rdTipoNotaCredito,
                                        {
                                            xtype: 'textarea',
                                            id: 'intIdObservacion',
                                            fieldLabel: 'Observación',
                                            labelAlign: 'left',
                                            name: 'strObservacion',
                                            width: 450,
                                            height: 50
                                        },
                                        {
                                            xtype: 'combobox',
                                            id: 'idCbxMotivosNc',
                                            fieldLabel: 'Motivos',
                                            store: storeMotivosNc,
                                            displayField: 'strNombreMotivo',
                                            valueField: 'intIdMotivo',
                                            loadingText: 'Buscando ...',
                                            listClass: 'x-combo-list-small',
                                            queryMode: 'local',
                                            width: 450
                                        },
                                        {
                                            id: 'idFsProValorFact',
                                            name: 'idFsProValorFact',
                                            xtype: 'fieldset',
                                            title: 'Proporcional del valor de la factura',
                                            width: 450,
                                            collapsible: false,
                                            collapsed: false,
                                            items: [dateFechaDesdePro, dateFechaHastaPro]
                                        },
                                        {
                                            id: 'idFsPorValorFact',
                                            name: 'idFsPorValorFact',
                                            xtype: 'fieldset',
                                            title: 'Porcentaje del valor de la factura',
                                            width: 450,
                                            collapsible: false,
                                            collapsed: false,
                                            items: [txtPorcentaje]
                                        }
                                    ],
                                    buttons: [
                                        {
                                            text: 'Crear Nota de Credito',
                                            name: 'btnGuardar',
                                            id: 'idBtnGuardar',
                                            disabled: false,
                                            handler: function () {
                                                //Valida que la variable no este vacia y contenga id facturas
                                                if (strIdDocumentos === '') {
                                                    Ext.Msg.alert('Alert', 'Debe seleccionar al menos una factura.');
                                                } else {
                                                    //Valida que se haya ingresado una observacion
                                                    if (Ext.getCmp('intIdObservacion').getValue() !== '')
                                                    {
                                                        //Valida que haya elegido un motivo
                                                        if (Ext.getCmp('idCbxMotivosNc').getValue() !== null) {
                                                            //Pregunta si la seleccion es Proporcional por dias y los campos fechas no son nulos.
                                                            if (rbValue === 'chkBoxProporcionaleXDias' &&
                                                                Ext.getCmp('dateFechaDesdePro').getValue() !== null &&
                                                                Ext.getCmp('dateFechaHastaPro').getValue() !== null) {
                                                                Ext.Ajax.request({
                                                                    url: urlCreaNotaCreditoMasiva,
                                                                    method: 'POST',
                                                                    timeout: 60000,
                                                                    params: {
                                                                        strIdDocumentos:        strIdDocumentos,
                                                                        dateFechaDesdePro:      Ext.getCmp('dateFechaDesdePro').getValue(),
                                                                        dateFechaHastaPro:      Ext.getCmp('dateFechaHastaPro').getValue(),
                                                                        strObservacion:         Ext.getCmp('intIdObservacion').getValue(),
                                                                        intIdMotivo:            Ext.getCmp('idCbxMotivosNc').getValue(),
                                                                        strTipoNotaCredito:     'PROPORCIONAL_DIAS',
                                                                        intPorcentaje:          0
                                                                    },
                                                                    success: function (response) {
                                                                        var text = Ext.decode(response.responseText);
                                                                        Ext.Msg.alert('Informational', text.messageStatus);
                                                                    },
                                                                    failure: function (result) {
                                                                        Ext.Msg.alert('Error ', 'Error juanito: ' + result.statusText);
                                                                    }
                                                                });
                                                                this.up('form').getForm().reset();
                                                                this.up('window').destroy();
                                                                storeDocumentos.load();
                                                            //Var por false cuando la seleccion es Proporcional por dias y los campos fechas son nulos.
                                                            } else if (rbValue === 'chkBoxProporcionaleXDias') {
                                                                Ext.Msg.alert('Alert', 'Debe seleccionar un rango de fechas.');
                                                            }
                                                            //Pregunta si la seleccion es Porcentaje del servicio y el campo porcentaje es diferente de nulo.
                                                            if (rbValue === 'chkBoxPorcentajeServicio' && Ext.getCmp('txtPorcentaje').getValue() !== '') {
                                                                //Valida que el porcentaje sea > 0 y < 101
                                                                if (Ext.getCmp('txtPorcentaje').getValue() > 0 && Ext.getCmp('txtPorcentaje').getValue() < 101) {
                                                                    Ext.Ajax.request({
                                                                        url: urlCreaNotaCreditoMasiva,
                                                                        method: 'POST',
                                                                        timeout: 60000,
                                                                        params: {
                                                                            strIdDocumentos:    strIdDocumentos,
                                                                            dateFechaDesdePro:  '00-00-0000',
                                                                            dateFechaHastaPro:  '00-00-0000',
                                                                            strObservacion:     Ext.getCmp('intIdObservacion').getValue(),
                                                                            intIdMotivo:        Ext.getCmp('idCbxMotivosNc').getValue(),
                                                                            strTipoNotaCredito: 'PORCENTAJE_SERVICIO',
                                                                            intPorcentaje:      Ext.getCmp('txtPorcentaje').getValue()
                                                                        },
                                                                        success: function (response) {
                                                                            var text = Ext.decode(response.responseText);
                                                                            Ext.Msg.alert('Informational', text.messageStatus);
                                                                        },
                                                                        failure: function (result) {
                                                                            Ext.Msg.alert('Error ', 'Error juanito: ' + result.statusText);
                                                                        }
                                                                    });
                                                                    this.up('form').getForm().reset();
                                                                    this.up('window').destroy();
                                                                    storeDocumentos.load();
                                                                }
                                                                else { //Entra al false cuando el valor no esta entre 1 Y 100
                                                                    Ext.Msg.alert('Informational', 'El valor del porcentaje debe ser entre 1 y 100');
                                                                }
                                                            //Entra al false cuando la seleccion es porcentaje del servicio y el campo porcentaje es nulo
                                                            } else if (rbValue === 'chkBoxPorcentajeServicio') {
                                                                Ext.Msg.alert('Alert', 'Debe ingresar un porcentaje.');
                                                            }
                                                            //Pregunta si la seleccion es valor original
                                                            if (rbValue === 'chkBoxValorOriginal') {
                                                                Ext.Ajax.request({
                                                                    url: urlCreaNotaCreditoMasiva,
                                                                    method: 'POST',
                                                                    timeout: 60000,
                                                                    params: {
                                                                        strIdDocumentos:    strIdDocumentos,
                                                                        dateFechaDesdePro:  '00-00-0000',
                                                                        dateFechaHastaPro:  '00-00-0000',
                                                                        strObservacion:     Ext.getCmp('intIdObservacion').getValue(),
                                                                        intIdMotivo:        Ext.getCmp('idCbxMotivosNc').getValue(),
                                                                        strTipoNotaCredito: 'VALOR_ORIGINAL',
                                                                        intPorcentaje:      0
                                                                    },
                                                                    success: function (response) {
                                                                        var text = Ext.decode(response.responseText);
                                                                        Ext.Msg.alert('Informational', text.messageStatus);
                                                                    },
                                                                    failure: function (result) {
                                                                        Ext.Msg.alert('Error ', 'Error juanito: ' + result.statusText);
                                                                    }
                                                                });
                                                                this.up('form').getForm().reset();
                                                                this.up('window').destroy();
                                                                storeDocumentos.load();
                                                            }
                                                        //Entra por false cuando no se ha seleccionado algun motivo
                                                        } else { //Ext.getCmp('idCbxMotivosNc').getValue()
                                                            Ext.Msg.alert('Alert', 'Debe ingresar un motivo.');
                                                        } //Ext.getCmp('idCbxMotivosNc').getValue()
                                                    //Entra por false cuando no se ha ingresado una observacion
                                                    } else { //Ext.getCmp('intIdObservacion').getValue()
                                                        Ext.Msg.alert('Alert', 'Debe ingresar una observacion.');
                                                    } //Ext.getCmp('intIdObservacion').getValue()
                                                } //if strIdDocumentos
                                            }
                                        },
                                        {
                                            text: 'Cancelar',
                                            handler: function () {
                                                this.up('form').getForm().reset();
                                                this.up('window').destroy();
                                            }
                                        }]
                                });
                                Ext.getCmp('idFsPorValorFact').setVisible(false);
                                Ext.getCmp('idFsProValorFact').setVisible(false);
                                Ext.getCmp('idBtnGuardar').setDisabled(true);
                                winCreaNotaCredito = new Ext.Window({
                                    title: 'New Developer',
                                    width: 600,
                                    height: 400,
                                    bodyStyle: 'background-color:#fff;padding: 10px',
                                    items: formCreaNotaCredito,
                                    resizable: false,
                                    draggable: false
                                });
                                winCreaNotaCredito.show();
                            }
                            //Entra por false cuando no ha seleccionado una factura
                            else { //chkBoxModel.getSelection().length
                                Ext.Msg.alert('Alert', 'Debe seleccionar al menos una factura.');
                            } //chkBoxModel.getSelection().length
                        }
                    }
                ]
        });

        //Panel Grid de los documentos
        gridDocumentos = Ext.create('Ext.grid.Panel', {
            title: 'Listado de Documentos',
            store: storeDocumentos,
            multiSelect: false,
            selModel: chkBoxModel,
            id: 'gridDocumentos',
            plugins: [{ptype: 'pagingselectpersist'}],
            viewConfig: {enableTextSelection: true, preserveScrollOnRefresh: true},
            dockedItems: [toolbar],
            columns: [
                {header: 'Tipo Documento ', dataIndex: 'strTipoDocumento', width: 120 },
                {header: 'No. Fact', dataIndex: 'strNumFactura', width: 120 },
                {header: 'Pto. Cliente', dataIndex: 'strLogin', width: 150 },
                {header: 'Cliente', dataIndex: 'strCliente', width: 120 },
                {header: 'Auto?', dataIndex: 'strEsAutomatica', width: 50 },
                {header: 'Elec?', dataIndex: 'strElectronica', width: 50 },
                {header: 'Estado', dataIndex: 'strEstado', width: 80 },
                {header: 'F. Emision', dataIndex: 'strFeEmision'},
                {header: 'F. Creacion', dataIndex: 'strFeCreacion'},
                {header: 'Total', dataIndex: 'intTotal'},
                {header: 'Saldo Disponible', dataIndex: 'intSaldoDisponible'}
            ],
            height: 400,
            width: 1133,
            renderTo: 'ListaFacturas',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeDocumentos,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            })
        });
    //Entra al false cuando no se ingresado algun filtro de busqueda
    } else {
        Ext.Msg.alert('Alert', 'Debe escoger al menos un parámetro de búsqueda, Favor revisar los campos!!!');
    }
}

function Limpiar() {
    //parametros financiero
    Ext.getCmp('dateFechaDesde').setValue('');
    Ext.getCmp('dateFechaHasta').setValue('');
    Ext.getCmp('txtRangoFacturaDesde').setValue('');
    Ext.getCmp('txtRangoFacturaHasta').setValue('');
    Ext.getCmp('idCbxOficina').value = null;
    Ext.getCmp('idCbxOficina').setRawValue(null);
    Ext.getCmp('idCbxEstados').value = null;
    Ext.getCmp('idCbxEstados').setRawValue(null);
    Ext.getCmp('idCbxProducto').value = null;
    Ext.getCmp('idCbxProducto').setRawValue(null);
    Ext.getCmp('idCbxPlanes').value = null;
    Ext.getCmp('idCbxPlanes').setRawValue(null);
    //parametros comercial
    Ext.getCmp('txtLogin').setValue('');
    Ext.getCmp('dateFechaSolicitudDesde').setValue('');
    Ext.getCmp('idCbxFormaPago').value = null;
    Ext.getCmp('idCbxFormaPago').setRawValue(null);
    Ext.getCmp('idCbxTipoCuenta').value = null;
    Ext.getCmp('idCbxTipoCuenta').setRawValue(null);
    Ext.getCmp('idCbxTipoSolicitud').value = null;
    Ext.getCmp('idCbxTipoSolicitud').setRawValue(null);
    Ext.getCmp('idCbxEstTipoSolicitud').value = null;
    Ext.getCmp('idCbxEstTipoSolicitud').setRawValue(null);
    //parametros tecnico
    Ext.getCmp('txtIdElemento').setValue('');
    document.getElementById('txtElemento').value = '';
    Ext.getCmp('idCbxInterfacesFiltro').value = null;
    Ext.getCmp('idCbxInterfacesFiltro').setRawValue(null);
    var myGrid = Ext.getCmp('gridDocumentos');
    if (myGrid) {
        gridDocumentos.destroy(true);
    }
}