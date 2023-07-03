Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

var itemsPerPage = 31;

Ext.onReady(function() {

    var mes_store = Ext.create('Ext.data.Store', {
        fields: ['valor', 'signo'],
        data: [
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

    var mes_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        labelAlign: 'left',
        store: mes_store,
        id: 'idmes',
        name: 'idmes',
        valueField: 'valor',
        displayField: 'signo',
        fieldLabel: 'Mes',
        width: 160,
        mode: 'local',
        allowBlank: true,
        listeners: {
            render: function(combobox) {
                combobox.setValue((new Date).getMonth() + 1);
            }
        }
    });


    var anio_store = Ext.create('Ext.data.Store', {
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
        ]
    });

    var anio_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        labelAlign: 'left',
        store: anio_store,
        id: 'idanio',
        name: 'idanio',
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



    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'linkVer', type: 'string'},
            {name: 'linkFile', type: 'string'},
            {name: 'size', type: 'string'}
        ]
    });


    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_grid,
            reader: {
                type: 'json',
                root: 'archivos',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },
        sortOnLoad: true,
        sorters: {
            property: 'linkVer',
            direction: 'DESC'
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.mes = Ext.getCmp('idmes').getValue();
                store.getProxy().extraParams.anio = Ext.getCmp('idanio').getValue();
            },
            load: function(store) {
                store.each(function(record) {
                });
            }
        }
    });

    store.load({params: {start: 0, limit: 31, mes: (new Date).getMonth() + 1, anio: (new Date).getFullYear()}});


    var listView = Ext.create('Ext.grid.Panel', {
        width: 800,
        height: 365,
        collapsible: false,
        title: '',
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    {xtype: 'tbfill'},
                ]}],
        renderTo: Ext.get('lista_reportes'),
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando registros {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Archivo',
                width: 450,
                dataIndex: 'linkVer'
            }, {
                text: 'Tamano',
                width: 70,
                dataIndex: 'size'
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 150,
                sortable: false,
                items:
                    [
                        {
                            getClass: function(v, meta, rec) {
                                
                                if(puedeDescargarDinardap)
                                {
                                    if (prefijo == 'MD')
                                        var classA = "button-grid-zip";
                                    else
                                        var classA = "button-grid-excel-green";

                                    if (classA == "icon-invisible")
                                        this.items[0].tooltip = '';
                                    else
                                        this.items[0].tooltip = 'Descargar Reporte de Dinardap';

                                    return classA;
                                }
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                if(puedeDescargarDinardap)
                                {
                                    var rec = store.getAt(rowIndex);
                                    if (prefijo == 'MD')
                                        var classA = "button-grid-zip";
                                    else
                                        var classA = "button-grid-excel-green";

                                    if (classA != "icon-invisible")
                                        window.location = rec.data.linkFile;
                                }
                                else
                                        Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    ]
            }
        ]
    });



    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, 
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
            bodyStyle: 'padding:10px'
        },
        collapsible: true,
        collapsed: false,
        width: 800,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: Buscar
            }
        ],
        items: [
            mes_cmb,
            {html: "&nbsp;", border: false, width: 50},
            anio_cmb,
            {html: "&nbsp;", border: false, width: 50}
        ],
        renderTo: 'filtro_reportes'
    });


    function Buscar() {
        store.load({params: {start: 0, limit: 31}});
    }

});

