/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {  
    Ext.tip.QuickTipManager.init();
            
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesdeSolPlanif = new Ext.form.DateField({
        id: 'fechaDesdeSolPlanif',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaSolPlanif = new Ext.form.DateField({
        id: 'fechaHastaSolPlanif',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });	
    DTFechaDesdePlanificacion = new Ext.form.DateField({
        id: 'fechaDesdePlanificacion',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaPlanificacion = new Ext.form.DateField({
        id: 'fechaHastaPlanificacion',
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
        pageSize: 19,
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
                fechaDesdeSolPlanif: '',
                fechaHastaSolPlanif: '',
                fechaDesdePlanificacion: '',
                fechaHastaPlanificacion: '',
                login2: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                estado: 'Todos',
                tipoSolicitud: ''
            }
        },
        fields:
                [
                    {name:'id_factibilidad', mapping:'id_factibilidad'},
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'id_punto', mapping:'id_punto'},
                    {name:'id_orden_trabajo', mapping:'id_orden_trabajo'},
                    {name:'cliente', mapping:'cliente'},
                    {name:'vendedor', mapping:'vendedor'},
                    {name:'login2', mapping:'login2'},
                    {name:'producto', mapping:'producto'},
                    {name:'coordenadas', mapping:'coordenadas'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'ciudad', mapping:'ciudad'},
                    {name:'nombreSector', mapping:'nombreSector'},
                    {name:'feSolicitaPlanificacion', mapping:'feSolicitaPlanificacion'},
                    {name:'fechaPlanificacionReal', mapping:'fechaPlanificacionReal'},
                    {name:'fePlanificada', mapping:'fePlanificada'},
                    {name:'HoraIniPlanificada', mapping:'HoraIniPlanificada'},
                    {name:'HoraFinPlanificada', mapping:'HoraFinPlanificada'},
                    {name:'rutaCroquis', mapping:'rutaCroquis'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'usrPlanifica', mapping:'usrPlanifica'},
                    {name:'motivo', mapping:'motivo'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'},
                    {name:'action4', mapping:'action4'},
                    {name:'action5', mapping:'action5'}                 
                ],
//         autoLoad: true
    });

    var pluginExpanded = true;
    
	//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
	var permiso = $("#ROLE_144-37");
	var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1  ? true : false);	
	
	var exportarBtn = "";
	if(boolPermiso1)
	{
		exportarBtn = Ext.create('Ext.button.Button', {
			iconCls: 'icon_exportar',
			itemId: 'exportar',
			text: 'Exportar',
			scope: this,
			handler: function(){ exportarExcel();}
		});
	}
			
	var toolbar = Ext.create('Ext.toolbar.Toolbar', {
		dock: 'top',
		align: '->',
		items   : [ '->', exportarBtn]
	});

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1230,
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
		dockedItems: [ toolbar ], 
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
                  id: 'cliente',
                  header: 'Cliente',
                  dataIndex: 'cliente',
                  width: 150,
                  sortable: true
                },
                {
                  id: 'vendedor',
                  header: 'Vendedor',
                  dataIndex: 'vendedor',
                  width: 150,
                  sortable: true
                },
                {
                  id: 'login2',
                  header: 'Login',
                  dataIndex: 'login2',
                  width: 110,
                  sortable: true
                },
                {
                  id: 'producto',
                  header: 'Producto',
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
                  id: 'feSolicitaPlanificacion',
                  header: 'Fecha Solicita Planificacion',
                  dataIndex: 'feSolicitaPlanificacion',
                  width: 130,
                  sortable: true
                },   
                {
                  id: 'fechaPlanificacionReal',
                  header: 'Fecha Planificacion',
                  dataIndex: 'fechaPlanificacionReal',
                  width: 160,
                  sortable: true
                },
                {
                  id: 'usrPlanifica',
                  header: 'Usr Planifica',
                  dataIndex: 'usrPlanifica',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'estado',
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 90,
                  sortable: true
                },
                {
                  id: 'motivo',
                  header: 'Motivo',
                  dataIndex: 'motivo',
                  width: 500,
                  sortable: true
                }, 
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
                    {html:"&nbsp;",border:false,width:200},
                    {html:"Fecha Solicita Planificacion:",border:false,width:325},
                    {html:"&nbsp;",border:false,width:150},
                    {html:"Fecha Planificacion:",border:false,width:325},
                    {html:"&nbsp;",border:false,width:200},
                    
                   
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaDesdeSolPlanif,
                    {html:"&nbsp;",border:false,width:150},
                    DTFechaDesdePlanificacion,
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaHastaSolPlanif,
                    {html:"&nbsp;",border:false,width:150},
                    DTFechaHastaPlanificacion,
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
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estado',
                        id: 'sltEstado',
                        value:'Todos',
                        store: [
                            ['Todos','Todos'],
                            ['PrePlanificada','PrePlanificada'],
                            ['Planificada','Planificada'],
                            ['Detenido','Detenido'],
                            ['Anulado','Anulado'],
                            ['Rechazada','Rechazada']
                        ],
                        width: '525'
                    },
                    {html:"&nbsp;",border:false,width:200},
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Tipo Solicitud',
                        id: 'sltTipoSolicitud',
                        value:'Todos',
                        store: [
                            ['Todos','Todos'],
                            ['SOLICITUD PLANIFICACION','SOLICITUD PLANIFICACION'],
                            ['SOLICITUD RETIRO EQUIPO','SOLICITUD RETIRO EQUIPO'],
                            ['SOLICITUD CAMBIO EQUIPO','SOLICITUD CAMBIO EQUIPO']
                ],	
                        width: 325,
                     }
                ],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    var boolError = false;
    
    if(( Ext.getCmp('fechaDesdeSolPlanif').getValue()!=null)&&(Ext.getCmp('fechaHastaSolPlanif').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesdeSolPlanif').getValue() > Ext.getCmp('fechaHastaSolPlanif').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Solicita Planificacion debe ser fecha menor a Fecha Hasta Solicita Planificacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });	
        }
    }
    
    if(( Ext.getCmp('fechaDesdePlanificacion').getValue()!=null)&&(Ext.getCmp('fechaHastaPlanificacion').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesdePlanificacion').getValue() > Ext.getCmp('fechaHastaPlanificacion').getValue())
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
        store.removeAll();
        store.getProxy().extraParams.fechaDesdeSolPlanif = Ext.getCmp('fechaDesdeSolPlanif').value;
        store.getProxy().extraParams.fechaHastaSolPlanif = Ext.getCmp('fechaHastaSolPlanif').value;
        store.getProxy().extraParams.fechaDesdePlanificacion = Ext.getCmp('fechaDesdePlanificacion').value;
        store.getProxy().extraParams.fechaHastaPlanificacion = Ext.getCmp('fechaHastaPlanificacion').value;
        store.getProxy().extraParams.login2= Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
        store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
        store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
        store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('sltTipoSolicitud').value;
        store.load(); 
    }          
}

