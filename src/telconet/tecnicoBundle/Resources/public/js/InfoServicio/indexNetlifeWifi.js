function agregarCredencialesWifi(data) {

    var nombreProducto = data.nombreProducto;

    Ext.get(gridServicios.getId()).mask('Generando Credenciales...');
    Ext.Ajax.request({
        url: ajaxGenerarCredencialesWifi,
        method: 'post',
        timeout: 400000,
        params: {
            servicioId: data.idServicio,
            strTipo: 'Crear'
        },
        success: function (response) {
            Ext.get(gridServicios.getId()).unmask();
            var credenciales = Ext.JSON.decode(response.responseText);

            if (credenciales.strStatus === "OK")
            {
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
                            title: '',
                            defaultType: 'textfield',
                            defaults: {
                                width: 200
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    id: 'usuario',
                                    name: 'usuario',
                                    readOnly: true,
                                    fieldLabel: 'Usuario',
                                    displayField: 'Usuario',
                                    value: credenciales.arrayData.strUsuario,
                                    width: '30%'
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'clave',
                                    name: 'clave',
                                    readOnly: true,
                                    fieldLabel: 'Clave',
                                    displayField: 'Clave',
                                    value: credenciales.arrayData.strClave,
                                    width: '30%'
                                }
                            ]
                        }//cierre credenciales wifi
                    ],
                    buttons: [{
                            text: 'Guardar',
                            formBind: true,
                            handler: function () {
                                var usuario = Ext.getCmp('usuario').getValue();
                                var clave   = Ext.getCmp('clave').getValue();

                                Ext.Msg.alert('Mensaje', 'Esta seguro que desea Activar el Servicio '+nombreProducto+'?', function (btn) {
                                    if (btn == 'ok')
                                    {
                                        Ext.get(formPanel.getId()).mask('Activando el servicio '+nombreProducto+'...');
                                        Ext.Ajax.request({
                                            url: agregarServicioWifi,
                                            method: 'post',
                                            timeout: 400000,
                                            params:
                                                    {
                                                        idServicio: data.idServicio,
                                                        usuario: usuario,
                                                        clave: clave
                                                    },
                                            success: function (response) {
                                                Ext.get(formPanel.getId()).unmask();
                                                var credenciales = Ext.JSON.decode(response.responseText);
                                                if (credenciales.strStatus === 'OK')
                                                {
                                                    Ext.Msg.alert('Mensaje', 'Se Activó el Servicio '+nombreProducto+'!', function (btn) {
                                                        if (btn == 'ok')
                                                        {
                                                            store.load();
                                                            win.destroy();
                                                        }
                                                    });
                                                } else {
                                                    Ext.Msg.alert('Mensaje ', credenciales.strMensaje);
                                                }
                                            },
                                            failure: function (result)
                                            {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                            }
                        }, {
                            text: 'Cancelar',
                            handler: function () {
                                win.destroy();
                            }
                        }]
                });


                var win = Ext.create('Ext.window.Window', {
                    title: 'Activar Servicio '+nombreProducto,
                    modal: true,
                    width: 300,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
            }
            else if(credenciales.strStatus === "DOC-ERROR")
            {
                Ext.Msg.alert('Error ',credenciales.strMensaje);
            }
            else
            {
                Ext.Msg.alert('Error ', 'Error al generar credenciales.');
            }
        }
    });
}

function resetearCredencialesWifi(data) {

    var nombreProducto = data.nombreProducto;

    Ext.get(gridServicios.getId()).mask('Generando Credenciales...');
    Ext.Ajax.request({
        url: ajaxGenerarCredencialesWifi,
        method: 'post',
        timeout: 400000,
        params: {
            servicioId: data.idServicio,
            strTipo: 'Resetear'
        },
        success: function (response) {
            Ext.get(gridServicios.getId()).unmask();
            var credenciales = Ext.JSON.decode(response.responseText);
            if (credenciales.strStatus === "OK")
            {
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
                            title: '',
                            defaultType: 'textfield',
                            defaults: {
                                width: 200
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    id: 'usuario',
                                    name: 'usuario',
                                    fieldLabel: 'Usuario',
                                    readOnly: true,
                                    displayField: "",
                                    value: credenciales.arrayData.strUsuario,
                                    width: '30%'
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'clave',
                                    name: 'clave',
                                    readOnly: true,
                                    fieldLabel: 'Clave',
                                    displayField: "",
                                    value: credenciales.arrayData.strClave,
                                    width: '30%'
                                }
                            ]
                        }//cierre interfaces cpe
                    ],
                    buttons: [{
                            text: 'Guardar',
                            formBind: true,
                            handler: function () {
                                var usuario = Ext.getCmp('usuario').getValue();
                                var clave = Ext.getCmp('clave').getValue();
                                Ext.Msg.alert('Mensaje', 'Esta seguro que desea recuperar la clave del Servicio '+nombreProducto+'?', function (btn) {
                                    if (btn == 'ok')
                                    {
                                        Ext.get(formPanel.getId()).mask('Actualizando Clave del Servicio '+nombreProducto+'...');
                                        Ext.Ajax.request({
                                            url: recuperarCredencialesWifi,
                                            method: 'post',
                                            timeout: 400000,
                                            params:
                                                    {
                                                        idServicio: data.idServicio,
                                                        usuario: usuario,
                                                        clave: clave
                                                    },
                                            success: function (response) {
                                                Ext.get(formPanel.getId()).unmask();
                                                var credenciales = Ext.JSON.decode(response.responseText);
                                                if (credenciales.strStatus === "OK")
                                                {
                                                    Ext.Msg.alert('Mensaje', 'Actualizó la clave del Servicio '+nombreProducto+'!', function (btn) {
                                                        if (btn == 'ok')
                                                        {
                                                            store.load();
                                                            win.destroy();
                                                        }
                                                    });
                                                } else {
                                                    Ext.Msg.alert('Mensaje ', credenciales.strMensaje);
                                                }
                                            },
                                            failure: function (result)
                                            {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                            }
                        }, {
                            text: 'Cancelar',
                            handler: function () {
                                win.destroy();
                            }
                        }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Recuperar Credenciales Servicio '+nombreProducto+'',
                    modal: true,
                    width: 300,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
            } else
            {
                Ext.Msg.alert('Error ', 'Error al generar credenciales.');
            }
        }
    });
}

function cancelarServiceWifi(data, idAccion) {

    var nombreProducto = data.nombreProducto;

    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Cancelar el Servicio '+nombreProducto+'?', function (btn) {
        if (btn == 'ok') {
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: ajaxOperacionesNetlifeWifi,
                method: 'get',
                timeout: 400000,
                params: {
                    idServicio: data.idServicio,
                    idAccion: idAccion,
                    strProceso: "Cancel"
                },
                success: function (response) {
                    Ext.get("grid").unmask();

                    if (response.responseText == "OK") {
                        Ext.Msg.alert('Mensaje', 'Se Cancelo el Servicio!', function (btn) {
                            if (btn == 'ok') {
                                store.load();
                                win.destroy();
                            }
                        });
                    } else {
                        Ext.Msg.alert('Mensaje ', response.responseText);
                    }

                }

            });
        }
    });
}

function cortarServicioNetlifeWifi(data, idAccion) {

    var nombreProducto = data.nombreProducto;

    Ext.Msg.alert('Mensaje', 'Esta seguro que desea cortar el Servicio '+nombreProducto+'?', function (btn) {
        if (btn == 'ok') {
            Ext.get("grid").mask('Cortando el Servicio...');
            Ext.Ajax.request({
                url: ajaxOperacionesNetlifeWifi,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio: data.idServicio,
                    idAccion: idAccion,
                    strProceso: "Cortar"
                },
                success: function (response) {
                    Ext.get("grid").unmask();
                    if (response.responseText == "OK") {
                        Ext.Msg.alert('Mensaje', 'Se ha cortado el servicio!', function (btn) {
                            if (btn == 'ok') {
                                store.load();
                                win.destroy();
                            }
                        });
                    } else {
                        Ext.Msg.alert('Mensaje ', response.responseText);
                    }
                }

            });
        }
    });
}

function reconectarServicioNetlifeWifi(data, idAccion) {

    var nombreProducto = data.nombreProducto;

    Ext.Msg.alert('Mensaje', 'Esta seguro que desea reconectar el Servicio '+nombreProducto+'?', function (btn) {
        if (btn == 'ok') {
            Ext.get("grid").mask('Reconectando el Servicio...');
            Ext.Ajax.request({
                url: ajaxOperacionesNetlifeWifi,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio: data.idServicio,
                    idAccion: idAccion,
                    strProceso: "Reactivar"
                },
                success: function (response) {
                    Ext.get("grid").unmask();
                    if (response.responseText == "OK") {
                        Ext.Msg.alert('Mensaje', 'Se ha reconectado el servicio!', function (btn) {
                            if (btn == 'ok') {
                                store.load();
                                win.destroy();
                            }
                        });
                    } else {
                        Ext.Msg.alert('Mensaje ', response.responseText);
                    }
                }

            });
        }
    });
}