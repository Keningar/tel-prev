/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var winMenuAsignacion;
var winAsignacion;
var winAsignacionIndividual;
var winRecursoDeRed;
var gridIp;
var gridIpPublica;
var gridIpMonitoreo;
var tareasJS;


var connTareas = new Ext.data.Connection({
		listeners: {
			'beforerequest': {
				fn: function (con, opt) {						
					Ext.MessageBox.show({
					   msg: 'Cargando Tareas',
					   progressText: 'loading...',
					   width:300,
					   wait:true,
					   waitConfig: {interval:200}
					});
					//Ext.get(document.body).mask('Loading...');
				},
				scope: this
			},
			'requestcomplete': {
				fn: function (con, res, opt) {
					Ext.MessageBox.hide();
					//Ext.get(document.body).unmask();
				},
				scope: this
			},
			'requestexception': {
				fn: function (con, res, opt) {
					Ext.MessageBox.hide();
					//Ext.get(document.body).unmask();
				},
				scope: this
			}
		}
});
		
var connAsignarResponsable = new Ext.data.Connection({
	listeners: {
		'beforerequest': {
			fn: function (con, opt) {						
				Ext.MessageBox.show({
				   msg: 'Grabando los datos, Por favor espere!!',
				   progressText: 'Saving...',
				   width:300,
				   wait:true,
				   waitConfig: {interval:200}
				});
				//Ext.get(document.body).mask('Loading...');
			},
			scope: this
		},
		'requestcomplete': {
			fn: function (con, res, opt) {
				Ext.MessageBox.hide();
				//Ext.get(document.body).unmask();
			},
			scope: this
		},
		'requestexception': {
			fn: function (con, res, opt) {
				Ext.MessageBox.hide();
				//Ext.get(document.body).unmask();
			},
			scope: this
		}
	}
});

var connRecursoDeRed = new Ext.data.Connection({
	listeners: {
		'beforerequest': {
			fn: function (con, opt) {						
				Ext.MessageBox.show({
				   msg: 'Grabando los datos, Por favor espere!!',
				   progressText: 'Saving...',
				   width:300,
				   wait:true,
				   waitConfig: {interval:200}
				});
				//Ext.get(document.body).mask('Loading...');
			},
			scope: this
		},
		'requestcomplete': {
			fn: function (con, res, opt) {
				Ext.MessageBox.hide();
				//Ext.get(document.body).unmask();
			},
			scope: this
		},
		'requestexception': {
			fn: function (con, res, opt) {
				Ext.MessageBox.hide();
				//Ext.get(document.body).unmask();
			},
			scope: this
		}
	}
});
/************************************************************************ */
/*********************** CAMBIAR TIPO RESPONSABLE *********************** */
/************************************************************************ */
function cambiarTipoResponsable(valor)
{
    if(valor == "empleado")
    {
        $('#tr_empleado').css("display", "table"); 
        $('#tr_cuadrilla').css("display", "none"); 
        $('#tr_empresa_externa').css("display", "none"); 
        
        storeEmpleados.load();
    }
    else if(valor == "cuadrilla")
    {
        $('#tr_empleado').css("display", "none"); 
        $('#tr_cuadrilla').css("display", "table"); 
        $('#tr_empresa_externa').css("display", "none"); 
        
        storeCuadrillas.load();
    }
    else if(valor == "empresaExterna")
    {
        $('#tr_empleado').css("display", "none"); 
        $('#tr_cuadrilla').css("display", "none"); 
        $('#tr_empresa_externa').css("display", "table"); 
        
        storeEmpresaExterna.load();
    }    
    
}

function cambiarTipoResponsable_Individual(i, valor)
{
    if(valor == "empleado")
    {
        
        Ext.getCmp('cmb_empleado_'+i).setVisible(true);
        Ext.getCmp('cmb_cuadrilla_'+i).setVisible(false);
        Ext.getCmp('cmb_empresa_externa_'+i).setVisible(false);
        
        Ext.getCmp('cmb_empleado_'+i).setDisabled(false);
        Ext.getCmp('cmb_cuadrilla_'+i).setDisabled(true);
        Ext.getCmp('cmb_empresa_externa_'+i).setDisabled(true);
            
       // eval("storeEmpleados_"+i+".load();");
    }
    else if(valor == "cuadrilla")
    {
        Ext.getCmp('cmb_empleado_'+i).setVisible(false);
        Ext.getCmp('cmb_cuadrilla_'+i).setVisible(true);
        Ext.getCmp('cmb_empresa_externa_'+i).setVisible(false);
        
        Ext.getCmp('cmb_empleado_'+i).setDisabled(true);
        Ext.getCmp('cmb_cuadrilla_'+i).setDisabled(false);
        Ext.getCmp('cmb_empresa_externa_'+i).setDisabled(true);
        
        
       // eval("storeCuadrillas_"+i+".load();");
    }
    else if(valor == "empresaExterna")
    { 
        Ext.getCmp('cmb_empleado_'+i).setVisible(false);
        Ext.getCmp('cmb_cuadrilla_'+i).setVisible(false);
        Ext.getCmp('cmb_empresa_externa_'+i).setVisible(true);
        
        Ext.getCmp('cmb_empleado_'+i).setDisabled(true);
        Ext.getCmp('cmb_cuadrilla_'+i).setDisabled(true);
        Ext.getCmp('cmb_empresa_externa_'+i).setDisabled(false);
        
      //  eval("storeEmpresaExterna_"+i+".load();");
    }    
} 

/************************************************************************ */
/******************* GUARDAR ASIGNAR RESPONSABLE  *********************** */
/************************************************************************ */
function asignarResponsable(origen, id)
{    
    var banderaEscogido = false;
    var codigoEscogido = "";
    var tituloError = "";
        
    var param = '';
    var boolError = true;
    if(origen == "local")
    {
        banderaEscogido = $("input[name='tipoResponsable']:checked").val();
        if(banderaEscogido == "empleado")
        {
            tituloError = "Empleado";
            codigoEscogido = Ext.getCmp('cmb_empleado').value;
        }    
        if(banderaEscogido == "cuadrilla")
        {
            tituloError = "Cuadrilla";
            codigoEscogido = Ext.getCmp('cmb_cuadrilla').value;
        } 
        if(banderaEscogido == "empresaExterna")
        {
            tituloError = "Contratista";
            codigoEscogido = Ext.getCmp('cmb_empresa_externa').value;
        }
    
        if(sm.getSelection().length > 0)
        {
            for(var i=0 ;  i < sm.getSelection().length ; ++i)
            {
                param = param + sm.getSelection()[i].data.id_factibilidad;
                if(i < (sm.getSelection().length -1))
                {
                    param = param + '|';
                }
            } 
            if(param && param!="")
            { 
                //NADA
            }
            else
            {
                boolError = false;
                Ext.Msg.alert('Alerta','No hay parametros parseados.');
            }

        }    
        else
        {
            boolError = false;
            Ext.Msg.alert('Alerta','Seleccione por lo menos un registro de la lista');
        }   
    }
    else if(origen == "otro" || origen == "otro2")
    {
        banderaEscogido = $("input[name='tipoResponsable_1']:checked").val();
        if(banderaEscogido == "empleado")
        {
            tituloError = "Empleado";
            codigoEscogido = Ext.getCmp('cmb_empleado_1').value;
        }    
        if(banderaEscogido == "cuadrilla")
        {
            tituloError = "Cuadrilla";
            codigoEscogido = Ext.getCmp('cmb_cuadrilla_1').value;
        } 
        if(banderaEscogido == "empresaExterna")
        {
            tituloError = "Contratista";
            codigoEscogido = Ext.getCmp('cmb_empresa_externa_1').value;
        }
        
        if(id == null || !id || id == 0 || id == "0" || id == "")
        {
            boolError = false;
            Ext.Msg.alert('Alerta','No existe el id Detalle Solicitud');
        }
    }
    else
    {
        boolError = false;
        Ext.Msg.alert('Alerta','No hay opcion escogida');
    }  
    
    if(boolError)
    {  
        if(codigoEscogido && codigoEscogido!="")
        {
            Ext.Msg.confirm('Alerta','Se asignaran los registros. Desea continuar?', function(btn){
                if(btn=='yes'){				
					connAsignarResponsable.request({
                        url: "../../planificar/asignar_responsable/asignarAjax",
                        method: 'post',
                        params: { origen : origen, id : id, param : param, banderaEscogido: banderaEscogido, codigoEscogido: codigoEscogido},
						success: function(response){			
							var text = response.responseText;
							if(text == "Se asigno la Tarea Correctamente.")
							{								
								if(origen=="otro"  || origen == "otro2"){
									cierraVentanaAsignacion();
								}
								Ext.Msg.alert('Mensaje', text, function(btn){
									if(btn=='ok'){	
										store.load();
                                                                                //showRecursosDeRed(id, "factibilidad");
									}
								});
							}
							else{
								Ext.Msg.alert('Alerta', 'Error: ' + text);
							}
						},
						failure: function(result) {
							Ext.Msg.alert('Alerta', result.responseText);
						}
					});
                }
            });
        }
        else
        {
            Ext.Msg.alert('Alerta','Por favor seleccione un valor del combo ' + tituloError);
        }
    }
}

