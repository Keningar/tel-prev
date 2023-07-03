/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var storeTareas = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getTareas',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idTarea', mapping: 'idTarea'},
                {name:'nombreTarea', mapping:'nombreTarea'},
                {name:'nombreTareaAnterior', mapping:'nombreTareaAnterior'},
                {name:'nombreTareaSiguiente', mapping:'nombreTareaSiguiente'},
                {name:'tiempoMax', mapping:'tiempoMax'},
                {name:'costo', mapping:'costo'},
                {name:'peso', mapping:'peso'},
                {name:'estado', mapping:'estado'}
              ]
    });
    
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridTareas = Ext.create('Ext.grid.Panel', {
        id:'gridTareas',
        store: storeTareas,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'idTarea',
            header: 'idTarea',
            dataIndex: 'idTarea',
            hidden: true,
            hideable: false
        }, {
            id: 'nombreTarea',
            header: 'Nombre Tarea',
            dataIndex: 'nombreTarea',
            width: 200,
            sortable: true
        },{
            id: 'nombreTareaAnterior',
            header: 'Tarea Anterior',
            dataIndex: 'nombreTareaAnterior',
            width: 180,
            sortable: true
        },{
            id: 'nombreTareaSiguiente',
            header: 'Tarea Siguiente',
            dataIndex: 'nombreTareaSiguiente',
            width: 180,
            sortable: true
        },{
            id: 'tiempoMax',
            header: 'Tiempo Maximo',
            dataIndex: 'tiempoMax',
            width: 90,
            sortable: true
        },{
            id: 'peso',
            header: 'Peso %',
            dataIndex: 'peso',
            width: 50,
            sortable: true
        },{
            id: 'estado',
            header: 'Estado',
            dataIndex: 'estado',
            width: 100,
            sortable: true
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 850,
        height: 200,
        frame: true,
        title: 'Tareas del Proceso',
        renderTo: 'grid'
    });
                  
    /**************************************************/
    
});
