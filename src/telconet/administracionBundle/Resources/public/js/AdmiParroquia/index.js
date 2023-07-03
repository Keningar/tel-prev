/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();          
            
	// **************** PAIS ******************
    Ext.define('PaisList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_pais', type:'int'},
            {name:'nombre_pais', type:'string'}
        ]
    });           
    eval("var storePais = Ext.create('Ext.data.Store', { "+
        "  pageSize: 200, "+
        "  id: 'storePais', "+
        "  model: 'PaisList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : 'getPaises',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_pais = new Ext.form.ComboBox({
        id: 'cmb_pais',
        name: 'cmb_pais',
        fieldLabel: "Pais",
        queryMode:'remote',
        width: 425,
        emptyText: 'Seleccione Pais',
        store: eval("storePais"),
        displayField: 'nombre_pais',
        valueField: 'id_pais',
        labelAlign: 'top',
        disabled: false,
		listeners:{
			select:{fn:function(combo, value) {
				Ext.getCmp('cmb_region').reset();
				Ext.getCmp('cmb_provincia').reset();  
				Ext.getCmp('cmb_canton').reset();  
				
				storeRegiones.proxy.extraParams = {idPais: combo.getValue()};
				storeRegiones.load();	
				
				storeProvincias.proxy.extraParams = {idPais: combo.getValue(), idRegion: ''};
				storeProvincias.load();	
				
				storeCantones.proxy.extraParams = {idPais: combo.getValue(), idRegion: '', idProvincia: ''};
				storeCantones.load();	
			}}
		}
    });
	
	// **************** REGION ******************
    Ext.define('RegionesList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_region', type:'int'},
            {name:'nombre_region', type:'string'}
        ]
    });           
    eval("var storeRegiones = Ext.create('Ext.data.Store', { "+
        "  pageSize: 200, "+
        "  id: 'storeRegiones', "+
        "  model: 'RegionesList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : 'getRegiones',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_region = new Ext.form.ComboBox({
        id: 'cmb_region',
        name: 'cmb_region',
        fieldLabel: "Regiones",
        queryMode:'remote',
        width: 425,
        emptyText: 'Seleccione Region',
        store: eval("storeRegiones"),
        displayField: 'nombre_region',
        valueField: 'id_region',
        labelAlign: 'top',
        disabled: false,
		listeners:{
			select:{fn:function(combo, value) {
				Ext.getCmp('cmb_provincia').reset();  
				Ext.getCmp('cmb_canton').reset();  
				
				storeProvincias.proxy.extraParams = {idRegion: combo.getValue()};
				storeProvincias.load();	
				
				storeCantones.proxy.extraParams = {idRegion: combo.getValue(), idProvincia: ''};
				storeCantones.load();	
			}}
		}
    });	
	
	// **************** PROVINCIA ******************
    Ext.define('ProvinciasList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_provincia', type:'int'},
            {name:'nombre_provincia', type:'string'}
        ]
    });           
    eval("var storeProvincias = Ext.create('Ext.data.Store', { "+
        "  pageSize: 200, "+
        "  id: 'storeProvincias', "+
        "  model: 'ProvinciasList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : 'getProvincias',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_provincia = new Ext.form.ComboBox({
        id: 'cmb_provincia',
        name: 'cmb_provincia',
        fieldLabel: "Provincias",
        queryMode:'remote',
        width: 425,
        emptyText: 'Seleccione Provincia',
        store: eval("storeProvincias"),
        displayField: 'nombre_provincia',
        valueField: 'id_provincia',
        labelAlign: 'top',
        disabled: false,
		listeners:{
			select:{fn:function(combo, value) {
				Ext.getCmp('cmb_canton').reset();  
				
				storeCantones.proxy.extraParams = {idProvincia: combo.getValue()};
				storeCantones.load();	
			}}
		}
    });	
	
	// **************** CANTONES ******************
    Ext.define('CantonesList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_canton', type:'int'},
            {name:'nombre_canton', type:'string'}
        ]
    });           
    eval("var storeCantones = Ext.create('Ext.data.Store', { "+
        "  pageSize: 200, "+
        "  id: 'storeCantones', "+
        "  model: 'CantonesList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : 'getCantones',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_canton = new Ext.form.ComboBox({
        id: 'cmb_canton',
        name: 'cmb_canton',
        fieldLabel: "Cantones",
        queryMode:'remote',
        width: 425,
        emptyText: 'Seleccione Canton',
        store: eval("storeCantones"),
        displayField: 'nombre_canton',
        valueField: 'id_canton',
        labelAlign: 'top',
        disabled: false
    });
	
	// **************** TIPOS PARROQUIA ******************
    Ext.define('TiposParroquiaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_tipo_parroquia', type:'int'},
            {name:'nombre_tipo_parroquia', type:'string'}
        ]
    });           
    eval("var storeTiposParroquia = Ext.create('Ext.data.Store', { "+
        "  pageSize: 200, "+
        "  id: 'storeTiposParroquia', "+
        "  model: 'TiposParroquiaList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : 'getTiposParroquia',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_tipo_parroquia = new Ext.form.ComboBox({
        id: 'cmb_tipo_parroquia',
        name: 'cmb_tipo_parroquia',
        fieldLabel: "Tipos Parroquia",
        queryMode:'remote',
        width: 425,
        emptyText: 'Seleccione Tipo Parroquia',
        store: eval("storeTiposParroquia"),
        displayField: 'nombre_tipo_parroquia',
        valueField: 'id_tipo_parroquia',
        labelAlign: 'top',
        disabled: false
    });
          
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_parroquia', mapping:'id_parroquia'},
			{name:'nombre_parroquia', mapping:'nombre_parroquia'},
			{name:'nombre_tipo_parroquia', mapping:'nombre_tipo_parroquia'},
			{name:'nombre_canton', mapping:'nombre_canton'},
			{name:'estado', mapping:'estado'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'}                
		],
        idProperty: 'id_parroquia'
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
                idPais: '',
                idRegion: '',
                idProvincia: '',
                idCanton: '',
                idTipoParroquia: '',
                nombre: '',
                estado: 'Todos'
            }
        },
        autoLoad: true
    });
   
    var pluginExpanded = true;
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso = $("#ROLE_27-8");
	var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	
	
	var permiso = $("#ROLE_27-9");
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
            loadMask: false
        },
		dockedItems: [ toolbar ], 
        columns:[
                {
                  id: 'id_parroquia',
                  header: 'IdParroquia',
                  dataIndex: 'id_parroquia',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_parroquia',
                  header: 'Nombre Parroquia',
                  dataIndex: 'nombre_parroquia',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'nombre_canton',
                  header: 'Nombre Canton',
                  dataIndex: 'nombre_canton',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'nombre_tipo_parroquia',
                  header: 'Nombre Tipo Parroquia',
                  dataIndex: 'nombre_tipo_parroquia',
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
								var permiso = $("#ROLE_27-6");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
								
								if (rec.get('action1') == "icon-invisible") 
									this.items[0].tooltip = '';
								else 
									this.items[0].tooltip = 'Ver';
									
								return rec.get('action1')
							},
                                /*getClass: function(v, meta, rec) {
								var permiso = '{{ is_granted("ROLE_28-6") }}';
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
								if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
								
								if (rec.get('action1') == "icon-invisible") 
									this.items[0].tooltip = '';
								else 
									this.items[0].tooltip = 'Ver';
									
								return rec.get('action1')
							},//*/
	                        tooltip: 'Ver',
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
									
								var permiso = $("#ROLE_27-6");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
								if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
																
								if(rec.get('action1')!="icon-invisible")
									window.location = rec.get('id_parroquia')+"/show";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                                /*handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
									
								var permiso = '{{ is_granted("ROLE_28-6") }}';
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
								if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
																
								if(rec.get('action1')!="icon-invisible")
									window.location = rec.get('id_parroquia')+"/show";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}//*/
                        },
                        {
	                        getClass: function(v, meta, rec) {
								var permiso = $("#ROLE_27-4");
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
								
								var permiso = $("#ROLE_27-4");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if(rec.get('action2')!="icon-invisible")
	                                window.location = rec.get('id_parroquia')+"/edit";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {
	                        getClass: function(v, meta, rec) {
								var permiso = $("#ROLE_27-8");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permiso = $("#ROLE_27-9");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[2].tooltip = '';
	                            else 
	                                this.items[2].tooltip = 'Eliminar';
	                            
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
								var permiso = $("#ROLE_27-8");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permiso = $("#ROLE_27-9");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if(rec.get('action3')!="icon-invisible")
								{
	                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
	                                    if(btn=='yes'){
	                                        Ext.Ajax.request({
	                                            url: "deleteAjax",
	                                            method: 'post',
	                                            params: { param : rec.get('id_parroquia')},
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
			combo_pais,
			{html:"&nbsp;",border:false,width:200},
			
			{html:"&nbsp;",border:false,width:200},
			combo_region,
			{html:"&nbsp;",border:false,width:200},
			
			{html:"&nbsp;",border:false,width:200},
			combo_canton,
			{html:"&nbsp;",border:false,width:200},
			
			{html:"&nbsp;",border:false,width:200},
			combo_tipo_parroquia,
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
	store.getProxy().extraParams.idPais = Ext.getCmp('cmb_pais').value;
	store.getProxy().extraParams.idRegion = Ext.getCmp('cmb_region').value;
	store.getProxy().extraParams.idProvincia = Ext.getCmp('cmb_provincia').value;
	store.getProxy().extraParams.idCanton = Ext.getCmp('cmb_canton').value;
	store.getProxy().extraParams.idTipoParroquia = Ext.getCmp('cmb_tipo_parroquia').value;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}

function limpiar(){
    Ext.getCmp('cmb_pais').value="";
    Ext.getCmp('cmb_pais').setRawValue("");
    Ext.getCmp('cmb_region').value="";
    Ext.getCmp('cmb_region').setRawValue("");
    Ext.getCmp('cmb_provincia').value="";
    Ext.getCmp('cmb_provincia').setRawValue("");
    Ext.getCmp('cmb_canton').value="";
    Ext.getCmp('cmb_canton').setRawValue("");
    Ext.getCmp('cmb_tipo_parroquia').value="";
    Ext.getCmp('cmb_tipo_parroquia').setRawValue("");
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
	store.getProxy().extraParams.idPais = Ext.getCmp('cmb_pais').value;
	store.getProxy().extraParams.idRegion = Ext.getCmp('cmb_region').value;
	store.getProxy().extraParams.idProvincia = Ext.getCmp('cmb_provincia').value;
	store.getProxy().extraParams.idCanton = Ext.getCmp('cmb_canton').value;
	store.getProxy().extraParams.idTipoParroquia = Ext.getCmp('cmb_tipo_parroquia').value;
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