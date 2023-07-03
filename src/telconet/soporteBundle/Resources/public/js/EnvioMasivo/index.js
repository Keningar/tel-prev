var itemsPrincipales                = null;
var itemsSecundarios                = null;
var boolNoLoadTiposNegocio          = true;
var boolNoLoadOficinasYFormasPago   = true;
var boolNoLimpiarFormasPago         = true;
var boolSinPlantilla                = true;
var extraParamsBusqueda             = null;
var extraParamsEnvioMasivo          = null;
var strHtmlInfoBusqueda             = null;
itemsSI_NO          = ['SI', 'NO'];
itemsTipoNegocio    = [];
itemsOficina        = [];
itemsFormaPago      = [];
itemsBancoTarjeta   = [];

var numFacturasAbiertas;
var fechaEmisionFactura;
var valorMontoDeuda;
var idFormaPago;

var idsOficinas;
var idsTiposNegocioGlobal;
var idsOficinasGlobal;
var idFormaPagoGlobal;
var valorClienteVIP;
var idsBancosTarjetasGlobal;
var valFechaDesdeFactura;
var valFechaHastaFactura;

Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    storeElementosNodo = new Ext.data.Store({
        total: 'total',
        pageSize: 100,
        proxy: {
            type: 'ajax',
            url: strUrlGetElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: this.nombreElemento,
                estado: 'ACTIVE',
                elemento: 'NODO'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });

    storeElementosSwitch = new Ext.data.Store({
        total: 'total',
        pageSize: 100,
        proxy: {
            type: 'ajax',
            url: strUrlGetSwitchesEnNodo,
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayResultado'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElementoSwith'},
                {name: 'nombreElemento', mapping: 'nombreElementoSwitch'}
            ]
    });

    cmbElementosNodo = new Ext.form.ComboBox({
        id: 'cmbElementosNodo',
        name: 'cmbElementosNodo',
        fieldLabel: 'NODO',
        emptyText: '',
        store: storeElementosNodo,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        border: 0,
        margin: 0,
        queryMode: "remote",
        forceSelection: true,
        listeners: {
            select: function() {

                Ext.getCmp('cmbElementosSwitch').setDisabled(false);
                Ext.getCmp('cmbElementosSwitch').setRawValue('');
                Ext.getCmp('cmbElementosSwitch').reset();
                storeElementosSwitch.proxy.extraParams = {
                    intIdElementoNodo: Ext.getCmp("cmbElementosNodo").getValue()
                };
                storeElementosSwitch.load();
            }
        }
    });



    cmbElementosSwitch = new Ext.form.ComboBox({
        id: 'cmbElementosSwitch',
        name: 'cmbElementosSwitch',
        fieldLabel: 'SWITCH',
        emptyText: '',
        store: storeElementosSwitch,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        border: 0,
        margin: 0,
        forceSelection: true,
        queryMode: "remote"
    });

    var frameTipoNegocio = new Ext.form.CheckboxGroup({
        id: 'frameTipoNegocio',
        vertical: true,
        align: 'left',
        columns: 3
    });

    storeTipoNegocio = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetTiposNegocio,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields: [{
                name: 'idTipoNegocio',
                mapping: 'idTipoNegocio'
            }, {
                name: 'nombreTipoNegocio',
                mapping: 'nombreTipoNegocio'
            }],
        listeners: {
            load: function(t, records, options) {
                frameTipoNegocio.removeAll();
                var i = 0;

                Ext.getCmp('panelTipoNegocio').setVisible(false);
                if (records[0].data.nombreTipoNegocio != "") {
                    for (var i = 0; i < records.length; i++) {
                        var cb = Ext.create('Ext.form.field.Checkbox',
                            {
                                boxLabel: records[i].data.nombreTipoNegocio,
                                inputValue: records[i].data.idTipoNegocio,
                                id: 'idTipoNegocio_' + i,
                                name: 'tipoNegocio'
                            });
                        frameTipoNegocio.add(cb);
                        itemsTipoNegocio[i] = cb;
                    }
                    Ext.getCmp('panelBbarTipoNegocio').setVisible(true);
                }
                Ext.getCmp('imgLoadTipoNegocio').setVisible(false);
                Ext.getCmp('panelTipoNegocio').setVisible(true);
            }
        }
    });


    var frameOficina = new Ext.form.CheckboxGroup({
        id: 'frameOficina',
        vertical: true,
        align: 'left',
        columns: 3
    });

    var frameFormaPago = new Ext.form.RadioGroup({
        id: 'frameFormaPago',
        vertical: true,
        align: 'left',
        columns: 1,
        listeners: {
            change: function(field, newValue, oldValue) {
                Ext.getCmp('frameBancoTarjeta').setVisible(false);
                Ext.getCmp('panelBbarBancoTarjeta').setVisible(false);
                Ext.get('imgLoadBancoTarjeta').setStyle('display', 'block');
                Ext.getCmp('panelBancoTarjeta').expand();
                storeBancoTarjeta.getProxy().extraParams.idFormaPagoSelected = newValue.formaPago;
                storeBancoTarjeta.removeAll();
                if (boolNoLimpiarFormasPago) {
                    storeBancoTarjeta.load();
                }
                boolNoLimpiarFormasPago = true;
                itemsBancoTarjeta = [];
            }
        }
    });

    var frameBancoTarjeta = new Ext.form.CheckboxGroup({
        id: 'frameBancoTarjeta',
        vertical: true,
        align: 'left',
        columns: 2
    });


    storeBancoTarjeta = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetBancosTarjetas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields: [{
                name: 'id',
                mapping: 'id'
            }, {
                name: 'nombre',
                mapping: 'nombre'
            }],
        listeners: {
            load: function(t, records, options) {
                frameBancoTarjeta.removeAll();
                var i = 0;
                Ext.getCmp('panelBancoTarjeta').setVisible(false);

                if (records[0].data.nombre != "") {
                    for (var i = 0; i < records.length; i++) {
                        var cb = Ext.create('Ext.form.field.Checkbox',
                            {
                                boxLabel: records[i].data.nombre,
                                inputValue: records[i].data.id,
                                id: 'idBancoTarjeta_' + i,
                                name: 'bancoTarjeta'
                            });
                        frameBancoTarjeta.add(cb);
                        itemsBancoTarjeta[i] = cb;
                    }
                    Ext.getCmp('frameBancoTarjeta').setVisible(true);
                    Ext.getCmp('panelBbarBancoTarjeta').setVisible(true);
                }
                Ext.get('imgLoadBancoTarjeta').setStyle('display', 'none');
                Ext.getCmp('panelBancoTarjeta').setVisible(true);
            }
        }
    });

    storeGruposProducto = new Ext.data.Store({
        proxy: {
            type: 'ajax',
            url: strUrlGetGruposProductos,
            reader: {
                type: 'json',
                root: 'grupos'
            }
        },
        fields:
            [
                {name: 'idGrupo', mapping: 'idGrupo'},
                {name: 'descripcionGrupo', mapping: 'descripcionGrupo'}
            ]
    });


    cmbGruposProducto = new Ext.form.ComboBox({
        id: 'cmbGrupos',
        name: 'cmbGrupos',
        fieldLabel: 'Grupo',
        emptyText: '',
        store: storeGruposProducto,
        displayField: 'descripcionGrupo',
        valueField: 'idGrupo',
        border: 0,
        margin: 0,
        forceSelection: true,
        queryMode: "remote"
    });

    storeSubgruposProducto = new Ext.data.Store({
        proxy: {
            type: 'ajax',
            url: strUrlGetSubgruposProductos,
            reader: {
                type: 'json',
                root: 'subgrupos'
            }
        },
        fields:
            [
                {name: 'idSubgrupo', mapping: 'idSubgrupo'},
                {name: 'descripcionSubgrupo', mapping: 'descripcionSubgrupo'}
            ]
    });

    cmbSubgruposProducto = new Ext.form.ComboBox({
        id: 'cmbSubgrupos',
        name: 'cmbSubgrupos',
        fieldLabel: 'Subgrupo',
        emptyText: '',
        store: storeSubgruposProducto,
        displayField: 'descripcionSubgrupo',
        valueField: 'idSubgrupo',
        border: 0,
        margin: 0,
        forceSelection: true,
        queryMode: "remote"
    });

    radioGroupClientesVIP = new Ext.form.RadioGroup({
        id: 'radioGroupClientesVIP',
        name: 'radioGroupClientesVIP',
        xtype: 'radiogroup',
        fieldLabel: 'Clientes VIP',
        vertical: true,
        columns: 2,
        width: '100%',
        items: [
            {boxLabel: 'SI', id: 'rbClientesVIP_1', name: 'rbClientesVIP', inputValue: 'S'},
            {boxLabel: 'NO', id: 'rbClientesVIP_2', name: 'rbClientesVIP', inputValue: 'N'}
        ]
    });

    cbPuntosFacturacion = new Ext.form.Checkbox({
        id: 'cbPuntosFacturacion',
        name: 'cbPuntosFacturacion',
        boxLabel: 'Sólo Puntos de Facturaci&oacute;n',
        checked: true
    });
    
    cbSaldoPendientePago = new Ext.form.Checkbox({
        id: 'cbSaldoPendientePago',
        name: 'cbSaldoPendientePago',
        boxLabel: 'Saldo Pendiente de Pago',
        checked: true,
        listeners: {
            change: function() {
                if(Ext.getCmp('cbSaldoPendientePago').value)
                {
                    Ext.getCmp('valorSaldoPendientePago').enable();
                }
                else
                {
                    Ext.getCmp('valorSaldoPendientePago').disable();
                    Ext.getCmp('valorSaldoPendientePago').reset();
                }
                
            }
        }
    });

    
    var storeEstadosServicio = Ext.create('Ext.data.Store', {
        id: 'storeEstadosServicio',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: strUrlGetEstadosFiltros,
            reader: {
                type: 'json',
                root: 'arrayRegistros'
            },
            extraParams: {
                    valor1: 'SERVICIO'
                }
        },
        fields:
            [
                {name: 'valor2', mapping: 'valor2'}
            ]
    });

    comboEstadosServicio = new Ext.form.ComboBox({
        id: 'cmbEstadosServicio',
        name: 'cmbEstadosServicio',
        fieldLabel: "Estado Servicio",
        queryMode: 'remote',
        emptyText: 'Seleccione...',
        store: storeEstadosServicio,
        displayField: 'valor2',
        valueField: 'valor2',
        layout: 'anchor',
        disabled: false,
        forceSelection: true,
        editable: false
    });
    
    var storeEstadosPunto = Ext.create('Ext.data.Store', {
        id: 'storeEstadosPunto',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: strUrlGetEstadosFiltros,
            reader: {
                type: 'json',
                root: 'arrayRegistros'
            },
            extraParams: {
                    valor1: 'PUNTO'
                }
        },
        fields:
            [
                {name: 'valor2', mapping: 'valor2'}
            ]
    });

    comboEstadosPunto = new Ext.form.ComboBox({
        id: 'cmbEstadosPunto',
        name: 'cmbEstadosPunto',
        fieldLabel: "Estado Punto",
        queryMode: 'remote',
        emptyText: 'Seleccione...',
        store: storeEstadosPunto,
        displayField: 'valor2',
        valueField: 'valor2',
        layout: 'anchor',
        disabled: false,
        forceSelection: true,
        editable: false
    }); 
    
    var storeEstadosCliente = Ext.create('Ext.data.Store', {
        id: 'storeEstadosCliente',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: strUrlGetEstadosFiltros,
            reader: {
                type: 'json',
                root: 'arrayRegistros'
            },
            extraParams: {
                    valor1: 'CLIENTE'
                }
        },
        fields:
            [
                {name: 'valor2', mapping: 'valor2'}
            ]
    });

    comboEstadosCliente = new Ext.form.ComboBox({
        id: 'cmbEstadosCliente',
        name: 'cmbEstadosCliente',
        fieldLabel: "Estado Cliente",
        queryMode: 'remote',
        emptyText: 'Seleccione...',
        store: storeEstadosCliente,
        displayField: 'valor2',
        valueField: 'valor2',
        layout: 'anchor',
        disabled: false,
        forceSelection: true,
        editable: false
    }); 
    
    
    itemsSuperiorGrid =
        [
            {
                iconCls: 'icon_check',
                text: 'Configurar Envío Masivo',
                itemId: 'enviar',
                scope: this,
                handler: function() 
                {
                    if (gridServicios.getStore().data.items.length == 0)
                    {
                        Ext.Msg.alert('Alerta ', 'Debe realizar su búsqueda primero');
                    }
                    else 
                    {
                        showConfigurarEnvioPlantilla();
                    }
                }
            },
            {xtype: 'tbfill'},
            {
                iconCls: 'icon_exportar',
                text: 'Exportar',
                itemId: 'exportar',
                scope: this,
                handler: function() {
                    if (gridServicios.getStore().data.items.length == 0)
                    {
                        Ext.Msg.alert('Alerta ', 'Debe realizar su búsqueda primero');
                    }
                    else 
                    {
                        exportarExcel();
                    }
                }
            }
        ];    
    
    cmbFechaDesdeFactura = new Ext.form.DateField({
            id: 'cmbFechaDesdeFactura',
            name: 'cmbFechaDesdeFactura',
            fieldLabel: 'Facturas desde',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            emptyText: "Seleccione...",
            allowBlank: true,
            editable: false
        });
        
        cmbFechaHastaFactura = new Ext.form.DateField({
            id: 'cmbFechaHastaFactura',
            name: 'cmbFechaHastaFactura',
            fieldLabel: 'Facturas hasta',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            emptyText: "Seleccione...",
            allowBlank: true,
            editable: false
        });

    var storeTiposFactura = Ext.create('Ext.data.Store', {
        id: 'storeTiposFactura',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: strUrlGetTiposFactura,
            reader: {
                type: 'json',
                root: 'arrayRegistros'
            }
        },
        fields:
            [
                {name: 'valor1', mapping: 'valor1'},
                {name: 'valor2', mapping: 'valor2'}
            ]
    });

    cmbTiposFactura = new Ext.form.ComboBox({
        id: 'cmbTiposFactura',
        name: 'cmbTiposFactura',
        fieldLabel: "Tipo Factura",
        queryMode: 'remote',
        emptyText: 'Seleccione...',
        store: storeTiposFactura,
        displayField: 'valor2',
        valueField: 'valor1',
        layout: 'anchor',
        disabled: false,
        forceSelection: true,
        editable: false
    }); 


    itemsPrincipales = [
        {html: "&nbsp;", border: false, width: 150},
        cmbGruposProducto,
        {html: "&nbsp;", border: false, width: 350},
        cmbSubgruposProducto,
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        cmbElementosNodo,
        {html: "&nbsp;", border: false, width: 350},
        cmbElementosSwitch,
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        comboEstadosCliente,
        {html: "&nbsp;", border: false, width: 350},
        radioGroupClientesVIP,
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        comboEstadosPunto,
        {html: "&nbsp;", border: false, width: 350},
        comboEstadosServicio,
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        cmbTiposFactura,
        {html: "&nbsp;", border: false, width: 350},
        {
            xtype: 'numberfield',
            fieldLabel: 'Facts. Min. Abiertas',
            id: 'facturasAbiertas',
            name: 'facturasAbiertas',
            minValue: 1,
            maxValue: 10,
            allowDecimals: false,
            decimalPrecision: 2,
            step: 1,
            emptyText: 'Rango (1-10)',
            labelStyle: 'text-align:left;'
        },
        {html: "&nbsp;", border: false, width: 150},
        
        
        {html: "&nbsp;", border: false, width: 150},
        cmbFechaDesdeFactura,
        {html: "&nbsp;", border: false, width: 350},
        cmbFechaHastaFactura,
        {html: "&nbsp;", border: false, width: 150},
        
        {html: "&nbsp;", border: false, width: 150},
        cbPuntosFacturacion,
        {html: "&nbsp;", border: false, width: 350},
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        
        {html: "&nbsp;", border: false, width: 150},
        cbSaldoPendientePago,
        {html: "&nbsp;", border: false, width: 350},
        {
            xtype: 'numberfield',
            hideTrigger: true,
            fieldLabel: 'Valor Min. Pendiente',
            id: 'valorSaldoPendientePago',
            name: 'valorSaldoPendientePago',
            labelStyle: 'text-align:left;'
        },
        {html: "&nbsp;", border: false, width: 150},
        
        {html: "&nbsp;", border: false, width: 150},
        {
            id: 'panelTipoNegocio',
            name: 'panelTipoNegocio',
            xtype: 'fieldset',
            title: 'Tipos de Negocio',
            colspan: 3,
            collapsible: true,
            collapsed: true,
            items: [
                {
                    xtype: 'image',
                    src: '/public/images/images_crud/ajax-loader.gif',
                    id: 'imgLoadTipoNegocio',
                    name: 'imgLoadTipoNegocio',
                    width: 32,
                    height: 32,
                    style: {
                        'display': 'block',
                        'margin': 'auto'
                    }
                },
                frameTipoNegocio,
                {
                    xtype: 'panel',
                    id: 'panelBbarTipoNegocio',
                    name: 'panelBbarTipoNegocio',
                    hidden: true,
                    buttonAlign: 'right',
                    bbar: [
                        {
                            text: 'Marcar Todos',
                            handler: function() {
                                for (var i = 0; i < itemsTipoNegocio.length; i++) {
                                    Ext.getCmp('idTipoNegocio_' + i).setValue(true);
                                }
                            }
                        },
                        '-',
                        {
                            text: 'Desmarcar Todos',
                            handler: function() {
                                for (var i = 0; i < itemsTipoNegocio.length; i++) {
                                    Ext.getCmp('idTipoNegocio_' + i).setValue(false);
                                }
                            }
                        }
                    ]
                }],
            listeners: {
                expand: function() {
                    if (boolNoLoadTiposNegocio)
                    {
                        storeTipoNegocio.load();
                        boolNoLoadTiposNegocio = false;
                    }
                }
            }
        },
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        {
            id: 'panelOficina',
            name: 'panelOficina',
            xtype: 'fieldset',
            title: 'Oficinas',
            colspan: 3,
            collapsible: true,
            collapsed: true,
            items: [
                {
                    xtype: 'image',
                    src: '/public/images/images_crud/ajax-loader.gif',
                    id: 'imgLoadOficina',
                    name: 'imgLoadOficina',
                    width: 32,
                    height: 32,
                    style: {
                        'display': 'block',
                        'margin': 'auto'
                    }
                },
                frameOficina,
                {
                    xtype: 'panel',
                    id: 'panelBbarOficina',
                    name: 'panelBbarOficina',
                    hidden: true,
                    buttonAlign: 'right',
                    bbar: [
                        {
                            text: 'Marcar Todos',
                            handler: function() {
                                for (var i = 0; i < itemsOficina.length; i++) {
                                    Ext.getCmp('idOficina_' + i).setValue(true);
                                }
                            }
                        },
                        '-',
                        {
                            text: 'Desmarcar Todos',
                            handler: function() {
                                for (var i = 0; i < itemsOficina.length; i++) {
                                    Ext.getCmp('idOficina_' + i).setValue(false);
                                }
                            }
                        }
                    ]
                }],
            listeners: {
                expand: function() {
                    if (boolNoLoadOficinasYFormasPago)
                    {
                        cargarOficinasYFormasPago(frameOficina, frameFormaPago);
                        boolNoLoadOficinasYFormasPago = false;
                    }
                }
            }
        },
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        {
            id: 'panelFormaPago',
            name: 'panelFormaPago',
            xtype: 'fieldset',
            title: 'Formas de Pago',
            colspan: 1,
            collapsible: true,
            collapsed: true,
            items: [
                {
                    xtype: 'image',
                    src: '/public/images/images_crud/ajax-loader.gif',
                    id: 'imgLoadFormaPago',
                    name: 'imgLoadFormaPago',
                    width: 32,
                    height: 32,
                    style: {
                        'display': 'block',
                        'margin': 'auto'
                    }
                },
                frameFormaPago
            ],
            listeners: {
                collapse: function() {
                    Ext.getCmp('panelBancoTarjeta').collapse();
                },
                expand: function() {
                    Ext.getCmp('panelBancoTarjeta').expand();
                    if (boolNoLoadOficinasYFormasPago)
                    {
                        cargarOficinasYFormasPago(frameOficina, frameFormaPago);
                        boolNoLoadOficinasYFormasPago = false;
                    }
                }

            }
        },
        {
            id: 'panelBancoTarjeta',
            name: 'panelBancoTarjeta',
            xtype: 'fieldset',
            title: 'Bancos / Tarjetas',
            colspan: 2,
            collapsible: true,
            collapsed: true,
            items: [
                {
                    xtype: 'image',
                    src: '/public/images/images_crud/ajax-loader.gif',
                    id: 'imgLoadBancoTarjeta',
                    name: 'imgLoadBancoTarjeta',
                    width: 32,
                    height: 32,
                    hidden: true,
                    style: {
                        'display': 'block',
                        'margin': 'auto'
                    }
                },
                frameBancoTarjeta,
                {
                    xtype: 'panel',
                    id: 'panelBbarBancoTarjeta',
                    name: 'panelBbarBancoTarjeta',
                    hidden: true,
                    buttonAlign: 'right',
                    bbar: [
                        {
                            text: 'Marcar Todos',
                            handler: function() {
                                for (var i = 0; i < itemsBancoTarjeta.length; i++) {
                                    Ext.getCmp('idBancoTarjeta_' + i).setValue(true);
                                }
                            }
                        },
                        '-',
                        {
                            text: 'Desmarcar Todos',
                            handler: function() {
                                for (var i = 0; i < itemsBancoTarjeta.length; i++) {
                                    Ext.getCmp('idBancoTarjeta_' + i).setValue(false);
                                }
                            }
                        }
                    ]
                }]
        },
        {html: "&nbsp;", border: false, width: 150}
    ];


    var filtrosBusqueda = Ext.create('Ext.panel.Panel', {
        id: 'filtrosBusqueda',
        bodyPadding: 5,
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
        width: 1190,
        title: 'Criterios de búsqueda',
        buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function() {
                        mostrarInfoBusquedaYConfirmacion('Consultar');
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
        items: itemsPrincipales,
        renderTo: 'divFiltrosBusqueda'
    });
    
    storePrincipal = new Ext.data.Store({
        pageSize: 100,
        total: 'intTotal',
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url: strUrlGridServicios,
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayResultado'
            }
        },
        fields:
            [
                {name: 'idServicio', mapping: 'ID_SERVICIO'},
                {name: 'idPunto', mapping: 'ID_PUNTO'},
                {name: 'idPer', mapping: 'ID_PERSONA_ROL'},
                {name: 'login', mapping: 'LOGIN'},
                {name: 'nombreCliente', mapping: 'NOMBRES_CLIENTE'},
                {name: 'oficina', mapping: 'NOMBRE_OFICINA'},
                {name: 'tipoNegocio', mapping: 'NOMBRE_TIPO_NEGOCIO'},
                {name: 'producto', mapping: 'DESCRIPCION_PRODUCTO'},
                {name: 'estadoServicio', mapping: 'ESTADO'},
            ]
    });

    gridServicios = Ext.create('Ext.grid.Panel', {
        width: '1190px',
        height: 500,
        id: 'gridServicios',
        store: storePrincipal,
        frame: false,
        setVisible: false,
        viewConfig: {enableTextSelection: true},
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            id: 'gv3',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        iconCls: 'icon-grid',
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: itemsSuperiorGrid
            }],
        columns: [
            {
                xtype: 'rownumberer',
                width: 50
            },
            {
                dataIndex: 'login',
                header: 'Login',
                width: 180,
                sortable: true
            },
            {
                dataIndex: 'nombreCliente',
                header: 'Cliente Nombre',
                width: 260,
                sortable: true
            },
            {
                dataIndex: 'oficina',
                header: 'Oficina',
                width: 220,
                sortable: true
            },
            {
                dataIndex: 'tipoNegocio',
                header: 'Tipo Negocio',
                width: 185,
                sortable: true
            },
            {
                dataIndex: 'producto',
                header: 'Producto',
                width: 185,
                sortable: true
            },
            {
                dataIndex: 'estadoServicio',
                header: 'Estado Servicio',
                width: 100,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storePrincipal,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid',
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

});


