/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var winVerMapa;
var winVerCroquis;
var winRechazarOrden_Factibilidad;
var winFactibilidadMateriales;

var connFactMateriales = new Ext.data.Connection({
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
/************************** VER MAPA ************************************ */
/************************************************************************ */
function showVerMapa(rec){
    winVerMapa="";

    if(rec.get("latitud")!=0 && rec.get("longitud")!=0)
    {
        if (!winVerMapa)
        {
            formPanelMapa = Ext.create('Ext.form.Panel', {
//                width:1010,
//                height:710,
                BodyPadding: 10,
                frame: true,
                items: [
                    {
                        html: "<div id='map_canvas' style='width:575px; height:450px'></div>"
                    }
                ]
            });

            winVerMapa = Ext.widget('window', {
                title: 'Mapa del Punto',
//                width: 1020,
//                height: 720,
//                minHeight: 380,
                layout: 'fit',
                resizable: false,
                modal: true,
                closable: true,
                items: [formPanelMapa]
            });
        }

        winVerMapa.show();
        muestraMapa(rec.get("latitud"), rec.get("longitud"));
    }
    else
    {
        alert('Estas coordenadas son incorrectas!!')
    }
}

function muestraMapa(vlat,vlong){
    var mapa;
    var ciudad = "";
    var markerPto ;

    if((vlat)&&(vlong)){
        var latlng = new google.maps.LatLng(vlat,vlong);
        //var latlng = new google.maps.LatLng(-2.176963, -79.883673);
        var myOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        if(mapa){
            mapa.setCenter(latlng);
        }else{
            mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        }

        if(ciudad=="gye")
            layerCiudad = 'http://157.100.3.122/Coberturas.kml';
        else
            layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';

        //google.maps.event.addListener(mapa, 'dblclick', function(event) {
        if(markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng, 
            map: mapa
        });
        mapa.setZoom(17);
        //  dd2dms(event.latLng.lat(),event.latLng.lng());
        //});
    }
} 

function cierraVentanaMapa(){
    winVerMapa.close();
    winVerMapa.destroy();
    
}

/************************************************************************ */
/************************** VER CROQUIS ********************************* */
/************************************************************************ */
function showVerCroquis(idDetalleSolicitud, rutaImagen){
    winVerCroquis="";

    if (!winVerCroquis)
    {
        formPanelCroquis = Ext.create('Ext.form.Panel', {
//            width:690,
//            height:460,
            BodyPadding: 10,
            frame: true,
            items: [
                {
                    html: rutaImagen
                }
            ]
        });
        
	winVerCroquis = Ext.widget('window', {
            title: 'Croquis del Punto',
//            width: 710,
//            height:480,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: true,
            items: [formPanelCroquis]
        });
    }
    
    winVerCroquis.show();
}

function cierraVentanaCroquis(){
    winVerCroquis.close();
    winVerCroquis.destroy();
}


/************************************************************************ */
/************************* RECHAZAR ORDEN ******************************* */
/************************************************************************ */
function showRechazarOrden_Factibilidad(rec)
{   winRechazarOrden_Factibilidad="";
    formPanelRechazarOrden_Factibilidad = "";
    
    if (!winRechazarOrden_Factibilidad)
    {
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
	
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';           
        CamposRequeridos =  Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: { color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0' }
        });
        
        storeMotivos = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getMotivosRechazo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'ACTIVE'
            }
        },
        fields:
                [
                    {name:'id_motivo', mapping:'id_motivo'},
                    {name:'nombre_motivo', mapping:'nombre_motivo'}
                ],
        autoLoad: true
    });
    
        formPanelRechazarOrden_Factibilidad = Ext.create('Ext.form.Panel', {
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
                        },itemTercerizadora
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos del Rechazo',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [ 
                        {
                            xtype: 'combobox',
                            id: 'cmbMotivo',
                            fieldLabel: '* Motivo',
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField:'nombre_motivo',
                            valueField: 'id_motivo',
                            selectOnTab: true,
                            store: storeMotivos,  
                            queryMode: "local",
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;"
                        },
                        {
                            xtype: 'textareafield',
                            fieldLabel: '* Observacion',
                            name: 'info_observacion',
                            id: 'info_observacion',
                            allowBlank: false,
                            labelStyle: "color:red;"
                        }
                    ]
                }
            ],
            buttons:[
                {
                    text: 'Rechazar',
                    handler: function(){
                        var txtObservacion  = Ext.getCmp('info_observacion').value;   
                        var cmbMotivo       = Ext.getCmp('cmbMotivo').value;   
                        var id_factibilidad = rec.get("id_factibilidad");
                        var intIdServicio   = rec.get("id_servicio");
                        
                        var boolError = false;
                        var mensajeError = "";
                        if(!id_factibilidad || id_factibilidad=="" || id_factibilidad==0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        if(!cmbMotivo || cmbMotivo=="" || cmbMotivo==0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if(!txtObservacion || txtObservacion=="" || txtObservacion==0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }
                           
                        if(!boolError)
                        {
                            connFactMateriales.request({
                                timeout: 900000,
                                method: 'POST',
                                params: { idSolicitud: id_factibilidad, 
                                          idServicio:  intIdServicio,
                                          id_motivo:   cmbMotivo, 
                                          observacion: txtObservacion },
                                url: strUrlRechazarSeguimientoMaterialesExcedentes,
                                success: function(response){			
                                            var text = Ext.decode(response.responseText);
                                            if(text.estado == 'OK')
                                            {   cierraVentanaRechazarOrden_Factibilidad();
                                                Ext.Msg.alert('Mensaje', text.mensaje, function(btn){
                                                        if(btn=='ok'){
                                                            store.load();
                                                        }
                                                });
                                            }
                                            else{
                                                cierraVentanaRechazarOrden_Factibilidad();
                                                Ext.Msg.alert('Alerta', 'Error: ' + text.mensaje);
                                            }
                                    },
                                    failure: function(result) {
                                            Ext.Msg.alert('Alerta', result.responseText);
                                    }
                                                    });
                        }
                        else{
                            Ext.Msg.alert('Alerta','Error: ' + mensajeError);
                        }                         
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function(){
                        cierraVentanaRechazarOrden_Factibilidad();
                    }
                }
            ]
        });
        
	winRechazarOrden_Factibilidad = Ext.widget('window', {
            title: 'Rechazo de Solicitud de Materiales Excedentes',
//            width: 640,
//            height:630,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelRechazarOrden_Factibilidad]
        });
    }
    
    winRechazarOrden_Factibilidad.show();    
}

