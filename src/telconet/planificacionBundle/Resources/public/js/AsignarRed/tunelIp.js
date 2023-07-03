/**
 * Funcion que sirve para mostrar la pantalla para la asignacion 
 * de recursos de red para Tunel Ip
 * 
 * @author Franciso Adum <fadum@telconet.ec>
 * @version 1.0 15-12-2015
 * */
function showRecursoRedTunelIp(data)
{
    Ext.get(grid.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({ 
            url: getDatosFactibilidad,
            method: 'post',
            timeout: 400000,
            params: { 
                idServicio: data.get('id_servicio')
            },
            success: function(response){
                Ext.get(grid.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                
                //-------------------------------------------------------------------------------------------
                
                var storeHilosDisponibles = new Ext.data.Store({  
                    pageSize: 100,
                    proxy: {
                        type: 'ajax',
                        url : getHilosDisponibles,
                        extraParams: {
                            idElemento:                 json[0].idElementoConector,
                            estadoInterface:            'connected',
                            estadoInterfaceNotConect:   'not connect',
                            estadoInterfaceReserved:    'reserved'
                        },
                        reader: {
                            type: 'json',
                            root: 'encontrados'
                        }
                    },
                    fields:
                        [
                          {name:'idInterfaceElemento', mapping:'idInterfaceElemento'},
                          {name:'colorHilo', mapping:'colorHilo'}
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
                        // The total column count must be specified here
                        columns: 3
                    },
                    defaults: {
                        // applied to each contained panel
                        bodyStyle: 'padding:20px'
                    },
                    items: [
                        
                        //informacion del servicio
                        {
                            colspan: 2,
                            rowspan:2,
                            xtype: 'panel',
                            title: 'Informacion del Servicio',
                            defaults: { 
                                height: 100
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
                                        {   width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoOrden',
                                            fieldLabel: 'Tipo Orden',
                                            displayField: data.get("tipo_orden"),
                                            value: data.get("tipo_orden"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: data.get("producto"),
                                            value: data.get("producto"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad1',
                                            fieldLabel: 'Capacidad1',
                                            displayField: json[0].capacidad1,
                                            value: json[0].capacidad1,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: json[0].capacidad2,
                                            value: json[0].capacidad2,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------
                                        
                                    ]
                                }

                            ]
                        },//cierre de informacion del servicio
                        
                        //informacion del cliente
                        {
                            colspan: 2,
                            rowspan:2,
                            xtype: 'panel',
                            title: 'Informacion del Cliente',
                            defaults: { 
                                height: 100
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

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'cliente',
                                            fieldLabel: 'Cliente',
                                            displayField: data.get('cliente'),
                                            value: data.get('cliente'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.get('login2'),
                                            value: data.get('login2'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'ciudad',
                                            fieldLabel: 'Ciudad',
                                            displayField: data.get('ciudad'),
                                            value: data.get('ciudad'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'direccion',
                                            fieldLabel: 'Direccion',
                                            displayField: data.get('direccion'),
                                            value: data.get('direccion'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'sector',
                                            fieldLabel: 'Sector',
                                            displayField: data.get('nombreSector'),
                                            value: data.get('nombreSector'),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'esRecontratacion',
                                            fieldLabel: 'Es Recontratacion',
                                            displayField: data.get("esRecontratacion"),
                                            value: data.get("esRecontratacion"),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false}
                                        
                                        //---------------------------------------------

                                    ]
                                }

                            ]
                        },//cierre de la informacion del cliente
                                                
                        //informacion de los elementos del cliente
                        {
                            colspan: 3,
                            xtype: 'panel',
                            title: 'Elementos dados por Factibilidad',
                            items: [
                                                                
                                //informacion del elemento backbone y distribucion
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 6,
                                        align: 'stretch'
                                    },
                                    items: [
                                        {   width: '20%', border: false},
                                        {
                                            xtype:          'textfield',
                                            id:             'nombreElemento',
                                            name:           'nombreElemento',
                                            fieldLabel:     'Nombre Elemento',
                                            displayField:   json[0].nombreElemento,
                                            value:          json[0].nombreElemento,
                                            width:          '25%'
                                        },
                                        { width: '20%', border: false},
                                        {
                                            xtype:          'textfield',
                                            id:             'nombreElementoConector',
                                            name:           'nombreElementoConector',
                                            fieldLabel:     'Nombre Elemento Conector',
                                            displayField:   json[0].nombreElementoConector,
                                            value:          json[0].nombreElementoConector,
                                            readOnly:       true,
                                            width:          '25%'
                                        },
                                        {
                                            xtype:          'hidden',
                                            id:             'validacionVlan',
                                            name:           'validacionVlan',
                                            value:          "",
                                            width:          '20%'
                                        },
                                        {
                                            xtype:          'textfield',
                                            id:             'vlan',
                                            name:           'vlan',
                                            fieldLabel:     'Vlan',
                                            displayField:   "1",
                                            value:          "1",
                                            width:          '25%',
                                            listeners: {
                                                blur: function(text){
                                                    var vlan = text.getValue();
                                                    if(vlan.match("^([0-9]{1,5})$"))
                                                    {
                                                        Ext.getCmp('validacionVlan').setValue = "correcta";
                                                        Ext.getCmp('validacionVlan').setRawValue("correcta");
                                                    }
                                                    else
                                                    {
                                                        Ext.getCmp('validacionVlan').setValue = "incorrecta";
                                                        Ext.getCmp('validacionVlan').setRawValue("incorrecta");
                                                        
                                                        Ext.Msg.alert('Mensaje ','Formato de Vlan Incorrecto, Favor Revisar!' );
                                                    }
                                                }
                                            }
                                        },

                                        //---------------------------------------

                                        { width: '20%', border: false},
                                        {
                                            xtype:          'textfield',
                                            id:             'nombreInterfaceElemento',
                                            name:           'nombreInterfaceElemento',
                                            fieldLabel:     'Nombre Interface Elemento',
                                            displayField:   json[0].nombreInterfaceElemento,
                                            value:          json[0].nombreInterfaceElemento,
                                            width:          '25%'
                                        },
                                        { width: '20%', border: false},
                                        {
                                            queryMode:      'local',
                                            xtype:          'combobox',
                                            id:             'hilosDisponibles',
                                            name:           'hilosDisponibles',
                                            fieldLabel:     'Hilos Disponibles',
                                            displayField:   'colorHilo',
                                            valueField:     'idInterfaceElemento',
                                            value:          json[0].colorHilo,
                                            loadingText:    'Buscando...',
                                            store:          storeHilosDisponibles,
                                            width: '25%'
                                        },
                                        {   width: '20%', border: false},
                                        {   width: '20%', border: false},

                                        //---------------------------------------

                                        {   width: '20%', border: false},
                                        {
                                            xtype:          'textfield',
                                            id:             'ipTunel',
                                            name:           'ipTunel',
                                            fieldLabel:     'Ip Publica',
                                            displayField:   "",
                                            value:          "",
                                            width:          '25%',
                                            listeners: {
                                                blur: function(text){
                                                    var ip = text.getValue();
                                                    if(ip.match("^([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})$"))
                                                    {
                                                        Ext.getCmp('validacionIpTunel').setValue = "correcta";
                                                        Ext.getCmp('validacionIpTunel').setRawValue("correcta");
                                                    }
                                                    else
                                                    {
                                                        Ext.getCmp('validacionIpTunel').setValue = "incorrecta";
                                                        Ext.getCmp('validacionIpTunel').setRawValue("incorrecta");
                                                        Ext.Msg.alert('Mensaje ','Formato de Ip Incorrecto, Favor Revisar!' );
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            width:          '20%',
                                            border:         false
                                        },
                                        {
                                            xtype:          'textfield',
                                            id:             'mascaraTunel',
                                            name:           'mascaraTunel',
                                            fieldLabel:     'Mascara',
                                            displayField:   "",
                                            value:          "",
                                            width:          '25%',
                                            listeners: {
                                                blur: function(text){
                                                    var mac = text.getValue();
                                                    if(mac.match("^([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})$"))
                                                    {
                                                        Ext.getCmp('validacionMascaraTunel').setValue = "correcta";
                                                        Ext.getCmp('validacionMascaraTunel').setRawValue("correcta");
                                                    }
                                                    else
                                                    {
                                                        Ext.getCmp('validacionMascaraTunel').setValue = "incorrecta";
                                                        Ext.getCmp('validacionMascaraTunel').setRawValue("incorrecta");
                                                        
                                                        Ext.Msg.alert('Mensaje ','Formato de Mascara Incorrecto, Favor Revisar!' );
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            xtype:          'hidden',
                                            id:             'validacionIpTunel',
                                            name:           'validacionIpTunel',
                                            value:          "",
                                            width:          '20%'
                                        },
                                        {
                                            xtype:          'hidden',
                                            id:             'validacionMascaraTunel',
                                            name:           'validacionMascaraTunel',
                                            value:          "",
                                            width:          '20%'
                                        }
                                        //---------------------------------------

                                    ]//items container
                                }//items panel
                                
                            ]

                        },//cierre informacion de los elementos del cliente
                    ],
                    buttons: 
                    [{
                        text: 'Grabar',
                        formBind: true,
                        handler: function(){
                            var validacionIpTunel   = Ext.getCmp('validacionIpTunel').getValue();
                            var validacionVlan      = Ext.getCmp('validacionVlan').getValue();
                            var validacionMascara   = Ext.getCmp('validacionMascaraTunel').getValue();
                            var hilosDisponibles    = Ext.getCmp('hilosDisponibles').getValue();
                            var vlan                = Ext.getCmp('vlan').getValue();
                            var ipTunel             = Ext.getCmp('ipTunel').getValue();
                            var mascaraTunel        = Ext.getCmp('mascaraTunel').getValue();
                            
                            if(hilosDisponibles === "" || validacionIpTunel === "incorrecta" || 
                               validacionMascara === "incorrecta" || validacionVlan === "incorrecta" )
                            {
                                validacion = false;
                            }
                            else
                            {
                                validacion = true;
                            }

                            if(validacion)
                            {
                                Ext.get(formPanel.getId()).mask('Guardando datos!');

                                Ext.Ajax.request({
                                    url: asignarRecursosTunel,
                                    method: 'post',
                                    timeout: 1000000,
                                    params: { 
                                        idServicio:             data.get('id_servicio'),
                                        idDetalleSolicitud:     data.get('id_factibilidad'),
                                        hiloDisponible:         hilosDisponibles,
                                        vlan:                   vlan,
                                        ipTunel:                ipTunel,
                                        mascaraTunel:           mascaraTunel
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK")
                                        {
                                            Ext.Msg.alert('Mensaje','Se Asignaron los Recursos de Red!', function(btn){
                                                if(btn=='ok')
                                                {
                                                    win.destroy();
                                                    store.load();
                                                }
                                            });
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje ',response.responseText );
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                });

                            }
                            else
                            {
                                Ext.Msg.alert("Validacion","Favor Revise los campos", function(btn){
                                    if(btn=='ok'){
                                    }
                                });
                            }
                        }//handler
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Asignar Recurso de Red - Tunel Ip',
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

                storeHilosDisponibles.load({
                    callback:function(){        
                        
                    }
                });
            }//cierre response
        });   
}