Ext.onReady(function() { 	
    storeNivelCriticidad = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../../administracion/soporte/admi_nivel_criticidad/grid',
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
			{name:'id_nivel_criticidad', mapping:'id_nivel_criticidad'},
			{name:'nombre_nivel_criticidad', mapping:'nombre_nivel_criticidad'}
		]
    });

    // Create the combo box, attached to the states data store
    comboNivelCriticidad = Ext.create('Ext.form.ComboBox', {
        id:'sop_comboNivelCriticidad',
        fieldLabel: 'Nivel de Criticidad:',
        store: storeNivelCriticidad,
        displayField: 'nombre_nivel_criticidad',
        valueField: 'id_nivel_criticidad',
        height:30,
		width: 360,
        border:0,
        margin:0,
		queryMode: "remote",
		emptyText: ''
    });
    
    storeTipoCaso = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../../administracion/soporte/admi_tipo_caso/grid',
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
                {name:'id_tipo_caso', mapping:'id_tipo_caso'},
                {name:'nombre_tipo_caso', mapping:'nombre_tipo_caso'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboTipoCaso = Ext.create('Ext.form.ComboBox', {
        id:'sop_comboTipoCaso',
        fieldLabel: 'Tipo Caso:',
        store: storeTipoCaso,
        displayField: 'nombre_tipo_caso',
        valueField: 'id_tipo_caso',
        height:30,
		width: 360,
        border:0,
        margin:0,
		queryMode: "remote",
		emptyText: ''
    });
    
    storeEmpleados = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEmpleados',
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
                {name:'login_empleado', mapping:'login_empleado'},
                {name:'nombre_empleado', mapping:'nombre_empleado'}
              ]
    });

    storeEmpleados2 = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEmpleados',
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
                {name:'login_empleado', mapping:'login_empleado'},
                {name:'nombre_empleado', mapping:'nombre_empleado'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboEmpleados1 = Ext.create('Ext.form.ComboBox', {
        id:'sop_comboEmpleados1',
        fieldLabel: 'Usuario Apertura:',
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'login_empleado',
        height:30,
		width: 360,
        border:0,
        margin:0,
		queryMode: "remote",
		emptyText: ''
    });
    
    comboEmpleados2 = Ext.create('Ext.form.ComboBox', {
        id:'sop_comboEmpleados2',
        fieldLabel: 'Usuario Cierre:',
        store: storeEmpleados2,
        displayField: 'nombre_empleado',
        valueField: 'login_empleado',
        height:30,
		width: 360,
        border:0,
        margin:0,	
		queryMode: "remote",
		emptyText: ''
    });
	
	
    storeFiliales_index = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: '../../administracion/comercial/info_oficina_grupo/grid',
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
    storeAreas_index = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            url: '../../soporte/info_caso/getAreas',
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
    storeDepartamentos_index = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            url: '../../soporte/info_caso/getDepartamentos',
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
    storeEmpleados_index = new Ext.data.Store({ 
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            url: '../../soporte/info_caso/getEmpleadosXDepartamento',
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
				var myData_message = storeEmpleados_index.getProxy().getReader().jsonData.myMetaData.message;
				var myData_boolSuccess = storeEmpleados_index.getProxy().getReader().jsonData.myMetaData.boolSuccess;
				
				if(myData_boolSuccess != "1")
				{	
					Ext.Msg.alert('Alerta ', myData_message);
				}
				else
				{
					Ext.each(sender, function(record, index){
						if(storeEmpleados_index.getCount()<=0){
							Ext.Msg.alert('Alerta ', "No existen jefes asignados para este departamento.");
						}
					});
				}
			}
		}
    });	
	
    comboFilial_index = Ext.create('Ext.form.ComboBox', {
        id:'sop_comboFilial_index',
        name:'sop_comboFilial_index',
        fieldLabel: 'Filial Asignada:',
        store: storeFiliales_index,
        displayField: 'nombre_oficina',
        valueField: 'id_oficina_grupo',
        height:30,
		width: 360,
        border:0,
        margin:0,	
		queryMode: "remote",
		emptyText: '',
		listeners: {
			select: function(combo){							
				Ext.getCmp('sop_comboArea_index').reset();	
				Ext.getCmp('sop_comboDepartamento_index').reset();
				Ext.getCmp('sop_comboEmpleado_index').reset();
											
				Ext.getCmp('sop_comboArea_index').setDisabled(false);
				Ext.getCmp('sop_comboDepartamento_index').setDisabled(true);
				Ext.getCmp('sop_comboEmpleado_index').setDisabled(true);
				
				presentarAreasIndex(combo.getValue(),combo.getRawValue(),this);
			}
		}
    });
    comboArea_index = Ext.create('Ext.form.ComboBox', {
        id:'sop_comboArea_index',
        name:'sop_comboArea_index',
        fieldLabel: 'Area Asignada:',
        store: storeAreas_index,
        displayField: 'nombre_area',
        valueField: 'id_area',
        height:30,
		width: 360,
        border:0,
        margin:0,	
		queryMode: "remote",
		emptyText: '',
		disabled: true,
		listeners: {
			select: function(combo){					
				Ext.getCmp('sop_comboDepartamento_index').reset();
				Ext.getCmp('sop_comboEmpleado_index').reset();
												
				Ext.getCmp('sop_comboDepartamento_index').setDisabled(false);
				Ext.getCmp('sop_comboEmpleado_index').setDisabled(true);
				
				var oficina = Ext.getCmp('sop_comboFilial_index').value;
				presentarDepartamentosIndex(combo.getValue(),combo.getRawValue(),this, oficina);
			}
		}
    });	
	comboDepartamento_index = Ext.create('Ext.form.ComboBox', {
        id:'sop_comboDepartamento_index',
        name:'sop_comboDepartamento_index',
        fieldLabel: 'Departamento Asignado:',
        store: storeDepartamentos_index,
        displayField: 'nombre_departamento',
        valueField: 'id_departamento',
        height:30,
		width: 360,
        border:0,
        margin:0,	
		queryMode: "remote",
		emptyText: '',
		disabled: true,
		listeners: {
			select: function(combo){					
				Ext.getCmp('sop_comboEmpleado_index').reset();
												
				Ext.getCmp('sop_comboEmpleado_index').setDisabled(false);
				
				var oficina = Ext.getCmp('sop_comboFilial_index').value;
				presentarEmpleadosJefesIndex(combo.getValue(),combo.getRawValue(),this, oficina);
			}
		}
    });	
    comboEmpleado_index = new Ext.form.ComboBox({
        id: 'sop_comboEmpleado_index',
        name: 'sop_comboEmpleado_index',
        fieldLabel: "Empleado Asignado",
        store: storeEmpleados_index,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        height:30,
		width: 360,
        border:0,
        margin:0,	
		queryMode: "remote",
		emptyText: '',
		disabled: true
    });
		
	comboHipotesisStore_index = new Ext.data.Store({ 
		total: 'total',
		proxy: {
			type: 'ajax',
			url: '../../administracion/soporte/admi_hipotesis/grid',
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
	comboHipotesis_index = Ext.create('Ext.form.ComboBox', {
		id:'sop_comboHipotesis_index',
		name:'sop_comboHipotesis_index',
		store: comboHipotesisStore_index,
		displayField: 'nombre_hipotesis',
		valueField: 'id_hipotesis',
		width: 250,
		height:30,
		border:0,
		margin:0,
		fieldLabel: 'Hipotesis:',	
		queryMode: "remote",
		emptyText: ''
	});
		
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanelSoporte = Ext.create('Ext.panel.Panel', {
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
        height: 520,
        title: 'Criterios Soporte',
		header: false,
		items: 
		[    
			{
				xtype:'fieldset',   
				width: 900,
				columnWidth: 0.5,
				title: 'Casos',
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
						id: 'sop_txtNumero',
						fieldLabel: 'Numero',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'combobox',
						fieldLabel: 'Estado',
						id: 'sop_sltEstado',
						value:'Todos',
						store: [
							['Todos','Todos'],
							['Creado','Creado'],
							['Asignado','Asignado'],
							['Abierto','Abierto'],
							['Cerrado','Cerrado']
						],
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
			
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'textfield',
						id: 'sop_txtTituloInicial',
						name: 'sop_txtTituloInicial',
						fieldLabel: 'Titulo Inicial',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'textfield',
						id: 'sop_txtVersionInicial',
						name: 'sop_txtVersionInicial',
						fieldLabel: 'Version Inicial',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'fieldcontainer',
						fieldLabel: 'Titulo Final',
						items: [
							{
								xtype: 'textfield',
								width: 250,
								id: 'sop_txtTituloFinal',
								name: 'sop_txtTituloFinal',
								fieldLabel: 'Texto:',
								value: ''
							},
							comboHipotesis_index
						]
					},
					/*{
						xtype: 'textfield',
						id: 'sop_txtTituloFinal',
						name: 'sop_txtTituloFinal',
						fieldLabel: 'Titulo Final',
						value: '',
						width: 360
					},*/
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'textfield',
						id: 'sop_txtVersionFinal',
						name: 'sop_txtVersionFinal',
						fieldLabel: 'Version Final',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					comboTipoCaso,
					{html:"&nbsp;",border:false,width:80},
					comboNivelCriticidad,
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'textfield',
						id: 'sop_txtClienteAfectado',
						fieldLabel: 'Cliente Afectado',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:80},
					{
						xtype: 'textfield',
						id: 'sop_txtLoginAfectado',
						fieldLabel: 'Login Afectado',
						value: '',
						width: 360
					},
					{html:"&nbsp;",border:false,width:50},
			
					{html:"&nbsp;",border:false,width:50},
					comboFilial_index,
					{html:"&nbsp;",border:false,width:80},
					comboArea_index,
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					comboDepartamento_index,
					{html:"&nbsp;",border:false,width:80},
					comboEmpleado_index,
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					comboEmpleados1,
					{html:"&nbsp;",border:false,width:80},
					comboEmpleados2,
					{html:"&nbsp;",border:false,width:50},
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'fieldcontainer',
						fieldLabel: 'Fecha Apertura',
						//layout: 'hbox',
						items: [
							{
								xtype: 'datefield',
								width: 250,
								id: 'sop_feAperturaDesde',
								fieldLabel: 'Desde:',
								format: 'Y-m-d',
								editable: false
							},
							{
								xtype: 'datefield',
								width: 250,
								id: 'sop_feAperturaHasta',
								fieldLabel: 'Hasta:',
								format: 'Y-m-d',
								editable: false
							}
						]
					},
					
					
					{html:"&nbsp;",border:false,width:50},
					{
						xtype: 'fieldcontainer',
						fieldLabel: 'Fecha Cierre',
						//layout: 'hbox',
						items: [
							{
								xtype: 'datefield',
								width: 250,
								id: 'sop_feCierreDesde',
								fieldLabel: 'Desde:',
								format: 'Y-m-d',
								editable: false
							},
							{
								xtype: 'datefield',
								width: 250,
								id: 'sop_feCierreHasta',
								fieldLabel: 'Hasta:',
								format: 'Y-m-d',
								editable: false
							}
						]
					}
			
				]
			}
		],	
        renderTo: 'filtro_soporte'
    }); 
});


