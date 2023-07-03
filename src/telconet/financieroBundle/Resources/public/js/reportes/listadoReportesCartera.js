Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();

var years = [];
year = 1998;
while (year<=(new Date).getFullYear()){
    years.push({'valor': year, 'signo': year});
    year++;
}

var itemsPerPage = 31;
var store = '';
var estado_id = '';

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
        data: years
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
                root: 'clientes',
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
            }
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
                                if(puedeDescargaReporteCartera)
                                {
                                    if (prefijo == 'MD')
                                        var classA = "button-grid-zip";
                                    else
                                        var classA = "button-grid-excel-green";

                                    if (rec.data.estado == "Inactivo") {
                                        classA = "icon-invisible";
                                    }

                                    if (classA == "icon-invisible")
                                        this.items[0].tooltip = '';
                                    else
                                        this.items[0].tooltip = 'Descargar Reporte de Cartera';

                                    return classA;
                                }
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                
                                if(puedeDescargaReporteCartera)
                                {
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
            }]
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
            }],
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

