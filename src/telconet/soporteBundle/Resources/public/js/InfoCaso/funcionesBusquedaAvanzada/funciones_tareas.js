/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function obtenerTareas()
{
  var array = new Object();
  array['total'] =  gridTareas.getStore().getCount();
  array['tareas'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridTareas.getStore().getCount(); i++)
  {
  	array_data.push(gridTareas.getStore().getAt(i).data);
  }
  array['tareas'] = array_data;
  return Ext.JSON.encode(array);
}

function obtenerMateriales()
{
  var array = new Object();
  array['total'] =  gridMaterialTareas.getStore().getCount();
  array['materiales'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridMaterialTareas.getStore().getCount(); i++)
  {
  	array_data.push(gridMaterialTareas.getStore().getAt(i).data);
  }
  array['materiales'] = array_data;
  return Ext.JSON.encode(array);
}

function agregarTarea(rec){
	var id_caso = rec.get('id_caso');
	var numero = rec.get('numero_caso');
	var fecha = rec.get('fecha_apertura');
	var hora = rec.get('hora_apertura');
	var version_inicial = rec.get('version_ini');
	var empresa = rec.get('empresa');

	var flag1 = rec.get('flag1');
	var flag2 = rec.get('flag2');
	var flag3 = rec.get('flag3');
	var flagCreador = rec.get('flagCreador');
	var flagBoolAsignado = rec.get('flagBoolAsignado');
	var flagAsignado = rec.get('flagAsignado');
	var flagTareasAbiertas = rec.get('flagTareasAbiertas');
	var flagTareasSolucionadas = rec.get('flagTareasSolucionadas');
	var ultimo_estado = rec.get('estado');
				
	var global_id_empleado = $('#global_id_empleado').val() ? $('#global_id_empleado').val() : '';	
	var globalComboEmpresa = $('#globalEmpresaEscogida').val();													
	var arrayValores = globalComboEmpresa.split('@@');
	var valorIdDepartamento = '';
	if(arrayValores && arrayValores.length > 3)
	{
		valorIdDepartamento = arrayValores[4];
		valorDepartamento = arrayValores[5];
	}
														
    winTareas = "";
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
	
    if (winTareas)
    {
		cierraVentanaByIden(winTareas);
		winTareas = "";
	}
	
    if (!winTareas)
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
	                
	                for(var i=0; i < gridTareas.getStore().getCount(); i++)
	                {
	                      if(gridTareas.getStore().getAt(i).data.id_asignado==""){
	                          Ext.Msg.alert("Alerta","Debe asignar la tarea por lo menos a un departamento.");
	                          return ;
	                      }
	                          
	                }
	                json_tareas = obtenerTareas();
	                conn.request({
	                    method: 'POST',
	                    params :{
	                        id_caso: id_caso,
	                        tareas: json_tareas
	                    },
	                    url: '../soporte/info_caso/' + 'actualizarTareas',
	                    success: function(response){
	                        Ext.Msg.alert('Mensaje','Se actualizaron las tareas.', function(btn){
	                            if(btn=='ok'){
									cierraVentanaByIden(winTareas);	
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
	    });
	    btncancelar = Ext.create('Ext.Button', {
	            text: 'Cerrar',
	            cls: 'x-btn-rigth',
	            handler: function() {
					cierraVentanaByIden(winTareas);
	            }
	    });
	       
	    storeHipotesis = new Ext.data.Store({ 
	        pageSize: 1000,
	        total: 'total',
	        proxy: {
	            type: 'ajax',
	            url : '../soporte/info_caso/' + 'getHipotesisXCaso',
	            reader: {
	                type: 'json',
	                totalProperty: 'total',
	                root: 'encontrados'
	            },
	            extraParams: {
	                band: 'tarea',
	                id: id_caso,
	                nombre: '',
	                estado: 'Activos'
	            }
	        },
	        fields:
			[
				{name:'id_sintoma', mapping:'id_sintoma'},
				{name:'nombre_sintoma', mapping:'nombre_sintoma'},
				{name:'id_hipotesis', mapping:'id_hipotesis'},
				{name:'nombre_hipotesis', mapping:'nombre_hipotesis'},
				{name:'asunto_asignacionCaso', mapping:'asunto_asignacionCaso'},
				{name:'departamento_asignacionCaso', mapping:'departamento_asignacionCaso'},
				{name:'empleado_asignacionCaso', mapping:'empleado_asignacionCaso'},
				{name:'observacion_asignacionCaso', mapping:'observacion_asignacionCaso'},
				{name:'nombre_asignacionCaso', mapping:'nombre_asignacionCaso'},
				{name:'origen', mapping:'origen'}
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
				   
					winTareas = "";
					formPanel = "";
				},
	            load: function(sender, node, records) {
	                Ext.each(records, function(record, index){
	                    if(storeHipotesis.getCount()>0){
	                        selModelHipotesis = Ext.create('Ext.selection.CheckboxModel', {
	                            listeners: {
	                                 selectionchange: function(sm, selections) {
										if(flagBoolAsignado && flagAsignado)
										{
											//alert("Permisos crear Hipotesis");
											gridTareas.down('#addButton').setDisabled(selections.length == 0);
										}
										else
										{
											//alert("No tiene Permisos crear Hipotesis");
											gridTareas.down('#addButton').setDisabled(false);
										}
	                                 }
	                             }
	                         });
							 
	                        gridHipotesis = Ext.create('Ext.grid.Panel', {
								id:'gridHipotesis',
								store: storeHipotesis,
								viewConfig: { enableTextSelection: true, stripeRows:true }, 
								columnLines: true,
								columns: 
								[
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
										width: 300
									},{
										id: 'id_hipotesis',
										header: 'id_hipotesis',
										dataIndex: 'id_hipotesis',
										hidden: true,
										hideable: false
									},{
										id: 'nombre_hipotesis',
										header: 'Hipotesis',
										dataIndex: 'nombre_hipotesis',
										width: 300,
										hideable: false
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
										id: 'observacion_asignacionCaso',
										dataIndex: 'observacion_asignacionCaso',
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
										id: 'origen',
										dataIndex: 'origen',
										hidden: true,
										hideable: false
									}
								],
								selModel: selModelHipotesis,
								width: 900,
								height: 200,
								frame: true,
								title: 'Informacion de Hipotesis'
							});
	                         
	                         comboTareaStore = new Ext.data.Store({ 
	                             pageSize: 200,
	                             total: 'total',
	                             proxy: {
	                                 type: 'ajax',
	                                 url : '../administracion/soporte/admi_tarea/grid',
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
	                                     {name:'id_tarea', mapping:'id_tarea'},
	                                     {name:'nombre_tarea', mapping:'nombre_tarea'}
	                                   ]
	                         });
//
//                         // Create the combo box, attached to the states data store
	                         comboTarea = Ext.create('Ext.form.ComboBox', {
	                             id:'comboTarea',
	                             store: comboTareaStore,
	                             displayField: 'nombre_tarea',
	                             valueField: 'id_tarea',
	                             height:30,
	                             border:0,
	                             margin:0,
								fieldLabel: false,	
								queryMode: "remote",
								emptyText: ''
	                         });
	                         
	                         comboTipoStore = new Ext.data.Store({ 
	                             pageSize: 10000,
	                             total: 'total',
	                             proxy: {
	                                 type: 'ajax',
	                                 url : '../soporte/info_caso/' + 'getTipos',
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
	                                     {name:'idTipo', mapping:'idTipo'},
	                                     {name:'nombreTipo', mapping:'nombreTipo'}
	                                   ]
	                         });
							 
	                         comboTipo = Ext.create('Ext.form.ComboBox', {
	                             id:'comboTipo',
	                             store: comboTipoStore,
	                             displayField: 'nombreTipo',
	                             valueField: 'idTipo',
	                             height:30,
	                             border:0,
	                             margin:0,
								fieldLabel: false,	
								queryMode: "remote",
								emptyText: ''
	                         });
							 
	                         comboElementoStore = new Ext.data.Store({ 
	                             pageSize: 10000,
	                             total: 'total',
	                             proxy: {
	                                 type: 'ajax',
	                                 url : '../soporte/info_caso/' + 'getElementos',
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
	                                     {name:'idElemento', mapping:'idElemento'},
	                                     {name:'nombreElemento', mapping:'nombreElemento'}
	                                   ]
	                         });
							 
	                         comboElemento = Ext.create('Ext.form.ComboBox', {
	                             id:'comboElemento',
	                             store: comboElementoStore,
	                             displayField: 'nombreElemento',
	                             valueField: 'idElemento',
	                             height:30,
	                             border:0,
	                             margin:0,
								fieldLabel: false,	
								queryMode: "remote",
								emptyText: ''
	                         });
							 
	                         Ext.define('Tarea', {
	                             extend: 'Ext.data.Model',
	                             fields: [
	                                 {name:'id_sintomaTarea', type:'string'},
	                                 {name:'nombre_sintomaTarea', type:'string'},
	                                 {name:'id_hipotesisTarea', type:'string'},
	                                 {name:'nombre_hipotesisTarea', type:'string'},
	                                 {name:'id_tarea', type:'string'},
	                                 {name:'nombre_tarea', type:'string'},
	                                 {name:'tipo', type:'string'},
	                                 {name:'idTipo', type:'string'},
	                                 {name:'nombreTipo', type:'string'},
	                                 {name:'id_asignado', type:'string'},
	                                 {name:'id_refAsignado', type:'string'},
	                                 {name:'id_personaEmpresaRol', type:'string'},
	                                 {name:'observacion', type:'string'},
	                                 {name:'asunto', type:'string'},
	                                 {name:'tar_origen', type:'string'},
	                                 {name:'tar_id_empleado', type:'string'},
	                                 {name:'tar_id_departamento', type:'string'}
	                                 
	                             ]
	                         });
							 
	                         storeTareas = new Ext.data.Store({ 
	                             pageSize: 1000,
	                             total: 'total',
	                             proxy: {
	                                 type: 'ajax',
	                                 url : '../soporte/info_caso/' + 'getTareasXCaso',
	                                 reader: {
	                                     type: 'json',
	                                     totalProperty: 'total',
	                                     root: 'encontrados'
	                                 },
	                                 extraParams: {
	                                     id: id_caso,
	                                     nombre: '',
	                                     estado: 'Activos'
	                                 }
	                             },
	                             fields:
	                                       [
	                                         {name:'id_sintomaTarea', mapping:'id_sintomaTarea'},
	                                         {name:'nombre_sintomaTarea', mapping:'nombre_sintomaTarea'},
	                                         {name:'id_hipotesisTarea', mapping:'id_hipotesisTarea'},
	                                         {name:'nombre_hipotesisTarea', mapping:'nombre_hipotesisTarea'},
	                                         {name:'id_tarea', mapping:'id_tarea'},
	                                         {name:'nombre_tarea', mapping:'nombre_tarea'},
	                                         {name:'tipo', mapping:'tipo'},
	                                         {name:'idTipo', mapping:'idTipo'},
	                                         {name:'nombreTipo', mapping:'nombreTipo'},
	                                         {name:'id_asignado', mapping:'id_asignado'},
	                                         {name:'id_refAsignado', mapping:'id_refAsignado'},
	                                         {name:'id_personaEmpresaRol', mapping:'id_personaEmpresaRol'},
	                                         {name:'tar_origen', mapping:'tar_origen'},
	                                         {name:'tar_id_empleado', mapping:'tar_id_empleado'},
	                                         {name:'tar_id_departamento', mapping:'tar_id_departamento'}
	                                       ]
	                         });
							 
	                         cellEditingTareas = Ext.create('Ext.grid.plugin.CellEditing', {
	                             clicksToEdit: 1,
	                             listeners: {
	                                 edit: function(){
	                                     gridTareas.getView().refresh();
	                                 }
	                             }
	                         });
							 
	                         selModelTareas = Ext.create('Ext.selection.CheckboxModel', {
								checkOnly: true,
	                            listeners: {
	                                 selectionchange: function(sm, selections) {
										gridTareas.down('#removeButton').setDisabled(selections.length == 0);
	                                 }
	                             }
	                         });
							 
							 
							combo_tarea = new Ext.form.ComboBox({
								id:'searchTarea_cmp',
								name: 'searchTarea_cmp',
								displayField:'nombre_tarea',
								valueField: 'id_tarea',
								store: comboTareaStore,
								loadingText: 'Buscando ...',
								disabled: true,
								fieldLabel: false,	
								queryMode: "remote",
								emptyText: '',
								listClass: 'x-combo-list-small'
							});
							 
							combo_tipo = new Ext.form.ComboBox({
								id:'searchTipo_cmp',
								name: 'searchTipo_cmp',
								typeAhead: true,
								triggerAction: 'all',
								selectOnTab: true,
								store: [
									['Elemento','Elemento'],
									['Tramo','Tramo']
								],
								disabled: true,
								lazyRender: true,
								listClass: 'x-combo-list-small',
								listeners: {
									select: function(combo){
										comboTipoStore.proxy.extraParams = {tipo: combo.getValue()};
										comboTipoStore.load({params: {limit: 1000, start: 0}}); 
									}
								}
							});
							
							combo_elemento = new Ext.form.ComboBox({
								id:'searchElemento_cmp',
								name: 'searchElemento_cmp',
								displayField:'nombreTipo',
								valueField: 'idTipo',
								store: comboTipoStore,
								loadingText: 'Buscando ...',
								disabled: true,
								fieldLabel: false,	
								queryMode: "remote",
								emptyText: '',
								listClass: 'x-combo-list-small'
							});
								
								
	                         gridTareas = Ext.create('Ext.grid.Panel', {
	                             id:'gridTareas',
	                             store: storeTareas,
								 viewConfig: { enableTextSelection: true, stripeRows:true }, 
	                             columnLines: true,
	                             columns: [{
	                                 id: 'id_sintomaTarea',
	                                 header: 'SintomaId',
	                                 dataIndex: 'id_sintomaTarea',
	                                 hidden: true,
	                                 hideable: false
	                             }, {
	                                 id: 'nombre_sintomaTarea',
	                                 header: 'Sintoma',
	                                 dataIndex: 'nombre_sintomaTarea',
	                                 width: 120
	                             },
	                             {   
	                                 id: 'id_hipotesisTarea',
	                                 header: 'HipotesisId',
	                                 dataIndex: 'id_hipotesisTarea',
	                                 hidden: true,
	                                 hideable: false
	                             }, {
	                                 id: 'nombre_hipotesisTarea',
	                                 header: 'Hipotesis',
	                                 dataIndex: 'nombre_hipotesisTarea',
	                                 width: 130,
	                                 sortable: true
	                             },
	                             {   
	                                 id: 'id_tarea',
	                                 header: 'HipotesisId',
	                                 dataIndex: 'id_tarea',
	                                 hidden: true,
	                                 hideable: false
	                             }, {
	                                 id: 'nombre_tarea',
	                                 header: 'Tarea',
	                                 dataIndex: 'nombre_tarea',
	                                 width: 180,
	                                 sortable: true,
	                                 renderer: function (value, metadata, record, rowIndex, colIndex, store){
										var dataOrigen = record.data.tar_origen;
										if(dataOrigen == 'Nuevo')
										{	
											combo_tarea.setDisabled(false);
										}
										
	                                     record.data.id_tarea = record.data.nombre_tarea;
	                                     for (var i = 0;i< comboTareaStore.data.items.length;i++)
	                                     {
	                                         if (comboTareaStore.data.items[i].data.id_tarea== record.data.id_tarea)
	                                         {
	                                             gridTareas.getStore().getAt(rowIndex).data.id_sintoma=record.data.id_tarea;

	                                             record.data.id_tarea = comboTareaStore.data.items[i].data.id_tarea;
	                                             record.data.nombre_tarea = comboTareaStore.data.items[i].data.nombre_tarea;
	                                             break;
	                                         }
	                                     }
	                                     return record.data.nombre_tarea;
	                                 },
	                                 editor: combo_tarea
	                             },
	                             {
	                                 id: 'tipo',
	                                 header: 'Tipo',
	                                 dataIndex: 'tipo',
	                                 width: 120,
	                                 sortable: true,
	                                 renderer: function (value, metadata, record, rowIndex, colIndex, store){
										var dataOrigen = record.data.tar_origen;
										if(dataOrigen == 'Nuevo')
										{	
											combo_tipo.setDisabled(false);
										}
										
	                                    return record.data.tipo;
	                                 },
	                                 editor: combo_tipo	                                
	                             },
	                             {   
	                                 id: 'idTipo',
	                                 header: 'idTipo',
	                                 dataIndex: 'idTipo',
	                                 hidden: true,
	                                 hideable: false
	                             }, {
	                                 id: 'nombreTipo',
	                                 header: 'Elemento/Tramo',
	                                 dataIndex: 'nombreTipo',
	                                 width: 180,
	                                 sortable: true,
	                                 renderer: function (value, metadata, record, rowIndex, colIndex, store){
										var dataOrigen = record.data.tar_origen;
										if(dataOrigen == 'Nuevo')
										{	
											combo_elemento.setDisabled(false);
										}
										
	                                     //record.data.idTipo  = record.data.nombreTipo;
	                                     for (var i = 0;i< comboTipoStore.data.items.length;i++)
	                                     {
	                                         if (comboTipoStore.data.items[i].data.idTipo == record.data.nombreTipo)
	                                         {
	                                             gridTareas.getStore().getAt(rowIndex).data.idTipo =comboTipoStore.data.items[i].data.idTipo;
	                                             gridTareas.getStore().getAt(rowIndex).data.nombreTipo = comboTipoStore.data.items[i].data.nombreTipo;
	                                             record.data.idTipo = comboTipoStore.data.items[i].data.idTipo;
	                                             record.data.nombreTipo = comboTipoStore.data.items[i].data.nombreTipo;
	                                             break;
	                                         }
	                                     }
	                                     return record.data.nombreTipo;
	                                 },
	                                 editor: combo_elemento
	                             },
	                             {
	                                header: 'Acciones',
	                                xtype: 'actioncolumn',
	                                width:80,
	                                sortable: false,
	                                items: [{
										getClass: function(v, meta, rec) {
											var cssName = "icon-invisible";
											if(rec.get('tar_origen') == 'Nuevo')
											{
												cssName = "button-grid-asignarResponsable";
											}
											
											if (cssName == "icon-invisible") 
												this.items[0].tooltip = '';
											else 
												this.items[0].tooltip = 'Asignar Tarea';
												
											return cssName;
										},
	                                    tooltip: 'Asignar Tarea',
	                                    handler: function(grid, rowIndex, colIndex) {
	                                        agregarAsignacionXTarea(grid.getStore().getAt(rowIndex), id_caso); 
	                                    }
	                                }]
	                            },
	                             {   
	                                 id: 'id_asignado',
	                                 header: 'id_asignado',
	                                 dataIndex: 'id_asignado',
	                                 hidden: true,
	                                 hideable: false
	                             },
	                             {   
	                                 id: 'id_refAsignado',
	                                 header: 'id_refAsignado',
	                                 dataIndex: 'id_refAsignado',
	                                 hidden: true,
	                                 hideable: false
	                             },
	                             {   
	                                 id: 'id_personaEmpresaRol',
	                                 header: 'id_personaEmpresaRol',
	                                 dataIndex: 'id_personaEmpresaRol',
	                                 hidden: true,
	                                 hideable: false
	                             },
	                             {   
	                                 id: 'observacion',
	                                 header: 'observacion',
	                                 dataIndex: 'observacion',
	                                 hidden: true,
	                                 hideable: false
	                             },
	                             {   
	                                 id: 'asunto',
	                                 header: 'asunto',
	                                 dataIndex: 'asunto',
	                                 hidden: true,
	                                 hideable: false
	                             },
								{
									id: 'tar_origen',
									dataIndex: 'tar_origen',
									hidden: true,
									hideable: false
								},
								{
									id: 'tar_id_empleado',
									dataIndex: 'tar_id_empleado',
									hidden: true,
									hideable: false
								},
								{
									id: 'tar_id_departamento',
									dataIndex: 'tar_id_departamento',
									hidden: true,
									hideable: false
								}
							 ],
	                         dockedItems: [{
	                                 xtype: 'toolbar',
	                                 items: [{
	                                         itemId: 'removeButton',
	                                         text:'Eliminar',
	                                         tooltip:'Elimina el item seleccionado',
	                                         disabled: true,
	                                         handler : function(){eliminarSeleccion(gridTareas, 'gridTareas', selModelTareas);}
	                                     }, '-', {
	                                         itemId: 'addButton',	
	                                         text:'Agregar',
	                                         tooltip:'Agrega un item a la lista',
	                                         disabled: false,
	                                         handler : function(){
												if((flagBoolAsignado && flagAsignado) || empresa == "TN")
												{
													if(selModelHipotesis.getSelection().length > 0)
													{
														if(selModelHipotesis.getSelection().length > 1){
															Ext.Msg.alert('Alerta','Solo debe seleccionar una hipotesis a la vez.' );
															return ;
														}
														
														for(var i=0 ;  i < selModelHipotesis.getSelection().length ; ++i)
														{
															sintoma_id = selModelHipotesis.getSelection()[i].data.id_sintoma;
															sintoma_nombre = selModelHipotesis.getSelection()[i].data.nombre_sintoma;
															hipotesis_id = selModelHipotesis.getSelection()[i].data.id_hipotesis;
															hipotesis_nombre = selModelHipotesis.getSelection()[i].data.nombre_hipotesis;
															id_empleado = selModelHipotesis.getSelection()[i].data.empleado_asignacionCaso;
															id_departamento = selModelHipotesis.getSelection()[i].data.departamento_asignacionCaso;
														} 
														/*
														if(global_id_empleado == id_empleado)
														{*/
															if(valorIdDepartamento == id_departamento || empresa == "TN")
															{
																var r = Ext.create('Tarea', {
																	 id_sintomaTarea: sintoma_id,
																	 nombre_sintomaTarea: sintoma_nombre,
																	 id_hipotesisTarea: hipotesis_id,
																	 nombre_hipotesisTarea: hipotesis_nombre,
																	 id_tarea: '',
																	 nombre_tarea: '',
																	 tipo: '',
																	 idTipo: '',
																	 nombreTipo: '',
																	 id_asignado: '',
																	 id_refAsignado: '',
																	 id_personaEmpresaRol: '',
																	 observacion: '',
																	 asunto: '',
																	 tar_origen: 'Nuevo',
																	 tar_id_empleado: id_empleado,
																	 tar_id_departamento: id_departamento
																});
																storeTareas.insert(0, r);	
															}
															else
															{
																Ext.Msg.alert('Alerta ', "Esta hipotesis esta asignada a otro departamento, no puede ingresar tareas.");
															}	
														/*}
														else
														{
															Ext.Msg.alert('Alerta ', "Esta hipotesis esta asignada a otra persona, no puede ingresar tareas.");
														}*/														
													}else{
														Ext.Msg.alert('Alerta','Debe escoger una hipotesis para ingresar las tareas correspondientes.');
														return ;
													}
												} 
												else
												{
													Ext.Msg.alert('Alerta ', "No tiene permisos para crear Tareas, porque el caso fue asignado a otra persona");
												}
	                                         }
	                                     }]
	                                 }],
	                             selModel: selModelTareas,
	                             width: 900,
	                             height: 200,
	                             frame: true,
	                             plugins: [cellEditingTareas],
	                             title: 'Ingresar Informacion de Tareas'
	                         });
							 
	                         formPanel = Ext.create('Ext.form.Panel', {
	                                 bodyPadding: 5,
	                                 waitMsgTarget: true,
	                                 height: 200,
	                                 layout: 'fit',
	                                 fieldDefaults: {
	                                     labelAlign: 'left',
	                                     labelWidth: 140,
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
	                                             id: 'numero_casoSintoma',
	                                             name: 'numero_casoSintoma',
	                                             value: numero
	                                         },
	                                         {
	                                             xtype: 'displayfield',
	                                             fieldLabel: 'Fecha apertura:',
	                                             id: 'fechacaso',
	                                             name: 'fechaCaso',
	                                             value: fecha+" "+hora
	                                         },
	                                         {
	                                             xtype: 'textarea',
	                                             fieldLabel: 'Version Inicial:',
	                                             id: 'version_inicialSintoma',
	                                             name: 'version_inicialSintoma',
	                                             rows: 3,
	                                             cols: 100,
	                                             value: version_inicial
	                                         },
	                                         gridHipotesis,
	                                         gridTareas
	                                     ]
	                                 }]
	                              });
								  
	                         winTareas = Ext.create('Ext.window.Window', {
	                                 title: 'Agregar Tareas',
	                                 modal: true,
	                                 width: 960,
	                                 height: 650,
	                                 resizable: false,
	                                 layout: 'fit',
									 closabled: false,
	                                 items: [formPanel],
	                                 buttonAlign: 'center',
	                                 buttons:[btnguardar,btncancelar]
	                         }).show(); 
	                    }else{
	                        Ext.Msg.alert("Alerta","Debe ingresar por lo menos una hipotesis para ingresar tareas al caso.");
	                    }
						
						Ext.MessageBox.hide();
	                }, this);
	            }
	        }
	    });
	
	}    
}

