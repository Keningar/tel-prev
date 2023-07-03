
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_persona', mapping:'id_persona'},
			{name:'nombre_persona', mapping:'nombre_persona'},
                        {name:'empresa', mapping:'empresa'},
                        {name:'ciudad', mapping:'ciudad'},
                        {name:'departamento', mapping:'departamento'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'},
			{name:'action4', mapping:'action4'},
			{name:'action5', mapping:'action5'},
                        {name:'action6', mapping:'action6'},
		],
        idProperty: 'id_persona'
    });
	
    store = new Ext.data.Store({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : strUrlGrid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Todos'
            }
        }
    });
    
     var storeEmpresas = Ext.create('Ext.data.Store', {
      
		fields: ['opcion', 'valor'],
		data: 
		[{
		    "opcion": "MEGADATOS",
		    "valor": "MD"
		    }, {
		    "opcion": "TRANSTELCO",
		    "valor": "TTCO"
		    },
		     {
		    "opcion": "TELCONET",
		    "valor": "TN"
		    },
            {
            "opcion": "ECUANET",
            "valor": "EN"
            }
		]
 	    });
  
    storeCiudades = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: strGetCiudadesPorEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'		
            }
        },
        fields:
              [
                {name:'id_canton', mapping:'id_canton'},
                {name:'nombre_canton', mapping:'nombre_canton'}
              ]
	});   
      
      
       storeDepartamentosCiudad = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: strGetDepartamentosPorEmpresaYCiudad,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'		
            }
        },
        fields:
              [
                {name:'id_departamento', mapping:'id_departamento'},
                {name:'nombre_departamento', mapping:'nombre_departamento'}
              ]
	}); 
        
    function presentarCiudades(empresa) {

        storeCiudades.proxy.extraParams = {empresa: empresa};
        storeCiudades.load();

    }
    
    function presentarDepartamentosPorCiudad(id_canton, empresa) {

        storeDepartamentosCiudad.proxy.extraParams = {id_canton: id_canton, empresa: empresa};
        storeDepartamentosCiudad.load();

    }

   
    var pluginExpanded = true;
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
    //Perfil Eliminar Perfil Persona
	var permiso = $("#ROLE_20-8");
	var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val()==1 ? true : false);	
	
    //Perfil Eliminar Perfil Persona
	var permiso = $("#ROLE_20-9");
	var boolPermiso2 = (typeof permiso === 'undefined') ? false : (permiso.val()==1 ? true : false);	
	
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
        id: 'grid',
        width: 850,
        height: 400,
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        dockedItems: [toolbar],
        columns: [
            {
                id: 'id_persona',
                header: 'IdPersona',
                dataIndex: 'id_persona',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombre_persona',
                header: 'Nombre Persona',
                dataIndex: 'nombre_persona',
                width: 520,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 190,
                items: [
                    {
                        getClass: function(v, meta, rec)
                        {
                            //Perfil Consultar Perfil Persona
                            var permiso = $("#ROLE_20-6");
                            
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') === "icon-invisible")
                            {
                                this.items[0].tooltip = '';
                            }
                            else
                            {
                                this.items[0].tooltip = 'Ver';
                            }

                            return rec.get('action1');
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            //Perfil Consultar Perfil Persona
                            var permiso = $("#ROLE_20-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') !== "icon-invisible")
                            {
                                mostrarPerfiles(rec.get('id_persona'), rec.get('nombre_persona'));
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta acción');
                            }
                        }
                    },
                        {
                        getClass: function(v, meta, rec)
                        {
                            //Perfil Editar Perfil Persona
                            var permiso = $("#ROLE_20-4");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                            if (!boolPermiso)
                            {
                                rec.data.action2 = "icon-invisible";
                            }

                            if (rec.get('action2') === "icon-invisible")
                            {
                                this.items[1].tooltip = '';
                            }
                            else
                            {
                                this.items[1].tooltip = 'Editar';
                            }

                            return rec.get('action2');
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            //Perfil Editar Perfil Persona
                            var permiso = $("#ROLE_20-4");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                            if (!boolPermiso) {
                                rec.data.action2 = "icon-invisible";
                            }

                            if (rec.get('action2') !== "icon-invisible")
                            {
                                window.location = rec.get('id_persona')+"/"+rec.get('empresa')+"/edit";
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                        {
                        getClass: function(v, meta, rec)
                        {
                            //Perfil Eliminar Perfil Persona
                            var permiso = $("#ROLE_20-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                            if (!boolPermiso)
                            {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') === "icon-invisible")
                            {
                                this.items[2].tooltip = '';
                            }
                            else
                            {
                                this.items[2].tooltip = 'Eliminar';
                            }

                            return rec.get('action3');
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            //Perfil Eliminar Perfil Persona
                            var permiso = $("#ROLE_20-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                            if (!boolPermiso)
                            {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') !== "icon-invisible")
                            {
                                Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function(btn) {
                                    if (btn === 'yes') {
                                        Ext.Ajax.request({
                                            url: "deleteAjax",
                                            method: 'post',
                                            params: {param: rec.get('id_persona')},
                                            success: function(response) {
                                                var text = response.responseText;
                                                store.load();
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
		        /*
                * 		ACCION PARA MOSTRAR Y EDITAR EL PERFIL DE LA PERSONA DE LA INFO_PERSONA_EMPRESA_ROL
                *              AUTOR: arsuarez 30/05/2014
                */
                    {
                        getClass: function(v, meta, rec) 
                        {

                            //Perfil Editar Roles Perfil Persona
                            var permiso = $("#ROLE_20-1937");
                            
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val()==1 ? true : false);
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action2') === "icon-invisible")
                            {
                                this.items[3].tooltip = '';
                            }
                            else
                            {
                                this.items[3].tooltip = 'Editar Persona Rol';
                            }

                            return rec.get('action4');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {

                            var rec = store.getAt(rowIndex);

                            //Perfil Editar Roles Perfil Persona
                            var permiso = $("#ROLE_20-1937");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val()==1 ? true : false);
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') !== "icon-invisible")
                            {
                                editarEmpresaRol(rec.get('id_persona'), rec.get('nombre_persona'));
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            //Perfil Consultar Perfil Persona
                            var permiso = $("#ROLE_20-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val()==1 ? true : false);
                            
                            console.log(boolPermiso)
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action5 = "icon-invisible";
                            }

                            if (rec.get('action5') === "icon-invisible")
                            {
                                this.items[4].tooltip = '';
                            }
                            else
                            {
                                this.items[4].tooltip = 'Ver Persona Rol';
                            }

                            return rec.get('action5');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            //Perfil Consultar Perfil Persona
                            var permiso = $("#ROLE_20-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val()==1 ? true : false);
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action5 = "icon-invisible";
                            }

                            if (rec.get('action5') !== "icon-invisible")
                            {
                                mostrarEmpresaRol(rec.get('id_persona'));
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            //Perfil Reseteo Clave
                            var permiso = $("#ROLE_20-7137");//resetearClave
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val()==1 ? true : false);
                            
                            console.log(boolPermiso);
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action6 = "icon-invisible";
                            }

                            if (rec.get('action6') === "icon-invisible")
                            {
                                this.items[5].tooltip = '';
                            }
                            else
                            {
                                this.items[5].tooltip = 'Reseteo de Clave';
                            }

                            return rec.get('action6');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            //Reseteo de Clave
                            var permiso = $("#ROLE_20-7137");//resetearClave
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val()==1 ? true : false);
                            if (!boolPermiso) 
                            {
                                rec.data.action6 = "icon-invisible";
                            }

                            if (rec.get('action6') !== "icon-invisible")
                            {
                                Ext.Msg.confirm('Alerta', 'Se cambiará la clave. Desea continuar?', function(btn) {
                                    if (btn === 'yes') {
                                        resetearClave(rec.get('id_persona'), Ext.getCmp('sltEmpresa').getValue());
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta acción');
                            }
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
    //se agregan filtrso empresa,ciudad y departamento
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders        
        border:false,        
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: false,
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
                            width: '450'			    
                        },
                        {html:"&nbsp;",border:false,width:50},
			{
                            xtype: 'textfield',
                            id: 'txtApellido',
                            fieldLabel: 'Apellido',
                            value: '',
                            width: '450'
                        },
                        {html:"&nbsp;",border:false,width:50},
                        {html:"&nbsp;",border:false,width:50},
                        {
				xtype: 'combobox',
				fieldLabel: 'Empresa Asignado:',
				id: 'sltEmpresa',
				name: 'sltEmpresa',
				store: storeEmpresas,
				displayField: 'opcion',
				valueField: 'valor',
				queryMode: "remote",
				emptyText: '',
				width:256,
				listeners: {
						  select: function(combo){							
						    
							  Ext.getCmp('comboCiudad').reset();									
							  Ext.getCmp('comboDepartamento').reset();
							  //Ext.getCmp('comboEmpleado').reset();
															  
							  Ext.getCmp('comboCiudad').setDisabled(false);								
							  Ext.getCmp('comboDepartamento').setDisabled(true);
							  //Ext.getCmp('comboEmpleado').setDisabled(true);
							  
							  presentarCiudades(combo.getValue());
						  }
				},
				forceSelection: true
			}, 
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'combobox',
				fieldLabel: 'Ciudad Asignado',
				id: 'comboCiudad',
				name: 'comboCiudad',
				store: storeCiudades,
				displayField: 'nombre_canton',
				valueField: 'id_canton',
				queryMode: "remote",
				emptyText: '',
				width:250,
				disabled: true,
				listeners: {
					select: function(combo){															
						Ext.getCmp('comboDepartamento').reset();
						//Ext.getCmp('comboEmpleado').reset();
																						
						Ext.getCmp('comboDepartamento').setDisabled(false);
						//Ext.getCmp('comboEmpleado').setDisabled(true);
						
						empresa = Ext.getCmp('sltEmpresa').getValue();
						
						presentarDepartamentosPorCiudad(combo.getValue(),empresa);
					}
				},
				forceSelection: true
			}, 
			{html:"&nbsp;",border:false,width:150},
			
			{html:"&nbsp;",border:false,width:50},
			{
				xtype: 'combobox',
				fieldLabel: 'Departamento Asignado',
				id: 'comboDepartamento',
				name: 'comboDepartamento',
				store: storeDepartamentosCiudad,
				displayField: 'nombre_departamento',
				valueField: 'id_departamento',
				queryMode: "remote",
				emptyText: '',
				width:400,
				disabled: true,
				forceSelection: true
			},
                        ],	
        renderTo: 'filtro'
    });
    
   
});


function buscar()
{
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.apellido = Ext.getCmp('txtApellido').value;
    store.getProxy().extraParams.empresa = Ext.getCmp('sltEmpresa').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('comboCiudad').value;
    store.getProxy().extraParams.departamento = Ext.getCmp('comboDepartamento').value;
    
    if( Ext.getCmp('sltEmpresa').value == null || Ext.getCmp('sltEmpresa').value == '')
    {
        Ext.Msg.alert('Atención', 'Debe seleccionar una empresa para realizar la búsqueda');
    }
    else
    {
        store.loadData([],false);
        store.currentPage = 1;
        store.load();
    }
}

function limpiar()
{
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('txtApellido').value="";
    Ext.getCmp('txtApellido').setRawValue("");
    Ext.getCmp('sltEmpresa').value="";
    Ext.getCmp('sltEmpresa').setRawValue("");
    Ext.getCmp('comboCiudad').value="";
    Ext.getCmp('comboCiudad').setRawValue("");
    Ext.getCmp('comboDepartamento').value="";
    Ext.getCmp('comboDepartamento').setRawValue("");
    
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.apellido = Ext.getCmp('txtApellido').value;
    store.getProxy().extraParams.empresa = Ext.getCmp('sltEmpresa').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('comboCiudad').value;
    store.getProxy().extraParams.departamento = Ext.getCmp('comboDepartamento').value;
    Ext.getCmp('comboCiudad').setDisabled(true);								
    Ext.getCmp('comboDepartamento').setDisabled(true);
    
    store.loadData([],false);
    store.currentPage = 1;
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

function mostrarEmpresaRol(idPersona){  
	
	var conn = new Ext.data.Connection({
	      listeners: {
		  'beforerequest': {
		      fn: function (con, opt) {
			  Ext.get(document.body).mask('Cargando Perfiles...');
		      },
		      scope: this
		  },
		  'requestcomplete': {
		      fn: function (con, res, opt) {
			  Ext.get(document.body).unmask();
		      },
		      scope: this
		  },
		  'requestexception': {
		      fn: function (con, res, opt) {
			  Ext.get(document.body).unmask();
		      },
		      scope: this
		  }
	      }
	  });   
	
	  Ext.define('ModelStore', {
	      extend: 'Ext.data.Model',
	      fields:
		      [				
			      {name:'id_persona_rol', mapping:'id_persona_rol'},
			      {name:'rol', mapping:'rol'},
			      {name:'oficina', mapping:'oficina'},
			      {name:'departamento', mapping:'departamento'},
			      {name:'empresa', mapping:'empresa'},
			      {name:'estado', mapping:'estado'},
			      {name:'usuario_creacion', mapping:'usuario_creacion'}			      
		      ],
	      idProperty: 'id_persona_rol'
	  });
	
	  var storeGridPerfiles  = new Ext.data.Store({ 
	      pageSize: 10,
	      model:'ModelStore',
	      total: 'total',
	      autoLoad:true,
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : strUrlGridPerfilesPersonas,
		  reader: {
		      type: 'json',
		      totalProperty: 'total',
		      root: 'encontrados'
		  },
		  extraParams: {
		      id : idPersona
		  }
	      }	          
	  }); 
	  
	  gridPerfilesPersona = Ext.create('Ext.grid.Panel', {
		id:'gridPerfilesPersona',
		store: storeGridPerfiles,		
		columnLines: true,
		loadMask:true,
		columns: [
			{
			      id: 'id_persona_rol',
			      header: 'id_persona_rol',
			      dataIndex: 'id_persona_rol',			      
			      hidden: true						 
			},
			  {
			      id: 'empresa',
			      header: 'Empresa',
			      dataIndex: 'empresa',
			      width:120,
			      sortable: true						 
			},
			  {
			      id: 'empresaRol',
			      header: 'Rol',
			      dataIndex: 'rol',
			      width:130,
			      sortable: true						 
			},
			  {
			      id: 'oficina',
			      header: 'Oficina',
			      dataIndex: 'oficina',
			      width:230,
			      sortable: true						 
			},
			  {
			      id: 'departamento',
			      header: 'Departamento',
			      dataIndex: 'departamento',
			      width:150,
			      sortable: true						 
			},
			  {
			      id: 'estado',
			      header: 'Estado',
			      dataIndex: 'estado',
			      width:80,
			      sortable: true						 
			},
			  {
			      id: 'usuario',
			      header: 'Usuario Creacion',
			      dataIndex: 'usuario_creacion',
			      width:112,
			      sortable: true						 
			}			
		],		
		width: 825,
		height: 150,
		listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            },
                            viewready: function (grid) {
                                var view = grid.view;

                                // record the current cellIndex
                                grid.mon(view, {
                                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                                        grid.cellIndex = cellIndex;
                                        grid.recordIndex = recordIndex;
                                    }
                                });

                                grid.tip = Ext.create('Ext.tip.ToolTip', {
                                    target: view.el,
                                    delegate: '.x-grid-cell',
                                    trackMouse: true,
                                    renderTo: Ext.getBody(),
                                    listeners: {
                                        beforeshow: function updateTipBody(tip) {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                            }
                                        }
                                    }
                                });

                            }                                    
                    }
	});
	
	
	  btnCerrar= Ext.create('Ext.Button', {
		    text: 'Cerrar',
		    cls: 'x-btn-rigth',
		    handler: function() {
			    perfiles.destroy();
		    }
	  });
	  
	  formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 250,
		width: 850,		
		layout: 'fit',		
		fieldDefaults: {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',				
				defaultType: 'textfield',
				items: 
				[
					gridPerfilesPersona
					
				]
			}
		]
	 });  
	  
	  perfiles = Ext.create('Ext.window.Window', {
		  title: 'Ver Persona Empresa Rol',
		  modal: true,
		  width: 870,
		  height: 270,
		  resizable: false,
		  layout: 'fit',
		  items: [formPanel],
		  buttonAlign: 'center',
		  buttons:[btnCerrar]
	  }).show(); 
      
}

function resetearClave(idPersona, prefijo){  
    
    $.ajax
    ({
        type: "POST",
        url: urlResetearClave,
        data:
            {
                'id'        : idPersona,
                'prefijo'   : prefijo
            },
        beforeSend: function()
            {
                Ext.get(document.body).mask('Reseteando Clave...');
            },
        complete: function()
            {
                Ext.get(document.body).unmask();
            },

        success: function(data)
        {
            var arrclave = Ext.JSON.decode(data);
            if (arrclave.encontrados.strStatus == 'true'){
            Ext.Msg.show({
                    title: 'Mensaje',
                    msg: '<b>Nueva clave:<b> <font size="3"><b style="color:green;">  '
                         +arrclave.encontrados.strClave+'</b></font>'
                         +'  <br/> Se envió mail de notificación a: '+arrclave.encontrados.strMail,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.INFO
                });
            }
            else
            {       Ext.Msg.show({  title: 'Error',
                                    msg: '<b>Ocurrió un error <b>'+ arrclave.encontrados.strMensaje,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR});
            }
                
        }
    }); 
}

function editarEmpresaRol(idPersona,nombrePersona){       
       
       var storeOficina  = new Ext.data.Store({ 
	      pageSize: 10,	     
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : strUrlGetOficinas,
		  reader: {
		      type: 'json',
		      totalProperty: 'total',
		      root: 'encontrados'
		  }		  		  				
	      },
	      fields:
	      [
		  {name:'id_oficina', mapping:'id_oficina'},
		  {name:'nombre_oficina', mapping:'nombre_oficina'}				
	      ],	     
	  }); 
	
	combo_oficina = new Ext.form.ComboBox({
		    id:'combo_oficina',
		    name: 'combo_oficina',
		    displayField:'nombre_oficina',
		    valueField: 'id_oficina',
		    store: storeOficina,
		    loadingText: 'Buscando ...',			  
		    fieldLabel: 'Oficina',	
		    queryMode: "remote",		    		    	    
		    width:400,
		    disabled:true,
	    });	
	
	var storeDepartamento  = new Ext.data.Store({ 
	      pageSize: 10,	     
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : strUrlGetDepartamentosByEmpresaYNombre,
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
		    queryMode: "remote",		    		    	    
		    width:400,
		    disabled:true,
	    });	
	
	var storeRoles  = new Ext.data.Store({ 
	      pageSize: 10,	     
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : strUrlGetRolesEmpleados,
		  reader: {
		      type: 'json',
		      totalProperty: 'total',
		      root: 'encontrados'
		  }		  		  				
	      },
	      fields:
	      [
		  {name:'id_empresa_rol', mapping:'id_empresa_rol'},
		  {name:'descripcion_rol', mapping:'descripcion_rol'}				
	      ],	     
	  }); 
	
	combo_roles = new Ext.form.ComboBox({
		    id:'combo_roles',
		    name: 'combo_roles',
		    displayField:'descripcion_rol',
		    valueField: 'id_empresa_rol',
		    store: storeRoles,
		    loadingText: 'Buscando ...',			  
		    fieldLabel: 'Rol',	
		    queryMode: "remote",		    		    	    
		    width:400,
		    disabled:true,
		    
	    });	
  
    oficina = '' , departamento = '' , rol = '' , esJefe = '' , id = '';
    
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando Perfil...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });    
    
    btncancelar2 = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {
			winEditarPersonaRol.destroy();
		}
    });
    
    btnGuardar = Ext.create('Ext.Button', {
		text: 'Guardar/Editar Rol',
		cls: 'x-btn-rigth',
		handler: function() {
			if(id != ''){
								  
			      oficina      = Ext.getCmp('combo_oficina').getValue();
			      departamento = Ext.getCmp('combo_departamento').getValue();
			      rol          = Ext.getCmp('combo_roles').getValue();
			      			      
			      
			      if(id == 'N/A'){										
				    if(oficina == null || departamento == null || rol == null){
					    Ext.Msg.alert('Alerta ','Debe escoger la oficina, departamento y rol para ingresar nuevo registro');
					    return;
				    }				    
			      }
			      
			      if(id != 'N/A'){
				    if(oficina == null && departamento == null && rol == null){
					    Ext.Msg.alert('Alerta ','Debe escoger al menos la oficina, departamento ó rol para actualizar un registro');
					    return;
				    }				   				
			      }
			      
			      if(isNaN(oficina)){
				      Ext.Msg.alert('Alerta ','Tiene que escoger una oficina válida');
				      return;			  
			      }else if(isNaN(departamento) ){
				      Ext.Msg.alert('Alerta ','Tiene que escoger un departamento válido');
				      return;
			      }else if(isNaN(rol) ){
				      Ext.Msg.alert('Alerta ','Tiene que escoger un rol válido');
				      return;
			      }
			      			      
			      
			      conn.request({
		      
				    method: 'POST',
				    params :{
					  id : id,
					  idPersona  : idPersona,
					  oficina: oficina,
					  departamento: departamento,
					  rol:rol					  
				    },
				    url: 'actualizarPersonaRol',
				    success: function(response){		  
					    var json = Ext.JSON.decode(response.responseText);
					    if(json.success == true)
					      Ext.Msg.alert('Alerta','Se actualizó correctamente');	
					    else Ext.Msg.alert('Alerta ','Ha ocurrido un error en la actualizacion');	
					    winEditarPersonaRol.destroy();
				    },
				    failure: function(response) {										      
					    Ext.Msg.alert('Alerta ','Ha ocurrido un error');
				    }
			      });
			
			}else Ext.Msg.alert('Alerta ','Debe Seleccionar una empresa');			
		}
    });
     
    btnEliminar = Ext.create('Ext.Button', {
		text: 'Eliminar Rol',
		cls: 'x-btn-rigth',
		handler: function() {
			if(id != ''){			  
				if(id != 'N/A'){
					Ext.Msg.confirm('Alerta','Se eliminará el Rol, Desea continuar?', function(btn){
					      if(btn=='yes'){
						    conn.request({
						  
							  method: 'POST',
							  params :{
								id : id				    					  
							  },
							  url: 'eliminarRol',
							  success: function(response){		  
								  var json = Ext.JSON.decode(response.responseText);
								  if(json.success == true){
								    Ext.Msg.alert('Alerta','Rol se eliminó correctamente');
								    winEditarPersonaRol.destroy();
								  }
								  else Ext.Msg.alert('Alerta ','Ha ocurrido un error en la actualizacion');
							  },
							  failure: function(response) {										      
								  Ext.Msg.alert('Alerta ','Ha ocurrido un error');
							  }
						    });				    
					      }
					});
				}else Ext.Msg.alert('Alerta ','No existe Rol en esta empresa para Eliminar');
			}else  Ext.Msg.alert('Alerta ','Debe Seleccionar una empresa');
		}
    });
    
    formPanel2 = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 280,
		width: 450,
		layout: 'fit',		
		fieldDefaults: {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',
				title: 'Información Actual',
				defaultType: 'textfield',
				items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Nombre:',
						id: 'nombre',
						width:425,
						readOnly:true,
						name: 'nombre',
						value: nombrePersona.toUpperCase()
					},
					{
					      xtype: 'combobox',
					      fieldLabel: 'Empresa',
					      id: 'cmbEmpresaBusqueda',
					      value:'',
					      store: [
						      ['MD','MEGADATOS'],
						      ['TN','TELCONET'],
						      ['TTCO','TRANSTELCO'],					
                              ['EN','ECUANET'],
						      
					      ],				
					      width: 425,
					      listeners:{
						      select: function(combo){	
							
							      empresa = combo.getValue();
							
							      conn.request({
								
								    method: 'POST',
								    params :{
									  id : idPersona,
									  empresa: combo.getValue()
								    },
								    url: 'verPerfil',
								    success: function(response){		  
								      
									    var json = Ext.JSON.decode(response.responseText);	
									    
									    if(json.success == 'true'){
									   									   
										  oficina      = json.encontrados[0].nombreOficina;
										  departamento = json.encontrados[0].nombreDepartamento;
										  rol          = json.encontrados[0].descripcionRol;
										  esJefe       = json.encontrados[0].esJefe=='S'?'SI':'NO';
										  nombres      = json.encontrados[0].nombres+' '+json.encontrados[0].apellidos;;
										  id           = json.encontrados[0].id;										  
									    
									    }else{
									      
										   oficina      = 'N/A';
										   departamento = 'N/A';
										   rol          = 'N/A';
										   esJefe       = 'N/A';										   
										   id           = 'N/A';
									    }
									    
									    Ext.getCmp('oficina').setRawValue(oficina);									    									  
									    Ext.getCmp('departamento').setRawValue(departamento);									    									    
									    Ext.getCmp('rol').setRawValue(rol);									    									    
									    Ext.getCmp('jefe').setRawValue(esJefe);
									    
									    Ext.getCmp('combo_oficina').reset();
									    Ext.getCmp('combo_departamento').reset();
									    Ext.getCmp('combo_roles').reset();
									    
									    Ext.getCmp('combo_oficina').setDisabled(false);
									    Ext.getCmp('combo_departamento').setDisabled(false);
									    Ext.getCmp('combo_roles').setDisabled(false);
									    
									    storeOficina.proxy.extraParams = { idEmpresa:combo.getValue(),tipo:'prefijo' };
									    storeOficina.load();
									    
									    storeDepartamento.proxy.extraParams = { empresa:combo.getValue() };
									    storeDepartamento.load();
									    
									    storeRoles.proxy.extraParams = { empresa:combo.getValue() };
									    storeRoles.load();
									    									    									    									   
								    },
								    failure: function(rec, op) {
									    var json = Ext.JSON.decode(op.response.responseText);
									    Ext.Msg.alert('Alerta ',json.mensaje);
								    }
							      });
													      
						      }				  
					      }
					  
					},					
					{
						xtype: 'textfield',
						fieldLabel: 'Oficina:',
						id: 'oficina',
						width:425,
						readOnly:true,
						name: 'oficina',
						value: oficina
					},
					{
						xtype: 'textfield',
						fieldLabel: 'Departamento:',
						id: 'departamento',
						width:425,
						readOnly:true,
						name: 'departamento',
						value: departamento
					},
					{
						xtype: 'textfield',
						fieldLabel: 'Rol:',
						id: 'rol',
						width:425,
						readOnly:true,
						name: 'rol',
						value: rol
					},
					{
						xtype: 'textfield',
						fieldLabel: 'Es Jefe:',
						id: 'jefe',
						width:425,
						readOnly:true,
						name: 'jefe',
						value: esJefe
					},
			                {
						xtype: 'fieldset',
						title: 'Nueva Información',												
						items: 						 
						[
							combo_oficina,
							combo_departamento,
			                                combo_roles			                             
						]
					}
					
				]
			}
		]
	 });  
	
    
    
    winEditarPersonaRol = Ext.create('Ext.window.Window', {
		title: 'Actualizar Persona Empresa Rol',
		modal: true,
		width: 500,
		height: 425,
		resizable: false,
		layout: 'fit',
		items: [formPanel2],
		buttonAlign: 'center',
		buttons:[btnGuardar,btnEliminar,btncancelar2]
    }).show(); 
    
   
}

