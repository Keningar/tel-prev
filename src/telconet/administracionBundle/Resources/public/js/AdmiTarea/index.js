/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
       
    /* ****************** PROCESO PADRE ************************ */
    Ext.define('ProcesosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_proceso', type:'int'},
            {name:'nombre_proceso', type:'string'}
        ]
    });
    storeProcesos = Ext.create('Ext.data.Store', {
        pageSize: 200,
		model: 'ProcesosList',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : 'getProcesos',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    
    combo_procesos = new Ext.form.ComboBox({
		id: 'cmb_proceso',
		name: 'cmb_proceso',
		fieldLabel: 'Proceso: ',
		anchor: '100%',
		queryMode:'remote',
		width: 425,
		emptyText: 'Seleccione Proceso',
		store:storeProcesos,
		displayField: 'nombre_proceso',
		valueField: 'id_proceso'
    });
    
	
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_tarea', mapping:'id_tarea'},
			{name:'nombre_proceso', mapping:'nombre_proceso'},
			{name:'nombre_rol_autoria', mapping:'nombre_rol_autoria'},
			{name:'nombre_tarea', mapping:'nombre_tarea'},
			{name:'peso', mapping:'peso'},
			{name:'costo', mapping:'costo'},
			{name:'precio_promedio', mapping:'precio_promedio'},
			{name:'estado', mapping:'estado'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'}                 
		],
        idProperty: 'id_tarea'
    });
	
    store = new Ext.data.Store({ 
        //pageSize: 10,
        model: 'ModelStore',
        total: 'total',        
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Todos',
		visible: 'Todos'		
            }
        },
        autoLoad: true
        
    });
   
    var pluginExpanded = true;
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso = '{{ is_granted("ROLE_53-8") }}';
        var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
	
	var permiso = '{{ is_granted("ROLE_53-9") }}';
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

    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        width: 850,
        height: 400,
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
			enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
		dockedItems: [ toolbar ], 
        columns:[
                {
                  id: 'id_tarea',
                  header: 'IdTarea',
                  dataIndex: 'id_tarea',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_proceso',
                  header: 'Nombre Proceso',
                  dataIndex: 'nombre_proceso',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'nombre_rol_autoria',
                  header: 'Nombre Rol Autoriza',
                  dataIndex: 'nombre_rol_autoria',
                  width: 120,
                  sortable: true
                },
                {
                  id: 'nombre_tarea',
                  header: 'Nombre Tarea',
                  dataIndex: 'nombre_tarea',
                  width: 300,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 80,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
						{
	                        getClass: function(v, meta, rec) {
                                    
                                    var permiso = '{{ is_granted("ROLE_53-6") }}';
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
									
                                    var permiso = '{{ is_granted("ROLE_53-6") }}';
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                    if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }

                                    if(rec.get('action1')!="icon-invisible")
                                            window.location = rec.get('id_tarea')+"/show";
                                    else
                                            Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                                }
                        },
                        {
	                        getClass: function(v, meta, rec) {
								
                                                                var permiso = '{{ is_granted("ROLE_53-4") }}';
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
								
								var permiso = '{{ is_granted("ROLE_53-4") }}';
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if(rec.get('action2')!="icon-invisible")
	                                window.location = rec.get('id_tarea')+"/edit";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {
	                        getClass: function(v, meta, rec) {
                                                                var permiso = '{{ is_granted("ROLE_53-8") }}';
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permiso = '{{ is_granted("ROLE_53-9") }}';
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
								
								var permiso = '{{ is_granted("ROLE_53-8") }}';
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permiso = '{{ is_granted("ROLE_53-9") }}';
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if(rec.get('action3')!="icon-invisible")
								{
	                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn)
                                    {
                                        var conn = new Ext.data.Connection({
                                            listeners: {
                                                'beforerequest': {
                                                    fn: function(con, opt) {
                                                        Ext.get(document.body).mask('Eliminando Tarea...');
                                                    },
                                                    scope: this
                                                },
                                                'requestcomplete': {
                                                    fn: function(con, res, opt) {
                                                        Ext.get(document.body).unmask();
                                                    },
                                                    scope: this
                                                },
                                                'requestexception': {
                                                    fn: function(con, res, opt) {
                                                        Ext.get(document.body).unmask();
                                                    },
                                                    scope: this
                                                }
                                            }
                                        });
	                                    if(btn=='yes')
                                        {
	                                        conn.request({
	                                            url: "deleteAjax",
	                                            method: 'post',
	                                            params: { param : rec.get('id_tarea')},
	                                            success: function(response)
                                                {	                                                
                                                    Ext.Msg.alert('Informacion ','Se elimino la Tarea',function(btn){
                                                        if(btn=='ok')
                                                        {
                                                            store.load();
                                                        }
                                                    });
	                                                
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
        width: 850,
        title: 'Criterios de busqueda',
		buttons: 
		[
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
			combo_procesos,
			{html:"&nbsp;",border:false,width:200},
		
			{html:"&nbsp;",border:false,width:200},
			{
				xtype: 'textfield',
				id: 'txtNombre',
				fieldLabel: 'Nombre',
				value: '',
				width: '425'
			},
			{html:"&nbsp;",border:false,width:200},
		
			{html:"&nbsp;",border:false,width:200},
			{
				xtype: 'combobox',
				fieldLabel: 'Estado',
				id: 'sltEstado',
				value:'Todos',
				store: [
					['Todos','Todos'],
					['ACTIVO','Activo'],
					['MODIFICADO','Modificado'],
					['ELIMINADO','Eliminado']
				],
				width: '425'
			},
			{html:"&nbsp;",border:false,width:200}
		],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.getProxy().extraParams.idProceso = Ext.getCmp('cmb_proceso').value;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}

function limpiar(){
    Ext.getCmp('cmb_proceso').value="";
    Ext.getCmp('cmb_proceso').setRawValue("");
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
    store.getProxy().extraParams.idProceso = Ext.getCmp('cmb_proceso').value;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
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