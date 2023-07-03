/**
 * cambioEquiposDualBand
 * 
 * Función que sirve para agregar los datos del wifi dual band y proceder al cambio de equipo
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 28-11-2018
 * @param data
 */
function cambioEquiposDualBand(data)
{
    var storeModelosWifiDualBand = new Ext.data.Store({
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: getModelosElemento,
            extraParams: {
                tipo:   'CPE',
                forma:  'Empieza con',
                estado: "Activo"
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'modelo', mapping: 'modelo'},
                {name: 'codigo', mapping: 'codigo'}
            ]
    });
    
    var agregarEquiposPanel = Ext.create('Ext.form.Panel', {
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
                                name: 'plan',
                                fieldLabel: 'Plan',
                                value: data.nombrePlan,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
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
                                id: 'serieWifiDualBand',
                                name: 'serieWifiDualBand',
                                fieldLabel: 'Serie',
                                displayField: "",
                                value: "",
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                queryMode: 'local',
                                xtype: 'combobox',
                                id: 'modeloWifiDualBand',
                                name: 'modeloWifiDualBand',
                                fieldLabel: 'Modelo',
                                displayField: 'modelo',
                                valueField: 'modelo',
                                loadingText: 'Buscando...',
                                store: storeModelosWifiDualBand,
                                width: 225,
                                listeners: {
                                    blur: function (combo) {
                                        Ext.getCmp('btnCambioEquiposDualBand').setDisabled(true);
                                        Ext.Ajax.request({
                                            url: buscarCpeNaf,
                                            method: 'post',
                                            params: {
                                                serieCpe: Ext.getCmp('serieWifiDualBand').getValue(),
                                                modeloElemento: combo.getValue(),
                                                estado: 'PI',
                                                bandera: 'ActivarServicio'
                                            },
                                            success: function (response)
                                            {
                                                Ext.getCmp('btnCambioEquiposDualBand').setDisabled(false);
                                                var respuesta = response.responseText.split("|");
                                                var status = respuesta[0];
                                                var mensaje = respuesta[1];

                                                if (status == "OK")
                                                {
                                                    Ext.getCmp('descripcionWifiDualBand').setValue = mensaje;
                                                    Ext.getCmp('descripcionWifiDualBand').setRawValue(mensaje);
                                                    var arrayInformacionWifi = mensaje.split(",");
                                                    Ext.getCmp('macWifiDualBand').setValue = arrayInformacionWifi[1];
                                                    Ext.getCmp('macWifiDualBand').setRawValue(arrayInformacionWifi[1]);
                                                } else
                                                {
                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                    Ext.getCmp('descripcionWifiDualBand').setValue = status;
                                                    Ext.getCmp('descripcionWifiDualBand').setRawValue(status);
                                                    Ext.getCmp('macWifiDualBand').setValue = "";
                                                    Ext.getCmp('macWifiDualBand').setRawValue("");
                                                }
                                            },
                                            failure: function (result)
                                            {
                                                Ext.getCmp('btnCambioEquiposDualBand').setDisabled(false);
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                Ext.getCmp('macWifiDualBand').setValue = "";
                                                Ext.getCmp('macWifiDualBand').setRawValue("");
                                            }
                                        });
                                    }
                                }
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'macWifiDualBand',
                                name: 'macWifiDualBand',
                                fieldLabel: 'Mac',
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'descripcionWifiDualBand',
                                name: 'descripcionWifiDualBand',
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
                id: 'btnCambioEquiposDualBand',
                text: 'Cambiar',
                formBind: true,
                handler: function () {
                    var strSerieWifiDualBand = Ext.getCmp('serieWifiDualBand').getValue();
                    var strModeloWifiDualBand = Ext.getCmp('modeloWifiDualBand').getValue();
                    var strMacWifiDualBand = Ext.getCmp('macWifiDualBand').getValue();
                    var strDescripcionWifiDualBand = Ext.getCmp('descripcionWifiDualBand').getValue();
                    var booleanValidacion = true;
                    intBanderaErroflag = 0;
                    
                    if (Ext.isEmpty(strMacWifiDualBand))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 1;
                    }
                    else if (strDescripcionWifiDualBand == "ELEMENTO ESTADO INCORRECTO" ||
                        strDescripcionWifiDualBand == "ELMENTO CON SALDO CERO" ||
                        strDescripcionWifiDualBand == "NO EXISTE ELEMENTO")
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 2;
                    } 
                    else if (Ext.isEmpty(strSerieWifiDualBand))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 3;
                    } 
                    else if (Ext.isEmpty(strModeloWifiDualBand))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 4;
                    }
                    
                    if (booleanValidacion)
                    {
                        Ext.get(agregarEquiposPanel.getId()).mask('Cargando...');
                        Ext.Ajax.request({
                            url: strUrlCambioEquiposDualBand,
                            method: 'post',
                            timeout: 400000,  
                            params: {
                                intIdServicio: data.idServicio,
                                strSerieWifiDualBand: strSerieWifiDualBand,
                                strModeloWifiDualBand: strModeloWifiDualBand,
                                strMacWifiDualBand: strMacWifiDualBand,
                                intIdSolicitudServicio: data.tieneSolicitudAgregarEquipo,
                                strEsAgregarEquipoMasivo: data.strEsAgregarEquipoMasivo
                            },
                            success: function (response) {
                                Ext.get(agregarEquiposPanel.getId()).unmask();
                                var objData = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.status;
                                var strMensaje = objData.mensaje;
                                if (strStatus == "OK") {
                                    win.destroy();
                                    Ext.Msg.alert('Mensaje', "Se Cambio el Elemento del Cliente", function (btn) {
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
                                Ext.get(agregarEquiposPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                    else
                    {
                        if (intBanderaErroflag == 1)
                        {
                            Ext.Msg.alert("Validación", "No existe valor de Mac, favor revisar!");
                        } 
                        else if (intBanderaErroflag == 2)
                        {
                            Ext.Msg.alert("Validación", "Datos incorrectos, favor revisar!");
                        }
                        else if (intBanderaErroflag == 3)
                        {
                            Ext.Msg.alert("Validación", "Por favor ingrese la serie correspondiente!");
                        }
                        else if (intBanderaErroflag == 4)
                        {
                            Ext.Msg.alert("Validación", "Por favor ingrese el modelo correspondiente!");
                        }
                        else
                        {
                            Ext.Msg.alert("Failed", "Existen campos vacíos. Por favor revisar.");
                        }
                    }
                }
            }, 
            {
                text: 'Cancelar',
                handler: function () {
                    win.destroy();
                }
            }]
    });
    
    storeModelosWifiDualBand.load();
    var win = Ext.create('Ext.window.Window', {
        title: "Cambio a Equipo Wifi Dual Band",
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [agregarEquiposPanel]
    }).show();
}

/**
 * Función que sirve para confirmar los servicios Wifi Dual Band de MD
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 13-05-2019
 * @param data
 * @param idAccion
 */
function confirmarServicioWifiDualBand(data)
{
    storeModelosCpeOnt = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url : strUrlModelosCpeOntPorSoporte,
            reader: {
                type: 'json',
                root: 'arrayModelosOnt'
            },
            extraParams: {
                    strTipoEquipos: 'DUAL BAND'
                }
        },
        fields:
		[
			{name:'strValueModelo', mapping:'strValueModelo'}
		],
		autoLoad: false
    });
    var strSerieOntValida = '';
    ModelosCpeOnt = new Ext.form.ComboBox({
        id: 'comboModelosCpeOnt',
        name: 'comboModelosCpeOnt',
        fieldLabel: "Modelo",
        store: storeModelosCpeOnt,
        displayField: 'strValueModelo',
        valueField: 'strValueModelo',
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
                    title: 'Información del Equipo CPE ONT',
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
                                    Ext.Msg.alert('Mensaje','Se activó el producto WIFI DUAL BAND del Cliente', function(btn){
                                        if(btn=='ok'){
                                            store.load();
                                            
                                        }
                                    });
                                }
                                else if(response.responseText == "NO ID CLIENTE"){
                                    Ext.Msg.alert('Mensaje ','Favor escoger al menos una ip para el CPE' );
                                }
                                else if(response.responseText == "MAX ID CLIENTE"){
                                    Ext.Msg.alert('Mensaje ','Límite de clientes por Puerto esta en el máximo, <br> Favor comunicarse con el departamento de GEPON' );
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
        title: 'Confirmar Servicio Wifi Dual Band',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [agregarFormPanel]
    }).show();
}


function cancelarServicioWifiDualBand(data, idAccion)
{
    Ext.Msg.alert('Mensaje', 'Está seguro que desea Cancelar el Servicio?', function (btn) {
        if (btn == 'ok') {
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: strUrlCancelarServicioWBoton,
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