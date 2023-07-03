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
			{name:'id_hipotesis', mapping:'id_hipotesis'},
			{name:'nombre_hipotesis', mapping:'nombre_hipotesis'},
			{name:'descripcion_hipotesis', mapping:'descripcion_hipotesis'},
			{name:'estado', mapping:'estado'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'}               
		],
        idProperty: 'id_hipotesis'
    });

    storeNivel1 = new Ext.data.Store({ 
        pageSize: 20,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
			url   : url_admiHipotesisArbolGrid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
				estado: 'Activo',
				padreHipotesis: '0'
            }
        },
		autoLoad: true,
        listeners: {
            beforeload: function(store, operation, options){
		        //deshabilita botón Nuevo de Nivel 2
	            gridNivel2.dockedItems.items[1].items.items[0].disable();
				Ext.getCmp('txtNombreHipotesisNivel2').disable();
				Ext.getCmp('botonBuscarHipotesisNivel2').disable();
				Ext.getCmp('botonLimpiarHipotesisNivel2').disable();
				//deshabilita botones de Nivel 3
		        gridNivel3.dockedItems.items[1].items.items[0].disable();
				gridNivel3.dockedItems.items[1].items.items[1].disable();
				Ext.getCmp('txtNombreHipotesisNivel3').disable();
				Ext.getCmp('botonBuscarHipotesisNivel3').disable();
				Ext.getCmp('botonLimpiarHipotesisNivel3').disable();
                if (storeNivel2.data.length !== 0)
                {
				    storeNivel2.getProxy().extraParams.padreHipotesis = '';
				    storeNivel2.load();
				}
                if (storeNivel3.data.length !== 0)
                {
                    storeNivel3.getProxy().extraParams.padreHipotesis = '';
					storeNivel3.load();
				}
            }
        }
	});

    storeNivel2 = new Ext.data.Store({ 
        pageSize: 20,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
			url   : url_admiHipotesisArbolGrid,
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
        autoLoad: false,
        listeners: {
            beforeload: function(store, operation, options){
		        //deshabilita botones de Nivel 3
		        gridNivel3.dockedItems.items[1].items.items[0].disable();
				gridNivel3.dockedItems.items[1].items.items[1].disable();
				Ext.getCmp('txtNombreHipotesisNivel3').disable();
				Ext.getCmp('botonBuscarHipotesisNivel3').disable();
				Ext.getCmp('botonLimpiarHipotesisNivel3').disable();
                if (storeNivel3.data.length !== 0)
                {
                    storeNivel3.getProxy().extraParams.padreHipotesis = '';
					storeNivel3.load();
				}
                if (gridNivel1.selModel.selected.items.length === 1)
                {
                    storeNivel2.getProxy().extraParams.padreHipotesis = '';
                }
            }
        }
	});

    storeNivel3 = new Ext.data.Store({ 
        pageSize: 20,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
			url   : url_admiHipotesisArbolGrid,
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
        autoLoad: false,
        listeners:
        {
            beforeload: function(store, operation, options)
            {
                if (gridNivel2.selModel.selected.items.length === 1)
                {
                    storeNivel3.getProxy().extraParams.padreHipotesis = '';
                }
            }
        }
    });

    //permiso para editar hipotesis
	var permiso = $("#ROLE_439-6718");
	var boolPermisoEditar = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    //permiso para eliminar hipotesis
	permiso = $("#ROLE_439-6719");
	var boolPermisoEliminar = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    //permiso para crear nueva hipotesis
	permiso = $("#ROLE_439-6717");
	var boolPermisoNuevo = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    //permiso para editar hipotesis nivel 1-2
	permiso = $("#ROLE_439-6698");
	var boolPermisoEditarNivel1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    //permiso para eliminar hipotesis nivel 1-2
	permiso = $("#ROLE_439-6699");
	var boolPermisoEliminarNivel1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    //permiso para crear nueva hipotesis nivel 1-2
	permiso = $("#ROLE_439-6697");
	var boolPermisoNuevoNivel1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    //permiso para asignar hipotesis existente
	permiso = $("#ROLE_439-6700");
	var boolPermisoAsignar = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    //permiso para mover hipotesis
	permiso = $("#ROLE_439-6701");
	var boolPermisoMover = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

	//********* GRID NIVEL 1 ***************/
	var toolbarNivel1 = Ext.create('Ext.toolbar.Toolbar', {
		dock: 'top',
		align: '->',
		items   : 
		[ 
			{
				iconCls: 'icon_add',
				text: 'Nuevo',
				itemId: 'nuevaHipotesisNivel1',
				scope: this,
				handler: function(){ 
					crearHipotesis('1','0');
				}
			}
		]
	});

    gridNivel1 = Ext.create('Ext.grid.Panel', {
        id : 'gridNivel1',
        width: 400,
        height: 400,
        store: storeNivel1,
        selModel: Ext.create('Ext.selection.CheckboxModel',{
		    mode:'SINGLE',
		    showHeaderCheckbox : false
	    }),
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
	    enableTextSelection: true,
            id: 'gv1',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
		dockedItems: [ toolbarNivel1 ], 
        columns:[
                {
                  id: 'id_hipotesis_nivel1',
                  header: 'IdHipotesis',
                  dataIndex: 'id_hipotesis',
                  hidden: true,
                  hideable: false
				},
                {
				  id: 'tipo_caso_id',
				  header: 'tipo caso',
				  dataIndex: 'tipo_caso_id',
				  hidden: true,
				  hideable: false
				},  
                {
                  id: 'descripcion_hipotesis',
                  header: 'descripción',
                  dataIndex: 'descripcion_hipotesis',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_hipotesis_nivel1',
                  header: 'Nombre Hipótesis',
                  dataIndex: 'nombre_hipotesis',
                  width: 250,
				  sortable: true,
				  renderer: function(value, metaData, record, colIndex, store, view) 
				  {
					  metaData.tdAttr = 'data-qtip="' + record.data.descripcion_hipotesis + '"';
					  return value;
				  }
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
                        {
	                        getClass: function(v, meta, rec) {						
								if(!boolPermisoEditarNivel1){ rec.data.action2 = "icon-invisible"; }
	                            if (rec.get('action2') == "icon-invisible") 
	                                this.items[0].tooltip = '';
	                            else 
	                                this.items[0].tooltip = 'Editar';
	                            return rec.get('action2')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = storeNivel1.getAt(rowIndex);
								if(!boolPermisoEditarNivel1){ rec.data.action2 = "icon-invisible"; }
	                            if(rec.get('action2')!="icon-invisible")
                                    editarHipotesis(rec,1);
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
							}
                        },
                        {
	                        getClass: function(v, meta, rec) {							
								if(!boolPermisoEliminarNivel1){ rec.data.action3 = "icon-invisible"; }
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[1].tooltip = '';
	                            else 
	                                this.items[1].tooltip = 'Eliminar';
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = storeNivel1.getAt(rowIndex);
								if(!boolPermisoEliminarNivel1){ rec.data.action3 = "icon-invisible"; }
	                            if(rec.get('action3')!="icon-invisible")
								{
									eliminarHipotesis(rec, 1);
								} 
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion'); 
							}
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: storeNivel1,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
		renderTo: 'gridNivel1'
    });

	gridNivel1.on('select', function(view, record, index, eOpts)
	{
        if (record != "")
        {
			//Habilita botones Nuevo y Asigna Existente de Nivel 2 y Nivel 3
			if (boolPermisoNuevoNivel1)
			{
				gridNivel2.dockedItems.items[1].items.items[0].enable();
			}
			Ext.getCmp('txtNombreHipotesisNivel2').enable();
			Ext.getCmp('botonBuscarHipotesisNivel2').enable();
			Ext.getCmp('botonLimpiarHipotesisNivel2').enable();

            gridNivel3.dockedItems.items[1].items.items[0].disable();
			gridNivel3.dockedItems.items[1].items.items[1].disable();
			Ext.getCmp('txtNombreHipotesisNivel3').disable();
			Ext.getCmp('botonBuscarHipotesisNivel3').disable();
			Ext.getCmp('botonLimpiarHipotesisNivel3').disable();
			storeNivel2.getProxy().extraParams.nombre         = "";
			Ext.getCmp('txtNombreHipotesisNivel2').setRawValue("");
			storeNivel3.getProxy().extraParams.nombre         = "";
			Ext.getCmp('txtNombreHipotesisNivel3').setRawValue("");
            storeNivel2.getProxy().extraParams.padreHipotesis = record.data.id_hipotesis;
            storeNivel2.load();
        }
	});

    filterPanelNivel1 = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : false,
        collapsed: false,
        width: 400,
        title: 'NIVEL 1',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar(1);}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar(1);}
                }

                ],                
                items: [
                        {
                            xtype: 'textfield',
                            id: 'txtNombreHipotesisNivel1',
							fieldLabel: '',
							msgTarget: 'Nombre',
							emptyText:'Hipótesis nivel 1',
							value: '',
                            width: '98%'
                        }
                        ],	
        renderTo: 'filtroNivel1'
    });


	// GRID NIVEL 2
	var toolbarNivel2 = Ext.create('Ext.toolbar.Toolbar', {
		dock: 'top',
		align: '->',
		items   : 
		[
			{
                iconCls: 'icon_add',
                text: 'Nuevo',
                itemId: 'select',
                scope: this,
                handler: function(){
                    crearHipotesis('2',gridNivel1.selModel.lastSelected.raw.id_hipotesis);
                }
			}
		]
	});

    gridNivel2 = Ext.create('Ext.grid.Panel', {
        id : 'gridNivel2',
        width: 400,
        height: 400,
        store: storeNivel2,
        selModel: Ext.create('Ext.selection.CheckboxModel',{
		    mode:'SINGLE',
		    showHeaderCheckbox : false
	    }),
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
	    enableTextSelection: true,
            id: 'gv2',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
		dockedItems: [ toolbarNivel2 ], 
        columns:[
                {
                  id: 'id_hipotesis_nivel2',
                  header: 'IdHipotesis',
                  dataIndex: 'id_hipotesis',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_hipotesis_nivel2',
                  header: 'Nombre Hipótesis',
                  dataIndex: 'nombre_hipotesis',
                  width: 250,
                  sortable: true,
				  renderer: function(value, metaData, record, colIndex, store, view) 
				  {
					  metaData.tdAttr = 'data-qtip="' + record.data.descripcion_hipotesis + '"';
					  return value;
				  }
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
                        {
	                        getClass: function(v, meta, rec) {							
								if(!boolPermisoEditarNivel1){ rec.data.action2 = "icon-invisible"; }
	                            if (rec.get('action2') == "icon-invisible") 
	                                this.items[0].tooltip = '';
	                            else 
	                                this.items[0].tooltip = 'Editar';
	                            return rec.get('action2')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = storeNivel2.getAt(rowIndex);
								if(!boolPermisoEditarNivel1){ rec.data.action2 = "icon-invisible"; }
								if(rec.get('action2')!="icon-invisible")
								{
									editarHipotesis(rec,2);
								}
								else
							    {
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
								}
							}
                        },
                        {
	                        getClass: function(v, meta, rec) {							
								if(!boolPermisoEliminarNivel1){ rec.data.action3 = "icon-invisible"; }
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[1].tooltip = '';
	                            else 
	                                this.items[1].tooltip = 'Eliminar';
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = storeNivel2.getAt(rowIndex);
								if(!boolPermisoEliminarNivel1){ rec.data.action3 = "icon-invisible"; }
	                            if(rec.get('action3')!="icon-invisible")
								{
									eliminarHipotesis(rec, 2);
								} 
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción'); 
							}
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
			store: storeNivel2,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridNivel2'
	});

	gridNivel2.on('select', function(view, record, index, eOpts)
	{
        if (record != "")
        {
			//Habilita botones Nuevo y Asigna Existente de Nivel 3
			if (boolPermisoNuevo)
			{
                gridNivel3.dockedItems.items[1].items.items[0].enable();
			}
			if (boolPermisoAsignar)
			{
				gridNivel3.dockedItems.items[1].items.items[1].enable();
			}
			Ext.getCmp('txtNombreHipotesisNivel3').enable();
			Ext.getCmp('botonBuscarHipotesisNivel3').enable();
			Ext.getCmp('botonLimpiarHipotesisNivel3').enable();
			storeNivel3.getProxy().extraParams.nombre         = "";
			Ext.getCmp('txtNombreHipotesisNivel3').setRawValue("");
            storeNivel3.getProxy().extraParams.padreHipotesis = record.data.id_hipotesis;
            storeNivel3.load();
        }
    });
    filterPanelNivel2 = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : false,
        collapsed: false,
        width: 400,
        title: 'NIVEL 2',
            buttons: [
                {
					text: 'Buscar',
					id: 'botonBuscarHipotesisNivel2',
                    iconCls: "icon_search",
                    handler: function(){ buscar(2);}
                },
                {
					text: 'Limpiar',
					id: 'botonLimpiarHipotesisNivel2',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar(2);}
                }

                ],                
                items: [
                        {
                            xtype: 'textfield',
                            id: 'txtNombreHipotesisNivel2',
							fieldLabel: '',
							emptyText:'Hipótesis nivel 2',
							value: '',
                            width: '98%'
                        }
                        ],	
        renderTo: 'filtroNivel2'
    });

	// GRID NIVEL 3
	var toolbarNivel3 = Ext.create('Ext.toolbar.Toolbar', {
		dock: 'top',
		align: '->',
		items   : 
		[
			{
                iconCls: 'icon_add',
                text: 'Nuevo',
                itemId: 'add',
                scope: this,
                handler: function(){
                    crearHipotesis('3',gridNivel2.selModel.lastSelected.raw.id_hipotesis);
                }
			},
			{
                iconCls: 'icon_check',
                text: 'Asigna Existente',
                itemId: 'addExistente',
                scope: this,
                handler: function(){
                    asignaHipotesisLibres(gridNivel2.selModel.lastSelected.raw.id_hipotesis);
                }
			}
		]
	});

    gridNivel3 = Ext.create('Ext.grid.Panel', {
        id : 'gridNivel3',
        width: 400,
        height: 400,
        store: storeNivel3,
        selModel: Ext.create('Ext.selection.CheckboxModel',{
		    mode:'SINGLE',
		    showHeaderCheckbox : false
	    }),
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
	    enableTextSelection: true,
            id: 'gv3',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
		dockedItems: [ toolbarNivel3 ], 
        columns:[
                {
                  id: 'id_hipotesis_nivel3',
                  header: 'IdHipotesis',
                  dataIndex: 'id_hipotesis',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_hipotesis_nivel3',
                  header: 'Nombre Hipótesis',
                  dataIndex: 'nombre_hipotesis',
                  width: 250,
                  sortable: true,
				  renderer: function(value, metaData, record, colIndex, store, view) 
				  {
					  metaData.tdAttr = 'data-qtip="' + record.data.descripcion_hipotesis + '"';
					  return value;
				  }
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
						{
	                        getClass: function(v, meta, rec) {
								rec.data.action1 = "button-grid-updowngrade";				
								if(!boolPermisoMover){ rec.data.action1 = "icon-invisible"; }
								if (rec.get('action1') == "icon-invisible") 
									this.items[0].tooltip = '';
								else 
									this.items[0].tooltip = 'Mover';
									
								return rec.get('action1')
							},
	                        tooltip: 'Mover',
	                        handler: function(grid, rowIndex, colIndex) {
								var rec = storeNivel3.getAt(rowIndex);
								rec.data.action1 = "button-grid-updowngrade";				
								if(!boolPermisoMover){ rec.data.action1 = "icon-invisible"; }							
								if(rec.get('action1')!="icon-invisible")
								    moverHipotesis(rec.raw.id_hipotesis);
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
							}
						},
                        {
	                        getClass: function(v, meta, rec) {						
								if(!boolPermisoEditar){ rec.data.action2 = "icon-invisible"; }
	                            if (rec.get('action2') == "icon-invisible") 
	                                this.items[1].tooltip = '';
	                            else 
	                                this.items[1].tooltip = 'Editar';
	                            return rec.get('action2')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = storeNivel3.getAt(rowIndex);													
								if(!boolPermisoEditar){ rec.data.action2 = "icon-invisible"; }
								
								if(rec.get('action2')!="icon-invisible")
								{
									editarHipotesis(rec,3);
								}
								else
								{
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
								}
							}
                        },
                        {
	                        getClass: function(v, meta, rec) {
								if(!boolPermisoEliminar){ rec.data.action3 = "icon-invisible"; }
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[2].tooltip = '';
	                            else 
	                                this.items[2].tooltip = 'Eliminar';
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = storeNivel3.getAt(rowIndex);						
								if(!boolPermisoEliminar){ rec.data.action3 = "icon-invisible"; }
	                            if(rec.get('action3')!="icon-invisible")
								{
									eliminarHipotesis(rec, 3);
								} 
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción'); 
							}
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
			store: storeNivel3,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridNivel3'
    });

	filterPanelNivel3 = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : false,
        collapsed: false,
        width: 400,
        title: 'NIVEL 3',
            buttons: [
                {
					text: 'Buscar',
					id: 'botonBuscarHipotesisNivel3',
                    iconCls: "icon_search",
                    handler: function(){ buscar(3);}
                },
                {
					text: 'Limpiar',
					id: 'botonLimpiarHipotesisNivel3',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar(3);}
                }

                ],                
                items: [
                        {
                            xtype: 'textfield',
                            id: 'txtNombreHipotesisNivel3',
							fieldLabel: '',
							emptyText:'Hipótesis nivel 3',
							value: '',
                            width: '98%'
                        }
                        ],	
        renderTo: 'filtroNivel3'
	});

	if (!boolPermisoNuevoNivel1)
	{
		gridNivel1.dockedItems.items[1].items.items[0].disable();
	}
    //Deshabilita botones de Nivel 2
	gridNivel2.dockedItems.items[1].items.items[0].disable();
	Ext.getCmp('txtNombreHipotesisNivel2').disable();
	Ext.getCmp('botonBuscarHipotesisNivel2').disable();
	Ext.getCmp('botonLimpiarHipotesisNivel2').disable();
    //Deshabilita botones de Nivel 3
	gridNivel3.dockedItems.items[1].items.items[0].disable();
	gridNivel3.dockedItems.items[1].items.items[1].disable();
	Ext.getCmp('txtNombreHipotesisNivel3').disable();
	Ext.getCmp('botonBuscarHipotesisNivel3').disable();
	Ext.getCmp('botonLimpiarHipotesisNivel3').disable();
	

	function crearHipotesis(nivel,hipotesisId)
	{
	    formPanelCrearHipotesis = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 200,                                
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                defaults: {
                    width: 500
                },
                items: 
				[
					{
						xtype: 'textfield',
						id: 'txtCreaHipotesisId',
						fieldLabel: 'idHipotesis',
						value: hipotesisId,
						hidden : true
					},
					{
						xtype: 'textfield',
						id: 'txtCreaNombre',
						fieldLabel: 'Nombre',
						emptyText:'Nombre',
						value: '',
					},
					{
						xtype: 'textareafield',
						id: 'txtCreaDescripcion',
						fieldLabel: 'Descripción',
						emptyText:'Descripción',
						value: '',
					},
                ]
            }],
            buttons: [{
			    text: 'Guardar',
			    formBind: true,
			    handler: function(){					
					var parametros = {
						"nombre"         : Ext.getCmp('txtCreaNombre').getValue(),
						"descripcion"    : Ext.getCmp('txtCreaDescripcion').getValue(),
						"tipoCaso"       : "",
						"padreHipotesis" : hipotesisId
					};
					$.ajax({
							data :  parametros,
							url  :  url_creaHipotesis,
							type :  'post',
							beforeSend: function () {
								if(Ext.getCmp('txtCreaNombre').getValue() === "")
								{
									Ext.Msg.alert('Alerta ', 'Por favor ingrese nombre');
									return false;
								}
								else if(Ext.getCmp('txtCreaDescripcion').getValue() === "")
								{
									Ext.Msg.alert('Alerta ', 'Por favor ingrese descripción');
									return false;
								}
								else
								{
								    return true;
								}
							},
							success:  function (response) {
								if (nivel === '1')
								{
									storeNivel1.getProxy().extraParams.padreHipotesis = hipotesisId;
									storeNivel1.load();				
								}
								else if (nivel === '2')
								{
									storeNivel2.getProxy().extraParams.padreHipotesis = hipotesisId;
									storeNivel2.load();				
								}
								else if (nivel === '3')
								{
									storeNivel3.getProxy().extraParams.padreHipotesis = hipotesisId;
									storeNivel3.load();				
								}
								Ext.Msg.alert('Mensaje ', 'Se creó Hipótesis con éxito');
								winAgregarHipotesis.destroy();
							},
							failure: function(response){
								Ext.Msg.alert('Alerta ', 'Se produjo un error, No se pudo crear Hipótesis');
							}
					});
			    }
		    },{
                text: 'Cancelar',
                handler: function(){
                    winAgregarHipotesis.destroy();
                }
		    }]
	    });
	    winAgregarHipotesis = Ext.create('Ext.window.Window', {
            title: 'Agregar Hipótesis',
            modal: true,
            closable: false,
            //width: 650,
            layout: 'fit',
            items: [formPanelCrearHipotesis]
	    }).show();
	}

	function asignaHipotesisLibres(hipotesisId)
	{
		var objStoreHipotesisSinPadre = 
		{ 
			total: 'total',
			proxy: {
				type  : 'ajax',
				url   : url_admiHipotesisArbolGrid,
				reader: {
					type          : 'json',
					totalProperty : 'total',
					root          : 'encontrados'
				},
				extraParams: {
						nombre: '',
						estado: 'Activo',
						buscarSinPadre : 'S',
						start : 0,
						limit : 999
				}
			},
			fields:
			[
				{name:'id_hipotesis', mapping:'id_hipotesis'},
				{name:'nombre_hipotesis', mapping:'nombre_hipotesis'}
			]
		};
		var storeStoreHipotesisSinPadre = new Ext.data.Store(objStoreHipotesisSinPadre);
		comboHipotesisLibres = Ext.create('Ext.form.ComboBox', {
			id:'comboHipotesisLibres',
			store: storeStoreHipotesisSinPadre,
			displayField: 'nombre_hipotesis',
			valueField: 'id_hipotesis',
			fieldLabel: 'Hipotesis:',	
			queryMode: "remote",
			emptyText: '',
		 });
		formPanelAsignarHipotesis = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 200,                                
            msgTarget: 'side'
        },
        items: [
        {
                xtype: 'fieldset',
                defaults: {
                    width: 500
                },
                items: 
				[
					comboHipotesisLibres,
                ]
            }],
            buttons: [{
			    text: 'Guardar',
			    formBind: true,
			    handler: function(){					
					var parametros = {
						"idHipotesis"    : formPanelAsignarHipotesis.form._fields.items[0].value,
						"padreHipotesis" : hipotesisId
					};
					$.ajax({
							data :  parametros,
							url  :  url_editarHipotesis,
							type :  'post',
							beforeSend: function () {

							},
							success:  function (response) {
								Ext.Msg.alert('Mensaje ', 'Se asignó hipótesis con éxito');

							},
							failure: function(response){
								Ext.Msg.alert('Error ', 'Se produjo un error y no se pudo asignar hipótesis');
							}
					});
					winAsignarHipotesis.destroy();
					storeNivel3.getProxy().extraParams.padreHipotesis = hipotesisId;
					storeNivel3.load(); 
			    }
		    },{
                text: 'Cancelar',
                handler: function(){
                    winAsignarHipotesis.destroy();
                }
		    }]
	    });
	    winAsignarHipotesis = Ext.create('Ext.window.Window', {
            title: 'Asignar Hipótesis',
            modal: true,
            closable: false,
            //width: 650,
            layout: 'fit',
            items: [formPanelAsignarHipotesis]
	    }).show();
	}

	function moverHipotesis(idHipotesis)
	{
		var objStoreHipotesis = 
		{ 
			total: 'total',
			proxy: {
				type  : 'ajax',
				url   : url_admiHipotesisArbolGrid,
				reader: {
					type          : 'json',
					totalProperty : 'total',
					root          : 'encontrados'
				},
				extraParams: {
						estado: 'Activo',
						start : 0,
						limit : 999
				}
			},
			fields:
			[
				{name:'id_hipotesis', mapping:'id_hipotesis'},
				{name:'nombre_hipotesis', mapping:'nombre_hipotesis'}
			]
		};
		var storeHipotesisNivel1 = new Ext.data.Store(objStoreHipotesis);
		storeHipotesisNivel1.getProxy().extraParams.padreHipotesis = 0;
		comboHipotesisNivel1 = Ext.create('Ext.form.ComboBox', {
			id:'comboMoverHipotesisNivel1',
			store: storeHipotesisNivel1,
			displayField: 'nombre_hipotesis',
			valueField: 'id_hipotesis',
			fieldLabel: 'Hipótesis Nivel 1:',	
			queryMode: "remote",
			emptyText: '',
			listeners:{
				select: function(combo)
				{
				 Ext.getCmp('comboMoverHipotesisNivel2').value = "";
				 Ext.getCmp('comboMoverHipotesisNivel2').setRawValue("");
				 Ext.getCmp('comboMoverHipotesisNivel2').reset();
				 Ext.getCmp('comboMoverHipotesisNivel2').setDisabled(false);
				 storeHipotesisNivel2.proxy.extraParams = {padreHipotesis: combo.getValue(),estado:'Activo'};
				 storeHipotesisNivel2.load();
				}
			}
		 });
		 var storeHipotesisNivel2 = new Ext.data.Store(objStoreHipotesis);
		 comboHipotesisNivel2 = Ext.create('Ext.form.ComboBox', {
			 id:'comboMoverHipotesisNivel2',
			 store: storeHipotesisNivel2,
			 displayField: 'nombre_hipotesis',
			 valueField: 'id_hipotesis',
			 fieldLabel: 'Hipótesis Nivel 2:',	
			 queryMode: "remote",
			 emptyText: '',
			 disabled: true
		  });
		formPanelMoverHipotesis = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 200,                                
                msgTarget: 'side'
            },
            items: [
            {
                xtype: 'fieldset',
                defaults: {
                    width: 500
                },
                items: 
				[
					comboHipotesisNivel1,
					comboHipotesisNivel2,
                ]
            }],
            buttons: [{
			    text: 'Guardar',
			    formBind: true,
			    handler: function(){					
					var parametros = {
						"idHipotesis"    : idHipotesis,
						"padreHipotesis" : Ext.getCmp('comboMoverHipotesisNivel2').getValue()
					};
					$.ajax({
							data :  parametros,
							url  :  url_editarHipotesis,
							type :  'post',
							beforeSend: function () {
								if (Ext.getCmp('comboMoverHipotesisNivel2').getValue() === "" || Ext.getCmp('comboMoverHipotesisNivel2').getValue() === null)
								{
									Ext.Msg.alert('Alerta ', 'Por favor seleccione una hipótesis de nivel 2');
									return false;
								}
								else
								{
								    return true;
								}
							},
							success:  function (response) {
								Ext.Msg.alert('Mensaje ', 'Se movió hipótesis con éxito');
								storeNivel3.getProxy().extraParams.padreHipotesis = gridNivel2.selModel.lastSelected.raw.id_hipotesis;
								storeNivel3.load();
								winMoverHipotesis.destroy();

							},
							failure: function(response){
								Ext.Msg.alert('Error ', 'Se produjo un error y no se pudo mover hipótesis');
							}
					});
			    }
		    },{
                text: 'Cancelar',
                handler: function(){
                    winMoverHipotesis.destroy();
                }
		    }]
	    });
	    winMoverHipotesis = Ext.create('Ext.window.Window', {
            title: 'Mover Hipótesis',
            modal: true,
            closable: false,
            //width: 650,
            layout: 'fit',
            items: [formPanelMoverHipotesis]
	    }).show();
	}

	function editarHipotesis(rec, nivel)
	{
		formPanelEditarHipotesis = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 200,                                
                msgTarget: 'side'
            },
            items: [
            {
                xtype: 'fieldset',
                defaults: {
                    width: 500
                },
                items: 
				[
					{
						xtype: 'textfield',
						id: 'txtEditaIdHipotesis',
						value: rec.raw.id_hipotesis,
						hidden : true
					},
					{
						xtype: 'textfield',
						id: 'txtEditaNombre',
						fieldLabel: 'Nombre',
						emptyText:'Nombre',
						value: rec.raw.nombre_hipotesis,
					},
					{
						xtype: 'textareafield',
						id: 'txtEditaDescripcion',
						fieldLabel: 'Descripción',
						emptyText:'Descripción',
						value: rec.raw.descripcion_hipotesis,
					},
                ]
            }],
            buttons: [{
			    text: 'Guardar',
			    formBind: true,
			    handler: function(){					
					var parametros = {
						"idHipotesis" : Ext.getCmp('txtEditaIdHipotesis').getValue(),
						"nombre"      : Ext.getCmp('txtEditaNombre').getValue(),
						"descripcion" : Ext.getCmp('txtEditaDescripcion').getValue(),
						"tipoCaso"    : "",
					};
					$.ajax({
							data :  parametros,
							url  :  url_editarHipotesis,
							type :  'post',
							beforeSend: function () {
								if(Ext.getCmp('txtEditaNombre').getValue() === "")
								{
									Ext.Msg.alert('Alerta ', 'Por favor ingrese nombre');
									return false;
								}
								else if(Ext.getCmp('txtEditaDescripcion').getValue() === "")
								{
									Ext.Msg.alert('Alerta ', 'Por favor ingrese descripción');
									return false;
								}
								else
								{
								    return true;
								}
							},
							success:  function (response) {
								if (nivel === 1)
								{
									storeNivel1.load();
								}
								else if (nivel === 2)
								{
									storeNivel2.load();
								}
								else if (nivel === 3)
								{
									storeNivel3.load();
								}
								Ext.Msg.alert('Mensaje ', 'Se editó Hipótesis con éxito');
								winEditarHipotesis.destroy();
							},
							failure: function(response){
								Ext.Msg.alert('Error ', 'Se produjo un error y no se pudo editar Hipótesis');
							}
					});
			    }
		    },{
                text: 'Cancelar',
                handler: function(){
                    winEditarHipotesis.destroy();
                }
		    }]
	    });
	    winEditarHipotesis = Ext.create('Ext.window.Window', {
            title: 'Editar Hipótesis',
            modal: true,
            closable: false,
            //width: 650,
            layout: 'fit',
            items: [formPanelEditarHipotesis]
	    }).show();
	}
	
	function eliminarHipotesis(rec, nivel)
	{
		Ext.MessageBox.show({
			icon: Ext.Msg.INFO,
			title:'Mensaje',
			msg: 'Está Seguro(a) de eliminar la hipótesis?',
			buttons    : Ext.MessageBox.YESNO,
			buttonText: {yes: "Si"},
			fn: function(btn){
				if(btn=='yes'){
					var parametros = {
						"idHipotesis" : rec.raw.id_hipotesis
					};
					$.ajax({
						data :  parametros,
						url  :  url_eliminarHipotesis,
						type :  'post',
						beforeSend: function () {
			
						},
						success:  function (response) {
							if (nivel === 1)
							{
								storeNivel1.load();
							}
							else if (nivel === 2)
							{
								storeNivel2.load();
							}
							else if (nivel === 3)
							{
								storeNivel3.load();
							}
							Ext.Msg.alert('Mensaje ', 'Se eliminó Hipótesis con éxito');
							//winEditarHipotesis.destroy();
						},
						failure: function(response){
							Ext.Msg.alert('Error ', 'Se produjo un error, No se pudo eliminar Hipótesis');
						}
					});
				}
				else
				{
					return false;
				}
			}
		});
	}
});

