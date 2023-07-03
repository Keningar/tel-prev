Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
	var store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../../admi_tarea_material/grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idTarea: tareaId,
                estado: 'Todos'
            }
        },
        fields:
                    [            
                    {name:'id_tarea_material', mapping:'id_tarea_material'},
                    {name:'nombre_tarea', mapping:'nombre_tarea'},
                    {name:'nombre_material', mapping:'nombre_material'},
                    {name:'unidad', mapping:'unidad'},
                    {name:'costo', mapping:'costo'},
                    {name:'precio', mapping:'precio'},
                    {name:'cantidad', mapping:'cantidad'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'}               
                    ],
        autoLoad: true
    });
	
    var storeTareaInterfaceModeloTramoScript = new Ext.data.Store({ 
        total: 'total',
		pageSize: 3,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getDatosTareaInterfaceModeloTramo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id'},
                {name:'opcion', mapping:'opcion'},
                {name:'comboId', mapping:'comboId'},
                {name:'nombreCombo', mapping:'nombreCombo'},
                {name:'interfaceModeloId', mapping:'interfaceModeloId'},
                {name:'tipoInterfaceNombre', mapping:'tipoInterfaceNombre'},
                {name:'script', mapping:'script'}
              ]
    });
    
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridTareaInterfaceModeloTramo = Ext.create('Ext.grid.Panel', {
        id:'gridTareaInterfaceModeloTramo',
        store: storeTareaInterfaceModeloTramoScript,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'opcion',
            header: 'Opcion',
            dataIndex: 'opcion',
            width: 100,
            sortable: true
        },{
            id: 'idCombo',
            header: 'idCombo',
            dataIndex: 'idCombo',
            hidden: true,
            hideable: false
        }, {
            id: 'nombreCombo',
            header: 'Elemento/Tramo',
            dataIndex: 'nombreCombo',
            width: 220,
            sortable: true
        },{
            id: 'interfaceModeloId',
            header: 'interfaceModeloId',
            dataIndex: 'interfaceModeloId',
            hidden: true,
            hideable: false
        }, {
            id: 'tipoInterfaceNombre',
            header: 'Nombre Interface',
            dataIndex: 'tipoInterfaceNombre',
            width: 220,
            sortable: true
        }, {
            id: 'script',
            header: 'script',
            dataIndex: 'script',
            hidden: true,
            hideable: false
        },{
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 120,
            items: [{
                getClass: function(v, meta, rec) {return 'button-grid-show'},
                tooltip: 'Ver Script',
                handler: function(grid, rowIndex, colIndex) {
                            verScript(grid.getStore().getAt(rowIndex).data);
                        }
                }
            ]
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 850,
        height: 180,
        frame: true,
        title: 'Detalle Tareas',
        renderTo: 'grid'
    });
                  
    /**************************************************/
	    
    grid = Ext.create('Ext.grid.Panel', {
        width: 850,
        height: 250,
        store: store,
        loadMask: true,
        frame: false,
        columns:[
                {
                  id: 'id_tarea_material',
                  header: 'IdTareaMaterial',
                  dataIndex: 'id_tarea_material',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_material',
                  header: 'Nombre Material',
                  dataIndex: 'nombre_material',
                  width: 400,
                  sortable: true
                },
                {
                  id: 'unidad',
                  header: 'Unidad',
                  dataIndex: 'unidad',
                  width: 70,
                  sortable: true
                },
                {
                  id: 'precio',
                  header: 'Precio Venta',
                  dataIndex: 'precio',
                  width: 80,
                  sortable: true
                },
                {
                  id: 'cantidad',
                  header: 'Cantidad',
                  dataIndex: 'cantidad',
                  width: 65,
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
								var permiso = $("#ROLE_54-4");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if (rec.get('action2') == "icon-invisible") 
	                                this.items[0].tooltip = '';
	                            else 
	                                this.items[0].tooltip = 'Editar';
	                            
	                            return rec.get('action2')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);								
								
								var permiso = $("#ROLE_54-4");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
								
	                            if(rec.get('action2')!="icon-invisible")
	                                window.location = "../../admi_tarea_material/"+rec.get('id_tarea_material')+"/edit?tareaId="+tareaId;
								else
									Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
							}
                        },
                        {
	                        getClass: function(v, meta, rec) {
								var permiso = $("#ROLE_54-8");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permiso = $("#ROLE_54-9");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if (rec.get('action3') == "icon-invisible") 
	                                this.items[1].tooltip = '';
	                            else 
	                                this.items[1].tooltip = 'Eliminar';
	                            
	                            return rec.get('action3')
	                        },
	                        handler: function(grid, rowIndex, colIndex) {
	                            var rec = store.getAt(rowIndex);
								
								var permiso = $("#ROLE_54-8");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
								var permiso = $("#ROLE_54-9");
								var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
								if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
								
	                            if(rec.get('action3')!="icon-invisible")
								{
	                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
	                                    if(btn=='yes'){
	                                        Ext.Ajax.request({
	                                            url: "../../admi_tarea_material/deleteAjax",
	                                            method: 'post',
	                                            params: { param : rec.get('id_tarea_material')},
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
		 viewConfig:{
            stripeRows:true
        },
        width: 850,
        height: 425,
        frame: true,
        title: 'Detalle Materiales Tarea',
        renderTo: 'gridMateriales'
    });
       
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    // var filterPanel = Ext.create('Ext.panel.Panel', {
        // bodyPadding: 7,  // Don't want content to crunch against the borders
        // bodyBorder: false,
        // border:false,
        // border: '1,1,0,1',
        // buttonAlign: 'center',
        // layout: {
            // type: 'hbox',
            // align: 'stretch'
        // },
        // bodyStyle: {
                    // background: '#fff'
                // },                     

        // collapsible : true,
        // collapsed: true,
        // width: 850,
        // title: 'Criterios de busqueda',
            // buttons: [
                // {
                    // text: 'Buscar',
                    // iconCls: "icon_search",
                    // handler: function(){ buscar();}
                // },
                // {
                    // text: 'Limpiar',
                    // iconCls: "icon_limpiar",
                    // handler: function(){ limpiar();}
                // }

                // ],                
                // items: [
                        // { width: '5%',border:false},
                        // {
                            // xtype: 'textfield',
                            // id: 'txtNombre',
                            // fieldLabel: 'Nombre',
                            // value: '',
                            // width: '250'
                        // },
                        // { width: '15%',border:false},
                        // ,{
                            // xtype: 'combobox',
                            // fieldLabel: 'Estado',
                            // id: 'sltEstado',
                            // value:'Todos',
                            // store: [
                                // ['Todos','Todos'],
                                // ['ACTIVO','Activo'],
                                // ['MODIFICADO','Modificado'],
                                // ['ELIMINADO','Eliminado']
                            // ],
                            // width: '200'
                        // }
                        // ],	
        // renderTo: 'filtro'
    // }); 
    
});

function verScript(data){

if(data){
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [{
            xtype: 'fieldset',
            title: 'Ver Scripting',
            defaultType: 'textfield',
            defaults: {
                width: 650
            },
            items: [
                
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                    xtype: 'textareafield',
                    id:'scripting',
                    name: 'scripting',
                    fieldLabel: 'Script',
                    value: data.script,
                    cols: 80,
                    rows: 10,
                    anchor: '100%',
                    readOnly:true
                    }]
                },

            ]
        }],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Ver Detalle de Scripting',
        modal: true,
        width: 730,
        closable: false,
        layout: 'fit',
        items: [formPanel]
    }).show();
}            

}