function showRecursosRedSatelital(data)
{
    Ext.get(grid.getId()).mask('Consultando Datos...');

    Ext.Ajax.request({ 
        url: getDatosFactibilidad,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio   : data.get('id_servicio'),                
            tipoSolicitud: data.get('descripcionSolicitud'),
            idSolicitud  : data.get('id_factibilidad'),
            esPseudoPe   : data.get('esPseudoPe'),
            ultimaMilla  : data.get('ultimaMilla')
        },
        success: function(response){
            Ext.get(grid.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);

            if(json.status==="OK")
            {                                      
                var esNuevoVsat = json.esNuevoVsat;                                
                
                storeVrfsDisponibles = new Ext.data.Store({
                    total: 'total',
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "No tiene asignado Vrfs para el servicio",
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR,
                                    fn: function(btn){
                                        win.destroy();
                                    }
                                });
                            }
                        }
                    },
                    proxy: {
                        type: 'ajax',
                        timeout: 600000,
                        url: urlAjaxGetVrfsDisponibles,
                        extraParams: {
                            idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                            idServicio:           data.get('id_servicio')
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'data'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        }
                    },
                    fields:
                        [
                            {name: 'id_vrf' , mapping: 'id_vrf'},
                            {name: 'vrf', mapping: 'vrf'}
                        ]
                });                              

                storeProtocolosEnrutamiento = new Ext.data.Store({
                    proxy: {
                        type: 'ajax',
                        url: urlAjaxGetProtocolosEnrutamiento,
                        reader: {
                            type: 'json',
                            root: 'data'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        }
                    },
                    fields:
                        [
                            {name: 'descripcion', mapping: 'descripcion'}
                        ]
                });

                storeMascaras = new Ext.data.Store({
                    fields: ['display','value'],
                    data: [
                        {
                            "display": "/24",
                            "value": "255.255.255.0"
                        },
                        {
                            "display": "/25",
                            "value": "255.255.255.128"
                        },
                        {
                            "display": "/26",
                            "value": "255.255.255.192"
                        },
                        {
                            "display": "/27",
                            "value": "255.255.255.224"
                        },
                        {
                            "display": "/28",
                            "value": "255.255.255.240"
                        },
                        {
                            "display": "/29",
                            "value": "255.255.255.248"
                        }
                    ]
                });

                Ext.Ajax.request({
                    url: urlAjaxGetAsPrivado,
                    method: 'post',
                    params: { idPersonaEmpresaRol : data.get('id_persona_empresa_rol') },
                    success: function(response){
                        var asPrivado = response.responseText;

                        Ext.getCmp('asPrivado').setVisible(false);

                        if(asPrivado>0)
                        {
                            Ext.getCmp('asPrivado').setValue(asPrivado);
                            Ext.getCmp('asPrivado').setReadOnly(true);
                        }
                        else
                        {
                            Ext.getCmp('asPrivado').setReadOnly(false);
                        }
                    },
                    failure: function(response)
                    {
                        Ext.getCmp('asPrivado').setVisible(false);

                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: response.responseText,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        }); 
                    }
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
                        columns: 2
                    },
                    defaults: {
                        bodyStyle: 'padding:20px'
                    },
                    items: [
                        //informacion del servicio
                        {
                            colspan: 1,
                            rowspan: 2,
                            xtype: 'panel',
                            title: 'Información del Servicio',
                            defaults: { 
                                height: 170
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
                                            name: 'ciudad',
                                            fieldLabel: 'Ciudad',
                                            displayField: data.get('ciudad'),
                                            value: data.get('ciudad'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},
                                        //----------------------------------//
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.get('login2'),
                                            value: data.get('login2'),
                                            readOnly: true,
                                            width: '30%'
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

                                        { width: '10%', border: false},
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'direccion',
                                            fieldLabel: 'Direccion',
                                            displayField: data.get('direccion'),
                                            value: data.get('direccion'),
                                            readOnly: true,
                                            width: '30%'
                                        },

                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'sector',
                                            fieldLabel: 'Sector',
                                            displayField: data.get('nombreSector'),
                                            value: data.get('nombreSector'),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {   width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: data.get("producto"),
                                            value: data.get("producto"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoOrden',
                                            fieldLabel: 'Tipo Orden',
                                            displayField: data.get("tipo_orden"),
                                            value: data.get("tipo_orden"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad1',
                                            fieldLabel: 'Capacidad1',
                                            displayField: json.capacidad1,
                                            value: json.capacidad1,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: json.capacidad2,
                                            value: json.capacidad2,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoEnlace',
                                            fieldLabel: 'Tipo Enlace',
                                            displayField: data.get('tipo_enlace'),
                                            value: data.get('tipo_enlace'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        { width: '15%', border: false},
                                        { width: '10%', border: false}
                                    ]
                                }

                            ]
                        },//cierre de la informacion del cliente
                        //Información del concentrador
                        {
                            colspan: 1,
                            rowspan: 2,
                            title: 'Información del Concentrador Actual',
                            xtype: 'panel',
                            defaults: { 
                                height: 170
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
                                            name: 'idClienteConcentrador',
                                            fieldLabel: 'Cliente',
                                            value: json.concentrador.cliente,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'idLoginConcentrador',
                                            fieldLabel: 'Login',
                                            value: json.concentrador.login,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},
                                        //---------------------------------------------
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'idProductoConcentrador',
                                            fieldLabel: 'Producto',
                                            value: json.concentrador.producto,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'idLoginAuxConcentrador',
                                            fieldLabel: 'Login Aux',
                                            value: json.concentrador.login_aux,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},
                                        //---------------------------------------------
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'idCapacidadUnoConcentrador',
                                            fieldLabel: 'Capacidad Uno',
                                            value: json.concentrador.capacidadUno,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'idCapacidadDosConcentrador',
                                            fieldLabel: 'Capacidad Dos',
                                            value: json.concentrador.capacidadDos,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false}
                                    ]
                                }
                            ]                               
                        },// cierre de información del concentrador                        
                        //informacion de los elementos del cliente
                        {
                            colspan: 2,
                            xtype: 'panel',
                            title: 'Información de Backbone',
                            items: [
                                //grupo de radio botones
                                {
                                    xtype: 'radiogroup',
                                    fieldLabel: 'Recursos',
                                    columns: 1,
                                    items: [
                                        {
                                            boxLabel: 'Nuevos', 
                                            id: 'rbRecursosNuevos', 
                                            name: 'rbRecursos', 
                                            inputValue: "nuevos", 
                                            listeners: 
                                            {
                                                change: function (cb, nv, ov) 
                                                {
                                                    if (nv)
                                                    {
                                                        Ext.getCmp('containerRecursosObligatorios').setVisible(true);
                                                        Ext.getCmp('containerNuevosDatos').setVisible(true);
                                                        Ext.getCmp('containerMismosDatos').setVisible(false);
                                                       
                                                        Ext.getCmp('vrfsDisponibles').setDisabled(false);
                                                        Ext.getCmp('protocolosEnrutamiento').setDisabled(false);
                                                        Ext.getCmp('mascaras').setDisabled(false);
                                                        Ext.getCmp('asPrivado').setDisabled(false);
                                                        Ext.getCmp('subredBb').setDisabled(true);
                                                        Ext.getCmp('defaultGateway').setVisible(false);

                                                        win.center();
                                                    }
                                                }
                                            }
                                        },

                                        {
                                            boxLabel: 'Existentes', 
                                            id: 'rbRecursosExistentes', 
                                            name: 'rbRecursos', 
                                            inputValue: "existentes",
                                            listeners: 
                                            {
                                                change: function (cb, nv, ov) 
                                                {
                                                    if (nv)
                                                    {
                                                        Ext.getCmp('containerRecursosObligatorios').setVisible(true);
                                                        Ext.getCmp('containerNuevosDatos').setVisible(false);
                                                        Ext.getCmp('containerMismosDatos').setVisible(true);
                                                        
                                                        Ext.getCmp('vrfsDisponibles').setDisabled(true);
                                                        Ext.getCmp('protocolosEnrutamiento').setDisabled(true);
                                                        Ext.getCmp('mascaras').setDisabled(true);
                                                        Ext.getCmp('asPrivado').setDisabled(true);
                                                        Ext.getCmp('subredBb').setDisabled(false);
                                                        Ext.getCmp('defaultGateway').setVisible(false);

                                                        win.center();
                                                    }
                                                }
                                            }
                                        }
                                    ]//items
                                },                 


                                {
                                    id: 'containerRecursosObligatorios',
                                    xtype: 'fieldset',
                                    title: '<b>Datos Factibilidad</b>',
                                    defaultType: 'textfield',
                                    items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 6,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                xtype:          'textfield',
                                                id:             'nombreElementoEdificio',
                                                name:           'nombreElementoEdificio',
                                                fieldLabel:     '<b>Edificio/Nodo</b>',
                                                readOnly:       true,
                                                displayField:   json.nombreEdificio,
                                                value:          json.nombreEdificio,
                                                width:          500
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------
                                            {
                                                xtype:          'textfield',
                                                id:             'nombrePe',
                                                name:           'nombrePe',
                                                fieldLabel:     '<b>Nombre Pe</b>',
                                                readOnly:       true,
                                                displayField:   json.nombrePe,
                                                value:          json.nombrePe,
                                                width:          500
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'interfacePe',
                                                name:           'interfacePe',
                                                fieldLabel:     '<b>Interface Pe</b>',
                                                readOnly:       true,
                                                displayField:   json.interfacePe,
                                                value:          json.interfacePe,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {
                                                xtype:          'textfield',
                                                id:             'nombreHub',
                                                name:           'nombreHub',
                                                fieldLabel:     '<b>Hub Satelital</b>',
                                                readOnly:       true,
                                                displayField:   json.nombreSwHub,
                                                value:          json.nombreSwHub,
                                                width:          500
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'interfaceHub',
                                                name:           'interfaceHub',
                                                fieldLabel:     '<b>Puerto Vsat</b>',
                                                readOnly:       true,
                                                displayField:   json.interfaceSwHub,
                                                value:          json.interfaceSwHub,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   }
                                        ]
                                    }]
                                },                                    

                                {
                                    id: 'containerNuevosDatos',
                                    xtype: 'fieldset',
                                    title: '<b>Nuevos Recursos</b>',
                                    defaultType: 'textfield',
                                    items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 6,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                id:             'mascaras',
                                                name:           'mascaras',
                                                fieldLabel:     '<b>Subred (Pe-Hub)</b>',
                                                displayField:   'display',
                                                valueField:     'value',
                                                store:          storeMascaras,
                                                width:          150
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },                                            
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //------------------------------------------
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                id:             'vrfsDisponibles',
                                                name:           'vrfsDisponibles',
                                                fieldLabel:     '<b>Vrf</b>',
                                                displayField:   'vrf',
                                                valueField:     'id_vrf',
                                                store:          storeVrfsDisponibles,
                                                width:          270
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                queryMode:      'local',
                                                xtype:          'textfield',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                id:             'vlansDisponibles',
                                                name:           'vlansDisponibles',
                                                fieldLabel:     '<b>Vlan</b>',
                                                displayField:   'vlan',
                                                valueField:     'id',
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                id:             'protocolosEnrutamiento',
                                                name:           'protocolosEnrutamiento',
                                                fieldLabel:     '<b>Protocolo</b>',
                                                displayField:   'descripcion',
                                                valueField:     'descripcion',
                                                store:          storeProtocolosEnrutamiento,
                                                width:          180,
                                                listeners: {
                                                    select: function(combo){
                                                        if(combo.getValue()!=="STANDARD")
                                                        {
                                                            Ext.getCmp('asPrivado').setVisible(true);
                                                            Ext.getCmp('asPrivado').setDisabled(false);
                                                        }
                                                        else
                                                        {    
                                                            Ext.getCmp('asPrivado').setVisible(false);
                                                            Ext.getCmp('asPrivado').setDisabled(true);
                                                        }    
                                                        if(combo.getValue()==="BGP"){
                                                            Ext.getCmp('defaultGateway').setVisible(true);
                                                        }else
                                                            Ext.getCmp('defaultGateway').setVisible(false);
                                                    }
                                                }
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //-----------------------------------------------
                                            {
                                                xtype:          'numberfield',
                                                id:             'asPrivado',
                                                allowBlank:      false,
                                                name:           'asPrivado',
                                                fieldLabel:     '<b>As Privado</b>',
                                                visible :        false,
                                                allowDecimals:   false,
                                                allowNegative:   false,
                                                maxLength:       8,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype: 'checkboxfield',
                                                id: 'defaultGateway',
                                                name: 'defaultGateway',
                                                visible :        false,
                                                boxLabel: '<b>Neighbor Default Gateway</b>'
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   border: false   }
                                        ]//items fielset
                                    }]
                                },//containerNuevosDatos
                                //container Mismos recursos
                                {
                                    id: 'containerMismosDatos',
                                    xtype: 'fieldset',
                                    title: '<b>Recursos Configurados</b>',
                                    defaultType: 'textfield',
                                    items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 6,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                xtype:          'textfield',
                                                id:             'subredBb',
                                                name:           'subredBb',
                                                fieldLabel:     '<b>Subred (Pe-Hub)</b>',
                                                value:          json.subredBbVsat,
                                                readOnly:       true,
                                                width:          270,
                                                fieldStyle:    'color: green;font-weight: bold;'
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //----------------------------------------
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVrf',
                                                name:           'mismaVrf',
                                                fieldLabel:     '<b>Vrf</b>',
                                                value:          json.vrfVsat,
                                                readOnly:       true,
                                                width:          270
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVlan',
                                                name:           'mismaVlan',
                                                fieldLabel:     '<b>Vlan</b>',
                                                value:          json.vlanVsat,
                                                readOnly:       true,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {
                                                xtype:          'textfield',
                                                id:             'mismoProtocolo',
                                                name:           'mismoProtocolo',
                                                fieldLabel:     '<b>Protocolo</b>',
                                                value:          json.protocoloVsat,
                                                readOnly:       true,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'mismoAsPrivado',
                                                name:           'mismoAsPrivado',
                                                fieldLabel:     '<b>As Privado</b>',
                                                value:          json.asPrivadoVsat,
                                                readOnly:       true,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   }
                                        ]
                                    }]
                                }//containerMismosDatos
                                ]
                        }//cierre informacion de los elementos del cliente
                    ],
                    buttons: 
                    [{
                        text: 'Guardar',
                        formBind: esNuevoVsat === 'S'?true:false,
                        handler: function()
                        {
                            if(esNuevoVsat === 'S')
                            {
                                var vlan        = parseInt(Ext.getCmp('vlansDisponibles').getValue());

                                var iniVlan = parseInt(json.vlanMin);
                                var finVlan = parseInt(json.vlanMax);

                                if(vlan < iniVlan || vlan > finVlan )
                                {
                                    Ext.Msg.alert('Alerta','El valor ingresado no cumple con el Rango establecido de Vlans para Enlaces Satelitales');
                                    boolContinua = false;
                                }
                                else
                                {
                                    boolContinua = true;
                                }
                            }
                            else
                            {
                                boolContinua = true;
                            }
                                                        
                            if(boolContinua)
                            {
                                Ext.Msg.confirm('Mensaje','Esta seguro de asignar los recursos de red ?', function(btn){
                                    if(btn==='yes')
                                    {
                                        var vrf            = Ext.getCmp('vrfsDisponibles').getValue();
                                        var protocolo      = Ext.getCmp('protocolosEnrutamiento').getValue();
                                        var asPrivado      = Ext.getCmp('asPrivado').getValue();
                                        var mascara        = Ext.getCmp('mascaras').getValue();
                                        var defaultGateway = Ext.getCmp('defaultGateway').getValue();
                                        var flagRecursos   = Ext.ComponentQuery.query('[name=rbRecursos]')[0].getGroupValue(); 
                                        var subred         = '';

                                        if(flagRecursos==="existentes")
                                        {
                                            vlan         = json.vlanVsat;
                                            vrf          = json.idVrfVsat;    
                                            protocolo    = json.protocoloVsat;
                                            subred       = json.idSubredBbVsat;
                                            asPrivado    = json.asPrivadoVsat;
                                        }
                                        
                                        Ext.get(formPanel.getId()).mask('Guardando datos...');

                                        Ext.Ajax.request({
                                            url: urlAjaxAsignarRecursosL3mpls,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: { 
                                                idPersonaEmpresaRol:    data.get('id_persona_empresa_rol') ,
                                                idServicio:             data.get('id_servicio'),
                                                idDetalleSolicitud:     data.get('id_factibilidad'),
                                                idElementoPadre:        json.idElemento,                                            
                                                vlan:                   vlan,
                                                vrf:                    vrf,
                                                protocolo:              protocolo,
                                                defaultGateway:         defaultGateway,
                                                asPrivado:              asPrivado,
                                                mascara:                mascara,
                                                mascaraCliente:         '255.255.255.252',
                                                idSubred:               subred,
                                                flagRecursos:           flagRecursos,
                                                ultimaMilla:            data.get('ultimaMilla'),
                                                esPseudoPe:             'S'
                                            },
                                            success: function(response){
                                                Ext.get(formPanel.getId()).unmask();

                                                var jsonGuardar = Ext.JSON.decode(response.responseText);

                                                if(jsonGuardar.status === "OK")
                                                {
                                                    Ext.Msg.alert('Mensaje','Se Asignaron los Recursos de Red', function(btn){
                                                        if(btn==='ok')
                                                        {
                                                            win.destroy();
                                                            store.load();
                                                        }
                                                    });
                                                }
                                                else{
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: jsonGuardar.mensaje,
                                                        buttons: Ext.MessageBox.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: result.statusText,
                                                    buttons: Ext.MessageBox.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });

                                            }
                                        });
                                    }//if(btn==='yes')                            
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
                
                if(esNuevoVsat === 'S')
                {
                    Ext.getCmp('rbRecursosExistentes').setDisabled(true);
                    Ext.getCmp('rbRecursosNuevos').checked = true;
                }
                else
                {
                    Ext.getCmp('rbRecursosNuevos').setDisabled(true);
                    Ext.getCmp('rbRecursosExistentes').checked = true;
                }

                Ext.getCmp('containerRecursosObligatorios').setVisible(false);
                Ext.getCmp('containerNuevosDatos').setVisible(false);
                Ext.getCmp('containerMismosDatos').setVisible(false);
                               
                var win = Ext.create('Ext.window.Window', {
                    title: 'Asignar Recurso de Red - L3MPLS ( SATELITAL )',
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

                Ext.get(formPanel.getId()).mask('Cargando datos...');

                storeVrfsDisponibles.load({
                    callback: function() {
                        storeProtocolosEnrutamiento.load({ 
                            callback: function() {
                                Ext.get(formPanel.getId()).unmask()
                            }
                        });
                    }
                });
            }
            else
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: json.msg,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });   
            }    
        },//cierre response
        failure: function(result) {
            Ext.MessageBox.show({
                title: 'Error',
                msg: result.responseText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}