function cierraVentanaRechazarOrden_Factibilidad(){
    winRechazarOrden_Factibilidad.close();
    winRechazarOrden_Factibilidad.destroy();
}


/************************************************************************ */
/********************* FACTIBILIDAD MATERIALES ************************** */
/************************************************************************ */
function showFactibilidadMateriales(rec, origen)
{
    winFactibilidadMateriales="";

    if (!winFactibilidadMateriales)
    {
        var id_factibilidad = rec.get("id_factibilidad");
        
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
	
        storeFactibilidadMateriales = new Ext.data.Store({ 
            pageSize: 10,
            total: 'total',
            proxy: {
                type: 'ajax',
                url : 'gridFactibilidadMateriales',
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
                        {name:'id_tarea', mapping:'id_tarea'},
                        {name:'id_tarea_material', mapping:'id_tarea_material'},
                        {name:'cod_material', mapping:'cod_material'},
                        {name:'nombre_material', mapping:'nombre_material'},
                        {name:'costo_material', mapping:'costo_material'},
                        {name:'precio_venta_material', mapping:'precio_venta_material'},
                        {name:'cantidad_empresa', mapping:'cantidad_empresa'},
                        {name:'cantidad_estimada', mapping:'cantidad_estimada'},
                        {name:'cantidad_cliente', mapping:'cantidad_cliente'}              
                    ],
            autoLoad: true
        });
                
        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(){
                    // refresh summaries
                    gridFactibilidadMateriales.getView().refresh();
                }
            }
        });
    
        gridFactibilidadMateriales = Ext.create('Ext.grid.Panel', {
//             width: 710,
            height: 150,
            store: storeFactibilidadMateriales,
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
                    id: 'cod_material',
                    header: 'Cod Material',
                    dataIndex: 'cod_material',
                    width: 120,
                    sortable: true
                },        
                {
                    id: 'nombre_material',
                    header: 'Nombre Material',
                    dataIndex: 'nombre_material',
                    width: 270,
                    sortable: true
                },  
               /* {
                    id: 'costo_material',
                    header: 'Costo Material',
                    dataIndex: 'costo_material',
                    width: 90, 
                    align: 'right',
                    sortable: true
                },         
                {
                    id: 'precio_venta_material',
                    header: 'Precio Venta Material',
                    dataIndex: 'precio_venta_material',
                    width: 125,
                    align: 'right',
                    sortable: true
                },    */        
                {
                    id: 'cantidad_empresa',
                    header: 'Cantidad (empresa)',
                    dataIndex: 'cantidad_empresa',
                    width: 120,
                    align: 'right',
                    sortable: true
                },             
                {                        
                    id: 'cantidad_estimada',
                    header: 'Cantidad (estimada)',
                    dataIndex: 'cantidad_estimada',
                    width: 130,
                    align: 'right',
                    sortable: true, 
                    tdCls: 'custom-azul'
                },             
                {
                    id: 'cantidad_cliente',
                    header: 'Cantidad (cliente)',
                    dataIndex: 'cantidad_cliente',
                    width: 110,
                    align: 'right',
                    sortable: true,
                    renderer: function (value, metaData, record, rowIndex, colIndex, store){  
                        var valor_empresa = parseInt(record.data.cantidad_empresa);
                        var valor_estimado = parseInt(record.data.cantidad_estimada);
                        var valor_cliente = parseInt(record.data.cantidad_cliente);
                        
                        if(valor_estimado > valor_empresa)
                        {
                            valor_cliente = parseInt(valor_estimado - valor_empresa);
                        }
                        else
                        {
                            valor_cliente = 0;
                        }
                        
                        if(valor_cliente > 0)
                        {
                            metaData.tdCls = 'custom-rojo';
                        }
                    
                        return valor_cliente;  
                    }
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeFactibilidadMateriales,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            plugins: [cellEditing]
        });
    
        formPanelFactibilidadMateriales = Ext.create('Ext.form.Panel', {
//            width:1050,
//            height:590,
			buttonAlign: 'center',
            BodyPadding: 10,
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
								},
                                {  
                                    xtype: 'fieldset',
                                    title: 'Valor total que asume la empresa',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 20px;",
                                    hidden : prefEmpresa == "TN" ? false : true,
                                    defaults: {
                                        width: '328px'
                                    },
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: '',
                                            name: 'valorAsumeEmpresaV',
                                            id: 'valorAsumeEmpresaV',
                                            value: rec.get("valorAsumeEmpresa"),
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                    ]

                                },
                                {
                                xtype: 'fieldset',
                                title: 'Valor total de facturación del cliente',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 20px;",
                                hidden : prefEmpresa == "TN" ? false : true,
                                defaults: {
                                    width: '328px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: '',
                                        name: 'valorAsumeCliente',
                                        id: 'valorAsumeCliente',
                                        value: rec.get("valorAsumeCliente"),
                                        allowBlank: false,
                                        readOnly: true
                                    }]
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
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Id Solicitud',
                                    name: 'id_detalle_solicitud',
                                    id: 'id_detalle_solicitud',
                                    value: rec.get("id_factibilidad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Vendedor(a)',
                                    name: 'nombre_vendedor',
                                    id: 'nombre_vendedor',
                                    value: rec.get("vendedor"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {  
                                xtype: 'fieldset',
                                title: 'Forma de contacto de ' + rec.get("vendedor"),
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 15px;",
                                defaults: {
                                    width: '330px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: rec.get("descripcionFormaContacto"),
                                        name: 'valor',
                                        id: 'valor',
                                        value: rec.get("valor"),
                                        allowBlank: false,
                                        readOnly: true
                                    }]
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Asistente',
                                    name: 'nombre_asistente',
                                    id: 'nombre_asistente',
                                    value: rec.get("nombreAsistente"),
                                    allowBlank: false,
                                    readOnly: true,
                                    hidden : prefEmpresa == "TN" ? false : true,
                                },
                                {
                                xtype: 'fieldset',
                                title: 'Forma de contacto de ' + rec.get("nombreAsistente"),
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 15px;",
                                hidden : prefEmpresa == "TN" ? false : true,
                                defaults: {
                                    width: '330px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: rec.get("descripcionFormaContactoAsistente"),
                                        name: 'valorAsistente',
                                        id: 'valorAsistente',
                                        value: rec.get("valorAsistente"),
                                        allowBlank: false,
                                        readOnly: true
                                    }]
                                },itemTercerizadora
							]
						}
					]
				},
		//gridFactibilidadMateriales,
		{
			xtype: 'fieldset',
			title: 'Datos Adicionales',
			defaultType: 'textfield',
			style: "font-weight:bold; margin-top: 15px;",
			defaults: {
				width: '800px'
			},
			items: [ {
                                    xtype: 'textareafield',
                                    fieldLabel: 'Observacion',
                                    name: 'materiales_observacion',
                                    id: 'materiales_observacion',
                                    allowBlank: true
                                 }
				]
		}
			],
            buttons:[
                {
                    text: 'Aprobar',
                    handler: function(){     
                        var id_factibilidad = rec.get("id_factibilidad");
                        
                        var boolError = false;
                        var mensajeError = "";   
                        
                        if(!id_factibilidad || id_factibilidad=="" || id_factibilidad==0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }        
                                                
                        if(!boolError)
                        {
                            showAprobarOrden_Factibilidad(rec);
                            cierraVentanaFactibilidadMateriales();
                        }
                        else{
                            Ext.Msg.alert('Alerta','Error: ' + mensajeError);
                        }                         
                    }
                },
                // {
                    // text: 'Rechazar',
                    // handler: function(){     
                        // var id_factibilidad = rec.get("id_factibilidad");
                        
                        // var boolError = false;
                        // var mensajeError = "";   
                        
                        // if(!id_factibilidad || id_factibilidad=="" || id_factibilidad==0)
                        // {
                            // boolError = true;
                            // mensajeError += "El id del Detalle Solicitud no existe.\n";
                        // }        
                                                
                        // if(!boolError)
                        // {
							// cierraVentanaFactibilidadMateriales();
							// showRechazarOrden_Factibilidad(rec);
                        // }
                        // else{
                            // Ext.Msg.alert('Alerta','Error: ' + mensajeError);
                        // }                         
                    // }
                // },
                {
                    text: 'Cerrar',
                    handler: function(){
                        cierraVentanaFactibilidadMateriales();
                    }
                }
            ]
        });
        
	winFactibilidadMateriales = Ext.widget('window', {
            title: 'Aprobacion de Excedentes de Materiales',
//            width: 1060,
//            height: 630,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelFactibilidadMateriales]
        });
    }                        
                         
    winFactibilidadMateriales.show();    
}

