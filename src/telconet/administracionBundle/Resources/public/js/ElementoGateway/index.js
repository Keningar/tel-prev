Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var estaEnVentana = false;
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_elemento', mapping:'id_elemento'},
			{name:'nombre_marca_elemento', mapping:'nombre_marca_elemento'},
			{name:'nombre_modelo_elemento', mapping:'nombre_modelo_elemento'},
			{name:'nombre_elemento', mapping:'nombre_elemento'},
			{name:'ip', mapping:'ip'},
			{name:'nombre_usuario_acceso', mapping:'nombre_usuario_acceso'},
			{name:'contrasena', mapping:'contrasena'},			
			{name:'estado', mapping:'estado'},
			{name:'empresa', mapping:'empresa'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'},
			{name:'action4', mapping:'action4'}  
		],
        idProperty: 'id_elemento'
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
                estado: 'Todos',
		marca: 'Todos',
		empresa:'Todos'
            }
        },
        autoLoad: true
    });

    var pluginExpanded = true;    
	
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
    //Permiso para acceder a icono de consulta de Saldos
	var permisoConsultarSaldos = $("#ROLE_239-2017");       
	var boolPermisoConsultarSaldos = (typeof permisoConsultarSaldos === 'undefined') ? false : (permisoConsultarSaldos.val()==1 ? true : false);
        	
	var consultarSaldoBoton = "";
    
	sm = Ext.create('Ext.selection.CheckboxModel', {
	        checkOnly: true
	});
    
	if(boolPermisoConsultarSaldos)
	{	    	
		consultarSaldoBoton = Ext.create('Ext.button.Button', {
			iconCls: 'icon_solicitud',
            text: 'Saldos',
            itemId: 'saldos',
            scope: this,
            handler: function() {
                verSaldos();
            }
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
            {
				iconCls: 'icon_exportar',
				text: 'Exportar Equipos',
				itemId: 'exportar',
				scope: this,
				handler: function(){ 
					exportar();
				}
			},            
            consultarSaldoBoton,
			{ xtype: 'tbfill' },
			{
				iconCls: 'icon_delete',
                text: 'Eliminar',
                itemId: 'deleteAjax',                
                scope   : this,
                handler: function(){ eliminarAlgunos();}
			}
		]
	});

    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        width: 920,
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
                  id: 'id_elemento',
                  header: 'IdElemento',
                  dataIndex: 'id_elemento',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_marca_elemento',
                  header: 'Marca Elemento',
                  dataIndex: 'nombre_marca_elemento',
                  width: 100,
                  sortable: true
                },
		{
                  id: 'nombre_modelo_elemento',
                  header: 'Modelo Elemento',
                  dataIndex: 'nombre_modelo_elemento',
                  width: 100,
                  sortable: true
                },
		{
                  id: 'nombre_elemento',
                  header: 'Nombre Elemento',
                  dataIndex: 'nombre_elemento',
                  width: 110,
                  sortable: true
                },		
		{
                  id: 'ip',
                  header: 'IP Elemento',
                  dataIndex: 'ip',
                  width: 90,
                  sortable: true
                },
		{
                  id: 'nombre_usuario_acceso',
                  header: 'Usuario Acceso',
                  dataIndex: 'nombre_usuario_acceso',
                  width: 100,
                  sortable: true
                },
		{
                  id: 'contrasena',
                  header: 'Password Acceso',
                  dataIndex: 'contrasena',
                  width: 100,
                  sortable: true
                },
		{
                  id: 'empresa',
                  header: 'Empresa',
                  dataIndex: 'empresa',
                  width: 100,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 70,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 180,
                    items: [
			{
	                        getClass: function(v, meta, rec) {
				    var permiso = '{{ is_granted("ROLE_239-6") }}';
				   
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
								
				    var permiso = '{{ is_granted("ROLE_239-6") }}';
				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
				    if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
				 								
	                            if(rec.get('action1')!="icon-invisible")
					    window.location = rec.get('id_elemento')+"/show";
				    else
					    Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							    
				}
		        },
				{
	                        getClass: function(v, meta, rec) {
				  
				    var permiso = '{{ is_granted("ROLE_239-4") }}';
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
								
				    var permiso = '{{ is_granted("ROLE_239-4") }}';
				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
				    if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if(rec.get('action2')!="icon-invisible")
	                                window.location = rec.get('id_elemento')+"/edit";
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
			{
	                        getClass: function(v, meta, rec) {				  				   
				    
				    var permiso = '{{ is_granted("ROLE_239-5") }}';
				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);								
				    if(!boolPermiso){ rec.data.action4 = "icon-invisible"; }
				    				    
	                            if (rec.get('action4') == "icon-invisible") 
	                                this.items[2].tooltip = '';
	                            else 
	                                this.items[2].tooltip = 'Activar/Inactivar';
	                            
	                            return rec.get('action4')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
// 	                             var rec = store.getAt(rowIndex);
// 								
// 				    var permiso = '{{ is_granted("ROLE_239-5") }}';
// 				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
// 				    if(!boolPermiso){ rec.data.action4 = "icon-invisible"; }
// 								
// 	                            if(rec.get('action4')!="icon-invisible")
// 	                                window.location = rec.get('id_elemento')+"/habilitar";
// 				    else
// 				        Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');    
// 				    
				   
				     var rec = store.getAt(rowIndex);
								
				    var permiso = '{{ is_granted("ROLE_239-5") }}';
				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);								
				    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }				    
								
	                            if(rec.get('action3')!="icon-invisible")
				    {	                                
	                                        Ext.Ajax.request({
	                                            url: "habilitar",
	                                            method: 'post',
	                                            params: { 
							param : rec.get('id_elemento')						      
						    },
	                                            success: function(response){
	                                                var text = response.responseText;							
	                                                store.load();
							Ext.Msg.alert('Aviso ','Gateway '+text);
	                                            },
	                                            failure: function(result)
	                                            {
	                                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
	                                            }
	                                        });	                                    	                                 
				      }
				      else
					      Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');                              
								
								
								
				}
                       },
                        {
	                        getClass: function(v, meta, rec) {
				  
				    var permiso = '{{ is_granted("ROLE_239-8") }}';
				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
				    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
				    
								
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[3].tooltip = '';
	                            else 
	                                this.items[3].tooltip = 'Eliminar';
	                            
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
				    var permiso = '{{ is_granted("ROLE_239-8") }}';
				    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);								
				    if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }				    
								
	                            if(rec.get('action3')!="icon-invisible")
								{
	                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
	                                    if(btn=='yes'){
	                                        Ext.Ajax.request({
	                                            url: "deleteAjax",
	                                            method: 'post',
	                                            params: { param : rec.get('id_elemento')},
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
    
    storeElementosMarca = new Ext.data.Store({ 
	    total: 'total',
	    pageSize: 10000,
	    proxy: {
		    type: 'ajax',
		    url : 'getMarcaElemento',
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
		    {name:'id_marca', mapping:'id_marca'},
		    {name:'nombre_marca_elemento', mapping:'nombre_marca_elemento'}
	    ]
    });
	
    cmb_marca = new Ext.form.ComboBox({
        id: 'cmb_marca',
        name: 'cmb_marca',
        fieldLabel: "Marca Elemento:",
        emptyText: 'Seleccione Marca',
        store: storeElementosMarca,
        displayField: 'nombre_marca_elemento',
        valueField: 'id_marca',
        //height:30,
	width: 300,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
    
    /**************************************************************************/
    
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
        fieldLabel: "Empresas Asignadas:",
        emptyText: 'Seleccione Empresa',
        store: storeEmpresas,
        displayField: 'nombre_empresa',
        valueField: 'id_empresa',
        //height:30,
	width: 300,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
       
    /**************************************************************************/
            
    
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type:'table',
            columns: 5
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 920,
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
                                ['INACTIVO','Inactivo'],
                                ['ELIMINADO','Eliminado']
                            ],
                            width: '300'
                        },
			{html:"&nbsp;",border:false,width:80},			
			
			{html:"&nbsp;",border:false,width:50},
			cmb_marca,
			{html:"&nbsp;",border:false,width:80},
			cmb_empresas
              ],	
        renderTo: 'filtro'
    });            
    
    estaEnVentana = false;
    
});

