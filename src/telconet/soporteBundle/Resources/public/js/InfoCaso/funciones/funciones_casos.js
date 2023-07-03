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
	
	var keyArray =new Array();
	
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
	  
     var storeEmpresas = new Ext.data.Store({
            total: 'total',
            pageSize: 200,
            proxy: {
                type: 'ajax',
                method: 'post',
                url: url_empresaPorSistema,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    app: 'TELCOS'                    
                }                
            },
            fields:
                [
                    {name: 'opcion', mapping: 'nombre_empresa'},
                    {name: 'valor', mapping: 'prefijo'}
                ]
        });
	         
	
    storeCiudades = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: 'getCiudadesPorEmpresa',
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
                {name:'id_canton', mapping:'id_canton'},
                {name:'nombre_canton', mapping:'nombre_canton'}
              ]
	});   
      
      
       storeDepartamentosCiudad = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: 'getDepartamentosPorEmpresaYCiudad',
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
                {name:'id_departamento', mapping:'id_departamento'},
                {name:'nombre_departamento', mapping:'nombre_departamento'}
              ]
	});   
    storeEmpleados = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            url: 'getEmpleadosPorDepartamentoCiudad',
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
						fieldLabel: 'Casos:',
						id: 'cas_numero_casoSintoma',
						name: 'cas_numero_casoSintoma',
						value: numero
					},
					{
						xtype: 'combobox',
						fieldLabel: 'Empresa:',
						id: 'comboEmpresa',
						name: 'comboEmpresa',
						store: storeEmpresas,
						displayField: 'opcion',
						valueField: 'valor',
						queryMode: "remote",
						emptyText: '',
						listeners: {
							select: function(combo){							
							  
								Ext.getCmp('comboCiudad').reset();									
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleado').reset();
																
								Ext.getCmp('comboCiudad').setDisabled(false);								
								Ext.getCmp('comboDepartamento').setDisabled(true);
								Ext.getCmp('comboEmpleado').setDisabled(true);
								
								presentarCiudades(combo.getValue());
							}
						},
						forceSelection: true
					}, 
					{
						xtype: 'combobox',
						fieldLabel: 'Ciudad',
						id: 'comboCiudad',
						name: 'comboCiudad',
						store: storeCiudades,
						displayField: 'nombre_canton',
						valueField: 'id_canton',
						queryMode: "remote",
						emptyText: '',
						disabled: true,
						listeners: {
							select: function(combo){															
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleado').reset();
																								
								Ext.getCmp('comboDepartamento').setDisabled(false);
								Ext.getCmp('comboEmpleado').setDisabled(true);
								
								empresa = Ext.getCmp('comboEmpresa').getValue();
								
								presentarDepartamentosPorCiudad(combo.getValue(),empresa);
							}
						},
						forceSelection: true
					}, 
					{
						xtype: 'combobox',
						fieldLabel: 'Departamento',
						id: 'comboDepartamento',
						name: 'comboDepartamento',
						store: storeDepartamentosCiudad,
						displayField: 'nombre_departamento',
						valueField: 'id_departamento',
						queryMode: "remote",
						minChars: 3,
						emptyText: '',
						disabled: true,
						listeners: {
							select: function(combo){							
								
								
								Ext.getCmp('comboEmpleado').reset();
																																
								Ext.getCmp('comboEmpleado').setDisabled(false);
								
								empresa = Ext.getCmp('comboEmpresa').getValue();
								
								canton  = Ext.getCmp('comboCiudad').getValue();
								
								presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, '' , 'si');
							}
						},
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
