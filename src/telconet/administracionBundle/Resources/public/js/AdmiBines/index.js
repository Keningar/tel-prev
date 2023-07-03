Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    Ext.define('ModelStore',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id_bin_dm', mapping: 'id_bin_dm'},
                    {name: 'bin_dm', mapping: 'bin_dm'},
                    {name: 'descripcion_dm', mapping: 'descripcion_dm'},
                    {name: 'banco_dm', mapping: 'banco_dm'},
                    {name: 'tarjeta_dm', mapping: 'tarjeta_dm'},
                    {name: 'asociados_dm', mapping: 'asociados_dm'},
                    {name: 'estado_dm', mapping: 'estado_dm'},
                    {name: 'action1', mapping: 'action1'},
                    {name: 'action2', mapping: 'action2'},
                    {name: 'action3', mapping: 'action3'}
                ],
            idProperty: 'id_bin'
        });

    dataStoreBines = new Ext.data.Store(
        {
            pageSize: 20,
            model: 'ModelStore',
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlGridBines,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'json_totalbines',
                            root: 'json_listabines'
                        },
                    extraParams:
                        {
                            bin: '',
                            estado: 'Todos'
                        }
                },
            autoLoad: true
        });

    Ext.create('Ext.grid.Panel',
        {
            bufferedRenderer: false,
            store: dataStoreBines,
            loadMask: true,
            frame: false,
            renderTo: 'gridListaBines',
            forceFit: true,
            height: 370,
            split: true,
            region: 'north',
            viewConfig:
                {
                    enableTextSelection: true,
                    preserveScrollOnRefresh: true
                },
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store: dataStoreBines,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                }),
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        header: "Bin",
                        dataIndex: 'bin_dm',
                        width: 35,
                        sortable: true
                    },
                    {
                        header: "Descripci&oacute;n",
                        dataIndex: 'descripcion_dm',
                        width: 180,
                        sortable: true
                    },
                    {
                        header: "Tarjeta",
                        dataIndex: 'tarjeta_dm',
                        width: 120,
                        sortable: true
                    },
                    {
                        header: "Banco",
                        dataIndex: 'banco_dm',
                        width: 180,
                        sortable: true
                    },
                    {
                        header: "Clientes",
                        dataIndex: 'asociados_dm',
                        align: 'center',
                        width: 55,
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado_dm',
                        width: 45,
                        sortable: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 65,
                        items: [
                            {
                                /*Ver BIN*/
                                getClass: function(v, meta, rec)
                                {
                                    return 'button-grid-show';
                                },
                                tooltip: 'Ver',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStoreBines.getAt(rowIndex);
                                    window.location = "" + rec.get('id_bin_dm') + "/show";
                                }
                            },
                            {
                                /*Eliminar Bin*/
                                getClass: function(v, meta, rec)
                                {
                                    strExportarClientesBin = 'button-grid-invisible';
                                    var permiso = $("#ROLE_294-2798");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (boolPermiso)
                                    {
                                        if ('Eliminado' !== rec.get('estado_dm'))
                                        {
                                            strExportarClientesBin = 'button-grid-delete';
                                        }
                                    }
                                    return strExportarClientesBin;
                                },
                                tooltip: 'Eliminar Bin',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStoreBines.getAt(rowIndex);
                                    Ext.Msg.confirm('Alerta', 'Desea continuar con el proceso de eliminaci&oacute;n del registro?', function(btn)
                                    {
                                        if (btn === 'yes')
                                        {
                                            window.location = "" + rec.get('id_bin_dm') + "/eliminar";
                                        }
                                    });
                                }
                            },
                            {
                                /*Exporta Excel*/
                                getClass: function(v, meta, rec)
                                {
                                    strExportarClientesBin = 'button-grid-invisible';
                                    var permiso = $("#ROLE_294-2857");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (boolPermiso)
                                    {
                                        strExportarClientesBin = 'button-grid-excel';
                                    }
                                    return strExportarClientesBin;
                                },
                                tooltip: 'Reporte Clientes',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStoreBines.getAt(rowIndex);
                                    $('#strBinNuevo').val(rec.get('bin_dm'));
                                    $('#strDescripcion').val(rec.get('descripcion_dm'));
                                    $('#strTarjeta').val(rec.get('tarjeta_dm'));
                                    $('#strBanco').val(rec.get('banco_dm'));
                                    $('#strEstado').val(rec.get('estado_dm'));
                                    document.forms[0].submit();
                                }
                            }
                        ]
                    }
                ]
        });

    /* ******************************************* */
    /* FILTROS DE BÚSQUEDA */
    /* ******************************************* */
    Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            renderTo: 'filtroBines',
            layout:
                {
                    type: 'hbox',
                    align: 'stretch'
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: true,
            title: 'Criterios de búsqueda',
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function()
                        {
                            dataStoreBines.getProxy().extraParams.bin = Ext.getCmp('txtBin').value;
                            dataStoreBines.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
                            dataStoreBines.load();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function()
                        {
                            Ext.getCmp('txtBin').value = "";
                            Ext.getCmp('txtBin').setRawValue("");
                            Ext.getCmp('sltEstado').value = "Todos";
                            Ext.getCmp('sltEstado').setRawValue("Todos");

                            dataStoreBines.getProxy().extraParams.bin = Ext.getCmp('txtBin').value;
                            dataStoreBines.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
                            dataStoreBines.load();
                        }
                    }
                ],
            items:
                [
                    {
                        width: '5%',
                        border: false
                    },
                    {
                        xtype: 'textfield',
                        id: 'txtBin',
                        fieldLabel: 'Bin',
                        value: '',
                        width: '250'
                    },
                    {width: '15%', border: false},
                    , {
                        xtype: 'combobox',
                        fieldLabel: 'Estado',
                        id: 'sltEstado',
                        value: 'Todos',
                        store:
                            [
                                ['Todos', 'Todos'],
                                ['Activo', 'Activo'],
                                ['Eliminado', 'Eliminado']
                            ],
                        width: '200'
                    }
                ]
        });
});