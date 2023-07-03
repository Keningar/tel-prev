/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var winMateriales;

function showMateriales(rec)
{
        winMateriales="";

    if (!winMateriales)
    {
        var id_solicitud = rec.get("id_solicitud");
	var tipoSolicitud = rec.get("tipoSolicitud");
        
	
	if(tipoSolicitud=='SOLICITUD MATERIALES EXCEDENTES'){
	    storeMateriales = new Ext.data.Store({ 
		pageSize: 10,
		total: 'total',
		proxy: {
		    type: 'ajax',
		    url : '../../autorizaciones/materiales_excedentes/gridFactibilidadMateriales',
		    reader: {
			type: 'json',
			totalProperty: 'total',
			root: 'encontrados'
		    },
		    extraParams: {
			id_detalle_solicitud: id_solicitud,
			estado: 'Todos'
		    }
		},
		fields:
			[   
			    {name:'id_detalle_solicitud', mapping:'id_detalle_solicitud'},
			    {name:'id_detalle_sol_material', mapping:'id_detalle_sol_material'},
			    {name:'id_tarea', mapping:'id_tarea'},
			    {name:'id_tarea_material', mapping:'id_tarea_material'},
			    {name:'cod_material', mapping:'cod_material'},
			    {name:'nombre_material', mapping:'nombre_material'},
			    {name:'costo_material', mapping:'costo_material'},
			    {name:'precio_venta_material', mapping:'precio_venta_material'},
			    {name:'cantidad_empresa', mapping:'cantidad_empresa'},
			    {name:'cantidad_estimada', mapping:'cantidad_estimada'},
			    {name:'cantidad_cliente', mapping:'cantidad_cliente'}              
			],
		autoLoad: true
	    });
		    
	    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 2,
		listeners: {
		    edit: function(){
			// refresh summaries
			gridMateriales.getView().refresh();
		    }
		}
	    });
	
	    gridMateriales = Ext.create('Ext.grid.Panel', {
		width: 715,
		height: 300,
		store: storeMateriales,
		loadMask: true,
		frame: false,
		columns:[
		    {
			id: 'id_detalle_solicitud',
			header: 'IdDetalleSolicitud',
			dataIndex: 'id_detalle_solicitud',
			hidden: true,
			hideable: false
		    },
		    {
			id: 'id_detalle_sol_material',
			header: 'IdDetalleSolMaterial',
			dataIndex: 'id_detalle_sol_material',
			hidden: true,
			hideable: false
		    },
		    {
			id: 'id_tarea',
			header: 'IdTarea',
			dataIndex: 'id_tarea',
			hidden: true,
			hideable: false
		    }, 
		    {
			id: 'id_tarea_material',
			header: 'IdTareaMaterial',
			dataIndex: 'id_tarea_material',
			hidden: true,
			hideable: false
		    },             
		    {
			id: 'cod_material',
			header: 'Cod Material',
			dataIndex: 'cod_material',
			width: 100,
			sortable: true
		    },        
		    {
			id: 'nombre_material',
			header: 'Nombre Material',
			dataIndex: 'nombre_material',
			width: 250,
			sortable: true
		    },         
		    {
			id: 'cantidad_empresa',
			header: 'Cantidad (empresa)',
			dataIndex: 'cantidad_empresa',
			width: 120,
			align: 'right',
			sortable: true,
			tdCls: 'custom-azul'
		    },             
		    {                        
			id: 'cantidad_estimada',
			header: 'Cantidad (estimada)',
			dataIndex: 'cantidad_estimada',
			width: 130,
			align: 'right',
			sortable: true, 
			tdCls: 'custom-rojo'
		    },             
		    {
			id: 'cantidad_cliente',
			header: 'Cantidad (cliente)',
			dataIndex: 'cantidad_cliente',
			width: 110,
			align: 'right',
			sortable: true,
			tdCls: 'custom-rojo'
		    }
		],
		bbar: Ext.create('Ext.PagingToolbar', {
		    store: storeMateriales,
		    displayInfo: true,
		    displayMsg: 'Mostrando {0} - {1} de {2}',
		    emptyMsg: "No hay datos que mostrar."
		})
	    });
	
	    formPanelMateriales = Ext.create('Ext.form.Panel', {
		buttonAlign: 'center',
		BodyPadding: 10,
		frame: true,
		items: [  gridMateriales,
			  {
				  xtype: 'fieldset',
				  title: 'Datos Adicionales',
				  defaultType: 'textfield',
				  style: "font-weight:bold; margin-top: 15px;",
				  defaults: {
					  width: '350px'
				  },
				  items: [ {
					    xtype: 'textareafield',
					    fieldLabel: 'Observacion',
					    name: 'materiales_observacion',
					    id: 'materiales_observacion',
					    value: rec.get('observacion2'),
					    allowBlank: true,
					    readOnly : true
					    }
					  ]
			  }
		],
		buttons:[
		    {
			text: 'Cerrar',
			handler: function(){
			    cierraVentanaMateriales();
			}
		    }
		]
	    });
	    
	    winMateriales = Ext.widget('window', {
		title: 'Materiales Excedentes',
    //            width: 1060,
    //            height: 630,
    //            minHeight: 380,
		layout: 'fit',
		resizable: false,
		modal: true,
		closable: false,
		items: [formPanelMateriales]
	    });
	}
	
	if(tipoSolicitud=='SOLICITUD PLANIFICACION'){
	     storeMateriales = new Ext.data.Store({ 
		pageSize: 10,
		total: 'total',
		proxy: {
		    type: 'ajax',
		    url : 'gridMaterialesUtilizados',
		    reader: {
			type: 'json',
			totalProperty: 'total',
			root: 'encontrados'
		    },
		    extraParams: {
			id_solicitud: id_solicitud,
			estado: 'Todos'
		    }
		},
		fields:
			[   
			    {name:'cod_material', mapping:'cod_material'},
			    {name:'nombre_material', mapping:'nombre_material'},
			    {name:'subgrupo_material', mapping:'subgrupo_material'},
			    {name:'cantidad_facturada', mapping:'cantidad_facturada'},
			    {name:'cantidad_usada', mapping:'cantidad_usada'},
			    {name:'cantidad_estimada', mapping:'cantidad_estimada'},
			    {name:'cantidad_cliente', mapping:'cantidad_cliente'}              
			],
		autoLoad: true
	    });
	
	    gridMateriales = Ext.create('Ext.grid.Panel', {
// 		width: 830,
		height: 300,
		store: storeMateriales,
		loadMask: true,
		frame: false,
		columns:[
		    {
			id: 'cod_material',
			header: 'Cod Material',
			dataIndex: 'cod_material',
			width: 100,
			sortable: true
		    },        
		    {
			id: 'nombre_material',
			header: 'Nombre Material',
			dataIndex: 'nombre_material',
			width: 250,
			sortable: true
		    },/*
		    {                        
			id: 'cantidad_estimada',
			header: 'Cantidad (estimada)',
			dataIndex: 'cantidad_estimada',
			width: 130,
			align: 'right',
			sortable: true, 
			tdCls: 'custom-azul'
		    },*/
		    {
			id: 'cantidad_usada',
			header: 'Cantidad (Usada Real)',
			dataIndex: 'cantidad_usada',
			width: 121,
			align: 'right',
			sortable: true,
			tdCls: 'custom-rojo'
		    },            
		    {
			id: 'cantidad_cliente',
			header: 'Cantidad (cliente)',
			dataIndex: 'cantidad_cliente',
			width: 110,
			align: 'right',
			sortable: true,
			tdCls: 'custom-rojo'
		    },            
		    {
			id: 'cantidad_facturada',
			header: 'Cantidad (Facturada)',
			dataIndex: 'cantidad_facturada',
			width: 115,
			align: 'right',
			sortable: true,
			tdCls: 'custom-rojo'
		    }
		],
		bbar: Ext.create('Ext.PagingToolbar', {
		    store: storeMateriales,
		    displayInfo: true,
		    displayMsg: 'Mostrando {0} - {1} de {2}',
		    emptyMsg: "No hay datos que mostrar."
		})
	    });
	
	    formPanelMateriales = Ext.create('Ext.form.Panel', {
		buttonAlign: 'center',
		BodyPadding: 10,
		frame: true,
		items: [  gridMateriales  ],
		buttons:[
		    {
			text: 'Cerrar',
			handler: function(){
			    cierraVentanaMateriales();
			}
		    }
		]
	    });
	    
	    winMateriales = Ext.widget('window', {
		title: 'Materiales Utilizados',
    //            width: 1060,
    //            height: 630,
    //            minHeight: 380,
		layout: 'fit',
		resizable: false,
		modal: true,
		closable: false,
		items: [formPanelMateriales]
	    });
	  
	}
    }                        
                         
    winMateriales.show();    
}

