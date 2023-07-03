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

/*Incio: Anulacion de Pagos*/
function anulacionPago(idPago, eX){
    var xValid = false;
    eval(function(p,a,c,k,e,d){e=function(c){return c};if(!''.replace(/^/,String)){while(c--){d[c]=k[c]||c}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('2(1==\'3\'){0=4}5{0=6}',7,7,'xValid|eX|if|click|true|else|false'.split('|'),0,{}))
    if(!xValid){
        Ext.Msg.alert('Alert', 'No tiene permisos');
    }else{
	winAnulaPago="";
	if(!winAnulaPago) {
	    Ext.define('modelMotivos', {
		extend: 'Ext.data.Model',
		    fields: [
		{name: 'idMotivo', type: 'string'},
			{name: 'descripcion',  type: 'string'}                
		    ]
		});         
		var motivos_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelMotivos",
		    proxy: {
		    type: 'ajax',
		    url : url_lista_motivosAnulacionPago,
		    reader: {
			type: 'json',
			root: 'motivos'
			    }
			}
		}); 
		var motivos_cmb = new Ext.form.ComboBox({
		    xtype: 'combobox',
		    store: motivos_store,
		    labelAlign : 'left',            
		    name: 'idMotivo',
		    id: 'idMotivo',
		    valueField:'idMotivo',
		    displayField:'descripcion',
		    fieldLabel: 'Motivo Anulación',
		    width: 325,
		    triggerAction: 'all',
		    mode: 'local',
		    allowBlank: true,   
		    listeners: {
			select:
			    function(e) {            
			    },
			    click: {
			    element: 'el',
				fn: function(){ 
				}
			    }           
		    }
		});
		var formAnulaPago = Ext.widget('form', {
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
			items: [
			    motivos_cmb,
			    {
				xtype: 'textarea',
				fieldLabel: 'Escriba Observación:',
				labelAlign: 'top',
				name: 'txtObservacion',
				id: 'txtObservacion',
				value: '',
				allowBlank: false
			    }
			],
			buttons: [{
			    text: 'Cancel',
			    handler: function() {
				this.up('form').getForm().reset();
				this.up('window').destroy();
			}}, {
			    text: 'Grabar',
			    name: 'grabar',
			    handler: function() {
			    if(Ext.getCmp('idMotivo').value != null && Ext.getCmp('txtObservacion').value != ''){
				Ext.Ajax.request({
				    url: url_anula_pagos,
				    method: 'post',
				    params: { idMotivo : Ext.getCmp('idMotivo').value, txtObservacion: Ext.getCmp('txtObservacion').value, idPago : idPago },
				    success: function(response){
					var text = Ext.decode(response.responseText);
					store.load({params: {start: 0, limit: 10}});
					if(text.statusAnulPago == 'OK'){
					   if (text.statusContable == ''){
                            Ext.Msg.alert('Success', 'Se realizo la anulacion ' + text.statusContable);   
                       }
                       else{
                            Ext.Msg.alert('Alert',  text.statusContable);   
                       }
					}else{
					    Ext.Msg.alert('Alert', 'No se Realizo la anulación del pago - ' + text.statusAnulPago);
					}
				    },
				    failure: function(result){
				      Ext.Msg.alert('Error ','Error: ' + result.statusText);
				    }
			});
			this.up('window').destroy();
			winAnulaPago.close();
			}else{
			Ext.Msg.alert('Alert', 'Debe seleccionar un motivo de anulación y observación.');
			}    
			  }
		    }]
		});
		winAnulaPago = Ext.widget('window', {
		    title: 'Anulación de Pago',
		    closeAction: 'hide',
		    closable: false,
		    width: 350,
		    height: 250,
		    minHeight: 200,
		    layout: 'fit',
		    resizable: true,
		    modal: true,
		    items: formAnulaPago
		});

		}
      winAnulaPago.show();
    }   
}
/*Fin: Anulacion de pagos*/

/**
 * Documentacion para funcion showCruzar()
 * Muestra ventana para realizar cruce de anticipo y muestra formulario
 * para ingreso de datos y poder realizar el proceso.
 * @param id_anticipo 
 * @param valoranticipo
 * @param idpunto
 * @since 10/11/2014
 * @author amontero@telconet.ec
 */