function cargarOficinasYFormasPago(frameOficina, frameFormaPago)
{
    Ext.Ajax.request({
        url: strUrlGetOficinasYFormasPago,
        method: 'post',
        success: function(response) {
            var variable = response.responseText.split("&");
            var oficinaGrupo = variable[0];
            var formaPago = variable[1];
            var registrosOficina = Ext.JSON.decode(oficinaGrupo);
            Ext.getCmp('panelOficina').setVisible(false);
            if (registrosOficina.encontrados[0].nombreOficina != "") {
                Ext.getCmp('panelOficina').setVisible(false);
                for (var i = 0; i < registrosOficina.total; i++) {
                    var cb = Ext.create('Ext.form.field.Checkbox',
                        {
                            boxLabel: registrosOficina.encontrados[i].nombreOficina,
                            inputValue: registrosOficina.encontrados[i].idOficina,
                            id: 'idOficina_' + i,
                            name: 'oficina'
                        });
                    frameOficina.add(cb);
                    itemsOficina[i] = cb;
                }
                Ext.getCmp('panelBbarOficina').setVisible(true);
                Ext.getCmp('imgLoadOficina').setVisible(false);
            }
            Ext.getCmp('panelOficina').setVisible(true);


            var registrosFormasPago = Ext.JSON.decode(formaPago);
            Ext.getCmp('panelFormaPago').setVisible(false);
            if (registrosFormasPago.encontrados[0].descripcionFormaPago != "") {
                Ext.getCmp('panelFormaPago').setVisible(false);
                for (var i = 0; i < registrosFormasPago.total; i++) {
                    var rb = Ext.create('Ext.form.field.Radio',
                        {
                            boxLabel: registrosFormasPago.encontrados[i].descripcionFormaPago,
                            inputValue: registrosFormasPago.encontrados[i].idFormaPago,
                            id: 'idFormaPago_' + i,
                            name: 'formaPago'
                        });
                    frameFormaPago.add(rb);
                    itemsFormaPago[i] = rb;
                }
                Ext.getCmp('imgLoadFormaPago').setVisible(false);
            }
            Ext.getCmp('panelFormaPago').setVisible(true);
        }
    });
}

