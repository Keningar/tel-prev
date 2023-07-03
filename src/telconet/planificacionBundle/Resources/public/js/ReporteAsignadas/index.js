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
    DTFechaDesdeAsignacion = new Ext.form.DateField({
        id: 'fechaDesdeAsignacion',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaAsignacion = new Ext.form.DateField({
        id: 'fechaHastaAsignacion',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    
    //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
    var iniHtml =   'Asignado a: '+
                    '&nbsp;&nbsp;<br/><br/>'+
                    '<input type="radio" onchange="cambiarTipoResponsable_reporte(this.value);" checked="" value="todos" name="tipoResponsable">&nbsp;Todos' + 
                    '&nbsp;&nbsp;'+
                    '<input type="radio" onchange="cambiarTipoResponsable_reporte(this.value);" value="empleado" name="tipoResponsable">&nbsp;Empleado' + 
                    '&nbsp;&nbsp;'+
                    '<input type="radio" onchange="cambiarTipoResponsable_reporte(this.value);" value="cuadrilla" name="tipoResponsable">&nbsp;Cuadrilla'+
                    '&nbsp;&nbsp;'+
                    '<input type="radio" onchange="cambiarTipoResponsable_reporte(this.value);" value="empresaExterna" name="tipoResponsable">&nbsp;Contratista'+
                    '';

    RadiosTiposResponsable =  Ext.create('Ext.Component', {
        html: iniHtml,
        width: 325,
        style: { color: '#000000' }
    });
    //******** html vacio...
    var iniHtmlVacio1 = '';           
    Vacio1 =  Ext.create('Ext.Component', {
        id: 'item_vacio',
        name: 'item_vacio',
        html: iniHtmlVacio1,
        width: 325,
        layout: 'anchor',
        labelAlign: 'top',
        style: { color: '#000000' }
    });
        
    // **************** EMPLEADOS ******************
    Ext.define('EmpleadosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_empleado', type:'int'},
            {name:'nombre_empleado', type:'string'}
        ]
    });           
    eval("var storeEmpleados = Ext.create('Ext.data.Store', { "+
        "  id: 'storeEmpleados', "+
        "  model: 'EmpleadosList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : '../../planificar/asignar_responsable/getEmpleados',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_empleados = new Ext.form.ComboBox({
        id: 'cmb_empleado',
        name: 'cmb_empleado',
        fieldLabel: "Empleados",
        anchor: '100%',
        queryMode:'remote',
        width: 300,
        emptyText: 'Seleccione Empleado',
        store: eval("storeEmpleados"),
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        layout: 'anchor',
        labelAlign: 'top',
        disabled: false
    });


    // ****************  EMPRESA EXTERNA  ******************
    Ext.define('EmpresaExternaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_empresa_externa', type:'int'},
            {name:'nombre_empresa_externa', type:'string'}
        ]
    });

    eval("var storeEmpresaExterna = Ext.create('Ext.data.Store', { "+
        "  id: 'storeEmpresaExterna', "+
        "  model: 'EmpresaExternaList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : '../../planificar/asignar_responsable/getEmpresasExternas',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_empresas_externas = new Ext.form.ComboBox({
        id: 'cmb_empresa_externa',
        name: 'cmb_empresa_externa',
        fieldLabel: "Contratista",
        anchor: '100%',
        queryMode:'remote',
        width: 300,
        emptyText: 'Seleccione Contratista',
        store: eval("storeEmpresaExterna"),
        displayField: 'nombre_empresa_externa',
        valueField: 'id_empresa_externa',
        layout: 'anchor',
        labelAlign: 'top',
        disabled: true
    });


    // **************** CUADRILLAS ******************
    Ext.define('CuadrillasList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_cuadrilla', type:'int'},
            {name:'nombre_cuadrilla', type:'string'}
        ]
    });            
    eval("var storeCuadrillas = Ext.create('Ext.data.Store', { "+
        "  id: 'storeCuadrillas', "+
        "  model: 'CuadrillasList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : '../../planificar/asignar_responsable/getCuadrillas',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    combo_cuadrillas = new Ext.form.ComboBox({
        id: 'cmb_cuadrilla',
        name: 'cmb_cuadrilla',
        fieldLabel: "Cuadrilla",
        anchor: '100%',
        queryMode:'remote',
        width: 300,
        emptyText: 'Seleccione Cuadrilla',
        store: eval("storeCuadrillas"),
        displayField: 'nombre_cuadrilla',
        valueField: 'id_cuadrilla',
        layout: 'anchor',
        labelAlign: 'top',
        disabled: true 
    });  

            
    store = new Ext.data.Store({ 
        pageSize: 19,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'grid',
		    timeout: 120000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                fechaDesdeAsignacion: '',
                fechaHastaAsignacion: '',
                login2: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                estado: 'Todos'
            }
        },
        fields:
                [
                    {name:'id_factibilidad', mapping:'id_factibilidad'},
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'id_punto', mapping:'id_punto'},
                    {name:'id_orden_trabajo', mapping:'id_orden_trabajo'},
                    {name:'id_detalle', mapping:'id_detalle'},
                    {name:'id_detalle_asignacion', mapping:'id_detalle_asignacion'},
                    {name:'cliente', mapping:'cliente'},
                    {name:'vendedor', mapping:'vendedor'},
                    {name:'login2', mapping:'login2'},
                    {name:'producto', mapping:'producto'},
                    {name:'coordenadas', mapping:'coordenadas'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'ciudad', mapping:'ciudad'},
                    {name:'nombreSector', mapping:'nombreSector'},
                    {name:'rutaCroquis', mapping:'rutaCroquis'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'feSolicitaPlanificacion', mapping:'feSolicitaPlanificacion'},
                    {name:'fechaPlanificacionReal', mapping:'fechaPlanificacionReal'},
                    {name:'fePlanificada', mapping:'fePlanificada'},
                    {name:'feAsignada', mapping:'feAsignada'},
                    {name:'usrPlanifica', mapping:'usrPlanifica'},
                    {name:'usrAsigna', mapping:'usrAsigna'},
                    {name:'nombreTarea', mapping:'nombreTarea'},
                    {name:'nombreAsignado', mapping:'nombreAsignado'}, 
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
	var permiso = $("#ROLE_145-37");
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
                  id: 'id_detalle',
                  header: 'IdDetalle',
                  dataIndex: 'id_detalle',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_detalle_asignacion',
                  header: 'IdDetalleAsignacion',
                  dataIndex: 'id_detalle_asignacion',
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
                  width: 170,
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
                  header: 'Producto',
                  dataIndex: 'producto',
                  width: 160,
                  sortable: true
                },  
                {
                  id: 'ciudad',
                  header: 'Ciudad',
                  dataIndex: 'ciudad',
                  width: 55,
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
                  width: 160,
                  sortable: true
                },   
                {
                  id: 'feAsignada',
                  header: 'Fecha Asignacion',
                  dataIndex: 'feAsignada',
                  width: 100,
                  sortable: true
                },
                {
                  id: 'usrAsigna',
                  header: 'Usr Asigna',
                  dataIndex: 'usrAsigna',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'nombreTarea',
                  header: 'Tarea',
                  dataIndex: 'nombreTarea',
                  width: 140,
                  sortable: true
                },
                {
                  id: 'nombreAsignado',
                  header: 'Asignado',
                  dataIndex: 'nombreAsignado',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'estado',
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 90,
                  sortable: true
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
                    {html:"Fecha Planificacion:",border:false,width:325},
                    {html:"&nbsp;",border:false,width:150},
                    {html:"Fecha Asignacion:",border:false,width:325},
                    {html:"&nbsp;",border:false,width:200},
                    
                   
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaDesdePlanif,
                    {html:"&nbsp;",border:false,width:150},
                    DTFechaDesdeAsignacion,
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaHastaPlanif,
                    {html:"&nbsp;",border:false,width:150},
                    DTFechaHastaAsignacion,
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
                            ['Asignada','Asignada'],
                            ['AsignadoTarea','AsignadoTarea']
                        ],
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:200},   
                    
                    {html:"&nbsp;",border:false,width:200},
                    RadiosTiposResponsable, 
                    {html:"&nbsp;",border:false,width:150},
                    Vacio1, 
                    combo_empleados, 
                    combo_cuadrillas, 
                    combo_empresas_externas, 
                    {html:"&nbsp;",border:false,width:200}
                    
                ],	
        renderTo: 'filtro'
    }); 
    
    Ext.getCmp('item_vacio').setVisible(true);
    Ext.getCmp('cmb_empleado').setVisible(false);
    Ext.getCmp('cmb_cuadrilla').setVisible(false);
    Ext.getCmp('cmb_empresa_externa').setVisible(false);
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function cambiarTipoResponsable_reporte(valor)
{
    if(valor == "todos")
    {
        Ext.getCmp('item_vacio').setVisible(true);        
        Ext.getCmp('cmb_empleado').setVisible(false);
        Ext.getCmp('cmb_cuadrilla').setVisible(false);
        Ext.getCmp('cmb_empresa_externa').setVisible(false);
        
        Ext.getCmp('item_vacio').setDisabled(false);        
        Ext.getCmp('cmb_empleado').setDisabled(true);
        Ext.getCmp('cmb_cuadrilla').setDisabled(true);
        Ext.getCmp('cmb_empresa_externa').setDisabled(true);
            
       // eval("storeEmpleados_"+i+".load();");
    }
    else if(valor == "empleado")
    {
        Ext.getCmp('item_vacio').setVisible(false);        
        Ext.getCmp('cmb_empleado').setVisible(true);
        Ext.getCmp('cmb_cuadrilla').setVisible(false);
        Ext.getCmp('cmb_empresa_externa').setVisible(false);
        
        Ext.getCmp('item_vacio').setDisabled(true);        
        Ext.getCmp('cmb_empleado').setDisabled(false);
        Ext.getCmp('cmb_cuadrilla').setDisabled(true);
        Ext.getCmp('cmb_empresa_externa').setDisabled(true);
            
       // eval("storeEmpleados_"+i+".load();");
    }
    else if(valor == "cuadrilla")
    {
        Ext.getCmp('item_vacio').setVisible(false);
        Ext.getCmp('cmb_empleado').setVisible(false);
        Ext.getCmp('cmb_cuadrilla').setVisible(true);
        Ext.getCmp('cmb_empresa_externa').setVisible(false);
        
        Ext.getCmp('item_vacio').setDisabled(true);  
        Ext.getCmp('cmb_empleado').setDisabled(true);
        Ext.getCmp('cmb_cuadrilla').setDisabled(false);
        Ext.getCmp('cmb_empresa_externa').setDisabled(true);
        
        
       // eval("storeCuadrillas_"+i+".load();");
    }
    else if(valor == "empresaExterna")
    { 
        Ext.getCmp('item_vacio').setVisible(false);
        Ext.getCmp('cmb_empleado').setVisible(false);
        Ext.getCmp('cmb_cuadrilla').setVisible(false);
        Ext.getCmp('cmb_empresa_externa').setVisible(true);
        
        Ext.getCmp('item_vacio').setDisabled(true);  
        Ext.getCmp('cmb_empleado').setDisabled(true);
        Ext.getCmp('cmb_cuadrilla').setDisabled(true);
        Ext.getCmp('cmb_empresa_externa').setDisabled(false);
        
      //  eval("storeEmpresaExterna_"+i+".load();");
    }    
} 


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
    
    if(( Ext.getCmp('fechaDesdeAsignacion').getValue()!=null)&&(Ext.getCmp('fechaHastaAsignacion').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesdeAsignacion').getValue() > Ext.getCmp('fechaHastaAsignacion').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Asignacion debe ser fecha menor a Fecha Hasta Asignacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });	
        }
    }
   
    //Se agrega validacion para filtrar que solo se pueda consultar maximo entre 30 dias en las fechas de planificacion   
    var desdeP = Ext.getCmp('fechaDesdePlanif').getValue();
    var hastaP = Ext.getCmp('fechaHastaPlanif').getValue();
   
    var fechaDesdeP = desdeP.getTime();
    var fechaHastaP = hastaP.getTime();

    var differenceP = Math.abs(fechaDesdeP - fechaHastaP)
   
    //Convierto de milisegundos a dias
    var diasP = differenceP/86400000;
   
    if(diasP >30){
        boolError = true;
        Ext.Msg.show({
           title:'Error en Busqueda',
           msg: 'Por Favor solo se puede realizar busquedas de hasta 30 dias de diferencia entre la Fecha Inicio y Fin',
           buttons: Ext.Msg.OK,
           animEl: 'elId',
           icon: Ext.MessageBox.ERROR
        });	
    }

    //Se agrega validacion para filtrar que solo se pueda consultar maximo entre 30 dias en las fechas de asignacion   
    var desdeA = Ext.getCmp('fechaDesdeAsignacion').getValue();
    var hastaA = Ext.getCmp('fechaHastaAsignacion').getValue();
   
    var fechadDesdeA = desdeA.getTime();
    var fechaHastaA  = hastaA.getTime();

    var differenceA = Math.abs(fechadDesdeA - fechaHastaA)
   
    //Convierto de milisegundos a dias
    var diasA = differenceA/86400000;     
   
    if(diasA >30){
        boolError = true;
        Ext.Msg.show({
           title:'Error en Busqueda',
           msg: 'Por Favor solo se puede realizar busquedas de hasta 30 dias de diferencia entre la Fecha Inicio y Fin',
           buttons: Ext.Msg.OK,
           animEl: 'elId',
           icon: Ext.MessageBox.ERROR
        });	
    }

    var banderaEscogido = $("input[name='tipoResponsable']:checked").val();
    var codigoEscogido = "";
    var tituloError = "";

    if(banderaEscogido == "empleado")
    {
        tituloError = "Empleado ";
        codigoEscogido = Ext.getCmp('cmb_empleado').value;
    }    
    if(banderaEscogido == "cuadrilla")
    {
        tituloError = "Cuadrilla";
        codigoEscogido = Ext.getCmp('cmb_cuadrilla').value;
    } 
    if(banderaEscogido == "empresaExterna")
    {
        tituloError = "Contratista";
        codigoEscogido = Ext.getCmp('cmb_empresa_externa').value;
    }                
    
    if(!boolError)
    {
        store.removeAll();
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.fechaDesdeAsignacion = Ext.getCmp('fechaDesdeAsignacion').value;
        store.getProxy().extraParams.fechaHastaAsignacion = Ext.getCmp('fechaHastaAsignacion').value;
        store.getProxy().extraParams.login2= Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
        store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
        store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
        store.getProxy().extraParams.tipoResponsable = banderaEscogido;
        store.getProxy().extraParams.codigoResponsable = codigoEscogido;
        store.load(); 
    }          
}

