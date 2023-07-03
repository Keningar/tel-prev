/* 
 * 
 * @author  Richard Cabrera   <rcabrera@telconet.ec>
 * @version 1.0 06-03-2017
 * 
 */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    store = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 1200000,
            url: url_gridReportesArcotel,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
        },
        fields:
            [
                {name: 'fe_creacion', mapping: 'fe_creacion'},
                {name: 'nombre_reporte', mapping: 'nombre_reporte'},
                {name: 'link_exportar', mapping: 'link_exportar'}
            ],
        autoLoad: true
    });

    grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: 670,
        height: 350,
        loadMask: true,
        frame: false,
        columns:
            [
                {
                    id: 'link_exportar',
                    header: 'link_exportar',
                    dataIndex: 'link_exportar',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'fe_creacion',
                    header: 'Fecha Creación',
                    dataIndex: 'fe_creacion',
                    width: 130,
                    hideable: true
                },
                {
                    id: 'nombre_reporte',
                    header: 'Nombre Reporte',
                    dataIndex: 'nombre_reporte',
                    sortable: true,
                    width: 400,
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 60,
                    items: [
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_377-5197");
                                var boton = "button-grid-excel";
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso)
                                {
                                    boton = "icon-invisible";
                                }

                                return boton;
                            },
                            tooltip: 'Descargar Reporte',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var ruta = rec.data.link_exportar;

                                window.open(ruta);
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
        }),
        renderTo: 'grid',
        listeners:
            {
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
            }
    });
});
