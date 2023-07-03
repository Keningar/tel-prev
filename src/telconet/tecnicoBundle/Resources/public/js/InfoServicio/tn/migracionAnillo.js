function cambioMigracionAnillo(data,migracionVlan)
{       
    var tituloPantalla = "Migración a Anillo";
    if(migracionVlan === "S")
    {
        tituloPantalla = "Migración a Vlan reservadas";
    }
    
    Ext.get(document.body).mask('Obteniendo Informacion...');
    Ext.Ajax.request({
        url:        urlGetDatosBackboneL3mpls,
        method:     'post',
        timeout:    400000,
        params: 
        {
            idServicio:         data.idServicio,
            tipoElementoPadre : 'ROUTER'
        },
        success: function(response) 
        {
            Ext.get(document.body).unmask();
            var datosBackbone = Ext.JSON.decode(response.responseText);
            
            if(datosBackbone.idElementoPadre == "")
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: datosBackbone.elementoPadre,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
            else
            {
                var storeVrfsDisponibles = new Ext.data.Store({
                    total: 'total',
                    proxy: {
                        type: 'ajax',
                        timeout: 600000,
                        url: urlAjaxGetVrfsDisponibles,
                        extraParams: {
                                idPersonaEmpresaRol : data.idPersonaEmpresaRol,
                                idServicio          : data.idServicio,
                                migracionVlan       : migracionVlan
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

                var storeVlansDisponibles = new Ext.data.Store({
                    total: 'total',
                    proxy: {
                        type: 'ajax',
                        url: urlAjaxGetVlansDisponibles,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'data'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: {
                            nombreElemento      : datosBackbone.elementoPadre,
                            idPersonaEmpresaRol : data.idPersonaEmpresaRol,
                            anillo              : datosBackbone.anillo
                        }
                    },
                    fields:
                        [
                            {name: 'id'  , mapping: 'id'},
                            {name: 'vlan', mapping: 'vlan'}
                        ]
                });
                
                var storeVlansDisponiblesPorVrf = new Ext.data.Store({
                        total: 'total',
                        listeners: {
                            load: function(store,records,options) {
                                if(store.totalCount===0)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + datosBackbone.elementoPadre + 
                                             " ] del switch [ "+ datosBackbone.elemento +"]",
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
                            url: urlAjaxGetVlansDisponiblesPorVrf,
                            timeout: 3000000,
                            reader: {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'data'
                            },
                            actionMethods: {
                                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                            },
                            extraParams: {
                                nombreElemento:       datosBackbone.elementoPadre,
                                idPersonaEmpresaRol : data.idPersonaEmpresaRol,
                                anillo:               datosBackbone.anillo,
                                idServicio:           data.idServicio,
                                strMigracionManual:   migracionVlan
                            }
                        },
                        fields:
                            [
                                {name: 'id'  , mapping: 'id'},
                                {name: 'vlan', mapping: 'vlan'}
                            ]
                    });
                    

                var storeSubredesL3mplsDisponibles = new Ext.data.Store({
                    total: 'total',
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.getCmp('rbRecursosExistentes').setDisabled(true);
                                Ext.getCmp('rbRecursosNuevos').checked = true;
                            }
                        }
                    },
                    proxy: {
                        type: 'ajax',
                        url: urlAjaxGetSubredesL3mplsDisponibles,
                        timeout: 600000,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'data'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: {
                            nombreElemento      : datosBackbone.elementoPadre,
                            idPersonaEmpresaRol : data.idPersonaEmpresaRol,
                            nombreTecnico       : 'L3MPLS',
                            tipoEnlace          : data.tipoEnlace,
                            anillo              : datosBackbone.anillo,
                            idServicio          : data.idServicio
                        }
                    },
                    fields:
                        [
                            {name: 'id'  , mapping: 'id'},
                            {name: 'subred', mapping: 'subred'},
                            {name: 'idServicio', mapping: 'idServicio'}
                        ]
                });

                var storeProtocolosEnrutamiento = new Ext.data.Store({
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

                var storeMascaras = new Ext.data.Store({
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
                        },
                        {
                            "display": "/30",
                            "value": "255.255.255.252"
                        }
                    ]
                });

                //llamada ajax para obtener asPrivado
                Ext.Ajax.request
                ({
                    url: urlAjaxGetAsPrivado,
                    method: 'post',
                    params: { idPersonaEmpresaRol : data.idPersonaEmpresaRol },
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
                        // The total column count must be specified here
                        columns: 1
                    },
                    items: [
                        //um actual
                        {
                            colspan: 2,
                            rowspan: 2,
                            xtype: 'fieldset',
                            title: 'Ultima Milla Actual',
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
                                    items:
                                        [
                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'Ultima Milla',
                                                fieldLabel: 'Ultima Milla',
                                                displayField: data.ultimaMilla,
                                                value: data.ultimaMilla,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                                                       
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'VLAN',
                                                fieldLabel: 'VLAN',
                                                displayField: datosBackbone.vlan,
                                                value: datosBackbone.vlan,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                                                           
                                            },
                                            {   width: '10%', border: false},                                                                               

                                            //-----------------------------------

                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'IP Publica',
                                                fieldLabel: 'IP Publica',
                                                displayField: datosBackbone.ip,
                                                value: datosBackbone.ip,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                                                       
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'MAC',
                                                fieldLabel: 'MAC',
                                                displayField: datosBackbone.mac,
                                                value: datosBackbone.mac,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                                                           
                                            },
                                            {   width: '10%', border: false},

                                            //------------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'Ancho Banda Subida',
                                                fieldLabel: 'Ancho Banda Subida',
                                                displayField: data.capacidadUno,
                                                value: data.capacidadUno,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                                                         
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'Ancho Banda Bajada',
                                                fieldLabel: 'Ancho Banda Bajada',
                                                displayField: data.capacidadDos,
                                                value: data.capacidadDos,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                         
                                            },
                                            { width: '10%', border: false},

                                            //---------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'Switch',
                                                fieldLabel: 'Switch',
                                                displayField: datosBackbone.elemento,
                                                value: datosBackbone.elemento,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                                                         
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'Puerto',
                                                fieldLabel: 'Puerto',
                                                displayField: datosBackbone.interfaceElemento,
                                                value: datosBackbone.interfaceElemento,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                         
                                            },
                                            { width: '10%', border: false},

                                            //------------------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'PE',
                                                fieldLabel: 'PE',
                                                displayField: datosBackbone.elementoPadre,
                                                value: datosBackbone.elementoPadre,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                                                         
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'Vrf',
                                                fieldLabel: 'Vrf',
                                                displayField: datosBackbone.vrf,
                                                value: datosBackbone.vrf,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                         
                                            },
                                            { width: '10%', border: false},

                                            //---------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'Anillo',
                                                fieldLabel: 'Anillo',
                                                displayField: datosBackbone.anillo,
                                                value: datosBackbone.anillo,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '40%'                                                                                         
                                            },
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'tipoEnlace',
                                                fieldLabel: 'Tipo Enlace',
                                                displayField: data.tipoEnlace,
                                                value: data.tipoEnlace,
                                                readOnly: true,
                                                labelStyle: 'font-weight:bold',
                                                width: '50%'
                                            },
                                            {width: '15%', border: false}
                                        ]
                                }                            
                            ]
                        },

                        //Seleccion de nuevo switch para cambio de ultima milla
                        {
                            colspan: 2,
                            rowspan: 2,
                            xtype: 'fieldset',
                            title: 'Datos Nuevos',
                            defaults: {
                                height: 130
                            },
                            items: 
                            [
                                {
                                    id: 'opcionesRecursos',
                                    xtype: 'fieldset',
                                    title: 'Opciones',
                                    defaultType: 'textfield',
                                    height: 90,
                                    items: 
                                    [
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
                                                                Ext.getCmp('containerNuevosDatos').setVisible(true);
                                                                Ext.getCmp('containerMismosDatos').setVisible(false);

                                                                Ext.getCmp('vlansDisponibles').setDisabled(true);
                                                                Ext.getCmp('vrfsDisponibles').setDisabled(false);
                                                                Ext.getCmp('protocolosEnrutamiento').setDisabled(false);
                                                                Ext.getCmp('mascaras').setDisabled(false);
                                                                Ext.getCmp('asPrivado').setDisabled(false);
                                                                Ext.getCmp('subredesDisponibles').setDisabled(true);
                                                                Ext.getCmp('defaultGateway').setVisible(false);

                                                                win.center();
                                                            }
                                                        }
                                                    }
                                                },
                                                //--------------------------------------
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
                                                                Ext.getCmp('containerNuevosDatos').setVisible(false);
                                                                Ext.getCmp('containerMismosDatos').setVisible(true);

                                                                Ext.getCmp('vlansDisponibles').setDisabled(true);
                                                                Ext.getCmp('vrfsDisponibles').setDisabled(true);
                                                                Ext.getCmp('protocolosEnrutamiento').setDisabled(true);
                                                                Ext.getCmp('mascaras').setDisabled(true);
                                                                Ext.getCmp('asPrivado').setDisabled(true);
                                                                Ext.getCmp('subredesDisponibles').setDisabled(false);
                                                                Ext.getCmp('defaultGateway').setVisible(false);

                                                                win.center();
                                                            }
                                                        }
                                                    }
                                                }
                                            ]//items
                                        }
                                    ]
                                },
                                
                                //container nuevos recursos
                                {
                                    id: 'containerNuevosDatos',
                                    xtype: 'fieldset',
                                    title: 'Nuevos Recursos',
                                    defaultType: 'textfield',
                                    items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 5,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {   width: 25,border: false   },
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                id:             'vrfsDisponibles',
                                                name:           'vrfsDisponibles',
                                                fieldLabel:     'Vrf',
                                                displayField:   'vrf',
                                                valueField:     'id_vrf',
                                                store:          storeVrfsDisponibles,
                                                listeners: {
                                                        select: function(combo){
                                                            Ext.getCmp('vlansDisponibles').reset();
                                                            Ext.getCmp('vlansDisponibles').setDisabled(true);
                                                            Ext.getCmp('vlansDisponibles').value = "loading..";
                                                            Ext.getCmp('vlansDisponibles').setRawValue("loading..");
                                                            storeVlansDisponiblesPorVrf.proxy.extraParams = {
                                                                idPersonaEmpresaRol: data.idPersonaEmpresaRol,
                                                                idServicio: data.idServicio,
                                                                anillo: datosBackbone.anillo,
                                                                idVrf: combo.getValue(),
                                                                nombreElemento: datosBackbone.elementoPadre,
                                                                strMigracionManual:   migracionVlan
                                                            };
                                                            
                                                            storeVlansDisponiblesPorVrf.load({callback: function () {
                                                                                              Ext.getCmp('vlansDisponibles').setDisabled(false);
                                                                                            }});
                                                        }
                                                    },
                                                width:          270
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                id:             'vlansDisponibles',
                                                name:           'vlansDisponibles',
                                                fieldLabel:     'Vlan',
                                                displayField:   'vlan',
                                                valueField:     'id',
                                                store:          storeVlansDisponiblesPorVrf,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {   width: 25,border: false   },
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                id:             'protocolosEnrutamiento',
                                                name:           'protocolosEnrutamiento',
                                                fieldLabel:     'Protocolo',
                                                displayField:   'descripcion',
                                                valueField:     'descripcion',
                                                store:          storeProtocolosEnrutamiento,
                                                width:          180,
                                                listeners: {
                                                    select: function(combo){
                                                        if(combo.getValue() !== "STANDARD")
                                                        {
                                                            Ext.getCmp('asPrivado').setVisible(true);
                                                            Ext.getCmp('defaultGateway').setValue(false);
                                                            Ext.getCmp('defaultGateway').setRawValue(false);
                                                        }
                                                        else
                                                        {
                                                            Ext.getCmp('asPrivado').setVisible(false);
                                                            Ext.getCmp('defaultGateway').setValue(false);
                                                            Ext.getCmp('defaultGateway').setRawValue(false);
                                                        }

                                                        if(combo.getValue() === "BGP")
                                                        {
                                                            Ext.getCmp('defaultGateway').setVisible(true);
                                                        }
                                                        else
                                                        {
                                                            Ext.getCmp('defaultGateway').setVisible(false);
                                                            Ext.getCmp('defaultGateway').setValue(false);
                                                            Ext.getCmp('defaultGateway').setRawValue(false);
                                                        }
                                                    }
                                                }
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                id:             'mascaras',
                                                name:           'mascaras',
                                                fieldLabel:     'Mascara',
                                                displayField:   'display',
                                                valueField:     'value',
                                                store:          storeMascaras,
                                                width:          150
                                            },
                                            {   width: 25,border: false   },

                                            //--------------------------------------
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'numberfield',
                                                id:             'asPrivado',
                                                allowBlank:      false,
                                                name:           'asPrivado',
                                                fieldLabel:     'As Privado',
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
                                                boxLabel: 'Neighbor Default Gateway',
                                            },
                                            {   width: 25,border: false   },
                                        ]//items fielset
                                    }]
                                },//containerNuevosDatos

                                //container Mismos recursos
                                {
                                    id: 'containerMismosDatos',
                                    xtype: 'fieldset',
                                    title: 'Mismos Recursos',
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
                                                id:             'subredesDisponibles',
                                                name:           'subredesDisponibles',
                                                fieldLabel:     'Subred',
                                                displayField:   'subred',
                                                valueField:     'id',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                store:          storeSubredesL3mplsDisponibles,
                                                width:          270,
                                                listeners: {
                                                    select: function(combo){
                                                        var raw = combo.valueModels[0].raw;
                                                        Ext.Ajax.request({
                                                            url: urlAjaxGetInfoBackboneSubredL3mpls,
                                                            method: 'post',
                                                            params: { idServicio : raw.idServicio },
                                                            success: function(response){
                                                                var jsonDatosSubred = Ext.JSON.decode(response.responseText);

                                                                Ext.getCmp('mismaVlan').setValue(jsonDatosSubred.vlan);
                                                                Ext.getCmp('mismaVlan').setVisible(true);
                                                                Ext.getCmp('mismaVrf').setValue(jsonDatosSubred.vrf);
                                                                Ext.getCmp('mismaVrf').setVisible(true);
                                                                Ext.getCmp('mismoProtocolo').setValue(jsonDatosSubred.protocolos);
                                                                Ext.getCmp('mismoProtocolo').setVisible(true);
                                                                Ext.getCmp('mismoAsPrivado').setValue(jsonDatosSubred.asPrivado);

                                                                if(jsonDatosSubred.protocolo!=="STANDARD")
                                                                    Ext.getCmp('mismoAsPrivado').setVisible(true);
                                                                else
                                                                    Ext.getCmp('mismoAsPrivado').setVisible(false);

                                                                Ext.getCmp('vlansDisponibles').setRawValue(jsonDatosSubred.idVlan);
                                                                Ext.getCmp('vrfsDisponibles').setRawValue(jsonDatosSubred.idVrf);
                                                                Ext.getCmp('protocolosEnrutamiento').setValue(jsonDatosSubred.protocolos);

                                                            },
                                                            failure: function(response)
                                                            {
                                                                Ext.getCmp('mismaVlan').setVisible(false);
                                                                Ext.getCmp('mismaVrf').setVisible(false);
                                                                Ext.getCmp('mismoProtocolo').setVisible(false);
                                                                Ext.getCmp('mismoAsPrivado').setVisible(false);

                                                                Ext.getCmp('vlansDisponibles').setRawValue(null);
                                                                Ext.getCmp('vrfsDisponibles').setRawValue(null);
                                                                Ext.getCmp('protocolosEnrutamiento').setValue(null);

                                                                Ext.MessageBox.show({
                                                                    title: 'Error',
                                                                    msg: response.responseText,
                                                                    buttons: Ext.MessageBox.OK,
                                                                    icon: Ext.MessageBox.ERROR
                                                                }); 
                                                            }
                                                        });
                                                    }
                                                },
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVrf',
                                                name:           'mismaVrf',
                                                fieldLabel:     'Vrf',
                                                readOnly:       true,
                                                width:          270
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVlan',
                                                name:           'mismaVlan',
                                                fieldLabel:     'Vlan',
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
                                                fieldLabel:     'Protocolo',
                                                readOnly:       true,
                                                width:          180,
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'mismoAsPrivado',
                                                name:           'mismoAsPrivado',
                                                fieldLabel:     'As Privado',
                                                readOnly:       true,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   }
                                        ]
                                    }]
                                }
                            ]
                        }  
                    ],
                    buttons: [
                        {
                            text: 'Grabar',
                            handler: function() {
                                var flag           = false;
                                var vlan           = Ext.getCmp('vlansDisponibles').getValue();
                                var vrf            = Ext.getCmp('vrfsDisponibles').getValue();
                                var protocolo      = Ext.getCmp('protocolosEnrutamiento').getValue();
                                var asPrivado      = Ext.getCmp('asPrivado').getValue();
                                var subred         = Ext.getCmp('subredesDisponibles').getValue();
                                var mascara        = Ext.getCmp('mascaras').getValue();
                                var defaultGateway = Ext.getCmp('defaultGateway').getValue();
                                var idElementoPadre= datosBackbone.idElementoPadre;
                                var ultimaMilla    = data.ultimaMilla;
                                var flagRecursos   = Ext.ComponentQuery.query('[name=rbRecursos]')[0].getGroupValue();

                                if(flagRecursos === "existentes")
                                {
                                    vlan         = Ext.getCmp('vlansDisponibles').getRawValue();
                                    vrf          = Ext.getCmp('vrfsDisponibles').getRawValue();    
                                }

                                if (flagRecursos === "nuevos")
                                {
                                    if (vlan == null || vrf == null || protocolo == null || mascara == null)
                                    {
                                        Ext.Msg.alert('Alerta ', "Por favor ingrese los valores de los nuevos Recursos");
                                        flag = false;
                                    }
                                    else
                                    {
                                        flag = true;
                                    }
                                }
                                else if (flagRecursos === "existentes")
                                {
                                    if (subred == null)
                                    {
                                        Ext.Msg.alert('Alerta ', "Por favor escoja la subred");
                                        flag = false;
                                    }
                                    else
                                    {
                                        flag = true;
                                    }
                                }

                                if(flag == true)
                                {
                                    Ext.get(formPanel.getId()).mask('Grabando Nuevos Datos...');
                                    Ext.Ajax.request({
                                        url: crearSolicitudMigracionAnillo,
                                        method: 'post',
                                        timeout: 400000,
                                        params: {
                                            idServicio              : data.idServicio, //servicio
                                            idPersonaEmpresaRol     : data.idPersonaEmpresaRol ,
                                            capacidadUno            : data.capacidadUno,
                                            capacidadDos            : data.capacidadDos,
                                            ultimaMilla             : ultimaMilla,
                                            strMigracionVlan        : migracionVlan,
                                            idElementoPadre         : idElementoPadre,
                                            //datos de recursos
                                            vlan                    : vlan,
                                            vrf                     : vrf,
                                            protocolo               : protocolo,
                                            defaultGateway          : defaultGateway,
                                            asPrivado               : asPrivado,
                                            mascara                 : mascara,
                                            idSubred                : subred,
                                            flagRecursos            : flagRecursos
                                        },
                                        success: function(response) {
                                            Ext.get(formPanel.getId()).unmask();                                                                        
                                            if (response.responseText === "OK") 
                                            {
                                                Ext.Msg.alert('Mensaje', "Se grabaron los nuevos datos de UM", function(btn) {
                                                    if (btn === 'ok') {
                                                        win.destroy();
                                                        store.load();
                                                    }
                                                });
                                            }
                                            else 
                                            {
                                                Ext.Msg.alert('Error ', response.responseText);
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.get(formPanel.getId()).unmask();
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                }
                            }
                        },
                        {
                            text: 'Cerrar',
                            handler: function() {
                                win.destroy();
                            }
                        }]
                });

                Ext.getCmp('containerNuevosDatos').setVisible(false);
                Ext.getCmp('containerMismosDatos').setVisible(false);

                var win = Ext.create('Ext.window.Window', {
                    title: tituloPantalla,
                    modal: true,
                    width: 700,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

                storeSubredesL3mplsDisponibles.load({
                    callback:function(){
                        storeVrfsDisponibles.load({
                            callback: function(){
                                storeProtocolosEnrutamiento.load({
                                    callback: function(){
                                    }
                                });
                            }
                        });
                    }
                });
            }
        },
        failure: function(result)
        {
            Ext.get(document.body).unmask();
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });                
}

