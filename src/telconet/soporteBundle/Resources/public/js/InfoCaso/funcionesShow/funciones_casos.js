function asignarCaso(rec, id_caso,numero,fecha,hora,version_inicial){
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
            url: '../getCiudadesPorEmpresa',
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
            url: '../getDepartamentosPorEmpresaYCiudad',
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
            url: '../getEmpleadosPorDepartamentoCiudad',
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
								
								presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, '','si');
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

var arrayAfectados
function editarCaso(data){
    arrayAfectados=data.serviciosAfectados
	let conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Por favor espere...');
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

    Ext.define('mdlNivelCriticidad', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'nombreCriticidad', type:'string'},
            {name:'idCriticidad', type:'int'}
        ]
    });
	
	let storeNivelCriticidad = new Ext.data.Store({
		autoLoad: true,
		proxy: {
            type: 'ajax',
            method: 'post',
            url: '../getNivelesCriticidad',
            reader: {
                type: 'json'
            }
        },
        model: 'mdlNivelCriticidad'
	});   

    let objNivelCriticidad = Ext.create('mdlNivelCriticidad', 
                            {
                                nombreCriticidad: data.nivelCriticidad, 
                                idCriticidad: data.idNivelCriticidad
                            });

    let comboNivelCriticidad = new Ext.form.ComboBox({
        id: 'cmbNivelCriticidad',
        name: 'cmbNivelCriticidad',
        fieldLabel: "Nivel de Criticidad",
		queryMode: 'remote',
        store: storeNivelCriticidad,
        displayField: 'nombreCriticidad',
        valueField: 'idCriticidad'
    });

    comboNivelCriticidad.select(objNivelCriticidad);

    var tb = Ext.create('Ext.toolbar.Toolbar');
    tb.add("<div style='font-weight:bold;'>Acciones:</div>");
    tb.add({
        icon: '/bundles/soporte/images/iconos/16/agregar_seguimiento.png',
        handler: function() {
            var objCliente=  data.serviciosAfectados.data.items.find(element => element.data.tipo_afectado =='Cliente');
            editarServiciosDeCaso(objCliente);
        },
        tooltip: 'Agregar Servicio'
    });
	
	
	formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 150,                                
			msgTarget: 'side'
		},
        items: [{
			xtype: 'fieldset',
			defaults: {
				width: 600
			},
            items: [{
						xtype: 'displayfield',
						fieldLabel: 'Caso:',
						id: 'numeroCaso',
						name: 'numeroCaso',
						value: data.numeroCaso
					},
					{
						xtype: 'combobox',
						fieldLabel: 'Tipo de Afectación',
						id: 'tipoAfectacion',
						name: 'tipoAfectacion',
						store: [
							['CAIDA', 'Caida'],
							['INTERMITENCIA', 'Intermitencia'],
							['SINAFECTACION', 'Sin Afectacion']
						],
						value: data.tipoAfectacion
					},
                    comboNivelCriticidad,
					{
						xtype: 'textfield',
						id: 'tituloInicial',
						fieldLabel: 'Título Inicial',
						name: 'tituloInicial',
						allowBlank: false,
						blankText: 'El Título Inicial no puede estar vacío',
						value: data.tituloInicial
                    },            
                    {
						xtype: 'textarea',
						id: 'versionInicial',
						fieldLabel: 'Versión Inicial',
						name: 'versionInicial',
						rows: 5,
						allowBlank: false,
						blankText: 'La Versión Inicial no puede estar vacía',
						value: data.versionInicial
                },
                gridAfectados_shows = Ext.create('Ext.grid.Panel', {
                    width: 600,
                    height: 150,
                    sortableColumns: true,
                    store: arrayAfectados,
                    id: 'gridAfectados_shows',
                    tbar: tb,
                    loadMask: true,
                    frame: false,
                    columns: [{
                            id: 'afectadosShow_id_afectados',
                            header: 'id_afectado',
                            dataIndex: 'id_afectado',
                            width: 100
                        },
                        {
                            id: 'afectadosShow_tipo_afectados',
                            header: 'Tipo Afectado',
                            dataIndex: 'tipo_afectado',
                            width: 100
                        },
                        {
                            id: 'afectadosShow_nombre_afectados',
                            header: 'Parte Afectada',
                            dataIndex: 'nombre_afectado',
                            width: 140,
                            sortable: true
                        },
                        {
                            id: 'afectadosShow_descripcion_afectados',
                            header: 'Descripcion',
                            dataIndex: 'descripcion_afectado',
                            width: 160,
                            sortable: true
                        },
                        {
                            xtype: 'actioncolumn',
                            header: 'Accion',
                            width: 100,
                            items: [{
                                getClass: function(v, meta, rec) {
                                    if (rec.get('tipo_afectado') !== "Cliente") {
                                        return 'button-grid-eliminarServicio';
                                    } else {
                                        return 'button-grid-invisible';
                                    }
                                },
                                tooltip: 'Seleccionar',
                                handler: function(grid, rowIndex, colIndex) {
                                    grid.getStore().removeAt(rowIndex);
                    }
                            }]
                    }
                ]
                })

            ]
        }],
		buttons: [{
			text: 'Guardar',
			formBind: true,
            handler: function() {
				let numeroCaso = Ext.getCmp('numeroCaso').value;
				let tipoAfectacion = Ext.getCmp('tipoAfectacion').value;
                let nivelCriticidad = Ext.getCmp('cmbNivelCriticidad').value;
				let tituloInicial = Ext.getCmp('tituloInicial').value;
				let versionInicial = Ext.getCmp('versionInicial').value;
                var array_data=new Array();
                for (var i = 0; i < gridAfectados_shows.getStore().getCount(); i++)
                {
                    array_data.push(gridAfectados_shows.getStore().getAt(i).data);
                }

                let jsonAfectados= Ext.JSON.encode(array_data);
                
                if (gridAfectados_shows.getStore().getCount() === 1)
                {
                    Ext.Msg.alert('Alerta ', 'Debe agregar al menos un servicio afectado');
                }
                else
                {
                    conn.request({
                        method: 'POST',
                        params: {
                            idCaso: data.idCaso,
                            numeroCaso: numeroCaso,
                            tipoAfectacion: tipoAfectacion,
                            nivelCriticidad: nivelCriticidad,
                            tituloInicial: tituloInicial,
                            versionInicial: versionInicial,
                            afectados:jsonAfectados
                        },
                        url: '../actualizar',
                        success: function(response) {
                            console.log(response);
                            var json = Ext.JSON.decode(response.responseText);
                            Ext.Msg.alert('Información ', json.mensaje);
                            winEditarCaso.destroy();
                            document.location.reload(true);
                        },
                        failure: function(rec, op) {
                            console.log(op);
                            var json = Ext.JSON.decode(op.response.responseText);
                            Ext.Msg.alert('Alerta ', json.mensaje);
                        }
                    });
                }
			}
        }, {
			text: 'Cancelar',
            handler: function() {
				winEditarCaso.destroy();
			}
		}]
	});

	let winEditarCaso = Ext.create('Ext.window.Window', {
		title: 'Editar Caso',
		modal: true,
		closable: false,
		width: 650,
		layout: 'fit',
		items: [formPanel]
	}).show();
}

