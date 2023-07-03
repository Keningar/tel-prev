Ext.onReady(function() { 

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DOCFechaDesdeCreacion = new Ext.form.DateField({
        id: 'fin_doc_fechaCreacionDesde',
        fieldLabel: 'Fecha Creacion Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
    });
    DOCFechaHastaCreacion = new Ext.form.DateField({
        id: 'fin_doc_fechaCreacionHasta',
        fieldLabel: 'Fecha Creacion Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
    });	
	
    DOCFechaDesdeEmision = new Ext.form.DateField({
        id: 'fin_doc_fechaEmisionDesde',
        fieldLabel: 'Fecha Emision Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
    });
    DOCFechaHastaEmision = new Ext.form.DateField({
        id: 'fin_doc_fechaEmisionHasta',
        fieldLabel: 'Fecha Emision Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
    });	
		
    PAGFechaDesdeCreacion = new Ext.form.DateField({
        id: 'fin_pag_fechaCreacionDesde',
        fieldLabel: 'Fecha Creacion Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
    });
    PAGFechaHastaCreacion = new Ext.form.DateField({
        id: 'fin_pag_fechaCreacionHasta',
        fieldLabel: 'Fecha Creacion Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
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
	
    storeBancos = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getBancos',
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
			{name:'id_banco', mapping:'id_banco'},
			{name:'descripcion_banco', mapping:'descripcion_banco'}
		]
    });
	
    storeTiposDocumento = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getTiposDocumento',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'codigo_tipo_documento', mapping:'codigo_tipo_documento'},
			{name:'nombre_tipo_documento', mapping:'nombre_tipo_documento'}
		]
    });
	
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanelFinanciero = Ext.create('Ext.panel.Panel', {
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
        height: 450,
        title: 'Criterios Financiero',
		header: false,
		items: 
		[    
			{
				xtype:'fieldset',   
				width: 900,
				columnWidth: 0.5,
				title: 'General',
				collapsible: false,			       
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
						id: 'fin_doc_tipoDocumento',
						fieldLabel: 'Tipo de Documento',
						typeAhead: true,
						triggerAction: 'all',
						displayField:'nombre_tipo_documento',
						valueField: 'codigo_tipo_documento',
						selectOnTab: true,
						store: storeTiposDocumento,
						width: 360,
						listeners:{
							select:{
								fn:function(combo, value) {
									var valorTipoDocumento = combo.getValue();
									if(valorTipoDocumento == 'FAC' || valorTipoDocumento == 'FACP' || valorTipoDocumento == 'NC' || valorTipoDocumento == 'ND')
									{
										Ext.getCmp('fieldsetDocumento').expand();
										Ext.getCmp('fieldsetPago').collapse();
									}
									else if(valorTipoDocumento == 'PAG' || valorTipoDocumento == 'ANT' || valorTipoDocumento == 'ANTS')
									{
										Ext.getCmp('fieldsetDocumento').collapse();
										Ext.getCmp('fieldsetPago').expand();
									}
									else
									{
										Ext.getCmp('fieldsetDocumento').collapse();
										Ext.getCmp('fieldsetPago').collapse();
									}
								}
							}
						}
					},
					{html:"&nbsp;",border:false,width:80},
					{html:"&nbsp;",border:false,width:360},
					{html:"&nbsp;",border:false,width:50}
				]
			},	
			{
				xtype:'fieldset', 
				id: 'fieldsetDocumento',  
				width: 900,
				columnWidth: 0.5,
				title: 'Documentos',
				collapsible: false,		
				collapsed: true,		       
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
						id: 'fin_doc_numDocumento',
						fieldLabel: '# Documento',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{html:"&nbsp;",border:false,width:360},
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'textfield',
						id: 'fin_doc_monto',
						fieldLabel: 'Valor por Documento',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'combobox',
						fieldLabel: false,
						id: 'fin_doc_montoFiltro',
						value:'i',
						store: [
							['p','menor que'],
							['i','igual que'],
							['m','mayor que']
						],
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},					
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'combobox',
						fieldLabel: 'Estado',
						id: 'fin_doc_estado',
						value:'0',
						store: [
							['0','-- Seleccione --'],
							['Activo','Activo'],
							['Inactivo','Inactivo'],
							['Pendiente','Pendiente'],
							['Courier','Courier'],
							['Cerrado','Cerrado']
						],
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'textfield',
						id: 'fin_doc_creador',
						fieldLabel: 'Creado Por',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},					
					
					{html:"&nbsp;",border:false,width:50},
					DOCFechaDesdeCreacion,
					{html:"&nbsp;",border:false,width:80},
					DOCFechaHastaCreacion,
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					DOCFechaDesdeEmision,
					{html:"&nbsp;",border:false,width:80},
					DOCFechaHastaEmision,
					{html:"&nbsp;",border:false,width:50}
				]
			},
			{
				xtype:'fieldset',
				id: 'fieldsetPago',
				width: 900,
				columnWidth: 0.5,
				title: 'Pago',
				collapsible: false,		
				collapsed: true,	
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
						id: 'fin_pag_numDocumento',
						fieldLabel: '# Documento',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'textfield',
						id: 'fin_pag_numReferencia',
						fieldLabel: '# Referencia',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
				
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'textfield',
						id: 'fin_pag_numDocumentoRef',
						fieldLabel: '# Documento Autorizado',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{html:"&nbsp;",border:false,width:360},
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'textfield',
						id: 'fin_pag_creador',
						fieldLabel: 'Creado Por',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'combobox',
						fieldLabel: 'Estado',
						id: 'fin_pag_estado',
						value:'0',
						store: [
							['0','-- Seleccione --'],
							['Activo','Activo'],
							['Pendiente','Pendiente'],
							['Cerrado','Cerrado']
						],
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'combobox',
						id: 'fin_pag_formaPago',
						fieldLabel: 'Forma de Pago',
						typeAhead: true,
						triggerAction: 'all',
						displayField:'descripcion_forma_pago',
						valueField: 'id_forma_pago',
						selectOnTab: true,
						store: storeFormasPago,
						width: 360,
						listeners:{
							select:{fn:function(combo, value) {
								/*if(combo.getValue() == "OT")
								{
									Ext.getCmp('com_doc_tipoOrden').setDisabled(false);
								}
								else
								{
									Ext.getCmp('com_doc_tipoOrden').setDisabled(true);
								}
								
								Ext.getCmp('com_doc_tipoOrden').reset(); */
							}}
						}
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'combobox',
						id: 'fin_pag_banco',
						fieldLabel: 'Banco',
						typeAhead: true,
						triggerAction: 'all',
						displayField:'descripcion_banco',
						valueField: 'id_banco',
						selectOnTab: true,
						store: storeBancos,
						width: 360,
					},
					{html:"&nbsp;",border:false,width:50},						
					
					{html:"&nbsp;",border:false,width:50},
					PAGFechaDesdeCreacion,
					{html:"&nbsp;",border:false,width:80},
					PAGFechaHastaCreacion,
					{html:"&nbsp;",border:false,width:50}		
				]
			}
		],	
        renderTo: 'filtro_financiero'
    }); 
 
});

