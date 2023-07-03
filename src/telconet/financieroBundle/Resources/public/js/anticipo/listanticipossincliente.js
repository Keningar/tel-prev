            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                'Ext.window.MessageBox',
                'Ext.ux.form.field.BoxSelect'				
            ]);

            var itemsPerPage = 10;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

/*Incio Anular Anticipos*/
function anulacionAnticipoSinCliente(idPago, eX){
    var xValid = false;
    eval(function(p,a,c,k,e,d){e=function(c){return c};if(!''.replace(/^/,String)){while(c--){d[c]=k[c]||c}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('2(1==\'3\'){0=4}5{0=6}',7,7,'xValid|eX|if|click|true|else|false'.split('|'),0,{}))
    if(!xValid){
	Ext.Msg.alert('Alert', 'No tiene permisos');
    }else{
	winAnulaAnticipo="";
	if(!winAnulaAnticipo) {
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
		    url : url_lista_motivosAnulacionAnticipo,
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
	    
	    var formAnulaAnticipo = Ext.widget('form', {
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
		    }
		}, {
		    text: 'Grabar',
		    name: 'grabar',
		    handler: function() {
			if(Ext.getCmp('idMotivo').value != null && Ext.getCmp('txtObservacion').value != ''){           
			    Ext.Ajax.request({
				url: url_anula_anticipo,
				method: 'post',
				params: { idMotivo : Ext.getCmp('idMotivo').value, txtObservacion: Ext.getCmp('txtObservacion').value, idPago : idPago },
				success: function(response){
				    var text = Ext.decode(response.responseText);
				    store.load({params: {start: 0, limit: 10}});
				
				    if(text.statusAnulPago == 'OK'){
					Ext.Msg.alert('Success', 'Se realizo la anulacion');
				    }else{
					Ext.Msg.alert('Alert', 'No se Realizo la anulación del anticipo - ' + text.statusAnulPago);
				    }
				},
				failure: function(result){
				    Ext.Msg.alert('Error ','Error: ' + result.statusText);
				}
			    });
			    this.up('window').destroy();
			    winAnulaAnticipo.close();
			  }else{
			      Ext.Msg.alert('Alert', 'Debe seleccionar un motivo de anulación y observación.');
			  }    
		    }
		}]
	    });
	    
	    winAnulaAnticipo = Ext.widget('window', {
		title: 'Anulación de Anticipo Sin Cliente',
		closeAction: 'hide',
		closable: false,
		width: 350,
		height: 250,
		minHeight: 200,
		layout: 'fit',
		resizable: true,
		modal: true,
		items: formAnulaAnticipo
	    });
	}
	winAnulaAnticipo.show();
	}
}
/*Fin Anular Anticpos*/

/**
 * Documentacion para funcion showCruzar()
 * Muestra ventana para realizar cruce de anticipo sin cliente y muestra formulario
 * para ingreso de datos y poder realizar el proceso.
 * @param id_anticipo 
 * @param valoranticipo
 * @param numeroanticipo
 * @since 10/11/2014
 * @author amontero@telconet.ec
 */