function mostrarInfoBusquedaYConfirmacion(accion)
{
    var strInfoFiltros = "";
    strHtmlInfoBusqueda = "";
    
    var boolOKValidacionBusq    = true;
    var fieldFechaDesdeFactura  = Ext.getCmp('cmbFechaDesdeFactura');
    valFechaDesdeFactura        = fieldFechaDesdeFactura.getSubmitValue();
    var fieldFechaHastaFactura  = Ext.getCmp('cmbFechaHastaFactura');
    valFechaHastaFactura        = fieldFechaHastaFactura.getSubmitValue();
    
    if(valFechaDesdeFactura && valFechaHastaFactura)
    {
        var valCompFechaDesdeFactura = Ext.Date.parse(valFechaDesdeFactura, "Y-m-d");
        var valCompFechaHastaFactura = Ext.Date.parse(valFechaHastaFactura, "Y-m-d");

        if ((isNaN(fieldFechaDesdeFactura.value) || isNaN(fieldFechaHastaFactura.value)) || 
            (fieldFechaDesdeFactura.value==="" || fieldFechaHastaFactura.value==="" ))
        {
            boolOKValidacionBusq = false;
            Ext.Msg.alert('Error', 'Los campos de las fechas no pueden estar vacías');
        }
        else if(valCompFechaDesdeFactura>valCompFechaHastaFactura)
        {
            boolOKValidacionBusq = false;
            Ext.Msg.alert('Error', 'La Fecha Desde '+ valFechaDesdeFactura +' no puede ser mayor a la Fecha Hasta '+valFechaHastaFactura); 
        }
        else if(valCompFechaDesdeFactura.getTime()  == valCompFechaHastaFactura.getTime() )
        {
            boolOKValidacionBusq = false;
            Ext.Msg.alert('Error', 'La Fecha Desde y la Fecha Hasta no pueden ser iguales'); 
        }
    }
    
    if(boolOKValidacionBusq)
    {
        if(Ext.getCmp('cmbGrupos').value)
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Grupo:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('cmbGrupos').value+"</td>"
                              +"</tr>";
        }


        if(Ext.getCmp('cmbSubgrupos').value)
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Subgrupo:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('cmbSubgrupos').value+"</td>"
                              +"</tr>";
        }

        if(!isNaN(parseInt(Ext.getCmp('cmbElementosNodo').value)))
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Nodo:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('cmbElementosNodo').getRawValue()+"</td>"
                              +"</tr>";
        }

        if(!isNaN(parseInt(Ext.getCmp('cmbElementosSwitch').value)))
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Switch:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('cmbElementosSwitch').getRawValue()+"</td>"
                              +"</tr>";
        }
        
        if(Ext.getCmp('cmbEstadosCliente').value)
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Estado del Cliente:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('cmbEstadosCliente').value+"</td>"
                              +"</tr>";
        }
        
        var valorClienteVIPSelected     = "";
        var valorUserClienteVIPSelected = "";
        for (var i = 1; i <= itemsSI_NO.length; i++) {
            clientesVIPSeteada = Ext.getCmp('rbClientesVIP_' + i).value;
            if (clientesVIPSeteada == true) {
                if (valorClienteVIPSelected != null && valorClienteVIPSelected == "") {
                    valorClienteVIPSelected     = Ext.getCmp('rbClientesVIP_' + i).inputValue;
                    valorUserClienteVIPSelected = Ext.getCmp('rbClientesVIP_' + i).boxLabel;
                }
            }
        }
        if(valorClienteVIPSelected != "")
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Clientes VIP:&nbsp;</b></td>"
                                +"<td>"+valorUserClienteVIPSelected+"</td>"
                              +"</tr>";
        }
        valorClienteVIP = valorClienteVIPSelected;
        

        if(Ext.getCmp('cmbEstadosPunto').value)
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Estado del Punto:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('cmbEstadosPunto').value+"</td>"
                              +"</tr>";
        }

        if(Ext.getCmp('cmbEstadosServicio').value)
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Estado del Servicio:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('cmbEstadosServicio').value+"</td>"
                              +"</tr>";
        }

        if(Ext.getCmp('cmbTiposFactura').value)
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Tipo de Factura:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('cmbTiposFactura').getRawValue()+"</td>"
                              +"</tr>";
        }


        if(!isNaN(parseInt(Ext.getCmp('facturasAbiertas').value)))
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Facts. Min. Abiertas:&nbsp;</b></td>"
                                +"<td>"+Ext.getCmp('facturasAbiertas').value+"</td>"
                              +"</tr>";
        }
        
        if(Ext.getCmp('cbPuntosFacturacion').value)
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Sólo Puntos de Facturación:&nbsp;</b></td>"
                                +"<td>SI</td>"
                              +"</tr>";
        }
        
        if(valFechaDesdeFactura != "")
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Facturas desde:&nbsp;</b></td>"
                                +"<td>"+valFechaDesdeFactura+"</td>"
                              +"</tr>";
        }
        
        if(valFechaHastaFactura != "")
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Facturas hasta:&nbsp;</b></td>"
                                +"<td>"+valFechaHastaFactura+"</td>"
                              +"</tr>";
        }
        
        if(Ext.getCmp('cbSaldoPendientePago').value)
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Pendiente de Pago:&nbsp;</b></td>"
                                +"<td>SI</td>"
                              +"</tr>";
                          
            if(Ext.getCmp('valorSaldoPendientePago').value)
            {      
                strInfoFiltros += "<tr>"
                                    +"<td><b>Valor Mínimo Pendiente:&nbsp;</b></td>"
                                    +"<td>"+Ext.getCmp('valorSaldoPendientePago').value+"</td>"
                                  +"</tr>";
            }       
        }

        var idsTiposNegocioSelected     = "";
        var nombresTiposNegocioSelected = "";
        if (itemsTipoNegocio) {
            for (var i = 0; i < itemsTipoNegocio.length; i++) {
                tiposNegocioSeteada = Ext.getCmp('idTipoNegocio_' + i).value;
                if (tiposNegocioSeteada == true) {
                    if (idsTiposNegocioSelected != null && idsTiposNegocioSelected == "") {
                        idsTiposNegocioSelected     = idsTiposNegocioSelected + Ext.getCmp('idTipoNegocio_' + i).inputValue;
                        nombresTiposNegocioSelected = nombresTiposNegocioSelected + Ext.getCmp('idTipoNegocio_' + i).boxLabel;
                    } else {
                        idsTiposNegocioSelected     = idsTiposNegocioSelected + ",";
                        idsTiposNegocioSelected     = idsTiposNegocioSelected + Ext.getCmp('idTipoNegocio_' + i).inputValue;
                        nombresTiposNegocioSelected = nombresTiposNegocioSelected + ", ";
                        nombresTiposNegocioSelected = nombresTiposNegocioSelected + Ext.getCmp('idTipoNegocio_' + i).boxLabel;
                    }
                }
            }
        }
        if(nombresTiposNegocioSelected !== "")
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Tipos de Negocio:&nbsp;</b></td>"
                                +"<td>"+nombresTiposNegocioSelected+"</td>"
                              +"</tr>";
        }
        idsTiposNegocioGlobal = idsTiposNegocioSelected;



        var idsOficinasSelected     = "";
        var nombresOficinasSelected = "";
        if (itemsOficina) {
            for (var i = 0; i < itemsOficina.length; i++) {
                oficinasSeteada = Ext.getCmp('idOficina_' + i).value;
                if (oficinasSeteada == true) {
                    if (idsOficinasSelected != null && idsOficinasSelected == "") {
                        idsOficinasSelected     = idsOficinasSelected + Ext.getCmp('idOficina_' + i).inputValue;
                        nombresOficinasSelected = nombresOficinasSelected + Ext.getCmp('idOficina_' + i).boxLabel;
                    } else {
                        idsOficinasSelected     = idsOficinasSelected + ",";
                        idsOficinasSelected     = idsOficinasSelected + Ext.getCmp('idOficina_' + i).inputValue;
                        nombresOficinasSelected = nombresOficinasSelected + ", ";
                        nombresOficinasSelected = nombresOficinasSelected + Ext.getCmp('idOficina_' + i).boxLabel;
                    }
                }
            }
        }
        if(nombresOficinasSelected !== "")
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Oficinas:&nbsp;</b></td>"
                                +"<td>"+nombresOficinasSelected+"</td>"
                              +"</tr>";
        }
        idsOficinasGlobal = idsOficinasSelected;



        var idFormaPagoSelected         = "";
        var nombreFormaPagoSelected     = "";
        for (var i = 0; i < itemsFormaPago.length; i++) {
            formaPagoSeteada = Ext.getCmp('idFormaPago_' + i).value;
            if (formaPagoSeteada == true) {
                if (idFormaPagoSelected != null && idFormaPagoSelected == "") {
                    idFormaPagoSelected         = idFormaPagoSelected + Ext.getCmp('idFormaPago_' + i).inputValue;
                    nombreFormaPagoSelected     = nombreFormaPagoSelected + Ext.getCmp('idFormaPago_' + i).boxLabel;
                }
            }
        }
        if(nombreFormaPagoSelected !== "")
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Forma de Pago:&nbsp;</b></td>"
                                +"<td>"+nombreFormaPagoSelected+"</td>"
                              +"</tr>";
        }
        idFormaPagoGlobal = idFormaPagoSelected;

        var idsBancosTarjetasSelected       = "";
        var nombresBancosTarjetasSelected   = "";
        if (idFormaPagoGlobal != "") {
            if (itemsBancoTarjeta) {
                for (var i = 0; i < itemsBancoTarjeta.length; i++) {
                    bancosTarjetasSeteada = Ext.getCmp('idBancoTarjeta_' + i).value;
                    if (bancosTarjetasSeteada == true) {
                        if (idsBancosTarjetasSelected != null
                            && idsBancosTarjetasSelected == "") {
                            idsBancosTarjetasSelected       = idsBancosTarjetasSelected + Ext.getCmp('idBancoTarjeta_' + i).inputValue;
                            nombresBancosTarjetasSelected   = nombresBancosTarjetasSelected + Ext.getCmp('idBancoTarjeta_' + i).boxLabel;
                        } else {
                            idsBancosTarjetasSelected       = idsBancosTarjetasSelected + ",";
                            idsBancosTarjetasSelected       = idsBancosTarjetasSelected + Ext.getCmp('idBancoTarjeta_' + i).inputValue;
                            nombresBancosTarjetasSelected   = nombresBancosTarjetasSelected + ",";
                            nombresBancosTarjetasSelected   = nombresBancosTarjetasSelected + Ext.getCmp('idBancoTarjeta_' + i).boxLabel;
                        }
                    }
                }
            }
        }
        if(nombresBancosTarjetasSelected !== "")
        {
            strInfoFiltros += "<tr>"
                                +"<td><b>Bancos/Tarjetas:&nbsp;</b></td>"
                                +"<td>"+nombresBancosTarjetasSelected+"</td>"
                              +"</tr>";
        }
        idsBancosTarjetasGlobal = idsBancosTarjetasSelected;
        if(strInfoFiltros !== "")
        {
            strHtmlInfoBusqueda = "<table>"
                                      +"<tr><b>Filtros Seleccionados</b></tr>"
                                      + strInfoFiltros
                                      + "</table>";
        }
        
        if(accion === "Consultar")
        {
            if(strHtmlInfoBusqueda !== "")
            {
                Ext.Msg.confirm('Alerta', strHtmlInfoBusqueda+"<br/>¿Está seguro de realizar la búsqueda?", function(btn) {
                    if (btn == 'yes') {
                        buscar();
                    }
                });
            }
            else
            {
                Ext.Msg.confirm('Alerta', "Usted no ha seleccionado filtros!<br/>¿Está seguro de realizar la búsqueda?", function(btn) {
                    if (btn == 'yes') {
                        buscar();
                    }
                });
            }
        }
        else if(accion === "Exportar")
        {
            document.getElementById('filtrosSeleccionadosExcel').value = strInfoFiltros;
        }
    }
    else
    {
        storePrincipal.removeAll();
    }
}

