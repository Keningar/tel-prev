function activarLineasAnalogicas(data) 
{
    var storeLineas = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_verLineasTelefonicas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio
            }
        },
        fields:
            [
                {name: 'idNumero', mapping: 'idNumero'},
                {name: 'numero', mapping: 'numero'},
                {name: 'idDominio', mapping: 'idDominio'},
                {name: 'dominio', mapping: 'dominio'},
                {name: 'idClave', mapping: 'idClave'},
                {name: 'clave', mapping: 'clave'},
                {name: 'idNumeroCanales', mapping: 'idNumeroCanales'},
                {name: 'numeroCanales', mapping: 'numeroCanales'},
                {name: 'estado', mapping: 'estado'}
            ]
    });

    //grid de usuarios
    var gridCorreos = Ext.create('Ext.grid.Panel', {
        id: 'gridCorreos',
        store: storeLineas,
        columnLines: true,
        dockedItems: [toolbar],
        columns: [
            {
                header: 'Numero',
                dataIndex: 'numero',
                width: 105,
                sortable: true
            },
            {
                header: 'Dominio',
                dataIndex: 'dominio',
                width: 120,
                sortable: true
            },
            {
                header: 'Clave',
                dataIndex: 'clave',
                width: 95,
                sortable: true
            },
            {
                header: 'Canales',
                dataIndex: 'numeroCanales',
                width: 55,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 80
            }
        ],
        viewConfig: {
            stripeRows: true
        },

        frame: true,
        height: 200
    });



    Ext.define('tipoCaracteristica', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'tipo', type: 'string'}
        ]
    });

    var storeModelosCpe = new Ext.data.Store({
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: getModelosElemento,
            extraParams: {
                tipo: 'CPE',
                forma: 'Empieza con',
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

    //-------------------------------------------------------------------------------------------

    var formPanel = Ext.create('Ext.form.Panel', {
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
            //informacion de los elementos del cliente
            //informacion del Cliente
            {
                xtype: 'fieldset',
                title: 'Líneas del Cliente',
                defaultType: 'textfield',
                defaults: {
                    width: 480
                },
                items: [

                    {
                        xtype: 'container',
                        items: [
                            gridCorreos
                        ]//cierre del container table
                    }
                ]//cierre del fieldset
            }, //cierre informacion ont
            {
                xtype: 'fieldset',
                title: 'Información de los Elementos del Cliente',
                defaultType: 'textfield',
                defaults: {
                    width: 480
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
                                id: 'serieCpe',
                                name: 'serieCpe',
                                fieldLabel: 'Serie',
                                displayField: "",
                                value: "",
                                width: '25%',
                                listeners: {
                                    blur: function (serie) {
                                        Ext.Ajax.request({
                                            url: buscarCpeNaf,
                                            method: 'post',
                                            params: {
                                                serieCpe: serie.getValue(),
                                                modeloElemento: '',
                                                estado: 'PI',
                                                bandera: 'ActivarServicio'
                                            },
                                            success: function (response) {
                                                var respuesta = response.responseText.split("|");
                                                var status = respuesta[0];
                                                var mensaje = respuesta[1].split(",");
                                                var descripcion = mensaje[0];
                                                var mac = mensaje[1];
                                                var modelo = mensaje[2];

                                                Ext.getCmp('macCpe').setValue = '';
                                                Ext.getCmp('macCpe').setRawValue('');
                                                Ext.getCmp('modeloCpe').setValue = '';
                                                Ext.getCmp('modeloCpe').setRawValue('');

                                                if (status == "OK")
                                                {
                                                    if (storeModelosCpe.find('modelo', modelo) == -1)
                                                    {
                                                        var strMsj = 'El Elemento con: <br>' +
                                                            'Modelo: <b>' + modelo + ' </b><br>' +
                                                            'Descripcion: <b>' + descripcion + ' </b><br>' +
                                                            'No corresponde a un CPE, <br>' +
                                                            'No podrá continuar con el proceso, Favor Revisar <br>';
                                                        Ext.Msg.alert('Advertencia', strMsj);
                                                    } else
                                                    {

                                                        Ext.getCmp('macCpe').setValue = mac;
                                                        Ext.getCmp('macCpe').setRawValue(mac);
                                                        Ext.getCmp('modeloCpe').setValue = modelo;
                                                        Ext.getCmp('modeloCpe').setRawValue(modelo);
                                                    }
                                                } else
                                                {
                                                    Ext.Msg.alert('Mensaje ', mensaje);
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
                            },
                            {width: '20%', border: false},
                            {
                                queryMode: 'local',
                                xtype: 'textfield',
                                id: 'modeloCpe',
                                name: 'modeloCpe',
                                fieldLabel: 'Modelo',
                                displayField: 'modelo',
                                valueField: 'modelo',
                                loadingText: 'Buscando...',
                                //store: storeModelosCpe,
                                width: '25%'
                            },
                            {width: '10%', border: false},
                            //---------------------------------------

                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id: 'macCpe',
                                name: 'macCpe',
                                fieldLabel: 'Mac',
                                displayField: "",
                                value: "",
                                width: '25%'
                            },
                            {
                                xtype: 'hidden',
                                id: 'validacionMacOnt',
                                name: 'validacionMacOnt',
                                value: "",
                                width: '20%'
                            },
                            {width: '10%', border: false},
                            //---------------------------------------

                            {width: '10%', border: false},
                            {
                                xtype: 'textareafield',
                                id: 'observacionCliente',
                                name: 'observacionCliente',
                                fieldLabel: 'Observacion',
                                displayField: "",
                                labelPad: -45,
                                colspan: 4,
                                value: "",
                                width: '87%'
                            }

                        ]//cierre del container table
                    }
                ]
            }//cierre informacion de los elementos del cliente

        ],
        buttons: [{
                text: 'Activar',
                formBind: true,
                handler: function () {

                    var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                    var serieCpe = Ext.getCmp('serieCpe').getValue();
                    var mac = Ext.getCmp('macCpe').getValue();
                    var observacion = Ext.getCmp('observacionCliente').getValue();


                    var validacion = true;
                    var flag = 0;

                    if (serieCpe == "")
                    {
                        validacion = false;
                        flag = 1;
                    }

                    if (mac)
                    {
                        var bandera = 1;
                        if (mac.length != 14)
                        {
                            bandera = 0;
                        }
                        if (mac.charAt(4) != ".")
                        {
                            bandera = 0;
                        }
                        if (mac.charAt(9) != ".")
                        {
                            bandera = 0;
                        }
                        if (bandera == 0)
                        {
                            Ext.Msg.alert('Mensaje ', "Favor ingrese la mac en formato correcto (aaaa.bbbb.cccc) * ");
                            return false;
                        }
                    } else
                    {
                        Ext.Msg.alert('Mensaje ', "Favor ingrese la mac");
                        return false;
                    }

                    if (validacion) {
                        Ext.get(formPanel.getId()).mask('Por Favor Espere...');


                        Ext.Ajax.request({
                            url: url_gestionarLineasTelefonicas,
                            method: 'post',
                            timeout: 400000,
                            params: {
                                opcion: 'ACTIVAR',
                                idServicio: data.idServicio,
                                idProducto: data.productoId,
                                login: data.login,
                                serie: serieCpe,
                                modelo: modeloCpe,
                                mac: mac,
                                observacion: observacion
                            },
                            success: function (response) {
                                Ext.get(formPanel.getId()).unmask();
                                if (response.responseText == "OK") {
                                    Ext.Msg.alert('Mensaje', 'Transacción exitosa.', function (btn) {
                                        if (btn == 'ok') {
                                            win.destroy();
                                            store.load();
                                        }
                                    });
                                } else
                                {
                                    Ext.Msg.alert('Mensaje ', response.responseText);
                                }
                            },
                            failure: function (result)
                            {
                                Ext.get(formPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });

                    } else {
                        if (flag == 1) {
                            Ext.Msg.alert("Validación", "Debe ingresar la serie.", function (btn) {
                                if (btn == 'ok') {
                                }
                            });
                        } else if (flag == 2) {
                            Ext.Msg.alert("Validación", "Datos del Ont incorrectos, favor revisar", function (btn) {
                                if (btn == 'ok') {
                                }
                            });
                        } else if (flag == 3) {
                            Ext.Msg.alert("Validación", "La vlan debe estar en un rango de 1 -2000", function (btn) {
                                if (btn == 'ok') {
                                }
                            });
                        } else if (flag == 4) {
                            Ext.Msg.alert("Validación", "La ip no tiene el formato correcto", function (btn) {
                                if (btn == 'ok') {
                                }
                            });
                        } else {
                            Ext.Msg.alert("Validación", "Favor revise los campos", function (btn) {
                                if (btn == 'ok') {
                                }
                            });
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

    var win = Ext.create('Ext.window.Window', {
        title: 'Activación',
        width: 520,
        items: [formPanel]
    }).show();

    storeModelosCpe.load({});

}


//activarLineasAnalogicas

function activarLineasTelefonicas(data) 
{
    var storeLineas = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_verLineasTelefonicas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio
            }
        },
        fields:
            [
                {name: 'idNumero', mapping: 'idNumero'},
                {name: 'numero', mapping: 'numero'},
                {name: 'idDominio', mapping: 'idDominio'},
                {name: 'dominio', mapping: 'dominio'},
                {name: 'idClave', mapping: 'idClave'},
                {name: 'clave', mapping: 'clave'},
                {name: 'idNumeroCanales', mapping: 'idNumeroCanales'},
                {name: 'numeroCanales', mapping: 'numeroCanales'},
                {name: 'estado', mapping: 'estado'}
            ]
    });

    //grid de usuarios
    var gridCorreos = Ext.create('Ext.grid.Panel', {
        id: 'gridCorreos',
        store: storeLineas,
        columnLines: true,
        dockedItems: [toolbar],
        columns: [
            {
                header: 'Numero',
                dataIndex: 'numero',
                width: 105,
                sortable: true
            },
            {
                header: 'Dominio',
                dataIndex: 'dominio',
                width: 120,
                sortable: true
            },
            {
                header: 'Clave',
                dataIndex: 'clave',
                width: 95,
                sortable: true
            },
            {
                header: 'Canales',
                dataIndex: 'numeroCanales',
                width: 55,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 80
            }
        ],
        viewConfig: {
            stripeRows: true
        },

        frame: true,
        height: 200
    });

    var formPanel = Ext.create('Ext.form.Panel', {
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
            //informacion de los elementos del cliente
            //informacion del Cliente
            {
                xtype: 'fieldset',
                title: 'Líneas del Cliente',
                defaultType: 'textfield',
                defaults: {
                    width: 480
                },
                items: [

                    {
                        xtype: 'container',
                        items: [
                            gridCorreos
                        ]//cierre del container table
                    }
                ]//cierre del fieldset
            }

        ],
        buttons: [{
                text: 'Activar',
                formBind: true,
                handler: function () {
                    Ext.get(formPanel.getId()).mask('Por Favor Espere...');
                    Ext.Ajax.request({
                        url: url_gestionarLineasTelefonicas,
                        method: 'post',
                        timeout: 400000,
                        params: {
                            opcion: 'ACTIVAR',
                            idServicio: data.idServicio,
                            idProducto: data.productoId,
                            login: data.login
                        },
                        success: function (response) {
                            Ext.get(formPanel.getId()).unmask();
                            if (response.responseText == "OK") {
                                Ext.Msg.alert('Mensaje', 'Transacción exitosa.', function (btn) {
                                    if (btn == 'ok') {
                                        win.destroy();
                                        store.load();
                                    }
                                });
                            } else
                            {
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
            }, {
                text: 'Cancelar',
                handler: function () {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Activación',
        width: 520,
        items: [formPanel]
    }).show();
}