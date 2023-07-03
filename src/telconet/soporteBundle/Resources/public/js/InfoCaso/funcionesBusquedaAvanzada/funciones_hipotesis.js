function agregarHipotesis(recCaso){
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
	var flagCreador = recCaso.get('flagCreador');
	var flagTareasAbiertas = recCaso.get('flagTareasAbiertas');
	var flagTareasSolucionadas = recCaso.get('flagTareasSolucionadas');
	var ultimo_estado = recCaso.get('estado');

    winHipotesis = "";
	var formPanel = "";
	
    if (winSintomas)
    {
		cierraVentanaByIden(winSintomas);
		winSintomas = "";
	}
	
    if (winHipotesis)
    {
		cierraVentanaByIden(winHipotesis);
		winHipotesis = "";
	}
	
    if (!winHipotesis)
    {  
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
	    btnguardar = Ext.create('Ext.Button', {
			text: 'Guardar',
			cls: 'x-btn-rigth',
			handler: function() {
				var valorBool = validarHipotesis();
				
				if(valorBool)
				{
					json_hipotesis = obtenerHipotesis();
					
					conn.request({
						method: 'POST',
						params :{
							id_caso: id_caso,
							hipotesis: json_hipotesis
						},
						url: '../soporte/info_caso/' + 'actualizarHipotesis',
						success: function(response){
							Ext.Msg.alert('Mensaje','Se actualizaron las hipotesis.', function(btn){
								if(btn=='ok'){
									cierraVentanaByIden(winHipotesis);
									storeSoporte.load();
								}
							});
						},
						failure: function(rec, op) {
							var json = Ext.JSON.decode(op.response.responseText);
							Ext.Msg.alert('Alerta ',json.mensaje);
						}
					});
				}
			}
	    });
	    btncancelar = Ext.create('Ext.Button', {
			text: 'Cerrar',
			cls: 'x-btn-rigth',
			handler: function() {
				cierraVentanaByIden(winHipotesis);
			}
	    });
	    
	         
	    storeSintomas = new Ext.data.Store({ 
	        pageSize: 1000,
	        total: 'total',
	        proxy: {
	            type: 'ajax',
	            url : '../soporte/info_caso/' + 'getSintomasXCaso',
	            reader: {
	                type: 'json',
	                totalProperty: 'total',
	                root: 'encontrados'
	            },
	            extraParams: {
	                id: id_caso,
	                nombre: '',
	                estado: 'Activos',
					boolCriteriosAfectados: 'NO'
	            }
	        },
	        
	        fields:
	                  [
	                    {name:'id_sintoma', mapping:'id_sintoma'},
	                    {name:'nombre_sintoma', mapping:'nombre_sintoma'}
	                  ],
	        autoLoad: true,
	        group: 'nombre_sintoma',
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
				   
					winHipotesis = "";
					formPanel = "";
				},
	            load: function(sender, node, records) {
	                Ext.each(records, function(record, index){
	                    if(storeSintomas.getCount()>0){
	                        //storeSintomas.group('id_sintoma');
							
							/*
	                        selModelSintomas = Ext.create('Ext.selection.CheckboxModel', {
								checkOnly: true,
	                            listeners: {
	                                 selectionchange: function(sm, selections) {										
										if(	((flagCreador && !flagBoolAsignado) || (flagBoolAsignado && flagAsignado)) && (flagTareasAbiertas && flagTareasSolucionadas) )
										{
											//alert("Permisos crear Hipotesis");
											gridHipotesis.down('#addButton').setDisabled(selections.length == 0);
										}
										else
										{
											//alert("No tiene Permisos crear Hipotesis");
											gridHipotesis.down('#addButton').setDisabled(true);
										}
									}
								}
							});
	                         */
							 
							gridSintomas = Ext.create('Ext.grid.Panel', {
	                             id:'gridSintomas',
	                             store: storeSintomas,
								 viewConfig: { enableTextSelection: true, stripeRows:true }, 
	                             columnLines: true,
	                             columns: [
									{
		                                 id: 'id_sintoma',
		                                 header: 'SintomaId',
		                                 dataIndex: 'id_sintoma',
		                                 hidden: true,
		                                 hideable: false
		                            }, {
		                                 id: 'nombre_sintoma',
		                                 header: 'Sintoma',
		                                 dataIndex: 'nombre_sintoma',
		                                 width: 320
		                            }
								 ],
	                             //selModel: selModelSintomas,
	                             width: 600,
	                             height: 160,
	                             frame: true,
	                             title: 'Informacion de Sintomas'
							});
							 
							selModelHipotesis = Ext.create('Ext.selection.CheckboxModel', {
								checkOnly: true,
	                            listeners: {
	                                 selectionchange: function(sm, selections) {
										gridHipotesis.down('#removeButton').setDisabled(selections.length == 0);
	                                 }
	                             }
							});
							 
							comboHipotesisStore = new Ext.data.Store({ 
	                             total: 'total',
	                             proxy: {
	                                 type: 'ajax',
	                                 url : '../administracion/soporte/admi_hipotesis/grid',
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
	                        comboHipotesis = Ext.create('Ext.form.ComboBox', {
								id:'comboSintomaHipotesis',
								store: comboHipotesisStore,
								displayField: 'nombre_hipotesis',
								valueField: 'id_hipotesis',
								height:30,
								border:0,
								margin:0,
								fieldLabel: false,	
								queryMode: "remote",
								emptyText: ''
							});
							
							Ext.define('Hipotesis', {
								extend: 'Ext.data.Model',
								fields: [
									{name:'id_sintomaHipotesis', type:'string'},
									{name:'nombre_sintomaHipotesis', type:'string'},
									{name:'id_hipotesis', type:'string'},
									{name:'nombre_hipotesis', type:'string'},
									{name:'criterios_hipotesis', type:'string'},
									{name:'afectados_hipotesis', type:'string'},
									{name:'origen', type:'string'},
									{name:'departamento_asignacionCaso', type:'string'},
									{name:'empleado_asignacionCaso', type:'string'},
									{name:'nombre_asignacionCaso', type:'string'},
									{name:'personaEmpresaRol_asignacionCaso', type:'string'},
									{name:'observacion_asignacionCaso', type:'string'},
									{name:'fecha_asignacionCaso', type:'string'},
									{name:'asunto_asignacionCaso', type:'string'}
								]
							});
							
							storeHipotesis = new Ext.data.Store({ 
								pageSize: 1000,
								total: 'total',
								autoLoad:true,
								proxy: {
									type: 'ajax',
									url : '../soporte/info_caso/' + 'getHipotesisXCaso',
									reader: {
										type: 'json',
										totalProperty: 'total',
										root: 'encontrados'
									},
									extraParams: {
										band: 'hipotesis',
										id: id_caso,
										nombre: '',
										estado: 'Activos'
									}
								},
								fields:
							   [
									{name:'id_sintomaHipotesis', mapping:'id_sintomaHipotesis'},
									{name:'nombre_sintomaHipotesis', mapping:'nombre_sintomaHipotesis'},
									{name:'id_hipotesis', mapping:'id_hipotesis'},
									{name:'nombre_hipotesis', mapping:'nombre_hipotesis'},
									{name:'criterios_hipotesis', mapping:'criterios_hipotesis'},
									{name:'afectados_hipotesis', mapping:'afectados_hipotesis'},
									{name:'asunto_asignacionCaso', mapping:'asunto_asignacionCaso'},
									{name:'departamento_asignacionCaso', mapping:'departamento_asignacionCaso'},
									{name:'empleado_asignacionCaso', mapping:'empleado_asignacionCaso'},
									{name:'observacion_asignacionCaso', mapping:'observacion_asignacionCaso'},
									{name:'nombre_asignacionCaso', mapping:'nombre_asignacionCaso'},
									{name:'personaEmpresaRol_asignacionCaso', mapping:'personaEmpresaRol_asignacionCaso'},
									{name:'fecha_asignacionCaso', mapping:'fecha_asignacionCaso'},
									{name:'origen', mapping:'origen'}
								]
							});
							
							cellEditingHipotesis = Ext.create('Ext.grid.plugin.CellEditing', {
								clicksToEdit: 1,
								listeners: {
									beforeedit: function(editor, e){
										var me = this;
										var allowed = !!me.isEditAllowed;
										
										var gridConsulta_ahorita = e.grid;
										var rowIndex_ahorita = e.rowIdx;
										var valorOrigen = gridConsulta_ahorita.store.getAt(rowIndex_ahorita).data.origen;
										
										if(valorOrigen == 'Nuevo')
										{	
											if (!me.isEditAllowed)
											{
												me.isEditAllowed = true;
												me.startEditByPosition({row: e.rowIdx, column: e.colIdx});
											}
										}	
										else
										{
											me.isEditAllowed = false;
										}
										
										return allowed;
									},
									edit: function(){
										this.isEditAllowed = false;
										
										gridHipotesis.getView().refresh();
									}
								}
							});
							
							combo_hipotesis = new Ext.form.ComboBox({
								id:'searchHipotesis_cmp',
								name: 'searchHipotesis_cmp',
								displayField:'nombre_hipotesis',
								valueField: 'id_hipotesis',
								store: comboHipotesisStore,
								loadingText: 'Buscando ...',
								fieldLabel: false,	
								disabled: true,
								queryMode: "remote",
								emptyText: '',
								listClass: 'x-combo-list-small'
							});
	
							text_observacion = new Ext.form.TextField({
								id:'txt_observacion',
								name: 'txt_observacion'
							});
							
							gridHipotesis = Ext.create('Ext.grid.Panel', {
								id:'gridHipotesis',
								store: storeHipotesis,
								viewConfig: { enableTextSelection: true, stripeRows:true }, 
								columnLines: true,
								columns: [{
									id: 'id_sintomaHipotesis',
									header: 'SintomaId',
									dataIndex: 'id_sintomaHipotesis',
									hidden: true,
									hideable: false
								}, {
									id: 'nombre_sintomaHipotesis',
									header: 'Sintoma',
									dataIndex: 'nombre_sintomaHipotesis',
									width: 220,
									hidden: true,
									hideable: false
								},
								{   
									id: 'id_hipotesis',
									header: 'HipotesisId',
									dataIndex: 'id_hipotesis',
									hidden: true,
									hideable: false
	                             }, {
	                                 id: 'nombre_hipotesis',
	                                 header: 'Hipotesis',
	                                 dataIndex: 'nombre_hipotesis',
	                                 width: 220,
	                                 sortable: true,
	                                 renderer: function (value, metadata, record, rowIndex, colIndex, store){
										var dataOrigen = record.data.origen;
										if(dataOrigen == 'Nuevo')
										{	
											combo_hipotesis.setDisabled(false);
										}
										
	                                     record.data.id_hipotesis = record.data.nombre_hipotesis;
	                                     for (var i = 0;i< comboHipotesisStore.data.items.length;i++)
	                                     {
	                                         if (comboHipotesisStore.data.items[i].data.id_hipotesis== record.data.id_hipotesis)
	                                         {
	                                             gridHipotesis.getStore().getAt(rowIndex).data.id_sintoma=record.data.id_hipotesis;

	                                             record.data.id_hipotesis = comboHipotesisStore.data.items[i].data.id_hipotesis;
	                                             record.data.nombre_hipotesis = comboHipotesisStore.data.items[i].data.nombre_hipotesis;
	                                             break;
	                                         }
	                                     }
	                                     return record.data.nombre_hipotesis;
	                                 },
	                                 editor: combo_hipotesis
	                             },
								{   
									id: 'asunto_asignacionCaso',
									dataIndex: 'asunto_asignacionCaso',
									hidden: true,
									hideable: false
	                            },
								{   
									id: 'departamento_asignacionCaso',
									dataIndex: 'departamento_asignacionCaso',
									hidden: true,
									hideable: false
	                            },
								{   
									id: 'empleado_asignacionCaso',
									dataIndex: 'empleado_asignacionCaso',
									hidden: true,
									hideable: false
	                            },
								{
									id: 'nombre_asignacionCaso',
									dataIndex: 'nombre_asignacionCaso',
									header: 'Asignado Caso',
									width: 220
								},
								{
									 id: 'fecha_asignacionCaso',
									 header: 'Fecha Asignacion',
									 dataIndex: 'fecha_asignacionCaso',
									 width: 110
								},
								{   
									id: 'observacion_asignacionCaso',
									dataIndex: 'observacion_asignacionCaso',
									header: 'Observacion',
									hideable: false,
									width: 280,
									renderer: function (value, metadata, record, rowIndex, colIndex, store){
										var dataOrigen = record.data.origen;
										/*if(dataOrigen == 'Nuevo')
										{	
											text_observacion.setDisabled(false);
										}*/
										
										return record.data.observacion_asignacionCaso;
									},
									editor: text_observacion
								},
								{
									id: 'personaEmpresaRol_asignacionCaso',
									dataIndex: 'personaEmpresaRol_asignacionCaso',
									hidden: true,
									hideable: false
								},
								{
									id: 'origen',
									dataIndex: 'origen',
									hidden: true,
									hideable: false
								},
								{
	                                 id: 'criterios_hipotesis',
	                                 header: 'criterios_hipotesis',
	                                 dataIndex: 'criterios_hipotesis',
	                                 hidden: true,
	                                 hideable: false
	                             },{
	                                 id: 'afectados_hipotesis',
	                                 header: 'afectados_hipotesis',
	                                 dataIndex: 'afectados_hipotesis',
	                                 hidden: true,
	                                 hideable: false
	                             },
	                             {
	                                header: 'Acciones',
	                                xtype: 'actioncolumn',
	                                width:115,
	                                sortable: false,
	                                items: [
										{
											getClass: function(v, meta, rec) {
												var cssName = "icon-invisible";
												if(rec.get('origen') == 'Nuevo')
												{
													cssName = "button-grid-asignarCaso";
												}
												
												if (cssName == "icon-invisible") 
													this.items[0].tooltip = '';
												else 
													this.items[0].tooltip = 'Asignar Caso';
													
												return cssName
											},
		                                    tooltip: 'Asignar Caso',
		                                    handler: function(grid, rowIndex, colIndex) {
												var rec =  gridHipotesis.getStore().getAt(rowIndex);													
												asignarCaso(rec, recCaso);
		                                    }
										}
									]
		                         }
							 ],
	                         dockedItems: [{
	                                 xtype: 'toolbar',
	                                 items: [{
	                                         itemId: 'removeButton',
	                                         text:'Eliminar',
	                                         tooltip:'Elimina el item seleccionado',
	                                         disabled: true,
	                                         handler : function(){eliminarSeleccion(gridHipotesis, 'gridHipotesis', selModelHipotesis);}
	                                     }, '-', {
	                                         itemId: 'addButton',
	                                         text:'Agregar',
	                                         tooltip:'Agrega un item a la lista',
	                                         disabled: true,
	                                         handler : function(){									
												if(	((flagCreador && !flagBoolAsignado) || (flagBoolAsignado && flagAsignado)) )
												{								
													if((flagTareasAbiertas && flagTareasSolucionadas) )
													{
														//alert("Permisos crear Hipotesis");
														/*
														if(selModelSintomas.getSelection().length > 0)
														{
															 if(selModelSintomas.getSelection().length > 1){
																 Ext.Msg.alert('Alerta','Solo debe seleccionar un sintoma a la vez.' );
																 return ;
															 }
															 for(var i=0 ;  i < selModelSintomas.getSelection().length ; ++i)
															 {
															   sintoma_id = selModelSintomas.getSelection()[i].data.id_sintoma;
															   sintoma_nombre = selModelSintomas.getSelection()[i].data.nombre_sintoma;
															 }      
														}else{
															 Ext.Msg.alert('Alerta','Debe escoger un sintoma para ingresar su hipotesis.');
															 return ;
														}*/
														 
														//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN HIPOTESIS ANTERIOR... ANTES DE CREAR OTRO..
														var storeValida = Ext.getCmp("gridHipotesis").getStore();
														var boolSigue = false;
														var boolSigue2 = false;
														
														var esOrigen = 0;
														if(storeValida.getCount() > 0)
														{
															var boolSigue_vacio = true;
															var boolSigue_igual = true;
															
															for(var i = 0; i < storeValida.getCount(); i++)
															{
																var origen = storeValida.getAt(i).data.origen;
																if(origen == "Nuevo")
																{
																	esOrigen++;
																}
																
																var id_sintoma = storeValida.getAt(i).data.id_sintomaHipotesis;
																var nombre_sintoma = storeValida.getAt(i).data.nombre_sintomaHipotesis;
																		
																var id_hipotesis = storeValida.getAt(i).data.id_hipotesis;
																var nombre_hipotesis = storeValida.getAt(i).data.nombre_hipotesis;
																		
																var empleado = storeValida.getAt(i).data.empleado_asignacionCaso;
																var departamento = storeValida.getAt(i).data.departamento_asignacionCaso;
																var observacion = storeValida.getAt(i).data.observacion_asignacionCaso;
																var fecha = storeValida.getAt(i).data.fecha_asignacionCaso;
																	
																if(id_hipotesis != "" && nombre_hipotesis != ""){ /*NADA*/  }
																else {  boolSigue_vacio = false; }
										
																if(i>0)
																{
																	for(var j = 0; j < i; j++)
																	{
																		var id_sintoma_valida = storeValida.getAt(j).data.id_sintomaHipotesis;
																		var nombre_sintoma_valida = storeValida.getAt(j).data.nombre_sintomaHipotesis;
																		
																		var id_hipotesis_valida = storeValida.getAt(j).data.id_hipotesis;
																		var nombre_hipotesis_valida = storeValida.getAt(j).data.nombre_hipotesis;
																		
																		var empleado_valida = storeValida.getAt(j).data.empleado_asignacionCaso;
																		var departamento_valida = storeValida.getAt(j).data.departamento_asignacionCaso;
																		var observacion_valida = storeValida.getAt(j).data.observacion_asignacionCaso;
																		var fecha_valida = storeValida.getAt(j).data.fecha_asignacionCaso;
																		
																		if(id_sintoma_valida == id_sintoma && id_hipotesis == id_hipotesis_valida &&
																		   empleado_valida == empleado && departamento == departamento_valida && 
																		   observacion_valida == observacion && fecha == fecha_valida)
																		{
																			boolSigue_igual = false;	
																		}
																	}
																}
															} 
															
															if(boolSigue_vacio) { boolSigue = true; }	
															if(boolSigue_igual) { boolSigue2 = true; }						
														}
														else
														{
															boolSigue = true;
															boolSigue2 = true;
														}
														
														if(boolSigue && boolSigue2 && esOrigen == 0)
														{
															var globalComboEmpresa = $('#globalEmpresaEscogida').val();													
															var arrayValores = globalComboEmpresa.split('@@');
															var valorIdDepartamento = '';
															var ValorIdPersonaEmpresaRol = '';
															if(arrayValores && arrayValores.length > 3)
															{
																valorIdDepartamento = arrayValores[4];
																valorDepartamento = arrayValores[5];
																ValorIdPersonaEmpresaRol = arrayValores[6];
															}
															var global_nombre_empleado = $('#global_nombre_empleado').val() ?  $('#global_nombre_empleado').val() : '';
															var global_id_empleado = $('#global_id_empleado').val() ? $('#global_id_empleado').val() : '';
															
															// Create a model instance
															var r = Ext.create('Hipotesis', {
																id_sintomaHipotesis: '1',/*sintoma_id,*/
																nombre_sintomaHipotesis: '',/*sintoma_nombre,*/
																id_hipotesis: '',
																nombre_hipotesis: '',
																criterios_hipotesis: '',
																afectados_hipotesis: '',
																departamento_asignacionCaso: valorIdDepartamento,
																empleado_asignacionCaso: global_id_empleado,
																nombre_asignacionCaso: global_nombre_empleado,
																personaEmpresaRol_asignacionCaso: ValorIdPersonaEmpresaRol,
																observacion_asignacionCaso: '',
																fecha_asignacionCaso: '',
																asunto_asignacionCaso: 'Autoasignacion del Caso',
																origen: 'Nuevo'
															 });
															 
															storeHipotesis.insert(0, r);
															gridHipotesis.getView().refresh();
														}
														else if(!boolSigue)
														{
															Ext.Msg.alert('Alerta ', "Debe completar datos de las hipotesis a ingresar, antes de solicitar una nueva hipotesis");
														}
														else if(!boolSigue2)
														{
															Ext.Msg.alert('Alerta ', "No puede ingresar el mismo sintoma y hipotesis! Debe modificar el registro repetido, antes de solicitar una nueva hipotesis");
														}
														else if(esOrigen > 0)
														{
															Ext.Msg.alert('Alerta ', "No puede crear mas de una hipotesis");
														}
														else
														{
															Ext.Msg.alert('Alerta ', "Debe completar datos de las hipotesis a ingresar, antes de solicitar una nueva hipotesis");
														}		
													}
													else
													{
														Ext.Msg.alert('Alerta ', "No puede crear Hipotesis, porque tiene tareas abiertas.");
													}
												}
												else
												{
													Ext.Msg.alert('Alerta ', "No tiene permisos para crear Hipotesis, porque el caso fue asignado a otra persona");
												}																									
	                                         }
	                                     }]
	                                 }],
	                             selModel: selModelHipotesis,
	                             width: 970,
	                             height: 200,
	                             frame: true,
	                             plugins: [cellEditingHipotesis],
	                             title: 'Ingresar Informacion de Hipotesis'
							});
							
															
							if(	((flagCreador && !flagBoolAsignado) || (flagBoolAsignado && flagAsignado)) && (flagTareasAbiertas && flagTareasSolucionadas) )
							{
								//alert("Permisos crear Hipotesis");
								gridHipotesis.down('#addButton').setDisabled(false);
							}
							else
							{
								//alert("No tiene Permisos crear Hipotesis");
								gridHipotesis.down('#addButton').setDisabled(true);
							}
							
							formPanel = Ext.create('Ext.form.Panel', {
								bodyPadding: 5,
								waitMsgTarget: true,
								height: 200,
								layout: 'fit',
								fieldDefaults: {
									labelAlign: 'left',
									labelWidth: 200,
									msgTarget: 'side'
								},

								items: [{
									xtype: 'fieldset',
									title: 'Información del Caso',
									defaultType: 'textfield',
									items: [
										{
											 xtype: 'displayfield',
											 fieldLabel: 'Caso:',
											 id: 'hip_numero_casoSintoma',
											 name: 'hip_numero_casoSintoma',
											 value: numero
										},
										{
											 xtype: 'displayfield',
											 fieldLabel: 'Fecha apertura:',
											 id: 'hip_fechacaso',
											 name: 'hip_fechaCaso',
											 value: fecha+" "+hora
										},
										{
											 xtype: 'textarea',
											 fieldLabel: 'Version Inicial:',
											 id: 'hip_version_inicialSintoma',
											 name: 'hip_version_inicialSintoma',
											 rows: 3,
											 cols: 100,
											 readOnly: true,
											 value: version_inicial
										},
										gridSintomas,
										gridHipotesis
									]
								}]
							});
							winHipotesis = Ext.create('Ext.window.Window', {
								title: 'Agregar Hipotesis',
								modal: true,
								width: 1030,
								height: 620,
								resizable: false,
								layout: 'fit',
								closabled: false,
								items: [formPanel],
								buttonAlign: 'center',
								buttons:[btnguardar,btncancelar]
	                        }).show(); 
							
	                    }else{
							//*****************************************************************************************************************************************
																					//HIPOTESIS SIN SINTOMAS 
							//*****************************************************************************************************************************************
							selModelHipotesis = Ext.create('Ext.selection.CheckboxModel', {
								checkOnly: true,
	                            listeners: {
	                                 selectionchange: function(sm, selections) {
										gridHipotesis.down('#removeButton').setDisabled(selections.length == 0);
	                                 }
	                             }
							});
							
	                         comboHipotesisStore = new Ext.data.Store({ 
	                             total: 'total',
	                             proxy: {
	                                 type: 'ajax',
	                                 url : '../administracion/soporte/admi_hipotesis/grid',
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
							comboHipotesis = Ext.create('Ext.form.ComboBox', {
	                             id:'comboSintomaHipotesis',
	                             store: comboHipotesisStore,
	                             displayField: 'nombre_hipotesis',
	                             valueField: 'id_hipotesis',
	                             height:30,
	                             border:0,
	                             margin:0,
								fieldLabel: false,	
								queryMode: "remote",
								emptyText: ''
							});
							
							Ext.define('Hipotesis', {
	                             extend: 'Ext.data.Model',
	                             fields: [
									{name:'id_sintomaHipotesis', type:'string'},
									{name:'nombre_sintomaHipotesis', type:'string'},
									{name:'id_hipotesis', type:'string'},
									{name:'nombre_hipotesis', type:'string'},
									{name:'criterios_hipotesis', type:'string'},
									{name:'afectados_hipotesis', type:'string'},
									{name:'origen', type:'string'},
									{name:'departamento_asignacionCaso', type:'string'},
									{name:'empleado_asignacionCaso', type:'string'},
									{name:'nombre_asignacionCaso', type:'string'},
									{name:'personaEmpresaRol_asignacionCaso', type:'string'},
									{name:'observacion_asignacionCaso', type:'string'},
									{name:'fecha_asignacionCaso', type:'string'},
									{name:'asunto_asignacionCaso', type:'string'}
	                             ]
							});
							
							storeHipotesis = new Ext.data.Store({ 
	                             pageSize: 1000,
	                             total: 'total',
	                             autoLoad:true,
	                             proxy: {
	                                 type: 'ajax',
	                                 url : '../soporte/info_caso/' + 'getHipotesisXCaso',
	                                 reader: {
	                                     type: 'json',
	                                     totalProperty: 'total',
	                                     root: 'encontrados'
	                                 },
	                                 extraParams: {
										 band: 'hipotesis',
	                                     id: id_caso,
	                                     nombre: '',
	                                     estado: 'Activos'
	                                 }
	                             },
								fields:
								[
									{name:'id_sintomaHipotesis', mapping:'id_sintomaHipotesis'},
									{name:'nombre_sintomaHipotesis', mapping:'nombre_sintomaHipotesis'},
									{name:'id_hipotesis', mapping:'id_hipotesis'},
									{name:'nombre_hipotesis', mapping:'nombre_hipotesis'},
									{name:'criterios_hipotesis', mapping:'criterios_hipotesis'},
									{name:'afectados_hipotesis', mapping:'afectados_hipotesis'},
									{name:'asunto_asignacionCaso', mapping:'asunto_asignacionCaso'},
									{name:'departamento_asignacionCaso', mapping:'departamento_asignacionCaso'},
									{name:'empleado_asignacionCaso', mapping:'empleado_asignacionCaso'},
									{name:'observacion_asignacionCaso', mapping:'observacion_asignacionCaso'},
									{name:'nombre_asignacionCaso', mapping:'nombre_asignacionCaso'},
									{name:'personaEmpresaRol_asignacionCaso', mapping:'personaEmpresaRol_asignacionCaso'},
									{name:'fecha_asignacionCaso', mapping:'fecha_asignacionCaso'},
									{name:'origen', mapping:'origen'}
								]
	                         });
							 
							cellEditingHipotesis = Ext.create('Ext.grid.plugin.CellEditing', {
								clicksToEdit: 1,
								listeners: {
									beforeedit: function(editor, e){
										var me = this;
										var allowed = !!me.isEditAllowed;
										
										var gridConsulta_ahorita = e.grid;
										var rowIndex_ahorita = e.rowIdx;
										var valorOrigen = gridConsulta_ahorita.store.getAt(rowIndex_ahorita).data.origen;
										
										if(valorOrigen == 'Nuevo')
										{	
											if (!me.isEditAllowed)
											{
												me.isEditAllowed = true;
												me.startEditByPosition({row: e.rowIdx, column: e.colIdx});
											}
										}	
										else
										{
											me.isEditAllowed = false;
										}
										
										return allowed;
									},
									edit: function(){
										this.isEditAllowed = false;
										
										gridHipotesis.getView().refresh();
									}
								}
							});
							
							 combo_hipotesis= new Ext.form.ComboBox({
								id:'searchHipotesis_cmp',
								name: 'searchHipotesis_cmp',
								displayField:'nombre_hipotesis',
								valueField: 'id_hipotesis',
								store: comboHipotesisStore,
								loadingText: 'Buscando ...',
								fieldLabel: false,	
								disabled: true,
								queryMode: "remote",
								emptyText: '',
								listClass: 'x-combo-list-small'
							});
							
							text_observacion = new Ext.form.TextField({
								id:'txt_observacion',
								name: 'txt_observacion'
							});
	
	                        gridHipotesis = Ext.create('Ext.grid.Panel', {
	                            id:'gridHipotesis',
	                            store: storeHipotesis,
								viewConfig: { enableTextSelection: true, stripeRows:true }, 
	                            columnLines: true,
	                            columns: 
								[
									{
										 id: 'id_sintomaHipotesis',
										 header: 'SintomaId',
										 dataIndex: 'id_sintomaHipotesis',
										 hidden: true,
										 hideable: false
									}, 
									{
										 id: 'nombre_sintomaHipotesis',
										 header: 'Sintoma',
										 dataIndex: 'nombre_sintomaHipotesis',
										 width: 220,
										hidden: true,
										hideable: false
									},
									{   
										 id: 'id_hipotesis',
										 header: 'HipotesisId',
										 dataIndex: 'id_hipotesis',
										 hidden: true,
										 hideable: false
									}, 
									{
										 id: 'nombre_hipotesis',
										 header: 'Hipotesis',
										 dataIndex: 'nombre_hipotesis',
										 width: 220,
										 sortable: true,
										 renderer: function (value, metadata, record, rowIndex, colIndex, store){
											var dataOrigen = record.data.origen;
											if(dataOrigen == 'Nuevo')
											{	
												combo_hipotesis.setDisabled(false);
											}
											
											 record.data.id_hipotesis = record.data.nombre_hipotesis;
											 for (var i = 0;i< comboHipotesisStore.data.items.length;i++)
											 {
												 if (comboHipotesisStore.data.items[i].data.id_hipotesis== record.data.id_hipotesis)
												 {
													 gridHipotesis.getStore().getAt(rowIndex).data.id_sintoma=record.data.id_hipotesis;

													 record.data.id_hipotesis = comboHipotesisStore.data.items[i].data.id_hipotesis;
													 record.data.nombre_hipotesis = comboHipotesisStore.data.items[i].data.nombre_hipotesis;
													 break;
												 }
											 }
											 return record.data.nombre_hipotesis;
										 },
										 editor: combo_hipotesis 
									},
									{   
										id: 'asunto_asignacionCaso',
										dataIndex: 'asunto_asignacionCaso',
										hidden: true,
										hideable: false
									},
									{   
										id: 'departamento_asignacionCaso',
										dataIndex: 'departamento_asignacionCaso',
										hidden: true,
										hideable: false
									},
									{   
										id: 'empleado_asignacionCaso',
										dataIndex: 'empleado_asignacionCaso',
										hidden: true,
										hideable: false
									},
									{
										id: 'nombre_asignacionCaso',
										dataIndex: 'nombre_asignacionCaso',
										header: 'Asignado Caso',
										width: 220
									},
									{
										 id: 'fecha_asignacionCaso',
										 header: 'Fecha Asignacion',
										 dataIndex: 'fecha_asignacionCaso',
										 width: 110
									},
									{   
										id: 'observacion_asignacionCaso',
										dataIndex: 'observacion_asignacionCaso',
										header: 'Observacion',
										hideable: false,
										width: 280,
										renderer: function (value, metadata, record, rowIndex, colIndex, store){
											var dataOrigen = record.data.origen;
											/*if(dataOrigen == 'Nuevo')
											{	
												text_observacion.setDisabled(false);
											}*/
											
											return record.data.observacion_asignacionCaso;
										},
										editor: text_observacion
									},
									{
										id: 'personaEmpresaRol_asignacionCaso',
										dataIndex: 'personaEmpresaRol_asignacionCaso',
										hidden: true,
										hideable: false
									},
									{
										id: 'origen',
										dataIndex: 'origen',
										hidden: true,
										hideable: false
									},{
										 id: 'criterios_hipotesis',
										 header: 'criterios_hipotesis',
										 dataIndex: 'criterios_hipotesis',
										 hidden: true,
										 hideable: false
									 },{
										 id: 'afectados_hipotesis',
										 header: 'afectados_hipotesis',
										 dataIndex: 'afectados_hipotesis',
										 hidden: true,
										 hideable: false
									 },
									{
	                                header: 'Acciones',
	                                xtype: 'actioncolumn',
	                                width:115,
	                                sortable: false,
	                                items: [
										{
		                                    //icon: 'shared/icons/fam/delete.gif',
		                                    iconCls: 'button-grid-afectados',
		                                    tooltip: 'Agregar Afectados',
		                                    handler: function(grid, rowIndex, colIndex) {
		                                        agregarAfectadosXHipotesis(grid.getStore().getAt(rowIndex).data.nombre_hipotesis, id_caso); 
		                                    }
										},												
										{
											getClass: function(v, meta, rec) {
												var cssName = "icon-invisible";
												if(rec.get('origen') == 'Nuevo')
												{
													cssName = "button-grid-asignarCaso";
												}
												
												if (cssName == "icon-invisible") 
													this.items[0].tooltip = '';
												else 
													this.items[0].tooltip = 'Asignar Caso';
													
												return cssName;
											},
		                                    tooltip: 'Asignar Caso',
		                                    handler: function(grid, rowIndex, colIndex) {
												var rec =  gridHipotesis.getStore().getAt(rowIndex);													
												asignarCaso(rec, recCaso);
		                                    }
										}
									]
								}
								],
								dockedItems: 
								[
									{
										xtype: 'toolbar',
										items: 
										[
											{
												 itemId: 'removeButton',
												 text:'Eliminar',
												 tooltip:'Elimina el item seleccionado',
												 disabled: true,
												 handler : function(){eliminarSeleccion(gridHipotesis, 'gridHipotesis', selModelHipotesis);}
											}, '-', 
											{	
												itemId: 'addButton',						
												text:'Agregar',
												tooltip:'Agrega un item a la lista',
												handler : function(){							
													if(	((flagCreador && !flagBoolAsignado) || (flagBoolAsignado && flagAsignado)) )
													{								
														if((flagTareasAbiertas && flagTareasSolucionadas) )
														{
															//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN HIPOTESIS ANTERIOR... ANTES DE CREAR OTRO..
															var storeValida = Ext.getCmp("gridHipotesis").getStore();
															var boolSigue = false;
															var boolSigue2 = false;
															
															var esOrigen = 0;
															if(storeValida.getCount() > 0)
															{
																var boolSigue_vacio = true;
																var boolSigue_igual = true;
																
																for(var i = 0; i < storeValida.getCount(); i++)
																{
																	var origen = storeValida.getAt(i).data.origen;
																	if(origen == "Nuevo")
																	{
																		esOrigen++;
																	}
																	
																	var id_sintoma = storeValida.getAt(i).data.id_sintomaHipotesis;
																	var nombre_sintoma = storeValida.getAt(i).data.nombre_sintomaHipotesis;
																			
																	var id_hipotesis = storeValida.getAt(i).data.id_hipotesis;
																	var nombre_hipotesis = storeValida.getAt(i).data.nombre_hipotesis;
																				
																	var empleado = storeValida.getAt(i).data.empleado_asignacionCaso;
																	var departamento = storeValida.getAt(i).data.departamento_asignacionCaso;
																	var observacion = storeValida.getAt(i).data.observacion_asignacionCaso;
																	var fecha = storeValida.getAt(i).data.fecha_asignacionCaso;
																	
																	if(id_hipotesis != "" && nombre_hipotesis != ""){ /*NADA*/  }
																	else {  boolSigue_vacio = false; }
											
																	if(i>0)
																	{
																		for(var j = 0; j < i; j++)
																		{
																			var id_sintoma_valida = storeValida.getAt(j).data.id_sintomaHipotesis;
																			var nombre_sintoma_valida = storeValida.getAt(j).data.nombre_sintomaHipotesis;
																			
																			var id_hipotesis_valida = storeValida.getAt(j).data.id_hipotesis;
																			var nombre_hipotesis_valida = storeValida.getAt(j).data.nombre_hipotesis;
																		
																			var empleado_valida = storeValida.getAt(j).data.empleado_asignacionCaso;
																			var departamento_valida = storeValida.getAt(j).data.departamento_asignacionCaso;
																			var observacion_valida = storeValida.getAt(j).data.observacion_asignacionCaso;
																			var fecha_valida = storeValida.getAt(j).data.fecha_asignacionCaso;
																			
																			if(id_sintoma_valida == id_sintoma && id_hipotesis == id_hipotesis_valida &&
																			   empleado_valida == empleado && departamento == departamento_valida && 
																			   observacion_valida == observacion && fecha == fecha_valida)
																			{
																				boolSigue_igual = false;	
																			}
																		}
																	}
																} 
																
																if(boolSigue_vacio) { boolSigue = true; }	
																if(boolSigue_igual) { boolSigue2 = true; }						
															}
															else
															{
																boolSigue = true;
																boolSigue2 = true;
															}
															
															if(boolSigue && boolSigue2 && esOrigen == 0)
															{
																var globalComboEmpresa = $('#globalEmpresaEscogida').val();													
																var arrayValores = globalComboEmpresa.split('@@');
																var valorIdDepartamento = '';
																var ValorIdPersonaEmpresaRol = '';
																if(arrayValores && arrayValores.length > 3)
																{
																	valorIdDepartamento = arrayValores[4];
																	valorDepartamento = arrayValores[5];
																	ValorIdPersonaEmpresaRol = arrayValores[6];
																}
																var global_nombre_empleado = $('#global_nombre_empleado').val() ?  $('#global_nombre_empleado').val() : '';
																var global_id_empleado = $('#global_id_empleado').val() ? $('#global_id_empleado').val() : '';
																
																// Create a model instance
																var r = Ext.create('Hipotesis', {
																	id_sintomaHipotesis: 0,
																	nombre_sintomaHipotesis: 'Ninguno',
																	id_hipotesis: '',
																	nombre_hipotesis: '',
																	criterios_hipotesis: '',
																	afectados_hipotesis: '',
																	departamento_asignacionCaso: valorIdDepartamento,
																	empleado_asignacionCaso: global_id_empleado,
																	nombre_asignacionCaso: global_nombre_empleado,
																	personaEmpresaRol_asignacionCaso: ValorIdPersonaEmpresaRol,
																	observacion_asignacionCaso: '',
																	fecha_asignacionCaso: '',
																	asunto_asignacionCaso: 'Autoasignacion del Caso',
																	origen: 'Nuevo'
																 });
																 
																storeHipotesis.insert(0, r);
																gridHipotesis.getView().refresh();
															}
															else if(!boolSigue)
															{
																Ext.Msg.alert('Alerta ', "Debe completar datos de las hipotesis a ingresar, antes de solicitar una nueva hipotesis");
															}
															else if(!boolSigue2)
															{
																Ext.Msg.alert('Alerta ', "No puede ingresar el mismo sintoma y hipotesis! Debe modificar el registro repetido, antes de solicitar una nueva hipotesis");
															}
															else if(esOrigen > 0)
															{
																Ext.Msg.alert('Alerta ', "No puede crear mas de una hipotesis");
															}
															else
															{
																Ext.Msg.alert('Alerta ', "Debe completar datos de las hipotesis a ingresar, antes de solicitar una nueva hipotesis");
															}	
														}
														else
														{
															Ext.Msg.alert('Alerta ', "No puede crear Hipotesis, porque tiene tareas abiertas.");
														}
													}
													else
													{
														Ext.Msg.alert('Alerta ', "No tiene permisos para crear Hipotesis, porque el caso fue asignado a otra persona");
													}
												}
											}
										]
									}
								],
								selModel: selModelHipotesis,
								width: 970,
								height: 200,
								frame: true,
								plugins: [cellEditingHipotesis],
								title: 'Ingresar Informacion de Hipotesis'
							});
							
							formPanel = Ext.create('Ext.form.Panel', {
								bodyPadding: 5,
								waitMsgTarget: true,
								height: 200,
								layout: 'fit',
								fieldDefaults: {
									labelAlign: 'left',
									labelWidth: 200,
									msgTarget: 'side'
								},

								items: [{
									xtype: 'fieldset',
									title: 'Información del Caso',
									defaultType: 'textfield',
									items: [
										{
											xtype: 'displayfield',
											fieldLabel: 'Caso:',
											id: 'hip_numero_casoSintoma',
											name: 'hip_numero_casoSintoma',
											value: numero
										},
										{
											xtype: 'displayfield',
											fieldLabel: 'Fecha apertura:',
											id: 'hip_fechacaso',
											name: 'hip_fechaCaso',
											value: fecha+" "+hora
										},
										{
											xtype: 'textarea',
											fieldLabel: 'Version Inicial:',
											id: 'hip_version_inicialSintoma',
											name: 'hip_version_inicialSintoma',
											rows: 3,
											cols: 100,
											readOnly: true,
											value: version_inicial
										},
										gridHipotesis
									]
								}]
							});
	                        winHipotesis = Ext.create('Ext.window.Window', {
								title: 'Agregar Hipotesis',
								modal: true,
								width: 1030,
								height: 460,
								resizable: false,
								layout: 'fit',
								closabled: false,
								items: [formPanel],
								buttonAlign: 'center',
								buttons:[btnguardar,btncancelar]
	                        }).show(); 
	                    }						
						Ext.MessageBox.hide();
	                }, this);
	            }
	        }
	    });
    }
}

function agregarAfectadosXHipotesis(hipotesis, id_caso){
    
    if(hipotesis=='')
    {
        Ext.Msg.alert('Alerta','Debe escoger una hipotesis primero.');
        return;
    }   
    
    
    string_html  = "<table width='100%' border='0' class='box-section-content' >";
    string_html += "    <tr>";
    string_html += "        <td width='80%' colspan='6'><b>Buscar Afectados:</b></td>";
    string_html += "    </tr>";
    string_html += "    <tr><td colspan='6'>&nbsp;</td></tr>";
    string_html += "    <tr>";
    string_html += "        <td colspan='6'>";
    string_html += "            <table width='100%' border='0'>";
    string_html += "                <tr>";
    string_html += "                    <td id='elementos'>";
    string_html += "                        <table width='100%' height='95px' border='0' class='box-section-subcontent'  style='vertical-align:top'>";
    string_html += "                            <tr>";
    string_html += "                                <td colspan='2' class='titulo-secundario'><b>Capa 2</b></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'> Tipo Elemento:</td>";
    string_html += "                                <td width='60%'><div id='searchTipoElemento'></div></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'> Elemento:</td>";
    string_html += "                                <td width='60%'><div id='searchElemento'></div></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'> Opcion:</td>";
    string_html += "                                <td width='60%'><div id='searchOpciones'></div></td>";
    string_html += "                            </tr>";
    string_html += "                        </table>";
    string_html += "                    </td>";
    string_html += "                    <td id='vacio1'>&nbsp;</td>";
    string_html += "                    <td id='segmentos'>";
    string_html += "                        <table width='100%' height='101px' border='0' class='box-section-subcontent'  style='vertical-align:top'>";
    string_html += "                            <tr>";
    string_html += "                                <td colspan='2' height='10px' class='titulo-secundario'><b>Capa 3</b></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'> Segmento:</td>";
    string_html += "                                <td width='60%'><div id='searchSegmento'></div></td>";
    string_html += "                            </tr>";   
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Opcion:</td>";
    string_html += "                                <td width='60%'><div id='searchOpcionesSegmento'></div></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td></td>";
    string_html += "                                <td colspan='2' width='60%'></td>";
    string_html += "                            </tr>";
    string_html += "                        </table>";
    string_html += "                    </td>";
    string_html += "                    <td id='vacio2'>&nbsp;</td>";
    string_html += "                    <td id='productos'>";
    string_html += "                        <table width='100%' height='101px' border='0' class='box-section-subcontent'  style='vertical-align:top'>";
    string_html += "                            <tr>";
    string_html += "                                <td colspan='2' height='10px' class='titulo-secundario'><b>Capa 7</b></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'> Producto:</td>";
    string_html += "                                <td width='60%'><div id='searchProducto'></div></td>";
    string_html += "                            </tr>";   
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Opcion:</td>";
    string_html += "                                <td width='60%'><div id='searchOpcionesProducto'></div></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td></td>";
    string_html += "                                <td colspan='2' width='60%'></td>";
    string_html += "                            </tr>";
    string_html += "                        </table>";
    string_html += "                    </td>";
    string_html += "                    <td id='vacio3'>&nbsp;</td>";
    string_html += "                    <td id='clientes'>";
    string_html += "                        <table width='100%' height='101px' border='0' class='box-section-subcontent'  style='vertical-align:top'>";
    string_html += "                            <tr>";
    string_html += "                                <td colspan='2' height='10px' class='titulo-secundario'><b>Otros</b></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'> Cliente:</td>";
    string_html += "                                <td width='60%'><div id='searchLogin'></div></td>";
    string_html += "                            </tr>";   
    string_html += "                            <tr>";
    string_html += "                                <td width='40%'>Opcion:</td>";
    string_html += "                                <td width='60%'><div id='searchOpcionesCliente'></div></td>";
    string_html += "                            </tr>";
    string_html += "                            <tr>";
    string_html += "                                <td></td>";
    string_html += "                                <td colspan='2' width='60%'></td>";
    string_html += "                            </tr>";
    string_html += "                        </table>";
    string_html += "                    </td>";
    string_html += "                    <td id='vacio4'>&nbsp;</td>";
    string_html += "                </tr>";
    string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>";
    string_html += "                <tr style='height:200px'>";
    string_html += "                    <td colspan='8'><div id='encontrados'></div></td>";
    string_html += "                </tr>";
    string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>";
    string_html += "                <tr>";
    string_html += "                    <td colspan='8' align='center'><input name='btn3' type='button' value='Agregar' class='btn-form' onclick='ingresarCriterio()'/></td>";
    string_html += "                </tr>"; 
    string_html += "                <tr><td colspan='8'>&nbsp;</td></tr>"; 
    string_html += "                <tr style='height:200px'>";
    string_html += "                    <td colspan='4'><div id='criterios'></div><input type='hidden' id='caso_criterios' name='caso_criterios' value='' /></td>";
    string_html += "                    <td colspan='4'><div id='afectados'></div><input type='hidden' id='caso_afectados' name='caso_afectados' value='' /></td>";
    string_html += "                </tr>";
    string_html += "            </table>";
    string_html += "        </td>";
    string_html += "    </tr>";
    string_html += "</table>";

    btnguardar2 = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                obtenerCriteriosHipotesis(hipotesis); 
                obtenerAfectadosHipotesis(hipotesis);
                win2.destroy();
            }
    });
    
    btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                win2.destroy();
            }
    });
    
    win2 = Ext.create('Ext.window.Window', {
            title: 'Agregar Afectados',
            modal: true,
            width: 940,
            height: 680,
            resizable: false,
            layout: 'fit',
            items: [
                {
                    xtype: 'panel',
                    width: 400,
                    html: '<div style="padding:6px;">'+string_html+'</div>'
                }
            ],
            buttonAlign: 'center',
            buttons:[btnguardar2,btncancelar2]
    }).show(); 
    
    Ext.define('Criterio', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_criterio_afectado', mapping:'id_criterio_afectado'},
            {name:'caso_id', mapping:'caso_id'},
            {name:'criterio', mapping:'criterio'},
            {name:'opcion', mapping:'opcion'}
        ]
    });
    Ext.define('Afectado', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', mapping:'id'},
            {name:'id_afectado', mapping:'id_afectado'},
            {name:'id_criterio', mapping:'id_criterio'},
            {name:'id_afectado_descripcion', mapping:'id_afectado_descripcion'},
            {name:'nombre_afectado', mapping:'nombre_afectado'},
            {name:'descripcion_afectado', mapping:'descripcion_afectado'}
        ]
    });
    
    
    // Grid encontrados
    storeEncontrados = new Ext.data.Store({ 
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
	    timeout: 1200000,
            url : '../soporte/info_caso/' + 'getEncontrados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
                  [
                    {name:'id_parte_afectada', mapping:'id_parte_afectada'},
                    {name:'nombre_parte_afectada', mapping:'nombre_parte_afectada'},
                    {name:'id_descripcion_1', mapping:'id_descripcion_1'},
                    {name:'nombre_descripcion_1', mapping:'nombre_descripcion_1'},
                    {name:'id_descripcion_2', mapping:'id_descripcion_2'},
                    {name:'nombre_descripcion_2', mapping:'nombre_descripcion_2'}
                  ]
    });
    smEncontrados = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })
    gridEncontrados = Ext.create('Ext.grid.Panel', {
        width: 910,
        height: 200,
        store: storeEncontrados,
		viewConfig: { enableTextSelection: true }, 
        forceFit:true,
        autoRender:true,
        id:'gridEncontrados',
        loadMask: true,
        frame:true,
        resizable:false,
        enableColumnResize :false,
        iconCls: 'icon-grid',
        selModel: smEncontrados,
        columns:[
                {
                  id: 'id_parte_afectada',
                  header: 'IdItemMenu',
                  dataIndex: 'id_parte_afectada',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_parte_afectada',
                  header: 'Parte afectada',
                  dataIndex: 'nombre_parte_afectada',
                  width: 400,
                  sortable: true
                },
                {
                  id: 'id_descripcion_1',
                  header: 'IdItemMenu',
                  dataIndex: 'id_descripcion_1',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_descripcion_1',
                  header: 'Descripcion 1',
                  dataIndex: 'nombre_descripcion_1',
                  width: 250,
                  sortable: true
                },
                {
                  id: 'id_descripcion_2',
                  header: 'IdItemMenu',
                  dataIndex: 'id_descripcion_2',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombre_descripcion_2',
                  header: 'Descripcion 2',
                  dataIndex: 'nombre_descripcion_2',
                  width: 250,
                  sortable: true
                }
            ],   
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeEncontrados,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),          
        renderTo: 'encontrados'
    });
    
    ////////////////Grid  Criterios////////////////  
    storeCriterios = new Ext.data.JsonStore(
    {
        total: 'total',
        pageSize: 200,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : '../soporte/info_caso/' + 'getCriterios2',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
			extraParams: {
				id: id_caso,
				id_sintoma: '',
				id_hipotesis: hipotesis
			}
        },
        fields:
        [
          {name:'id_criterio_afectado', mapping:'id_criterio_afectado'},
          {name:'caso_id', mapping:'caso_id'},
          {name:'criterio', mapping:'criterio'},
          {name:'opcion', mapping:'opcion'}
        ]                
    });
    smCriterios = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
                   selectionchange: function(sm, selections) {
                        gridCriterios.down('#removeButton').setDisabled(selections.length == 0);
                   }
                }
    })
    gridCriterios = Ext.create('Ext.grid.Panel', {
        title:'Criterios de Seleccion',
        width: 450,
        height: 200,
        autoRender:true,
        enableColumnResize :false,
        id:'gridCriterios',
        store: storeCriterios,
		viewConfig: { enableTextSelection: true }, 
        loadMask: true,
        frame:true,
        forceFit:true,
        iconCls: 'icon-grid',
        selModel: smCriterios,
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarCriterio(gridCriterios,storeAfectados);}
            }]
        }],
        columns:[
                {
                  id: 'id_criterio_afectado',
                  header: 'id_criterio_afectado',
                  dataIndex: 'id_criterio_afectado',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'caso_id',
                  header: 'caso_id',
                  dataIndex: 'caso_id',
                  hidden: true,
                  sortable: true
                },
                {
                  id: 'criterio',
                  header: 'Criterio',
                  dataIndex: 'criterio',
                  width: 100,
                  hideable: false
                },
                {
                  id: 'opcion',
                  header: 'Opcion',
                  dataIndex: 'opcion',
                  width: 300,
                  sortable: true
                }
            ],      
        renderTo: 'criterios'
    });
    
    
    ////////////////Grid  Afectados////////////////  
    storeAfectados = new Ext.data.JsonStore(
    {
        pageSize: 4000,
        autoLoad: true,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../soporte/info_caso/' + 'getAfectados2',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
			extraParams: {
				id: id_caso,
				id_sintoma: '',
				id_hipotesis: hipotesis
			}
        },
        fields:
        [
            {name:'id', mapping:'id'},
            {name:'id_afectado', mapping:'id_afectado'},
            {name:'id_criterio', mapping:'id_criterio'},
            {name:'id_afectado_descripcion', mapping:'id_afectado_descripcion'},
            {name:'nombre_afectado', mapping:'nombre_afectado'},
            {name:'descripcion_afectado', mapping:'descripcion_afectado'}
        ]                
    });
    
    gridAfectados = Ext.create('Ext.grid.Panel', {
        title:'Equipos Afectados',
        width: 450,
        height: 200,
        sortableColumns:false,
        store: storeAfectados,
		viewConfig: { enableTextSelection: true }, 
        id:'gridAfectados',
        enableColumnResize :false,
        loadMask: true,
        frame:true,
        forceFit:true,
        iconCls: 'icon-grid',
        columns: [
                /* Ext.create('Ext.grid.RowNumberer'),*/
                 {
                  id: 'id',
                  header: 'id',
                  dataIndex: 'id',
                  hidden: true,
                  hideable: false
                },
                 {
                  id: 'id_afectado',
                  header: 'id_afectado',
                  dataIndex: 'id_afectado',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_criterio',
                  header: 'id_criterio',
                  dataIndex: 'id_criterio',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_afectado_descripcion',
                  header: 'id_afectado_descripcion',
                  dataIndex: 'id_afectado_descripcion',
                  hidden: true,
                  hideable: false 
                },
                {
                  id: 'nombre_afectado',
                  header: 'Parte Afectada',
                  dataIndex: 'nombre_afectado',
                  width:250
                },
                {
                  id: 'descripcion_afectado',
                  header: 'Descripcion',
                  dataIndex: 'descripcion_afectado',
                  width:150
                }
                
            ],     
        renderTo: 'afectados'
    });
    
    
    /* STORES CAPA 2 --- ELEMENTOS */
    storeOpciones = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'nombre'],
        data : [
                {"opcion":"Puertos", "nombre":"Puertos"},
                {"opcion":"Logines", "nombre":"Punto Cliente"},
                {"opcion":"Elementos", "nombre":"Elementos Relacionados"}
            ]
    });
    comboOpciones = Ext.create('Ext.form.ComboBox', {
        id:'comboOpciones',        
        store: storeOpciones,
        queryMode: 'local',
        valueField: 'opcion',
        displayField: 'nombre',
        listeners: {
            select: function(combo){
                presentarEncontrados(Ext.getCmp("comboElemento").getValue(),Ext.getCmp("comboElemento").getRawValue(),combo.getValue(),combo.getRawValue(), "Capa2");
            }
        },
        renderTo: 'searchOpciones'
    });
    Ext.getCmp('comboOpciones').setDisabled(true);
    
    
    // Buscador Switches
    storeTipoElementos = new Ext.data.Store({ 
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../soporte/info_caso/' + 'getTiposElementos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idTipoElemento', mapping:'idTipoElemento'},
                {name:'nombreTipoElemento', mapping:'nombreTipoElemento'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboTipoElemento = Ext.create('Ext.form.ComboBox', {
        id:'comboTipoElemento',
        store: storeTipoElementos,
        displayField: 'nombreTipoElemento',
		valueField: 'idTipoElemento',
        height:30,
        border:0,
        margin:0,
		fieldLabel: false,	
		queryMode: "remote",
		emptyText: '',
        listeners: {
            select: function(){
                limpiarCombos("todo", "Capa2");
                
                Ext.getCmp('comboElemento').setDisabled(false);
                Ext.getCmp('comboElemento').setRawValue('');
				Ext.getCmp('comboElemento').reset();
                
                storeElementos.proxy.extraParams = {
                    tipoElemento: Ext.getCmp("comboTipoElemento").getValue()
                };
                storeElementos.load();
                storeEncontrados.removeAll();
            }
        },
        renderTo: 'searchTipoElemento'
    });
    
    
    storeElementos = new Ext.data.Store({ 
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../soporte/info_caso/' + 'getElementos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idElemento', mapping:'idElemento'},
                {name:'nombreElemento', mapping:'nombreElemento'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboElemento = Ext.create('Ext.form.ComboBox', {
        id:'comboElemento',
        store: storeElementos,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        height:30,
        border:0,
        margin:0,
		fieldLabel: false,	
		queryMode: "remote",
		emptyText: '',
        listeners: {
            select: function(){
                Ext.getCmp('comboOpciones').setDisabled(false);
		Ext.getCmp('comboOpciones').reset();
                storeEncontrados.removeAll();
            }
        },
        renderTo: 'searchElemento'
    });
    
    
    /* STORES CAPA 3 --- SEGMENTOS */  
    storeSegmentos = new Ext.data.Store({ 
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../soporte/info_caso/' + 'getSegmentos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id_segmento', mapping:'id_segmento'},
                {name:'segmento', mapping:'segmento'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboSegmentos = Ext.create('Ext.form.ComboBox', {
        id:'comboSegmentos',
        store: storeSegmentos,
        displayField: 'segmento',
        valueField: 'id_segmento',
        height:30,
        border:0,
        margin:0,
		fieldLabel: false,	
		queryMode: "remote",
		emptyText: '',
        listeners: {
            select: function(){
                limpiarCombos("todo", "Capa3");
                Ext.getCmp('comboOpcionesSegmento').setDisabled(false);                
                storeEncontrados.removeAll();
            }
        },
        renderTo: 'searchSegmento'
    });
    
    storeOpcionesSegmento = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'nombre'],
        data : [
                {"opcion":"Logines", "nombre":"Punto Cliente"},
                {"opcion":"ELementos", "nombre":"Elementos"}
            ]
    });
    comboOpciones = Ext.create('Ext.form.ComboBox', {        
        id:'comboOpcionesSegmento',        
        store: storeOpcionesSegmento,
        queryMode: 'local',
        valueField: 'opcion',
        displayField: 'nombre',
        listeners: {
            select: function(combo){
                presentarEncontrados(Ext.getCmp("comboSegmentos").getValue(),Ext.getCmp("comboSegmentos").getRawValue(),combo.getValue(),combo.getRawValue(), "Capa3");
            }
        },
        renderTo: 'searchOpcionesSegmento'
    });
    Ext.getCmp('comboOpcionesSegmento').setDisabled(true);
    
    /* STORES CAPA 7 --- PRODUCTOS */  
    storeProductos = new Ext.data.Store({ 
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../soporte/info_caso/' + 'getProductos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id_producto', mapping:'id_producto'},
                {name:'producto', mapping:'producto'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboProductos = Ext.create('Ext.form.ComboBox', {
        id:'comboProductos',
        store: storeProductos,
        displayField: 'producto',
        valueField: 'id_producto',
        height:30,
        border:0,
        margin:0,
		fieldLabel: false,	
		queryMode: "remote",
		emptyText: '',
        listeners: {
            select: function(){
                limpiarCombos("todo", "Capa7");
                Ext.getCmp('comboOpcionesProducto').setDisabled(false);  
                storeEncontrados.removeAll();
            }
        },
        renderTo: 'searchProducto'
    });
    
    storeOpcionesProducto = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'nombre'],
        data : [
                {"opcion":"Logines", "nombre":"Punto Cliente"},
                {"opcion":"ELementos", "nombre":"Elementos"}
            ]
    });
    comboOpciones = Ext.create('Ext.form.ComboBox', {        
        id:'comboOpcionesProducto',        
        store: storeOpcionesProducto,
        queryMode: 'local',
        valueField: 'opcion',
        displayField: 'nombre',
        listeners: {
            select: function(combo){
                presentarEncontrados(Ext.getCmp("comboProductos").getValue(),Ext.getCmp("comboProductos").getRawValue(),combo.getValue(),combo.getRawValue(), "Capa7");
            }
        },
        renderTo: 'searchOpcionesProducto'
    });
    Ext.getCmp('comboOpcionesProducto').setDisabled(true);
    
    
    /* STORES OTROS --- CLIENTES */
    storeClientes = new Ext.data.Store({ 
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../soporte/info_caso/' + 'getClientes',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id_cliente', mapping:'id_cliente'},
                {name:'cliente', mapping:'cliente'}
              ]
    });

    // Create the combo box, attached to the states data store
    comboClientes = Ext.create('Ext.form.ComboBox', {
        id:'comboClientes',
        store: storeClientes,
        displayField: 'cliente',
        valueField: 'id_cliente',
        height:30,
        border:0,
        margin:0,
		fieldLabel: false,	
		queryMode: "remote",
		emptyText: '',
        listeners: {
            select: function(){
                limpiarCombos("todo", "Otros");
                Ext.getCmp('comboOpcionesCliente').setDisabled(false);                  
                storeEncontrados.removeAll();
            }
        },
        renderTo: 'searchLogin'
    });
	
    storeOpcionesCliente = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'nombre'],
        data : [
                {"opcion":"Logines", "nombre":"Punto Cliente"},
                {"opcion":"ELementos", "nombre":"Elementos"}
            ]
    });
    comboOpciones = Ext.create('Ext.form.ComboBox', {        
        id:'comboOpcionesCliente',        
        store: storeOpcionesCliente,
        queryMode: 'local',
        valueField: 'opcion',
        displayField: 'nombre',
        listeners: {
            select: function(combo){
                presentarEncontrados(Ext.getCmp("comboClientes").getValue(),Ext.getCmp("comboClientes").getRawValue(),combo.getValue(),combo.getRawValue(), "Otros");
            }
        },
        renderTo: 'searchOpcionesCliente'
    });
    Ext.getCmp('comboOpcionesCliente').setDisabled(true);
}

