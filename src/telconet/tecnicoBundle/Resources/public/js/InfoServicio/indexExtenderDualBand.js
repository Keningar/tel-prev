/**
 * confirmarServicioExtenderDualBand
 * 
 * Función que sirve para confirmar los servicios Extender Dual Band de MD
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 28-11-2018
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.1 19-04-2021 Se modifica la obtención de modelos permitidos por tipo de equipo
 * 
 * @param data
 * @param idAccion
 */
function confirmarServicioExtenderDualBand(data, idAccion, tipoServicio)
{
    var storeModelosExtenderDualBand = new Ext.data.Store({
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: strUrlGetModelosEquiposPorTecnologia,
            extraParams: {
                strTipoEquipos: 'EXTENDER DUAL BAND',
                intIdServicio: data.idServicio
            },
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayRegistros'
            }
        },
        fields:
            [
                {name: 'strNombreModelo', mapping: 'strNombreModelo'}
            ]
    });

    var confirmarFormPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            columns: 1
        },
        defaults: {
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
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
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'login',
                                fieldLabel: 'Login',
                                value: data.login,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'estado',
                                fieldLabel: 'Estado',
                                value: data.estado,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'producto',
                                fieldLabel: 'Producto',
                                value: data.nombreProducto,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {width: 25, border: false},

                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                id: 'observacionActivarServicio',
                                xtype: 'textarea',
                                name: 'observacionActivarServicio',
                                fieldLabel: '* Observación',
                                value: "",
                                readOnly: false,
                                required: true,
                                width: 280
                            },
                            {
                                id: 'campoVacio',
                                name: 'campoVacio',
                                width: 280,
                                border: false
                            },
                            {width: 50, border: false},
                            {width: 200, border: false},
                            {width: 25, border: false}
                        ]
                    }

                ]
            },
            {
                xtype: 'fieldset',
                title: 'Información del Equipo Extender Dual Band',
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
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'serieExtenderDualBand',
                                name: 'serieExtenderDualBand',
                                fieldLabel: 'Serie',
                                displayField: "",
                                value: "",
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                queryMode: 'local',
                                xtype: 'combobox',
                                id: 'modeloExtenderDualBand',
                                name: 'modeloExtenderDualBand',
                                fieldLabel: 'Modelo',
                                displayField: 'strNombreModelo',
                                valueField: 'strNombreModelo',
                                loadingText: 'Buscando...',
                                store: storeModelosExtenderDualBand,
                                width: 225,
                                listeners: {
                                    blur: function (combo) {
                                        Ext.getCmp('btnConfirmar').setDisabled(true);
                                        Ext.Ajax.request({
                                            url: buscarCpeNaf,
                                            method: 'post',
                                            params: {
                                                serieCpe: Ext.getCmp('serieExtenderDualBand').getValue(),
                                                modeloElemento: combo.getValue(),
                                                estado: 'PI',
                                                bandera: 'ActivarServicio'
                                            },
                                            success: function (response)
                                            {
                                                Ext.getCmp('btnConfirmar').setDisabled(false);
                                                var respuesta = response.responseText.split("|");
                                                var status = respuesta[0];
                                                var mensaje = respuesta[1];

                                                if (status == "OK")
                                                {
                                                    Ext.getCmp('descripcionExtenderDualBand').setValue = mensaje;
                                                    Ext.getCmp('descripcionExtenderDualBand').setRawValue(mensaje);
                                                    var arrayInformacionWifi = mensaje.split(",");
                                                    Ext.getCmp('macExtenderDualBand').setValue = arrayInformacionWifi[1];
                                                    Ext.getCmp('macExtenderDualBand').setRawValue(arrayInformacionWifi[1]);
                                                } else
                                                {
                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                    Ext.getCmp('descripcionExtenderDualBand').setValue = status;
                                                    Ext.getCmp('descripcionExtenderDualBand').setRawValue(status);
                                                    Ext.getCmp('macExtenderDualBand').setValue = "";
                                                    Ext.getCmp('macExtenderDualBand').setRawValue("");
                                                }
                                            },
                                            failure: function (result)
                                            {
                                                Ext.getCmp('btnConfirmar').setDisabled(false);
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                Ext.getCmp('macExtenderDualBand').setValue = "";
                                                Ext.getCmp('macExtenderDualBand').setRawValue("");
                                            }
                                        });
                                    }
                                }
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'macExtenderDualBand',
                                name: 'macExtenderDualBand',
                                fieldLabel: 'Mac',
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'descripcionExtenderDualBand',
                                name: 'descripcionExtenderDualBand',
                                fieldLabel: 'Descripción',
                                displayField: "",
                                value: "",
                                readOnly: true,
                                width: 225
                            },
                            {width: 25, border: false}
                        ]
                    }
                ]
            }
        ],
        buttons: [{
                id: 'btnConfirmar',
                text: 'Confirmar',
                formBind: true,
                handler: function () {

                    var strObservacion = Ext.getCmp('observacionActivarServicio').getValue();
                    var strSerieExtenderDualBand = Ext.getCmp('serieExtenderDualBand').getValue();
                    var strModeloExtenderDualBand = Ext.getCmp('modeloExtenderDualBand').getValue();
                    var strMacExtenderDualBand = Ext.getCmp('macExtenderDualBand').getValue();
                    var strDescripcionExtenderDualBand = Ext.getCmp('descripcionExtenderDualBand').getValue();
                    var booleanValidacion = true;
                    intBanderaErroflag = 0;

                    if (Ext.isEmpty(strObservacion) && tipoServicio === "PRODUCTO")
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 1;
                    } else if (Ext.isEmpty(strMacExtenderDualBand))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 2;
                    } else if (strDescripcionExtenderDualBand == "ELEMENTO ESTADO INCORRECTO" ||
                        strDescripcionExtenderDualBand == "ELMENTO CON SALDO CERO" ||
                        strDescripcionExtenderDualBand == "NO EXISTE ELEMENTO")
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 3;
                    } else if (Ext.isEmpty(strSerieExtenderDualBand))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 4;
                    } else if (Ext.isEmpty(strModeloExtenderDualBand))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 5;
                    }
                    if (booleanValidacion)
                    {
                        var idSolicitud = 0;
                        if(tipoServicio === "MIGRACION")
                        {
                            idSolicitud = data.idSolicitudMigracionExtender;
                        }
                        else
                        {
                            idSolicitud = data.tieneSolicitudAgregarEquipo;
                        }
                        Ext.get(confirmarFormPanel.getId()).mask('Cargando...');
                        Ext.Ajax.request({
                            url: strUrlConfirmarServicioExtenderDualBand,
                            method: 'post',
                            timeout: 400000,
                            params: {
                                idServicio: data.idServicio,
                                strObservacionServicio: strObservacion,
                                idAccion: idAccion,
                                strSerieExtenderDualBand: strSerieExtenderDualBand,
                                strModeloExtenderDualBand: strModeloExtenderDualBand,
                                strMacExtenderDualBand: strMacExtenderDualBand,
                                intIdServicioInternet: data.idServicioRefIpFija,
                                intIdSolicitudServicio: idSolicitud,
                                strTipoServicio: tipoServicio
                            },
                            success: function (response) {
                                Ext.get(confirmarFormPanel.getId()).unmask();
                                var objData = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.status;
                                var strMensaje = objData.mensaje;
                                if (strStatus == "OK") {
                                    win.destroy();
                                    Ext.Msg.alert('Mensaje', strMensaje, function (btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });
                                } else {
                                    Ext.Msg.alert('Mensaje ', 'Error:' + strMensaje);
                                }
                            },
                            failure: function (result)
                            {
                                Ext.get(confirmarFormPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    } else
                    {
                        if (intBanderaErroflag == 1)
                        {
                            Ext.Msg.alert("Validación", "Por favor ingrese la observación correspondiente!");
                        } else if (intBanderaErroflag == 2)
                        {
                            Ext.Msg.alert("Validación", "No existe valor de Mac, favor revisar!");
                        } else if (intBanderaErroflag == 3)
                        {
                            Ext.Msg.alert("Validación", "Datos del Wifi incorrectos, favor revisar!");
                        } else if (intBanderaErroflag == 4)
                        {
                            Ext.Msg.alert("Validación", "Por favor ingrese la serie correspondiente!");
                        } else if (intBanderaErroflag == 5)
                        {
                            Ext.Msg.alert("Validación", "Por favor ingrese el modelo correspondiente!");
                        } else
                        {
                            Ext.Msg.alert("Failed", "Existen campos vacíos. Por favor revisar.");
                        }
                    }
                }
            }, {
                text: 'Cancelar',
                handler: function () {
                    win.destroy();
                }
            }]
    });
    var tituloVentana = "";
    if(tipoServicio === "PRODUCTO")
    {
        Ext.getCmp('observacionActivarServicio').setVisible(true);
        Ext.getCmp('campoVacio').setVisible(false);
        tituloVentana = 'Confirmar Servicio Extender Dual Band';
    }
    else if(tipoServicio === "MIGRACION")
    {
        Ext.getCmp('observacionActivarServicio').setVisible(false);
        Ext.getCmp('campoVacio').setVisible(true);
        tituloVentana = 'Agregar Equipo Extender Dual Band Por Migración';
    }
    else
    {
        Ext.getCmp('observacionActivarServicio').setVisible(false);
        Ext.getCmp('campoVacio').setVisible(true);
        tituloVentana = 'Agregar Equipo Extender Dual Band';
    }
    var win = Ext.create('Ext.window.Window', {
        title: tituloVentana,
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [confirmarFormPanel]
    }).show();

    storeModelosExtenderDualBand.load();
}

function cancelarServicioExtenderDualBand(data, idAccion)
{
    Ext.Msg.alert('Mensaje', 'Está seguro que desea Cancelar el Servicio?', function (btn) {
        if (btn == 'ok') {
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: cancelarServicioApWifiBoton,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio: data.idServicio,
                    idAccion: idAccion,
                    esExtenderDualBand: "SI"
                },
                success: function (response) {
                    Ext.get("grid").unmask();
                    var objData = Ext.JSON.decode(response.responseText);
                    var strStatus = objData.strStatus;
                    var strMensaje = objData.strMensaje;
                    if (strStatus == "OK") {
                        Ext.Msg.alert('Mensaje', 'Se Canceló el Servicio! '+strMensaje, function (btn) {
                            if (btn == 'ok') {
                                store.load();
                            }
                        });
                    } else {
                        Ext.Msg.alert('Mensaje ', 'No se pudo cancelar el servicio!');
                    }

                }

            });
        }
    });
}