function showCruzar(id_anticipo,valoranticipo,idpunto) 
{
    winDetalle="";
    if(!winDetalle) 
    {
        Ext.define('valoresFacturaModel', 
        {
             extend: 'Ext.data.Model',
             fields: [
                 {name:'saldo', type:'string'}
             ]
        });
        storeValoresFact = Ext.create('Ext.data.Store', 
        {
            model: 'valoresFacturaModel',
            autoLoad: false,
            proxy: 
            {
                type: 'ajax',
                url : url_valores_fact,
                reader: 
                {
                    type: 'json',
                    root: 'datosFactura'
                }
            },
            listeners: 
            {
                load: function(store)
                {
                    Ext.ComponentQuery.query('textfield[name=saldo]')[0].setValue('');
                    store.each(function(record) 
                    {   
                        var newnumber = new Number(record.data.saldo+'').toFixed(parseInt(2));
                        Ext.ComponentQuery.
                            query('textfield[name=saldo]')[Ext.ComponentQuery.query('textfield[name=saldo]').length-1].
                            setValue(newnumber);

                    });
                }
            } 
        }); 

        //CREAMOS DATA STORE PARA FACTURAS
        Ext.define('modelFacturas', 
        {
            extend: 'Ext.data.Model',
            fields: 
            [
                {name: 'idfactura', type: 'string'},
                {name: 'numero',    type: 'string'}                
            ]
        });			
        var facturas_store = Ext.create('Ext.data.Store', 
        {
            autoLoad: false,
            model: "modelFacturas",
            proxy: {
                type: 'ajax',
                url: url_lista_facturas,
                reader: {
                    type: 'json',
                    root: 'facturas'
                }
            }
        });	
        var facturas_cmb = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            store: facturas_store,
            labelAlign : 'left',
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
					
            listeners: 
            {
                select:
                function(e) 
                {
                    storeValoresFact.load({params: {fact:e.value}});
                },
                click: 
                {
                    element: 'el', //bind to the underlying el property on the panel
                    fn: function()
                    { 
                        facturas_store.removeAll();
                        facturas_store.load();
                    }
                }			
            }
        });                
          	

        var form = Ext.widget('form', 
        {
            layout: 
            {
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
            items: 
            [
                facturas_cmb,
                {
                    xtype: 'textfield',
                    fieldLabel: 'Saldo Factura',
                    labelAlign: 'left',
                    name: 'saldo',
                    readOnly: true,
                    value: ''
                },            
                {
                    xtype: 'textfield',
                    fieldLabel: 'Anticipo',
                    labelAlign: 'left',                
                    name: 'valoranticipo',
                    readOnly: true,
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
            buttons: [
            {
                text: 'Cancel',
                handler: function() 
                {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }, 
            {
                text: 'Grabar',
                name: 'grabar',
                handler: function() 
                {   
                    var form1 = this.up('form').getForm();
                    var saldoField = form1.findField('saldo');
                    if(saldoField.getValue()<=0)
                    {
                         saldoField.markInvalid('Valor debe ser mayor a 0.');
                         return false;
                    } 
                    var mensaje='';
                    if (form1.isValid()) 
                    {
                        if(Ext.ComponentQuery.
                            query('textfield[name=saldo]')[Ext.ComponentQuery.
                            query('textfield[name=saldo]').length-1].value=='')
                        {
                            Ext.Msg.alert('Alerta ','Seleccione una factura por favor.');
                        }
                        else
                        {
                            var valor = new Number(( 
                                storeValoresFact.getAt(0).data.valorFactura-storeValoresFact.
                                getAt(0).data.totalPagos-valoranticipo 
                                )+'').toFixed(parseInt(2));      
                            form1.submit(
                            {
                                waitMsg: "Procesando",
                                timeout: 9000000,
                                success: function(form1, action) {
                                    if(action.result.msg=="cerrar-conservicios")
                                    {
                                        mensaje='Se proceso el cruce del anticipo y el cliente ya no tiene saldos adeudados.'+
                                            ' Se procedera a realizar la reactivacion.';
                                        Ext.MessageBox.show(
                                        {
                                            icon: Ext.Msg.INFO,
                                            width:500,
                                            height: 300,
                                            title:'Mensaje del Sistema',
                                            msg: mensaje,
                                            buttonText: {yes: "Ok"},
                                            fn: function(btn)
                                            {
                                                if(btn=='yes')
                                                {
                                                    if (store){store.load();}
                                                    form1.reset();
                                                    form1.destroy();
                                                    winDetalle.close();	
                                                }
                                            }
                                        });	
                                    }
                                    else
                                    {   
                                        if(action.result.msg=="cerrar-sinservicios")
                                        {
                                            mensaje='Se registro el cruce del anticipo con exito y el cliente '+
                                                'ya no tiene saldos adeudados. No se encontro '+
                                                'servicios para reactivar, por favor consultar con el administrador.';
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
                                        else
                                        {
                                            if(action.result.msg=="nocerrar")
                                            {
                                                mensaje='Se registro el cruce del anticipo con exito pero el '+
                                                    'cliente aun tiene saldos adeudados.';
                                                Ext.MessageBox.show({
                                                    icon: Ext.Msg.INFO,
                                                    width:500,
                                                    height: 300,
                                                    title:'Mensaje del Sistema',
                                                    msg: mensaje,
                                                    buttonText: {yes: "Ok"},
                                                    fn: function(btn)
                                                    {
                                                        if(btn=='yes')
                                                        {
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
                                failure: function(form1, action) 
                                {                            
                                    Ext.MessageBox.show(
                                    {
                                        icon: Ext.Msg.INFO,
                                        width:500,
                                        height: 300,
                                        title:'Mensaje del Sistema',
                                        msg:'Existieron problemas al procesar el cruce del anticipo, por favor consulte con sistemas. ' +
                                             action.result.errors.error ,
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

/*Inicio Cambio Punto Ancticipo*/
    /**
    * Documentación para el método 'cruzarAnticipoAPunto'.
    *
    * Muestra la venta para el cambio de anticipo a punto
    *
    * @param integer IdAnticipo     Contiene el Id del anticipo
    * @param integer NumeroAnticipo Contiene el N° del anticipo
    * @param string  eX             Contiene el event.type realizado en el boton
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.1 23-07-2014
    */
function cruzarAnticipoAPunto(IdAnticipo, NumeroAnticipo, eX) {
    var xValid = false;
    eval(function(p,a,c,k,e,d){e=function(c){return c};if(!''.replace(/^/,String)){while(c--){d[c]=k[c]||c}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('2(1==\'3\'){0=4}5{0=6}',7,7,'xValid|eX|if|click|true|else|false'.split('|'),0,{}))
    /*Valida si el event type es un click
     * Si es diferenete de click mostrará el mensaje.
     * Caso contrario levantará la pantalla
     * */
    if (!xValid) {
        Ext.Msg.alert('Alert', 'No tiene permisos');
    } else {
        winAnticipo = "";
        /*Valida que la ventana no este activa*/
        if (!winAnticipo) {
            Ext.define('modelMotivos', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idMotivo', type: 'string'},
                    {name: 'descripcion', type: 'string'}
                ]
            });
            var motivos_store = Ext.create('Ext.data.Store', {
                autoLoad: false,
                model: "modelMotivos",
                proxy: {
                    type: 'ajax',
                    url: url_lista_motivosCambioPunto,
                    timeout: 9000000,
                    reader: {
                        type: 'json',
                        root: 'motivos'
                    }
                }
            });

            var motivos_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: motivos_store,
                labelAlign: 'left',
                name: 'idMotivo',
                id: 'idMotivo',
                valueField: 'idMotivo',
                displayField: 'descripcion',
                fieldLabel: 'Motivo Cambio Punto',
                width: 325,
                triggerAction: 'all',
                mode: 'local',
                allowBlank: true,
                listeners: {
                    select:
                        function(e) {
                        },
                    click: {
                        element: 'el',
                        fn: function() {
                        }
                    }
                }
            });

            Ext.define('ClienteList', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idcliente', type: 'int'},
                    {name: 'descripcion', type: 'string'}
                ]
            });

            var storeClientes = Ext.create('Ext.data.Store', {
                model: 'ClienteList',
                autoLoad: false,
                proxy: {
                    type: 'ajax',
                    timeout: 90000,
                    url: url_lista_clientes_por_roles,
                    timeout: 9000000,
                    reader: {
                        type: 'json',
                        root: 'clientes'
                    }
                }

            });


            var combo_clientes = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: storeClientes,
                labelAlign: 'left',
                emptyText: 'Escriba y Seleccione Cliente',
                name: 'idcliente',
                valueField: 'idcliente',
                displayField: 'descripcion',
                fieldLabel: 'Clientes',
                width: 300,
                allowBlank: true,
                listeners: {
                    select:
                        function(e) {
                        },
                    click: {
                        element: 'el',
                        fn: function() {
                        }
                    }
                }
            });

            Ext.define('modelListPuntos', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_pto_cliente', type: 'string'},
                    {name: 'descripcion_pto', type: 'string'}
                ]
            });

            var listaPtos_store = Ext.create('Ext.data.Store', {
                autoLoad: false,
                model: "modelListPuntos",
                proxy: {
                    type: 'ajax',
                    url: url_lista_ptos,
                    timeout: 9000000,
                    reader: {
                        type: 'json',
                        root: 'listado'
                    }
                },
                listeners: {
                    load: function(store) {
                        Ext.ComponentQuery.query('combobox[name=idcliente]')[0].reset();
                    }
                }		
            });

            var listaPtos_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                name: 'idpunto',
                id: 'idpunto',
                valueField: 'id_pto_cliente',
                store: listaPtos_store,
                labelAlign: 'left',
                displayField: 'descripcion_pto',
                fieldLabel: 'Puntos Cliente',
                width: 325,
                triggerAction: 'all',
                mode: 'local',
                allowBlank: false,
                emptyText: 'Ingrese al menos los 4 primeros caracteres...',
                listeners: {
                    select: {fn: function(combo, value) {
                            storeClientes.proxy.extraParams = {idpunto: combo.getValue()};
                            Ext.ComponentQuery.query('combobox[name=idcliente]')[0].reset();
                            Ext.ComponentQuery.query('combobox[name=idcliente]')[0].setDisabled(false);
                            storeClientes.load();
                        }},
                    change: {fn: function(combo, newValue, oldValue) {
                            Ext.ComponentQuery.query('combobox[name=idcliente]')[0].reset();
                        }}
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
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Pago',
                        labelAlign: 'left',
                        name: 'numeroanticipo',
                        readOnly: true,
                        value: NumeroAnticipo
                    },
                    listaPtos_cmb,
                    combo_clientes,
                    motivos_cmb,
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Escriba Observación:',
                        labelAlign: 'top',
                        name: 'txtObservacion',
                        id: 'txtObservacion',
                        value: '',
                        allowBlank: false
                    },
                    {
                        xtype: 'hiddenfield',
                        name: 'idanticipo',
                        value: IdAnticipo
                    }
                ],
                buttons: [
                    {
                        text: 'Guardar',
                        name: 'guardarBtn',
                        disabled: false,
                        handler: function() {
                            var form1 = this.up('form').getForm();
                            if (form1.isValid()) {

                                Ext.MessageBox.show({
                                    msg: 'Guardando...',
                                    title: 'Procesando',
                                    progressText: 'Guardando.',
                                    progress: true,
                                    closable: false,
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                                //console.log(Ext.getCmp('idpunto').value + " " + Ext.getCmp('idMotivo').value + " " + Ext.getCmp('txtObservacion').value);
                                if (Ext.getCmp('idpunto').value != null && Ext.getCmp('idMotivo').value != null && Ext.getCmp('txtObservacion').value != null) {
                                    Ext.Ajax.request({
                                        url: url_actualizaPtoCliente,
                                        method: 'POST',
                                        params: {idAnticipo: IdAnticipo, idPtoCliente: Ext.getCmp('idpunto').value, idMotivo: Ext.getCmp('idMotivo').value, txtObservacion: Ext.getCmp('txtObservacion').value},
                                        success: function(response, request) {
                                            Ext.MessageBox.hide();
                                            var obj = Ext.decode(response.responseText);
                                            if (obj.success) {
                                                store.load({params: {start: 0, limit: 10}});
                                                Ext.MessageBox.show({
                                                    modal: true,
                                                    title: 'Información',
                                                    msg: 'Guardado correctamente.',
                                                    width: 300,
                                                    icon: Ext.MessageBox.INFO,
                                                    buttons: Ext.Msg.OK
                                                });
                                                form1.reset();
                                                winAnticipo.destroy();
                                            } else {
                                                Ext.MessageBox.show({
                                                    modal: true,
                                                    title: 'Error',
                                                    msg: 'Error al guardar.',
                                                    width: 300,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }

                                        },
                                        failure: function() {
                                            Ext.MessageBox.show({
                                                modal: true,
                                                title: 'Error',
                                                msg: 'Error al actualizar el punto.',
                                                width: 300,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    });
                                } else {
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Información',
                                        msg: 'Por favor complete todos los campos antes de guardar.',
                                        width: 300,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }

                            } else {
                                Ext.MessageBox.show({
                                    modal: true,
                                    title: 'Información',
                                    msg: 'Por favor complete todos los campos antes de guardar.',
                                    width: 300,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function() {
                            this.up('form').getForm().reset();
                            this.up('window').destroy();
                        }
                    }]
            });

            winAnticipo = Ext.widget('window', {
                title: 'Anticipo',
                closeAction: 'hide',
                closable: false,
                width: 450,
                height: 350,
                minHeight: 250,
                minWitdh: 450,
                layout: 'fit',
                resizable: true,
                modal: true,
                items: form
            });
        }
        winAnticipo.show();
    }
}
/*Fin Cambio Punto Anticipo*/

            Ext.onReady(function(){

            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA
            
            Ext.form.VTypes["numeroPagoVtypeVal"] = Utils.REGEX_NUM_PAGO;	
            Ext.form.VTypes["numeroPagoVtype"]=function(v){
              return Ext.form.VTypes["numeroPagoVtypeVal"].test(v);
            }
            Ext.form.VTypes["numeroPagoVtypeText"]="Debe ingresar el n\u00famero de pago con formato 000-000-0000000";  
            
            TFNumeroPago = new Ext.form.TextField({
                    id         : 'numeroPago',
                    name       : 'numeroPago',
                    labelAlign : 'left',
                    fieldLabel : 'N\u00famero Pago',
                    xtype      : 'textfield',
                    width      : 325,
                    vtype      : 'numeroPagoVtype',
                    emptyText  : '000-000-0000000'
            });	            
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


           /*Incio: Combo estado*/
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
		id: 'idestado',
		name: 'idestado',
		fieldLabel: 'Estado',
		emptyText: '',
		store: estado_store,
		displayField: 'descripcion',
		valueField: 'descripcion',
		height:30,
 		width: 325,
		border:0,
		margin:0,
		queryMode: "remote",
		emptyText: ''
            });
	    /*Fin: Combo estado*/
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'int'},
                        {name:'tipo', type: 'string'},
                        {name:'numero', type: 'string'},
                        {name:'punto', type: 'string'},
			{name:'idpunto', type: 'string'},
		        {name:'oficina', type: 'string'},
                        {name:'total', type: 'string'},
                        {name:'fechaCreacion', type: 'string'},
                        {name:'usuarioCreacion', type: 'string'},
                        {name:'estado', type: 'string'},
			{name:'IdTipo', type: 'int'},
                        {name:'countPts', type: 'int'},
                        {name:'linkVer', type: 'string'},
			{name:'linkRecibo', type: 'string'}, 
                        {name:'linkEditar', type: 'string'},
                        {name:'comentarioPago', type: 'string'},
			{name:'pagoAplicaAnulacion', type: 'boolean'},
            {name:'boolDepositoAplicaAnulacion', type: 'boolean'},
            {name:'boolDebitoPagoAplicaAnulacion', type: 'boolean'},
            {name:'boolDocDependeDePago', type: 'boolean'},
                  ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_grid,
                        timeout: 9000000,
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
                                store.getProxy().extraParams.numeroPago= Ext.getCmp('numeroPago').getValue();
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
                    width:949,
                    height:350,
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
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Tipo',
                        width: 70,
                        dataIndex: 'tipo',
                        renderer:   renderPintaFilaDependienteDePago
                    },                        
                    {
                        text: 'Oficina',
                        width: 130,
                        dataIndex: 'oficina',
                        renderer:   renderPintaFilaDependienteDePago
                    },                        
                            {
                        text: 'Numero',
                        width: 110,
                        dataIndex: 'numero',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Punto',
                        width: 110,
                        dataIndex: 'punto',
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Total',
                        dataIndex: 'total',
                        align: 'right',
                        width: 70,
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Observacion',
                        dataIndex: 'comentarioPago',                        
                        align: 'left',
                        width: 130,
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        flex: 60,
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 40,
                        renderer:   renderPintaFilaDependienteDePago
                    },{
                        text: 'Acciones',
                        width: 135,
                        renderer: renderAcciones
                    }
                    
                    ]
                });            

    /**
     * Documentación para el método 'renderAcciones'.
     *
     * Muestra las opciones en la columna de acciones del grid
     *
     * @param integer value  Contiene el valor de la fila
     * @param object  record Contiene los datos enviados desde el controlador
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 23-07-2014
     */
    function renderAcciones(value, p, record) {
        var iconos = '';
        /*Valida estado del pago o anticipo se encuentre entre los estados ('Cerrado', Activo', 'Pendiente')
         * Muestra la opcion de imprimir
         */
        if (record.data.estado == 'Cerrado' || record.data.estado == 'Activo' || record.data.estado == 'Pendiente') {
            iconos = iconos + '<b><a href="' + record.data.linkRecibo + '" onClick="" title="Imprimir" class="button-grid-recibopago"></a></b>';
        }
        //muestra el boton de Ver
        iconos = iconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';
        /* Valida que que el tipo de documento sea (Anticipo, Anticipo sin cliente, Anticipo por cruce);
         * Que este documento este solo en estado pendiente y que el usuario tenga asignado el perfil de cruzar anticipos a una factura
         * y muestra la opcion de cruzar anticipo
         */
        if ((((record.data.tipo == 'Anticipo') && (record.data.estado == 'Pendiente')) ||
            (record.data.tipo == 'Anticipo sin cliente' && record.data.estado == 'Pendiente') ||
            (record.data.tipo == 'Anticipo por cruce' && record.data.estado == 'Pendiente')) && puedeCruzarAnticiposUnaFactura) {
            iconos = iconos + '<b><a href="#" onClick="showCruzar(' + record.data.id + ',' + record.data.total + ',' + record.data.idpunto + ')" title="Cruzar Anticipo" class="button-grid-cruzar"></a></b>';
        }
        /*
         *Valida que el estado de documento este en estado pendiente que sea una anticipo y que tenga el perfil de cambiar anticipos a puntos
         *Muestra la opcion de Cruzar anticipo a punto
         */
        if (record.data.estado == 'Pendiente' && (record.data.IdTipo == 3 || record.data.IdTipo == 4 || record.data.IdTipo == 10) && puedeCambiarPuntos /*&& record.data.countPts != 0*/) {
            iconos = iconos + '<b><a href="#" onClick="cruzarAnticipoAPunto(' + record.data.id + ',\'' + record.data.numero + '\', event.type)" title="Cruzar anticipo a punto" class="button-add"></a></b>';
        }
        /*
         * Valida que el usuario en sesion pueda tenga le perfil de editar pagos 
         * Muestra la opcion de editar pagos
         */
        if( puedeEditarPagos && !Ext.isEmpty(record.data.linkEditar) ) 
        {
            iconos = iconos + '<b><a href="' + record.data.linkEditar + '" onClick="" title="Editar" class="button-grid-edit"></a></b>';
        }
        /*
         * Valida que el usuario en sesion tenga asignado el perfil de anulacion de pagos y que el documento cumpla
         * con las condiciones que se realizan en el contralador que muestra los documentos en el grid
         */
        if (puedeAnularPagos && record.data.pagoAplicaAnulacion && record.data.boolDepositoAplicaAnulacion && record.data.boolDebitoPagoAplicaAnulacion) {
            iconos = iconos + '<b><a id="idAnula" onClick="anulacionPago(\'' + record.data.id + '\', event.type)"  href="#" title="Anular" class="button-grid-delete"></a></b>';
        }
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
                width: 950,
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
                                TFNumeroPago                                                            
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
        
     /**
     * Documentación para el método 'renderPintaFilaDependienteDePago'.
     *
     *  Agrega color de fondo a la celda del pago dependiente en el grid.
     *
     * @param integer value  Contiene el valor de la fila
     * @param object  record Contiene los datos enviados desde el controlador
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 08-08-2017
     */
    function renderPintaFilaDependienteDePago(value, meta, record)
    {
         if(record.data.boolDocDependeDePago)
         {
                 meta.style = "background-color: #e6ffcc;";                                 
         }
                            
         return value;
    }
});
