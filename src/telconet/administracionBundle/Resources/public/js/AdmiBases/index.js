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
			{name:'descripcion', mapping:'descripcion'},
			{name:'VENDEDOR', mapping:'VENDEDOR'},
            {name:'BASEID', mapping:'BASEID'},
            {name:'BASEBS', mapping:'BASEBS'},
			{name:'VALOR', mapping:'VALOR'},
			{name:'VIGENCIA', mapping:'VIGENCIA'},
			{name:'estado', mapping:'estado'},
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
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permisoDelete = $("#ROLE_417-8");
	var boolPermiso1 = (typeof permisoDelete === 'undefined') ? false : (permisoDelete.val() == 1 ? true : false);	
	
	var permisoDeleteAjax = $("#ROLE_417-9");
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
			//tbfill -> alinea los items siguientes a la derecha
			{ xtype: 'tbfill' },
			eliminarBtn
		]
	});

    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        width: 1000,
        height: 390,
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
                  id: 'descripcion',
                  header: 'Descripcion',
                  dataIndex: 'descripcion',
                  width: 170,
                  sortable: true
                },
                {
                  id: 'VENDEDOR',
                  header: 'Vendedor',
                  dataIndex: 'VENDEDOR',
                  width: 240,
                  sortable: true
				},
                {
					id: 'BASEID',
					header: 'Base I/D',
					dataIndex: 'BASEID',
					width: 80,
					sortable: true
				},
                {
					id: 'BASEBS',
					header: 'Base Bs',
					dataIndex: 'BASEBS',
					width: 80,
					sortable: true
				},
                {
					id: 'VALOR',
					header: 'Valor',
					dataIndex: 'VALOR',
					width: 100,
					sortable: true
				},				
                {
                  id: 'VIGENCIA',
                  header: 'Vigencia',
                  dataIndex: 'VIGENCIA',
                  width: 120,
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
                    width: 100,
                    items: [{/*VER*/
	                        getClass: function(v, meta, rec) {							
								var permisoShow = $("#ROLE_417-6");
								var boolPermiso = (typeof permisoShow === 'undefined') ? false : (permisoShow.val() == 1 ? true : false);							
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
									
								var permisoShow = $("#ROLE_417-6");
								var boolPermiso = (typeof permisoShow === 'undefined') ? false : (permisoShow.val() == 1 ? true : false);
								if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
																
								if(rec.get('action1')!="icon-invisible")
									window.location = rec.get('id_parametro_det')+"/show";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {/*EDITAR*/
	                        getClass: function(v, meta, rec) {
								var permisoEdit = $("#ROLE_417-4");
								var boolPermiso = (typeof permisoEdit === 'undefined') ? false : (permisoEdit.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if (rec.get('action2') == "icon-invisible") 
	                                this.items[1].tooltip = '';
	                            else 
	                                this.items[1].tooltip = 'Editar';
	                            
	                            return rec.get('action2')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
								var permisoEdit = $("#ROLE_417-4");
								var boolPermiso = (typeof permisoEdit === 'undefined') ? false : (permisoEdit.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if(rec.get('action2')!="icon-invisible")
	                                window.location = rec.get('id_parametro_det')+"/edit";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {/*ELIMINAR*/
	                        getClass: function(v, meta, rec) {
								var permisoDelete = $("#ROLE_417-8");
								var boolPermiso = (typeof permisoDelete === 'undefined') ? false : (permisoDelete.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permisoDeleteAjax = $("#ROLE_417-9");
								var boolPermisoDeleteAjax = (typeof permisoDeleteAjax === 'undefined') ? false : (permisoDeleteAjax.val() == 1 ? true : false);							
								if(!boolPermisoDeleteAjax){ rec.data.action3 = "icon-invisible"; }
								
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[2].tooltip = '';
	                            else 
	                                this.items[2].tooltip = 'Eliminar';
	                            
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
								var permisoDelete = $("#ROLE_417-8");
								var boolPermiso   = (typeof permisoDelete === 'undefined') ? false : (permisoDelete.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permisoDeleteAjax     = $("#ROLE_417-9");
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
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
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
                items: [
					{html: "&nbsp;", border: false, width: 110},
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre',
							value: '',
                            width: '365'
						},
                        {
                            xtype: 'textfield',
                            id: 'txtApellido',
                            fieldLabel: 'Apellido',
							value: '',
                            width: '365'
						},
						{html: "&nbsp;", border: false, width: 110},
                        {
                            xtype: 'textfield',
                            id: 'txtlogin',
                            fieldLabel: 'Login',
							value: '',
                            width: '365'
						},						
                        ,{
                            xtype: 'combobox',
                            fieldLabel: 'Mes vigente',
                            id: 'sltmes',
							value:'',
                            store: [
                                ['Enero','Enero'],
                                ['Febrero','Febrero'],
								['Marzo','Marzo'],
                                ['Abril','Abril'],
                                ['Mayo','Mayo'],
                                ['Junio','Junio'],								
								['Julio','Julio'],
								['Agosto','Agosto'],
                                ['Septiembre','Septiembre'],
                                ['Octubre','Octubre'],
                                ['Noviembre','Noviembre'],								
                                ['Diciembre','Diciembre']								
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
	store.getProxy().extraParams.apellido = Ext.getCmp('txtApellido').value;
	store.getProxy().extraParams.login = Ext.getCmp('txtlogin').value;
    store.getProxy().extraParams.mes = Ext.getCmp('sltmes').value;
	store.currentPage = 1;
	store.load();
}
function limpiar(){
    Ext.getCmp('txtNombre').value="";
	Ext.getCmp('txtNombre').setRawValue("");

    Ext.getCmp('txtApellido').value="";
	Ext.getCmp('txtApellido').setRawValue("");

	Ext.getCmp('txtlogin').value="";
	Ext.getCmp('txtlogin').setRawValue("");

    Ext.getCmp('sltmes').value="";
    Ext.getCmp('sltmes').setRawValue("");
    
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
	store.getProxy().extraParams.apellido = Ext.getCmp('txtApellido').value;
    store.getProxy().extraParams.login = Ext.getCmp('txtlogin').value;
    store.getProxy().extraParams.mes = Ext.getCmp('sltmes').value;	
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

function uploadPointsLine()
    {
        var arrayFile = '';
        var reader = new FileReader();
        var objFile = document.getElementById('infopuntotype_file');
        if (objFile != undefined && objFile != null)
        {//alert('2');
            arrayFile = objFile.files[0];
            reader.readAsText(objFile.files[0]);
            if(arrayFile != null && arrayFile != undefined)
            {//alert('3');
                var mimeType = arrayFile.type;
                if(mimeType.match(/csv\/*/) == null)
                {
                    Ext.Msg.alert('Error ','Error: Solo archivos .csv delimitado por ;') ; 	
                }
                else
                {
                    reader.onload = function(e){
                    var csv  = reader.result;
                        Ext.Msg.confirm('Alerta','Se ingresara masivamente los registros. Desea continuar?', function(btn){
                            if(btn=='yes'){
                                Ext.Ajax.request({
                                url: "masivoAjax",
                                method: 'POST',
                                timeout: 600000,
                                params: { param : csv},
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
                            var input = document.getElementById("infopuntotype_file");
                            input.value = '';
                         });
                    }
                }
            }
        }
    }
