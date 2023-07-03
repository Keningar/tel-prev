var idServicio       = "";
var elementoId       = "";
var vrf              = "";
var idProducto       = "";
var banderaVerificar = "N";
var subredValida     = "";

function crearRutaAutomatica(data, grid)
{
        idServicio        = data.idServicio;
        elementoId        = data.elementoId;
        vrf               = data.vrf;
        idProducto        = data.productoId;

        var iniHtml       = '<button id="btn_verificar" class="btn-copy" onclick="verificarSubred()" disabled="true">\n\
                             Verificar Subred</button>';
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


    if(data.elementoPadre=="No definido")
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: 'No existe PE asociado, Imposible crear ruta automatica',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
        }); 
        return;
    }
    else
    {
        Ext.get(grid.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({
            url: verIps,
            method: 'post',
            timeout: 400000,
            params: {
                idServicio: data.idServicio
            },
            success: function(response) {
                Ext.get(grid.getId()).unmask();
                var json = Ext.JSON.decode(response.responseText);
                
                if (json.total > 1 && json.total < 1) {
                    Ext.Msg.show({
                        title: 'Error',
                        msg: 'Error: Problemas de configuraciones con la IP asignada',
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                } else {
                    var storeMascaras = Ext.create('Ext.data.Store', {
                        fields: ['value', 'name'],
                        data: [
                            {"value": "0", "name": "--Seleccione--"},
                            {"value": "255.255.255.254", "name": "/31"},
                            {"value": "255.255.255.252", "name": "/30"},
                            {"value": "255.255.255.248", "name": "/29"},
                            {"value": "255.255.255.240", "name": "/28"},
                            {"value": "255.255.255.224", "name": "/27"},
                            {"value": "255.255.255.192", "name": "/26"},
                            {"value": "255.255.255.128", "name": "/25"},
                            {"value": "255.255.255.0", "name": "/24"}
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
                                        xtype: 'combobox',
                                        fieldLabel: 'Mascara',
                                        name: 'cbxMaskLan',
                                        id: 'cbxMaskLan',
                                        store: storeMascaras,
                                        queryMode: 'local',
                                        displayField: 'name',
                                        valueField: 'value',
                                        value: "0",
                                        editable: false,
                                        width: 250,
                                        listeners: {
                                            select: function(combo){
                                                generarSubRed("select",combo.getValue());
                                            }
                                        }
                                    },
                                    espacio_blanco2,
                                    {
                                        
                                        fieldLabel: 'Sub Red Asignada',
                                        name: 'txtSubRedAsignada',
                                        id: 'txtSubRedAsignada',
                                        value: '',
                                        readOnly: true,
                                        width: 250
                                    },
                                    btn_verificar,
                                    {
                                        fieldLabel: 'Ip Destino',
                                        name: 'txtIpDestino',
                                        id: 'txtIpDestino',
                                        value: json.encontrados[0].ip,
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

                                    var cbxMaskLan          = Ext.getCmp('cbxMaskLan');
                                    var txtIpDestino        = Ext.getCmp('txtIpDestino');
                                    var txtNombreRuta       = Ext.getCmp('txtNombreRuta');
                                    var txtDistanciaAdmin   = Ext.getCmp('txtDistanciaAdmin');

                                    if (!expRegNombre.test(txtNombreRuta.getValue())) {
                                        var msg = 'Por favor ingrese un nombre correcto para la Ruta';
                                        txtNombreRuta.markInvalid(msg);
                                        return;
                                    }

                                    if (cbxMaskLan.getValue() === "0")
                                    {
                                        var msg = 'Mascara incorrecta. Por favor corrija';
                                        cbxMaskLan.markInvalid(msg);
                                        return;
                                    }

                                    Ext.Msg.show({
                                        title: 'Confirmar',
                                        msg: 'Esta seguro de crear la ruta automática?',
                                        buttons: Ext.Msg.YESNOCANCEL,
                                        icon: Ext.MessageBox.QUESTION,
                                        buttonText: {
                                            yes: 'si', no: 'no', cancel: 'cancelar'
                                        },
                                        fn: function(btn) {
                                            if (btn === 'yes') {
                                                Ext.MessageBox.wait('Creando Ruta...');

                                                Ext.Ajax.request({
                                                    url: asignarRutaAutomatica,
                                                    method: 'post',
                                                    timeout: 400000,
                                                    params: {
                                                        idServicio:         data.idServicio,
                                                        idProducto:         data.productoId,
                                                        idElemento:         data.elementoId,
                                                        vrf:                data.vrf,
                                                        nombreRuta:         txtNombreRuta.getValue(),
                                                        maskLan:            cbxMaskLan.getValue(),
                                                        ipDestino:          txtIpDestino.getValue(),
                                                        distanciaAdmin:     txtDistanciaAdmin.getValue(),
                                                        subred:             Ext.getCmp('txtSubRedAsignada').getValue(),
                                                        rutaAutomatica:     "S"
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
    //                                                    Ext.MessageBox.hide();
    //                                                    win.destroy();

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
                                    var subred = "";
                                    subred = Ext.getCmp('txtSubRedAsignada').getValue();

                                    if(subred !== "")
                                    {
                                        liberarSubRed();
                                        win.destroy();
                                    }
                                    else
                                    {
                                        win.destroy();
                                    }
                                }
                            }]
                    });

                    var win = Ext.create('Ext.window.Window', {
                        title: 'Crear Ruta Automatica',
                        modal: true,
                        width: 380,
                        closable: false,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();
                }
            }//cierre response
        });
    }  
}

function verificarSubred()
{
    if(banderaVerificar === "N")
    {
        var strSubred    = Ext.getCmp('txtSubRedAsignada').getValue();
        var strMascara   = Ext.getCmp('cbxMaskLan').getValue();
        var strIpDestino = Ext.getCmp('txtIpDestino').getValue();
        banderaVerificar = "S";

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
                    subred       : strSubred,
                    IdServicio   : idServicio,
                    ipDestino    : strIpDestino,
                    idElemento   : elementoId,
                    vrf          : vrf,
                    idProducto   : idProducto,

                },
            success: function(response) {
                var text           = Ext.decode(response.responseText);
                var ventanaMensaje = null;
                if(text.strSubredValida === "S")
                {
                    subredValida   = "S";
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
                    subredValida    = "N";
                    ventanaMensaje  = Ext.Msg.show({
                        title:'Información',
                        msg: text.strMsg,
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFO,
                        fn: function(btn) {
                            if (btn === 'ok')
                            {
                                generarSubRed("",strMascara)
                            }
                        }
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
        var mensaje = "";
        if(subredValida === "S")
        {
            mensaje = "si es valida";
        }
        else
        {
            mensaje = "no es valida";
        }

        Ext.Msg.show(
        {
            title: 'Información',
            msg: "La opcion ya fue ejecutada, la subred "+mensaje,
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.INFO
        });
    }
}

function generarSubRed(origen,mascara)
{
    var strSubred    = Ext.getCmp('txtSubRedAsignada').getValue()?Ext.getCmp('txtSubRedAsignada').getValue():"";
    banderaVerificar = "N";

    var connConsultarSubRed = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Consultando y asignando una subred...',
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

    connConsultarSubRed.request({
        url: urlConsultarSubredAsignada,
        method: 'post',
        params:
            {
                origen     : origen,
                subred     : strSubred,
                idServicio : idServicio,
                idElemento : elementoId,
                maskLan    : mascara
            },
        success: function(response) {
            var text = Ext.decode(response.responseText);

            if(text.strEstatus === "OK")
            {
                Ext.getCmp('txtSubRedAsignada').setValue(text.strSubred);

                document.getElementById('btn_verificar').disabled = false;
                Ext.getCmp('btnCrear').setDisabled(true);
            }
            else
            {
                Ext.getCmp('txtSubRedAsignada').setValue("");

                var messagebox = Ext.Msg.show(
                {
                    title: 'Información',
                    msg: text.strMsg,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.INFO
                });

                Ext.Function.defer(function () {
                    messagebox.zIndexManager.bringToFront(messagebox);
                },100);

                document.getElementById('btn_verificar').disabled = true;
                Ext.getCmp('btnCrear').setDisabled(true);
            }
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

function liberarSubRed()
{
    var connLiberarSubRed = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Cerrando...',
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

    connLiberarSubRed.request({
        url: urlLiberarSubredAsignada,
        method: 'post',
        params:
            {
                idServicio: idServicio,
                idElemento: elementoId,
                subred    : Ext.getCmp('txtSubRedAsignada').getValue()
            },
        success: function(response) {
            var text = Ext.decode(response.responseText);

            var messagebox = Ext.Msg.show(
            {
                title: 'Información',
                msg: text.strMsg,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.INFO
            });

            Ext.Function.defer(function () {
                messagebox.zIndexManager.bringToFront(messagebox);
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