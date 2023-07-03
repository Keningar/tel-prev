Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

var itemsPerPage = 31;
var store = '';
var estado_id = '';

Ext.onReady(function() {

    var objMesStore = Ext.create('Ext.data.Store', {
        fields: ['valor', 'signo'],
        data: [
            {"valor": "00", "signo": "Todos"},
            {"valor": "01", "signo": "01"},
            {"valor": "02", "signo": "02"},
            {"valor": "03", "signo": "03"},
            {"valor": "04", "signo": "04"},
            {"valor": "05", "signo": "05"},
            {"valor": "06", "signo": "06"},
            {"valor": "07", "signo": "07"},
            {"valor": "08", "signo": "08"},
            {"valor": "09", "signo": "09"},
            {"valor": "10", "signo": "10"},
            {"valor": "11", "signo": "11"},
            {"valor": "12", "signo": "12"}
        ]
    });

    var cmbMes = new Ext.form.ComboBox({
        xtype: 'combobox',
        labelAlign: 'left',
        store: objMesStore,
        id: 'intIdMes',
        name: 'intIdMes',
        valueField: 'valor',
        displayField: 'signo',
        fieldLabel: 'Mes',
        width: 165,
        mode: 'local',
        allowBlank: true,
        listeners: {
            render: function(combobox) {
                combobox.setValue("00");
            }
        }
    });

    var objAnioStore = Ext.create('Ext.data.Store', {
        fields: ['valor', 'signo'],
        data: [
            {"valor": "1998", "signo": "1998"},
            {"valor": "1999", "signo": "1999"},
            {"valor": "2000", "signo": "2000"},
            {"valor": "2001", "signo": "2001"},
            {"valor": "2002", "signo": "2002"},
            {"valor": "2003", "signo": "2003"},
            {"valor": "2004", "signo": "2004"},
            {"valor": "2005", "signo": "2005"},
            {"valor": "2006", "signo": "2006"},
            {"valor": "2007", "signo": "2007"},
            {"valor": "2008", "signo": "2008"},
            {"valor": "2009", "signo": "2009"},
            {"valor": "2010", "signo": "2010"},
            {"valor": "2011", "signo": "2011"},
            {"valor": "2012", "signo": "2012"},
            {"valor": "2013", "signo": "2013"},
            {"valor": "2014", "signo": "2014"},
            {"valor": "2015", "signo": "2015"},
            {"valor": "2016", "signo": "2016"},
            {"valor": "2017", "signo": "2017"},
            {"valor": "2018", "signo": "2018"},
            {"valor": "2019", "signo": "2019"},
            {"valor": "2020", "signo": "2020"}
            //...
        ]
    });

    var cmbAnio = new Ext.form.ComboBox({
        xtype: 'combobox',
        labelAlign: 'left',
        store: objAnioStore,
        id: 'intIdAnio',
        name: 'intIdAnio',
        valueField: 'valor',
        displayField: 'signo',
        fieldLabel: 'Anio',
        width: 160,
        mode: 'local',
        allowBlank: true,
        listeners: {
            render: function(combobox) {
                combobox.setValue((new Date).getFullYear());
            }
        }
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 5,
        width: 360,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 4,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        defaults: {
            bodyStyle: 'padding:7px'
        },
        collapsible: true,
        collapsed: false,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: Buscar
            }
        ],
        items: [
            cmbMes,
            {html: "&nbsp;", border: false, width: 15},
            cmbAnio,
            {html: "&nbsp;", border: false, width: 15}
        ],
        renderTo: 'filtro_reportes'
    });


    function Buscar() {

        strAnio = Ext.getCmp('intIdAnio').getValue();
        strMes = Ext.getCmp('intIdMes').getValue();

        store.load({params: {strAnioParam: strAnio, strMesParam: strMes}});

    }

    store = new Ext.data.Store({
        pageSize: 12,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_grid,
            timeout: 9000000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'arrayAts'
            },
            extraParams: {strMesParam: '00'},
        },
        fields:
            [
                {name: 'strIdEmpresa', mapping: 'strIdEmpresa'},
                {name: 'strAnio', mapping: 'strAnio'},
                {name: 'strMes', mapping: 'strMes'},
                {name: 'intTamanio', mapping: 'intTamanio'}
            ]
    });

    grid = Ext.create('Ext.grid.Panel', {
        width: 360,
        height: 380,
        store: store,
        loadMask: true,
        renderTo: 'lista_reportes',
        frame: false,
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'strAnio',
                header: 'Año',
                dataIndex: 'strAnio',
                width: 120,
                sortable: true
            },
            {
                id: 'strMes',
                header: 'Mes',
                dataIndex: 'strMes',
                width: 80,
                sortable: true
            },
            {
                id: 'intTamanio',
                header: 'Tamaño',
                dataIndex: 'intTamanio',
                width: 80,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 78,
                items:
                    [
                        {
                            getClass: function(v, meta, rec)
                            {
                                return 'button-grid-pdf';
                            },
                            tooltip: 'Download',
                            handler: function(grid, rowIndex, colIndex)
                            {
                                window.location = '0/descargarDocumento?strIdEmpresa=' + grid.getStore().getAt(rowIndex).data.strIdEmpresa
                                    + '&strAnio=' + grid.getStore().getAt(rowIndex).data.strAnio
                                    + '&strMes=' + grid.getStore().getAt(rowIndex).data.strMes;

                            }
                        }
                    ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });

});