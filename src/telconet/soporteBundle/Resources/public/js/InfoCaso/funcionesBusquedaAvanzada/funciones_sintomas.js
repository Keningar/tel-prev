function agregarSintoma(rec){
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
	
	winSintomas = "";
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
	
    if (!winSintomas)
    { 
	
		Ext.MessageBox.show({
		   msg: 'Cargando los datos, Por favor espere!!',
		   progressText: 'Saving...',
		   width:300,
		   wait:true,
		   waitConfig: {interval:200}
		});
					
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
					var valorBool = validarSintomas();
					
					if(valorBool)
					{
					
		                json_sintomas = obtenerSintomas();
		                
		                conn.request({
		                    method: 'POST',
		                    params :{
		                        id_caso: id_caso,
		                        sintomas: json_sintomas
		                    },
		                    url: '../soporte/info_caso/' + 'actualizarSintomas',
		                    success: function(response){
		                        Ext.Msg.alert('Mensaje','Se actualizaron los sintomas.', function(btn){
									if(btn=='ok'){
										cierraVentanaByIden(winSintomas);										
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
					cierraVentanaByIden(winSintomas);
	            }
	    });
	    
	         
	    storeSintomas = new Ext.data.Store({ 
	        pageSize: 10,
	        autoLoad: true,
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
	                estado: 'Todos',
					boolCriteriosAfectados: ''
	            }
	        },
	        fields:
			[
				{name:'id_sintoma', mapping:'id_sintoma'},
				{name:'nombre_sintoma', mapping:'nombre_sintoma'},
				{name:'criterios_sintoma', mapping:'criterios_sintoma'},
				{name:'afectados_sintoma', mapping:'afectados_sintoma'}
			]
	    });
	    selModelSintomas = Ext.create('Ext.selection.CheckboxModel', {
			checkOnly: true,
			listeners: {
	            selectionchange: function(sm, selections) {
	                gridSintomas.down('#removeButton').setDisabled(selections.length == 0);
	            }
	        }
	    })
	    cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
	        clicksToEdit: 1,
	        listeners: {
	            edit: function(){
	                gridSintomas.getView().refresh();
	            }
	        }
	    });
	    comboSintomaStore = new Ext.data.Store({ 
	        total: 'total',
	        proxy: {
	            type: 'ajax',
	            url : '../administracion/soporte/admi_sintoma/grid',
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
	                {name:'id_sintoma', mapping:'id_sintoma'},
	                {name:'nombre_sintoma', mapping:'nombre_sintoma'}
	              ]
	    });

	    // Create the combo box, attached to the states data store
	    comboSintoma = Ext.create('Ext.form.ComboBox', {
	        id:'comboSintoma',
	        store: comboSintomaStore,
	        displayField: 'nombre_sintoma',
	        valueField: 'id_sintoma',
	        height:30,
	        border:0,
	        margin:0,
			fieldLabel: false,	
			queryMode: "remote",
			emptyText: ''
	    });
	    Ext.define('Sintoma', {
	        extend: 'Ext.data.Model',
	        fields: [
	            {name: 'id_sintoma',  type: 'string'},
	            {name: 'nombre_sintoma',  type: 'string'}
	        ]
	    });
	    gridSintomas = Ext.create('Ext.grid.Panel', {
	        id:'gridSintomas',
	        store: storeSintomas,
			viewConfig: { enableTextSelection: true, stripeRows:true }, 
	        columnLines: true,
	        columns: [{
	            id: 'id_sintoma',
	            header: 'SintomaId',
	            dataIndex: 'id_sintoma',
	            hidden: true,
	            hideable: false
	        }, {
	            id: 'nombre_sintoma',
	            header: 'Sintoma',
	            dataIndex: 'nombre_sintoma',
	            width: 320,
	            sortable: true,
	            renderer: function (value, metadata, record, rowIndex, colIndex, store){
	                record.data.id_sintoma = record.data.nombre_sintoma;
	                for (var i = 0;i< comboSintomaStore.data.items.length;i++)
	                {
	                    if (comboSintomaStore.data.items[i].data.id_sintoma== record.data.id_sintoma)
	                    {
	                        gridSintomas.getStore().getAt(rowIndex).data.id_sintoma=record.data.id_sintoma;
	                        //alert(gridSintomas.getStore().getAt(rowIndex).data.id_sintoma);
	                        record.data.id_sintoma = comboSintomaStore.data.items[i].data.id_sintoma;
	                        record.data.nombre_sintoma = comboSintomaStore.data.items[i].data.nombre_sintoma;
	                        break;
	                    }
	                }
	                return record.data.nombre_sintoma;
	            },
	            editor: {
	                id:'searchSintoma_cmp',
	                xtype: 'combobox',
	                displayField:'nombre_sintoma',
	                valueField: 'id_sintoma',
	                loadingText: 'Buscando ...',
	                store: comboSintomaStore,
					fieldLabel: false,	
					queryMode: "remote",
					emptyText: '',
	                listClass: 'x-combo-list-small'
	            }
	        },{
	            id: 'criterios_sintoma',
	            header: 'criterios_sintoma',
	            dataIndex: 'criterios_sintoma',
	            hidden: true,
	            hideable: false
	        },{
	            id: 'afectado_sintoma',
	            header: 'afectado_sintoma',
	            dataIndex: 'afectado_sintoma',
	            hidden: true,
	            hideable: false
	        },
	        {
	            header: 'Acciones',
	            xtype: 'actioncolumn',
	            width:130,
	            sortable: false,
	            items: [{                
	                iconCls: 'button-grid-afectados',
	                tooltip: 'Agregar Afectados',
	                handler: function(grid, rowIndex, colIndex) {
	                    agregarAfectadosXSintoma(grid.getStore().getAt(rowIndex).data.nombre_sintoma, id_caso); 
	                }
	            }]
	        }],
	        selModel: selModelSintomas,
	        // inline buttons
	        dockedItems: [{
	            xtype: 'toolbar',
	            items: [{
	                itemId: 'removeButton',
	                text:'Eliminar',
	                tooltip:'Elimina el item seleccionado',
	                disabled: true,
	                handler : function(){eliminarSeleccion(gridSintomas, 'gridSintomas', selModelSintomas);}
	            }, '-', {
	                itemId: 'addButton',
	                text:'Agregar',
	                tooltip:'Agrega un item a la lista',
	                handler : function(){		
						if(	flagCreador && !flagBoolAsignado )
						{
							//alert("Permisos crear Sintomas");
							//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN SINTOMA ANTERIOR... ANTES DE CREAR OTRO..
							var storeValida = Ext.getCmp("gridSintomas").getStore();
							var boolSigue = false;
							var boolSigue2 = false;
							
							if(storeValida.getCount() > 0)
							{
								var boolSigue_vacio = true;
								var boolSigue_igual = true;
								for(var i = 0; i < storeValida.getCount(); i++)
								{
									var id_sintoma = storeValida.getAt(i).data.id_sintoma;
									var nombre_sintoma = storeValida.getAt(i).data.nombre_sintoma;
									
									if(id_sintoma != "" && nombre_sintoma != ""){ /*NADA*/  }
									else {  boolSigue_vacio = false; }
									
									if(i>0)
									{
										for(var j = 0; j < i; j++)
										{
											var id_sintoma_valida = storeValida.getAt(j).data.id_sintoma;
											var nombre_sintoma_valida = storeValida.getAt(j).data.nombre_sintoma;
											
											if(id_sintoma_valida == id_sintoma || nombre_sintoma_valida == nombre_sintoma)
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
								// Create a model instance
								var r = Ext.create('Sintoma', {
									id_sintoma: '',
									nombre_sintoma: '',
									criterios_sintoma: '',
									afectados_sintoma: ''
								});
								storeSintomas.insert(0, r);
							}
							else if(!boolSigue)
							{
								Ext.Msg.alert('Alerta ',"Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
							}
							else if(!boolSigue2)
							{
								Ext.Msg.alert('Alerta ',"No puede ingresar el mismo sintoma! Debe modificar el registro repetido, antes de solicitar un nuevo sintoma");
							}
							else
							{
								Ext.Msg.alert('Alerta ',"Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
							}
						}
						else
						{
							Ext.Msg.alert('Alerta ', "No tiene permisos para crear Sintomas, porque el caso fue asignado a otra persona");
						}
	                }
	            }]
	        }],

	        width: 600,
	        height: 170,
	        frame: true,
	        title: 'Agregar Informacion de Sintomas',
	        plugins: [cellEditing]
	    });
		
	    formPanel = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 300,
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
						xtype: 'textfield',
						fieldLabel: 'Caso:',
						id: 'numero_casoSintoma',
						name: 'numero_casoSintoma',
						value: numero
					},
					{
						xtype: 'textfield',
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
						cols: 57,
						value: version_inicial
					},
					gridSintomas
				]
			}]
		 });
			 
	    winSintomas = Ext.create('Ext.window.Window', {
			title: 'Agregar Sintomas',
			modal: true,
			width: 660,
			height: 440,
			resizable: false,
			layout: 'fit',
			closabled: false,
			items: [formPanel],
			buttonAlign: 'center',
			buttons:[btnguardar,btncancelar]
	    }).show(); 
		
		Ext.MessageBox.hide();
	}
}