function presentarAreas(id, name, obj)
{
    storeAreas.proxy.extraParams = {id_param: id};
    storeAreas.load(); 
//    Ext.getCmp("comboDepartamentos").reset();
//    Ext.getCmp("comboAreas").reset();
//    Ext.getCmp("comboEmpleados").reset();
}

function presentarDepartamentos(id, name, obj, oficina)
{
    storeDepartamentos.proxy.extraParams = {id_param: id, id_oficina: oficina};
    storeDepartamentos.load(); 
//    Ext.getCmp("comboAreas").setRawValue("");
//    Ext.getCmp("comboEmpleados").setRawValue("");
}

function presentarEmpleados(id, name, obj, oficina, id_caso)
{	
    storeEmpleados.proxy.extraParams = {id_param: id, id_oficina: oficina, id_caso: id_caso};
    storeEmpleados.load(); 
}

function presentarEmpleadosJefes(id, name, obj, oficina)
{	
    storeEmpleados.proxy.extraParams = {id_param: id, id_oficina: oficina, soloJefes: 'S'};
    storeEmpleados.load(); 
}

function agregarAsignacionXTarea(tarea, id_caso){
	var globalComboEmpresa = $('#globalEmpresaEscogida').val();													
	var arrayValores = globalComboEmpresa.split('@@');
	var valorIdOficina = ''; var valorOficina = '';
	var valorIdDepartamento = ''; var valorDepartamento = '';
	if(arrayValores && arrayValores.length > 3)
	{
		valorIdOficina = arrayValores[2];
		valorOficina = arrayValores[3];
		valorIdDepartamento = arrayValores[4];
		valorDepartamento = arrayValores[5];
	}
//    if(tarea.data.nombre_tarea=='')
//    {
//        Ext.Msg.alert('Alerta','Debe escoger una tarea primero.');
//        return;
//    }
//    if(tarea.data.nombreElemento==''&&tarea.nombre_tramo=='')
//    {
//        Ext.Msg.alert('Alerta','Debe escoger un tramo o un elemento primero.');
//        return;
//    }
/*
    storeFiliales = new Ext.data.Store({ 
        total: 'total',
        autoLoad: true,
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
    
    storeAreas = new Ext.data.Store({ 
        total: 'total',
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: 'getAreas',
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
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: 'getDepartamentos',
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
    });*/
    storeEmpleados = new Ext.data.Store({ 
        total: 'total',
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: '../soporte/info_caso/' + 'getEmpleadosXDepartamento',
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
				metaProperty: 'myMetaData'
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
							Ext.Msg.alert('Alerta ', "No existen empleados asignados para este departamento.");
						}
					});
				}
			}
		}
    });
	presentarEmpleados(valorIdDepartamento, '', '', valorIdOficina, id_caso);

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
					/*{
						xtype: 'combobox',
						fieldLabel: 'Filial',
						id: 'comboFilial',
						name: 'comboFilial',
						store: storeFiliales,
						displayField: 'nombre_oficina',
						valueField: 'id_oficina_grupo',
						listeners: {
							select: function(combo){							
								Ext.getCmp('comboArea').reset();	
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleado').reset();
								
								presentarAreas(combo.getValue(),combo.getRawValue(),this);
							}
						},
						emptyText: '',
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
						listeners: {
							select: function(combo){
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleado').reset();
								
								var oficina = Ext.getCmp('comboFilial').value;						
								presentarDepartamentos(combo.getValue(), combo.getRawValue(), this, oficina);
							}
						},
						emptyText: '',
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
						listeners: {
							select: function(combo){
								Ext.getCmp('comboEmpleado').reset();
								
								var oficina = Ext.getCmp('comboFilial').value;
								presentarEmpleados(combo.getValue(), combo.getRawValue(), this, oficina);
							}
						},
						emptyText: '',
						allowBlank: false,
						forceSelection: true
					},*/
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Empleado',
                        id:'comboEmpleado',
                        name:'comboEmpleado',
                        store: storeEmpleados,
                        displayField: 'nombre_empleado',
                        valueField: 'id_empleado',
                        emptyText: '',
						queryMode: "remote"
                    },                        
                    {
                      xtype: 'textarea',
                      id: 'observacionAsignacion',
                      fieldLabel: 'Observacion',
                      name: 'observacion',
                      rows: 3,
                      allowBlank: false
                    },
					{
                      xtype: 'textfield',
                      id: 'asuntoAsignacion',
                      fieldLabel: 'Asunto del correo',
                      name: 'asunto',
                      width: 'auto',
                      allowBlank: true
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
					if(Ext.getCmp('comboEmpleado') && Ext.getCmp('comboEmpleado').value)
					{		
						var comboEmpleado = Ext.getCmp('comboEmpleado').value;
						var valoresComboEmpleado = comboEmpleado.split("@@"); 
						var idEmpleado = valoresComboEmpleado[0];
						var idPersonaEmpresaRol = valoresComboEmpleado[1];
						
						var array_data = new Array();
						tarea.data.id_asignado = valorIdDepartamento; //Ext.getCmp('comboDepartamento').value;
						tarea.data.id_refAsignado = idEmpleado;
						tarea.data.id_personaEmpresaRol = idPersonaEmpresaRol;
					   // tarea.data.id_refAsignado = Ext.getCmp('comboEmpleado').value;
						tarea.data.observacion = Ext.getCmp('observacionAsignacion').value;
						tarea.data.asunto = Ext.getCmp('asuntoAsignacion').value;
						winAgregarAsignacionTarea.destroy();
					}
					else
					{
						Ext.Msg.alert('Alerta ', 'Por favor escoja el empleado');
					}
                }
            },
			{
                text: 'Cancelar',
                handler: function(){
                    winAgregarAsignacionTarea.destroy();
                }
            }
		]
	});

	winAgregarAsignacionTarea = Ext.create('Ext.window.Window', {
		title: 'Asignar Tarea',
		modal: true,
		closable: false,
		width: 650,
		layout: 'fit',
		items: [formPanel]
	}).show();
}



