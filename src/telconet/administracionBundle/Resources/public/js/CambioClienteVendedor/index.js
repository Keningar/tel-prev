Ext.Loader.setPath('Ext.ux', '/./././public/js/ext-4.1.1/examples/ux');
Ext.Loader.setPath('Images', '/./././public/js/ext-4.1.1/examples/ux/css/images');
Ext.require([
	'Ext.form.Panel',
	'Ext.ux.form.MultiSelect',
	'Ext.ux.form.ItemSelector',
	'Ext.tip.QuickTipManager',
	'Ext.ux.ajax.JsonSimlet',
	'Ext.ux.ajax.SimManager'
]);

Ext.onReady(function(){

	Ext.tip.QuickTipManager.init();

	var objTextRazonSocial = new Ext.form.TextField({
		id: 'idFieldRazonSocial',
		xtype: 'textfield',
		fieldLabel: '',
		emptyText: 'Filtrar por razón social',
		labelSeparator: '',
		width: 200,
		enableKeyEvents: true,
		filterAnyMatch: true,
		filterIgnoreCase: true,
		filterOnSelector: customFilter,
		listeners: {
			scope: this,
			change: function (field, newValue, oldValue, options) {
				var fromStore = Ext.getCmp('idItemSelector').fromField.boundList.getStore();

				fromStore.clearFilter();

				if (String(newValue).trim() != '') {
					fromStore.filterBy(function (rec, id) {
						return field.filterOnSelector(rec, newValue, 'strRazonSocial');
					}, this);
				}
			}
		},

	});

	Ext.define('ModelVendedor', {
		extend: 'Ext.data.Model',
		fields: [
		{ name: 'login', type: 'string', mapping: 'login' },
		{ name: 'nombre', type: 'string', mapping: 'nombre' },
		{ name: 'intIdPersona', type: 'string', mapping: 'intIdPersona' },
		{ name: 'intIdPersonaEmpresaRol', type: 'string', mapping: 'intIdPersonaEmpresaRol' }]
	});

	Ext.define('ModelRazonSocial', {
		extend: 'Ext.data.Model',
		fields: [
		{ name: 'intIdPersona', type: 'integer', mapping: 'intIdPersona' },
		{ name: 'intIdPersonaEmpresaRol', type: 'integer', mapping: 'intIdPersonaEmpresaRol' },
		{ name: 'strRazonSocial', type: 'string', mapping: 'strRazonSocial' },
		{ name: 'strIdentificacion', type: 'string', mapping: 'strIdentificacion' }]
	});

	var objStoreVendedorOrigen = Ext.create('Ext.data.Store', {
		model: 'ModelVendedor',
		id: 'idStoreVendedorOrigen',
		autoLoad: true,
		proxy:
		{
			type: 'ajax',
			url: urlVendedores,
			timeout: 60000,
			reader:
			{
				type: 'json',
				root: 'registros',
				totalProperty: 'total'
			},
			extraParams:
			{
				strFiltrarTodosEstados: 'S'
			},
			simpleSortMode: true
		},
		listeners:
		{
			load: function(store, records)
			{
				var cbxVendedorOrigen = Ext.getCmp('idCbxVendedorOrigen');

				if (!Ext.isEmpty(cbxVendedorOrigen))
				{
					cbxVendedorOrigen.emptyText = 'Busque o seleccione vendedor origen';
					cbxVendedorOrigen.applyEmptyText();
				}
			}
		}
	});

	var objStoreVendedorDestino = Ext.create('Ext.data.Store', {
		model: 'ModelVendedor',
		id: 'idStoreVendedorDestino',
		autoLoad: true,
		proxy:
		{
			type: 'ajax',
			url: urlVendedores,
			timeout: 60000,
			reader:
			{
				type: 'json',
				root: 'registros',
				totalProperty: 'total'
			},
			simpleSortMode: true
		},
		listeners:
		{
			load: function(store, records)
			{
				var cbxVendedorDestino = Ext.getCmp('idCbxVendedorDestino');

				if (!Ext.isEmpty(cbxVendedorDestino))
				{
					cbxVendedorDestino.emptyText = 'Busque o seleccione vendedor destino';
					cbxVendedorDestino.applyEmptyText();
				}
			}
		}
	});

	var objStoreRazonSocial = Ext.create('Ext.data.Store', {
		model: 'ModelRazonSocial',
		id: 'idStoreRazonSocial',
		autoLoad: false,
		proxy:
		{
			type: 'ajax',
			url: urlRazonSocial,
			reader:
				{
					type: 'json',
					root: 'registros',
					totalProperty: 'total'
				},
			extraParams:
			{
				strLoginVendedor: ''
			},
			simpleSortMode: true,
		},
		listeners:
		{
			beforeload: function(store, operation, eOpts){
				if(!Ext.isEmpty(objSelectorMask)) {
					objSelectorMask.msg = 'Cargando clientes...';
					objSelectorMask.show();
				}
			},
			load: function(store, operation, eOpts){
				if(!Ext.isEmpty(objSelectorMask)) {
					objSelectorMask.hide();
				}
			},
		}
	});

	Ext.create('Ext.form.Panel', {
		width: '100%',
		bodyPadding: 10,
		header: false,
		height: 470,
		renderTo: 'itemselector',
		layout: {
			type: 'vbox',
			pack: 'start',
			align: 'stretch'
		},
		items:[
			{
				xtype: 'itemselector',
				name: 'itemselector',
				id: 'idItemSelector',
				displayField: 'strRazonSocial',
				valueField: 'intIdPersonaEmpresaRol',
				height: 372,
				autoHeight: true,
				imagePath: 'Images',
				store: objStoreRazonSocial,
				allowBlank: false,
				style:'font-size: 8px',
				// msgTarget: 'side',
				fromTitle: 'Clientes disponibles',
				toTitle: 'Clientes seleccionados',
				listeners: {
					change: function( e, newValue, oldValue, eOpts ) {
						var total_clientes = (Ext.getCmp('idItemSelector').up('form').getForm().getValues().itemselector).split(",").length;
						if(total_clientes > 5){
							e.setValue(oldValue);
							Ext.Msg.alert('Alerta', '!Solo puede seleccionar máximo 5 clientes¡');
							return;
						}
						
					}
				}
			},
		],
		dockedItems: [
		{
			xtype: 'toolbar',
			dock: 'top',
			padding: 10,
			layout:
			{
				type: 'hbox',
				pack: 'start',
				align: 'stretch'
			},
			items:
			[{
				xtype: 'container',
				layout:
				{
					type: 'hbox',
					pack: 'start'
				},
				// padding: '4',
				items: [
					{
						xtype: 'combobox',
						id: 'idCbxVendedorOrigen',
						fieldLabel: '',
						fieldSeparator: '',
						displayField: 'nombre',
						valueField: 'login',
						style: 'font-weight:bold; font-size:9px;',
						store: objStoreVendedorOrigen,
						width: 300,
						queryMode: 'local',
						emptyText: 'Cargando vendedor origen...',
						allowBlank: true,
						editable: true,
						transform: 'stateSelect',
						forceSelection: true,
						triggerAction: 'all',
						listeners: {
							beforequery: function(record)
							{
								record.query = new RegExp(record.query, 'i');
								record.forceAll = true;
							},
							select: function(combobox, records, eOpts)
							{

								combobox.disable();
								Ext.getCmp('idItemSelector').reset();
								objTextRazonSocial.setValue('');
								objStoreRazonSocial.load({
									params:
									{
										strLoginVendedor: combobox.getValue()
									},
									addRecords: false,
									scope: this,
									callback: function(records, operation, success){
										if (success) {
											Ext.getCmp('idItemSelector').reset();

											var objResponse = (!Ext.isEmpty(operation.response.responseText))
																? Ext.JSON.decode(operation.response.responseText)
																: null;

											if(!Ext.isEmpty(objResponse) && objResponse.strStatus != '100'){
												Ext.Msg.alert('Error', objResponse.strMessageStatus);
											}
										} else {
											Ext.Msg.alert('Error', 'Existió un error al cargar vendedores, vuelva a intentar.');
										}
										combobox.enable();
									}
								});
							},
						}
					},
					{
						html: '<span style="font-size:25px; background-color:#e8e8e8;">&#8594;</span>',
						margin: '-5 10 0 10',
						border: false,
						width: 'auto',
						height: 30,
					},
					{
						xtype: 'combobox',
						id: 'idCbxVendedorDestino',
						fieldLabel: '',
						fieldSeparator: '',
						displayField: 'nombre',
						valueField: 'login',
						style: Utils.STYLE_BOLD,
						store: objStoreVendedorDestino,
						width: 300,
						queryMode: 'local',
						emptyText: 'Cargando vendedor destino...',
						anyMatch: true,
						allowBlank: true,
						editable: true,
						typeAhead: true,
						transform: 'stateSelect',
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						listeners: {
							beforequery: function(record)
							{
								record.query = new RegExp(record.query, 'i');
								record.forceAll = true;
							}
						}
					},
				]
			},
			{
				xtype: 'tbfill'
			},
			objTextRazonSocial
			]
		},
		{
			xtype: 'toolbar',
			dock: 'bottom',
			ui: 'footer',
			buttonAlign: 'center',
			defaults: {
				minWidth: 75
			},
			items: ['->', {
				text: 'Reset',
				iconCls: 'x-btn-icon button-cambiovendedor-reset',
				handler: function() {
					Ext.getCmp('idItemSelector').reset();
				}
			},
			{
			text: 'Guardar',
			iconCls: 'x-btn-icon button-cambiovendedor-guardar',
			handler: function(){

				if(Ext.isEmpty(Ext.getCmp('idCbxVendedorOrigen').getValue())){
					Ext.Msg.alert('Alerta', 'Seleccione al vendedor origen.');
					return;
				}

				if(Ext.isEmpty(Ext.getCmp('idCbxVendedorDestino').getValue())){
					Ext.Msg.alert('Alerta', 'Seleccione al vendedor destino.');
					return;
				}

				if(Ext.getCmp('idCbxVendedorOrigen').getValue() == Ext.getCmp('idCbxVendedorDestino').getValue()){
					Ext.Msg.alert('Alerta', 'Vendedor origen y vendedor destino deben ser diferentes.');
					return;
				}

				if(Ext.isEmpty(Ext.getCmp('idItemSelector').up('form').getForm().getValues().itemselector)){
					Ext.Msg.alert('Alerta', 'Seleccione al menos un cliente.');
					return;
				}

				var objRecordVendedorOrigen = Ext.getCmp('idCbxVendedorOrigen').getStore().
					findRecord('login', Ext.getCmp('idCbxVendedorOrigen').getValue());
				var objRecordVendedorDestino = Ext.getCmp('idCbxVendedorDestino').getStore().
					findRecord('login', Ext.getCmp('idCbxVendedorDestino').getValue());

				var strMensajeConfirmacion = '¿Desea reasignar los clientes seleccionados?<br/>' +
					'Vendedor Origen:&nbsp;&nbsp;<b>' + Ext.getCmp('idCbxVendedorOrigen').getRawValue() + '</b><br/>' +
					'Vendedor Destino:&nbsp;<b>' + Ext.getCmp('idCbxVendedorDestino').getRawValue() + '</b><br/><br/>' +
					'Se creará una solicitud de <b>\'Cambio Masivo Cliente Vendedor\'</b>, el cual deberá ser autorizada por el Gerente Comercial.';

				Ext.Msg.confirm({
					title: 'Confirmar',
					msg: strMensajeConfirmacion,
					buttons: Ext.Msg.YESNO,
					buttonText:
					{
						yes: 'S&iacute;',
						no: 'No'
					},
					fn: function (button) {
						if (button == 'yes') {
							var objWaitBox = Ext.MessageBox.show({
								msg: 'Espere por favor.',
								title: 'Creando Solicitud',
								progress: true,
								closable: false,
								width: 300,
								wait: true,
								waitConfig: {interval: 200}
							});

							Ext.Ajax.request({
								url: urlCrearSolicitudCambioVendedor,
								method: 'post',
								timeout: 60000,
								params:
								{
									strLoginVendedorOrigen: Ext.getCmp('idCbxVendedorOrigen').getValue(),
									intIdPersonaEmpresaRolOrigen: objRecordVendedorOrigen.get('intIdPersonaEmpresaRol'),
									strLoginVendedorDestino: Ext.getCmp('idCbxVendedorDestino').getValue(),
									intIdPersonaEmpresaRolDestino: objRecordVendedorDestino.get('intIdPersonaEmpresaRol'),
									strIdClientes: Ext.getCmp('idItemSelector').up('form').getForm().getValues().itemselector,
								},
								success: function (response) {
									objWaitBox.close();
									Ext.getCmp('idItemSelector').reset();
									Ext.Msg.alert('Información', !Ext.isEmpty(Ext.JSON.decode(response.responseText).strMessageStatus)
										? Ext.JSON.decode(response.responseText).strMessageStatus
										: 'Existió un error, vuelva a intentar.');
								},
								failure: function (response) {
									objWaitBox.close();
									Ext.Msg.alert('Error', 'Error: ' + !Ext.isEmpty(response)
										? response.status == 500
											? 'Existió un error, vuelva a intentar.'
											: response.statusText
										: 'Existió un error, vuelva a intentar.');
								}
							});
						}
					}
				});
			}
		}]
		}
		]
	});

	var objSelectorMask = new Ext.LoadMask(Ext.getCmp('idItemSelector').down('#multiselectfield-1010').el);

	function customFilter(rec, filter, displayField) {
		var value = rec.get(displayField);

		if (this.filterIgnoreCase) {
			value = value.toLocaleUpperCase();
		}

		if (this.filterIgnoreCase) {
			filter = filter.toLocaleUpperCase();
		}

		if (Ext.isEmpty(filter)) return true;

		var objOpts;
		var objRegex;

		if (this.filterAnyMatch && this.filterWordStart)
		{
			objOpts = this.filterIgnoreCase ? 'i' : '';
			objRegex = new RegExp('(^|[\\s\\.!?;"\'\\(\\)\\[\\]\\{\\}])' + Ext.escapeRe(filter), objOpts);
			return objRegex.test(value);
		}
		else if (this.filterAnyMatch)
		{
			objOpts = this.filterIgnoreCase ? 'i' : '';
			objRegex = new RegExp(Ext.escapeRe(filter), objOpts);
			return objRegex.test(value);
		}
		else
		{
			objOpts = this.filterIgnoreCase ? 'i' : '';
			objRegex = new RegExp('^\s*'+Ext.escapeRe(filter), objOpts);
			return objRegex.test(value);
		}
	}
});