/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Ext.onReady(function(){
    
    Ext.tip.QuickTipManager.init(); 

    var storeElementosPorPop = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getElementosPorPop',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreElemento', mapping:'nombreElemento'},
                {name:'modeloElemento', mapping:'modeloElemento'},
                {name:'tipoElemento', mapping:'tipoElemento'},
                {name:'ip', mapping:'ip'},
                {name:'estado', mapping:'estado'}
              ]
    });
    
    gridElementosPorPop = Ext.create('Ext.grid.Panel', {
        id:'gridElementosPorPop',
        store: storeElementosPorPop,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'nombreElemento',
            header: 'Nombre',
            dataIndex: 'nombreElemento',
            width: 150,
            sortable: true
        }, {
            id: 'modeloElemento',
            header: 'Modelo',
            dataIndex: 'modeloElemento',
            width: 120,
            sortable: true
        },{
            id: 'tipoElemento',
            header: 'Tipo',
            dataIndex: 'tipoElemento',
            width: 120,
            sortable: true
        },{
            id: 'ip',
            header: 'Ip',
            dataIndex: 'ip',
            width: 150,
            sortable: true
        }, {
            id: 'estado',
            header: 'Estado',
            dataIndex: 'estado',
            width: 150,
            sortable: true
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 850,
        height: 200,
        frame: true,
        title: 'Elementos',
        renderTo: 'gridElementos'
    });
    
    //-------------------------------------------------------------------------------
    
});
