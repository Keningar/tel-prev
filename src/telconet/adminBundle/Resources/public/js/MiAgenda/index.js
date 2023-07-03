/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {      
    
    
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 3,
            align: 'left'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 1300,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],                
                items: 
                [   
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Origen',
                        id: 'sltOrigen',
                        value:'Todos',
                        store: [
                            ['Todos','Todos'],
                            ['Casos','Casos'],
                            ['Planificacion','Planificacion'],
                            ['Soporte','Soporte']
                        ],
                        width: '600'
                    },
                    {html:"&nbsp;",border:false,width:500},   
                ],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */
function buscar(){
    var boolError = false;
       
    if(!boolError)
    {
        eventStore.removeAll();
        eventStore.getProxy().extraParams.startDate = globalStartDate;
        eventStore.getProxy().extraParams.endDate = globalEndDate;
        eventStore.getProxy().extraParams.origen = Ext.getCmp('sltOrigen').value;
        eventStore.load();
    }          
}

function limpiar(){    
    Ext.getCmp('sltOrigen').value="Todos";
    Ext.getCmp('sltOrigen').setRawValue("Todos");
                            
    eventStore.removeAll();
    eventStore.getProxy().extraParams.startDate = globalStartDate;
    eventStore.getProxy().extraParams.endDate = globalEndDate;
    eventStore.getProxy().extraParams.origen = Ext.getCmp('sltOrigen').value;
    eventStore.load();
}
