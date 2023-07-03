
function cortarWifi(data,idAccion){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosCliente,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            
            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;
            
            var storeMotivos = new Ext.data.Store({  
                pageSize: 50,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url : getMotivos,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        accion: "cortarCliente"
                    }
                },
                fields:
                    [
                      {name:'idMotivo', mapping:'idMotivo'},
                      {name:'nombreMotivo', mapping:'nombreMotivo'}
                    ]
            });
            
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
                title: 'Corte Servicio',
                defaultType: 'textfield',
                defaults: {
                    width: 520
                },
                items: [

                    //informacion del cliente
                    {
                        xtype: 'fieldset',
                        title: 'Información Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500
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
                                        name: 'nombreCompleto',
                                        fieldLabel: 'Cliente',
                                        displayField: datos[0].nombreCompleto,
                                        value: datos[0].nombreCompleto,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'tipoNegocio',
                                        fieldLabel: 'Tipo Negocio',
                                        displayField: datos[0].tipoNegocio,
                                        value: datos[0].tipoNegocio,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        name: 'direccion',
                                        fieldLabel: 'Direccion',
                                        displayField: datos[0].direccion,
                                        value: datos[0].direccion,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '30%', border: false},
                                    { width: '10%', border: false},

                                    //---------------------------------------------
                                ]
                            }

                        ]
                    },//cierre de la informacion del cliente

                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Información Servicio',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500,
                            height: 50
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
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'capacidadUno',
                                        fieldLabel: 'Capacidad Uno',
                                        displayField: data.capacidadUno,
                                        value: data.capacidadUno,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                ]
                            }

                        ]
                    },//cierre de la informacion servicio/producto

                    //motivo de cancelacion
                    {
                        xtype: 'fieldset',
                        title: 'Motivo Corte',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500
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
                                        xtype: 'combo',
                                        id:'comboMotivos',
                                        name: 'comboMotivos',
                                        store: storeMotivos,
                                        fieldLabel: 'Motivo',
                                        displayField: 'nombreMotivo',
                                        valueField: 'idMotivo',
                                        queryMode: 'local'
                                    },
                                    { width: '15%', border: false},
                                    { width: '30%', border: false},
                                    { width: '10%', border: false},

                                    //---------------------------------------------
                                ]
                            }

                        ]
                    }//cierre del motivo de cancelacion

                ]
            }],
            buttons: [{
                text: 'Ejecutar',
                formBind: true,
                handler: function(){
                    
                    var motivo = Ext.getCmp('comboMotivos').getValue();
                    var validacion = false;
                    
                    if(motivo!=null){
                        validacion=true;
                    }
                    
                    if(validacion){
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Cortar el Servicio?', function(btn){
                            if(btn=='ok'){
                                Ext.MessageBox.wait("Esperando Respuesta del Elemento...");
                                Ext.Ajax.request({
                                    url: cortarWifiCliente,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio: data.idServicio,
                                        idProducto: data.productoId,
                                        perfil:     data.perfilDslam,
                                        login:      data.login,
                                        capacidad1: data.capacidadUno,
                                        capacidad2: data.capacidadDos,
                                        motivo:     motivo,
                                        idAccion:   idAccion
                                    },
                                    success: function(response){
                                        Ext.get(gridServicios.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Corto el Servicio', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else if(response.responseText=="NO EXISTE TAREA"){
                                            Ext.Msg.alert('Mensaje ','No existe la Tarea, favor revisar' );
                                        }
                                        else if(response.responseText=="OK SIN EJECUCION"){
                                            Ext.Msg.alert('Mensaje ','Se Corto el Servicio, Sin ejecutar Script' );
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje ',response.responseText );
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(gridServicios.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert("Advertencia","Favor Escoja un Motivo", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }
                    

                }
            },{
                text: 'Cancelar',
                handler: function(){
                    win.destroy();
                }
            }]
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'Cortar Servicio',
            modal: true,
            width: 580,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
            
        }//cierre response
    }); 
}


function activacionWifi(data, gridIndex){

    var iniHtmlCamposRequeridos = '<p style="text-align: left; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
    CamposRequeridos = Ext.create('Ext.Component', {
        html: iniHtmlCamposRequeridos,
        padding: 1,
        layout: 'anchor',
        style: {color: 'black', textAlign: 'left', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });
    
        Ext.get(gridServicios.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({ 
            url: getDatosBackbone,
            method: 'post',
            timeout: 400000,
            params: { 
                idServicio: data.idServicio
            },
            success: function(response){
                Ext.get(gridServicios.getId()).unmask();
                var strEsWifiExistente = "NO";
                var json = Ext.JSON.decode(response.responseText);
                var datos = json.encontrados;
                //-------------------------------------------------------------------------------------------
                Ext.define('tipoCaracteristica', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'tipo', type: 'string'}
                    ]
                });

                var comboModoOperacionCpe = new Ext.data.Store({ 
                    model: 'tipoCaracteristica',
                    data : [
                        {tipo:'ROUTER' },
                        {tipo:'NAT' },
                        {tipo:'BRIDGE' }
                    ]
                });

                var storeModelosCpe = new Ext.data.Store({  
                    pageSize: 1000,
                    proxy: {
                        type: 'ajax',
                        url : getModelosElemento,
                        extraParams: {
                            tipo:   'CPE',
                            forma:  'Empieza con',
                            estado: "Activo"
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields:
                        [
                          {name:'modelo', mapping:'modelo'},
                          {name:'codigo', mapping:'codigo'}
                        ]
                });

                var storeInterfacesSplitter = new Ext.data.Store({  
                    pageSize: 100,
                    proxy: {
                        type: 'ajax',
                        timeout: 400000,
                        url : getInterfacesPorElemento,
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
                    fields:
                        [
                          {name:'idInterface', mapping:'idInterface'},
                          {name:'nombreInterface', mapping:'nombreInterface'}
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
                        columns: 2
                    },
                    defaults: {
                        bodyStyle: 'padding:20px'
                    },
                    items: [
                        //informacion del servicio/producto
                        {
                            xtype: 'fieldset',
                            title: 'Información del Servicio',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 540,
                                height: 130
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
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.login,
                                            value: data.login,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidadUno',
                                            fieldLabel: 'Capacidad',
                                            displayField: data.capacidadUno,
                                            value: data.capacidadUno,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'ultimaMilla',
                                            name: 'ultimaMilla',
                                            fieldLabel: 'Última Milla',
                                            displayField: data.ultimaMilla,
                                            value: data.ultimaMilla,
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '15%', border: false}
                                    ]
                                }

                            ]
                        },//cierre de la informacion servicio/producto

                        //informacion de backbone
                        {
                            xtype: 'fieldset',
                            title: 'Información de backbone',
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
                                        { width: '10%', border: false},
                                       {
                                            xtype: 'textfield',
                                            name: 'Elemento',
                                            fieldLabel: 'Elemento',
                                            displayField: data.elementoNombre,
                                           value: data.elementoNombre,

                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'ipElemento',
                                            fieldLabel: 'Ip Elemento',
                                            displayField: data.ipElemento,
                                            value: data.ipElemento,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'interfaceElemento',
                                            fieldLabel: 'Puerto Elemento',
                                            displayField: data.interfaceElementoNombre,
                                            value: data.interfaceElementoNombre,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'modeloELemento',
                                            fieldLabel: 'Modelo Elemento',
                                            displayField: data.modeloElemento,
                                            value: data.modeloElemento,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'splitterElemento',
                                            fieldLabel: 'Elemento Conector',
                                            displayField: datos[0].nombreSplitter,
                                            value: datos[0].nombreSplitter,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'splitterInterfaceElemento',
                                            name: 'splitterInterfaceElemento',
                                            fieldLabel: 'Interface Elemento',
                                            displayField: 'nombreInterface',
                                            valueField:'idInterface',
                                            value: datos[0].nombrePuertoSplitter,
                                            loadingText: 'Buscando...',
                                            store: storeInterfacesSplitter,
                                            width: '25%',

                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'cajaElemento',
                                            fieldLabel: 'Elemento Contenedor',
                                            displayField: datos[0].nombreCaja,
                                            value: datos[0].nombreCaja,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        { width: '10%', border: false},
                                        { width: '10%', border: false}

                                    ]
                                }

                            ]
                        },//cierre de info de backbone

                        //informacion de los elementos del cliente
                        {
                        xtype: 'fieldset',
                        title: 'Información de los Elementos del Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 540
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
                                        id: 'serieCpe',
                                        name: 'serieCpe',
                                        fieldLabel: '* Serie',
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    {width: '20%', border: false},
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modeloCpe',
                                        name: 'modeloCpe',
                                        fieldLabel: '* Modelo',
                                        displayField: 'modelo',
                                        valueField: 'modelo',
                                        loadingText: 'Buscando...',
                                        store: storeModelosCpe,
                                        width: '25%',
                                        listeners: {
                                            blur: function(combo) {
                                                Ext.Ajax.request({
                                                    url: buscarCpeHuaweiNaf,
                                                    method: 'post',
                                                    params: {
                                                        serieCpe: Ext.getCmp('serieCpe').getValue(),
                                                        modeloElemento: combo.getValue(),
                                                        estado: 'PI',
                                                        bandera: 'ActivarServicio',
                                                        idServicio: data.idServicio
                                                    },
                                                    success: function(response) {
                                                        var respuesta = response.responseText.split("|");
                                                        var status         = respuesta[0];
                                                        var mensaje        = respuesta[1].split(",");
                                                        strEsWifiExistente = respuesta[2];
                                                        var descripcion    = mensaje[0];
                                                        var macOntNaf      = mensaje[1];
                                                        console.log(status);
                                                        if (status == "OK")
                                                        {
                                                            Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                            Ext.getCmp('descripcionCpe').setRawValue(descripcion);

                                                            Ext.getCmp('macCpe').setValue = macOntNaf;
                                                            Ext.getCmp('macCpe').setRawValue(macOntNaf);
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Mensaje ', mensaje);
                                                            Ext.getCmp('descripcionCpe').setValue = status;
                                                            Ext.getCmp('descripcionCpe').setRawValue(status);

                                                            Ext.getCmp('macCpe').setValue = status;
                                                            Ext.getCmp('macCpe').setRawValue(status);
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
                                    {width: '10%', border: false},
                                    //---------------------------------------

                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'macCpe',
                                        name: 'macCpe',
                                        fieldLabel: '* Mac',
                                        displayField: "",
                                        value: "",                                       
                                        width: '25%'
                                    },
                                    {
                                        xtype: 'hidden',
                                        id: 'validacionMacOnt',
                                        name: 'validacionMacOnt',
                                        value: "",
                                        width: '20%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'descripcionCpe',
                                        name: 'descripcionCpe',
                                        fieldLabel: '* Descripción',
                                        displayField: "",
                                        value: "",
                                        readOnly: true,
                                        width: '25%'
                                    },
                                    {width: '10%', border: false},
                                    //---------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        id:'observacionCliente',
                                        name: 'observacionCliente',
                                        fieldLabel: '* Observación',
                                        displayField: "",
                                        labelPad: -45,
                                        colspan: 4,
                                        value: "",
                                        width: '87%'
                                    }

                                ]//cierre del container table
                            }
                        ]

                    },//cierre informacion de los elementos del cliente

                        //informacion del Cliente
                        {
                            xtype: 'fieldset',
                            title: 'Información del Cliente',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 540,
                                height: 133
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
                                            id:'ssid',
                                            name: 'ssid',
                                            fieldLabel: 'SSID Cliente',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'password',
                                            name: 'password',
                                            fieldLabel: 'Password',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'numeroPc',
                                            name: 'numeroPc',
                                            fieldLabel: 'Número PCs',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'modoOperacion',
                                            name: 'modoOperacion',
                                            fieldLabel: 'Modo Operación',
                                            displayField:'tipo',
                                            valueField: 'tipo',
                                            loadingText: 'Buscando...',
                                            store: comboModoOperacionCpe,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        //---------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'vlan',
                                            name: 'vlan',
                                            fieldLabel: 'Vlan',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'ipElementoCliente',
                                            name: 'ipElementoCliente',
                                            fieldLabel: '* Ip',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},                                        

                                    ]//cierre del container table
                                }                


                            ]//cierre del fieldset
                        }//cierre informacion ont
                        ,CamposRequeridos
                    ],
                    buttons: [{
                        text: 'Activar',
                        formBind: true,
                        handler: function(){
                            
                            var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                            var serieCpe = Ext.getCmp('serieCpe').getValue();
                            var descripcionCpe = Ext.getCmp('descripcionCpe').getValue();
                            var mac = Ext.getCmp('macCpe').getValue();
                            var modoOperacion = Ext.getCmp('modoOperacion').getValue();
                            var numPc = Ext.getCmp('numeroPc').getValue();
                            var ssid = Ext.getCmp('ssid').getValue();
                            var password = Ext.getCmp('password').getValue();
                            var observacion = Ext.getCmp('observacionCliente').getValue();
                            var interfaceSplitter =Ext.getCmp('splitterInterfaceElemento').getRawValue();
                            var ipElementoCliente = Ext.getCmp('ipElementoCliente').getValue();
                            var vlan = Ext.getCmp('vlan').getValue();
                            
                            var validacion=true;
                            flag = 0;                            
                            //valido la ip
                            var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
                            if (!ipElementoCliente.match(ipformat))
                            {
                                validacion = false;
                                flag=4;
                            }
                            
                            if(vlan > 2000)
                            {
                                validacion=false;
                                flag=3;
                            }
                            
                            if(serieCpe=="" || mac==""){
                                validacion=false;
                            }
                            if(mac)
                            {
                                var bandera = 1;
                                if (mac.length != 14) 
                                {
                                    bandera = 0;
                                }
                                if (mac.charAt(4) != ".")
                                {
                                    bandera = 0;
                                }
                                if (mac.charAt(9) != ".")
                                {
                                    bandera = 0;
                                }
                                if (bandera == 0)
                                {
                                    alert("Favor ingrese la mac en formato correcto (aaaa.bbbb.cccc) * ");
                                    return false;
                                }
                            }
                            

                            if(descripcionCpe=="ELEMENTO ESTADO INCORRECTO" || 
                               descripcionCpe=="ELMENTO CON SALDO CERO" || 
                               descripcionCpe=="NO EXISTE ELEMENTO")
                            {
                                validacion=false;
                                flag=2;
                            }

                            if(validacion){
                                Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');


                                Ext.Ajax.request({
                                    url: activarWifi,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio:                     data.idServicio,
                                        idProducto:                     data.productoId,
                                        perfil:                         data.perfilDslamacOntm,
                                        login:                          data.login,
                                        capacidad1:                     data.capacidadUno,
                                        capacidad2:                     data.capacidadDos,
                                        interfaceElementoId:            data.interfaceElementoId,
                                        interfaceElementoSplitterId:    interfaceSplitter,
                                        ultimaMilla:                    data.ultimaMilla,
                                        plan:                           data.planId,
                                        serieWifi:                      serieCpe,
                                        modeloWifi:                     modeloCpe,
                                        macWifi:                        mac,
                                        numPc:                          numPc,
                                        ssid:                           ssid,
                                        password:                       password,
                                        modoOperacion:                  modoOperacion,
                                        observacionCliente:             observacion,
                                        ipElementoCliente:              ipElementoCliente,
                                        vlan:                           vlan,
                                        strEsWifiExistente:             strEsWifiExistente
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Activo el Cliente', function(btn){
                                                if(btn=='ok'){
                                                    win.destroy();
                                                    store.load();
                                                }
                                            });
                                        }
                                        else if(response.responseText == "SERIAL YA EXISTE"){
                                            Ext.Msg.alert('Mensaje ','Serial de ONT, ya existe en el OLT!' );
                                        }
                                        else if(response.responseText == "NO ID CLIENTE"){
                                            Ext.Msg.alert('Mensaje ','Serial de Ont erroneo!' );
                                        }
                                        else if(response.responseText == "CANTIDAD CERO"){
                                            Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar' );
                                        }
                                        else if(response.responseText == "NO EXISTE PRODUCTO"){
                                            Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                        }
                                        else if(response.responseText == "NO EXISTE CPE"){
                                            Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar' );
                                        }
                                        else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                                            Ext.Msg.alert('Mensaje ','Equipo no esta en PENDIENTE INSTALACION/RETIRADO, favor revisar' );
                                        }
                                        else if(response.responseText == "NAF"){
                                            Ext.Msg.alert('Mensaje ',response.responseText);
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
                            else{
                                if(flag==1){
                                    Ext.Msg.alert("Validacion","Alguna Mac esta incorrecta, favor revisar", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==2){
                                    Ext.Msg.alert("Validacion","Datos del Ont incorrectos, favor revisar", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==3){
                                    Ext.Msg.alert("Validacion","La vlan debe estar en un rando de 1 -2000", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==4){
                                    Ext.Msg.alert("Validacion","La ip no tiene el formato correcto", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }                                  
                                else{
                                    Ext.Msg.alert("Validacion","Favor Revise los campos", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }

                            }

                        }
                    },{
                        text: 'Cancelar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Activar Internet Wifi',
                    modal: true,
                    width: 1200,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

                storeInterfacesSplitter.load({
                    callback:function(){        
                        storeModelosCpe.load({

                        });
                    }
                });
            }//cierre response
        });

}


function ingresarElementoCliente(data, gridIndex){
    
        Ext.get(gridServicios.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({ 
            url: getDatosBackbone,
            method: 'post',
            timeout: 400000,
            params: { 
                idServicio: data.idServicio
            },
            success: function(response){
                Ext.get(gridServicios.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                var datos = json.encontrados;
                
                if(datos[0])
                {
                    if(datos[0].idElementoCliente)
                    {
                        Ext.Msg.alert('Error', 'Ya tiene elemento cliente, debe realizar un cambio de elemento');
                        return false;                                
                    }
                   
                }
                
                //-------------------------------------------------------------------------------------------
                Ext.define('tipoCaracteristica', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'tipo', type: 'string'}
                    ]
                });

                var comboModoOperacionCpe = new Ext.data.Store({ 
                    model: 'tipoCaracteristica',
                    data : [
                        {tipo:'ROUTER' },
                        {tipo:'NAT' },
                        {tipo:'BRIDGE' }
                    ]
                });
                               
                var comboUm = Ext.create('Ext.data.Store', {
                    fields: ['value', 'name'],
                    data : [
                        {"value":"Fibra Optica", "name":"Fibra Optica"},
                        {"value":"UTP", "name":"UTP"},
                        {"value":"Radio", "name":"Radio"},
                        {"value":"SATELITAL", "name":"SATELITAL"}
                    ]
                });

                var storeModelosCpe = new Ext.data.Store({  
                    pageSize: 1000,
                    proxy: {
                        type: 'ajax',
                        url : getModelosElemento,
                        extraParams: {
                            tipo:   'CPE',
                            forma:  'Empieza con',
                            estado: "Activo"
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                    },
                    fields:
                        [
                          {name:'modelo', mapping:'modelo'},
                          {name:'codigo', mapping:'codigo'}
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
                        columns: 1
                    },
                    defaults: {
                        bodyStyle: 'padding:20px'
                    },
                    items: [
                        //informacion del servicio/producto
                        {
                            xtype: 'fieldset',
                            title: 'Información del Servicio',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 540,
                                height: 50
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
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.login,
                                            value: data.login,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidadUno',
                                            fieldLabel: 'Capacidad',
                                            displayField: data.capacidadUno,
                                            value: data.capacidadUno,
                                            readOnly: false,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'combobox',
                                            id:'ultimaMilla',
                                            name: 'ultimaMilla',
                                            fieldLabel: 'Última Milla',
                                            displayField:'name',
                                            valueField: 'value',
                                            store: comboUm,
                                            width: '35%'
                                        },
                                        {
                                            xtype: 'textfield',
                                            id:'ultimaMillaGrid',
                                            name: 'ultimaMillaGrid',
                                            fieldLabel: 'Última Milla',
                                            displayField: data.ultimaMilla,
                                            value: data.ultimaMilla,
                                            
                                            readOnly: true,
                                            width: '35%'
                                        },                                        
                                        { width: '15%', border: false}
                                    ]
                                }

                            ]
                        },//cierre de la informacion servicio/producto
                        //informacion de los elementos del cliente
                        //informacion del Cliente
                        {
                            xtype: 'fieldset',
                            title: 'Información del Cliente',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 540
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
                                            id:'ssid',
                                            name: 'ssid',
                                            fieldLabel: 'SSID Cliente',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'password',
                                            name: 'password',
                                            fieldLabel: 'Password',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'numeroPc',
                                            name: 'numeroPc',
                                            fieldLabel: 'Numero PCs',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'modoOperacion',
                                            name: 'modoOperacion',
                                            fieldLabel: 'Modo Operacion',
                                            displayField:'tipo',
                                            valueField: 'tipo',
                                            loadingText: 'Buscando...',
                                            store: comboModoOperacionCpe,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        //---------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'vlan',
                                            name: 'vlan',
                                            fieldLabel: 'Vlan',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'ipElementoCliente',
                                            name: 'ipElementoCliente',
                                            fieldLabel: 'Ip',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},                                        

                                    ]//cierre del container table
                                }
                            ]//cierre del fieldset
                        },//cierre informacion ont
                        {
                        xtype: 'fieldset',
                        title: 'Información de los Elementos del Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 540
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
                                        id: 'serieCpe',
                                        name: 'serieCpe',
                                        fieldLabel: 'Serie',
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    {width: '20%', border: false},
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modeloCpe',
                                        name: 'modeloCpe',
                                        fieldLabel: 'Modelo',
                                        displayField: 'modelo',
                                        valueField: 'modelo',
                                        loadingText: 'Buscando...',
                                        store: storeModelosCpe,
                                        width: '25%'
                                    },
                                    {width: '10%', border: false},
                                    //---------------------------------------

                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'macCpe',
                                        name: 'macCpe',
                                        fieldLabel: 'Mac',
                                        displayField: "",
                                        value: "",                                       
                                        width: '25%'
                                    },
                                    {
                                        xtype: 'hidden',
                                        id: 'validacionMacOnt',
                                        name: 'validacionMacOnt',
                                        value: "",
                                        width: '20%'
                                    },
                                    {width: '10%', border: false},
                                    //---------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        id:'observacionCliente',
                                        name: 'observacionCliente',
                                        fieldLabel: 'Observacion',
                                        displayField: "",
                                        labelPad: -45,
                                        colspan: 4,
                                        value: "",
                                        width: '87%'
                                    }

                                ]//cierre del container table
                            }
                        ]
                    }//cierre informacion de los elementos del cliente

                    ],
                    buttons: [{
                        text: 'Ingresar',
                        formBind: true,
                        handler: function(){
                            
                            var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                            var serieCpe = Ext.getCmp('serieCpe').getValue();
                            var mac = Ext.getCmp('macCpe').getValue();
                            var modoOperacion = Ext.getCmp('modoOperacion').getValue();
                            var numPc = Ext.getCmp('numeroPc').getValue();
                            var ssid = Ext.getCmp('ssid').getValue();
                            var password = Ext.getCmp('password').getValue();
                            var observacion = Ext.getCmp('observacionCliente').getValue();
                            var ipElementoCliente = Ext.getCmp('ipElementoCliente').getValue();
                            var vlan = Ext.getCmp('vlan').getValue();
                            var ultimaMilla = Ext.getCmp('ultimaMilla').getValue();
                            
                            var validacion=true;
                            flag = 0;                            
                            //valido la ip
                            var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
                            if (!ipElementoCliente.match(ipformat))
                            {
                                validacion = false;
                                flag=4;
                            }
                            
                            if(vlan > 2000)
                            {
                                validacion=false;
                                flag=3;
                            }
                            
                            if(serieCpe=="")
                            {
                                validacion=false;
                                flag=1;
                            }
                            
                            if(mac)
                            {
                                var bandera = 1;
                                if (mac.length != 14) 
                                {
                                    bandera = 0;
                                }
                                if (mac.charAt(4) != ".")
                                {
                                    bandera = 0;
                                }
                                if (mac.charAt(9) != ".")
                                {
                                    bandera = 0;
                                }
                                if (bandera == 0)
                                {
                                    alert("Favor ingrese la mac en formato correcto (aaaa.bbbb.cccc) * ");
                                    return false;
                                }
                            }
                            else
                            {
                                alert("Favor ingrese la mac");
                                return false;
                            }

                            if(validacion){
                                Ext.get(formPanel.getId()).mask('Por Favor Espere...');


                                Ext.Ajax.request({
                                    url: url_ingresarElemento,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio:                     data.idServicio,
                                        idProducto:                     data.productoId,
                                        perfil:                         data.perfilDslamacOntm,
                                        login:                          data.login,
                                        capacidad1:                     data.capacidadUno,
                                        capacidad2:                     data.capacidadDos,
                                        interfaceElementoId:            data.interfaceElementoId,
                                        ultimaMilla:                    ultimaMilla,
                                        plan:                           data.planId,
                                        serieWifi:                      serieCpe,
                                        modeloWifi:                     modeloCpe,
                                        macWifi:                        mac,
                                        numPc:                          numPc,
                                        ssid:                           ssid,
                                        password:                       password,
                                        modoOperacion:                  modoOperacion,
                                        observacionCliente:             observacion,
                                        ipElementoCliente:              ipElementoCliente,
                                        vlan:                           vlan
                                        
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Transacción exitosa.', function(btn){
                                                if(btn=='ok'){
                                                    win.destroy();
                                                    store.load();
                                                }
                                            });
                                        }
                                        else
                                        {
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
                            else{
                                if(flag==1){
                                    Ext.Msg.alert("Validación","Debe ingresar la serie.", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==2){
                                    Ext.Msg.alert("Validación","Datos del Ont incorrectos, favor revisar", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==3){
                                    Ext.Msg.alert("Validación","La vlan debe estar en un rando de 1 -2000", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==4){
                                    Ext.Msg.alert("Validación","La ip no tiene el formato correcto", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }                                  
                                else{
                                    Ext.Msg.alert("Validación","Favor Revise los campos", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }

                            }

                        }
                    },{
                        text: 'Cancelar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Ingresar Elemento',
                    width: 600,
                    items: [formPanel]
                }).show();

                storeModelosCpe.load({ });
                                
                if(data.ultimaMilla != '' || data.ultimaMilla != 'null')
                {
                    Ext.getCmp('ultimaMillaGrid').setVisible(false);
                }
                else
                {
                    Ext.getCmp('ultimaMilla').setVisible(false);
                }

            }//cierre response
        });
}


function ingresarElementoClienteAlquiler(data, gridIndex){

    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;

            if(datos[0])
            {
                if(datos[0].idElementoCliente)
                {
                    Ext.Msg.alert('Error', 'Ya tiene elemento cliente, debe realizar un cambio de elemento');
                    return false;
                }

            }

            //-------------------------------------------------------------------------------------------
            Ext.define('tipoCaracteristica', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'tipo', type: 'string'}
                ]
            });

            var comboModoOperacionCpe = new Ext.data.Store({
                model: 'tipoCaracteristica',
                data : [
                    {tipo:'ROUTER' },
                    {tipo:'NAT' },
                    {tipo:'BRIDGE' }
                ]
            });

            var comboUm = Ext.create('Ext.data.Store', {
                fields: ['value', 'name'],
                data : [
                    {"value":"Fibra Optica", "name":"Fibra Optica"},
                    {"value":"UTP", "name":"UTP"},
                    {"value":"Radio", "name":"Radio"},
                    {"value":"SATELITAL", "name":"SATELITAL"}
                ]
            });

            var storeModelosCpe = new Ext.data.Store({
                pageSize: 1000,
                proxy: {
                    type: 'ajax',
                    url : getModelosElemento,
                    extraParams: {
                        tipo:   'CPE',
                        forma:  'Empieza con',
                        estado: "Activo"
                    },
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                    [
                        {name:'modelo', mapping:'modelo'},
                        {name:'codigo', mapping:'codigo'}
                    ]
            });

            //-------------------------------------------------------------------------------------------

            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 95,
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    columns: 1
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Información del Servicio',
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [
                            {
                                xtype: 'container',
                                autoWidth: true,
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '50%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'estado',
                                        fieldLabel: 'Estado',
                                        value: data.estado,
                                        readOnly: true,
                                        width: '50%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'descripcionFactura',
                                        fieldLabel: 'Descripción Factura',
                                        value: data.descripcionPresentaFactura,
                                        readOnly: true,
                                        width: '50%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        value: data.nombreProducto,
                                        readOnly: true,
                                        width: '50%'
                                    },
                                ]//cierre del container table
                            }
                        ]//cierre del fieldset
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Información del Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                    align: 'stretch'
                                },
                                items: [
                                    {
                                        xtype: 'numberfield',
                                        id:'vlan',
                                        name: 'vlan',
                                        fieldLabel: '* Vlan',
                                        displayField: "",
                                        value: "",
                                        width: '50%'
                                    },
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modoOperacion',
                                        name: 'modoOperacion',
                                        fieldLabel: '* Modo Operación',
                                        displayField:'tipo',
                                        valueField: 'tipo',
                                        loadingText: 'Buscando...',
                                        store: comboModoOperacionCpe,
                                        width: '50%'
                                    },
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'ultimaMilla',
                                        name: 'ultimaMilla',
                                        fieldLabel: '* Última Milla',
                                        displayField: 'name',
                                        valueFueld: 'value',
                                        loadingText: 'Buscando...',
                                        store: comboUm,
                                        width: '50%'
                                    }
                                ]//cierre del container table
                            }
                        ]//cierre del fieldset
                    },//cierre informacion ont
                    {
                        xtype: 'fieldset',
                        title: 'Información de los Elementos del Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
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
                                        id: 'serieCpe',
                                        name: 'serieCpe',
                                        fieldLabel: '* Serie',
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    {width: '20%', border: false},
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modeloCpe',
                                        name: 'modeloCpe',
                                        fieldLabel: '* Modelo',
                                        displayField: 'modelo',
                                        valueField: 'modelo',
                                        loadingText: 'Buscando...',
                                        store: storeModelosCpe,
                                        width: '25%',
                                        listeners: {
                                            blur: function(combo) {
                                                Ext.Ajax.request({
                                                    url: buscarCpeHuaweiNaf,
                                                    method: 'post',
                                                    params: {
                                                        serieCpe: Ext.getCmp('serieCpe').getValue(),
                                                        modeloElemento: combo.getValue(),
                                                        estado: 'PI',
                                                        bandera: 'ActivarServicio'
                                                    },
                                                    success: function(response) {
                                                        var respuesta = response.responseText.split("|");
                                                        var status = respuesta[0];
                                                        var mensaje = respuesta[1].split(",");
                                                        var descripcion = mensaje[0];
                                                        var macOntNaf = mensaje[1];

                                                        if (status == "OK")
                                                        {
                                                            Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                            Ext.getCmp('descripcionCpe').setRawValue(descripcion);

                                                            Ext.getCmp('macCpe').setValue = macOntNaf;
                                                            Ext.getCmp('macCpe').setRawValue(macOntNaf);
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Mensaje ', mensaje);
                                                            Ext.getCmp('descripcionCpe').setValue = status;
                                                            Ext.getCmp('descripcionCpe').setRawValue(status);

                                                            Ext.getCmp('macCpe').setValue = status;
                                                            Ext.getCmp('macCpe').setRawValue(status);
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
                                    {width: '10%', border: false},
                                    //---------------------------------------

                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'macCpe',
                                        name: 'macCpe',
                                        fieldLabel: '* Mac',
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    {
                                        xtype: 'hidden',
                                        id: 'validacionMacOnt',
                                        name: 'validacionMacOnt',
                                        value: "",
                                        width: '20%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'descripcionCpe',
                                        name: 'descripcionCpe',
                                        fieldLabel: '* Descripción',
                                        displayField: "",
                                        value: "",
                                        readOnly: true,
                                        width: '25%'
                                    },
                                    {width: '10%', border: false},
                                    //---------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        id:'observacionCliente',
                                        name: 'observacionCliente',
                                        fieldLabel: '* Observación',
                                        displayField: "",
                                        labelPad: -56.9,
                                        colspan: 5,
                                        value: "",
                                        width: '85%'
                                    }

                                ]//cierre del container table
                            }
                        ]
                    }//cierre informacion de los elementos del cliente

                ],
                buttons: [{
                    text: 'Activar',
                    formBind: true,
                    handler: function(){
                        Ext.Msg.confirm('Confirmación', '¿Esta segur@ que desea <b class="blue-text">registrar</b> y <b class="blue-text">activar</b> este servicio?', function(id) {
                            if (id === 'yes')
                            {
                                if (data.estado === 'Asignada' || data.estado === 'Pendiente')
                                {
                                    let modeloCpe       = Ext.getCmp('modeloCpe').getValue();
                                    let serieCpe        = Ext.getCmp('serieCpe').getValue();
                                    let mac             = Ext.getCmp('macCpe').getValue();
                                    let modoOperacion   = Ext.getCmp('modoOperacion').getValue();
                                    let observacion     = Ext.getCmp('observacionCliente').getValue();
                                    let vlan            = Ext.getCmp('vlan').getValue();
                                    let ultimaMilla     = Ext.getCmp('ultimaMilla').getValue();
        
                                    let validacion      = true;
        
                                    if(vlan > 2000)
                                    {
                                        Ext.Msg.alert('Mensaje ', 'VLAN incumple con los parámetros.');
                                        validacion=false;
                                    }
        
                                    if(serieCpe=="")
                                    {
                                        Ext.Msg.alert("Validación", "Debe ingresar la serie.");
                                        validacion=false;
                                    }
        
                                    if(modoOperacion == "" || modoOperacion == null)
                                    {
                                        Ext.Msg.alert('Mensaje ', 'Ingrese el modo de operación.');
                                        validacion=false;
                                    }
        
                                    if(ultimaMilla == "" || ultimaMilla == null)
                                    {
                                        Ext.Msg.alert('Mensaje ', 'Ingrese tipo de Última Milla.');
                                        validacion=false;
                                    }
        
                                    if(mac)
                                    {
                                        var bandera = 1;
                                        if (mac.length != 14)
                                        {
                                            bandera = 0;
                                        }
                                        if (mac.charAt(4) != ".")
                                        {
                                            bandera = 0;
                                        }
                                        if (mac.charAt(9) != ".")
                                        {
                                            bandera = 0;
                                        }
                                        if (bandera == 0)
                                        {
                                            Ext.Msg.alert('Mensaje ', 'Favor ingrese la mac en formato correcto (aaaa.bbbb.cccc) * ');
                                            validacion = false;
                                        }
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Mensaje ', 'Favor ingrese la MAC.');
                                        validacion = false;
                                    }
        
                                    if (observacion == "")
                                    {
                                        Ext.Msg.alert('Mensaje ', 'Favor ingrese una observación.');
                                        validacion = false;
                                    }
        
                                    if (validacion)
                                    {
                                        Ext.get(formPanel.getId()).mask('Activando servicio...');
                                        Ext.Ajax.request({
                                            url: confirmarActivacionBoton,
                                            method: 'post',
                                            timeout: 400000,
                                            params: {
                                                idServicio: data.idServicio,
                                                idProducto: data.productoId,
                                                observacionActivarServicio: observacion,
                                                idAccion: 847,
                                                strNombreTecnico: data.descripcionProducto
                                            },
                                            success: function (response) {
                                                if (response.responseText == "OK")
                                                {
        
                                                    Ext.Msg.alert('Mensaje ', 'Se confirmo el Servicio: ' + data.login);
        
                                                    Ext.get(formPanel.getId()).mask('Registrando equipos...');
        
                                                    Ext.Ajax.request({
                                                        url: url_ingresarElemento,
                                                        method: 'post',
                                                        timeout: 400000,
                                                        params: {
                                                            idServicio: data.idServicio,
                                                            idProducto: data.productoId,
                                                            perfil: data.perfilDslamacOntm,
                                                            login: data.login,
                                                            capacidad1: data.capacidadUno,
                                                            capacidad2: data.capacidadDos,
                                                            interfaceElementoId: data.interfaceElementoId,
                                                            ultimaMilla: ultimaMilla,
                                                            plan: data.planId,
                                                            serieWifi: serieCpe,
                                                            modeloWifi: modeloCpe,
                                                            macWifi: mac,
                                                            modoOperacion: modoOperacion,
                                                            observacionCliente: observacion,
                                                            vlan: vlan
        
                                                        },
                                                        success: function (response) {
                                                            Ext.get(formPanel.getId()).unmask();
                                                            if (response.responseText == "OK")
                                                            {
                                                                Ext.Msg.alert('Mensaje', 'Transacción exitosa.', function (btn) {
                                                                    if (btn == 'ok')
                                                                    {
                                                                        win.destroy();
                                                                        store.load();
                                                                    }
                                                                });
                                                            } else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', response.responseText);
                                                            }
                                                        },
                                                        failure: function (result) {
                                                            Ext.get(formPanel.getId()).unmask();
                                                            store.load();
                                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                                else {
                                                    Ext.Msg.alert('Mensaje ', 'Error:' + response.responseText);
                                                }
                                            },
                                            failure: function (result) {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                    else {
                                        Ext.Msg.alert("Error", "Favor revise los campos obligatorios");
                                    }
                                }
                            }
                        });
                    }
                },{
                    text: 'Cancelar',
                    handler: function(){
                        win.destroy();
                    }
                }]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Activación y Registro Elemento Alquiler',
                width: 585,
                modal: true,
                items: [formPanel]
            }).show();

            storeModelosCpe.load({ });

        }//cierre response
    });
}

/**
 * Funcion que muestra la ventana con la informacion tecnica del servicio
 * "Wifi Alquiler de Equipos."
 * 
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 28-08-2019
 * 
 */
function verInformacionTecnicaWifiAlquiler(data) {

    objFieldStyle = {
        'backgroundColor': '#F0F2F2',
        'backgrodunImage': 'none'
    };

    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosWifiAlquiler,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            let objData = JSON.parse(response.responseText);
            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 95,
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    columns: 1
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Información del Servicio',
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [
                            {
                                xtype: 'container',
                                autoWidth: true,
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '50%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'estado',
                                        fieldLabel: 'Estado',
                                        value: data.estado,
                                        readOnly: true,
                                        width: '50%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'descripcionFactura',
                                        fieldLabel: 'Descripción Factura',
                                        value: data.descripcionPresentaFactura,
                                        readOnly: true,
                                        width: '50%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        value: data.nombreProducto,
                                        readOnly: true,
                                        width: '50%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                ]//cierre del container table
                            }
                        ]//cierre del fieldset
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Información del Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                    align: 'stretch'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        id:'vlan',
                                        name: 'vlan',
                                        fieldLabel: 'Vlan',
                                        readOnly: true,
                                        value: objData.vlan,
                                        width: '50%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'modoOperacion',
                                        name: 'modoOperacion',
                                        fieldLabel: 'Modo Operación',
                                        value: objData.modoOperacion,
                                        readOnly: true,
                                        width: '50%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'ultimaMilla',
                                        name: 'ultimaMilla',
                                        fieldLabel: 'Última Milla',
                                        value: objData.ultimaMilla,
                                        readOnly: true,
                                        width: '50%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'mesesContrato',
                                        name: 'mesesContrato',
                                        fieldLabel: 'Meses Contratados',
                                        value: objData.wfMesesContrato,
                                        readOnly: true,
                                        width: '50%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    }
                                ]//cierre del container table
                            }
                        ]//cierre del fieldset
                    },//cierre informacion ont
                    {
                        xtype: 'fieldset',
                        title: 'Información de los Elementos del Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                    align: 'stretch'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        id: 'serieCpe',
                                        name: 'serieCpe',
                                        fieldLabel: 'Serie',
                                        displayField: "",
                                        value: objData.serieElemento,
                                        width: '25%',
                                        readOnly: true,
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'modeloCpe',
                                        name: 'modeloCpe',
                                        fieldLabel: 'Modelo',
                                        displayField: 'modelo',
                                        value: objData.modeloElemento,
                                        readOnly: true,
                                        width: '25%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'wfModelo',
                                        name: 'wfModelo',
                                        fieldLabel: 'WF Modelo',
                                        value: objData.wfModelo,
                                        readOnly: true,
                                        width: '25%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'macCpe',
                                        name: 'macCpe',
                                        fieldLabel: 'Mac',
                                        readOnly: true,
                                        value: objData.macWifi ? objData.macWifi.toUpperCase() : '',
                                        width: '25%',
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                    }
                                ]//cierre del container table
                            }
                        ]
                    }//cierre informacion de los elementos del cliente

                ],
                buttons: [{
                    text: 'OK',
                    formBind: true,
                    handler: function(){
                        win.destroy();
                    }
                }]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Información del Servicio',
                width: 585,
                modal:true,
                items: [formPanel]
            }).show();

        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: Ha ocurrido un error y no se pudo obtener información, notificar a sistemas.');
            Ext.get(gridServicios.getId()).unmask();
        }
    });
}

