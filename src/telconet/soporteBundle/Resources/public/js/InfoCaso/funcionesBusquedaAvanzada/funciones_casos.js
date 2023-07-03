function asignarCaso(rec, recCaso){
	var id_caso = recCaso.get('id_caso');
	var numero = recCaso.get('numero_caso');
	var fecha = recCaso.get('fecha_apertura');
	var hora = recCaso.get('hora_apertura');
	var version_inicial = recCaso.get('version_ini');
	
	var flag1 = recCaso.get('flag1');
	var flag2 = recCaso.get('flag2');
	var flag3 = recCaso.get('flag3');
	var flagCreador = recCaso.get('flagCreador');
	var flagBoolAsignado = recCaso.get('flagBoolAsignado');
	var flagAsignado = recCaso.get('flagAsignado');
	var flagTareasAbiertas = rec.get('flagTareasAbiertas');
	var flagTareasSolucionadas = rec.get('flagTareasSolucionadas');
	var ultimo_estado = recCaso.get('estado');
	
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
	
    storeFiliales = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: '../administracion/comercial/info_oficina_grupo/grid',
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
                {name:'id_oficina_grupo', mapping:'id_oficina_grupo'},
                {name:'nombre_oficina', mapping:'nombre_oficina'}
              ]
    });
    storeAreas = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            url: '../soporte/info_caso/' + 'getAreas',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id_area', mapping:'id_area'},
                {name:'nombre_area', mapping:'nombre_area'}
              ]
    });
    storeDepartamentos = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            url: '../soporte/info_caso/' + 'getDepartamentos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id_departamento', mapping:'id_departamento'},
                {name:'nombre_departamento', mapping:'nombre_departamento'}
              ]
    });
    storeEmpleados = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            url: '../soporte/info_caso/' + 'getEmpleadosXDepartamento',
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
				metaProperty: 'myMetaData'
            },
			extraParams: {
				soloJefes: 'S'
			}
        },
        fields:
		[
			{name:'id_empleado', mapping:'id_empleado'},
			{name:'nombre_empleado', mapping:'nombre_empleado'}
		],
		listeners: {
			load: function(sender, node, records) {
				var myData_message = storeEmpleados.getProxy().getReader().jsonData.myMetaData.message;
				var myData_boolSuccess = storeEmpleados.getProxy().getReader().jsonData.myMetaData.boolSuccess;
				
				if(myData_boolSuccess != "1")
				{	
					Ext.Msg.alert('Alerta ', myData_message);
				}
				else
				{
					Ext.each(sender, function(record, index){
						if(storeEmpleados.getCount()<=0){
							Ext.Msg.alert('Alerta ', "No existen jefes asignados para este departamento.");
						}
					});
				}
			}
		}
    });	
	
    combo_empleados = new Ext.form.ComboBox({
        id: 'comboEmpleado',
        name: 'comboEmpleado',
        fieldLabel: "Empleado",
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
		queryMode: "remote",
		emptyText: '',
		disabled: true
    });
	
	formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 200,                                
			msgTarget: 'side'
		},
		items: 
		[
			{
			xtype: 'fieldset',
			defaults: {
				width: 500
			},
                items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Caso:',
						id: 'cas_numero_casoSintoma',
						name: 'cas_numero_casoSintoma',
						value: numero
					},
					{
						xtype: 'combobox',
						fieldLabel: 'Filial',
						id: 'comboFilial',
						name: 'comboFilial',
						store: storeFiliales,
						displayField: 'nombre_oficina',
						valueField: 'id_oficina_grupo',
						queryMode: "remote",
						emptyText: '',
						listeners: {
							select: function(combo){							
								Ext.getCmp('comboArea').reset();	
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleado').reset();
																
								Ext.getCmp('comboArea').setDisabled(false);
								Ext.getCmp('comboDepartamento').setDisabled(true);
								Ext.getCmp('comboEmpleado').setDisabled(true);
								
								presentarAreas(combo.getValue(),combo.getRawValue(),this);
							}
						},
						forceSelection: true
					}, 
					{
						xtype: 'combobox',
						fieldLabel: 'Area',
						id:'comboArea',
						name:'comboArea',
						store: storeAreas,
						displayField: 'nombre_area',
						valueField: 'id_area',
						queryMode: "remote",
						emptyText: '',
						disabled: true,
						listeners: {
							select: function(combo){
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleado').reset();
																
								Ext.getCmp('comboDepartamento').setDisabled(false);
								Ext.getCmp('comboEmpleado').setDisabled(true);
								
								var oficina = Ext.getCmp('comboFilial').value;
								presentarDepartamentos(combo.getValue(), combo.getRawValue(), this, oficina);
							}
						},
						forceSelection: true
					},
					{
						xtype: 'combobox',
						fieldLabel: 'Departamento',
						id:'comboDepartamento',
						name:'comboDepartamento',
						store: storeDepartamentos,
						displayField: 'nombre_departamento',
						valueField: 'id_departamento',
						queryMode: "remote",
						emptyText: '',
						disabled: true,
						listeners: {
							select: function(combo){
								Ext.getCmp('comboEmpleado').reset();
								
								Ext.getCmp('comboEmpleado').setDisabled(false);
								
								var oficina = Ext.getCmp('comboFilial').value;
								presentarEmpleadosJefes(combo.getValue(), combo.getRawValue(), this, oficina);
							}
						},
						allowBlank: false,
						forceSelection: true
					},
					combo_empleados,
					/*
				                    {
				                        xtype: 'combobox',
				                        fieldLabel: 'Empleado',
				                        id:'comboEmpleado',
				                        name:'comboEmpleado',
				                        store: storeEmpleados,
				                        displayField: 'nombre_empleado',
				                        valueField: 'id_empleado',
				                        emptyText: ''
				                    },           
					*/            
                    {
						xtype: 'textarea',
						id: 'observacionAsignacion',
						fieldLabel: 'Observacion',
						name: 'observacion',
						rows: 3,
						allowBlank: false,
						value: rec.data.observacion_asignacionCaso
                    },{
						xtype: 'textfield',
						id: 'asuntoAsignacion',
						fieldLabel: 'Asunto del correo',
						name: 'asunto',
						allowBlank: true,
						value: rec.data.asunto_asignacionCaso
                    }
                ]
            }
		],
		buttons: [{
			text: 'Guardar',
			formBind: true,
			handler: function(){
				var array_data = new Array();
				
				if(Ext.getCmp('comboEmpleado') && Ext.getCmp('comboEmpleado').value)
				{						
					var comboEmpleado = Ext.getCmp('comboEmpleado').value;
					var valoresComboEmpleado = comboEmpleado.split("@@"); 
					var idEmpleado = valoresComboEmpleado[0];
					var idPersonaEmpresaRol = valoresComboEmpleado[1];
					
					rec.data.departamento_asignacionCaso = Ext.getCmp('comboDepartamento').value;
					rec.data.empleado_asignacionCaso = idEmpleado;
					rec.data.personaEmpresaRol_asignacionCaso = idPersonaEmpresaRol;
					rec.data.nombre_asignacionCaso = Ext.getCmp('comboEmpleado').getRawValue();
					rec.data.observacion_asignacionCaso = Ext.getCmp('observacionAsignacion').value;
					rec.data.asunto_asignacionCaso = Ext.getCmp('asuntoAsignacion').value;
					rec.data.origen = "Nuevo";
			
					rec.commit();
					gridHipotesis.getView().refresh();
					
					winAsignarCaso.destroy();
				}
				else
				{
                    Ext.Msg.alert('Alerta ', 'Por favor escoja el empleado');
				}
			}
		},{
			text: 'Cancelar',
			handler: function(){
				winAsignarCaso.destroy();
			}
		}]
	});

	winAsignarCaso = Ext.create('Ext.window.Window', {
		title: 'Asignar Caso',
		modal: true,
		closable: false,
		width: 650,
		layout: 'fit',
		items: [formPanel]
	}).show();
}

