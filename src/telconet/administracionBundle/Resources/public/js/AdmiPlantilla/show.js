Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
    

         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
            [
                {name: 'id_alias', mapping: 'id_alias'},
                {name: 'valor', mapping: 'valor'},
                {name: 'estado', mapping: 'estado'},
                {name: 'empresa', mapping: 'empresa'},
                {name: 'jurisdiccion', mapping: 'jurisdiccion'},
                {name: 'departamento', mapping: 'departamento'},
                {name: 'esCC', mapping: 'esCC'}
            ],
        idProperty: 'id_alias'
    });
	
    store = new Ext.data.Store({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
	    timeout: 600000,
            url : '/administracion/comunicacion/admi_plantilla/getPlantillaAlias',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                id: idPlantilla                
            }
        },
        autoLoad: true
    });
  

    grid = Ext.create('Ext.grid.Panel', {
        id: 'grid',
        width: 860,
        height: 250,
        store: store,
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            loadMask: true
        },
        columns: [
            {
                id: 'id_alias',
                header: 'IdAlias',
                dataIndex: 'id_alias',
                hidden: true,
                hideable: false
            },
            {
                id: 'valor',
                header: 'Valor',
                dataIndex: 'valor',
                width: 237,
                sortable: true
            },
            {
                header: 'Es Copia',
                dataIndex: 'esCC',
                width: 60,
                sortable: true
            },
            {
                header: 'Empresa',
                dataIndex: 'empresa',
                width: 150,
                sortable: true
            },
            {
                header: 'Ciudad',
                dataIndex: 'jurisdiccion',
                width: 150,
                sortable: true
            },
            {
                header: 'Departamento',
                dataIndex: 'departamento',
                width: 150,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 100,
                sortable: true
            },
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            displayInfo: true,
            store: store,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        frame: true,
        title: 'Aliases',
        renderTo: 'aliases'
    });
   
});

function verPlantilla(){    
     
     formPanel = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 400,
			width:650,
			layout: 'fit',			
			items: [{
				xtype: 'fieldset',				
				defaultType: 'textfield',
				items: [					
					{
					    xtype: 'textarea',
					    id: 'plantillaPanel',
					    readOnly:true,
					    height: 400,
					    width:650,
					    value:document.getElementById('plantilla_hd').value
					}
				]
			}]
		 });
       
	
	btncancelar = Ext.create('Ext.Button', {
            text: 'Aceptar',
            cls: 'x-btn-rigth',
            handler: function() {			     
		      winAliases.destroy();													
            }
    });        
	
	winAliases = Ext.create('Ext.window.Window', {
			title: 'Plantilla Notificacion',
			modal: true,
			width: 700,
			height: 515,
			resizable: true,
			layout: 'fit',
			items: [formPanel],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show();       
}
