function mostrarDetalleActivar(data)
{
    var boolEsConcentrador = data.descripcionProducto==='CONCINTER';
    var boolEsL3Mpls       = data.descripcionProducto==='L3MPLS'; 
        
    Ext.get(gridServicios.getId()).mask('Obteniendo Datos...');
    
    Ext.Ajax.request({
        url: urlGetInformacionServiciosHeredados,
        method: 'post',
        params: 
        { 
            idServicioHeredado : data.servicioHeredadoFact,
            idServicio         : data.idServicio
        },
        success: function(response)
        {
            Ext.get(gridServicios.getId()).unmask();
            
            var objJson = Ext.JSON.decode(response.responseText)[0];
            
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
                                    fieldLabel: '<b>Capacidad 1</b>',
                                    width: 300,
                                    value: data.capacidadUno,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Capacidad 2</b>',
                                    value: data.capacidadDos,                            
                                    width: 300,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Switch</b>',
                                    width: 300,
                                    value: data.elementoNombre,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Puerto</b>',
                                    value: data.interfaceElementoNombre,                            
                                    width: 300,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Pe</b>',
                                    width: 300,
                                    value: data.elementoPadre,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Cpe</b>',
                                    width: 300,
                                    value: objJson.nombreCpe,
                                    readOnly: true,
                                    hidden:boolEsConcentrador
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Interfaz Cpe</b>',
                                    width: 300,
                                    value: objJson.interfaceCpe,
                                    readOnly: true,
                                    hidden:boolEsConcentrador
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Mac</b>',
                                    width: 300,
                                    value: objJson.macCpe,
                                    readOnly: true,
                                    fieldStyle:'color:green;',
                                    hidden:boolEsConcentrador
                                },
                                {   width: '10%', border: false}
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            id: 'datosTecnicosServicioAnterior',
                            hidden:boolEsConcentrador,
                            title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;\n\
                                    <b>Datos Técnicos del Servicio <label style="color:red;">Heredado</label></b>',
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
                                    fieldLabel: '<b>Cliente</b>',
                                    width: 300,
                                    value: objJson.razonSocial,
                                    fieldStyle:'font-weight:bold;',
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Login</b>',
                                    value: objJson.login,
                                    fieldStyle:'font-weight:bold;',
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
                                    readOnly: true,
                                    hidden:!boolEsL3Mpls
                                },
                                {   width: '10%', border: false},
                                {   width: '10%', border: false},
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>VRF</b>',
                                    width: 300,
                                    value: objJson.vrf,
                                    readOnly: true,
                                    hidden:!boolEsL3Mpls
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>VLAN</b>',
                                    width: 300,
                                    value: objJson.vlan,
                                    readOnly: true,
                                    hidden:!boolEsL3Mpls
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Protocolo</b>',
                                    width: 300,
                                    value: objJson.protocolo,
                                    readOnly: true,
                                    hidden:!boolEsL3Mpls
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>As Privado</b>',
                                    width: 300,
                                    value: objJson.asPrivado,
                                    readOnly: true,
                                    hidden:!boolEsL3Mpls
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Ip Servicio</b>',
                                    width: 300,
                                    value: objJson.ip,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Subred</b>',
                                    width: 300,
                                    value: objJson.subred,
                                    readOnly: true
                                },
                                {   width: '10%', border: false}
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            id: 'datosTecnicosServicioNuevo',
                            title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;\n\
                                    <b>Datos Técnicos Servicio a ser <label style="color:green;">Activado</label></b>',
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
                                    fieldLabel: '<b>Concentrador</b>',
                                    width: 300,
                                    value: objJson.concentradorActual,
                                    readOnly: true,
                                    hidden:!boolEsL3Mpls
                                },
                                {   width: '10%', border: false},
                                {   width: '10%', border: false},
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>VRF</b>',
                                    width: 300,
                                    value: data.vrf,
                                    readOnly: true,
                                    hidden:(!boolEsL3Mpls && !boolEsConcentrador)
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>VLAN</b>',
                                    width: 300,
                                    value: data.vlan,
                                    readOnly: true,
                                    hidden:(!boolEsL3Mpls && !boolEsConcentrador)
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Protocolo</b>',
                                    width: 300,
                                    value: data.protocolo,
                                    readOnly: true,
                                    hidden:(!boolEsL3Mpls && !boolEsConcentrador)
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>As Privado</b>',
                                    width: 300,
                                    value: data.asPrivado,
                                    readOnly: true,
                                    hidden:(!boolEsL3Mpls && !boolEsConcentrador)
                                },
                                {   width: '10%', border: false},
                                //-------------------------------------
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Ip Servicio</b>',
                                    width: 300,
                                    value: data.ipServicio,
                                    readOnly: true
                                },
                                {   width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Subred</b>',
                                    width: 300,
                                    value: data.subredServicio,
                                    readOnly: true
                                },
                                {   width: '10%', border: false}
                            ]
                        }
                    ],
                buttons: [
                    {
                        text: '<i class="fa fa-plug" aria-hidden="true"></i>&nbsp;Activar Servicio',
                        handler: function()
                        {
                            Ext.get(winActivacionHeredada.getId()).mask('Activando Servicio...');
    
                            Ext.Ajax.request({
                                url: urlActivarServicioPorFactHeredada,
                                timeout:600000,
                                method: 'post',
                                params: 
                                { 
                                    idServicioAnterior      : data.servicioHeredadoFact,
                                    idServicioNuevo         : data.idServicio,
                                    //Datos Anteriores
                                    vlanAnterior            : objJson.vlan,
                                    vrfAnterior             : objJson.vrf,
                                    protocoloAnterior       : objJson.protocolo,
                                    asPrivadoAnterior       : objJson.asPrivado,
                                    ipAnterior              : objJson.ip,
                                    subredAnterior          : objJson.subred,
                                    loginAuxAnterior        : objJson.login,
                                    capacidadUnoAnterior    : objJson.capacidadUno,
                                    capacidadDosAnterior    : objJson.capacidadDos,
                                    cpeAnterior             : objJson.nombreCpe,
                                    interfazCpeAnterior     : objJson.interfaceCpe,
                                    //Nuevos Datos
                                    vlanNueva               : data.vlan,
                                    vrfNueva                : data.vrf,
                                    protocoloNueva          : data.protocolo,
                                    asPrivadoNueva          : data.asPrivado,
                                    ipNueva                 : data.ipServicio,
                                    subredNueva             : data.subredServicio,
                                    loginAuxNueva           : data.loginAux,
                                    capacidadUnoNueva       : data.capacidadUno,
                                    capacidadDosNueva       : data.capacidadDos,
                                    gwNueva                 : data.gwSubredServicio,
                                    mascaraSubredNueva      : data.mascaraSubredServicio,
                                    elementoPadre           : data.elementoPadre,
                                    rdId                    : data.rdId,
                                    defaultGw               : data.defaultGateway,
                                    macCpe                  : objJson.macCpe,
                                    anillo                  : data.anillo
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
                title: boolEsConcentrador?'Activación de Servicio Concentrador':'Activación de Servicio con Factibilidad Heredada',
                layout: 'fit',
                resizable: true,
                modal: true,
                closable: true,
                width: 'auto',
                items: [formActivacionHeredada]
            });

            winActivacionHeredada.show();
        }
     });
    
    
}
