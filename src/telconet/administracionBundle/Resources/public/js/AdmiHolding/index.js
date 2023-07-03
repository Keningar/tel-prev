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
			{name:'id_parametro_det', mapping:'id_parametro_det'},
			{name:'Nombre', mapping:'Nombre'},
			{name:'Identificacion', mapping:'Identificacion'},
            {name:'Estado', mapping:'Estado'},
            {name:'Vendedor', mapping:'Vendedor'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'}                 
		],
        idProperty: 'id_parametro_det'
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
                nombre: ''
            }
        },
        autoLoad: true
    });
   
   storeEstados = Ext.create('Ext.data.Store', {
        fields : ['strIdEstado', 'strNombreEstado'],
        data   : [
            {strIdEstado : 'Activo', strNombreEstado: 'Activo'},
            {strIdEstado : 'Eliminado',  strNombreEstado: 'Eliminado'}
        ]
    });
    cboEstados = new Ext.form.ComboBox({
        xtype: 'combobox',
        editable: false,
        store: storeEstados,
        labelAlign: 'right',
        id: 'cboEstados',
        name: 'cboEstados',
        valueField: 'strIdEstado',
        displayField: 'strNombreEstado',
        fieldLabel: 'Estado',
        value:'',
        width: 200
    });  
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permisoDelete = $("#ROLE_452-1");
	var boolPermiso1 = (typeof permisoDelete === 'undefined') ? false : (permisoDelete.val() == 1 ? true : false);	
	
	var permisoDeleteAjax = $("#ROLE_452-1");
	var boolPermiso2 = (typeof permisoDeleteAjax === 'undefined') ? false : (permisoDeleteAjax.val() == 1 ? true : false);	
	
	var eliminarBtn = "";
	var verificar   = "";
	if(boolPermiso1 && boolPermiso2)
	{
	    verificar = Ext.create('Ext.selection.CheckboxModel', {
	        checkOnly: true
	    })
	
		eliminarBtn = Ext.create('Ext.button.Button', {
			iconCls: 'icon_delete',
			text: 'Eliminar varios',
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
				text: 'Marcar Todos',
				itemId: 'select',
				scope: this,
				handler: function(){ 
					Ext.getCmp('grid').getPlugin('pagingSelectionPersistence').selectAll()
				}
			},
			{
				iconCls: 'icon_limpiar',
				text: 'Desmarcar Todos',
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
        id : 'grid',
        width: 840,
        height: 387,
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
                  id: 'id_parametro_det',
                  header: 'id_parametro_det',
                  dataIndex: 'id_parametro_det',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'Nombre',
                  header: 'Nombre',
                  dataIndex: 'Nombre',
                  width: 220,
                  sortable: true
                },
                {
                  id: 'Identificacion',
                  header: 'Identificacion',
                  dataIndex: 'Identificacion',
                  width: 150,
                  sortable: true
				},
                {
                  id: 'Estado',
                  header: 'Estado',
                  dataIndex: 'Estado',
                  width: 85,
                  sortable: true
                },
                {
                  id: 'Vendedor',
                  header: 'Vendedor',
                  dataIndex: 'Vendedor',
                  width: 220,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 140,
                    items: [
                        {/*EDITAR*/
	                        getClass: function(v, meta, rec) {
								var permisoEdit = $("#ROLE_452-1");
								var boolPermiso = (typeof permisoEdit === 'undefined') ? false : (permisoEdit.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if (rec.get('action2') == "icon-invisible") 
	                                this.items[0].tooltip = '';
	                            else 
	                                this.items[0].tooltip = 'Editar';
	                            
	                            return rec.get('action2')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
								var permisoEdit = $("#ROLE_452-1");
								var boolPermiso = (typeof permisoEdit === 'undefined') ? false : (permisoEdit.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if(rec.get('action2')!="icon-invisible"){
                                    ///////
                                    var formPanel = Ext.create('Ext.form.Panel', {
                                        bodyPadding: 2,
                                        waitMsgTarget: true,
                                        fieldDefaults: {
                                            labelAlign: 'left',
                                            labelWidth: 80,
                                            msgTarget: 'side'
                                        },
                                        items: [

                                        {
                                            xtype: 'fieldset',
                                            title: '',
                                            defaultType: 'textfield',
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    title: 'Razón Social',
                                                    defaultType: 'textfield',
                                                    defaults: {
                                                        width: 250
                                                    },
                                                    items: [
                                                        {                                                
                                                            xtype: 'textfield',
                                                            fieldLabel: 'Nombre',
                                                            id: 'NombreC',
                                                            name: 'NombreC',
                                                            displayField: rec.get('Nombre'),
                                                            value: rec.get('Nombre'),
                                                            readOnly: false
                                                        },
                                                        {                                                
                                                            xtype: 'textfield',
                                                            fieldLabel: 'Identificacion',
                                                            id: 'IdentificacionC',
                                                            name: 'IdentificacionC',
                                                            displayField: rec.get('Identificacion'),
                                                            value: rec.get('Identificacion'),
                                                            readOnly: false
                                                        },
                                                        {
                                                            xtype: 'combobox',
                                                            name: 'Estado',
                                                            id: 'EstadoC',
                                                            displayField: "strNombreEstado",
                                                            valueField: "strIdEstado",
                                                            fieldLabel: "Estado",
                                                            emptyText: 'Seleccione',
                                                            store:storeEstados  

                                                        }
                                                    ]
                                                }

                                            ]
                                        }
                                        ],
                                        buttons: [
                                            {
                                                text: 'Guardar',
                                                formBind: true,
                                                handler: function(){

                                                    var IdParametroDet  = rec.get('id_parametro_det');
                                                    var Identificacion  = Ext.getCmp('IdentificacionC').value;
                                                    var Nombre          = Ext.getCmp('NombreC').value;
                                                    var Estado          = Ext.getCmp('EstadoC').getValue();

                                                    if(Identificacion == '' || Nombre == '')
                                                    {
                                                        Ext.MessageBox.show({
                                                            title: 'Error',
                                                            msg: 'Por favor Ingresar el Nombre y Identificación Requeridos',
                                                            buttons: Ext.MessageBox.OK,
                                                            icon: Ext.MessageBox.ERROR
                                                        }); 
                                                        return;
                                                    }  

                                                    Ext.Msg.confirm({
                                                        title:'Alerta',
                                                        msg: 'Esta seguro de Actualizar los datos?',
                                                        buttons: Ext.Msg.YESNO,
                                                        icon: Ext.MessageBox.QUESTION,
                                                        buttonText: {
                                                            yes: 'si', no: 'no'
                                                        },
                                                        fn: function(btn){
                                                            if(btn=='yes'){
                                                                Ext.MessageBox.wait('Actualizando los datos...');

                                                                Ext.Ajax.request({
                                                                    url: updateHolding,
                                                                    method: 'post',
                                                                    timeout: 900000,
                                                                    params: { 
                                                                        idParametroDet:     IdParametroDet,
                                                                        identificacion:     Identificacion,
                                                                        nombre:             Nombre,
                                                                        estado:             Estado
                                                                    },
                                                                    success: function(response){
                                                                        Ext.MessageBox.hide();
                                                                        win.hide();

                                                                        Ext.Msg.alert('Mensaje',response.responseText, function(btn){
                                                                            if(btn=='ok' || btn=='cancel')
                                                                            {
                                                                                    win.destroy();
                                                                                store.load();
                                                                            }
                                                                        });
                                                                    },
                                                                    failure: function(response)
                                                                    {
                                                                        Ext.MessageBox.hide();
                                                                        win.hide();
                                                                        Ext.MessageBox.show({
                                                                            title: 'Error',
                                                                            msg: response.responseText,
                                                                            buttons: Ext.MessageBox.OK,
                                                                            icon: Ext.MessageBox.ERROR,
                                                                            fn: function(btn){
                                                                                if(btn=='ok')
                                                                                {
                                                                                    win.show();    
                                                                                }    
                                                                            }
                                                                        }); 
                                                                    }
                                                                }); 
                                                            }
                                                        }
                                                    });
                                                }
                                            },
                                            {
                                                text: 'Cerrar',
                                                handler: function(){
                                                    win.destroy();
                                                }
                                            }]
                                    });

                                    var win = Ext.create('Ext.window.Window', {
                                        title: 'Modificación Holding',
                                        modal: true,
                                        closable: true,
                                        layout: 'fit',
                                        width: 340,
                                        items: [formPanel]
                                    }).show();
                                }else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {/*ELIMINAR*/
	                        getClass: function(v, meta, rec) {
								var permisoDelete = $("#ROLE_452-1");
								var boolPermiso = (typeof permisoDelete === 'undefined') ? false : (permisoDelete.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permisoDeleteAjax = $("#ROLE_452-1");
								var boolPermisoDeleteAjax = (typeof permisoDeleteAjax === 'undefined') ? false : (permisoDeleteAjax.val() == 1 ? true : false);							
								if(!boolPermisoDeleteAjax){ rec.data.action3 = "icon-invisible"; }
								
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[1].tooltip = '';
	                            else 
	                                this.items[1].tooltip = 'Eliminar';
	                            
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
								var permisoDelete = $("#ROLE_452-1");
								var boolPermiso   = (typeof permisoDelete === 'undefined') ? false : (permisoDelete.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permisoDeleteAjax     = $("#ROLE_452-1");
								var boolPermisoDeleteAjax = (typeof permisoDeleteAjax === 'undefined') ? false : (permisoDeleteAjax.val() == 1 ? true : false);							
								if(!boolPermisoDeleteAjax){ rec.data.action3 = "icon-invisible"; }
								
	                            if(rec.get('action3')!="icon-invisible")
								{
	                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
	                                    if(btn=='yes'){
	                                        Ext.Ajax.request({
	                                            url: "deleteAjax",
	                                            method: 'post',
	                                            params: { param : rec.get('id_parametro_det')},
	                                            success: function(response){
													store.load();
													Ext.Msg.alert('Informacion ',response.responseText);
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
        border:false,
        buttonAlign: 'center',
        layout: {
			type: 'table',
			columns: 3,
            align: 'center'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 840,
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
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre',
							value: '',
                            width: '365'
						},
                        {html: "      ", border: false, width: 110},
                        {
                            xtype: 'textfield',
                            id: 'txtIdentificacion',
                            fieldLabel: 'Identificación',
							value: '',
                            width: '365'
						},
                        
                        ,{
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltestado',
							value:'',
                            store: [
                                ['Activo','Activo'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '365'
						},						
						{html: "&nbsp;", border: false, width: 110},
						{html: "&nbsp;", border: false, width: 355}
						
                        ],	
        renderTo: 'filtro'
    }); 
    
});

/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
	store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltestado').value;	
	store.currentPage = 1;
	store.load();
}
function limpiar(){
    Ext.getCmp('txtNombre').value="";
	Ext.getCmp('txtNombre').setRawValue("");

    Ext.getCmp('txtIdentificacion').value="";
	Ext.getCmp('txtIdentificacion').setRawValue("");
	
    Ext.getCmp('sltestado').value="";
    Ext.getCmp('sltestado').setRawValue("");
    
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
	store.getProxy().extraParams.apellido = Ext.getCmp('txtIdentificacion').value;
    store.getProxy().extraParams.mes = Ext.getCmp('sltestado').value;	
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
							store.load();
							Ext.Msg.alert('Informacion ',response.responseText);
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
			Ext.Msg.alert('Alerta','Por lo menos uno de las registro se encuentra en estado ELIMINADO');
		}
    }
    else
    {
		Ext.Msg.alert('Alerta','Seleccione por lo menos un registro de la lista');        
    }
}