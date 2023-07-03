
/* global Ext */

Ext.onReady(function()
{
    DTFechaDesde = Ext.create('Ext.data.fecha', {
        id: 'fechaDesde',
        name: 'fechaDesde',
        fieldLabel: 'Desde'
    });

    DTFechaHasta = Ext.create('Ext.data.fecha', {
        id: 'fechaHasta',
        name: 'fechaHasta',
        fieldLabel: 'Hasta'
    });

    cmbJurisdiccion = Ext.create('Ext.data.comboJurisdiccion', {
        id: 'cmbJurisdiccion',
        name: 'cmbJurisdiccion'});

    var storePlantilla = new Ext.data.Store
        ({
            pageSize: 20,
            total: 'total',
            timeout: 1200000,
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetGrid,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'idAgendaCupos', mapping: 'idAgendaCupos'},
                    {name: 'empresaCod', mapping: 'empresaCod'},
                    {name: 'fechaPeriodo', mapping: 'fechaPeriodo'},
                    {name: 'totalCupos', mapping: 'totalCupos'},
                    {name: 'observacion', mapping: 'observacion'},
                    {name: 'nombreJurisdiccion', mapping: 'nombreJurisdiccion'},
                    {name: 'nombrePlantilla', mapping: 'nombrePlantilla'},
                    {name: 'action2', mapping: 'action2'}
                ],
            autoLoad: true
        });
    var gridPlantilla = Ext.create('Ext.grid.Panel',
        {
            width: 950,
            height: 300,
            store: storePlantilla,
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
                        header: 'Fecha',
                        dataIndex: 'fechaPeriodo',
                        width: 80,
                        sortable: true
                    },
                    {
                        header: 'Plantilla Base',
                        dataIndex: 'nombrePlantilla',
                        width: 200,
                        align: 'left',
                        sortable: true
                    },

                    {
                        header: 'Jurisdiccion',
                        dataIndex: 'nombreJurisdiccion',
                        width: 200,
                        align: 'left',
                        sortable: true
                    },
                    {
                        header: 'Total Cupos',
                        dataIndex: 'totalCupos',
                        width: 70,
                        align: 'rigth',
                        sortable: true
                    },
                    {
                        header: 'Observacion',
                        dataIndex: 'observacion',
                        width: 300,
                        align: 'left',
                        sortable: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 80,
                        items: [

                            {
                                getClass: function(v, meta, rec) {
                                    return 'button-grid-show'
                                },
                                tooltip: 'Ver',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storePlantilla.getAt(rowIndex);
                                    window.location = rec.get('idAgendaCupos') + "/show";
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    return rec.get('action2');
                                },
                                tooltip: 'Editar Agenda',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storePlantilla.getAt(rowIndex);
                                    window.location = "" + rec.get('idAgendaCupos') + "/edit";
                                }
                            }
                        ]
                    }
                ],
            title: 'Agenda de Cupos',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storePlantilla,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'grid'
        });
    var filterPlantilla = Ext.create('Ext.panel.Panel',
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
                    {width: '40%', border: false},
                    {
                        xtype: 'fieldset',
                        defaultType: 'datefield',
                        style: "font-weight:bold; margin-bottom: 15px; border:none",
                        layout: 'anchor',
                        defaults:
                            {
                                width: '250px',
                                border: false,
                                frame: false
                            },
                        items: [
                            DTFechaDesde,
                            DTFechaHasta
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        defaultType: 'datefield',
                        style: "font-weight:bold; margin-bottom: 15px; border:none",
                        //layout: 'anchor',
                        defaults:
                            {
                                width: '250px',
                                border: false,
                                frame: false
                            },
                        items: [
                            {
                                xtype: 'textfield',
                                id: 'txtPlantilla',
                                fieldLabel: 'Plantilla',
                                value: '',
                                width: 360
                            },
                            cmbJurisdiccion
                        ]
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
        Ext.getCmp('txtPlantilla').value = "";
        Ext.getCmp('txtPlantilla').setRawValue("");
        Ext.getCmp('fechaDesde').value = "";
        Ext.getCmp('fechaDesde').setRawValue("");
        Ext.getCmp('fechaHasta').value = "";
        Ext.getCmp('fechaHasta').setRawValue("");
        Ext.getCmp('cmbJurisdiccion').value = "";
        Ext.getCmp('cmbJurisdiccion').setRawValue("");
        storePlantilla.loadData([], false);
        cargarFiltrosBusquedaAlStore();
        storePlantilla.currentPage = 1;
        storePlantilla.load();
    }


    function cargarFiltrosBusquedaAlStore()
    {
        storePlantilla.getProxy().extraParams.descripcion = Ext.getCmp('txtPlantilla').value;
        storePlantilla.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').value;
        storePlantilla.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').value;
        storePlantilla.getProxy().extraParams.jurisdiccion = Ext.getCmp('cmbJurisdiccion').value;
    }
});