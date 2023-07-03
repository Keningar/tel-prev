
/* global Ext */

Ext.onReady(function()
{
    var storePlantilla = new Ext.data.Store
        ({
            pageSize: 20,
            total: 'total',
            timeout: 1200000,
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetPlantillaHorario,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'idPlantillaHorarioCab', mapping: 'idPlantillaHorarioCab'},
                    {name: 'descripcion', mapping: 'descripcion'},
                    {name: 'usrCreacion', mapping: 'usrCreacion'},
                    {name: 'feCreacion', mapping: 'feCreacion'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'esDefault', mapping: 'esDefault'},
                    {name: 'strNombreJurisdiccion', mapping: 'strNombreJurisdiccion'}
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
                        header: 'Descripción',
                        dataIndex: 'descripcion',
                        width: 250,
                        sortable: true
                    },
                    {
                        header: 'Jurisdicción',
                        dataIndex: 'strNombreJurisdiccion',
                        width: 250,
                        sortable: true
                    },
                    {
                        header: 'Usuario Creación',
                        dataIndex: 'usrCreacion',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Fecha Creación',
                        dataIndex: 'feCreacion',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 70,
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
                                    var rec = storePlantilla.getAt(rowIndex);
                                    window.location = rec.get('idPlantillaHorarioCab') + "/show";
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    return 'button-grid-edit'
                                },
                                tooltip: 'Editar Detalle',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storePlantilla.getAt(rowIndex);
                                    //alert(rec.get('idPlantillaHorarioCab'));
                                    window.location = "" + rec.get('idPlantillaHorarioCab') + "/edit";
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    return 'button-grid-Tuerca';
                                },
                                tooltip: 'Generar Cupos por periodo',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storePlantilla.getAt(rowIndex);
                                    //alert(rec.get('idPlantillaHorarioCab'));
                                    window.location = "" + rec.get('idPlantillaHorarioCab') + "/generar";
                                }
                            }
                        ]
                    }
                ],
            title: 'Plantilla de Horarios',
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
                            {
                                id: 'fechaDesde',
                                name: 'fechaDesde',
                                fieldLabel: 'Desde',
                                labelAlign: 'left',
                                xtype: 'datefield',
                                format: 'Y-m-d',
                                width: 250,
                                editable: false
                            },
                            {
                                id: 'fechaHasta',
                                name: 'fechaHasta',
                                fieldLabel: 'Hasta',
                                labelAlign: 'left',
                                xtype: 'datefield',
                                format: 'Y-m-d',
                                width: 250,
                                editable: false
                            }]
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
                            }]
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