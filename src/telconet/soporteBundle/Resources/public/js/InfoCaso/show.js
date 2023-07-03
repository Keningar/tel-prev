/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.QuickTips.init();


Ext.onReady(function(){
    Ext.define('Criterio', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'tipo', mapping:'tipo'},
            {name:'nombre', mapping:'nombre'},
            {name:'id_criterio_afectado', mapping:'id_criterio_afectado'},
            {name:'caso_id', mapping:'caso_id'},
            {name:'criterio', mapping:'criterio'},
            {name:'opcion', mapping:'opcion'}
        ]
    });
    Ext.define('Afectado', {
        extend: 'Ext.data.Model',
        fields: [
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
    
    storeCriterios_show = new Ext.data.JsonStore(
    {
        total: 'total',
        pageSize: 10,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : 'getCriterios',
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
    gridCriterios_show = Ext.create('Ext.grid.Panel', {
        title:'Criterios de Seleccion',
        width: 1000,
        height: 200,
        autoRender:true,
        enableColumnResize :false,
        id:'gridCriterios_show',
        store: storeCriterios_show,
	viewConfig: { enableTextSelection: true }, 
        loadMask: true,
        frame:true,
        forceFit:true,
	dockedItems: 
		[ 
			{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: 
				[                    
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_exportar',
                        text: 'Exportar',
                        scope: this,
                        handler: function(){ exportarExcelAfectados();}
                    }
                ]
			}
        ],       
        columns:[
                {
                  id: 'criteriosShow_id_criterio_afectado',
                  header: 'id_criterio_afectado',
                  dataIndex: 'id_criterio_afectado',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'criteriosShow_caso_id',
                  header: 'caso_id',
                  dataIndex: 'caso_id',
                  hidden: true,
                  sortable: true
                },
                {
                  id: 'criteriosShow_tipo_criterio',
                  header: 'Tipo Detalle',
                  dataIndex: 'tipo',
                  width:45,
                  hideable: false
                },
                {
                  id: 'criteriosShow_nombre_tipo_criterio',
                  header: 'Nombre Detalle',
                  dataIndex: 'nombre',
                  width:185,
                  hideable: false,
                  sortable: true
                },
                {
                  id: 'criteriosShow_criterio',
                  header: 'Criterio',
                  dataIndex: 'criterio',
                  width: 50,
                  hideable: false
                },
                {
                  id: 'criteriosShow_opcion',
                  header: 'Opcion',
                  dataIndex: 'opcion',
                  width: 280,
                  sortable: true
                }
            ],
         bbar: Ext.create('Ext.PagingToolbar', {
			    store: storeCriterios_show,
			    displayInfo: true,
			    displayMsg: 'Mostrando {0} - {1} de {2}',
			    emptyMsg: "No hay datos que mostrar."
			}),         
        renderTo: 'criterios_show'
    });
    
    ////////////////Grid  Afectados////////////////  
    storeAfectados_show = new Ext.data.JsonStore(
    {
        autoLoad: true,
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getAfectados',
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
            {name:'tipo_afectado', mapping:'tipo_afectado'},
            {name:'nombre_afectado', mapping:'nombre_afectado'},
            {name:'descripcion_afectado', mapping:'descripcion_afectado'}
        ]                
    });
    gridAfectados_show = Ext.create('Ext.grid.Panel', {
        title:'Equipos Afectados',
        width: 1000,
        height: 300,
        sortableColumns:false,
        store: storeAfectados_show,
		viewConfig: { enableTextSelection: true }, 
        id:'gridAfectados_show',
        enableColumnResize :false,
        loadMask: true,
        frame:true,
        forceFit:true,
        columns: 
		[
			 Ext.create('Ext.grid.RowNumberer'),
			 {
			  id: 'afectadosShow_id',
			  header: 'id',
			  dataIndex: 'id',
			  hidden: true,
			  hideable: false
			},
			 {
			  id: 'afectadosShow_id_afectado',
			  header: 'id_afectado',
			  dataIndex: 'id_afectado',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'afectadosShow_id_criterio',
			  header: 'id_criterio',
			  dataIndex: 'id_criterio',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'afectadosShow_caso_id_afectado',
			  header: 'caso_id_afectado',
			  dataIndex: 'caso_id_afectado',
			  hidden: true,
			  hideable: false 
			},
			{
			  id: 'afectadosShow_tipo_detalle_afectado',
			  header: 'Tipo Detalle',
			  dataIndex: 'tipo',
			  width: 48,
			  hideable: false
			},
			{
			  id: 'afectadosShow_nombre_tipo_detalle_afectado',
			  header: 'Nombre Detalle',
			  dataIndex: 'nombre',
			  width: 185,
			  hideable: false,
			  sortable: true
			},
			{
			  id: 'afectadosShow_tipo_afectado',
			  header: 'Tipo Afectado',
			  dataIndex: 'tipo_afectado',
			  width: 55,
			  hideable: false,
			  sortable: true
			},
			{
			  id: 'afectadosShow_nombre_afectado',
			  header: 'Parte Afectada',
			  dataIndex: 'nombre_afectado',
			  width: 120,
			  sortable: true
			},
			{
			  id: 'afectadosShow_descripcion_afectado',
			  header: 'Descripcion',
			  dataIndex: 'descripcion_afectado',
			  width: 160,
			  sortable: true
			},                   
			{
				xtype: 'actioncolumn',
				header: 'Acciones',
				width: 45,
				items: [
					{
						getClass: function(v, meta, rec) {	
							var actionClass = 'button-grid-show';
							
							var permiso = '{{ is_granted("ROLE_151-1") }}';
							//var permiso = $("#ROLE_147-124");
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ actionClass = "icon-invisible"; }
							if(rec.get('tipo_afectado') != "Cliente") { actionClass = "icon-invisible"; }
								
							if (actionClass == "icon-invisible") 
								this.items[0].tooltip = '';
							else 
								this.items[0].tooltip = 'Ver Cliente - Informacion Tecnica';
							
							return actionClass
						},
						handler: function(grid, rowIndex, colIndex) {
							var actionClass = 'button-grid-show';
							var rec = storeAfectados_show.getAt(rowIndex);
							
							var permiso = '{{ is_granted("ROLE_151-1") }}';
							//var permiso = $("#ROLE_147-124");
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ actionClass= "icon-invisible"; }
							
							// FALTA PERMISO DE  (/comercial/punto /Cliente/show)
							
							if(actionClass !="icon-invisible")
							{
								if(rec.get('tipo_afectado') == "Cliente") 
								{
									Ext.Ajax.request({
										url: "../../../inicio/cargaSession",
										method: 'post',
										params: { puntoId : rec.get('id_afectado')},
										success: function(response){
											var text = response.responseText;
											var urlNueva = "../../../tecnico/clientes";    
											//var urlNueva = "../../../comercial/punto/"+rec.get('id_afectado')+"/Cliente/show";    
											//window.location
											window.open(urlNueva);
										},
										failure: function(result)
										{
											Ext.Msg.alert('Error ','Error: ' + result.statusText);
										}
									});
								}
								else
									Ext.Msg.alert('Error ','Este afectado no es un cliente');
							}
							else
								Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');							
						}
					}
				]
			}
                
		], 
         bbar: Ext.create('Ext.PagingToolbar', {
			    store: storeAfectados_show,
			    displayInfo: true,
			    displayMsg: 'Mostrando {0} - {1} de {2}',
			    emptyMsg: "No hay datos que mostrar."
			}), 
        renderTo: 'afectados_show'
    });
    
	var id_caso = $('#id_caso').val();	
	
    ////////////////Grid  SINTOMAS E HIPOTESIS ////////////////      
	storeSintomas_show = new Ext.data.Store({ 
		pageSize: 100,
		autoLoad: true,
		total: 'total',
		proxy: {
			type: 'ajax',
			url : '../getSintomasXCaso',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				id: id_caso,
				nombre: '',
				estado: 'Todos',
				boolCriteriosAfectados: 'NO'
			}
		},
		fields:
		[
			{name:'id_sintoma', mapping:'id_sintoma'},
			{name:'nombre_sintoma', mapping:'nombre_sintoma'}
		]
	});	
	gridSintomas_show = Ext.create('Ext.grid.Panel', {
        title:'Sintomas',
        width: 1000,
        height: 200,
        autoRender:true,
        enableColumnResize :false,
        id:'gridSintomas_show',
        store: storeSintomas_show,
		viewConfig: { enableTextSelection: true }, 
        loadMask: true,
        frame:true,
        forceFit:true,
		columns: [
			{
				 id: 'detalleSintomas_id_sintoma',
				 header: 'SintomaId',
				 dataIndex: 'id_sintoma',
				 hidden: true,
				 hideable: false
			}, {
				 id: 'detalleSintomas_nombre_sintoma',
				 header: 'Sintoma',
				 dataIndex: 'nombre_sintoma',
				 width: 340
			}
		],
        renderTo: 'sintomas_show'
	});
			
	storeHipotesis_show = new Ext.data.Store({ 
		pageSize: 100,
		total: 'total',
		autoLoad:true,
		proxy: {
			type: 'ajax',
			url : '../getHipotesisXCaso',
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
			{name:'nombreDepartamento_asignacionCaso', mapping:'nombreDepartamento_asignacionCaso'},
			{name:'oficina_asignacionCaso', mapping:'oficina_asignacionCaso'},
			{name:'empresa_asignacionCaso', mapping:'empresa_asignacionCaso'},
			{name:'empleado_asignacionCaso', mapping:'empleado_asignacionCaso'},
			{name:'observacion_asignacionCaso', mapping:'observacion_asignacionCaso'},
			{name:'nombre_asignacionCaso', mapping:'nombre_asignacionCaso'},
			{name:'asignadoPor_asignacionCaso', mapping:'asignadoPor_asignacionCaso'},
			{name:'fecha_asignacionCaso', mapping:'fecha_asignacionCaso'},
			{name:'personaEmpresaRol_asignacionCaso', mapping:'personaEmpresaRol_asignacionCaso'},
			{name:'origen', mapping:'origen'}
		]
	});
	
	gridHipotesis_show = Ext.create('Ext.grid.Panel', {
        title:'Hipotesis',
        width: 1000,
        height: 300,
        autoRender:true,
        enableColumnResize :false,
        id:'gridHipotesis_show',
		store: storeHipotesis_show,
		viewConfig: { enableTextSelection: true }, 
        loadMask: true,
        frame:true,
        forceFit:true,
		columns: 
		[
			{
				id: 'detalleHipotesis_id_sintomaHipotesis',
				header: 'SintomaId',
				dataIndex: 'id_sintomaHipotesis',
				hidden: true,
				hideable: false
			}, {
				id: 'detalleHipotesis_nombre_sintomaHipotesis',
				header: 'Sintoma',
				dataIndex: 'nombre_sintomaHipotesis',
				width: 220,
				hidden: true,
				hideable: false
			},
			{   
				id: 'detalleHipotesis_id_hipotesis',
				header: 'HipotesisId',
				dataIndex: 'id_hipotesis',
				hidden: true,
				hideable: false
			 }, {
				 id: 'detalleHipotesis_nombre_hipotesis',
				 header: 'Hipotesis',
				 dataIndex: 'nombre_hipotesis',
				 width: 180,
				 sortable: true
			},
			{   
				id: 'detalleHipotesis_asunto_asignacionCaso',
				dataIndex: 'asunto_asignacionCaso',
				hidden: true,
				hideable: false
			},
			{   
				id: 'detalleHipotesis_departamento_asignacionCaso',
				dataIndex: 'departamento_asignacionCaso',
				hidden: true,
				hideable: false
			},
			{   
				id: 'detalleHipotesis_empleado_asignacionCaso',
				dataIndex: 'empleado_asignacionCaso',
				hidden: true,
				hideable: false
			},
			{
				id: 'detalleHipotesis_nombre_asignacionCaso',
				header: 'Asignado Caso',
				xtype: 'templatecolumn', 
				width: 260,
				tpl: '<span class="bold">Oficina Asignada:</span> {oficina_asignacionCaso}</br> <span class="bold">Departamento Asignado:</span> {nombreDepartamento_asignacionCaso}</br> <span class="bold">Asignado Por:</span> {asignadoPor_asignacionCaso}</br>  <span class="bold">Empleado Asignado:</span> {nombre_asignacionCaso}</br> <span class="bold">Fecha Asignacion:</span> {fecha_asignacionCaso}'	
			},
			{   
				id: 'detalleHipotesis_observacion_asignacionCaso',
				dataIndex: 'observacion_asignacionCaso',
				header: 'Observacion',
				width: 270
			},
			{
				id: 'detalleHipotesis_personaEmpresaRol_asignacionCaso',
				dataIndex: 'personaEmpresaRol_asignacionCaso',
				hidden: true,
				hideable: false
			},
			{
				xtype: 'actioncolumn',
				header: 'Acciones',
				width: 80,
				items: 
				[				
					{
						getClass: function(v, meta, rec) {
							var clas = "icon-invisible"; 
							if(rec.get('observacion_asignacionCaso') != '')
							{
								clas = "button-grid-show"; 
							}
							
							if (clas == "icon-invisible") 
								this.items[0].tooltip = '';
							else 
								this.items[0].tooltip = 'Ver Observacion';
								
							return clas 
						},
						tooltip: 'Ver Observacion',
						handler: function(grid, rowIndex, colIndex) {
							var rec = storeHipotesis_show.getAt(rowIndex);
							
							//window.location = rec.get('id_caso')+"/show";
								
							btncancelarObservacion = Ext.create('Ext.Button', {
								text: 'Cerrar',
								cls: 'x-btn-rigth',
								handler: function() {
									winObservacion.destroy();
								}
							});
							
							formPanelObservacion = Ext.create('Ext.form.Panel', {
								bodyPadding: 5,
								waitMsgTarget: true,
								height: 200,
								layout: 'fit',
								fieldDefaults: {
									labelAlign: 'left',
									labelWidth: 80,
									msgTarget: 'side'
								},
								items: 
								[
									{
										xtype: 'fieldset',
										title: 'Detalle Observacion',
										defaultType: 'textfield',
										items: 
										[
											{
												xtype: 'textarea',
												fieldLabel: 'Observacion:',
												id: 'detalle_observacionCaso',
												name: 'detalle_observacionCaso',
												value: rec.get('observacion_asignacionCaso'),
												readOnly: true,
												rows: 9,
												cols: 80
											}
										]
									}
								]
							});
				
							winObservacion = Ext.create('Ext.window.Window', {
								title: 'Informacion de la Hipotesis',
								modal: true,
								width: 600,
								height: 300,
								resizable: false,
								layout: 'fit',
								items: [formPanelObservacion],
								buttonAlign: 'center',
								buttons:[btncancelarObservacion]
							}).show(); 
						}
					}
				]
			}
		],    
        renderTo: 'hipotesis_show'
    });
	
    ////////////////Grid  Detalles////////////////  
    storeDetalles_show = new Ext.data.JsonStore(
    {
        pageSize: 200,
        autoLoad: true,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getDetalles',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'id', mapping:'id'},
            {name:'id_sintoma', mapping:'id_sintoma'},
            {name:'id_hipotesis', mapping:'id_hipotesis'},
            {name:'id_tarea', mapping:'id_tarea'},
            {name:'nombre_sintoma', mapping:'nombre_sintoma'},
            {name:'nombre_hipotesis', mapping:'nombre_hipotesis'},
            {name:'nombre_tarea', mapping:'nombre_tarea'},
	    {name:'id_detAsig', mapping:'id_detAsig'},
            {name:'motivo_detAsig', mapping:'motivo_detAsig'},
            {name:'fecha_estado', mapping:'fecha_estado'},
            {name:'estado', mapping:'estado'},
            {name:'esSolucion', mapping:'esSolucion'},
            {name:'id_caso', mapping:'id_caso'},
	    {name:'tiempoCliente', mapping:'tiempoCliente'},
	    {name:'tiempoEmpresa', mapping:'tiempoEmpresa'},
	    {name:'tiempoTotal', mapping:'tiempoTotal'},
	    {name:'fechaEjecucion', mapping:'fechaEjecucion'},
	    {name:'fechaFinalizacion', mapping:'fechaFinalizacion'},
	    {name:'numero_caso', mapping:'numero_caso'},
	    {name:'observacion', mapping:'observacion'},
	    {name:'accion', mapping:'accion'},
	    {name:'accion1', mapping:'accion1'},
            {name:'tareaEsHal', mapping:'tareaEsHal'},
            {name:'esHal', mapping:'esHal'},
            {name:'fechaCreacion', mapping:'fechaCreacion'},
            {name:'atenderAntes', mapping:'atenderAntes'}
        ]                
    });
    gridDetalles_show = Ext.create('Ext.grid.Panel', {
        title:'Detalle de Tareas',
        width: 1000,
        height: 400,
        sortableColumns:false,
        store: storeDetalles_show,
	viewConfig: { enableTextSelection: true },
        id:'gridDetalles_show',
        enableColumnResize :false,
        loadMask: true,
        frame:true,
        forceFit:true,
	listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            },
                            viewready: function (grid) {
                                var view = grid.view;

                                // record the current cellIndex
                                grid.mon(view, {
                                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                                        grid.cellIndex = cellIndex;
                                        grid.recordIndex = recordIndex;
                                    }
                                });

                                grid.tip = Ext.create('Ext.tip.ToolTip', {
                                    target: view.el,
                                    delegate: '.x-grid-cell',
                                    trackMouse: true,
                                    renderTo: Ext.getBody(),
                                    listeners: {
                                        beforeshow: function updateTipBody(tip) {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                            }
                                        }
                                    }
                                });

                            }                                    
                    },
        columns: [
			Ext.create('Ext.grid.RowNumberer'),
			{
			  id: 'detalle_id',
			  header: 'id',
			  dataIndex: 'id',
			  hidden: true,
			  hideable: false
			},
			 {
			  id: 'detalle_id_sintoma',
			  header: 'idSintoma',
			  dataIndex: 'id_sintoma',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'detalle_id_hipotesis',
			  header: 'idHipotesis',
			  dataIndex: 'id_hipotesis',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'detalle_id_tarea',
			  header: 'idTarea',
			  dataIndex: 'id_tarea',
			  hidden: true,
			  hideable: false 
			},
			{
				id: 'detalle_nombre_sintoma',
				header: 'Sintoma',
				dataIndex: 'nombre_sintoma',
				width:170,
				hideable: false,
				sortable: true,
				renderer: function (value, metaData, record, rowIndex, colIndex, store){  
					var esSolucion = record.data.esSolucion;
					if(esSolucion == "SI")
					{
						metaData.tdCls = 'custom-verde';
					}
					return  record.data.nombre_sintoma;
				}
			},
			{
				id: 'detalle_nombre_hipotesis',
				header: 'Hipotesis',
				dataIndex: 'nombre_hipotesis',
				width:180,
				hideable: false,
				sortable: true,
				renderer: function (value, metaData, record, rowIndex, colIndex, store){  
					var esSolucion = record.data.esSolucion;
					if(esSolucion == "SI")
					{
						metaData.tdCls = 'custom-verde';
					}
					return  record.data.nombre_hipotesis; 
				}
			},
			{
				id: 'detalle_nombre_tarea',
				header: 'Tarea',
				dataIndex: 'nombre_tarea',
				width:190,
				hideable: false,
				sortable: true,
				renderer: function (value, metaData, record, rowIndex, colIndex, store){  
					var esSolucion = record.data.esSolucion;
					if(esSolucion == "SI")
					{
						metaData.tdCls = 'custom-verde';
					}
					return  record.data.nombre_tarea;  
				}
			},
			
			
			              
                           {
				id: 'detalle_nombre_motivo',
				header: 'Observacion de Tarea',
				dataIndex: 'motivo_detAsig',
				width:170,
				hideable: false,
				sortable: true,
				renderer: function (value, metaData, record, rowIndex, colIndex, store){  
					var esSolucion = record.data.esSolucion;
					if(esSolucion == "SI")
					{
						metaData.tdCls = 'custom-verde';
					}
					return  record.data.motivo_detAsig;  
				}
                                
			},   
			
			
			{
			  id: 'detalle_fecha_estado',
			  header: 'Fecha Estado',
			  dataIndex: 'fecha_estado',
			  width:130,
			  sortable: true,
				renderer: function (value, metaData, record, rowIndex, colIndex, store){  
					var esSolucion = record.data.esSolucion;
					if(esSolucion == "SI")
					{
						metaData.tdCls = 'custom-verde';
					}
					return  record.data.fecha_estado;  
				}
			},
			{
			  id: 'detalle_estado',
			  header: 'Estado',
			  dataIndex: 'estado',
			  width:90,
			  sortable: true,
				renderer: function (value, metaData, record, rowIndex, colIndex, store){  
					var esSolucion = record.data.esSolucion;
					if(esSolucion == "SI")
					{
						metaData.tdCls = 'custom-verde';
					}
					return  record.data.estado;
				}
			},
			{
				id: 'detalle_esSolucion',
				header: 'Es Solucion',
				dataIndex: 'esSolucion',
				width:100,
				sortable: true,
				renderer: function (value, metaData, record, rowIndex, colIndex, store){  
					var esSolucion = record.data.esSolucion;
					if(esSolucion == "SI")
					{
						metaData.tdCls = 'custom-verde';
					}
					return esSolucion;  
				}
			},
                        {
                            id        : 'esHal',
                            dataIndex : 'esHal',
                            header    : 'Es Hal',
                            width     :  70,
                            sortable  :  true,
                            renderer  : function (value, metaData, record, rowIndex, colIndex, store) {
                                if(record.data.esSolucion.toUpperCase() === "SI") {
                                    metaData.tdCls = 'custom-verde';
                                }
                                return record.data.esHal;
                            }
                        },
                        {
                            id        : 'atenderAntes',
                            dataIndex : 'atenderAntes',
                            header    : 'Atender Antes',
                            width     :  120,
                            sortable  :  true,
                            renderer  : function (value, metaData, record, rowIndex, colIndex, store) {
                                if(record.data.esSolucion.toUpperCase() === "SI") {
                                    metaData.tdCls = 'custom-verde';
                                }
                                return record.data.atenderAntes;
                            }
                        },
                        {
                            id        : 'tareaEsHal',
                            dataIndex : 'tareaEsHal',
                            hidden    : true,
                            hideable  : false
                        },
			{
				header: 'Acciones',
				xtype: 'actioncolumn',
				width:130,
				sortable: false,
				items: 
				[
					{
						getClass: function(v, meta, rec) {
							var clas = "button-grid-verAsignado";
							
							var permiso = '{{ is_granted("ROLE_78-50") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ clas = "icon-invisible"; }
								
							if (clas == "icon-invisible") 
								this.items[0].tooltip = '';
							else 
								this.items[0].tooltip = 'Ver Asignado';
							storeDetalles_show
							return clas;
						},
						handler: function(grid, rowIndex, colIndex) {
							var rec = storeDetalles_show.getAt(rowIndex);
								
							var clas = "button-grid-verAsignado";
							
							var permiso = '{{ is_granted("ROLE_78-50") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
							if(!boolPermiso){ clas = "icon-invisible"; }
								
							if(clas != "icon-invisible")
								verAsignadoTarea('', '',gridDetalles_show.getStore().getAt(rowIndex).data, 'global'); 
							else
								Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
						}
					},
				   
					{
						  getClass: function(v, meta, rec) {
							  var permiso = '{{ is_granted("ROLE_78-50") }}';
							  var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							  if(!boolPermiso){ rec.data.accion = "icon-invisible"; }
								  
							  if (rec.get('accion') == "icon-invisible") 
								  this.items[1].tooltip = '';
							  else 
								  this.items[1].tooltip = 'Ingresar Seguimiento';
							  
							  return rec.get('accion');
						  },
						  handler: function(grid, rowIndex, colIndex) {
							 var rec = storeDetalles_show.getAt(rowIndex);
								  
							  var permiso = '{{ is_granted("ROLE_78-50") }}';
							  var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
							  if(!boolPermiso){ rec.data.accion = "icon-invisible"; }
								  
							  if(rec.get('accion')!="icon-invisible")
								  agregarSeguimiento(gridDetalles_show.getStore().getAt(rowIndex).data.id_caso,gridDetalles_show.getStore().getAt(rowIndex).data.numero_caso,gridDetalles_show.getStore().getAt(rowIndex).data); 
							  else
								  Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
						  }
					},
				   
				   
				   
				   //VER SEGUIMIENTO DE TAREA					
					{
						getClass: function(v, meta, rec) {
							var clas = "button-grid-show";
							
							var permiso = '{{ is_granted("ROLE_78-50") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							if(!boolPermiso){ clas = "icon-invisible"; }
								
							if (clas == "icon-invisible") 
								this.items[2].tooltip = '';
							else 
								this.items[2].tooltip = 'Ver Seguimiento Tarea';
							
							return clas;
						},
						handler: function(grid, rowIndex, colIndex) {
							var rec = storeDetalles_show.getAt(rowIndex);
								
							var clas = "button-grid-show";
							
							var permiso = '{{ is_granted("ROLE_78-50") }}';
							var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
							if(!boolPermiso){ clas = "icon-invisible"; }
								
							if(clas != "icon-invisible")
								verSeguimientoTarea(gridDetalles_show.getStore().getAt(rowIndex).data); 
							else
								Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
						}
					},
				   
					
					{
						  getClass: function(v, meta, rec) {
							  var permiso = '{{ is_granted("ROLE_78-50") }}';
							  var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
							  if(!boolPermiso){ rec.data.accion1 = "icon-invisible"; }
								  
							  if (rec.get('accion1') == "icon-invisible") 
								  this.items[3].tooltip = '';
							  else 
								  this.items[3].tooltip = 'Detalle de Tarea';
							  
							  return rec.get('accion1');
						  },
						  handler: function(grid, rowIndex, colIndex) {
							 var rec = storeDetalles_show.getAt(rowIndex);
								  
							  var permiso = '{{ is_granted("ROLE_78-50") }}';
							  var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
							  if(!boolPermiso){ rec.data.accion1 = "icon-invisible"; }
								  
							  if(rec.get('accion1')!="icon-invisible")
								  verDetalleFinalTarea(gridDetalles_show.getStore().getAt(rowIndex).data); 
							  else
								  Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
						  }
					},
				]
			},
			
		],    
        renderTo: 'detalles_show'
    });
    
	//********* TOOLBAR
    var flag1                  = $('#flag1').val();
    var flag2                  = $('#flag2').val();
    var flag3                  = $('#flag3').val();
    var flag4                  = $('#flag4').val();
    var flagCerrarCasoTN       = $('#flagCerrarCasoTN').val();
    var empresa                = $('#empresa').val();    
    var flagCreador            = $('#flagCreador').val();
    var flagBoolAsignado       = $('#flagBoolAsignado').val();
    var flagAsignado           = $('#flagAsignado').val();
    var flagTareasTodas        = $('#flagTareasTodas').val();
    var flagTareasTodasCanceladas= $('#flagTareasTodasCanceladas').val();
    var flagTareasAbiertas     = $('#flagTareasAbiertas').val();
    var flagTareasSolucionadas = $('#flagTareasSolucionadas').val();
    var ultimo_estado          = $('#ultimo_estado').val();
    var mostrarHipotesis       = $('#mostrarHipotesis').val();
    var id_caso                = $('#id_caso').val();
    var tiempoTotalCaso        = $('#tiempoTotalCaso').val();
    var nuevoEsquema           = $('#esCasoNuevoEsquema').val();
    var numero_caso            = $('#numero_caso').val();
    var fecha_apertura         = $('#fecha_apertura').val();
    var hora_apertura          = $('#hora_apertura').val();    
    var version_ini            = $('#version_ini').val();
    var elementoAfectado       = $('#elementoAfectado').val();
    var hipotesisIniciales     = $('#hipotesisIniciales').val();
    var tipo_afectacion        = $('#tipo_afectacion').val();	
	var fechaFin               = $('#fechaFin').val(); 
	var horaFin                = $('#horaFin').val(); 
	var tituloInicial          = $('#titulo_ini').val();
	var tipoAfectacion         = $('#tipo_afectacion').val();
	var idNivelCriticidad      = $('#idNivelCriticidad').val();
	var nivelCriticidad        = $('#nivelCriticidad').val();
	var origen       		   = $('#origen').val();
	
	var esDepartamento = $('#esDepartamento').val(); 					

	if(flagTareasTodas==1 || flagTareasTodas=="Si" || flagTareasTodas){
	    editar=false;
	    tieneTareas = true;
	}else{
	    editar=true;	
	    tieneTareas = false;
	}

	var flagVistaSintomas = true; var flagVistaHipotesis = true; var flagVistaTareas = true; var flagVistaCerrar = true;var flagVistaCerrarTN = true;
	if(	flagCreador && !flagBoolAsignado ){ /* nada */} else{ flagVistaSintomas = false; }
	if(	((flagCreador && !flagBoolAsignado) || (flagBoolAsignado && flagAsignado)) && (flagTareasSolucionadas) ){ /* nada */} else{ flagVistaHipotesis = false; }
	if(	((flagCreador && !flagBoolAsignado) || (flagBoolAsignado && flagAsignado)) ){ /* nada */} else{ flagVistaTareas = false; }
	if(	((flagCreador && !flagBoolAsignado) || (flagBoolAsignado && flagAsignado)) 
        && (!flagTareasTodas || (flagTareasTodas && flagTareasAbiertas && !flagTareasSolucionadas)) ){ /* nada */}else{ flagVistaCerrar = false; }
    if(	((flagCreador && !flagBoolAsignado) || (flagBoolAsignado && flagAsignado)) 
        && (!flagTareasTodas || (flagTareasTodas && flagTareasAbiertas && flagCerrarCasoTN)) ){ /* nada */}else{ flagVistaCerrarTN = false; }
    var tb = Ext.create('Ext.toolbar.Toolbar');
    tb.add("<div style='font-weight:bold;'>Acciones:</div>");
	
	var permiso = '{{ is_granted("ROLE_78-32") }}';
    var boolPermisoSintoma = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
    if (ultimo_estado !== "Cerrado" && !flag1 && boolPermisoSintoma && flagVistaSintomas && flagTareasSolucionadas)
    {
        tb.add({
            icon: '/bundles/soporte/images/iconos/16/agregar_sintoma.png',
            handler: function()
            {                
                var obj = '{'
                    + '"id_caso"         :  ' + id_caso + ','
                    + '"numero_caso"     : "' + numero_caso + '",'
                    + '"fecha_apertura"  : "' + fecha_apertura + '",'
                    + '"hora_apertura"   : "' + hora_apertura + '",'
                    + '"version_ini"     : "' + version_ini + '",'
                    + '"flagCreador"     : "' + flagCreador + '",'
                    + '"flagBoolAsignado": "' + flagBoolAsignado + '",'
                    + '"visualizacion"   : "show"'
                    + '}';

                var json = Ext.JSON.decode(obj);
                agregarSintoma(json);
            },
            tooltip: 'Agregar Sintoma'
        }
        );
    }
	
	var permiso = '{{ is_granted("ROLE_78-31") }}';
	var boolPermisoHipotesis = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
	if((ultimo_estado!="Cerrado" && boolPermisoHipotesis && flagVistaHipotesis && flagTareasSolucionadas) ||
            (ultimo_estado!="Cerrado" && empresa == "TN" && mostrarHipotesis == "S"))
	{
	    tb.add({
	            icon: '/bundles/soporte/images/iconos/16/agregar_hipotesis.png',
	            handler: function() {
				   agregarHipotesis(id_caso, numero_caso, fecha_apertura, hora_apertura, version_ini);
	            },
	            tooltip: 'Agregar Hipotesis'
	        }
	    );
	}
	
	var permiso = '{{ is_granted("ROLE_78-33") }}';
	var boolPermisoTareas = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
	if((ultimo_estado!="Cerrado" && ultimo_estado!="Creado" && empresa == "TN") || (
        ultimo_estado!="Cerrado" && flag2 && boolPermisoTareas && flagVistaTareas && flagTareasSolucionadas))
	{
	    tb.add({
	            icon: '/bundles/soporte/images/iconos/16/agregar_tarea.png',
	            handler: function() {
				   agregarTarea(id_caso, numero_caso, fecha_apertura, hora_apertura, version_ini, esDepartamento,elementoAfectado,empresa);
	            },
	            tooltip: 'Agregar Tarea'
	        }
	    );
	}
	
	var permiso = '{{ is_granted("ROLE_78-51") }}';
	var boolPermisoAdminTareas = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
	if((ultimo_estado!="Cerrado" && ultimo_estado!="Creado" && empresa == "TN") || (
        ultimo_estado!="Cerrado" && flag3 && boolPermisoAdminTareas && flagVistaTareas && flagTareasSolucionadas))
	{
	    tb.add({
	            icon: '/bundles/soporte/images/iconos/16/administrar_tarea.png',
	            handler: function() {
				   administrarTareas(id_caso, numero_caso, fecha_apertura, hora_apertura, version_ini);
	            },
	            tooltip: 'Administrar Tareas'
	        }
	    );
	}
	
	var permiso = '{{ is_granted("ROLE_78-39") }}';
    var boolPermisoSintoma = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
    if (ultimo_estado !== "Cerrado" && boolPermisoSintoma && empresa ==='TN' && tipoCaso === 'Backbone')
    {
        tb.add({
            icon: '/bundles/soporte/images/iconos/16/afectados.png',
            handler: function()
            {                
                agregarAfectadosCaso(id_caso,"show");
            },
            tooltip: 'Agregar Afectados'
        }
        );
    }

	var permiso = '{{ is_granted("ROLE_78-36") }}';
	var boolPermisoCerrarCaso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
    
    
	if( 
    (ultimo_estado != "Cerrado" && boolPermisoCerrarCaso && flagVistaCerrar && flag4 == "1" && empresa != "TN") ||
    (ultimo_estado != "Cerrado" && boolPermisoCerrarCaso && flagVistaCerrarTN && flag4 == "1" && empresa == "TN"))
    {
        tb.add({
            icon: '/bundles/soporte/images/iconos/16/cerrar_caso.png',
            handler: function() {
                var irFlujoTN=0;
                if(empresa=="TN")
                {
                    if(tieneTareas)
                    {
                        if(!flagTareasTodasCanceladas)
                        {
                            irFlujoTN=1;
                        }
                    }
                }
                
                if(irFlujoTN==1)
                {
                    var data = {
                                id_caso       : id_caso,
                                numero_caso   : numero_caso,
                                fecha_apertura: fecha_apertura,
                                hora_apertura : hora_apertura,                    
                                tiene_tareas  : tieneTareas,
                                fecha_final   : fechaFin,
                                hora_final    : horaFin,
                                hipotesisIniciales : hipotesisIniciales,
                                afectacion    : tipo_afectacion,
                                boolPermisoVerSeguimientosCerrarCaso: boolPermisoVerSeguimientosCerrarCaso,
                                tiempoTotalCaso    : tiempoTotalCaso,
                                nuevoEsquema: nuevoEsquema
                            };

                    cerrarCasoTN(data);
                }
                else
                {
                    var conn = new Ext.data.Connection({
                        listeners: {
                            'beforerequest': {
                                fn: function(con, opt) {
                                    Ext.get(document.body).mask('Obteniendo Fecha y Hora...');
                                },
                                scope: this
                            },
                            'requestcomplete': {
                                fn: function(con, res, opt) {
                                    Ext.get(document.body).unmask();
                                },
                                scope: this
                            },
                            'requestexception': {
                                fn: function(con, res, opt) {
                                    Ext.get(document.body).unmask();
                                },
                                scope: this
                            }
                        }
                    });
                    
                    conn.request({
                        method: 'POST',                                                                                                                                                                          
                        url:url_obtenerFechaServer,
                        success: function(response) 
                        {
                            var json = Ext.JSON.decode(response.responseText);                                                                                                

                            if (json.success)
                            {                              
                                var data = {
                                    id_caso       : id_caso,
                                    numero_caso   : numero_caso,
                                    fecha_apertura: fecha_apertura,
                                    hora_apertura : hora_apertura,                    
                                    tiene_tareas  : tieneTareas,
                                    fecha_final   : fechaFin,
                                    hora_final    : horaFin,
                                    hipotesisIniciales : hipotesisIniciales,
                                    fechaActual   : json.fechaActual,
                                    horaActual    : json.horaActual,
                                    afectacion    : tipo_afectacion,
                                    tiempoTotalCaso : tiempoTotalCaso,
                                    nuevoEsquema    : nuevoEsquema
                                };
                                cerrarCaso(data);

                            }
                            else
                            {
                                Ext.Msg.alert('Alerta ', json.error);
                            }
                        }
                    });
                } 
            },
            tooltip: 'Cerrar Caso'
        });
    }

    if(cantidadCasosAp > 0)
    {
        tb.add({
            icon    : '/bundles/soporte/images/iconos/16/buscarCasosAp.png',
            tooltip : 'Ver Casos Aperturados',
            handler: function() {

                var data = {idCaso : id_caso};
                verCasosAperturados(data);
            }
        });
    }

	if (empresa ==='TN' && tipoCaso === 'Tecnico' && origen === 'E' && permisoEditarCasosExtranet)
    {
        tb.add({
            icon: '/bundles/soporte/images/iconos/16/editar.png',
            handler: function()
            {   
				let data = {
					idCaso       : id_caso,
					numeroCaso   : numero_caso,
					fechaApertura: fecha_apertura,
					horaApertura : hora_apertura,                    
					tieneTareas  : tieneTareas,
					hipotesisIniciales : hipotesisIniciales,
					afectacion    : tipo_afectacion,
					tituloInicial : tituloInicial,
					tipoAfectacion: tipoAfectacion,
					idNivelCriticidad: idNivelCriticidad,
					nivelCriticidad: nivelCriticidad,
					versionInicial: version_ini,
					serviciosAfectados: storeAfectados_show
				};
                editarCaso(data);
            },
            tooltip: 'Editar caso'
        });
    }

    tabs = Ext.create('Ext.tab.Panel', {
        id:'tab_panel',
        renderTo: 'contenedor_principal',
        width: 1005,
        autoScroll: true,
        activeTab: 0,
        defaults : { autoHeight: true },
        plain:true,
        tbar: tb,
        deferredRender:false,
        hideMode: 'offsets',
        frame:false,
        items: [
			{
	            contentEl:'datos_generales', 
	            title: 'Datos Generales',
	            id:'tab_datos_generales',
	            autoRender:true,
	            autoShow:true,
	            closable: false            
	        },
			{
	            contentEl:'datos_afectados', 
	            title: 'Afectados',
	            id:'tab_afectados',
	            listeners: {
	                activate: function(tab){
	                    gridCriterios_show.view.refresh();
	                    gridAfectados_show.view.refresh();
	                }
	            },
	            closable: false
	        },
			{
	            contentEl:'datos_sintomas_hipotesis', 
	            title: 'Detalle de Sintomas e Hipotesis',
	            id:'tab_sintomas_hipotesis',
	            listeners: {
	                activate: function(tab){
	                    gridSintomas_show.view.refresh();
	                    gridHipotesis_show.view.refresh();
	                }
	            },
	            closable: false
	        },
			{
	            contentEl:'datos_detalles', 
	            title: 'Detalles de Tareas',
	            id:'tab_detalles',
	            listeners: {
	                activate: function(tab){
	                    gridDetalles_show.view.refresh();
	                }
	            },
	            closable: false
	        }
        ]
    });
});


