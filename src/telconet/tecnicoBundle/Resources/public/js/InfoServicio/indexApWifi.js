/**
 * confirmarServicioApWifi
 * 
 * Funcion que sirve para confirmar los servicios Ap Wifi de MD
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 23-02-2017
 * @param data
 * @param idAccion
 */
function confirmarServicioApWifi(data, idAccion)
{
    Ext.define('ListaParametrosDetModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strValor3', type: 'string'},
            {name: 'strValor4', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });
    
    var storeModelosCpe = new Ext.data.Store({  
                            pageSize: 1000,
                            proxy: {
                                type: 'ajax',
                                url : getModelosElemento,
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
                                  {name:'modelo', mapping:'modelo'},
                                  {name:'codigo', mapping:'codigo'}
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
                            {
                                xtype: 'textfield',
                                name: 'ultimaMillaServicio',
                                fieldLabel: 'Última Milla',
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                id : 'observacionActivarServicio',
                                xtype: 'textarea',
                                name: 'observacionActivarServicio',
                                fieldLabel: '* Observación',
                                value: "",
                                readOnly: false,
                                required : true,
                                width: 280
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
                title: 'Información del Equipo Ap Wifi',
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
                                id: 'serieWifi',
                                name: 'serieWifi',
                                fieldLabel: 'Serie Wifi',
                                displayField: "",
                                value: "",
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                queryMode: 'local',
                                xtype: 'combobox',
                                id: 'modeloWifi',
                                name: 'modeloWifi',
                                fieldLabel: 'Modelo Wifi',
                                displayField: 'modelo',
                                valueField: 'modelo',
                                loadingText: 'Buscando...',
                                store: storeModelosCpe,
                                width: 225,
                                listeners: {
                                    blur: function(combo) {
                                        Ext.getCmp('btnConfirmar').setDisabled(true);
                                        Ext.Ajax.request({
                                            url: buscarCpeNaf,
                                            method: 'post',
                                            params: {
                                                serieCpe: Ext.getCmp('serieWifi').getValue(),
                                                modeloElemento: combo.getValue(),
                                                estado: 'PI',
                                                bandera: 'ActivarServicio'
                                            },
                                            success: function(response) 
                                            {
                                                Ext.getCmp('btnConfirmar').setDisabled(false);
                                                var respuesta = response.responseText.split("|");
                                                var status = respuesta[0];
                                                var mensaje = respuesta[1];

                                                if (status == "OK")
                                                {
                                                    Ext.getCmp('descripcionWifi').setValue = mensaje;
                                                    Ext.getCmp('descripcionWifi').setRawValue(mensaje);
                                                    var arrayInformacionWifi       = mensaje.split(",");
                                                    Ext.getCmp('macWifi').setValue = arrayInformacionWifi[1];
                                                    Ext.getCmp('macWifi').setRawValue(arrayInformacionWifi[1]);
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                    Ext.getCmp('descripcionWifi').setValue = status;
                                                    Ext.getCmp('descripcionWifi').setRawValue(status);
                                                    Ext.getCmp('macWifi').setValue = "";
                                                    Ext.getCmp('macWifi').setRawValue("");
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.getCmp('btnConfirmar').setDisabled(false);
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                Ext.getCmp('macWifi').setValue = "";
                                                Ext.getCmp('macWifi').setRawValue("");
                                            }
                                        });
                                    }
                                }
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'macWifi',
                                name: 'macWifi',
                                fieldLabel: 'Mac Wifi',
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'descripcionWifi',
                                name: 'descripcionWifi',
                                fieldLabel: 'Descripción Wifi',
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
                handler: function() {

                    var strObservacion     = Ext.getCmp('observacionActivarServicio').getValue();
                    var strSerieWifi       = Ext.getCmp('serieWifi').getValue();
                    var strModeloWifi      = Ext.getCmp('modeloWifi').getValue();
                    var strMacWifi         = Ext.getCmp('macWifi').getValue();
                    var strDescripcionWifi = Ext.getCmp('descripcionWifi').getValue();
                    var booleanValidacion  = true;
                    var intBanderaErroflag = 0;
                    
                    if (Ext.isEmpty(strObservacion))
                    {
                        booleanValidacion  = false;
                        intBanderaErroflag = 1;
                    }
                    else if(Ext.isEmpty(strMacWifi))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 2;
                    }
                    else if( strDescripcionWifi == "ELEMENTO ESTADO INCORRECTO" || 
                        strDescripcionWifi == "ELMENTO CON SALDO CERO"    || 
                        strDescripcionWifi == "NO EXISTE ELEMENTO" )
                    {
                        booleanValidacion  = false;
                        intBanderaErroflag = 3;
                    }
                    else if(Ext.isEmpty(strSerieWifi))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 4;
                    }  
                    else if(Ext.isEmpty(strModeloWifi))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 5;
                    }
                    if (booleanValidacion) 
                    {
                        Ext.get(confirmarFormPanel.getId()).mask('Cargando...');
                        Ext.Ajax.request({
                            url: confirmarServicioApWifiBoton,
                            method: 'post',
                            timeout: 400000,
                            params: {
                                idServicio                      : data.idServicio,
                                idProducto                      : data.productoId,
                                strObservacionServicio          : strObservacion,
                                idAccion                        : idAccion,
                                strSerieApWifi                  : strSerieWifi,
                                strModeloApWifi                 : strModeloWifi,
                                strMacApWifi                    : strMacWifi,
                                intIdServicioInternet           : data.idServicioRefIpFija,
                                intIdSolicitudServicio          : data.tieneSolicitudPlanificacion
                            },
                            success: function(response) {
                                Ext.get(confirmarFormPanel.getId()).unmask();
                                var objData   = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.strStatus;
                                if (strStatus == "OK") {
                                    win.destroy();
                                    Ext.Msg.alert('Mensaje', 'Se confirmó el Servicio: ' + data.login, function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });
                                }
                                else {
                                    Ext.Msg.alert('Mensaje ', 'Error:' + strStatus);
                                }
                            },
                            failure: function(result)
                            {
                                Ext.get(confirmarFormPanel.getId()).unmask();
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
        title: 'Confirmar Servicio Ap Wifi',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [confirmarFormPanel]
    }).show();
    
     storeModelosCpe.load();
}

/**
 * cancelarServicioApWifi
 * 
 * Funcion que sirve para cancelar los servicios Ap Wifi de MD
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 23-02-2017
 * @param data
 * @param idAccion
 */
function cancelarServicioApWifi(data, idAccion)
{
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar el Servicio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: cancelarServicioApWifiBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();
                    var objData   = Ext.JSON.decode(response.responseText);
                    var strStatus = objData.strStatus;
                    if(strStatus == "OK"){
                        Ext.Msg.alert('Mensaje','Se Canceló el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo cancelar el servicio!' );
                    }

                }

            });
        }
    });
}

