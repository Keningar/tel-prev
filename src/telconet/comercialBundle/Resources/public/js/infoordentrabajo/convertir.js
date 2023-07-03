Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var gridVentaNormal         = null;
var gridVentaExterna        = null;
var selectionVentaNormal    = null;
var selectionVentaExterna   = null;
var itemsPerPage            = 1000;
var storeVenta              = '';
var area_id                 = '';
var login_id                = '';
var tipo_asignacion         = '';
var pto_sucursal            = '';
var idClienteSucursalSesion;
var strOTClienteConDeuda    = 'N';

Ext.onReady(function()
{  
    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields:
        [
            {name:'id',                              type: 'string'},
            {name:'descripcion',                     type: 'string'},
            {name:'cantidad',                        type: 'string'},
            {name:'estado',                          type: 'string'},
            {name:'precio',                          type: 'string'},
            {name:'strTipoRed',                      type: 'string'},
            {name:'strEsCheckeable',                 type: 'string'},
            {name:'strEsVenta',                      type: 'string'},
            {name:'strExisteServicioInternet',       type: 'string'},
            {name:'strExistenServiciosVentaExterna', type: 'string'}
        ]
    });
    
    store = Ext.create('Ext.data.JsonStore',
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy:
        {
            type: 'ajax',
            url: strUrlStore,
            timeout: 120000,
            reader:
            {
                type: 'json',
                root: 'listado',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },
        listeners:
        {
            load: function(store)
            {
                if( !Ext.isEmpty(store.getAt(0)) )
                {
                    var objRecord = store.getAt(0).data;

                    document.getElementById("mensajeError").style.display             = 'none';
                    document.getElementById("sinServiciosInternet").style.display     = 'none';
                    document.getElementById("preServiciosVentaExterna").style.display = 'none';

                    if( objRecord.strExisteServicioInternet == "N" || objRecord.strExistenServiciosVentaExterna == "S" )
                    {
                        document.getElementById("mensajeError").style.display = '';

                        if( objRecord.strExisteServicioInternet == "N" )
                        {
                            document.getElementById("sinServiciosInternet").style.display = '';
                        }

                        if( objRecord.strExistenServiciosVentaExterna == "S" )
                        {
                            document.getElementById("preServiciosVentaExterna").style.display = '';
                        }
                    }
                }
            }
        }
    });
    
    
    /*
     * GRID VENTA NORMAL
     */
    selectionVentaNormal = new Ext.selection.CheckboxModel
    ({
        renderer: function(value, metaData, record, rowIndex, colIndex, store, view)
        {
            if( record.get('strEsCheckeable') == 'N' )
            {
                return '<div>&nbsp;</div>';
            }
            else
            {
                return '<div class="'+Ext.baseCSSPrefix+'grid-row-checker">&#160;</div>';
            }
        },
        listeners:
        {
            beforeselect:function(selModel, record, index)
            {
                if( record.get('strEsCheckeable') == 'N' )
                {
                    return false;
                }
            },
            selectionchange: function(sm, selections)
            {
                gridVentaNormal.down('#aprobar').setDisabled( selections.length == 0 );
            }
        }
    });

    gridVentaNormal = Ext.create('Ext.grid.Panel',
    {
        width:800,
        height:275,
        collapsible:false,
        title: '',
        selModel: selectionVentaNormal,
        dockedItems:
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items:
            [
                { xtype: 'tbfill' },
                {
                    iconCls: 'icon_aprobar',
                    text: 'Aprobar',
                    itemId: 'aprobar',
                    scope: this,
                    disabled: true,
                    handler: function()
                    {
                        aprobarAlgunos(selectionVentaNormal);
                    }
                }
            ]
        }],
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando servicios {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),	
        store: store,
        multiSelect: true,
        viewConfig:
        {
            emptyText: 'No hay datos para mostrar'
        },
        listeners:
        {
            viewready: function(grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e)
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
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if (header.dataIndex != null)
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if (view.getRecord(parent).get(columnDataIndex) != null)
                                    {
                                        var columnText = view.getRecord(parent).get(columnDataIndex).toString();

                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function()
                        {
                            grid.tip.hide();
                        }, 500);
                    });

                    grid.tip.getEl().on('mouseover', function()
                    {
                        window.clearTimeout(timeout);
                    });

                    Ext.get(view.el).on('mouseover', function()
                    {
                        window.clearTimeout(timeout);
                    });

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function()
                        {
                            grid.tip.hide();
                        }, 500);
                    });
                });
            }
        },
        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'strEsVenta',
                width: 100,
                hidden: true
            },
            {
                text: 'Servicio',
                width: 301,
                dataIndex: 'descripcion'
            },
            {
                text: 'Tipo Red',
                width: 90,
                dataIndex: 'strTipoRed'
            },
            {
                text: 'Cantidad',
                width: 100,
                dataIndex: 'cantidad'
            },
            {
                text: 'Precio',
                width: 100,
                dataIndex: 'precio'
            },
            {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'right',
                width: 140
            }
        ]
    });
    /*
     * FIN GRID VENTA NORMAL
     */
    
    
    /*
     * GRID VENTA EXTERNA
     */
    selectionVentaExterna = new Ext.selection.CheckboxModel
    ({
        renderer: function(value, metaData, record, rowIndex, colIndex, store, view)
        {
            if( record.get('strEsCheckeable') == 'N' )
            {
                return '<div>&nbsp;</div>';
            }
            else
            {
                return '<div class="'+Ext.baseCSSPrefix+'grid-row-checker">&#160;</div>';
            }
        },
        listeners:
        {
            beforeselect:function(selModel, record, index)
            {
                if( record.get('strEsCheckeable') == 'N' )
                {
                    return false;
                }
            },
            selectionchange: function(sm, selections)
            {
                gridVentaExterna.down('#aprobar').setDisabled( selections.length == 0 );
            }
        }
    });

    gridVentaExterna = Ext.create('Ext.grid.Panel',
    {
        width:800,
        height:275,
        collapsible:false,
        title: '',
        selModel: selectionVentaExterna,
        dockedItems:
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items:
            [
                { xtype: 'tbfill' },
                {
                    iconCls: 'icon_aprobar',
                    text: 'Aprobar',
                    itemId: 'aprobar',
                    scope: this,
                    disabled: true,
                    handler: function()
                    {
                        aprobarAlgunos(selectionVentaExterna);
                    }
                }
            ]
        }],
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando servicios {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),	
        store: store,
        multiSelect: true,
        viewConfig:
        {
            emptyText: 'No hay datos para mostrar'
        },
        listeners:
        {
            viewready: function(grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e)
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
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if (header.dataIndex != null)
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if (view.getRecord(parent).get(columnDataIndex) != null)
                                    {
                                        var columnText = view.getRecord(parent).get(columnDataIndex).toString();

                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function()
                        {
                            grid.tip.hide();
                        }, 500);
                    });

                    grid.tip.getEl().on('mouseover', function()
                    {
                        window.clearTimeout(timeout);
                    });

                    Ext.get(view.el).on('mouseover', function()
                    {
                        window.clearTimeout(timeout);
                    });

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function()
                        {
                            grid.tip.hide();
                        }, 500);
                    });
                });
            }
        },
        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'strEsVenta',
                width: 100,
                hidden: true
            },
            {
                text: 'Servicio',
                width: 391,
                dataIndex: 'descripcion'
            },
            {
                text: 'Cantidad',
                width: 110,
                dataIndex: 'cantidad'
            },
            {
                text: 'Precio',
                width: 130,
                dataIndex: 'precio'
            },
            {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'right',
                width: 100
            }
        ]
    });
    /*
     * FIN GRID VENTA EXTERNA
     */
    
    
    var tabsVentas = Ext.create('Ext.tab.Panel',
    {
        id: 'tab_panel',
        width: 800,
        columns: 3,
        autoScroll: true,
        activeTab: 0,
        colspan: 5,
        defaults: {autoHeight: true},
        plain: true,
        deferredRender: false,
        hideMode: 'offsets',
        frame: false,
        buttonAlign: 'center',
        items:
        [
            {
                contentEl: 'fieldsTabVentasNormales',
                title: 'Ventas Normales',
                id: 'idTabVentasNormales',
                hidden: boolTabHiddenVentaNormal,
                layout:
                {
                    type: 'table',
                    columns: 3,
                    align: 'left'
                },
                items:[ gridVentaNormal ],
                closable: false,
                listeners: 
                {
                    activate: function(selModel, Cmp)
                    {
                        store.loadData([],false);
                        store.getProxy().extraParams.strEsVenta = 'NORMAL';
                        store.load();
                    }
                }
            },
            {
                contentEl: 'fieldsTabVentasExternas',
                title: 'Ventas Externas',
                id: 'idTabVentaExterna',
                hidden: boolTabHiddenVentaExterna,
                layout:
                {
                    type: 'table',
                    columns: 3,
                    align: 'left'
                },
                items:[ gridVentaExterna ],
                closable: false,
                listeners: 
                {
                    activate: function(selModel, Cmp)
                    {
                        store.loadData([],false);
                        store.getProxy().extraParams.strEsVenta = 'EXTERNA';
                        store.load();
                    }
                }
            }
        ]
    });

    if ("S" == strMuestraGridOT)
    {
        var objVentasPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 7,
            border: false,
            bodyStyle:
            {
                background: '#fff'
            },
            collapsible: false,
            collapsed: false,
            title: '',
            width: 900,
            items:[tabsVentas],
            renderTo: 'listaVentas'
        });
    }
});