function asignarResponsableIndividual(rec,origen, id)
{
    var param = '';
    var boolError = true;
    if(origen == "local")
    {
//        if(sm.getSelection().length > 0)
//        {
//            for(var i=0 ;  i < sm.getSelection().length ; ++i)
//            {
//                param = param + sm.getSelection()[i].data.id_factibilidad;
//                if(i < (sm.getSelection().length -1))
//                {
//                    param = param + '|';
//                }
//            } 
//            if(param && param!="")
//            { 
//                //NADA
//            }
//            else
//            {
//                boolError = false;
//                Ext.Msg.alert('Alerta','No hay parametros parseados.');
//            }
//
//        }    
//        else
//        {
//            boolError = false;
//            Ext.Msg.alert('Alerta','Seleccione por lo menos un registro de la lista');
//        }   
          param = rec.data.id_factibilidad;
    }
    else if(origen == "otro" || origen == "otro2")
    {
        if(id == null || !id || id == 0 || id == "0" || id == "")
        {
            boolError = false;
            Ext.Msg.alert('Alerta','No existe el id Detalle Solicitud');
        }
    }
    else
    {
        boolError = false;
        Ext.Msg.alert('Alerta','No hay opcion escogida');
    }  

    if(boolError)
    {      
        var paramResponsables = ''; 
        var boolErrorTareas = false;
        var mensajeError = "";

        for(i in tareasJS)
        { 
            var banderaEscogido = $("input[name='tipoResponsable_"+i+"']:checked").val();
            var codigoEscogido = "";
            var tituloError = "";

            if(banderaEscogido == "empleado")
            {
                tituloError = "Empleado ";
                codigoEscogido = Ext.getCmp('cmb_empleado_'+i).value;
            }    
            if(banderaEscogido == "cuadrilla")
            {
                tituloError = "Cuadrilla";
                codigoEscogido = Ext.getCmp('cmb_cuadrilla_'+i).value;
            } 
            if(banderaEscogido == "empresaExterna")
            {
                tituloError = "Contratista";
                codigoEscogido = Ext.getCmp('cmb_empresa_externa_'+i).value;
            }                


            if(codigoEscogido && codigoEscogido!="")
            {
                paramResponsables = paramResponsables + + tareasJS[i]['idTarea'] + "@@" + banderaEscogido + "@@" + codigoEscogido;
                if(i < (tareasJS.length -1))
                {
                    paramResponsables = paramResponsables + '|';
                }
            }      
            else
            {
                boolErrorTareas = true;
                mensajeError += "Tarea:"+ tareasJS[i]['nombreTarea'] + " -- Combo: "+tituloError + "<br>";                    
            }            
        }//FIN FOR

        if(!boolErrorTareas)
        {
            Ext.Msg.confirm('Alerta','Se asignaran los registros. Desea continuar?', function(btn){
                if(btn=='yes'){						
					connAsignarResponsable.request({
                        url: "../../planificar/asignar_responsable/asignarIndividualmenteAjax",
                        method: 'post',
                        params: { origen : origen, id : id, param : param, paramResponsables : paramResponsables},
						success: function(response){			
							var text = response.responseText;
							if(text == "Se asigno la Tarea Correctamente.")
							{
								cierraVentanaAsignacionIndividual();
								Ext.Msg.alert('Mensaje', text, function(btn){
									if(btn=='ok'){
										store.load();
                                                                                showRecursoDeRed(rec, id,"asignarResponsable");
									}
								});
							}
							else{
								Ext.Msg.alert('Alerta', 'Error: ' + text);
							}
						},
						failure: function(result) {
							Ext.Msg.alert('Alerta', result.responseText);
						}
					});
                }
            });
        }
        else
        {
            Ext.Msg.alert('Alerta', 'Por favor seleccione todos los combos de los responsables de cada tarea.<br><br>'+
                                    'En esta lista menciona los combos que no han sido seleccionados:<br><br>' +mensajeError);

        } 
    }
}

