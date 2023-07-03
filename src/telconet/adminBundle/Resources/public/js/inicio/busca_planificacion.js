Ext.onReady(function() { 

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesdeSolPlanif = new Ext.form.DateField({
        id: 'plan_desde_solPlanif',
        fieldLabel: 'Fecha Solicita Planificacion Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    }); 
    DTFechaHastaSolPlanif = new Ext.form.DateField({
        id: 'plan_hasta_solPlanif',
        fieldLabel: 'Fecha Solicita Planificacion Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });	
		
    DTFechaDesdePlanif = new Ext.form.DateField({
        id: 'plan_desde_planif',
        fieldLabel: 'Fecha Planificacion Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaPlanif = new Ext.form.DateField({
        id: 'plan_hasta_planif',
        fieldLabel: 'Fecha Planificacion Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });	
		
    DTFechaDesdeAsig = new Ext.form.DateField({
        id: 'plan_desde_asig',
        fieldLabel: 'Fecha Asignacion Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaAsig = new Ext.form.DateField({
        id: 'plan_hasta_asig',
        fieldLabel: 'Fecha Asignacion Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
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
        width: 360,
        style: { color: '#000000' }
    });
    //******** html vacio...
    var iniHtmlVacio1 = '';           
    Vacio1 =  Ext.create('Ext.Component', {
        id: 'item_vacio',
        name: 'item_vacio',
        html: iniHtmlVacio1,
        width: 360,
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
        "    url : 'getEmpleados',"+
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
        width: 360,
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
        "    url : 'getEmpresasExternas',"+
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
        width: 360,
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
        "    url : 'getCuadrillas',"+
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
        width: 360,
        emptyText: 'Seleccione Cuadrilla',
        store: eval("storeCuadrillas"),
        displayField: 'nombre_cuadrilla',
        valueField: 'id_cuadrilla',
        layout: 'anchor',
        labelAlign: 'top',
        disabled: true 
    });  
	
	
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanelPlanificacion = Ext.create('Ext.panel.Panel', {
		bodyPadding: 7,
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 1,
            align: 'left'
        },
		border: false,
        bodyStyle: {
			background: '#fff'
		},     
        width: 950, 
        height: 500,
        title: 'Criterios Planificacion',
		header: false,
		items: 
		[    
			{
				xtype:'fieldset',   
				width: 900,
				columnWidth: 0.5,
				title: 'Generales',
				collapsible: true,			       
				layout:{
					type:'table',
					columns: 5,
					align: 'left'
				},
				items :
				[
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'textfield',
						id: 'plan_ciudad',
						fieldLabel: 'Ciudad',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'textfield',
						id: 'plan_numOrdenServicio',
						fieldLabel: '# Orden Servicio',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					DTFechaDesdeSolPlanif,
					{html:"&nbsp;",border:false,width:80},
					DTFechaHastaSolPlanif,
					{html:"&nbsp;",border:false,width:50}, 
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'combobox',
						fieldLabel: 'Estado',
						id: 'plan_estado',
						value:'Todos',
						store: [
                            ['Todos','Todos'],
                            ['PrePlanificada','PrePlanificada'],
                            ['Planificada','Planificada'],
                            ['Detenido','Detenido'],
                            ['Anulado','Anulado'],
                            ['Rechazada','Rechazada'],
                            ['Asignada','Asignada'],
                            ['Finalizada','Finalizada']
						],
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{html:"&nbsp;",border:false,width:360},
					{html:"&nbsp;",border:false,width:50}					
				]
			},

			
			{
				xtype:'fieldset',
				width: 900,
				columnWidth: 0.5,
				title: 'Planificada',
				collapsible: true,		       
				layout:{
					type:'table',
					columns: 5,
					align: 'left'
				},
				items :
				[
					{html:"&nbsp;",border:false,width:50},
					DTFechaDesdePlanif,
					{html:"&nbsp;",border:false,width:80},
					DTFechaHastaPlanif,
					{html:"&nbsp;",border:false,width:50}		
				]
			},

			
			{
				xtype:'fieldset',
				width: 900,
				columnWidth: 0.5,
				title: 'Asignada',
				collapsible: true,		       
				layout:{
					type:'table',
					columns: 5,
					align: 'left'
				},
				items :
				[
                    {html:"&nbsp;",border:false,width:50},
                    RadiosTiposResponsable, 
                    {html:"&nbsp;",border:false,width:80},
                    Vacio1, 
                    combo_empleados, 
                    combo_cuadrillas, 
                    combo_empresas_externas, 
                    {html:"&nbsp;",border:false,width:50},										
					
					{html:"&nbsp;",border:false,width:50},
					DTFechaDesdeAsig,
					{html:"&nbsp;",border:false,width:80},
					DTFechaHastaAsig,
					{html:"&nbsp;",border:false,width:50}		
				]
			}
		],	
        renderTo: 'filtro_planificacion'
    }); 
 
    Ext.getCmp('item_vacio').setVisible(true);
    Ext.getCmp('cmb_empleado').setVisible(false);
    Ext.getCmp('cmb_cuadrilla').setVisible(false);
    Ext.getCmp('cmb_empresa_externa').setVisible(false);
});


function llenarPlanificacion()
{
    Ext.tip.QuickTipManager.init();         	 
         
    $('#grid').html("");
	
    var banderaEscogido = $("input[name='tipoResponsable']:checked").val();
    var codigoEscogido = "";
    var tituloError = "";

    if(banderaEscogido == "empleado")
    {
        tituloError = "Empleado ";
        codigoEscogido = Ext.getCmp('cmb_empleado').value;
    }    
    else if(banderaEscogido == "cuadrilla")
    {
        tituloError = "Cuadrilla";
        codigoEscogido = Ext.getCmp('cmb_cuadrilla').value;
    } 
    else if(banderaEscogido == "empresaExterna")
    {
        tituloError = "Contratista";
        codigoEscogido = Ext.getCmp('cmb_empresa_externa').value;
    }                
	else
	{
		banderaEscogido = "todos";
		codigoEscogido = "";
	}
	
    storePlanificacion = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : 'buscar_datos_planificacion',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {    
                estados_pto: $('#search_estados_pto').val(),     
                negocios_pto: $('#search_negocios_pto').val(),
                login: $('#search_login2').val(),
                descripcion_pto: $('#search_descripcion_pto').val(),
                direccion_pto: $('#search_direccion_pto').val(),
                vendedor: $('#search_vendedor').val(),
                identificacion: $('#search_identificacion').val(),
                nombre: $('#search_nombre').val(),
                apellido: $('#search_apellido').val(),
                razon_social: $('#search_razon_social').val(),
                direccion_grl: $('#search_direccion_grl').val(),
                depende_edificio: $('#search_depende_edificio').val(),
                es_edificio: $('#search_es_edificio').val(),
		
                plan_desde_solPlanif: Ext.getCmp('plan_desde_solPlanif').value,
                plan_hasta_solPlanif: Ext.getCmp('plan_hasta_solPlanif').value,
                plan_desde_planif: Ext.getCmp('plan_desde_planif').value,
                plan_hasta_planif: Ext.getCmp('plan_hasta_planif').value,
                plan_desde_asig: Ext.getCmp('plan_desde_asig').value,
                plan_hasta_asig: Ext.getCmp('plan_hasta_asig').value,				
                plan_ciudad: Ext.getCmp('plan_ciudad').value,
                plan_num_orden_servicio: Ext.getCmp('plan_numOrdenServicio').value,
                plan_estado: Ext.getCmp('plan_estado').value,                
                plan_tipoResponsable: banderaEscogido,
                plan_codigoResponsable: codigoEscogido
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
			{name:'num_orden_trabajo', mapping:'num_orden_trabajo'},
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
			{name:'action2', mapping:'action2'}                
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
				gridPlanificacion = "";
				$('#grid').html("");
				$('#tr_error').css("display", "none");
				$('#busqueda_error').html("");
				
				if(storePlanificacion.getCount()>0){														
					var pluginExpanded = true;
					
					gridPlanificacion = Ext.create('Ext.grid.Panel', {
						width: 1300,
						height: 500,
						store: storePlanificacion,
						loadMask: true,
						frame: false,
						viewConfig: { enableTextSelection: true },  
						columns:
						[
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
							  id: 'num_orden_trabajo',
							  header: '# Orden Servicio',
							  dataIndex: 'num_orden_trabajo',
							  width: 125,
							  sortable: true
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
							  width: 140,
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
							  width: 160,
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
							  width: 250,
							  sortable: true
							},
							{
							  id: 'nombreAsignado',
							  header: 'Asignado',
							  dataIndex: 'nombreAsignado',
							  width: 250,
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
							{
								xtype: 'actioncolumn',
								header: 'Acciones',
								width: 120,
								items: 
								[
									{
										getClass: function(v, meta, rec) {
											var permiso = $("#ROLE_147-124");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
											
											// FALTA PERMISO DE  (/comercial/punto /Cliente/show)
											
											if (rec.get('action1') == "icon-invisible") 
												this.items[0].tooltip = '';
											else 
												this.items[0].tooltip = 'Ver Punto';
											
											return rec.get('action1')
										},
										handler: function(grid, rowIndex, colIndex) {
											var rec = storePlanificacion.getAt(rowIndex);
											
											var permiso = $("#ROLE_147-124");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
											
											// FALTA PERMISO DE  (/comercial/punto /Cliente/show)
											
											if(rec.get('action1')!="icon-invisible")
											{
												Ext.Ajax.request({
													url: "cargaSession",
													method: 'post',
													params: { puntoId : rec.get('id_punto')},
													success: function(response){
														var text = response.responseText;
														window.open("../comercial/punto/"+rec.get('id_punto')+"/Cliente/show");    
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
											var permiso = $("#ROLE_147-124");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
											
											// FALTA PERMISO DE  (/comercial/documentos/orden/ /show)
											
											if (rec.get('action2') == "icon-invisible") 
												this.items[0].tooltip = '';
											else 
												this.items[0].tooltip = 'Ver Orden Trabajo';
											
											return rec.get('action2')
										},
										handler: function(grid, rowIndex, colIndex) {
											var rec = storePlanificacion.getAt(rowIndex);	
											
											var permiso = $("#ROLE_147-124");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
											
											// FALTA PERMISO DE  (/comercial/documentos/orden/ /show)
											
											if(rec.get('action2')!="icon-invisible")
											{
												Ext.Ajax.request({
													url: "cargaSession",
													method: 'post',
													params: { puntoId : rec.get('id_punto')},
													success: function(response){
														var text = response.responseText;
														//alert('Carga Excelente');
														
														window.location("../comercial/documentos/orden/"+rec.get('id_orden_trabajo')+"/show");    
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
									}
								]
							}
						],
						bbar: Ext.create('Ext.PagingToolbar', {
							store: storePlanificacion,
							displayInfo: true,
							displayMsg: 'Mostrando {0} - {1} de {2}',
							emptyMsg: "No hay datos que mostrar."
						}),
						renderTo: 'grid'
					});

					mostrarOcultarBusqueada(false);
				}//FIN IF TIENE DATA
				else
				{	
					$('#tr_error').css("display", "table-row");
					$('#busqueda_error').html("Alerta: No existen registros para esta busqueda");
					mostrarOcultarBusqueada(true);
				}
				
				Ext.MessageBox.hide();
			}
		}
    });

}

function limpiarPlanificacion()
{    
    limpiarPrincipal();
	
    Ext.getCmp('plan_desde_solPlanif').value="";
    Ext.getCmp('plan_desde_solPlanif').setRawValue("");
    Ext.getCmp('plan_hasta_solPlanif').value="";
    Ext.getCmp('plan_hasta_solPlanif').setRawValue("");
	
    Ext.getCmp('plan_desde_planif').value="";
    Ext.getCmp('plan_desde_planif').setRawValue("");
    Ext.getCmp('plan_hasta_planif').value="";
    Ext.getCmp('plan_hasta_planif').setRawValue("");
	
    Ext.getCmp('plan_desde_asig').value="";
    Ext.getCmp('plan_desde_asig').setRawValue("");
    Ext.getCmp('plan_hasta_asig').value="";
    Ext.getCmp('plan_hasta_asig').setRawValue("");
	
    Ext.getCmp('cmb_empleado').value="";
    Ext.getCmp('cmb_empleado').setRawValue("");
	
    Ext.getCmp('cmb_empresa_externa').value="";
    Ext.getCmp('cmb_empresa_externa').setRawValue("");
	
    Ext.getCmp('cmb_cuadrilla').value="";
    Ext.getCmp('cmb_cuadrilla').setRawValue("");
	
    Ext.getCmp('plan_ciudad').value="";
    Ext.getCmp('plan_ciudad').setRawValue("");
	
    Ext.getCmp('plan_numOrdenServicio').value="";
    Ext.getCmp('plan_numOrdenServicio').setRawValue("");
	
    Ext.getCmp('plan_estado').value="Todos";
    Ext.getCmp('plan_estado').setRawValue("Todos");
		
    Ext.getCmp('item_vacio').setVisible(true);
    Ext.getCmp('cmb_empleado').setVisible(false);
    Ext.getCmp('cmb_cuadrilla').setVisible(false);
    Ext.getCmp('cmb_empresa_externa').setVisible(false);
	
    $("input[name=tipoResponsable]").each(function(){	
        if(this.value == "todos"){ this.checked = true; }
    });
    cambiarTipoResponsable_reporte("todos"); 
    
    llenarPlanificacion();
}


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