
function mostrarDetalleL2mpls(data)
{
    var formActivarL2mpls = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        id: 'formActivarL2mpls',
        BodyPadding: 10,
        width: 750,
        height: 'auto',
        bodyStyle: "background: white; padding: 5px; border: 0px none;",
        frame: true,
        items:
            [
                {
                    xtype: 'fieldset',
                    id: 'resumenCrearMv',
                    title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos Comerciales y Recursos generados</b>',
                    layout: {
                        tdAttrs: {style: 'padding: 3px;'},
                        type: 'table',
                        columns: 5,
                        pack: 'center'
                    },
                    items: [
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Cliente</b>',
                            width: 300,
                            value: data.nombreCompleto,
                            readOnly: true
                        },
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Login</b>',
                            value: data.login,
                            width: 300,
                            readOnly: true
                        },
                        {width: '10%', border: false},
                        //-------------------------------------
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Capacidad 1</b>',
                            width: 300,
                            value: data.capacidadUno,
                            readOnly: true
                        },
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Capacidad 2</b>',
                            value: data.capacidadDos,
                            width: 300,
                            readOnly: true
                        },
                        {width: '10%', border: false},
                        //-------------------------------------
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>RO Conc.DC</b>',
                            width: 300,
                            value: data.elementoNombre,
                            readOnly: true
                        },
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Puerto</b>',
                            value: data.interfaceElementoNombre,
                            width: 300,
                            readOnly: true
                        },
                        {width: '10%', border: false},
                        //-------------------------------------
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Ip loopback Cpe</b>',
                            width: 300,
                            value: data.ipServicio,
                            readOnly: true,
                            fieldStyle:'color:green;font-weigth:bold;'
                        },
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Virtual Connect</b>',
                            width: 300,
                            value: data.virtualConnect,
                            readOnly: true,
                            fieldStyle:'color:green;font-weigth:bold;'
                        },
                        {width: '10%', border: false},
                        //--------------------------------------
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Ip loopback Pe</b>',
                            width: 300,
                            value: data.iploopback,
                            readOnly: true,
                            fieldStyle:'color:green;font-weigth:bold;'
                        },
                        {width: '10%', border: false},
                        {
                            xtype: 'textfield',
                            fieldLabel: '<b>Pe (Extremo)</b>',
                            width: 300,
                            value: data.peExtremoL2,
                            readOnly: true,
                            fieldStyle:'color:green;font-weigth:bold;'
                        },
                        {width: '10%', border: false}
                    ]
                }
            ],
        buttons: [
            {
                text: '<i class="fa fa-plug" aria-hidden="true"></i>&nbsp;Activar Servicio',
                handler: function ()
                {
                    Ext.get(winActivarL2mpls.getId()).mask('Activando Servicio...');

                    Ext.Ajax.request({
                        url: urlActivacionL2mpls,
                        timeout: 600000,
                        method: 'post',
                        params:
                            {
                                idServicio    : data.idServicio,
                                tipoEnlace    : data.tipoEnlace,
                                elementoNombre: data.elementoNombre,
                                interface     : data.interfaceElementoNombre,
                                ipServicio    : data.ipReservada,
                                subred        : data.subredServicio,
                                gateway       : data.gwSubredServicio,
                                mascara       : data.mascaraSubredServicio,
                                solicitud     : data.tieneSolicitudPlanificacion,
                                virtualConnect: data.virtualConnect
                            },
                        success: function (response)
                        {
                            Ext.get(winActivarL2mpls.getId()).unmask();

                            var objJson = Ext.JSON.decode(response.responseText);

                            Ext.Msg.alert('Mensaje', objJson.mensaje, function (btn) {
                                if (btn == 'ok')
                                {
                                    if (objJson.status === 'OK')
                                    {
                                        winActivarL2mpls.close();
                                        winActivarL2mpls.destroy();
                                        store.load();
                                    }
                                }
                            });
                        }
                    });
                }
            },
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function ()
                {
                    winActivarL2mpls.close();
                    winActivarL2mpls.destroy();
                }
            }
        ]});

    var winActivarL2mpls = Ext.widget('window', {
        id: 'winActivacionHeredada',
        title: 'Activación de Servicio Concentrador L2',
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: true,
        width: 'auto',
        items: [formActivarL2mpls]
    });

    winActivarL2mpls.show();

}