// custom summary renderer example
function totalRelaciones(v, params, data)
{
    params.attr = 'ext:qtip="No. Total de Relaciones"'; // summary column tooltip example
    return v ? ((v === 0 || v > 1) ? '(' + v + ' Relaciones)' : '(1 Relacion)') : '';
}

/*******************Creacion Grid******************/
////////////////Grid  Relaciones////////////////


/**************************************************/
// trigger the data store load   
function mostrarPerfiles(idPersona,nombre) {
    winMostrarPerfiles = null;
    //storeAsignaciones.getProxy().extraParams.id = idPersona;
     Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_perfil', mapping:'id_perfil'},
            {name:'nombre_perfil', mapping:'nombre_perfil'},
        ]
    });
    
    // create the Data Store
    var storeAsignaciones = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoLoad: true,
        model: 'Asignacion',
        proxy: {
            type: 'ajax',
            url: 'gridAsignaciones',
            // specify a XmlReader (coincides with the XML format of the returned data)
            reader: {
                type: 'json',
                totalProperty: 'total',
                // records will have a 'plant' tag
                root: 'asignaciones'
            },
            extraParams: {
		idPersona: idPersona
            }
        }
    });
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridRelaciones = Ext.create('Ext.grid.Panel', {
        id:'gridAsignaciones',
        store: storeAsignaciones,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
                {
                    id: 'id_perfil',
                    header: 'PerfilId',
                    dataIndex: 'id_perfil',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'nombre_perfil',
                    header: 'Perfil',
                    dataIndex: 'nombre_perfil',
                    width: 320,
                    sortable: true
                } 
        ],        
        viewConfig:{
            stripeRows:true
        },
        width: 320,
        height: 500,
        frame: true,
        title: 'Informacion de Perfiles Asignados a '+nombre,
    });
    winMostrarPerfiles = Ext.widget('window', {
        title: 'Ver perfiles',
//             width: 1030,
//             height: 650,
//             minHeight: 380,
        layout: 'fit',
        resizable: false,
        closabled: false,
        items: [gridRelaciones]
    });


    winMostrarPerfiles.show();
}