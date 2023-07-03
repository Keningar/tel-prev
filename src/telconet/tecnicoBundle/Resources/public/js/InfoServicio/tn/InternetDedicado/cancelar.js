function cancelarServicioInternetDedicado(data,idAccion)
{
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function(response) {
            Ext.get(gridServicios.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datosBackbone = json.encontrados[0];
            
            if(datosBackbone.idElementoPadre == 0)
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: datosBackbone.nombreElementoPadre,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
            else
            {
                var storeMotivos = new Ext.data.Store({
                    pageSize: 50,
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url: getMotivos,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams: {
                            accion: "cancelarCliente"
                        }
                    },
                    fields:
                        [
                            {name: 'idMotivo', mapping: 'idMotivo'},
                            {name: 'nombreMotivo', mapping: 'nombreMotivo'}
                        ]
                });

                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [{
                            xtype: 'fieldset',
                            title: 'Corte Servicio',
                            defaultType: 'textfield',
                            defaults: {
                                width: 620
                            },
                            items: [
                                //informacion del cliente
                                {
                                    xtype: 'fieldset',
                                    title: 'Información de Servicio',
                                    defaultType: 'textfield',
                                    defaults: {
                                        width: 600
                                    },
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 5,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {width: '5%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'nombreCompleto',
                                                    fieldLabel: 'Cliente',
                                                    displayField: data.nombreCompleto,
                                                    value: data.nombreCompleto,
                                                    readOnly: true,
                                                    width: 300
                                                },
                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'login',
                                                    fieldLabel: 'Login',
                                                    displayField: data.login,
                                                    value: data.login,
                                                    readOnly: true,
                                                    width: '40%'
                                                },
                                                {width: '5%', border: false},

                                                //---------------------------------------------

                                                {width: '5%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'producto',
                                                    fieldLabel: 'Producto',
                                                    displayField: data.nombreProducto,
                                                    value: data.nombreProducto,
                                                    readOnly: true,
                                                    width: 300
                                                },
                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tipoOrden',
                                                    fieldLabel: 'Tipo Orden',
                                                    displayField: data.tipoOrdenCompleto,
                                                    value: data.tipoOrdenCompleto,
                                                    readOnly: true,
                                                    width: '40%'
                                                },
                                                {width: '5%', border: false},

                                                //-----------------------------------------

                                                {width: '5%', border: false},
                                                {
                                                    xtype: 'textfield',                                              
                                                    name: 'capacidadUno',
                                                    fieldLabel: 'Capacidad Uno',
                                                    displayField: data.capacidadUno,
                                                    value: data.capacidadUno,                                                
                                                    readOnly: true,
                                                    width: '40%'
                                                },
                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',                                                
                                                    name: 'capacidadDos',
                                                    fieldLabel: 'Capacidad Dos',
                                                    displayField: data.capacidadDos,
                                                    value: data.capacidadDos,                                                
                                                    readOnly: true,
                                                    width: '40%'
                                                },
                                                {width: '5%', border: false}

                                                //---------------------------------------------
                                            ]
                                        }

                                    ]
                                }, //cierre de la informacion del cliente

                                //informacion del servicio/producto
                                {
                                    xtype: 'fieldset',
                                    title: 'Informacion de Backbone Actual',
                                    defaultType: 'textfield',
                                    defaults: {
                                        width: 600,
                                        height: 120
                                    },
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 5,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',                                                
                                                    name: 'elementoPadre',
                                                    fieldLabel: 'PE',
                                                    displayField: datosBackbone.nombreElementoPadre,
                                                    value: datosBackbone.nombreElementoPadre,                                                
                                                    readOnly: true,
                                                    width: 350
                                                },
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',                                               
                                                    name: 'vlan',
                                                    fieldLabel: 'Vlan',
                                                    displayField: data.vlan,
                                                    value: data.vlan,
                                                    readOnly: true,
                                                    width: 150
                                                },
                                                {width: '0%', border: false},

                                                //---------------------------------------------

                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',                                                
                                                    name: 'elemento',
                                                    fieldLabel: 'Switch',
                                                    displayField: datosBackbone.nombreElemento,
                                                    value: datosBackbone.nombreElemento,                                                
                                                    readOnly: true,
                                                    width: 350
                                                },
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',                                                
                                                    name: 'interface',
                                                    fieldLabel: 'Interface',
                                                    displayField: datosBackbone.nombreInterfaceElemento,
                                                    value: datosBackbone.nombreInterfaceElemento,
                                                    readOnly: true,
                                                    width: 150
                                                },
                                                {width: '0%', border: false},

                                                //---------------------------------------------

                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',                                                
                                                    name: 'elementoConector',
                                                    fieldLabel: 'Cassette',
                                                    displayField: datosBackbone.nombreSplitter,
                                                    value: datosBackbone.nombreSplitter,
                                                    readOnly: true,
                                                    width: 350
                                                },
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',                                               
                                                    name: 'hilo',
                                                    fieldLabel: 'Hilo',
                                                    displayField: datosBackbone.colorHilo,
                                                    value: datosBackbone.colorHilo,
                                                    readOnly: true,
                                                    width: 150
                                                },
                                                {width: '0%', border: false},

                                                //---------------------------------------------

                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',                                               
                                                    name: 'elementoContenedor',
                                                    fieldLabel: 'Caja',
                                                    displayField: datosBackbone.nombreCaja,
                                                    value: datosBackbone.nombreCaja,
                                                    readOnly: true,
                                                    width: 400
                                                },
                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tipoEnlace',
                                                    fieldLabel: 'Tipo Enlace',
                                                    displayField: data.tipoEnlace,
                                                    value: data.tipoEnlace,
                                                    readOnly: true,
                                                    width: '50%'
                                                },
                                                {width: '15%', border: false}
                                            ]
                                        }
                                    ]
                                }, //cierre de la informacion servicio/producto

                                //motivo de cancelacion
                                {
                                    xtype: 'fieldset',
                                    title: 'Motivo Cancelacion',
                                    defaultType: 'textfield',
                                    defaults: {
                                        width: 600
                                    },
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 5,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'combo',
                                                    id: 'comboMotivos',
                                                    name: 'comboMotivos',
                                                    store: storeMotivos,
                                                    fieldLabel: 'Motivo',
                                                    displayField: 'nombreMotivo',
                                                    valueField: 'idMotivo',
                                                    queryMode: 'local'
                                                },
                                                {width: '15%', border: false},
                                                {width: '30%', border: false},
                                                {width: '10%', border: false}
                                                //---------------------------------------------
                                            ]
                                        }

                                    ]
                                }//cierre del motivo de cancelacion

                            ]
                        }],
                    buttons: [{
                            text: 'Ejecutar',
                            formBind: true,
                            handler: function() {
                                var motivo = Ext.getCmp('comboMotivos').getValue();
                                var validacion = false;

                                if (motivo != null) {
                                    validacion = true;
                                }

                                if (validacion) {
                                    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Cancelar el Servicio?', function(btn) {
                                        if (btn == 'ok') {
                                            Ext.get(formPanel.getId()).mask('Esperando Respuesta del Elemento...');
                                            Ext.Ajax.request({
                                                url: urlCancelarClienteTN,
                                                method: 'post',
                                                timeout: 400000,
                                                params: {
                                                    idServicio: data.idServicio,
                                                    idProducto: data.productoId,                                                                                                                                 
                                                    idMotivo  : motivo,
                                                    idAccion  : idAccion,                                                                                             
                                                    vlan      : data.vlan,
                                                    mac       : datosBackbone.mac,
                                                    anillo    : data.anillo,
                                                    capacidadUno : data.capacidadUno,
                                                    capacidadDos : data.capacidadDos,
                                                    esPseudoPe   : data.esPseudoPe
                                                },
                                                success: function(response) {
                                                    Ext.get(formPanel.getId()).unmask();
                                                    if (response.responseText == "OK") {
                                                        Ext.Msg.alert('Mensaje', 'Se Canceló el Servicio', function(btn) {
                                                            if (btn == 'ok') {
                                                                store.load();
                                                                win.destroy();
                                                            }
                                                        });
                                                    }
                                                    else if (response.responseText == "NO EXISTE TAREA") {
                                                        Ext.Msg.alert('Mensaje ', 'No existe la Tarea, favor revisar!');
                                                    }
                                                    else if (response.responseText == "OK SIN EJECUCION") {
                                                        Ext.Msg.alert('Mensaje ', 'Se Corto el Servicio, Sin ejecutar Script');
                                                    }
                                                    else {
                                                        Ext.Msg.alert('Mensaje ', response.responseText);
                                                    }
                                                },
                                                failure: function(result)
                                                {
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                }
                                            });
                                        }
                                    });
                                }
                                else {
                                    Ext.Msg.alert("Advertencia", "Favor Escoja un Motivo", function(btn) {
                                        if (btn == 'ok') {
                                        }
                                    });
                                }


                            }
                        }, {
                            text: 'Cancelar',
                            handler: function() {
                                win.destroy();
                            }
                        }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Cancelar Servicio',
                    modal: true,
                    width: 680,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
            }
        }//cierre response
    }); 
}