/**
 * cortarServicioApWifi
 * 
 * Funcion que sirve para cortar los servicios Ap Wifi de MD
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 23-02-2017
 * @param data
 * @param idAccion
 */
function cortarServicioApWifi(data, idAccion)
{
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cortar el Servicio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cortando el Servicio...');
            Ext.Ajax.request({
                url: cortarServicioApWifiBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();
                    var objData   = Ext.JSON.decode(response.responseText);
                    var strStatus = objData.strStatus;
                    if(strStatus == "OK"){
                        Ext.Msg.alert('Mensaje','Se Cortó el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo Cortar el servicio!' );
                    }

                }

            });
        }
    });
}

/**
 * reconectarServicioApWifi
 * 
 * Funcion que sirve para reconectar los servicios Ap Wifi de MD
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 23-02-2017
 * @param data
 * @param idAccion
 */
function reconectarServicioApWifi(data,idAccion)
{
    Ext.Msg.alert('Mensaje','Esta seguro que desea Reconectar el Servicio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Reconectando el Servicio...');
            Ext.Ajax.request({
                url: reconectarServicioApWifiBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();
                    var objData   = Ext.JSON.decode(response.responseText);
                    var strStatus = objData.strStatus;
                    if(strStatus == "OK"){
                        Ext.Msg.alert('Mensaje','Se Reconectó el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo reconectar el servicio!' );
                    }

                }

            });
        }
    });
}

/**
 * agregarElementoApWifi
 * 
 * Funcion que sirve para agregar elemento en los servicios Ap Wifi de MD
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 23-02-2017
 * @param data
 * @param idAccion
 */