function cerrarCaso(rec){
	var id_caso = rec.get('id_caso');
	var numero = rec.get('numero_caso');
	var fecha = rec.get('fecha_apertura');
	var hora = rec.get('hora_apertura');
	var version_inicial = rec.get('version_ini');
	var tipo_afectacion = $('#tipo_afectacion').val();
	var flag1 = rec.get('flag1');
	var flag2 = rec.get('flag2');
	var flag3 = rec.get('flag3');
	var flagCreador = rec.get('flagCreador');
	var flagBoolAsignado = rec.get('flagBoolAsignado');
	var flagAsignado = rec.get('flagAsignado');
	var flagTareasAbiertas = rec.get('flagTareasAbiertas');
	var flagTareasSolucionadas = rec.get('flagTareasSolucionadas');
	var ultimo_estado = rec.get('estado');
	
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
    
	cierre_comboHipotesisStore = new Ext.data.Store({ 
		total: 'total',
		proxy: {
			type: 'ajax',
			url: '../administracion/soporte/admi_hipotesis/grid',
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
			{name:'id_hipotesis', mapping:'id_hipotesis'},
			{name:'nombre_hipotesis', mapping:'nombre_hipotesis'}
		]
	});
		
	 // Create the combo box, attached to the states data store
	cierre_comboHipotesis = Ext.create('Ext.form.ComboBox', {
		id:'tituloFinalHipotesis',
		name:'tituloFinalHipotesis',
		store: cierre_comboHipotesisStore,
		displayField: 'nombre_hipotesis',
		valueField: 'id_hipotesis',
		height:30,
		border:0,
		margin:0,
		fieldLabel: 'Titulo Final',	
		queryMode: "remote",
		emptyText: ''
	});
						
    formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 200,                                
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',
				defaults: {
					width: 500
				},
				items: 
				[
					{
						xtype: 'textfield',
						fieldLabel: 'Caso:',
						id: 'numero_casoSintoma',
						name: 'numero_casoSintoma',
						value: numero,
						readOnly:true,
					},{
						xtype: 'datefield',
						fieldLabel: 'Fecha de Cierre:',
						id: 'fe_cierre_value',
						name:'fe_cierre_value',
						editable: false,
						format: 'Y-m-d',
						readOnly:true,
						value:new Date(),
						maxValue: new Date()  // limited to the current date or prior
					},{
						xtype: 'timefield',
						format: 'H:i',
						fieldLabel: 'Hora de Cierre:',
						id: 'ho_cierre_value',
						name: 'ho_cierre_value',
						minValue: '00:01 AM',
						maxValue: '23:59 PM',
						increment: 1,
						readOnly:true,
						value:new Date()
					},
					cierre_comboHipotesis,
					/*{
						xtype: 'textfield',
						id: 'tituloFinal',
						fieldLabel: 'Titulo Final Texto',
						name: 'tituloFinal',
						allowBlank: true
					},*/
					{
						xtype: 'textarea',
						id: 'versionFinal',
						fieldLabel: 'Version final',
						name: 'versionFinal',
						rows: 3,
						allowBlank: false
					}
				]
			}
		],
		buttons: 
		[
			{
				text: 'Guardar',
				formBind: true,
				handler: function(){
					var array_data = new Array();
					if(Ext.getCmp('tituloFinalHipotesis').value)
					{
						if(Ext.getCmp('versionFinal').value)
						{
							conn.request({
								method: 'POST',
								params :{
									id_caso: id_caso,
									fe_cierre: Ext.getCmp('fe_cierre_value').value,
									hora_cierre: Ext.getCmp('ho_cierre_value').value,
									/*tituloFinal: Ext.getCmp('tituloFinal').value,*/
									tituloFinalHipotesis: Ext.getCmp('tituloFinalHipotesis').value,
									versionFinal: Ext.getCmp('versionFinal').value
								},
								url: '../soporte/info_caso/' +  'cerrarCaso',
								success: function(response){
									Ext.Msg.alert('Mensaje','Se cerro el caso.', function(btn){
										if(btn=='ok'){						
											winCerarrCaso.destroy();
											storeSoporte.load();
										}
									});
								},
								failure: function(rec, op) {
									var json = Ext.JSON.decode(op.response.responseText);
									Ext.Msg.alert('Alerta ',json.mensaje);
								}
							});
							winCerarrCaso.destroy();
						}
						else
							Ext.Msg.alert('Alerta ', 'Debe ingresar la version Final');
					}
					else
						Ext.Msg.alert('Alerta ', 'Debe escoger el titulo Final');					
				}
			},{
				text: 'Cancelar',
				handler: function(){
					winCerarrCaso.destroy();
				}
			}
		]
	});

	winCerarrCaso = Ext.create('Ext.window.Window', {
		title: 'Cerrar Caso',
		modal: true,
		closable: false,
		width: 650,
		layout: 'fit',
		items: [formPanel]
	}).show();

}