function editarServiciosDeCaso(objCliente) {
    idPuntoCliente = objCliente.data.id_afectado;
    storeServcios = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        autoLoad: true,
        proxy: {
            timeout: 100000,
            type: 'ajax',
            url: url_getServiciosPorCliente,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idPunto: idPuntoCliente
            }
        },
        fields: [
            { name: 'idServicio', mapping: 'idServicio' },
            { name: 'nombreProducto', mapping: 'nombreProducto' },
            { name: 'estadoServicio', mapping: 'estado' },
            { name: 'loginAux', mapping: 'loginAux' }
        ]
    });


    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            msgTarget: 'side'
        },
        items: [{
            xtype: 'fieldset',
            defaults: {
                width: 600
            },
            items: [
                gridServicios = Ext.create('Ext.grid.Panel', {
                    width: 600,
                    height: 294,
                    store: storeServcios,
                    loadMask: true,
                    frame: false,
                    iconCls: 'icon-grid',
                    columns: [{
                            id: 'idServicio',
                            header: 'idServicio',
                            dataIndex: 'idServicio',
                            hidden: true,
                            hideable: false
                        },
                        {
                            header: 'Nombre Servicio/Producto',
                            dataIndex: 'nombreProducto',
                            width: 200,
                            sortable: true
                        },
                        {
                            header: 'Login Aux',
                            dataIndex: 'loginAux',
                            width: 200,
                            sortable: true
                        },
                        {
                            header: 'Estado',
                            dataIndex: 'estadoServicio',
                            width: 100,
                            sortable: true
                        },
                        {
                            xtype: 'actioncolumn',
                            header: 'Accion',
                            width: 100,
                            items: [{
                                getClass: function(v, meta, rec) {
                                    if (rec.get('nombreProducto') !== "Todos" || rec.get('nombreProducto') !== "") {
                                        return 'button-grid-seleccionar';
                                    } else {
                                        return 'button-grid-invisible';
                                    }
                                },
                                tooltip: 'Seleccionar',
                                handler: function(grid, rowIndex, colIndex) {
                                    var servcioCliente = grid.getStore().getAt(rowIndex)
                                    var  objServicioAfectado ={
                                            caso_id_afectado: "",
                                            descripcion_afectado: servcioCliente.data.nombreProducto,
                                            id_afectado: servcioCliente.data.idServicio,
                                            id_criterio: 0,
                                            nombre: "",
                                            nombre_afectado: servcioCliente.data.nombreProducto,
                                            tipo: "Sintoma",
                                            tipo_afectado: "Servicio"
                                        }
                                    
                                  
                                    var position = arrayAfectados.getCount();
                                    var objServicioExiste= arrayAfectados.data.items.find(element => element.data.nombre_afectado == servcioCliente.data.nombreProducto);
                                        if (objServicioExiste) {
                                            Ext.Msg.alert('Alerta ', 'El servicio seleccionado ya se encuentra agregado');
                                        }else{
                                            arrayAfectados.insert(position,objServicioAfectado);
                                            winEditarServiciosDeCaso.destroy();
                                        }
                                }
                            }]
                        }
                    ],
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: storeServcios,
                        displayInfo: true,
                        displayMsg: 'Mostrando {0} - {1} de {2}',
                        emptyMsg: "No hay datos que mostrar."
                    })
                })

            ]
        }],
        buttons: [{
            text: 'Cancelar',
            handler: function() {
                winEditarServiciosDeCaso.destroy();
            }
        }]
    });

    let winEditarServiciosDeCaso = Ext.create('Ext.window.Window', {
        title: 'Editar Servicios',
        modal: true,
        closable: false,
        width: 650,
        layout: 'fit',
        items: [formPanel]
    }).show();

}