/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {   
    Ext.tip.QuickTipManager.init();
    Ext.require([
        'Ext.ux.CheckColumn'
    ]);
    
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesdeAsig = new Ext.form.DateField({
        id: 'fechaDesdeAsig',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaAsig = new Ext.form.DateField({
        id: 'fechaHastaAsig',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });	
    
    store = new Ext.data.Store({ 
        pageSize: 14,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Todos'
            }
        },
        fields:
                [
                    {name:'id_factibilidad', mapping:'id_factibilidad'},
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'id_punto', mapping:'id_punto'},
                    {name:'id_orden_trabajo', mapping:'id_orden_trabajo'},
                    {name:'num_orden_trabajo', mapping:'num_orden_trabajo'},
                    {name:'cliente', mapping:'cliente'},
		    {name:'tercerizadora', mapping:'tercerizadora'},
		    {name:'esRecontratacion', mapping:'esRecontratacion'},
                    {name:'vendedor', mapping:'vendedor'},
                    {name:'login2', mapping:'login2'},
                    {name:'producto', mapping:'producto'},
                    {name:'coordenadas', mapping:'coordenadas'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'ciudad', mapping:'ciudad'},
                    {name:'tipo_orden', mapping:'tipo_orden'},
                    {name:'nombreSector', mapping:'nombreSector'},
                    {name:'fechaAsignadaReal', mapping:'fechaAsignadaReal'},
                    {name:'rutaCroquis', mapping:'rutaCroquis'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'longitud', mapping:'longitud'},
					{name:'responsable', mapping:'responsable'}, 
					{name:'url_responsable', mapping:'url_responsable'}, 
					{name:'fieldIdResponsable', mapping:'fieldIdResponsable'}, 
					{name:'fieldValueResponsable', mapping:'fieldValueResponsable'}, 
					{name:'observacion_excedente', mapping:'observacion_excedente'}, 
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'}              
                ],
//         autoLoad: true
    });

    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1230,
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    {xtype: 'tbfill'}
                ]}
        ], 
        columns:[
                {
                  id: 'id_factibilidad',
                  header: 'IdFactibilidad',
                  dataIndex: 'id_factibilidad',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_servicio',
                  header: 'IdServicio',
                  dataIndex: 'id_servicio',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'tipo_orden',
                  header: 'tipo_orden',
                  dataIndex: 'tipo_orden',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'tercerizadora',
                  header: 'tercerizadora',
                  dataIndex: 'tercerizadora',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_punto',
                  header: 'IdPunto',
                  dataIndex: 'id_punto',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_orden_trabajo',
                  header: 'IdOrdenTrabajo',
                  dataIndex: 'id_orden_trabajo',
                  hidden: true,
                  hideable: false
                },                
                {
                    id: 'responsable',
                    header: 'responsable',
                    dataIndex: 'responsable',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'url_responsable',
                    header: 'url_responsable',
                    dataIndex: 'url_responsable',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'fieldIdResponsable',
                    header: 'fieldIdResponsable',
                    dataIndex: 'fieldIdResponsable',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'fieldValueResponsable',
                    header: 'fieldValueResponsable',
                    dataIndex: 'fieldValueResponsable',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'observacion_excedente',
                    header: 'observacionExcedente',
                    dataIndex: 'observacion_excedente',
                    hidden: true,
                    hideable: false
                }, 				
//                 {
//                   id: 'num_orden_trabajo',
//                   header: '# Orden Servicio',
//                   dataIndex: 'num_orden_trabajo',
//                   width: 100,
//                   sortable: true
//                 },
                {
                  id: 'cliente',
                  header: 'Cliente',
                  dataIndex: 'cliente',
                  width: 170,
                  sortable: true
                },
                {
                  id: 'esRecontratacion',
                  header: 'esRecontratacion',
                  dataIndex: 'esRecontratacion',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'vendedor',
                  header: 'Vendedor',
                  dataIndex: 'vendedor',
                  width: 127,
                  sortable: true
                },
                {
                  id: 'login2',
                  header: 'Login',
                  dataIndex: 'login2',
                  width: 140,
                  sortable: true
                },
                {
                  id: 'producto',
                  header: 'Servicio',
                  dataIndex: 'producto',
                  width: 140,
                  sortable: true
                },  
                {
                  id: 'ciudad',
                  header: 'Ciudad',
                  dataIndex: 'ciudad',
                  width: 80,
                  sortable: true
                },   
                {
                  id: 'coordenadas',
                  header: 'Coordenadas',
                  dataIndex: 'coordenadas',
                  width: 130,
                  sortable: true
                },
                {
                  id: 'direccion',
                  header: 'Direccion',
                  dataIndex: 'direccion',
                  width: 130,
                  sortable: true
                },   
                {
                  id: 'nombreSector',
                  header: 'Sector',
                  dataIndex: 'nombreSector',
                  width: 80,
                  sortable: true
                },  
                {
                  id: 'fechaAsignadaReal',
                  header: 'Fecha Asignada',
                  dataIndex: 'fechaAsignadaReal',
                  width: 110,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
                        {
                            getClass: function(v, meta, rec) {return rec.get('action1')},
                            tooltip: 'Ver Mapa',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
								
							    if(rec.get("latitud")!=0 && rec.get("longitud")!=0)
									showVerMapa(rec);
								else
									Ext.Msg.alert('Error ','Las coordenadas son incorrectas');	
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {return rec.get('action2')},
                            tooltip: 'Ver Croquis',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
								
							    if(rec.get("id_factibilidad")!="" && rec.get("rutaCroquis")!="")
									showVerCroquis(rec.get('id_factibilidad'), rec.get('rutaCroquis'));
								else
									Ext.Msg.alert('Error ','Las ruta no existe');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
								var permiso1 = $("#ROLE_142-115");
								var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1  ? true : false);									
								var permiso2 = $("#ROLE_142-116");
								var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2.val() == 1 ? true : false);							
								if(!boolPermiso1 || !boolPermiso2){ rec.data.action3 = "icon-invisible"; }
								
								if (rec.get('action3') == "icon-invisible") 
									this.items[2].tooltip = '';
								else 
									this.items[2].tooltip = 'Finalizar Instalacion';
									
								return rec.get('action3')
							},
                            tooltip: 'Finalizar Instalacion',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
								
								var permiso1 = $("#ROLE_142-115");
								var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1  ? true : false);									
								var permiso2 = $("#ROLE_142-116");
								var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2.val() == 1  ? true : false);							
								if(!boolPermiso1 || !boolPermiso2){ rec.data.action3 = "icon-invisible"; }
								
								if(rec.get('action3')!="icon-invisible")
									showMaterialesUtilizados(rec);
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
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 1230,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){buscar();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){limpiar();}
                }

                ],                
                items: 
                [              
                    {html:"&nbsp;",border:false,width:200},
                    {html:"Fecha Asignacion:",border:false,width:325},
                    {html:"&nbsp;",border:false,width:150},
                    {html:"&nbsp;",border:false,width:325},
                    {html:"&nbsp;",border:false,width:200},                    
                   
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaDesdeAsig,
                    {html:"&nbsp;",border:false,width:150},
                    DTFechaHastaAsig,
                    {html:"&nbsp;",border:false,width:200},                    
                
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'textfield',
                        id: 'txtLogin',
                        fieldLabel: 'Login',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtDescripcionPunto',
                        fieldLabel: 'Descripcion Punto',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'textfield',
                        id: 'txtVendedor',
                        fieldLabel: 'Vendedor',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtCiudad',
                        fieldLabel: 'Ciudad',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'textfield',
                        id: 'txtNumOrdenServicio',
                        fieldLabel: 'Número Orden Servicio',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {html:"&nbsp;",border:false,width:525},
                    {html:"&nbsp;",border:false,width:200}
                    
                ],	
        renderTo: 'filtro'
    });
});