function buscar() {
    extraParamsBusqueda = {
        grupo: Ext.getCmp('cmbGrupos').value,
        subgrupo: Ext.getCmp('cmbSubgrupos').value,
        idElementoNodo: Ext.getCmp('cmbElementosNodo').value,
        idElementoSwitch: Ext.getCmp('cmbElementosSwitch').value,
        estadoServicio: Ext.getCmp('cmbEstadosServicio').value,
        estadoPunto: Ext.getCmp('cmbEstadosPunto').value,
        estadoCliente: Ext.getCmp('cmbEstadosCliente').value,
        clientesVIP: valorClienteVIP,
        usrCreacionFactura: Ext.getCmp('cmbTiposFactura').value,
        numFacturasAbiertas: Ext.getCmp('facturasAbiertas').value,
        puntosFacturacion: Ext.getCmp('cbPuntosFacturacion').value ? 'S' : '',
        idsTiposNegocio: idsTiposNegocioGlobal,
        idsOficinas: idsOficinasGlobal,
        idFormaPago: idFormaPagoGlobal,
        idsBancosTarjetas: idsBancosTarjetasGlobal,
        fechaDesdeFactura: valFechaDesdeFactura,
        fechaHastaFactura: valFechaHastaFactura,
        saldoPendientePago: Ext.getCmp('cbSaldoPendientePago').value ? 'S' : '', 
        valorSaldoPendientePago: Ext.getCmp('valorSaldoPendientePago').value
    };
    storePrincipal.loadData([],false);
    storePrincipal.currentPage = 1;
    storePrincipal.getProxy().extraParams = extraParamsBusqueda;
    storePrincipal.load();
}


