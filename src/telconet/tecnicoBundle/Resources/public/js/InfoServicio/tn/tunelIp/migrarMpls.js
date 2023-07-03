/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function migrarDataTunelAMpls(data)
{
    Ext.get("grid").mask('Consultando Datos...');
    Ext.Ajax.request({ 
        url: getDatosFactibilidad,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            ultimaMilla: data.ultimaMilla
        },
        success: function(response){
        var json = Ext.JSON.decode(response.responseText);
        //llamada ajax para obtener datos backbone
        if(json.status=='ERROR')
        {
            Ext.MessageBox.show({
                title: 'Error',
                msg: json.msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            }); 
            
            Ext.get("grid").unmask();
        }
        else
        {
            Ext.Ajax.request
            ({
                url: getDatosBackbone,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    tipoElementoPadre: 'ROUTER'
                },
                success: function(response){
                    var jsonDatosBackbone = Ext.JSON.decode(response.responseText);
                    var datosBackbone = jsonDatosBackbone.encontrados[0];

                    Ext.get("grid").unmask();

                    storeHilosDisponibles = new Ext.data.Store({  
                        autoLoad: true,
                        pageSize: 100,
                        proxy: {
                            type: 'ajax',
                            url : getHilosDisponibles,
                            extraParams: {
                                idElemento:                 json.idElementoConector,
                                estadoInterface:            'connected',
                                estadoInterfaceNotConect:   'not connect',
                                estadoInterfaceReserved:    'Factible'
                            },
                            reader: {
                                type: 'json',
                                root: 'encontrados'
                            }
                        },
                        fields:
                            [
                                {name:'idInterfaceElemento'   ,   mapping: 'idInterfaceElemento'},
                                {name:'idInterfaceElementoOut',   mapping: 'idInterfaceElementoOut'},
                                {name:'colorHilo',                mapping:'colorHilo'},
                                {name:'numeroHilo',               mapping:'numeroHilo'},
                                {name:'numeroColorHilo',          mapping:'numeroColorHilo'}
                            ]
                    });

                    storeVrfsDisponibles = new Ext.data.Store({
                        total: 'total',
                        proxy: {
                            type: 'ajax',
                            url: urlAjaxGetVrfsDisponibles,
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

                    storeVlansDisponibles = new Ext.data.Store({
                        total: 'total',
                        autoLoad: true,
                        listeners: {
                            load: function(store,records,options) {
                                if(store.totalCount===0)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + json.nombreElementoPadre + " ]",
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
                                nombreElemento: json.nombreElementoPadre,
                                idPersonaEmpresaRol : data.idPersonaEmpresaRol,
                                anillo              : json.anillo
                            }
                        },
                        fields:
                            [
                                {name: 'id'  , mapping: 'id'},
                                {name: 'vlan', mapping: 'vlan'}
                            ]
                    });

                    storeSubredesL3mplsDisponibles = new Ext.data.Store({
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
                                nombreElemento: json.nombreElementoPadre,
                                idPersonaEmpresaRol : data.idPersonaEmpresaRol,
                                nombreTecnico : 'L3MPLS',
                                tipoEnlace: data.tipoEnlace
                            }
                        },
                        fields:
                            [
                                {name: 'id'  , mapping: 'id'},
                                {name: 'subred', mapping: 'subred'},
                                {name: 'idServicio', mapping: 'idServicio'}
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
                                            { width: '10%', border: false}

                                        ]
                                    }

                                ]
                            },//cierre de informacion del servicio

                            //informacion del nuevo backbone
                            {
                                colspan: 3,
                                xtype: 'panel',
                                title: 'Informacion de Backbone',
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

                                                            Ext.getCmp('vlansDisponibles').setDisabled(false);
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
                                    },                                

                                    //informacion de Datos
                                    //container recursos obligatorios
                                    {
                                        id: 'containerRecursosObligatorios',
                                        xtype: 'fieldset',
                                        title: 'Datos Factibilidad',
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
                                                    id:             'nombreElementoPadre',
                                                    name:           'nombreElementoPadre',
                                                    fieldLabel:     'Nombre Elemento Padre',
                                                    readOnly:       true,
                                                    displayField:   json.nombreElementoPadre,
                                                    value:          json.nombreElementoPadre,
                                                    width:          300
                                                },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                {
                                                    xtype:          'textfield',
                                                    id:             'nombreElemento',
                                                    name:           'nombreElemento',
                                                    fieldLabel:     'Nombre Elemento',
                                                    readOnly:       true,
                                                    displayField:   json.nombreElemento,
                                                    value:          json.nombreElemento,
                                                    width:          300
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    xtype:          'textfield',
                                                    id:             'nombreInterfaceElemento',
                                                    name:           'nombreInterfaceElemento',
                                                    fieldLabel:     'Nombre Interface Elemento',
                                                    readOnly:       true,
                                                    displayField:   json.nombreInterfaceElemento,
                                                    value:          json.nombreInterfaceElemento,
                                                    width:          180
                                                },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                //---------------------------------------
                                                {
                                                    xtype:          'textfield',
                                                    id:             'nombreElementoContenedor',
                                                    name:           'nombreElementoContenedor',
                                                    fieldLabel:     'Nombre Elemento Contenedor',
                                                    displayField:   json.nombreElementoContenedor,
                                                    value:          json.nombreElementoContenedor,
                                                    readOnly:       true,
                                                    width   :       600
                                                },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                {   width: 25,border: false   },
                                                //---------------------------------------
                                                {
                                                    xtype:          'textfield',
                                                    id:             'nombreElementoConector',
                                                    name:           'nombreElementoConector',
                                                    fieldLabel:     'Nombre Elemento Conector',
                                                    displayField:   json.nombreElementoConector,
                                                    value:          json.nombreElementoConector,
                                                    readOnly:       true,
                                                    width    :      600
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'colorHilo',
                                                    fieldLabel: 'Hilo',
                                                    displayField: datosBackbone.colorHilo,
                                                    value: datosBackbone.colorHilo,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                            ]
                                        }]
                                    },//containerRecursosObligatorios

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
                                                columns: 6,
                                                align: 'stretch'
                                            },
                                            items: [
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
                                                    store:          storeVlansDisponibles,
                                                    width:          180,
                                                    listeners: {
                                                        select: function(combo){
                                                            storeVrfsDisponibles.proxy.extraParams = {  
                                                                                          idPersonaEmpresaRol : data.idPersonaEmpresaRol,
                                                                                          idServicio: data.idServicio,
                                                                                          idVlan: combo.getValue() 
                                                                                                    };
                                                            storeVrfsDisponibles.load({ params: {} });
                                                        }
                                                    },
                                                },
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
                                                    width:          270
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
                                                    fieldLabel:     'Protocolo',
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
                                                            if(combo.getValue()=="BGP"){
                                                                Ext.getCmp('defaultGateway').setVisible(true);
                                                            }else
                                                                Ext.getCmp('defaultGateway').setVisible(false);
                                                        }
                                                    },
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
                                                {   width: 25,border: false   },
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
                                                {   width: 25,border: false   },
                                                {   border: false   }
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
                                                                    
                                                                    Ext.getCmp('subredesDisponibles').setValue(null);
                                                                    Ext.getCmp('subredesDisponibles').setRawValue(null);
                                                                    
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
                                            },
                                ]
                            }//cierre informacion del nuevo backbone
                        ],
                        buttons: 
                        [{
                            text: 'Guardar',
                            formBind: true,
                            handler: function(){
                                Ext.Msg.confirm('Mensaje','Está seguro de generar los recursos de red ?', function(btn){
                                    if(btn==='yes')
                                    {
                                        var vlan        = Ext.getCmp('vlansDisponibles').getValue();
                                        var vrf         = Ext.getCmp('vrfsDisponibles').getValue();
                                        var protocolo   = Ext.getCmp('protocolosEnrutamiento').getValue();
                                        var asPrivado   = Ext.getCmp('asPrivado').getValue();
                                        var mascara     = Ext.getCmp('mascaras').getValue();
                                        var defaultGateway = Ext.getCmp('defaultGateway').getValue();
                                        var subred      = Ext.getCmp('subredesDisponibles').getValue();
                                        
                                        var flagRecursos = Ext.ComponentQuery.query('[name=rbRecursos]')[0].getGroupValue();
                                        if(flagRecursos==="existentes")
                                        {
                                            vlan = Ext.getCmp('vlansDisponibles').getRawValue();
                                            vrf = Ext.getCmp('vrfsDisponibles').getRawValue();
                                        }

                                        Ext.get(formPanel.getId()).mask('Guardando datos...');

                                        Ext.Ajax.request({
                                            url: ajaxMigrarServicioTunelIpADatosMpls,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: { 
                                                idPersonaEmpresaRol:    data.idPersonaEmpresaRol ,
                                                idServicio:             data.idServicio,
                                                idElementoPadre:        json.idElementoPadre,
                                                vlan:                   vlan,
                                                vrf:                    vrf,
                                                protocolo:              protocolo,
                                                asPrivado:              asPrivado,
                                                mascara:                mascara,
                                                idSubred:               subred,
                                                defaultGateway:         defaultGateway,
                                                flagRecursos:           flagRecursos
                                            },
                                            success: function(response){
                                                Ext.get(formPanel.getId()).unmask();
                                                if(response.responseText === "OK")
                                                {
                                                    Ext.Msg.alert('Mensaje','Se Asignaron los Recursos de Red de la Migración', function(btn){
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

                    Ext.getCmp('containerRecursosObligatorios').setVisible(false);
                    Ext.getCmp('containerNuevosDatos').setVisible(false);
                    Ext.getCmp('containerMismosDatos').setVisible(false);

                    var win = Ext.create('Ext.window.Window', {
                        title: 'Migración Tunel IP a L3MPLS',
                        modal: true,
                        width: 1100,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();

                    storeSubredesL3mplsDisponibles.load({
                        callback:function(){
                            storeHilosDisponibles.load({
                                callback:function(){
                                    storeVlansDisponibles.load({
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
        }},//cierre response
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

function ejecutarMigracion(data){
    Ext.get("grid").mask('Consultando Datos...');
    Ext.Ajax.request
            ({
                url: getDatosBackbone,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    tipoElementoPadre: 'ROUTER'
                },
                success: function(response){
                    var jsonDatosBackbone = Ext.JSON.decode(response.responseText);
                    var datosBackbone = jsonDatosBackbone.encontrados[0];

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
                                                name: 'capacidad2',
                                                fieldLabel: 'Capacidad2',
                                                displayField: data.capacidadDos,
                                                value: data.capacidadDos,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            { width: '10%', border: false}

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
                                                displayField: datosBackbone.nombreElementoPadre,
                                                value: datosBackbone.nombreElementoPadre,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            { width: '15%', border: false},
                                            {
                                                xtype:  'hidden',
                                                id:     'idElementoPadre',
                                                name:   'idElementoPadre',
                                                value:  datosBackbone.idElementoPadre,
                                                width:  '30%'
                                            },
                                            { width: '10%', border: false},

                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                id: 'nombreElemento',
                                                name: 'nombreElemento',
                                                fieldLabel: 'Nombre Elemento',
                                                displayField: datosBackbone.nombreElemento,
                                                value: datosBackbone.nombreElemento,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            { width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                id: 'interfaceElemento',
                                                name: 'interfaceElemento',
                                                fieldLabel: 'Interface Elemento',
                                                displayField: datosBackbone.nombreInterfaceElemento,
                                                value: datosBackbone.nombreInterfaceElemento,
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
                                                width:  '30%'
                                            },
                                            { width: '15%', border: false},
                                            {
                                                xtype:  'hidden',
                                                id:     'idInterfaceElemento',
                                                name:   'idInterfaceElemento',
                                                value:  datosBackbone.idInterfaceElemento,
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
                                Ext.Msg.confirm('Mensaje','Está seguro de ejecutar la migración de tunel ip a l3mpls ?', function(btn){
                                    if(btn==='yes')
                                    {
                                        var vlan                = Ext.getCmp('vlan').getValue();
                                        var vrf                 = Ext.getCmp('vrf').getValue();
                                        var asPrivado           = Ext.getCmp('asPrivado').getValue();
                                        var protocolo           = Ext.getCmp('protocolo').getValue();
                                        var ipL3mpls            = Ext.getCmp('ipL3mpls').getValue();
                                        var subred              = Ext.getCmp('subred').getValue();
                                        var idElementoPadre     = Ext.getCmp('idElementoPadre').getValue();
                                        var idElemento          = Ext.getCmp('idElemento').getValue();
                                        var idInterfaceElemento = Ext.getCmp('idInterfaceElemento').getValue();
                                        
                                        Ext.get(formPanel.getId()).mask('Guardando datos...');

                                        Ext.Ajax.request({
                                            url: ajaxEjecutaMigracionServicioTunelADatosMpls,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: { 
                                                idServicio:             data.idServicio,
                                                idElementoPadre:        idElementoPadre,
                                                idElemento:             idElemento,
                                                idInterfaceElemento:    idInterfaceElemento,
                                                vlan:                   vlan,
                                                vrf:                    vrf,
                                                protocolo:              protocolo,
                                                asPrivado:              asPrivado,
                                                ipL3mpls:               ipL3mpls,
                                                idSubred:               subred,
                                                mac:                    datosBackbone.mac,
                                                ipServicio:             data.ipServicio,
                                                defaultGateway:         data.defaultGateway,
                                                rdId:                   data.rdId,
                                                subredServicio:         data.subredServicio,
                                                gwSubredServicio:       data.gwSubredServicio,
                                                mascaraSubredServicio:  data.mascaraSubredServicio
                                            },
                                            success: function(response){
                                                Ext.get(formPanel.getId()).unmask();
                                                if(response.responseText === "OK")
                                                {
                                                    Ext.Msg.alert('Mensaje','Se ejecuto la migración con éxito', function(btn){
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
                        title: 'Migración Tunel IP a L3MPLS',
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