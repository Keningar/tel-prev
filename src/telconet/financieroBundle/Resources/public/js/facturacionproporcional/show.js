/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function(){     

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    clicksToEdit: 1
});
 
Ext.define('ListadoDetalleOrden', {
    extend: 'Ext.data.Model',
    fields: [
             {name:'informacion', type: 'string'},
             {name:'login', type: 'string'},
             {name:'precio', type: 'string'},
             {name:'cantidad', type: 'string'},
             {name:'descuento', type: 'string'},
             {name:'observacion', type: 'string'},
            ]
}); 

 store = Ext.create('Ext.data.Store', {
    // destroy the store if the grid is destroyed
    autoDestroy: true,
    model: 'ListadoDetalleOrden',
    proxy: {
        type: 'ajax',
        timeout: 9000000,
        url: url_listar_informacion_existente,
        reader: {
            type: 'json',
            root: 'listadoInformacion'
            // records will have a 'plant' tag
        },
        extraParams:{facturaid:factura_id},
        simpleSortMode: true               
    },
});

store.load();

// create the grid and specify what field you want
// to use for the editor at each header.
grid = Ext.create('Ext.grid.Panel', {
        store:store,
        columns: [new Ext.grid.RowNumberer(), 
        {
            text: 'Producto/Plan',
            width: 200,
            dataIndex: 'informacion'
        }
    ,{
        text: 'Login',
        dataIndex: 'login',
        align: 'left',
        width: 180			
    },{
        text: 'Descripcion',
        width: 200,
        dataIndex: 'observacion'
    },{
            text: 'Cantidad',
            dataIndex: 'cantidad',
            align: 'right',
            width: 70			
        },{
            text: 'Descuento',
            dataIndex: 'descuento',
            align: 'right',
            width: 70			
        },{
            text: 'Precio',
            width: 80,
            dataIndex: 'precio',
            align: 'right'
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: 'listado_detalle_factura',
        width: 900,
        height: 200,
        title: 'Detalle de factura',
        frame: true,
        plugins: [cellEditing],
        viewConfig: 
        {
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
        }
    });
});