function cierraVentanaMateriales(){
    winMateriales.close();
    winMateriales.destroy();
}

function showHistorial(rec){
    var id_solicitud = rec.get('id_solicitud');
    var storeHistorial = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : 'getJsonHistorialSolicitud',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idSolicitud: id_solicitud
            }
        },
        fields:
            [
              {name:'usrCreacion', mapping:'usrCreacion'},
              {name:'feCreacion', mapping:'feCreacion'},
              {name:'ipCreacion', mapping:'ipCreacion'},
              {name:'estado', mapping:'estado'},
              {name:'nombreMotivo', mapping:'nombreMotivo'},
              {name:'observacion', mapping:'observacion'}
            ]
    });
    
    Ext.define('HistorialServicio', {
        extend: 'Ext.data.Model',
        fields: [
              {name:'usrCreacion', mapping:'usrCreacion'},
              {name:'feCreacion', mapping:'feCreacion'},
              {name:'ipCreacion', mapping:'ipCreacion'},
              {name:'estado', mapping:'estado'},
              {name:'nombreMotivo', mapping:'nombreMotivo'},
              {name:'observacion', mapping:'observacion'}
        ]
    });
    
    //grid de usuarios
    gridHistorialServicio = Ext.create('Ext.grid.Panel', {
        id:'gridHistorialServicio',
        store: storeHistorial,
        columnLines: true,
        columns: [{
            //id: 'nombreDetalle',
            header: 'Usuario Creacion',
            dataIndex: 'usrCreacion',
            width: 100,
            sortable: true
        },{
            header: 'Fecha Creacion',
            dataIndex: 'feCreacion',
            width: 120
        },
        {
            header: 'Ip Creacion',
            dataIndex: 'ipCreacion',
            width: 100
        },
        {
            header: 'Estado',
            dataIndex: 'estado',
            width: 150
        },
        {
            header: 'Motivo',
            dataIndex: 'nombreMotivo',
            width: 225
        },
        {
            header: 'Observacion',
            dataIndex: 'observacion',
            width: 225
        }],
        viewConfig:{
            stripeRows:true
        },
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex,
                                        columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                    if (columnText)
                                    {
                                        tip.update(columnText);
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });
            }
        },
        frame: true,
        height: 200
        //title: 'Historial del Servicio'
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
        
        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
//                checkboxToggle: true,
//                collapsed: true,
            defaults: {
                width: 900
            },
            items: [

                gridHistorialServicio

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Historial de la Solicitud',
        modal: true,
        width: 950,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}