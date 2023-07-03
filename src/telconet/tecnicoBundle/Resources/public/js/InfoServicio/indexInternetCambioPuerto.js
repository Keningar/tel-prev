function verTrazabilidadElementos(interfaceElementoSplitterId, data, windowsPrincipal) {
    var storeHistorial = new Ext.data.Store({
        pageSize: 50000,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: getTrazabilidadElementos,
            reader: {
                type: 'json',
                totalProperty: 'status',
                root: 'mensaje'
            },
            extraParams: {
                interfaceElementoSplitterId: interfaceElementoSplitterId,
                tipoElementoPadre: 'OLT'
            }
        },
        fields: [
            { name: 'tipoElemento', mapping: 'tipoElemento' },
            { name: 'nombreElemento', mapping: 'nombreElemento' },
            { name: 'nombreInterface', mapping: 'nombreInterface' },
        ]
    });

    //grid de usuarios
    gridHistorialServicio = Ext.create('Ext.grid.Panel', {
        id: 'gridHistorialServicio',
        store: storeHistorial,
        columnLines: true,
        columns: [{
                header: 'Tipo Elemento',
                dataIndex: 'tipoElemento',
                width: 100,
                sortable: true
            }, {
                header: 'Nombre Elemento',
                dataIndex: 'nombreElemento',
                width: 350
            },
            {
                header: 'Nombre Interface',
                dataIndex: 'nombreInterface',
                width: 100
            }
        ],
        viewConfig: {
            stripeRows: true,
            enableTextSelection: true
        },
        frame: true,
        height: 250
            //title: 'Historial del Servicio'
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [

            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                //                checkboxToggle: true,
                //                collapsed: true,
                defaults: {
                    width: 570
                },
                items: [

                    gridHistorialServicio

                ]
            } //cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function() {
                win.destroy();
            }
        }, {
            text: 'Ejecutar',
            handler: function() {

                var elementoId = Ext.getCmp('nombreElementoNuevo').getValue();
                var interfaceElementoId = Ext.getCmp('interfaceElementoNuevo').getValue();
                var elementoCajaId = Ext.getCmp('cajaElementoNuevo').getValue();
                var elementoSplitterId = Ext.getCmp('splitterElementoNuevo').getValue();
                var interfaceElementoSplitterId = Ext.getCmp('splitterInterfaceElementoNuevo').getValue();

                Ext.get(formPanel.getId()).mask('Cambiando de Puerto!');
                Ext.Ajax.request({
                    url: cambiarPuertoCliente,
                    method: 'post',
                    timeout: 400000,
                    params: {
                        idServicio: data.idServicio,
                        elementoId: elementoId,
                        interfaceElementoId: interfaceElementoId,
                        elementoCajaId: elementoCajaId,
                        elementoSplitterId: elementoSplitterId,
                        interfaceElementoSplitterId: interfaceElementoSplitterId
                    },
                    success: function(response) {
                        Ext.get(formPanel.getId()).unmask();
                        if (response.responseText == "OK") {
                            Ext.Msg.alert('Mensaje', 'Se Cambio de Puerto', function(btn) {
                                if (btn == 'ok') {
                                    win.destroy();
                                    windowsPrincipal.destroy();
                                    store.load();
                                }
                            });
                        } else {
                            Ext.Msg.alert('Mensaje ', response.responseText);
                        }
                    },
                    failure: function(result) {
                        Ext.get(formPanel.getId()).unmask();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });

            },

        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Enlaces de elementos',
        modal: true,
        width: 610,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function cambiarPuerto(data, gridIndex) {
    if (data.ultimaMilla == "Cobre") {
        var storePop = new Ext.data.Store({
            pageSize: 50,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: getPops,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields: [
                { name: 'idElementoPop', mapping: 'idElementoPop' },
                { name: 'nombreElementoPop', mapping: 'nombreElementoPop' }
            ]
        });

        var storeDslams = new Ext.data.Store({
            pageSize: 50,
            //autoLoad: true,
            proxy: {
                type: 'ajax',
                url: getDslamsPorPop,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields: [
                { name: 'idElemento', mapping: 'idElemento' },
                { name: 'nombreElemento', mapping: 'nombreElemento' }
            ]
        });

        var storePuertos = new Ext.data.Store({
            pageSize: 50,
            //autoLoad: true,
            proxy: {
                type: 'ajax',
                url: getPuertosPorDslam,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields: [
                { name: 'idInterface', mapping: 'idInterface' },
                { name: 'nombreInterface', mapping: 'nombreInterface' }
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
                columns: 1
            },
            defaults: {
                // applied to each contained panel
                bodyStyle: 'padding:20px'
            },

            items: [

                //informacion tecnica actual
                {
                    xtype: 'fieldset',
                    title: 'Informacion Tecnica Actual',
                    defaultType: 'textfield',
                    defaults: {
                        width: 550
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
                                { width: '10%', border: false },
                                {
                                    xtype: 'textfield',
                                    //id:'nombreCpe',
                                    name: 'nombrePop',
                                    fieldLabel: 'Pop',
                                    displayField: data.popNombre,
                                    value: data.popNombre,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false },
                                {
                                    xtype: 'textfield',
                                    //id:'ipCpe',
                                    name: 'nombreDslam',
                                    fieldLabel: 'Dslam',
                                    displayField: data.elementoNombre,
                                    value: data.elementoNombre,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '10%', border: false },

                                //---------------------------------------------

                                { width: '10%', border: false },
                                {
                                    xtype: 'textfield',
                                    //id:'modeloDslam',
                                    name: 'interfaceDslam',
                                    fieldLabel: 'Puerto',
                                    displayField: data.interfaceElementoNombre,
                                    value: data.interfaceElementoNombre,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false },
                                { width: '30%', border: false },
                                { width: '10%', border: false }

                                //---------------------------------------------

                            ]
                        }

                    ]
                }, //cierre info tecnica actual

                //informacion tecnica nueva
                {
                    xtype: 'fieldset',
                    title: 'Informacion Tecnica Nueva',
                    defaultType: 'textfield',
                    defaults: {
                        width: 550
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
                                { width: '10%', border: false },
                                {
                                    xtype: 'combo',
                                    id: 'comboPops',
                                    name: 'comboPops',
                                    store: storePop,
                                    fieldLabel: 'Pop Nuevo',
                                    displayField: 'nombreElementoPop',
                                    valueField: 'idElementoPop',
                                    queryMode: 'local',
                                    listeners: {
                                        select: function(combo) {
                                            storeDslams.proxy.extraParams = { popId: combo.getValue() };  
                                            storeDslams.load({ params: {} });
                                        }
                                    }, //cierre listener
                                    width: '30%'
                                },
                                { width: '15%', border: false },
                                {
                                    xtype: 'combo',
                                    id: 'comboDslams',
                                    name: 'comboDslams',
                                    store: storeDslams,
                                    fieldLabel: 'Dslam Nuevo',
                                    displayField: 'nombreElemento',
                                    valueField: 'idElemento',
                                    queryMode: 'local',
                                    width: '30%',
                                    listeners: {
                                        select: function(combo) {
                                            storePuertos.proxy.extraParams = { dslamId: combo.getValue(), estado: "not connect" };  
                                            storePuertos.load({ params: {} });
                                        }
                                    } //cierre listener
                                },
                                { width: '10%', border: false },

                                //---------------------------------------------

                                { width: '10%', border: false },
                                {
                                    xtype: 'combo',
                                    id: 'comboPuertos',
                                    name: 'comboPuertos',
                                    store: storePuertos,
                                    fieldLabel: 'Puerto Nuevo',
                                    displayField: 'nombreInterface',
                                    valueField: 'idInterface',
                                    queryMode: 'local',
                                    width: '30%'
                                },
                                { width: '15%', border: false },
                                { width: '30%', border: false },
                                { width: '10%', border: false }

                                //---------------------------------------------

                            ]
                        }

                    ]
                }, //cierre info tecnica nueva



            ], //cierre items
            buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function() {
                    var interfaceId = Ext.getCmp('comboPuertos').getValue();
                    var elementoId = Ext.getCmp('comboDslams').getValue();
                    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Cambiar de Puerto al cliente?', function(btn) {
                        if (btn == 'ok') {
                            Ext.get(gridServicios.getId()).mask('Cambiando de Puerto...');
                            Ext.Ajax.request({
                                url: cambiarPuertoCliente,
                                method: 'post',
                                timeout: 400000,
                                params: {
                                    idServicio: data.idServicio,
                                    interfaceElementoId: interfaceId,
                                    dslamId: elementoId
                                },
                                success: function(response) {
                                    Ext.get(gridServicios.getId()).unmask();
                                    if (response.responseText == "OK") {
                                        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                        Ext.Msg.alert('Mensaje', 'Se cambio de puerto al Cliente', function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    } else if (response.responseText == "ERROR PERFIL") {
                                        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                        Ext.Msg.alert('Mensaje', 'No se cambio el Puerto, Verificar perfil en el Dslam Destino', function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    } else {
                                        Ext.Msg.alert('Mensaje ', response.responseText);
                                    }
                                },
                                failure: function(result) {
                                    Ext.get(gridServicios.getId()).unmask();
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }
                    });

                }
            }, {
                text: 'Cancelar',
                handler: function() {
                    win.destroy();
                }
            }]
        });
    } //cierre if cobre
    else if (data.ultimaMilla == "Radio") {
        var storeRadios = new Ext.data.Store({
            pageSize: 50,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: getRadios,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields: [
                { name: 'idElementoRadio', mapping: 'idElementoRadio' },
                { name: 'nombreElementoRadio', mapping: 'nombreElementoRadio' }
            ]
        });

        var storePuertos = new Ext.data.Store({
            pageSize: 50,
            //autoLoad: true,
            proxy: {
                type: 'ajax',
                url: getPuertosPorDslam,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields: [
                { name: 'idInterface', mapping: 'idInterface' },
                { name: 'nombreInterface', mapping: 'nombreInterface' }
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
                columns: 1
            },
            defaults: {
                // applied to each contained panel
                bodyStyle: 'padding:20px'
            },

            items: [

                //informacion tecnica actual
                {
                    xtype: 'fieldset',
                    title: 'Informacion Tecnica Actual',
                    defaultType: 'textfield',
                    defaults: {
                        width: 550
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
                                { width: '10%', border: false },
                                {
                                    xtype: 'textfield',
                                    //id:'ipCpe',
                                    name: 'nombreDslam',
                                    fieldLabel: 'Radio',
                                    displayField: data.elementoNombre,
                                    value: data.elementoNombre,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false },
                                {
                                    xtype: 'textfield',
                                    //id:'modeloDslam',
                                    name: 'interfaceDslam',
                                    fieldLabel: 'Puerto',
                                    displayField: data.interfaceElementoNombre,
                                    value: data.interfaceElementoNombre,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '10%', border: false }

                                //---------------------------------------------

                            ]
                        }

                    ]
                }, //cierre info tecnica actual

                //informacion tecnica nueva
                {
                    xtype: 'fieldset',
                    title: 'Informacion Tecnica Nueva',
                    defaultType: 'textfield',
                    defaults: {
                        width: 550
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
                                { width: '10%', border: false },
                                {
                                    xtype: 'combo',
                                    id: 'comboDslams',
                                    name: 'comboDslams',
                                    store: storeRadios,
                                    fieldLabel: 'Radio Nuevo',
                                    displayField: 'nombreElementoRadio',
                                    valueField: 'idElementoRadio',
                                    queryMode: 'local',
                                    width: '30%',
                                    listeners: {
                                        select: function(combo) {
                                            storePuertos.proxy.extraParams = { dslamId: combo.getValue(), estado: "Todos" };

                                              
                                            storePuertos.load({ params: {} });
                                        }
                                    } //cierre listener
                                },
                                { width: '15%', border: false },
                                {
                                    xtype: 'combo',
                                    id: 'comboPuertos',
                                    name: 'comboPuertos',
                                    store: storePuertos,
                                    fieldLabel: 'Puerto Nuevo',
                                    displayField: 'nombreInterface',
                                    valueField: 'idInterface',
                                    queryMode: 'local',
                                    width: '30%'
                                },
                                { width: '10%', border: false }

                                //---------------------------------------------

                            ]
                        }

                    ]
                }, //cierre info tecnica nueva



            ], //cierre items
            buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function() {
                    var interfaceId = Ext.getCmp('comboPuertos').getValue();
                    var elementoId = Ext.getCmp('comboDslams').getValue();
                    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Cambiar de Puerto al cliente?', function(btn) {
                        if (btn == 'ok') {
                            Ext.get(gridServicios.getId()).mask('Cambiando de Puerto...');
                            Ext.Ajax.request({
                                url: cambiarPuertoCliente,
                                method: 'post',
                                timeout: 400000,
                                params: {
                                    idServicio: data.idServicio,
                                    interfaceElementoId: interfaceId,
                                    dslamId: elementoId
                                },
                                success: function(response) {
                                    Ext.get(gridServicios.getId()).unmask();
                                    if (response.responseText == "OK") {
                                        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                        Ext.Msg.alert('Mensaje', 'Se cambio de puerto al Cliente', function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    } else if (response.responseText == "ERROR PERFIL") {
                                        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                        Ext.Msg.alert('Mensaje', 'No se cambio el Puerto, Verificar perfil en el Dslam Destino', function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    } else {
                                        Ext.Msg.alert('Mensaje ', 'No se pudo Cambiar el Puerto del Cliente!');
                                    }
                                },
                                failure: function(result) {
                                    Ext.get(gridServicios.getId()).unmask();
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }
                    });

                }
            }, {
                text: 'Cancelar',
                handler: function() {
                    win.destroy();
                }
            }]
        });
    } //cierre else radio



    var win = Ext.create('Ext.window.Window', {
        title: 'Cambiar Puerto',
        modal: true,
        width: 570,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function cambioLineaPom(data, gridIndex) {
    const idServicio = data.idServicio;
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio
        },
        success: function(response) {
                Ext.get(gridServicios.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                var datos = json.encontrados;

                //-------------------------------------------------------------------------------------------

                //store para elementos (olt)
                var storeElementos = new Ext.data.Store({
                    pageSize: 100,
                    listeners: {
                        load: function() {

                        }
                    },
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getElementosPorTipo,
                        extraParams: {
                            esISB: data.esISB,
                            idServicio: data.idServicio,
                            nombreElemento: this.nombreElemento,
                            tipoElemento: 'OLT',
                            marcaElemento: data.marcaElemento,
                            ldap: data.ldap,
                            validaTnp: 'SI',
                            strTipoRed: data.strTipoRed
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idElemento', mapping: 'idElemento' },
                        { name: 'nombreElemento', mapping: 'nombreElemento' },
                        { name: 'ipElemento', mapping: 'ip' }
                    ]
                });

                var storeInterfacesElemento = new Ext.data.Store({
                    pageSize: 100,
                    //                autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getInterfacesPorElemento,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idInterface', mapping: 'idInterface' },
                        { name: 'nombreInterface', mapping: 'nombreInterface' }
                    ]
                });

                var storeElementosContenedor = new Ext.data.Store({
                    pageSize: 100,
                    //                autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getElementosContenedoresPorPuerto,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idElementoContenedor', mapping: 'idElementoContenedor' },
                        { name: 'nombreElementoContenedor', mapping: 'nombreElementoContenedor' }
                    ]
                });

                var storeElementosConector = new Ext.data.Store({
                    pageSize: 100,
                    //                autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getElementosConectorPorElementoContenedor,
                        extraParams: {
                            esISB: data.esISB
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idElementoConector', mapping: 'idElementoConector' },
                        { name: 'nombreElementoConector', mapping: 'nombreElementoConector' }
                    ]
                });

                var storeInterfacesSplitter = new Ext.data.Store({
                    pageSize: 100,
                    //                autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getInterfacesPorElemento,
                        extraParams: {
                            idElemento: datos[0].idSplitter,
                            estado: 'not connect'
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idInterface', mapping: 'idInterface' },
                        { name: 'nombreInterface', mapping: 'nombreInterface' }
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
                        columns: 2
                    },
                    defaults: {
                        // applied to each contained panel
                        bodyStyle: 'padding:20px'
                    },
                    items: [

                        //informacion de backbone anterior
                        {
                            xtype: 'fieldset',
                            title: 'Anterior',
                            defaultType: 'textfield',
                            defaults: {
                                width: 540,
                                height: 130
                            },
                            items: [

                                //gridInfoBackbone

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    },
                                    items: [
                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'Elemento',
                                            fieldLabel: 'Elemento',
                                            displayField: data.elementoNombre,
                                            value: data.elementoNombre,

                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            //                                id:'perfilDslam',
                                            name: 'ipElemento',
                                            fieldLabel: 'Ip Elemento',
                                            displayField: data.ipElemento,
                                            value: data.ipElemento,
                                            //displayField: "",
                                            //value: "",
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'interfaceElemento',
                                            fieldLabel: 'Puerto Elemento',
                                            displayField: data.interfaceElementoNombre,
                                            value: data.interfaceElementoNombre,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            //                                id:'perfilDslam',
                                            name: 'modeloELemento',
                                            fieldLabel: 'Modelo Elemento',
                                            displayField: data.modeloElemento,
                                            value: data.modeloElemento,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'splitterElemento',
                                            fieldLabel: 'Splitter Elemento',
                                            displayField: datos[0].nombreSplitter,
                                            value: datos[0].nombreSplitter,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            id: 'splitterInterfaceElemento',
                                            name: 'splitterInterfaceElemento',
                                            fieldLabel: 'Splitter Interface',
                                            displayField: datos[0].nombrePuertoSplitter,
                                            value: datos[0].nombrePuertoSplitter,
                                            width: '25%',

                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'cajaElemento',
                                            fieldLabel: 'Caja Elemento',
                                            displayField: datos[0].nombreCaja,
                                            value: datos[0].nombreCaja,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        { width: '10%', border: false },
                                        { width: '10%', border: false }

                                    ]
                                }

                            ]
                        }, //cierre de info de backbone anterior

                        //informacion de backbone nuevo
                        {
                            xtype: 'fieldset',
                            title: 'Nuevo',
                            defaultType: 'textfield',
                            defaults: {
                                width: 540,
                                height: 130
                            },
                            items: [

                                //gridInfoBackbone

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    },
                                    items: [
                                        { width: '10%', border: false },
                                        {
                                            //queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'nombreElementoNuevo',
                                            name: 'nombreElementoNuevo',
                                            fieldLabel: 'Elemento',
                                            displayField: 'nombreElemento',
                                            valueField: 'idElemento',
                                            loadingText: 'Buscando...',
                                            width: '25%',
                                            queryMode: "remote",
                                            lazyRender: true,
                                            forceSelection: true,
                                            emptyText: 'Ingrese nombre Olt..',
                                            minChars: 3,
                                            typeAhead: true,
                                            triggerAction: 'all',
                                            selectOnTab: true,
                                            listClass: 'x-combo-list-small',
                                            store: storeElementos,
                                            listeners: {
                                                select: function(combo) {
                                                    for (var i = 0; i < storeElementos.data.items.length; i++) {
                                                        if (storeElementos.data.items[i].data.idElemento == combo.getValue()) {
                                                            //                                                        //console.log(storeElementos.data.items[i].data.ipElemento);
                                                            Ext.getCmp('ipElementoNuevo').setRawValue(storeElementos.data.items[i].data.ipElemento);
                                                            break;
                                                        }
                                                    }

                                                    storeInterfacesElemento.proxy.extraParams = { idElemento: combo.getValue() };  
                                                    storeInterfacesElemento.load({ params: {} });
                                                }
                                            }
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            id: 'ipElementoNuevo',
                                            name: 'ipElementoNuevo',
                                            fieldLabel: 'Ip Elemento',
                                            displayField: '',
                                            value: '',
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'interfaceElementoNuevo',
                                            name: 'interfaceElementoNuevo',
                                            fieldLabel: 'Puerto Elemento',
                                            displayField: 'nombreInterface',
                                            valueField: 'idInterface',
                                            loadingText: 'Buscando...',
                                            store: storeInterfacesElemento,
                                            listeners: {
                                                select: function(combo) {
                                                    storeElementosContenedor.proxy.extraParams = {
                                                        idInterfaceElemento: combo.getValue()
                                                    };
                                                    storeElementosContenedor.load({ params: {} });
                                                }
                                            },
                                            width: '25%',
                                        },
                                        { width: '15%', border: false },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'cajaElementoNuevo',
                                            name: 'cajaElementoNuevo',
                                            fieldLabel: 'Caja Elemento',
                                            displayField: 'nombreElementoContenedor',
                                            valueField: 'idElementoContenedor',
                                            loadingText: 'Buscando...',
                                            store: storeElementosContenedor,
                                            listeners: {
                                                select: function(combo) {
                                                    storeElementosConector.proxy.extraParams = {
                                                        idElementoContenedor: combo.getValue(),
                                                        esISB: data.esISB,
                                                        idServicio: idServicio
                                                    };  
                                                    storeElementosConector.load({ params: {} });
                                                }
                                            },
                                            width: '25%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'splitterElementoNuevo',
                                            name: 'splitterElementoNuevo',
                                            fieldLabel: 'Splitter Elemento',
                                            displayField: 'nombreElementoConector',
                                            valueField: 'idElementoConector',
                                            loadingText: 'Buscando...',
                                            store: storeElementosConector,
                                            listeners: {
                                                select: function(combo) {
                                                    storeInterfacesSplitter.proxy.extraParams = { idElemento: combo.getValue(), estado: 'not connect' };  
                                                    storeInterfacesSplitter.load({ params: {} });
                                                }
                                            },
                                            width: '25%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'splitterInterfaceElementoNuevo',
                                            name: 'splitterInterfaceElementoNuevo',
                                            fieldLabel: 'Splitter Interface',
                                            displayField: 'nombreInterface',
                                            valueField: 'idInterface',
                                            loadingText: 'Buscando...',
                                            store: storeInterfacesSplitter,
                                            width: '25%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        { width: '10%', border: false },
                                        { width: '15%', border: false },
                                        { width: '10%', border: false },
                                        { width: '10%', border: false }

                                    ]
                                }

                            ]
                        }, //cierre de info de backbone nuevo

                    ],
                    buttons: [{
                        text: 'Ejecutar',
                        formBind: true,
                        handler: function() {
                            var validacion = true;
                            var elementoId = Ext.getCmp('nombreElementoNuevo').getValue();
                            var interfaceElementoId = Ext.getCmp('interfaceElementoNuevo').getValue();
                            var elementoCajaId = Ext.getCmp('cajaElementoNuevo').getValue();
                            var elementoSplitterId = Ext.getCmp('splitterElementoNuevo').getValue();
                            var interfaceElementoSplitterId = Ext.getCmp('splitterInterfaceElementoNuevo').getValue();

                            if (elementoId == '' || interfaceElementoId == '' || elementoCajaId == '' || elementoSplitterId == '' || interfaceElementoSplitterId == '') {
                                validacion = false;
                            }

                            if (validacion) {
                                console.log(data);
                                Ext.get(formPanel.getId()).mask('Ejecutando Cambio de Linea Pon...');
                                Ext.Ajax.request({
                                    url: cambiarLineaPon, //cambiarPuertoCliente,
                                    method: 'post',
                                    timeout: 1000000,
                                    params: {
                                        idSolicitudLineaPom: data.idSolicitudLineaPom,
                                        idServicio: data.idServicio,
                                        elementoId: elementoId,
                                        interfaceElementoId: interfaceElementoId,
                                        elementoCajaId: elementoCajaId,
                                        elementoSplitterId: elementoSplitterId,
                                        interfaceElementoSplitterId: interfaceElementoSplitterId,
                                        productoId: data.productoId,
                                        esISB: data.esISB,
                                        strTipoRed: data.strTipoRed,
                                        strUltimaMilla: data.ultimaMilla,
                                        strTipoEnlace: data.tipoEnlace,
                                        strModeloCpe:  data.modeloElemento,
                                        capacidadUno:   data.capacidadUno,
                                        capacidadDos:   data.capacidadDos
                                    },
                                    success: function(response) {
                                        Ext.get(formPanel.getId()).unmask();
                                        var objData = Ext.JSON.decode(response.responseText);
                                        var strStatus = objData.status;
                                        var strMensaje = objData.mensaje;
                                        if (strStatus == "OK") {
                                            Ext.Msg.alert('Mensaje', strMensaje, function(btn) {
                                                if (btn == 'ok') {
                                                    win.destroy();
                                                    store.load();
                                                }
                                            });
                                        } else {
                                            Ext.Msg.alert('Mensaje ', response.responseText);
                                        }
                                    },
                                    failure: function(result) {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });

                            } else {
                                Ext.Msg.alert("Failed", "Favor Revise los campos", function(btn) {
                                    if (btn == 'ok') {
                                        store.load();
                                    }
                                });
                            }

                        }
                    }, {
                        text: 'Cancelar',
                        handler: function() {
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Cambio de línea Pon',
                    modal: true,
                    width: 1160,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

            } //cierre response

    });
}