function agregarElementoApWifi(data, idAccion)
{
    Ext.define('ListaParametrosDetModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strValor3', type: 'string'},
            {name: 'strValor4', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });
    
    var storeModelosCpe = new Ext.data.Store({  
                            pageSize: 1000,
                            proxy: {
                                type: 'ajax',
                                url : getModelosElemento,
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
                                  {name:'modelo', mapping:'modelo'},
                                  {name:'codigo', mapping:'codigo'}
                                ]
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
                                fieldLabel: 'Plan',
                                value: data.nombrePlan,
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
                title: 'Información del Equipo Ap Wifi',
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
                                id: 'serieWifi',
                                name: 'serieWifi',
                                fieldLabel: 'Serie Wifi',
                                displayField: "",
                                value: "",
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                queryMode: 'local',
                                xtype: 'combobox',
                                id: 'modeloWifi',
                                name: 'modeloWifi',
                                fieldLabel: 'Modelo Wifi',
                                displayField: 'modelo',
                                valueField: 'modelo',
                                loadingText: 'Buscando...',
                                store: storeModelosCpe,
                                width: 225,
                                listeners: {
                                    blur: function(combo) {
                                        Ext.getCmp('btnAgregar').setDisabled(true);
                                        Ext.Ajax.request({
                                            url: buscarCpeNaf,
                                            method: 'post',
                                            params: {
                                                serieCpe: Ext.getCmp('serieWifi').getValue(),
                                                modeloElemento: combo.getValue(),
                                                estado: 'PI',
                                                bandera: 'ActivarServicio'
                                            },
                                            success: function(response) 
                                            {
                                                Ext.getCmp('btnAgregar').setDisabled(false);
                                                var respuesta = response.responseText.split("|");
                                                var status = respuesta[0];
                                                var mensaje = respuesta[1];

                                                if (status == "OK")
                                                {
                                                    Ext.getCmp('descripcionWifi').setValue = mensaje;
                                                    Ext.getCmp('descripcionWifi').setRawValue(mensaje);
                                                    var arrayInformacionWifi       = mensaje.split(",");
                                                    Ext.getCmp('macWifi').setValue = arrayInformacionWifi[1];
                                                    Ext.getCmp('macWifi').setRawValue(arrayInformacionWifi[1]);
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                    Ext.getCmp('descripcionWifi').setValue = status;
                                                    Ext.getCmp('descripcionWifi').setRawValue(status);
                                                    Ext.getCmp('macWifi').setValue = "";
                                                    Ext.getCmp('macWifi').setRawValue("");
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.getCmp('btnAgregar').setDisabled(false);
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                Ext.getCmp('macWifi').setValue = "";
                                                Ext.getCmp('macWifi').setRawValue("");
                                            }
                                        });
                                    }
                                }
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'macWifi',
                                name: 'macWifi',
                                fieldLabel: 'Mac Wifi',
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'descripcionWifi',
                                name: 'descripcionWifi',
                                fieldLabel: 'Descripción Wifi',
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
                id: 'btnAgregar',
                text: 'Agregar',
                formBind: true,
                handler: function() {

                    var strSerieWifi       = Ext.getCmp('serieWifi').getValue();
                    var strModeloWifi      = Ext.getCmp('modeloWifi').getValue();
                    var strMacWifi         = Ext.getCmp('macWifi').getValue();
                    var strDescripcionWifi = Ext.getCmp('descripcionWifi').getValue();
                    var booleanValidacion  = true;
                    var intBanderaErroflag = 0;
                    
                    if(Ext.isEmpty(strMacWifi))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 2;
                    }
                    else if( strDescripcionWifi == "ELEMENTO ESTADO INCORRECTO" || 
                        strDescripcionWifi == "ELMENTO CON SALDO CERO"    || 
                        strDescripcionWifi == "NO EXISTE ELEMENTO" )
                    {
                        booleanValidacion  = false;
                        intBanderaErroflag = 3;
                    }
                    else if(Ext.isEmpty(strSerieWifi))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 4;
                    }  
                    else if(Ext.isEmpty(strModeloWifi))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 5;
                    }
                    if (booleanValidacion) 
                    {
                        Ext.get(agregarFormPanel.getId()).mask('Cargando...');
                        Ext.Ajax.request({
                            url: agregarEquipoApWifiBoton,
                            method: 'post',
                            timeout: 400000,
                            params: {
                                idServicio                      : data.idServicio,
                                idAccion                        : idAccion,
                                strSerieApWifi                  : strSerieWifi,
                                strModeloApWifi                 : strModeloWifi,
                                strMacApWifi                    : strMacWifi,
                                intIdServicioInternet           : data.idServicioRefIpFija,
                                intIdSolicitudServicio          : data.tieneSolicitudAgregarEquipo
                            },
                            success: function(response) {
                                Ext.get(agregarFormPanel.getId()).unmask();
                                var objData   = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.strStatus;
                                if (strStatus == "OK") {
                                    win.destroy();
                                    Ext.Msg.alert('Mensaje', 'Se agregó el equipo Ap Wifi al Servicio: ' + data.login, function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });
                                }
                                else {
                                    Ext.Msg.alert('Mensaje ', 'Error:' + strStatus);
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
        title: 'Agregar Equipo Ap Wifi',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [agregarFormPanel]
    }).show();
    
     storeModelosCpe.load();
}