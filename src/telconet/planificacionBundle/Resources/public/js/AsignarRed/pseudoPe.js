/**
 * Funcion que sirve para mostrar la pantalla para la asignacion 
 * de recursos de red para L3mpls con PesudoPe
 * */
function showRecursoRedLPseudoPe(data)
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
                            nombreElemento      : json.nombrePe,
                            idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                            nombreTecnico       : data.get('nombreTecnico'),
                            tipoEnlace          : data.get('tipo_enlace'),
                            anillo              : json.anillo,
                            esPseudoPe          : data.get('esPseudoPe')
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
                                height: 200
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
                                            xtype: 'textarea',
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
                                height: 200
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
                                                id:             'nombreElementoEdificio',
                                                name:           'nombreElementoEdificio',
                                                fieldLabel:     'Edificio',
                                                readOnly:       true,
                                                displayField:   json.nombreEdificio,
                                                value:          json.nombreEdificio,
                                                width:          500
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'administradoPor',
                                                name:           'administradoPor',
                                                fieldLabel:     'Administra',
                                                readOnly:       true,
                                                displayField:   json.administradoPor,
                                                value:          json.administradoPor,
                                                width:          300
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'nombrePe',
                                                name:           'nombrePe',
                                                fieldLabel:     'Nombre Pe',
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
                                                fieldLabel:     'Interface Pe',
                                                readOnly:       true,
                                                displayField:   json.interfacePe,
                                                value:          json.interfacePe,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   }
                                            //---------------------------------------
                                        ]
                                    }]
                                },                                    

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
                                                id:             'vrfsDisponibles',
                                                name:           'vrfsDisponibles',
                                                fieldLabel:     'Vrf',
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
                                                fieldLabel:     'Vlan',
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
                                                        if(combo.getValue()==="BGP"){
                                                            Ext.getCmp('defaultGateway').setVisible(true);
                                                        }else
                                                            Ext.getCmp('defaultGateway').setVisible(false);
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
                                                boxLabel: 'Neighbor Default Gateway'
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
                                                        Ext.get(formPanel.getId()).mask('Obteniendo información...');
                                                        Ext.Ajax.request({
                                                            url: urlAjaxGetInfoBackboneSubredL3mpls,
                                                            method: 'post',
                                                            params: { idServicio : raw.idServicio },
                                                            success: function(response){
                                                                Ext.get(formPanel.getId()).unmask();
                                                                
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

                                                                Ext.getCmp('vlansDisponibles').setRawValue(jsonDatosSubred.vlan);
                                                                Ext.getCmp('vrfsDisponibles').setRawValue(jsonDatosSubred.idVrf);
                                                                Ext.getCmp('protocolosEnrutamiento').setValue(jsonDatosSubred.protocolos);

                                                            },
                                                            failure: function(response)
                                                            {
                                                                Ext.get(formPanel.getId()).unmask();
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
                                                }
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
                                                width:          180
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
                                }//containerMismosDatos
                                ]
                        }//cierre informacion de los elementos del cliente
                    ],
                    buttons: 
                    [{
                        text: 'Guardar',
                        formBind: true,
                        handler: function()
                        {
                            var ultimaMilla = data.get('ultimaMilla');
                            var vlan        = parseInt(Ext.getCmp('vlansDisponibles').getValue());
                            
                            if(ultimaMilla === 'SATELITAL')
                            {
                                //Se obtiene limite establecido para VLANs por Departamento Satelital
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
                                boolContinua= true;
                            }
                            
                            if(boolContinua)
                            {
                                Ext.Msg.confirm('Mensaje','Esta seguro de asignar los recursos de red ?', function(btn){
                                    if(btn==='yes')
                                    {
                                        var vrf            = Ext.getCmp('vrfsDisponibles').getValue();
                                        var protocolo      = Ext.getCmp('protocolosEnrutamiento').getValue();
                                        var asPrivado      = Ext.getCmp('asPrivado').getValue();
                                        var subred         = Ext.getCmp('subredesDisponibles').getValue();
                                        var mascara        = Ext.getCmp('mascaras').getValue();
                                        var defaultGateway = Ext.getCmp('defaultGateway').getValue();
                                        var flagRecursos   = Ext.ComponentQuery.query('[name=rbRecursos]')[0].getGroupValue();

                                        if(flagRecursos==="existentes")
                                        {
                                            vlan         = Ext.getCmp('vlansDisponibles').getRawValue();
                                            vrf          = Ext.getCmp('vrfsDisponibles').getRawValue();    
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
                                                tipoSolicitud:          data.get('descripcionSolicitud'),
                                                idElementoPadre:        json.idElemento,                                            
                                                vlan:                   vlan,
                                                vrf:                    vrf,
                                                protocolo:              protocolo,
                                                defaultGateway:         defaultGateway,
                                                asPrivado:              asPrivado,
                                                mascara:                mascara,
                                                idSubred:               subred,
                                                flagRecursos:           flagRecursos,
                                                ultimaMilla:            ultimaMilla,
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

                Ext.getCmp('containerRecursosObligatorios').setVisible(false);
                Ext.getCmp('containerNuevosDatos').setVisible(false);
                Ext.getCmp('containerMismosDatos').setVisible(false);
                
                var tipoProceso = '';
                
                switch(data.get('ultimaMilla'))
                {
                    case 'TERCERIZADA':
                        tipoProceso = '( TERCERIZADA )';
                        break;
                    case 'SATELITAL':
                        tipoProceso = '( VSAT/SATELITAL )';
                        break;
                    default:
                        tipoProceso = '( PSEUDO-PE )';
                        break;
                }
                
                var win = Ext.create('Ext.window.Window', {
                    title: 'Asignar Recurso de Red - L3MPLS '+tipoProceso,
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

                Ext.get(formPanel.getId()).mask('Cargando datos...');

                storeVrfsDisponibles.load({
                    callback: function() {
                        storeSubredesL3mplsDisponibles.load({
                            callback: function() {
                                Ext.get(formPanel.getId()).unmask();
                                    storeProtocolosEnrutamiento.load({});
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

function showRecursosRedIntMplsPseudoPe(data)
{
    var idInterfaceElementoOutAsignado = 0;
    var nombreElemento                 = "";
    var nombreElementoConector         = "";
    var anillo                         = "";
    var esPseudoPe                     = data.get('esPseudoPe');
    var esSatelital                    = false;
    
    var numeroColorHiloSeleccionado    = "";
    Ext.get(grid.getId()).mask('Consultando Datos...');
    
    if(data.get('ultimaMilla') === 'SATELITAL')
    {        
        esSatelital   = true;
    }
        
    var storeMascaras = new Ext.data.Store({
        fields: ['subred','idSubred'],
        data: [
            {
                "subred": "/29",
                "idSubred": "255.255.255.248"
            },
            {
                "subred": "/30",
                "idSubred": "255.255.255.252"
            }
        ]
    });
    
    Ext.Ajax.request({
        url: getDatosFactibilidad,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.get('id_servicio'),
            ultimaMilla: data.get('ultimaMilla'),
            tipoSolicitud: data.get('descripcionSolicitud'),
            idSolicitud  : data.get('id_factibilidad')
        },
        success: function(response) {
            Ext.get(grid.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            idInterfaceElementoOutAsignado = json.idInterfaceElementoConector;
            numeroColorHiloSeleccionado    = json.numeroColorHilo;
            anillo                         = json.anillo;          
            nombreElemento                 = json.nombreElemento;
            nombreElementoConector         = json.nombreElementoConector;
            
            //-------------------------------------------------------------------------------------------
            if(json.status==="OK")
            {                
                var storeVrfInternet = new Ext.data.Store({
                    pageSize: 100,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: getVrfInternet,
                        reader: {
                            type: 'json',
                            root: 'encontrados'
                        }
                    },
                    fields:
                            [
                                {name: 'id', mapping:  'id'},
                                {name: 'valor', mapping: 'valor'}
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
                            rowspan: 2,
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
                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoOrden',
                                            fieldLabel: 'Tipo Orden',
                                            displayField: data.get("tipo_orden"),
                                            value: data.get("tipo_orden"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: data.get("producto"),
                                            value: data.get("producto"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------

                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad1',
                                            fieldLabel: 'Capacidad1',
                                            displayField: json.capacidad1,
                                            value: json.capacidad1,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: json.capacidad2,
                                            value: json.capacidad2,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------
                                        
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
                        }, //cierre de informacion del servicio

                        //informacion del cliente
                        {
                            colspan: 2,
                            rowspan: 2,
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
                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'cliente',
                                            fieldLabel: 'Cliente',
                                            displayField: data.get('cliente'),
                                            value: data.get('cliente'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.get('login2'),
                                            value: data.get('login2'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------

                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'ciudad',
                                            fieldLabel: 'Ciudad',
                                            displayField: data.get('ciudad'),
                                            value: data.get('ciudad'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'direccion',
                                            fieldLabel: 'Direccion',
                                            displayField: data.get('direccion'),
                                            value: data.get('direccion'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------

                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'sector',
                                            fieldLabel: 'Sector',
                                            displayField: data.get('nombreSector'),
                                            value: data.get('nombreSector'),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'esRecontratacion',
                                            fieldLabel: 'Es Recontratacion',
                                            displayField: data.get("esRecontratacion"),
                                            value: data.get("esRecontratacion"),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        {width: '10%', border: false}

                                        //---------------------------------------------

                                    ]
                                }

                            ]
                        }, //cierre de la informacion del cliente

                        //informacion tecnica (generada en la factibilidad)
                        {
                            colspan: 3,
                            xtype: 'panel',
                            title: 'Información técnica asignada por la Factibilidad',
                            layout: 'anchor',
                            items: [
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    items: [
                                        // ===========================================
                                        // Elemento Padre 
                                        // ===========================================
                                        {
                                            xtype: 'fieldset',
                                            title: 'Elemento Padre',
                                            collapsible: false,
                                            collapsed: false,
                                            defaultType: 'textfield',
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch',
                                                        type: 'hbox'
                                                    },
                                                    items: [
                                                        {
                                                            id: 'txtNombreElementoPadre',
                                                            name: 'txtNombreElementoPadre',
                                                            xtype: 'textfield',
                                                            fieldLabel: '<b>Nombre Pe</b>',
                                                            displayField: json.nombrePe,
                                                            value: json.nombrePe,
                                                            readOnly: true,
                                                            width: '40%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtAnilloElementoPadre',
                                                            name: 'txtAnilloElementoPadre',
                                                            fieldLabel: '<b>Anillo</b>',
                                                            displayField: json.anillo,
                                                            value: json.anillo,
                                                            readOnly: true,
                                                            width: '10%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtVlan',
                                                            name: 'txtVlan',
                                                            fieldLabel: '<b>Vlan</b>',
                                                            displayField: (!esSatelital?'':json.vlan),
                                                            value: (!esSatelital?'':json.vlan),
                                                            readOnly: (!esSatelital?false:true),
                                                            width: '10%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            id: 'cbxVrf',
                                                            name: 'cbxVrf',
                                                            xtype: 'combobox',
                                                            fieldLabel: '<b>VRF</b>',
                                                            store: storeVrfInternet,
                                                            queryMode: 'local',
                                                            displayField: 'valor',
                                                            valueField: 'id',
                                                            editable: false,
                                                            width: '20%',
                                                            labelAlign: 'top'
                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'hbox',
                                                align: 'stretch'
                                            },
                                            items: [
                                                // ===========================================
                                                // Elemento Asignado 
                                                // ===========================================
                                                {
                                                    flex: 11,
                                                    xtype: 'fieldset',
                                                    height: "auto",
                                                    title: 'Elemento',
                                                    collapsible: false,
                                                    defaultType: 'textfield',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNodo',
                                                            name: 'txtNodo',
                                                            fieldLabel: '<b>Nodo/Edificio</b>',
                                                            displayField: json.nombreEdificio,
                                                            value: json.nombreEdificio,
                                                            readOnly: true,
                                                            width: '50%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreElemento',
                                                            name: 'txtNombreElemento',
                                                            fieldLabel: '<b>Nombre</b>',
                                                            displayField: json.nombreSwHub,
                                                            value: json.nombreSwHub,
                                                            readOnly: true,
                                                            width: '50%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreInterfaceElemento',
                                                            name: 'txtNombreInterfaceElemento',
                                                            fieldLabel: '<b>Interface</b>',
                                                            displayField: json.interfaceSwHub,
                                                            value: json.interfaceSwHub,
                                                            readOnly: true,
                                                            width: '50%',
                                                            labelAlign: 'top'
                                                        }
                                                    ]
                                                },
                                                {
                                                    flex: 1,
                                                    border: false
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'fieldset',
                                            title: 'Asignación de Subred',
                                            collapsible: false,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch',
                                                        type: 'hbox'
                                                    },
                                                    items: [
                                                        {width: '30%', border: false},
                                                        {width: '5%', border: false},
                                                        {
                                                            id: 'cbxSubred',
                                                            name: 'cbxSubred',
                                                            xtype: 'combobox',
                                                            fieldLabel: '<b>Subred Vsat</b>',
                                                            store: storeMascaras,
                                                            queryMode: 'local',
                                                            displayField: 'subred',
                                                            valueField: 'idSubred',
                                                            width: '20%',
                                                            labelAlign: 'top',
                                                            editable: false
                                                        },
                                                        {width: '30%', border: false}
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }// cierre informacion tecnica
                    ],
                    buttons:
                            [{
                                    id: 'btnGuardar',
                                    text: 'Grabar',
                                    formBind: true,
                                    handler: function() {
                                        var txtVlan                     = Ext.getCmp('txtVlan');
                                        var cbxVrf                      = Ext.getCmp('cbxVrf');
                                        var cbxSubred                   = Ext.getCmp('cbxSubred');                                                                                
                                        var txtNombreInterfaceElemento  = Ext.getCmp('txtNombreInterfaceElemento');
                                        // =============================================================
                                        // Validaciones de los datos requeridos
                                        // =============================================================                                        
                                        if (cbxSubred.getValue() === "0")
                                        {
                                            cbxSubred.markInvalid('Seleccione la subred');
                                            return;
                                        }
                                        if (cbxVrf.getValue() === "0")
                                        {
                                            cbxVrf.markInvalid('Seleccione la VRF');
                                            return;
                                        }

                                        Ext.get(formPanel.getId()).mask('Guardando datos!');
                                        Ext.getCmp('btnGuardar').disable();

                                        Ext.Ajax.request({
                                            url: asignarRecursosInternetMPLS,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: {
                                                idServicio:                 data.get('id_servicio'),
                                                idDetalleSolicitud:         data.get('id_factibilidad'),
                                                tipoSolicitud:              data.get('descripcionSolicitud'),
                                                vlan:                       txtVlan.getValue(),
                                                vrf:                        cbxVrf.getValue(),
                                                subred:                     cbxSubred.getValue(),
                                                tipoSubred:                 null,
                                                idElementoPadre:            json.idElemento,
                                                hiloSeleccionado:           idInterfaceElementoOutAsignado,
                                                numeroColorHiloSeleccionado:numeroColorHiloSeleccionado,
                                                anillo                     : anillo,
                                                nombreInterfaceElemento:    txtNombreInterfaceElemento.getValue(),
                                                nombreElemento:             nombreElemento,
                                                nombreElementoConector:     nombreElementoConector,
                                                ultimaMilla:                data.get('ultimaMilla'),
                                                esPseudoPe:                 esPseudoPe
                                            },
                                            success: function(response) {
                                                Ext.get(formPanel.getId()).unmask();
                                                if (response.responseText === "OK")
                                                {
                                                    Ext.Msg.show({
                                                        title: 'Información',
                                                        msg: 'Se Asignaron los Recursos de Red!',
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.INFO,
                                                        fn: function(btn, text) {
                                                            if (btn === 'ok') {
                                                                win.destroy();
                                                                store.load();
                                                            }
                                                        }
                                                    });
                                                }
                                                else {
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.getCmp('btnGuardar').enable();
                                                    Ext.Msg.show({
                                                        title: 'Error',
                                                        msg: response.responseText,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.getCmp('btnGuardar').enable();
                                                Ext.Msg.show({
                                                    title: 'Error',
                                                    msg: 'Error: ' + result.statusText,
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }
                                        });
                                    }// Fin de Funcionalidad del Boton Guardar
                                },
                                {
                                    text: 'Cancelar',
                                    handler: function()
                                    {
                                        win.destroy();
                                    }
                                }]
                });
                               
                var descripcion = '';
                
                if(data.get('ultimaMilla') === 'SATELITAL')
                {
                    descripcion = ' ( VSAT )';
                }

                var win = Ext.create('Ext.window.Window', {
                    title: 'Asignar Recurso de Red - Internet MPLS '+descripcion,
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
                
                storeVrfInternet.load({
                    callback: function(){
                        storeVrfInternet.insert(0, {id: '0', valor: 'Seleccione...'});
                        Ext.getCmp('cbxVrf').setValue('0');
                        
                        storeMascaras.insert(0, {idSubred: '0', subred: 'Seleccione...'});
                        Ext.getCmp('cbxSubred').setValue('0');
                    }
                });
            }// if(json.status=="OK")
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