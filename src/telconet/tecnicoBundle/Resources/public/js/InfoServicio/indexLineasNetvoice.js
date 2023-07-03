function activarLineaNetvoice(data, gridIndex) {

    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: url_getDatosLinea,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio
        },
        success: function(response) {
            Ext.get(gridServicios.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;


            var formPanel = Ext.create('Ext.form.Panel', {
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },
                items: [
                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Información del Servicio',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500,
                            height: 60
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
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    {width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'telefono',
                                        id: 'telefono',
                                        fieldLabel: 'Número',
                                        displayField: datos[0].numeroTelefono,
                                        value: datos[0].numeroTelefono,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    {width: '10%', border: false},
                                    //---------------------------------------------

                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'ultimaMilla',
                                        name: 'ultimaMilla',
                                        fieldLabel: 'Última Milla',
                                        displayField: data.ultimaMilla,
                                        value: data.ultimaMilla,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    {width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'dominio',
                                        name: 'dominio',
                                        fieldLabel: 'Dominio',
                                        displayField: datos[0].dominio,
                                        value: datos[0].dominio,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    {width: '10%', border: false},
                                    //---------------------------------------------

                                    {width: '10%', border: false},

                                    {width: '15%', border: false},
                                    {width: '10%', border: false}

                                ]
                            }

                        ]
                    }, //cierre de la informacion servicio/producto

                ],
                buttons: [{
                        text: 'Activar',
                        formBind: true,
                        handler: function() {

                            var dominio = Ext.getCmp('dominio').getValue();
                            var telefono = Ext.getCmp('telefono').getValue();

                            Ext.get(formPanel.getId()).mask('Activando el servicio!');


                            Ext.Ajax.request({
                                url: url_activarLinea,
                                method: 'post',
                                timeout: 400000,
                                params: {
                                    idServicio: data.idServicio,
                                    idProducto: data.productoId,
                                    login: data.login,
                                    dominio: dominio,
                                    telefono: telefono

                                },
                                success: function(response) {
                                    Ext.get(formPanel.getId()).unmask();
                                    if (response.responseText == "OK") {
                                        Ext.Msg.alert('Mensaje', 'Se Activo el Cliente', function(btn) {
                                            if (btn == 'ok') {
                                                win.destroy();
                                                store.load();
                                            }
                                        });
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
                    }, {
                        text: 'Cancelar',
                        handler: function() {
                            win.destroy();
                        }
                    }]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Activar Línea Telefónica',
                modal: true,
                width: 530,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();

        }//cierre response
    });

}


function cierraVentanaIngresoFactibilidad() {
    winIngresoFactibilidad.close();
    winIngresoFactibilidad.destroy();
}