function cambiarPuertoTN(data, grid) {
    var esSatelital = false;
    var strEsCpeExistente = "NO";
    var strEsRadioExistente = "NO";
    var strEsTransceiverExistente = "NO";
    var nombreCamara = "";
    var boolSeLiberanRecursos = true;
    var idOnt = data.idOnt;
    var serieOnt = data.serieOnt;
    var macOnt = data.macOnt;
    var modeloOnt = data.modeloOnt;
    var idSwPoeGpon = data.idSwPoeGpon;

    if (data.estadoDatosSafecity == "Activo" && data.booleanActivarSwPoeGpon != "S") {
        var tituloElementoConector = "Nombre Cassette";
        var height = 170;

        Ext.get(gridServicios.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({
            url: getDatosBackbone,
            method: 'post',
            timeout: 400000,
            params: {
                idServicio: data.idServicio,
                tipoElementoPadre: 'ROUTER'
            },
            success: function(response) {
                    Ext.get(gridServicios.getId()).unmask();

                    var json = Ext.JSON.decode(response.responseText);
                    var datos = json.encontrados;

                    //-------------------------------------------------------------------------------------------

                    if (datos[0].idElementoPadre == 0) {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: datos[0].nombreElementoPadre,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    } else {
                        var storeInterfacesPorEstadoYElemento = new Ext.data.Store({
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url: getInterfacesPoEstadoYElemento,
                                extraParams: {
                                    estadoInterface: "not connect",
                                    elementoId: idOnt
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                },
                                afterRequest: function(req, res) {
                                    console.log(JSON.parse(req.operation.response.responseText));
                                    const responseContent = JSON.parse(req.operation.response.responseText)
                                    if (responseContent.encontrados == "[]") {
                                        Ext.Msg.alert('Mensaje ', 'No existe disponibilidad de puerto en el equipo');
                                    }
                                }
                            },
                            fields: [
                                { name: 'idInterface', mapping: 'nombreInterface' }
                            ]
                        });


                        var storeInterfacesPorEstadoYElementoSwPoe = new Ext.data.Store({
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url: getInterfacesPoEstadoYElemento,
                                extraParams: {
                                    estadoInterface: "not connect",
                                    elementoId: idSwPoeGpon
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                },
                                afterRequest: function(req, res) {
                                    console.log(JSON.parse(req.operation.response.responseText));
                                    const responseContent = JSON.parse(req.operation.response.responseText)
                                    if (responseContent.encontrados == "[]") {
                                        Ext.Msg.alert('Mensaje ', 'No existe disponibilidad de puerto en el equipo');
                                    }
                                }
                            },
                            fields: [
                                { name: 'idInterface', mapping: 'nombreInterface' }
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

                                //informacion de backbone
                                {
                                    colspan: 2,
                                    rowspan: 2,
                                    xtype: 'panel',
                                    title: 'Informacion de backbone',
                                    defaults: {
                                        height: height
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
                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'Elemento',
                                                    fieldLabel: 'Elemento',
                                                    displayField: data.elementoNombre,
                                                    value: data.elementoNombre,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false },
                                                { width: '15%', border: false },
                                                { width: '10%', border: false },

                                                //---------------------------------------------

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'interfaceElemento',
                                                    fieldLabel: 'Puerto Elemento',
                                                    displayField: data.interfaceElementoNombre,
                                                    value: data.interfaceElementoNombre,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'modeloELemento',
                                                    fieldLabel: 'Modelo Elemento',
                                                    displayField: data.modeloElemento,
                                                    value: data.modeloElemento !== '' || typeof data.modeloElemento == 'undefined' ? 'NA' : data.modeloElemento,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vlan',
                                                    fieldLabel: data.esServicioWifiSafeCity === 'S' ? 'Vlan SSID' : 'Vlan',
                                                    displayField: data.vlan,
                                                    value: data.vlan !== '' || typeof data.vlan == 'undefined' ? 'NA' : data.vlan,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vrf',
                                                    fieldLabel: data.esServicioWifiSafeCity === 'S' ? 'Vrf SSID' : 'Vrf',
                                                    displayField: data.vrf,
                                                    value: data.vrf !== '' || typeof data.vrf == 'undefined' ? 'NA' : data.vrf,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------

                                                { width: '10%', border: false, hidden: data.esServicioWifiSafeCity === 'S' ? false : true },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vlanAdmin',
                                                    fieldLabel: 'Vlan Admin',
                                                    displayField: data.vlanAdmin,
                                                    value: data.vlanAdmin,
                                                    hidden: data.esServicioWifiSafeCity === 'S' ? false : true,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false, hidden: data.esServicioWifiSafeCity === 'S' ? false : true },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vrfAdmin',
                                                    fieldLabel: 'Vrf Admin',
                                                    displayField: data.vrfAdmin,
                                                    value: data.vrfAdmin,
                                                    hidden: data.esServicioWifiSafeCity === 'S' ? false : true,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false, hidden: data.esServicioWifiSafeCity === 'S' ? false : true },

                                                //---------------------------------------------

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'protocolo',
                                                    fieldLabel: 'Protocolo',
                                                    displayField: data.protocolo,
                                                    value: data.protocolo !== '' || typeof data.protocolo == 'undefined' ? 'NA' : data.protocolo,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'asPrivado',
                                                    fieldLabel: 'AS Privado',
                                                    displayField: data.asPrivado,
                                                    value: data.asPrivado,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'pe',
                                                    name: 'pe',
                                                    fieldLabel: 'PE',
                                                    displayField: datos[0].nombreElementoPadre,
                                                    value: datos[0].nombreElementoPadre,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'anillo',
                                                    name: 'anillo',
                                                    fieldLabel: 'Anillo',
                                                    displayField: datos[0].anillo,
                                                    value: datos[0].anillo !== '' || typeof datos[0].anillo == 'undefined' ? 'NA' : datos[0].anillo,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------                                                                                               

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'umExistente',
                                                    name: 'umExistente',
                                                    fieldLabel: 'Utiliza UM Existente',
                                                    displayField: data.usaUltimaMillaExistente,
                                                    value: data.usaUltimaMillaExistente,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false },

                                                { width: '10%', border: false },

                                                //---------------------------------------------                                                                                               

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'subredBb',
                                                    name: 'subredBb',
                                                    fieldLabel: 'Subred (Pe-Hub)',
                                                    displayField: data.subredVsatBackbone,
                                                    value: data.subredVsatBackbone,
                                                    readOnly: true,
                                                    width: '30%',
                                                    hidden: !esSatelital
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'subredCli',
                                                    name: 'subredCli',
                                                    fieldLabel: 'Subred (Vsat-Cliente)',
                                                    displayField: data.subredVsatCliente,
                                                    value: data.subredVsatCliente,
                                                    readOnly: true,
                                                    width: '30%',
                                                    hidden: !esSatelital
                                                },
                                                { width: '10%', border: false }
                                            ]
                                        }

                                    ]
                                }, //cierre de info de backbone

                                //informacion del servicio/producto
                                {
                                    colspan: 2,
                                    rowspan: 2,
                                    xtype: 'panel',
                                    title: 'Informacion del Servicio',
                                    defaults: {
                                        height: height
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

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'producto',
                                                    fieldLabel: 'Producto',
                                                    displayField: data.descripcionPresentaFactura,
                                                    value: data.descripcionPresentaFactura,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'login',
                                                    fieldLabel: 'Login',
                                                    displayField: data.login,
                                                    value: data.login,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadUno',
                                                    fieldLabel: 'Capacidad Uno',
                                                    displayField: data.capacidadUno,
                                                    value: data.capacidadUno !== '' || typeof data.capacidadUno == 'undefined' ?
                                                        'NA' : data.capacidadUno,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadDos',
                                                    fieldLabel: 'Capacidad Dos',
                                                    displayField: data.capacidadDos,
                                                    value: data.capacidadDos !== '' || typeof data.capacidadDos == 'undefined' ?
                                                        'NA' : data.capacidadDos,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'ultimaMilla',
                                                    name: 'ultimaMilla',
                                                    fieldLabel: 'Ultima Milla',
                                                    displayField: data.ultimaMilla,
                                                    value: data.ultimaMilla,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'elementoContenedor',
                                                    name: 'elementoContenedor',
                                                    fieldLabel: 'Caja',
                                                    displayField: datos[0].nombreCaja,
                                                    value: datos[0].nombreCaja,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'nombreElementoConector',
                                                    name: 'nombreElementoConector',
                                                    fieldLabel: tituloElementoConector,
                                                    displayField: datos[0].nombreSplitter,
                                                    value: datos[0].nombreSplitter,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'hilo',
                                                    name: 'hilo',
                                                    fieldLabel: 'Color Hilo',
                                                    displayField: datos[0].colorHilo,
                                                    value: datos[0].colorHilo,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------

                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'tipoEnlace',
                                                    name: 'tipoEnlace',
                                                    fieldLabel: 'Tipo Enlace',
                                                    displayField: data.tipoEnlace,
                                                    value: data.tipoEnlace,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '15%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'ipServicio',
                                                    name: 'ipServicio',
                                                    fieldLabel: 'Ip WAN',
                                                    displayField: data.ipServicio,
                                                    value: data.ipServicio !== '' || typeof data.ipServicio == 'undefined' ?
                                                        'NA' : data.ipServicio,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '10%', border: false },

                                                //---------------------------------------------
                                                { width: '10%', border: false },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'tipoRed',
                                                    name: 'tipoRed',
                                                    fieldLabel: 'Tipo de Red',
                                                    displayField: data.strTipoRed,
                                                    value: data.strTipoRed,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false },
                                            ]
                                        }

                                    ]
                                }, //cierre de la informacion servicio/producto

                                //informacion de los elementos del cliente
                                {
                                    colspan: 3,
                                    xtype: 'panel',
                                    title: 'Información de los Elementos del Cliente',
                                    items: [
                                        //nueva CAMARA
                                        {
                                            id: 'nuevoCamara',
                                            xtype: 'fieldset',
                                            title: 'Camara',
                                            defaultType: 'textfield',
                                            hidden: false,
                                            items: [{
                                                        xtype: 'container',
                                                        layout: {
                                                            type: 'table',
                                                            columns: 3,
                                                            align: 'stretch'
                                                        },
                                                        items: [
                                                                //---------------------------------------
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'serieNuevoCamara',
                                                                    name: 'serieNuevoCamara',
                                                                    fieldLabel: 'Serie:',
                                                                    displayField: "",
                                                                    value: "",
                                                                    width: '25%',
                                                                    listeners: {

                                                                    }
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'nombreNuevoCamara',
                                                                    name: 'nombreNuevoCamara',
                                                                    fieldLabel: 'Nombre Camara',
                                                                    displayField: "",
                                                                    value: "",
                                                                    width: '25%'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'modeloCamara',
                                                                    name: 'modeloCamara',
                                                                    fieldLabel: 'Modelo',
                                                                    displayField: "",
                                                                    value: "",
                                                                    width: '25%'
                                                                },
                                                                //---------------------------------------
                                                            ] //items container
                                                    } //items panel
                                                ] //items panel
                                        },
                                        //nueva CAMARA
                                        {
                                            id: 'nuevoWifiAP',
                                            xtype: 'fieldset',
                                            title: 'WIFI AP',
                                            defaultType: 'textfield',
                                            hidden: true,
                                            items: [{
                                                        xtype: 'container',
                                                        layout: {
                                                            type: 'table',
                                                            columns: 3,
                                                            align: 'stretch'
                                                        },
                                                        items: [
                                                                //---------------------------------------
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'serieWifi',
                                                                    name: 'serieWifi',
                                                                    fieldLabel: 'Serie:',
                                                                    displayField: "",
                                                                    value: "",
                                                                    width: '25%',
                                                                    listeners: {}
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'nombreWifi',
                                                                    name: 'nombreWifi',
                                                                    fieldLabel: 'Nombre AP',
                                                                    displayField: "",
                                                                    value: "",
                                                                    width: '25%'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'modeloWifi',
                                                                    name: 'modeloWifi',
                                                                    fieldLabel: 'Modelo',
                                                                    displayField: "",
                                                                    value: "",
                                                                    width: '25%'
                                                                },
                                                                //---------------------------------------
                                                            ] //items container
                                                    } //items panel
                                                ] //items panel
                                        },
                                        //Bloque Ont del Datos Safecity
                                        {
                                            id: 'OntDatosSafecity',
                                            xtype: 'fieldset',
                                            title: 'Ont',
                                            defaultType: 'textfield',
                                            hidden: false,
                                            items: [{
                                                        xtype: 'container',
                                                        layout: {
                                                            type: 'table',
                                                            columns: 3,
                                                            align: 'stretch'
                                                        },
                                                        items: [{
                                                                    xtype: 'textfield',
                                                                    id: 'nombreOnt',
                                                                    name: 'nombreOnt',
                                                                    fieldLabel: 'Nombre',
                                                                    displayField: "",
                                                                    value: data.nombreOnt,
                                                                    readOnly: true,
                                                                    width: '40%'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'marcaOnt',
                                                                    name: 'marcaOnt',
                                                                    fieldLabel: 'Marca',
                                                                    displayField: "",
                                                                    value: data.marcaOnt,
                                                                    readOnly: true,
                                                                    width: '40%'
                                                                },
                                                                { width: '20%', border: false },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'serieOnt',
                                                                    name: 'serieOnt',
                                                                    fieldLabel: 'Serie',
                                                                    displayField: "",
                                                                    value: data.serieOnt,
                                                                    readOnly: true,
                                                                    width: '30%'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'modeloOnt',
                                                                    name: 'modeloOnt',
                                                                    fieldLabel: 'Modelo',
                                                                    displayField: "",
                                                                    value: data.modeloOnt,
                                                                    readOnly: true,
                                                                    width: '30%'
                                                                },
                                                                {
                                                                    queryMode: 'local',
                                                                    xtype: 'combobox',
                                                                    id: 'puertosOnt',
                                                                    name: 'puertosOnt',
                                                                    fieldLabel: 'Puertos Displnibles',
                                                                    displayField: 'idInterface',
                                                                    value: '-Seleccione-',
                                                                    valueField: 'nombreInterface',
                                                                    store: storeInterfacesPorEstadoYElemento,
                                                                    width: '30%'
                                                                },
                                                                { width: '20%', border: false },
                                                                //---------------------------------------

                                                            ] //items container
                                                    } //items panel
                                                ] //items panel
                                        },
                                        //Bloque Sw Poe del Datos Safecity
                                        {
                                            id: 'SwPoeDatosSafecity',
                                            xtype: 'fieldset',
                                            title: 'Switch PoE',
                                            defaultType: 'textfield',
                                            hidden: true,
                                            items: [{
                                                        xtype: 'container',
                                                        layout: {
                                                            type: 'table',
                                                            columns: 3,
                                                            align: 'stretch'
                                                        },
                                                        items: [{
                                                                    xtype: 'textfield',
                                                                    id: 'nombreSwPoeGpon',
                                                                    name: 'nombreSwPoeGpon',
                                                                    fieldLabel: 'Nombre',
                                                                    displayField: "",
                                                                    value: data.nombreSwPoeGpon,
                                                                    readOnly: true,
                                                                    width: '40%'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'marcaSwPoeGpon',
                                                                    name: 'marcaSwPoeGpon',
                                                                    fieldLabel: 'Marca',
                                                                    displayField: "",
                                                                    value: data.marcaSwPoeGpon,
                                                                    readOnly: true,
                                                                    width: '40%'
                                                                },
                                                                { width: '20%', border: false },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'serieSwPoeGpon',
                                                                    name: 'serieSwPoeGpon',
                                                                    fieldLabel: 'Serie',
                                                                    displayField: "",
                                                                    value: data.serieSwPoeGpon,
                                                                    readOnly: true,
                                                                    width: '30%'
                                                                },
                                                                {
                                                                    xtype: 'textfield',
                                                                    id: 'modeloSwPoeGpon',
                                                                    name: 'modeloSwPoeGpon',
                                                                    fieldLabel: 'Modelo',
                                                                    displayField: "",
                                                                    value: data.modeloSwPoeGpon,
                                                                    readOnly: true,
                                                                    width: '30%'
                                                                },
                                                                {
                                                                    queryMode: 'local',
                                                                    xtype: 'combobox',
                                                                    id: 'puertosSwPoe',
                                                                    name: 'puertosSwPoe',
                                                                    fieldLabel: 'Puertos Disponibles',
                                                                    displayField: 'idInterface',
                                                                    value: '-Seleccione-',
                                                                    valueField: 'nombreInterface',
                                                                    store: storeInterfacesPorEstadoYElementoSwPoe,
                                                                    width: '30%'
                                                                },
                                                                { width: '20%', border: false },
                                                                //---------------------------------------

                                                            ] //items container
                                                    } //items panel
                                                ] //items panel
                                        },

                                    ]
                                },
                                //cierre informacion de los elementos del cliente
                            ],
                            buttons: [{
                                    text: 'Activar',
                                    formBind: true,
                                    handler: function() {
                                            //datos camara safecity
                                            var nombreOnt = Ext.getCmp('nombreOnt').getValue();
                                            var puertosOnt = Ext.getCmp('puertosOnt').getRawValue();
                                            var nombreNuevoCamara = Ext.getCmp('nombreNuevoCamara').getValue();
                                            var serieNuevoCamara = Ext.getCmp('serieNuevoCamara').getValue();
                                            var modeloCamara = Ext.getCmp('modeloCamara').getValue();

                                            //datos sw poe safecity
                                            var nombreSwPoeGpon = Ext.getCmp('nombreSwPoeGpon').getValue();
                                            var puertosSwPoe = Ext.getCmp('puertosSwPoe').getRawValue();
                                            var nombreWifi = Ext.getCmp('nombreWifi').getValue();
                                            var serieWifi = Ext.getCmp('serieWifi').getValue();
                                            var modeloWifi = Ext.getCmp('modeloWifi').getValue();


                                            var validacion = true;
                                            var flag = 0;

                                            if ((boolSeLiberanRecursos && puertosOnt == "-Seleccione-") ||
                                                (!boolSeLiberanRecursos && puertosSwPoe == "-Seleccione-")) {
                                                validacion = false;
                                                flag = 1;
                                            }

                                            if (validacion) {
                                                Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');

                                                Ext.Ajax.request({
                                                    url: cambiarPuertoBotonGpon,
                                                    method: 'post',
                                                    timeout: 1000000,
                                                    params: {
                                                        idServicio: data.idServicio,
                                                        tipoEnlace: data.tipoEnlace,
                                                        interfaceElementoId: data.interfaceElementoId,
                                                        idProducto: data.productoId,
                                                        login: data.login,
                                                        tipoRed: data.strTipoRed,
                                                        servicioEnSwPoe: data.servicioEnSwPoe,
                                                        strExisteSwPoeGpon: data.strExisteSwPoeGpon,

                                                        //datos l3mpls
                                                        loginAux: data.loginAux,
                                                        elementoPadre: data.elementoPadre,
                                                        elementoNombre: data.elementoNombre,
                                                        interfaceElementoNombre: data.interfaceElementoNombre,
                                                        ipServicio: data.ipServicio,
                                                        subredServicio: data.subredServicio,
                                                        gwSubredServicio: data.gwSubredServicio,
                                                        mascaraSubredServicio: data.mascaraSubredServicio,
                                                        protocolo: data.protocolo,
                                                        defaultGateway: data.defaultGateway,
                                                        asPrivado: data.asPrivado,
                                                        vrf: data.vrf,
                                                        rdId: data.rdId,
                                                        ultimaMilla: data.ultimaMilla,
                                                        vrfAdmin: data.vrfAdmin,
                                                        vlanAdmin: data.vlanAdmin,

                                                        //datos camara y ont 
                                                        idOnt: idOnt,
                                                        nombreOnt: nombreOnt,
                                                        serieOnt: serieOnt,
                                                        macOnt: macOnt,
                                                        modeloOnt: modeloOnt,
                                                        puertosOnt: puertosOnt,
                                                        banderaCamaraSafecity: data.esServicioCamaraSafeCity,
                                                        nombreNuevoCamara: nombreNuevoCamara,
                                                        serieNuevoCamara: serieNuevoCamara,
                                                        modeloCamara: modeloCamara,

                                                        //datos sw poe
                                                        idServicioSwPoe: data.idServicioSwPoe,
                                                        idInterfaceOnt: data.idInterfaceOnt,
                                                        idSwPoe: idSwPoeGpon,
                                                        nombreSwPoe: nombreSwPoeGpon,
                                                        puertosSwPoe: puertosSwPoe,
                                                        strMigrarSwPoe: data.strMigrarSwPoe,

                                                        //datos ap wifi
                                                        banderaWifiSafecity: data.esServicioWifiSafeCity,
                                                        nombreWifi: nombreWifi,
                                                        serieWifi: serieWifi,
                                                        modeloWifi: modeloWifi,

                                                        //Datos para WS
                                                        vlan: data.vlan,
                                                        anillo: data.anillo,
                                                        capacidad1: data.capacidadUno,
                                                        capacidad2: data.capacidadDos,

                                                        nombreProducto: data.nombreProducto,

                                                        strEsRadioExistente: strEsRadioExistente,
                                                        strEsCpeExistente: strEsCpeExistente,
                                                        strEsTransceiverExistente: strEsTransceiverExistente
                                                    },
                                                    success: function(res) {
                                                        const response = JSON.parse(res.responseText);
                                                        if (response.status === "OK") {
                                                            Ext.get(formPanel.getId()).unmask();
                                                            Ext.Msg.alert('Mensaje', 'Se realizó el cambio de puerto', function(btn) {
                                                                if (btn === 'ok') {
                                                                    win.destroy();
                                                                    store.load();
                                                                }
                                                            });
                                                        } else {
                                                            Ext.get(formPanel.getId()).unmask();
                                                            Ext.Msg.alert('Mensaje ', response.msg);
                                                        }
                                                    },
                                                    failure: function(result) {
                                                        Ext.get(formPanel.getId()).unmask();
                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                    }
                                                });
                                            } else {
                                                if (flag === 1) {
                                                    Ext.Msg.alert('Validación ', 'Debe seleccionar un puerto para realizar el cambio.');
                                                }
                                            }
                                        } //handler
                                },
                                {
                                    text: 'Cancelar',
                                    handler: function() {
                                        win.destroy();
                                    }
                                }
                            ]
                        });
                        nombreCamara = "cam-" + data.loginAux;
                        Ext.getCmp('nombreNuevoCamara').setValue = nombreCamara;
                        Ext.getCmp('nombreNuevoCamara').setRawValue(nombreCamara);
                        Ext.getCmp('nombreWifi').setValue = "ap-" + data.loginAux;
                        Ext.getCmp('nombreWifi').setRawValue("ap-" + data.loginAux);

                        if (data.servicioEnSwPoe === 'N') {
                            storeInterfacesPorEstadoYElemento.load();
                        }
                        if (data.esServicioCamaraSafeCity === 'S' && data.servicioEnSwPoe === 'S') {
                            storeInterfacesPorEstadoYElementoSwPoe.load();
                        }


                        var win = Ext.create('Ext.window.Window', {
                            title: 'Cambio de puerto servicio: ' + data.nombreProducto,
                            modal: true,
                            width: 1100,
                            closable: true,
                            layout: 'fit',
                            items: [formPanel]
                        }).show();

                        /*Validación para WIFI Safe City - [Se debe liberar y mandar a activar nuevamente.]*/
                        if (data.esServicioWifiSafeCity === 'S') {
                            Ext.getCmp('OntDatosSafecity').setVisible(true);
                            Ext.getCmp('SwPoeDatosSafecity').setVisible(false);
                            Ext.getCmp('nuevoCamara').setVisible(false);
                            Ext.getCmp('nuevoWifiAP').setVisible(true);

                            /*Definimos las variables dentro del cuadro de información.*/
                            Ext.getCmp('modeloWifi').setValue = data.modeloElementoCliente;
                            Ext.getCmp('modeloWifi').setRawValue(data.modeloElementoCliente);
                            Ext.getCmp('modeloWifi').setReadOnly(true);
                            Ext.getCmp('serieWifi').setValue = data.serieElementoCliente;
                            Ext.getCmp('serieWifi').setRawValue(data.serieElementoCliente);
                            Ext.getCmp('serieWifi').setReadOnly(true);

                        }

                        /*Validación para CAM en ONT - [Se debe liberar y mandar a activar nuevamente.]*/
                        if (data.esServicioCamaraSafeCity === 'S' && data.servicioEnSwPoe === 'N') {
                            Ext.getCmp('OntDatosSafecity').setVisible(true);
                            Ext.getCmp('SwPoeDatosSafecity').setVisible(false);
                            Ext.getCmp('nuevoCamara').setVisible(true);
                            Ext.getCmp('nuevoWifiAP').setVisible(false);

                            /*Definimos las variables dentro del cuadro de información.*/
                            Ext.getCmp('serieNuevoCamara').setValue = serieElementoCliente;
                            Ext.getCmp('serieNuevoCamara').setRawValue(serieElementoCliente);
                            Ext.getCmp('serieNuevoCamara').setReadOnly(true);
                            Ext.getCmp('modeloCamara').setValue = modeloElementoCliente;
                            Ext.getCmp('modeloCamara').setRawValue(modeloElementoCliente);
                            Ext.getCmp('modeloCamara').setReadOnly(true);

                        }

                        /*Validación para Switch PoE - [Se debe liberar y mandar a activar nuevamente.]*/
                        if (data.esServicioCamaraSafeCity === 'N' &&
                            data.esServicioWifiSafeCity === 'N' &&
                            data.descripcionProducto === 'SAFECITYSWPOE') {
                            Ext.getCmp('OntDatosSafecity').setVisible(true);
                            Ext.getCmp('SwPoeDatosSafecity').setVisible(false);
                            Ext.getCmp('nuevoCamara').setVisible(false);
                            Ext.getCmp('nuevoWifiAP').setVisible(false);
                        }

                        /*Validación para CAM en Switch PoE - [Solo se actualiza data en bdd.]*/
                        if (data.esServicioCamaraSafeCity === 'S' && data.servicioEnSwPoe === 'S') {
                            boolSeLiberanRecursos = false;
                            Ext.getCmp('OntDatosSafecity').setVisible(false);
                            Ext.getCmp('SwPoeDatosSafecity').setVisible(true);
                            Ext.getCmp('nuevoCamara').setVisible(false);
                            Ext.getCmp('nuevoWifiAP').setVisible(false);
                        }
                    }
                } //cierre response
        });
    } else if (data.strActivarSwPoeGpon === "S") {
        Ext.Msg.alert('Validacion ', 'El servicio Switch PoE GPON tiene que estar en estado Activo');
    } else {
        Ext.Msg.alert('Validacion ', 'El servicio principal Datos Safecity tiene que estar en estado Activo');
    }

}

