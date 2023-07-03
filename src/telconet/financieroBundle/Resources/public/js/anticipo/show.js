Ext.require([
    '*'
]);

Ext.onReady(function(){
    Ext.define('InfoPagoDetModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'formaPago', type: 'string'},
            {name: 'banco', type: 'string'},
            {name: 'tipoCuenta', type: 'string'},
            {name: 'numeroCta', type: 'string'},            
            {name: 'referencia', type: 'string'},
            {name: 'numeroCtaEmpresa', type: 'string'},             
            {name: 'valor', type: 'float'},
            {name: 'feDeposito', type: 'string'},            
            {name: 'comentario', type: 'string'}
        ]
    });
    
    storeDetalle = Ext.create('Ext.data.Store', {
        autoDestroy: true,
        model: 'InfoPagoDetModel',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_grid,
            reader: {
                type: 'json',
                root: 'detalles'
            }             
        }
    });

    grid = Ext.create('Ext.grid.Panel', {
        store: storeDetalle,
        columns: [ {
            text: 'Forma Pago',
            dataIndex: 'formaPago',
            width: 150,
            align: 'right'
        }, {
            text: 'Banco',
            dataIndex: 'banco',
            width: 150,
            align: 'right'
        }, {
            text: 'Tipo Cuenta',
            dataIndex: 'tipoCuenta',
            width: 150,
            align: 'right'
        }, {
            text: '#Cta',
            dataIndex: 'numeroCta',
            width: 80,
            align: 'right'
        }, {
            text: '#Doc',
            dataIndex: 'referencia',
            width: 90,
            align: 'right'
        }, {
            text: 'CtaEmpresa',
            dataIndex: 'numeroCtaEmpresa',
            width: 80,
            align: 'right'
        }, {
            text: 'Valor',
            dataIndex: 'valor',
            width: 45,
            align: 'right'
        }, {
            text: 'Fecha Proceso',
            dataIndex: 'feDeposito',
            width: 85,
            align: 'right'
        }, {
            text: 'Comentarios',
            dataIndex: 'comentario',
            width: 190,
            align: 'left'
        }],
        listeners:
        {    
            viewready: function (grid) 
            {
                var view = grid.view;
                // record the current cellIndex
                grid.mon(view, 
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
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
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_detalles'),
        width: 1050,
        height: 200,
        title: ''
    });
});
