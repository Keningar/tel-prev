/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_alias', mapping:'id_alias'},
			{name:'valor', mapping:'valor'},
			{name:'estado', mapping:'estado'},
			{name:'empresa', mapping:'empresa'},
			{name:'jurisdiccion', mapping:'jurisdiccion'},
			{name:'departamento', mapping:'departamento'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'}                
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
            url : 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Todos'
            }
        },
        autoLoad: true
    });

    var pluginExpanded = true;    
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso = $("#ROLE_236-8");
	var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	
	
	var permiso = $("#ROLE_236-9");
	var boolPermiso2 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	
	
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
                  width: 200,
                  sortable: true
                },
		{
                  header: 'Empresa',
                  dataIndex: 'empresa',
                  width: 130,
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
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
						{
	                        getClass: function(v, meta, rec) {
								var permiso = $("#ROLE_236-6");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
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
								
								var permiso = $("#ROLE_236-6");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
								if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
																
	                            if(rec.get('action1')!="icon-invisible")
									window.location = rec.get('id_alias')+"/show";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
									
							}
						},
                        {
	                        getClass: function(v, meta, rec) {
								var permiso = $("#ROLE_236-4");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if (rec.get('action2') == "icon-invisible") 
	                                this.items[1].tooltip = '';
	                            else 
	                                this.items[1].tooltip = 'Editar';
	                            
	                            return rec.get('action2')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
								var permiso = $("#ROLE_236-4");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if(rec.get('action2')!="icon-invisible")
	                                window.location = rec.get('id_alias')+"/edit";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {
	                 getClass: function(v, meta, rec) {
			   
				    var permiso = $("#ROLE_236-8");
				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
				    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
				    
// 				    var permiso = $("#ROLE_236-9");
// 				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
// 				    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[2].tooltip = '';
	                            else 
	                                this.items[2].tooltip = 'Eliminar';
	                            
	                            return rec.get('action3')
	                        },
	                handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
				    var permiso = $("#ROLE_236-8");
				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
				    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
				    
// 				    var permiso = $("#ROLE_236-9");
// 				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
// 				    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if(rec.get('action3')!="icon-invisible")
								{
	                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
	                                    if(btn=='yes'){
	                                        Ext.Ajax.request({
	                                            url: "deleteAjax",
	                                            method: 'post',
	                                            params: { param : rec.get('id_alias')},
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
    
     storeEmpresas = new Ext.data.Store({ 
	    total: 'total',
	    pageSize: 10000,
	    proxy: {
		    type: 'ajax',
		    url : 'getEmpresas',
		    reader: {
			    type: 'json',
			    totalProperty: 'total',
			    root: 'encontrados'
		    },
		    extraParams: {			    
			    estado: 'Todos',			   
		    }
	    },
	    fields:
	    [
		    {name:'id_empresa', mapping:'id_empresa'},
		    {name:'nombre_empresa', mapping:'nombre_empresa'}
	    ]
    });
	
    cmb_empresas = new Ext.form.ComboBox({
		id: 'cmb_empresas',
		name: 'cmb_empresas',
		fieldLabel: "Empresa:",
		//emptyText: 'Seleccione Empresa',
		store: storeEmpresas,
		displayField: 'nombre_empresa',
		valueField: 'id_empresa',		
		width: 250,
		border:0,
		margin:0,
		queryMode: "remote",
		emptyText: '',
		listeners: {
				select: function(combo){							
				  
					Ext.getCmp('combo_ciudad').reset();																											
					Ext.getCmp('combo_ciudad').setDisabled(false);
					
					Ext.getCmp('combo_departamento').reset();																											
					Ext.getCmp('combo_departamento').setDisabled(true);
					
					storeCiudadEmpresa.proxy.extraParams = { empresa:combo.getValue()};
					storeCiudadEmpresa.load();
				}
			},
    });
       

 
       var storeCiudadEmpresa  = new Ext.data.Store({ 
	      pageSize: 10000,
	      //model: 'ModelStore',
	      total: 'total',
	      proxy: {
		  type: 'ajax',		  
		  url : 'getCiudadesPorEmpresa',
		  reader: {
		      type: 'json',
		      totalProperty: 'total',
		      root: 'encontrados'
		  }		  		  				
	      },
	      fields:
			    [
			        {name:'id_canton', mapping:'id_canton'},
				{name:'nombre_canton', mapping:'nombre_canton'}				
			    ],	     
	  }); 

	
	combo_ciudad = new Ext.form.ComboBox({
		    id:'combo_ciudad',
		    name: 'combo_ciudad',
		     fieldLabel: 'Ciudad',
		    displayField:'nombre_canton',
		    valueField: 'id_canton',
		    store: storeCiudadEmpresa,
		    //loadingText: 'Buscando ...',			  		   	
		    queryMode: "remote",
		    emptyText: '',		    		    
		    width:250,
		    disabled:true,
		    listeners:{
			  select:function(combo){
			    
				Ext.getCmp('combo_departamento').reset();																											
				Ext.getCmp('combo_departamento').setDisabled(false);
				
				empresa = Ext.getCmp('cmb_empresas').value;																											
				
				storeDepartamento.proxy.extraParams = { id_canton:combo.getValue(), empresa:empresa};
				storeDepartamento.load();
								    
			  }
		      
		    }
	    });	
	
	
	var storeDepartamento  = new Ext.data.Store({ 
	      pageSize: 10,	      
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : 'getDepartamentosPorEmpresaYCiudad',
		  reader: {
		      type: 'json',
		      totalProperty: 'total',
		      root: 'encontrados'
		  }		  		  				
	      },
	      fields:
			    [
			        {name:'id_departamento', mapping:'id_departamento'},
				{name:'nombre_departamento', mapping:'nombre_departamento'}				
			    ],	     
	  }); 

	
	combo_departamento = new Ext.form.ComboBox({
		    id:'combo_departamento',
		    name: 'combo_departamento',
		    displayField:'nombre_departamento',
		    valueField: 'id_departamento',
		    store: storeDepartamento,
		    loadingText: 'Buscando ...',			  
		    fieldLabel: 'Departamento',	
		    queryMode: "local",
		    emptyText: '',
		    listClass: 'x-combo-list-small',		    
		    width:250,
		    disabled:true
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
        layout: {
	    type: 'table',
	    columns: 5,
	    align: 'stretch'
	},
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 850,
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
                        {html:"&nbsp;",border:false,width:50},
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre',
                            value: '',
                            width: '300'
                        },
                        {html:"&nbsp;",border:false,width:80},	
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
                            width: '300'
                        },
			{html:"&nbsp;",border:false,width:80},	
			
			
			
 			{html:"&nbsp;",border:false,width:50},
 			cmb_empresas,
 			{html:"&nbsp;",border:false,width:80},	
			combo_ciudad,
			{html:"&nbsp;",border:false,width:80},	
			
			{html:"&nbsp;",border:false,width:50},
 			combo_departamento,
 			
                        ],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.getProxy().extraParams.empresa = Ext.getCmp('cmb_empresas').value?Ext.getCmp('cmb_empresas').value:'';
    store.getProxy().extraParams.ciudad = Ext.getCmp('combo_ciudad').value?Ext.getCmp('combo_ciudad').value:'';
    store.getProxy().extraParams.departamento = Ext.getCmp('combo_departamento').value?Ext.getCmp('combo_departamento').value:'';
    store.load();
}

function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
     Ext.getCmp('cmb_empresas').reset();
    Ext.getCmp('cmb_empresas').setRawValue("");
    Ext.getCmp('cmb_empresas').value="";
    
     Ext.getCmp('combo_ciudad').reset();
    Ext.getCmp('combo_ciudad').setRawValue("");
    Ext.getCmp('combo_ciudad').value="";
    
    Ext.getCmp('combo_departamento').reset();
    Ext.getCmp('combo_departamento').setRawValue("");
    Ext.getCmp('combo_departamento').value="";    
    
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
     store.getProxy().extraParams.empresa = '';
    store.getProxy().extraParams.ciudad = '';
    store.getProxy().extraParams.departamento = '';
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