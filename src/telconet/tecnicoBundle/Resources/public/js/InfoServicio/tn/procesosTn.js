
/*
 * Funcion utilizada para realizar la regularización de información de servicios radio TN
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 26-08-2016 
 * @since 1.0
 */
function regularizacionServiciosRadioTn(data)
{
    Ext.get("grid").mask('Consultando Datos...');

    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function (response) {
            Ext.get("grid").unmask();

            var json = Ext.JSON.decode(response.responseText);
            if (json)
            {
                var datosBackbone = json.encontrados[0];

                if (datosBackbone.idElementoPadre == 0)
                {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: datosBackbone.nombreElementoPadre,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                } else
                {
                    var storeModelosRadio = new Ext.data.Store({
                        pageSize: 100,
                        autoLoad: true,
                        proxy: {
                            type: 'ajax',
                            url : getModelosElemento,
                            extraParams: {
                                tipo:   'RADIO',
                                forma:  'Igual que',
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

                    var storeClientesRadioTn = new Ext.data.Store({
                        pageSize: 100,
                        autoLoad: true,
                        proxy: {
                            type: 'ajax',
                            url: urlObtieneServRadioTn,
                            extraParams: {
                                idServicio: data.idServicio
                            },
                            reader: {
                                type: 'json',
                                root: 'encontrados'
                            }
                        },
                        fields:
                                [
                                    {name: 'loginAux', mapping: 'loginAux'},
                                    {name: 'idServicio', mapping: 'idServicio'}
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
                                            columns: 4,
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
                                                width: 300
                                            },
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
                                                name: 'loginAux',
                                                fieldLabel: 'LoginAux',
                                                displayField: data.loginAux,
                                                value: data.loginAux,
                                                readOnly: true,
                                                width: 300
                                            },
                                            {width: '5%', border: false},
                                            {
                                                xtype: 'textfield',
                                                id: 'um',
                                                name: 'um',
                                                fieldLabel: 'Última Milla',
                                                displayField: data.ultimaMilla + "/" + datosBackbone.tipoBackbone,
                                                value: data.ultimaMilla + "/" + datosBackbone.tipoBackbone,
                                                fieldStyle: 'color: green;',
                                                readOnly: true,
                                                width: '30%'
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
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'capacidadUno',
                                                fieldLabel: 'Capacidad Uno',
                                                displayField: data.capacidadUno,
                                                value: data.capacidadUno,
                                                readOnly: true,
                                                width: '40%'
                                            },
                                            {width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'capacidadDos',
                                                fieldLabel: 'Capacidad Dos',
                                                displayField: data.capacidadDos,
                                                value: data.capacidadDos,
                                                readOnly: true,
                                                width: '40%'
                                            },
                                            {width: '10%', border: false},
                                        ]
                                    }
                                ]
                            }, //cierre de la informacion del cliente

                            //informacion del servicio/producto
                            {
                                xtype: 'fieldset',
                                title: 'Información de Backbone Actual',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 600,
                                    height: 65
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
                                                name: 'elemento',
                                                id: 'nombreSw',
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
                                                id: 'puertoSw',
                                                fieldLabel: 'Interface',
                                                displayField: datosBackbone.nombreInterfaceElemento,
                                                value: datosBackbone.nombreInterfaceElemento,
                                                readOnly: true,
                                                width: 150
                                            },
                                            {width: '0%', border: false},
                                            {width: 10, border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'elementoCliente',
                                                fieldLabel: 'CPE',
                                                displayField: datosBackbone.elementoCpeUmRadio,
                                                value: datosBackbone.elementoCpeUmRadio,
                                                readOnly: true,
                                                width: 350
                                            },
                                            {width: 10, border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'interfaceElementoCliente',
                                                fieldLabel: 'Puerto Cpe',
                                                displayField: datosBackbone.intElementoCpeUmRadio,
                                                value: datosBackbone.intElementoCpeUmRadio,
                                                readOnly: true,
                                                width: 150
                                            },
                                            {width: '0%', border: false}
                                        ]
                                    }
                                ]
                            }, //cierre de la informacion servicio/producto
                            //informacion de regularización
                            {
                                xtype: 'fieldset',
                                title: 'Opciones Regularización',
                                defaults: {
                                    width: 600,
                                    height: 35,
                                    align: 'center'
                                },
                                items: [
                                    //grupo de radio botones
                                    {
                                        xtype: 'radiogroup',
                                        id: 'radioSelect',
                                        columns: 2,
                                        items: [
                                            {
                                                boxLabel: 'Ingreso de Información faltante',
                                                id: 'rbIngresoInfo',
                                                name: 'rbInfo',
                                                inputValue: "nuevo",
                                                listeners:
                                                        {
                                                            change: function (cb, nv, ov)
                                                            {
                                                                if (nv)
                                                                {
                                                                    Ext.getCmp('modeloRadioBb').reset();
                                                                    Ext.getCmp('modeloRadioCli').reset();
                                                                    Ext.getCmp('serviciosRadio').reset();
                                                                    Ext.getCmp('macRadioBb').setValue       = '';
                                                                    Ext.getCmp('macRadioBb').setRawValue('') ;
                                                                    Ext.getCmp('macRadioCli').setValue       = '';
                                                                    Ext.getCmp('macRadioCli').setRawValue('') ;
                                                                    Ext.getCmp('sidRadioBb').setValue       = '';
                                                                    Ext.getCmp('sidRadioBb').setRawValue('') ;
                                                                    Ext.getCmp('sidRadioCli').setValue       = '';
                                                                    Ext.getCmp('sidRadioCli').setRawValue('') ;
                                                                    Ext.getCmp('ipRadioBb').setValue       = '';
                                                                    Ext.getCmp('ipRadioBb').setRawValue('') ;
                                                                    Ext.getCmp('switch').setValue       = '';
                                                                    Ext.getCmp('switch').setRawValue('') ;
                                                                    Ext.getCmp('puertoSwitch').setValue = '';
                                                                    Ext.getCmp('puertoSwitch').setRawValue('') ;
                                                                    Ext.getCmp('radioBb').setValue      = '';
                                                                    Ext.getCmp('radioBb').setRawValue('') ;
                                                                    Ext.getCmp('radioCli').setValue     = '';
                                                                    Ext.getCmp('radioCli').setRawValue('') ;
                                                                    Ext.getCmp('cpeCliente').setValue   = '';
                                                                    Ext.getCmp('cpeCliente').setRawValue('') ;
                                                                    Ext.getCmp('existenteRadio').setVisible(false);
                                                                    Ext.getCmp('nuevaRadioBb').setVisible(true);
                                                                    win.center();
                                                                }
                                                            }
                                                        }
                                            },
                                            {
                                                boxLabel: 'Utilizar información de servicios existentes',
                                                id: 'rbExisteInfo',
                                                name: 'rbInfo',
                                                inputValue: "existe",
                                                listeners:
                                                        {
                                                            change: function (cb, nv, ov)
                                                            {
                                                                if (nv)
                                                                {
                                                                    Ext.getCmp('modeloRadioBb').reset();
                                                                    Ext.getCmp('modeloRadioCli').reset();
                                                                    Ext.getCmp('serviciosRadio').reset();
                                                                    Ext.getCmp('macRadioBb').setValue       = '';
                                                                    Ext.getCmp('macRadioBb').setRawValue('') ;
                                                                    Ext.getCmp('macRadioCli').setValue       = '';
                                                                    Ext.getCmp('macRadioCli').setRawValue('') ;
                                                                    Ext.getCmp('sidRadioBb').setValue       = '';
                                                                    Ext.getCmp('sidRadioBb').setRawValue('') ;
                                                                    Ext.getCmp('sidRadioCli').setValue       = '';
                                                                    Ext.getCmp('sidRadioCli').setRawValue('') ;
                                                                    Ext.getCmp('ipRadioBb').setValue       = '';
                                                                    Ext.getCmp('ipRadioBb').setRawValue('') ;
                                                                    Ext.getCmp('switch').setValue       = '';
                                                                    Ext.getCmp('switch').setRawValue('') ;
                                                                    Ext.getCmp('puertoSwitch').setValue = '';
                                                                    Ext.getCmp('puertoSwitch').setRawValue('') ;
                                                                    Ext.getCmp('radioBb').setValue      = '';
                                                                    Ext.getCmp('radioBb').setRawValue('') ;
                                                                    Ext.getCmp('radioCli').setValue     = '';
                                                                    Ext.getCmp('radioCli').setRawValue('') ;
                                                                    Ext.getCmp('cpeCliente').setValue   = '';
                                                                    Ext.getCmp('cpeCliente').setRawValue('') ;
                                                                    Ext.getCmp('nuevaRadioBb').setVisible(false);
                                                                    Ext.getCmp('existenteRadio').setVisible(true);
                                                                    win.center();
                                                                }
                                                            }
                                                        }
                                            }
                                        ]//items
                                    }
                                ]
                            },
                            //nuevo radio - radio
                            {
                                id: 'nuevaRadioBb',
                                xtype: 'fieldset',
                                title: 'Ingreso de Información',
                                defaultType: 'textfield',
                                hidden: true,
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 2,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                queryMode: 'local',
                                                xtype: 'combobox',
                                                id: 'modeloRadioBb',
                                                name: 'modeloRadioBb',
                                                fieldLabel: '* Modelo Radio BackBone',
                                                displayField: 'modelo',
                                                valueField: 'modelo',
                                                allowBlank: false,
                                                blankText: 'Ingrese información',
                                                loadingText: 'Buscando...',
                                                store: storeModelosRadio,
                                                forceSelection: true,
                                                width: '25%'
                                            },
                                            {
                                                queryMode: 'local',
                                                xtype: 'combobox',
                                                id: 'modeloRadioCli',
                                                name: 'modeloRadioCli',
                                                fieldLabel: '* Modelo Radio Cliente',
                                                displayField: 'modelo',
                                                valueField: 'modelo',
                                                allowBlank: false,
                                                loadingText: 'Buscando...',
                                                forceSelection: true,
                                                blankText: 'Ingrese información',
                                                store: storeModelosRadio,
                                                width: '25%'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'macRadioBb',
                                                name: 'macRadioBb',
                                                fieldLabel: '* Mac Radio BackBone',
                                                displayField: "",
                                                value: "",
                                                readOnly: false,
                                                allowBlank: false,
                                                width: '25%',
                                                blankText: 'Ingrese información',
                                                regex: Utils.REGEX_MAC,
                                                regexText: 'Formato de Mac BB Incorrecto (xxxx.xxxx.xxxx)'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'macRadioCli',
                                                name: 'macRadioCli',
                                                fieldLabel: '* Mac Radio Cliente',
                                                displayField: "",
                                                value: "",
                                                readOnly: false,
                                                allowBlank: false,
                                                width: '25%',
                                                blankText: 'Ingrese información',
                                                regex: Utils.REGEX_MAC,
                                                regexText: 'Formato de Mac Cliente Incorrecto (xxxx.xxxx.xxxx)'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'sidRadioBb',
                                                name: 'sidRadioBb',
                                                allowBlank: false,
                                                fieldLabel: '* SID Radio BackBone',
                                                displayField: "",
                                                maxLength: 100,
                                                blankText: 'Ingrese información',
                                                maxLengthText: 'Tamaño máximo 100 caracteres',
                                                value: "",
                                                width: '25%'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'sidRadioCli',
                                                name: 'sidRadioCli',
                                                allowBlank: false,
                                                fieldLabel: '* SID Radio Cliente',
                                                displayField: "",
                                                maxLength: 100,
                                                blankText: 'Ingrese información',
                                                maxLengthText: 'Tamaño máximo 100 caracteres',
                                                value: "",
                                                width: '25%'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'ipRadioBb',
                                                allowBlank: false,
                                                name: 'ipRadioBb',
                                                fieldLabel: '* Ip Radio BackBone',
                                                displayField: "",
                                                value: "",
                                                readOnly: false,
                                                blankText: 'Ingrese información',
                                                width: '25%',
                                                regex: Utils.REGEX_IP,
                                                regexText: 'Ip Incorrecta (xxx.xxx.xxx.xxx)'
                                            }

                                        ]//items container
                                    }//items panel
                                ]//items panel
                            },
                            //Información de servicios Um Radio existentes por punto
                            {
                                id: 'existenteRadio',
                                xtype: 'fieldset',
                                title: 'Información servicios Existentes',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 640
                                },
                                hidden: true,
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 2,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                queryMode: 'local',
                                                xtype: 'combobox',
                                                id: 'serviciosRadio',
                                                name: 'serviciosRadio',
                                                fieldLabel: '* Servicios Radio',
                                                displayField: 'loginAux',
                                                valueField: 'idServicio',
                                                loadingText: 'Buscando...',
                                                store: storeClientesRadioTn,
                                                forceSelection: true,
                                                width: 350,
                                                listeners: {
                                                    select: function (combo) {
                                                        Ext.get(formPanel.getId()).mask('Consultando Información...');
                                                        var idServicio = combo.getValue();
                                                        Ext.getCmp('switch').setValue       = '';
                                                        Ext.getCmp('switch').setRawValue('') ;
                                                        Ext.getCmp('puertoSwitch').setValue = '';
                                                        Ext.getCmp('puertoSwitch').setRawValue('') ;
                                                        Ext.getCmp('radioBb').setValue      = '';
                                                        Ext.getCmp('radioBb').setRawValue('') ;
                                                        Ext.getCmp('radioCli').setValue     = '';
                                                        Ext.getCmp('radioCli').setRawValue('') ;
                                                        Ext.getCmp('cpeCliente').setValue   = '';
                                                        Ext.getCmp('cpeCliente').setRawValue('') ;
                                                        Ext.Ajax.request({
                                                            url: urlObtieneInfoServRadioTn,
                                                            method: 'post',
                                                            params: { idServicio : idServicio},
                                                            success: function (response) {
                                                                Ext.get(formPanel.getId()).unmask();
                                                                var variable = response.responseText;
                                                                var r        = Ext.JSON.decode(variable);
                                                                Ext.getCmp('switch').setValue       = r.nombreSw;
                                                                Ext.getCmp('switch').setRawValue(r.nombreSw) ;
                                                                Ext.getCmp('puertoSwitch').setValue = r.puertoSw;
                                                                Ext.getCmp('puertoSwitch').setRawValue(r.puertoSw) ;
                                                                Ext.getCmp('radioBb').setValue      = r.radioBb;
                                                                Ext.getCmp('radioBb').setRawValue(r.radioBb) ;
                                                                Ext.getCmp('radioCli').setValue     = r.radioCli;
                                                                Ext.getCmp('radioCli').setRawValue(r.radioCli) ;
                                                                Ext.getCmp('cpeCliente').setValue   = r.cpeCli;
                                                                Ext.getCmp('cpeCliente').setRawValue(r.cpeCli) ;
                                                            },
                                                            failure: function (result) {
                                                                Ext.get(formPanel.getId()).unmask();
                                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                            }
                                                        });


                                                    }
                                                }
                                            },
                                            {width: 250, border: false},
                                            {
                                                xtype: 'textfield',
                                                id: 'switch',
                                                name: 'switch',
                                                fieldLabel: '* Switch',
                                                displayField: "",
                                                value: "",
                                                readOnly: true,
                                                width: 300
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'puertoSwitch',
                                                name: 'puertoSwitch',
                                                fieldLabel: '* Puerto Switch',
                                                displayField: "",
                                                value: "",
                                                readOnly: true,
                                                width: 250
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'radioBb',
                                                name: 'radioBb',
                                                fieldLabel: '* Radio BackBone',
                                                displayField: "",
                                                value: "",
                                                readOnly: true,
                                                width: 300
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'radioCli',
                                                name: 'radioCli',
                                                fieldLabel: '* Radio Cliente',
                                                displayField: "",
                                                value: "",
                                                readOnly: true,
                                                width: 250
                                            },
                                            {
                                                xtype: 'textfield',
                                                id: 'cpeCliente',
                                                name: 'cpeCliente',
                                                fieldLabel: '* CPE Cliente',
                                                displayField: "",
                                                value: "",
                                                readOnly: true,
                                                width: 300
                                            }

                                        ]//items container
                                    }//items panel
                                ]//items panel
                            }
                        ],
                        buttons: [{
                                text: 'Ejecutar',
                                handler: function () {
                                    var variable = Ext.getCmp('radioSelect').getChecked()[0];
                                    var form     = this.up('form').getForm();
                                    if (variable != null)
                                    {
                                        var tipoRegularizacion = variable.getGroupValue();
                                        if (tipoRegularizacion == 'nuevo')
                                        {
                                            if (form.isValid())
                                            {
                                                Ext.Msg.alert('Mensaje', 'Esta seguro que desea ejecutar la regularización del servicio?', function (btn) {
                                                    if (btn == 'ok') {
                                                        Ext.get(formPanel.getId()).mask('Ejecutando Cambio...');
                                                        Ext.Ajax.request({
                                                            url: urlRegulaRadioTn,
                                                            method: 'post',
                                                            timeout: 400000,
                                                            params: {
                                                                tipoRegularizacion : tipoRegularizacion,
                                                                idServicio: data.idServicio,
                                                                nombreSw: Ext.getCmp('nombreSw').getValue(),
                                                                puertoSw: Ext.getCmp('puertoSw').getValue(),
                                                                modeloRadioBb: Ext.getCmp('modeloRadioBb').getValue(),
                                                                macRadioBb: Ext.getCmp('macRadioBb').getValue(),
                                                                ipRadioBb: Ext.getCmp('ipRadioBb').getValue(),
                                                                sidRadioBb: Ext.getCmp('sidRadioBb').getValue(),
                                                                modeloRadioCli: Ext.getCmp('modeloRadioCli').getValue(),
                                                                macRadioCli: Ext.getCmp('macRadioCli').getValue(),
                                                                sidRadioCli: Ext.getCmp('sidRadioCli').getValue()
                                                            },
                                                            success: function (response) {
                                                                Ext.get(formPanel.getId()).unmask();
                                                                if (response.responseText == "OK") {
                                                                    store.load();
                                                                    win.destroy();
                                                                    Ext.Msg.alert('Mensaje', 'Se realizó la regularización del servicio');
                                                                } else {
                                                                    Ext.Msg.alert('Mensaje ', response.responseText);
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
                                            } else
                                            {
                                                Ext.Msg.alert('Mensaje ', 'Formulario no válido');
                                            }
                                        } else
                                        {
                                            var comboServiciosRadioTN = Ext.getCmp('serviciosRadio').getValue();
                                            if (comboServiciosRadioTN != null)
                                            {
                                                Ext.get(formPanel.getId()).mask('Ejecutando Cambio...');
                                                Ext.Ajax.request({
                                                            url: urlRegulaRadioTn,
                                                            method: 'post',
                                                            timeout: 400000,
                                                            params: {
                                                                tipoRegularizacion : tipoRegularizacion,
                                                                idServicio         : data.idServicio,
                                                                idServicioInfo     : comboServiciosRadioTN 
                                                            },
                                                            success: function (response) {
                                                                Ext.get(formPanel.getId()).unmask();
                                                                if (response.responseText == "OK") {
                                                                    store.load();
                                                                    win.destroy();
                                                                    Ext.Msg.alert('Mensaje', 'Se realizó la regularización del servicio');
                                                                } else {
                                                                    Ext.Msg.alert('Mensaje ', response.responseText);
                                                                }
                                                            },
                                                            failure: function (result)
                                                            {
                                                                Ext.get(formPanel.getId()).unmask();
                                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                            }
                                                        });
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Mensaje ', 'Seleccione un servicio de Radio');
                                            }
                                        }
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Mensaje ', 'Seleccione una opción de regularización');
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
                        title: 'Regularización Servicio Um Radio',
                        modal: true,
                        width: 710,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();
                }
            }
        }//cierre response
    });
}