/************************************************************************ */
/**************************** RECURSO DE RED ****************************** */
/************************************************************************ */
function showRecursoDeRed(rec,id,origen)
{   
    winRecursoDeRed="";
    formPanelRecursosDeRed = "";
    
    if (!winRecursoDeRed)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';           
        CamposRequeridos =  Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
//            width: 600,
            padding: 1,
            layout: 'anchor',
            style: { color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0' }
        });
        //////////////////////nuevo grid//////////////////////////////////
		
		Ext.define('tipoCaracteristica', {
			extend: 'Ext.data.Model',
			fields: [
				{name: 'tipo', type: 'string'}
			]
		});
		
		var comboCaracteristica = new Ext.data.Store({ 
			model: 'tipoCaracteristica',
			data : [
				{tipo:'PUBLICA' },
				{tipo:'MONITOREO' },
				{tipo:'WAN' },
				{tipo:'LAN' }
			]
		});
		
		
		var storeIps = new Ext.data.Store({  
			// pageSize: 50,
			// autoLoad: true,
			// proxy: {
				// type: 'ajax',
				// url : 'getIpPublicas',
				// reader: {
					// type: 'json',
					// totalProperty: 'total',
					// root: 'encontrados'
				// },
				// extraParams: {
					// idServicio: rec.get('id_servicio')
				// }
			// },
			fields:
				[
				  {name:'ip', mapping:'ip'},
				  {name:'mascara', mapping:'mascara'},
				  {name:'gateway', mapping:'gateway'},
				  {name:'tipo', mapping:'tipo'}
				]
		});
    
	
		Ext.define('Ips', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'ip', mapping:'ip'},
              {name:'mascara', mapping:'mascara'},
              {name:'gateway', mapping:'gateway'},
              {name:'tipo', mapping:'tipo'}
        ]
    });
    
    var cellEditingIps = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(editor,object){
				// gridIpPublica.getView().refresh();
				// var rowIdx = object.rowIdx ;
				// var currentIp= object.value;
				// if(esIpValida(currentIp)){
					// if(!existeRecordIp(rowIdx,currentIp, gridIp))
					// {
						// $('input[name="ipPublica_text"]').val('');
						// $('input[name="ipPublica_text"]').val(currentIp);
						// this.collapse();
					// }
					// else
					// {
						// Ext.MessageBox.show({
							// title: 'Error',
							// msg: "Ip ya existente. Por favor ingrese otra.",
							// buttons: Ext.MessageBox.OK,
							// icon: Ext.MessageBox.ERROR
						 // });
						// eliminarSeleccion(gridIp);
					// }
				// }else{
					// Ext.MessageBox.show({
						// title: 'Error',
						// msg: "Ingrese una Ip valida",
						// buttons: Ext.MessageBox.OK,
						// icon: Ext.MessageBox.ERROR
					 // });
					 // eliminarSeleccion(gridIp);
				// }
					
                // refresh summaries
                gridIps.getView().refresh();
            }
        }
    });
    
    var selIps = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridIps.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    //grid de ips
    gridIps = Ext.create('Ext.grid.Panel', {
        id:'gridIps',
        store: storeIps,
        columnLines: true,
        columns: [{
            //id: 'nombreDetalle',
            header: 'Tipo',
            dataIndex: 'tipo',
            width: 100,
            sortable: true,
            editor: {
                //id:'searchTipo_cmp',
                queryMode: 'local',
                xtype: 'combobox',
                displayField:'tipo',
                valueField: 'tipo',
                loadingText: 'Buscando...',
                store: comboCaracteristica
            }
        },{
            header: 'Ip',
            dataIndex: 'ip',
            width: 150,
            editor: {
                id:'ip',
                name:'ip',
                xtype: 'textfield',
                valueField: ''
            }
        },
        {
            header: 'Mascara',
            dataIndex: 'mascara',
            width: 150,
            editor: {
                id:'mascara',
                name:'mascara',
                xtype: 'textfield',
                valueField: ''
            }
        },
        {
            header: 'Gateway',
            dataIndex: 'gateway',
            width: 150,
            editor: {
                id:'gateway',
                name:'gateway',
                xtype: 'textfield',
                valueField: ''
            }
        }],
        selModel: selIps,
        viewConfig:{
            stripeRows:true
        },

        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina el item seleccionado',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridIps);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('Ips', { 
                        ip: '',
                        mascara: '',
                        gateway: '',
                        tipo: ''
                        
                    });
                    if(!existeRecordIp(r, gridIps))
                    {
                        storeIps.insert(0, r);
                        cellEditingIps.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],
        frame: true,
        height: 200,
        title: 'Caracteristicas del Cliente',
        plugins: [cellEditingIps]
    });
    
		//////////////////////////////////////////////////////////////////
		Ext.define('IpPublica', {
			extend: 'Ext.data.Model',
			fields: [
				{name:'ipPublica', mapping:'ipPublica'}
			]
		});
		
		Ext.define('IpMonitoreo', {
			extend: 'Ext.data.Model',
			fields: [
				{name:'ipMonitoreo', mapping:'ipMonitoreo'}
			]
		});

		
		storeIpsPublicas = Ext.create('Ext.data.Store', {
			// destroy the store if the grid is destroyed
			// autoDestroy: true,
			// autoLoad: false,
			model: 'IpPublica',        
			// proxy: {
				// type: 'ajax',
				// load remote data using HTTP
				// url: 'gridTecnologia',
				// specify a XmlReader (coincides with the XML format of the returned data)
				// reader: {
					// type: 'json',
					// totalProperty: 'total',
					// records will have a 'plant' tag
					// root: 'tecnologias'
				// }
			// }
		});
		
		storeIpsMonitoreo = Ext.create('Ext.data.Store', {
			// destroy the store if the grid is destroyed
			// autoDestroy: true,
			// autoLoad: false,
			model: 'IpMonitoreo',        
			// proxy: {
				// type: 'ajax',
				// load remote data using HTTP
				// url: 'gridTecnologia',
				// specify a XmlReader (coincides with the XML format of the returned data)
				// reader: {
					// type: 'json',
					// totalProperty: 'total',
					// records will have a 'plant' tag
					// root: 'tecnologias'
				// }
			// }
		});
	
        var cellEditingIpPublica = Ext.create('Ext.grid.plugin.CellEditing', {
			clicksToEdit: 2,
			listeners: {
				edit: function(editor,object){
					// refresh summaries
					// gridIpPublica.getView().refresh();
					var rowIdx = object.rowIdx ;
					var currentIpPublica = object.value;
					if(esIpValida(currentIpPublica)){
						if(!existeRecordIpPublica(rowIdx,currentIpPublica, gridIpPublica))
						{
							$('input[name="ipPublica_text"]').val('');
							$('input[name="ipPublica_text"]').val(currentIpPublica);
							// this.collapse();
						}
						else
						{
							Ext.MessageBox.show({
								title: 'Error',
								msg: "Ip ya existente. Por favor ingrese otra.",
								buttons: Ext.MessageBox.OK,
								icon: Ext.MessageBox.ERROR
							 });
							eliminarSeleccion(gridIpPublica);
						}
					}else{
						Ext.MessageBox.show({
							title: 'Error',
							msg: "Ingrese una Ip valida",
							buttons: Ext.MessageBox.OK,
							icon: Ext.MessageBox.ERROR
						 });
						 eliminarSeleccion(gridIpPublica);
					}	
				}
			}
		});
		
		var selIpsPublicas = Ext.create('Ext.selection.CheckboxModel', {
			listeners: {
				selectionchange: function(sm, selections) {
					gridIpPublica.down('#removeButton').setDisabled(selections.length == 0);
				}
			}
		});
	
		var cellEditingIpMonitoreo = Ext.create('Ext.grid.plugin.CellEditing', {
			clicksToEdit: 2,
			listeners: {
				edit: function(editor,object){
					// refresh summaries
					// gridIpMonitoreo.getView().refresh();
					var rowIdx = object.rowIdx;
					var currentIpMonitoreo = object.value;
					if(esIpValida(currentIpMonitoreo)){
						if(!existeRecordIpMonitoreo(rowIdx,currentIpMonitoreo, gridIpMonitoreo))
						{
							$('input[name="ipMonitoreo_text"]').val('');
							$('input[name="ipMonitoreo_text"]').val(currentIpMonitoreo);
							// this.collapse();
						}
						else
						{
							Ext.MessageBox.show({
								title: 'Error',
								msg: "Ip ya existente. Por favor ingrese otra.",
								buttons: Ext.MessageBox.OK,
								icon: Ext.MessageBox.ERROR
							 });
							eliminarSeleccion(gridIpMonitoreo);
						}
					}else{
						Ext.MessageBox.show({
							title: 'Error',
							msg: "Ingrese una Ip valida",
							buttons: Ext.MessageBox.OK,
							icon: Ext.MessageBox.ERROR
						}); 
						 eliminarSeleccion(gridIpMonitoreo);
					}
				}
			}
		});
		
		var selIpsMonitoreo = Ext.create('Ext.selection.CheckboxModel', {
			listeners: {
				selectionchange: function(sm, selections) {
					gridIpMonitoreo.down('#removeButton').setDisabled(selections.length == 0);
				}
			}
		});
	
		storeInterfacesByElemento = new Ext.data.Store({
                    autoLoad: true,
	        total: 'total',
			pageSize: 10000,
	        proxy: {
	            type: 'ajax',
	            url : 'getJsonInterfacesByElemento',
	            reader: {
	                type: 'json',
	                totalProperty: 'total',
	                root: 'encontrados'
	            },
	            extraParams: {
	                idElemento: rec.get("elementoId")
	            }
	        },
	        fields:
	                [
	                    {name:'idInterfaceElemento', mapping:'idInterfaceElemento'},
	                    {name:'nombreInterfaceElemento', mapping:'nombreInterfaceElemento'}
	                ]
	    });
		
	//grid Ip Publica
    gridIpPublica = Ext.create('Ext.grid.Panel', {
        id:'	gridIpPublica',
        store: storeIpsPublicas,
        columnLines: true,
        columns: [{
            id: 'ipPublica',
            header: 'Ip Publica',
            dataIndex: 'ipPublica',
            width: 290,
            // sortable: true,
            // renderer: function (value, metadata, record, rowIndex, colIndex, store){
			// alert("aqui estoy");
                // if (typeof(record.data.usuarioAccesoNombre) == "number")
                // {
                    
                    // record.data.usuarioAccesoId = record.data.usuarioAccesoNombre;
                    // for (var i = 0;i< storeUsuarios.data.items.length;i++)
                    // {
                        // if (storeUsuarios.data.items[i].data.idUsuarioAcceso == record.data.usuarioAccesoId)
                        // {
                            // record.data.usuarioAccesoNombre = storeUsuarios.data.items[i].data.nombreUsuarioAcceso;
                            // break;
                        // }
                    // }
                // }
                // return record.data.usuarioAccesoNombre;
            // },
            editor: {
                id:'ipPublica_text',
                name:'ipPublica_text',
                xtype: 'textfield',
                // typeAhead: true,
                // displayField:'nombreUsuarioAcceso',
                // valueField: 'idUsuarioAcceso',
                // triggerAction: 'all',
                // selectOnFocus: true,
                // loadingText: 'Buscando ...',
                // hideTrigger: false,
                // store: storeUsuarios,
                // lazyRender: true,
                // listClass: 'x-combo-list-small',
                // listeners: {
                    // edit: function(editor,textField){
                        // var currentIpPublica = textField.getValue();
						// if(esIpValida(currentIpPublica)){
							// if(!existeRecordIpPublica(currentIpPublica, gridIpPublica))
							// {
								// Ext.get('ipPublica_cmp').dom.value='';
								// Ext.get('ipPublica_cmp').dom.value=currentIpPublica;
								// this.collapse();
								
							// }
							// else
							// {
								// Ext.MessageBox.show({
									// title: 'Error',
									// msg: "Ip ya existente. Por favor ingrese otra.",
									// buttons: Ext.MessageBox.OK,
									// icon: Ext.MessageBox.ERROR
								 // });
								// eliminarSeleccion(gridIpPublica);
							// }
						// }else{
							// Ext.MessageBox.show({
								// title: 'Error',
								// msg: "Ingrese una Ip valida",
								// buttons: Ext.MessageBox.OK,
								// icon: Ext.MessageBox.ERROR
							 // });
						// }
                    // }
                // }
            }
        }],
        selModel: selIpsPublicas,
        viewConfig:{
            stripeRows:true
        },

        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    if(!existeRecordIpPublica("","",gridIpPublica))
                    {
						// Create a model instance
						var r = Ext.create('IpPublica', { 
								ipPublica: ''
						});
						storeIpsPublicas.insert(0, r);
                        // cellEditingIpPublica.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      Ext.MessageBox.show({
						title: 'Error',
						msg: "Ya existe un registro vacio para que sea llenado.",
						buttons: Ext.MessageBox.OK,
						icon: Ext.MessageBox.ERROR
					 });
                    }
                }
            }, '-', {
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina el item seleccionado',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridIpPublica);}
            }]
        }],

        // width: 425,
        // height: 200,
        frame: true,
        title: 'Ips Publicas',
        // renderTo: 'gridIpPublica',
        plugins: [cellEditingIpPublica]
    });
	
	function existeRecordIp(myRecord, grid)
	{
	  var existe=false;
	  var num=grid.getStore().getCount();

	  for(var i=0; i < num ; i++)
	  {
		var canton=grid.getStore().getAt(i).get('ip');

		if((canton == myRecord.get('ip') ) || canton == myRecord.get('ip'))
		{
		  existe=true;
		  break;
		}
	  }
	  return existe;
	}

	function existeRecordIpPublica(rowIdx,ip,grid)
	{
	  var existe=false;
	  var num=grid.getStore().getCount();

	  for(var i=0; i < num ; i++)
	  {
		if(i != rowIdx){
			var ipPublica=grid.getStore().getAt(i).get('ipPublica');
	
			if((ipPublica == ip ))
			{
			  existe=true;
			  break;
			}
		}
	  }
	  return existe;
	}
	
	//grid Ip Monitoreo
    gridIpMonitoreo = Ext.create('Ext.grid.Panel', {
        id:'	gridIpMonitoreo',
        store: storeIpsMonitoreo,
        columnLines: true,
        columns: [{
            id: 'ipMonitoreo',
            header: 'Ip Monitoreo',
            dataIndex: 'ipMonitoreo',
            width: 290,
            // sortable: true,
            // renderer: function (value, metadata, record, rowIndex, colIndex, store){
			// alert("aqui estoy");
                // if (typeof(record.data.usuarioAccesoNombre) == "number")
                // {
                    
                    // record.data.usuarioAccesoId = record.data.usuarioAccesoNombre;
                    // for (var i = 0;i< storeUsuarios.data.items.length;i++)
                    // {
                        // if (storeUsuarios.data.items[i].data.idUsuarioAcceso == record.data.usuarioAccesoId)
                        // {
                            // record.data.usuarioAccesoNombre = storeUsuarios.data.items[i].data.nombreUsuarioAcceso;
                            // break;
                        // }
                    // }
                // }
                // return record.data.usuarioAccesoNombre;
            // },
            editor: {
                id:'ipMonitoreo_cmp',
                xtype: 'textfield',
                // typeAhead: true,
                // displayField:'nombreUsuarioAcceso',
                // valueField: 'idUsuarioAcceso',
                // triggerAction: 'all',
                // selectOnFocus: true,
                // loadingText: 'Buscando ...',
                // hideTrigger: false,
                // store: storeUsuarios,
                // lazyRender: true,
                // listClass: 'x-combo-list-small',
                // listeners: {
                    // edit: function(textField){
                        // var currentIpMonitoreo = textField.getValue();
						// if(esIpValida(currentIpMonitoreo)){
							// if(!existeRecordIpMonitoreo(currentIpMonitoreo, gridIpMonitoreo))
							// {
								// Ext.get('ipMonitoreo_cmp').dom.value='';
								// Ext.get('ipMonitoreo_cmp').dom.value=currentIpMonitoreo;
								// this.collapse();
								
							// }
							// else
							// {
								// Ext.MessageBox.show({
									// title: 'Error',
									// msg: "Ip ya existente. Por favor ingrese otra.",
									// buttons: Ext.MessageBox.OK,
									// icon: Ext.MessageBox.ERROR
								 // });
								// eliminarSeleccion(gridIpMonitoreo);
							// }
						// }else{
							// Ext.MessageBox.show({
								// title: 'Error',
								// msg: "Ingrese una Ip valida",
								// buttons: Ext.MessageBox.OK,
								// icon: Ext.MessageBox.ERROR
							 // });
						// }
                    // }
                // }
            }
        }],
        selModel: selIpsMonitoreo,
        viewConfig:{
            stripeRows:true
        },

        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    if(!existeRecordIpMonitoreo("","",gridIpMonitoreo))
                    {
						// Create a model instance
						var r = Ext.create('IpMonitoreo', { 
								ipPublica: ''
						});
						storeIpsMonitoreo.insert(0, r);
                        // cellEditingIpPublica.startEditByPosition({row: 0, column: 1});
                    }
                    else
                    {
                      Ext.MessageBox.show({
						title: 'Error',
						msg: "Ya existe un registro vacio para que sea llenado.",
						buttons: Ext.MessageBox.OK,
						icon: Ext.MessageBox.ERROR
					 });
                    }
                }
            }, '-', {
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina el item seleccionado',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridIpMonitoreo);}
            }]
        }],

        // width: 425,
        // height: 200,
        frame: true,
        title: 'Ips Monitoreo',
        // renderTo: 'gridIpPublica',
        plugins: [cellEditingIpMonitoreo]
    });
	
	function existeRecordIpMonitoreo(rowIdx,ip,grid)
	{
	  var existe=false;
	  var num=grid.getStore().getCount();

	  for(var i=0; i < num ; i++)
	  {
		if(i != rowIdx){
			var ipMonitoreo=grid.getStore().getAt(i).get('ipMonitoreo');

			if((ipMonitoreo == ip ))
			{
			  existe=true;
			  break;
			}
		}
	  }
	  return existe;
	}
	
	function eliminarSeleccion(datosSelect)
	{
	  for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
	  {
		datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
	  }
	}

	function obtenerDatosIps()
	{
		if(gridIps.getStore().getCount()>=1){
			  var array_relaciones = new Object();
			  array_relaciones['total'] =  gridIps.getStore().getCount();
			  array_relaciones['caracteristicas'] = new Array();
			  var array_data = new Array();
			  for(var i=0; i < gridIps.getStore().getCount(); i++)
			  {
				array_data.push(gridIps.getStore().getAt(i).data);
			  }
			  array_relaciones['caracteristicas'] = array_data;
			  return Ext.JSON.encode(array_relaciones);
		}else{
			return "";
		}	  
	}

	function obtenerIpsPublicas()
	{
		if(gridIpPublica.getStore().getCount()>=1){
			var ips = "";
		  for(var i=0; i < gridIpPublica.getStore().getCount(); i++)
		  {
			if(i==0){
				ips = gridIpPublica.getStore().getAt(i).data.ipPublica;
			}else{
				ips = ips + "@" + gridIpPublica.getStore().getAt(i).data.ipPublica;
			}
		  }
		  return ips;
		}else{
			return "";
		}
	}
	function obtenerIpsMonitoreo()
	{
		if(gridIpMonitoreo.getStore().getCount()>=1){
			var ips = "";
		  for(var i=0; i < gridIpMonitoreo.getStore().getCount(); i++)
		  {
			if(i==0){
				ips = gridIpMonitoreo.getStore().getAt(i).data.ipMonitoreo;
			}else{
				ips = ips + "@" + gridIpMonitoreo.getStore().getAt(i).data.ipMonitoreo;
			}
		  }
		  return ips;
		}else{
			return "";
		}
	}	

        formPanelRecursosDeRed = Ext.create('Ext.form.Panel', {
//            width:600,
            // height:600,
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'fieldset',
                    title: 'Datos del Cliente',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    layout: 'anchor',
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        { 
                            xtype: 'textfield',
                            fieldLabel: 'Cliente',
                            name: 'info_cliente',
                            id: 'info_cliente',
                            value: rec.get("cliente"),
                            allowBlank: false,
                            readOnly : true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Login',
                            name: 'info_login',
                            id: 'info_login',
                            value: rec.get("login2"),
                            allowBlank: false,
                            readOnly : true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Ciudad',
                            name: 'info_ciudad',
                            id: 'info_ciudad',
                            value: rec.get("ciudad"),
                            allowBlank: false,
                            readOnly : true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Coordenadas',
                            name: 'info_coordenadas',
                            id: 'info_coordenadas',
                            value: rec.get("coordenadas"),
                            allowBlank: false,
                            readOnly : true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Direccion',
                            name: 'info_direccion',
                            id: 'info_direccion',
                            value: rec.get("direccion"),
                            allowBlank: false,
                            readOnly : true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Sector',
                            name: 'info_nombreSector',
                            id: 'info_nombreSector',
                            value: rec.get("nombreSector"),
                            allowBlank: false,
                            readOnly : true
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos del Servicio',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [ 
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Servicio',
                            name: 'info_servicio',
                            id: 'info_servicio',
                            value: rec.get("producto"),
                            allowBlank: false,
                            readOnly : true
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos de Recursos de Red',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [ 
			{
                            xtype: 'textfield',
                            fieldLabel: 'Ultima Milla',
                            name: 'txt_um',
                            id: 'txt_um',
                            value: rec.get("ultimaMilla"),
                            allowBlank: false,
                            readOnly : true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Radio',
                            name: 'txt_radio',
                            id: 'txt_radio',
                            value: rec.get("radio"),
                            allowBlank: false,
                            readOnly : true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Pop',
                            name: 'txt_pop',
                            id: 'txt_pop',
                            value: rec.get("pop"),
                            allowBlank: false,
                            readOnly : true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Dslam',
                            name: 'txt_dslam',
                            id: 'txt_dslam',
                            value: rec.get("dslam"),
                            allowBlank: false,
                            readOnly : true
                        },
						 {
                            xtype: 'numberfield',
                            fieldLabel: 'VCI',
                            name: 'vci',
                            id: 'vci',
                            allowNegative:false,
							value:1,
							emptyText: 'Ingrese un numero',
                            labelStyle: "color:red;",
							validator: function(val) {
								 if (!Ext.isEmpty(val)) {
									if(val>=1 && val<=100)
										return true;
									else{
										Ext.getCmp('vci').setValue("1");
										Ext.MessageBox.show({
											title: 'Error',
											msg: "VCI debe ser entre 1 y 100",
											buttons: Ext.MessageBox.OK,
											icon: Ext.MessageBox.ERROR
										 });
										return "VCI debe ser entre 1 y 100";
									}	
								 } else {
									Ext.getCmp('vci').setValue("1");
									Ext.MessageBox.show({
											title: 'Error',
											msg: "VCI debe ser entre 1 y 100",
											buttons: Ext.MessageBox.OK,
											icon: Ext.MessageBox.ERROR
										 });
									 return "VCI debe ser entre 1 y 100";
								 }
						    }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_Interface',
                            name: 'cmb_Interface',
                            fieldLabel: '* Interface',
                            typeAhead: true,
							allowBlank: false,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField:'nombreInterfaceElemento',
                            valueField: 'idInterfaceElemento',
                            selectOnTab: true,
                            store: storeInterfacesByElemento,              
//                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            emptyText: 'Seleccione',
                            labelStyle: "color:red;",
//                            disabled: true,
//                            editable: false,
                            // listeners:{
//                                select:{fn:function(combo, value) {                                        
//                                    Ext.Ajax.request({
//                                        url: "ajaxCargaElemento",
//                                        method: 'post',
//                                        params: { idElemento : combo.getValue()},
//                                        success: function(response){
//                                            var ContDisponibilidad = response.responseText;
//                                            $('input[name="puertos_disponibles"]').val(ContDisponibilidad);                                            
//                                        },
//                                        failure: function(result)
//                                        {
//                                             Ext.MessageBox.show({
//                                                title: 'Error',
//                                                msg: result.statusText,
//                                                buttons: Ext.MessageBox.OK,
//                                                icon: Ext.MessageBox.ERROR
//                                             });
////                             Ext.Msg.alert('Alerta','Error: ' + result.statusText);
//                                        }
//                                    });
//                                }}
                            // }
                        // },
						// {
                            // xtype: 'textfield',
                            // fieldLabel: 'Ip Wan',
                            // name: 'ip_wan',
                            // id: 'ip_wan',
                            // allowBlank: false,
							// emptyText: 'Ingrese una Ip',
                            // labelStyle: "color:red;",
                        // },
						// {
                            // xtype: 'textfield',
                            // fieldLabel: 'Ip Lan',
                            // name: 'ip_lan',
                            // id: 'ip_lan',
                            // allowBlank: false,
							// emptyText: 'Ingrese una Ip',
                            // labelStyle: "color:red;",
                        // },{
							// xtype: 'panel',
							// BodyPadding: 10,
							// bodyStyle: "background: white; padding:10px; border: 0px none;",
							// frame: true,
							// items: [gridIpPublica]
						// },{
							// xtype: 'panel',
							// BodyPadding: 10,
							// bodyStyle: "background: white; padding:10px; border: 0px none;",
							// frame: true,
							// items: [gridIpMonitoreo]
						// }	
						},{
							xtype: 'panel',
							BodyPadding: 10,
							bodyStyle: "background: white; padding:10px; border: 0px none;",
							frame: true,
							items: [gridIps]
						}
                    ]
                }
            ],
            buttons:[
                {
                    text: 'Guardar',
                    handler: function(){     
                        // var ip_wan = $('input[name="ip_wan"]').val();  
                        // var ip_lan = $('input[name="ip_lan"]').val();  
                        var vci = $('input[name="vci"]').val();  
                        var id_interface = Ext.getCmp('cmb_Interface').value;  
                        var id_factibilidad = rec.get("id_factibilidad");
                        var boolError = false;
                        var mensajeError = "";    
                        if(!id_factibilidad || id_factibilidad=="" || id_factibilidad==0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }else{
							if(rec.get('ultimaMilla')=="Radio"){
								
							}
							if(rec.get('ultimaMilla')=="Cobre"){
								  if(!id_interface || id_interface=="" || id_interface==0)
								  {
									boolError = true;
									mensajeError += "La Interface no fue escogida, por favor seleccione.\n";
								  }
							}
						
						} 
						
						              
                        if(!boolError)
                        {
                            connRecursoDeRed.request({
                                url: "guardaRecursosDeRed",
                                method: 'post',
                                // params: { id: id_factibilidad,interface_id: id_interface, ip_wan: ip_wan, ip_lan : ip_lan, ips_publicas: obtenerIpsPublicas() , ips_monitoreo: obtenerIpsMonitoreo()},
                                params: { id: id_factibilidad,interface_id: id_interface,vci: vci, datosIps: obtenerDatosIps() },
								success: function(response){			
									var text = response.responseText;
									if(text == "Se guardo correctamente los Recursos de Red")
									{
										cierraVentanaRecursoDeRed();
										Ext.Msg.alert('Mensaje', text, function(btn){
											   if(btn=='ok'){
												   store.load();
											   }
									   });
									}
									else{
										Ext.MessageBox.show({
                                                                                    title: 'Error',
                                                                                    msg: text,
                                                                                    buttons: Ext.MessageBox.OK,
                                                                                    icon: Ext.MessageBox.ERROR
                                                                                 });
//                                             Ext.Msg.alert('Alerta', 'Error: ' + text);
									}
								},
								failure: function(result) {
                                                                                Ext.MessageBox.show({
                                                                                    title: 'Error',
                                                                                    msg: result.responseText,
                                                                                    buttons: Ext.MessageBox.OK,
                                                                                    icon: Ext.MessageBox.ERROR
                                                                                 });
//                                                                                 Ext.Msg.alert('Alerta', result.responseText);
								}
							});
                        }
                        else{
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: mensajeError,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                             });
//                            Ext.Msg.alert('Error' ,'Error: ' + mensajeError);
                        }                         
                    }
                }
               ,{
                   text: 'Cerrar',
                   handler: function(){
                       cierraVentanaRecursoDeRed();
                   }
               }
            ]
        });
    
	if(rec.get('ultimaMilla')=="Radio"){
		Ext.getCmp('txt_pop').setVisible( false );
		Ext.getCmp('txt_dslam').setVisible( false );
		Ext.getCmp('cmb_Interface').setVisible( false );
		Ext.getCmp('vci').setVisible( false );
	}
	if(rec.get('ultimaMilla')=="Cobre"){
		Ext.getCmp('txt_radio').setVisible( false );
	}
	winRecursoDeRed = Ext.widget('window', {
            title: 'Ingreso de Recursos de Red',
//            width: 640,
//            height:630,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            // closable: false,
            items: [formPanelRecursosDeRed]
        });
    }
    
    winRecursoDeRed.show();    
}