function administrarTareas(rec){    
	var id_caso = rec.get('id_caso');
	var numero = rec.get('numero_caso');
	var fecha = rec.get('fecha_apertura');
	var hora = rec.get('hora_apertura');
	var version_inicial = rec.get('version_ini');
	
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
    btnguardar = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                json_tareas = obtenerTareas();
                
                conn.request({
                    method: 'POST',
                    params :{
                        id_caso: id_caso,
                        tareas: json_tareas
                    },
                    url: '../soporte/info_caso/' + 'actualizarTareas',
                    success: function(response){
                        Ext.Msg.alert('Mensaje','Se actualizaron las tareas.', function(btn){
                            if(btn=='ok'){
								winAdministrarTareas.destroy();
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
    });
    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winAdministrarTareas.destroy();	
				storeSoporte.load();
            }
    });
    
				
	Ext.define('Tarea', {
		extend: 'Ext.data.Model',
		fields: [
			{name:'id_sintomaTarea', type:'string'},
			{name:'nombre_sintomaTarea', type:'string'},
			{name:'id_hipotesisTarea', type:'string'},
			{name:'nombre_hipotesisTarea', type:'string'},
			{name:'id_tarea', type:'string'},
			{name:'nombre_tarea', type:'string'},
			{name:'tipo', type:'string'},
			{name:'idTipo', type:'string'},
			{name:'nombreTipo', type:'string'},
			{name:'id_asignado', type:'string'},
			{name:'id_refAsignado', type:'string'},
			{name:'observacion', type:'string'},
			{name:'estado', type:'string'},
			{name:'action1', type:'string'},
			{name:'action2', type:'string'}

		]
	});
	storeTareas = new Ext.data.Store({ 
		pageSize: 1000,
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : '../soporte/info_caso/' + 'getTareasXCaso',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				id: id_caso,
				nombre: '',
				estado: 'Activos'
			}
		},
		fields:
				  [
					{name:'id_sintomaTarea', mapping:'id_sintomaTarea'},
					{name:'nombre_sintomaTarea', mapping:'nombre_sintomaTarea'},
					{name:'id_hipotesisTarea', mapping:'id_hipotesisTarea'},
					{name:'nombre_hipotesisTarea', mapping:'nombre_hipotesisTarea'},
					{name:'id_tarea', mapping:'id_tarea'},
					{name:'nombre_tarea', mapping:'nombre_tarea'},
					{name:'tipo', mapping:'tipo'},
					{name:'idTipo', mapping:'idTipo'},
					{name:'nombreTipo', mapping:'nombreTipo'},
					{name:'id_asignado', mapping:'id_asignado'},
					{name:'id_refAsignado', mapping:'id_refAsignado'},
					{name:'observacion', mapping:'observacion'},
					{name:'estado', mapping:'estado'},
					{name:'action0', mapping:'action0'},
					{name:'action1', mapping:'action1'},
					{name:'action2', mapping:'action2'},
					{name:'action3', mapping:'action3'},
                    {name:'action6', mapping:'action6'}
				  ]
	});
	cellEditingTareas = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 1,
		listeners: {
			edit: function(){
				gridTareas.getView().refresh();
			}
		}
	});
	selModelTareas = Ext.create('Ext.selection.CheckboxModel', {
	   listeners: {
			selectionchange: function(sm, selections) {

			}
		}
	})
	gridTareas = Ext.create('Ext.grid.Panel', {
		id:'gridTareas',
		store: storeTareas,
		viewConfig: { enableTextSelection: true, stripeRows:true }, 
		columnLines: true,
		columns: [{
			id: 'id_sintomaTarea',
			header: 'SintomaId',
			dataIndex: 'id_sintomaTarea',
			hidden: true,
			hideable: false
		}, {
			id: 'nombre_sintomaTarea',
			header: 'Sintoma',
			dataIndex: 'nombre_sintomaTarea',
			width: 180
		},
		{   
			id: 'id_hipotesisTarea',
			header: 'HipotesisId',
			dataIndex: 'id_hipotesisTarea',
			hidden: true,
			hideable: false
		}, {
			id: 'nombre_hipotesisTarea',
			header: 'Hipotesis',
			dataIndex: 'nombre_hipotesisTarea',
			width: 180,
			sortable: true
		},
		{   
			id: 'id_tarea',
			header: 'HipotesisId',
			dataIndex: 'id_tarea',
			hidden: true,
			hideable: false
		}, {
			id: 'nombre_tarea',
			header: 'Tarea',
			dataIndex: 'nombre_tarea',
			width: 180,
			sortable: true
		},
		{   
			id: 'tipo',
			header: 'Tipo',
			dataIndex: 'tipo',
			hideable: false
		}, {
			id: 'nombreTipo',
			header: 'Elemento/Tramo',
			dataIndex: 'nombreTipo',
			width: 130,
			sortable: true
		},
		{   
			id: 'idTipo',
			header: 'idTipo',
			dataIndex: 'idTipo',
			hidden: true,
			hideable: false
		},{
			id: 'estado',
			header: 'Estado',
			dataIndex: 'estado',
			width: 80,
			sortable: true
		},
		{
			header: 'Acciones',
			xtype: 'actioncolumn',
			width:100,
			sortable: false,
			items: 
			[
				{
					getClass: function(v, meta, rec) {
						var permiso = $("#ROLE_78-50");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
						if(!boolPermiso){ rec.data.action0 = "icon-invisible"; }
							
						if (rec.get('action0') == "icon-invisible") 
							this.items[0].tooltip = '';
						else 
							this.items[0].tooltip = 'Ver Asignado';
						
						return rec.get('action0');
					},
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = $("#ROLE_78-50");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
						if(!boolPermiso){ rec.data.action0 = "icon-invisible"; }
							
						if(rec.get('action0')!="icon-invisible")
							verAsignadoTarea(id_caso,numero,grid.getStore().getAt(rowIndex).data); 
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				},
				{
					getClass: function(v, meta, rec) {
						var permiso = $("#ROLE_78-157");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
						if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
							
						if (rec.get('action1') == "icon-invisible") 
							this.items[1].tooltip = '';
						else 
							this.items[1].tooltip = 'Ingresar Seguimiento';
						
						return rec.get('action1');
					},
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = $("#ROLE_78-157");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
						if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
							
						if(rec.get('action1')!="icon-invisible")
							agregarSeguimiento(id_caso,numero,grid.getStore().getAt(rowIndex).data); 
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				},
				{
					getClass: function(v, meta, rec) {
						var permiso = $("#ROLE_78-38");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
						if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
							
						if (rec.get('action2') == "icon-invisible") 
							this.items[2].tooltip = '';
						else 
							this.items[2].tooltip = 'Finalizar Tarea';
						
						return rec.get('action2');
					},
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = $("#ROLE_78-38");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
						if(!boolPermiso){ rec.data.action2 = "icon-invisible"; }
							
						if(rec.get('action2')!="icon-invisible")
							finalizarTarea(id_caso,numero,grid.getStore().getAt(rowIndex).data); 
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				},
				{
					getClass: function(v, meta, rec) {
						var permiso = $("#ROLE_78-156");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
						if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
							
						if (rec.get('action3') == "icon-invisible") 
							this.items[3].tooltip = '';
						else 
							this.items[3].tooltip = 'Aceptar/Rechazar Tarea';
						
						return rec.get('action3');
					},
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = $("#ROLE_78-156");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
						if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
							
						if(rec.get('action3')!="icon-invisible")
							aceptarRechazarTarea(grid.getStore().getAt(rowIndex).data); 
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				}
			]
		},
		{   
			id: 'id_asignado',
			header: 'id_asignado',
			dataIndex: 'id_asignado',
			hidden: true,
			hideable: false
		},
		{   
			id: 'id_refAsignado',
			header: 'id_refAsignado',
			dataIndex: 'id_refAsignado',
			hidden: true,
			hideable: false
		},
		{   
			id: 'observacion',
			header: 'observacion',
			dataIndex: 'observacion',
			hidden: true,
			hideable: false
		},
		{   
			id: 'asunto',
			header: 'asunto',
			dataIndex: 'asunto',
			hidden: true,
			hideable: false
		}],
		selModel: selModelTareas,
		width: 1010,
		height: 370,
		frame: true,
		plugins: [cellEditingTareas],
		title: 'Tareas Agregadas'
	});
	formPanel = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 200,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				labelWidth: 140,
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
						id: 'numero_casoSintoma',
						name: 'numero_casoSintoma',
						value: numero
					},
					{
						xtype: 'displayfield',
						fieldLabel: 'Fecha apertura:',
						id: 'fechacaso',
						name: 'fechaCaso',
						value: fecha+" "+hora
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Version Inicial:',
						id: 'version_inicialSintoma',
						name: 'version_inicialSintoma',
						rows: 3,
						cols: 100,
						readOnly: true,
						value: version_inicial
					},
					gridTareas
				]
			}]
		 });
	winAdministrarTareas = Ext.create('Ext.window.Window', {
			title: 'Administrar Tareas',
			modal: true,
			width: 1060,
			height: 620,
			resizable: false,
			layout: 'fit',
			items: [formPanel],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show(); 

}

function verAsignadoTarea(id_caso,numero,data){
    winAsignadoTarea = "";
	var formPanel2 = "";
	
    if (winAsignadoTarea)
    {
		cierraVentanaByIden(winAsignadoTarea);
		winAsignadoTarea = "";
	}
	
    if (!winAsignadoTarea)
    {     
	    btncancelar2 = Ext.create('Ext.Button', {
	            text: 'Cerrar',
	            cls: 'x-btn-rigth',
	            handler: function() {
					cierraVentanaByIden(winAsignadoTarea);
	            }
	    });
		
	    storeAsignadoTarea = new Ext.data.Store({ 
	        total: 'total',
	        autoLoad:true,
	        proxy: {
	            type: 'ajax',
	            url : '../soporte/info_caso/' + 'getTareaAsignado',
	            reader: {
	                type: 'json',
	                totalProperty: 'total',
	                root: 'encontrados'
	            },
	            extraParams: {
	                id_detalle: data.id_sintomaTarea
	            }
	        },
	        fields:
	              [
	                {name:'oficina', mapping:'oficina'},
	                {name:'area', mapping:'area'},
	                {name:'departamento', mapping:'departamento'},
	                {name:'empleado', mapping:'empleado'}
	              ],
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
				   
					winAsignadoTarea = "";
					formPanel2 = "";
				},
	            load: function(sender, node, records) {
	               // console.log(store.data.items[0].data.oficina);
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
	                        title: 'Información de Asignacion',
	                        defaultType: 'textfield',
	                        items: [
	                            {
	                                xtype: 'displayfield',
	                                fieldLabel: 'Oficina:',
	                                id: 'tareaOficina',
	                                name: 'tareaOficina',
	                                value: storeAsignadoTarea.data.items[0].data.oficina
	                            },{
	                                xtype: 'displayfield',
	                                fieldLabel: 'Area:',
	                                id: 'tareaArea',
	                                name: 'tareaArea',
	                                value: storeAsignadoTarea.data.items[0].data.area
	                            },{
	                                xtype: 'displayfield',
	                                fieldLabel: 'Departamento:',
	                                id: 'tareaDepartamento',
	                                name: 'tareaDepartamento',
	                                value: storeAsignadoTarea.data.items[0].data.departamento
	                            },
	                            {
	                                xtype: 'displayfield',
	                                fieldLabel: 'Empleado:',
	                                id: 'tareaEmpleado',
	                                name: 'tareaEmpleado',
	                                value: storeAsignadoTarea.data.items[0].data.empleado
	                            }
	                        ]
	                    }]
					});
					
		            winAsignadoTarea = Ext.create('Ext.window.Window', {
		                    title: 'Ver asignado de la tarea',
		                    modal: true,
		                    width: 660,
		                    height: 240,
		                    resizable: false,
		                    layout: 'fit',
							closabled: false,
		                    items: [formPanel2],
		                    buttonAlign: 'center',
		                    buttons:[btncancelar2]
		            }).show(); 
					
					Ext.MessageBox.hide();
	            }
	        }
	    });
    }
}