function showCruzar(id_anticipo, valoranticipo, numeroanticipo) 
{

    winDetalle = "";
    if (!winDetalle) 
    {
        Ext.define('valoresFacturaModel', 
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'saldo', type: 'string'}
            ]
        });
        storeValoresFact = Ext.create('Ext.data.Store',
        {
            model: 'valoresFacturaModel',
            autoLoad: false,
            proxy:
            {
                type: 'ajax',
                url: url_valores_fact,
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
                        Ext.ComponentQuery.query('textfield[name=saldo]')[0].
                            setValue(record.data.saldo);

                    });
                }
            }
        });


        Ext.define('ClienteList', 
        {
            extend: 'Ext.data.Model',
            fields: 
            [
                {name: 'idcliente', type: 'int'},
                {name: 'descripcion', type: 'string'}
            ]
        });
        storeClientes = Ext.create('Ext.data.Store', 
        {
            model: 'ClienteList',
            autoLoad: false,
            proxy: 
            {
                type: 'ajax',
                timeout: 90000,
                url: url_lista_clientes,
                reader: 
                {
                    type: 'json',
                    root: 'clientes'
                }
            },
            listeners: 
            {
                load: function(store) 
                {
                    Ext.ComponentQuery.query('combobox[name=idpunto]')[0].reset();
                }
            }
        });

        combo_clientes = new Ext.form.ComboBox({
            xtype: 'combobox',
            store: storeClientes,
            labelAlign: 'left',
            emptyText: 'Escriba y Seleccione Cliente',
            name: 'idcliente',
            valueField: 'idcliente',
            displayField: 'descripcion',
            fieldLabel: 'Clientes',
            width: 300,
            allowBlank: false,
            listeners: 
            {
                select: 
                {
                    fn: function(combo, value) 
                    {
                        Ext.ComponentQuery.query('combobox[name=idformasmultiselect]')[0].reset();
                        facturas_cliente_store.proxy.extraParams = {idcliente: combo.getValue()};
                        facturas_cliente_store.load();
                    }
                },
                change: 
                {
                    fn: function(combo, newValue, oldValue) 
                    {
                        Ext.ComponentQuery.query('combobox[name=idpunto]')[0].reset();
                    }
                }
            }
        });
        Ext.define('PtosList', 
        {
            extend: 'Ext.data.Model',
            fields: 
            [
                {name: 'id_pto_cliente', type: 'int'},
                {name: 'descripcion_pto', type: 'string'}
            ]
        });

        storePtos = Ext.create('Ext.data.Store', 
        {
            model: 'PtosList',
            proxy: 
            {
                type: 'ajax',
                url: url_lista_ptos,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'listado'
                }
            }
        });
        combo_ptos = new Ext.form.ComboBox(
        {
            name: 'idpunto',
            labelAlign: 'left',
            fieldLabel: 'Puntos',
            anchor: '100%',
            disabled: true,
            width: 200,
            emptyText: 'Seleccione punto',
            store: storePtos,
            displayField: 'descripcion_pto',
            valueField: 'id_pto_cliente',
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            listeners: 
            {
                select: 
                {
                    fn: function(combo, value) 
                    {
                        Ext.ComponentQuery.query('combobox[name=idfactura]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idfactura]')[0].setDisabled(false);
                        facturas_store.proxy.extraParams = {idpto: combo.getValue()};
                        facturas_store.load();

                    }
                },
                change: 
                {
                    fn: function(combo, newValue, oldValue) 
                    {
                        Ext.ComponentQuery.query('combobox[name=idfactura]')[0].reset();
                    }
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
                {name: 'numero', type: 'string'}
            ]
        });
        var facturas_store = Ext.create('Ext.data.Store', 
        {
            autoLoad: false,
            model: "modelFacturas",
            proxy: 
            {
                type: 'ajax',
                url: url_lista_facturas,
                reader: 
                {
                    type: 'json',
                    root: 'facturas'
                }
            }
        });
        var facturas_cmb = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            store: facturas_store,
            labelAlign: 'left',
            name: 'idfactura',
            valueField: 'idfactura',
            displayField: 'numero',
            fieldLabel: 'Facturas',
            width: 325,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            emptyText: 'Seleccione Factura',
            disabled: true,
            listeners: 
            {
                select:
                    function(e) 
                    {
                        storeValoresFact.load({params: {fact: e.value}});

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



        //CREAMOS DATA STORE PARA EMPLEADOS
        Ext.define('modelFacturasCliente', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idFactura', type: 'string'},
                {name: 'saldo', type: 'float'},
                {name: 'valorFactura', type: 'float'},
                {name: 'numeroFacturaSri', type: 'string'}
            ]
        });
        var facturas_cliente_store = Ext.create('Ext.data.Store', 
        {
            autoLoad: false,
            model: "modelFacturasCliente",
            proxy: 
            {
                type: 'ajax',
                url: url_lista_facturas_cliente,
                reader: 
                {
                    type: 'json',
                    root: 'facturas'
                }
            }
        });
        var baseMultiSelectConfig = 
        {
            name: 'idformasmultiselect',
            fieldLabel: 'Facturas',
            displayField: 'numeroFacturaSri',
            valueField: 'idFactura',
            width: 200,
            labelWidth: 130,
            emptyText: 'Seleccione',
            store: facturas_cliente_store,
            mode: 'local',
            allowBlank: false
        };
        var facturasmultiselect_cmb = Ext.create('Ext.ux.form.field.BoxSelect', baseMultiSelectConfig);
        var saldoFact = 0;
        var idfacturas = '';
        var banderaNegativo = 0;
        var cuantasFacturas = 0;
        facturasmultiselect_cmb.on('select', function(combo, records, eOpts)
        {
            saldoFact = 0;
            cuantasFacturas = 0;
            idfacturas = '';
            Ext.Array.each(records, function(record)
            { 
                saldoFact = saldoFact + record.get('saldo');
                idfacturas = idfacturas + record.get('idFactura') + ",";
                cuantasFacturas++;
            });
            if ((valoranticipo - saldoFact) <= 0 && ((valoranticipo - saldoFact) * (-1)) > valoranticipo) 
            {
                banderaNegativo = 1;
            } 
            else 
            {
                banderaNegativo = 0;
            }
            Ext.ComponentQuery.query('textfield[name=saldo]')[0].setReadOnly(false);
            var newnumber = new Number((saldoFact) + '').toFixed(parseInt(2));
            Ext.ComponentQuery.query('textfield[name=saldo]')[0].setValue(newnumber);
            Ext.ComponentQuery.query('textfield[name=saldo]')[0].setReadOnly(true);
            Ext.ComponentQuery.query('hiddenfield[name=idfacturas]')[0].setValue(idfacturas);
            var arreglo = Ext.ComponentQuery.query('textfield[name=saldo]');
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
            fieldDefaults: 
            {
                labelAlign: 'top',
                labelWidth: 130,
                labelStyle: 'font-weight:bold'
            },
            defaults: 
            {
                margins: '0 0 10 0'
            },
            url: url_cruzar,
            items: 
            [
                {
                    xtype: 'textfield',
                    fieldLabel: 'Pago',
                    labelAlign: 'left',
                    name: 'numeroanticipo',
                    readOnly: true,
                    value: numeroanticipo
                },
                combo_clientes,
                facturasmultiselect_cmb,
                {
                    xtype: 'textfield',
                    fieldLabel: 'Saldo Factura',
                    labelAlign: 'left',
                    name: 'saldo',
                    value: '',
                    allowBlank: false,
                    readOnly: true
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Anticipo',
                    labelAlign: 'left',
                    name: 'valoranticipo',
                    value: valoranticipo,
                    readOnly: true
                },
                {
                    xtype: 'hiddenfield',
                    name: 'idanticipo',
                    value: id_anticipo
                },
                {
                    xtype: 'hiddenfield',
                    name: 'idfacturas',
                    value: ''
                }
            ],
            buttons: 
            [
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
                        if (form1.isValid()) 
                        {
                            if (Ext.ComponentQuery.query('textfield[name=idfactura]')[0].value == '') 
                            {
                                Ext.Msg.alert('Error ', 'Seleccione una factura por favor.');
                            }
                            else 
                            {    
                                if (banderaNegativo == 1 && cuantasFacturas > 1) 
                                {
                                    alert('El anticipo no puede cubrir el numero de facturas escogidas');
                                }
                                else
                                {

                                    Ext.MessageBox.show(
                                    {
                                        icon: Ext.Msg.INFO,
                                        width: 500,
                                        height: 300,
                                        title: 'Mensaje del Sistema',
                                        msg: 'Se procedera a crear pagos por cada factura seleccionada.',
                                        buttonText: {yes: "Ok"},
                                        fn: function(btn) 
                                        {
                                            if (btn == 'yes') 
                                            {
                                                form1.submit(
                                                {
                                                    waitMsg: "Procesando",
                                                    success: function(form1, action) 
                                                    {
                                                        if (action.result.msg == "cerrar-conservicios") 
                                                        {
                                                            mensaje = 'Se proceso el cruce del anticipo con exito y procedio '+
                                                                'a realizar la reactivacion de los servicios.';
                                                            Ext.MessageBox.show(
                                                            {
                                                                icon: Ext.Msg.INFO,
                                                                width: 500,
                                                                height: 300,
                                                                title: 'Mensaje del Sistema',
                                                                msg: mensaje,
                                                                buttonText: {yes: "Ok"},
                                                                fn: function(btn) 
                                                                {
                                                                    if (btn == 'yes') 
                                                                    {
                                                                        if (store) 
                                                                        {
                                                                            store.load();
                                                                        }
                                                                        form1.reset();
                                                                        form1.destroy();
                                                                        winDetalle.destroy();
                                                                        winDetalle.close();

                                                                    }
                                                                }
                                                            });
                                                        } 
                                                        else 
                                                        {
                                                            if (action.result.msg == "cerrar-sinservicios") 
                                                            {
                                                                mensaje = 'Se proceso el cruce del anticipo con exito y '+
                                                                    'no se encontro servicios cortados para reactivar.';
                                                                Ext.MessageBox.show(
                                                                {
                                                                    icon: Ext.Msg.INFO,
                                                                    width: 500,
                                                                    height: 300,
                                                                    title: 'Mensaje del Sistema',
                                                                    msg: mensaje,
                                                                    buttonText: {yes: "Ok"},
                                                                    fn: function(btn) 
                                                                    {
                                                                        if (btn == 'yes') 
                                                                        {
                                                                            if (store) 
                                                                            {
                                                                                store.load();
                                                                            }
                                                                            form1.reset();
                                                                            form1.destroy();
                                                                            winDetalle.destroy();
                                                                            winDetalle.close();
                                                                        }
                                                                    }
                                                                });
                                                            } 
                                                            else 
                                                            {
                                                                if (action.result.msg == "nocerrar") 
                                                                {
                                                                    mensaje = 'Se proceso el cruce del anticipo con exito, '+
                                                                        'pero el cliente aun tiene saldos adeudados.';
                                                                    Ext.MessageBox.show(
                                                                    {
                                                                        icon: Ext.Msg.INFO,
                                                                        width: 500,
                                                                        height: 300,
                                                                        title: 'Mensaje del Sistema',
                                                                        msg: mensaje,
                                                                        buttonText: {yes: "Ok"},
                                                                        fn: function(btn) 
                                                                        {
                                                                            if (btn == 'yes') 
                                                                            {
                                                                                if (store) 
                                                                                {
                                                                                    store.load();
                                                                                }
                                                                                form1.reset();
                                                                                form1.destroy();
                                                                                winDetalle.destroy();
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
                                                            width: 500,
                                                            height: 300,
                                                            title: 'Mensaje del Sistema',
                                                            msg: 'Existieron problemas al procesar el cruce del anticipo,'+
                                                                ' por favor consulte con sistemas.',
                                                            buttonText: {yes: "Ok"},
                                                            fn: function(btn) 
                                                            {
                                                                if (btn == 'yes') 
                                                                {
                                                                    if (store) 
                                                                    {
                                                                        store.load();
                                                                    }
                                                                    form1.reset();
                                                                    form1.destroy();
                                                                    winDetalle.destroy();
                                                                    winDetalle.close();
                                                                }
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        }
                        else
                        {
                            alert('Faltan datos por ingresar');
                        }
                    }
                }
            ]
        });

        winDetalle = Ext.widget('window', 
        {
            title: 'Cruzar Anticipo sin Cliente',
            closeAction: 'hide',
            closable: false,
            width: 450,
            height: 450,
            minHeight: 400,
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
                    width:200
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:200
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
            TFNumero         = new Ext.form.TextField({
                                   labelAlign : 'left', 
                                   id         : 'numeroPago',
                                   fieldLabel : 'Numero',
                                   xtype      : 'textfield'
                               });
            TFIdentificacion = new Ext.form.TextField({
                                   labelAlign : 'left', 
                                   id         : 'identificacion',
                                   fieldLabel : 'Cedula/Ruc',
                                   xtype      : 'textfield'
                               });
            TFReferencia     = new Ext.form.TextField({
                                   labelAlign : 'left', 
                                   id         : 'referencia',
                                   fieldLabel : 'Referencia',
                                   xtype      : 'textfield'
                               });                           
            Ext.define('ListaDetalleModel', {
                extend: 'Ext.data.Model',
                fields: [
                    {name:'id', type: 'int'},
                    {name:'tipo', type: 'string'},
                    {name:'numero', type: 'string'},
                    {name:'total', type: 'string'},
                    {name:'fechaCreacion', type: 'string'},
                    {name:'usuarioCreacion', type: 'string'},
                    {name:'estado', type: 'string'},
                    {name:'linkVer', type: 'string'},
                    {name:'observacion', type: 'string'},
                    {name:'pagoAplicaAnulacion', type: 'boolean'},
                    {name:'identificacion', type: 'string'},
                    {name:'cliente', type: 'string'}
                ]
            }); 


                store = Ext.create('Ext.data.JsonStore', 
                {
                    model    : 'ListaDetalleModel',
                    pageSize : itemsPerPage,
                    proxy: 
                    {
                        type    : 'ajax',
                        url     : url_grid,
                        timeout :9000000,
                        reader: 
                        {
                            type          : 'json',
                            root          : 'pagos',
                            totalProperty : 'total'
                        },
                        extraParams :
                        {
                            fechaDesde           : '',
                            fechaHasta           : '', 
                            estado               : '', 
                            numeroPago           : '', 
                            numeroIdentificacion :'', 
                            numeroReferencia     :''
                        },
                        simpleSortMode: true
                    },
                    listeners: 
                    {
                        beforeload: function(store)
                        {
                            store.getProxy().extraParams.fechaDesde           = Ext.getCmp('fechaDesde').getValue();
                            store.getProxy().extraParams.fechaHasta           = Ext.getCmp('fechaHasta').getValue();   
                            store.getProxy().extraParams.estado               = Ext.getCmp('idestado').getValue();
                            store.getProxy().extraParams.numeroPago           = Ext.getCmp('numeroPago').getValue();
                            store.getProxy().extraParams.numeroIdentificacion = Ext.getCmp('identificacion').getValue();
                            store.getProxy().extraParams.numeroReferencia     = Ext.getCmp('referencia').getValue();
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
                    id          : 'listView',
                    width       : 950,
                    height      : 290,
                    collapsible : false,
                    title       : '',
                    dockedItems : [ 
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items: [
                        { xtype: 'tbfill' },
                        {
                            xtype: 'button',
                            itemId: 'grid-excel-button',
                            iconCls: 'x-btn-icon icon_exportar',
                            text: 'Exportar',
                            handler: function() 
                            {
                                if(puedeDescargarExcel)
                                {    
                                    document.location = url_excel_anticipos+"?fechaDesde="+Ext.getCmp('fechaDesde').getValue()
                                        +"&fechaHasta="+Ext.getCmp('fechaHasta').getValue()+"&numeroPago="+Ext.getCmp('numeroPago').getValue()
                                        +"&numeroIdentificacion="+Ext.getCmp('identificacion').getValue()
                                        +"&numeroReferencia="+Ext.getCmp('referencia').getValue()+"&estado="+Ext.getCmp('idestado').getValue();
                                }
                                else
                                {
                                    Ext.Msg.show(
                                    {
                                        title   : 'Mensaje',
                                        msg     : "Ud no tiene permisos para poder descargar este archivo",
                                        buttons : Ext.Msg.OK,
                                        icon    : Ext.MessageBox.ERROR
                                    });                                    
                                }    
                            }
                        }]
                    }],                    
                    renderTo : Ext.get('lista_pagos'),
                    bbar     : Ext.create('Ext.PagingToolbar', 
                    {
                        store       : store,
                        displayInfo : true,
                        displayMsg  : 'Mostrando Anticipos {0} - {1} de {2}',
                        emptyMsg    : "No hay datos para mostrar"
                    }),	
                    store       : store,
                    multiSelect : false,
                    viewConfig: 
                    {
                        emptyText: 'No hay datos para mostrar'
                    },
                    listeners:
                    {
                        itemdblclick: function( view, record, item, index, eventobj, obj )
                        {
                            var position = view.getPositionByEvent(eventobj),
                                data     = record.data,
                                value    = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show(
                                {
                                    title   : 'Copiar texto?',
                                    msg     : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons : Ext.Msg.OK,
                                    icon    : Ext.MessageBox.INFO
                                });
                        },
                        viewready: function (grid) 
                        {
                            var view = grid.view;
                            grid.mon(view, 
                            {
                                uievent: function (type, view, cell, recordIndex, cellIndex, e) 
                                {
                                    grid.cellIndex   = cellIndex;
                                    grid.recordIndex = recordIndex;
                                }
                            });

                            grid.tip = Ext.create('Ext.tip.ToolTip', 
                            {
                                target     : view.el,
                                delegate   : '.x-grid-cell',
                                trackMouse : true,
                                renderTo   : Ext.getBody(),
                                listeners  : 
                                {
                                    beforeshow: function updateTipBody(tip) 
                                    {
                                        if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) 
                                        {
                                            header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                            tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                        }
                                    }
                                }
                            });
                        }                            
                    },
                    columns: [
                        new Ext.grid.RowNumberer(),  
                        {
                            text: 'Tipo',
                            width: 110,
                            dataIndex: 'tipo'
                        },{
                            text: 'Numero',
                            width: 100,
                            dataIndex: 'numero'
                        },{
                            text: 'Total',
                            dataIndex: 'total',
                            align: 'right',
                            width: 60			
                        },{
                            text: 'Fecha Creacion',
                            dataIndex: 'fechaCreacion',
                            align: 'right',
                            flex: 70			
                        },{
                            text: 'Observacion',
                            dataIndex: 'observacion',
                            align: 'left',
                            flex: 120			
                        },{
                            text: 'Cedula/Ruc',
                            dataIndex: 'identificacion',
                            align: 'left',
                            flex: 50			
                        },{
                            text: 'Cliente',
                            dataIndex: 'cliente',
                            align: 'left',
                            flex: 100			
                        },{
                            text: 'Estado',
                            dataIndex: 'estado',
                            align: 'right',
                            flex: 50
                        },{
                            text: 'Acciones',
                            width: 125,
                            renderer: renderAcciones
                        }
                    ]
                });            

           
            function renderAcciones(value, p, record) {
                var iconos='';
                iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';
                if(puedeAnularPagos && record.data.pagoAplicaAnulacion && puedeAnularPagos)
                    iconos=iconos+'<b><a href="#" onClick="anulacionAnticipoSinCliente('+record.data.id+
                           ', event.type)" title="Anular" class="button-grid-delete" ></a></b>';
                if(record.data.estado=='Pendiente' && puedeCruzarAnticiposVariasFacturas)
                    iconos=iconos+'<b><a href="#" onClick="showCruzar('+record.data.id+','+record.data.total+
                           ',\''+record.data.numero+'\')" title="Cruzar Anticipo sin Cliente" class="button-grid-cruzar"></a></b>';
                if(puedeCruzarAnticiposSinClientePunto)
                    iconos=iconos+'<b><a href="#" onClick="showAnticipoPunto('+record.data.id+',\''+record.data.numero+
                           '\')" title="Cruzar anticipo sin cliente a punto" class="button-add"></a></b>';                    
                return Ext.String.format(
                    iconos,
                    value);
            }

	 /*Inicio: Combo Estados*/
	 Ext.define('modelEstado', {
	    extend: 'Ext.data.Model',
	    fields: [
		{name: 'idestado', type: 'string'},
		{name: 'codigo',  type: 'string'},
		{name: 'descripcion',  type: 'string'}]                   
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
	/*Fin: Combo Estados*/

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding : 7,
        border      :false,
        buttonAlign : 'center',
        layout:{
            type    :'table',
            columns : 6,
            align   : 'left'
        },
        bodyStyle:{
            background : '#fff'
        },                     
        defaults: {
            bodyStyle  : 'padding:10px'
        },
        collapsible : true,
        collapsed   : true,
        width       : 950,
        title       : 'Criterios de busqueda',
        buttons     : [
        {
            text: 'Buscar',
            iconCls: "icon_search",
            handler: Buscar
        },
        {
            text: 'Limpiar',
            iconCls: "icon_limpiar",
            handler: Limpiar
        }],                
        items: [
            {html:"&nbsp;",border:false,width:50},
            DTFechaDesde,
            {html:"&nbsp;",border:false,width:50},
            DTFechaHasta,
            {html:"&nbsp;",border:false,width:50},
            TFNumero,
            {html:"&nbsp;",border:false,width:50},
            estado_cmb,
            {html:"&nbsp;",border:false,width:50},
            TFIdentificacion,
            {html:"&nbsp;",border:false,width:50},
            TFReferencia
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
            Ext.getCmp('numeroPago').setValue('');
	    Ext.getCmp('idestado').setValue('');
        }

		
		
		

});


function showAnticipoPunto(id_anticipo, numeroanticipo) {

    winAnticipo = "";
    if (!winAnticipo) {
        Ext.define('ClienteList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idcliente', type: 'int'},
                {name: 'descripcion', type: 'string'}
            ]
        });

        clientesStore = Ext.create('Ext.data.Store', {
            model: 'ClienteList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: url_lista_clientes,
                reader: {
                    type: 'json',
                    root: 'clientes'
                }
            },
            listeners: {
                load: function(store) {
                    Ext.ComponentQuery.query('combobox[name=puntos]')[0].reset();
                }
            }
        });

        combo_clientes = new Ext.form.ComboBox({
            xtype: 'combobox',
            store: clientesStore,
            labelAlign: 'left',
            emptyText: 'Escriba y Seleccione Cliente',
            name: 'idcliente',
            valueField: 'idcliente',
            displayField: 'descripcion',
            fieldLabel: 'Clientes',
            width: 300,
            allowBlank: false,
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=puntos]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=puntos]')[0].setDisabled(false);
                        puntosStore.proxy.extraParams = {idcliente: combo.getValue()};
                        puntosStore.load();
                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=puntos]')[0].reset();
                    }}
            }

        });

        Ext.define('PuntosList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_pto_cliente', type: 'int'},
                {name: 'descripcion_pto', type: 'string'}
            ]
        });

        puntosStore = Ext.create('Ext.data.Store', {
            model: 'PuntosList',
            proxy: {
                type: 'ajax',
                url: url_lista_ptos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'listado'
                }
            }
        });

        combo_puntos = new Ext.form.ComboBox({
            name: 'puntos',
            labelAlign: 'left',
            fieldLabel: 'Puntos',
            anchor: '100%',
            disabled: true,
            width: 200,
            emptyText: 'Seleccione punto',
            store: puntosStore,
            displayField: 'descripcion_pto',
            valueField: 'id_pto_cliente',
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('button[name=guardarBtn]')[0].setDisabled(false);

                    }},
                change: {fn: function(combo, newValue, oldValue) {
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
                    value: numeroanticipo
                },
                combo_clientes,
                combo_puntos,
                {
                    xtype: 'hiddenfield',
                    name: 'idanticipo',
                    value: id_anticipo
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    name: 'guardarBtn',
                    disabled: true,
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

                            Ext.Ajax.request({
                                url: id_anticipo + '/' + Ext.ComponentQuery.query('combobox[name=puntos]')[0].getValue() + '/actualizarPagoAntPunto',
                                method: 'POST',
                                success: function(response, request) {
                                    Ext.MessageBox.hide();
                                    var obj = Ext.decode(response.responseText);
                                    if (obj.success) {
                                        Ext.getCmp('listView').getStore().load();
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
                                        msg: 'Error al actualizar el pago.',
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
            height: 250,
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