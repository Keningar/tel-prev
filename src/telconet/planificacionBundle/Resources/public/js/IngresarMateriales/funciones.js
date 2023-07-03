/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var winMaterialesUtilizados;

var connMaterialesUtilizados = new Ext.data.Connection({
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
/********************* MATERIALES UTILIZADO ************************** */
/************************************************************************ */
function validarNumModemsUtilizados()
{
    var materiales = gridMaterialesUtilizados.getStore().getCount();
	var numModemsUsados = 0;
    if(materiales > 0)
    {
        for(var i=0; i < gridMaterialesUtilizados.getStore().getCount(); i++)
        {
            var subgrupo = gridMaterialesUtilizados.getStore().getAt(i).data.subgrupo_material;
			if(subgrupo=="MODEM"){
				var cantidad_usada = parseInt(gridMaterialesUtilizados.getStore().getAt(i).data.cantidad_estimada);
				if(cantidad_usada > 0)
				{
					numModemsUsados = numModemsUsados + cantidad_usada;
				}
				
				if(numModemsUsados>1){
					for(var i=0; i < gridMaterialesUtilizados.getStore().getCount(); i++)
					{
						var subgrupo = gridMaterialesUtilizados.getStore().getAt(i).data.subgrupo_material;
						if(subgrupo=="MODEM"){
							gridMaterialesUtilizados.getStore().getAt(i).data.cantidad_estimada = 0;
							gridMaterialesUtilizados.getStore().getAt(i).data.cantidad_excedente = 0;
						}
					}
					Ext.Msg.alert('Error ','Error: ' + "Solo puede registrar el uso de un Modem. Por favor ingresar nuevamente.");
					return numModemsUsados;
				}
			}
            
        }
    }
    else
    {
        Ext.Msg.alert('Error ','Error: ' + "No se han registrado los materiales");
        return numModemsUsados;
    }
  
}
function validarMaterialesUtilizados()
{
    var materiales = gridMaterialesUtilizados.getStore().getCount();
    if(materiales > 0)
    {
        var boolVacio = true;
		var numModems = 0;
        for(var i=0; i < gridMaterialesUtilizados.getStore().getCount(); i++)
        {
            var cantidad_usada = parseInt(gridMaterialesUtilizados.getStore().getAt(i).data.cantidad_estimada);
            var subgrupo = gridMaterialesUtilizados.getStore().getAt(i).data.subgrupo_material;
            
			if(cantidad_usada == 0 && subgrupo!="RADIO" && subgrupo!="MODEM" && subgrupo!="CABLE DE RED" && subgrupo!="CABLE ACOMETIDA" )
            {
                boolVacio = false;
				break;
            }
			// if(cantidad_usada > 0 && subgrupo=="MODEM"){
				// numModems++;
			// }
        }
        
		// if(numModems==0){
			// Ext.Msg.alert('Error ','Error: ' + "Debe ingresar por lo menos la cantidad de un modem.");
            // return false;
		// }
	boolVacio = true;	
        if(boolVacio)
        {
            var materialesJson = retornaMaterialesUtilizados();
            return materialesJson;
        }
        else
        {
            Ext.Msg.alert('Error ','Error: ' + "En un registro no ingreso la cantidad usada real.");
            return false;
        }
    }
    else
    {
        Ext.Msg.alert('Error ','Error: ' + "No se han registrado los materiales");
        return false;
    }
  
}

function retornaMaterialesUtilizados()
{
    //Ext.get('materiales_editados').dom.value = "";
    var materiales_json = false;
    var array_materiales = new Object();
    array_materiales['total'] =  gridMaterialesUtilizados.getStore().getCount();
    array_materiales['materiales'] = new Array();
  
    var array_data = new Array();
    for(var i=0; i < gridMaterialesUtilizados.getStore().getCount(); i++)
    {
        array_data.push(gridMaterialesUtilizados.getStore().getAt(i).data);
    }
  
    array_materiales['materiales'] = array_data;
    materiales_json = Ext.JSON.encode(array_materiales);
    //Ext.get('materiales_editados').dom.value = Ext.JSON.encode(array_materiales);
    
    return materiales_json;
}

function showMaterialesUtilizados(rec)
{
    winMaterialesUtilizados="";

    if (!winMaterialesUtilizados)
    {
        var id_factibilidad = rec.get("id_factibilidad");
        var first_time = true;
		storeResponsables = new Ext.data.Store({ 
			total: 'total',
			pageSize: 10000,
			listeners: {
			load: function() {
					var valor = Ext.getCmp("cmb_responsable").value;
				 
					 if(valor>0){
					 }else{
						Ext.getCmp("cmb_responsable").setValue(rec.data.responsable);
					}
				}
			},
			proxy: {
				type: 'ajax',
				url : '../../planificar/asignar_responsable/'+rec.data.url_responsable,
				reader: {
					type: 'json',
					totalProperty: 'total',
					root: 'encontrados'
				},
				actionMethods: {
				    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
				},
				extraParams: {
					// nombre: this.nombreElemento,
					// estado: 'ACTIVE',
					// elemento: 'RADIO'
				}
			},
			fields:
					[
						{name: rec.data.fieldIdResponsable, mapping:rec.data.fieldIdResponsable},
						{name: rec.data.fieldValueResponsable, mapping: rec.data.fieldValueResponsable}
					],
			autoLoad: true
		});
			
		combo_responsables = new Ext.form.ComboBox({
            id: 'cmb_responsable',
            name: 'cmb_responsable',
            fieldLabel: "Nombre: ",
            anchor: '30%',
            queryMode:'local',
            width: 350,
            store: storeResponsables,
            displayField: rec.data.fieldValueResponsable,
            valueField: rec.data.fieldIdResponsable,
            layout: 'anchor',
            disabled: false
        });
        if(rec.get("tercerizadora")){
	      itemTercerizadora = new Ext.form.TextField({
			      xtype: 'textfield',
			      fieldLabel: 'Tercerizadora',
			      name: 'fieldtercerizadora',
			      id: 'fieldtercerizadora',
			      value: rec.get("tercerizadora"),
			      allowBlank: false,
			      readOnly : true
	      });
	}else{
	      itemTercerizadora = Ext.create('Ext.Component', {
		  html: "<br>",
	      });
	}
        storeMaterialesUtilizados = new Ext.data.Store({ 
            pageSize: 40,
            total: 'total',
			listeners: {
				load: function() {
					first_time = false;
				}	
			},
            proxy: {
                type: 'ajax',
                url : 'gridMaterialesUtilizados',
		timeout: 400000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    id_detalle_solicitud: id_factibilidad,
                    estado: 'Todos'
                }
            },
            fields:
                    [   
                        {name:'id_detalle_solicitud', mapping:'id_detalle_solicitud'},
                        {name:'id_detalle_sol_material', mapping:'id_detalle_sol_material'},
                        {name:'id_detalle', mapping:'id_detalle'},
                        {name:'id_tarea', mapping:'id_tarea'},
                        {name:'id_tarea_material', mapping:'id_tarea_material'},
                        {name:'subgrupo_material', mapping:'subgrupo_material'},
                        {name:'cod_material', mapping:'cod_material'},
                        {name:'nombre_material', mapping:'nombre_material'},
                        {name:'costo_material', mapping:'costo_material'},
                        {name:'precio_venta_material', mapping:'precio_venta_material'},
                        {name:'cantidad_empresa', mapping:'cantidad_empresa'},
                        {name:'cantidad_estimada', mapping:'cantidad_estimada'},
                        {name:'cantidad_cliente', mapping:'cantidad_cliente'},
                        {name:'cantidad_usada', mapping:'cantidad_usada'},
                        {name:'cantidad_excedente', mapping:'cantidad_excedente'}, 
                        {name:'facturar', mapping:'facturar', type: 'bool'}               
                    ],
            autoLoad: true
        });
                
        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(){
                    // refresh summaries
                    gridMaterialesUtilizados.getView().refresh();
                }
            }
        });
    
        gridMaterialesUtilizados = Ext.create('Ext.grid.Panel', {
            height: 250,
            store: storeMaterialesUtilizados,
            loadMask: true,
            frame: false,
            columns:[
                {
                    id: 'id_detalle_solicitud',
                    header: 'IdDetalleSolicitud',
                    dataIndex: 'id_detalle_solicitud',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_detalle_sol_material',
                    header: 'IdDetalleSolMaterial',
                    dataIndex: 'id_detalle_sol_material',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_detalle',
                    header: 'IdDetalle',
                    dataIndex: 'id_detalle',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_tarea',
                    header: 'IdTarea',
                    dataIndex: 'id_tarea',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'id_tarea_material',
                    header: 'IdTareaMaterial',
                    dataIndex: 'id_tarea_material',
                    hidden: true,
                    hideable: false
                },   
                {
                    id: 'id_tarea_material',
                    header: 'IdTareaMaterial',
                    dataIndex: 'id_tarea_material',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'subgrupo_material',
                    header: 'subgrupoMaterial',
                    dataIndex: 'subgrupo_material',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'cod_material',
                    header: 'Cod Material',
                    dataIndex: 'cod_material',
                    width: 100,
                    sortable: true
                },        
                {
                    id: 'nombre_material',
                    header: 'Nombre Material',
                    dataIndex: 'nombre_material',
                    width: 350,
                    sortable: true
                },  
                // {
                    // id: 'costo_material',
                    // header: 'Costo Material',
                    // dataIndex: 'costo_material',
                    // width: 90, 
                    // align: 'right',
                    // sortable: true
                // },         
                // {
                    // id: 'precio_venta_material',
                    // header: 'Precio Venta Material',
                    // dataIndex: 'precio_venta_material',
                    // width: 125,
                    // align: 'right',
                    // sortable: true
                // },            
                {
                    id: 'cantidad_empresa',
                    header: 'Cantidad (empresa)',
                    dataIndex: 'cantidad_empresa',
                    width: 120,
                    align: 'right',
                    sortable: true
                },             
                {                        
                    id: 'cantidad_usada',
                    header: 'Cantidad (usada real)',
                    dataIndex: 'cantidad_estimada',
                    width: 130,
                    align: 'right',
                    sortable: true, 
                    tdCls: 'custom-azul',
                    editor: new Ext.form.NumberField(
                    {
                        allowBlank: false,
                        allowNegative: false
                    }) 
                },              
                {
                    id: 'cantidad_cliente',
                    header: 'Cantidad (Excdt)',
                    dataIndex: 'cantidad_cliente',
                    width: 105,
                    align: 'right',
                    sortable: true,
                    renderer: function (value, metaData, record, rowIndex, colIndex, store){  
						
						var valor_empresa = parseInt(record.data.cantidad_empresa);
                        var valor_excdt = parseInt(record.data.cantidad_cliente);
                        var valor_cliente = parseInt(record.data.cantidad_cliente);
                        var valor_usado = parseInt(record.data.cantidad_estimada);                        
                        var valor_facturar = parseInt(record.data.cantidad_excedente);                       
                        var valor_excedente = parseInt(record.data.cantidad_excedente);                       
                        var subgrupo = record.data.subgrupo_material;
                        // var valor_cobertura = valor_empresa + valor_cliente;
						// alert(valor_usado+" > "+valor_empresa);
                        if(valor_usado > valor_empresa)
                        {
                            valor_excedente = parseInt(valor_usado - valor_empresa);
							 
                        }
                        else
                        {
                            valor_excedente = 0;
                        }
						
                        if(subgrupo=="MODEM"){
							var num_modems = validarNumModemsUtilizados();
							if(num_modems>1)
								valor_excedente = 0;
                        }
						
                        if(valor_excedente > 0)
                        {
                            metaData.tdCls = 'custom-rojo';
							record.data.cantidad_cliente = valor_excedente;
							if(first_time){
								record.data.cantidad_excedente = valor_excedente;
							}
							// alert(valor_excdt+"=="+valor_facturar);
							if(valor_excdt==valor_facturar){
								record.data.cantidad_excedente = valor_excedente;
							}	
						}else{
							record.data.cantidad_excedente = 0;
							record.data.cantidad_cliente = 0;
                        }
						record.data.cantidad_usada = valor_usado;
						return valor_excedente;  
							
                    }
                }, 
                {
                    id: 'cantidad_excedente',
                    header: 'Cantidad (Facturar)',
                    dataIndex: 'cantidad_excedente',
                    width: 125,
                    align: 'right',
                    sortable: true,
					editor: new Ext.form.NumberField(
                    {
                        allowBlank: false,
                        allowNegative: false
                    }),
					tdCls: 'custom-rojo',
                    // renderer: function (value, metaData, record, rowIndex, colIndex, store){
						// var valor_empresa = parseInt(record.data.cantidad_empresa);
                        // var valor_cliente = parseInt(record.data.cantidad_cliente);
                        // var valor_usado = parseInt(record.data.cantidad_estimada);                        
                        // var valor_excedente = parseInt(record.data.cantidad_excedente);                       
                        // var subgrupo = record.data.subgrupo_material;
                        // var valor_cobertura = valor_empresa + valor_cliente;
						// alert(valor_usado+" > "+valor_empresa);
                        // if(valor_usado > valor_empresa)
                        // {
                            // valor_excedente = parseInt(valor_usado - valor_empresa);
							 
                        // }
						// else
                        // {
                            // valor_excedente = 0;
                        // }
						
						// if(subgrupo=="MODEM"){
							// var num_modems = validarNumModemsUtilizados();
							// if(num_modems>1)
								// valor_excedente = 0;
						// }
					
						// if(valor_excedente > 0)
						// {
							// metaData.tdCls = 'custom-rojo';
						// }
						
						// return valor_excedente; 
                    // }
                }, 
                {
                    xtype: 'checkcolumn',
                    header: 'Facturar?',
                    id: 'facturar',
                    dataIndex: 'facturar',
                    width: 70,
                    renderer : function(v, p, record){  
                        var valor_empresa = parseInt(record.data.cantidad_empresa);
                        var valor_cliente = parseInt(record.data.cantidad_cliente);
                        var valor_usado = parseInt(record.data.cantidad_estimada);                        
                        var valor_excedente = parseInt(record.data.cantidad_excedente);                       
                        var subgrupo = record.data.subgrupo_material;
                        // var valor_cobertura = valor_empresa + valor_cliente;
						// alert(valor_usado+" > "+valor_empresa);
                        if(valor_usado > valor_empresa)
                        {
                            valor_excedente = parseInt(valor_usado - valor_empresa);
							 
                        }
                        else
                        {
                            valor_excedente = 0;
                        }
                        
						if(subgrupo=="MODEM"){
							var num_modems = validarNumModemsUtilizados();
							if(num_modems>1)
								valor_excedente = 0;
						}
                        
                        if(valor_excedente > 0)
                        {  
							v = true;
                            p.css += ' x-grid-checkheader'; 
							record.data.facturar = true;
                            return '<div class="x-grid-checkheader x-grid-checkheader'+(v?'-checked':'')+' x-grid3-cc-'+this.id+'">&#160;</div>';
                        }
                        
                        record.data.facturar = false;
                        p.css += ' x-grid3-check-col-td'; 
                        return '<div class="x-grid3-check-col'+(v?'-on':'')+' x-grid3-cc-'+this.id+'">&#160;</div>';
                    }                  
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeMaterialesUtilizados,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            plugins: [cellEditing]
        });
		
        formPanelMaterialesUtilizados = Ext.create('Ext.form.Panel', {
            //width:1240,
           // height:590,
            BodyPadding: 10,
			autoScroll: true,
			buttonAlign: 'center',
            frame: true,
            items: [ 
				{
					xtype: 'panel',
					border:false,
					frame : true,
					layout: { type: 'hbox', align: 'stretch' },
					items: [
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
								},
								{
								    xtype: 'textfield',
								    fieldLabel: 'Es Recontratacion',
								    name: 'es_recontratacion',
								    id: 'es_recontratacion',
								    value: rec.get("esRecontratacion"),
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
								},
								{
									xtype: 'textfield',
									fieldLabel: 'Tipo Orden',
									name: 'tipo_orden_servicio',
									id: 'tipo_orden_servicio',
									value: rec.get("tipo_orden"),
									allowBlank: false,
									readOnly : true
								},itemTercerizadora,
								{
									xtype: 'textareafield',
									fieldLabel: 'Observacion de la Aprobacion de Materiales',
									name: 'materiales_observacion',
									id: 'materiales_observacion',
									value: rec.get("observacion_excedente"),
									allowBlank: true,
									readOnly : true
								}
							]
						}
					]
				}
				,
				{
						xtype: 'fieldset',
						title: 'Custodio Asignado',
						defaultType: 'textfield',
						style: "font-weight:bold; margin-top: 15px;",
						defaults: {
							width: '350px'
						},
						items: [ combo_responsables ] 
					} ,gridMaterialesUtilizados ],
            buttons:[
                {
                    text: 'Guardar',
                    handler: function(){     
                        var id_factibilidad = rec.get("id_factibilidad");
                        var id_responsable = Ext.getCmp('cmb_responsable').value;  
                        var boolError = false;
                        var mensajeError = "";   
                        
                        if(!id_factibilidad || id_factibilidad=="" || id_factibilidad==0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }        
                                                
                        if(!boolError)
                        {
                            var boolError2 = false;
                            var jsonMateriales = validarMaterialesUtilizados();
                            if(!jsonMateriales)
                            {
                                boolError2 = true;
                            }                          
                                                
                            if(!boolError2)
                            {
								connMaterialesUtilizados.request({
	                                url: "finalizarAjax",
	                                method: 'post',
                                    params: { id: id_factibilidad,id_responsable: id_responsable, materiales: jsonMateriales },
									success: function(response){			
										var text = response.responseText;
										
										text = text.toString();
										
										var n = text.indexOf("finalizo");
										
										if(n>0)
										{
											cierraVentanaMaterialesUtilizados();
											Ext.Msg.alert('Mensaje', text, function(btn){
												if(btn=='ok'){
			                                        store.load();
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
                        }
                        else{
                            Ext.Msg.alert('Alerta','Error: ' + mensajeError);
                        }                         
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function(){
                        cierraVentanaMaterialesUtilizados();
                    }
                }
            ]
        });
        
	winMaterialesUtilizados = Ext.widget('window', {
            title: 'Finalizar - Materiales Utilizados',
            width: 1030,
            height: 650,
            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelMaterialesUtilizados]
        });
    }                        
                         
    winMaterialesUtilizados.show();    
}

function cierraVentanaMaterialesUtilizados(){
    winMaterialesUtilizados.close();
    winMaterialesUtilizados.destroy();
}