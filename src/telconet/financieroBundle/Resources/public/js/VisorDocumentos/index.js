/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.QuickTips.init();
Ext.onReady(function() {        
    
	store = new Ext.data.Store({ 
		pageSize: 15,
		total: 'total',
		proxy: {
			type: 'ajax',
			url : '/soporte/gestion_documentos/grid',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {				
				estado: 'Todos',
				modulo: 'FINANCIERO'
			}
		},
		fields:
		[
			{name:'id', mapping:'id'},
			{name:'nombre', mapping:'nombre'},			
			{name:'estado', mapping:'estado'},
			{name:'extension', mapping:'extension'},
			{name:'tipoDocumento', mapping:'tipoDocumento'},
			{name:'punto', mapping:'punto'},
			{name:'documentoFinan', mapping:'documentoFinan'},		
			{name:'ubicacionLogica', mapping:'ubicacionLogica'},
			{name:'ubicacionFisica', mapping:'ubicacionFisica'},					
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'},
			{name:'action4', mapping:'action4'}	
		],
		autoLoad: true
	});
											 
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1000,
        height: 550,
        store: store,
        loadMask: true,
	renderTo:grid,
        frame: false,
	viewConfig: { enableTextSelection: true },
        selModel: sm,
        dockedItems: 
		[ 
			{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: 
		[                    
                    { xtype: 'tbfill' },                   
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        itemId: 'delete',
                        scope: this,
                        handler: function(){ eliminarAlgunos();}
                    }
                ]
			}
        ],                  
        columns:[
			{
			      id: 'id',
			      header: 'id',
			      dataIndex: 'id',
			      hidden: true,
			      hideable: false
			},
			{
			      id: 'nombre',
			      header: 'Nombre Documento',
			      dataIndex: 'nombre',
			      width: 200,
			      sortable: true			
			},
			{
			      id: 'documentoFinan',
			      header: 'Numero Documento',
			      dataIndex: 'documentoFinan',
			      width: 120,
			      sortable: true
			},			
			{
			      id: 'tipoDocumento',
			      header: 'Tipo Documento',
			      dataIndex: 'tipoDocumento',
			      width: 100,
			      sortable: true			  
			},
		        {
			      id: 'extension',
			      header: 'Extension',
			      dataIndex: 'extension',
			      width: 70,
			      sortable: true
			},			
		        {
			      id: 'ubicacionLogica',
			      header: 'Archivo',
			      dataIndex: 'ubicacionLogica',
			      width: 170,
			      sortable: true
			},
		        {
			      id: 'punto',
			      header: 'Login',
			      dataIndex: 'punto',
			      width: 140,
			      sortable: true
			},
			{
			      id: 'estado',
			      header: 'Estado',
			      dataIndex: 'estado',
			      width: 80,
			      sortable: true
			},
			{
			      xtype: 'actioncolumn',
			      header: 'Acciones',
			      width: 100,
			      items: 
			      [
				      {
					    getClass: function(v, meta, rec) 
					    {
									    
						    if (rec.get('action1') == "icon-invisible") 
							this.items[0].tooltip = '';
						    else 
							this.items[0].tooltip = 'Ver Documento';										    
						    
						    return rec.get('action1')
					    },
					    tooltip: 'Ver',
					    handler: function(grid, rowIndex, colIndex) 
					    {
						    var rec = store.getAt(rowIndex);									    
																	    
						    if(rec.get('action1')!="icon-invisible")
							    window.location = "/soporte/gestion_documentos/"+rec.get('id')+"/financiero/show";
						    else
							    Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
										    
					    }
				      },
				      {
					     getClass: function(v, meta, rec) 
					     {
									    
						    if (rec.get('action4') == "icon-invisible") 
							this.items[1].tooltip = '';
						    else 
							this.items[1].tooltip = 'Descargar';
						    
						    return rec.get('action4')
					    },
					    handler: function(grid, rowIndex, colIndex) 
					    {
						    var rec = store.getAt(rowIndex);
								
						    if(rec.get('action4')!="icon-invisible")
							window.location = "/soporte/gestion_documentos/"+rec.get('id')+"/descargarDocumento";
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
		listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj )
			    {
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
			    viewready: function (grid) 
		            {
				  var view = grid.view;

				  // record the current cellIndex
				  grid.mon(view, {
				      uievent: function (type, view, cell, recordIndex, cellIndex, e) {
					  grid.cellIndex = cellIndex;
					  grid.recordIndex = recordIndex;
				      }
				  });

				  grid.tip = Ext.create('Ext.tip.ToolTip', 
				  {
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
    //****************************************************************
    //                    Combos para Filtros de Busqueda
    //****************************************************************
    
    //-------------- CLIENTES -------------------
    
    storeClientes = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '/soporte/tareas/getClientes',
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
			{name:'id_cliente', mapping:'id_cliente'},
			{name:'cliente', mapping:'cliente'}
		],
		autoLoad: false
    });
    
    comboCliente = new Ext.form.ComboBox({
        id: 'cmb_cliente',
        name: 'cmb_cliente',        	        
	fieldLabel: 'Login',
        store: storeClientes,
        displayField: 'cliente',
        valueField: 'id_cliente',
        height:30,
	width: 400,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
      
    //-------------- TIPO DOCUMENTO GENERAL -------------------
    
    storeTipoDocumentoGeneral = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '/soporte/gestion_documentos/getTipoDocumentoGeneral',
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
			{name:'idTipo', mapping:'idTipo'},
			{name:'descripcionTipoDocumento', mapping:'descripcionTipoDocumento'}
		],
		autoLoad: false
    });
    
    comboTipoDocumentoGeneral = new Ext.form.ComboBox({
        id: 'cmb_tipoDocumentoGeneral',
        name: 'cmb_tipoDocumentoGeneral',        	
        fieldLabel: 'Tipo Documento',
        store: storeTipoDocumentoGeneral,
        displayField: 'descripcionTipoDocumento',
        valueField: 'idTipo',
        height:30,
	width: 400,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
    
     //-------------- TIPO DOCUMENTO ( EXTENSION ) -------------------
    
    storeTipoDocumento = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '/soporte/gestion_documentos/getTipoDocumento',
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
			{name:'idTipo', mapping:'idTipo'},
			{name:'extensionTipoDocumento', mapping:'extensionTipoDocumento'}
		],
		autoLoad: false
    });
    
    comboTipoDocumento = new Ext.form.ComboBox({
        id: 'cmb_tipoDocumento',
        name: 'cmb_tipoDocumento',        	
        fieldLabel: 'Extension',
        store: storeTipoDocumento,
        displayField: 'extensionTipoDocumento',
        valueField: 'idTipo',
        height:30,
	width: 400,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
    
        
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders        
        border:false,        
        buttonAlign: 'center',
        layout: {
            type:'table',
            columns: 5
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: false,
        width: 1000,
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
			{html:"&nbsp;",border:false,width:50},		
			{
				xtype: 'textfield',
				id: 'txt_nombre',
				name: 'txt_nombre',
				fieldLabel: 'Nombre',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:80},
			comboTipoDocumentoGeneral,
			{html:"&nbsp;",border:false,width:50},
			
			//----------------------------------//
			
			{html:"&nbsp;",border:false,width:50},
			comboTipoDocumento,
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'textfield',
				id: 'txt_contrato',
				name: 'txt_contrato',
				fieldLabel: 'Numero Documento',
				value: '',
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},
			
			//----------------------------------//
								
			{html:"&nbsp;",border:false,width:50},
			comboCliente,
			{html:"&nbsp;",border:false,width:80},
			{
				xtype: 'combobox',
				fieldLabel: 'Estado',
				id: 'cmb_estado',
				value:'Todos',
				store: [
					['Todos','Todos'],
					['Activo','Activo'],
					['Modificado','Modificado'],
					['Eliminado','Eliminado']									 
				],
				width: 400
			},
			{html:"&nbsp;",border:false,width:50},	
			
			//----------------------------------//
			
			
		],	
        renderTo: 'filtro'
    }); 
    
});

function buscar()
{                                 				
    store.proxy.extraParams = {
                            nombre       : Ext.getCmp('txt_nombre').value ? Ext.getCmp('txt_nombre').value : '',
                            numeroDocumento    : Ext.getCmp('txt_contrato').value ? Ext.getCmp('txt_contrato').value : '',
                            tipoDocumento: Ext.getCmp('cmb_tipoDocumentoGeneral').value ? Ext.getCmp('cmb_tipoDocumentoGeneral').value : '',
                            extensionDoc : Ext.getCmp('cmb_tipoDocumento').value ? Ext.getCmp('cmb_tipoDocumento').value : '',
			    modulo       : 'FINANCIERO',                          
                            login        : Ext.getCmp('cmb_cliente').value ? Ext.getCmp('cmb_cliente').value : '',                          
                            estado       : Ext.getCmp('cmb_estado').value ? Ext.getCmp('cmb_estado').value : '',                          
                        };	
    store.load();
}

function limpiar()
{
  
    Ext.getCmp('txt_nombre').value = "";
    Ext.getCmp('txt_nombre').setRawValue("");
    Ext.getCmp('txt_contrato').value = "";
    Ext.getCmp('txt_contrato').setRawValue("");
    Ext.getCmp('cmb_tipoDocumentoGeneral').value= "";
    Ext.getCmp('cmb_tipoDocumentoGeneral').setRawValue("");
    Ext.getCmp('cmb_tipoDocumento').valu= "";
    Ext.getCmp('cmb_tipoDocumento').setRawValue(""); 
    Ext.getCmp('cmb_cliente').value= "";
    Ext.getCmp('cmb_cliente').setRawValue("");
    Ext.getCmp('cmb_estado').value= "Todos";
    Ext.getCmp('cmb_estado').setRawValue("Todos");
			
    store.proxy.extraParams = { estado: 'Todos' , modulo: 'FINANCIERO'};
    store.load();
}

function eliminarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
      var estado = 0;
      for(var i=0 ;  i < sm.getSelection().length ; ++i)
      {
        param = param + sm.getSelection()[i].data.id;
        
        if(sm.getSelection()[i].data.estado == 'Eliminado')
        {
          estado = estado + 1;
        }
        if(i < (sm.getSelection().length -1))
        {
          param = param + '|';
        }
      }      
      if(estado == 0)
      {
        Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: "/soporte/gestion_documentos/deleteAjax",
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
	    Ext.Msg.alert("Alerta","Por lo menos uno de las registro se encuentra en estado ELIMINADO");        
      }
    }
    else
    {
	  Ext.Msg.alert("Alerta","Seleccione por lo menos un registro de la lista.");              
    }
}