function obtenerCriteriosHipotesis(hipotesis)
{
    var array_criterios = new Object();
    array_criterios['total'] =  gridCriterios.getStore().getCount();
    array_criterios['criterios'] = new Array();
    var array_data = new Array();
    for(var i=0; i < gridCriterios.getStore().getCount(); i++)
    {

        array_data.push(gridCriterios.getStore().getAt(i).data);
    }

    array_criterios['criterios'] = array_data;

    for(var i=0; i < gridHipotesis.getStore().getCount(); i++)
    {
         if(gridHipotesis.getStore().getAt(i).data.nombre_hipotesis==hipotesis)
            gridHipotesis.getStore().getAt(i).data.criterios_hipotesis = Ext.JSON.encode(array_criterios);
    } 
}

function obtenerAfectadosHipotesis(hipotesis)
{
  var array_afectados = new Object();
    array_afectados['total'] =  gridAfectados.getStore().getCount();
    array_afectados['afectados'] = new Array();
    var array_data = new Array();
    for(var i=0; i < gridAfectados.getStore().getCount(); i++)
    {

        array_data.push(gridAfectados.getStore().getAt(i).data);
    }

    array_afectados['afectados'] = array_data;

    for(var i=0; i < gridHipotesis.getStore().getCount(); i++)
    {
         if(gridHipotesis.getStore().getAt(i).data.nombre_hipotesis==hipotesis)
            gridHipotesis.getStore().getAt(i).data.afectados_hipotesis = Ext.JSON.encode(array_afectados);
    } 
  
}

