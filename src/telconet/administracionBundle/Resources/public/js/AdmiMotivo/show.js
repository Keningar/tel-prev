/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {    
    /* ****************** MOTIVOS ************************ */
    Ext.define('Motivos', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_motivo', mapping:'id_motivo'},
            {name:'relacionsistema_id', mapping:'relacionsistema_id'},
            {name:'nombre_motivo', mapping:'nombre_motivo'}
        ]
    });
    storeMotivos = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        autoLoad: false,
        model: 'Motivos',        
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: '../getListadoMotivos',
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
    gridMotivos = Ext.create('Ext.grid.Panel', {
        id:'gridMotivos',
        store: storeMotivos,
        columnLines: true,
        columns: [{
            id: 'id_motivo',
            header: 'MotivoId',
            dataIndex: 'id_motivo',
            hidden: true,
            hideable: false
        }, {
            id: 'relacionsistema_id',
            header: 'RelacionSistemaId',
            dataIndex: 'relacionsistema_id',
            hidden: true,
            hideable: false
        }, {
            id: 'nombre_motivo',
            header: 'Motivo',
            dataIndex: 'nombre_motivo',
            width: 320,
            sortable: true,
            editor: {
                id:'searchAccion_cmp',
                xtype: 'textfield',
                typeAhead: true,
                displayField:'nombre_motivo',
                valueField: 'id_motivo',
                size: 300
            } 
        }
        ],

        width: 700,
        height: 200,
        frame: true,
        title: 'Motivos',
        renderTo: 'grid'
    });
});