function cierraVentanaRecursoDeRed(){
    winRecursoDeRed.close();
    winRecursoDeRed.destroy();
}

function esIpValida(ip) {
    var RegExPattern = /^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/ ;;
    if ((ip.match(RegExPattern)) && (ip!='')) {
       return true;
    } else {
      return false;
    }
}

/************************************************************************ */
/*********************** ASIGNACION RESPONSABLE ************************* */
/************************************************************************ */
function showMenuAsignacion(origen, id, panelAsignados)
{
    winMenuAsignacion="";
    formPanelMenuAsignacion= "";
    
    if (!winMenuAsignacion)
    {      
        //******** html vacio...
        var iniHtmlVacio1 = '';           
        Vacio1 =  Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            width: 300,
            padding: 4,
            layout: 'anchor',
            style: { color: '#000000' }
        });             
                     
        //******** HtmlDesc
        var iniHtml =   'Por favor escoja algun boton';
        HtmlDesc =  Ext.create('Ext.Component', {
            html: iniHtml,
            width: 300,
            padding: 10,
            style: { color: '#000000' }
        });

        //******** html vacio...
        var iniHtmlVacio = '';           
        Vacio =  Ext.create('Ext.Component', {
            html: iniHtmlVacio,
            width: 300,
            padding: 8,
            layout: 'anchor',
            style: { color: '#000000' }
        });
                        
        formPanelMenuAsignacion = Ext.create('Ext.form.Panel', {
            width:380,
            height:150,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1, HtmlDesc, Vacio1, Vacio],
            buttons:[
                {
                    text: 'Asignacion Global',
                    handler: function(){
                        cierraVentanaMenuAsignacion();
                        showAsignacion(origen, id, panelAsignados);                        
                    }
                },
                {
                    text: 'Asignacion Individual',
                    handler: function(){
                        cierraVentanaMenuAsignacion();
                        showAsignacionIndividual(origen, id, panelAsignados);   
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function(){
                        cierraVentanaMenuAsignacion();
                    }
                }
            ]
        });  
               
	winMenuAsignacion = Ext.widget('window', {
            title: 'Menu Asignacion',
            width: 400,
            height:170,
            minHeight: 170,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelMenuAsignacion]
        });
    }
    
    winMenuAsignacion.show();    
}