function ejecutarCadaTiempo()
{
    if(estaEnVentana)
        buscarSaldos();
}

/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
     
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.getProxy().extraParams.marca  = Ext.getCmp('cmb_marca').value;
    store.getProxy().extraParams.empresa  = Ext.getCmp('cmb_empresas').value;
    store.load();
}

function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    Ext.getCmp('cmb_marca').value="Todos";
    Ext.getCmp('cmb_marca').setRawValue("Todos");
    Ext.getCmp('cmb_empresas').setRawValue("Todos");
    
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.getProxy().extraParams.marca  = Ext.getCmp('cmb_marca').value;
    store.getProxy().extraParams.empresa  = Ext.getCmp('cmb_empresas').value;
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

function exportar()
{
    $('#nombre_hidden').val(Ext.getCmp('txtNombre').value);
    $('#marca_hidden').val(Ext.getCmp('cmb_marca').value);
    $('#estado_hidden').val(Ext.getCmp('sltEstado').value);
    $('#empresa_hidden').val(Ext.getCmp('cmb_empresas').value);        

    document.forms[0].submit();
}

function buscarSaldos()
{
    storeSaldos.getProxy().extraParams.ip = Ext.getCmp('txtIpElemento').value;  
    storeSaldos.load();
}

function limpiarSaldos(){
    Ext.getCmp('txtIpElemento').value="Todos";
    Ext.getCmp('txtIpElemento').setRawValue("Todos");
   
    storeSaldos.getProxy().extraParams.ip  = Ext.getCmp('txtIpElemento').value;
    storeSaldos.load();
}