function reconectarWifi(data,idAccion){
    Ext.MessageBox.wait("Esperando Respuesta del Elemento...");
    Ext.Ajax.request({
        url: url_reconectarWifi,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            idProducto: data.productoId,
            login: data.login,
            idAccion: idAccion
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            if(response.responseText == "OK")
            {
                Ext.Msg.alert('Mensaje','Se Reconecto el Cliente', function(btn)
                {
                    if(btn=='ok')
                    {
                        store.load();
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Mensaje',response.responseText, function(btn)
                {
                    if(btn=='ok')
                    {
                        store.load();
                    }
                });
            }
        },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });    
}


function cancelarWifi(data,idAccion){

    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosCliente,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            
            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;
            
            var storeMotivos = new Ext.data.Store({  
                pageSize: 50,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url : getMotivos,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        accion: "cancelarCliente"
                    }
                },
                fields:
                    [
                      {name:'idMotivo', mapping:'idMotivo'},
                      {name:'nombreMotivo', mapping:'nombreMotivo'}
                    ]
            });

            var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 10,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 85,
                msgTarget: 'side'
            },
            items: [{
                xtype: 'fieldset',
                title: 'Cancelar Servicio',
                defaultType: 'textfield',
                items: [

                    //informacion del cliente
                    {
                        xtype: 'fieldset',
                        title: 'Información Cliente',
                        defaultType: 'textfield',
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        name: 'nombreCompleto',
                                        fieldLabel: 'Cliente',
                                        displayField: datos[0].nombreCompleto,
                                        value: datos[0].nombreCompleto,
                                        readOnly: true,
                                        fieldCls: 'details-disabled',
                                        width: '50%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'tipoNegocio',
                                        fieldLabel: 'Tipo Negocio',
                                        displayField: datos[0].tipoNegocio,
                                        value: datos[0].tipoNegocio,
                                        readOnly: true,
                                        fieldCls: 'details-disabled',
                                        width: '50%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'loginAux',
                                        fieldLabel: 'Login Aux',
                                        value: data.loginAux ? data.loginAux : '-',
                                        readOnly: true,
                                        fieldCls: 'details-disabled',
                                        width: '50%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'nombreProducto',
                                        fieldLabel: 'Producto',
                                        value: data.nombreProducto ? data.nombreProducto : data.descripcionProducto,
                                        readOnly: true,
                                        fieldCls: 'details-disabled',
                                        width: '50%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'direccion',
                                        fieldLabel: 'Direccion',
                                        displayField: datos[0].direccion,
                                        value: datos[0].direccion,
                                        readOnly: true,
                                        fieldCls: 'details-disabled',
                                        colspan: 2,
                                        width: 502
                                    },
                                ]
                            }

                        ]
                    },//cierre de la informacion del cliente
                    //motivo de cancelacion
                    {
                        xtype: 'fieldset',
                        title: 'Motivo Cancelacion',
                        defaultType: 'textfield',
                        bodyPadding: 100,
                        items: [

                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                },
                                items: [
                                    {
                                        xtype: 'combo',
                                        id:'comboMotivos',
                                        name: 'comboMotivos',
                                        store: storeMotivos,
                                        fieldLabel: 'Motivo',
                                        displayField: 'nombreMotivo',
                                        valueField: 'idMotivo',
                                        queryMode: 'local',
                                        width: 502
                                    },
                                ]
                            }

                        ]
                    }//cierre del motivo de cancelacion

                ]
            }],
            buttons: [{
                text: 'Ejecutar',
                formBind: true,
                handler: function(){
                    var motivo = Ext.getCmp('comboMotivos').getValue();
                    var validacion = false;
                    
                    if(motivo!=null){
                        validacion=true;
                    }
                    
                    if(validacion){
                        Ext.Msg.confirm('Mensaje','Esta seguro que desea Cancelar?', function(btn){
                            if(btn=='yes'){
                                Ext.MessageBox.wait("Esperando Respuesta del Elemento...");
                                Ext.Ajax.request({
                                    url: url_cancelarClienteWifi,
                                    method: 'post',
                                    timeout: 300000, 
                                    params: { 
                                        idServicio: data.idServicio,
                                        idProducto: data.productoId,
                                        login: data.login,
                                        motivo: motivo,
                                        idAccion: idAccion
                                    },
                                    success: function(response){
                                        Ext.get(gridServicios.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Cancelo el Cliente', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Mensaje ',response.responseText );
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(gridServicios.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
                            }
                            else{
                                win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert("Advertencia","Favor Escoja un Motivo", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }
      
                }
            },{
                text: 'Cancelar',
                handler: function(){
                    win.destroy();
                }
            }]
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'Cancelar Servicio',
            modal: true,
            width: 600,
            closable: true,
            items: [formPanel]
        }).show();
            
        }//cierre response
    });       
}


function cambioElementoWifi(data){

    var hiddenResponsable    = true;
    var seleccionResponsable = "C";
    var strNombreLider       = "";

    if(data.flujo == "TN")
    {
        hiddenResponsable = false;
    }

    storeCuadrillas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_cuadrillas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Eliminado'
            }
        },
        fields:
            [
                {name: 'id_cuadrilla', mapping: 'id_cuadrilla'},
                {name: 'nombre_cuadrilla', mapping: 'nombre_cuadrilla'}
            ]
    });

    storeEmpleados = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empleadosPorEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'intIdPersonEmpresaRol', mapping: 'intIdPersonEmpresaRol'},
                {name: 'strNombresEmpleado', mapping: 'strNombresEmpleado'}
            ]
    });

    var storeSolicitud = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        timeout: 400000,
        proxy: {
            type: 'ajax',
            url : getElementosPorSolicitud,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio
            }
        },
        fields:
            [
              {name:'idElemento', mapping:'idElemento'},
              {name:'nombre', mapping:'nombre'},
              {name:'nombreModelo', mapping:'nombreModelo'},
              {name:'tipoElemento', mapping:'tipoElemento'},
              {name:'serie', mapping:'serie'},
              {name:'mac', mapping:'mac'},
              {name:'ip', mapping:'ip'},
              {name:'descripcion', mapping:'descripcion'}
            ]
    });
    
    Ext.define('ElementosPorSolicitud', {
        extend: 'Ext.data.Model',
        fields: [
              {name:'idElemento', mapping:'idElemento'},
              {name:'nombre', mapping:'nombre'},
              {name:'nombreModelo', mapping:'nombreModelo'},
              {name:'tipoElemento', mapping:'tipoElemento'},
              {name:'serie', mapping:'serie'},
              {name:'mac', mapping:'mac'},
              {name:'ip', mapping:'ip'},
              {name:'descripcion', mapping:'descripcion'}
        ]
    });
    
    gridElementosPorSolicitud = new Ext.create('Ext.grid.Panel', {
        id:'gridElementosPorSolicitud',
        store: storeSolicitud,
        columnLines: true,
        columns: [
        {
            header: 'Nombre Elemento',
            dataIndex: 'nombre',
            width: 200,
            sortable: true
        },
        {
            header: 'Modelo Elemento',
            dataIndex: 'nombreModelo',
            width: 100,
            sortable: true
        },
        {
            header: 'Tipo Elemento',
            dataIndex: 'tipoElemento',
            width: 100,
            sortable: true
        },
        {
            header: 'Ip',
            dataIndex: 'ip',
            width: 150,
            sortable: true
        },
        {
            header: 'Mac',
            dataIndex: 'mac',
            width: 120,
            sortable: true
        },
        {
            header: 'Serie',
            dataIndex: 'serie',
            width: 120,
            sortable: true
        },
        {
            header: 'idElemento',
            dataIndex: 'idElemento',
            width: 120,
            hidden: true,
            hideable: false
        },
        {
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 85,
            items: [
                //CAMBIAR DE ELEMENTO
                {
                    getClass: function(v, meta, rec) {
                        return 'button-grid-verDslam';
                    },
                    tooltip: 'Cambiar Elemento Cliente',
                    handler: function(grid, rowIndex, colIndex) {
                        var idElementoCliente = grid.getStore().getAt(rowIndex).data.idElemento;
                        var nombreElementoCliente = grid.getStore().getAt(rowIndex).data.nombre;
                        var ipElementoCliente = grid.getStore().getAt(rowIndex).data.ip;
                        
                        var storeModelosCpe = new Ext.data.Store({
                            pageSize: 1000,
                            autoLoad: true,
                            proxy: {
                                type: 'ajax',
                                url : getModelosElemento,
                                extraParams: {
                                    tipo:   'CPE',
                                    forma:  'Empieza con',
                                    estado: "Activo"
                                },
                                reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                }
                            },
                            fields:
                                [
                                  {name:'modelo', mapping:'modelo'},
                                  {name:'codigo', mapping:'codigo'}
                                ]
                        });
                        

                            var elementoRadioGrupo = {
                                xtype: 'radiogroup',
                                hidden: hiddenResponsable,
                                fieldLabel: '<b>Responsable</b>',
                                columns: 1,
                                items: [
                                    {
                                        boxLabel: 'Cuadrilla',
                                        id: 'rbResponsableCuadrilla',
                                        name: 'rbResponsable',
                                        checked: true,
                                        inputValue: "cuadrilla",
                                        listeners:
                                        {
                                            change: function (cb, nv, ov)
                                            {
                                                if (nv)
                                                {
                                                    Ext.getCmp('cmb_cuadrillas').setVisible(true);
                                                    Ext.getCmp('nombreLider').setVisible(true);
                                                    Ext.getCmp('cmb_empleados').setVisible(false);
                                                    Ext.getCmp('cmb_empleados').value = "";
                                                    Ext.getCmp('cmb_empleados').setRawValue("");
                                                    seleccionResponsable = "C";
                                                    Ext.getCmp('cmb_empleados').reset();
                                                }
                                            }
                                        }
                                    },
                                    {
                                        boxLabel: 'Empleado',
                                        id: 'rbResponsableEmpleado',
                                        name: 'rbResponsable',
                                        checked: false,
                                        inputValue: "empleado",
                                        listeners:
                                        {
                                            change: function (cb, nv, ov)
                                            {
                                                if (nv)
                                                {
                                                    Ext.getCmp('cmb_cuadrillas').setVisible(false);
                                                    Ext.getCmp('nombreLider').setVisible(false);
                                                    Ext.getCmp('cmb_empleados').setVisible(true);
                                                    Ext.getCmp('cmb_cuadrillas').value = "";
                                                    Ext.getCmp('cmb_cuadrillas').setRawValue("");
                                                    Ext.getCmp('nombreLider').value = "";
                                                    Ext.getCmp('nombreLider').setRawValue("");
                                                    seleccionResponsable = "E";
                                                    Ext.getCmp('cmb_cuadrillas').reset();
                                                }
                                            }
                                        }
                                    }
                                ]
                            };

                        var elementoResponsable = {
                                    xtype: 'fieldset',
                                    id: 'responsableCambioCpe',
                                    title: 'Seleccionar responsable del retiro de equipo (*)',
                                    defaultType: 'textfield',
                                    visible: true,
                                    hidden: hiddenResponsable,
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 1,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {
                                                    xtype: 'combobox',
                                                    queryMode: 'remote',
                                                    id: 'cmb_cuadrillas',
                                                    name: 'cmb_cuadrillas',
                                                    fieldLabel: 'Cuadrilla',
                                                    displayField: 'nombre_cuadrilla',
                                                    valueField: 'id_cuadrilla',
                                                    width: 350,
                                                    minChars: 3,
                                                    loadingText: 'Buscando...',
                                                    store: storeCuadrillas,
                                                    listeners: {
                                                        select: function(combo) {

                                                            seteaLiderCuadrilla(combo.getValue());
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Líder:',
                                                    id: 'nombreLider',
                                                    name: 'nombreLider',
                                                    value: ""
                                                },
                                                {
                                                    xtype: 'combobox',
                                                    queryMode: 'remote',
                                                    id: 'cmb_empleados',
                                                    name: 'cmb_empleados',
                                                    fieldLabel: 'Empleado',
                                                    hidden: true,
                                                    displayField: 'strNombresEmpleado',
                                                    valueField: 'intIdPersonEmpresaRol',
                                                    width: 400,
                                                    loadingText: 'Buscando...',
                                                    store: storeEmpleados
                                                }
                                        ]
                                    }
                                ]
                            };

                        var elementoClienteNuevo = {
                            xtype: 'fieldset',
                            title: 'Elemento Nuevo',            
                            defaultType: 'textfield',
                            defaults: {
                                width: 500,
                                height: 100
                            },
                            items: [{
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
                                            id: 'nombreCpe',
                                            name: 'nombreCpe',
                                            fieldLabel: 'Elemento',
                                            displayField: nombreElementoCliente,
                                            value: nombreElementoCliente,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'ipCpe',
                                            name: 'ipCpe',
                                            fieldLabel: 'Ip',
                                            displayField: ipElementoCliente,
                                            value: ipElementoCliente,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'serieCpe',
                                            name: 'serieCpe',
                                            fieldLabel: 'Serie Elemento',
                                            displayField: "",
                                            value: "",
                                            width: '35%',
                                            listeners: {
                                                blur: function(serie){
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe: serie.getValue(),
                                                            modeloElemento: '',
                                                            estado: 'PI',
                                                            bandera: 'ActivarServicio'
                                                        },
                                                        success: function(response){
                                                            var respuesta = response.responseText.split("|");
                                                            var status = respuesta[0];                                                            
                                                            var mensaje = respuesta[1].split(","); 	
                                                            var descripcion = mensaje[0]; 	
                                                            var mac = mensaje[1];
                                                            var modelo = mensaje[2];
                                                            
                                                            Ext.getCmp('descripcionCpe').setValue = ''; 
                                                            Ext.getCmp('descripcionCpe').setRawValue(''); 	
                                                            Ext.getCmp('macCpe').setValue = ''; 	
                                                            Ext.getCmp('macCpe').setRawValue('');
                                                            Ext.getCmp('modeloCpe').setValue = ''; 	
                                                            Ext.getCmp('modeloCpe').setRawValue('');

                                                            if(status=="OK")
                                                            {
                                                                if(storeModelosCpe.find('modelo',modelo)==-1)
                                                                {
                                                                    var strMsj = 'El Elemento con: <br>'+
                                                                    'Modelo: <b>'+modelo+' </b><br>'+
                                                                    'Descripcion: <b>'+descripcion+' </b><br>'+
                                                                    'No corresponde a un CPE, <br>'+
                                                                    'No podrá continuar con el proceso, Favor Revisar <br>';
                                                                    Ext.Msg.alert('Advertencia', strMsj);
                                                                }
                                                                else
                                                                {
                                                                    Ext.getCmp('descripcionCpe').setValue = descripcion; 
                                                                    Ext.getCmp('descripcionCpe').setRawValue(descripcion); 	
                                                                    Ext.getCmp('macCpe').setValue = mac; 	
                                                                    Ext.getCmp('macCpe').setRawValue(mac);
                                                                    Ext.getCmp('modeloCpe').setValue = modelo; 	
                                                                    Ext.getCmp('modeloCpe').setRawValue(modelo);
                                                                }
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.get(formPanel.getId()).unmask();
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype:        'textfield',
                                            id:           'modeloCpe',
                                            name:         'modeloCpe',
                                            fieldLabel:   'Modelo',
                                            displayField: '',
                                            valueField:   '',
                                            readOnly:       true,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'macCpe',
                                            name: 'macCpe',
                                            fieldLabel: 'Mac',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'descripcionCpe',
                                            name: 'descripcionCpe',
                                            fieldLabel: 'Descripcion',
                                            displayField: '',
                                            value: '',
                                            width: '35%'
                                        },
                                        {
                                            xtype: 'hidden',
                                            id:'mensaje',
                                            name: 'mensaje',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'hidden',
                                            id: 'idElemento',
                                            name: 'idElemento',
                                            fieldLabel: 'id',
                                            displayField: idElementoCliente,
                                            value: idElementoCliente,
                                            readOnly: true,
                                            width: '30%'
                                        }

                                        //---------------------------------------
                                ]
                            }]
                        };
                        
                        var formPanelElementoNuevo = Ext.create('Ext.form.Panel', {
                            bodyPadding: 2,
                            waitMsgTarget: true,
                            fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 85,
                                    msgTarget: 'side'
                            },
                            items: [
                                elementoClienteNuevo,
                                elementoRadioGrupo,
                                elementoResponsable
                            ],
                            buttons: [{
                                text: 'Cambiar',
                                formBind: true,
                                handler: function(){

                                    var intIdResponsable;

                                    if(Ext.getCmp('rbResponsableCuadrilla').checked)
                                    {
                                        intIdResponsable = Ext.getCmp('cmb_cuadrillas').getValue();
                                        strNombreLider   = Ext.getCmp('nombreLider').getValue();
                                    }
                                    else if(Ext.getCmp('rbResponsableEmpleado').checked)
                                    {
                                        intIdResponsable = Ext.getCmp('cmb_empleados').getValue();
                                    }

                                    var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                                    var ipCpe = Ext.getCmp('ipCpe').getValue();
                                    var nombreCpe = Ext.getCmp('nombreCpe').getValue();
                                    var macCpe = Ext.getCmp('macCpe').getValue();
                                    var serieCpe = Ext.getCmp('serieCpe').getValue();
                                    var descripcionCpe = Ext.getCmp('descripcionCpe').getValue();
                                    var idElemento = Ext.getCmp('idElemento').getValue();
                                    var tipoElemento = grid.getStore().getAt(rowIndex).data.tipoElemento;
                                    
                                    var validacion=false;
                                    flag = 0;
                                    if(serieCpe=="" || macCpe==""){
                                        validacion=false;
                                    }
                                    else{
                                        validacion=true;
                                    }

                                    if(descripcionCpe=="NO HAY STOCK" || descripcionCpe=="NO EXISTE SERIAL" || descripcionCpe=="CPE NO ESTA EN ESTADO"){
                                        validacion=false;
                                        flag=3;
                                    }

                                    if((!hiddenResponsable) && (intIdResponsable == "" || intIdResponsable == null))
                                    {
                                        validacion=false;
                                        Ext.Msg.alert('Validación ','Favor escoger el responsable del retiro de equipo');
                                        return;
                                    }

                                    if(strNombreLider === "N/A")
                                    {
                                        validacion=false;
                                        Ext.Msg.alert('Validación ','Es obligatorio que la cuadrilla tenga un Líder para realizar la asignación');
                                        return;
                                    }

                                    if(validacion){
                                        Ext.get(formPanelElementoNuevo.getId()).mask('Cambiando Elemento del Cliente...');
                                        
                                        Ext.Ajax.request({
                                            url: url_cambiarEquipoWifi,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: { 
                                                idServicio: data.idServicio,
                                                idElemento: idElemento,
                                                modeloCpe: modeloCpe,
                                                ipCpe: ipCpe,
                                                nombreCpe: nombreCpe,
                                                macCpe: macCpe,
                                                serieCpe: serieCpe,
                                                descripcionCpe: descripcionCpe,
                                                tipoElementoCpe: tipoElemento,
                                                strRegistraEquipo:  data.registroEquipo,
                                                idResponsable:      intIdResponsable,
                                                tipoResponsable:    seleccionResponsable
                                            },
                                            success: function(response){
                                                Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                if(response.responseText == "OK"){
                                                    Ext.Msg.alert('Mensaje','Se Cambio el Elemento del Cliente', function(btn){
                                                        if(btn=='ok'){
                                                            store.load();
                                                            storeSolicitud.load();
                                                            win.destroy();
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
                                    else{
                                        if(flag==3){
                                            Ext.Msg.alert("Validación","Datos del Elemento incorrectos, favor revisar", function(btn){
                                                    if(btn=='ok'){
                                                    }
                                            });
                                        }
                                        else{
                                            Ext.Msg.alert("Validación","Favor Revise los campos", function(btn){
                                                    if(btn=='ok'){
                                                    }
                                            });
                                        }
                                    }
                                }
                            },
                            {
                                text: 'Cancelar',
                                handler: function(){
                                    Ext.get(gridServicios.getId()).unmask();
                                    win.destroy();
                                }
                            }]
                        });
                        
                        var win = Ext.create('Ext.window.Window', {
                            title: 'Cambiar Equipo del Cliente',
                            modal: true,
                            width: 600,
                            closable: true,
                            layout: 'fit',
                            items: [formPanelElementoNuevo]
                        }).show();
                    }
                }
            ]
        }
        ],
        viewConfig:{
            stripeRows:true
        },
        frame: false,
        height: 200
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
            defaults: {
                width: 900
            },
            items: [

                gridElementosPorSolicitud

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Elementos Por Solicitud',
        modal: true,
        width: 950,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function cambioElementoWAE(data){

    var hiddenResponsable    = true;
    var seleccionResponsable = "C";
    var strNombreLider       = "";

    const objFieldStyle = {
        'backgroundColor': '#F0F2F2',
        'backgrodunImage': 'none'
    };

    if(data.flujo == "TN")
    {
        hiddenResponsable = false;
    }

    storeCuadrillas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_cuadrillas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Eliminado'
            }
        },
        fields:
            [
                {name: 'id_cuadrilla', mapping: 'id_cuadrilla'},
                {name: 'nombre_cuadrilla', mapping: 'nombre_cuadrilla'}
            ]
    });

    storeEmpleados = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empleadosPorEmpresa,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'intIdPersonEmpresaRol', mapping: 'intIdPersonEmpresaRol'},
                {name: 'strNombresEmpleado', mapping: 'strNombresEmpleado'}
            ]
    });

    var storeSolicitud = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        timeout: 400000,
        proxy: {
            type: 'ajax',
            url : getElementosPorSolicitud,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio
            }
        },
        fields:
            [
                {name:'idElemento', mapping:'idElemento'},
                {name:'nombre', mapping:'nombre'},
                {name:'nombreModelo', mapping:'nombreModelo'},
                {name:'tipoElemento', mapping:'tipoElemento'},
                {name:'serie', mapping:'serie'},
                {name:'mac', mapping:'mac'},
                {name:'ip', mapping:'ip'},
                {name:'descripcion', mapping:'descripcion'}
            ]
    });

    Ext.define('ElementosPorSolicitud', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idElemento', mapping:'idElemento'},
            {name:'nombre', mapping:'nombre'},
            {name:'nombreModelo', mapping:'nombreModelo'},
            {name:'tipoElemento', mapping:'tipoElemento'},
            {name:'serie', mapping:'serie'},
            {name:'mac', mapping:'mac'},
            {name:'ip', mapping:'ip'},
            {name:'descripcion', mapping:'descripcion'}
        ]
    });

    gridElementosPorSolicitud = new Ext.create('Ext.grid.Panel', {
        id:'gridElementosPorSolicitud',
        store: storeSolicitud,
        columnLines: true,
        columns: [
            {
                header: 'Nombre Elemento',
                dataIndex: 'nombre',
                width: 200,
                sortable: true
            },
            {
                header: 'Modelo Elemento',
                dataIndex: 'nombreModelo',
                width: 100,
                sortable: true
            },
            {
                header: 'Tipo Elemento',
                dataIndex: 'tipoElemento',
                width: 100,
                sortable: true
            },
            {
                header: 'Mac',
                dataIndex: 'mac',
                width: 120,
                sortable: true
            },
            {
                header: 'Serie',
                dataIndex: 'serie',
                width: 120,
                sortable: true
            },
            {
                header: 'idElemento',
                dataIndex: 'idElemento',
                width: 120,
                hidden: true,
                hideable: false
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                align:'center',
                style: 'text-align:left',
                width: 85,
                items: [
                    //CAMBIAR DE ELEMENTO
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-verDslam';
                        },
                        tooltip: 'Cambiar Elemento Cliente',
                        handler: function(grid, rowIndex, colIndex) {
                            var idElementoCliente = grid.getStore().getAt(rowIndex).data.idElemento;
                            var nombreElementoCliente = grid.getStore().getAt(rowIndex).data.nombre;

                            var storeModelosCpe = new Ext.data.Store({
                                pageSize: 1000,
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url : getModelosElemento,
                                    extraParams: {
                                        tipo:   'CPE',
                                        forma:  'Empieza con',
                                        estado: "Activo"
                                    },
                                    reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    }
                                },
                                fields:
                                    [
                                        {name:'modelo', mapping:'modelo'},
                                        {name:'codigo', mapping:'codigo'}
                                    ]
                            });


                            var elementoRadioGrupo = {
                                xtype: 'fieldset',
                                title: 'Seleccionar el tipo (*)',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 500,
                                    height: 25
                                },
                                items: [
                                    {
                                        xtype: 'radiogroup',
                                        hidden: hiddenResponsable,
                                        columns: 2,
                                        items: [
                                            {
                                                boxLabel: 'Cuadrilla',
                                                id: 'rbResponsableCuadrilla',
                                                name: 'rbResponsable',
                                                checked: true,
                                                inputValue: "cuadrilla",
                                                listeners:
                                                    {
                                                        change: function (cb, nv, ov)
                                                        {
                                                            if (nv)
                                                            {
                                                                Ext.getCmp('cmb_cuadrillas').setVisible(true);
                                                                Ext.getCmp('nombreLider').setVisible(true);
                                                                Ext.getCmp('cmb_empleados').setVisible(false);
                                                                Ext.getCmp('cmb_empleados').value = "";
                                                                Ext.getCmp('cmb_empleados').setRawValue("");
                                                                seleccionResponsable = "C";
                                                                Ext.getCmp('cmb_empleados').reset();
                                                            }
                                                        }
                                                    }
                                            },
                                            {
                                                boxLabel: 'Empleado',
                                                id: 'rbResponsableEmpleado',
                                                name: 'rbResponsable',
                                                checked: false,
                                                inputValue: "empleado",
                                                listeners:
                                                    {
                                                        change: function (cb, nv, ov)
                                                        {
                                                            if (nv)
                                                            {
                                                                Ext.getCmp('cmb_cuadrillas').setVisible(false);
                                                                Ext.getCmp('nombreLider').setVisible(false);
                                                                Ext.getCmp('cmb_empleados').setVisible(true);
                                                                Ext.getCmp('cmb_cuadrillas').value = "";
                                                                Ext.getCmp('cmb_cuadrillas').setRawValue("");
                                                                Ext.getCmp('nombreLider').value = "";
                                                                Ext.getCmp('nombreLider').setRawValue("");
                                                                seleccionResponsable = "E";
                                                                Ext.getCmp('cmb_cuadrillas').reset();
                                                            }
                                                        }
                                                    }
                                            }
                                        ]}
                                ]

                            };

                            var elementoResponsable = {
                                xtype: 'fieldset',
                                id: 'responsableCambioCpe',
                                title: 'Seleccionar responsable del retiro de equipo (*)',
                                defaultType: 'textfield',
                                visible: true,
                                hidden: hiddenResponsable,
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 1,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                xtype: 'combobox',
                                                queryMode: 'remote',
                                                id: 'cmb_cuadrillas',
                                                name: 'cmb_cuadrillas',
                                                fieldLabel: 'Cuadrilla',
                                                displayField: 'nombre_cuadrilla',
                                                valueField: 'id_cuadrilla',
                                                width: 350,
                                                minChars: 3,
                                                loadingText: 'Buscando...',
                                                store: storeCuadrillas,
                                                listeners: {
                                                    select: function(combo) {

                                                        seteaLiderCuadrilla(combo.getValue());
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Líder:',
                                                id: 'nombreLider',
                                                name: 'nombreLider',
                                                value: ""
                                            },
                                            {
                                                xtype: 'combobox',
                                                queryMode: 'remote',
                                                id: 'cmb_empleados',
                                                name: 'cmb_empleados',
                                                fieldLabel: 'Empleado',
                                                hidden: true,
                                                displayField: 'strNombresEmpleado',
                                                valueField: 'intIdPersonEmpresaRol',
                                                width: 400,
                                                loadingText: 'Buscando...',
                                                store: storeEmpleados
                                            }
                                        ]
                                    }
                                ]
                            };

                            var elementoClienteNuevo = {
                                xtype: 'fieldset',
                                title: 'Elemento Nuevo',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 500,
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
                                            id: 'nombreCpe',
                                            name: 'nombreCpe',
                                            fieldLabel: 'Elemento',
                                            displayField: nombreElementoCliente,
                                            value: nombreElementoCliente,
                                            width: '60%',
                                            fieldCls: 'details-disabled',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: 'ipCpe',
                                            name: 'ipCpe',
                                            fieldLabel: 'Ip',
                                            displayField: '-',
                                            value: '-',
                                            width: '30%',
                                            fieldCls: 'details-disabled',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'serieCpe',
                                            name: 'serieCpe',
                                            fieldLabel: 'Serie Elemento',
                                            displayField: "",
                                            value: "",
                                            width: '35%',
                                            listeners: {
                                                blur: function(serie){
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe: serie.getValue(),
                                                            modeloElemento: '',
                                                            estado: 'PI',
                                                            bandera: 'ActivarServicio'
                                                        },
                                                        success: function(response){
                                                            var respuesta = response.responseText.split("|");
                                                            var status = respuesta[0];
                                                            var mensaje = respuesta[1].split(",");
                                                            var descripcion = mensaje[0];
                                                            var mac = mensaje[1];
                                                            var modelo = mensaje[2];

                                                            Ext.getCmp('descripcionCpe').setValue = '';
                                                            Ext.getCmp('descripcionCpe').setRawValue('');
                                                            Ext.getCmp('macCpe').setValue = '';
                                                            Ext.getCmp('macCpe').setRawValue('');
                                                            Ext.getCmp('modeloCpe').setValue = '';
                                                            Ext.getCmp('modeloCpe').setRawValue('');

                                                            if(status=="OK")
                                                            {
                                                                if(storeModelosCpe.find('modelo',modelo)==-1)
                                                                {
                                                                    var strMsj = 'El Elemento con: <br>'+
                                                                        'Modelo: <b>'+modelo+' </b><br>'+
                                                                        'Descripcion: <b>'+descripcion+' </b><br>'+
                                                                        'No corresponde a un CPE, <br>'+
                                                                        'No podrá continuar con el proceso, Favor Revisar <br>';
                                                                    Ext.Msg.alert('Advertencia', strMsj);
                                                                }
                                                                else
                                                                {
                                                                    Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                                    Ext.getCmp('descripcionCpe').setRawValue(descripcion);
                                                                    Ext.getCmp('macCpe').setValue = mac;
                                                                    Ext.getCmp('macCpe').setRawValue(mac);
                                                                    Ext.getCmp('modeloCpe').setValue = modelo;
                                                                    Ext.getCmp('modeloCpe').setRawValue(modelo);
                                                                }
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.get(formPanel.getId()).unmask();
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype:        'textfield',
                                            id:           'modeloCpe',
                                            name:         'modeloCpe',
                                            fieldLabel:   'Modelo',
                                            displayField: '',
                                            valueField:   '',
                                            readOnly:       true,
                                            width: '35%',
                                            fieldCls: 'details-disabled',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'macCpe',
                                            name: 'macCpe',
                                            fieldLabel: 'Mac',
                                            displayField: "",
                                            value: "",
                                            width: '35%',
                                            fieldCls: 'details-disabled',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'descripcionCpe',
                                            name: 'descripcionCpe',
                                            fieldLabel: 'Descripcion',
                                            displayField: '',
                                            value: '',
                                            width: '35%',
                                            fieldCls: 'details-disabled',
                                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                        },
                                        {
                                            xtype: 'hidden',
                                            id:'mensaje',
                                            name: 'mensaje',
                                            displayField: "",
                                            value: "",
                                            width: '35%'
                                        },
                                        { width: '10%', border: false},
                                        {
                                            xtype: 'hidden',
                                            id: 'idElemento',
                                            name: 'idElemento',
                                            fieldLabel: 'id',
                                            displayField: idElementoCliente,
                                            value: idElementoCliente,
                                            readOnly: true,
                                            width: '30%'
                                        }

                                        //---------------------------------------
                                    ]
                                }]
                            };

                            var formPanelElementoNuevo = Ext.create('Ext.form.Panel', {
                                bodyPadding: 2,
                                waitMsgTarget: true,
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 85,
                                    msgTarget: 'side'
                                },
                                items: [
                                    elementoClienteNuevo,
                                    elementoRadioGrupo,
                                    elementoResponsable
                                ],
                                buttons: [{
                                    text: 'Cambiar',
                                    formBind: true,
                                    handler: function(){

                                        var intIdResponsable;

                                        if(Ext.getCmp('rbResponsableCuadrilla').checked)
                                        {
                                            intIdResponsable = Ext.getCmp('cmb_cuadrillas').getValue();
                                            strNombreLider   = Ext.getCmp('nombreLider').getValue();
                                        }
                                        else if(Ext.getCmp('rbResponsableEmpleado').checked)
                                        {
                                            intIdResponsable = Ext.getCmp('cmb_empleados').getValue();
                                        }

                                        var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                                        var nombreCpe = Ext.getCmp('nombreCpe').getValue();
                                        var macCpe = Ext.getCmp('macCpe').getValue();
                                        var serieCpe = Ext.getCmp('serieCpe').getValue();
                                        var descripcionCpe = Ext.getCmp('descripcionCpe').getValue();
                                        var idElemento = Ext.getCmp('idElemento').getValue();
                                        var tipoElemento = grid.getStore().getAt(rowIndex).data.tipoElemento;

                                        var validacion=false;

                                        flag = 0;

                                        if (serieCpe !== "" || macCpe !== "")
                                        {
                                            validacion = true;
                                        }
                                        
                                        if(descripcionCpe=="NO HAY STOCK" || descripcionCpe=="NO EXISTE SERIAL" || descripcionCpe=="CPE NO ESTA EN ESTADO"){
                                            validacion=false;
                                            flag=3;
                                        }

                                        if((!hiddenResponsable) && (intIdResponsable == "" || intIdResponsable == null))
                                        {
                                            Ext.Msg.alert('Validación ','Favor escoger el responsable del retiro de equipo');
                                            return;
                                        }

                                        if(strNombreLider === "N/A")
                                        {
                                            Ext.Msg.alert('Validación ','Es obligatorio que la cuadrilla tenga un Líder para realizar la asignación');
                                            return;
                                        }

                                        if(validacion){
                                            Ext.get(formPanelElementoNuevo.getId()).mask('Cambiando Elemento del Cliente...');

                                            Ext.Ajax.request({
                                                url: url_cambiarEquipoWifi,
                                                method: 'post',
                                                timeout: 1000000,
                                                params: {
                                                    idServicio: data.idServicio,
                                                    idElemento: idElemento,
                                                    modeloCpe: modeloCpe,
                                                    nombreCpe: nombreCpe,
                                                    macCpe: macCpe,
                                                    serieCpe: serieCpe,
                                                    descripcionCpe: descripcionCpe,
                                                    tipoElementoCpe: tipoElemento,
                                                    strRegistraEquipo:  data.registroEquipo,
                                                    idResponsable:      intIdResponsable,
                                                    tipoResponsable:    seleccionResponsable
                                                },
                                                success: function(response){
                                                    Ext.get(formPanelElementoNuevo.getId()).unmask();
                                                    if(response.responseText == "OK"){
                                                        Ext.Msg.alert('Mensaje','Se Cambio el Elemento del Cliente', function(btn){
                                                            if(btn=='ok'){
                                                                store.load();
                                                                storeSolicitud.load();
                                                                win.destroy();
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
                                        else{
                                            if(flag==3){
                                                Ext.Msg.alert("Validación","Datos del Elemento incorrectos, favor revisar");
                                            }
                                            else{
                                                Ext.Msg.alert("Validación","Favor Revise los campos");
                                            }
                                        }
                                    }
                                },
                                    {
                                        text: 'Cancelar',
                                        handler: function(){
                                            Ext.get(gridServicios.getId()).unmask();
                                            win.destroy();
                                        }
                                    }]
                            });

                            var win = Ext.create('Ext.window.Window', {
                                title: 'Cambiar Equipo del Cliente',
                                modal: true,
                                width: 535,
                                closable: true,
                                layout: 'fit',
                                items: [formPanelElementoNuevo]
                            }).show();
                        }
                    }
                ]
            }
        ],
        viewConfig:{
            stripeRows:true
        },
        frame: false,
        height: 200
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
                defaults: {
                    width: 727
                },
                items: [

                    gridElementosPorSolicitud

                ]
            }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Elementos Por Solicitud',
        modal: true,
        width: 770,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function cambioNodoWifi(rec)
{
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosCliente,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: rec.get('idServicio')
        },
        success: function(response)
        {
            Ext.get(gridServicios.getId()).unmask();
            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados[0];
            
            var idPunto = datos.idPunto;

            var arrayParametros = [];
            var interfaceOdf;
            arrayParametros['prefijoEmpresa'] = prefijoEmpresa;
            arrayParametros['strDescripcionTipoRol'] = 'Contacto';
            arrayParametros['strEstadoTipoRol'] = 'Activo';
            arrayParametros['strDescripcionRol'] = 'Contacto Tecnico';
            arrayParametros['strEstadoRol'] = 'Activo';
            arrayParametros['strEstadoIER'] = 'Activo';
            arrayParametros['strEstadoIPER'] = 'Activo';
            arrayParametros['strEstadoIPC'] = 'Activo';
            arrayParametros['intIdPersonaEmpresaRol'] = rec.get("idPersonaEmpresaRol");
            arrayParametros['intIdPunto'] = idPunto;
            arrayParametros['idStore'] = 'storeContactoIngFac';
            arrayParametros['strJoinPunto'] = '';
            strDefaultType = "hiddenfield";
            strXtype = "hiddenfield";
            strNombres = '';
            strApellidos = '';
            strErrorMetraje = '';
            boolCheckObraCivil = false;
            boolCheckObservacionRegeneracion = false;
            storeRolContacto = '';
            storeRolContactoPunto = '';

            var boolEsEdificio = true;
            var boolDependeDeEdificio = true;
            var boolNombreEdificio = true;
            if ("S" === rec.get("strEsEdificio")) {
                boolEsEdificio = false;
            }
            if ("S" === rec.get("strDependeDeEdificio")) {
                boolDependeDeEdificio = false;
            }
            if (false === boolDependeDeEdificio || false === boolEsEdificio) {
                boolNombreEdificio = false;
            }
            var strNombreTipoElemento = "SPLITTER";
            var strNombreElementoPadre = "Olt";
            strNombreTipoElemento = "CASSETTE";

            winIngresoFactibilidad = "";
            formPanelInresoFactibilidad = "";
            if (!winIngresoFactibilidad)
            {
                //******** html campos requeridos...
                var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
                CamposRequeridos = Ext.create('Ext.Component', {
                    html: iniHtmlCamposRequeridos,
                    padding: 1,
                    layout: 'anchor',
                    style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
                });

                storeElementosFactibles = new Ext.data.Store
                    ({
                        total: 'total',
                        pageSize: 200,
                        proxy:
                            {
                                type: 'ajax',
                                method: 'post',
                                url: getElementosFactibles,
                                reader:
                                    {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                extraParams:
                                    {
                                        idServicio: rec.get('idServicio')

                                    }
                            },
                        fields:
                            [
                                {name: 'idElementoFactible', mapping: 'idElementoFactible'},
                                {name: 'nombreElementoFactible', mapping: 'nombreElementoFactible'}
                            ],
                        listeners:
                            {
                                load: function(store, records)
                                {
                                    var mensaje = store.proxy.reader.jsonData.mensaje;
                                    if (mensaje != 'OK')
                                    {
                                        Ext.MessageBox.alert('Error', mensaje);
                                    }
                                }
                            },
                    });


                var storeInterfacesElemento = new Ext.data.Store({
                    pageSize: 100,
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
                    fields:
                        [
                            {name: 'idInterface', mapping: 'idInterface'},
                            {name: 'nombreInterface', mapping: 'nombreInterface'}
                        ]
                });

                storeElementosNodoWifi = new Ext.data.Store({
                    total: 'total',
                    autoDestroy: true,
                    autoLoad: false,
                    listeners: {
                        load: function() {

                        }
                    },
                    proxy: {
                        type: 'ajax',
                        url: url_getElementosWifi,
                        timeout: 120000,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        listeners: {
                            exception: function(proxy, response, options) {
                                Ext.MessageBox.alert('Error', "Favor ingrese el nombre");
                            }
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: {
                            nombre: this.nombreElemento,
                            tipoElemento: 'NODO WIFI',
                            idServicio: rec.get('idServicio')
                        }
                    },
                    fields:
                        [
                            {name: 'idElemento', mapping: 'idElemento'},
                            {name: 'nombreElemento', mapping: 'nombreElemento'},
                            {name: 'modelo', mapping: 'modelo'}
                        ],
                });

                DTFechaProgramacion = new Ext.form.DateField({
                    id: 'fechaProgramacion',
                    name: 'fechaProgramacion',
                    fieldLabel: '* Fecha',
                    labelAlign: 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    editable: false,
                    minValue: new Date(),
                    value: new Date(),
                    labelStyle: "color:red;"
                });

                storeElementos = new Ext.data.Store({
                    total: 'total',
                    listeners: {
                        load: function() {
                        }
                    },
                    proxy: {
                        type: 'ajax',
                        url: urlComboCajas,
                        timeout: 120000,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        listeners: {
                            exception: function(proxy, response, options) {
                                Ext.MessageBox.alert('Error', "Favor ingrese un nombre de caja");
                            }
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: {
                            nombre: this.strNombreElemento
                        }
                    },
                    fields:
                        [
                            {name: 'intIdElemento', mapping: 'intIdElemento'},
                            {name: 'strNombreElemento', mapping: 'strNombreElemento'}
                        ]
                });

                storeElementosByPadre = new Ext.data.Store({
                    total: 'total',
                    pageSize: 10000,
                    proxy: {
                        type: 'ajax',
                        url: urlComboElementosByPadre,
                        timeout: 120000,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: {
                            popId: '',
                            elemento: strNombreTipoElemento
                        }
                    },
                    fields:
                        [
                            {name: 'idElemento', mapping: 'idElemento'},
                            {name: 'nombreElemento', mapping: 'nombreElemento'}
                        ]
                });

                /*modelClaseTipoMedio*/
                Ext.define('modelClaseTipoMedio', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'idClaseTipoMedio', type: 'int'},
                        {name: 'nombreClaseTipoMedio', type: 'string'}
                    ]
                });

                //Store de storeClaseTipoMedio
                storeClaseTipoMedio = Ext.create('Ext.data.Store', {
                    autoLoad: true,
                    model: "modelClaseTipoMedio",
                    proxy: {
                        type: 'ajax',
                        url: urlGetClaseTipoMedio,
                        reader: {
                            type: 'json',
                            root: 'encontrados'
                        },
                        extraParams: {
                            tipoMedioId: rec.get('txtIdUltimaMilla'),
                            estado: 'Activo'
                        }
                    }
                });

                Ext.define('modelInterfaceElemento', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'intIdInterfaceElemento', type: 'int'},
                        {name: 'strNombreInterfaceElemento', type: 'string'}
                    ]
                });

                storePuertos = Ext.create('Ext.data.Store', {
                    model: "modelInterfaceElemento",
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: urlInterfacesByElemento,
                        reader: {
                            type: 'json',
                            root: 'encontrados'
                        },
                        extraParams: {
                            intIdElemento: '',
                            strEstado: ''
                        }
                    }
                });

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
                            strBuscaHilosServicios: 'BUSCA_HILOS_SERVICIOS',
                            intIdPunto: idPunto
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

                cbxPuertos = Ext.create('Ext.form.ComboBox', {
                    id: 'cbxPuertos',
                    name: 'cbxPuertos',
                    store: storeHilosDisponibles,
                    queryMode: 'local',
                    fieldLabel: '* Hilos Disponibles',
                    labelStyle: "color:red;",
                    displayField: 'numeroColorHilo',
                    valueField: 'idInterfaceElementoOut',
                    editable: false,
                    hidden: true,
                    listeners:
                        {
                            select: function(combo)
                            {
                                var objeto = combo.valueModels[0].raw;
                                Ext.Ajax.request({
                                    url: ajaxJsonPuertoOdfByHilo,
                                    method: 'post',
                                    async: false,
                                    params: {idInterfaceElementoConector: objeto.idInterfaceElemento},
                                    success: function(response)
                                    {
                                        var json = Ext.JSON.decode(response.responseText);
                                        interfaceOdf = json.idInterfaceElemento;
                                        intIdInterfaceElemento = json.idInterfaceElemento;
                                        var arrayParamDistribucionTN = [];
                                        arrayParamDistribucionTN['strUrlInfoCaja'] = urlInfoCaja;
                                        arrayParamDistribucionTN['intIdElementoContenedor'] = Ext.getCmp('cbxIdElementoCaja').value;
                                        arrayParamDistribucionTN['strIdElementoDistribucion'] = objeto.idInterfaceElemento;
                                        arrayParamDistribucionTN['strTipoBusqueda'] = 'INTERFACE';
                                        arrayParamDistribucionTN['strNombreElementoPadre'] = strNombreElementoPadre.toUpperCase();
                                        arrayParamDistribucionTN['strNombreCaja'] = Ext.getCmp('cbxIdElementoCaja').getRawValue();
                                        arrayParamDistribucionTN['strNombreElemento'] = Ext.getCmp('cbxElementoPNivel').getRawValue();
                                        arrayParamDistribucionTN['strNombreTipoElemento'] = strNombreTipoElemento;
                                        arrayParamDistribucionTN['strNombreTipoMedio'] = rec.get("strNombreTipoMedio");
                                        arrayParamDistribucionTN['prefijoEmpresa'] = prefijoEmpresa;
                                        arrayParamDistribucionTN['strUrlDisponibilidadElemento'] = urlDisponibilidadElemento;
                                        arrayParamDistribucionTN['strUrlCalculaMetraje'] = urlCalculaMetraje;
                                        arrayParamDistribucionTN['intIdPunto'] = idPunto;
                                        arrayParamDistribucionTN['winIngresoFactibilidad'] = winIngresoFactibilidad;

                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });


                            }
                        }
                });

                formPanelIngresoFactibilidad = Ext.create('Ext.form.Panel', {
                    buttonAlign: 'center',
                    BodyPadding: 10,
                    bodyStyle: "background: white; padding: 5px; border: 0px none;",
                    frame: true,
                    items: [
                        CamposRequeridos,
                        {
                            xtype: 'fieldset',
                            title: '',
                            layout: {
                                tdAttrs: {style: 'padding: 5px;'},
                                type: 'table',
                                columns: 2,
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: 'fieldset',
                                    title: 'Datos del Cliente',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: 'anchor',
                                    defaults: {
                                        width: '350px'
                                    },
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Cliente',
                                            name: 'info_cliente',
                                            id: 'info_cliente',
                                            value: datos.nombreCompleto,
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Login',
                                            name: 'info_login',
                                            id: 'info_login',
                                            value: rec.get("login"),
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Ciudad',
                                            name: 'info_ciudad',
                                            id: 'info_ciudad',
                                            value: datos.strCanton,
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Direccion',
                                            name: 'info_direccion',
                                            id: 'info_direccion',
                                            value: datos.direccion,
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Sector',
                                            name: 'info_nombreSector',
                                            id: 'info_nombreSector',
                                            value: datos.strSector,
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Latitud',
                                            name: 'strLatitud',
                                            id: 'intIdLatitud',
                                            value: datos.latitud,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Longitud',
                                            name: 'strLongitud',
                                            id: 'intIdLongitud',
                                            value: datos.longitud,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'checkboxfield',
                                            fieldLabel: 'Es Edificio',
                                            checked: true,
                                            readOnly: true,
                                            hidden: boolEsEdificio
                                        },
                                        {
                                            xtype: 'checkboxfield',
                                            fieldLabel: 'Depende de Edificio',
                                            readOnly: true,
                                            checked: true,
                                            hidden: boolDependeDeEdificio
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Nombre Edificio',
                                            name: 'strNombreEdificio',
                                            id: 'intIdNombreEdificio',
                                            value: rec.get("strNombreEdificio"),
                                            allowBlank: false,
                                            readOnly: true,
                                            hidden: boolNombreEdificio
                                        }
                                    ]
                                },
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos de Factibilidad',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 5px;",
                            layout: {
                                tdAttrs: {style: 'padding: 5px;'},
                                type: 'table',
                                columns: 2,
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: 'fieldset',
                                    style: "border:0",
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Elemento',
                                            name: 'txtNameElementoPadre',
                                            id: 'txtIdNameElementoPadre',
                                            readOnly: true,
                                            hidden: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Tipo Elemento',
                                            name: 'txtNameTipoElemento',
                                            id: 'txtIdTipoElemento',
                                            readOnly: true,
                                            hidden: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Última Milla',
                                            name: 'txtNameUltimaMilla',
                                            id: 'txtIdUltimaMilla',
                                            readOnly: true,
                                            hidden: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Puerto',
                                            name: 'txtPuerto',
                                            id: 'txtIdPuerto',
                                            readOnly: true,
                                            hidden: true
                                        },
                                        {
                                            xtype: 'combobox',
                                            id: 'idCbxClaseFibra',
                                            fieldLabel: 'Clase Fibra',
                                            store: storeClaseTipoMedio,
                                            triggerAction: 'all',
                                            displayField: 'nombreClaseTipoMedio',
                                            valueField: 'idClaseTipoMedio',
                                            loadingText: 'Seleccione ...',
                                            listClass: 'x-combo-list-small',
                                            queryMode: 'local',
                                            hidden: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'MODELO NODO',
                                            name: 'txtModeloNodoWifi',
                                            id: 'txtModeloNodoWifi',
                                            readOnly: true,
                                            hidden: true
                                        },
                                        {
                                            loadingText: 'Buscando ...',
                                            xtype: 'combobox',
                                            name: 'cmbNodoWifi',
                                            id: 'cmbNodoWifi',
                                            fieldLabel: '* NODO WIFI',
                                            labelStyle: "color:red;",
                                            displayField: 'nombreElemento',
                                            queryMode: "remote",
                                            valueField: 'idElemento',
                                            store: storeElementosNodoWifi,
                                            lazyRender: true,
                                            forceSelection: true,
                                            loadingText: 'Buscando...',
                                                width: 300,
                                            minChars: 3,
                                            listeners: {
                                                select: function(combo) {
                                                    var objeto = combo.valueModels[0].raw;

                                                    storeElementosFactibles.proxy.extraParams = {idElemento: combo.getValue(), idServicio: rec.get('idServicio')};
                                                    storeElementosFactibles.load({params: {}});
                                                    Ext.getCmp('cmbElementoFactible').setVisible(true);
                                                    Ext.getCmp('txtModeloNodoWifi').setValue('');
                                                    Ext.getCmp('txtModeloNodoWifi').setVisible(true);
                                                    Ext.getCmp('txtModeloNodoWifi').setValue(objeto.nombreModeloElemento);
                                                    Ext.getCmp('cbxIdElementoCaja').disable();

                                                    Ext.getCmp('cbxIdElementoCaja').setValue('');
                                                    Ext.getCmp('cbxElementoPNivel').setValue('');
                                                    Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                    Ext.getCmp('cbxPuertos').setValue("");
                                                    Ext.getCmp('txtIdPuerto').setValue("");

                                                    Ext.getCmp('cbxIdElementoCaja').setVisible(false);
                                                    Ext.getCmp('cbxElementoPNivel').setVisible(false);
                                                    Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                    Ext.getCmp('cbxPuertos').setVisible(false);
                                                    Ext.getCmp('txtIdPuerto').setVisible(false);
                                                    if (objeto.nombreModeloElemento == 'BACKBONE')
                                                    {
                                                        Ext.getCmp('cbxIdElementoCaja').setVisible(true);
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'cmbElementoFactible',
                                            name: 'cmbElementoFactible',
                                            fieldLabel: '* ELEMENTO',
                                            labelStyle: "color:red;",
                                            hidden: true,
                                            displayField: 'nombreElementoFactible',
                                            valueField: 'idElementoFactible',
                                            loadingText: 'Buscando...',
                                            store: storeElementosFactibles,
                                            width: 300,
                                            listeners: {
                                                select: function(combo) {
                                                    storeInterfacesElemento.proxy.extraParams = {idElemento: combo.getValue(), estado: 'not connect'};
                                                    storeInterfacesElemento.load({params: {}});
                                                    Ext.getCmp('cbxIdElementoCaja').enable();
                                                    Ext.getCmp('interfaceElementoNuevo').setVisible(true);
                                                }
                                            },
                                        },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'interfaceElementoNuevo',
                                            name: 'interfaceElementoNuevo',
                                            fieldLabel: '* PUERTO ELEMENTO',
                                            labelStyle: "color:red;",
                                            hidden: true,
                                            displayField: 'nombreInterface',
                                            valueField: 'idInterface',
                                            loadingText: 'Buscando...',
                                            store: storeInterfacesElemento,
                                            width: 300,
                                        },
                                        {
                                            xtype: 'combobox',
                                            id: 'cbxIdElementoCaja',
                                            name: 'cbxElementoCaja',
                                            fieldLabel: '* CAJA',
                                            typeAhead: true,
                                            triggerAction: 'all',
                                            displayField: 'strNombreElemento',
                                            queryMode: "remote",
                                            valueField: 'intIdElemento',
                                            selectOnTab: true,
                                            hidden: true,
                                            store: storeElementos,
                                            width: 470,
                                            lazyRender: true,
                                            listClass: 'x-combo-list-small',
                                            labelStyle: "color:red;",
                                            forceSelection: true,
                                            disabled: true,
                                            minChars: 3,
                                            listeners: {
                                                select: {fn: function(combo, value) {
                                                        Ext.getCmp('cbxElementoPNivel').setVisible(true);
                                                        Ext.getCmp('txtIdInterfacesDisponibles').setValue(0);

                                                        Ext.getCmp('txtIdNameElementoPadre').setValue("");
                                                        Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                                                        Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                        Ext.getCmp('txtIdTipoElemento').setValue("");
                                                        Ext.getCmp('txtIdModeloElemento').setValue("");
                                                        Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                        Ext.getCmp('cbxPuertos').setValue("");
                                                        Ext.getCmp('txtIdPuerto').setValue("");

                                                        Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                        Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                                                        Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                        Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                        Ext.getCmp('txtIdModeloElemento').setVisible(false);
                                                        Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                        Ext.getCmp('cbxPuertos').setVisible(false);
                                                        Ext.getCmp('txtIdPuerto').setVisible(false);

                                                        storeElementosByPadre.proxy.extraParams = {
                                                            popId: combo.getValue(),
                                                            elemento: strNombreTipoElemento,
                                                            estado: 'Activo'
                                                        };
                                                        storeElementosByPadre.load({params: {}});
                                                    }}
                                            }
                                        },
                                        {
                                            xtype: 'combobox',
                                            id: 'cbxElementoPNivel',
                                            name: 'cbxElementoPNivel',
                                            fieldLabel: '* ' + strNombreTipoElemento,
                                            hidden: true,
                                            typeAhead: true,
                                            width: 470,
                                            queryMode: "local",
                                            triggerAction: 'all',
                                            displayField: 'nombreElemento',
                                            valueField: 'idElemento',
                                            selectOnTab: true,
                                            store: storeElementosByPadre,
                                            lazyRender: true,
                                            listClass: 'x-combo-list-small',
                                            emptyText: 'Seleccione un ' + strNombreTipoElemento,
                                            labelStyle: "color:red;",
                                            editable: false,
                                            listeners: {
                                                select: {fn: function(combo, value) {

                                                        Ext.getCmp('txtIdNameElementoPadre').setValue("");
                                                        Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                                                        Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                        Ext.getCmp('txtIdTipoElemento').setValue("");
                                                        Ext.getCmp('txtIdModeloElemento').setValue("");
                                                        Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                        Ext.getCmp('cbxPuertos').setValue("");
                                                        Ext.getCmp('txtIdPuerto').setValue("");

                                                        Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                        Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                                                        Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                        Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                        Ext.getCmp('txtIdModeloElemento').setVisible(false);
                                                        Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                        Ext.getCmp('cbxPuertos').setVisible(false);
                                                        Ext.getCmp('txtIdPuerto').setVisible(false);

                                                        var arrayParamInfoElemDist = [];
                                                        arrayParamInfoElemDist['prefijoEmpresa'] = prefijoEmpresa;
                                                        arrayParamInfoElemDist['strIdElementoDistribucion'] = combo.getValue();
                                                        arrayParamInfoElemDist['strNombreCaja'] = Ext.getCmp('cbxIdElementoCaja').getRawValue();
                                                        arrayParamInfoElemDist['intIdElementoContenedor'] = Ext.getCmp('cbxIdElementoCaja').value;
                                                        arrayParamInfoElemDist['strUrlInfoCaja'] = urlInfoCaja;
                                                        arrayParamInfoElemDist['strTipoBusqueda'] = 'ELEMENTO';
                                                        arrayParamInfoElemDist['strNombreElementoPadre'] = strNombreElementoPadre;
                                                        arrayParamInfoElemDist['strNombreElemento'] = combo.getRawValue();
                                                        arrayParamInfoElemDist['strNombreTipoElemento'] = strNombreTipoElemento;
                                                        arrayParamInfoElemDist['strNombreTipoMedio'] = rec.get("strNombreTipoMedio");
                                                        arrayParamInfoElemDist['strUrlDisponibilidadElemento'] = urlDisponibilidadElemento;
                                                        arrayParamInfoElemDist['strUrlCalculaMetraje'] = urlCalculaMetraje;
                                                        arrayParamInfoElemDist['intIdPunto'] = idPunto;
                                                        arrayParamInfoElemDist['winIngresoFactibilidad'] = winIngresoFactibilidad;
                                                        if ("TN" === prefijoEmpresa) {
                                                            arrayParamInfoElemDist['storeHilosDisponibles'] = storeHilosDisponibles;
                                                            var objResponseHiloMetraje = buscaHiloCalculaMetraje(arrayParamInfoElemDist);
                                                            Ext.getCmp('chbxIdObraCivil').setVisible(false);
                                                            Ext.getCmp('chbxIdObservacionRegeneracion').setVisible(false);
                                                            Ext.getCmp('txtIdObservacionRegeneracion').setVisible(false);
                                                            if ("100" !== objResponseHiloMetraje.strStatus) {
                                                                strErrorMetraje = objResponseHiloMetraje.strMessageStatus;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Metraje',
                                            name: 'txtNamefloatMetraje',
                                            hidden: true,
                                            id: 'txtIdfloatMetraje',
                                            regex: /^(?:\d*\.\d{1,2}|\d+)$/,
                                            value: 0,
                                            readOnly: false
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Disponibilidad',
                                            name: 'txtInterfacesDisponibles',
                                            hidden: true,
                                            id: 'txtIdInterfacesDisponibles',
                                            maxLength: 3,
                                            value: 0,
                                            readOnly: true
                                        },
                                        cbxPuertos
                                    ]
                                },
                                {
                                    xtype: 'fieldset',
                                    style: "border:0",
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Modelo Elemento',
                                            name: 'txtNameModeloElemento',
                                            id: 'txtIdModeloElemento',
                                            readOnly: true,
                                            hidden: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Marca Elemento',
                                            name: 'txtMarcaElementoPadre',
                                            id: 'txtIdMarcaElementoPadre',
                                            readOnly: true,
                                            hidden: true
                                        },
                                        {
                                            xtype: 'checkboxfield',
                                            fieldLabel: 'Obra Civil',
                                            name: 'chbxObraCivil',
                                            checked: boolCheckObraCivil,
                                            id: 'chbxIdObraCivil',
                                            hidden: true
                                        },
                                        {
                                            xtype: 'checkboxfield',
                                            fieldLabel: 'Requiere permisos regeneración',
                                            name: 'chbxObservacionRegeneracion',
                                            id: 'chbxIdObservacionRegeneracion',
                                            checked: boolCheckObservacionRegeneracion,
                                            hidden: true
                                        },
                                        {
                                            xtype: 'textarea',
                                            fieldLabel: 'Observacion',
                                            name: 'txtObservacionRegeneracion',
                                            id: 'txtIdObservacionRegeneracion',
                                            value: rec.get("strObservacionPermiRegeneracion"),
                                            hidden: true
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    buttons: [
                        {
                            text: 'Guardar',
                            handler: function() {
                                var txtModelo = Ext.getCmp('txtModeloNodoWifi').value;

                                var intIdElementoCaja = Ext.getCmp('cbxIdElementoCaja').value;//caja
                                var intElementoPNivel = Ext.getCmp('cbxElementoPNivel').value;//casette
                                var intInterfaceElementoDistribucion = Ext.getCmp('cbxPuertos').value;//interfaceCasette
                                var intPuertosDisponibles = Ext.getCmp('txtIdInterfacesDisponibles').value;
                                var chbxObservacionRegeneracion = Ext.getCmp('chbxIdObservacionRegeneracion').value;
                                var floatMetraje = Ext.getCmp('txtIdfloatMetraje').value;
                                var boolError = false;
                                var parametros;
                                var mensajeError = "";

                                var idNodoWifi = Ext.getCmp('cmbNodoWifi').value;
                                var idElementoWifi = Ext.getCmp('cmbElementoFactible').value;
                                var idInterfaceElementoWifi = Ext.getCmp('interfaceElementoNuevo').value;

                                if (!idNodoWifi || idNodoWifi == "" || idNodoWifi == 0)
                                {
                                    boolError = true;
                                    mensajeError += "Favor ingrese el nodo wifi \n";
                                }

                                if (!idElementoWifi || idElementoWifi == "" || idElementoWifi == 0)
                                {
                                    boolError = true;
                                    mensajeError += " Favor ingrese el elemento . \n";
                                }

                                if (!idInterfaceElementoWifi || idInterfaceElementoWifi == "" || idInterfaceElementoWifi == 0)
                                {
                                    boolError = true;
                                    mensajeError += " Favor ingrese el puerto del elemento.\n";
                                }


                                if (txtModelo == 'BACKBONE')
                                {
                                    if (interfaceOdf == 0 || interfaceOdf == "")
                                    {
                                        boolError = true;
                                        mensajeError += "El casette no tiene relacionado un ODF, favor corregir.\n";
                                    }

                                    if (!intIdElementoCaja || intIdElementoCaja == "" || intIdElementoCaja == 0)
                                    {
                                        boolError = true;
                                        mensajeError += "El Elemento Caja no fue escogido, por favor seleccione.\n";
                                    }
                                    if (!intElementoPNivel || intElementoPNivel == "" || intElementoPNivel == 0)
                                    {
                                        boolError = true;
                                        mensajeError += "El Elemento " + strNombreTipoElemento + " no fue escogido, por favor seleccione.\n";
                                    }
                                    if (null == intInterfaceElementoDistribucion) {
                                        boolError = true;
                                        mensajeError += "No ha seleccionado un hilo para el " + strNombreTipoElemento + ". \n";
                                    }
                                }


                                parametros = {
                                    modeloNodoWifi: txtModelo,
                                    idNodoWifi: idNodoWifi,
                                    idElementoWifi: idElementoWifi,
                                    idInterfaceElementoWifi: idInterfaceElementoWifi,
                                    intInterfaceOdf: interfaceOdf,
                                    intIdElementoCaja: intIdElementoCaja,
                                    idCasette: intElementoPNivel,
                                    idInterfaceCasette: intInterfaceElementoDistribucion,
                                    floatMetraje: floatMetraje,
                                    strErrorMetraje: strErrorMetraje,
                                    strNombreTipoElemento: strNombreTipoElemento,
                                    idServicio: rec.get("idServicio")
                                };

                                if (!boolError)
                                {
                                    connFactibilidad.request({
                                        url: url_cambioNodoWifi,
                                        method: 'post',
                                        timeout: 120000,
                                        params: parametros,
                                        success: function(response) {
                                            var text = response.responseText;
                                            cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                            if (text == "OK")
                                            {
                                                Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function(btn) {
                                                    if (btn == 'ok') {
                                                        store.load();
                                                    }
                                                });
                                            }
                                            else {
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: text,
                                                    buttons: Ext.MessageBox.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }
                                        },
                                        failure: function(result) {
                                            cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: result.responseText,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    });
                                }
                                else {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: mensajeError,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        },
                        {
                            text: 'Cerrar',
                            handler: function() {
                                cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                            }
                        }
                    ]
                });

                winIngresoFactibilidad = Ext.widget('window', {
                    title: 'Cambio de nodo Wifi',
                    layout: 'fit',
                    resizable: false,
                    modal: true,
                    closable: false,
                    items: [formPanelIngresoFactibilidad]
                });
            }

            winIngresoFactibilidad.show();

        }
    });
    
}

function cierraVentanaIngresoFactibilidad() {
    winIngresoFactibilidad.close();
    winIngresoFactibilidad.destroy();
}

function seteaLiderCuadrilla(cuadrilla)
{
    connCargarLider.request({
        url: getLiderCuadrilla,
        method: 'post',
        params:
            {
                cuadrillaId: cuadrilla
            },
        success: function(response) {
            var text = Ext.decode(response.responseText);

            Ext.getCmp('nombreLider').setValue(text.nombres);
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