/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(nivel){
	if (nivel === 1)
	{
		storeNivel1.getProxy().extraParams.nombre = Ext.getCmp('txtNombreHipotesisNivel1').value;
		storeNivel1.load();
	}
	else if (nivel === 2)
	{
		storeNivel2.getProxy().extraParams.nombre = Ext.getCmp('txtNombreHipotesisNivel2').value;
		storeNivel2.load();
	}
	else if (nivel === 3)
	{
		storeNivel3.getProxy().extraParams.nombre = Ext.getCmp('txtNombreHipotesisNivel3').value;
		storeNivel3.load();
	}
}
function limpiar(nivel){
	if (nivel === 1)
	{
		Ext.getCmp('txtNombreHipotesisNivel1').value="";
		Ext.getCmp('txtNombreHipotesisNivel1').setRawValue("");
		storeNivel1.getProxy().extraParams.nombre = Ext.getCmp('txtNombreHipotesisNivel1').value;
		storeNivel1.load();
	}
	else if (nivel === 2)
	{
		Ext.getCmp('txtNombreHipotesisNivel2').value="";
		Ext.getCmp('txtNombreHipotesisNivel2').setRawValue("");
		storeNivel2.getProxy().extraParams.nombre = Ext.getCmp('txtNombreHipotesisNivel2').value;
		storeNivel2.load();
	}
	else if (nivel === 3)
	{
		Ext.getCmp('txtNombreHipotesisNivel3').value="";
		Ext.getCmp('txtNombreHipotesisNivel3').setRawValue("");
		storeNivel3.getProxy().extraParams.nombre = Ext.getCmp('txtNombreHipotesisNivel3').value;
		storeNivel3.load();
	}
}