function cierraVentanaMenuAsignacion(){
    winMenuAsignacion.close();
    winMenuAsignacion.destroy();
}   

/************************************************************************ */
/*********************** ASIGNACION RESPONSABLE ************************* */
/************************************************************************ */
function showAsignacion(origen, id, panelAsignados)
{
    winAsignacion="";
    formPanelAsignacion= "";
    
    if (!winAsignacion)
    {      
        //******** html vacio...
        var iniHtmlVacio1 = '';           
        Vacio1 =  Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            width: 600,
            padding: 4,
            layout: 'anchor',
            style: { color: '#000000' }
        });  
           
        var i = 1;   
                     
        //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
        var iniHtml =   '<input type="radio" onchange="cambiarTipoResponsable_Individual('+i+', this.value);" checked="" value="empleado" name="tipoResponsable_'+i+'">&nbsp;Empleado' + 
                        '&nbsp;&nbsp;'+
                        '<input type="radio" onchange="cambiarTipoResponsable_Individual('+i+', this.value);" value="cuadrilla" name="tipoResponsable_'+i+'">&nbsp;Cuadrilla'+
                        '&nbsp;&nbsp;'+
                        '<input type="radio" onchange="cambiarTipoResponsable_Individual('+i+', this.value);" value="empresaExterna" name="tipoResponsable_'+i+'">&nbsp;Contratista'+
                        '';
        RadiosTiposResponsable =  Ext.create('Ext.Component', {
            html: iniHtml,
            width: 600,
            padding: 10,
            style: { color: '#000000' }
        });

        // **************** EMPLEADOS ******************
        Ext.define('EmpleadosList', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'id_empleado', type:'int'},
                {name:'nombre_empleado', type:'string'}
            ]
        });           
        eval("var storeEmpleados_"+i+"= Ext.create('Ext.data.Store', { "+
            "  id: 'storeEmpleados_"+i+"', "+
            "  model: 'EmpleadosList', "+
            "  autoLoad: false, "+
            " proxy: { "+
                "   type: 'ajax',"+
            "    url : '../../planificar/asignar_responsable/getEmpleados',"+
                "   reader: {"+
            "        type: 'json',"+
                "       totalProperty: 'total',"+
            "        root: 'encontrados'"+
                "  }"+
            "  }"+
        " });    ");
        combo_empleados = new Ext.form.ComboBox({
            id: 'cmb_empleado_'+i,
            name: 'cmb_empleado_'+i,
            fieldLabel: "Empleados",
            anchor: '100%',
            queryMode:'remote',
            width: 300,
            emptyText: 'Seleccione Empleado',
            store: eval("storeEmpleados_"+ i),
            displayField: 'nombre_empleado',
            valueField: 'id_empleado',
            layout: 'anchor',
            disabled: false
        });


        // ****************  EMPRESA EXTERNA  ******************
        Ext.define('EmpresaExternaList', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'id_empresa_externa', type:'int'},
                {name:'nombre_empresa_externa', type:'string'}
            ]
        });

        eval("var storeEmpresaExterna_"+i+"= Ext.create('Ext.data.Store', { "+
            "  id: 'storeEmpresaExterna_"+i+"', "+
            "  model: 'EmpresaExternaList', "+
            "  autoLoad: false, "+
            " proxy: { "+
                "   type: 'ajax',"+
            "    url : '../../planificar/asignar_responsable/getEmpresasExternas',"+
                "   reader: {"+
            "        type: 'json',"+
                "       totalProperty: 'total',"+
            "        root: 'encontrados'"+
                "  }"+
            "  }"+
        " });    ");
        combo_empresas_externas = new Ext.form.ComboBox({
            id: 'cmb_empresa_externa_'+i,
            name: 'cmb_empresa_externa_'+i,
            fieldLabel: "Contratista",
            anchor: '100%',
            queryMode:'remote',
            width: 300,
            emptyText: 'Seleccione Contratista',
            store: eval("storeEmpresaExterna_"+ i),
            displayField: 'nombre_empresa_externa',
            valueField: 'id_empresa_externa',
            layout: 'anchor',
            disabled: true
        });


        // **************** CUADRILLAS ******************
        Ext.define('CuadrillasList', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'id_cuadrilla', type:'int'},
                {name:'nombre_cuadrilla', type:'string'}
            ]
        });            
        eval("var storeCuadrillas_"+i+"= Ext.create('Ext.data.Store', { "+
            "  id: 'storeCuadrillas_"+i+"', "+
            "  model: 'CuadrillasList', "+
            "  autoLoad: false, "+
            " proxy: { "+
                "   type: 'ajax',"+
            "    url : '../../planificar/asignar_responsable/getCuadrillas',"+
                "   reader: {"+
            "        type: 'json',"+
                "       totalProperty: 'total',"+
            "        root: 'encontrados'"+
                "  }"+
            "  }"+
        " });    ");
        combo_cuadrillas = new Ext.form.ComboBox({
            id: 'cmb_cuadrilla_'+i,
            name: 'cmb_cuadrilla_'+i,
            fieldLabel: "Cuadrilla",
            anchor: '100%',
            queryMode:'remote',
            width: 300,
            emptyText: 'Seleccione Cuadrilla',
            store: eval("storeCuadrillas_"+ i),
            displayField: 'nombre_cuadrilla',
            valueField: 'id_cuadrilla',
            layout: 'anchor',
            disabled: true 
        });  


        //******** html vacio...
        var iniHtmlVacio = '';           
        Vacio =  Ext.create('Ext.Component', {
            html: iniHtmlVacio,
            width: 600,
            padding: 8,
            layout: 'anchor',
            style: { color: '#000000' }
        });
                        
        formPanelAsignacion = Ext.create('Ext.form.Panel', {
            width:700,
            height:150,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1, RadiosTiposResponsable, Vacio1, combo_empleados, combo_cuadrillas, combo_empresas_externas, Vacio],
            buttons:[
                {
                    text: 'Guardar',
                    handler: function(){
                        asignarResponsable(origen, id);                        
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function(){
                        cierraVentanaAsignacion();
                    }
                }
            ]
        });  
              
        Ext.getCmp('cmb_empleado_'+i).setVisible(true);
        Ext.getCmp('cmb_cuadrilla_'+i).setVisible(false);
        Ext.getCmp('cmb_empresa_externa_'+i).setVisible(false);
               
	winAsignacion = Ext.widget('window', {
            title: 'Formulario Asignacion',
            width: 740,
            height:200,
            minHeight: 200,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelAsignacion]
        });
         /*
        if(origen == "otro2" && panelAsignados)
        {
            winAsignacion = Ext.widget('window', {
                title: 'Formulario Asignacion',
                width: 740,
                height:500,
                minHeight: 500,
                layout: 'fit',
                resizable: false,
                modal: true,
                closabled: false,
                items: [formPanelAsignacion]
            });
        }
        else
        {
            winAsignacion = Ext.widget('window', {
                title: 'Formulario Asignacion',
                width: 740,
                height:200,
                minHeight: 200,
                layout: 'fit',
                resizable: false,
                modal: true,
                closabled: false,
                items: [formPanelAsignacion]
            });
        }*/
    }
    
    winAsignacion.show();    
}

