var permisoFechaContabilizacion      = $("#ROLE_212-5037");
var boolPermisoFechaContabilizacion  = (typeof permisoFechaContabilizacion === 'undefined') ? false : (permisoFechaContabilizacion.val() == 1
                                        ? true : false);
var datePagFechaContabilizacionHasta = null;
var datePagFechaContabilizacionDesde = null;
var cboEstadoPunto                   = null;

Ext.onReady(function() {

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    dateDocFechaDesdeAutorizacion = new Ext.form.DateField
    ({
        id: 'finDocFechaAutorizacionDesde',
        fieldLabel: 'Fecha Autorización Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
    });
    
    dateDocFechaHastaAutorizacion = new Ext.form.DateField
    ({
        id: 'finDocFechaAutorizacionHasta',
        fieldLabel: 'Fecha Autorización Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        editable: false
    });
    
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

    if( boolPermisoFechaContabilizacion )
    {
        datePagFechaContabilizacionDesde = new Ext.form.DateField
        ({
            id: 'datePagFechaContabilizacionDesde',
            fieldLabel: 'Fecha Contabilización Desde',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            width:360,
            editable: false
        });

        datePagFechaContabilizacionHasta = new Ext.form.DateField
        ({
            id: 'datePagFechaContabilizacionHasta',
            fieldLabel: 'Fecha Contabilización Hasta',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            width:360,
            editable: false
        });
    }
	
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

    storeFormasPago.proxy.limitParam=null;

    storeEstadoPunto = new Ext.data.Store({
        total: 'intTotal',
        proxy: {
            type: 'ajax',
            url : 'getEstadoPunto',
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'objEstadoPunto'
            }
        },
        autoLoad: true,
        fields:
                [
                        {name:'intValue', mapping:'intValue'},
                        {name:'strDescripcionEstadoPunto', mapping:'strDescripcionEstadoPunto'}
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
            url : 'getTipoDocumentosPerfil',
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

    if (boolPermisoEmpresas){
        // Crea el combo multi seleccion cboEstadoPunto
        cboEstadoPunto = Ext.create('Ext.form.ComboBox', {
            xtype: 'combobox',
            fieldLabel: 'Estado del Punto',
            id: 'strEstPunto',
            store: storeEstadoPunto,
            displayField:'strDescripcionEstadoPunto',
            valueField: 'intValue',
            width: 360
        });
    }

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
        height: 400,
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
                                    if( valorTipoDocumento == 'FAC'  || 
                                        valorTipoDocumento == 'FACP' || 
                                        valorTipoDocumento == 'NC'   || 
                                        valorTipoDocumento == 'NCI'  || 
                                        valorTipoDocumento == 'ND'   ||   
                                        valorTipoDocumento == 'NDI'  ||
                                        valorTipoDocumento == 'DEV'
                                        )
                                    {
                                        Ext.getCmp('fieldsetDocumento').expand();
                                        Ext.getCmp('fieldsetPago').collapse();
                                    }
                                    else if(valorTipoDocumento == 'PAG'  || 
                                            valorTipoDocumento == 'PAGC' || 
                                            valorTipoDocumento == 'ANT'  || 
                                            valorTipoDocumento == 'ANTC' || 
                                            valorTipoDocumento == 'ANTS')
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
            store: 
                [
                    ['0',         '-- Seleccione --'],
                    ['Activo',    'Activo'],
                    ['Inactivo',  'Inactivo'],
                    ['Pendiente', 'Pendiente'],
                    ['Anulado',   'Anulado'],
                    ['Aprobada',  'Aprobada'],
                    ['Eliminado', 'Eliminado'],
                    ['Rechazado', 'Rechazado'],
                    ['Rechazada', 'Rechazada'],
                    ['Asignado',  'Asignado'],
                    ['Cerrado',   'Cerrado']
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
					dateDocFechaDesdeAutorizacion,
					{html:"&nbsp;",border:false,width:80},
					dateDocFechaHastaAutorizacion,
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
					boolPermisoEmpresas?cboEstadoPunto:{html:"&nbsp;",border:false,width:50},
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
							['Cerrado','Cerrado'],
                                                        ['Asignado','Asignado']
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
					{html:"&nbsp;",border:false,width:50},
                    
					{html:"&nbsp;",border:false,width:50},
					datePagFechaContabilizacionDesde,
					{html:"&nbsp;",border:false,width:80},
					datePagFechaContabilizacionHasta,
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
    
    var strPagFechaContabilizacionDesde = "";
    var strPagFechaContabilizacionHasta = "";
    var strEstadoPuntoFiltro            = "";
    
    if( boolPermisoFechaContabilizacion )
    {
        strPagFechaContabilizacionDesde = Ext.getCmp('datePagFechaContabilizacionDesde').value;
        strPagFechaContabilizacionHasta = Ext.getCmp('datePagFechaContabilizacionHasta').value;
        
        if( !Ext.isEmpty(strPagFechaContabilizacionDesde) )
        {
            strPagFechaContabilizacionDesde = Ext.util.Format.date(strPagFechaContabilizacionDesde, 'd-m-Y');
        }
        
        if( !Ext.isEmpty(strPagFechaContabilizacionHasta) )
        {
            strPagFechaContabilizacionHasta = Ext.util.Format.date(strPagFechaContabilizacionHasta, 'd-m-Y');
        } 
    }
    
    if (cboEstadoPunto)
    {
        strEstadoPuntoFiltro=Ext.getCmp('strEstPunto').value;
    }
    
    
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
				url : 'buscar_datosFinanciero',
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
                    finDocFechaAutorizacionDesde: Ext.getCmp('finDocFechaAutorizacionDesde').value,
                    finDocFechaAutorizacionHasta: Ext.getCmp('finDocFechaAutorizacionHasta').value,
                    fin_doc_fechaCreacionDesde: Ext.getCmp('fin_doc_fechaCreacionDesde').value,
                    fin_doc_fechaCreacionHasta: Ext.getCmp('fin_doc_fechaCreacionHasta').value,
                    fin_doc_fechaEmisionDesde: Ext.getCmp('fin_doc_fechaEmisionDesde').value,
                    fin_doc_fechaEmisionHasta: Ext.getCmp('fin_doc_fechaEmisionHasta').value,

                    fin_pag_numDocumento: Ext.getCmp('fin_pag_numDocumento').value,
                    fin_pag_numReferencia: Ext.getCmp('fin_pag_numReferencia').value,
                    fin_pag_numDocumentoRef: Ext.getCmp('fin_pag_numDocumentoRef').value,
                    strEstPunto: strEstadoPuntoFiltro,
                    fin_pag_creador: Ext.getCmp('fin_pag_creador').value,
                    fin_pag_formaPago: Ext.getCmp('fin_pag_formaPago').value,
                    fin_pag_banco: Ext.getCmp('fin_pag_banco').value,
                    fin_pag_estado: Ext.getCmp('fin_pag_estado').value,
                    fin_pag_fechaCreacionDesde: Ext.getCmp('fin_pag_fechaCreacionDesde').value,
                    fin_pag_fechaCreacionHasta: Ext.getCmp('fin_pag_fechaCreacionHasta').value,
                    strPagFechaContabilizacionDesde: strPagFechaContabilizacionDesde,
                    strPagFechaContabilizacionHasta: strPagFechaContabilizacionHasta
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
                                {name:'strTipoCuenta', mapping:'strTipoCuenta'},
				{name:'referenciaId', mapping:'referenciaId'},
				{name:'CodigoDocumentoRef', mapping:'CodigoDocumentoRef'},
				{name:'NombreDocumentoRef', mapping:'NombreDocumentoRef'},
				{name:'NumeroDocumentoRef', mapping:'NumeroDocumentoRef'},
				{name:'nombreCreador', mapping:'nombreCreador'},
                {name:'strUsrUltModificacion', mapping:'strUsrUltModificacion'},
				{name:'FeEmision', mapping:'FeEmision'},
				{name:'Fecreacion', mapping:'Fecreacion'},
                {name:'strFeUltModificacion', mapping:'strFeUltModificacion'},
				{name:'Fedeposito', mapping:'Fedeposito'},
				{name:'Feprocesado', mapping:'Feprocesado'},
				{name:'FechaCruce', mapping:'FechaCruce'},
				{name:'NoComprobanteDeposito', mapping:'NoComprobanteDeposito'},
				{name:'Estado', mapping:'Estado'},
				{name:'action1', mapping:'action1'},
				{name:'action2', mapping:'action2'},
				{name:'action3', mapping:'action3'},
				{name:'tipoNegocio', mapping:'tipoNegocio'},
                {name:'nombreBancoEmpresa', mapping:'nombreBancoEmpresa'},
                {name:'strFeAutorizacion', mapping:'strFeAutorizacion'}
			],				
			autoLoad: true,
			listeners: {
				beforeload: function(sender, options )
				{ 
				   $('#grid').html("");
				  $('#tr_error').css("display", "none");
                                  $('#busqueda_error').html("");
				  
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
								text: 'Generar-Enviar CSV',
								scope: this,
								handler: function(){ generarReporteFinanciero("Exportar");}
							});
						}
								
						var toolbar_financiero = Ext.create('Ext.toolbar.Toolbar', {
							dock: 'top',
							align: '->',
              items:
                  [
                      {
                          id:    'labelSeleccion',
                          cls:   'greenTextGrid',
                          text:  'Importante: Consulta mensual es el rango máximo permitido para generar csv.',
                          scope: this
                      },
                      {xtype: 'tbfill'},
                      exportarBtn_financiero
                  ]
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
								  id: 'strTipoCuenta',
								  header: 'Tipo Cuenta',
								  dataIndex: 'strTipoCuenta',
								  width: 180,
								  sortable: true
								},
								{
								  id: 'nombreBancoEmpresa',
								  header: 'Banco Empresa',
								  dataIndex: 'nombreBancoEmpresa',
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
								  id: 'usrUltMod',
								  header: 'Usuario Ult.Mod',
								  dataIndex: 'strUsrUltModificacion',
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
								  id: 'strFeUltModificacion',
								  header: 'F. Ult.Modificacion',
								  dataIndex: 'strFeUltModificacion',
								  width: 110,
								  sortable: true
								},                                  
								{
								  id: 'FeEmision',
								  header: 'F. Emision',
								  dataIndex: 'FeEmision',
								  width: 110,
								  sortable: true
								},  
                                {
								  id: 'strFeAutorizacion',
								  header: 'F. Autorizacion',
								  dataIndex: 'strFeAutorizacion',
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
								  id: 'FechaCruce',
								  header: 'FechaCruce',
								  dataIndex: 'FechaCruce',
								  width: 110,
								  sortable: true
								},
								{
								  id: 'NoComprobanteDeposito',
								  header: 'No. Comprobante',
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
														//urlDocumento = "../financiero/documentos/facturas/"+rec.get('idDocumento')+"/show";
													      urlDocumento = "../documentos/facturas/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "FACP")
														//urlDocumento = "../financiero/documentos/facturas_proporcionales/"+rec.get('idDocumento')+"/show";
													       urlDocumento = "../documentos/facturas_proporcionales/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "NC")
														//urlDocumento = "../financiero/documentos/nota_de_credito/"+rec.get('idDocumento')+"/show";
													       urlDocumento = "../documentos/nota_de_credito/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "ND")
														//urlDocumento = "../financiero/documentos/nota_de_debito/"+rec.get('idDocumento')+"/show";
													       urlDocumento = "../documentos/nota_de_debito/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "PAG")
														//urlDocumento = "../financiero/pagos/infopagocab/"+rec.get('idDocumento')+"/show";
													       urlDocumento = "../pagos/infopagocab/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "ANT")
														//urlDocumento = "../financiero/pagos/anticipo/"+rec.get('idDocumento')+"/show";
													       urlDocumento = "../pagos/anticipo/"+rec.get('idDocumento')+"/show";
													if(rec.get('codigoTipoDocumento') == "ANTS")
														//urlDocumento = "../financiero/pagos/anticipo/"+rec.get('idDocumento')+"/showsincliente";
													       urlDocumento = "../pagos/anticipo/"+rec.get('idDocumento')+"/showsincliente";
														
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
                                                
                                                /* Validad columna TIPO CUENTA para los reportes (PAG, PAGC, ANT, ANTC, ANTS) */
                                                if(tipoDocumento == "PAG" || tipoDocumento == "PAGC" || tipoDocumento == "ANT" 
                                                        || tipoDocumento == "ANTC" || tipoDocumento == "ANTS")
						{
							gridFinanciero.columns[21].show(); // quick hide
						}
                                                else
                                                {
                                                        gridFinanciero.columns[21].hide(); // quick hide
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
						$('#busqueda_error').html("Alerta: No existen registros <br> para esta busqueda");
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
   // limpiarPrincipal();
    $('#grid').html("");
    $('#tr_error').css("display", "none");
    $('#busqueda_error').html("");
    
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
    Ext.getCmp('finDocFechaAutorizacionDesde').value="";
    Ext.getCmp('finDocFechaAutorizacionDesde').setRawValue("");
    Ext.getCmp('finDocFechaAutorizacionHasta').value="";
    Ext.getCmp('finDocFechaAutorizacionHasta').setRawValue("");
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
    if (cboEstadoPunto)
    {
        Ext.getCmp('strEstPunto').value="0";
        Ext.getCmp('strEstPunto').setRawValue("-- Seleccione --");
    }
    Ext.getCmp('fin_pag_creador').value="";
    Ext.getCmp('fin_pag_creador').setRawValue("");
    Ext.getCmp('fin_pag_fechaCreacionDesde').value="";
    Ext.getCmp('fin_pag_fechaCreacionDesde').setRawValue("");
    Ext.getCmp('fin_pag_fechaCreacionHasta').value="";
    Ext.getCmp('fin_pag_fechaCreacionHasta').setRawValue("");
    
    if( boolPermisoFechaContabilizacion )
    {
        Ext.getCmp('datePagFechaContabilizacionDesde').value = "";
        Ext.getCmp('datePagFechaContabilizacionHasta').value = "";
        Ext.getCmp('datePagFechaContabilizacionHasta').setRawValue("");
        Ext.getCmp('datePagFechaContabilizacionDesde').setRawValue("");
    }
    
    Ext.getCmp('fin_pag_formaPago').value="0";
    Ext.getCmp('fin_pag_formaPago').setRawValue("-- Seleccione --");
    Ext.getCmp('fin_pag_banco').value="0";
    Ext.getCmp('fin_pag_banco').setRawValue("-- Seleccione --");
    Ext.getCmp('fin_pag_estado').value="0";
    Ext.getCmp('fin_pag_estado').setRawValue("-- Seleccione --");
}

function exportarExcel_financiero()
{
	var parametros                      = "";
    var strPagFechaContabilizacionDesde = "";
    var strPagFechaContabilizacionHasta = "";
    
    if( boolPermisoFechaContabilizacion )
    {
        if( !Ext.isEmpty(Ext.getCmp('datePagFechaContabilizacionDesde').getValue()) )
        {
            strPagFechaContabilizacionDesde = Ext.getCmp('datePagFechaContabilizacionDesde').getValue();
            strPagFechaContabilizacionDesde = transformarFecha(strPagFechaContabilizacionDesde);
        }
        else
        {
            strPagFechaContabilizacionDesde = "";
        }

        if( !Ext.isEmpty(Ext.getCmp('datePagFechaContabilizacionHasta').getValue()) )
        {
            strPagFechaContabilizacionHasta = Ext.getCmp('datePagFechaContabilizacionHasta').getValue();
            strPagFechaContabilizacionHasta = transformarFecha(strPagFechaContabilizacionHasta);
        }
        else
        {
            strPagFechaContabilizacionHasta = "";
        }
    }
					
    if(Ext.getCmp('finDocFechaAutorizacionDesde').getValue()!=null)
    {
        finDocFechaAutorizacionDesde = Ext.getCmp('finDocFechaAutorizacionDesde').getValue();
        finDocFechaAutorizacionDesde = transformarFecha(finDocFechaAutorizacionDesde);
    }
    else
    {
        finDocFechaAutorizacionDesde = "";
    }
    
    if(Ext.getCmp('finDocFechaAutorizacionHasta').getValue())
    {
        finDocFechaAutorizacionHasta = Ext.getCmp('finDocFechaAutorizacionHasta').getValue();
        finDocFechaAutorizacionHasta = transformarFecha(finDocFechaAutorizacionHasta);
    }
    else
    {
        finDocFechaAutorizacionHasta = "";
    }					
					
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
	parametros = parametros + "&finDocFechaAutorizacionDesde=" + finDocFechaAutorizacionDesde;
	parametros = parametros + "&finDocFechaAutorizacionHasta=" + finDocFechaAutorizacionHasta;
	parametros = parametros + "&fin_doc_fechaEmisionDesde=" + fin_doc_fechaEmisionDesde;
	parametros = parametros + "&fin_doc_fechaEmisionHasta=" + fin_doc_fechaEmisionHasta;
	parametros = parametros + "&fin_pag_fechaCreacionDesde=" + fin_pag_fechaCreacionDesde;
	parametros = parametros + "&fin_pag_fechaCreacionHasta=" + fin_pag_fechaCreacionHasta;
	parametros = parametros + "&strPagFechaContabilizacionDesde=" + strPagFechaContabilizacionDesde;
	parametros = parametros + "&strPagFechaContabilizacionHasta=" + strPagFechaContabilizacionHasta;
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
					
	//window.open("exportarConsulta_BusquedaFinanciera" + parametros);
	window.open("buscar_consultaBusquedaFinanciera" + parametros);
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




    function mostrarOcultarBusqueada(mostrar)
        {
            if(mostrar)
            {
                $("#filtroBusqueda").fadeIn(1500);
               /* $("#tool-1041-toolEl").removeClass("x-tool-expand-bottom");
                $("#tool-1041-toolEl").addClass("x-tool-collapse-top");

                $("#banderaFiltro").val(0);*/	
           
            
            }
            else
            {
                //$("#filtroBusqueda").fadeOut(1000);
                $("#filtroBusqueda").fadeOut(20);
               
                /*$("#tool-1041-toolEl").removeClass("x-tool-collapse-top");
                $("#tool-1041-toolEl").addClass("x-tool-expand-bottom");
                
                $("#banderaFiltro").val(1);
                */
            }
        }
        
   
 function botonFiltro()
  {
            var bandera = $("#banderaFiltro").val();
          //alert(bandera);
            if(bandera == 1)
            {
                mostrarOcultarBusqueada(false);
                 $("#banderaFiltro").val(0);
                 
                 //  $("#panel-1035_header").width(1276);
                 //width: 1286px;
            }
            else
            {
                 // $("#panel-1035_header").width(1264);
                mostrarOcultarBusqueada(true);
                $("#banderaFiltro").val(1);
               
            }
    } 
 
    /**
    * Función que valida los filtros necesarios para la generación del reporte de documentos financieros
    *
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.1 16-09-2016 
    * @author Edson Franco <efranco@telconet.ec>
    * @version 1.2 19-12-2016 - Se añaden las fechas de contabilización para la búsqueda de los documentos asociados a cobranzas. Adicional se valida
    *                           el ingreso de cualquier tipo de fechas para realizar la consulta respectiva.
    */  
    function generarReporteFinanciero(strTipoConsulta)
    {
       var boolFechaCreacionDesde        = false;
       var boolFechaCreacionHasta        = false;
       var boolFechaEmisionDesde         = false;
       var boolFechaEmisionHasta         = false;
       var boolFechaAutorizacionDesde    = false;
       var boolFechaAutorizacionHasta    = false;
       var boolFechaPagCreacionDesde     = false;
       var boolFechaPagCreacionHasta     = false; 
       var finDocFechaAutorizacionDesde  = "";
       var finDocFechaAutorizacionHasta  = "";
       var fin_doc_fechaCreacionDesde    = "";
       var fin_doc_fechaCreacionHasta    = "";
       var fin_doc_fechaEmisionDesde     = "";
       var fin_doc_fechaEmisionHasta     = "";
       var fin_pag_fechaCreacionDesde    = "";
       var fin_pag_fechaCreacionHasta    = "";
       var strEstadoPuntoFiltro          = "";
       
       var strPagFechaContabilizacionDesde  = "";
       var strPagFechaContabilizacionHasta  = "";
       var boolFechaPagContabilizacionDesde = false;
       var boolFechaPagContabilizacionHasta = false; 

       if(Ext.getCmp('fin_doc_tipoDocumento').getValue()==0)
       {
           Ext.Msg.alert('Alerta ','Seleccione Tipo de Documento ');
           return false;
       }  
       
       
        if( boolPermisoFechaContabilizacion )
        {
            if( !Ext.isEmpty(Ext.getCmp('datePagFechaContabilizacionDesde').getValue()) )
            {
                strPagFechaContabilizacionDesde  = Ext.util.Format.date(Ext.getCmp('datePagFechaContabilizacionDesde').getValue(), 'd-m-Y');
                boolFechaPagContabilizacionDesde = true;
            }

            if( !Ext.isEmpty(Ext.getCmp('datePagFechaContabilizacionHasta').getValue()) )
            {
                strPagFechaContabilizacionHasta  = Ext.util.Format.date(Ext.getCmp('datePagFechaContabilizacionHasta').getValue(), 'd-m-Y');
                boolFechaPagContabilizacionHasta = true;
            }
        }
            

        if(Ext.getCmp('finDocFechaAutorizacionDesde').getValue()!=null)
        {
            finDocFechaAutorizacionDesde = Ext.getCmp('finDocFechaAutorizacionDesde').getValue();
            finDocFechaAutorizacionDesde = transformarFecha(finDocFechaAutorizacionDesde);  
            boolFechaAutorizacionDesde   = true;
        }

       if(Ext.getCmp('finDocFechaAutorizacionHasta').getValue()!=null)
       {
           finDocFechaAutorizacionHasta = Ext.getCmp('finDocFechaAutorizacionHasta').getValue();
           finDocFechaAutorizacionHasta = transformarFecha(finDocFechaAutorizacionHasta);
           boolFechaAutorizacionHasta   = true;
       }
					
      
       if(Ext.getCmp('fin_doc_fechaCreacionDesde').getValue()!=null){
           fin_doc_fechaCreacionDesde = Ext.getCmp('fin_doc_fechaCreacionDesde').getValue();
           fin_doc_fechaCreacionDesde = transformarFecha(fin_doc_fechaCreacionDesde);
           boolFechaCreacionDesde     = true;
       }
       
       if(Ext.getCmp('fin_doc_fechaCreacionHasta').getValue()!=null){
           fin_doc_fechaCreacionHasta = Ext.getCmp('fin_doc_fechaCreacionHasta').getValue();
           fin_doc_fechaCreacionHasta = transformarFecha(fin_doc_fechaCreacionHasta);
           boolFechaCreacionHasta       = true;
       }

       if(Ext.getCmp('fin_doc_fechaEmisionDesde').getValue()!=null){
           fin_doc_fechaEmisionDesde = Ext.getCmp('fin_doc_fechaEmisionDesde').getValue();
           fin_doc_fechaEmisionDesde = transformarFecha(fin_doc_fechaEmisionDesde);
           boolFechaEmisionDesde     = true;
       }
       
       if(Ext.getCmp('fin_doc_fechaEmisionHasta').getValue()!=null){
           fin_doc_fechaEmisionHasta = Ext.getCmp('fin_doc_fechaEmisionHasta').getValue();
           fin_doc_fechaEmisionHasta = transformarFecha(fin_doc_fechaEmisionHasta);
           boolFechaEmisionHasta     = true;
       }
        
        
        if(boolFechaCreacionDesde || boolFechaCreacionHasta)
        {
            if(boolFechaCreacionDesde && boolFechaCreacionHasta)
            {
                var aFecha1 = fin_doc_fechaCreacionDesde.split('-'); 
                var aFecha2 = fin_doc_fechaCreacionHasta.split('-'); 
                var fFecha1 = aFecha1[0]+'-'+(aFecha1[1]-1)+'-'+aFecha1[2]; 
                var fFecha2 = aFecha2[0]+'-'+(aFecha2[1]-1)+'-'+aFecha2[2];             
                var rangoFechaCreacion = Utils.restaFechas(fFecha1,fFecha2);

                if(rangoFechaCreacion>31)
                {
                    Ext.Msg.alert('Alerta ','Rango de fechas excede el  limite permitido (31 dias)  ');
                    return false;            
                }
            }
            else
            {
                Ext.Msg.alert("Atención", "Debe elegir un rango de Fechas de Creación válido");
                return false;
            }
        }


       if(Ext.getCmp('fin_pag_fechaCreacionDesde').getValue()!=null){
           fin_pag_fechaCreacionDesde = Ext.getCmp('fin_pag_fechaCreacionDesde').value;
           fin_pag_fechaCreacionDesde = transformarFecha(fin_pag_fechaCreacionDesde);
           boolFechaPagCreacionDesde  = true;
       }
       
       if(Ext.getCmp('fin_pag_fechaCreacionHasta').getValue()!=null){
           fin_pag_fechaCreacionHasta = Ext.getCmp('fin_pag_fechaCreacionHasta').getValue();
           fin_pag_fechaCreacionHasta = transformarFecha(fin_pag_fechaCreacionHasta);
           boolFechaPagCreacionHasta  = true;
       }

       if (cboEstadoPunto){
           strEstadoPuntoFiltro = Ext.getCmp('strEstPunto').getValue();
       }

        if(boolFechaPagContabilizacionDesde || boolFechaPagContabilizacionHasta)
        {
            if( boolFechaPagContabilizacionDesde && boolFechaPagContabilizacionHasta )
            {
                var rangoFechaContabilizacion = Utils.restaFechas(strPagFechaContabilizacionDesde, strPagFechaContabilizacionHasta);        

                if(rangoFechaContabilizacion > 31)
                {
                    Ext.Msg.alert('Alerta ','Rango de fechas excede el limite permitido (31 dias) ');
                    return false;            
                }
            }
            else
            {
                Ext.Msg.alert("Atención", "Debe elegir un rango de Fechas de Contabilización válido");
                return false;
            }
        }
        
        
        if(boolFechaPagCreacionDesde || boolFechaPagCreacionHasta)
        {
            if(boolFechaPagCreacionDesde && boolFechaPagCreacionHasta)
            {
                var aFecha1 = fin_pag_fechaCreacionDesde.split('-'); 
                var aFecha2 = fin_pag_fechaCreacionHasta.split('-'); 
                var fFecha1 = aFecha1[0]+'-'+(aFecha1[1]-1)+'-'+aFecha1[2]; 
                var fFecha2 = aFecha2[0]+'-'+(aFecha2[1]-1)+'-'+aFecha2[2];             
                var rangoFechaCreacion = Utils.restaFechas(fFecha1,fFecha2);        

                if(rangoFechaCreacion>31)
                {
                    Ext.Msg.alert('Alerta ','Rango de fechas excede el  limite permitido (31 dias)  ');
                    return false;            
                }
            } 
            else
            {
                Ext.Msg.alert("Atención", "Debe elegir un rango de Fechas de Creación válido");
                return false;
            }
        }
        
        
        if(boolFechaEmisionDesde || boolFechaEmisionHasta)
        {
            if(boolFechaEmisionDesde && boolFechaEmisionHasta)
            {
                var aFecha1 = fin_doc_fechaEmisionDesde.split('-'); 
                var aFecha2 = fin_doc_fechaEmisionHasta.split('-'); 
                var fFecha1 = aFecha1[0]+'-'+(aFecha1[1]-1)+'-'+aFecha1[2]; 
                var fFecha2 = aFecha2[0]+'-'+(aFecha2[1]-1)+'-'+aFecha2[2];        
                var rangoFechaEmision = Utils.restaFechas(fFecha1,fFecha2);             

                if(rangoFechaEmision>31)
                {
                    Ext.Msg.alert('Alerta ','Rango de fechas excede el  limite permitido (31 dias)  ');
                    return false;            
                }
            } 
            else
            {
                Ext.Msg.alert("Atención", "Debe elegir un rango de Fechas de Emisión válido");
                return false;            
            }
        }
        
        
        if(boolFechaAutorizacionDesde || boolFechaAutorizacionHasta)
        {
            if(boolFechaAutorizacionDesde && boolFechaAutorizacionHasta)
            { 
                var aFecha1 = finDocFechaAutorizacionDesde.split('-'); 
                var aFecha2 = finDocFechaAutorizacionHasta.split('-'); 
                var fFecha1 = aFecha1[0]+'-'+(aFecha1[1]-1)+'-'+aFecha1[2]; 
                var fFecha2 = aFecha2[0]+'-'+(aFecha2[1]-1)+'-'+aFecha2[2]; 
                var rangoFechaAutorizacion = Utils.restaFechas(fFecha1,fFecha2);  

                if(rangoFechaAutorizacion>31)
                {
                    Ext.Msg.alert('Alerta ','Rango de fechas excede el  limite permitido (31 dias)  ');
                    return false;            
                }
            } 
            else
            {
                Ext.Msg.alert("Atención", "Debe elegir un rango de Fechas de Autorización válido");
                return false;            
            }
        }
        

        if( boolFechaCreacionDesde || boolFechaCreacionHasta || boolFechaEmisionDesde || boolFechaEmisionHasta || boolFechaAutorizacionDesde 
            || boolFechaAutorizacionHasta || boolFechaPagCreacionDesde || boolFechaPagCreacionHasta || boolFechaPagContabilizacionDesde 
            || boolFechaPagContabilizacionHasta )
        {
            if( !Ext.isEmpty(strTipoConsulta) )
            {
                if( strTipoConsulta == "Consultar")
                {
                    llenarFinanciero();
                }//( strTipoConsulta == "Consultar")
                else
                {
                    Ext.MessageBox.wait('Generando Reporte. Favor espere..');
                    Ext.Ajax.request(
                    {
                        timeout: 900000,
                        url: urlGenerarReporteFinanciero,
                        params:
                        {
                            fin_doc_fechaCreacionDesde: fin_doc_fechaCreacionDesde,
                            fin_doc_fechaCreacionHasta: fin_doc_fechaCreacionHasta,
                            finDocFechaAutorizacionDesde: finDocFechaAutorizacionDesde,
                            finDocFechaAutorizacionHasta: finDocFechaAutorizacionHasta,
                            fin_doc_fechaEmisionDesde: fin_doc_fechaEmisionDesde,
                            fin_doc_fechaEmisionHasta: fin_doc_fechaEmisionHasta,
                            fin_pag_fechaCreacionDesde: fin_pag_fechaCreacionDesde,
                            fin_pag_fechaCreacionHasta: fin_pag_fechaCreacionHasta,
                            strPagFechaContabilizacionDesde: strPagFechaContabilizacionDesde,
                            strPagFechaContabilizacionHasta: strPagFechaContabilizacionHasta,
                            fin_doc_tipoDocumento: Ext.getCmp('fin_doc_tipoDocumento').getValue(),
                            fin_doc_tipoDocumento_texto: Ext.getCmp('fin_doc_tipoDocumento').getRawValue(),
                            fin_doc_numDocumento: Ext.getCmp('fin_doc_numDocumento').value,
                            fin_doc_monto: Ext.getCmp('fin_doc_monto').value,
                            fin_doc_montoFiltro: Ext.getCmp('fin_doc_montoFiltro').value,
                            fin_doc_montoFiltro_texto: Ext.getCmp('fin_doc_montoFiltro').getRawValue(),
                            fin_doc_estado: Ext.getCmp('fin_doc_estado').getValue(),
                            fin_doc_estado_texto: Ext.getCmp('fin_doc_estado').getRawValue(),
                            fin_doc_creador: Ext.getCmp('fin_doc_creador').value,
                            fin_pag_numDocumento: Ext.getCmp('fin_pag_numDocumento').value,
                            fin_pag_numReferencia: Ext.getCmp('fin_pag_numReferencia').value,
                            fin_pag_numDocumentoRef: Ext.getCmp('fin_pag_numDocumentoRef').value,
                            strEstPunto: strEstadoPuntoFiltro,
                            fin_pag_creador: Ext.getCmp('fin_pag_creador').value,
                            fin_pag_formaPago: (!isNaN(Ext.getCmp('fin_pag_formaPago').getValue())?Ext.getCmp('fin_pag_formaPago').getValue():''),
                            fin_pag_formaPago_texto: Ext.getCmp('fin_pag_formaPago').getRawValue(),
                            fin_pag_banco: (!isNaN(Ext.getCmp('fin_pag_banco').getValue()) ? Ext.getCmp('fin_pag_banco').getValue() : ''),
                            fin_pag_banco_texto: Ext.getCmp('fin_pag_banco').getRawValue(),
                            fin_pag_estado: Ext.getCmp('fin_pag_estado').getValue(),
                            fin_pag_estado_texto: Ext.getCmp('fin_pag_estado').getRawValue(),
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
                            es_edificio: $('#search_es_edificio').val()
                        },
                        method: 'get',
                        success: function(response) 
                        {                 
                            Ext.Msg.alert('Mensaje', response.responseText);
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Error al generar y enviar reporte: ' + result.statusText);
                        }
                    });
                }
            }//( !Ext.isEmpty(strTipoConsulta) )
        }/*( boolFechaCreacionDesde || boolFechaCreacionHasta || boolFechaEmisionDesde || boolFechaEmisionHasta || boolFechaAutorizacionDesde 
            || boolFechaAutorizacionHasta || boolFechaPagCreacionDesde || boolFechaPagCreacionHasta || boolFechaPagContabilizacionDesde 
            || boolFechaPagContabilizacionHasta )*/
        else
        {
            Ext.Msg.alert("Atención", "Debe elegir un rango de fecha válido para realizar la consulta respectiva");
        }
    }