function verSaldos()
{        
    estaEnVentana = true;
    
    btncancelar = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {
            estaEnVentana=false;
			winVerSaldos.destroy();
		}
    });
    
     Ext.define('ModelStoreSaldos', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_elemento', mapping:'id_elemento'},
			{name:'ip', mapping:'ip'},
			{name:'puerto', mapping:'puerto'},
			{name:'operadora', mapping:'operadora'},
            {name:'numero', mapping:'numero'},
			{name:'saldo', mapping:'saldo'},			
			{name:'action1', mapping:'action1'}			
		],
        idProperty: 'id_elemento'
    });
	
    storeSaldos = new Ext.data.Store({ 
        pageSize: 16,
        model: 'ModelStoreSaldos',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : 'gridSaldos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                ip: 'Todos'                
            }
        },
        autoLoad: true
    });      
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    }); 
    
    /***** PERMISOS PARA GENERAR Y ENVIAR REPORTE DE SALDOS GENERADOS ******/
    var permisoGenerarReporteSaldos = $("#ROLE_239-2018");
	var boolPermisoGenerarReporteSaldos = (typeof permisoGenerarReporteSaldos === 'undefined') ? false : 
                                           (permisoGenerarReporteSaldos.val()==1 ? true : false);					
	
	var exportarReporteSaldos = "";
    var enviarReporteSaldos = "";
    
	if(boolPermisoGenerarReporteSaldos)
	{	    	
		exportarReporteSaldos = Ext.create('Ext.button.Button', {
            itemId: 'exportarSaldoButton',
            text: 'Exportar',
            tooltip: 'Exportar Saldos',
            iconCls: 'icon_exportar',
            disabled: false,
            handler: function() {
                exportarSaldos();
            }
        });
        enviarReporteSaldos = Ext.create('Ext.button.Button', {
            itemId: 'enviarReporte',
            text: 'Enviar Reporte Saldos',
            tooltip: 'Enviar Reporte Saldos',
            iconCls: 'icon_solicitud',
            disabled: false,
            handler: function() {
                gestionEnvioReporte();
            }
        });
	}
    
    var toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [
                {
                    itemId: 'consultarSaldosTotal',
                    text: 'Obtener Saldos',
                    tooltip: 'Obtener Saldos de todos los equipos',
                    iconCls: 'icon_saldos',
                    disabled: false,
                    handler: function() {
                        consultarSaldos('');
                    }
                },
                enviarReporteSaldos,
                exportarReporteSaldos
            ]
    });
    
    //Grid que trae los saldos consultados para cada chip por puerto
    gridSaldos = Ext.create('Ext.grid.Panel', {
        id: 'gridSaldos',
        width: 1000,
        height: 470,
        store: storeSaldos,               
        plugins: [cellEditing,{ptype : 'pagingselectpersist'}],
        columns: [
            {
                id: 'id_elemento',                
                dataIndex: 'id_elemento',
                header: 'IdElemento',
                hidden: true,
                hideable: true
            },
            {
                id: 'ip',
                header: 'IP',
                dataIndex: 'ip',
                width: 100,
                sortable: false
            },
             {
                id: 'puerto',
                header: 'Puerto',
                dataIndex: 'puerto',
                width: 50,
                sortable: false
            },
             {
                id: 'numero',
                header: 'Numero',
                dataIndex: 'numero',
                width: 80,
                sortable: false
            },
             {
                id: 'operadora',
                header: 'Operadora',
                dataIndex: 'operadora',
                width: 80,
                sortable: false
            },
             {
                id: 'saldo',
                header: 'Saldo',
                dataIndex: 'saldo',
                width: 580,
                sortable: false
            } , 
            {
			      id: 'procesando',
			      header: '',
			      dataIndex: 'procesando',
			      width: 20,
                  renderer: renderAccionEjecutando			      
			},
             {
                xtype: 'actioncolumn',
                header: 'Acciones',
                sortable: false,
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec) {                            
                                                       
                            this.items[0].tooltip = 'Obtener Saldo Inmediato de equipo '+rec.get('ip');

                            return rec.get('action1');
                        },
                        tooltip: 'Obtener Saldo Inmediato de equipo',
                        handler: function(grid, rowIndex, colIndex) {

                            var rec = storeSaldos.getAt(rowIndex);      
                                                        
                            consultarSaldos(rec.data.id_elemento);                                                     
                        }
                    }
                ]
            }  
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeSaldos,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        dockedItems: [ toolbar ]
    });
    
    var FiltroPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,         
        border: false,        
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 1000,
        title: 'Filtros',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscarSaldos();
                }
            }
        ],
        items: [                      
            {width: '30%', border: false},
            {                
                xtype: 'textfield',
                id: 'txtIpElemento',
                fieldLabel: 'IP Gateway',
                value: '',
                width: '250'
            }          
        ]
    }); 
    
    formPanel = Ext.create('Ext.form.Panel', {
        width: 1000,
        height: 500,
        BodyPadding: 10,
        layout: {
            type: 'table',
            columns: 1,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        items: [                  
            FiltroPanel,
            gridSaldos
        ]

    });
	
    winVerSaldos = Ext.create('Ext.window.Window', {
		title: 'Saldos de Chips',
		modal: true,
		width: 1020,
		height: 620,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btncancelar]
    }).show(); 
    
    setInterval('ejecutarCadaTiempo()',30000);
}
function enviarReporteSaldos()
{    
    var correoClaro = $("#correoClaro").val();
    var correoMovi  = $("#correoMovistar").val();
    
    var esOk = true;    
    var tipo;
    
    if($("#claro").is(':checked'))
    {       
        tipo = "CLARO";
        if(correoClaro==="")esOk=false;   
    }
    else if($("#movistar").is(':checked'))
    {   
        tipo = "MOVISTAR";
        if(correoMovi==="")esOk=false;   
    }
    else if($("#todos").is(':checked'))
    {
        tipo = "Todos";
        if(correoClaro==="" || correoMovi==="")esOk=false;        
    }
    
    if(esOk)
    {
        Ext.MessageBox.wait("Generando y enviando reporte de Saldos");

        Ext.Ajax.request({
            url: 'enviarReporteSaldos',
            method: 'post',
            params: {
                correoClaro: correoClaro,
                correoMovistar: correoMovi,
                tipo:tipo,
                elementoId: Ext.getCmp('txtIpElemento').value
            },
            success: function(response) {
                var json = Ext.JSON.decode(response.responseText);
                Ext.Msg.alert('Mensaje', json.mensaje);
            }
        });  
    }
    else
    {
        Ext.Msg.alert('Alerta', "Debe ingresar al menos un correo a enviar");
    }
            
}