function aprobarAlgunos(selectionModel)
{
    Ext.MessageBox.show
    ({
        msg: 'Validando los datos, Por favor espere!!',
        progressText: 'Guardando...',
        width:300,
        wait:true,
        waitConfig: {interval:200}
    });

    var strIdServicios                   = '';
    var intCantidadServiciosVentaExterna = 0;
    
    if(selectionModel.getSelection().length > 0)
    {
        for(var i=0 ;  i < selectionModel.getSelection().length ; ++i)
        {
            if( selectionModel.getSelection()[i].data.strEsVenta == 'E' )
            {
                intCantidadServiciosVentaExterna++;
            }
            
            strIdServicios = strIdServicios + selectionModel.getSelection()[i].data.id;
            
            if( i < (selectionModel.getSelection().length -1) )
            {
                strIdServicios = strIdServicios + '|';
            }
        }
        
        /*
         * SE VERIFICA SI EXISTEN SERVICIOS DE VENTA EXTERNA SELECCIONADOS PARA VALIDAR QUE ESTEN ASOCIADOS A UN DOCUMENTO Y QUE TODOS LOS SERVICIOS
         * DE VENTA EXTERNA ESTEN SELECCIONADOS
         */
        if( intCantidadServiciosVentaExterna > 0 )
        {
            Ext.Ajax.request
            ({
                url: strUrlValidarServiciosVentaExterna,
                timeout: 9000000,
                method: 'post',
                params:
                { 
                    intIdPtoCliente: intIdPtoCliente,
                    intCantidadServiciosVentaExterna: intCantidadServiciosVentaExterna
                },
                success: function(response)
                {
                    var text = response.responseText;
                    
                    if( "OK" == text )
                    {
                        Ext.MessageBox.hide();
                        aprobarOrdenesTrabajoSeleccionadas(strIdServicios);
                    }
                    else
                    {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Atención', text);
                    }
                },
                failure: function(result)
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error', 'Error: ' + result.statusText);
                }
            });
        }
        else
        {
            aprobarOrdenesTrabajoSeleccionadas(strIdServicios);
        }
    }
    else
    {
        Ext.Msg.alert('Atención', 'Seleccione por lo menos un servicio de la lista');
    }
}


