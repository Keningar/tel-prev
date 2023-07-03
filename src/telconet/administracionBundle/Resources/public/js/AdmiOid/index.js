
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() 
{ 
    Ext.tip.QuickTipManager.init();

    var storeMarcaElemento = new Ext.data.Store
    ({ 
        total: 'total',
        autoLoad:true,
        proxy: 
        {
            type: 'ajax',
            url : strUrlGetMarcas,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                tipoElemento: tipoElemento
            }
        },
        fields:
        [
            {name:'nombreMarcaElemento', mapping:'nombreMarcaElemento'},
            {name:'idMarcaElemento',     mapping:'idMarcaElemento'}
        ]
    });
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'idOid', mapping:'idOid'},
			{name:'nombreOid', mapping:'nombreOid'},
			{name:'marcaElemento', mapping:'marcaElemento'},
			{name:'estado', mapping:'estado'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'}               
		],
        idProperty: 'idOid'
    });
	
    store = new Ext.data.Store({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : 'getEncontrados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreIpv4: '',
                proveedorRed: '',
                estado: 'Todos'
            }
        },
        autoLoad: true
    });
   
    var pluginExpanded = true;
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso = '{{ is_granted("ROLE_120-8") }}';
        var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
	
        var permiso = '{{ is_granted("ROLE_120-9") }}';
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
			//tbfill -> alinea los items siguientes a la derecha
			{ xtype: 'tbfill' },
			eliminarBtn
		]
	});

    grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: 649,
        height: 400,
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: 
        {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        dockedItems: [ toolbar ],                
        columns:
        [
                {
                  id: 'idOid',
                  header: 'idOid',
                  dataIndex: 'idOid',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'marcaElemento',
                  header: 'Marca Elemento',
                  dataIndex: 'marcaElemento',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'nombreOid',
                  header: 'Nombre OId',
                  dataIndex: 'nombreOid',
                  width: 200,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 100,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
						{
	                        getClass: function(v, meta, rec) {
                                    var permiso = '{{ is_granted("ROLE_120-6") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                                    if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
								
								if (rec.get('action1') == "icon-invisible") 
									this.items[0].tooltip = '';
								else 
									this.items[0].tooltip = 'Ver';
									
								return rec.get('action1')
							},
	                        tooltip: 'Ver',
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
                                    var permiso = '{{ is_granted("ROLE_120-6") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                                    if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
																
								if(rec.get('action1')!="icon-invisible")
									window.location = rec.get('idOid')+"/show";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {
	                        getClass: function(v, meta, rec) {
                                    var permiso = '{{ is_granted("ROLE_120-4") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                                    if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if (rec.get('action2') == "icon-invisible") 
	                                this.items[1].tooltip = '';
	                            else 
	                                this.items[1].tooltip = 'Editar';
	                            
	                            return rec.get('action2')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);		
                                    var permiso = '{{ is_granted("ROLE_120-4") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                                    if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if(rec.get('action2')!="icon-invisible")
	                                window.location = rec.get('idOid')+"/edit";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {
	                        getClass: function(v, meta, rec) {
                                    var permiso = '{{ is_granted("ROLE_120-8") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                                    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
                                    var permiso = '{{ is_granted("ROLE_120-9") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                                    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[2].tooltip = '';
	                            else 
	                                this.items[2].tooltip = 'Eliminar';
	                            
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
                                    var permiso = '{{ is_granted("ROLE_120-8") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                                    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
                                    var permiso = '{{ is_granted("ROLE_120-9") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
                                    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if(rec.get('action3')!="icon-invisible")
								{
	                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
	                                    if(btn=='yes'){
	                                        Ext.Ajax.request({
	                                            url: "deleteAjax",
	                                            method: 'post',
	                                            params: { param : rec.get('idOid')},
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
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion'); 
							}
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle:{ background: '#fff' },
        collapsible : true,
        collapsed: true,
        width: 649,
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
                items: [
                        { width: '10%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre Oid',
                            value: '',
                            width: '200px'
                        },
                        { width: '20%',border:false},
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltEstado',
                            value:'Todos',
                            store: [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Modificado','Modificado'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        },
                        { width: '10%',border:false},
                    
                        { width: '10%',border:false}, //inicio
                        {
                            xtype: 'combo',
                            id: 'sltMarcaElemento',
                            fieldLabel: 'Marca Elemento',
                            store: storeMarcaElemento,
                            displayField: 'nombreMarcaElemento',
                            valueField: 'idMarcaElemento',
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        { width: '30%',border:false},
                        { width: '10%',border:false}, //final
                        
                        
                        ],	
        renderTo: 'filtro'
    }); 
    
});

function buscar()
{
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarcaElemento').value;
    store.getProxy().extraParams.nombre        = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado        = Ext.getCmp('sltEstado').value;
    store.load();
}

function limpiar()
{
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    
    Ext.getCmp('sltMarcaElemento').value="";
    Ext.getCmp('sltMarcaElemento').setRawValue("");
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
    store.loadData([],false);
    store.currentPage = 1;
    store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarcaElemento').value;
    store.getProxy().extraParams.nombre        = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado        = Ext.getCmp('sltEstado').value;
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