var connActCaracteristicas = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function (con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        }
    }
});

function cambiarCorreoEnServicioActivo(data)
{
    var valorCorreoActual   = data.strCorreoMcAfee;
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    columns: 2
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 250
                            },
                        items:
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'correoElectronico',
                                    name: 'correoElectronico',
                                    fieldLabel: 'Correo electrónico',
                                    displayField: '',
                                    value: data.strCorreoMcAfee,
                                    valueField: '',
                                    maxLength: 1900,
                                    width: '250',
                                    allowBlank: false
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Grabar',
                        formBind: true,
                        handler: function ()
                        {
                            var strCorreoSuscripcion = Ext.getCmp('correoElectronico').value;
                            var booleanCorreoValido = validaCorreo(strCorreoSuscripcion);
                            if (Ext.isEmpty(strCorreoSuscripcion))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese el correo correspondiente!");
                            }
                            else if (!booleanCorreoValido)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo válido!");
                            }
                            else if (valorCorreoActual === strCorreoSuscripcion)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo diferente al actual!");
                            }
                            else
                            {
                                connActCaracteristicas.request({
                                        url: strUrlCambiarCorreoEnServicioActivo,
                                        method: 'post',
                                        waitMsg: 'Esperando Respuesta...',
                                        timeout: 400000,
                                        params:
                                            {
                                                intIdServicio: data.idServicio,
                                                intProductoId: data.intProductoMcAfeeId,
                                                strCorreoSuscripcion: strCorreoSuscripcion
                                            },
                                        success: function (response)
                                        {
                                            var objData = Ext.JSON.decode(response.responseText);
                                            var strStatus = objData.status;
                                            var strMensaje = objData.mensaje;

                                            if (strStatus == "ERROR")
                                            {
                                                Ext.Msg.alert('Error ', strMensaje);
                                            } else
                                            {
                                                Ext.Msg.alert('MENSAJE ', 'Se actualizó la información correctamente.');
                                            }
                                            store.load();
                                        },
                                        failure: function (result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                winActualizarCorreo.destroy();
                            }

                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function ()
                        {
                            winActualizarCorreo.destroy();
                        }
                    }
                ]
        });

    var winActualizarCorreo = Ext.create('Ext.window.Window',
        {
            title: 'Solicitar Nuevo subscriber  Id',
            modal: true,
            width: 300,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function actualizacionCorreo(data)
{
    var valorCorreoActual   = data.strCorreoMcAfee;
    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            layout:
                {
                    type: 'table',
                    columns: 2
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 250
                            },
                        items:
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'correoElectronico',
                                    name: 'correoElectronico',
                                    fieldLabel: 'Correo electrónico',
                                    displayField: '',
                                    value: data.strCorreoMcAfee,
                                    valueField: '',
                                    maxLength: 1900,
                                    width: '250',
                                    allowBlank: false
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Grabar',
                        formBind: true,
                        handler: function ()
                        {
                            var strCorreoSuscripcionNuevo = Ext.getCmp('correoElectronico').value;
                            var booleanCorreoValido = validaCorreo(strCorreoSuscripcionNuevo);
                            if (Ext.isEmpty(strCorreoSuscripcionNuevo))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese el correo correspondiente!");
                            }
                            else if (!booleanCorreoValido)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo válido!");
                            }
                            else if (valorCorreoActual === strCorreoSuscripcionNuevo)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo diferente al actual!");
                            }
                            else
                            {
                                connActCaracteristicas.request({
                                        url: strUrlActualizacionCorreo,
                                        method: 'post',
                                        waitMsg: 'Esperando Respuesta...',
                                        timeout: 400000,
                                        params:
                                            {
                                                intIdServicio: data.idServicio,
                                                intProductoId: data.intProductoMcAfeeId,
                                                strCorreoSuscripcionNuevo: strCorreoSuscripcionNuevo
                                            },
                                        success: function (response)
                                        {
                                            var objData = Ext.JSON.decode(response.responseText);
                                            var strStatus = objData.status;
                                            var strMensaje = objData.mensaje;

                                            if (strStatus == "ERROR")
                                            {
                                                Ext.Msg.alert('Error ', strMensaje);
                                            } else
                                            {
                                                Ext.Msg.alert('MENSAJE ', 'Se actualizó la información correctamente.');
                                            }
                                            store.load();
                                        },
                                        failure: function (result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                winActualizarCorreo.destroy();
                            }

                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function ()
                        {
                            winActualizarCorreo.destroy();
                        }
                    }
                ]
        });

    var winActualizarCorreo = Ext.create('Ext.window.Window',
        {
            title: 'Cambio de Correo',
            modal: true,
            width: 300,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function reintentarActivacionServicio(data,gridIndex, accion){
    Ext.get(gridServicios.getId()).mask('Reintentando activación...');
    
    Ext.Ajax.request({
        url: strUrlReintentarActivacionServicio,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio
        },
        success: function(response)
        {
            Ext.get(gridServicios.getId()).unmask();
            var objData = Ext.JSON.decode(response.responseText);
            var strStatus = objData.status;
            var strMensaje = objData.mensaje;
            if (strStatus == "OK") {
                Ext.Msg.alert('Mensaje',strMensaje, function(btn){
                    if(btn=='ok'){
                        store.load();
                    }
                });
            }
            else{
                Ext.Msg.alert('Mensaje ','No se pudo realizar la activación <br>'+strMensaje );
            }
        },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
}

function validaCorreo(correo) {
    var respuesta = false;
    var RegExPattern = Utils.REGEX_MAIL;
    if ((correo.match(RegExPattern)) && (correo.value != '')) {
        respuesta = true;
    }
    return respuesta;
}