/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    var boolError = false;
    
    if(( Ext.getCmp('fechaDesdeAsig').getValue()!=null)&&(Ext.getCmp('fechaHastaAsig').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesdeAsig').getValue() > Ext.getCmp('fechaHastaAsig').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Asigicacion debe ser fecha menor a Fecha Hasta Asigicacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });	
        }
    }
    
    if(!boolError)
    {
        store.getProxy().extraParams.fechaDesdeAsig = Ext.getCmp('fechaDesdeAsig').value;
        store.getProxy().extraParams.fechaHastaAsig = Ext.getCmp('fechaHastaAsig').value;
        store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
        store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
        store.load();
    }          
}

function limpiar(){
    Ext.getCmp('fechaDesdeAsig').setRawValue("");
    Ext.getCmp('fechaHastaAsig').setRawValue("");
    
    Ext.getCmp('txtLogin').value="";
    Ext.getCmp('txtLogin').setRawValue("");
    
    Ext.getCmp('txtDescripcionPunto').value="";
    Ext.getCmp('txtDescripcionPunto').setRawValue("");
    
    Ext.getCmp('txtVendedor').value="";
    Ext.getCmp('txtVendedor').setRawValue("");
    
    Ext.getCmp('txtCiudad').value="";
    Ext.getCmp('txtCiudad').setRawValue("");
    
    Ext.getCmp('txtNumOrdenServicio').value="";
    Ext.getCmp('txtNumOrdenServicio').setRawValue("");
    
    store.getProxy().extraParams.fechaDesdeAsig = Ext.getCmp('fechaDesdeAsig').value;
    store.getProxy().extraParams.fechaHastaAsig = Ext.getCmp('fechaHastaAsig').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
    store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
    store.load();
}