function agregarSeguimiento(id_caso,numero,data){
    
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
    btnguardar2 = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                json_tareas = obtenerTareas();
                
                conn.request({
                    method: 'POST',
                    params :{
                        id_caso: id_caso,
                        id_detalle: data.id_sintomaTarea,
                        seguimiento: Ext.getCmp('seguimiento').value
                    },
                    url: '../soporte/info_caso/' + 'ingresarSeguimiento',
                    success: function(response){
                        Ext.Msg.alert('Mensaje','Se ingreso el seguimiento.', function(btn){
                            if(btn=='ok'){
                                    winSeguimiento.destroy();
                            }
                        });
                    },
                    failure: function(rec, op) {
                        var json = Ext.JSON.decode(op.response.responseText);
                        Ext.Msg.alert('Alerta ',json.mensaje);
                    }
            });
            }
    });
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
                        fieldLabel: 'Caso:',
                        id: 'seguimientoCaso',
                        name: 'seguimientoCaso',
                        value: numero
                    },{
                        xtype: 'displayfield',
                        fieldLabel: 'Tarea:',
                        id: 'tareaCaso',
                        name: 'tareaCaso',
                        value: data.nombre_tarea
                    },{
                        xtype: 'displayfield',
                        fieldLabel: 'Elemento/Tramo:',
                        id: 'elemento/tramo',
                        name: 'elemento/tramo',
                        value: data.nombreTipo
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Seguimiento:',
                        id: 'seguimiento',
                        name: 'seguimiento',
                        rows: 7,
                        cols: 70
                    }
                ]
            }]
         });
    winSeguimiento = Ext.create('Ext.window.Window', {
            title: 'Ingresar Seguimiento',
            modal: true,
            width: 660,
            height: 340,
            resizable: false,
            layout: 'fit',
            items: [formPanel2],
            buttonAlign: 'center',
            buttons:[btnguardar2,btncancelar2]
    }).show(); 
    
}