function limpiar(){
    Ext.getCmp('fechaDesdeSolPlanif').setRawValue("");
    Ext.getCmp('fechaHastaSolPlanif').setRawValue("");
    Ext.getCmp('fechaDesdePlanificacion').setRawValue("");
    Ext.getCmp('fechaHastaPlanificacion').setRawValue("");
    
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
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
        
    store.removeAll();
    store.getProxy().extraParams.fechaDesdeSolPlanif = "";
    store.getProxy().extraParams.fechaHastaSolPlanif = "";
    store.getProxy().extraParams.fechaDesdePlanificacion = "";
    store.getProxy().extraParams.fechaHastaPlanificacion = "";
    store.getProxy().extraParams.login2= "";
    store.getProxy().extraParams.descripcionPunto = "";
    store.getProxy().extraParams.vendedor = "";
    store.getProxy().extraParams.ciudad = "";
    store.getProxy().extraParams.numOrdenServicio = "";
    store.getProxy().extraParams.estado = "Todos";
    store.getProxy().extraParams.tipoSolicitud = "Todos";
    store.load();
}

function exportarExcel(){
	var parametros = "";
        	
	//se obtienen valores de parametros de fechas de manera correcta	
	parametros = "?fechaDesdeSolPlanif="+(( Ext.getCmp('fechaDesdeSolPlanif').getRawValue() ) ?  Ext.getCmp('fechaDesdeSolPlanif').getRawValue() : '' );
	parametros = parametros+"&fechaHastaSolPlanif="+(( Ext.getCmp('fechaHastaSolPlanif').getRawValue() ) ?  Ext.getCmp('fechaHastaSolPlanif').getRawValue() : '' );;
        parametros = parametros+"&fechaDesdePlanificacion="+(( Ext.getCmp('fechaDesdePlanificacion').getRawValue() ) ?  Ext.getCmp('fechaDesdePlanificacion').getRawValue() : '' );
        parametros = parametros+"&fechaHastaPlanificacion="+(( Ext.getCmp('fechaHastaPlanificacion').getRawValue() ) ?  Ext.getCmp('fechaHastaPlanificacion').getRawValue() : '' );
        
        parametros = parametros+"&login2="+Ext.getCmp('txtLogin').value;
	parametros = parametros+"&descripcionPunto="+Ext.getCmp('txtDescripcionPunto').value;
	parametros = parametros+"&vendedor="+Ext.getCmp('txtVendedor').value;
	parametros = parametros+"&ciudad="+Ext.getCmp('txtCiudad').value;
	parametros = parametros+"&numOrdenServicio="+Ext.getCmp('txtNumOrdenServicio').value;
	parametros = parametros+"&estado="+Ext.getCmp('sltEstado').value;
        //Se agrega codigo para funcionamiento de nuevo filtro
        parametros = parametros+"&tipoSolicitud="+Ext.getCmp('sltTipoSolicitud').value;
    
	window.open("exportarConsulta"+parametros);
	
	
}