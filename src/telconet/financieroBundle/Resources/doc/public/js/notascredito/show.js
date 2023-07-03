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
             {name:'precio', type: 'float'},
             {name:'cantidad', type: 'string'},
             {name:'descuento', type: 'string'},
            ]
}); 

 store = Ext.create('Ext.data.Store', {
    // destroy the store if the grid is destroyed
    autoDestroy: true,
    model: 'ListadoDetalleOrden',
    proxy: {
        type: 'ajax',
        // load remote data using HTTP
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
            width: 130,
            dataIndex: 'precio'
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: 'listado_detalle_factura',
        width: 510,
        height: 200,
        title: 'Detalle de nota de credito',
        frame: true,
        plugins: [cellEditing]
    });
});