function cierraVentanaAsignacion(){
    winAsignacion.close();
    winAsignacion.destroy();
}  

/************************************************************************ */
/***************** ASIGNACION INDIVIDUAL RESPONSABLE ******************** */
/************************************************************************ */
function showAsignacionIndividual(rec,origen, id, panelAsignados)
{
    winAsignacionIndividual="";
    formPanelAsignacionIndividual = "";
    
    if (!winAsignacionIndividual)
    {
		var id_servicio = rec.get("id_servicio");	
        //******** html vacio...
        var iniHtmlVacio1 = '';           
        Vacio1 =  Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
//            width: 600,
            padding: 4,
            layout: 'anchor',
            style: { color: '#000000' }
        });
		
		// storeTareasByProcesoAndTarea = new Ext.data.Store({ 
            // total: 'total',
            // pageSize: 10000,
            // proxy: {
                // type: 'ajax',
                // url : "{{ path('factibilidadinstalacion_getTareasByProcesoAndTarea') }}",
                // reader: {
                    // type: 'json',
                    // totalProperty: 'total',
                    // root: 'encontrados'
                // },
                // extraParams: {
                    // servicioId: id_servicio,
                    // nombreTarea: 'instalacion',
                    // estado: 'Activo'
                // }
            // },
            // fields:
                    // [
                        // {name:'idTarea', mapping:'idTarea'},
                        // {name:'nombreTarea', mapping:'no	mbreTarea'}
                    // ],
            // autoLoad: true
        // });
            
        formPanelAsignacionIndividual = Ext.create('Ext.form.Panel', {
//            width:700,
//            height:750,
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1],
            buttons:[
                {
                    text: 'Guardar',
                    handler: function(){
                        asignarResponsableIndividual(rec,origen, id);                        
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function(){
                        cierraVentanaAsignacionIndividual();
                    }
                }
            ]
        });
        
		connTareas.request({
			method: 'GET',
			url: "../../factibilidad/factibilidad_instalacion/getTareasByProcesoAndTarea",
			params: { servicioId: id_servicio, nombreTarea: 'todas', estado: 'Activo' },
			success: function(response){		
				var data = Ext.JSON.decode(response.responseText.trim());
													
				if(data)
				{
					tareasJS = data.encontrados;
					for(i in tareasJS)
					{      
						//******** hidden id tarea
						var hidden_tarea = new Ext.form.Hidden({
							id: 'hidden_id_tarea_'+i,
							name: 'hidden_id_tarea_'+i,
							value: tareasJS[i]["idTarea"]
						});
						//******** text nombre tarea
						var text_tarea = new Ext.form.Label({
							forId: 'txt_nombre_tarea_'+i,
							style: "font-weight:bold; font-size:14px; color:red; margin-bottom: 15px;",
							layout: 'anchor',
							text: tareasJS[i]["nombreTarea"]
						});  
									
						//******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
						var iniHtml =   '<input type="radio" onchange="cambiarTipoResponsable_Individual('+i+', this.value);" checked="" value="empleado" name="tipoResponsable_'+i+'">&nbsp;Empleado' + 
										'&nbsp;&nbsp;'+
										'<input type="radio" onchange="cambiarTipoResponsable_Individual('+i+', this.value);" value="cuadrilla" name="tipoResponsable_'+i+'">&nbsp;Cuadrilla'+
										'&nbsp;&nbsp;'+
										'<input type="radio" onchange="cambiarTipoResponsable_Individual('+i+', this.value);" value="empresaExterna" name="tipoResponsable_'+i+'">&nbsp;Contratista'+
										'';
					   
						RadiosTiposResponsable =  Ext.create('Ext.Component', {
							html: iniHtml,
							width: 600,
							padding: 10,
							style: { color: '#000000' }
						});
						
						// **************** EMPLEADOS ******************
						Ext.define('EmpleadosList', {
							extend: 'Ext.data.Model',
							fields: [
								{name:'id_empleado', type:'int'},
								{name:'nombre_empleado', type:'string'}
							]
						});           
						eval("var storeEmpleados_"+i+"= Ext.create('Ext.data.Store', { "+
						  "  id: 'storeEmpleados_"+i+"', "+
						  "  model: 'EmpleadosList', "+
						  "  autoLoad: false, "+
						   " proxy: { "+
							 "   type: 'ajax',"+
							"    url : '../../planificar/asignar_responsable/getEmpleados',"+
							 "   reader: {"+
							"        type: 'json',"+
							 "       totalProperty: 'total',"+
							"        root: 'encontrados'"+
							  "  }"+
						  "  }"+
					   " });    ");
						combo_empleados = new Ext.form.ComboBox({
							id: 'cmb_empleado_'+i,
							name: 'cmb_empleado_'+i,
							fieldLabel: "Empleados",
							anchor: '100%',
							queryMode:'remote',
							width: 300,
							emptyText: 'Seleccione Empleado',
							store: eval("storeEmpleados_"+ i),
							displayField: 'nombre_empleado',
							valueField: 'id_empleado',
							layout: 'anchor',
							disabled: false
						});
						
						
						// ****************  EMPRESA EXTERNA  ******************
						Ext.define('EmpresaExternaList', {
							extend: 'Ext.data.Model',
							fields: [
								{name:'id_empresa_externa', type:'int'},
								{name:'nombre_empresa_externa', type:'string'}
							]
						});
						
						eval("var storeEmpresaExterna_"+i+"= Ext.create('Ext.data.Store', { "+
						  "  id: 'storeEmpresaExterna_"+i+"', "+
						  "  model: 'EmpresaExternaList', "+
						  "  autoLoad: false, "+
						   " proxy: { "+
							 "   type: 'ajax',"+
							"    url : '../../planificar/asignar_responsable/getEmpresasExternas',"+
							 "   reader: {"+
							"        type: 'json',"+
							 "       totalProperty: 'total',"+
							"        root: 'encontrados'"+
							  "  }"+
						  "  }"+
					   " });    ");
						combo_empresas_externas = new Ext.form.ComboBox({
							id: 'cmb_empresa_externa_'+i,
							name: 'cmb_empresa_externa_'+i,
							fieldLabel: "Contratista",
							anchor: '100%',
							queryMode:'remote',
							width: 300,
							emptyText: 'Seleccione Contratista',
							store: eval("storeEmpresaExterna_"+ i),
							displayField: 'nombre_empresa_externa',
							valueField: 'id_empresa_externa',
							layout: 'anchor',
							disabled: true
						});
						

						// **************** CUADRILLAS ******************
						Ext.define('CuadrillasList', {
							extend: 'Ext.data.Model',
							fields: [
								{name:'id_cuadrilla', type:'int'},
								{name:'nombre_cuadrilla', type:'string'}
							]
						});            
						eval("var storeCuadrillas_"+i+"= Ext.create('Ext.data.Store', { "+
						  "  id: 'storeCuadrillas_"+i+"', "+
						  "  model: 'CuadrillasList', "+
						  "  autoLoad: false, "+
						   " proxy: { "+
							 "   type: 'ajax',"+
							"    url : '../../planificar/asignar_responsable/getCuadrillas',"+
							 "   reader: {"+
							"        type: 'json',"+
							 "       totalProperty: 'total',"+
							"        root: 'encontrados'"+
							  "  }"+
						  "  }"+
					   " });    ");
						combo_cuadrillas = new Ext.form.ComboBox({
							id: 'cmb_cuadrilla_'+i,
							name: 'cmb_cuadrilla_'+i,
							fieldLabel: "Cuadrilla",
							anchor: '100%',
							queryMode:'remote',
							width: 300,
							emptyText: 'Seleccione Cuadrilla',
							store: eval("storeCuadrillas_"+ i),
							displayField: 'nombre_cuadrilla',
							valueField: 'id_cuadrilla',
							layout: 'anchor',
							disabled: true 
						});  
				
									
						//******** html vacio...
						var iniHtmlVacio = '';           
						Vacio =  Ext.create('Ext.Component', {
							html: iniHtmlVacio,
							width: 600,
							padding: 8,
							layout: 'anchor',
							style: { color: '#000000' }
						});
						
						formPanelAsignacionIndividual.items.add(hidden_tarea);
						formPanelAsignacionIndividual.items.add(text_tarea);
						formPanelAsignacionIndividual.items.add(RadiosTiposResponsable);
						formPanelAsignacionIndividual.items.add(combo_empleados);
						formPanelAsignacionIndividual.items.add(combo_cuadrillas);
						formPanelAsignacionIndividual.items.add(combo_empresas_externas);    
						formPanelAsignacionIndividual.items.add(Vacio);        
						formPanelAsignacionIndividual.doLayout();     
						
						
						Ext.getCmp('cmb_empleado_'+i).setVisible(true);
						Ext.getCmp('cmb_cuadrilla_'+i).setVisible(false);
						Ext.getCmp('cmb_empresa_externa_'+i).setVisible(false);
					}

					winAsignacionIndividual = Ext.widget('window', {
						title: 'Formulario Asignacion Individual',
			//            width: 740,
			//            height:660,
			//            minHeight: 380,
						layout: 'fit',
						resizable: false,
						modal: true,
						closable: false,
						items: [formPanelAsignacionIndividual]
					});	

					winAsignacionIndividual.show();    					
				}
				else{
					Ext.MessageBox.show({
						title: 'Error',
						msg: "Ocurrio un Error en la Obtencion de las Tareas",
						buttons: Ext.MessageBox.OK,
						icon: Ext.MessageBox.ERROR
					 });
				}
			},
			failure: function(result) {
					Ext.MessageBox.show({
						title: 'Error',
						msg: result.responseText,
						buttons: Ext.MessageBox.OK,
						icon: Ext.MessageBox.ERROR
					 });
			}
		});
    
        
        
        /* 
        if(origen == "otro2" && panelAsignados)
        {
            winAsignacionIndividual = Ext.widget('window', {
                title: 'Formulario Asignacion Individual',
                width: 740,
                height:800,
                minHeight: 800,
                layout: 'fit',
                resizable: false,
                modal: true,
                closabled: false,
                items: [formPanelAsignacionIndividual]
            });
        }
        else
        {
            winAsignacionIndividual = Ext.widget('window', {
                title: 'Formulario Asignacion Individual',
                width: 740,
                height:660,
                minHeight: 380,
                layout: 'fit',
                resizable: false,
                modal: true,
                closabled: false,
                items: [formPanelAsignacionIndividual]
            });

        }*/
    }
    
}

function cierraVentanaAsignacionIndividual(){
    winAsignacionIndividual.close();
    winAsignacionIndividual.destroy();
}   