function validarTareasMateriales()
{
	//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN HIPOTESIS ANTERIOR... ANTES DE CREAR OTRO..
	var storeValida = Ext.getCmp("gridMaterialTareas").getStore();
	var boolSigue = false;
	var boolSigue2 = false;
	
	if(storeValida.getCount() > 0)
	{
		var boolSigue_vacio = true;
		var boolSigue_igual = true;
		
		for(var i = 0; i < storeValida.getCount(); i++)
		{
			var id_material = storeValida.getAt(i).data.id_material;
			var nombre_material = storeValida.getAt(i).data.nombre_material;
			var cod_material = storeValida.getAt(i).data.cod_material;
			
			if(id_material != "" && nombre_material != ""){ /*NADA*/  }
			else {  boolSigue_vacio = false; }

			if(i>0)
			{
				for(var j = 0; j < i; j++)
				{
					if(i != j)
					{
						var id_material_valida = storeValida.getAt(j).data.id_material;
						var nombre_material_valida = storeValida.getAt(j).data.nombre_material;
						var cod_material_valida = storeValida.getAt(j).data.cod_material;
						
						if(id_material_valida == id_material && nombre_material == nombre_material_valida)
						{
							boolSigue_igual = false;	
						}
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
		Ext.Msg.alert('Alerta ', "Debe escoger un material del combo, antes de solicitar un nuevo material");
		return false;
	}
	else if(!boolSigue2)
	{
		Ext.Msg.alert('Alerta ', "No puede ingresar el mismo material! Debe modificar el registro repetido, antes de solicitar un nuevo material");
		return false;
	}
	else
	{
		Ext.Msg.alert('Alerta ', "Debe completar datos de los materiales a ingresar, antes de solicitar un nuevo material");
		return false;
	}	
}

function finalizarTarea(id_caso,numero,data){
    
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
    btnguardar2 = Ext.create('Ext.Button', {
		text: 'Guardar',
		cls: 'x-btn-rigth',
		handler: function() {	
			var valorBool = validarTareasMateriales();
			if(valorBool)
			{
				json_materiales = obtenerMateriales();
				
				var radio1 = Ext.getCmp('radio1').getValue();
				var radio2 = Ext.getCmp('radio2').getValue();
				
				conn.request({
					method: 'POST',
					params :{
						id_caso: id_caso,
						id_detalle: data.id_sintomaTarea,
						es_solucion: Ext.getCmp('radio1').getValue(),
						materiales: json_materiales
					},
					url: '../soporte/info_caso/' + 'finalizarTarea',
					success: function(response){
						var json = Ext.JSON.decode(response.responseText);
						if(json.success)
						{
							Ext.Msg.alert('Mensaje','Se finalizo la tarea.', function(btn){
								if(btn=='ok'){
									winFinalizarTarea.destroy();
									storeTareas.load();
								}
							});
						}
						else
						{
							Ext.Msg.alert('Alerta ',json.mensaje);
						}
					},
					failure: function(rec, op) {
						var json = Ext.JSON.decode(op.response.responseText);
						Ext.Msg.alert('Alerta ',json.mensaje);
					}
				});
			}	
		}
    });
    btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winFinalizarTarea.destroy();
            }
    });
    
                        
    Ext.define('Material', {
        extend: 'Ext.data.Model',
        fields: 
		[
            {name:'id_detalle', type:'string'},
            {name:'id_tarea', type:'string'},
            {name:'id_material', type:'string'},
            {name:'cod_material', type:'string'},
            {name:'nombre_tarea', type:'string'},
            {name:'nombre_material', type:'string'},
            {name:'costo', type:'string'},
            {name:'precio_venta_material', type:'string'},
            {name:'cant_nf', type:'string'},
            {name:'cant_f', type:'string'},
            {name:'cant', type:'string'},
            {name:'valor', type:'string'},
            {name:'fin_origen', type:'string'}

        ]
    });
	
    storeMaterialTareas = new Ext.data.Store({ 
        pageSize: 1000,
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : '../soporte/info_caso/' + 'getMaterialesByTarea',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
				id_detalle: data.id_sintomaTarea
            }
        },
        fields:
		[
			{name:'id_detalle', mapping:'id_material'},
			{name:'id_tarea', mapping:'id_tarea'},
			{name:'id_material', mapping:'id_material'},
			{name:'cod_material', mapping:'cod_material'},
			{name:'nombre_tarea', mapping:'nombre_tarea'},
			{name:'nombre_material', mapping:'nombre_material'},
			{name:'costo', mapping:'costo'},
			{name:'precio_venta_material', mapping:'precio_venta_material'},
			{name:'cant_nf', mapping:'cant_nf'},
			{name:'cant_f', mapping:'cant_f'},
			{name:'cant', mapping:'cant'},
			{name:'valor', mapping:'valor'},
			{name:'fin_origen', mapping:'fin_origen'}
		]
    });
	
    cellEditingMaterialTareas = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            edit: function(){
                gridMaterialTareas.getView().refresh();
            }
        }
    });
    selModelMaterialTareas = Ext.create('Ext.selection.CheckboxModel', {
		checkOnly: true,
		listeners: {
			 selectionchange: function(sm, selections) {
				gridMaterialTareas.down('#removeButton').setDisabled(selections.length == 0);
			 }
		 }
    })
    comboMaterialStore = new Ext.data.Store({ 
		total: 'total',
		proxy: {
			type: 'ajax',
			url : '../soporte/info_caso/' + 'getMateriales',
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
				{name:'id_material', mapping:'id_material'},
				{name:'cod_material', mapping:'cod_material'},
				{name:'nombre_material', mapping:'nombre_material'},
				{name:'costo_material', mapping:'costo_material'},
				{name:'cant_material', mapping:'cant_material'}
			  ]
	});

	comboMaterial = Ext.create('Ext.form.ComboBox', {
		id:'comboMaterial',
		store: comboMaterialStore,
		displayField: 'nombre_material',
		valueField: 'id_material',
		height:30,
		border:0,
		margin:0,
		fieldLabel: false,	
		queryMode: "remote",
		emptyText: ''
	});
    
	gridMaterialTareas = Ext.create('Ext.grid.Panel', {
        id:'gridMaterialTareas',
        store: storeMaterialTareas,
		viewConfig: { enableTextSelection: true, stripeRows:true }, 
        columnLines: true,
        columns: [
			{
				id: 'id_material',
				header: 'id_material',
				dataIndex: 'id_material',
				hidden: true,
				hideable: false
			},
			{
				id: 'precio_venta_material',
				header: 'precio_venta_material',
				dataIndex: 'precio_venta_material',
				hidden: true,
				hideable: false
			},
			{
				id: 'fin_origen',
				header: 'fin_origen',
				dataIndex: 'fin_origen',
				hidden: true,
				hideable: false
			},
			{
				id: 'cod_material',
				header: 'Codido Material',
				dataIndex: 'cod_material',
				width: 100
			},
			{
				id: 'nombre_material',
				header: 'Nombre Material',
				dataIndex: 'nombre_material',
				width: 260,
				sortable: true,
				renderer: function (value, metadata, record, rowIndex, colIndex, store){
					record.data.id_material = record.data.nombre_material;
					for (var i = 0;i< comboMaterialStore.data.items.length;i++)
					{
						if (comboMaterialStore.data.items[i].data.id_material== record.data.id_material)
						{
							gridMaterialTareas.getStore().getAt(rowIndex).data.costo = comboMaterialStore.data.items[i].data.costo_material;
							gridMaterialTareas.getStore().getAt(rowIndex).data.cant = comboMaterialStore.data.items[i].data.cant_material;
							gridMaterialTareas.getStore().getAt(rowIndex).data.id_material=comboMaterialStore.data.items[i].data.id_material;

							record.data.id_material = comboMaterialStore.data.items[i].data.id_material;
							record.data.cod_material = comboMaterialStore.data.items[i].data.cod_material;
							record.data.nombre_material = comboMaterialStore.data.items[i].data.nombre_material;
							break;
						}
					}
					
					//record.commit();
					//gridMaterialTareas.getView().refresh();
					
					return record.data.nombre_material;
				},
				editor: {
					id:'searchMaterial_cmp',
					xtype: 'combobox',
					displayField:'nombre_material',
					valueField: 'id_material',
					loadingText: 'Buscando ...',
					store: comboMaterialStore,
					fieldLabel: false,	
					queryMode: "remote",
					emptyText: '',
					listClass: 'x-combo-list-small'
				}
			},
			{   
				id: 'costo',
				header: 'Costo',
				width: 60,
				dataIndex: 'costo',
				hideable: false
			}, {
				id: 'cant_nf',
				header: 'Cant. no Fact.',
				dataIndex: 'cant_nf',
				width: 90,
				sortable: true,
				editor: {
					xtype: 'numberfield'
				}
	//            renderer: function (value, metadata, record, rowIndex, colIndex, store){
	//                alert(gridMaterialTareas.getStore().getAt(rowIndex).data.cant+"-"+record.data.cant_nf);
	//                if(gridMaterialTareas.getStore().getAt(rowIndex).data.cant<record.data.cant_nf){
	//                    Ext.Msg.alert("Alerta","La cantidad ingresada es mayor que la cantidad maxima permitida.");
	//                    return;
	//                }
	//                    
	//                
	//                return record.data.cant_nf;
	//            }
			},
			{   
				id: 'cant_f',
				header: 'Cant. Fact.',
				dataIndex: 'cant_f',
				width: 80,
				hideable: false,
				editor: {
					xtype: 'numberfield'
				},
				renderer: function (value, metadata, record, rowIndex, colIndex, store){
					gridMaterialTareas.getStore().getAt(rowIndex).data.valor = gridMaterialTareas.getStore().getAt(rowIndex).data.costo*gridMaterialTareas.getStore().getAt(rowIndex).data.cant_f;
	//                if(gridMaterialTareas.getStore().getAt(rowIndex).data.cant==""){
	//                    Ext.Msg.alert("Alerta","Debe escoger primero un material.");
	//                    return;
	//                }
					return record.data.cant_f;
				}
			},
			{   
				id: 'cant',
				header: 'Cantidad Max. Fact. ',
				dataIndex: 'cant',            
				width: 120,
				hideable: false
			}, {
				id: 'valor',
				header: 'Valor',
				dataIndex: 'valor',
				width: 70,
				sortable: true
			}
		],
        selModel: selModelMaterialTareas,
        dockedItems: [{
            xtype: 'toolbar',
            items: [
				{
					itemId: 'removeButton',
					text:'Eliminar',
					tooltip:'Elimina el item seleccionado',
					disabled: true,
					handler : function(){eliminarSeleccion(gridMaterialTareas, 'gridMaterialTareas', selModelMaterialTareas);}
				}, '-', 
				{
					text:'Agregar',
					tooltip:'Agrega un item a la lista',
					handler : function(){
						var boolValida = validarTareasMateriales();
						if(boolValida)
						{
							// Create a model instance
							var r = Ext.create('Material', {
								id_detalle: '',
								id_tarea: '',
								id_material: '',
								cod_material: '',
								nombre_tarea: '',
								nombre_material: '',
								costo: '',
								precio_venta_material: '',
								cant_nf: 0,
								cant_f: 0,
								cant: '',
								valor: '',
								fin_origen: 'Nuevo'
							});
							storeMaterialTareas.insert(0, r);
						}
					}
				}
			]
        }],
        width: 840,
        height: 350,
        frame: false,
        plugins: [cellEditingMaterialTareas],
        title: 'Materiales'
    });        

    formPanel2 = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 600,
		width: 900,
		layout: 'fit',
		fieldDefaults: {
			labelAlign: 'left',
			width: 300,
			msgTarget: 'side'
		},

		items: [{
			xtype: 'fieldset',
			title: 'Información',
			defaultType: 'textfield',
			items: [
				{
					xtype: 'displayfield',
					fieldLabel: 'Caso:',
					id: 'seguimientoCaso',
					name: 'seguimientoCaso',
					value: numero
				},{
					xtype: 'displayfield',
					fieldLabel: 'Tarea:',
					id: 'tareaCaso',
					name: 'tareaCaso',
					value: data.nombre_tarea
				},{
					xtype: 'displayfield',
					fieldLabel: 'Elemento/Tramo:',
					id: 'elemento/tramo',
					name: 'elemento/tramo',
					value: data.nombreTipo
				},{
					xtype      : 'fieldcontainer',
					fieldLabel : 'Es Solucion',
					defaultType: 'radiofield',
					defaults: {
						flex: 1
					},
					layout: 'hbox',
					items: [
						{
							boxLabel  : 'Si',
							name      : 'esSolucion',
							inputValue: 'S',
							id        : 'radio1'
						}, {
							boxLabel  : 'No',
							name      : 'esSolucion',
							inputValue: 'N',
							id        : 'radio2'
						}
					]
				},gridMaterialTareas
			]
		}]
	});
		 
    Ext.getCmp('radio1').setValue(true);  
    
	winFinalizarTarea = Ext.create('Ext.window.Window', {
		title: 'Finalizar Tarea',
		modal: true,
		width: 900,
		height: 620,
		resizable: false,
		layout: 'fit',
		items: [formPanel2],
		buttonAlign: 'center',
		buttons:[btnguardar2,btncancelar2]
    }).show();
    
}

