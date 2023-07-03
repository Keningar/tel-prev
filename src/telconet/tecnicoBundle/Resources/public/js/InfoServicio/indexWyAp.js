/**
 * confirmarServicioWyAp
 * 
 * Función que sirve para confirmar los servicios Extender Dual Band de MD
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 22-09-2020
 * @param data
 * @param idAccion
 */
function gestionarServicioWyAp(data, idAccion, tipoServicio)
{
    if(data.estado=="PendienteAp" || data.estado=="Activo")
    {
        confirmarServicioWyApAgregarEdb(data, idAccion, tipoServicio);
    }
    else if(data.estado=="Asignada")
    {
        confirmarServicioWyApCambioAWdb(data, idAccion, tipoServicio);
    }
}

function confirmarServicioWyApAgregarEdb(data, idAccion, tipoServicio)
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
                                intIdSolicitudServicio: data.tieneSolicitudAgregarEquipo,
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
    if(data.estado=="PendienteAp")
    {
        tituloVentana = 'Confirmar Servicio - Agregar Ap'
    }
    else
    {
        tituloVentana = 'Agregar Ap'
    }
    Ext.getCmp('observacionActivarServicio').setVisible(true);
    Ext.getCmp('campoVacio').setVisible(false);
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

function confirmarServicioWyApCambioAWdb(data, idAccion, tipoServicio)
{
    var storeModelosWifiDualBand = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetModelosEquiposPorTecnologia,
            extraParams: {
                strTipoEquipos: 'WIFI DUAL BAND',
                intIdServicio: data.idServicio
            },
            reader: {
                type: 'json',
                root: 'arrayRegistros'
            }
        },
        fields:
            [
                {name: 'strNombreModelo', mapping: 'strNombreModelo'}
            ]
    });

    var strSerieOntValida = '';
    ModelosCpeOnt = new Ext.form.ComboBox({
        id: 'comboModelosCpeOnt',
        name: 'comboModelosCpeOnt',
        fieldLabel: "Modelo",
        store: storeModelosWifiDualBand,
        displayField: 'strNombreModelo',
        valueField: 'strNombreModelo',
        allowBlank: false,
        height:30,
        width: 225,
        border:0,
        margin:0,
        listeners: {
            blur: function (combo) {
                Ext.getCmp('btnConfirmarWifiDB').setDisabled(true);
                strSerieOntValida = Ext.getCmp('serieCpeOnt').getValue();
                Ext.Ajax.request({
                    url: buscarCpeNaf,
                    method: 'post',
                    params: {
                        serieCpe: strSerieOntValida,
                        modeloElemento: combo.getValue(),
                        estado: 'PI',
                        bandera: 'ActivarServicio'
                    },
                    success: function (response)
                    {
                        Ext.getCmp('btnConfirmarWifiDB').setDisabled(false);
                        var respuesta = response.responseText.split("|");
                        var status = respuesta[0];
                        var mensaje = respuesta[1];

                        if (status == "OK")
                        {
                            Ext.getCmp('descripcionCpeOnt').setValue = mensaje;
                            Ext.getCmp('descripcionCpeOnt').setRawValue(mensaje);
                            var arrayInformacionWifi = mensaje.split(",");
                            Ext.getCmp('macCpeOnt').setValue = arrayInformacionWifi[1];
                            Ext.getCmp('macCpeOnt').setRawValue(arrayInformacionWifi[1]);
                        } else
                        {
                            Ext.Msg.alert('Mensaje ', mensaje);
                            Ext.getCmp('descripcionCpeOnt').setValue = status;
                            Ext.getCmp('descripcionCpeOnt').setRawValue(status);
                            Ext.getCmp('macCpeOnt').setValue = "";
                            Ext.getCmp('macCpeOnt').setRawValue("");
                        }
                    },
                    failure: function (result)
                    {
                        Ext.getCmp('btnConfirmarWifiDB').setDisabled(false);
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        Ext.getCmp('macCpeOnt').setValue = "";
                        Ext.getCmp('macCpeOnt').setRawValue("");
                    }
                });
            }
        }

    });

    var agregarFormPanel = Ext.create('Ext.form.Panel', {
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
                            {
                                xtype: 'textfield',
                                name: 'ultimaMillaServicio',
                                fieldLabel: 'Última Milla',
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false}
                        ]
                    }

                ]
            },
            {
                xtype: 'fieldset',
                    title: 'Información del Equipo Wifi Dual Band',
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
                                id: 'serieCpeOnt',
                                name: 'serieCpeOnt',
                                fieldLabel: 'Serie Cpe Ont',
                                displayField: "",
                                value: "",
                                width: 280
                            },
                            {width: 50, border: false},
                            ModelosCpeOnt,
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'macCpeOnt',
                                name: 'macCpeOnt',
                                fieldLabel: 'Mac Cpe Ont',
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'descripcionCpeOnt',
                                name: 'descripcionCpeOnt',
                                fieldLabel: 'Descripción Cpe Ont',
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
                id: 'btnConfirmarWifiDB',
                text: 'Ejecutar',
                formBind: true,
                handler: function() {

                    var strSerieCpeOnt       = Ext.getCmp('serieCpeOnt').getValue();
                    var strModeloCpeOnt      = Ext.getCmp('comboModelosCpeOnt').getValue();
                    var strMacCpeOnt         = Ext.getCmp('macCpeOnt').getValue();
                    var strDescripcionCpeOnt = Ext.getCmp('descripcionCpeOnt').getValue();
                    var booleanValidacion  = true;
                    intBanderaErroflag     = 0;

                    if(Ext.isEmpty(strMacCpeOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 2;
                    }
                    else if( strDescripcionCpeOnt == "ELEMENTO ESTADO INCORRECTO" || 
                             strDescripcionCpeOnt == "ELMENTO CON SALDO CERO"    || 
                             strDescripcionCpeOnt == "NO EXISTE ELEMENTO" )
                    {
                        booleanValidacion  = false;
                        intBanderaErroflag = 3;
                    }
                    else if(Ext.isEmpty(strSerieCpeOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 4;
                    }  
                    else if(Ext.isEmpty(strModeloCpeOnt))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 5;
                    }
                    else if(strSerieOntValida != strSerieCpeOnt)
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 6;
                    }
                    if (booleanValidacion) 
                    {
                        Ext.get(agregarFormPanel.getId()).mask('Cargando...');

                        Ext.Ajax.request({
                                            url: cambiarCpeBoton,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: {
                                                intIdServicioInternet:    data.idServicioRefIpFija,
                                                idServicio:               data.idServicio,
                                                idElemento:               data.intIdElementoHw,
                                                modeloCpe:                strModeloCpeOnt,
                                                nombreCpe:                data.strNombreElementoHw,
                                                macCpe:                   strMacCpeOnt,
                                                serieCpe:                 strSerieCpeOnt,
                                                descripcionCpe:           strDescripcionCpeOnt,
                                                tipoElementoCpe:          "CPE ONT",
                                                strEsWifiDualBand:        "SI",
                                                strCambioEquiposDualBand: "SI"
                            },
                            success: function(response) {
                                Ext.get(agregarFormPanel.getId()).unmask();
                                if(response.responseText == "OK"){
                                    win.destroy();
                                    Ext.Msg.alert('Mensaje','Se Realizó el Cambio del Elemento Cliente CPE ONT', function(btn){
                                        if(btn=='ok'){
                                            store.load();
                                            confirmarServicioWyApAgregarEdb(data, idAccion, tipoServicio);
                                        }
                                    });
                                }
                                else if(response.responseText == "NO ID CLIENTE"){
                                    Ext.Msg.alert('Mensaje ','Favor escoger al menos una ip para el CPE' );
                                }
                                else if(response.responseText == "MAX ID CLIENTE"){
                                    Ext.Msg.alert('Mensaje ','Límite de clientes por Puerto esta en el máximo, <br> '
                                                             + 'Favor comunicarse con el departamento de GEPON' );
                                }
                                else if(response.responseText=="IP DEL EQUIPO INCORRECTA"){
                                    Ext.Msg.alert('Mensaje ', response.responseText + ', <BR> NO PODRA CONTINUAR CON EL CAMBIO DEL EQUIPO');
                                }
                                else if(response.responseText == "CANTIDAD CERO"){
                                    Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                                }
                                else if(response.responseText == "NO EXISTE PRODUCTO"){
                                    Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                }
                                else if(response.responseText == "NO EXISTE CPE"){
                                    Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                                }
                                else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                                    Ext.Msg.alert('Mensaje ','CPE con estado incorrecto, favor revisar!' );
                                }
                                else if(response.responseText == "NAF"){
                                    Ext.Msg.alert('Mensaje ',response.responseText);
                                }
                                else{
                                    Ext.Msg.alert('Mensaje ',response.responseText );
                                }
                            },
                            failure: function(result)
                            {
                                Ext.get(agregarFormPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                    else 
                    {
                        if( intBanderaErroflag == 1 )
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese la observación correspondiente!");
                        }
                        else if( intBanderaErroflag == 2 )
                        {
                            Ext.Msg.alert("Validación","No existe valor de Mac, favor revisar!");
                        }
                        else if( intBanderaErroflag == 3 )
                        {
                            Ext.Msg.alert("Validación","Datos del Wifi incorrectos, favor revisar!");
                        }
                        else if( intBanderaErroflag == 4 )
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese la serie correspondiente!");
                        }
                        else if( intBanderaErroflag == 5 )
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese el modelo correspondiente!");
                        }
                        else if( intBanderaErroflag == 6)
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese la serie de manera correcta!");
                        }
                        else
                        {
                            Ext.Msg.alert("Failed", "Existen campos vacíos. Por favor revisar.");
                        }
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
        title: 'Confirmar Servicio - Cambio a Wifi Dual Band',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [agregarFormPanel]
    }).show();
}

function cancelarServicioWyAp(data, idAccion)
{
    Ext.Msg.alert('Mensaje', 'Está seguro que desea Cancelar el Servicio?', function (btn) {
        if (btn == 'ok') {
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: strUrlCancelarServicioWyApBoton,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function (response) {
                    Ext.get("grid").unmask();
                    var objData = Ext.JSON.decode(response.responseText);
                    var strStatus = objData.strStatus;
                    var strMensaje = objData.strMensaje;
                    if (strStatus == "OK") {
                        Ext.Msg.alert('Mensaje', 'Se Canceló el Servicio!', function (btn) {
                            if (btn == 'ok') {
                                store.load();
                            }
                        });
                    } else {
                        Ext.Msg.alert('Mensaje ', 'No se pudo cancelar el servicio! '+strMensaje);
                    }

                }

            });
        }
    });
}