function limpiar() {
    extraParamsBusqueda     = null;
    extraParamsEnvioMasivo  = null;
    Ext.getCmp('cmbGrupos').value = "";
    Ext.getCmp('cmbGrupos').setRawValue("");
    Ext.getCmp('cmbSubgrupos').value = "";
    Ext.getCmp('cmbSubgrupos').setRawValue("");

    Ext.getCmp('cmbElementosNodo').value = "";
    Ext.getCmp('cmbElementosNodo').setRawValue("");
    storeElementosSwitch.proxy.extraParams = {
        intIdElementoNodo: 0
    };
    storeElementosSwitch.load();
    Ext.getCmp('cmbElementosSwitch').value = "";
    Ext.getCmp('cmbElementosSwitch').setRawValue("");

    Ext.getCmp('cmbEstadosServicio').value = "";
    Ext.getCmp('cmbEstadosServicio').setRawValue("");
    
    Ext.getCmp('cmbEstadosPunto').value = "";
    Ext.getCmp('cmbEstadosPunto').setRawValue("");
    
    Ext.getCmp('cmbEstadosCliente').value = "";
    Ext.getCmp('cmbEstadosCliente').setRawValue("");
    
    valorClienteVIP = "";
    for (var i = 1; i <= itemsSI_NO.length; i++) {
        Ext.getCmp('rbClientesVIP_' + i).setValue(false);
    }

    Ext.getCmp('cmbTiposFactura').value = "";
    Ext.getCmp('cmbTiposFactura').setRawValue("");

    Ext.getCmp('facturasAbiertas').reset();
    Ext.getCmp('cbPuntosFacturacion').setValue(false);
    
    valFechaDesdeFactura    = "";
    valFechaHastaFactura    = "";
    Ext.getCmp('cmbFechaDesdeFactura').setValue('');
    Ext.getCmp('cmbFechaHastaFactura').setValue('');
    Ext.getCmp('cbSaldoPendientePago').setValue(false);    
    Ext.getCmp('valorSaldoPendientePago').reset();

    idsTiposNegocio = "";
    for (var i = 0; i < itemsTipoNegocio.length; i++) {
        Ext.getCmp('idTipoNegocio_' + i).setValue(false);
    }
    idsOficinasGlobal = "";
    for (var i = 0; i < itemsOficina.length; i++) {
        Ext.getCmp('idOficina_' + i).setValue(false);
    }
    idFormaPagoGlobal = "";
    boolNoLimpiarFormasPago = false;
    for (var i = 0; i < itemsFormaPago.length; i++) {
        Ext.getCmp('idFormaPago_' + i).setValue(false);
    }
    idsBancosTarjetasGlobal = "";
    for (var i = 0; i < itemsBancoTarjeta.length; i++) {
        Ext.getCmp('idBancoTarjeta_' + i).setValue(false);
    }
    Ext.get('imgLoadBancoTarjeta').setStyle('display', 'none');

    storePrincipal.loadData([],false);
    storePrincipal.currentPage = 1;
}



