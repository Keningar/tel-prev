Ext.onReady(function() { 

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesdeCreacion = new Ext.form.DateField({
        id: 'com_doc_desde_creacion',
        fieldLabel: 'Fecha Creacion Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaCreacion = new Ext.form.DateField({
        id: 'com_doc_hasta_creacion',
        fieldLabel: 'Fecha Creacion Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });	
	
    storeFormasPago = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getFormasPago',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'id_forma_pago', mapping:'id_forma_pago'},
			{name:'descripcion_forma_pago', mapping:'descripcion_forma_pago'}
		]
    });
	
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanelComercial = Ext.create('Ext.panel.Panel', {
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
        height: 360,
        title: 'Criterios Comercial',
		header: false,
		items: 
		[    
			{
				xtype:'fieldset',   
				width: 900,
				columnWidth: 0.5,
				title: 'Contrato',
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
						xtype: 'combobox',
						fieldLabel: 'Estado de Contrato',
						id: 'com_con_estadoContrato',
						value:'0',
						store: [
							['0','-- Seleccione --'],
							['Pendiente','Pendiente'],
							['Activo','Activo'],
							['Inactivo','Inactivo'],
							['Cancelado','Cancelado']
						],
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'combobox',
						id: 'com_con_formaPago',
						fieldLabel: 'Forma de Pago',
						typeAhead: true,
						triggerAction: 'all',
						displayField:'descripcion_forma_pago',
						valueField: 'id_forma_pago',
						selectOnTab: true,
						store: storeFormasPago,
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
					
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'textfield',
						id: 'com_con_numContrato',
						fieldLabel: '# Contrato',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'combobox',
						fieldLabel: 'Es Vip',
						id: 'com_con_esVip',
						value:'0',
						store: [
							['0','-- Seleccione --'],
							['S','Si'],
							['N','No']
						],
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
					
				]
			},

			
			{
				xtype:'fieldset',
				width: 900,
				columnWidth: 0.5,
				title: 'Documentos',
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
						xtype: 'combobox',
						fieldLabel: 'Tipo de Documento',
						id: 'com_doc_tipoDocumento',
						value:'0',
						store: [
							['0','-- Seleccione --'],
							['OT','Orden de Trabajo'],
							['C','Cotizacion']
						],
						width: 360,
						listeners:{
							select:{fn:function(combo, value) {
								if(combo.getValue() == "OT")
								{
									Ext.getCmp('com_doc_tipoOrden').setDisabled(false);
								}
								else
								{
									Ext.getCmp('com_doc_tipoOrden').setDisabled(true);
								}
								
								Ext.getCmp('com_doc_tipoOrden').reset(); 
							}}
						}
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'combobox',
						fieldLabel: 'Tipo de Orden',
						id: 'com_doc_tipoOrden',
						value:'0',
						store: [
							['0','-- Seleccione --'],
							['N','Nueva'],
							['R','Reubicación'],
							['T','Trasladada']
						],
						disabled: true,
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},							
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'textfield',
						id: 'com_doc_numeroDocumento',
						fieldLabel: '# Documento',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'textfield',
						id: 'com_doc_creadoPor',
						fieldLabel: 'Creado Por',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},						
					
					{html:"&nbsp;",border:false,width:50},
					DTFechaDesdeCreacion,
					{html:"&nbsp;",border:false,width:80},
					DTFechaHastaCreacion,
					{html:"&nbsp;",border:false,width:50}		
				]
			}
		],	
        renderTo: 'filtro_comercial'
    }); 
 
});