function limpiar(){
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");
    Ext.getCmp('fechaDesdeAsignacion').setRawValue("");
    Ext.getCmp('fechaHastaAsignacion').setRawValue("");
    
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
    
    var banderaEscogido = $("input[name='tipoResponsable']:checked").val();
    if(banderaEscogido == "empleado")
    {
        Ext.getCmp('cmb_empleado').value="";
        Ext.getCmp('cmb_empleado').setRawValue("");
    }    
    if(banderaEscogido == "cuadrilla")
    {
        Ext.getCmp('cmb_cuadrilla').value="";
        Ext.getCmp('cmb_cuadrilla').setRawValue("");
    } 
    if(banderaEscogido == "empresaExterna")
    {
        Ext.getCmp('cmb_empresa_externa').value="";
        Ext.getCmp('cmb_empresa_externa').setRawValue("");
    }  
    
    $("input[name=tipoResponsable]").each(function(){	
        if(this.value == "todos"){ this.checked = true; }
    });
    cambiarTipoResponsable_reporte("todos");   
        
    store.removeAll();
    store.getProxy().extraParams.fechaDesdePlanif = "";
    store.getProxy().extraParams.fechaHastaPlanif = "";
    store.getProxy().extraParams.fechaDesdeAsignacion = "";
    store.getProxy().extraParams.fechaHastaAsignacion = "";
    store.getProxy().extraParams.login2= "";
    store.getProxy().extraParams.descripcionPunto = "";
    store.getProxy().extraParams.vendedor = "";
    store.getProxy().extraParams.ciudad = "";
    store.getProxy().extraParams.numOrdenServicio = "";
    store.getProxy().extraParams.estado = "Todos";
    store.getProxy().extraParams.tipoResponsable = "todos";
    store.getProxy().extraParams.codigoResponsable = "";
    store.load();
}