function showConfigurarEnvioPlantilla()
{
    winConfigEnvioMasivo         = "";
    formPanelConfigEnvioMasivo   = "";

    if (!winConfigEnvioMasivo)
    {
        var htmlFormasEnvio = 'Formas de Envío: ' +
                              '&nbsp;&nbsp;&nbsp;&nbsp;' +
                              '<label>&nbsp;EMAIL&nbsp;&nbsp;&nbsp;</label>';

        cHtmlRadiosFormasEnvio = Ext.create('Ext.Component', {
            html: htmlFormasEnvio,
            width: 400,
            padding: 4,
            style: {color: '#000000'}
        });

        var storePlantillasCorreo = Ext.create('Ext.data.Store', {
            id: 'storePlantillas',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: strUrlGetPlantillasEnvioMasivo,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name: 'idPlantilla', mapping: 'id_plantilla'},
                {name: 'nombrePlantilla', mapping: 'nombre_plantilla'}
            ]
        });
        
        comboPlantillasCorreo = new Ext.form.ComboBox({
            id: 'cmbPlantillaEmail',
            name: 'cmbPlantillaEmail',
            fieldLabel: "Plantillas Correo",
            anchor: '100%',
            queryMode: 'remote',
            width: 250,
            padding: 4,
            emptyText: 'Seleccione Plantilla Correo',
            store: storePlantillasCorreo,
            displayField: 'nombrePlantilla',
            valueField: 'idPlantilla',
            layout: 'anchor',
            disabled: false,
            forceSelection: true,
            allowBlank: false
        });
        
        itemsPlantillas =
            {
                layout: 'table',
                border: false,
                items:
                    [
                        {
                            layout: 'form',
                            border: false,
                            width: 380,
                            items:
                                [
                                    comboPlantillasCorreo
                                ]
                        },
                        {
                            layout: 'form',
                            border: false,
                            items:
                                [
                                    {
                                        xtype: 'displayfield',
                                        value: "&nbsp;&nbsp;&nbsp;"
                                    }
                                ]
                        },
                        {
                            layout: 'form',
                            border: false,
                            items:
                                [
                                    {
                                        xtype: 'button',
                                        tooltip: 'Ver Plantilla',
                                        cls: 'imgVerPlantilla',
                                        handler: function() {
                                            verPlantilla();
                                        }
                                    }
                                ]
                        }
                    ]
            };
        
        txtAreaAsunto = new Ext.form.TextArea({
            fieldLabel: 'Asunto',
            height: 30,
            width: 300,
            anchor: '100%',
            maxLength:78,
            enforceMaxLength : true,
            submitValue: false,
            autoScroll: true,
            id: 'txtAreaAsunto',
            name: 'txtAreaAsunto'
        });

        var storeTiposContacto = Ext.create('Ext.data.Store', {
            id: 'storePlantillas',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: strUrlGetAdmiRolbyTipoRol,
                reader: {
                    type: 'json',
                    root: 'registros'
                },
                extraParams: {
                    strAppendRol: 'Todos',
                    strComparadorEmpRol: 'NOT IN',
                    strComparadorEmpRolDis: 'NOT IN',
                    strComparadorEstRol: 'NOT IN',
                    strComparadorEstTipoRol: 'NOT IN',
                    strComparadorPerEmpRolDis: 'NOT IN',
                    strDescripcionTipoRol: 'Contacto',
                    strEstadoEmpRolDis: 'Eliminado, Anulado, Inactivo',
                    strEstadoEmpresaRol: 'Eliminado, Anulado, Inactivo',
                    strEstadoPerEmpRolDis: 'Eliminado, Anulado, Inactivo',
                    strEstadoRol: 'Eliminado, Anulado, Inactivo',
                    strEstadoTipoRol: 'Eliminado, Anulado, Inactivo'
                }
            },
            fields: 
            [
                {name: 'intIdRol', type: 'int'},
                {name: 'intIdEmpresaRol', type: 'int'},
                {name: 'strDescripcionRol', type: 'string'},
                {name: 'strEstado', type: 'string'}
            ]
        });
        
        Ext.define('cboSelectedCountTipoContacto', {
            alias: 'plugin.selectedAdmiRol',
            init: function(cboSelectedCountTipoContacto) {
                cboSelectedCountTipoContacto.on({
                    select: function(me, objRecords) {
                        intNumeroRegistros = objRecords.length;
                        storeCboSelectedCountTipoContacto = cboSelectedCountTipoContacto.getStore();
                        boolDiffRowCbo = objRecords.length !== storeCboSelectedCountTipoContacto.count;
                        boolNewAll = false;
                        boolSelectedAll = false;
                        objNewRecords = [];
                        Ext.each(objRecords, function(obj, i, objRecordsItself) {
                            if (objRecords[i].data.intIdEmpresaRol === 0) {
                                boolSelectedAll = true;
                                if (!cboSelectedCountTipoContacto.boolCboSelectedAll) {
                                    intNumeroRegistros = storeCboSelectedCountTipoContacto.getCount();
                                    cboSelectedCountTipoContacto.select(storeCboSelectedCountTipoContacto.getRange());
                                    cboSelectedCountTipoContacto.boolCboSelectedAll = true;
                                    boolNewAll = true;
                                }
                            } else {
                                if (boolDiffRowCbo && !boolNewAll)
                                    objNewRecords.push(objRecords[i]);
                            }
                        });
                        if (cboSelectedCountTipoContacto.boolCboSelectedAll && !boolSelectedAll) {
                            cboSelectedCountTipoContacto.clearValue();
                            cboSelectedCountTipoContacto.boolCboSelectedAll = false;
                        } else if (boolDiffRowCbo && !boolNewAll) {
                            cboSelectedCountTipoContacto.select(objNewRecords);
                            cboSelectedCountTipoContacto.boolCboSelectedAll = false;
                        }
                    }
                });
            }
        });
        comboTiposContacto = new Ext.form.ComboBox({
            disabled: false,
            multiSelect: true,
            plugins: ['selectedAdmiRol'],
            id: 'cmbTiposContacto',
            fieldLabel: 'Tipo de Contacto',
            padding: 4,
            store: storeTiposContacto,
            queryMode: 'local',
            editable: false,
            displayField: 'strDescripcionRol',
            valueField: 'intIdEmpresaRol',
            width: 350,
            displayTpl: '<tpl for="."> { strDescripcionRol } <tpl if="xindex < xcount">, </tpl> </tpl>',
            allowBlank: false,
            listConfig: {
                itemTpl: '{ strDescripcionRol} <div class="uncheckedChkbox"></div>'
            }
        });
        
        var htmlRadiosTipoEnvio =   'Tipo de Envío: ' +
                                    '&nbsp;&nbsp;&nbsp;&nbsp;' +
                                    '<input type="radio" checked="" value="INMEDIATO" name="tipoEnvio" '+
                                    'onchange="cambiarTipoEnvio(this.value);">&nbsp;Inmediato' +
                                    '&nbsp;&nbsp;' +
                                    '<input type="radio" value="PROGRAMADO" name="tipoEnvio" '+
                                    'onchange="cambiarTipoEnvio(this.value);">&nbsp;Programado'+
                                    '&nbsp;&nbsp;' +
                                    '<input type="radio" value="RECURRENTE" name="tipoEnvio" '+
                                    'onchange="cambiarTipoEnvio(this.value);">&nbsp;Recurrente';
                                
        cHtmlRadiosTipoEnvio = Ext.create('Ext.Component', {
            html: htmlRadiosTipoEnvio,
            width: 400,
            padding: 4,
            style: {color: '#000000'}
        });
        
        combosDatetimeProgramado = Ext.create('widget.datetimefield',
            {
                id: 'cmbDatetimeProgramado',
                name: 'cmbDatetimeProgramado',
                fieldLabel: 'Fecha y Hora Programada',
                width: 300,
                margin: 2,
                labelStyle: 'padding:4px;',
                dateConfig:
                    {
                        minValue: new Date()
                    }
            }
        );
        combosDatetimeProgramado.setValue(new Date());
        Ext.getCmp('cmbDatetimeProgramado').hide();
        
        cmbFechaEjecucionDesde = new Ext.form.DateField({
            id: 'cmbFechaEjecucionDesde',
            name: 'cmbFechaEjecucionDesde',
            fieldLabel: 'Ejecutar desde',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            padding: 4,
			minValue: new Date(),
            value:new Date(),
            emptyText: "Seleccione...",
            allowBlank: false,
            labelStyle: 'padding:4px;'
        });
        Ext.getCmp('cmbFechaEjecucionDesde').hide();
        
        cmbHoraEjecucion = new Ext.form.TimeField({
            id: 'cmbHoraEjecucion',
            name: 'cmbHoraEjecucion',
            fieldLabel: 'Hora de Ejecución',
            xtype: 'timefield',
            format: 'H:i',
            padding: 4,
            value: new Date(),
            emptyText: "Seleccione...",
            allowBlank: false,
            labelStyle: 'text-align:right;padding:4px;'
        });
        Ext.getCmp('cmbHoraEjecucion').hide();
        
        
        
        itemsFechaDesdeYHoraEjecucion =
            {
                layout: 'table',
                border: false,
                items:
                    [
                        {
                            layout: 'form',
                            border: false,
                            width: 250,
                            items:
                                [
                                    cmbFechaEjecucionDesde
                                ]
                        },
                        {
                            layout: 'form',
                            border: false,
                            items:
                                [
                                    {
                                        xtype: 'displayfield',
                                        value: "&nbsp;&nbsp;&nbsp;"
                                    }
                                ]
                        },
                        {
                            layout: 'form',
                            border: false,
                            width: 200,
                            items:
                                [
                                    cmbHoraEjecucion
                                ]
                        }
                    ]
            };
        
        Ext.define('ModelStorePeriodicidad',
            {
                extend: 'Ext.data.Model',
                fields:
                    [
                        {name: 'id', type: 'string', convert: function(value, record)
                            {
                                return record.get('valor1') + '_' + record.get('valor3');
                            }
                        },
                        {name:'valorUsuario',   mapping:'valor2'},
                        {name:'valor1',         mapping:'valor1'},
                        {name:'valor3',         mapping:'valor3'}
                    ],
                idProperty: 'id'
            });
        
        var storePeriodicidad = Ext.create('Ext.data.Store', {
            id: 'storePeriodicidad',
            model: 'ModelStorePeriodicidad',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: strUrlGetPeriodicidades,
                reader: {
                    type: 'json',
                    root: 'arrayRegistros'
                }
            }
        });

        comboPeriodicidades = new Ext.form.ComboBox({
            id: 'cmbPeriodicidad',
            name: 'cmbPeriodicidad',
            fieldLabel: "Periodicidad",
            queryMode: 'local',
            labelStyle: 'padding:4px;',
            emptyText: 'Seleccione...',
            store: storePeriodicidad,
            displayField: 'valorUsuario',
            allowBlank: false,
            valueField: 'id',
            layout: 'anchor',
            disabled: false,
            forceSelection: true,
            editable: false,
            listeners:{
                select: function() {
                    var valorPeriodicidadDias = Ext.getCmp("cmbPeriodicidad").getValue();
                    var periodicidadDias = valorPeriodicidadDias.split("_");
                    if (periodicidadDias[1] === 'SI')
                    {
                        Ext.getCmp('nfDiaMesEjecucion').show();
                    }
                    else
                    {
                        Ext.getCmp('nfDiaMesEjecucion').hide();
                    }
                }
            }
        });
        
        Ext.getCmp('cmbPeriodicidad').hide();
        
        nfDiaMesEjecucion = new Ext.form.NumberField({
            xtype: 'numberfield',
            fieldLabel: '# Día',
            id: 'nfDiaMesEjecucion',
            name: 'nfDiaMesEjecucion',
            minValue: 1,
            maxValue: 31,
            margin: 4,
            padding: 4,
            allowDecimals: false,
            allowBlank: false,
            decimalPrecision: 2,
            step: 1,
            emptyText: 'Rango (1-31)',
            layout: 'anchor',
            labelStyle: 'text-align:right;'
        });
        Ext.getCmp('nfDiaMesEjecucion').hide();
        
        itemsPeriodicidadYDias =
            {
                layout: 'table',
                border: false,
                items:
                    [
                        {
                            layout: 'form',
                            border: false,
                            width: 250,
                            items:
                                [
                                    comboPeriodicidades
                                ]
                        },
                        {
                            layout: 'form',
                            border: false,
                            items:
                                [
                                    {
                                        xtype: 'displayfield',
                                        value: "&nbsp;&nbsp;&nbsp;"
                                    }
                                ]
                        },
                        {
                            layout: 'form',
                            border: false,
                            width: 200,
                            items:
                                [
                                    nfDiaMesEjecucion
                                ]
                        }
                    ]
            };

        formPanelConfigEnvioMasivo = Ext.create('Ext.form.Panel', {
            width: 450,
            bodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [cHtmlRadiosFormasEnvio, 
                    itemsPlantillas,
                    txtAreaAsunto,
                    comboTiposContacto,
                    cHtmlRadiosTipoEnvio,
                    combosDatetimeProgramado,
                    itemsFechaDesdeYHoraEjecucion,
                    itemsPeriodicidadYDias
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        guardarEnvioMasivoPlantilla();
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function() {
                        winConfigEnvioMasivo.close();
                        winConfigEnvioMasivo.destroy();
                    }
                }
            ]
        });

        winConfigEnvioMasivo = Ext.widget('window', {
            title: 'Configurar Envío Masivo',
            width: 500,
            layout: 'fit',
            modal: true,
            closabled: true,
            items: [formPanelConfigEnvioMasivo]
        });
    }
    winConfigEnvioMasivo.show();
}