function llenarSoporte()
{
    Ext.tip.QuickTipManager.init();         	 
         
    $('#grid').html("");
	
	if(isNaN(comboTipoCaso.getValue())) comboTipoCaso.setValue('');
	if(isNaN(comboHipotesis_index.getValue())) comboHipotesis_index.setValue('');
	if(isNaN(comboNivelCriticidad.getValue())) comboNivelCriticidad.setValue('');
	if(isNaN(comboFilial_index.getValue())) comboFilial_index.setValue('');
	if(isNaN(comboArea_index.getValue())) comboArea_index.setValue('');
	if(isNaN(comboDepartamento_index.getValue())) comboDepartamento_index.setValue('');
	if(isNaN(comboEmpleado_index.getValue())) comboEmpleado_index.setValue('');
	//if(isNaN(comboEmpleados1.getValue())) comboEmpleados1.setValue('');
	//if(isNaN(comboEmpleados2.getValue())) comboEmpleados2.setValue('');
				
    storeSoporte = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : 'buscar_datos_soporte',
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
		
				sop_numero: Ext.getCmp('sop_txtNumero').value,
				sop_estado: Ext.getCmp('sop_sltEstado').value,
				sop_tituloInicial: Ext.getCmp('sop_txtTituloInicial').value,
				sop_versionInicial: Ext.getCmp('sop_txtVersionInicial').value,
				sop_tituloFinal: Ext.getCmp('sop_txtTituloFinal').value,
				sop_tituloFinalHip: comboHipotesis_index.getValue(),
				sop_versionFinal: Ext.getCmp('sop_txtVersionFinal').value,
				sop_tipoCaso: comboTipoCaso.getValue(),
				sop_nivelCriticidad: comboNivelCriticidad.getValue(),
				sop_clienteAfectado: Ext.getCmp('sop_txtClienteAfectado').value,
				sop_loginAfectado: Ext.getCmp('sop_txtLoginAfectado').value,
				sop_ca_filial: comboFilial_index.getValue(),
				sop_ca_area: comboArea_index.getValue(),
				sop_ca_departamento: comboDepartamento_index.getValue(),
				sop_ca_empleado: comboEmpleado_index.getValue(),
				sop_usrApertura: comboEmpleados1.getValue(),
				sop_usrCierre: comboEmpleados2.getValue(),
				sop_feAperturaDesde: Ext.getCmp('sop_feAperturaDesde').value,
				sop_feAperturaHasta: Ext.getCmp('sop_feAperturaHasta').value,
				sop_feCierreDesde: Ext.getCmp('sop_feCierreDesde').value,
				sop_feCierreHasta: Ext.getCmp('sop_feCierreHasta').value
			}
        },
		fields:
		[
			{name:'id_caso', mapping:'id_caso'},
			{name:'numero_caso', mapping:'numero_caso'},
			{name:'titulo_ini', mapping:'titulo_ini'},
			{name:'titulo_fin', mapping:'titulo_fin'},
			{name:'version_ini', mapping:'version_ini'},
			{name:'version_fin', mapping:'version_fin'},
			{name:'departamento_asignado', mapping:'departamento_asignado'},
			{name:'empleado_asignado', mapping:'empleado_asignado'},
			{name:'oficina_asignada', mapping:'oficina_asignada'},
			{name:'empresa_asignada', mapping:'empresa_asignada'},
			{name:'asignado_por', mapping:'asignado_por'},
			{name:'fecha_asignacionCaso', mapping:'fecha_asignacionCaso'},
			{name:'fecha_apertura', mapping:'fecha_apertura'},
			{name:'hora_apertura', mapping:'hora_apertura'},
			{name:'fecha_cierre', mapping:'fecha_cierre'},
			{name:'hora_cierre', mapping:'hora_cierre'},
			{name:'estado', mapping:'estado'},
			{name:'usuarioApertura', mapping:'usuarioApertura'},
			{name:'usuarioCierre', mapping:'usuarioCierre'},
			{name:'action1', mapping:'action1'},
			{name:'action2', mapping:'action2'},
			{name:'action3', mapping:'action3'},
			{name:'action4', mapping:'action4'},
			{name:'action5', mapping:'action5'},
			{name:'action6', mapping:'action6'},
			{name:'action7', mapping:'action7'},
			{name:'action8', mapping:'action8'},
			{name:'flag1', mapping:'flag1'},
			{name:'flag2', mapping:'flag2'},
			{name:'flag3', mapping:'flag3'},
			{name:'flagCreador', mapping:'flagCreador'},
			{name:'flagBoolAsignado', mapping:'flagBoolAsignado'},
			{name:'flagAsignado', mapping:'flagAsignado'},
			{name:'flagTareasTodas', mapping:'flagTareasTodas'},
			{name:'flagTareasAbiertas', mapping:'flagTareasAbiertas'},
			{name:'flagTareasSolucionadas', mapping:'flagTareasSolucionadas'},
			{name:'siTareasAbiertas', mapping:'siTareasAbiertas'},
			{name:'siTareasSolucionadas', mapping:'siTareasSolucionadas'},
			{name:'siTareasTodas', mapping:'siTareasTodas'}
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
				gridSoporte = "";
				$('#grid').html("");
				$('#tr_error').css("display", "none");
				$('#busqueda_error').html("");
				
				if(storeSoporte.getCount()>0){														
					var pluginExpanded = true;
					
					gridSoporte = Ext.create('Ext.grid.Panel', {
						width: 1300,
						height: 480,
						store: storeSoporte,
						loadMask: true,
						frame: false,    
						viewConfig: { enableTextSelection: true },      
						columns:
						[		
							{
							  id: 'id_caso',
							  header: 'IdCaso',
							  dataIndex: 'id_caso',
							  hidden: true,
							  hideable: false
							},	
							{
							  id: 'flag1',
							  header: 'flag1',
							  dataIndex: 'flag1',
							  hidden: true,
							  hideable: false
							},	
							{
							  id: 'flag2',
							  header: 'flag2',
							  dataIndex: 'flag2',
							  hidden: true,
							  hideable: false
							},	
							{
							  id: 'flag3',
							  header: 'flag3',
							  dataIndex: 'flag3',
							  hidden: true,
							  hideable: false
							},	
							{
							  id: 'flagCreador',
							  header: 'flagCreador',
							  dataIndex: 'flagCreador',
							  hidden: true,
							  hideable: false
							},	
							{
							  id: 'flagBoolAsignado',
							  header: 'flagBoolAsignado',
							  dataIndex: 'flagBoolAsignado',
							  hidden: true,
							  hideable: false
							},	
							{
							  id: 'flagAsignado',
							  header: 'flagAsignado',
							  dataIndex: 'flagAsignado',
							  hidden: true,
							  hideable: false
							},		
							{
							  id: 'flagTareasTodas',
							  header: 'flagTareasTodas',
							  dataIndex: 'flagTareasTodas',
							  hidden: true,
							  hideable: false
							},
							{
							  id: 'flagTareasAbiertas',
							  header: 'flagTareasAbiertas',
							  dataIndex: 'flagTareasAbiertas',
							  hidden: true,
							  hideable: false
							},	
							{
							  id: 'flagTareasSolucionadas',
							  header: 'flagTareasSolucionadas',
							  dataIndex: 'flagTareasSolucionadas',
							  hidden: true,
							  hideable: false
							},
							{
							  id: 'numero_caso',
							  header: 'Numero Caso',
							  dataIndex: 'numero_caso',
							  width: 130,
							  sortable: true
							},
							{
							  id: 'caso',
							  header: 'Caso',
							  xtype: 'templatecolumn', 
							  width: 500,
							  tpl: '<tpl if="version_fin==\'N/A\'">\n\
										  <span class="bold">Titulo Inicial:</span></br>\n\
										  <span class="box-detalle">{titulo_ini}</span>\n\
										  <span class="bold">Version Inicial:</span></br>\n\
										  <span>{version_ini}</span></br>\n\
									</tpl>\n\
									<tpl if="version_fin!=\'N/A\'">\n\
										  <span class="bold">Titulo Final:</span></br>\n\
										  <span class="box-detalle">{titulo_fin}</span>\n\
										  <span class="bold">Version Final:</span></br>\n\
										  <span>{version_fin}</span></br>\n\
									</tpl></br>\n\\n\
									<span class="bold">Empresa Asignada:</span> {empresa_asignada}</br>\n\\n\
									<span class="bold">Oficina Asignada:</span> {oficina_asignada}</br>\n\\n\
									<span class="bold">Departamento Asignado:</span> {departamento_asignado}</br>\n\\n\
									<span class="bold">Asignado Por:</span> {asignado_por}</br>\n\\n\
									<span class="bold">Empleado Asignado:</span> {empleado_asignado}</br>\n\\n\
									<span class="bold">Fecha Asignacion:</span> {fecha_asignacionCaso}</br></br>\n\\n\
									<span class="bold">Usuario Apertura:</span> {usuarioApertura}</br>\n\\n\
									<span class="bold">Usuario Cierre:</span> {usuarioCierre}</br></br>\n\\n\
									<span class="bold">Tareas Creadas:</span> {siTareasTodas}</br>\n\\n\
									<span class="bold">Tareas Abiertas:</span> {siTareasAbiertas}</br>\n\\n\
									<span class="bold">Tareas Solucionadas:</span> {siTareasSolucionadas}'
							},
							{
							  header: 'Fecha Apertura',
							  xtype: 'templatecolumn', 
							  align: 'center',
							  tpl: '<span class="center">{fecha_apertura}</br>{hora_apertura}</span>',
							  width: 150
							},
							{
							  header: 'Fecha Cierre',
							  xtype: 'templatecolumn', 
							  align: 'center',
							  tpl: '<span class="center">{fecha_cierre}</br>{hora_cierre}</span>',
							  width: 150
							},
							{
							  header: 'Estado',
							  dataIndex: 'estado',
							  width: 100,
							  sortable: true
							},
							{
								xtype: 'actioncolumn',
								header: 'Acciones',
								width: 200,
								items: 
								[				
									{
										getClass: function(v, meta, rec) {
											var permiso = $("#ROLE_78-6");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
											
											if (rec.get('action1') == "icon-invisible") 
												this.items[0].tooltip = '';
											else 
												this.items[0].tooltip = 'Ver Caso';
												
											return rec.get('action1')
										},
										tooltip: 'Ver Caso',
										handler: function(grid, rowIndex, colIndex) {
											var rec = storeSoporte.getAt(rowIndex);
											
											var permiso = $("#ROLE_78-6");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
											if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
																			
											if(rec.get('action1')!="icon-invisible")
												window.location = "../soporte/info_caso/"+rec.get('id_caso')+"/show";
											else
												Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
												
										}
									},
									{
										getClass: function(v, meta, rec) 
										{
											var permiso1 = $("#ROLE_78-39");	
											var permiso2 = $("#ROLE_78-42");
											var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);
											var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2.val() == 1 ? true : false);						
											if(!boolPermiso1 || !boolPermiso2){ rec.data.action8 = "icon-invisible"; }
											
											if (rec.get('action8') == "icon-invisible") 
												this.items[1].tooltip = '';
											else 
												this.items[1].tooltip = 'Ver Afectados';
											
											return rec.get('action8');
										},
										handler: function(grid, rowIndex, colIndex) 
										{
											var rec = storeSoporte.getAt(rowIndex);
											
											var permiso1 = $("#ROLE_78-39");	
											var permiso2 = $("#ROLE_78-42");
											var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);
											var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2.val() == 1 ? true : false);						
											if(!boolPermiso1 || !boolPermiso2){ rec.data.action8 = "icon-invisible"; }
											
											if(rec.get('action8')!="icon-invisible")
												verAfectados(rec);
											else
												Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
										}
									},
									{
										getClass: function(v, meta, rec) {
											var permiso = $("#ROLE_78-32");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
											
											if (rec.get('action2') == "icon-invisible") 
												this.items[2].tooltip = '';
											else 
												this.items[2].tooltip = 'Ingresar Sintomas';
												
											return rec.get('action2')
										},
										handler: function(grid, rowIndex, colIndex) 
										{
											var rec = storeSoporte.getAt(rowIndex);
											
											var permiso = $("#ROLE_78-32");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
											if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
																			
											if(rec.get('action2')!="icon-invisible")
												agregarSintoma(rec);
											else
												Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
										}
									},
									{
										getClass: function(v, meta, rec)
										{
											var permiso = $("#ROLE_78-31");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
											
											if (rec.get('action3') == "icon-invisible") 
												this.items[3].tooltip = '';
											else 
												this.items[3].tooltip = 'Ingresar Hipotesis';
											
											return rec.get('action3');
										},
										handler: function(grid, rowIndex, colIndex) 
										{
											var rec = storeSoporte.getAt(rowIndex);
											
											var permiso = $("#ROLE_78-31");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
											if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
																			
											if(rec.get('action3')!="icon-invisible")
												agregarHipotesis(rec);
											else
												Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
										}
									},
									{
										getClass: function(v, meta, rec)
										{
											var permiso = $("#ROLE_78-33");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action4 = "icon-invisible"; }
											
											if (rec.get('action4') == "icon-invisible") 
												this.items[4].tooltip = '';
											else 
												this.items[4].tooltip = 'Ingresar Tarea';
											
											return rec.get('action4');
										},
										handler: function(grid, rowIndex, colIndex)
										{
											var rec = storeSoporte.getAt(rowIndex);
											
											var permiso = $("#ROLE_78-33");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
											if(!boolPermiso){ rec.data.action4 = "icon-invisible"; }
											
											if(rec.get('action4')!="icon-invisible")
												agregarTarea(rec);
											else
												Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
										}
									},
									{
										getClass: function(v, meta, rec) 
										{
											var permiso = $("#ROLE_78-51");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action5 = "icon-invisible"; }
											
											if (rec.get('action5') == "icon-invisible") 
												this.items[5].tooltip = '';
											else 
												this.items[5].tooltip = 'Administrar Tareas';
											
											return rec.get('action5');
										},
										handler: function(grid, rowIndex, colIndex) 
										{
											var rec = storeSoporte.getAt(rowIndex);
											
											var permiso = $("#ROLE_78-51");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
											if(!boolPermiso){ rec.data.action5 = "icon-invisible"; }
											
											if(rec.get('action5')!="icon-invisible")
												administrarTareas(rec);
											else
												Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
										}
									},
									/*{
										getClass: function(v, meta, rec) 
										{
											if (rec.get('action6') == "icon-invisible") 
												this.items[5].tooltip = '';
											else 
												this.items[5].tooltip = 'Asignar Caso';
											
											return rec.get('action6');
										},
										handler: function(grid, rowIndex, colIndex) 
										{
											var rec = storeSoporte.getAt(rowIndex);
											//if(rec.get('action6')!="icon-invisible")
												//asignarCaso(rec.get('id_caso'),rec.get('numero_caso'),rec.get('fecha_apertura'),rec.get('hora_apertura'),rec.get('version_ini'));
										}
									},*/
									{
										getClass: function(v, meta, rec) 
										{
											var permiso = $("#ROLE_78-36");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
											if(!boolPermiso){ rec.data.action7 = "icon-invisible"; }
											
											if (rec.get('action7') == "icon-invisible") 
												this.items[6].tooltip = '';
											else 
												this.items[6].tooltip = 'Cerrar Caso';
											
											return rec.get('action7');
										},
										handler: function(grid, rowIndex, colIndex) 
										{
											var rec = storeSoporte.getAt(rowIndex);
											
											var permiso = $("#ROLE_78-36");
											var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
											if(!boolPermiso){ rec.data.action7 = "icon-invisible"; }
											
											if(rec.get('action7')!="icon-invisible")
												cerrarCaso(rec);
											else
												Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
										}
									}
								]
							}
						],
						bbar: Ext.create('Ext.PagingToolbar', {
							store: storeSoporte,
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

function limpiarSoporte()
{    
    limpiarPrincipal();
	
    Ext.getCmp('sop_txtNumero').value="";
    Ext.getCmp('sop_txtNumero').setRawValue("");
    Ext.getCmp('sop_sltEstado').value="Todos";
    Ext.getCmp('sop_sltEstado').setRawValue("Todos");
	
    Ext.getCmp('sop_comboHipotesis_index').value="";
    Ext.getCmp('sop_comboHipotesis_index').setRawValue("");	
    Ext.getCmp('sop_comboFilial_index').value="";
    Ext.getCmp('sop_comboFilial_index').setRawValue("");	
    Ext.getCmp('sop_comboArea_index').value="";
    Ext.getCmp('sop_comboArea_index').setRawValue("");
    Ext.getCmp('sop_comboDepartamento_index').value="";
    Ext.getCmp('sop_comboDepartamento_index').setRawValue("");
    Ext.getCmp('sop_comboEmpleado_index').value="";
    Ext.getCmp('sop_comboEmpleado_index').setRawValue("");
		
	Ext.getCmp('sop_comboFilial_index').reset();	
	Ext.getCmp('sop_comboArea_index').reset();	
	Ext.getCmp('sop_comboDepartamento_index').reset();
	Ext.getCmp('sop_comboEmpleado_index').reset();								
	Ext.getCmp('sop_comboArea_index').setDisabled(true);
	Ext.getCmp('sop_comboDepartamento_index').setDisabled(true);
	Ext.getCmp('sop_comboEmpleado_index').setDisabled(true);
				
    Ext.getCmp('sop_txtTituloInicial').value="";
    Ext.getCmp('sop_txtTituloInicial').setRawValue("");
    Ext.getCmp('sop_txtVersionInicial').value="";
    Ext.getCmp('sop_txtVersionInicial').setRawValue("");
    Ext.getCmp('sop_txtTituloFinal').value="";
    Ext.getCmp('sop_txtTituloFinal').setRawValue("");
    Ext.getCmp('sop_txtVersionFinal').value="";
    Ext.getCmp('sop_txtVersionFinal').setRawValue("");
	
    Ext.getCmp('sop_comboTipoCaso').value="";
    Ext.getCmp('sop_comboTipoCaso').setRawValue("");	
    Ext.getCmp('sop_comboNivelCriticidad').value="";
    Ext.getCmp('sop_comboNivelCriticidad').setRawValue("");
    Ext.getCmp('sop_txtClienteAfectado').value="";
    Ext.getCmp('sop_txtClienteAfectado').setRawValue("");
    Ext.getCmp('sop_txtLoginAfectado').value="";
    Ext.getCmp('sop_txtLoginAfectado').setRawValue("");
    Ext.getCmp('sop_comboEmpleados1').value="";
    Ext.getCmp('sop_comboEmpleados1').setRawValue("");	
    Ext.getCmp('sop_comboEmpleados2').value="";
    Ext.getCmp('sop_comboEmpleados2').setRawValue("");
	
    Ext.getCmp('sop_feAperturaDesde').value="";
    Ext.getCmp('sop_feAperturaDesde').setRawValue("");	
    Ext.getCmp('sop_feAperturaHasta').value="";
    Ext.getCmp('sop_feAperturaHasta').setRawValue("");
    Ext.getCmp('sop_feCierreDesde').value="";
    Ext.getCmp('sop_feCierreDesde').setRawValue("");	
    Ext.getCmp('sop_feCierreHasta').value="";
    Ext.getCmp('sop_feCierreHasta').setRawValue("");
	
    llenarSoporte();
}

function presentarAreasIndex(id, name, obj)
{
    storeAreas_index.proxy.extraParams = {id_param: id};
    storeAreas_index.load(); 
}

function presentarDepartamentosIndex(id, name, obj, oficina)
{
    storeDepartamentos_index.proxy.extraParams = {id_param: id, id_oficina: oficina};
    storeDepartamentos_index.load(); 
}

function presentarEmpleadosIndex(id, name, obj, oficina)
{	
    storeEmpleados_index.proxy.extraParams = {id_param: id, id_oficina: oficina};
    storeEmpleados_index.load(); 
}

function presentarEmpleadosJefesIndex(id, name, obj, oficina)
{	
    storeEmpleados_index.proxy.extraParams = {id_param: id, id_oficina: oficina, soloJefes: 'S'};
    storeEmpleados_index.load(); 
}


function verAfectados(rec)
{	
	var id_caso = rec.get('id_caso');
	var numero = rec.get('numero_caso');
	var fecha = rec.get('fecha_apertura');
	var hora = rec.get('hora_apertura');
	var version_inicial = rec.get('version_ini');
	
	winVerAfectados = "";
	var formPanelVerAfectados = "";
	
    if (winVerAfectados)
    {
		cierraVentanaByIden(winVerAfectados);
		winVerAfectados = "";
	}

    if (!winSintomas)
    { 
		btncancelar = Ext.create('Ext.Button', {
			text: 'Cerrar',
			cls: 'x-btn-rigth',
			handler: function() {
				cierraVentanaByIden(winVerAfectados);
			}
		});	
		
		var string_html  = "<table width='100%' border='0' >";
		string_html += "    <tr>";
		string_html += "        <td colspan='6'>";
		string_html  = "			<table width='100%' border='0' >";
		string_html += "                <tr style='height:270px'>";
		string_html += "                    <td colspan='4'><div id='criterios_aceptar'></div></td>";
		string_html += "                </tr>";
		string_html += "                <tr style='height:270px'>";
		string_html += "                    <td colspan='4'><div id='afectados_aceptar'></div></td>";
		string_html += "                </tr>";
		string_html += "            </table>";
		string_html += "        </td>";
		string_html += "    </tr>";
		string_html += "</table>";
		
		DivsCriteriosAfectados =  Ext.create('Ext.Component', {
			html: string_html,
			padding: 1,
			layout: 'anchor',
			style: { border: '0' }
		});
		
		
		formPanelVerAfectados = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			layout: 'column',
			fieldDefaults: {
				labelAlign: 'left',
				labelWidth: 150,
				msgTarget: 'side'
			},
			items: 
			[
				{
					xtype: 'fieldset',
					title: 'Información de Afectados',                    
					autoHeight: true,
					width: 1030,
					items: 
					[
						DivsCriteriosAfectados
					]
				}
			]
		});

		winVerAfectados = Ext.create('Ext.window.Window', {
			title: 'Caso ' + numero,
			modal: true,
			width: 1060,
			height: 640,
			resizable: false,
			layout: 'fit',
			items: [formPanelVerAfectados],
			buttonAlign: 'center',
			buttons:[btncancelar]
		}).show(); 
		
		
		////////////////Grid  Criterios////////////////  
		storeCriterios_aceptar = new Ext.data.JsonStore(
		{
			total: 'total',
			pageSize: 400,
			autoLoad: true,
			proxy: {
				type: 'ajax',
				url : '../soporte/info_caso/'+id_caso+'/getCriterios',
				reader: {
					type: 'json',
					totalProperty: 'total',
					root: 'encontrados'
				},
				extraParams: {
					todos: 'YES'
				}
			},
			fields:
			[
				{name:'tipo', mapping:'tipo'},
				{name:'nombre', mapping:'nombre'},
				{name:'id_criterio_afectado', mapping:'id_criterio_afectado'},
				{name:'caso_id', mapping:'caso_id'},
				{name:'criterio', mapping:'criterio'},
				{name:'opcion', mapping:'opcion'}
			]                
		});
		gridCriterios_aceptar = Ext.create('Ext.grid.Panel', {
			title:'Criterios de Seleccion',
			width: 1000,
			height: 260,
			autoRender:true,
			enableColumnResize :false,
			id:'gridCriterios_aceptar',
			store: storeCriterios_aceptar,
			loadMask: true,
			frame:true,
			forceFit:true,
			columns:
			[
				{
				  id: 'aceptar_id_criterio_afectado',
				  header: 'id_criterio_afectado',
				  dataIndex: 'id_criterio_afectado',
				  hidden: true,
				  hideable: false
				},
				{
				  id: 'aceptar_caso_id',
				  header: 'caso_id',
				  dataIndex: 'caso_id',
				  hidden: true,
				  sortable: true
				},
				{
				  id: 'aceptar_tipo_criterio',
				  header: 'Tipo',
				  dataIndex: 'tipo',
				  width:60,
				  hideable: false
				},
				{
				  id: 'aceptar_nombre_tipo_criterio',
				  header: 'Nombre',
				  dataIndex: 'nombre',
				  width:80,
				  hideable: false,
				  sortable: true
				},
				{
				  id: 'aceptar_criterio',
				  header: 'Criterio',
				  dataIndex: 'criterio',
				  width: 70,
				  hideable: false
				},
				{
				  id: 'aceptar_opcion',
				  header: 'Opcion',
				  dataIndex: 'opcion',
				  width: 260,
				  sortable: true
				}
			],
			renderTo: 'criterios_aceptar'
		});
		
		////////////////Grid  Afectados////////////////  
		storeAfectados_aceptar = new Ext.data.JsonStore(
		{
			autoLoad: true,
			pageSize: 4000,
			total: 'total',
			proxy: {
				type: 'ajax',
				url : '../soporte/info_caso/'+id_caso+'/getAfectados',
				reader: {
					type: 'json',
					totalProperty: 'total',
					root: 'encontrados'
				},
				extraParams: {
					todos: 'YES'
				}
			},
			fields:
			[
				{name:'tipo', mapping:'tipo'},
				{name:'nombre', mapping:'nombre'},
				{name:'id', mapping:'id'},
				{name:'id_afectado', mapping:'id_afectado'},
				{name:'id_criterio', mapping:'id_criterio'},
				{name:'caso_id_afectado', mapping:'caso_id_afectado'},
				{name:'nombre_afectado', mapping:'nombre_afectado'},
				{name:'descripcion_afectado', mapping:'descripcion_afectado'}
			]                
		});
		gridAfectados_aceptar = Ext.create('Ext.grid.Panel', {
			title:'Equipos Afectados',
			width: 1000,
			height: 260,
			sortableColumns:false,
			store: storeAfectados_aceptar,
			id:'gridAfectados_aceptar',
			enableColumnResize :false,
			loadMask: true,
			frame:true,
			forceFit:true,
			columns: [
				Ext.create('Ext.grid.RowNumberer'),
				{
				  id: 'aceptar_id',
				  header: 'id',
				  dataIndex: 'id',
				  hidden: true,
				  hideable: false
				},
				 {
				  id: 'aceptar_id_afectado',
				  header: 'id_afectado',
				  dataIndex: 'id_afectado',
				  hidden: true,
				  hideable: false
				},
				{
				  id: 'aceptar_id_criterio',
				  header: 'id_criterio',
				  dataIndex: 'id_criterio',
				  hidden: true,
				  hideable: false
				},
				{
				  id: 'aceptar_caso_id_afectado',
				  header: 'caso_id_afectado',
				  dataIndex: 'caso_id_afectado',
				  hidden: true,
				  hideable: false 
				},
				{
				  id: 'aceptar_tipo_afectado',
				  header: 'Tipo',
				  dataIndex: 'tipo',
				  width:65,
				  hideable: false
				},
				{
				  id: 'aceptar_nombre_tipo_afectado',
				  header: 'Nombre',
				  dataIndex: 'nombre',
				  width:85,
				  hideable: false,
				  sortable: true
				},
				{
				  id: 'aceptar_nombre_afectado',
				  header: 'Parte Afectada',
				  dataIndex: 'nombre_afectado',
				  width:210,
				  sortable: true
				},
				{
				  id: 'aceptar_descripcion_afectado',
				  header: 'Descripcion',
				  dataIndex: 'descripcion_afectado',
				  width:145,
				  sortable: true
				}
			],    
			renderTo: 'afectados_aceptar'
		});
	}
}
