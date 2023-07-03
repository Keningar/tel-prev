/**
 * Funcion que sirve para crear la solicitud de cambio de ultima milla
 * 
 * @author Francisco Adum
 * @version 1.0 24-06-2016
 * */
function crearSolicitudCambioUM(data)
{
    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Crear la Solicitud de Cambio de Ultima Milla?', function(btn)
    {
        if (btn === 'ok')
        {
            Ext.get(gridServicios.getId()).mask();
            Ext.Ajax.request
            ({
                url:        urlCrearSolicitudCambioUM,
                method:     'post',
                timeout:    400000,
                params: 
                {
                    idServicio: data.idServicio
                },
                success: function(response) 
                {
                    Ext.get(gridServicios.getId()).unmask();
                    var respuesta = response.responseText.split("|");
                    var status    = respuesta[0];
                    var mensaje   = respuesta[1];
                    if (status === "OK")
                    {
                        Ext.Msg.alert('Mensaje', 'Se crearon las Solicitudes de Cambio de UM para los servicios con '+
                                                 'misma UM: <b>'+mensaje+'</b>', function(btn)
                        {
                            if (btn === 'ok')
                            {
                                store.load();
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje', response.responseText, function(btn)
                        {
                            if (btn === 'ok')
                            {
                                store.load();
                            }
                        });
                    }
                },
                failure: function(result)
                {
                    Ext.get(gridServicios.getId()).unmask();
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}

    function ejecutaCambioUM(data)
{
    if (data.ultimaMilla == "Radio" )
    {
        etiquetaElementoConector = "Radio BackBone";
    }
    else
    {
        etiquetaElementoConector = "Cassette";
    }
        
    Ext.get("grid").mask('Consultando Datos...');
    if(data.descripcionProducto == "L3MPLS")
    {
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
                                height: 120
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
                                            fieldLabel: 'Mac Cpe',
                                            displayField: datosBackbone.mac,
                                            value: datosBackbone.mac,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'um',
                                            name: 'um',
                                            fieldLabel: 'Ultima Milla',
                                            displayField: data.ultimaMilla,
                                            value: data.ultimaMilla,
                                            fieldStyle: 'color: green;',
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'macRadio',
                                            name: 'macRadio',
                                            fieldLabel: 'Mac Radio Cliente',
                                            displayField: datosBackbone.macRadio,
                                            value: datosBackbone.macRadio,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        { width: '30%', border: false},
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
                                            id: 'protocolo',
                                            name: 'protocolo',
                                            fieldLabel: 'Protocolo',
                                            displayField: data.protocolo,
                                            value: data.protocolo,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
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
                                        { width: '10%', border: false},

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'ipServicio',
                                            name: 'ipServicio',
                                            fieldLabel: 'Ip WAN',
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
                            Ext.Msg.confirm('Mensaje','Esta seguro que desea ejecutar el Cambio de Ultima Milla?', function(btn){
                                if(btn==='yes')
                                {
                                    Ext.get(formPanel.getId()).mask('Ejecutando Cambio de Ultima Milla...');

                                    Ext.Ajax.request({
                                        url: cambiarUltimaMilla,
                                        method: 'post',
                                        timeout: 1000000,
                                        params: { 
                                            idServicio  : data.idServicio,
                                            //Datos para WS
                                            mac         : datosBackbone.mac,
                                            macRadio    : datosBackbone.macRadio,
                                            vlan        : data.vlan,
                                            anillo      : data.anillo,
                                            capacidad1  : data.capacidadUno,
                                            capacidad2  : data.capacidadDos,
                                            ultimaMilla : data.ultimaMilla
                                        },
                                        success: function(response){
                                            Ext.get(formPanel.getId()).unmask();
                                            if(response.responseText === "OK")
                                            {
                                                Ext.Msg.alert('Mensaje','Se realizó el Cambio de Ultima Milla exitosamente', function(btn){
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
                    title: 'Cambio de Ultima Milla',
                    modal: true,
                    width: 620,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
                
                if (data.ultimaMilla != "Radio")
                {
                    Ext.getCmp('macRadio').setVisible(false);
                }
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
    else
    {
        Ext.Ajax.request({
            url: getDatosBackbone,
            method: 'post',
            timeout: 400000,
            params: {
                idServicio: data.idServicio,
                tipoElementoPadre: 'ROUTER'
            },
            success: function(response) {
                Ext.get("grid").unmask();

                var json = Ext.JSON.decode(response.responseText);
                var datosBackbone = json.encontrados[0];

                if(datosBackbone.idElementoPadre == 0)
                {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: datosBackbone.nombreElementoPadre,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
                else
                {
                    var formPanel = Ext.create('Ext.form.Panel', {
                        bodyPadding: 2,
                        waitMsgTarget: true,
                        fieldDefaults: {
                            labelAlign: 'left',
                            labelWidth: 85,
                            msgTarget: 'side'
                        },
                        items: [{
                                xtype: 'fieldset',
                                title: 'Cambio Ultima Milla',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 620
                                },
                                items: [
                                    //informacion del cliente
                                    {
                                        xtype: 'fieldset',
                                        title: 'Información de Servicio',
                                        defaultType: 'textfield',
                                        defaults: {
                                            width: 600
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
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'nombreCompleto',
                                                        fieldLabel: 'Cliente',
                                                        displayField: data.nombreCompleto,
                                                        value: data.nombreCompleto,
                                                        readOnly: true,
                                                        width: 300
                                                    },
                                                    {width: '10%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'login',
                                                        fieldLabel: 'Login',
                                                        displayField: data.login,
                                                        value: data.login,
                                                        readOnly: true,
                                                        width: '40%'
                                                    },
                                                    {width: '5%', border: false},

                                                    //---------------------------------------------

                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'producto',
                                                        fieldLabel: 'Producto',
                                                        displayField: data.nombreProducto,
                                                        value: data.nombreProducto,
                                                        readOnly: true,
                                                        width: 300
                                                    },
                                                    {width: '10%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'tipoOrden',
                                                        fieldLabel: 'Tipo Orden',
                                                        displayField: data.tipoOrdenCompleto,
                                                        value: data.tipoOrdenCompleto,
                                                        readOnly: true,
                                                        width: '40%'
                                                    },
                                                    {width: '5%', border: false},

                                                    //-----------------------------------------

                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',                                              
                                                        name: 'capacidadUno',
                                                        fieldLabel: 'Capacidad Uno',
                                                        displayField: data.capacidadUno,
                                                        value: data.capacidadUno,                                                
                                                        readOnly: true,
                                                        width: '40%'
                                                    },
                                                    {width: '10%', border: false},
                                                    {
                                                        xtype: 'textfield',                                                
                                                        name: 'capacidadDos',
                                                        fieldLabel: 'Capacidad Dos',
                                                        displayField: data.capacidadDos,
                                                        value: data.capacidadDos,                                                
                                                        readOnly: true,
                                                        width: '40%'
                                                    },
                                                    { width: '10%', border: false},

                                                    { width: '10%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        id: 'mac',
                                                        name: 'mac',
                                                        fieldLabel: 'Mac Cpe',
                                                        displayField: datosBackbone.mac,
                                                        value: datosBackbone.mac,
                                                        readOnly: true,
                                                        width: '30%'
                                                    },
                                                    { width: '15%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        id: 'um',
                                                        name: 'um',
                                                        fieldLabel: 'Ultima Milla',
                                                        displayField: data.ultimaMilla+"/"+datosBackbone.tipoBackbone,
                                                        value: data.ultimaMilla+"/"+datosBackbone.tipoBackbone,
                                                        fieldStyle: 'color: green;',
                                                        readOnly: true,
                                                        width: '30%'
                                                    },
                                                    { width: '10%', border: false},
                                                    
                                                    //---------------------------------------------
                                                    { width: '10%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        id: 'macRadio',
                                                        name: 'macRadio',
                                                        fieldLabel: 'Mac Radio Cliente',
                                                        displayField: datosBackbone.macRadio,
                                                        value: datosBackbone.macRadio,
                                                        readOnly: true,
                                                        width: '30%'
                                                    },
                                                    { width: '15%', border: false},
                                                    { width: '30%', border: false},
                                                    { width: '10%', border: false},
                                                    { width: '10%', border: false},
                                                    
                                                ]
                                            }

                                        ]
                                    }, //cierre de la informacion del cliente

                                    //informacion del servicio/producto
                                    {
                                        xtype: 'fieldset',
                                        title: 'Informacion de Backbone Actual',
                                        defaultType: 'textfield',
                                        defaults: {
                                            width: 600,
                                            height: 120
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
                                                    {width: 10, border: false},
                                                    {
                                                        xtype: 'textfield',                                                
                                                        name: 'elementoPadre',
                                                        fieldLabel: 'PE',
                                                        displayField: datosBackbone.nombreElementoPadre,
                                                        value: datosBackbone.nombreElementoPadre,                                                
                                                        readOnly: true,
                                                        width: 350
                                                    },
                                                    {width: 10, border: false},
                                                    {
                                                        xtype: 'textfield',                                               
                                                        name: 'vlan',
                                                        fieldLabel: 'Vlan',
                                                        displayField: data.vlan,
                                                        value: data.vlan,
                                                        readOnly: true,
                                                        width: 150
                                                    },
                                                    {width: '0%', border: false},

                                                    //---------------------------------------------

                                                    {width: 10, border: false},
                                                    {
                                                        xtype: 'textfield',                                                
                                                        name: 'elemento',
                                                        fieldLabel: 'Switch',
                                                        displayField: datosBackbone.nombreElemento,
                                                        value: datosBackbone.nombreElemento,                                                
                                                        readOnly: true,
                                                        width: 350
                                                    },
                                                    {width: 10, border: false},
                                                    {
                                                        xtype: 'textfield',                                                
                                                        name: 'interface',
                                                        fieldLabel: 'Interface',
                                                        displayField: datosBackbone.nombreInterfaceElemento,
                                                        value: datosBackbone.nombreInterfaceElemento,
                                                        readOnly: true,
                                                        width: 150
                                                    },
                                                    {width: '0%', border: false},

                                                    //---------------------------------------------

                                                    {width: 10, border: false},
                                                    {
                                                        xtype: 'textfield',                                                
                                                        name: 'elementoConector',
                                                        fieldLabel: etiquetaElementoConector,
                                                        displayField: datosBackbone.nombreSplitter,
                                                        value: datosBackbone.nombreSplitter,
                                                        readOnly: true,
                                                        width: 350
                                                    },
                                                    {width: 10, border: false},
                                                    {
                                                        xtype: 'textfield',                                               
                                                        name: 'hilo',
                                                        fieldLabel: 'Hilo',
                                                        id: 'hilo',
                                                        displayField: datosBackbone.colorHilo,
                                                        value: datosBackbone.colorHilo,
                                                        readOnly: true,
                                                        width: 150
                                                    },
                                                    {width: '0%', border: false},

                                                    //---------------------------------------------

                                                    {width: 10, border: false},
                                                    {
                                                        xtype: 'textfield',                                               
                                                        name: 'elementoContenedor',
                                                        fieldLabel: 'Caja',
                                                        id: 'caja',
                                                        displayField: datosBackbone.nombreCaja,
                                                        value: datosBackbone.nombreCaja,
                                                        readOnly: true,
                                                        width: 400
                                                    },
                                                    {width: '10%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'tipoEnlace',
                                                        fieldLabel: 'Tipo Enlace',
                                                        displayField: data.tipoEnlace,
                                                        value: data.tipoEnlace,
                                                        readOnly: true,
                                                        width: 175
                                                    },
                                                    {width: '15%', border: false}
                                                ]
                                            }
                                        ]
                                    } //cierre de la informacion servicio/producto
                                ]
                            }],
                        buttons: [{
                                text: 'Ejecutar',
                                formBind: true,
                                handler: function() {
                                    Ext.Msg.alert('Mensaje', 'Esta seguro que desea ejecutar el Cambio de Ultima Milla?', function(btn) {
                                        if (btn == 'ok') {
                                            Ext.get(formPanel.getId()).mask('Ejecutando Cambio...');
                                            Ext.Ajax.request({
                                                url: cambiarUltimaMilla,
                                                method: 'post',
                                                timeout: 400000,
                                                params: {
                                                    idServicio: data.idServicio,
                                                    //Datos para WS
                                                    mac         : datosBackbone.mac,
                                                    macRadio    : datosBackbone.macRadio,
                                                    vlan        : data.vlan,
                                                    anillo      : data.anillo,
                                                    capacidad1  : data.capacidadUno,
                                                    capacidad2  : data.capacidadDos,
                                                    ultimaMilla : data.ultimaMilla,
                                                },
                                                success: function(response) {
                                                    Ext.get(formPanel.getId()).unmask();
                                                    if (response.responseText == "OK") {
                                                        Ext.Msg.alert('Mensaje', 'Se realizó el Cambio de Ultima Milla', function(btn) {
                                                            if (btn == 'ok') {
                                                                store.load();
                                                                win.destroy();
                                                            }
                                                        });
                                                    }
                                                    else {
                                                        Ext.Msg.alert('Mensaje ', response.responseText);
                                                    }
                                                },
                                                failure: function(result)
                                                {
                                                    Ext.get(formPanel.getId()).unmask();
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

                    var win = Ext.create('Ext.window.Window', {
                        title: 'Cambio de Ultima Milla',
                        modal: true,
                        width: 680,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();
                }
                if (data.ultimaMilla != "Radio")
                {
                    Ext.getCmp('macRadio').setVisible(false);
                }
                else
                {
                    Ext.getCmp('caja').setDisabled(true);
                    Ext.getCmp('hilo').setDisabled(true);
                }
            }//cierre response
        }); 
    }
}


function ejecutaCambioElementoPasivo(data)
{

    var storeHilosDisponibles = new Ext.data.Store({
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: getHilosDisponibles,
            extraParams: {
                idElemento: '',
                estadoInterface: 'connected',
                estadoInterfaceNotConect: 'not connect',
                estadoInterfaceReserved: 'not connect',
                strBuscaHilosServicios: 'NO',
                intIdPunto: '',
                strTipoEnlace: data.tipoEnlace
            },
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                {name: 'idInterfaceElementoOut', mapping: 'idInterfaceElementoOut'},
                {name: 'colorHilo', mapping: 'colorHilo'},
                {name: 'numeroHilo', mapping: 'numeroHilo'},
                {name: 'numeroColorHilo', mapping: 'numeroColorHilo'}
            ]
    });


    Ext.get("grid").mask('Consultando Datos...');

    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function(response) {
            Ext.get("grid").unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datosBackbone = json.encontrados[0];
            
            storeElementosByPuerto = new Ext.data.Store({
                total: 'total',
                pageSize: 10000,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: urlgetElementosByPuerto,
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        puertoElemento: datosBackbone.idInterfaceElemento,
                        tipoElemento: 'CASSETTE',
                        estado: 'Activo'                
                    }
                },
                fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
            });

            
            if (datosBackbone.tipoBackbone != 'RUTA')
            {
                Ext.Msg.alert('Error ', 'El tipo de factibilidad debe ser RUTA');
                return false;
            }

            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [{
                        xtype: 'fieldset',
                        title: 'Cambio Elemento Pasivo',
                        defaultType: 'textfield',
                        defaults: {
                            width: 620
                        },
                        items: [
                            //informacion del cliente
                            {
                                xtype: 'fieldset',
                                title: 'Información de Servicio',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 600
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
                                            {width: '5%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'nombreCompleto',
                                                fieldLabel: 'Cliente',
                                                displayField: data.nombreCompleto,
                                                value: data.nombreCompleto,
                                                readOnly: true,
                                                width: 300
                                            },
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'login',
                                                fieldLabel: 'Login',
                                                displayField: data.login,
                                                value: data.login,
                                                readOnly: true,
                                                width: '40%'
                                            },
                                            {width: '5%', border: false},
                                            //---------------------------------------------

                                            {width: '5%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'producto',
                                                fieldLabel: 'Producto',
                                                displayField: data.nombreProducto,
                                                value: data.nombreProducto,
                                                readOnly: true,
                                                width: 300
                                            },
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'tipoEnlace',
                                                fieldLabel: 'Tipo Enlace',
                                                displayField: data.tipoEnlace,
                                                value: data.tipoEnlace,
                                                readOnly: true,
                                                width: '40%'
                                            },
                                            {width: '5%', border: false},
                                            //-----------------------------------------

                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                id: 'mac',
                                                name: 'mac',
                                                fieldLabel: 'Mac Cpe',
                                                displayField: datosBackbone.mac,
                                                value: datosBackbone.mac,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                id: 'um',
                                                name: 'um',
                                                fieldLabel: 'Ultima Milla',
                                                displayField: data.ultimaMilla + "/" + datosBackbone.tipoBackbone,
                                                value: data.ultimaMilla + "/" + datosBackbone.tipoBackbone,
                                                fieldStyle: 'color: green;',
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {width: '10%', border: false},
                                            //---------------------------------------------

                                            {width: '15%', border: false},
                                            {width: '30%', border: false},
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                        ]
                                    }

                                ]
                            }, //cierre de la informacion del cliente
                            
                            {
                                xtype: 'fieldset',
                                title: 'Informacion de Backbone Actual',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 600
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
                                            {width: '5%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'elemento',
                                                fieldLabel: 'Switch',
                                                displayField: datosBackbone.nombreElemento,
                                                value: datosBackbone.nombreElemento,
                                                readOnly: true,
                                                width: 350                                            
                                            },
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'interface',
                                                fieldLabel: 'Interface',
                                                displayField: datosBackbone.nombreInterfaceElemento,
                                                value: datosBackbone.nombreInterfaceElemento,
                                                readOnly: true,
                                                width: 160
                                            },
                                            {width: '5%', border: false},
                                            //---------------------------------------------

                                            {width: '5%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'interface',
                                                fieldLabel: 'Caja',
                                                displayField: datosBackbone.nombreCaja,
                                                value: datosBackbone.nombreCaja,
                                                readOnly: true,
                                                width: 350
                                            },
                                            {width: '10%', border: false},
                                            {
                                                xtype: 'hidden',
                                            },
                                            {width: '5%', border: false},
                                            //-----------------------------------------


                                            {width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'elementoConector',
                                                fieldLabel: 'CASSETTE',
                                                displayField: datosBackbone.nombreSplitter,
                                                value: datosBackbone.nombreSplitter,
                                                readOnly: true,
                                                width: 350
                                            },   
                                            {width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'hilo',
                                                fieldLabel: 'Hilo',
                                                id: 'hilo',
                                                displayField: datosBackbone.colorHilo,
                                                value: datosBackbone.colorHilo,
                                                readOnly: true,
                                                width: 160
                                            },
                                            {width: '10%', border: false},
                                            //---------------------------------------------

                                            {width: '15%', border: false},
                                            {width: '30%', border: false},
                                            {width: '10%', border: false},
                                            {width: '10%', border: false},
                                        ]
                                    }

                                ]
                            }, //cierre de la informacion del cliente

                            //informacion del servicio/producto
                            {
                                xtype: 'fieldset',
                                title: 'Informacion de Backbone Nueva',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 600,
                                    height: 80
                                },
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 1
                                        },
                                        items: [
                                            {
                                                xtype: 'combobox',
                                                id: 'cbxElementoPNivel',
                                                name: 'cbxElementoPNivel',
                                                fieldLabel: '* CASSETTE',
                                                typeAhead: true,
                                                width: 470,
                                                queryMode: "local",
                                                triggerAction: 'all',
                                                displayField: 'nombreElemento',
                                                valueField: 'idElemento',
                                                selectOnTab: true,
                                                store: storeElementosByPuerto,
                                                lazyRender: true,
                                                listClass: 'x-combo-list-small',
                                                emptyText: 'Seleccione un CASSETTE',
                                                labelStyle: "color:red;",
                                                
                                                editable: false,                                                
                                                listeners: {
                                                    select: {fn: function(combo, value) {
                                                            
                                                            Ext.getCmp('cbxPuertos').reset();
                                                            Ext.getCmp('cbxPuertos').setDisabled(false);
                                                            
                                                            storeHilosDisponibles.proxy.extraParams = {                                                                
                                                                
                                                                idElemento: combo.getValue(),
                                                                estadoInterface: 'connected',
                                                                estadoInterfaceNotConect: 'not connect',
                                                                estadoInterfaceReserved: 'not connect',
                                                                strBuscaHilosServicios: 'NO',
                                                                intIdPunto: '',
                                                                strTipoEnlace: data.tipoEnlace
                                                            };
                                                            storeHilosDisponibles.load({params: {}});                                                            
                                                        }
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'combobox',
                                                id: 'cbxPuertos',
                                                name: 'cbxPuertos',
                                                store: storeHilosDisponibles,
                                                queryMode: 'local',
                                                fieldLabel: '* HILOS',
                                                labelStyle: "color:red;",
                                                displayField: 'numeroColorHilo',
                                                valueField: 'idInterfaceElementoOut',
                                                emptyText: 'Seleccione el Hilo..',
                                                disabled: true,
                                                listeners: {
                                                    select: {fn: function(combo, value) {
                                                            Ext.MessageBox.show({
                                                                msg: 'Procesando...',
                                                                width: 300,
                                                                wait: true,
                                                                waitConfig: {interval: 400}
                                                            });
                                                            Ext.Ajax.request({
                                                                url: urlInfoCaja,
                                                                method: 'post',
                                                                timeout: 400000,
                                                                params: {
                                                                    intIdElementoContenedor: '',
                                                                    intIdElementoDistribucion: combo.getValue(),
                                                                    strTipoBusqueda: 'INTERFACE',
                                                                    strNombreElementoPadre: 'SWITCH'
                                                                },
                                                                success: function(response) {
                                                                    
                                                                    objInformacionElemento = Ext.JSON.decode(response.responseText);

                                                                    if (objInformacionElemento.error > 0) {
                                                                        Ext.getCmp('cbxPuertos').setValue("");
                                                                        Ext.MessageBox.show({
                                                                            title: 'Error',
                                                                            msg: objInformacionElemento.msg,
                                                                            buttons: Ext.MessageBox.OK,
                                                                            icon: Ext.MessageBox.ERROR
                                                                        });
                                                                    } else {

                                                                        //si trajo informacion verifico si coincide con el puerto del sw
                                                                        if (objInformacionElemento.linea != datosBackbone.nombreInterfaceElemento)
                                                                        {
                                                                            Ext.getCmp('cbxPuertos').setValue("");
                                                                            Ext.MessageBox.show({
                                                                                title: 'Error',
                                                                                msg: 'Este hilo no esta enlazado correctamente al puerto.',
                                                                                buttons: Ext.MessageBox.OK,
                                                                                icon: Ext.MessageBox.ERROR
                                                                            });
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.MessageBox.hide();
                                                                        }

                                                                    }                                                                    
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.MessageBox.show({
                                                                        title: 'Error',
                                                                        msg: result.statusText,
                                                                        buttons: Ext.MessageBox.OK,
                                                                        icon: Ext.MessageBox.ERROR
                                                                    });
                                                                }
                                                            });
                                                            
                                                        }
                                                    }
                                                }
                                            }                                            
                                        ]
                                    }
                                ]
                            } //cierre de la informacion servicio/producto
                        ]
                    }],
                buttons: [{
                        text: 'Ejecutar',
                        formBind: true,
                        handler: function() {
                                                                
                            var idElementoConector  = Ext.getCmp('cbxElementoPNivel').getValue();
                            var idHilo              = Ext.getCmp('cbxPuertos').getValue();
                            var validacion          = true;

                            if(idElementoConector == '' || idElementoConector == 'undefined' || idElementoConector == null)
                            {
                                Ext.Msg.alert('Alerta ', 'Favor seleccionar el Cassette.');
                                validacion = false;
                            }

                            if(idHilo == '' || idHilo == 'undefined' || idHilo == null)
                            {
                                Ext.Msg.alert('Alerta ', 'Favor seleccionar el Hilo.');
                                validacion = false;
                            }  

                            if (validacion)
                            {

                                Ext.Msg.alert('Mensaje', 'Esta seguro que desea ejecutar el Cambio de Elemento Pasivo?', function(btn) {
                                    if (btn == 'ok') {

                                        Ext.get(formPanel.getId()).mask('Ejecutando Cambio...');

                                        Ext.Ajax.request({
                                            url: urlCambiarElementoPasivo,
                                            method: 'post',
                                            timeout: 400000,
                                            params: {
                                                idServicio: data.idServicio,
                                                idElementoConector: idElementoConector,
                                                idHilo: idHilo,
                                                strElementoConector: datosBackbone.nombreSplitter,
                                                strHilo: datosBackbone.colorHilo,
                                                strElementoConectorNuevo: Ext.getCmp('cbxElementoPNivel').getRawValue(),
                                                strHiloNuevo: Ext.getCmp('cbxPuertos').getRawValue()
                                            },
                                            success: function(response) {
                                                Ext.get(formPanel.getId()).unmask();
                                                var content = JSON.parse(response.responseText);

                                                if (content.status != "ERROR") {
                                                    Ext.Msg.alert('Mensaje', 'Se realizó el Cambio de Elemento Pasivo', function(btn) {
                                                        if (btn == 'ok') {
                                                            store.load();
                                                            win.destroy();
                                                        }
                                                    });
                                                }                                                
                                                else {
                                                    Ext.Msg.alert('Mensaje ', content.mensaje);
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });

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
                title: 'Cambio de Elemento Pasivo',
                modal: true,
                width: 680,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();

        }//cierre response
    });

}

/**
 * Función para reversar la solicitud de cambio de UM
 *
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.0 18-03-2020
 * */
function reversarSolicitudCambioUM(data)
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
                url: urlReversarSolicitudCambioUM,
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
                title: 'Reversar la Solicitud de Cambio de Ultima Milla',
                autoHeight: true,
                width: 475,
                items:
                [
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
        title: "Reversar la Solicitud de Cambio de Ultima Milla",
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