function aprobarOrdenesTrabajoSeleccionadas(strIdServicios)
{

    strMensaje = "";
    if (intPuntosDeuda > 0)
    {
        strMensaje = "</br><b>CLIENTE CON DEUDA</b>: </br>" + strMensajeObservacion + "</br>";
        strOTClienteConDeuda = "S";
    }
    else
    {
        strOTClienteConDeuda = "N";
    }

    Ext.Msg.confirm('Atención',strMensaje + 'Se generarán las ordenes de trabajo de los servicios seleccionados. Desea continuar?', function(btn)
    {
        if( btn == 'yes' )
        {
            Ext.MessageBox.show
            ({
                msg: 'Generando las ordenes de trabajo, Por favor espere!!',
                progressText: 'Generando...',
                width:300,
                wait:true,
                waitConfig: {interval:200}
            });

            Ext.Ajax.request
            ({
                url: strUrlAprobar,
                timeout: 9000000,
                method: 'post',
                params:
                { 
                    param                : strIdServicios,
                    strMensajeObservacion: strMensajeObservacion,
                    strOTClienteConDeuda : strOTClienteConDeuda
                },
                success: function(response)
                {
                    var text = response.responseText;
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Atención', text);
                    store.load();
                },
                failure: function(result)
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error', 'Error: ' + result.statusText);
                }
            });
        }
    });
}