function ejecutaMigracionAnillo(data,migracionVlan)
{
    var tituloPantalla = "Ejecutar Migración a Anillo";
    if(migracionVlan === "S")
    {
        tituloPantalla = "Ejecutar Migración de VLAN";
    }

    Ext.get("grid").mask('Consultando Datos...');
    Ext.Ajax.request
    ({
        url: urlGetDatosBackboneL3mpls,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function(response){
            var datosBackbone = Ext.JSON.decode(response.responseText);

            Ext.get("grid").unmask();

            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 1,
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
                        colspan: 2,
                        rowspan: 2,
                        xtype: 'panel',
                        title: 'Informacion del Cliente y Servicio',
                        defaults: { 
                            height: 100
                        },
                        items: [
                           {
                                xtype: 'container',
                                layout: {
                                    type:    'table',
                                    columns: 5,
                                    align:   'stretch'
                                },
                                items: [

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'cliente',
                                        fieldLabel: 'Cliente',
                                        displayField: data.nombreCompleto,
                                        value: data.nombreCompleto,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'Login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    {   width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        displayField: data.nombreProducto,
                                        value: data.nombreProducto,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'tipoOrden',
                                        fieldLabel: 'Tipo Orden',
                                        displayField: data.tipoOrdenCompleto,
                                        value: data.tipoOrdenCompleto,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'capacidad1',
                                        name: 'capacidad1',
                                        fieldLabel: 'Capacidad1',
                                        displayField: data.capacidadUno,
                                        value: data.capacidadUno,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'capacidad2',
                                        name: 'capacidad2',
                                        fieldLabel: 'Capacidad2',
                                        displayField: data.capacidadDos,
                                        value: data.capacidadDos,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'mac',
                                        name: 'mac',
                                        fieldLabel: 'Mac',
                                        displayField: datosBackbone.mac,
                                        value: datosBackbone.mac,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '10%', border: false},
                                    { width: '10%', border: false},

                                ]
                            }

                        ]
                    },//cierre de informacion del servicio

                    //informacion del backbone
                    {
                        colspan: 2,
                        rowspan: 2,
                        xtype: 'panel',
                        title: 'Informacion del Backbone',
                        defaults: { 
                            height: 180
                        },
                        items: [
                           {
                                xtype: 'container',
                                layout: {
                                    type:    'table',
                                    columns: 5,
                                    align:   'stretch'
                                },
                                items: [

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'nombreElementoPadre',
                                        name: 'nombreElementoPadre',
                                        fieldLabel: 'Nombre Elemento Padre',
                                        displayField: datosBackbone.elementoPadre,
                                        value: datosBackbone.elementoPadre,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    {
                                        xtype:  'hidden',
                                        id:     'idElementoPadre',
                                        name:   'idElementoPadre',
                                        value:  datosBackbone.idElementoPadre,
                                        width:  '30%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'anillo',
                                        name: 'anillo',
                                        fieldLabel: 'Anillo',
                                        displayField: datosBackbone.anillo,
                                        value: datosBackbone.anillo,
                                        readOnly: true,
                                        width: '15%'
                                    },
                                    { width: '10%', border: false},

                                    {   width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'nombreElemento',
                                        name: 'nombreElemento',
                                        fieldLabel: 'Nombre Elemento',
                                        displayField: datosBackbone.elemento,
                                        value: datosBackbone.elemento,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'interfaceElemento',
                                        name: 'interfaceElemento',
                                        fieldLabel: 'Interface Elemento',
                                        displayField: datosBackbone.interfaceElemento,
                                        value: datosBackbone.interfaceElemento,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'vlan',
                                        name: 'vlan',
                                        fieldLabel: 'Vlan',
                                        displayField: data.vlan,
                                        value: data.vlan,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'vrf',
                                        name: 'vrf',
                                        fieldLabel: 'Vrf',
                                        displayField: data.vrf,
                                        value: data.vrf,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'asPrivado',
                                        name: 'asPrivado',
                                        fieldLabel: 'As Privado',
                                        displayField: data.asPrivado,
                                        value: data.asPrivado,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'protocolo',
                                        name: 'protocolo',
                                        fieldLabel: 'Protocolo',
                                        displayField: data.protocolo,
                                        value: data.protocolo,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'ipL3mpls',
                                        name: 'ipL3mpls',
                                        fieldLabel: 'Ip L3MPLS',
                                        displayField: data.ipServicio,
                                        value: data.ipServicio,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'subred',
                                        name: 'subred',
                                        fieldLabel: 'Subred',
                                        displayField: data.subredServicio,
                                        value: data.subredServicio,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    { width: '10%', border: false},
                                    {
                                        xtype:  'hidden',
                                        id:     'idElemento',
                                        name:   'idElemento',
                                        value:  datosBackbone.idElemento,
                                        displayField:  datosBackbone.idElemento,
                                        width:  '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype:  'hidden',
                                        id:     'idInterfaceElemento',
                                        name:   'idInterfaceElemento',
                                        value:  datosBackbone.idInterfaceElemento,
                                        displayField:  datosBackbone.idInterfaceElemento,
                                        width:  '30%'
                                    },
                                    { width: '10%', border: false}

                                ]
                            }

                        ]
                    }//cierre de informacion del backbone
                ],
                buttons: 
                [{
                    text: 'Ejecutar',
                    formBind: true,
                    handler: function(){
                        var mensaje = "a Anillo";
                        if(migracionVlan === "S")
                        {
                            mensaje = "de VLAN";
                        }

                        Ext.Msg.confirm('Mensaje','Está seguro de ejecutar la Migración '+mensaje+'?', function(btn){
                            if(btn==='yes')
                            {
                                var vlan                    = Ext.getCmp('vlan').getValue();
                                var vrf                     = Ext.getCmp('vrf').getValue();
                                var asPrivado               = Ext.getCmp('asPrivado').getValue();
                                var protocolo               = Ext.getCmp('protocolo').getValue();
                                var ipL3mpls                = Ext.getCmp('ipL3mpls').getValue();
                                var subred                  = Ext.getCmp('subred').getValue();
                                var idElementoPadre         = Ext.getCmp('idElementoPadre').getValue();
                                var idElemento              = Ext.getCmp('idElemento').getValue();
                                var idInterfaceElemento     = Ext.getCmp('idInterfaceElemento').getValue();
                                var capacidad1              = Ext.getCmp('capacidad1').getValue();
                                var capacidad2              = Ext.getCmp('capacidad2').getValue();
                                var rdId                    = data.rdId;
                                var anillo                  = Ext.getCmp('anillo').getValue();
                                var nombreElementoPadre     = data.elementoPadre;
                                var mascaraSubredServicio   = data.mascaraSubredServicio;
                                var gwSubredServicio        = data.gwSubredServicio;
                                var tipoEnlace              = data.tipoEnlace;
                                var defaultGateway          = data.defaultGateway;

                                Ext.get(formPanel.getId()).mask('ejecutando migracion...');

                                Ext.Ajax.request({
                                    url: ejecutaMigracionAnilloEstable,
                                    method: 'post',
                                    timeout: 1000000,
                                    params: { 
                                        idServicio:             data.idServicio,
                                        idElementoPadre:        idElementoPadre,
                                        idElemento:             idElemento,
                                        idInterfaceElemento:    idInterfaceElemento,
                                        vlan:                   vlan,
                                        mac:                    datosBackbone.mac,
                                        vrf:                    vrf,
                                        protocolo:              protocolo,
                                        asPrivado:              asPrivado,
                                        ipServicio:             ipL3mpls,
                                        subredServicio:         subred,
                                        capacidadUno:           capacidad1,
                                        capacidadDos:           capacidad2,
                                        rdId:                   rdId,
                                        anillo:                 anillo,
                                        nombreElementoPadre:    nombreElementoPadre,
                                        mascaraSubredServicio:  mascaraSubredServicio,
                                        gwSubredServicio:       gwSubredServicio,
                                        defaultGateway:         defaultGateway,
                                        tipoEnlace:             tipoEnlace,
                                        strMigracionVlan:       migracionVlan
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText === "OK")
                                        {
                                            Ext.Msg.alert('Mensaje','Se ejecutó la Migración '+mensaje+' con éxito', function(btn){
                                                if(btn==='ok')
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
                            }//if(btn==='yes')
                        });
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
                title: tituloPantalla,
                modal: true,
                width: 600,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
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

/**
 * Función para reversar la solicitud de migración de anillo o vlan
 *
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.0 23-03-2020
 * */
function reversarSolicitudMigracionAnillo(data)
{
    var connReversar = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    btnGuardarReversar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function() {
            var strObservacion = Ext.getCmp('observacionSerie').value;
            winReversar.destroy();
            connReversar.request({
                method: 'POST',
                params:{
                    intIdServicio:  data.idServicio,
                    strObservacion: strObservacion
                },
                url: urlReversarSolicitudMigracionAnillo,
                success: function(response){
                    store.load();
                    Ext.Msg.alert('Alerta ',response.responseText);
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
    });
    btnCancelarReversar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            winReversar.destroy();
        }
    });
    formPanelReversar = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        layout: 'column',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            msgTarget: 'side'
        },
        items:
        [
            {
                xtype: 'fieldset',
                title: 'Reversar la Solicitud de Migración a Anillo',
                autoHeight: true,
                width: 475,
                items:
                [
                    {
                            xtype: 'displayfield',
                            fieldLabel: 'Login Aux:',
                            id: 'elemento_serie',
                            name: 'elemento_serie',
                            value: data.loginAux
                    },
                    {
                            xtype: 'textarea',
                            fieldLabel: 'Observación:',
                            id: 'observacionSerie',
                            name: 'observacionSerie',
                            rows: 3,
                            cols: 40,
                    }
                ]
            }
        ]
    });
    winReversar = Ext.create('Ext.window.Window', {
        title: "Reversar la Solicitud de Migración a Anillo",
        closable: false,
        modal: true,
        width: 500,
        height: 200,
        resizable: false,
        layout: 'fit',
        items: [formPanelReversar],
        buttonAlign: 'center',
        buttons:[btnGuardarReversar,btnCancelarReversar]
    }).show();
}