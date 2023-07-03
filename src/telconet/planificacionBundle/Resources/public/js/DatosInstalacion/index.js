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
    
    // **************** EMPLEADOS ******************
//    Ext.define('EmpleadosList', {
//        extend: 'Ext.data.Model',
//        fields: [
//            {name:'id_empleado', type:'int'},
//            {name:'nombre_empleado', type:'string'}
//        ]
//    });
//    storeEmpleados = Ext.create('Ext.data.Store', {
//            model: 'EmpleadosList',
//            autoLoad: true,
//            proxy: {
//                type: 'ajax',
//                url : 'getEmpleados',
//                reader: {
//                    type: 'json',
//                    totalProperty: 'total',
//                    root: 'encontrados'
//                }
//            }
//    });    
//    combo_empleados = new Ext.form.ComboBox({
//            id: 'cmb_empleado',
//            name: 'cmb_empleado',
//            fieldLabel: false,
//            anchor: '100%',
//            queryMode:'remote',
//            width: 400,
//            emptyText: 'Seleccione Empleado',
//            store:storeEmpleados,
//            displayField: 'nombre_empleado',
//            valueField: 'id_empleado',
//            renderTo: 'combo_empleado'
//    });
//    
//    // **************** EMPRESA EXTERNA ******************
//    Ext.define('EmpresaExternaList', {
//        extend: 'Ext.data.Model',
//        fields: [
//            {name:'id_empresa_externa', type:'int'},
//            {name:'nombre_empresa_externa', type:'string'}
//        ]
//    });
//    storeEmpresaExterna = Ext.create('Ext.data.Store', {
//            model: 'EmpresaExternaList',
//            proxy: {
//                type: 'ajax',
//                url : 'getEmpresasExternas',
//                reader: {
//                    type: 'json',
//                    totalProperty: 'total',
//                    root: 'encontrados'
//                }
//            }
//    });    
//    combo_empresas_externas = new Ext.form.ComboBox({
//            id: 'cmb_empresa_externa',
//            name: 'cmb_empresa_externa',
//            fieldLabel: false,
//            anchor: '100%',
//            queryMode:'remote',
//            width: 400,
//            emptyText: 'Seleccione Empresa Externa',
//            store:storeEmpresaExterna,
//            displayField: 'nombre_empresa_externa',
//            valueField: 'id_empresa_externa',
//            renderTo: 'combo_empresa_externa'
//    });
//    
//    
//    // **************** CUADRILLAS ******************
//    Ext.define('CuadrillasList', {
//        extend: 'Ext.data.Model',
//        fields: [
//            {name:'id_cuadrilla', type:'int'},
//            {name:'nombre_cuadrilla', type:'string'}
//        ]
//    });
//    storeCuadrillas = Ext.create('Ext.data.Store', {
//            model: 'CuadrillasList',
//            proxy: {
//                type: 'ajax',
//                url : 'getCuadrillas',
//                reader: {
//                    type: 'json',
//                    totalProperty: 'total',
//                    root: 'encontrados'
//                }
//            }
//    });    
//    combo_cuadrillas = new Ext.form.ComboBox({
//            id: 'cmb_cuadrilla',
//            name: 'cmb_cuadrilla',
//            fieldLabel: false,
//            anchor: '100%',
//            queryMode:'remote',
//            width: 400,
//            emptyText: 'Seleccione Cuadrilla',
//            store:storeCuadrillas,
//            displayField: 'nombre_cuadrilla',
//            valueField: 'id_cuadrilla',
//            renderTo: 'combo_cuadrilla'
//    });
    
    
    
    
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
                    {name:'ultimaMilla', mapping:'ultimaMilla'},
                    {name:'radio', mapping:'radio'},
                    {name:'pop', mapping:'pop'},
                    {name:'dslam', mapping:'dslam'},
                    {name:'elementoId', mapping:'elementoId'},
                    {name:'id_factibilidad', mapping:'id_factibilidad'},
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'id_punto', mapping:'id_punto'},
                    {name:'id_orden_trabajo', mapping:'id_orden_trabajo'},
                    {name:'num_orden_trabajo', mapping:'num_orden_trabajo'},
                    {name:'cliente', mapping:'cliente'},
                    {name:'vendedor', mapping:'vendedor'},
                    {name:'login2', mapping:'login2'},
                    {name:'producto', mapping:'producto'},
                    {name:'coordenadas', mapping:'coordenadas'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'ciudad', mapping:'ciudad'},
                    {name:'nombreSector', mapping:'nombreSector'},
                    {name:'fechaPlanificacionReal', mapping:'fechaPlanificacionReal'},
                    {name:'fePlanificada', mapping:'fePlanificada'},
                    {name:'HoraIniPlanificada', mapping:'HoraIniPlanificada'},
                    {name:'HoraFinPlanificada', mapping:'HoraFinPlanificada'},
                    {name:'rutaCroquis', mapping:'rutaCroquis'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},          
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
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
//        selModel: sm,
//		dockedItems: [ toolbar ], 
        columns:[
                {
                  id: 'ultimaMilla',
                  header: 'ultimaMilla',
                  dataIndex: 'ultimaMilla',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'radio',
                  header: 'radio',
                  dataIndex: 'radio',
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
                  id: 'id_orden_trabajo',
                  header: 'IdOrdenTrabajo',
                  dataIndex: 'id_orden_trabajo',
                  hidden: true,
                  hideable: false
                },                
//                 {
//                   id: 'num_orden_trabajo',
//                   header: '# Orden Servicio',
//                   dataIndex: 'num_orden_trabajo',
//                   width: 110,
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
                  id: 'vendedor',
                  header: 'Vendedor',
                  dataIndex: 'vendedor',
                  width: 120,
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
                  width: 90,
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
                    width: 70,
                    items: [
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_135-94");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);							
                                if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }

                                if (rec.get('action1') == "icon-invisible") 
                                        this.items[0].tooltip = '';
                                else 
                                        this.items[0].tooltip = 'Descargar Datos de Instalacion';

                                return rec.get('action1')
                            },
                            tooltip: 'Descargar Datos de Instalacion',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_135-95");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);
                                if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }

                                if(rec.get('action1')!="icon-invisible")
                                        window.open("getDatosInstalacionPdf?id_servicio="+rec.data.id_servicio+"&cliente="+rec.data.cliente+"&id_solicitud="+rec.data.id_factibilidad);
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
        store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
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
    
    Ext.getCmp('txtNumOrdenServicio').value="";
    Ext.getCmp('txtNumOrdenServicio').setRawValue("");
    
    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
    store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
    store.load();
}