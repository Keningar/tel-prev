            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;



function showCruzar(id_anticipo,valoranticipo,idpunto) {
   
    winDetalle="";
            if(!winDetalle) {

            Ext.define('valoresFacturaModel', {
                 extend: 'Ext.data.Model',
                 fields: [
                     {name:'totalPagos', type:'string'},
                     {name:'valorFactura', type:'string'}
                 ]
             });
             storeValoresFact = Ext.create('Ext.data.Store', {
                     model: 'valoresFacturaModel',
                     autoLoad: false,
                     proxy: {
                         type: 'ajax',
                         url : url_valores_fact,
                         reader: {
                             type: 'json',
                             root: 'datosFactura'
                         }
                     },
                     listeners: {
                                     load: function(store){
                                         Ext.ComponentQuery.query('textfield[name=saldo]')[0].setValue('');
                                         store.each(function(record) {
                                         //console.log('load storeValoresFact');    
                                         
                                         Ext.ComponentQuery.query('textfield[name=saldo]')[0].setValue(record.data.valorFactura-record.data.totalPagos);
                                              
                                         });
                             }
                     } 
             }); 

            //CREAMOS DATA STORE PARA FACTURAS
            Ext.define('modelFacturas', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idfactura', type: 'string'},
                    {name: 'numero',  type: 'string'}                
                ]
            });			
            var facturas_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelFacturas",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_facturas,
		        reader: {
		            type: 'json',
		            root: 'facturas'
                        }
                    }
            });	
            var facturas_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: facturas_store,
                labelAlign : 'left',
                //id:'idfactura',
                name: 'idfactura',
		valueField:'idfactura',
                displayField:'numero',
                fieldLabel: 'Facturas',
		width: 325,
		triggerAction: 'all',
		selectOnFocus:true,
		lastQuery: '',
		mode: 'local',
		allowBlank: true,	
					
		listeners: {
                    select:
                    function(e) {
                        //console.log(e.value);
                        //alert(Ext.getCmp('idestado').getValue());
                        //estado_id = Ext.getCmp('idestado').getValue();
                        //console.log(Ext.ComponentQuery.query('combobox[name=idfactura]')[0].value);
                        storeValoresFact.load({params: {fact:e.value}});
                       
                    },
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){ 
                            //estado_id='';
                            facturas_store.removeAll();
                            facturas_store.load();
                        }
                    }			
		}
            });                
          	

        var form = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,

            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 130,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            url: url_cruzar,
            items: [
            facturas_cmb,
            {
                xtype: 'textfield',
                fieldLabel: 'Saldo Factura',
                labelAlign: 'left',
                name: 'saldo',
                value: ''
            },            
            {
                xtype: 'textfield',
                fieldLabel: 'Anticipo',
                labelAlign: 'left',                
                name: 'valoranticipo',
                value: valoranticipo
            },            
            {
                xtype: 'hiddenfield',
                name: 'idanticipo',
                value: id_anticipo
            },            
            {
                xtype: 'hiddenfield',
                name: 'idpunto',
                value: idpunto
            }             
            ],
            buttons: [{
                text: 'Cancel',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }, {
                text: 'Grabar',
                name: 'grabar',
                //disabled: true,
                //hidden: true,
                handler: function() {
                var form1 = this.up('form').getForm();
				var mensaje='';
                    if (form1.isValid()) {

                    
                    if(Ext.ComponentQuery.query('textfield[name=saldo]')[0].value==''){
                        Ext.Msg.alert('Alerta ','Seleccione una factura por favor.');
                    }
                    else{
                        
                    var valor=storeValoresFact.getAt(0).data.valorFactura-storeValoresFact.getAt(0).data.totalPagos-valoranticipo;
                    if(valor<0){
                        Ext.Msg.alert('Alerta ','El pago excede el saldo de la factura, por favor seleccionar otra factura o cruce otro anticipo.');
                    }
                    else
                    {    
                    form1.submit({
                        waitMsg: "Procesando",
                        success: function(form1, action) {
							if(action.result.msg=="cerrar-conservicios"){
									mensaje='Se proceso el pago y el cliente ya no tiene saldos adeudados. Se procedera a realizar la reactivacion.';
									Ext.MessageBox.show({
										icon: Ext.Msg.INFO,
										width:500,
										height: 300,
										title:'Mensaje del Sistema',
										msg: mensaje,
										buttonText: {yes: "Ok"},
										fn: function(btn){
											if(btn=='yes'){

												//REALIZA REACTIVACION MASIVA
												//-----------------------------
											   $.ajax({
														type: "POST",
														data: "param=" + action.result.servicios,
														url: url_reactivacion_masiva,
														success: function(msg){
															if (msg != ''){
																if(msg=="OK"){
																	Ext.MessageBox.show({
																		icon: Ext.Msg.INFO,
																		width:500,
																		height: 300,
																		title:'Mensaje del Sistema',
																		msg: 'Se proceso la reactivacion masiva de los servicios con exito.',
																		buttonText: {yes: "Ok"},
																		fn: function(btn){
																			if(btn=='yes'){
																				if (store){store.load();}
																				form1.reset();
																				form1.destroy();
																				winDetalle.close();
																			}
																		}
																	});
																}else{
																	Ext.MessageBox.show({
																		icon: Ext.Msg.ERROR,
																		width:500,
																		height: 300,
																		title:'Mensaje del Sistema',
																		msg: 'No se proceso la reactivacion de los servicios, por favor consultar con el administrador.',
																		buttonText: {yes: "Ok"},
																		fn: function(btn){
																			if(btn=='yes'){
																				if (store){store.load();}
																				form1.reset();
																				form1.destroy();
																				winDetalle.close();
																			}
																		}
																	});
																}					
														   }
														   else
														   {
																	Ext.MessageBox.show({
																		icon: Ext.Msg.ERROR,
																		width:500,
																		height: 300,
																		title:'Mensaje del Sistema',
																		msg: 'No se pudo procesar la reactivacion de los servicios, por favor consultar con el administrador.',
																		buttonText: {yes: "Ok"},
																		fn: function(btn){
																			if(btn=='yes'){
																				if (store){store.load();}
																				form1.reset();
																				form1.destroy();
																				winDetalle.close();
																			}
																		}
																	});
														   }
														}          
												});	
											}
										}
									});	
	
							}else{
								if(action.result.msg=="cerrar-sinservicios"){
									mensaje='Se registro el pago con exito y el cliente ya no tiene saldos adeudados. No se encontro servicios para reactivar, por favor consultar con el administrador.';
									Ext.MessageBox.show({
										icon: Ext.Msg.INFO,
										width:500,
										height: 300,
										title:'Mensaje del Sistema',
										msg: mensaje,
										buttonText: {yes: "Ok"},
										fn: function(btn){
											if(btn=='yes'){
												if (store){store.load();}
												form1.reset();
												form1.destroy();
												winDetalle.close();												
											}
										}
									});																	
								}else{
									if(action.result.msg=="nocerrar"){
										mensaje='Se registro el pago con exito pero el cliente aun tiene saldos adeudados.';
										Ext.MessageBox.show({
											icon: Ext.Msg.INFO,
											width:500,
											height: 300,
											title:'Mensaje del Sistema',
											msg: mensaje,
											buttonText: {yes: "Ok"},
											fn: function(btn){
												if(btn=='yes'){
													if (store){store.load();}
													form1.reset();
													form1.destroy();
													winDetalle.close();													
												}
											}
										});								
									}
										
								}							
							
							}
                        },
                        failure: function(form1, action) {
                            console.log(action.result.errors.error);                            
							Ext.MessageBox.show({
								icon: Ext.Msg.INFO,
								width:500,
								height: 300,
								title:'Mensaje del Sistema',
								msg:'No se pudo procesar el pago, por favor consulte con el administrador.',
								buttonText: {yes: "Ok"},
								fn: function(btn){
									if(btn=='yes'){
										if (store){
												store.load();
										}
										form1.reset();
										form1.destroy();	
										winDetalle.close();
									}
								}
							});	
                        }
                    });

                    }
                    }
                   }
                }	
            }]
        });

        winDetalle = Ext.widget('window', {
            title: 'Cruzar Anticipos',
            closeAction: 'hide',
            closable: false,
            width: 350,
            height: 240,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });

    }

    winDetalle.show();

}



            Ext.onReady(function(){

            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325
            });


            //CREAMOS DATA STORE PARA EMPLEADOS
            Ext.define('modelEstado', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idestado', type: 'string'},
                    {name: 'codigo',  type: 'string'},
                    {name: 'descripcion',  type: 'string'}                    
                ]
            });			
            var estado_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelEstado",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_estados,
		        reader: {
		            type: 'json',
		            root: 'estados'
                        }
                    }
            });	
            var estado_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: estado_store,
                labelAlign : 'left',
                id:'idestado',
                name: 'idestado',
		valueField:'descripcion',
                displayField:'descripcion',
                fieldLabel: 'Estado',
		width: 325,
		triggerAction: 'all',
		selectOnFocus:true,
		lastQuery: '',
		mode: 'local',
		allowBlank: true,	
					
		listeners: {
                    select:
                    function(e) {
                        //alert(Ext.getCmp('idestado').getValue());
                        estado_id = Ext.getCmp('idestado').getValue();
                    },
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){ 
                            estado_id='';
                            estado_store.removeAll();
                            estado_store.load();
                        }
                    }			
		}
            });

                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'int'},
                        {name:'tipo', type: 'string'},
                            {name:'numero', type: 'string'},
                            {name:'punto', type: 'string'},
							{name:'idpunto', type: 'string'},
                            {name:'total', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'usuarioCreacion', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'linkVer', type: 'string'}
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_grid,
                        reader: {
                            type: 'json',
                            root: 'pagos',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
								store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
								store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
                                store.getProxy().extraParams.estado= Ext.getCmp('idestado').getValue();     
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                store.load({params: {start: 0, limit: 10}});    



                 var sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                        selectionchange: function(selectionModel, selected, options){
                            arregloSeleccionados= new Array();
                            Ext.each(selected, function(record){
                                    //arregloSeleccionados.push(record.data.idOsDet);
                    });			
                            //console.log(arregloSeleccionados);

                        }
                    }
                });


                var listView = Ext.create('Ext.grid.Panel', {
                    width:800,
                    height:275,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        //tbfill -> alinea los items siguientes a la derecha
                                        { xtype: 'tbfill' },
                                        /*{
                                        iconCls: 'icon_add',
                                        text: 'Add',    
                                        scope: this,
                                        handler: function(){}
                                    }, {
                                        iconCls: 'icon_delete',
                                        text: '',
                                        disabled: false,
                                        itemId: 'delete',
                                        scope: this,
                                        handler: function(){ eliminarAlgunos();}
                                    }*/]}],                    
                    renderTo: Ext.get('lista_pagos'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando clientes {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
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
                            }
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Tipo',
                        width: 150,
                        dataIndex: 'tipo'
                    },                        
                            {
                        text: 'Numero',
                        width: 150,
                        dataIndex: 'numero'
                    },{
                        text: 'Punto',
                        width: 115,
                        dataIndex: 'punto'
                    },{
                        text: 'Total',
                        dataIndex: 'total',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        flex: 85			
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 60
                    },{
                        text: 'Acciones',
                        width: 90,
                        renderer: renderAcciones
                    }
                    
                    ]
                });            

            
            function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';
                    if((record.data.tipo=='Anticipo') && (record.data.estado=='Pendiente'))
                        iconos=iconos+'<b><a href="#" onClick="showCruzar('+record.data.id+','+record.data.total+','+record.data.idpunto+')" title="Cruzar Anticipo" class="button-grid-cruzar"></a></b>';
                    return Ext.String.format(
                                    iconos,
                        value
                    );
            }



            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                //bodyBorder: false,
                border:false,
                //border: '1,1,0,1',
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 5,
                    align: 'left'
                },
                bodyStyle: {
                            background: '#fff'
                },                     
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:10px'
                },
                collapsible : true,
                collapsed: true,
                width: 800,
                title: 'Criterios de busqueda',
                buttons: [
                        {
                            text: 'Buscar',
                            iconCls: "icon_search",
                            handler: Buscar
                        },
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: Limpiar
                        }
                ],                
                items: [
                                {html:"&nbsp;",border:false,width:50},
                                DTFechaDesde,
                                {html:"&nbsp;",border:false,width:50},
                                DTFechaHasta,
                                {html:"&nbsp;",border:false,width:50},
                                {html:"&nbsp;",border:false,width:50},
                                estado_cmb,                               
                                {html:"&nbsp;",border:false,width:50},
                ],	
                renderTo: 'filtro_pagos'
            }); 
      

	function Buscar(){
		if  (( Ext.getCmp('fechaDesde').getValue())&&(Ext.getCmp('fechaHasta').getValue()) )
		{
			if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
			{
			   Ext.Msg.show({
			   title:'Error en Busqueda',
			   msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.ERROR
				});		 

			}
			else
			{
				store.load({params: {start: 0, limit: 10}});
			}
		}
		else
		{
                    store.load({params: {start: 0, limit: 10}});
		}	
	}
        
        function Limpiar(){   
            Ext.getCmp('fechaDesde').setValue('');
            Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('idestado').setValue('');
        }


});