function cierraVentanaFactibilidadMateriales(){
    winFactibilidadMateriales.close();
    winFactibilidadMateriales.destroy();
}

/************************************************************************ */
/************************* APROBAR ORDEN ******************************* */
/************************************************************************ */
function showAprobarOrden_Factibilidad(rec)
{
	var id_factibilidad = rec.get("id_factibilidad");
	var txtObservacion  = Ext.getCmp('materiales_observacion').value;   
        var intIdServicio   = rec.get("id_servicio");
	
	var boolError = false;
	var mensajeError = "";   
	
	if(!id_factibilidad || id_factibilidad=="" || id_factibilidad==0)
	{
		boolError = true;
		mensajeError += "El id del Detalle Solicitud no existe.\n";
	}        
		
	if(!boolError)
	{	
		connFactMateriales.request({
			method: 'POST',
            timeout: 900000,
			params :{ idSolicitud: id_factibilidad, 
                                  idServicio:  intIdServicio, 
                                  observacion: txtObservacion },
			url: strUrlAprobarSeguimientoMaterialesExcedentes,
			success: function(response){			
                            var text = Ext.decode(response.responseText);
                            if(text.estado == 'OK')
                            {
                                Ext.Msg.alert('Mensaje', text.mensaje, function(btn){
                                        if(btn=='ok'){
                                            store.load();
                                        }
                                });
                            }
                            else{
                                Ext.Msg.alert('Alerta', 'Error: ' + text.mensaje);
                            }
			},
			failure: function(result) {
				Ext.Msg.alert('Alerta', result.responseText);
			}
		});
	}
	else{
		Ext.Msg.alert('Alerta','Error: ' + mensajeError);
	} 							
}