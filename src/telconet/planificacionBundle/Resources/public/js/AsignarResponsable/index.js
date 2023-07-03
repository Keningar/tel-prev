/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {   
    Ext.tip.QuickTipManager.init();
              
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesdePlanif = new Ext.form.DateField({
        id: 'fechaDesdePlanif',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaPlanif = new Ext.form.DateField({
        id: 'fechaHastaPlanif',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });	
    
    storeTiposSolicitud = new Ext.data.Store({ 
	total: 'total',
	pageSize: 50,
	proxy: {
	    type: 'ajax',
	    url : '/comercial/solicitud/solicitudes/ajaxGetTiposSolicitud',
	    reader: {
		type: 'json',
		totalProperty: 'total',
		root: 'encontrados'
	    }
	},
	fields:
		[
		    {name:'id_tipo_solicitud', mapping:'id_tipo_solicitud'},
		    {name:'tipo_solicitud', mapping:'tipo_solicitud'}
		],
	autoLoad: true
    });
      
    store = new Ext.data.Store({ 
        pageSize: 15,
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
                    {name:'ultimaMilla', mapping:'ultimaMilla'},
                    {name:'pop', mapping:'pop'},
                    {name:'dslam', mapping:'dslam'},
                    {name:'elementoId', mapping:'elementoId'},
                    {name:'id_factibilidad', mapping:'id_factibilidad'},
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'id_punto', mapping:'id_punto'},
		    {name:'traslado', mapping:'traslado'},
                    {name:'tipo_orden', mapping:'tipo_orden'},
                    {name:'cliente', mapping:'cliente'},
		    {name:'esRecontratacion', mapping:'esRecontratacion'},
                    {name:'vendedor', mapping:'vendedor'},
                    {name:'login2', mapping:'login2'},
					{name:'tercerizadora', mapping:'tercerizadora'},
			       {name:'descripcionSolicitud', mapping:'descripcionSolicitud'},
                    {name:'producto', mapping:'producto'},
                    {name:'coordenadas', mapping:'coordenadas'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'ciudad', mapping:'ciudad'},
                    {name:'jurisdiccion', mapping:'jurisdiccion'},
                    {name:'nombreSector', mapping:'nombreSector'},
                    {name:'fechaPlanificacionReal', mapping:'fechaPlanificacionReal'},
                    {name:'fePlanificada', mapping:'fePlanificada'},
                    {name:'HoraIniPlanificada', mapping:'HoraIniPlanificada'},
                    {name:'HoraFinPlanificada', mapping:'HoraFinPlanificada'},
                    {name:'rutaCroquis', mapping:'rutaCroquis'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'strTipoEnlace', mapping:'strTipoEnlace'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'},
                    {name:'action4', mapping:'action4'}                
                ],
//         autoLoad: true
    });

    var pluginExpanded = true;
												
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso = $("#ROLE_139-111");
	var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);	
	
	var permiso = $("#ROLE_139-112");
	var boolPermiso2 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);	
	
	var asignarGlobalBtn = "";
	var asignarIndividualBtn = "";
	sm = "";
	if(boolPermiso1 && boolPermiso2)
	{
	    sm = Ext.create('Ext.selection.CheckboxModel', {
	        checkOnly: true
	    })
	}
	if(boolPermiso1)
	{
		asignarGlobalBtn = Ext.create('Ext.button.Button', {
			iconCls: 'icon_delete',
			text: 'Asignar',
			itemId: 'asignar',
		    scope   : this,
			handler: function(){asignarResponsable('local', '0');}
		});
	}
	if(boolPermiso2)
	{
		asignarIndividualBtn = Ext.create('Ext.button.Button', {
			iconCls: 'icon_delete',
			text: 'Asignacion Individual',
			itemId: 'asignacion_individual',
			scope: this,
			handler: function(){showAsignacionIndividual('local', '0', false);}
		});
	}
			
	var toolbar = Ext.create('Ext.toolbar.Toolbar', {
		dock: 'top',
		align: '->',
		items   : [ '->', asignarGlobalBtn, asignarIndividualBtn]
	});
	
    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1230,
        height: 503,
        store: store,
        loadMask: true,
        frame: false,
        columns:[
                {
                  id: 'ultimaMilla',
                  header: 'ultimaMilla',
                  dataIndex: 'ultimaMilla',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'pop',
                  header: 'pop',
                  dataIndex: 'pop',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'dslam',
                  header: 'dslam',
                  dataIndex: 'dslam',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'elementoId',
                  header: 'elementoId',
                  dataIndex: 'elementoId',
                  hidden: true,
                  hideable: false
                },
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
                  id: 'id_punto',
                  header: 'IdPunto',
                  dataIndex: 'id_punto',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'traslado',
                  header: 'traslado',
                  dataIndex: 'traslado',
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
                  id: 'tipo_orden',
                  header: 'tipo_orden',
                  dataIndex: 'tipo_orden',
                  hidden: true,
                  hideable: false
                },      
                {
                  id: 'esRecontratacion',
                  header: 'esRecontratacion',
                  dataIndex: 'esRecontratacion',
                  hidden: true,
                  hideable: false
                },		
                {
                  id: 'descripcionSolicitud',
                  header: 'Tipo Solicitud',
                  dataIndex: 'descripcionSolicitud',
                  width: 140,
                  sortable: true
                },
                {
                  id: 'cliente',
                  header: 'Cliente',
                  dataIndex: 'cliente',
                  width: 168,
                  sortable: true
                },
                {
                  id: 'vendedor',
                  header: 'Vendedor',
                  dataIndex: 'vendedor',
                  width: 100,
                  sortable: true
                },
                {
                  id: 'login2',
                  header: 'Login',
                  dataIndex: 'login2',
                  width: 120,
                  sortable: true
                },
                {
                  id: 'producto',
                  header: 'Servicio',
                  dataIndex: 'producto',
                  width: 150,
                  sortable: true
                },  
                {
                  id: 'jurisdiccion',
                  header: 'Jurisdiccion',
                  dataIndex: 'jurisdiccion',
                  width: 80,
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
                  id: 'fechaPlanificacionReal',
                  header: 'Fecha Planificacion',
                  dataIndex: 'fechaPlanificacionReal',
                  width: 150,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 100,
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
                                var permiso = $("#ROLE_139-7");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);							
                                if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }

                                if (rec.get('action3') == "icon-invisible") 
                                        this.items[2].tooltip = '';
                                else 
                                        this.items[2].tooltip = 'Asignar Responsable';

                                return rec.get('action3')
                            },
                            tooltip: 'Asignar Responsable',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_139-7");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);
                                if(!boolPermiso){ rec.data.action4 = "icon-invisible"; }

                                if(rec.get('action4')!="icon-invisible")
                                        showAsignacionIndividual(rec,'local', '0', false);
                                else
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: 'No tiene permisos para realizar esta accion',
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                         });
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
    
    if(prefijoEmpresa == "TN")
    {
        grid.headerCt.insert(
                                    15,
                                    {
                                        text: 'T. Enlace',
                                        width: 60,
                                        dataIndex: 'strTipoEnlace',
                                        sortable: true
                                    }
                                );
    }
    if(prefijoEmpresa == "MD")
    {
        grid.headerCt.insert(
                                    18,
                                    {
                                        id: 'nombreSector',
                                        header: 'Sector',
                                        dataIndex: 'nombreSector',
                                        width: 80,
                                        sortable: true
                                    }
                                );
    }
    
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
                    {html:"Fecha Planificacion:",border:false,width:325},
                    {html:"&nbsp;",border:false,width:150},
                    {html:"&nbsp;",border:false,width:325},
                    {html:"&nbsp;",border:false,width:200},                    
                   
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaDesdePlanif,
                    {html:"&nbsp;",border:false,width:150},
                    DTFechaHastaPlanif,
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
//                     {
//                         xtype: 'textfield',
//                         id: 'txtNumOrdenServicio',
//                         fieldLabel: 'Número Orden Servicio',
//                         value: '',
//                         width: '325'
//                     },
		    {
                        xtype: 'combobox',
                        id: 'filtro_tipo_solicitud',
                        name: 'filtro_tipo_solicitud',
                        fieldLabel: 'Tipo Solicitud',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField:'tipo_solicitud',
                        valueField: 'id_tipo_solicitud',
                        selectOnTab: true,
                        store: [
                            ['SOLICITUD PLANIFICACION','SOLICITUD PLANIFICACION'],
                            ['SOLICITUD RETIRO EQUIPO','SOLICITUD RETIRO EQUIPO'],
                            ['SOLICITUD CAMBIO EQUIPO','SOLICITUD CAMBIO EQUIPO'],
                            ['SOLICITUD MIGRACION','SOLICITUD MIGRACION'],
                            ['SOLICITUD AGREGAR EQUIPO','SOLICITUD AGREGAR EQUIPO'],
                            ['SOLICITUD AGREGAR EQUIPO MASIVO','SOLICITUD AGREGAR EQUIPO MASIVO'],
                            ['SOLICITUD REUBICACION','SOLICITUD REUBICACION'],
                        ], 
                        lazyRender: true,
                        queryMode: "local",
                        listClass: 'x-combo-list-small',
                        width: 325,
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
    
    if(( Ext.getCmp('fechaDesdePlanif').getValue()!=null)&&(Ext.getCmp('fechaHastaPlanif').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesdePlanif').getValue() > Ext.getCmp('fechaHastaPlanif').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Planificacion debe ser fecha menor a Fecha Hasta Planificacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });	
        }
    }
    
    if(!boolError)
    {
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
//         store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
	store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('filtro_tipo_solicitud').value;
        store.load();
    }          
}

function limpiar(){
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");
    
    Ext.getCmp('txtLogin').value="";
    Ext.getCmp('txtLogin').setRawValue("");
    
    Ext.getCmp('txtDescripcionPunto').value="";
    Ext.getCmp('txtDescripcionPunto').setRawValue("");
    
    Ext.getCmp('txtVendedor').value="";
    Ext.getCmp('txtVendedor').setRawValue("");
    
    Ext.getCmp('txtCiudad').value="";
    Ext.getCmp('txtCiudad').setRawValue("");
    /*
    Ext.getCmp('txtNumOrdenServicio').value="";
    Ext.getCmp('txtNumOrdenServicio').setRawValue("");*/
       Ext.getCmp('filtro_tipo_solicitud').value="";
    Ext.getCmp('filtro_tipo_solicitud').setRawValue("");
    
    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
//     store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
    store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('filtro_tipo_solicitud').value;
    store.load();
}