function aceptarRechazarTarea(data){
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
			text: 'Aceptar',
			cls: 'x-btn-rigth',
			handler: function() {
				conn.request({
					method: 'POST',
					params :{
						id: data.id_sintomaTarea,
						observacion: Ext.getCmp('calendario_observacion').value,
						bandera: 'Aceptada'
					},
					url: '../soporte/info_caso/' + 'administrarTareaAsignadaGrid',
					success: function(response){
						Ext.Msg.alert('Mensaje','Se actualizo los datos.', function(btn){
									if(btn=='ok'){
											win.destroy();
											storeTareas.load();
									}
								});
					},
					failure: function(rec, op) {
						var json = Ext.JSON.decode(op.response.responseText);
						Ext.Msg.alert('Alerta ',json.mensaje);
					}
			});
			}
	});
	btnrechazar = Ext.create('Ext.Button', {
			text: (data.action6 == 'button-grid-rechazarTarea') ? 'Anular' : 'Rechazar',
		   cls: 'x-btn-rigth',
			handler: function() {
				conn.request({
					method: 'POST',
					params :{
						id: data.id_sintomaTarea,
						observacion: Ext.getCmp('calendario_observacion').value,
						bandera: (data.action6 == 'button-grid-rechazarTarea') ? 'Rechazada' : 'Anulada'
					},
					url: '../soporte/info_caso/' + 'administrarTareaAsignadaGrid',
					success: function(response){
						Ext.Msg.alert('Mensaje','Se actualizo los datos.', function(btn){
									if(btn=='ok'){
											win.destroy();
											storeTareas.load();
									}
								});
					},
					failure: function(rec, op) {
						var json = Ext.JSON.decode(op.response.responseText);
						Ext.Msg.alert('Alerta ',json.mensaje);
					}
			});
			}
	});
	btncancelar = Ext.create('Ext.Button', {
			text: 'Cerrar',
			cls: 'x-btn-rigth',
			handler: function() {
				win.destroy();
			}
	});
     
	
    var string_html  = "<table width='100%' border='0' >";
    string_html += "    <tr>";
    string_html += "        <td colspan='6'>";
    string_html += "                <tr style='height:380px'>";
    string_html += "                    <td colspan='4'><div id='criterios_aceptar'></div></td>";
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
		
	formPanel = Ext.create('Ext.form.Panel', {
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
			},	
			{
				xtype: 'fieldset',
				title: 'Información de la tarea',                       
                autoHeight: true,
				width: 1030,
				items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Tarea:',
						id: 'calendario_tarea',
						name: 'calendario_tarea',
						value: data.nombre_tarea
//                        },
//                        {
//                            xtype: 'textfield',
//                            fieldLabel: 'Fecha de Asignacion:',
//                            id: 'calendario_fechaAsignacion',
//                            name: 'calendario_fechaAsignacion',
//                            value: o.data.EndDate.toLocaleDateString()
//                        },
//                        {
//                            xtype: 'textfield',
//                            fieldLabel: 'Hora de Asignacion:',
//                            id: 'calendario_horaAsignacion',
//                            name: 'calendario_horaAsignacion',
//                            value: o.data.EndDate.toLocaleTimeString()
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Obsevacion:',
						id: 'calendario_observacion',
						name: 'calendario_observacion',
						rows: 3,
						cols: 120
					}
				]
			}
		]
	});
				
	win = Ext.create('Ext.window.Window', {
		title: 'Aceptar / Rechazar Tarea Asignada',
		modal: true,
		width: 1060,
		height: 650,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btnguardar,btnrechazar,btncancelar]
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
        width: 500,
        height: 380,
        autoRender:true,
        enableColumnResize :false,
        id:'gridCriterios_aceptar',
        store: storeCriterios_aceptar,
		viewConfig: { enableTextSelection: true }, 
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
        width: 500,
        height: 380,
        sortableColumns:false,
        store: storeAfectados_aceptar,
		viewConfig: { enableTextSelection: true }, 
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