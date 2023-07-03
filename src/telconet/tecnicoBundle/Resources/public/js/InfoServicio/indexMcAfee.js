var connActCaracteristicas = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
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

function reintentarActivacionMcAfeeEnPlan(data,gridIndex, accion){
    Ext.get(gridServicios.getId()).mask('Reintentando activación...');
    
    Ext.Ajax.request({
        url: strUrlReintentarActivacionMcAfeeEnPlan,
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

function actualizarCorreoMcAfeeEnPlanPorReintento(data,gridIndex, accion)
{
    var valorAct = data.strCorreoMcAfee;
    var estadoCaracteristica = 'Activo';
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
                        handler: function()
                        {
                            var strCorreoMcAfee     = Ext.getCmp('correoElectronico').value;
                            var booleanCorreoValido = validaCorreo(strCorreoMcAfee);
                            if (Ext.isEmpty(strCorreoMcAfee))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese el correo correspondiente!");
                            }
                            else if (!booleanCorreoValido)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo válido!");
                            }
                            else if (valorAct === strCorreoMcAfee)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo diferente al actual!");
                            }
                            else
                            {
                                connActCaracteristicas.request
                                    ({
                                        url: actualizarCaracteristica,
                                        method: 'post',
                                        waitMsg: 'Esperando Respuesta del Elemento',
                                        timeout: 400000,
                                        params:
                                            {
                                                idServicioProdCaract: data.intIdCaractCorreoMcAfee,
                                                valor: Ext.getCmp('correoElectronico').value,
                                                estado: estadoCaracteristica,
                                                caracteristica: 'CORREO ELECTRONICO'
                                            },
                                        success: function(response)
                                        {
                                            var respuesta = response.responseText;

                                            if (respuesta == "Error")
                                            {
                                                Ext.Msg.alert('Error ', 'Se presentaron problemas al actualizar la información,' +
                                                    ' favor notificar a Sistemas');
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('MENSAJE ', 'Se actualizó la información correctamente.');
                                                
                                            }
                                            store.load();
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                winValorCaract.destroy();
                            }

                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winValorCaract.destroy();
                        }
                    }
                ]
        });

    var winValorCaract = Ext.create('Ext.window.Window',
        {
            title: 'Actualizar valor de Característica - Pendiente Activación',
            modal: true,
            width: 300,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function actualizarCorreoMcAfeeEnPlanServicioActivo(data,gridIndex, accion)
{
    var valorAct = data.strCorreoMcAfee;
    var estadoCaracteristica = 'Activo';
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
                        handler: function()
                        {
                            var strCorreoMcAfee     = Ext.getCmp('correoElectronico').value;
                            var booleanCorreoValido = validaCorreo(strCorreoMcAfee);
                            if (Ext.isEmpty(strCorreoMcAfee))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese el correo correspondiente!");
                            }
                            else if (!booleanCorreoValido)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo válido!");
                            }
                            else if (valorAct === strCorreoMcAfee)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo diferente al actual!");
                            }
                            else
                            {
                                connActCaracteristicas.request
                                    ({
                                        url: actualizarCorreoSuscripcionMcAfee,
                                        method: 'post',
                                        waitMsg: 'Esperando Respuesta del Elemento',
                                        timeout: 400000,
                                        params:
                                            {
                                                intIdServicio: data.idServicio,
                                                intProductoId: data.intProductoMcAfeeId,
                                                strCorreoMcAfee: strCorreoMcAfee
                                            },
                                        success: function(response)
                                        {
                                            var objData    = Ext.JSON.decode(response.responseText);
                                            var strStatus  = objData.strStatus;
                                            var strMensaje = objData.strMensaje;

                                            if (strStatus == "ERROR")
                                            {
                                                Ext.Msg.alert('Error ', strMensaje);
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('MENSAJE ', 'Se actualizó la información correctamente.');
                                                
                                            }
                                            store.load();
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                winValorCaract.destroy();
                            }

                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winValorCaract.destroy();
                        }
                    }
                ]
        });

    var winValorCaract = Ext.create('Ext.window.Window',
        {
            title: 'Actualizar correo de Suscripción activa McAfee',
            modal: true,
            width: 300,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function validaCorreo(correo) {
    var respuesta = false;
    var RegExPattern = Utils.REGEX_MAIL;
    if ((correo.match(RegExPattern)) && (correo.value != '')) {
        respuesta = true;
    } else {
        respuesta = false;
    }
    return respuesta;
}

function reenvioCorreoKasperskyEnPlanServicioActivo(data,gridIndex, accion)
{
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
                                    readOnly:true,
                                    allowBlank: false
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Reenviar',
                        formBind: true,
                        handler: function()
                        {
                            var strCorreoMcAfee     = Ext.getCmp('correoElectronico').value;
                            var booleanCorreoValido = validaCorreo(strCorreoMcAfee);
                            if (Ext.isEmpty(strCorreoMcAfee))
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese el correo correspondiente!");
                            }
                            else if (!booleanCorreoValido)
                            {
                                Ext.Msg.alert("Alerta", "Favor ingrese un correo válido!");
                            }
                            else
                            {
                                connActCaracteristicas.request({
                                        url: reenvioCorreoKaspersky,
                                        method: 'post',
                                        waitMsg: 'Esperando Respuesta del Elemento',
                                        timeout: 400000,
                                        params:
                                            {
                                                intIdServicio: data.idServicio,
                                                intProductoId: data.intProductoMcAfeeId,
                                                strCorreoMcAfee: strCorreoMcAfee
                                            },
                                        success: function(response)
                                        {
                                            var objData    = Ext.JSON.decode(response.responseText);
                                            var strStatus  = objData.strStatus;
                                            var strMensaje = objData.strMensaje;

                                            if (strStatus == "ERROR")
                                            {
                                                Ext.Msg.alert('Error ', strMensaje);
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('MENSAJE ', 'Se reenvió la información correctamente.');
                                                
                                            }
                                            store.load();
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                winValorCaract.destroy();
                            }

                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            winValorCaract.destroy();
                        }
                    }
                ]
        });

    var winValorCaract = Ext.create('Ext.window.Window',
        {
            title: 'Se enviará al correo que se presenta en la pantalla',
            modal: true,
            width: 300,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}