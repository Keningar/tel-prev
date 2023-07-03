/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    // custom summary renderer example
    function totalRelaciones(v, params, data)
    {
        params.attr = 'ext:qtip="No. Total de Relaciones"'; // summary column tooltip example
        return v? ((v === 0 || v > 1) ? '(' + v +' Relaciones)' : '(1 Relacion)') : '';
    }
    
    /*******************Creacion Grid******************/
    ////////////////Grid  Relaciones////////////////
    Ext.define('Relacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'modulo_id', mapping:'modulo_id'},
            {name:'accion_id', mapping:'accion_id'},
            {name:'accion_nombre'},
            {name:'item_menu_id', mapping:'item_menu_id'},
            {name:'item_menu_nombre'},
            {name:'tarea_id', mapping:'tarea_id'},
            {name:'tarea_nombre'}
        ]
    });
    
    // create the Data Store
    var storeRelaciones = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoLoad: true,
        model: 'Relacion',
        proxy: {
            type: 'ajax',
            url: 'gridRelaciones',
            // specify a XmlReader (coincides with the XML format of the returned data)
            reader: {
                type: 'json',
                totalProperty: 'total',
                // records will have a 'plant' tag
                root: 'encontrados'
            }
        }
    });
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridRelaciones = Ext.create('Ext.grid.Panel', {
        id:'gridRelaciones',
        store: storeRelaciones,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'modulo_id',
            header: 'ModuloId',
            dataIndex: 'modulo_id',
            hidden: true,
            hideable: false
        }, {
            id: 'accion_id',
            header: 'AccionId',
            dataIndex: 'accion_id',
            hidden: true,
            hideable: false
        }, {
            id: 'accion_nombre',
            header: 'Accion',
            dataIndex: 'accion_nombre',
            width: 220,
            sortable: true
        }, {
            id: 'item_menu_id',
            header: 'ItemMenuId',
            dataIndex: 'item_menu_id',
            hidden: true,
            hideable: false
        }, {
            id: 'item_menu_nombre',
            header: 'Item Menu',
            dataIndex: 'item_menu_nombre',
            width: 220,
            sortable: true
        }, {
            id: 'tarea_id',
            header: 'TareaId',
            dataIndex: 'tarea_id',
            hidden: true,
            hideable: false
        }, {
            id: 'tarea_nombre',
            header: 'Tarea/Modelo',
            dataIndex: 'tarea_nombre',
            width: 220,
            sortable: true
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 700,
        height: 200,
        frame: true,
        title: 'Informacion de Relacion',
        renderTo: 'grid'
    });
                  
    /**************************************************/
    
});

