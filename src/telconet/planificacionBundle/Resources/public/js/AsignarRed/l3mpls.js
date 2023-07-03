/**
 * Funcion que sirve para mostrar la pantalla para la asignacion 
 * de recursos de red para L3mpls
 * 
 * @author Kenneth Jimenez Pluas <kjimenez@telconet.ec>
 * @param {type} data
 * @version 1.0 28-03-2016
 * */
function showRecursoRedL3mpls(data)
{
    var strTipoRed = "";
    if (typeof data.get('strTipoRed') !== "undefined"){
        strTipoRed = data.get('strTipoRed');
    }
    Ext.get(grid.getId()).mask('Consultando Datos...');
    var tituloElementoConector = "Nombre Elemento Conector";
    if (data.get('ultimaMilla')=="Radio")
    {
        tituloElementoConector = "Nombre Elemento Radio";
    }
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
            success: function(response){
                Ext.get(grid.getId()).unmask();
                
                var json = Ext.JSON.decode(response.responseText);
                
                var esInterconexion = json.esInterconexion==='SI';
                
                if(json.status==="OK")
                {
                    storeProtocolosEnrutamiento = new Ext.data.Store({
                        proxy: {
                            type: 'ajax',
                            url: urlAjaxGetProtocolosEnrutamiento,
                            timeout: 600000,
                            reader: {
                                type: 'json',
                                root: 'data'
                            },                            
                        },
                        fields:
                            [
                                {name: 'descripcion', mapping: 'descripcion'}
                            ]
                    });
                    
                    storeHilosDisponibles = new Ext.data.Store({ 
                        pageSize: 100,
                        proxy: {
                            type: 'ajax',
                            url : getHilosDisponibles,
                            timeout: 600000,
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
                        listeners: {
                            load: function(store,records,options) {
                                if(store.totalCount===0)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "No tiene asignado Vrfs para el servicio [ " + json.nombreElementoPadre + " ]",
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
                                idServicio:           data.get('id_servicio'),
                                strTipoRed:           strTipoRed
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
                    
                    storeVlansDisponibles = new Ext.data.Store({
                        total: 'total',
                        listeners: {
                            load: function(store,records,options) {
                                if(store.totalCount===0)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + json.nombreElementoPadre + " ] del switch [ "+ json.nombreElemento +" ]",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        },
                        proxy: {
                            type: 'ajax',
                            url: urlAjaxGetVlansDisponibles,
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
                                nombreElemento: json.nombreElementoPadre,
                                idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                                anillo: json.anillo
                            }
                        },
                        fields:
                            [
                                {name: 'id'  , mapping: 'id'},
                                {name: 'vlan', mapping: 'vlan'}
                            ]
                    });
                    
                    storeVlansDisponiblesPorVrf = new Ext.data.Store({
                        total: 'total',
                        listeners: {
                            load: function(store,records,options) {
                                if(store.totalCount===0)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + json.nombreElementoPadre + 
                                             " ] del switch [ "+ json.nombreElemento +"]",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
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
                                nombreElemento: json.nombreElementoPadre,
                                idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                                anillo: json.anillo
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
                                idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                                nombreTecnico : data.get('nombreTecnico'),
                                tipoEnlace: data.get('tipo_enlace'),
                                anillo: json.anillo,
                                idServicio:data.get('id_servicio')
                            }
                        },
                        fields:
                            [
                                {name: 'id'  , mapping: 'id'},
                                {name: 'subred', mapping: 'subred'},
                                {name: 'idServicio', mapping: 'idServicio'}
                            ]
                    });   
                    
                    
                    if (data.get('strDescripcion') == 'CANAL TELEFONIA')
                    {
                        storeMascaras = new Ext.data.Store({
                            fields: ['display', 'value'],
                            data: [
                                {
                                    "display": "/24",
                                    "value": "255.255.255.0"
                                }
                            ]
                        });
                    }
                    //Cuando se trata de flujo de Interconexion para asignacion de recursos de red de concentradores
                    else if(esInterconexion)
                    {
                        storeMascaras = new Ext.data.Store({
                            fields: ['display','value'],
                            data: [
                                {
                                    "display": "/31",
                                    "value": "255.255.255.254"
                                }
                            ]
                        });
                    }
                    else
                    {
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
                    }
                    
                    
                    Ext.Ajax.request({
                        url: urlAjaxGetAsPrivado,
                        timeout: 600000,
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
                                            {
                                                xtype: 'textfield',
                                                name: 'tipoRed',
                                                fieldLabel: 'Tipo Red',
                                                value: strTipoRed,
                                                readOnly: true,
                                                width: '30%'
                                            },
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
                                                            
                                                            Ext.getCmp('vlansDisponibles').setDisabled(true);
                                                            Ext.getCmp('vrfsDisponibles').setDisabled(false);
                                                            Ext.getCmp('protocolosEnrutamiento').setDisabled(false);
                                                           
                                                            Ext.getCmp('asPrivado').setDisabled(false);
                                                            Ext.getCmp('subredesDisponibles').setDisabled(true);
                                                            Ext.getCmp('defaultGateway').setVisible(false);
                                                            
                                                            if(esInterconexion)
                                                            {
                                                                Ext.getCmp('mascaras').setRawValue("/31");
                                                                Ext.getCmp('mascaras').setValue("255.255.255.254");
                                                            }
                                                            else
                                                            {
                                                                Ext.getCmp('mascaras').setDisabled(false);
                                                            }
                                                            
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
                                                {
                                                    xtype:          'textfield',
                                                    id:             'anillo',
                                                    name:           'anillo',
                                                    fieldLabel:     'Anillo',
                                                    readOnly:       true,
                                                    displayField:   json.anillo,
                                                    value:          json.anillo,
                                                    width:          300
                                                },
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
                                                    fieldLabel:     tituloElementoConector,
                                                    displayField:   json.nombreElementoConector,
                                                    value:          json.nombreElementoConector,
                                                    readOnly:       true,
                                                    width    :      600
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    queryMode:      'local',
                                                    xtype:          'combobox',
                                                    id:             'hilosDisponibles',
                                                    name:           'hilosDisponibles',
                                                    fieldLabel:     'Hilos Disponibles',
                                                    displayField:   'numeroColorHilo',
                                                    valueField:     'idInterfaceElementoOut',
                                                    value:          json.numeroColorHilo,
                                                    loadingText:    'Buscando...',
                                                    store:          storeHilosDisponibles,
                                                    readOnly:       true,
                                                    width: '25%',
                                                    listeners: 
                                                    {   
                                                        select: function(combo)
                                                        {
                                                            var objeto = combo.valueModels[0].raw;
                                                            Ext.Ajax.request
                                                            ({
                                                                url: ajaxGetPuertoSwitchByHilo,
                                                                timeout: 600000,
                                                                method: 'post',
                                                                params: { idInterfaceElementoConector : objeto.idInterfaceElemento },
                                                                success: function(response)
                                                                {
                                                                    var objJson    = Ext.JSON.decode(response.responseText);
                                                                    if(objJson.status === "ERROR"){
                                                                        Ext.getCmp('nombreInterfaceElemento').setValue(null);
                                                                        Ext.getCmp('nombreInterfaceElemento').setRawValue(null);
                                                                        Ext.Msg.alert('Error',objJson.msg, function(btn){
                                                                        
                                                                        });
                                                                    }else {
                                                                        Ext.getCmp('nombreInterfaceElemento').setValue = objJson.idInterfaceElemento;
                                                                        Ext.getCmp('nombreInterfaceElemento').setRawValue(objJson.nombreInterfaceElemento);
                                                                    }
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.Msg.alert('Error',result.statusText, function(btn){
                                                                        if(btn==='ok')
                                                                        {
                                                                            win.destroy();
                                                                        }
                                                                    });
                                                                }
                                                            });
                                                        }
                                                    }
                                                }
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
                                                    id:             'vrfsDisponibles',
                                                    name:           'vrfsDisponibles',
                                                    fieldLabel:     'Vrf',
                                                    displayField:   'vrf',
                                                    valueField:     'id_vrf',
                                                    store:          storeVrfsDisponibles,
                                                    width:          270,
                                                    listeners: {
                                                        select: function(combo){
                                                            Ext.getCmp('vlansDisponibles').reset();
                                                            Ext.getCmp('vlansDisponibles').setDisabled(true);
                                                            Ext.getCmp('vlansDisponibles').value = "loading..";
                                                            Ext.getCmp('vlansDisponibles').setRawValue("loading..");
                                                            storeVlansDisponiblesPorVrf.proxy.extraParams = {
                                                                idPersonaEmpresaRol: data.get('id_persona_empresa_rol'),
                                                                idServicio: data.get('id_servicio'),
                                                                anillo: json.anillo,
                                                                idVrf: combo.getValue(),
                                                                nombreElemento: json.nombreElementoPadre
                                                            };
                                                            storeVlansDisponiblesPorVrf.load({callback: function () {
                                                                                              Ext.getCmp('vlansDisponibles').setDisabled(false);
                                                                                            }});
                                                        }
                                                    }
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
                                                            Ext.Ajax.request({
                                                                url: urlAjaxGetInfoBackboneSubredL3mpls,
                                                                timeout: 600000,
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
                            handler: function(){
                                Ext.Msg.confirm('Mensaje','Esta seguro de asignar los recursos de red ?', function(btn){
                                    if(btn==='yes')
                                    {  
                                        var hilo           = Ext.getCmp('hilosDisponibles').getValue();
                                        var vlan           = Ext.getCmp('vlansDisponibles').getValue();
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
                                                idElementoPadre:        json.idElementoPadre,
                                                hilo:                   hilo,
                                                vlan:                   vlan,
                                                vrf:                    vrf,
                                                protocolo:              protocolo,
                                                defaultGateway:         defaultGateway,
                                                asPrivado:              asPrivado,
                                                mascara:                mascara,
                                                idSubred:               subred,
                                                flagRecursos:           flagRecursos,
                                                ultimaMilla:            data.get('ultimaMilla')
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
                        title: 'Asignar Recurso de Red - L3MPLS',
                        modal: true,
                        width: 1100,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();
                    
                    Ext.get(formPanel.getId()).mask('Cargando datos...');
                    //Se cambia el orden para consultar los protocolos de enrutamiento
                    storeProtocolosEnrutamiento.load({});
                    storeVrfsDisponibles.load({
                        callback: function() {
                            storeSubredesL3mplsDisponibles.load({
                                callback: function() {
                                    Ext.get(formPanel.getId()).unmask();
                                    storeHilosDisponibles.load({});
                                }
                            });
                        }
                    });

                    if (data.get('ultimaMilla')=="Radio")
                    {
                        Ext.getCmp('hilosDisponibles').setDisabled(true);
                        Ext.getCmp('nombreElementoContenedor').setDisabled(true);
                    }
                    
                    if (data.get('ultimaMilla')=="UTP")
                    {
                        Ext.getCmp('hilosDisponibles').setVisible(false);
                        Ext.getCmp('nombreElementoContenedor').setVisible(false);
                        Ext.getCmp('nombreElementoConector').setVisible(false);
                    }                                        

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

/**
 * Funcion que sirve para mostrar la pantalla para la asignacion
 * de recursos de red para L3mpls en
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @param {type} rec
 * @version 1.0 15-10-2019
 * */
function showRecursosRedL3mplsFttx(rec)
{
    var strTipoRed = "";
    if (typeof rec.get('strTipoRed') !== "undefined"){
        strTipoRed = rec.get('strTipoRed');
    }
    Ext.get(grid.getId()).mask('Consultando Datos...');

    objFieldStyle = {
        'backgroundColor': '#F0F2F2',
        'backgrodunImage': 'none',
        'color': 'green'
    };

    Ext.Ajax.request({
        url: getDatosFactibilidad,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: rec.get('id_servicio'),
            ultimaMilla: rec.get('ultimaMilla'),
            tipoSolicitud: rec.get('descripcionSolicitud'),
            idSolicitud  : rec.get('id_factibilidad')
        },
        success: function(response){
            Ext.get(grid.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);

            var esInterconexion = json.esInterconexion==='SI';

            if(json.status==="OK")
            {
                storeProtocolosEnrutamiento = new Ext.data.Store({
                    proxy: {
                        type: 'ajax',
                        url: urlAjaxGetProtocolosEnrutamiento,
                        timeout: 600000,
                        extraParams: {
                            nombreTecnico: rec.get('nombreTecnico'),
                            strTipo:       'Asignar',
                            strTipoRed:    strTipoRed
                        },
                        reader: {
                            type: 'json',
                            root: 'data'
                        },
                    },
                    fields:
                        [
                            {name: 'descripcion', mapping: 'descripcion'}
                        ]
                });

                storeHilosDisponibles = new Ext.data.Store({
                    pageSize: 100,
                    proxy: {
                        type: 'ajax',
                        url : getHilosDisponibles,
                        timeout: 600000,
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
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "No tiene asignado Vrfs para el servicio [ " + json.nombreElementoPadre + " ]",
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
                            idPersonaEmpresaRol : rec.get('id_persona_empresa_rol'),
                            strTipoRed:           strTipoRed
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

                storeVlansDisponibles = new Ext.data.Store({
                    total: 'total',
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + json.nombreElementoPadre + " ] del OLT [ "+ json.nombreElemento +" ]",
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
                            nombreElemento: json.nombreElementoPadre,
                            idPersonaEmpresaRol : rec.get('id_persona_empresa_rol'),
                            anillo: json.anillo,
                            strTipoRed: strTipoRed
                        }
                    },
                    fields:
                        [
                            {name: 'id'  , mapping: 'id'},
                            {name: 'vlan', mapping: 'vlan'}
                        ]
                });

                storeVlansDisponiblesPorVrf = new Ext.data.Store({
                    total: 'total',
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + json.nombreElementoPadre +
                                        " ] del OLT [ "+ json.nombreElemento +"]",
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
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
                            nombreElemento: json.nombreElementoPadre,
                            idPersonaEmpresaRol : rec.get('id_persona_empresa_rol'),
                            anillo: json.anillo,
                            strTipoRed: strTipoRed
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
                            idPersonaEmpresaRol : rec.get('id_persona_empresa_rol'),
                            nombreTecnico : rec.get('nombreTecnico'),
                            tipoEnlace: rec.get('tipo_enlace'),
                            anillo: json.anillo,
                            idServicio:rec.get('id_servicio'),
                            strTipoRed:strTipoRed
                        }
                    },
                    fields:
                        [
                            {name: 'id'  , mapping: 'id'},
                            {name: 'subred', mapping: 'subred'},
                            {name: 'idServicio', mapping: 'idServicio'}
                        ]
                });


                if (rec.get('strDescripcion') == 'CANAL TELEFONIA')
                {
                    storeMascaras = new Ext.data.Store({
                        fields: ['display', 'value'],
                        data: [
                            {
                                "display": "/24",
                                "value": "255.255.255.0"
                            }
                        ]
                    });
                }
                //Cuando se trata de flujo de Interconexion para asignacion de recursos de red de concentradores
                else if(esInterconexion)
                {
                    storeMascaras = new Ext.data.Store({
                        fields: ['display','value'],
                        data: [
                            {
                                "display": "/31",
                                "value": "255.255.255.254"
                            }
                        ]
                    });
                }
                else
                {
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
                }


                Ext.Ajax.request({
                    url: urlAjaxGetAsPrivado,
                    timeout: 600000,
                    method: 'post',
                    params: { idPersonaEmpresaRol : rec.get('id_persona_empresa_rol') },
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
                                            displayField: rec.get('cliente'),
                                            value: rec.get('cliente'),
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                         background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'ciudad',
                                            fieldLabel: 'Ciudad',
                                            displayField: rec.get('ciudad'),
                                            value: rec.get('ciudad'),
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                         background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '10%', border: false},
                                        //----------------------------------//
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: rec.get('login2'),
                                            value: rec.get('login2'),
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                         background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },

                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'esRecontratacion',
                                            fieldLabel: 'Es Recontratacion',
                                            displayField: rec.get("esRecontratacion"),
                                            value: rec.get("esRecontratacion"),
                                            readOnly: true,
                                            width: '35%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                         background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },

                                        { width: '10%', border: false},
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textarea',
                                            name: 'direccion',
                                            fieldLabel: 'Dirección',
                                            displayField: rec.get('direccion'),
                                            value: rec.get('direccion'),
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                         background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },

                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'sector',
                                            fieldLabel: 'Sector',
                                            displayField: rec.get('nombreSector'),
                                            value: rec.get('nombreSector'),
                                            readOnly: true,
                                            width: '35%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '10%', border: false},
                                        {   width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: rec.get("producto"),
                                            value: rec.get("producto"),
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoOrden',
                                            fieldLabel: 'Tipo Orden',
                                            displayField: rec.get("tipo_orden"),
                                            value: rec.get("tipo_orden"),
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
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
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: json.capacidad2,
                                            value: json.capacidad2,
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '10%', border: false},

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoEnlace',
                                            fieldLabel: 'Tipo Enlace',
                                            displayField: rec.get('tipo_enlace'),
                                            value: rec.get('tipo_enlace'),
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoRed',
                                            fieldLabel: 'Tipo Red',
                                            value: strTipoRed,
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        // { width: '15%', border: false},
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
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'idLoginConcentrador',
                                            fieldLabel: 'Login',
                                            value: json.concentrador.login,
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
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
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'idLoginAuxConcentrador',
                                            fieldLabel: 'Login Aux',
                                            value: json.concentrador.login_aux,
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
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
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'idCapacidadDosConcentrador',
                                            fieldLabel: 'Capacidad Dos',
                                            value: json.concentrador.capacidadDos,
                                            readOnly: true,
                                            width: '30%',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                            fieldCls: 'details-disabled'
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

                                                            Ext.getCmp('vlansDisponibles').setDisabled(true);
                                                            Ext.getCmp('vrfsDisponibles').setDisabled(false);
                                                            Ext.getCmp('protocolosEnrutamiento').setDisabled(false);

                                                            Ext.getCmp('asPrivado').setDisabled(false);
                                                            Ext.getCmp('subredesDisponibles').setDisabled(true);
                                                            Ext.getCmp('defaultGateway').setVisible(false);

                                                            if(esInterconexion)
                                                            {
                                                                Ext.getCmp('mascaras').setRawValue("/31");
                                                                Ext.getCmp('mascaras').setValue("255.255.255.254");
                                                            }
                                                            else
                                                            {
                                                                Ext.getCmp('mascaras').setDisabled(false);
                                                            }

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
                                                columns: 4,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {   width: 25,border: false   },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'PE',
                                                    name: 'txt_pe',
                                                    id: 'txt_pe',
                                                    value: json.nombreElementoPadre,
                                                    allowBlank: false,
                                                    readOnly: true,
                                                    fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                                    fieldCls: 'details-disabled'
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'OLT',
                                                    name: 'txt_olt',
                                                    id: 'txt_olt',
                                                    value: rec.get("pop"),
                                                    allowBlank: false,
                                                    readOnly: true,
                                                    fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                                    fieldCls: 'details-disabled'
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Ultima Milla',
                                                    name: 'txt_um',
                                                    id: 'txt_um',
                                                    value: rec.get("ultimaMilla"),
                                                    allowBlank: false,
                                                    readOnly: true,
                                                    fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                                    fieldCls: 'details-disabled'
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Linea',
                                                    name: 'txt_linea',
                                                    id: 'txt_linea',
                                                    value: rec.get("intElemento"),
                                                    allowBlank: false,
                                                    readOnly: true,
                                                    fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                                    fieldCls: 'details-disabled'
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Caja',
                                                    name: 'txt_caja',
                                                    width: 450,
                                                    id: 'txt_caja',
                                                    value: rec.get("caja"),
                                                    allowBlank: false,
                                                    readOnly: true,
                                                    fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; 
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                                    fieldCls: 'details-disabled'
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Splitter',
                                                    name: 'txt_splitter',
                                                    width: 470,
                                                    id: 'txt_splitter',
                                                    value: rec.get("splitter"),
                                                    allowBlank: false,
                                                    readOnly: true,
                                                    fieldStyle: `background-color: ${objFieldStyle.backgroundColor};
                                                    background-image: ${objFieldStyle.backgrodunImage};`,
                                                    fieldCls: 'details-disabled'
                                                }
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
                                                    id:             'vrfsDisponibles',
                                                    name:           'vrfsDisponibles',
                                                    fieldLabel:     'Vrf',
                                                    displayField:   'vrf',
                                                    valueField:     'id_vrf',
                                                    store:          storeVrfsDisponibles,
                                                    width:          270,
                                                    listeners: {
                                                        select: function(combo){
                                                            Ext.getCmp('vlansDisponibles').reset();
                                                            Ext.getCmp('vlansDisponibles').setDisabled(true);
                                                            Ext.getCmp('vlansDisponibles').value = "loading..";
                                                            Ext.getCmp('vlansDisponibles').setRawValue("loading..");
                                                            storeVlansDisponiblesPorVrf.proxy.extraParams = {
                                                                idPersonaEmpresaRol: rec.get('id_persona_empresa_rol'),
                                                                idServicio: rec.get('id_servicio'),
                                                                anillo: json.anillo,
                                                                idVrf: combo.getValue(),
                                                                nombreElemento: json.nombreElementoPadre,
                                                                strTipoRed: strTipoRed
                                                            };
                                                            storeVlansDisponiblesPorVrf.load({callback: function () {
                                                                    Ext.getCmp('vlansDisponibles').setDisabled(false);
                                                                }});
                                                        }
                                                    }
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
                                                            Ext.Ajax.request({
                                                                url: urlAjaxGetInfoBackboneSubredL3mpls,
                                                                timeout: 600000,
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
                            handler: function(){
                                Ext.Msg.confirm('Mensaje','Esta seguro de asignar los recursos de red ?', function(btn){
                                    if(btn==='yes')
                                    {
                                        var vlan           = Ext.getCmp('vlansDisponibles').getValue();
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
                                                idPersonaEmpresaRol:    rec.get('id_persona_empresa_rol') ,
                                                idServicio:             rec.get('id_servicio'),
                                                idDetalleSolicitud:     rec.get('id_factibilidad'),
                                                tipoSolicitud:          rec.get('descripcionSolicitud'),
                                                idElementoPadre:        json.idElementoPadre,
                                                vlan:                   vlan,
                                                vrf:                    vrf,
                                                protocolo:              protocolo,
                                                defaultGateway:         defaultGateway,
                                                asPrivado:              asPrivado,
                                                mascara:                mascara,
                                                idSubred:               subred,
                                                flagRecursos:           flagRecursos,
                                                ultimaMilla:            rec.get('ultimaMilla')
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
                    title: 'Asignar Recurso de Red - L3MPLS',
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

                Ext.get(formPanel.getId()).mask('Cargando datos...');
                //Se cambia el orden para consultar los protocolos de enrutamiento
                storeProtocolosEnrutamiento.load({});
                storeVrfsDisponibles.load({
                    callback: function() {
                        storeSubredesL3mplsDisponibles.load({
                            callback: function() {
                                Ext.get(formPanel.getId()).unmask();
                                storeHilosDisponibles.load({});
                            }
                        });
                    }
                });

                if (rec.get('ultimaMilla')=="Radio")
                {
                    Ext.getCmp('hilosDisponibles').setDisabled(true);
                    Ext.getCmp('nombreElementoContenedor').setDisabled(true);
                }

                if (rec.get('ultimaMilla')=="UTP")
                {
                    Ext.getCmp('hilosDisponibles').setVisible(false);
                    Ext.getCmp('nombreElementoContenedor').setVisible(false);
                    Ext.getCmp('nombreElementoConector').setVisible(false);
                }

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

/**
 * Funcion que sirve para mostrar la pantalla para la asignacion
 * de recursos de red para los servicios SafeCity
 *
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.0 30-06-2021
 *
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.1 18-10-2021 - Se agrega validación para la asignación de servicios Wifi SafeCity
 **/
function showRecursoRedServiciosSafeCity(data)
{
    var strTipoRed = "";
    if (typeof data.get('strTipoRed') !== "undefined"){
        strTipoRed = data.get('strTipoRed');
    }
    var nombreElemento                 = "";
    var nombreElementoConector         = "";
    Ext.get(grid.getId()).mask('Consultando Datos...');
    var tituloElementoConector = "Elemento Conector";

    Ext.Ajax.request({
        url: getDatosFactibilidad,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.get('id_servicio'),
            ultimaMilla: data.get('ultimaMilla'),
            tipoSolicitud: data.get('descripcionSolicitud'),
            idSolicitud  : data.get('id_factibilidad'),
            tipoRed : data.get('strTipoRed')
        },
        success: function(response) {
            Ext.get(grid.getId()).unmask();
            var json = Ext.JSON.decode(response.responseText);
            nombreElemento                 = json.nombreElemento;
            nombreElementoConector         = json.nombreElementoConector;

            //-------------------------------------------------------------------------------------------
            if(json.status=="OK")
            {
                storeVrfsDisponibles = new Ext.data.Store({
                    total: 'total',
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "No tiene asignado Vrfs para el servicio [ " + json.nombreElementoPadre + " ]",
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
                            idServicio:           data.get('id_servicio'),
                            strTipoRed:           strTipoRed
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
                storeVlansDisponiblesPorVrf = new Ext.data.Store({
                    total: 'total',
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + json.nombreElementoPadre +
                                        " ] del OLT [ "+ json.nombreElemento +"]",
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
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
                            nombreElemento: json.nombreElementoPadre,
                            idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                            idServicio: data.get('id_servicio'),
                            anillo: json.anillo,
                            strTipoRed: strTipoRed
                        }
                    },
                    fields:
                        [
                            {name: 'id'  , mapping: 'id'},
                            {name: 'vlan', mapping: 'vlan'}
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
                                            displayField: data.get('capacidad1'),
                                            value: data.get('capacidad1'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: data.get('capacidad2'),
                                            value: data.get('capacidad2'),
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
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoRed',
                                            fieldLabel: 'Tipo de Red',
                                            displayField: data.get('strTipoRed'),
                                            value: data.get('strTipoRed'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        { width: '10%', border: false}
                                    ]
                                }
                            ]
                        },
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
                        },
                        //informacion tecnica (generada en la factibilidad)
                        {
                            colspan: data.get("booleanWifiSafeCity") ? 5 : 3,
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
                                                            fieldLabel: 'Nombre',
                                                            displayField: json.nombreElementoPadre,
                                                            value: json.nombreElementoPadre,
                                                            readOnly: true,
                                                            width: data.get("booleanWifiSafeCity") ? '25%' : '40%',
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
                                                            fieldLabel: 'VRF',
                                                            width: '30%',
                                                            hidden: data.get("booleanWifiSafeCity") || data.get("booleanCamVpnSafeCity"),
                                                            disabled: data.get("booleanWifiSafeCity") || data.get("booleanCamVpnSafeCity"),
                                                            labelAlign: 'top',
                                                            queryMode: 'local',
                                                            editable: false,
                                                            allowBlank: false,
                                                            forceSelection: true,
                                                            store: storeVrfsDisponibles,
                                                            valueField: 'id_vrf',
                                                            displayField: 'vrf',
                                                            listeners: {
                                                                select: function(combo){
                                                                    Ext.getCmp('cbxVlan').reset();
                                                                    Ext.getCmp('cbxVlan').setDisabled(true);
                                                                    Ext.getCmp('cbxVlan').value = "loading..";
                                                                    Ext.getCmp('cbxVlan').setRawValue("loading..");
                                                                    storeVlansDisponiblesPorVrf.proxy.extraParams = {
                                                                        idPersonaEmpresaRol: data.get('id_persona_empresa_rol'),
                                                                        idServicio: data.get('id_servicio'),
                                                                        anillo: json.anillo,
                                                                        idVrf: combo.getValue(),
                                                                        nombreElemento: json.nombreElementoPadre,
                                                                        strTipoRed: strTipoRed
                                                                    };
                                                                    storeVlansDisponiblesPorVrf.load({callback: function () {
                                                                        Ext.getCmp('cbxVlan').setDisabled(false);
                                                                    }});
                                                                }
                                                            }
                                                        },
                                                        {
                                                            id: 'cbxVrfSsid',
                                                            name: 'cbxVrfSsid',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'VRF SSID',
                                                            displayField: data.get("strVrfCamaraGpon"),
                                                            value: data.get("strVrfCamaraGpon"),
                                                            hidden: !data.get("booleanWifiSafeCity"),
                                                            readOnly: true,
                                                            width: '15%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            xtype: 'combobox',
                                                            id: 'cbxVlan',
                                                            name: 'cbxVlan',
                                                            fieldLabel: 'Vlan',
                                                            hidden: data.get("booleanWifiSafeCity") || data.get("booleanCamVpnSafeCity"),
                                                            disabled: data.get("booleanWifiSafeCity") || data.get("booleanCamVpnSafeCity"),
                                                            width: '15%',
                                                            labelAlign: 'top',
                                                            queryMode: 'local',
                                                            editable: false,
                                                            allowBlank: false,
                                                            forceSelection: true,
                                                            store: storeVlansDisponiblesPorVrf,
                                                            valueField: 'id',
                                                            displayField: 'vlan'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtVlanSsid',
                                                            name: 'txtVlanSsid',
                                                            fieldLabel: 'Vlan SSID',
                                                            displayField: data.get("strVlanCamaraGpon"),
                                                            value: data.get("strVlanCamaraGpon"),
                                                            readOnly: true,
                                                            hidden: !data.get("booleanWifiSafeCity"),
                                                            width: '8%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            hidden: !data.get("booleanWifiSafeCity"),
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            id: 'cbxVrfAdmin',
                                                            name: 'cbxVrfAdmin',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'VRF ADMIN',
                                                            displayField: data.get("strVrfAdminGpon"),
                                                            value: data.get("strVrfAdminGpon"),
                                                            readOnly: true,
                                                            hidden: !data.get("booleanWifiSafeCity"),
                                                            width: '15%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            hidden: !data.get("booleanWifiSafeCity"),
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtVlanAdmin',
                                                            name: 'txtVlanAdmin',
                                                            fieldLabel: 'Vlan ADMIN',
                                                            displayField: data.get("strVlanAdminGpon"),
                                                            value: data.get("strVlanAdminGpon"),
                                                            readOnly: true,
                                                            hidden: !data.get("booleanWifiSafeCity"),
                                                            width: '8%',
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
                                                    flex: 10,
                                                    xtype: 'fieldset',
                                                    height: "auto",
                                                    title: 'Elemento',
                                                    collapsible: false,
                                                    defaultType: 'textfield',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreElemento',
                                                            name: 'txtNombreElemento',
                                                            fieldLabel: 'Nombre',
                                                            displayField: nombreElemento,
                                                            value: nombreElemento,
                                                            readOnly: true,
                                                            width: '100%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreInterfaceElemento',
                                                            name: 'txtNombreInterfaceElemento',
                                                            fieldLabel: 'Interface',
                                                            displayField: data.get('intElemento'),
                                                            value: data.get('intElemento'),
                                                            readOnly: true,
                                                            width: '50%',
                                                            labelAlign: 'top'
                                                        }
                                                    ]
                                                },
                                                {
                                                    flex: 1,
                                                    border: false
                                                },
                                                // ===========================================
                                                // Elemento Conectaro Asignado
                                                // ===========================================
                                                {
                                                    flex: 10,
                                                    xtype: 'fieldset',
                                                    height: "auto",
                                                    title: tituloElementoConector,
                                                    collapsible: false,
                                                    defaultType: 'textfield',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreElementoConector',
                                                            name: 'txtNombreElementoConector',
                                                            fieldLabel: 'Nombre',
                                                            displayField: nombreElementoConector,
                                                            value: nombreElementoConector,
                                                            readOnly: true,
                                                            width: '100%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'container',
                                                            layout: {
                                                                align: 'stretch',
                                                                type: 'hbox'
                                                            },
                                                            items: [
                                                                {
                                                                    id: 'interfaceElementoConector',
                                                                    name: 'interfaceElementoConector',
                                                                    xtype: 'textfield',
                                                                    fieldLabel: 'Interface',
                                                                    displayField: data.get('idInterfaceConector'),
                                                                    value: data.get('idInterfaceConector'),
                                                                    readOnly: true,
                                                                    width: '50%',
                                                                    labelAlign: 'top'
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    buttons:[
                        {
                            id: 'btnGuardar',
                            text: 'Grabar',
                            formBind: true,
                            handler: function() {
                                var intIdVrf  = null;
                                var intIdVlan = null;
                                if(data.get('nombreTecnico') == "SAFECITYDATOS")
                                {
                                    intIdVrf  = Ext.getCmp('cbxVrf').getValue();
                                    intIdVlan = Ext.getCmp('cbxVlan').getValue();
                                }
                                //verificar variables
                                if( (data.get('nombreTecnico') == "SAFECITYDATOS" && intIdVrf != null && intIdVlan != null)
                                    || data.get('nombreTecnico') == "SAFECITYWIFI" || data.get("booleanCamVpnSafeCity") )
                                {
                                    Ext.get(formPanel.getId()).mask('Guardando datos!');
                                    Ext.getCmp('btnGuardar').disable();
                                    Ext.Ajax.request({
                                        url: strUrlGuardaRecursosRedCamaraGpon,
                                        method: 'post',
                                        timeout: 1000000,
                                        params: {
                                            idServicio:         data.get('id_servicio'),
                                            idDetalleSolicitud: data.get('id_factibilidad'),
                                            tipoSolicitud:      data.get('descripcionSolicitud'),
                                            tipoRed:            data.get('strTipoRed'),
                                            strEsCamVpnGpon:    data.get("booleanCamVpnSafeCity") ? 'S' : 'N',
                                            intIdVrf:           intIdVrf,
                                            intIdVlan:          intIdVlan
                                        },
                                        success: function(response) {
                                            Ext.get(formPanel.getId()).unmask();
                                            var datosResponse = Ext.JSON.decode(response.responseText);
                                            if (datosResponse.status === "OK"){
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
                                                    msg: datosResponse.mensaje,
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
                                }
                                else if(intIdVrf == null)
                                {
                                    Ext.Msg.alert('Error','Se debe seleccionar la VRF del servicio', function(btn){
                                    });
                                }
                                else if(intIdVlan == null)
                                {
                                    Ext.Msg.alert('Error','Se debe seleccionar la VLAN del servicio', function(btn){
                                    });
                                }
                            }
                        },
                        {
                            text: 'Cancelar',
                            handler: function()
                            {
                                win.destroy();
                            }
                        }
                    ]
                });
                var win = Ext.create('Ext.window.Window', {
                    title: 'Asignar Recurso de Red - ' + data.get("producto"),
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
                if(data.get('nombreTecnico') == "SAFECITYDATOS")
                {
                    storeVrfsDisponibles.load({});
                }
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
        },
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

/**
 * Funcion que sirve para mostrar la pantalla para la asignacion 
 * de recursos de red para L3mpls SDWAN
 * 
 * @author Joel Muñoz <jrmunoz@telconet.ec>
 * @param {array} data
 * @version 1.0 01-12-2022
 * */
function showRecursoRedL3mplsMigracionSDWAN(data)
{
    var strTipoRed        = "";
    var strProductoSDWAN  = 'Asignar Recursos de Red' + (data.get('producto') ? ' - ' + data.get('producto') : '')
    if (typeof data.get('strTipoRed') !== "undefined"){
        strTipoRed = data.get('strTipoRed');
    }
    Ext.get(grid.getId()).mask('Consultando Datos...');
    var tituloElementoConector = "Nombre Elemento Conector";
    if (data.get('ultimaMilla')=="Radio")
    {
        tituloElementoConector = "Nombre Elemento Radio";
    }
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
            success: function(response){
                Ext.get(grid.getId()).unmask();
                
                var json = Ext.JSON.decode(response.responseText);
                
                var esInterconexion = json.esInterconexion==='SI';
                
                if(json.status==="OK")
                {
                    storeProtocolosEnrutamiento = new Ext.data.Store({
                        proxy: {
                            type: 'ajax',
                            url: urlAjaxGetProtocolosEnrutamiento,
                            timeout: 600000,
                            reader: {
                                type: 'json',
                                root: 'data'
                            },                            
                        },
                        fields:
                            [
                                {name: 'descripcion', mapping: 'descripcion'}
                            ]
                    });
                    
                    storeHilosDisponibles = new Ext.data.Store({ 
                        pageSize: 100,
                        proxy: {
                            type: 'ajax',
                            url : getHilosDisponibles,
                            timeout: 600000,
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
                        listeners: {
                            load: function(store,records,options) {
                                if(store.totalCount===0)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "No tiene asignado Vrfs para el servicio [ " + json.nombreElementoPadre + " ]",
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
                                idServicio:           data.get('id_servicio'),
                                strTipoRed:           strTipoRed
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
                    
                    storeVlansDisponibles = new Ext.data.Store({
                        total: 'total',
                        listeners: {
                            load: function(store,records,options) {
                                if(store.totalCount===0)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + json.nombreElementoPadre + " ] del switch [ "+ json.nombreElemento +" ]",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        },
                        proxy: {
                            type: 'ajax',
                            url: urlAjaxGetVlansDisponibles,
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
                                nombreElemento: json.nombreElementoPadre,
                                idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                                anillo: json.anillo
                            }
                        },
                        fields:
                            [
                                {name: 'id'  , mapping: 'id'},
                                {name: 'vlan', mapping: 'vlan'}
                            ]
                    });
                    
                    storeVlansDisponiblesPorVrf = new Ext.data.Store({
                        total: 'total',
                        listeners: {
                            load: function(store,records,options) {
                                if(store.totalCount===0)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Imposible asignar recursos de Red. Favor reservar Vlans para el Pe [ " + json.nombreElementoPadre + 
                                             " ] del switch [ "+ json.nombreElemento +"]",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
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
                                nombreElemento: json.nombreElementoPadre,
                                idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                                anillo: json.anillo
                            }
                        },
                        fields:
                            [
                                {name: 'id'  , mapping: 'id'},
                                {name: 'vlan', mapping: 'vlan'}
                            ]
                    });
                    
                    

                    
                    var storeSubredesL3mplsDisponibles = Ext.create('Ext.data.Store', {
                        fields: ['value', 'name'],
                        data: [
                            { "value": data.get("InfoMigracionSDWAN").objIp?.subredId, "name": data.get("InfoMigracionSDWAN").objIp?.subred },
                        ]
                    });
                    
                    
                    if (data.get('strDescripcion') == 'CANAL TELEFONIA')
                    {
                        storeMascaras = new Ext.data.Store({
                            fields: ['display', 'value'],
                            data: [
                                {
                                    "display": "/24",
                                    "value": "255.255.255.0"
                                }
                            ]
                        });
                    }
                    //Cuando se trata de flujo de Interconexion para asignacion de recursos de red de concentradores
                    else if(esInterconexion)
                    {
                        storeMascaras = new Ext.data.Store({
                            fields: ['display','value'],
                            data: [
                                {
                                    "display": "/31",
                                    "value": "255.255.255.254"
                                }
                            ]
                        });
                    }
                    else
                    {
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
                    }
                    
                    
                    Ext.Ajax.request({
                        url: urlAjaxGetAsPrivado,
                        timeout: 600000,
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
                                            {
                                                xtype: 'textfield',
                                                name: 'tipoRed',
                                                fieldLabel: 'Tipo Red',
                                                value: strTipoRed,
                                                readOnly: true,
                                                width: '30%'
                                            },
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
                                                disabled: true,
                                                id: 'rbRecursosNuevos', 
                                                name: 'rbRecursos', 
                                                inputValue: "nuevos"
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


                                                            Ext.getCmp('mismaVlan').setRawValue("Cargando...");
                                                            Ext.getCmp('mismaVrf').setRawValue("Cargando...");
                                                            Ext.getCmp('mismoProtocolo').setRawValue("Cargando...");
                                                            Ext.getCmp('mismoAsPrivado').setRawValue("Cargando...");
                                                            Ext.getCmp('btnGuardar').setDisabled(true);
                                                            


                                                    

                                                            Ext.Ajax.request({
                                                                url: urlAjaxGetInfoBackboneSubredL3mpls,
                                                                timeout: 600000,
                                                                method: 'post',
                                                                params: { idServicio : data.get("InfoMigracionSDWAN").idServicioPrincipal},
                                                                success: function(response) {

                                                                    var jsonDatosSubred = Ext.JSON.decode(response.responseText);
                                                                    
                                                                    Ext.getCmp('mismaVlan').setValue(jsonDatosSubred.vlan);
                                                                    Ext.getCmp('mismaVlanId').setValue(jsonDatosSubred.idVlan);
                                                                    Ext.getCmp('mismaVlan').setVisible(true);

                                                                    Ext.getCmp('mismaVrf').setValue(jsonDatosSubred.vrf);
                                                                    Ext.getCmp('mismaVrfId').setValue(jsonDatosSubred.idVrf);
                                                                    Ext.getCmp('mismaVrf').setVisible(true);
    
                                                                    Ext.getCmp('mismoProtocolo').setValue(jsonDatosSubred.protocolos);
                                                                    Ext.getCmp('mismoProtocolo').setVisible(true);
                                                                    Ext.getCmp('mismoAsPrivado').setValue(jsonDatosSubred.asPrivado);
                                                                    
                                                                    if(jsonDatosSubred.protocolo!=="STANDARD")
                                                                        Ext.getCmp('mismoAsPrivado').setVisible(true);
                                                                    else
                                                                        Ext.getCmp('mismoAsPrivado').setVisible(false);
                                                                    
                                                                    Ext.getCmp('protocolosEnrutamiento').setValue(jsonDatosSubred.protocolos);
                                                                    Ext.getCmp('btnGuardar').setDisabled(false);

                                                                    
                                                                },
                                                                failure: function(response)
                                                                {
                                                                    Ext.getCmp('mismaVlan').setVisible(false);
                                                                    Ext.getCmp('mismaVrf').setVisible(false);
                                                                    Ext.getCmp('mismoProtocolo').setVisible(false);
                                                                    Ext.getCmp('mismoAsPrivado').setVisible(false);
                                                                    
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
                                                {
                                                    xtype:          'textfield',
                                                    id:             'anillo',
                                                    name:           'anillo',
                                                    fieldLabel:     'Anillo',
                                                    readOnly:       true,
                                                    displayField:   json.anillo,
                                                    value:          json.anillo,
                                                    width:          300
                                                },
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
                                                    fieldLabel:     tituloElementoConector,
                                                    displayField:   json.nombreElementoConector,
                                                    value:          json.nombreElementoConector,
                                                    readOnly:       true,
                                                    width    :      600
                                                },
                                                {   width: 25,border: false   },
                                                {
                                                    queryMode:      'local',
                                                    xtype:          'combobox',
                                                    id:             'hilosDisponibles',
                                                    name:           'hilosDisponibles',
                                                    fieldLabel:     'Hilos Disponibles',
                                                    displayField:   'numeroColorHilo',
                                                    valueField:     'idInterfaceElementoOut',
                                                    value:          json.numeroColorHilo,
                                                    loadingText:    'Buscando...',
                                                    store:          storeHilosDisponibles,
                                                    readOnly:       true,
                                                    width: '25%',
                                                    listeners: 
                                                    {   
                                                        select: function(combo)
                                                        {
                                                            var objeto = combo.valueModels[0].raw;
                                                            Ext.Ajax.request
                                                            ({
                                                                url: ajaxGetPuertoSwitchByHilo,
                                                                timeout: 600000,
                                                                method: 'post',
                                                                params: { idInterfaceElementoConector : objeto.idInterfaceElemento },
                                                                success: function(response)
                                                                {
                                                                    var objJson    = Ext.JSON.decode(response.responseText);
                                                                    if(objJson.status === "ERROR"){
                                                                        Ext.getCmp('nombreInterfaceElemento').setValue(null);
                                                                        Ext.getCmp('nombreInterfaceElemento').setRawValue(null);
                                                                        Ext.Msg.alert('Error',objJson.msg, function(btn){
                                                                        
                                                                        });
                                                                    }else {
                                                                        Ext.getCmp('nombreInterfaceElemento').setValue = objJson.idInterfaceElemento;
                                                                        Ext.getCmp('nombreInterfaceElemento').setRawValue(objJson.nombreInterfaceElemento);
                                                                    }
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.Msg.alert('Error',result.statusText, function(btn){
                                                                        if(btn==='ok')
                                                                        {
                                                                            win.destroy();
                                                                        }
                                                                    });
                                                                }
                                                            });
                                                        }
                                                    }
                                                }
                                            ]
                                        }]
                                    },//containerRecursosObligatorios
                                        //container nuevos recursos
                                    {
                                        id: 'containerNuevosDatos',
                                        xtype: 'fieldset',
                                        title: 'Nuevos Recursos',
                                        hidden: true,
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
                                                    width:          270,
                                                    listeners: {
                                                        select: function(combo){
                                                            Ext.getCmp('vlansDisponibles').reset();
                                                            Ext.getCmp('vlansDisponibles').setDisabled(true);
                                                            Ext.getCmp('vlansDisponibles').value = "loading..";
                                                            Ext.getCmp('vlansDisponibles').setRawValue("loading..");
                                                            storeVlansDisponiblesPorVrf.proxy.extraParams = {
                                                                idPersonaEmpresaRol: data.get('id_persona_empresa_rol'),
                                                                idServicio: data.get('id_servicio'),
                                                                anillo: json.anillo,
                                                                idVrf: combo.getValue(),
                                                                nombreElemento: json.nombreElementoPadre
                                                            };
                                                            storeVlansDisponiblesPorVrf.load({callback: function () {
                                                                                                Ext.getCmp('vlansDisponibles').setDisabled(false);
                                                                                            }});
                                                        }
                                                    }
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
                                                displayField:   'name',
                                                valueField:     'value',
                                                allowBlank:      false,
                                                forceSelection:  true,
                                                store:          storeSubredesL3mplsDisponibles,
                                                width:          270,
                                                readOnly:       true,
                                                value:          data.get("InfoMigracionSDWAN").objIp?.subredId
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
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVrfId',
                                                name:           'mismaVrfId',
                                                fieldLabel:     'Vrf',
                                                readOnly:       true,
                                                hidden:         true,
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
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVlanId',
                                                name:           'mismaVlanId',
                                                fieldLabel:     'Vlan',
                                                readOnly:       true,
                                                hidden:         true,
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
                            id: 'btnGuardar',
                            text: 'Guardar',
                            formBind: true,
                            handler: function(){
                                Ext.Msg.confirm('Mensaje','Esta seguro de asignar los recursos de red ?', function(btn){
                                    if(btn==='yes')
                                    {  
                                        var hilo           = Ext.getCmp('hilosDisponibles').getValue();
                                        var vlan           = Ext.getCmp('vlansDisponibles').getValue();
                                        var vrf            = Ext.getCmp('vrfsDisponibles').getValue();
                                        var protocolo      = Ext.getCmp('protocolosEnrutamiento').getValue();
                                        var asPrivado      = Ext.getCmp('asPrivado').getValue();
                                        var subred         = Ext.getCmp('subredesDisponibles').getValue();
                                        var mascara        = Ext.getCmp('mascaras').getValue();
                                        var defaultGateway = Ext.getCmp('defaultGateway').getValue();
                                        var flagRecursos   = Ext.ComponentQuery.query('[name=rbRecursos]')[0].getGroupValue();
                                        
                                        if(flagRecursos==="existentes")
                                        {
                                            vlan         = Ext.getCmp('mismaVlanId').getValue();
                                            vrf          = Ext.getCmp('mismaVrfId').getValue(); 
                                            protocolo    = Ext.getCmp('mismoProtocolo').getValue();
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
                                                idElementoPadre:        json.idElementoPadre,
                                                hilo:                   hilo,
                                                vlan:                   vlan,
                                                vrf:                    vrf,
                                                protocolo:              protocolo,
                                                defaultGateway:         defaultGateway,
                                                asPrivado:              asPrivado,
                                                mascara:                mascara,
                                                idSubred:               subred,
                                                flagRecursos:           flagRecursos,
                                                ultimaMilla:            data.get('ultimaMilla'),
                                                migracionSDWAM:         (data.get("InfoMigracionSDWAN")?.EsSDWAN === true 
                                                                        && data.get("InfoMigracionSDWAN")?.EsMigracionSDWAN === true) ? 'SI': 'NO'
                                                
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
                    Ext.getCmp('containerMismosDatos').setVisible(false);
                    
                    var win = Ext.create('Ext.window.Window', {
                        title: strProductoSDWAN,
                        modal: true,
                        width: 1100,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();
                    
                    Ext.get(formPanel.getId()).mask('Cargando datos...');
                    //Se cambia el orden para consultar los protocolos de enrutamiento
                    storeProtocolosEnrutamiento.load({});
                    storeVrfsDisponibles.load({
                        callback: function() {
                            storeSubredesL3mplsDisponibles.load({
                                callback: function() {
                                    Ext.get(formPanel.getId()).unmask();
                                    storeHilosDisponibles.load({});
                                }
                            });
                        }
                    });

                    if (data.get('ultimaMilla')=="Radio")
                    {
                        Ext.getCmp('hilosDisponibles').setDisabled(true);
                        Ext.getCmp('nombreElementoContenedor').setDisabled(true);
                    }
                    
                    if (data.get('ultimaMilla')=="UTP")
                    {
                        Ext.getCmp('hilosDisponibles').setVisible(false);
                        Ext.getCmp('nombreElementoContenedor').setVisible(false);
                        Ext.getCmp('nombreElementoConector').setVisible(false);
                    }                                        

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