function cambiarTipoEnvio(tipoEnvio)
{
    if(tipoEnvio === "PROGRAMADO")
    {
        Ext.getCmp('cmbDatetimeProgramado').show();
        Ext.getCmp('cmbFechaEjecucionDesde').hide();
        Ext.getCmp('cmbHoraEjecucion').hide();
        Ext.getCmp('cmbPeriodicidad').hide();
	}
    else if(tipoEnvio === "RECURRENTE")
    {
        Ext.getCmp('cmbDatetimeProgramado').hide();
        Ext.getCmp('cmbFechaEjecucionDesde').show();
        Ext.getCmp('cmbHoraEjecucion').show();
        Ext.getCmp('cmbPeriodicidad').show();
        
    }
	else
	{
        Ext.getCmp('cmbDatetimeProgramado').hide();
        Ext.getCmp('cmbFechaEjecucionDesde').hide();
        Ext.getCmp('cmbHoraEjecucion').hide();
        Ext.getCmp('cmbPeriodicidad').hide();
	}
    Ext.getCmp('nfDiaMesEjecucion').hide();
}


function guardarEnvioMasivoPlantilla() 
{
    var boolOKValidacion            = true;
    var idPlantillaEmail            = Ext.getCmp('cmbPlantillaEmail').value;
    var idsTipoContacto             = Ext.getCmp('cmbTiposContacto').getValue().toString();
    var tipoEnvio                   = $("input[name='tipoEnvio']:checked").val();
    var fechaHoraProgramada         = Ext.getCmp('cmbDatetimeProgramado').getSubmitValue();
    var fechaEjecucionDesde         = Ext.getCmp('cmbFechaEjecucionDesde').getSubmitValue();
    var horaEjecucion               = Ext.getCmp('cmbHoraEjecucion').getValue();
    var periodicidadYMostrarDias    = Ext.getCmp('cmbPeriodicidad').value;
    var numeroDia                   = Ext.getCmp('nfDiaMesEjecucion').value;
    var asunto                      = Ext.getCmp('txtAreaAsunto').getValue();
    
    
    if (Ext.isEmpty(idPlantillaEmail)) {
        Ext.Msg.alert('Error', 'Debe elegir la plantilla que desea enviar');
        boolOKValidacion = false;
    }
    else if (Ext.isEmpty(idsTipoContacto)) {
        Ext.Msg.alert('Error', 'Debe elegir al menos un tipo de contacto');
        boolOKValidacion = false;
    }
    else if (Ext.isEmpty(asunto)) {
        Ext.Msg.alert('Error', 'Debe colocar el asunto del envío');
        boolOKValidacion = false;
    }
    else if (Ext.isEmpty(tipoEnvio)) {
        Ext.Msg.alert('Error', 'Debe elegir un tipo de envío');
        boolOKValidacion = false;
    }
    else
    {
        if(tipoEnvio==="PROGRAMADO" && Ext.isEmpty(fechaHoraProgramada))
        {
            Ext.Msg.alert('Error', 'Por favor seleccione la fecha y hora programada para el envío');
            boolOKValidacion = false;
        }
        else if(tipoEnvio==="RECURRENTE")
        {
            if (Ext.isEmpty(fechaEjecucionDesde)) {
                Ext.Msg.alert('Error', 'Por favor seleccione la fecha desde que se ejecutará el envío');
                boolOKValidacion = false;
            }
            else if (Ext.isEmpty(horaEjecucion)) {
                Ext.Msg.alert('Error', 'Por favor seleccione la hora en la que se ejecutará el envío');
                boolOKValidacion = false;
            }
            else if (Ext.isEmpty(periodicidadYMostrarDias)) {
                Ext.Msg.alert('Error', 'Por favor seleccione la periodicidad en la que se ejecutará el envío');
                boolOKValidacion = false;
            }
            else if (Ext.getCmp('cmbPeriodicidad').getRawValue() === "Mensual" && Ext.isEmpty(numeroDia)) {
                Ext.Msg.alert('Error', 'Por favor seleccione el día del mes en que se ejecutará el envío');
                boolOKValidacion = false;
            }
        }
    }
    
    if(boolOKValidacion)
    {
        Ext.MessageBox.show({
            title: 'Guardando Envío Masivo',
            progressText: 'Guardando el Envío Masivo...Por favor espere!',
            width: 300,
            height:100,
            wait: true,
            waitConfig: {interval: 200},
            icon: 'ext-mb-download',
            animEl: 'buttonID',
            progress: true,
            closable: false
        });
        Ext.Ajax.request({
            url: strUrlValidacionEnvioMasivo,
            method: 'post',
            params: {
                        intIdPlantilla: idPlantillaEmail,
                        strTipoEnvio: tipoEnvio
                    },
            success: function(response) {
                var json = Ext.JSON.decode(response.responseText);

                if (json.strStatus === "OK")
                {
                    horaEjecucion = Ext.Date.format(horaEjecucion, 'H:i');

                    var cerrarMensajeEspera = function() {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: 'Result',
                            width: 300,
                            msg: 'Envío masivo guardado correctamente',
                            buttons: Ext.MessageBox.OK
                        });
                    };

                    extraParamsEnvioMasivo = {
                        idPlantilla: idPlantillaEmail,
                        idsTipoContacto: idsTipoContacto,
                        asunto: asunto,
                        tipoEnvio: tipoEnvio,
                        fechaHoraProgramada: fechaHoraProgramada,
                        fechaEjecucionDesde: fechaEjecucionDesde,
                        horaEjecucion: horaEjecucion,
                        periodicidad: periodicidadYMostrarDias,
                        numeroDia: numeroDia,
                        infoBusqueda : strHtmlInfoBusqueda
                    };

                    Ext.Ajax.request({
                        url: strUrlGuardarEnvioMasivo,
                        method: 'post',
                        params: Ext.merge(extraParamsBusqueda, extraParamsEnvioMasivo),
                        success: function(response) {
                            cerrarMensajeEspera();
                            var json = Ext.JSON.decode(response.responseText);

                            if (json.strStatus === "OK")
                            {
                                Ext.Msg.alert('Mensaje ', json.strMensaje);
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', json.strMensaje);
                            }
                            winConfigEnvioMasivo.close();
                            winConfigEnvioMasivo.destroy();
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Error ', json.strMensaje);
                }
                winConfigEnvioMasivo.close();
                winConfigEnvioMasivo.destroy();
            }
        });
    }
}

function exportarExcel() {
    $('#grupoExcel').val(Ext.getCmp('cmbGrupos').value);
    $('#subgrupoExcel').val(Ext.getCmp('cmbSubgrupos').value);
    $('#idElementoNodoExcel').val(Ext.getCmp('cmbElementosNodo').value);
    $('#idElementoSwitchExcel').val(Ext.getCmp('cmbElementosSwitch').value);
    $('#estadoServicioExcel').val(Ext.getCmp('cmbEstadosServicio').value);
    $('#estadoPuntoExcel').val(Ext.getCmp('cmbEstadosPunto').value);
    $('#estadoClienteExcel').val(Ext.getCmp('cmbEstadosCliente').value);
    $('#clientesVIPExcel').val(valorClienteVIP);
    $('#usrCreacionFacturaExcel').val(Ext.getCmp('cmbTiposFactura').value);
    $('#numFacturasAbiertasExcel').val(Ext.getCmp('facturasAbiertas').value);
    $('#puntosFacturacionExcel').val(Ext.getCmp('cbPuntosFacturacion').value ? 'S' : '');
    $('#idsTiposNegocioExcel').val(idsTiposNegocioGlobal);
    $('#idsOficinasExcel').val(idsOficinasGlobal);
    $('#idFormaPagoExcel').val(idFormaPagoGlobal);
    $('#idsBancosTarjetasExcel').val(idsBancosTarjetasGlobal);
    $('#fechaDesdeFacturaExcel').val(valFechaDesdeFactura);
    $('#fechaHastaFacturaExcel').val(valFechaHastaFactura);
    $('#saldoPendientePagoExcel').val(Ext.getCmp('cbSaldoPendientePago').value ? 'S' : '');
    $('#valorSaldoPendientePagoExcel').val(Ext.getCmp('valorSaldoPendientePago').value);
    mostrarInfoBusquedaYConfirmacion('Exportar');
    document.forms[0].submit();

}


function verPlantilla()
{
    if(boolSinPlantilla)
    {
        var idPlantilla = Ext.getCmp('cmbPlantillaEmail').value;
        if (Ext.isEmpty(idPlantilla)) {
            Ext.Msg.alert('Error', 'Por favor seleccione una plantilla');
        }
        else
        {
            boolSinPlantilla = false;
            Ext.Ajax.request({
                url: strUrlGetContenidoPlantilla,
                method: 'post',
                params: {
                    intIdPlantilla: idPlantilla
                },
                success: function(response) {
                    var json = Ext.JSON.decode(response.responseText);

                    var winPlantilla = new Ext.Window({
                        title: 'Contenido de Plantilla',
                        width: 640,
                        height: 400,
                        preventBodyReset: true,
                        html: json.strContenidoPlantilla,
                        autoScroll: true,
                        bodyCls: 'verContenidoPlantilla',
                        listeners: {
                            'close': function(win) {
                                boolSinPlantilla = true;
                            }
                        }

                    });
                    winPlantilla.show();
                }
            });
        }
    }
}
