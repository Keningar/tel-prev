var globalStartDate = "";
var globalEndDate = "";

Ext.onReady(function(){
    eventStore = Ext.create('Extensible.calendar.data.EventStore', {
        autoLoad: true,
        proxy: {
            type: 'rest',
            url: 'getTareasAgenda',
            noCache: false,
            timeout: 900000,
            reader: {
                type: 'json',
                root: 'encontrados'
            },
            
            writer: {
                type: 'json',
                nameProperty: 'mapping'
            }
        },
        listeners: {
            'write': function(store, operation){
                var title = Ext.value(operation.records[0].data[Extensible.calendar.data.EventMappings.Title.name], '(No title)');
                switch(operation.action){
                    case 'create':
                        Extensible.example.msg('Add', 'Added "' + title + '"');
                        break;
                    case 'update':
                        Extensible.example.msg('Update', 'Updated "' + title + '"');
                        break;
                    case 'destroy':
                        Extensible.example.msg('Delete', 'Deleted "' + title + '"');
                        break;
                }
            }
        }
    });
    
    var cp = Ext.create('Extensible.calendar.CalendarPanel', {
        eventStore: eventStore,
        renderTo: 'calendar1',
        title: 'Agenda',
        width: 1000,
        height: 500
    });
    combo_estados = new Ext.form.ComboBox({
        id: 'cmb_estado',
        name: 'cmb_estado',
        fieldLabel: 'Estado',
        anchor: '100%',
        queryMode:'local',
        width: 600,
        store:[
            ['Asignada','Asignada'],
            ['Aceptada','Aceptada'],
            ['Rechazada','Rechazada'],
            ['Finalizada','Finalizada']
        ],
        displayField: 'nombre_estado',
        valueField: 'id_estado'
    });
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
        width: 1000,
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
                    combo_estados,
                    {html:"&nbsp;",border:false,width:500},
                    
                ],	
        renderTo: 'filtro'
    }); 
    
    
});

function buscar(){
    
    eventStore.removeAll();
    eventStore.getProxy().extraParams.startDate = globalStartDate;
    eventStore.getProxy().extraParams.endDate = globalEndDate;
    eventStore.getProxy().extraParams.estado= Ext.getCmp('cmb_estado').getValue();
    eventStore.load();
}

function limpiar(){
    Ext.getCmp('estado').setRawValue("");
    
    eventStore.removeAll();
    eventStore.getProxy().extraParams.startDate = globalStartDate;
    eventStore.getProxy().extraParams.endDate = globalEndDate;
    eventStore.getProxy().extraParams.estado= Ext.getCmp('cmb_estado').getValue();
    eventStore.load();
}