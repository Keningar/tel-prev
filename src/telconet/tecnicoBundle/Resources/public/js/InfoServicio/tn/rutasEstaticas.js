var idServicio       = "";
var elementoId       = "";
var vrf              = "";
var idProducto       = "";

function crearRutaEstatica(data, grid)
{
    idServicio        = data.idServicio;
    elementoId        = data.elementoId;
    vrf               = data.vrf;
    idProducto        = data.productoId;

    var iniHtml =  '';
    if( data.descripcionProducto==='INTERNET' || data.descripcionProducto==='INTMPLS' || data.descripcionProducto==='INTERNET SDWAN' )
    {
        iniHtml = '<button id="btn_verificar" class="btn-copy" onclick="verificarSubredEstatica()">\n\Verificar Subred</button>';
    }

    var espacioBlanco = '<div>&nbsp;</div>';

    btn_verificar =  Ext.create('Ext.Component', {
        html : iniHtml,
        width: 200,
        style: { color: '#000000' }
    });

        espacio_blanco =  Ext.create('Ext.Component', {
            id   : 'espacioVacio',
            html : espacioBlanco,
            width: 50,
            style: { color: '#000000' }
        });

        espacio_blanco2 =  Ext.create('Ext.Component', {
            id   : 'espacioVacio2',
            html : espacioBlanco,
            width: 50,
            style: { color: '#000000' }
        });

        espacio_blanco3 =  Ext.create('Ext.Component', {
            id   : 'espacioVacio3',
            html : espacioBlanco,
            width: 50,
            style: { color: '#000000' }
        });

        espacio_blanco4 =  Ext.create('Ext.Component', {
            id   : 'espacioVacio4',
            html : espacioBlanco,
            width: 50,
            style: { color: '#000000' }
        });

    if(data.elementoPadre=="No definido")
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: 'No existe PE asociado, Imposible crear ruta estatica',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
        }); 
        return;
    }
    else
    {
        var storeMascaras = Ext.create('Ext.data.Store', {
            fields: ['value', 'name'],
            data: [
                {"value": "0", "name": "--Seleccione--"},
                {"value": "255.255.255.255", "name": "/32"},
                {"value": "255.255.255.254", "name": "/31"},
                {"value": "255.255.255.252", "name": "/30"},
                {"value": "255.255.255.248", "name": "/29"},
                {"value": "255.255.255.240", "name": "/28"},
                {"value": "255.255.255.224", "name": "/27"},
                {"value": "255.255.255.192", "name": "/26"},
                {"value": "255.255.255.128", "name": "/25"},
                {"value": "255.255.255.0", "name": "/24"},                
                {"value": "255.255.254.0", "name": "/23"},
                {"value": "255.255.252.0", "name": "/22"},
                {"value": "255.255.248.0", "name": "/21"},
                {"value": "0.0.0.0", "name": "/0"},
            ]
        });

        var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 2,
            buttonAlign: 'center',
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 85,
                msgTarget: 'side'
            },
            items: [
                {
                    xtype: 'fieldset',
                    title: 'Informacion de Ruta',
                    defaultType: 'textfield',
                    defaults: {
                        width: 200
                    },
                    layout: {
                        type: 'table',
                        columns: 2,
                        align: 'left',
                        border: false,
                    },
                    items: [
                        {
                            fieldLabel: 'Nombre',
                            name: 'txtNombreRuta',
                            id: 'txtNombreRuta',
                            width: 250
                        },
                        espacio_blanco,
                        {
                            fieldLabel: 'Red Lan',
                            name: 'txtRedLan',
                            id: 'txtRedLan',
                            width:250                                    
                        },
                        espacio_blanco2,
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Máscara',
                            name: 'cbxMaskLan',
                            id: 'cbxMaskLan',
                            store: storeMascaras,
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'value',
                            value: "0",
                            editable: false,
                            width: 250
                        },
                        btn_verificar,
                        {
                            fieldLabel: 'Ip Destino',
                            name: 'txtIpDestino',
                            id: 'txtIpDestino',
                            value: data.ipServicio,
                            readOnly: true,
                            width: 250
                        },
                        espacio_blanco3,
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Distancia Admin',
                            name: 'txtDistanciaAdmin',
                            id: 'txtDistanciaAdmin',
                            value: 1,
                            maxValue: 255,
                            minValue: 1
                        },
                        espacio_blanco4,
                        {
                            xtype: 'checkboxfield',
                            fieldLabel: 'Enrutar en el PE',
                            name: 'chkEnrutarPE',
                            id: 'chkEnrutarPE',
                            checked: true,
                            inputValue: '0',
                            listeners: {
                                change: function (cb, nv, ov)
                                {
                                    if( data.descripcionProducto==='INTERNET' || data.descripcionProducto==='INTMPLS' 
                                        || data.descripcionProducto==='INTERNET SDWAN')
                                    {
                                        if(Ext.getCmp('chkEnrutarPE').getValue())
                                        {
                                            Ext.getCmp('btnCrear').setDisabled(true);
                                            document.getElementById('btn_verificar').disabled = false;
                                        }
                                        else
                                        {
                                            Ext.getCmp('btnCrear').setDisabled(false);
                                            document.getElementById('btn_verificar').disabled = true;
                                        }
                                    }
                                }
                            }
                        }
                    ]
                }
            ],
            buttons: [{
                    text: 'Crear',
                    id: 'btnCrear',
                    disabled: true,
                    handler: function() {
                        var expRegNombre = /^[a-zA-Z0-9_]+$/;
                        var expRegIp     = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;

                        var cbxMaskLan          = Ext.getCmp('cbxMaskLan');
                        var chkEnrutarPE        = Ext.getCmp('chkEnrutarPE');
                        var txtRedLan           = Ext.getCmp('txtRedLan');
                        var txtIpDestino        = Ext.getCmp('txtIpDestino');
                        var txtNombreRuta       = Ext.getCmp('txtNombreRuta');
                        var txtDistanciaAdmin   = Ext.getCmp('txtDistanciaAdmin');
                        
                        var msg = '';

                        if (!expRegNombre.test(txtNombreRuta.getValue()))
                        {
                            msg = 'Por favor ingrese un nombre correcto para la Ruta.\n Los valores permitidos: A-z a-z 0-9 y subguión( _ )';
                            txtNombreRuta.markInvalid(msg);
                            return;
                        }
                        
                        if (!expRegIp.test(txtRedLan.getValue()))
                        {
                            msg = 'Por favor ingrese una red correcta para la Ruta';
                            txtRedLan.markInvalid(msg);
                            return;
                        }

                        if (cbxMaskLan.getValue() === "0")
                        {
                            msg = 'Mascara incorrecta. Por favor corrija';
                            cbxMaskLan.markInvalid(msg);
                            return;
                        }
                        
                         if(cbxMaskLan.getValue() === "0.0.0.0" &&
                            (
                                data.descripcionProducto==='INTERNET' ||
                                data.descripcionProducto==='INTMPLS' ||
                                data.descripcionProducto==='INTERNET SDWAN'
                            )
                          )
                        {
                            msg = 'Mascara NO PERMITIDA para servicios INTERNET. Por favor elegir otra.';
                            cbxMaskLan.markInvalid(msg);
                            return;
                        }

                        Ext.Msg.show({
                            title: 'Confirmar',
                            msg: 'Está seguro de crear la ruta estática?',
                            buttons: Ext.Msg.YESNOCANCEL,
                            icon: Ext.MessageBox.QUESTION,
                            buttonText: {
                                yes: 'Si', no: 'No', cancel: 'Cancelar'
                            },
                            fn: function(btn) {
                                if (btn === 'yes') {
                                    Ext.MessageBox.wait('Creando Ruta...');

                                    Ext.Ajax.request({
                                        url: asignarRutaEstatica,
                                        method: 'post',
                                        timeout: 400000,
                                        params: {
                                            idServicio:         data.idServicio,
                                            idProducto:         data.productoId,
                                            idElemento:         data.elementoId,
                                            vrf:                data.vrf,
                                            nombreRuta:         txtNombreRuta.getValue(),
                                            redLan:             txtRedLan.getValue(),
                                            maskLan:            cbxMaskLan.getValue(),
                                            maskValue:          cbxMaskLan.getRawValue(),
                                            enrutarPE:          chkEnrutarPE.getValue(),
                                            ipDestino:          txtIpDestino.getValue(),
                                            distanciaAdmin:     txtDistanciaAdmin.getValue()
                                        },
                                        success: function(response) {
                                            Ext.MessageBox.hide();
                                            win.destroy();

                                            Ext.Msg.alert('Mensaje', response.responseText, function(btn) {
                                                if (btn === 'ok') {
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        },
                                        failure: function(response)
                                        {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: response.responseText,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    });
                                }
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
            title: 'Crear Ruta Estatica',
            modal: true,
            width: 380,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();  

        if( data.descripcionProducto !== 'INTERNET' && data.descripcionProducto !== 'INTMPLS' && data.descripcionProducto !== 'INTERNET SDWAN')
        {
            Ext.getCmp('btnCrear').setDisabled(false);
        }
    }
}

function verificarSubredEstatica()
{
    var strRedLan    = Ext.getCmp('txtRedLan').getValue();
    var strMascara   = Ext.getCmp('cbxMaskLan').getValue();

    if(strRedLan !== "" && strRedLan !== null && strMascara !== "" && strMascara !== null)
    {
        strRedLan    = Ext.getCmp('txtRedLan').getValue();
        strMascara   = Ext.getCmp('cbxMaskLan').getValue();
        var strIpDestino = Ext.getCmp('txtIpDestino').getValue();

        var connVerificarSubred = new Ext.data.Connection({
            listeners: {
                'beforerequest': {
                    fn: function(con, opt) {
                        Ext.MessageBox.show({
                            msg: 'Verificando subred con NetWorking',
                            progressText: 'Saving...',
                            width: 300,
                            wait: true,
                            waitConfig: {interval: 200}
                        });
                    },
                    scope: this
                },
                'requestcomplete': {
                    fn: function(con, res, opt) {
                        Ext.MessageBox.hide();
                    },
                    scope: this
                },
                'requestexception': {
                    fn: function(con, res, opt) {
                        Ext.MessageBox.hide();
                    },
                    scope: this
                }
            }
        });

        connVerificarSubred.request({
            url: urlVerificarSubredAsignada,
            method: 'post',
            timeout: 300000,
            params:
                {
                    subred       : strRedLan,
                    mascara      : strMascara,
                    IdServicio   : idServicio,
                    ipDestino    : strIpDestino,
                    idElemento   : elementoId,
                    vrf          : vrf,
                    idProducto   : idProducto,
                    origen       : "rutaEstatico",
                    enrutarPE    : Ext.getCmp('chkEnrutarPE').getValue()
                },
            success: function(response) {
                var text           = Ext.decode(response.responseText);
                var ventanaMensaje = null;
                if(text.strSubredValida === "S")
                {
                    ventanaMensaje = Ext.Msg.show(
                    {
                        title: 'Información',
                        msg: text.strMsg,
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.INFO
                    });

                    Ext.getCmp('btnCrear').setDisabled(false);
                }
                else
                {
                    Ext.getCmp('btnCrear').setDisabled(true);
                    ventanaMensaje  = Ext.Msg.show({
                        title:'Información',
                        msg: text.strMsg,
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFO
                    });
                }

                Ext.Function.defer(function () {
                    ventanaMensaje.zIndexManager.bringToFront(ventanaMensaje);
                },100);

            },
            failure: function(result) {
                Ext.Msg.show({
                    title: 'Error',
                    msg: result.statusText,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });
    }
    else
    {
        Ext.Msg.show(
        {
            title: 'Alerta',
            msg: "Por favor ingresar una Red Lan y Máscara",
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.INFO
        });
    }
}