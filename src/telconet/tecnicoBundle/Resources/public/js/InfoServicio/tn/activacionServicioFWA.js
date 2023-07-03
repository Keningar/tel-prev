function mostrarDetalleActivarFWA(data)
{
    
    Ext.get(gridServicios.getId()).mask('Obteniendo Datos...');
    
    Ext.Ajax.request({
        url: urlGetInformacionServiciosFWA,
        method: 'post',
        params: 
        { 
            idServicioHeredado : data.servicioHeredadoFact,
            idServicio         : data.idServicio
        },
        success: function(response)
        {
            Ext.get(gridServicios.getId()).unmask();
            
            var objRespFWA = Ext.JSON.decode(response.responseText);
            if(objRespFWA.strStatus == 'OK')
            {
                var objJson = objRespFWA.arrayData;
                var formActivacionHeredada = Ext.create('Ext.form.Panel', {
                buttonAlign: 'center',
                id: 'formActivacionHeredada',
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
                            title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos Comerciales y Factibilidad del Servicio</b>',
                            layout: {
                                tdAttrs: {style: 'padding: 3px;'},
                                type: 'table',
                                columns: 5,
                                pack: 'center'
                            },
                            items: [
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Cliente</b>',
                                    width: 300,
                                    value: data.nombreCompleto,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Login</b>',
                                    value: data.login,                            
                                    width: 300,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Concentrador</b>',
                                    width: 300,
                                    value: objJson.concentrador,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Concentrador Virtual</b>',
                                    width: 300,
                                    value: objJson.concentradorFWA,
                                    id: 'cpeFWA'
                                },
                                {   width: '10%', border: false}
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            id: 'datosTecnicosServicioNuevo',
                            title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;'+
                                    '\n\<b>Datos Técnicos Servicio a ser <label style="color:green;">Activado</label></b>',
                            layout: {
                                tdAttrs: {style: 'padding: 3px;'},
                                type: 'table',
                                columns: 5,
                                pack: 'center'
                            },
                            items: 
                            [
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<label style="color:blue;">Ip Loopback</label>',
                                    width: 300,
                                    value: objJson.ipLoopBack,
                                    readOnly: true,
                                    id: 'ipLoopBack'
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<label style="color:green;">Ip Wan<br>(Telefónica)</label>',
                                    width: 300,
                                    id: 'ipWanTelefonica'
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<label style="color:blue;">Login FWA</label>',
                                    width: 300,
                                    id: 'loginFWA'
                                },
                                {   width: '10%', border: false},
                                {   width: '10%', border: false},
                                {   width: '10%', border: false}
                            ]
                        }
                    ],
                buttons: [
                    {
                        text: '<i class="fa fa-plug" aria-hidden="true"></i>&nbsp;Activar Servicio',
                        handler: function()
                        {
                            var ipLoopBack      = Ext.getCmp('ipLoopBack').getValue();
                            var ipWanTelefonica = Ext.getCmp('ipWanTelefonica').getValue();
                            var loginFWA        = Ext.getCmp('loginFWA').getValue();
                            var boolContinuar   = true;

                            if(Ext.isEmpty(ipWanTelefonica))
                            {
                                Ext.Msg.alert('Error','Debe escribir la ip Wan de Telefónica.');
                                boolContinuar = false;
                            }
                            else if(Ext.isEmpty(loginFWA))
                            {
                                Ext.Msg.alert('Error','Debe escribir el login del servicio provisto por Telefónica.');
                                boolContinuar = false;
                            }

                            if(boolContinuar)
                            {
                                Ext.get(winActivacionHeredada.getId()).mask('Activando Servicio...');
                                Ext.Ajax.request({
                                    url: activarClienteBoton,
                                    timeout:600000,
                                    method: 'post',
                                    params: 
                                    {
                                        idServicio              : data.idServicio,

                                        vlan                    : data.vlan,
                                        vrf                     : data.vrf,
                                        protocolo               : data.protocolo,
                                        asPrivado               : data.asPrivado,
                                        ipServicio              : data.ipServicio,
                                        subredServicio          : data.subredServicio,
                                        loginAux                : data.loginAux,
                                        capacidadUno            : data.capacidadUno,
                                        capacidadDos            : data.capacidadDos,
                                        gwSubredServicio        : data.gwSubredServicio,
                                        mascaraSubredServicio   : data.mascaraSubredServicio,
                                        elementoPadre           : data.elementoPadre,
                                        rdId                    : data.rdId,
                                        defaultGateway          : data.defaultGateway,
                                        macCpe                  : objJson.macCpe,
                                        anillo                  : data.anillo,


                                        tipoEnlace              :data.tipoEnlace,
                                        interfaceElementoId     :data.interfaceElementoId,
                                        idProducto              :data.productoId,
                                        login                   :data.login,
                                        flagCpe                 :false,
                                        boolActivarWifi         :false,
                                        idServicioWifi          :'',
                                        idIntWifiSim            :'',

                                        //PseudoPe
                                        esPseudoPe              : 'S',

                                        //DatoFWA
                                        ipLoopBack              : ipLoopBack,
                                        ipWanTelefonica         : ipWanTelefonica,
                                        loginFWA                : loginFWA
                                    },
                                    success: function(response)
                                    {
                                        Ext.get(winActivacionHeredada.getId()).unmask();

                                        var objJson = Ext.JSON.decode(response.responseText);

                                        Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                                            if (btn == 'ok')
                                            {
                                                if(objJson.status === 'OK')
                                                {
                                                    winActivacionHeredada.close();
                                                    winActivacionHeredada.destroy();
                                                    store.load();
                                                }
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                        handler: function()
                        {
                            winActivacionHeredada.close();
                            winActivacionHeredada.destroy();
                        }
                    }
                ]});

                var winActivacionHeredada = Ext.widget('window', {
                    id: 'winActivacionHeredada',
                    title: 'Activación de Servicio FWA',
                    layout: 'fit',
                    resizable: true,
                    modal: true,
                    closable: true,
                    width: 'auto',
                    items: [formActivacionHeredada]
                });

                winActivacionHeredada.show();
            }
            else
            {
                Ext.Msg.show(
                            {
                                title: 'Error',
                                msg: objRespFWA.strMensaje,
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR
                            });
            }

        }
     });
    
    
}