function existeRecordSintoma(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
      
    nombre=grid.getStore().getAt(i).get('nombre_sintoma');
    if(nombre!="")
    {
        if((nombre == myRecord.get('nombre_sintoma')))
        {
          existe=true;
          break;
        }
    }
  }
  return existe;
}

function obtenerSintomas()
{
  var array = new Object();
  array['total'] =  gridSintomas.getStore().getCount();
  array['sintomas'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridSintomas.getStore().getCount(); i++)
  {
  	array_data.push(gridSintomas.getStore().getAt(i).data);
  }
  array['sintomas'] = array_data;
  return Ext.JSON.encode(array);
}

function validarSintomas()
{		
	//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN SINTOMA ANTERIOR... ANTES DE CREAR OTRO..
	var storeValida = Ext.getCmp("gridSintomas").getStore();
	var boolSigue = false;
	var boolSigue2 = false;
	
	if(storeValida.getCount() > 0)
	{
		var boolSigue_vacio = true;
		var boolSigue_igual = true;
		for(var i = 0; i < storeValida.getCount(); i++)
		{
			var id_sintoma = storeValida.getAt(i).data.id_sintoma;
			var nombre_sintoma = storeValida.getAt(i).data.nombre_sintoma;
			
			if(id_sintoma != "" && nombre_sintoma != ""){ /*NADA*/  }
			else {  boolSigue_vacio = false; }
			
			if(i>0)
			{
				for(var j = 0; j < i; j++)
				{
					var id_sintoma_valida = storeValida.getAt(j).data.id_sintoma;
					var nombre_sintoma_valida = storeValida.getAt(j).data.nombre_sintoma;
					
					if(id_sintoma_valida == id_sintoma || nombre_sintoma_valida == nombre_sintoma)
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
		Ext.Msg.alert('Alerta ',"Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
		return false;
	}
	else if(!boolSigue2)
	{
		Ext.Msg.alert('Alerta ',"No puede ingresar el mismo sintoma! Debe modificar el registro repetido, antes de solicitar un nuevo sintoma");
		return false;
	}
	else
	{
		Ext.Msg.alert('Alerta ',"Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
		return false;
	}
}

function obtenerCriterios(sintoma)
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

    for(var i=0; i < gridSintomas.getStore().getCount(); i++)
    {
         if(gridSintomas.getStore().getAt(i).data.nombre_sintoma==sintoma)
            gridSintomas.getStore().getAt(i).data.criterios_sintoma = Ext.JSON.encode(array_criterios);
    } 
}

function obtenerAfectados(sintoma)
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

    for(var i=0; i < gridSintomas.getStore().getCount(); i++)
    {
         if(gridSintomas.getStore().getAt(i).data.nombre_sintoma==sintoma)
            gridSintomas.getStore().getAt(i).data.afectados_sintoma = Ext.JSON.encode(array_afectados);
    } 
  
}

function validarFormulario(){
    
    fecha = document.getElementById("fecha_apertura");
    fecha.value =  Ext.getCmp('fe_apertura').getValues().fe_apertura_value;
    hora = document.getElementById("hora_apertura");
    hora.value =  Ext.getCmp('ho_apertura').getValues().ho_apertura_value;
    
    obtenerCriterios(); 
    obtenerAfectados();

    return true;
    
    
}

function presentarEncontrados(id, name,objId,objName, band)
{  
	var tipoElemento = '';
    if(band == "Capa2")
    {
        //if (objName == 'Puertos' || objName == 'Cascada' || objName == 'Cliente')
        cri_opcion = '';
        cri_opcion = 'Elementos';

        cri_detalle = '';
        cri_detalle = 'Elemento: '+name+' | OPCION: '+objName;

        cri_query = '';
        cri_query_2 = '';
		
		tipoElemento = Ext.getCmp("comboTipoElemento").getValue();
    }
  
    if(band == "Capa3")
    {
        // if (objName == 'Logines' || objName == 'Elementos')
        cri_opcion = '';
        cri_opcion = 'Segmentos';

        cri_detalle = '';
        cri_detalle = 'Segmento: '+name+' | OPCION: '+objName;

        cri_query = '';
        cri_query_2 = '';
    }
    
    if(band == "Capa7")
    {
        // if (objName == 'Logines' || objName == 'Elementos')
        cri_opcion = '';
        cri_opcion = 'Productos';

        cri_detalle = '';
        cri_detalle = 'Producto: '+name+' | OPCION: '+objName;

        cri_query = '';
        cri_query_2 = '';
    }
    
    if(band == "Otros")
    {
        // if (objName == 'Logines' || objName == 'Elementos')
        cri_opcion = '';
        cri_opcion = 'Clientes';

        cri_detalle = '';
        cri_detalle = 'Cliente: '+name+' | OPCION: '+objName;

        cri_query = '';
        cri_query_2 = '';
        
    }
    
    limpiarCombos('notodo', band);    
    gridEncontrados.getStore().removeAll;

    storeEncontrados.proxy.extraParams = {id_param: id, nombre_param: name, tipo_param: objId, band: band, tipoElementoId: tipoElemento};
    storeEncontrados.load({params: {limit: 8, start: 0}}); 
}

function limpiarCombos(bandBorra, band)
{
    if(band == "Capa2")
    {    
        Ext.getCmp('comboSegmentos').reset();
        Ext.getCmp('comboProductos').reset();
        Ext.getCmp('comboClientes').reset();
    }
    else if(band == "Capa3")
    {    
        Ext.getCmp('comboTipoElemento').reset();
        Ext.getCmp('comboProductos').reset();
        Ext.getCmp('comboClientes').reset();
    }
    else if(band == "Capa7")
    {    
        Ext.getCmp('comboTipoElemento').reset();
        Ext.getCmp('comboSegmentos').reset();
        Ext.getCmp('comboClientes').reset();
    }
    else if(band == "Otros")
    {    
        Ext.getCmp('comboTipoElemento').reset();
        Ext.getCmp('comboSegmentos').reset();
        Ext.getCmp('comboProductos').reset();
    }
    else
    {
        Ext.getCmp('comboTipoElemento').reset();
        Ext.getCmp('comboSegmentos').reset();
        Ext.getCmp('comboProductos').reset();
        Ext.getCmp('comboClientes').reset();
    }                
    
    if(bandBorra == "todo")
    {
        Ext.getCmp('comboElemento').reset();	
        Ext.getCmp('comboOpciones').reset();	
        Ext.getCmp('comboOpcionesSegmento').reset();	
        Ext.getCmp('comboOpcionesProducto').reset();	
        Ext.getCmp('comboOpcionesCliente').reset();

        Ext.getCmp('comboOpciones').setDisabled(true);
        Ext.getCmp('comboOpcionesSegmento').setDisabled(true);
        Ext.getCmp('comboOpcionesProducto').setDisabled(true);
        Ext.getCmp('comboOpcionesCliente').setDisabled(true);
        
        storeEncontrados.removeAll();
    }
    else
    {
        //nada
    }
    
}

function ingresarCriterio()
{
  var query='';
  
  if(cri_opcion)
  {
    if(smEncontrados.getSelection().length > 0)
    {
        id_criterio = getIdCriterio();
        var r = Ext.create('Criterio', {
                            id_criterio_afectado: id_criterio,
                            caso_id: '',
                            criterio: cri_opcion,
                            opcion: cri_detalle
                        });          
        if(!existeCriterio(r, gridCriterios))
        {
            storeCriterios.insert(0, r);  
        } 
		else
		{
			id_criterio = getIdCriterioGuardado(r, gridCriterios);
		}
		
		for(var i=0 ;  i < smEncontrados.getSelection().length ; ++i)
		{
			var rAfectados = Ext.create('Afectado', {
							id: '',
							id_afectado: smEncontrados.getSelection()[i].get('id_parte_afectada'),
							id_criterio: id_criterio,
							id_afectado_descripcion: smEncontrados.getSelection()[i].get('id_descripcion_1'),
							nombre_afectado: smEncontrados.getSelection()[i].get('nombre_parte_afectada'),
							descripcion_afectado: smEncontrados.getSelection()[i].get('nombre_descripcion_1')
						});  
			if(!existeAfectado(rAfectados, gridAfectados))
			{
				storeAfectados.insert(0, rAfectados);  
			} 						
		}
        
        //limpiarCombos('todo', ''); 
       // storeEncontrados.proxy.extraParams = {id_param: '', nombre_param: '', tipo_param: 'login'};
       // storeEncontrados.load({params: {limit: 8, start: 0}});
    }
    else
    {
		alert('Seleccione por lo menos un afectado de la lista');
    }
  }
  else
	alert('Primero debe seleccionar algun criterio de busqueda para los afectados');
}

function getIdCriterio(){
    var id=0;
    if(storeCriterios.getCount()==0)
        return 1;
    else{
        for(var i=0; i < storeCriterios.getCount(); i++)
        {
            if(storeCriterios.getAt(i).get('id_criterio_afectado')>id)
                id=storeCriterios.getAt(i).get('id_criterio_afectado');
        }
    }
    return id+1;   
}

function getIdCriterioGuardado(myRecord, grid){
    var id=0;
   
	var existe=false;
	var num=grid.getStore().getCount();  
	for(var i=0; i < num ; i++)
	{
		var id_criterio_afectado=grid.getStore().getAt(i).get('id_criterio_afectado');
		var criterio=grid.getStore().getAt(i).get('criterio');
		var detalle=grid.getStore().getAt(i).get('opcion'); 
		
		if(criterio == myRecord.get('criterio') && detalle == myRecord.get('opcion'))
		{ 
			id =id_criterio_afectado;
			break;
		}
	}
	return id; 
}

function existeCriterio(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();  
  for(var i=0; i < num ; i++)
  {
    var criterio=grid.getStore().getAt(i).get('criterio');
    var detalle=grid.getStore().getAt(i).get('opcion'); 
    
    if(criterio == myRecord.get('criterio') && detalle == myRecord.get('opcion'))
    { 
        existe=true;
       //alert('Ya existe un criterio similar');
        break;
    }
  }
  return existe;	
}

function existeAfectado(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();  
  for(var i=0; i < num ; i++)
  {
    var id_criterio=grid.getStore().getAt(i).get('id_criterio');
    var id_afectado=grid.getStore().getAt(i).get('id_afectado'); 
    var nombre_afectado=grid.getStore().getAt(i).get('nombre_afectado'); 
    var descripcion_afectado=grid.getStore().getAt(i).get('descripcion_afectado'); 
    
    if(id_criterio == myRecord.get('id_criterio') && nombre_afectado == myRecord.get('nombre_afectado') && descripcion_afectado == myRecord.get('descripcion_afectado'))
    { 
        existe=true;
       //alert('Ya existe un criterio similar');
        break;
    }
  }
  return existe;	
}


function eliminarCriterio(datosSelect,storeAfectados)
{
    for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {
        var num = storeAfectados.getCount();
        for(var j = 0; j < storeAfectados.getCount(); j++)
        {
            if(storeAfectados.getAt(j).get('id_criterio')==datosSelect.getSelectionModel().getSelection()[i].get('id_criterio_afectado')){
                storeAfectados.removeAt(j);
                j--;
            }
                
        }
        
		datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
 
}

function agregarAfectadosXSintoma(sintoma, id_caso){
    
    if(sintoma=='')
    {
        Ext.Msg.alert('Alerta','Debe escoger un sintoma primero.');
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
                obtenerCriterios(sintoma); 
                obtenerAfectados(sintoma);
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
        pageSize: 200,
        total: 'total',
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
				id_sintoma: sintoma,
				id_hipotesis: 'NO'
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
				id_sintoma: sintoma,
				id_hipotesis: 'NO'
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
                 Ext.create('Ext.grid.RowNumberer'),
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
                {"opcion":"Logines", "nombre":"Punto Cliente"}
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
        pageSize: 200,
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
        pageSize: 200,
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
        pageSize: 200,
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