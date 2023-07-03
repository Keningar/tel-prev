function cambiarSubredes(data)
{
    var tieneSolicitudCambioPdte = data.tieneSolCambioIp === 'SI'?true:false;
    var gateway;
    var storeSubredDisponibles = new Ext.data.Store({
        pageSize: 100,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: getSubredDisponible,
            reader: {
                type: 'json',
                root: 'encontrados'
            },
            extraParams:
            {
                tipo       : data.tipoSubred === 'WAN'?'LAN':'WAN',//Si viene tipo red Wan pide cambio a LAN y visceversa
                idElemento : data.idElementoPadre,
                anillo     : data.anillo,
                uso        : 'INTMPLS'
            }
        },
        fields:
            [
                {name: 'idSubred', mapping: 'idSubred'},
                {name: 'subred',   mapping: 'subred'},
                {name: 'gateway',   mapping: 'gateway'}
            ]
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
        items: [
            {
                xtype: 'container',
                layout: {
                    type: 'table',
                    columns: 3,
                    align: 'stretch'
                },
                items: [                   
                    
                    //---------------- opciones -------------------
                    {width: '10%', border: false},
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 3,
                            align: 'stretch'
                        },
                        items: [
                            {width: '10%', border: false},
                            {
                                xtype: 'fieldset',
                                title: tieneSolicitudCambioPdte?"<b>Información de Subred Generada</b>":"<b>Información de Subred Existente</b>",
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 3,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: '<b>Pe</b>',
                                                displayField: data.elementoPadre,
                                                value: data.elementoPadre,
                                                readOnly: true,
                                                width: '100%'
                                            },
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: '<b>Tipo Subred</b>',
                                                displayField: data.tipoSubred==='WAN'?'Pública':'Privada',
                                                value: data.tipoSubred==='WAN'?'Pública':'Privada',
                                                readOnly: true,
                                                fieldStyle:'font-weight:bold',
                                                width: '100%'
                                            },
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: '<b>Subred</b>',
                                                displayField: data.subredServicio,
                                                value: data.subredServicio,
                                                readOnly: true,
                                                width: '100%'
                                            },
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: '<b>Ip Servicio</b>',
                                                displayField: data.ipServicio,
                                                value: data.ipServicio,
                                                readOnly: true,
                                                width: '100%'
                                            },
                                            {width: '10%', border: false}
                                        ]
                                    }
                                ]
                            },
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {
                                xtype: 'fieldset',
                                hidden: tieneSolicitudCambioPdte,
                                title: "<b>Asignación de Subred "+(data.tipoSubred === 'WAN'?'Privada':'Pública')+'</b>',
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 3,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {width: '10%', border: false},
                                            {
                                                id: 'cbmSubred',
                                                name: 'cbmSubred',
                                                xtype: 'combobox',
                                                fieldLabel: '<b>Subred</b>',
                                                store: storeSubredDisponibles,
                                                queryMode: 'local',
                                                displayField: 'subred',
                                                valueField: 'idSubred',
                                                width: '20%',
                                                editable: false,
                                                listeners:{
                                                    select:function(combo)
                                                    {
                                                        var objeto = combo.valueModels[0].raw;
                                                        gateway = objeto.gateway;
                                                    }
                                                }
                                            },
                                            {width: '10%', border: false}
                                        ]
                                    }
                                ]
                            },
                            {width: '10%', border: false}
                        ]
                    }
                    
                ]
            }
        ],
        buttons: [
            {
                text: tieneSolicitudCambioPdte?'Cambio Subred':'Generar IP',
                iconCls: tieneSolicitudCambioPdte?'icon_cambiarSubred':'icon_generacionIpPorSubred',
                handler: function() 
                {
                    //Validar que se haya escogido la nueva subred a ser asignada
                    var subred = Ext.getCmp('cbmSubred').value;
                    
                    if(Ext.isEmpty(subred) && !tieneSolicitudCambioPdte)
                    {
                        Ext.Msg.alert('Alerta', 'Debe escoger la nueva Subred a ser asignada');
                        return;
                    }
                    
                    Ext.get(windowCambiarSubred.getId()).mask(tieneSolicitudCambioPdte?'Realizando cambio de Subred...':'Generando nueva IP');
                    Ext.Ajax.request({
                        url    : urlCambiarSubredesIntMpls,
                        method : 'post',
                        timeout: 400000,
                        params : {
                            idServicio  :   data.idServicio,
                            subred      :   subred,
                            tipoSubred  :   (data.tipoSubred === 'WAN'?'LAN':'WAN'),
                            vrf         :   data.vrf,
                            protocolo   :   data.protocolo,
                            ipServicio  :   data.ipServicio,
                            vlan        :   data.vlan,
                            gateway     :   data.gwSubredServicio,                            
                            asPrivado   :   data.asPrivado,
                            tipoEnlace  :   data.tipoEnlace,
                            loginAux    :   data.loginAux,
                            elemento    :   data.elementoNombre,
                            pe          :   data.elementoPadre,
                            solCambioIp :   data.tieneSolCambioIp
                        },
                        success: function(response) 
                        {
                            Ext.get(windowCambiarSubred.getId()).unmask();

                            var json = Ext.JSON.decode(response.responseText);
                            
                            Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                if (btn === 'ok') {
                                    store.load();
                                    windowCambiarSubred.destroy();
                                }
                            });
                        },
                        failure: function(result)
                        {
                            Ext.get(windowCambiarSubred.getId()).unmask();
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            },
            {
                text: 'Cerrar',
                iconCls: 'icon_cerrar',
                handler: function() {                    
                    windowCambiarSubred.close();
                }
            }]
    });
    
    windowCambiarSubred = Ext.create('Ext.window.Window', {
        title: tieneSolicitudCambioPdte?'<b>Ejecución de Cambio de Subred</b>':'<b>Generación de nueva IP</b>',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}