function llenarFinanciero()
{
    Ext.tip.QuickTipManager.init();
         
    $('#grid').html("");
	
	var tipoDocumento = Ext.getCmp('fin_doc_tipoDocumento').value
	if(tipoDocumento != "" && tipoDocumento)
	{
		storeFinanciero = new Ext.data.Store({ 
			pageSize: 20,
			model: 'sims',
			total: 'total',
			proxy: {
				type: 'ajax',
				timeout: 600000,
				url : 'buscar_datos_financiero',
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
					
					fin_doc_tipoDocumento: Ext.getCmp('fin_doc_tipoDocumento').value,
					fin_doc_numDocumento: Ext.getCmp('fin_doc_numDocumento').value,
					fin_doc_monto: Ext.getCmp('fin_doc_monto').value,
					fin_doc_montoFiltro: Ext.getCmp('fin_doc_montoFiltro').value,
					fin_doc_estado: Ext.getCmp('fin_doc_estado').value,
					fin_doc_creador: Ext.getCmp('fin_doc_creador').value,
					fin_doc_fechaCreacionDesde: Ext.getCmp('fin_doc_fechaCreacionDesde').value,
					fin_doc_fechaCreacionHasta: Ext.getCmp('fin_doc_fechaCreacionHasta').value,
					fin_doc_fechaEmisionDesde: Ext.getCmp('fin_doc_fechaEmisionDesde').value,
					fin_doc_fechaEmisionHasta: Ext.getCmp('fin_doc_fechaEmisionHasta').value,
						
					fin_pag_numDocumento: Ext.getCmp('fin_pag_numDocumento').value,
					fin_pag_numReferencia: Ext.getCmp('fin_pag_numReferencia').value,
					fin_pag_numDocumentoRef: Ext.getCmp('fin_pag_numDocumentoRef').value,
					fin_pag_creador: Ext.getCmp('fin_pag_creador').value,
					fin_pag_formaPago: Ext.getCmp('fin_pag_formaPago').value,
					fin_pag_banco: Ext.getCmp('fin_pag_banco').value,
					fin_pag_estado: Ext.getCmp('fin_pag_estado').value,
					fin_pag_fechaCreacionDesde: Ext.getCmp('fin_pag_fechaCreacionDesde').value,
					fin_pag_fechaCreacionHasta: Ext.getCmp('fin_pag_fechaCreacionHasta').value
				}
			},
			fields:
			[
				{name:'id', mapping:'id'},
				{name:'oficinaId', mapping:'oficinaId'},
				{name:'idPunto', mapping:'idPunto'},
				{name:'idContrato', mapping:'idContrato'},
				{name:'login1', mapping:'login1'},
				{name:'Punto', mapping:'Punto'},
				{name:'idEstadoPtoCliente', mapping:'idEstadoPtoCliente'},
				{name:'identificacion', mapping:'identificacion'},
				{name:'numeroContrato', mapping:'numeroContrato'},
				{name:'vendedor', mapping:'vendedor'},
				{name:'calificacion', mapping:'calificacion'},
				{name:'fechaAprobacion', mapping:'fechaAprobacion'},					
				{name:'idDocumento', mapping:'idDocumento'},				
				{name:'idDocumentoDetalle', mapping:'idDocumentoDetalle'},		
				{name:'comentarioPago', mapping:'comentarioPago'},		
				{name:'comentarioDetallePago', mapping:'comentarioDetallePago'},
				{name:'codigoTipoDocumento', mapping:'codigoTipoDocumento'},
				{name:'nombreTipoDocumento', mapping:'nombreTipoDocumento'},
				{name:'descripcionFormaPago', mapping:'descripcionFormaPago'},
				{name:'NumeroDocumento', mapping:'NumeroDocumento'},
				{name:'Cliente', mapping:'Cliente'},
				{name:'Esautomatica', mapping:'Esautomatica'},
				{name:'ValorTotal', mapping:'ValorTotal'},
				{name:'referencia', mapping:'referencia'},
				{name:'nombreBanco', mapping:'nombreBanco'},
				{name:'referenciaId', mapping:'referenciaId'},
				{name:'CodigoDocumentoRef', mapping:'CodigoDocumentoRef'},
				{name:'NombreDocumentoRef', mapping:'NombreDocumentoRef'},
				{name:'NumeroDocumentoRef', mapping:'NumeroDocumentoRef'},
				{name:'nombreCreador', mapping:'nombreCreador'},
				{name:'Feemision', mapping:'Feemision'},
				{name:'Fecreacion', mapping:'Fecreacion'},
				{name:'Fedeposito', mapping:'Fedeposito'},
				{name:'Feprocesado', mapping:'Feprocesado'},
				{name:'NoComprobanteDeposito', mapping:'NoComprobanteDeposito'},
				{name:'Estado', mapping:'Estado'},
				{name:'action1', mapping:'action1'},
				{name:'action2', mapping:'action2'},
				{name:'action3', mapping:'action3'},
				{name:'tipoNegocio', mapping:'tipoNegocio'},
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
					gridFinanciero = "";
					$('#grid').html("");
					$('#tr_error').css("display", "none");
					$('#busqueda_error').html("");
					
					if(storeFinanciero.getCount()>0){														
						var pluginExpanded = true;
						
						//****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
						var permiso = $("#ROLE_147-564");
						var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	
						
						boolPermiso1 = true; //quitar y dejar permiso creado
						
						var exportarBtn_financiero = "";
						if(boolPermiso1)
						{
							exportarBtn_financiero = Ext.create('Ext.button.Button', {
								iconCls: 'icon_exportar',
								itemId: 'exportar',
								text: 'Exportar',
								scope: this,
								handler: function(){ exportarExcel_financiero();}
							});
						}
								
						var toolbar_financiero = Ext.create('Ext.toolbar.Toolbar', {
							dock: 'top',
							align: '->',
							items   : [ '->', exportarBtn_financiero]
						});
					
						gridFinanciero = Ext.create('Ext.grid.Panel', {
							width: 1300,
							height: 500,
							store: storeFinanciero,
							loadMask: true, 
							viewConfig: { enableTextSelection: true },   
							dockedItems: [ toolbar_financiero ],
							columns:
							[
								{
								  id: 'id',
								  header: 'Id',
								  dataIndex: 'id',
								  hidden: true,
								  hideable: false
								},
								{
								  id: 'oficinaId',
								  header: 'Oficina',
								  dataIndex: 'oficinaId'
								},
								{
								  id: 'idPunto',
								  header: 'idPunto',
								  dataIndex: 'idPunto',
								  hidden: true,
								  hideable: false
								},
								{
								  id: 'idContrato',
								  header: 'idContrato',
								  dataIndex: 'idContrato',
								  hidden: true,
								  hideable: false
								},
								{
								  id: 'idDocumento',
								  header: 'idDocumento',
								  dataIndex: 'idDocumento',
								  hidden: true,
								  hideable: false
								},
								{
								  id: 'codigoTipoDocumento',
								  header: 'codigoTipoDocumento',
								  dataIndex: 'codigoTipoDocumento',
								  hidden: true,
								  hideable: false
								},
								{
								  id: 'idDocumento2',
								  header: 'Id Doc.',
								  dataIndex: 'idDocumento',
								  width: 70,
								  sortable: true
								  
								},
								{
								  id: 'idDocumentoDetalle',
								  header: 'Id Det. Doc.',
								  dataIndex: 'idDocumentoDetalle',
								  width: 80,
								  sortable: true
								  
								},
								{
								  id: 'login1',
								  header: 'Login',
								  dataIndex: 'login1',
								  width: 100,
								  sortable: true
								  
								},
								{
								  id: 'Punto',
								  header: 'Descripcion Pto Cliente',
								  dataIndex: 'Punto',
								  width: 200,
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
								  id: 'Cliente',
								  header: 'Cliente',
								  dataIndex: 'Cliente',
								  width: 200,
								  sortable: true
								},/*
								{
								  id: 'numeroContrato',
								  header: 'Número de Contrato',
								  dataIndex: 'numeroContrato',
								  width: 120,
								  sortable: true
								},*/
								{
								  id: 'nombreTipoDocumento',
								  header: 'Tipo Documento',
								  dataIndex: 'nombreTipoDocumento',
								  width: 140,
								  sortable: true
								},
								{
								  id: 'NumeroDocumento',
								  header: 'No. Documento',
								  dataIndex: 'NumeroDocumento',
								  width: 115,
								  sortable: true
								},
								{
								  id: 'ValorTotal',
								  header: 'Valor',
								  dataIndex: 'ValorTotal',
								  width: 70,
								  sortable: true
								},
								{
								  id: 'Esautomatica',
								  header: 'Es automatica',
								  dataIndex: 'Esautomatica',
								  width: 80,
								  sortable: true
								},
								{
								  id: 'descripcionFormaPago',
								  header: 'Forma Pago',
								  dataIndex: 'descripcionFormaPago',
								  width: 160,
								  sortable: true
								},
								{
								  id: 'referencia',
								  header: '# Referencia',
								  dataIndex: 'referencia',
								  width: 120,
								  sortable: true
								},
								{
								  id: 'comentarioDetallePago',
								  header: 'Comentario',
								  dataIndex: 'comentarioDetallePago',
								  width: 320,
								  sortable: true
								},
								{
								  id: 'nombreBanco',
								  header: 'Banco',
								  dataIndex: 'nombreBanco',
								  width: 180,
								  sortable: true
								},
								{
								  id: 'NombreDocumentoRef',
								  header: 'Tipo Documento Auto.',
								  dataIndex: 'NombreDocumentoRef',
								  width: 140,
								  sortable: true
								},
								{
								  id: 'NumeroDocumentoRef',
								  header: '# Documento Autorizado',
								  xtype: 'templatecolumn', 
								  width: 135,
								  tpl: '<tpl if="NumeroDocumentoRef!=\'\'">\n\
											<tpl if="CodigoDocumentoRef==\'FAC\'">\n\
												<span><a href="#" onClick="window.open(\'../financiero/documentos/facturas/{referenciaId}/show\');" />{NumeroDocumentoRef}</a></span>\n\
											</tpl>\n\
											<tpl if="CodigoDocumentoRef==\'FACP\'">\n\
												<span><a href="#" onClick="window.open(\'../financiero/documentos/facturas_proporcionales/{referenciaId}/show\');" />{NumeroDocumentoRef}</a></span>\n\
											</tpl>\n\
											<tpl if="CodigoDocumentoRef==\'NC\'">\n\
												<span><a href="#" onClick="window.open(\'../financiero/documentos/nota_de_credito/{referenciaId}/show\');" />{NumeroDocumentoRef}</a></span>\n\
											</tpl>\n\
											<tpl if="CodigoDocumentoRef==\'ND\'">\n\
												<span><a href="#" onClick="window.open(\'../financiero/documentos/nota_de_debito/{referenciaId}/show\');" />{NumeroDocumentoRef}</a></span>\n\
											</tpl>\n\
										</tpl>'				
								},
								{
								  id: 'nombreCreador',
								  header: 'Usuario Creacion',
								  dataIndex: 'nombreCreador',
								  width: 220,
								  sortable: true
								},
								{
								  id: 'Estado',
								  header: 'Estado',
								  dataIndex: 'Estado',
								  width: 70,
								  sortable: true
								},
								{
								  id: 'Fecreacion',
								  header: 'F. Creacion',
								  dataIndex: 'Fecreacion',
								  width: 110,
								  sortable: true
								},  
								{
								  id: 'Feemision',
								  header: 'F. Emision',
								  dataIndex: 'Feemision',
								  width: 110,
								  sortable: true
								},  
								{
								  id: 'Fedeposito',
								  header: 'F. Deposito',
								  dataIndex: 'Fedeposito',
								  width: 110,
								  sortable: true
								},
								{
								  id: 'Feprocesado',
								  header: 'F. Procesado',
								  dataIndex: 'Feprocesado',
								  width: 110,
								  sortable: true
								},
								{
								  id: 'NoComprobanteDeposito',
								  header: 'No. Comp. Deposito',
								  dataIndex: 'NoComprobanteDeposito',
								  width: 110,
								  sortable: true
								},                
								{
								  id: 'tipoNegocio',
								  header: 'T. Negocio',
								  dataIndex: 'tipoNegocio',
								  width: 110,
								  sortable: true
								},                
								{
									xtype: 'actioncolumn',
									header: 'Acciones',
									width: 120,
									items: [
										{
											getClass: function(v, meta, rec) {	
												/*var permiso = $("#ROLE_147-124");
												var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
												if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
												*/
												// FALTA PERMISO DE  (/comercial/punto /Cliente/show)
													
												if (rec.get('action1') == "icon-invisible") 
													this.items[0].tooltip = '';
												else 
													this.items[0].tooltip = 'Ver Documento';
												
												return rec.get('action1')
											},
											handler: function(grid, rowIndex, colIndex) {
												var rec = storeFinanciero.getAt(rowIndex);
												/*
												var permiso = $("#ROLE_147-124");
												var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
												if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
												*/
												// FALTA PERMISO DE  (/comercial/punto /Cliente/show)
												
												if(rec.get('action1')!="icon-invisible")
												{
													var urlDocumento = "";
													
													if(rec.get('codigoTipoDocumento').toUpperCase() === "FAC")
														urlDocumento = "../financiero/documentos/facturas/"+rec.get('idDocumento')+"/show";									
													if(rec.get('codigoTipoDocumento') == "FACP")
														urlDocumento = "../financiero/documentos/facturas_proporcionales/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "NC")
														urlDocumento = "../financiero/documentos/nota_de_credito/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "ND")
														urlDocumento = "../financiero/documentos/nota_de_debito/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "PAG")
														urlDocumento = "../financiero/pagos/infopagocab/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "ANT")
														urlDocumento = "../financiero/pagos/anticipo/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "ANTS")
														urlDocumento = "../financiero/pagos/anticipo/"+rec.get('idDocumento')+"/showsincliente";
														
													if(urlDocumento != "")
													{									
														Ext.Ajax.request({
															url: "cargaSession",
															method: 'post',
															params: { puntoId : rec.get('idPunto')},
															success: function(response){
																var text = response.responseText;
																window.open(urlDocumento);    
																//window.location = "../financiero/punto/"+rec.get('idPunto')+"/Cliente/show";    
															},
															failure: function(result)
															{
																Ext.Msg.alert('Error ','Error: ' + result.statusText);
															}
														});
													}
												}
												else
													Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');							
											}
										}
									]
								}
							],
							bbar: Ext.create('Ext.PagingToolbar', {
								store: storeFinanciero,
								displayInfo: true,
								displayMsg: 'Mostrando {0} - {1} de {2}',
								emptyMsg: "No hay datos que mostrar."
							}),
							renderTo: 'grid'
						});
						
							
						if(tipoDocumento == "ANTS")
						{
							gridFinanciero.columns[7].hide(); // quick hide
							gridFinanciero.columns[8].hide(); // quick hide
							gridFinanciero.columns[9].hide(); // quick hide
							gridFinanciero.columns[10].hide(); // quick hide
							gridFinanciero.columns[11].hide(); // quick hide
						}
						if(tipoDocumento == "FAC" || tipoDocumento == "FACP" || tipoDocumento == "NC" || tipoDocumento == "ND")
						{
							/*gridFinanciero.columns[6].hide(); // quick hide
							gridFinanciero.columns[16].hide(); // quick hide
							gridFinanciero.columns[17].hide(); // quick hide
							gridFinanciero.columns[18].hide(); // quick hide
							gridFinanciero.columns[19].hide(); // quick hide
							gridFinanciero.columns[20].hide(); // quick hide
							gridFinanciero.columns[21].hide(); // quick hide*/
						}
						
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
	else
	{
		mostrarOcultarBusqueada(true);
		Ext.Msg.alert('Error ','No ha escogido el Tipo de Documento, por favor seleccione un registro del combo.');	
	}
}

function limpiarFinanciero()
{    
    limpiarPrincipal();
    
    Ext.getCmp('fin_doc_tipoDocumento').value="";
    Ext.getCmp('fin_doc_tipoDocumento').setRawValue("");
    Ext.getCmp('fin_doc_numDocumento').value="";
    Ext.getCmp('fin_doc_numDocumento').setRawValue("");
    Ext.getCmp('fin_doc_monto').value="";
    Ext.getCmp('fin_doc_monto').setRawValue("");
    Ext.getCmp('fin_doc_montoFiltro').value="i";
    Ext.getCmp('fin_doc_montoFiltro').setRawValue("igual que");
    Ext.getCmp('fin_doc_estado').value="0";
    Ext.getCmp('fin_doc_estado').setRawValue("-- Seleccione --");
    Ext.getCmp('fin_doc_creador').value="";
    Ext.getCmp('fin_doc_creador').setRawValue("");
    Ext.getCmp('fin_doc_fechaCreacionDesde').value="";
    Ext.getCmp('fin_doc_fechaCreacionDesde').setRawValue("");
    Ext.getCmp('fin_doc_fechaCreacionHasta').value="";
    Ext.getCmp('fin_doc_fechaCreacionHasta').setRawValue("");
    Ext.getCmp('fin_doc_fechaEmisionDesde').value="";
    Ext.getCmp('fin_doc_fechaEmisionDesde').setRawValue("");
    Ext.getCmp('fin_doc_fechaEmisionHasta').value="";
    Ext.getCmp('fin_doc_fechaEmisionHasta').setRawValue("");
			
    Ext.getCmp('fin_pag_numDocumento').value="";
    Ext.getCmp('fin_pag_numDocumento').setRawValue("");
    Ext.getCmp('fin_pag_numReferencia').value="";
    Ext.getCmp('fin_pag_numReferencia').setRawValue("");
    Ext.getCmp('fin_pag_numDocumentoRef').value="";
    Ext.getCmp('fin_pag_numDocumentoRef').setRawValue("");
    Ext.getCmp('fin_pag_creador').value="";
    Ext.getCmp('fin_pag_creador').setRawValue("");
    Ext.getCmp('fin_pag_fechaCreacionDesde').value="";
    Ext.getCmp('fin_pag_fechaCreacionDesde').setRawValue("");
    Ext.getCmp('fin_pag_fechaCreacionHasta').value="";
    Ext.getCmp('fin_pag_fechaCreacionHasta').setRawValue("");
    Ext.getCmp('fin_pag_formaPago').value="0";
    Ext.getCmp('fin_pag_formaPago').setRawValue("-- Seleccione --");
    Ext.getCmp('fin_pag_banco').value="0";
    Ext.getCmp('fin_pag_banco').setRawValue("-- Seleccione --");
    Ext.getCmp('fin_pag_estado').value="0";
    Ext.getCmp('fin_pag_estado').setRawValue("-- Seleccione --");
					
    llenarFinanciero();
}

function exportarExcel_financiero(){
	var parametros = "";					
					
	if(Ext.getCmp('fin_doc_fechaCreacionDesde').getValue()!=null){
		fin_doc_fechaCreacionDesde = Ext.getCmp('fin_doc_fechaCreacionDesde').getValue();
		fin_doc_fechaCreacionDesde = transformarFecha(fin_doc_fechaCreacionDesde);
	}else{
		fin_doc_fechaCreacionDesde = "";
	}
	if(Ext.getCmp('fin_doc_fechaCreacionHasta').getValue()){
		fin_doc_fechaCreacionHasta = Ext.getCmp('fin_doc_fechaCreacionHasta').getValue();
		fin_doc_fechaCreacionHasta = transformarFecha(fin_doc_fechaCreacionHasta);
	}else{
		fin_doc_fechaCreacionHasta = "";
	}
	if(Ext.getCmp('fin_doc_fechaEmisionDesde').getValue()!=null){
		fin_doc_fechaEmisionDesde = Ext.getCmp('fin_doc_fechaEmisionDesde').getValue();
		fin_doc_fechaEmisionDesde = transformarFecha(fin_doc_fechaEmisionDesde);
	}else{
		fin_doc_fechaEmisionDesde = "";
	}
	if(Ext.getCmp('fin_doc_fechaEmisionHasta').getValue()!=null){
		fin_doc_fechaEmisionHasta = Ext.getCmp('fin_doc_fechaEmisionHasta').getValue();
		fin_doc_fechaEmisionHasta = transformarFecha(fin_doc_fechaEmisionHasta);
	}else{
		fin_doc_fechaEmisionHasta = "";
	}
	if(Ext.getCmp('fin_pag_fechaCreacionDesde').getValue()!=null){
		fin_pag_fechaCreacionDesde = Ext.getCmp('fin_pag_fechaCreacionDesde').value;
		fin_pag_fechaCreacionDesde = transformarFecha(fin_pag_fechaCreacionDesde);
	}else{
		fin_pag_fechaCreacionDesde = "";
	}
	if(Ext.getCmp('fin_pag_fechaCreacionHasta').getValue()!=null){
		fin_pag_fechaCreacionHasta = Ext.getCmp('fin_pag_fechaCreacionHasta').getValue();
		fin_pag_fechaCreacionHasta = transformarFecha(fin_pag_fechaCreacionHasta);
	}else{
		fin_pag_fechaCreacionHasta = "";
	}
	
	//if(isNaN(Ext.getCmp('fin_doc_tipoDocumento').getValue())) Ext.getCmp('fin_doc_tipoDocumento').setValue('');
	//if(isNaN(Ext.getCmp('fin_doc_estado').getValue())) Ext.getCmp('fin_doc_estado').setValue('');
	//if(isNaN(Ext.getCmp('fin_pag_formaPago').getValue())) Ext.getCmp('fin_pag_formaPago').setValue('');
	
	parametros = "?fin_doc_fechaCreacionDesde=" + fin_doc_fechaCreacionDesde;
	parametros = parametros + "&fin_doc_fechaCreacionHasta=" + fin_doc_fechaCreacionHasta;
	parametros = parametros + "&fin_doc_fechaEmisionDesde=" + fin_doc_fechaEmisionDesde;
	parametros = parametros + "&fin_doc_fechaEmisionHasta=" + fin_doc_fechaEmisionHasta;
	parametros = parametros + "&fin_pag_fechaCreacionDesde=" + fin_pag_fechaCreacionDesde;
	parametros = parametros + "&fin_pag_fechaCreacionHasta=" + fin_pag_fechaCreacionHasta;
	/*
	alert(Ext.getCmp('fin_doc_tipoDocumento').getValue());
	alert(!isNaN(Ext.getCmp('fin_doc_tipoDocumento').getValue()) );*/
	
	parametros = parametros + "&fin_doc_tipoDocumento=" + Ext.getCmp('fin_doc_tipoDocumento').getValue();
	parametros = parametros + "&fin_doc_tipoDocumento_texto=" + Ext.getCmp('fin_doc_tipoDocumento').getRawValue();
	parametros = parametros + "&fin_doc_numDocumento=" + Ext.getCmp('fin_doc_numDocumento').value;
	parametros = parametros + "&fin_doc_monto=" + Ext.getCmp('fin_doc_monto').value;
	parametros = parametros + "&fin_doc_montoFiltro=" + Ext.getCmp('fin_doc_montoFiltro').value;
	parametros = parametros + "&fin_doc_montoFiltro_texto=" + Ext.getCmp('fin_doc_montoFiltro').getRawValue();
	parametros = parametros + "&fin_doc_estado=" + Ext.getCmp('fin_doc_estado').getValue();
	parametros = parametros + "&fin_doc_estado_texto=" + Ext.getCmp('fin_doc_estado').getRawValue();
	parametros = parametros + "&fin_doc_creador=" + Ext.getCmp('fin_doc_creador').value;
	parametros = parametros + "&fin_pag_numDocumento=" + Ext.getCmp('fin_pag_numDocumento').value;
	parametros = parametros + "&fin_pag_numReferencia=" + Ext.getCmp('fin_pag_numReferencia').value;
	parametros = parametros + "&fin_pag_numDocumentoRef=" + Ext.getCmp('fin_pag_numDocumentoRef').value;
	parametros = parametros + "&fin_pag_creador=" + Ext.getCmp('fin_pag_creador').value;
	parametros = parametros + "&fin_pag_formaPago=" + (!isNaN(Ext.getCmp('fin_pag_formaPago').getValue()) ? Ext.getCmp('fin_pag_formaPago').getValue() : '');
	parametros = parametros + "&fin_pag_formaPago_texto=" + Ext.getCmp('fin_pag_formaPago').getRawValue();
	parametros = parametros + "&fin_pag_banco=" + (!isNaN(Ext.getCmp('fin_pag_banco').getValue()) ? Ext.getCmp('fin_pag_banco').getValue() : '');
	parametros = parametros + "&fin_pag_banco_texto=" + Ext.getCmp('fin_pag_banco').getRawValue();
	parametros = parametros + "&fin_pag_estado=" + Ext.getCmp('fin_pag_estado').getValue();
	parametros = parametros + "&fin_pag_estado_texto=" + Ext.getCmp('fin_pag_estado').getRawValue();
	
	parametros = parametros + "&estados_pto=" + $('#search_estados_pto').val();
	parametros = parametros + "&negocios_pto=" + $('#search_negocios_pto').val();
	parametros = parametros + "&login=" + $('#search_login2').val();
	parametros = parametros + "&descripcion_pto=" + $('#search_descripcion_pto').val();
	parametros = parametros + "&direccion_pto=" + $('#search_direccion_pto').val();
	parametros = parametros + "&vendedor=" + $('#search_vendedor').val();
	parametros = parametros + "&identificacion=" + $('#search_identificacion').val();
	parametros = parametros + "&nombre=" + $('#search_nombre').val();
	parametros = parametros + "&apellido=" + $('#search_apellido').val();
	parametros = parametros + "&razon_social=" + $('#search_razon_social').val();
	parametros = parametros + "&direccion_grl=" + $('#search_direccion_grl').val();
	parametros = parametros + "&depende_edificio=" + $('#search_depende_edificio').val();
	parametros = parametros + "&es_edificio=" + $('#search_es_edificio').val();
					
	window.open("exportarConsulta_BusquedaFinanciera" + parametros);
}

function transformarFecha(dateSinFormat)
{
	var d = new Date(dateSinFormat);
	var curr_date = d.getDate();
	var curr_month = d.getMonth();
	curr_month++;
	var curr_year = d.getFullYear();
	
	var dateFormato = curr_date + "-" + curr_month + "-" + curr_year;
	
	return dateFormato;
}