function getCamaraPrincipalSwPoe(data) {
    data.forEach(element => {
        console.log(element.data)
    });

}

function cambiarPuertoMd(data, gridIndex) {
    const idServicio = data.idServicio;
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio
        },
        success: function(response) {
                Ext.get(gridServicios.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                var datos = json.encontrados;

                //-------------------------------------------------------------------------------------------

                //store para elementos (olt)
                var storeElementos = new Ext.data.Store({
                    pageSize: 100,
                    listeners: {
                        load: function() {

                        }
                    },
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getElementosPorTipo,
                        extraParams: {
                            idServicio: data.idServicio,
                            nombreElemento: this.nombreElemento,
                            tipoElemento: 'OLT',
                            marcaElemento: data.marcaElemento,
                            ldap: data.ldap,
                            validaTnp: 'SI'
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idElemento', mapping: 'idElemento' },
                        { name: 'nombreElemento', mapping: 'nombreElemento' },
                        { name: 'ipElemento', mapping: 'ip' }
                    ]
                });

                var storeInterfacesElemento = new Ext.data.Store({
                    pageSize: 100,
                    //                autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getInterfacesPorElemento,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idInterface', mapping: 'idInterface' },
                        { name: 'nombreInterface', mapping: 'nombreInterface' }
                    ]
                });

                var storeElementosContenedor = new Ext.data.Store({
                    pageSize: 100,
                    //                autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getElementosContenedoresPorPuerto,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idElementoContenedor', mapping: 'idElementoContenedor' },
                        { name: 'nombreElementoContenedor', mapping: 'nombreElementoContenedor' }
                    ]
                });

                var storeElementosConector = new Ext.data.Store({
                    pageSize: 100,
                    //                autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getElementosConectorPorElementoContenedor,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idElementoConector', mapping: 'idElementoConector' },
                        { name: 'nombreElementoConector', mapping: 'nombreElementoConector' }
                    ]
                });

                var storeInterfacesSplitter = new Ext.data.Store({
                    pageSize: 100,
                    //                autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url: getInterfacesPorElemento,
                        extraParams: {
                            idElemento: datos[0].idSplitter,
                            estado: 'not connect'
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields: [
                        { name: 'idInterface', mapping: 'idInterface' },
                        { name: 'nombreInterface', mapping: 'nombreInterface' }
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
                        columns: 2
                    },
                    defaults: {
                        // applied to each contained panel
                        bodyStyle: 'padding:20px'
                    },
                    items: [

                        //informacion de backbone anterior
                        {
                            xtype: 'fieldset',
                            title: 'Anterior',
                            defaultType: 'textfield',
                            defaults: {
                                width: 540,
                                height: 130
                            },
                            items: [

                                //gridInfoBackbone

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    },
                                    items: [
                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'Elemento',
                                            fieldLabel: 'Elemento',
                                            displayField: data.elementoNombre,
                                            value: data.elementoNombre,

                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            //                                id:'perfilDslam',
                                            name: 'ipElemento',
                                            fieldLabel: 'Ip Elemento',
                                            displayField: data.ipElemento,
                                            value: data.ipElemento,
                                            //displayField: "",
                                            //value: "",
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'interfaceElemento',
                                            fieldLabel: 'Puerto Elemento',
                                            displayField: data.interfaceElementoNombre,
                                            value: data.interfaceElementoNombre,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            //                                id:'perfilDslam',
                                            name: 'modeloELemento',
                                            fieldLabel: 'Modelo Elemento',
                                            displayField: data.modeloElemento,
                                            value: data.modeloElemento,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'splitterElemento',
                                            fieldLabel: 'Splitter Elemento',
                                            displayField: datos[0].nombreSplitter,
                                            value: datos[0].nombreSplitter,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            id: 'splitterInterfaceElemento',
                                            name: 'splitterInterfaceElemento',
                                            fieldLabel: 'Splitter Interface',
                                            displayField: datos[0].nombrePuertoSplitter,
                                            value: datos[0].nombrePuertoSplitter,
                                            width: '25%',

                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'cajaElemento',
                                            fieldLabel: 'Caja Elemento',
                                            displayField: datos[0].nombreCaja,
                                            value: datos[0].nombreCaja,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        { width: '10%', border: false },
                                        { width: '10%', border: false }

                                    ]
                                }

                            ]
                        }, //cierre de info de backbone anterior

                        //informacion de backbone nuevo
                        {
                            xtype: 'fieldset',
                            title: 'Nuevo',
                            defaultType: 'textfield',
                            defaults: {
                                width: 540,
                                height: 130
                            },
                            items: [
                                //gridInfoBackbone
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    },
                                    items: [
                                        { width: '10%', border: false },
                                        {
                                            //queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'nombreElementoNuevo',
                                            name: 'nombreElementoNuevo',
                                            fieldLabel: 'Elemento',
                                            displayField: 'nombreElemento',
                                            valueField: 'idElemento',
                                            loadingText: 'Buscando...',
                                            width: '25%',
                                            queryMode: "remote",
                                            lazyRender: true,
                                            forceSelection: true,
                                            emptyText: 'Ingrese nombre Olt..',
                                            minChars: 3,
                                            typeAhead: true,
                                            triggerAction: 'all',
                                            selectOnTab: true,
                                            listClass: 'x-combo-list-small',
                                            store: storeElementos,
                                            listeners: {
                                                select: function(combo) {
                                                    for (var i = 0; i < storeElementos.data.items.length; i++) {
                                                        if (storeElementos.data.items[i].data.idElemento == combo.getValue()) {
                                                            //                                                        //console.log(storeElementos.data.items[i].data.ipElemento);
                                                            Ext.getCmp('ipElementoNuevo').setRawValue(storeElementos.data.items[i].data.ipElemento);
                                                            break;
                                                        }
                                                    }

                                                    storeInterfacesElemento.proxy.extraParams = { idElemento: combo.getValue() };  
                                                    storeInterfacesElemento.load({ params: {} });
                                                }
                                            }
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            id: 'ipElementoNuevo',
                                            name: 'ipElementoNuevo',
                                            fieldLabel: 'Ip Elemento',
                                            displayField: '',
                                            value: '',
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'interfaceElementoNuevo',
                                            name: 'interfaceElementoNuevo',
                                            fieldLabel: 'Puerto Elemento',
                                            displayField: 'nombreInterface',
                                            valueField: 'idInterface',
                                            loadingText: 'Buscando...',
                                            store: storeInterfacesElemento,
                                            listeners: {
                                                select: function(combo) {
                                                    storeElementosContenedor.proxy.extraParams = {
                                                        idInterfaceElemento: combo.getValue()
                                                    };  
                                                    storeElementosContenedor.load({ params: {} });
                                                }
                                            },
                                            width: '25%',
                                        },
                                        { width: '15%', border: false },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'cajaElementoNuevo',
                                            name: 'cajaElementoNuevo',
                                            fieldLabel: 'Caja Elemento',
                                            displayField: 'nombreElementoContenedor',
                                            valueField: 'idElementoContenedor',
                                            loadingText: 'Buscando...',
                                            store: storeElementosContenedor,
                                            listeners: {
                                                select: function(combo) {
                                                    storeElementosConector.proxy.extraParams = {
                                                        idElementoContenedor: combo.getValue(),
                                                        idServicio: idServicio
                                                    };  
                                                    storeElementosConector.load({ params: {} });
                                                }
                                            },
                                            width: '25%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'splitterElementoNuevo',
                                            name: 'splitterElementoNuevo',
                                            fieldLabel: 'Splitter Elemento',
                                            displayField: 'nombreElementoConector',
                                            valueField: 'idElementoConector',
                                            loadingText: 'Buscando...',
                                            store: storeElementosConector,
                                            listeners: {
                                                select: function(combo) {
                                                    storeInterfacesSplitter.proxy.extraParams = {
                                                        idElemento: combo.getValue(),
                                                        estado: 'not connect'
                                                    };  
                                                    storeInterfacesSplitter.load({ params: {} });
                                                }
                                            },
                                            width: '25%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'splitterInterfaceElementoNuevo',
                                            name: 'splitterInterfaceElementoNuevo',
                                            fieldLabel: 'Splitter Interface',
                                            displayField: 'nombreInterface',
                                            valueField: 'idInterface',
                                            loadingText: 'Buscando...',
                                            store: storeInterfacesSplitter,
                                            width: '25%'
                                        },
                                        { width: '10%', border: false },

                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        { width: '10%', border: false },
                                        { width: '15%', border: false },
                                        { width: '10%', border: false },
                                        { width: '10%', border: false }

                                    ]
                                }
                            ]
                        }, //cierre de info de backbone nuevo

                    ],
                    buttons: [{
                        text: 'Ver Enlaces',
                        formBind: true,
                        handler: function() {
                            var validacion = false;
                            var elementoId = Ext.getCmp('nombreElementoNuevo').getValue();
                            var interfaceElementoId = Ext.getCmp('interfaceElementoNuevo').getValue();
                            var elementoCajaId = Ext.getCmp('cajaElementoNuevo').getValue();
                            var elementoSplitterId = Ext.getCmp('splitterElementoNuevo').getValue();
                            var interfaceElementoSplitterId = Ext.getCmp('splitterInterfaceElementoNuevo').getValue();

                            if (elementoId && interfaceElementoId && elementoCajaId && elementoSplitterId && interfaceElementoSplitterId) {
                                validacion = true;
                            }

                            if (validacion) {

                                Ext.get(formPanel.getId()).mask('Consultando enlaces!');
                                Ext.Ajax.request({
                                    url: getTrazabilidadElementos,
                                    method: 'post',
                                    timeout: 400000,
                                    params: {
                                        interfaceElementoSplitterId: interfaceElementoSplitterId,
                                        tipoElementoPadre: 'OLT'
                                    },
                                    success: function(response) {

                                        Ext.get(formPanel.getId()).unmask();
                                        var json = Ext.JSON.decode(response.responseText);

                                        if (json.status == "OK") {
                                            verTrazabilidadElementos(interfaceElementoSplitterId, data, win);
                                        } else {
                                            Ext.Msg.alert('Mensaje ', json.mensaje);
                                        }
                                    },
                                    failure: function(result) {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });

                            } else {
                                Ext.Msg.alert("Failed", "Todos los campos son obligatorios", function(btn) {
                                    if (btn == 'ok') {
                                        store.load();
                                    }
                                });
                            }

                        }
                    }, {
                        text: 'Cancelar',
                        handler: function() {
                            win.destroy();
                        }
                    }]
                });
                var win = Ext.create('Ext.window.Window', {
                    title: 'Cambiar de Elemento',
                    modal: true,
                    width: 1200,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

                //            storeElementos.load({
                //                callback:function(){ 
                //                }
                //            });

            } //cierre response

    });
}