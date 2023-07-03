Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_detalle_interface', mapping:'id_detalle_interface'},
			{name:'id_interface_elemento', mapping:'id_interface_elemento'},
			{name:'puertos', mapping:'puertos'},
			{name:'detalle_nombre', mapping:'detalle_nombre'},
			{name:'detalle_valor', mapping:'detalle_valor'},
			{name:'estado_interfaz', mapping:'estado_interfaz'},						
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'}                
		],
        idProperty: 'id_detalle_interface'
    });
	
    store = new Ext.data.Store({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',        
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : 'getInterfaceElementos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Todos',
		marca: 'Todos'
            }
        },
        autoLoad: true
    });

    var pluginExpanded = true;    
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso = '{{ is_granted("ROLE_119-8") }}';
	var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso ? true : false);		
	
	var permiso = '{{ is_granted("ROLE_119-9") }}';	
	var boolPermiso2 = (typeof permiso === 'undefined') ? false : (permiso ? true : false);		
	
	var eliminarBtn = "";
	sm = "";
	if(boolPermiso1 && boolPermiso2)
	{
	    sm = Ext.create('Ext.selection.CheckboxModel', {
	        checkOnly: true
	    })
	
		eliminarBtn = Ext.create('Ext.button.Button', {
			iconCls: 'icon_delete',
			text: 'Eliminar',
			itemId: 'deleteAjax',
		    text    : 'Eliminar',
		    scope   : this,
			handler: function(){ eliminarAlgunos();}
		});
	}
	
	var toolbar = Ext.create('Ext.toolbar.Toolbar', {
		dock: 'top',
		align: '->',
		items   : 
		[ 
			{
				iconCls: 'icon_add',
				text: 'Seleccionar Todos',
				itemId: 'select',
				scope: this,
				handler: function(){ 
					Ext.getCmp('grid').getPlugin('pagingSelectionPersistence').selectAll()
				}
			},
			{
				iconCls: 'icon_limpiar',
				text: 'Borrar Todos',
				itemId: 'clear',
				scope: this,
				handler: function(){ 
					Ext.getCmp('grid').getPlugin('pagingSelectionPersistence').clearPersistedSelection()
				}
			},			
			{ xtype: 'tbfill' },
			eliminarBtn
		]
	});

    grid = Ext.create('Ext.grid.Panel', {
        id: 'grid',
        width: 450,
        height: 400,
        store: store,
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        columns: [
            {
                id: 'id_detalle_interface',
                header: 'id_detalle_interface',
                dataIndex: 'id_detalle_interface',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_interface_elemento',
                header: 'id_interface_elemento',
                dataIndex: 'id_interface_elemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'puertos',
                header: 'Puertos',
                dataIndex: 'puertos',
                width: 100,
                sortable: true
            },
            {
                id: 'detalle_nombre',
                header: 'Modulos',
                dataIndex: 'detalle_nombre',
                width: 130,
                sortable: true
            },
            {
                id: 'detalle_valor',
                header: 'Numero Asignado',
                dataIndex: 'detalle_valor',
                width: 100,
                sortable: true
            },
            {
                id: 'estado_interfaz',
                header: 'Estado Modulo',
                dataIndex: 'estado_interfaz',
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
        title: 'Interfaces del Elemento',
        renderTo: 'grid'
    });
   
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
     
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.getProxy().extraParams.marca  = Ext.getCmp('cmb_marca').value;
    store.load();
}

function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    Ext.getCmp('cmb_marca').value="Todos";
    Ext.getCmp('cmb_marca').setRawValue("Todos");
    
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.getProxy().extraParams.marca  = Ext.getCmp('cmb_marca').value;
    store.load();
}

function eliminarAlgunos(){
    var param = '';
	var selection = grid.getPlugin('pagingSelectionPersistence').getPersistedSelection();
	
    if(selection.length > 0)
    {
		var estado = 0;
		for(var i=0 ;  i < selection.length ; ++i)
		{
			param = param + selection[i].getId();
			
			if(i < (selection.length -1))
			{
				param = param + '|';
			}
		}
		
		if(estado == 0)
		{
			Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
				if(btn=='yes'){
					Ext.Ajax.request({
						url: "deleteAjax",
						method: 'post',
						params: { param : param},
						success: function(response){
							var text = response.responseText;
							store.load();
						},
						failure: function(result)
						{
							Ext.Msg.alert('Error ','Error: ' + result.statusText);
						}
					});
				}
			});
		}
		else
		{
			alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
		}
    }
    else
    {
        alert('Seleccione por lo menos un registro de la lista');
    }
}