function exportarExcel(){
    var url = "exportarConsulta";
    var banderaEscogido = $("input[name='tipoResponsable']:checked").val();
    var codigoEscogido = "";
    var tituloError = "";

    if(banderaEscogido == "empleado")
    {
        tituloError = "Empleado ";
        codigoEscogido = Ext.getCmp('cmb_empleado').value;
    }    
    if(banderaEscogido == "cuadrilla")
    {
        tituloError = "Cuadrilla";
        codigoEscogido = Ext.getCmp('cmb_cuadrilla').value;
    } 
    if(banderaEscogido == "empresaExterna")
    {
        tituloError = "Contratista";
        codigoEscogido = Ext.getCmp('cmb_empresa_externa').value;
    }                
    
    
        url = url+"?fechaDesdePlanif="+Ext.getCmp('fechaDesdePlanif').getRawValue();
        url = url+"&fechaHastaPlanif="+Ext.getCmp('fechaHastaPlanif').getRawValue();
        url = url+"&fechaDesdeAsignacion="+Ext.getCmp('fechaDesdeAsignacion').getRawValue();
        url = url+"&fechaHastaAsignacion="+Ext.getCmp('fechaHastaAsignacion').getRawValue();
        url = url+"&login2="+Ext.getCmp('txtLogin').value;
        url = url+"&descripcionPunto="+Ext.getCmp('txtDescripcionPunto').value;
        url = url+"&vendedor="+Ext.getCmp('txtVendedor').value;
        url = url+"&ciudad="+Ext.getCmp('txtCiudad').value;
        url = url+"&numOrdenServicio="+Ext.getCmp('txtNumOrdenServicio').value;
        url = url+"&estado="+Ext.getCmp('sltEstado').value;
        url = url+"&tipoResponsable="+banderaEscogido;
        url = url+"&codigoResponsable="+codigoEscogido;
              
//   alert(url);
    window.open(url);
}