function verDetalleFinalTarea(data){    
  
  
       
    btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winSeguimiento.destroy();
            }
    });
    
                        
            
    formPanel2 = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            height: 200,
            width: 500,
            layout: 'fit',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 140,
                msgTarget: 'side'
            },

            items: [{
                xtype: 'fieldset',
                title: 'Información',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Tarea:',
                        id: 'tareaCaso',
                        name: 'tareaCaso',
                        value: data.nombre_tarea
                    },
		     {
                        xtype: 'displayfield',
                        fieldLabel: 'Observacion:',
                        id: 'observacion',
                        name: 'observacion',
                        value: data.observacion
                    },
                    {
                        xtype : 'displayfield',
                        fieldLabel: 'Fecha Creación:',
                        id: 'fechaCreacion',
                        name: 'fechaCreacion',
                        value: data.fechaCreacion
                    },
		     {
                        xtype: 'displayfield',
                        fieldLabel: 'Fecha Ejecucion:',
                        id: 'feEjecucion',
                        name: 'feEjecucion',
                        value: data.fechaEjecucion,
                        hidden: true
                    },
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Fecha Finalización:',
                        id: 'feFinalizacion',
                        name: 'feFinalizacion',
                        value: data.fechaFinalizacion
                    },
		     {
                        xtype: 'displayfield',
                        fieldLabel: 'Tiempo Total:',
                        id: 'tiempoTotal',
                        name: 'tiempoTotal',
                        value: data.tiempoTotal
                    },
		     {
                        xtype: 'displayfield',
                        fieldLabel: 'Tiempo Empresa:',
                        id: 'tiempoEmpresa',
                        name: 'tiempoEmpresa',
                        value: data.tiempoEmpresa
                    },
		     {
                        xtype: 'displayfield',
                        fieldLabel: 'Tiempo Cliente:',
                        id: 'tiempoCliente',
                        name: 'tiempoCliente',
                        value: data.tiempoCliente
                    }
                ]
            }]
         });
    winSeguimiento = Ext.create('Ext.window.Window', {
            title: 'Detalle Final de Tarea',
            modal: true,
            width: 600,
            height: 300,
            resizable: false,
            layout: 'fit',
            items: [formPanel2],
            buttonAlign: 'center',
            buttons:[btncancelar2]
    }).show(); 
    
}

function exportarExcelAfectados(){
  
  $('#hid_id_caso').val(($('#id_caso').val()));  
  $( "#formExportar" ).submit();
  
  
}
