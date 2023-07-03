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
            {name: 'referencia', type: 'string'},
            {name: 'valor', type: 'float'}            
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
            text: 'Numero',
            dataIndex: 'referencia',
            width: 150,
            align: 'right'
        }, {
            text: 'Valor',
            dataIndex: 'valor',
            width: 100,
            align: 'right'
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_detalles'),
        width: 850,
        height: 200,
        title: ''
    });
});
