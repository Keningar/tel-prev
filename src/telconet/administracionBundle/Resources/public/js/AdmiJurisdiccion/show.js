/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var storeCantonJurisdiccion = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getCantonesJurisdicciones',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreCanton', mapping:'nombreCanton'},
                {name:'mailTecnico', mapping:'mailTecnico'},
                {name:'ipReserva', mapping:'ipReserva'},
                {name:'idCantonJurisdiccion', mapping:'idCantonJurisdiccion'}
              ]
    });
    
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridCantonJurisdiccion = Ext.create('Ext.grid.Panel', {
        id:'gridCantonJurisdiccion',
        store: storeCantonJurisdiccion,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'nombreCanton',
            header: 'Nombre Canton',
            dataIndex: 'nombreCanton',
            width: 320,
            sortable: true
        }, {
            id: 'mailTecnico',
            header: 'Mail Tecnico',
            dataIndex: 'mailTecnico',
            width: 120,
            sortable: true
        }, {
            id: 'ipReserva',
            header: 'Ip Reserva',
            dataIndex: 'ipReserva',
            width: 80,
            sortable: true
        }, {
            id: 'idCantonJurisdiccion',
            header: 'idCantonJurisdiccion',
            dataIndex: 'idCantonJurisdiccion',
            hidden: true,
            hideable: false
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 700,
        height: 200,
        frame: true,
        title: 'Relacion Canton - Jurisdiccion',
        renderTo: 'grid'
    });
                  
    /**************************************************/
    
});

