
/* Función que sirve para mostrar la pantalla de cambio de plan para los servicios Internet Small Business 
 * y realiza la llamada ajax para la ejecucion de scripts y actualizacion en la base de datos sobre el servicio para la
 * empresa TN
 * 
 * @author      Lizbeth Cruz <mlcruz@telconet.ec>
 * @version     1.0     05-03-2018
 * @param Array data    Informacion que fue cargada en el grid
 */
function cambioPlanClienteIsbTn(data){
    Ext.MessageBox.wait('Consultando datos. Por favor espere..');
    Ext.Ajax.request({
        url: strUrlGetCaractProdFuncionPrecio,
        method: 'post',
        timeout: 400000,
        params:
            {
                idServicio: data.idServicio,
                productoId: data.productoId
            },
        success: function (response)
        {
            Ext.MessageBox.hide();

            var datosCaracts = Ext.JSON.decode(response.responseText);

            if (datosCaracts.strStatusGetInfo === "OK")
            {
                var storeVelocidadesIsb = new Ext.data.Store({
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url: strUrlGetVelocidadesIsb,
                        reader: {
                            type: 'json',
                            root: 'arrayRegistros'
                        },
                        extraParams:
                            {
                                intIdProducto: data.productoId,
                                strNombreTecnicoProducto: data.descripcionProducto
                            }
                    },
                    fields:
                        [
                            {name: 'valor1', type: 'string'}
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
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Información Actual',
                            defaultType: 'textfield',
                            defaults: {
                                width: 650
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
                                            xtype: 'textfield',
                                            name: 'nombreProducto',
                                            fieldLabel: 'Producto',
                                            displayField: data.nombreProducto,
                                            value: data.nombreProducto,
                                            readOnly: true,
                                            width: 300
                                        },
                                        {width: '0%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.login,
                                            value: data.login,
                                            readOnly: true,
                                            width: 300
                                        },
                                        {width: '0%', border: false},
                                        
                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'velocidad',
                                            name: 'velocidad',
                                            fieldLabel: 'Velocidad(MB)',
                                            displayField: data.velocidadISB,
                                            value: data.velocidadISB,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'precio',
                                            fieldLabel: 'Precio $',
                                            displayField: datosCaracts.precioVenta,
                                            value: datosCaracts.precioVenta,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'descripcionFactura',
                                            fieldLabel: 'Descripción Factura',
                                            displayField: data.descripcionPresentaFactura,
                                            value: data.descripcionPresentaFactura,
                                            readOnly: true,
                                            width:300,
                                            colspan:4
                                        },
                                        { width: '10%', border: false}
                                    ]
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Información Nueva',
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
                                            allowBlank: false,
                                            store: storeVelocidadesIsb,
                                            id: 'comboVelocidadesIsb',
                                            name: 'comboVelocidadesIsb',
                                            fieldLabel: 'Velocidad(MB)',
                                            displayField: 'valor1',
                                            valueField: 'valor1',
                                            queryMode: 'local',
                                            editable: false,
                                            width: '30%',
                                            listeners:
                                                {
                                                    select: function(combo, record, index)
                                                    {
                                                        var respuestaPrecios = calcularValoresProductoIsb(datosCaracts);
                                                        var precioProducto  = respuestaPrecios["precioISB"];
                                                        Ext.getCmp('precioNuevo').setRawValue(precioProducto);
                                                        Ext.getCmp('precioNuevo').setValue(precioProducto);
                                                        Ext.getCmp('descripcionFacturaNueva').setRawValue(data.nombreProducto+" "+
                                                            Ext.getCmp('comboVelocidadesIsb').value);
                                                        Ext.getCmp('descripcionFacturaNueva').setValue(data.nombreProducto+" "+
                                                            Ext.getCmp('comboVelocidadesIsb').value);
                                                    }
                                                }
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'precioNuevo',
                                            name: 'precioNuevo',
                                            fieldLabel: 'Precio $',
                                            displayField: "",
                                            value: "",
                                            readOnly: true,
                                            width: '30%',
                                            labelStyle: 'padding-left: 20px; padding-left: 20px;'
                                        },
                                        {width: '10%', border: false},
                                        
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'descripcionFacturaNueva',
                                            name: 'descripcionFacturaNueva',
                                            fieldLabel: 'Descripción Factura',
                                            value: '',
                                            readOnly: true,
                                            width:300,
                                            colspan:4
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'precioNuevoIp',
                                            name: 'precioNuevoIp',
                                            displayField: '',
                                            value: '',
                                            hidden: true
                                        }
                                    ]
                                }

                            ]
                        }
                    ],
                    buttons: [{
                            text: 'Ejecutar',
                            formBind: true,
                            handler: function () {
                                Ext.get(formPanel.getId()).mask('Realizando cambio de velocidad...');
                                var velocidadNueva      = Ext.getCmp('comboVelocidadesIsb').getValue();
                                var velocidadAnterior   = Ext.getCmp('velocidad').getValue();
                                var precioNuevo         = Ext.getCmp('precioNuevo').getValue();
                                if(velocidadNueva === velocidadAnterior)
                                {
                                    Ext.Msg.alert("Error", "La velocidad nueva no puede ser igual a la anterior", function (btn) {
                                        if (btn == 'ok') {
                                            Ext.get(formPanel.getId()).unmask();
                                        }
                                    });
                                }
                                else
                                {
                                    Ext.Ajax.request({
                                        url: cambioVelocidad,
                                        method: 'post',
                                        timeout: 900000,
                                        params: {
                                            idServicio: data.idServicio,
                                            precioNuevo: precioNuevo,
                                            velocidadNueva: velocidadNueva,
                                            velocidadAnterior: velocidadAnterior,
                                            esIsb: data.esISB
                                        },
                                        success: function (response) {
                                            Ext.get(formPanel.getId()).unmask();
                                            var objData     = Ext.JSON.decode(response.responseText);
                                            var strStatus   = objData.status;
                                            var strMensaje  = objData.mensaje;
                                            if(strStatus == "OK") {
                                                Ext.Msg.alert('Mensaje', strMensaje, function (btn) {
                                                    if (btn == 'ok') {
                                                        win.destroy();
                                                        store.load();
                                                    }
                                                });
                                            }else{
                                                Ext.Msg.alert('Mensaje ', strMensaje);
                                            }
                                        },
                                        failure: function (result)
                                        {
                                            Ext.get(formPanel.getId()).unmask();
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                }
                            }
                        }, {
                            text: 'Cancelar',
                            handler: function () {
                                win.destroy();
                            }
                        }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Cambio de Velocidad',
                    modal: true,
                    width: 665,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

            } else
            {
                Ext.Msg.alert('Error ', 'No se ha podido obtener la información correctamente');
            }
        },
        failure: function ()
        {
            Ext.MessageBox.hide();
        }
    });
}
