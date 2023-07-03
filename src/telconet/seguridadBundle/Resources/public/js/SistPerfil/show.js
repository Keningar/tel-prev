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
    Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'perfil_id', mapping:'perfil_id'},
            {name:'accion_id', mapping:'accion_id'},
            {name:'accion_nombre'},
            {name:'modulo_id', mapping:'modulo_id'},
            {name:'modulo_nombre'}
        ]
    });
    
    // create the Data Store
    var storeAsignaciones = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoLoad: true,
        model: 'Asignacion',
        proxy: {
            type: 'ajax',
            url: 'gridAsignaciones',
            // specify a XmlReader (coincides with the XML format of the returned data)
            reader: {
                type: 'json',
                totalProperty: 'total',
                // records will have a 'plant' tag
                root: 'asignaciones'
            }
        }
    });
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridRelaciones = Ext.create('Ext.grid.Panel', {
        id:'gridAsignaciones',
        store: storeAsignaciones,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'perfil_id',
            header: 'PerfilId',
            dataIndex: 'perfil_id',
            hidden: true,
            hideable: false
        }, {
            id: 'modulo_id',
            header: 'Modulod',
            dataIndex: 'modulo_id',
            hidden: true,
            hideable: false
        }, {
            id: 'modulo_nombre',
            header: 'Modulo',
            dataIndex: 'modulo_nombre',
            width: 320,
            sortable: true
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
            width: 320,
            sortable: true
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 700,
        height: 400,
        frame: true,
        title: 'Informacion de Asignacion',
        renderTo: 'grid'
    });
                  
    /**************************************************/
    // trigger the data store load          
});