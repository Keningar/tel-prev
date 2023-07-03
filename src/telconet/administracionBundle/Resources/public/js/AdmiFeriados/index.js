
/* global Ext */

Ext.onReady(function()
{
    var storeFeriados = new Ext.data.Store
        ({
            pageSize: 20,
            total: 'total',
            timeout: 1200000,
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetFeriados,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'idFeriados' , mapping: 'idFeriados'},
                    {name: 'descripcion', mapping: 'descripcion'},
                    {name: 'tipo'       , mapping: 'tipo'},
                    {name: 'mes'        , mapping: 'mes'},
                    {name: 'nombreMes'  , mapping: 'nombreMes'},
                    {name: 'dia'        , mapping: 'dia'},
                    {name: 'estado'     , mapping: 'estado'},
                    {name: 'action1'    , mapping: 'action1'},
                    {name: 'action2'    , mapping: 'action2'},
                    {name: 'action3'    , mapping: 'action3'}
                ],
            autoLoad: true
        });

    var gridFeriados = Ext.create('Ext.grid.Panel',
        {
            width: 950,
            height: 300,
            store: storeFeriados,
            viewConfig:
                {
                    enableTextSelection: true,
                    trackOver: true,
                    stripeRows: true,
                    loadMask: true
                },
            columns:
                [
                    {
                        header: 'Descripción',
                        dataIndex: 'descripcion',
                        width: 250,
                        sortable: true
                    },
                    {
                        header: 'Tipo',
                        dataIndex: 'tipo',
                        width: 250,
                        sortable: true
                    },
                    {
                        header: 'Mes',
                        dataIndex: 'nombreMes',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Dia',
                        dataIndex: 'dia',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 120,
                        items: [

                            {
                                getClass: function(v, meta, rec) {
                                    return 'button-grid-show'
                                },
                                tooltip: 'Ver',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storeFeriados.getAt(rowIndex);
                                    window.location = rec.get('idFeriados') + "/show";
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    return 'button-grid-edit'
                                },
                                tooltip: 'Editar Detalle',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storeFeriados.getAt(rowIndex);
                                    window.location = "" + rec.get('idFeriados') + "/edit";
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    return 'button-grid-Delete';
                                },
                                tooltip: 'Anular',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storeFeriados.getAt(rowIndex);
                                    window.location = "" + rec.get('idPlantillaHorarioCab') + "/generar";
                                }
                            }
                        ]
                    }
                ],
            title: 'Feriados',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeFeriados,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'grid'
        });
    var filterFeriados = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout:
                {
                    type: 'table',
                    columns: 3,
                    align: 'center'
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: true,
            width: 950,
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
                    {
                        xtype: 'textfield',
                        id: 'txtDescripcion',
                        fieldLabel: 'Descripción',
                        value: '',
                        width: 360
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estado',
                        id: 'estado',
                        value: 'Todos',
                        store: [
                            ['Todos', '-- Todos los Estados --'],
                            ['Activo', 'Activo'],
                            ['Inactivo', 'Inactivo']
                        ],
                        width: 360
                    }
                ],
            renderTo: 'filtro'
        });


    function buscar()
    {
        cargarFiltrosBusquedaAlStore();
        storePlantilla.load();
    }


    function limpiar()
    {
        Ext.getCmp('txtDescripcion').value = "";
        Ext.getCmp('txtDescripcion').setRawValue("");
        Ext.getCmp('fechaDesde').value = "";
        Ext.getCmp('fechaDesde').setRawValue("");
        Ext.getCmp('fechaHasta').value = "";
        Ext.getCmp('fechaHasta').setRawValue("");
        Ext.getCmp('estado').value = "Todos";
        Ext.getCmp('estado').setRawValue("-- Todos los Estados --");


        storePlantilla.loadData([], false);
        cargarFiltrosBusquedaAlStore();
        storePlantilla.currentPage = 1;
        storePlantilla.load();
    }


    function cargarFiltrosBusquedaAlStore()
    {
        storePlantilla.getProxy().extraParams.descripcion = Ext.getCmp('txtDescripcion').value;
        storePlantilla.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').value;
        storePlantilla.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').value;
        storePlantilla.getProxy().extraParams.estado = Ext.getCmp('estado').value;
    }
});