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
			 {name:'motivo', type: 'string'},
             {name:'observacion', type: 'string'},
             {name:'valor', type: 'string'},
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
            text: 'Motivo',
            width: 200,
            dataIndex: 'motivo'
        },{
            text: 'Observacion',
            dataIndex: 'observacion',
            align: 'right',
            width: 70			
        },{
            text: 'Valor',
            dataIndex: 'valor',
            align: 'right',
            width: 70			
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: 'listado_detalle_nota_debito',
        width: 510,
        height: 200,
        title: 'Detalle de nota de debito',
        frame: true,
        plugins: [cellEditing]
    });
});
