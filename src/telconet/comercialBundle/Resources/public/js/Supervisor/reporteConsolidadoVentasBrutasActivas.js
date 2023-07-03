Ext.onReady(function() 
{
    var modelVentas = Ext.define('VentasModel',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                { name: 'intIdVendedor',          mapping: 'intIdVendedor' },
                { name: 'strNombreVendedor',      mapping: 'strNombreVendedor' },
                { name: 'intTiempoVendedor',      mapping: 'intTiempoVendedor' },
                { name: 'strMetabrutas',          mapping: 'strMetabrutas' },
                { name: 'intCumplimientobrutas',  mapping: 'intCumplimientobrutas' },
                { name: 'intFaltabrutas',         mapping: 'intFaltabrutas' },
                { name: 'intPorcentajebrutas',    mapping: 'intPorcentajebrutas' },
                { name: 'strMetaactivas',         mapping: 'strMetaactivas' },
                { name: 'intCumplimientoactivas', mapping: 'intCumplimientoactivas' },
                { name: 'intFaltaactivas',        mapping: 'intFaltaactivas' },
                { name: 'intPorcentajeactivas',   mapping: 'intPorcentajeactivas' }
            ]
        });
        
        
    var storeReporteConsolidado = new Ext.data.Store
        ({
            pageSize: 40,
            total: 'total',
            model: modelVentas,
            proxy: 
            {
                type: 'ajax',
                url: strUrlGetConsolidadoVentas,
                timeout: 900000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
        });

    var gridReporteConsolidado = Ext.create('Ext.grid.Panel',
        {
            width: 1150,
            height: 510,
            store: storeReporteConsolidado,
            loadMask: true,
            iconCls: 'icon-grid',
            layout:'fit',
            dockedItems:
            [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items:
                    [
                        {xtype: 'tbfill'},
                        {
                            iconCls: 'icon_exportar',
                            text: 'Exportar',
                            scope: this,
                            handler: function() 
                            {
                                exportarExcel();
                            }
                        }
                    ]
                }
            ],
            viewConfig: 
            {
                emptyText: 'No hay datos para mostrar',
                enableTextSelection: true,
                trackOver: true,
                stripeRows: true,
                loadMask: true
            },
            listeners: 
            {
                itemdblclick: function(view, record, item, index, eventobj, obj) 
                {
                    var position = view.getPositionByEvent(eventobj),
                        data     = record.data,
                        value    = data[this.columns[position.column].dataIndex];

                    Ext.Msg.show
                    ({
                        title: 'Copiar texto?',
                        msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFORMATION
                    });
                },
                beforerender: function (cmp, eOpts)
                {
                    cmp.columns[0].setHeight(30);
                },
                viewready: function (grid)
                {
                    var view = grid.view;
                    
                    grid.mon(view,
                    {
                        uievent: function (type, view, cell, recordIndex, cellIndex, e)
                        {
                            grid.cellIndex = cellIndex;
                            grid.recordIndex = recordIndex;
                        }
                    });

                    grid.tip = Ext.create('Ext.tip.ToolTip',
                    {
                        target: view.el,
                        delegate: '.x-grid-cell',
                        trackMouse: true,
                        renderTo: Ext.getBody(),
                        listeners:
                        {
                            beforeshow: function updateTipBody(tip)
                            {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                }
                            }
                        }
                    });
                }
            },
            layoutConfig: 
            {
                align: 'middle'
            },
            columns: 
            [            
                new Ext.grid.RowNumberer(),
                {
                    header: 'intIdVendedor',
                    dataIndex: 'intIdVendedor',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Asesor',
                    dataIndex: 'strNombreVendedor',
                    width: 255,
                    sortable: true
                },
                {
                    header: 'Tiempo del Asesor<br/>(en días)',
                    dataIndex: 'intTiempoVendedor',
                    width: 150,
                    sortable: true,
                    align: 'center'
                },
                {
                    text: 'Brutas',    
                    columns:
                    [
                        {
                            text: 'Meta',
                            dataIndex: 'strMetabrutas',
                            align: 'center',
                            width: 90,
                            sortable: true		
                        },
                        {
                            text: 'Cumplimiento',
                            dataIndex: 'intCumplimientobrutas',
                            align: 'center',
                            width: 90,
                            sortable: true		
                        },
                        {
                            text: 'Falta',
                            dataIndex: 'intFaltabrutas',
                            align: 'center',
                            width: 90,
                            sortable: true		
                        },
                        {
                            text: '%<br/>Cumplimiento',
                            dataIndex: 'intPorcentajebrutas',
                            align: 'center',
                            width: 90,
                            sortable: true,
                            renderer: function(value, metaData, record, row, col, store, gridView)
                            {
                                var strReturn = '';

                                 strReturn += '<div style="float:left; width: 20px;">';

                                if( value < 70)
                                {
                                    strReturn += '<div class="trafficlight trafficlight-red-small">&nbsp;</div>';
                                }
                                else if( value >= 70 && value < 99 )
                                {
                                    strReturn += '<div class="trafficlight trafficlight-yellow-small">&nbsp;</div>';
                                }
                                else
                                {
                                    strReturn += '<div class="trafficlight trafficlight-green-small">&nbsp;</div>';
                                }

                                strReturn += '</div>'
                                            +'<div style="float:right; width: 50px; text-align: right;">'
                                                + value +'%'
                                            +'</div>';

                                return  strReturn;
                            } 
                        }
                    ]
                },
                {
                    text: 'Activas',    
                    columns:
                    [
                        {
                            text: 'Meta',
                            dataIndex: 'strMetaactivas',
                            align: 'center',
                            width: 90,
                            sortable: true		
                        },
                        {
                            text: 'Cumplimiento',
                            dataIndex: 'intCumplimientoactivas',
                            align: 'center',
                            width: 90,
                            sortable: true		
                        },
                        {
                            text: 'Falta',
                            dataIndex: 'intFaltaactivas',
                            align: 'center',
                            width: 90,
                            sortable: true		
                        },
                        {
                            text: '%<br/>Cumplimiento',
                            dataIndex: 'intPorcentajeactivas',
                            align: 'center',
                            width: 90,
                            sortable: true,
                            renderer: function(value, metaData, record, row, col, store, gridView)
                            {
                                var strReturn = '';

                                 strReturn += '<div style="float:left; width: 20px;">';

                                if( value < 70)
                                {
                                    strReturn += '<div class="trafficlight trafficlight-red-small">&nbsp;</div>';
                                }
                                else if( value >= 70 && value < 99 )
                                {
                                    strReturn += '<div class="trafficlight trafficlight-yellow-small">&nbsp;</div>';
                                }
                                else
                                {
                                    strReturn += '<div class="trafficlight trafficlight-green-small">&nbsp;</div>';
                                }

                                strReturn += '</div>'
                                            +'<div style="float:right; width: 50px; text-align: right;">'
                                                + value +'%'
                                            +'</div>';

                                return  strReturn;
                            }
                        }
                    ]
                }
            ],
            title: 'Reporte Consolidado de Ventas Brutas y Activas',
            bbar: Ext.create('Ext.PagingToolbar', 
            {
                store: storeReporteConsolidado,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'gridReporteConsolidado'
        });
        
    
    
    /*
     * Filtros de Búsqueda
     */
    var filterReporteVentasSupervisor = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout:
            {
                type: 'table',
                columns: 9,
                align: 'left'
            },
            bodyStyle:
            {
                background: '#fff'
            },
            collapsible: true,
            collapsed: false,
            width: 1150,
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
                {width: 40, border: false, height: 20},
                {width: 230, border: false, height: 20},
                {width: 50, border: false, height: 20},
                {width: 230, border: false, height: 20},
                {width: 50, border: false, height: 20},
                {width: 230, border: false, height: 20},
                {width: 50, border: false, height: 20},
                {width: 230, border: false, height: 20},
                {width: 60, border: false, height: 20},
                {width: 40, border: false},
                {
                    xtype:'fieldset',
                    title: 'Fecha Creación Punto',
                    collapsible: false,
                    border: true,
                    items: 
                    [
                        {
                            xtype: 'datefield',
                            width: 160,
                            id: 'feCreacionPuntoDesde',
                            name: 'feCreacionPuntoDesde',
                            fieldLabel: 'Desde:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 160,
                            id: 'feCreacionPuntoHasta',
                            name: 'feCreacionPuntoHasta',
                            fieldLabel: 'Hasta:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {width: 50, border: false, height: 20},
                {
                    xtype:'fieldset',
                    title: 'Fecha Aprobación',
                    collapsible: false,
                    border: true,
                    items: 
                    [
                        {
                            xtype: 'datefield',
                            width: 160,
                            id: 'feAprobacionDesde',
                            name: 'feAprobacionDesde',
                            fieldLabel: 'Desde:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 160,
                            id: 'feAprobacionHasta',
                            name: 'feAprobacionHasta',
                            fieldLabel: 'Hasta:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {width: 50, border: false},
                {
                    xtype:'fieldset',
                    title: 'Fecha Activación',
                    collapsible: false,
                    border: true,
                    items: 
                    [
                        {
                            xtype: 'datefield',
                            width: 160,
                            id: 'feActivacionDesde',
                            name: 'feActivacionDesde',
                            fieldLabel: 'Desde:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 160,
                            id: 'feActivacionHasta',
                            name: 'feActivacionHasta',
                            fieldLabel: 'Hasta:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {width: 50, border: false},
                {
                    xtype:'fieldset',
                    title: 'Fecha Planificación',
                    collapsible: false,
                    border: true,
                    items: 
                    [
                        {
                            xtype: 'datefield',
                            width: 160,
                            id: 'fePlanificacionDesde',
                            name: 'fePlanificacionDesde',
                            fieldLabel: 'Desde:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 160,
                            id: 'fePlanificacionHasta',
                            name: 'fePlanificacionHasta',
                            fieldLabel: 'Hasta:',
                            labelWidth: 50,
                            labelPad: 10,
                            labelAlign : 'right',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {width: 60, border: false, height: 20},
                {width: 40, border: false, height: 20},
                {width: 230, border: false, height: 20},
                {width: 50, border: false, height: 20},
                {width: 230, border: false, height: 20},
                {width: 50, border: false, height: 20},
                {width: 230, border: false, height: 20},
                {width: 50, border: false, height: 20},
                {width: 230, border: false, height: 20},
                {width: 60, border: false, height: 20}
            ],
            renderTo: 'filtros'
        });    


    function buscar() 
    {
        var boolContinuar = true;
        
        if( Ext.getCmp('feCreacionPuntoDesde').getValue()     != null ||
            Ext.getCmp('feCreacionPuntoHasta').getValue()     != null ||
            Ext.getCmp('feAprobacionDesde').getValue()        != null ||
            Ext.getCmp('feAprobacionHasta').getValue()        != null ||
            Ext.getCmp('feActivacionDesde').getValue()        != null ||
            Ext.getCmp('feActivacionHasta').getValue()        != null ||
            Ext.getCmp('fePlanificacionDesde').getValue()     != null ||
            Ext.getCmp('fePlanificacionHasta').getValue()     != null
          )
        {
            if( Ext.getCmp('feCreacionPuntoDesde').getValue() != '' || Ext.getCmp('feCreacionPuntoHasta').getValue() != '' )
            {
                if( Ext.getCmp('feCreacionPuntoDesde').getValue() > Ext.getCmp('feCreacionPuntoHasta').getValue() )
                {
                    boolContinuar = false;
                    
                    Ext.Msg.show
                    ({
                        title:'Error en Busqueda',
                        msg: 'La Fecha de Creación de Punto Inicial debe ser menor o igual a la Fecha de Creación de Punto Final.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            
            if( Ext.getCmp('feAprobacionDesde').getValue() != null || Ext.getCmp('feAprobacionHasta').getValue() != null )
            {
                if( Ext.getCmp('feAprobacionDesde').getValue() > Ext.getCmp('feAprobacionHasta').getValue() )
                {
                    boolContinuar = false;
                    
                    Ext.Msg.show
                    ({
                        title:'Error en Busqueda',
                        msg: 'La Fecha de Aprobación Inicial debe ser menor o igual a la Fecha de Aprobación Final.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            
            if( Ext.getCmp('feActivacionDesde').getValue() != '' || Ext.getCmp('feActivacionHasta').getValue() != '' )
            {
                if( Ext.getCmp('feActivacionDesde').getValue() > Ext.getCmp('feActivacionHasta').getValue() )
                {
                    boolContinuar = false;
                    
                    Ext.Msg.show
                    ({
                        title:'Error en Busqueda',
                        msg: 'La Fecha de Activación Inicial debe ser menor o igual a la Fecha de Activación Final.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            
            if( Ext.getCmp('fePlanificacionDesde').getValue() != '' || Ext.getCmp('fePlanificacionHasta').getValue() != '' )
            {
                if( Ext.getCmp('fePlanificacionDesde').getValue() > Ext.getCmp('fePlanificacionHasta').getValue() )
                {
                    boolContinuar = false;
                    
                    Ext.Msg.show
                    ({
                        title:'Error en Busqueda',
                        msg: 'La Fecha de Planificación Inicial debe ser menor o igual a la Fecha de Planificación Final.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                    });
                }
            }
            
            if( boolContinuar )
            {
                storeReporteConsolidado.loadData([],false);
                cargarFiltrosBusquedaAlStore();
                storeReporteConsolidado.currentPage = 1;
                storeReporteConsolidado.load();
            }
        }
        else
        {
            Ext.Msg.show
            ({
                title:'Error en Busqueda',
                msg: 'Debe escoger al menos un criterio de búsqueda.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }


    function limpiar() 
    {
        Ext.getCmp('feAprobacionDesde').value = null;
        Ext.getCmp('feAprobacionDesde').setRawValue(null);
        Ext.getCmp('feAprobacionHasta').value = null;
        Ext.getCmp('feAprobacionHasta').setRawValue(null);
        Ext.getCmp('feActivacionDesde').value = null;
        Ext.getCmp('feActivacionDesde').setRawValue(null);
        Ext.getCmp('feActivacionHasta').value = null;
        Ext.getCmp('feActivacionHasta').setRawValue(null);
        Ext.getCmp('feCreacionPuntoDesde').value = null;
        Ext.getCmp('feCreacionPuntoDesde').setRawValue(null);
        Ext.getCmp('feCreacionPuntoHasta').value = null;
        Ext.getCmp('feCreacionPuntoHasta').setRawValue(null);
        Ext.getCmp('fePlanificacionDesde').value = null;
        Ext.getCmp('fePlanificacionDesde').setRawValue(null);
        Ext.getCmp('fePlanificacionHasta').value = null;
        Ext.getCmp('fePlanificacionHasta').setRawValue(null);
        
        storeReporteConsolidado.loadData([],false);
    }
    
    
    function cargarFiltrosBusquedaAlStore()
    {
        storeReporteConsolidado.getProxy().extraParams.feAprobacionDesde    = Ext.getCmp('feAprobacionDesde').value;
        storeReporteConsolidado.getProxy().extraParams.feAprobacionHasta    = Ext.getCmp('feAprobacionHasta').value;
        storeReporteConsolidado.getProxy().extraParams.feActivacionDesde    = Ext.getCmp('feActivacionDesde').value;
        storeReporteConsolidado.getProxy().extraParams.feActivacionHasta    = Ext.getCmp('feActivacionHasta').value;
        storeReporteConsolidado.getProxy().extraParams.feCreacionPuntoDesde = Ext.getCmp('feCreacionPuntoDesde').value;
        storeReporteConsolidado.getProxy().extraParams.feCreacionPuntoHasta = Ext.getCmp('feCreacionPuntoHasta').value;
        storeReporteConsolidado.getProxy().extraParams.fePlanificacionDesde = Ext.getCmp('fePlanificacionDesde').value;
        storeReporteConsolidado.getProxy().extraParams.fePlanificacionHasta = Ext.getCmp('fePlanificacionHasta').value;
    }
    
    
    function exportarExcel()
    {                
        $('#expFeAprobacionDesde').val(Ext.getCmp('feAprobacionDesde').value 
                                       ? Ext.util.Format.date(Ext.getCmp('feAprobacionDesde').getValue()) : '');
        $('#expFeAprobacionHasta').val(Ext.getCmp('feAprobacionHasta').value 
                                       ? Ext.util.Format.date(Ext.getCmp('feAprobacionHasta').getValue()) : '');
        $('#expFeActivacionDesde').val(Ext.getCmp('feActivacionDesde').value
                                       ? Ext.util.Format.date(Ext.getCmp('feActivacionDesde').getValue()) : '');
        $('#expFeActivacionHasta').val(Ext.getCmp('feActivacionHasta').value
                                       ? Ext.util.Format.date(Ext.getCmp('feActivacionHasta').getValue()) : '');
        $('#expFeCreacionPuntoDesde').val(Ext.getCmp('feCreacionPuntoDesde').value 
                                          ? Ext.util.Format.date(Ext.getCmp('feCreacionPuntoDesde').getValue()) : '');
        $('#expFeCreacionPuntoHasta').val(Ext.getCmp('feCreacionPuntoHasta').value
                                          ? Ext.util.Format.date(Ext.getCmp('feCreacionPuntoHasta').getValue()) : '');
        $('#expFePlanificacionDesde').val(Ext.getCmp('fePlanificacionDesde').value 
                                          ? Ext.util.Format.date(Ext.getCmp('fePlanificacionDesde').getValue()) : '');
        $('#expFePlanificacionHasta').val(Ext.getCmp('fePlanificacionHasta').value
                                          ? Ext.util.Format.date(Ext.getCmp('fePlanificacionHasta').getValue()) : '');
        
        document.forms[0].submit();		
    }
    
});