function exportarSaldos()
{
    $('#ip_hidden').val(Ext.getCmp('txtIpElemento').value);           

    document.forms[1].submit();
}

function consultarSaldos(idElemento)
{
    Ext.MessageBox.wait("Cargando...");
    
    Ext.Ajax.request({
        url: 'getSaldos',
        method: 'post',
        params: {
            elementoId: idElemento
        },
        success: function(response) {
            var json = Ext.JSON.decode(response.responseText);
            Ext.Msg.alert('Mensaje', json.mensaje, function(btn) 
            {
                if (btn === 'ok')
                {
                    storeSaldos.proxy.extraParams = {ip: 'Todos'};
                    storeSaldos.load();
                }
            });
        }
    });    
}

function gestionEnvioReporte()
{            
    
    btncancelar = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {         
			winGestionEnvioReporte.destroy();
		}
    });
    
    btnEnviarReporte = Ext.create('Ext.Button', {
		text: 'Enviar Reporte',
		cls: 'x-btn-left',
		handler: function() 
        {         
			enviarReporteSaldos();
            winGestionEnvioReporte.destroy();
		}
    });
    
    espacioVacio =  Ext.create('Ext.Component', {
        html: '',
        width: 10,
        padding: 5,
        layout: 'anchor',
        style: { color: '#000000' }
    }); 
    
    var iniHtml=   
        '<table>'+
          '<tr>'+
            '<td colspan="2"><b>Se envia reporte a las personas encargadas de cada Operadora</b></td>'+      
          '</tr>'+
          '<tr>'+
            '<td>&nbsp;</td>'+   
          '</tr>'+
          '<tr>'+
              '<td colspan="3">'+
                '<input id="claro" type="radio" checked="" value="claro" name="operadora" onclick="checkBoxAction(this)">&nbsp;CLARO&nbsp;'+   
                '&nbsp;&nbsp;&nbsp;\n\
                 <input id="movistar" type="radio" checked="" value="movistar" name="operadora" onclick="checkBoxAction(this)">&nbsp;MOVISTAR&nbsp;'+   
                '&nbsp;&nbsp;&nbsp;\n\
                 <input id="todos" type="radio" checked="" value="todos" name="operadora" onclick="checkBoxAction(this)" >&nbsp;TODOS&nbsp;'+
              '</td>'+   
          '</tr>'+
          '<tr>'+
            '<td>&nbsp;</td>'+   
          '</tr>'+
          '<tr>'+
            '<td>CHIPS CLARO NOTIFICAR A:</td>'+
            '<td><input type="text" name="correoClaro" id="correoClaro" value=""/></td>'+
          '</tr>'+
          '<tr>'+
            '<td>&nbsp;</td>'+   
          '</tr>'+
          '<tr>'+
            '<td>CHIPS MOVISTAR NOTIFICAR A:</td>'+
            '<td><input type="text" name="correoMovistar" id="correoMovistar" value=""/></td>'+
          '</tr>'+
       '</table>';
      
    html =  Ext.create('Ext.Component', {
            html: iniHtml,
            width: 500,
            padding: 5,
            style: { color: '#000000' }
        });	

    formPanel = Ext.create('Ext.form.Panel', {
        width: 400,
        height: 250,
        BodyPadding: 10,
        layout: {
            type: 'table',
            columns: 1,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        items: [        
            espacioVacio,html        
        ]

    });
	
    winGestionEnvioReporte = Ext.create('Ext.window.Window', {
		title: 'Gestion Envio Reporte',
		modal: true,
		width: 400,
		height: 250,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btnEnviarReporte,btncancelar]
    }).show();         
        
}

function renderAccionEjecutando(value, p, record) 
{
    var iconos='';    
    if(record.data.id_elemento === '' && record.data.saldo==='Consultando...' )
    {
        iconos=iconos+iconoEjecutando;                    
    }
    return Ext.String.format(iconos,value);
}

function checkBoxAction(el)
{    
    switch(el.id)
    {
        case 'claro':           
            $("#correoMovistar").attr('disabled','disabled');   
            $("#correoClaro").removeAttr('disabled');
            break;
        case 'movistar':
            $("#correoClaro").attr('disabled','disabled');   
            $("#correoMovistar").removeAttr('disabled');
            break;
        default:
            $("#correoMovistar").removeAttr('disabled');  
            $("#correoClaro").removeAttr('disabled');
            break;            
    }
}
