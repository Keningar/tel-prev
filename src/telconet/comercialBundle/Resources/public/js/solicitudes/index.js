/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.1 27-07-2017 - Se agrega el timeout para el store grid
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
    DTFechaDesdeIngOrd = new Ext.form.DateField({
        id: 'fechaDesdeIngOrd',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaIngOrd = new Ext.form.DateField({
        id: 'fechaHastaIngOrd',
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
	    url : 'ajaxGetTiposSolicitud',
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
	
	storeEstadosTiposSolicitud = new Ext.data.Store({ 
	total: 'total',
	pageSize: 50,
	proxy: {
	    type: 'ajax',
	    url : 'ajaxGetEstadosTiposSolicitud',
	    reader: {
		type: 'json',
		totalProperty: 'total',
		root: 'encontrados'
	    }
	},
	fields:
		[
		    {name:'estado_tipo_solicitud', mapping:'estado_tipo_solicitud'}
		],
	autoLoad: true
    });
    
    store = new Ext.data.Store({ 
        pageSize: 15,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'grid',
            timeout : 600000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                fechaDesdeIngOrd: '',
                fechaHastaIngOrd: '',
                login2: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                tipoSolicitud: '',
                estadoTipoSolicitud: '',
                estado: 'Todos'
            }
        },
        fields:
                [
                    {name:'id_solicitud', mapping:'id_solicitud'},
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'id_punto', mapping:'id_punto'},
                    {name:'id_orden_trabajo', mapping:'id_orden_trabajo'},
                    {name:'cliente', mapping:'cliente'},
                    {name:'tipoSolicitud', mapping:'tipoSolicitud'},
                    {name:'tipo_orden', mapping:'tipo_orden'},
                    {name:'vendedor', mapping:'vendedor'},
                    {name:'login2', mapping:'login2'},
                    {name:'producto', mapping:'producto'},
                    {name:'coordenadas', mapping:'coordenadas'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'ciudad', mapping:'ciudad'},
                    {name:'nombreSector', mapping:'nombreSector'},
                    {name:'fePlanificacion', mapping:'fePlanificacion'},
                    {name:'precioTraslado', mapping:'precioTraslado'},
                    {name:'descripcionTraslado', mapping:'descripcionTraslado'},
                    {name:'tipoNegocio', mapping:'tipoNegocio'},
                    {name:'idsServicioTraslado', mapping:'idsServicioTraslado'},
                    {name:'loginATrasladar', mapping:'loginATrasladar'},
                    {name:'tiempoEsperaMeses', mapping:'tiempoEsperaMeses'},
                    {name:'saldoPunto', mapping:'saldoPunto'},
                    {name:'rutaCroquis', mapping:'rutaCroquis'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'precioDescuento', mapping:'precioDescuento'},
                    {name:'esCloudForm', mapping:'esCloudForm'},
                    {name:'linkDescarga', mapping:'linkDescarga'},
                    {name:'precioDescGrid', mapping:'precioDescGrid'},
                    {name:'observacion2', mapping:'observacion2'},
                    {name:'motivo', mapping:'motivo'},
                    {name:'usr_rechazo', mapping:'usr_rechazo'},
                    {name:'fe_rechazo', mapping:'fe_rechazo'},
                    {name:'fe_ejecucion', mapping:'fe_ejecucion'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'},
                    {name:'action4', mapping:'action4'},
                    {name:'action5', mapping:'action5'},
                    {name:'action9', mapping:'action9'},
                    {name:'prefEmpresa', mapping:'prefEmpresa'},
                    {name:'usrVendedor', mapping:'usrVendedor'}
                    
                    // {name:'action3', mapping:'action3'},
                    // {name:'action4', mapping:'action4'},
                    // {name:'action5', mapping:'action5'}                 
                ],
//         autoLoad: true
    });

    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1320,
        height: 510,
        store: store,
        loadMask: true,
        frame: false,
        viewConfig: { enableTextSelection: true },
        /*selModel: sm,
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        itemId: 'delete',
                        scope: this,
                        handler: function(){ eliminarAlgunos();}
                    }
                ]}
        ], */
        columns:[
                {
                  id: 'id_solicitud',
                  header: 'id_solicitud',
                  dataIndex: 'id_solicitud',
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
                  width: 110,
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
                  width: 100,
                  sortable: true
                },
                {
                  id: 'producto',
                  header: 'Producto',
                  dataIndex: 'producto',
                  width: 125,
                  sortable: true
                },  
                {
                  id: 'tipo_orden',
                  header: 'T. Servicio',
                  dataIndex: 'tipo_orden',
                  width: 75,
                  sortable: true
                }, 
                {
                  id: 'ciudad',
                  header: 'Ciudad',
                  dataIndex: 'ciudad',
                  width: 60,
                  sortable: true
                },
                {
                  id: 'tipoSolicitud',
                  header: 'Tipo Solicitud',
                  dataIndex: 'tipoSolicitud',
                  width: 140,
                  sortable: true
                },  
                {
                  id: 'fePlanificacion',
                  header: 'F. Solicita Aprob',
                  dataIndex: 'fePlanificacion',
                  width: 100,
                  sortable: true
                },                 
                {
                  id: 'fe_ejecucion',
                  header: 'F. Ejecucion',
                  dataIndex: 'fe_ejecucion',
                  width: 80,
                  sortable: true
                },   
                {
                  id: 'fe_rechazo',
                  header: 'F. Rechazo',
                  dataIndex: 'fe_rechazo',
                  width: 80,
                  sortable: true
                },  
                {
                  id: 'precioDescuento',
                  header: 'Precio',
                  dataIndex: 'precioDescGrid',
                  width: 60,
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
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 190,
                    items: [
                        {
                            getClass: function(v, meta, rec) {return rec.get('action2')},
                            tooltip: 'Ver Historial',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                showHistorial(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {return rec.get('action1')},
                            tooltip: 'Ver Materiales',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                showMateriales(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_404-5639");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    
                                if (rec.get("tipoSolicitud") == "SOLICITUD TRASLADO" &&
                                    rec.get("estado") == "Pendiente" && boolPermiso)
                                {
                                    return 'button-grid-cambioVelocidad'
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Cambiar Precio de Traslado',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                actualizarValorPrecioTraslado(rec.get("id_solicitud"),rec.get("precioDescuento"));
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_404-5638");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (rec.get("tipoSolicitud") == "SOLICITUD REUBICACION" &&
                                    rec.get("estado") == "Pendiente" && boolPermiso)
                                {
                                    return 'button-grid-aprobar';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Aprobar solicitud de reubicación',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                aprobarSolicitudReubicacion(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if ((rec.get("tipoSolicitud") == "SOLICITUD CAMBIO EQUIPO POR SOPORTE" ||
                                    rec.get("tipoSolicitud") == "SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO" ) &&
                                    rec.get("estado") == "Pendiente" && permiteAprobarSolicitudCEPS)
                                {
                                    return 'button-grid-aprobar';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Aprobar solicitud de cambio de equipo por soporte',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                aprobarSolicitudCEPS(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_404-5917");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (rec.get("tipoSolicitud") == "SOLICITUD TRASLADO" &&
                                    rec.get("estado") == "PendienteAutorizar" && boolPermiso)
                                {
                                    return 'button-grid-aprobar';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Aprobar solicitud de traslado',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                aprobarTraslado(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_420-6117");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (rec.get("tipoSolicitud") == "SOLICITUD APROBACION SERVICIO" &&
                                    rec.get("estado") == "Pendiente" && boolPermiso)                        
                                {
                                    return 'button-grid-aprobar';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Gestionar solicitud de servicio',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                gestionarSolicitudServicio(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var objRolPermitido = $("#ROLE_440-6857");
                                var boolPermiso = (typeof objRolPermitido === 'undefined') ? false : (objRolPermitido.val() == 1 ? true : false);
                                if (rec.get("tipoSolicitud") == "SOLICITUD APROBACION SERVICIO TIPO RED MPLS" &&
                                    rec.get("estado") == "Pendiente" && boolPermiso)
                                {
                                    return 'button-grid-aprobar';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Gestionar solicitud de servicio con tipo de red Mpls',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                gestionarSolicitudServicioMPLS(rec);
                            }
                        },
                        //Descargar documento cargado por producto cloudpublic
                        {
                            getClass: function(v, meta, rec) 
                            {
                                if (rec.get("tipoSolicitud") === "SOLICITUD APROBACION CLOUDFORM" &&
                                    rec.get("estado") === "PendienteAutorizar" && puedeAprobarSolicitudCloud)
                                {
                                    return 'button-grid-descargar';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Descargar Documento',
                            handler: function(grid, rowIndex) {
                                var rec = store.getAt(rowIndex);
                                window.location = rec.get('linkDescarga');
                            }
                        },
                        //Aprobacion de contrato subido por producto cloudpublic
                        {
                            getClass: function(v, meta, rec) 
                            {
                                if (rec.get("tipoSolicitud") === "SOLICITUD APROBACION CLOUDFORM" &&
                                    rec.get("estado") === "PendienteAutorizar" && puedeAprobarSolicitudCloud)
                                {
                                    return 'button-grid-aprobar';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Aprobar/Rechazar Contrato Cloud Public',
                            handler: function(grid, rowIndex) {
                                var rec = store.getAt(rowIndex);
                                aprobarContratoCloudPublic(rec);
                            }
                        },
                        // Ver Seguimiento
                        {
                            getClass: function(v, meta, rec)
                            {
                                    this.items[3].tooltip = 'Ver Seguimiento';
    
                                return rec.get('action3');
                            },tooltip : 'Ver Seguimiento',
                            handler: function(grid, rowIndex, colIndex)
                            {
                                var rec = store.getAt(rowIndex);
    
                                verSeguimientoTareaExcedSol(rec.data.id_solicitud, rec.data.id_punto, rec.data.login2, rec.data.producto, rec.data.prefEmpresa, rec.data.usrVendedor);//
    
                            }
                        },
                        //Cargar Archivo
                        {
                        getClass: function(v, meta, rec) {
                                this.items[4].tooltip = 'Cargar Archivo';
                    
                            return rec.get('action4');
                        },tooltip : 'Cargar Archivo',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                        
                            subirMultipleAdjuntosMateriales(rec.data.id_solicitud,
                                rec.data.id_servicio);
                        }
                        },
                        // Ver Archivos
                        {
                            getClass: function(v, meta, rec)
                            {
                                this.items[5].tooltip =  'Ver Archivos'; 
                        
                                return rec.get('action5')
                            },tooltip :  'Ver Archivos',
                            handler: function(grid, rowIndex, colIndex)
                            {
                            var rec = store.getAt(rowIndex);
                    
                            presentarDocumentosMaterialesExcedentes(rec);
                               
                            }
                        },
                        

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
        width: 1320,
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
                    {html:"Fecha Ingreso Orden:",border:false,width:325},
                    {html:"&nbsp;",border:false,width:200},
                    
                   
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaDesdePlanif,
                    {html:"&nbsp;",border:false,width:150},
                    DTFechaDesdeIngOrd,
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaHastaPlanif,
                    {html:"&nbsp;",border:false,width:150},
                    DTFechaHastaIngOrd,
                    {html:"&nbsp;",border:false,width:200},
                    
                
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'textfield',
                        id: 'txtLogin',
                        fieldLabel: 'Login',
                        value: '',
                        width: 325
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtDescripcionPunto',
                        fieldLabel: 'Descripcion Punto',
                        value: '',
                        width: 325
                    },
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'textfield',
                        id: 'txtVendedor',
                        fieldLabel: 'Vendedor',
                        value: '',
                        width: 325
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtCiudad',
                        fieldLabel: 'Ciudad',
                        value: '',
                        width: 325
                    },
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
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
						store: storeTiposSolicitud,              
						lazyRender: true,
						queryMode: "local",
						listClass: 'x-combo-list-small',
						width: 325,
					},
                    {html:"&nbsp;",border:false,width:150},
					{
						xtype: 'combobox',
						id: 'estado_tipo_solicitud',
						name: 'estado_tipo_solicitud',
						fieldLabel: 'Estado Solicitud',
						typeAhead: true,
						triggerAction: 'all',
						displayField:'estado_tipo_solicitud',
						valueField: 'estado_tipo_solicitud',
						selectOnTab: true,
						store: storeEstadosTiposSolicitud,              
						lazyRender: true,
						queryMode: "local",
						listClass: 'x-combo-list-small',
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
    
    if(( Ext.getCmp('fechaDesdeIngOrd').getValue()!=null)&&(Ext.getCmp('fechaHastaIngOrd').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesdeIngOrd').getValue() > Ext.getCmp('fechaHastaIngOrd').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Ingreso Orden debe ser fecha menor a Fecha Hasta Ingreso Orden.',
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
        store.getProxy().extraParams.fechaDesdeIngOrd = Ext.getCmp('fechaDesdeIngOrd').value;
        store.getProxy().extraParams.fechaHastaIngOrd = Ext.getCmp('fechaHastaIngOrd').value;
        store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
        store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('filtro_tipo_solicitud').value;
	store.getProxy().extraParams.estadoTipoSolicitud = Ext.getCmp('estado_tipo_solicitud').value;
        store.load();
    }          
}

function limpiar(){
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaDesdePlanif').value="";

    Ext.getCmp('fechaHastaPlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').value="";
    
    Ext.getCmp('fechaDesdeIngOrd').setRawValue("");
    Ext.getCmp('fechaDesdeIngOrd').value="";
    
    Ext.getCmp('fechaHastaIngOrd').setRawValue("");
    Ext.getCmp('fechaHastaIngOrd').value="";
    
    Ext.getCmp('txtLogin').value="";
    Ext.getCmp('txtLogin').setRawValue("");
    
    Ext.getCmp('txtDescripcionPunto').value="";
    Ext.getCmp('txtDescripcionPunto').setRawValue("");
    
    Ext.getCmp('txtVendedor').value="";
    Ext.getCmp('txtVendedor').setRawValue("");
    
    Ext.getCmp('txtCiudad').value="";
    Ext.getCmp('txtCiudad').setRawValue("");
    
    Ext.getCmp('estado_tipo_solicitud').value="";
    Ext.getCmp('estado_tipo_solicitud').setRawValue("");
	
    Ext.getCmp('filtro_tipo_solicitud').value="";
    Ext.getCmp('filtro_tipo_solicitud').setRawValue("");
    
    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.fechaDesdeIngOrd = Ext.getCmp('fechaDesdeIngOrd').value;
    store.getProxy().extraParams.fechaHastaIngOrd = Ext.getCmp('fechaHastaIngOrd').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
    store.getProxy().extraParams.estadoTipoSolicitud = Ext.getCmp('estado_tipo_solicitud').value;
    store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('filtro_tipo_solicitud').value;
    store.load();
}

var connActPrecioTraslado = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        }
    }
});

/**
 * actualizarValorPrecioTraslado
 * 
 * Documentación para el método 'actualizarValorPrecioTraslado'.
 *
 * Función que muestra pantalla de actualización de precio de solicitudes de traslado
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 22-01-2018
 * @since
 */
function actualizarValorPrecioTraslado(intIdSolicitud, precioAct)
{
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    // The total column count must be specified here
                    columns: 2
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 410
                            },
                        items:
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'precioActual',
                                    name: 'precioActual',
                                    fieldLabel: 'Precio Actual',
                                    displayField: '',
                                    value: precioAct,
                                    valueField: '',
                                    maxLength: 20,
                                    readOnly: true,
                                    disabled: true,
                                    width: '250'
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'precioNuevo',
                                    name: 'precioNuevo',
                                    fieldLabel: 'Precio Nuevo',
                                    displayField: '',
                                    valueField: '',
                                    maxLength: 20,
                                    width: '250',
                                    maskRe: /[0-9.]/,
                                    regex: /^[0-9]+(?:\.[0-9][0-9])?$/,
                                    regexText: 'Solo numeros',
                                    allowBlank: false
                                },
                                {
                                    xtype     : 'textareafield',
                                    id        : 'observacion',
                                    grow      : true,
                                    name      : 'observación',
                                    fieldLabel: 'Observación',
                                    anchor    : '100%',
                                    allowBlank: false
                                }

                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Actualizar',
                        formBind: true,
                        handler: function()
                        {
                            var strPrecioNuevo = Ext.getCmp('precioNuevo').value;
                            var strObservacion = Ext.getCmp('observacion').value;
                            if (Ext.isEmpty(strPrecioNuevo) || Ext.isEmpty(strObservacion))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese los valores correspondientes!");
                            }
                            else
                            {
                                connActPrecioTraslado.request
                                    ({
                                        url: url_actualizarPrecioTraslado,
                                        method: 'post',
                                        waitMsg: 'Ejecutando...',
                                        timeout: 400000,
                                        params:
                                            {
                                                idSolicitud: intIdSolicitud,
                                                precioNuevo: strPrecioNuevo,
                                                observacion: strObservacion
                                            },
                                        success: function(response) {
                                            var datosActualizaPrecioTrasladoTn = Ext.JSON.decode(response.responseText);
                                            if (datosActualizaPrecioTrasladoTn.strStatus == "OK")
                                            {
                                                store.load();
                                                winPrecioTraslado.destroy();
                                                Ext.Msg.alert('Mensaje ', datosActualizaPrecioTrasladoTn.strMensaje);
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error ', datosActualizaPrecioTrasladoTn.strMensaje);
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winPrecioTraslado.destroy();
                        }
                    }
                ]
        });

    var winPrecioTraslado = Ext.create('Ext.window.Window',
        {
            title: 'Actualizar precio de traslado',
            modal: true,
            width: 450,
            closable: true,
            resizable: false,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

/**
 * aprobarSolicitudReubicacion
 * 
 * Documentación para el método 'aprobarSolicitudReubicacion'.
 *
 * Función que muestra pantalla de aprobación de solicitudes de reubicación
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 22-01-2018
 * @since
 */
function aprobarSolicitudReubicacion(rec)
{
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    // The total column count must be specified here
                    columns: 1
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: 'Información del servicio',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 410
                            },
                        items:
                            [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Dirección',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                    },
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 410
                            },
                        items:
                            [
                                {
                                    xtype     : 'textareafield',
                                    id        : 'observacion',
                                    grow      : true,
                                    name      : 'observación',
                                    fieldLabel: 'Observación',
                                    anchor    : '100%',
                                    allowBlank: false
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: '<span style="font-weight:bold; color:black;"> Aprobar </span>',
                        formBind: true,
                        handler: function()
                        {
                            var strObservacion = Ext.getCmp('observacion').value;
                            if (Ext.isEmpty(strObservacion))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese los valores correspondientes!");
                            }
                            else
                            {
                                
                                connActPrecioTraslado.request
                                    ({
                                        url: url_aprobarSolicitudReubicacion,
                                        method: 'post',
                                        waitMsg: 'Ejecutando...',
                                        timeout: 400000,
                                        params:
                                            {
                                                idSolicitud: rec.get("id_solicitud"),
                                                observacion: strObservacion,
                                                proceso    : "PrePlanificada"
                                            },
                                        success: function(response) {
                                            var datosRespuestaTn = Ext.JSON.decode(response.responseText);
                                            if (datosRespuestaTn.strStatus == "OK")
                                            {
                                                store.load();
                                                winPrecioTraslado.destroy();
                                                Ext.Msg.alert('Mensaje ', datosRespuestaTn.strMensaje);
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error ', datosRespuestaTn.strMensaje);
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                            }
                        }
                    },
                    {
                        text: '<span style="font-weight:bold; color:red;"> Rechazar </span>',
                        formBind: true,
                        handler: function()
                        {
                            var strObservacion = Ext.getCmp('observacion').value;
                            if (Ext.isEmpty(strObservacion))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese los valores correspondientes!");
                            }
                            else
                            {
                                
                                connActPrecioTraslado.request
                                    ({
                                        url: url_aprobarSolicitudReubicacion,
                                        method: 'post',
                                        waitMsg: 'Ejecutando...',
                                        timeout: 400000,
                                        params:
                                            {
                                                idSolicitud: rec.get("id_solicitud"),
                                                observacion: strObservacion,
                                                proceso    : "Rechazado"
                                            },
                                        success: function(response) {
                                            var datosRespuestaTn = Ext.JSON.decode(response.responseText);
                                            if (datosRespuestaTn.strStatus == "OK")
                                            {
                                                store.load();
                                                winPrecioTraslado.destroy();
                                                Ext.Msg.alert('Mensaje ', datosRespuestaTn.strMensaje);
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error ', datosRespuestaTn.strMensaje);
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winPrecioTraslado.destroy();
                        }
                    }
                ]
        });

    var winPrecioTraslado = Ext.create('Ext.window.Window',
        {
            title: 'Aprobar solicitud de Reubicación',
            modal: true,
            width: 450,
            closable: true,
            resizable: false,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

/**
 * aprobarSolicitudCEPS
 * 
 * Documentación para el método 'aprobarSolicitudCEPS'.
 *
 * Función que muestra pantalla de aprobación de solicitudes de cambio de equipo por Soporte
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 22-01-2018
 * @since
 */
function aprobarSolicitudCEPS(rec)
{
    storeModelosCpeOnt = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url : url_getModelosCpeOntPorSoporte,
            reader: {
                type: 'json',
                root: 'arrayModelosOnt'
            },
            extraParams: {
                    nombre: '',
                }
        },
        fields:
		[
			{name:'strValueModelo', mapping:'strValueModelo'}
		],
		autoLoad: false
    });

    ModelosCpeOnt = new Ext.form.ComboBox({
        id: 'comboModelosCpeOnt',
        name: 'comboModelosCpeOnt',
        fieldLabel: "Modelo ONT",
        store: storeModelosCpeOnt,
        displayField: 'strValueModelo',
        valueField: 'strValueModelo',
        allowBlank: false,
        height:30,
		width: 250,
        border:0,
        margin:0
    });
    
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    columns: 1
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: 'Información del servicio',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 410
                            },
                        items:
                            [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                    },
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 410
                            },
                        items:
                            [
                                ModelosCpeOnt,
                                {
                                    xtype     : 'textareafield',
                                    id        : 'observacion',
                                    grow      : true,
                                    name      : 'observación',
                                    fieldLabel: 'Observación',
                                    anchor    : '100%',
                                    allowBlank: false
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: '<span style="font-weight:bold; color:black;"> Aprobar </span>',
                        formBind: true,
                        handler: function()
                        {
                            var strObservacion = Ext.getCmp('observacion').value;
                            var strModeloCpe   = Ext.getCmp('comboModelosCpeOnt').value;
                            if (Ext.isEmpty(strObservacion))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese la observación correspondiente!");
                            }
                            if (Ext.isEmpty(strModeloCpe))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese el modelo correspondiente!");
                            }
                            else
                            {
                                
                                connActPrecioTraslado.request
                                    ({
                                        url: url_aprobarSolicitudSoporte,
                                        method: 'post',
                                        waitMsg: 'Ejecutando...',
                                        timeout: 400000,
                                        params:
                                            {
                                                idSolicitud  : rec.get("id_solicitud"),
                                                observacion  : strObservacion,
                                                strModeloOnt : strModeloCpe,
                                                proceso      : "PrePlanificada"
                                            },
                                        success: function(response) {
                                            var datosRespuestaTn = Ext.JSON.decode(response.responseText);
                                            if (datosRespuestaTn.strStatus == "OK")
                                            {
                                                store.load();
                                                winPrecioTraslado.destroy();
                                                Ext.Msg.alert('Mensaje ', datosRespuestaTn.strMensaje);
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error ', datosRespuestaTn.strMensaje);
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                            }
                        }
                    },
                    {
                        text: '<span style="font-weight:bold; color:red;"> Rechazar </span>',
                        handler: function()
                        {
                            var strObservacion = Ext.getCmp('observacion').value;
                            if (Ext.isEmpty(strObservacion))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese la observación correspondiente!");
                            }
                            else
                            {
                                
                                connActPrecioTraslado.request
                                    ({
                                        url: url_aprobarSolicitudSoporte,
                                        method: 'post',
                                        waitMsg: 'Ejecutando...',
                                        timeout: 400000,
                                        params:
                                            {
                                                idSolicitud: rec.get("id_solicitud"),
                                                observacion: strObservacion,
                                                proceso    : "Rechazado"
                                            },
                                        success: function(response) {
                                            var datosRespuestaTn = Ext.JSON.decode(response.responseText);
                                            if (datosRespuestaTn.strStatus == "OK")
                                            {
                                                store.load();
                                                winPrecioTraslado.destroy();
                                                Ext.Msg.alert('Mensaje ', datosRespuestaTn.strMensaje);
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error ', datosRespuestaTn.strMensaje);
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winPrecioTraslado.destroy();
                        }
                    }
                ]
        });

    var winPrecioTraslado = Ext.create('Ext.window.Window',
        {
            title: 'Aprobar solicitud de Cambio de equipo por Soporte',
            modal: true,
            width: 455,
            closable: true,
            resizable: false,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function aprobarTraslado(rec)
{
    btnCancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		      winServiciosTraslado.destroy();
            }
    });

    btnAprobar = Ext.create('Ext.Button', {
        text: '<span style="font-weight:bold; color:black;"> Aprobar </span>',
        formBind: true,
        handler: function()
        {
            connActPrecioTraslado.request
                ({
                    url: url_aprobarSolTraslado,
                    method: 'post',
                    waitMsg: 'Ejecutando...',
                    timeout: 400000,
                    params:
                        {
                            idDetalleSolicitud : rec.get('id_solicitud'),
                            banderaAutorizarSol: "S",
                            idsServiciosTrasladar : rec.get('idsServicioTraslado'),
                            idPuntoSession        : rec.get('id_punto'),
                            precioTrasladoTn      : rec.get('precioTraslado'),
                            descripcionTrasladoTn : rec.get('descripcionTraslado')
                        },
                    success: function(response) {
                        var datosRespuestaTn = Ext.JSON.decode(response.responseText);
                        if (datosRespuestaTn.strStatus == "OK")
                        {
                            store.load();
                            winServiciosTraslado.destroy();
                            Ext.Msg.alert('Mensaje ', datosRespuestaTn.strMensaje);
                        }
                        else
                        {
                            winServiciosTraslado.destroy();
                            Ext.Msg.alert('Error ', datosRespuestaTn.strMensaje);
                        }
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
        }
    });

    btnRechazar = Ext.create('Ext.Button', {
        text: '<span style="font-weight:bold; color:red;"> Rechazar </span>',
        formBind: true,
        handler: function()
        {
            presentarMotivosRechazo(rec);
        }
    });

    storeServiciosTrasladar = new Ext.data.Store({
        total: 'total',
        pageSize: 10,
        proxy: {
            timeout: 9600000,
            type: 'ajax',
            method: 'post',
            url: url_getServiciosATrasladar,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idsServiciosTraslado: rec.get('idsServicioTraslado')
            }
        },
        fields:
        [
            { name:'servicio' , mapping:'servicio' },
            { name:'estado' , mapping:'estado' }
        ]
    });


    //se crea el grid para los datos
    gridServiciosTrasladar = Ext.create('Ext.grid.Panel',
    {
        width: '100%',
        height: 130,
        store: storeServiciosTrasladar,
        loadMask: true,
        frame: false,
        viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        //iconCls: 'icon-grid',
        columns:
        [
            {
                header: 'Servicio',
                dataIndex: 'servicio',
                width: 400,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 100,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeServiciosTrasladar,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        listeners:
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        }
    });



	formPanelServicios = Ext.create('Ext.form.Panel', {
			bodyPadding: 3,
			waitMsgTarget: true,
			height: 700,
			width:580,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				msgTarget: 'side'
			},
			items: [
                {
                    xtype: 'fieldset',
                    defaultType: 'textfield',
                    items: [
                                {
                                    xtype: 'fieldset',
                                    title: 'Informacion del Cliente',
                                    defaultType: 'textfield',
                                    layout: {
                                        type: 'table',
                                        columns: 1,
                                        pack: 'center'
                                    },
                                    items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Razon Social',
                                                name: 'info_razonSocial',
                                                id: 'info_razonSocial',
                                                value: rec.get('cliente'),
                                                width: 400,
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Pto. cliente',
                                                name: 'info_punto',
                                                id: 'info_punto',
                                                value: rec.get('login2'),
                                                width: 400,
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Tipo de negocio',
                                                name: 'info_negocio',
                                                id: 'info_negocio',
                                                value: rec.get('tipoNegocio'),
                                                width: 400,
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                           ]
                                },
                                {
                                    xtype: 'fieldset',
                                    title: 'Informacion Financiera',
                                    defaultType: 'textfield',
                                    layout: {
                                        type: 'table',
                                        columns: 1,
                                        pack: 'center'
                                    },
                                    items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Precio de Traslado',
                                                name: 'info_precioTraslado',
                                                id: 'info_precioTraslado',
                                                value: rec.get('precioTraslado'),
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                            {
                                                xtype     : 'textareafield',
                                                id        : 'info_descripcionTraslado',
                                                grow      : true,
                                                name      : 'info_descripcionTraslado',
                                                fieldLabel: 'Descripcion Traslado',
                                                cols     : 200,
                                                rows     : 3,
                                                value     : rec.get('descripcionTraslado'),
                                                allowBlank: false,
                                                readOnly: true
                                            }
                                           ]
                                },
                                {
                                    xtype: 'fieldset',
                                    defaultType: 'textfield',
                                    title: 'Informacion del Traslado - '+rec.get('loginATrasladar'),
                                    bodyStyle: 'padding:0px',
                                    items: [
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Saldo',
                                                    name: 'info_saldo',
                                                    id: 'info_saldo',
                                                    value: rec.get('saldoPunto'),
                                                    allowBlank: false,
                                                    readOnly: true
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Tiempo espera meses corte',
                                                    name: 'info_tiempo',
                                                    id: 'info_tiempo',
                                                    value: rec.get('tiempoEsperaMeses'),
                                                    allowBlank: false,
                                                    readOnly: true
                                                },
                                                gridServiciosTrasladar
                                           ]
                                }
                            ]
                }
            ]
		 });

    storeServiciosTrasladar.proxy.extraParams = {idsServiciosTraslado: rec.get('idsServicioTraslado')};
    storeServiciosTrasladar.load();

	winServiciosTraslado = Ext.create('Ext.window.Window', {
			title: 'Aprobar Solicitud de Traslado',
			modal: true,
			width: 580,
			height: 600,
			resizable: true,
			layout: 'fit',
			items: [formPanelServicios],
			buttonAlign: 'center',
			buttons:[btnCancelar,btnAprobar,btnRechazar]
	}).show();


}

function presentarMotivosRechazo(rec)
{    
    var boolEsCloudPublic = rec.get('esCloudForm')==='S'?true:false;
    var url = '';
    
    if(boolEsCloudPublic)
    {
        url = url_rechazarSolicitudCloudPublic;
    }
    else
    {
        url = url_rechazarSolTraslado;
    }
    storeMotivoRechazo = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url : boolEsCloudPublic?url_getMotivoRechazoCloud:url_getMotivoRechazo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                }
        },
        fields:
		[
			{name:'id_motivo', mapping:'id_motivo'},
			{name:'nombre_motivo', mapping:'nombre_motivo'}
		],
		autoLoad: false
    });

    comboEstados = new Ext.form.ComboBox({
        id: 'comboMotivoRechazo',
        name: 'comboMotivoRechazo',
        fieldLabel: "Motivo",
        store: storeMotivoRechazo,
        displayField: 'nombre_motivo',
        valueField: 'id_motivo',
        height:30,
		width: 425,
        border:0,
        margin:0
    });
    
    
    
    btnguardar = Ext.create('Ext.Button', {
    text: 'Aceptar',
    cls: 'x-btn-rigth',
    handler: function()
    {
        var strValueMotivoRechazo = Ext.getCmp('comboMotivoRechazo').value;
        win.destroy();

        if(strValueMotivoRechazo == "" || strValueMotivoRechazo == null || strValueMotivoRechazo == " ")
        {
            win.destroy();
            Ext.Msg.alert('Mensaje ',"Seleccione por favor un motivo de rechazo");
        }
        else
        {
            connActPrecioTraslado.request
                ({
                    url: url,
                    method: 'post',
                    waitMsg: 'Ejecutando...',
                    timeout: 400000,
                    params:
                        {
                            idDetalleSolicitud : rec.get('id_solicitud'),
                            idPunto            : rec.get('id_punto'),
                            motivo             : strValueMotivoRechazo,
                            descripcion        : !Ext.isEmpty(Ext.getCmp('descripcion').getValue())?Ext.getCmp('descripcion').getValue():'N/A'
                        },
                    success: function(response) {
                        var datosRespuestaTn = Ext.JSON.decode(response.responseText);
                        if (datosRespuestaTn.strStatus == "OK")
                        {
                            store.load();
                            winServiciosTraslado.destroy();
                            Ext.Msg.alert('Mensaje ', datosRespuestaTn.strMensaje);
                        }
                        else
                        {
                            winServiciosTraslado.destroy();
                            Ext.Msg.alert('Error ', datosRespuestaTn.strMensaje);
                        }
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });        
            }
    }
    });

    btncancelar = Ext.create('Ext.Button', {
                text: 'Cerrar',
                cls: 'x-btn-rigth',
                handler: function() {
                    win.destroy();
                }
        });


    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        layout: 'column',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            msgTarget: 'side'
        },
        items:
        [
            comboEstados
        ]
    });

    win = Ext.create('Ext.window.Window', {
        title: "Rechazar Solicitud",
        closable: false,
        modal: true,
        width: 480,
        height: 120,
        resizable: false,
        layout: 'fit',
        items: [formPanel],
        buttonAlign: 'center',
        buttons:[btnguardar,btncancelar]
    }).show();    
  
}

/**
 * isNumberKey
 * 
 * Documentación para el método 'isNumberKey'.
 *
 * Función que solo permite el ingreso de numeros decimales o enteros en un campo
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 22-01-2018
 * @since
 */
function isNumberKey(txt, evt) {

    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46) {
        //Check if the text already contains the . character
        if (txt.value.indexOf('.') === -1) {
            return true;
        } else {
            return false;
        }
    } else {
        if (charCode > 31
             && (charCode < 48 || charCode > 57))
            return false;
    }
    return true;
}



function verSeguimientoTareaExcedSol(idDetalleSolicitud, IdPunto, Login, Producto, Empresa, Vendedor){ //IdFactibilidad, IdPunto, Login, Vendedor, Producto, Empresa
  
    if(!Vendedor)
    {
        Ext.Msg.alert('Mensaje','EL vendedor no se encuentra registrado!.', function(btn){
            if(btn=='ok'){
                return;
            }
        });
    }
  
    var conn = new Ext.data.Connection({
      listeners: {
          'beforerequest': {
              fn: function (con, opt) {
                  Ext.get(document.body).mask('Loading...');
              },
              scope: this
          },
          'requestcomplete': {
              fn: function (con, res, opt) {
                  Ext.get(document.body).unmask();
              },
              scope: this
          },
          'requestexception': {
              fn: function (con, res, opt) {
                  Ext.get(document.body).unmask();
              },
              scope: this
          }
      }
  });
 
  btncancelar = Ext.create('Ext.Button', {
          text: 'Cerrar',
          cls: 'x-btn-rigth',
          handler: function() {
            winSeguimientoTarea.destroy();													
          }
  });
  
  storeSeguimientoTarea = new Ext.data.Store({ 
      total: 'total',
      async: false,
      autoLoad: true,
      proxy: {
          type: 'ajax',
          url : strUrlGetSeguimientoMaterialesExcedentes,
          reader: {
              type: 'json'              
          },
          extraParams: {
            idDetalleSolicitud: idDetalleSolicitud,
            pantallaDe: 'Solicitudes'				
        }
      },
      fields:
      [
            {name:'id', mapping:'id'},
            {name:'observacion', mapping:'observacion'},
            {name:'estado', mapping:'estado'},
            {name:'usrCreacion', mapping:'usrCreacion'},
            {name:'feCreacion', mapping:'feCreacion'}					
      ]
  });
  gridSeguimiento = Ext.create('Ext.grid.Panel', {
      id:'gridSeguimiento',
      store: storeSeguimientoTarea,		
      columnLines: true,
      columns: [
          {
                id: 'observacion',
                header: 'Observacion',
                dataIndex: 'observacion',
                width:400,
                sortable: true						 
          },
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width:80,
                sortable: true						 
          },
            {
                id: 'usrCreacion',
                header: 'User Creación',
                dataIndex: 'usrCreacion',
                width:80,
                sortable: true						 
          },
            {
                id: 'feCreacion',
                header: 'Fecha Creación',
                dataIndex: 'feCreacion',
                width:120,
                sortable: true						 
          }
      ],		
      width: 700,
      height: 175,
      listeners:{
                          itemdblclick: function( view, record, item, index, eventobj, obj ){
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
                          viewready: function (grid) {
                              var view = grid.view;

                              // record the current cellIndex
                              grid.mon(view, {
                                  uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                                      grid.cellIndex = cellIndex;
                                      grid.recordIndex = recordIndex;
                                  }
                              });

                              grid.tip = Ext.create('Ext.tip.ToolTip', {
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

  
  btnguardar3 = Ext.create('Ext.Button', {        
          text: 'Guardar',
          cls: 'x-btn-rigth',
          handler: function() {
              var valorSeguimiento = Ext.getCmp('seguimiento').value;
              winSeguimientoTarea.destroy();
              conn.request({
                  method: 'POST',
                  params :{
                      id_factibilidad: idDetalleSolicitud,
                      id_punto: IdPunto,
                      login: Login,
                      vendedor: Vendedor,
                      producto: Producto,
                      empresa: Empresa,
                      pantallaDe: 'Solicitudes',
                      seguimiento: valorSeguimiento
                  },
                  url: strUrlIngresarSeguimientoMaterialesExcedentes,
                  success: function(response){
                      var json = Ext.JSON.decode(response.responseText);
                      if(json.mensaje != "cerrada")
                      {
                          Ext.Msg.alert('Mensaje','Se ingreso el seguimiento.', function(btn){
                              if(btn=='ok'){
                                  return;
                              }
                          });
                      }
                      else
                      {
                          Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                      }
                  },
                  failure: function(rec, op) {
                      var json = Ext.JSON.decode(op.response.responseText);
                      Ext.Msg.alert('Alerta ',json.mensaje);
                  }
          });
          }
  });
  

  btnaprobar = Ext.create('Ext.Button', {        
    text: 'Aprobar',
    cls: 'x-btn-rigth',
    handler: function() {
        var valorSeguimiento = Ext.getCmp('seguimiento').value;
        winSeguimientoTarea.destroy();
        conn.request({
            method: 'POST',
            params :{
                id: idDetalleSolicitud ,
                observacion: valorSeguimiento
            },
            url: strUrlAprobarSeguimientoMaterialesExcedentes,
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                if(json.mensaje != "cerrada")
                {
                    Ext.Msg.alert('Mensaje','Se aprobó el seguimiento.', function(btn){
                        if(btn=='ok'){
                            return;
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Alerta ',"El seguimiento se encuentra Cerrado, por favor consultela nuevamente");
                }
            },
            failure: function(rec, op) {
                var json = Ext.JSON.decode(op.response.responseText);
                Ext.Msg.alert('Alerta ',json.mensaje);
            }
        });
    }
});


btnrechazar = Ext.create('Ext.Button', {        
    text: 'Rechazar',
    cls: 'x-btn-rigth',
    handler: function() {
        var valorSeguimiento = Ext.getCmp('seguimiento').value;
        winSeguimientoTarea.destroy();
        conn.request({
            method: 'POST',
            params :{
                id: idDetalleSolicitud ,
                observacion: valorSeguimiento
            },
            url: strUrlRechazarSeguimientoMaterialesExcedentes,
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                if(json.mensaje != "cerrada")
                {
                    Ext.Msg.alert('Mensaje','Se rechazó el seguimiento.', function(btn){
                        if(btn=='ok'){
                            return;
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Alerta ',"El seguimiento se encuentra Cerrado, por favor consultela nuevamente");
                }
            },
            failure: function(rec, op) {
                var json = Ext.JSON.decode(op.response.responseText);
                Ext.Msg.alert('Alerta ',json.mensaje);
            }
    });
    }
});




  btncancelar3 = Ext.create('Ext.Button', {
          text: 'Cerrar',
          cls: 'x-btn-rigth',
          handler: function() {
              winSeguimientoTarea.destroy();
          }
  });

  formPanel3 = Ext.create('Ext.form.Panel', {    
    waitMsgTarget: true,
    height: 140,
    width: 700,
    layout: 'fit',
    fieldDefaults: {
        labelAlign: 'left',
        labelWidth: 140,
        msgTarget: 'side'
    },

    items: [{
        xtype: 'fieldset',
        title: 'Información',
        defaultType: 'textfield',
        items: [
            {
                xtype: 'textarea',
                fieldLabel: 'Seguimiento:',
                id: 'seguimiento',
                name: 'seguimiento',
                rows: 4,
                cols: 70
            }
        ]
    }]
 });


  formPanelSeguimiento = Ext.create('Ext.form.Panel', {
          waitMsgTarget: true,
          height: 200,
          width:700,
          layout: 'fit',
          fieldDefaults: {
              labelAlign: 'left',
              msgTarget: 'side'
          },

          items: [{
              xtype: 'fieldset',				
              defaultType: 'textfield',
              items: [					
                  gridSeguimiento, formPanel3
              ]
          }]
  });


  winSeguimientoTarea = Ext.create('Ext.window.Window', {
          title: 'Historial Solicitud',
          modal: true,
          width: 750,
          height: 400,
          resizable: true,
          layout: 'fit',
          items: [formPanelSeguimiento],
          buttonAlign: 'center',
          buttons:[btnguardar3,btncancelar3]
  }).show();       
}



/**
 * Documentación para el método 'gestionarSolicitudServicio'.
 *
 * Función que muestra pantalla de aprobación de solicitudes de servicio
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 28-10-2018
 */
function gestionarSolicitudServicio(rec)
{
    var storeServiciosAsociados = new Ext.data.Store({
        total: 'intTotal',
        pageSize: 25,
        proxy: {
            timeout: 400000,
            type: 'ajax',
            method: 'post',
            url: strUrlGetInfoSolicitudesServicio,
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayResultado'
            },
            extraParams: {
                idSolicitud: rec.get('id_solicitud')
            }
        },
        fields:
        [
            { name:'loginPuntoAsociado', mapping:'loginPuntoAsociado'},
            { name:'estadoServicioAsociado', mapping:'estadoServicioAsociado'}
        ]
    });


    var gridServiciosAsociados = Ext.create('Ext.grid.Panel',
    {
        width: '100%',
        height: 130,
        store: storeServiciosAsociados,
        loadMask: true,
        frame: false,
        viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        columns:
        [
            {
                header: 'Login',
                dataIndex: 'loginPuntoAsociado',
                width: 200,
                sortable: true
            },
            {
                header: 'Estado del Servicio',
                dataIndex: 'estadoServicioAsociado',
                width: 150,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeServiciosAsociados,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        listeners:
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        }
    });
    
    storeServiciosAsociados.load();

    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    columns: 1
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: 'Información de la solicitud',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 410
                            },
                        items:
                            [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textareafield',
                                    fieldLabel: 'Descripción',
                                    name: 'observacionSolicitud',
                                    id: 'observacionSolicitud',
                                    value: rec.get("observacion2"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Puntos asociados',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 410
                            },
                        items:
                            [
                                {
                                    xtype: 'numberfield',
                                    id: 'numServiciosClienteContrato',
                                    name: 'numServiciosClienteContrato',
                                    fieldLabel: 'Número Total de Cuentas del Cliente',
                                    labelWidth: 225,
                                    anchor: '70%'
                                },
                                gridServiciosAsociados
                            ]
                    },
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 410
                            },
                        items:
                            [
                                {
                                    xtype: 'textareafield',
                                    id: 'observacion',
                                    name: 'observacion',
                                    fieldLabel: 'Observación',
                                    anchor: '90%',
                                    allowBlank: false
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: '<span style="font-weight:bold; color:black;"> Aprobar </span>',
                        formBind: true,
                        handler: function ()
                        {
                            var strObservacion              = Ext.getCmp('observacion').value;
                            var numTotalServiciosTelcoHome  = Ext.getCmp('numServiciosClienteContrato').value;
                            if (Ext.isEmpty(strObservacion) || Ext.isEmpty(numTotalServiciosTelcoHome))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese los valores correspondientes!");
                            }
                            else
                            {
                                Ext.Msg.alert('Mensaje','Al aprobar la solicitud, todos los servicios asociados pasarán a estado Pre-servicio<br>'
                                              +'Está seguro que desea aprobar la solicitud?', function(btn){
                                    if(btn=='ok'){
                                        connActPrecioTraslado.request
                                        ({
                                            url: strUrlGestionarSolicitudServicio,
                                            method: 'post',
                                            waitMsg: 'Ejecutando...',
                                            timeout: 400000,
                                            params:
                                                {
                                                    idSolicitud: rec.get("id_solicitud"),
                                                    observacion: strObservacion,
                                                    accion: "aprobar",
                                                    numTotalServiciosTelcoHome: numTotalServiciosTelcoHome
                                                },
                                            success: function (response) {
                                                var datosRespuesta = Ext.JSON.decode(response.responseText);
                                                if (datosRespuesta.strStatus == "OK")
                                                {
                                                    store.load();
                                                    winSolicitudServicio.destroy();
                                                    Ext.Msg.alert('Mensaje ', datosRespuesta.strMensaje);
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Error ', datosRespuesta.strMensaje);
                                                }
                                            },
                                            failure: function (result)
                                            {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: '<span style="font-weight:bold; color:red;"> Rechazar </span>',
                        formBind: true,
                        handler: function ()
                        {
                            var strObservacion = Ext.getCmp('observacion').value;
                            if (Ext.isEmpty(strObservacion))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese los valores correspondientes!");
                            } 
                            else
                            {
                                Ext.Msg.alert('Mensaje','Al rechazar la solicitud, se rechazarán todos los servicios asociados<br>'
                                              +'Está seguro que desea rechazar la solicitud?', function(btn){
                                    if(btn=='ok'){
                                        connActPrecioTraslado.request
                                        ({
                                            url: strUrlGestionarSolicitudServicio,
                                            method: 'post',
                                            waitMsg: 'Ejecutando...',
                                            timeout: 400000,
                                            params:
                                                {
                                                    idSolicitud: rec.get("id_solicitud"),
                                                    observacion: strObservacion,
                                                    accion: "rechazar"
                                                },
                                            success: function (response) {
                                                var datosRespuesta = Ext.JSON.decode(response.responseText);
                                                if (datosRespuesta.strStatus == "OK")
                                                {
                                                    store.load();
                                                    winSolicitudServicio.destroy();
                                                    Ext.Msg.alert('Mensaje ', datosRespuesta.strMensaje);
                                                } else
                                                {
                                                    Ext.Msg.alert('Error ', datosRespuesta.strMensaje);
                                                }
                                            },
                                            failure: function (result)
                                            {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function ()
                        {
                            winSolicitudServicio.destroy();
                        }
                    }
                ]
        });

    var winSolicitudServicio = Ext.create('Ext.window.Window',
        {
            title: 'Gestionar solicitud de servicio',
            modal: true,
            width: 450,
            closable: true,
            resizable: false,
            layout: 'fit',
            items: [formPanel]
        }).show();
}
    
function aprobarContratoCloudPublic(rec)
{
    var btnCancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		      winServiciosTraslado.destroy();
            }
    });

    var btnAprobar = Ext.create('Ext.Button', {
        text: '<span style="font-weight:bold; color:black;"> Aprobar </span>',
        formBind: true,
        handler: function()
        {
            connActPrecioTraslado.request
                ({
                    url: url_aprobarSolicitudCloudPublic,
                    method: 'post',
                    waitMsg: 'Aprobando Solicitud...',
                    timeout: 400000,
                    params:
                        {
                            idDetalleSolicitud : rec.get('id_solicitud'),                            
                            idPunto            : rec.get('id_punto'),                            
                            descripcion        : Ext.getCmp('descripcion')
                        },
                    success: function(response) 
                    {
                        var datosRespuestaTn = Ext.JSON.decode(response.responseText);
                        if (datosRespuestaTn.strStatus === "OK")
                        {
                            store.load();
                            winServiciosTraslado.destroy();
                            Ext.Msg.alert('Mensaje ', datosRespuestaTn.strMensaje);
                        }
                        else
                        {
                            winServiciosTraslado.destroy();
                            Ext.Msg.alert('Error ', datosRespuestaTn.strMensaje);
                        }
                    },
                    failure: function(result)
                    {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
        }
    });

    var btnRechazar = Ext.create('Ext.Button', {
        text: '<span style="font-weight:bold; color:red;"> Rechazar </span>',
        formBind: true,
        handler: function()
        {
            presentarMotivosRechazo(rec);
        }
    });   

	var formPanelServicios = Ext.create('Ext.form.Panel', {
			bodyPadding: 3,
			waitMsgTarget: true,
			height: 250,
			width:580,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				msgTarget: 'side'
			},
			items: [
                {
                    xtype: 'fieldset',
                    defaultType: 'textfield',
                    items: [                                
                        {
                            xtype: 'fieldset',
                            defaultType: 'textfield',
                            title: '<b>Detalle de Aprobación de Contrato</b>',
                            bodyStyle: 'padding:0px',
                            items: 
                                [
                                    {
                                        xtype: 'textarea',
                                        fieldLabel: '<b>Descripción</b>',
                                        name: 'descripcion',
                                        id: 'descripcion',                                        
                                        width:400,
                                        height:100
                                    }
                               ]
                        }
                    ]
                }
            ]
		 });
   
	winServiciosTraslado = Ext.create('Ext.window.Window', {
			title: 'Aprobar Documentación para Servicio Cloud Public',
			modal: true,
			width: 580,
			height: 250,
			resizable: true,
			layout: 'fit',
			items: [formPanelServicios],
			buttonAlign: 'center',
			buttons:[btnCancelar,btnAprobar,btnRechazar]
	}).show();
}


function subirMultipleAdjuntosMateriales(idDetalleSolicitud, idServicio)
{
    var id_tarea = idDetalleSolicitud;
    var id_servicio = idServicio;

    var panelMultiupload = Ext.create('widget.multiupload',{ fileslist: [] });
    var formPanel = Ext.create('Ext.form.Panel',
     {
        width: 500,
        frame: true,
        bodyPadding: '10 10 0',
        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },
        items: [panelMultiupload],
        buttons: [{
            text: 'Subir',
            handler: function()
            {
                var form = this.up('form').getForm();
                if(form.isValid())
                {
                    if(numArchivosSubidos>0)
                    {
                        form.submit({
                            url: url_multipleFileUpload,
                            params :{
                              idSolicitud    : id_tarea,
                              servicio     : id_servicio,
                              origenMateriales: 'S'
                            },
                            waitMsg: 'Procesando Archivo...',
                            success: function(fp, o)
                            {
                                Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
                                    if(btn=='ok')
                                    {
                                        numArchivosSubidos=0;
                                        win.destroy();
                                          
                                    }
                                });
                            },
                            failure: function(fp, o) {
                              Ext.Msg.alert("Alerta",o.result.respuesta);
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert("Mensaje", "No existen archivos para subir", function(btn){
                            if(btn=='ok')
                            {
                                numArchivosSubidos=0;
                                win.destroy();
                            }
                        });
                    }
                    
                }
            }
        },
        {
            text: 'Cancelar',
            handler: function() {
                numArchivosSubidos=0;
                win.destroy();
            }
        }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Archivos Tarea',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    
}


function presentarDocumentosMaterialesExcedentes(rec)
{
    var id_factibilidad           = rec.data.id_factibilidad;
    var id_servicio           = rec.data.id_servicio;
	
   storeDocumentosMateriales = new Ext.data.Store({ 
        pageSize: 1100,
        proxy: {
            type: 'ajax',
            url : url_documentosMaterialesExced,
            reader: {
                type : 'json'
            },
            extraParams: {
                idFactibilidad             : id_factibilidad,
                idServicio                 : id_servicio
            }
        },
        fields:
		[
            {name:'idDocumento',            mapping:'idDocumento'},
            {name:'ubicacionLogica',        mapping:'ubicacionLogica'},
            {name:'feCreacion',             mapping:'feCreacion'},
            {name:'usrCreacion',            mapping:'usrCreacion'},
            {name:'linkVerDocumento',       mapping:'linkVerDocumento'},
            {name:'boolEliminarDocumento',  mapping:'boolEliminarDocumento'}
		],
        autoLoad: true,
		listeners: {
			beforeload: function(sender, options )
			{
				Ext.MessageBox.show({
				   msg: 'Cargando los datos, Por favor espere!!',
				   progressText: 'Saving...',
				   width:300,
				   wait:true,
				   waitConfig: {interval:200}
				});
			},
			load: function(sender, node, records) {
				gridDocumentosCaso = "";
				
				if(storeDocumentosMateriales.getCount()>0){														
					
                    //grid de documentos por Caso
                    gridDocumentosCaso = Ext.create('Ext.grid.Panel', {
                        id:'gridMaterialesPunto',
                        store: storeDocumentosMateriales,
                        columnLines: true,
                        columns: [{
                            header   : 'Nombre Archivo',
                            dataIndex: 'ubicacionLogica',
                            width    : 260
                        },
                        {
                            header   : 'Usr. Creación',
                            dataIndex: 'usrCreacion',
                            width    : 80
                        },
                        {
                            header   : 'Fecha de Carga',
                            dataIndex: 'feCreacion',
                            width    : 120
                        },
                        {
                            xtype: 'actioncolumn',
                            header: 'Acciones',
                            width: 190,
                            items:
                            [
                                {
                                    iconCls: 'button-grid-show',
                                    tooltip: 'Ver Archivo Digital',
                                    handler: function(grid, rowIndex, colIndex) {
                                        var rec         = storeDocumentosMateriales.getAt(rowIndex);
                                        verArchivoDigital(rec);
                                    }
                                },
                                {
                                    getClass: function(v, meta, rec) 
                                    {
                                        var strClassButton  = 'button-grid-delete';
                                        if(!rec.get('boolEliminarDocumento'))
                                        {
                                            strClassButton = ""; 
                                        }

                                        if (strClassButton == "")
                                        {
                                            this.items[0].tooltip = ''; 
                                        }   
                                        else
                                        {
                                            this.items[0].tooltip = 'Eliminar Archivo Digital';
                                        }
                                        return strClassButton;

                                    },
                                    tooltip: 'Eliminar Archivo Digital',
                                    handler: function(grid, rowIndex, colIndex) 
                                    {
                                        var rec                 = storeDocumentosMateriales.getAt(rowIndex);
                                        var idDocumento         = rec.get('idDocumento');
                                        var strClassButton      = 'button-grid-delete';
                                        if(!rec.get('boolEliminarDocumento'))
                                        {
                                            strClassButton = ""; 
                                        }

                                        if (strClassButton != "" )
                                        {
                                            eliminarAdjunto(storeDocumentosMateriales,idDocumento);
                                                
                                        } 
                                    }
                                }
                            ]
                        }
                    ],
                        viewConfig:{
                            stripeRows:true,
                            enableTextSelection: true
                        },
                        frame : true,
                        height: 200
                    });


                function verArchivoDigital(rec)
                {
                    var rutaFisica = rec.get('linkVerDocumento');
                    var posicion = rutaFisica.indexOf('/public')
                    window.open(rutaFisica.substring(posicion,rutaFisica.length));
                }

                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding  : 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget : 'side'
                    },
                    items: [

                    {
                        xtype      : 'fieldset',
                        title      : '',
                        defaultType: 'textfield',

                        defaults: {
                            width: 550
                        },
                        items: [

                            gridDocumentosCaso

                        ]
                    }
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title   : 'Documentos Cargados',
                    modal   : true,
                    width   : 580,
                    closable: true,
                    layout  : 'fit',
                    items   : [formPanel]
                }).show();                    
                
                Ext.MessageBox.hide();
				}//FIN IF TIENE DATA
				else
				{	
                    Ext.Msg.show({
                        title  :'Mensaje',
                        msg    : 'La tarea seleccionada no posee archivos adjuntos.',
                        buttons: Ext.Msg.OK,
                        animEl : 'elId',
                    });
				}
			
			}
		}
    });

}
    /**
     * Documentación para la función 'gestionarSolicitudServicioMPLS'.
     *
     * Función que muestra pantalla de aprobación de solicitudes de servicio con tipo de red MPLS.
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 10-10-2019
     */
    function gestionarSolicitudServicioMPLS(rec)
    {
        var objformPanel = Ext.create('Ext.form.Panel',
            {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults:
                    {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                layout:
                    {
                        type: 'table',
                        columns: 1
                    },
                items:
                    [
                        {
                            xtype: 'fieldset',
                            title: 'Información de la solicitud',
                            defaultType: 'textfield',
                            defaults:
                                {
                                    width: 410
                                },
                            items:
                                [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Cliente',
                                        name: 'info_cliente',
                                        id: 'info_cliente',
                                        value: rec.get("cliente"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Login',
                                        name: 'info_login',
                                        id: 'info_login',
                                        value: rec.get("login2"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Ciudad',
                                        name: 'info_ciudad',
                                        id: 'info_ciudad',
                                        value: rec.get("ciudad"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Direccion',
                                        name: 'info_direccion',
                                        id: 'info_direccion',
                                        value: rec.get("direccion"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Sector',
                                        name: 'info_nombreSector',
                                        id: 'info_nombreSector',
                                        value: rec.get("nombreSector"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textareafield',
                                        fieldLabel: 'Descripción',
                                        name: 'observacionSolicitud',
                                        id: 'observacionSolicitud',
                                        value: rec.get("observacion2"),
                                        allowBlank: false,
                                        readOnly: true
                                    }
                                ]
                        },
                        {
                            xtype: 'fieldset',
                            title: '',
                            defaultType: 'textfield',
                            defaults:
                                {
                                    width: 410
                                },
                            items:
                                [
                                    {
                                        xtype: 'textareafield',
                                        id: 'observacion',
                                        name: 'observacion',
                                        fieldLabel: 'Observación',
                                        anchor: '90%',
                                        allowBlank: false
                                    }
                                ]
                        }
                    ],
                buttons:
                    [
                        {
                            text: '<span style="font-weight:bold; color:black;"> Aprobar </span>',
                            formBind: true,
                            handler: function ()
                            {
                                var strObservacion = Ext.getCmp('observacion').value;
                                if (Ext.isEmpty(strObservacion))
                                {
                                    Ext.Msg.alert("Alerta", "Por favor ingrese los valores correspondientes!");
                                }
                                else
                                {
                                    Ext.Msg.confirm('Mensaje','Al aprobar la solicitud, el servicio pasará a estado Pre-servicio<br>'
                                                +'¿Está seguro que desea aprobar la solicitud?', function(strRespuesta){
                                        if(strRespuesta=='yes')
                                        {
                                            $.ajax({
                                                type: "POST",
                                                data: "idSolicitud=" + rec.get("id_solicitud") + "&observacion=" + strObservacion + "&accion=aprobar",
                                                url: strUrlGestionarSolServicioMpls,
                                                beforeSend: function()
                                                {
                                                    Ext.MessageBox.wait("Ejecutando...");
                                                },
                                                success: function(objRespuesta)
                                                {
                                                    if (objRespuesta.strStatus === "OK")
                                                    {
                                                        store.load();
                                                        objVentanaSolicitudServicioMPLS.destroy();
                                                        Ext.MessageBox.hide();
                                                        Ext.Msg.alert('Mensaje ', objRespuesta.strMensaje);
                                                    }
                                                    else
                                                    {
                                                        Ext.MessageBox.hide();
                                                        Ext.Msg.alert('Error ', objRespuesta.strMensaje);
                                                    }
                                                },
                                                failure: function(objRespuesta)
                                                {
                                                    Ext.MessageBox.hide();
                                                    Ext.Msg.alert('Error ', 'Error: ' + objRespuesta.statusText);
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        },
                        {
                            text: '<span style="font-weight:bold; color:red;"> Rechazar </span>',
                            formBind: true,
                            handler: function ()
                            {
                                var strObservacion = Ext.getCmp('observacion').value;
                                if (Ext.isEmpty(strObservacion))
                                {
                                    Ext.Msg.alert("Alerta", "Por favor ingrese los valores correspondientes.");
                                } 
                                else
                                {
                                    Ext.Msg.confirm('Mensaje','Al rechazar la solicitud, el servicio pasará a estado Eliminado<br>'
                                                +'¿Está seguro que desea rechazar la solicitud?', function(strRespuesta){
                                        if(strRespuesta=='yes')
                                        {
                                            $.ajax({
                                                type: "POST",
                                                data: "idSolicitud=" + rec.get("id_solicitud") + "&observacion=" + strObservacion + "&accion=rechazar",
                                                url: strUrlGestionarSolServicioMpls,
                                                beforeSend: function()
                                                {
                                                    Ext.MessageBox.wait("Ejecutando...");
                                                },
                                                success: function(objRespuesta)
                                                {
                                                    if (objRespuesta.strStatus === "OK")
                                                    {
                                                        store.load();
                                                        objVentanaSolicitudServicioMPLS.destroy();
                                                        Ext.Msg.alert('Mensaje ', objRespuesta.strMensaje);
                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.alert('Error ', objRespuesta.strMensaje);
                                                    }
                                                },
                                                failure: function(objRespuesta)
                                                {
                                                    Ext.Msg.alert('Error ', 'Error: ' + objRespuesta.statusText);
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        },
                        {
                            text: 'Cancelar',
                            handler: function ()
                            {
                                objVentanaSolicitudServicioMPLS.destroy();
                            }
                        }
                    ]
            });

        var objVentanaSolicitudServicioMPLS = Ext.create('Ext.window.Window',
        {
            title     : 'Gestionar solicitud de servicio con tipo de red MPLS',
            modal     : true,
            width     : 450,
            closable  : true,
            resizable : false,
            layout    : 'fit',
            items     : [objformPanel]
        }).show();
    }
