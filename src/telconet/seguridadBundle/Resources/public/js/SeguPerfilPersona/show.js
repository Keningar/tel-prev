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
            {name:'id_perfil', mapping:'id_perfil'},
            {name:'nombre_perfil', mapping:'nombre_perfil'},
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
                    id: 'id_perfil',
                    header: 'PerfilId',
                    dataIndex: 'id_perfil',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'nombre_perfil',
                    header: 'Perfil',
                    dataIndex: 'nombre_perfil',
                    width: 320,
                    sortable: true
                } 
        ],        
        viewConfig:{
            stripeRows:true
        },
        width: 700,
        height: 500,
        frame: true,
        title: 'Informacion de Perfiles Asignados a la Persona',
        renderTo: 'grid'
    });
                  
    /**************************************************/
    // trigger the data store load          
});