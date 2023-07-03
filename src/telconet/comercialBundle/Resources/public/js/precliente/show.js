Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.QuickTips.init();
        
Ext.onReady(function()
{
    new Ext.Panel
    ({
        id: 'paneltoolBarVip',
        renderTo: 'toolBarVip',
        baseCls:  'x-plain',
        dockedItems:
        [
            {
                xtype:   'toolbar',
                dock:    'top',
                baseCls: 'x-plain',
                items:
                [
                    {
                        xtype: 'button',
                        id: 'btnHistoralCliente',
                        cls: 'icon_cliente_log',
                        tooltip: '<b>Historial Prospecto',
                        handler: function()
                        {
                            verHistorialProspecto();
                        }
                    }
                ]
            }
        ]
    });
});


function verHistorialProspecto()
{
    var dataStoreHistorialCliente = new Ext.data.Store
    ({
        autoLoad: true,
        total: 'total',
        proxy:
        {
            type: 'ajax',
            timeout: 600000,
            url: urlGridHistorialProspecto,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
        [
            {name: 'accion', mapping: 'accion', type: 'string'},
            {name: 'usuario', mapping: 'usuario', type: 'string'},
            {name: 'fecha', mapping: 'fecha', type: 'string'}
        ]
    });

    var gridHistorialProspecto = Ext.create('Ext.grid.Panel',
    {
        id: 'gridHistorialProspecto',
        store: dataStoreHistorialCliente,
        width: 690,
        height: 300,
        collapsible: false,
        multiSelect: true,
        viewConfig: 
        {
            emptyText: '<br><center><b>No hay datos para mostrar',
            forceFit: true,
            stripeRows: true,
            enableTextSelection: true
        },
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
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
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                        
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
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        layout: 'fit',
        region: 'center',
        buttons:
        [
            {
                text: 'Cerrar',
                handler: function()
                {
                    win4.destroy();
                }
            }
        ],
        columns:
        [
            {
                dataIndex: 'accion',
                header: 'Acci\xf3n',
                width: 537
            },
            {
                dataIndex: 'usuario',
                header: 'Usuario',
                width: 100
            },
            {
                dataIndex: 'fecha',
                header: 'Fecha',
                width: 150
            }
        ]
    });

    Ext.create('Ext.form.Panel',
    {
        id: 'formHistorialProspecto',
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults:
        {
            labelAlign: 'left',
            labelWidth: 125,
            msgTarget: 'side'
        },
        items:
        [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:{ width: 700 },
                layout:
                {
                    type: 'table',
                    columns: 3,
                    align: 'left'
                },
                items:[ gridHistorialProspecto ]
            }
        ]
    });

    var win4 = Ext.create('Ext.window.Window',
    {
        title: 'Historial Prospecto',
        modal: true,
        width: 800,
        closable: true,
        layout: 'fit',
        items: [gridHistorialProspecto]
    }).show();
}