function obtenerHipotesis()
{
  var array = new Object();
  array['total'] =  gridHipotesis.getStore().getCount();
  array['hipotesis'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridHipotesis.getStore().getCount(); i++)
  {
  	array_data.push(gridHipotesis.getStore().getAt(i).data);
  }
  array['hipotesis'] = array_data;
  return Ext.JSON.encode(array);
}

function validarHipotesis()
{
	//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN HIPOTESIS ANTERIOR... ANTES DE CREAR OTRO..
	var storeValida = Ext.getCmp("gridHipotesis").getStore();
	var boolSigue = false;
	var boolSigue2 = false;
	
	if(storeValida.getCount() > 0)
	{
		var boolSigue_vacio = true;
		var boolSigue_igual = true;
		
		for(var i = 0; i < storeValida.getCount(); i++)
		{
			var id_sintoma = storeValida.getAt(i).data.id_sintomaHipotesis;
			var nombre_sintoma = storeValida.getAt(i).data.nombre_sintomaHipotesis;
					
			var id_hipotesis = storeValida.getAt(i).data.id_hipotesis;
			var nombre_hipotesis = storeValida.getAt(i).data.nombre_hipotesis;
																				
			var empleado = storeValida.getAt(i).data.empleado_asignacionCaso;
			var departamento = storeValida.getAt(i).data.departamento_asignacionCaso;
			var observacion = storeValida.getAt(i).data.observacion_asignacionCaso;
			var fecha = storeValida.getAt(i).data.fecha_asignacionCaso;
			
			if(id_hipotesis != "" && nombre_hipotesis != ""){ /*NADA*/  }
			else {  boolSigue_vacio = false; }

			if(i>0)
			{
				for(var j = 0; j < i; j++)
				{
					var id_sintoma_valida = storeValida.getAt(j).data.id_sintomaHipotesis;
					var nombre_sintoma_valida = storeValida.getAt(j).data.nombre_sintomaHipotesis;
					
					var id_hipotesis_valida = storeValida.getAt(j).data.id_hipotesis;
					var nombre_hipotesis_valida = storeValida.getAt(j).data.nombre_hipotesis;
																		
					var empleado_valida = storeValida.getAt(j).data.empleado_asignacionCaso;
					var departamento_valida = storeValida.getAt(j).data.departamento_asignacionCaso;
					var observacion_valida = storeValida.getAt(j).data.observacion_asignacionCaso;
					var fecha_valida = storeValida.getAt(j).data.fecha_asignacionCaso;
					
					if(id_sintoma_valida == id_sintoma && id_hipotesis == id_hipotesis_valida &&
					   empleado_valida == empleado && departamento == departamento_valida && 
					   observacion_valida == observacion && fecha == fecha_valida)
					{
						boolSigue_igual = false;	
					}
				}
			}
		} 
		
		if(boolSigue_vacio) { boolSigue = true; }	
		if(boolSigue_igual) { boolSigue2 = true; }						
	}
	else
	{
		boolSigue = true;
		boolSigue2 = true;
	}
	
	if(boolSigue && boolSigue2)
	{
		return true;
	}
	else if(!boolSigue)
	{
		Ext.Msg.alert('Alerta ', "Debe completar datos de las hipotesis a ingresar, antes de solicitar una nueva hipotesis");
		return false;
	}
	else if(!boolSigue2)
	{
		Ext.Msg.alert('Alerta ', "No puede ingresar el mismo sintoma y hipotesis! Debe modificar el registro repetido, antes de solicitar una nueva hipotesis");
		return false;
	}
	else
	{
		Ext.Msg.alert('Alerta ', "Debe completar datos de las hipotesis a ingresar, antes de solicitar una nueva hipotesis");
		return false;
	}	
}