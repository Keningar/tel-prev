Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
            [
                {name: 'intIdNotifMasivaLog',   mapping: 'intIdNotifMasivaLog'},
                {name: 'intIdNotifMasiva',      mapping: 'intIdNotifMasiva'},
                {name: 'strNombreJob',          mapping: 'strNombreJob'},
                {name: 'strFechaCreacion',      mapping: 'strFechaCreacion'},
                {name: 'intNumProcesados',      mapping: 'intNumProcesados'},
                {name: 'intNumEnviados',        mapping: 'intNumEnviados'},
                {name: 'intNumNoEnviados',      mapping: 'intNumNoEnviados'},
                {name: 'strEstado',             mapping: 'strEstado'}
            ],
        idProperty: 'intIdNotifMasivaLog'
    });

    storeLogsNotifMasiva = new Ext.data.Store({
        pageSize: 10,
        model: 'ModelStore',
        total: 'intTotal',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: strUrlVerLogsEjecucion,
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayResultado'
            },
            extraParams: {
                intIdNotifMasiva: intIdNotifMasiva
            }
        },
        autoLoad: true
    });


    gridLogsNotifMasiva = Ext.create('Ext.grid.Panel', {
        id: 'gridLogsNotifMasiva',
        width: 900,
        height: 250,
        store: storeLogsNotifMasiva,
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            loadMask: true
        },
        columns: [
            {
                id: 'intIdNotifMasivaLog',
                header: 'intIdNotifMasivaLog',
                dataIndex: 'intIdNotifMasivaLog',
                hidden: true,
                hideable: false
            },
            {
                id: 'strFechaCreacionLog',
                header: 'Fecha Creación',
                dataIndex: 'strFechaCreacion',
                width: 150,
                sortable: true
            },
            {
                id: 'strNombreJob',
                header: 'NOMBRE JOB',
                dataIndex: 'strNombreJob',
                width: 150,
                sortable: true
            },
            {
                header: '# Procesados',
                dataIndex: 'intNumProcesados',
                width: 100,
                sortable: true
            },
            {
                header: '# Enviados',
                dataIndex: 'intNumEnviados',
                width: 100,
                sortable: true
            },
            {
                header: '# No Enviados',
                dataIndex: 'intNumNoEnviados',
                width: 100,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 100,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items:
                    [
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = '{{ is_granted("ROLE_398-6") }}';
                                var strClassButton = 'btn-acciones button-grid-show';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                if (!boolPermiso) {
                                    strClassButton = "icon-invisible";
                                }

                                return strClassButton;
                            },
                            tooltip: 'Ver Detalle del Log',
                            handler: function(grid, rowIndex, colIndex) {

                                var rec = storeLogsNotifMasiva.getAt(rowIndex);
                                var strClassButton = 'btn-acciones button-grid-show';
                                var permiso = '{{ is_granted("ROLE_398-6") }}';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                if (!boolPermiso) {
                                    strClassButton = "icon-invisible";
                                }

                                if (strClassButton != "icon-invisible")
                                {
                                    showDetalleLog(rec.get('intIdNotifMasivaLog'));
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
        bbar: Ext.create('Ext.PagingToolbar', {
            displayInfo: true,
            store: storeLogsNotifMasiva,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        frame: true,
        title: 'Logs',
        renderTo: 'divLogsEnvioMasivo'
    });

});

function showDetalleLog(intIdNotifMasivaLog) {
    document.getElementById("idNotifMasivaLog").value = intIdNotifMasivaLog;
    document.getElementById("divFiltrosLogsDetsEnvioMasivo").innerHTML = "";
    document.getElementById("divLogsDetsEnvioMasivo").innerHTML = "";
    Ext.define('ModelLogsDetStore', {
        extend: 'Ext.data.Model',
        fields:
            [
                {name: 'intIdNotifMasivaLogDet',    mapping: 'intIdNotifMasivaLogDet'},
                {name: 'strLogin',                  mapping: 'strLogin'},
                {name: 'strNombres',                mapping: 'strNombres'},
                {name: 'strCorreo',                 mapping: 'strCorreo'},
                {name: 'strTipoContacto',           mapping: 'strTipoContacto'},
                {name: 'strEstado',                 mapping: 'strEstado'}
            ],
        idProperty: 'intIdNotifMasivaLog'
    });

    storeLogsDetsNotifMasiva = new Ext.data.Store({
        pageSize: 50,
        model: 'ModelLogsDetStore',
        total: 'intTotal',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: strUrlVerLogsDetsEjecucion,
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayResultado'
            },
            extraParams: {
                intIdNotifMasivaLog: intIdNotifMasivaLog
            }
        },
        autoLoad: true
    });

    itemsBusquedaLogsDetsNotifMasiva = [
        {html: "&nbsp;", border: false, width: 150},
        {
            xtype: 'textfield',
            id: 'txtLogin',
            fieldLabel: 'Login',
            value: '',
            width: '300'
        },
        {html: "&nbsp;", border: false, width: 150},
        {
            xtype: 'textfield',
            id: 'txtNombre',
            fieldLabel: 'Nombres',
            value: '',
            width: '300'
        },
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        {
            xtype: 'combobox',
            fieldLabel: 'Estado',
            id: 'cmbEstadoEnvio',
            name: 'cmbEstadoEnvio',
            store: [
                ['Enviado', 'Enviado'],
                ['No Enviado', 'No Enviado']
            ]
        },
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150}
    ];
    var filtrosLogsDetsNotifMasiva = Ext.create('Ext.panel.Panel', {
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
        width: 1000,
        title: 'Criterios de búsqueda',
        buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function() {
                        buscarDetalleLog();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function() {
                        limpiarDetalleLog();
                    }
                }
            ],
        items: itemsBusquedaLogsDetsNotifMasiva,
        renderTo: 'divFiltrosLogsDetsEnvioMasivo'
    });



    gridLogsDetsNotifMasiva = Ext.create('Ext.grid.Panel', {
        id: 'gridLogsDetsNotifMasiva',
        width: 1000,
        height: 400,
        store: storeLogsDetsNotifMasiva,
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            loadMask: true
        },
        columns: [
            {
                id: 'intIdNotifMasivaLogDet',
                header: 'intIdNotifMasivaLogDet',
                dataIndex: 'intIdNotifMasivaLogDet',
                hidden: true,
                hideable: false
            },
            {
                id: 'strLogin',
                header: 'Login',
                dataIndex: 'strLogin',
                width: 150,
                sortable: true
            },
            {
                id: 'strNombres',
                header: 'NOMBRES',
                dataIndex: 'strNombres',
                width: 300,
                sortable: true
            },
            {
                header: 'Correo',
                dataIndex: 'strCorreo',
                width: 200,
                sortable: true
            },
            {
                header: 'Tipo Contacto',
                dataIndex: 'strTipoContacto',
                width: 200,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'strEstado',
                width: 100,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            displayInfo: true,
            store: storeLogsDetsNotifMasiva,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        frame: true,
        title: 'Detalle del Log',
        renderTo: 'divLogsDetsEnvioMasivo'
    });
}

function buscarDetalleLog() {
    extraParamsBusquedaDetalle = {
        strLogin: Ext.getCmp('txtLogin').value,
        strNombres: Ext.getCmp('txtNombre').value,
        strEstado: Ext.getCmp('cmbEstadoEnvio').value,
        intIdNotifMasivaLog: document.getElementById("idNotifMasivaLog").value
    };
    storeLogsDetsNotifMasiva.loadData([], false);
    storeLogsDetsNotifMasiva.currentPage = 1;
    storeLogsDetsNotifMasiva.getProxy().extraParams = extraParamsBusquedaDetalle;
    storeLogsDetsNotifMasiva.load();
}


function limpiarDetalleLog() {
    extraParamsBusquedaDetalle = null;
    Ext.getCmp('txtLogin').value = "";
    Ext.getCmp('txtLogin').setRawValue("");
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('cmbEstadoEnvio').value = "";
    Ext.getCmp('cmbEstadoEnvio').setRawValue("");

    storeLogsDetsNotifMasiva.loadData([], false);
    storeLogsDetsNotifMasiva.currentPage = 1;
}