function llenarComercial()
{
    Ext.tip.QuickTipManager.init();
    
	grid = "";
    $('#grid').html("");
			
    store = new Ext.data.Store({ 
        pageSize: 15,
        model: 'sims',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : 'buscar_datos_comercial',
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
                
                con_estado: Ext.getCmp('com_con_estadoContrato').value,
                con_forma_pago: Ext.getCmp('com_con_formaPago').value,
                con_num_contrato: Ext.getCmp('com_con_numContrato').value,
                con_es_vip: Ext.getCmp('com_con_esVip').value,
                
                doc_tipo: Ext.getCmp('com_doc_tipoDocumento').value,
                doc_creado: Ext.getCmp('com_doc_creadoPor').value,
                doc_numero: Ext.getCmp('com_doc_numeroDocumento').value,
                doc_tipo_orden: Ext.getCmp('com_doc_tipoOrden').value,
                doc_desde_creacion: Ext.getCmp('com_doc_desde_creacion').value,
                doc_hasta_creacion: Ext.getCmp('com_doc_hasta_creacion').value
            }
        },
        fields:
		[
			{name:'id', mapping:'id'},
			{name:'idPunto', mapping:'idPunto'},
			{name:'login1', mapping:'login1'},
			{name:'descripcionClienteSucursal', mapping:'descripcionClienteSucursal'},
			{name:'direccionClienteSucursal', mapping:'direccionClienteSucursal'},
			{name:'idEstadoPtoCliente', mapping:'idEstadoPtoCliente'},
			{name:'identificacion', mapping:'identificacion'},
			{name:'cliente', mapping:'cliente'},
			{name:'numeroContrato', mapping:'numeroContrato'},
			{name:'estadoContrato', mapping:'estadoContrato'},
			{name:'vendedor', mapping:'vendedor'},
			{name:'calificacion', mapping:'calificacion'},
			{name:'fechaAprobacion', mapping:'fechaAprobacion'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'}
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
				grid = "";
				$('#grid').html("");
				$('#tr_error').css("display", "none");
				$('#busqueda_error').html("");
				
				if(store.getCount()>0){														
					var pluginExpanded = true;
					
					grid = Ext.create('Ext.grid.Panel', {
						width: 1300,
						height: 480,
						store: store,
						loadMask: true,   
						viewConfig: { enableTextSelection: true },     
						dockedItems: 
						[ 
							{
								xtype: 'toolbar',
								dock: 'top',
								align: '->',
								items: [
									//tbfill -> alinea los items siguientes a la derecha
									{ xtype: 'tbfill' },
								]}
						],
						columns:[
							{
							  id: 'id',
							  header: 'Id',
							  dataIndex: 'id',
							  hidden: true,
							  hideable: false
							},
							{
							  id: 'id_punto',
							  header: 'idPunto',
							  dataIndex: 'id_punto',
							  hidden: true,
							  hideable: false
							},
							{
							  id: 'login1',
							  header: 'Login',
							  dataIndex: 'login1',
							  width: 110,
							  sortable: true
							},
							{
							  id: 'descripcionClienteSucursal',
							  header: 'Descripcion Pto Cliente',
							  dataIndex: 'descripcionClienteSucursal',
							  width: 200,
							  sortable: true
							},
							{
							  id: 'direccionClienteSucursal',
							  header: 'Direccion Pto Cliente',
							  dataIndex: 'direccionClienteSucursal',
							  width: 280,
							  sortable: true
							},
							{
							  id: 'idEstadoPtoCliente',
							  header: 'Estado Pto Cliente',
							  dataIndex: 'idEstadoPtoCliente',
							  width: 110,
							  sortable: true
							},
							{
							  id: 'identificacion',
							  header: 'Identificacion',
							  dataIndex: 'identificacion',
							  width: 100,
							  sortable: true
							},
							{
							  id: 'cliente',
							  header: 'Cliente',
							  dataIndex: 'cliente',
							  width: 240,
							  sortable: true
							},
							{
							  id: 'numeroContrato',
							  header: 'Número de Contrato',
							  dataIndex: 'numeroContrato',
							  width: 140,
							  sortable: true
							},
							{
							  id: 'estadoContrato',
							  header: 'Estado Contrato',
							  dataIndex: 'estadoContrato',
							  width: 100,
							  sortable: true
							},
							{
							  id: 'vendedor',
							  header: 'Vendedor',
							  dataIndex: 'vendedor',
							  width: 200,
							  sortable: true
							},
							/*{
							  id: 'calificacion',
							  header: 'Calificación',
							  dataIndex: 'calificacion',
							  width: 100,
							  sortable: true
							},
							{
							  id: 'fechaAprobacion',
							  header: 'Fecha de Aprobación',
							  dataIndex: 'fechaAprobacion',
							  width: 140,
							  sortable: true
							},   */                     
							{
								xtype: 'actioncolumn',
								header: 'Acciones',
								width: 120,
								items: [
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
											var rec = store.getAt(rowIndex);
											
											var permiso = $("#ROLE_147-124");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
											
											// FALTA PERMISO DE  (/comercial/punto /Cliente/show)
											
											if(rec.get('action1')!="icon-invisible")
											{
												Ext.Ajax.request({
													url: "cargaSession",
													method: 'post',
													params: { puntoId : rec.get('idPunto')},
													success: function(response){
														var text = 
														window.open("../comercial/punto/"+rec.get('idPunto')+"/Cliente/show");    
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
							store: store,
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

function limpiarComercial()
{    
    limpiarPrincipal();
    
    Ext.getCmp('com_con_estadoContrato').value="0";
    Ext.getCmp('com_con_estadoContrato').setRawValue("-- Seleccione --");
    Ext.getCmp('com_con_formaPago').value="0";
    Ext.getCmp('com_con_formaPago').setRawValue("-- Seleccione --");	
    Ext.getCmp('com_con_numContrato').value="";
    Ext.getCmp('com_con_numContrato').setRawValue("");
    Ext.getCmp('com_con_esVip').value="0";
    Ext.getCmp('com_con_esVip').setRawValue("-- Seleccione --");
	
    Ext.getCmp('com_doc_tipoDocumento').value="0";
    Ext.getCmp('com_doc_tipoDocumento').setRawValue("-- Seleccione --");
    Ext.getCmp('com_doc_tipoOrden').value="0";
    Ext.getCmp('com_doc_tipoOrden').setRawValue("-- Seleccione --");
    Ext.getCmp('com_doc_creadoPor').value="";
    Ext.getCmp('com_doc_creadoPor').setRawValue("");
    Ext.getCmp('com_doc_numeroDocumento').value="";
    Ext.getCmp('com_doc_numeroDocumento').setRawValue("");
	
    Ext.getCmp('com_doc_desde_creacion').value="";
    Ext.getCmp('com_doc_desde_creacion').setRawValue("");
    Ext.getCmp('com_doc_hasta_creacion').value="";
    Ext.getCmp('com_doc_hasta_creacion').setRawValue("");
	
	Ext.getCmp('com_doc_tipoOrden').setDisabled(true);
	Ext.getCmp('com_doc_tipoOrden').reset(); 
    
    llenarComercial();
}
