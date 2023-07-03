Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

boolFormaContactoSeleccionadoLoad   = false;
boolCorreoUsuarioSeleccionadoLoad   = false;

function confirmarServicioNetlifeCamStoragePortal(data,intIdAccion)
{
    Ext.MessageBox.wait("Verificando servicio de Internet...Por favor espere!!!");
    Ext.Ajax.request({
        url: strUrlTieneServicioInternetValido,
        method: 'post',
        timeout: 400000,
        params:
            {
                intIdServicio: data.idServicio,
            },
        success: function(response)
        {
            var json = Ext.JSON.decode(response.responseText);
            if (json.strStatus === "OK")
            {
                var boolTieneInternetValido   = json.boolTieneInternetValido;
                Ext.MessageBox.wait("Cargando Información...Por favor espere");
                if(boolTieneInternetValido)
                {
                    Ext.Ajax.request({
                        url: strUrlGetInfoNetlifeCam,
                        method: 'post',
                        timeout: 400000,
                        params:
                            {
                                idServicio: data.idServicio
                            },
                        success: function(response)
                        {
                            var json = Ext.JSON.decode(response.responseText);
                            if (json.strStatus === "OK")
                            {
                                var boolUserExiste   = json.boolUserExiste;
                                var boolRolExiste    = json.boolRolExiste;
                                var strCorreoUsuario = json.arrayUser3dEYE.userName;

                                storeModelosCamaras = new Ext.data.Store({
                                    pageSize: 1000,
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url: strUrlGetModelosCamara,
                                        extraParams: {
                                            tipo: 'CAMARA',
                                            forma: 'Empieza con',
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
                                            {name: 'intIdModeloElemento',       mapping: 'codigo'},
                                            {name: 'strNombreModeloElemento',   mapping: 'modelo'}
                                        ],
                                    listeners:
                                        {
                                            load: function(store)
                                            {
                                                store.insert(0,[{ intIdModeloElemento: 0, strNombreModeloElemento: 'Seleccione el modelo'}]);
                                            }
                                        }
                                });

                                storeTipoActivacion = Ext.create('Ext.data.Store', {
                                    fields: ['value', 'name'],
                                    data : [
                                        {"value": "P2P", "name":"P2P"},
                                        {"value": "ONVIF", "name":"Onvif"},
                                        {"value": "GENERIC", "name":"Generico"}
                                    ]
                                });

                                storeFabricantes = Ext.create('Ext.data.Store', {
                                    fields: ['value', 'name'],
                                    data : [
                                        {"value": "Other", "name":"Otros"},
                                        {"value": "ACTi", "name":"ACTi"},
                                        {"value": "Avigilon", "name":"Avigilon"},
                                        {"value": "Axis", "name":"Axis"},
                                        {"value": "Bosch", "name":"Bosch"},
                                        {"value": "Brickcom", "name":"Brickcom"},
                                        {"value": "Canon", "name":"Canon"},
                                        {"value": "Dahua", "name":"Dahua"},
                                        {"value": "FlexWATCH", "name":"FlexWATCH"},
                                        {"value": "Flir", "name":"Flir"},
                                        {"value": "Hikvision", "name":"Hikvision"},
                                        {"value": "Honeywell", "name":"Honeywell"},
                                        {"value": "Panasonic", "name":"Panasonic"},
                                        {"value": "Pelco", "name":"Pelco"},
                                        {"value": "Samsung", "name":"Samsung"},
                                        {"value": "Sony", "name":"Sony"},
                                        {"value": "VIVOTEK", "name":"VIVOTEK"},
                                        {"value": "Uniview", "name":"Uniview"}
                                    ]
                                });
                                
                                function resetValuesP2P() {
                                    Ext.getCmp("registrationCodeP2P").setValue("");
                                }
                                
                                function resetValuesONVIF() {
                                    Ext.getCmp("DDNSCamaraONVIF").setValue("");
                                    Ext.getCmp("puertoHTTPCamaraONVIF").setValue("");
                                    Ext.getCmp("puertoRTSPCamaraONVIF").setValue("");
                                }

                                function resetValuesGENERIC() {
                                    Ext.getCmp("deviceBrandGENERIC").setValue("");
                                    Ext.getCmp("DDNSCamaraGENERIC").setValue("");
                                    Ext.getCmp("RTSPCamaraGENERIC").setValue("");
                                    Ext.getCmp("puertoHTTPCamaraGENERIC").setValue("");
                                    Ext.getCmp("puertoRTSPCamaraGENERIC").setValue("");
                                }
                                
                                function validateFormActivacion(tipoActivacion,value) {
                                    var tipoActivacionCamara     = Ext.getCmp('tipoActivacionCamara').getValue();
                                    if (tipoActivacionCamara === tipoActivacion){
                                        if (value === ""){
                                            return "El campo es requerido"
                                        } else {
                                            return true;
                                        }
                                    } else {
                                        return true;
                                    }
                                }
                                
                                var formInfoCamara = Ext.create('Ext.form.Panel', {
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    border: false,
                                    bodyPadding: 10,
                                    fieldDefaults: {
                                        labelAlign: 'top',
                                        labelWidth: 130,
                                        labelStyle: 'font-weight:bold'
                                    },
                                    defaults: {
                                        margins: '0 0 10 0'
                                    },
                                    title: 'Información de la Cámara',
                                    id: 'formInfoCamara',
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            id: 'nombreCamara',
                                            name: 'nombreCamara',
                                            fieldLabel: '* Nombre Cámara',
                                            value: json.strNombreCamara,
                                            allowBlank: false,
                                            readOnly: true,
                                            maxLength: 50
                                        },
                                        {
                                            xtype: 'textfield',
                                            id: 'macCamara',
                                            name: 'macCamara',
                                            fieldLabel: '* MAC Cámara',
                                            value: '',
                                            allowBlank: false,
                                            maxLength: 50
                                        },
                                        {
                                            xtype: 'textfield',
                                            id: 'serieCamara',
                                            name: 'serieCamara',
                                            fieldLabel: '* Serie Cámara',
                                            value: '',
                                            allowBlank: false
                                        },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'modeloCamara',
                                            name: 'modeloCamara',
                                            fieldLabel: '* Modelo Cámara',
                                            displayField: 'strNombreModeloElemento',
                                            valueField: 'intIdModeloElemento',
                                            loadingText: 'Buscando...',
                                            store: storeModelosCamaras,
                                            width: 225,
                                            allowBlank: false,
                                            listeners: {
                                                blur: function() {
                                                    var valueSerieCamara    = Ext.getCmp('serieCamara').getValue();
                                                    var valueModeloCamara   = Ext.getCmp('modeloCamara').getValue();
                                                    var index               = storeModelosCamaras.find('intIdModeloElemento', valueModeloCamara );
                                                    if((index !== -1 && valueModeloCamara !== 0) && valueSerieCamara.trim()!=="")
                                                    {
                                                        var recordModelo = storeModelosCamaras.getAt(index);
                                                        Ext.Ajax.request({
                                                            url: strUrlBuscarCamNafNetlifeCamStoragePortal,
                                                            method: 'post',
                                                            params: {
                                                                serieCam: valueSerieCamara,
                                                                modeloCam: recordModelo.data.strNombreModeloElemento,
                                                                estado: 'PI',
                                                                bandera: 'ActivarServicio'
                                                            },
                                                            success: function(response)
                                                            {
                                                                var respuesta = Ext.decode(response.responseText);
                                                                if(respuesta.strStatus!=="OK")
                                                                {
                                                                    Ext.Msg.alert('Error ', respuesta.strMensaje
                                                                        + '<br>Por favor verifique la información de la cámara');
                                                                }
                                                            },
                                                            failure: function()
                                                            {
                                                                Ext.Msg.alert('Error ', 'Ha ocurrido un problema. Por favor notifique a Sistemas');
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'tipoActivacionCamara',
                                            name: 'tipoActivacionCamara',
                                            fieldLabel: '* Tipo de activación',
                                            displayField: 'name',
                                            valueField: 'value',
                                            store: storeTipoActivacion,
                                            width: 225,
                                            allowBlank: false,
                                            forceSelection : false,
                                            listeners : {
                                                'select' : {
                                                    fn : function(combo, record, index) {
                                                        var value = combo.getValue();
                                                        var formP2P = Ext.getCmp('formP2P');
                                                        var formONVIF = Ext.getCmp('formONVIF');
                                                        var formGENERIC = Ext.getCmp('formGENERIC');
                                                        if (value == "P2P"){
                                                            formP2P.show();
                                                            resetValuesONVIF();
                                                            formONVIF.hide();
                                                            resetValuesGENERIC();
                                                            formGENERIC.hide();
                                                        } else if (value == "ONVIF"){
                                                            formONVIF.show();
                                                            resetValuesP2P();
                                                            formP2P.hide();
                                                            resetValuesGENERIC();
                                                            formGENERIC.hide();
                                                        } else if (value == "GENERIC"){
                                                            formGENERIC.show();
                                                            resetValuesP2P();
                                                            formP2P.hide();
                                                            resetValuesONVIF();
                                                            formONVIF.hide();
                                                        }
                                                    },
                                                },
                                            },
                                        },
                                        {
                                            xtype: 'form',
                                            layout: {
                                                type: 'vbox',
                                                align: 'stretch'
                                            },
                                            border: false,
                                            bodyPadding: 10,
                                            fieldDefaults: {
                                                labelAlign: 'top',
                                                labelWidth: 130,
                                                labelStyle: 'font-weight:bold'
                                            },
                                            defaults: {
                                                margins: '0 0 10 0'
                                            },
                                            id: 'formP2P',
                                            name: 'formP2P',
                                            hidden: true,
                                            items: [
                                                {
                                                    xtype: 'textfield',
                                                    id: 'registrationCodeP2P',
                                                    name: 'registrationCodeP2P',
                                                    fieldLabel: '* Código Push',
                                                    value: '',
                                                    minLengthText: 15,
                                                    minlength: 15,
                                                    maxLength: 40,
                                                    validator : function(value){
                                                        return validateFormActivacion('P2P', value);
                                                    }
                                                },

                                            ]
                                        },
                                        {
                                            xtype: 'form',
                                            layout: {
                                                type: 'vbox',
                                                align: 'stretch'
                                            },
                                            border: false,
                                            bodyPadding: 10,
                                            fieldDefaults: {
                                                labelAlign: 'top',
                                                labelWidth: 130,
                                                labelStyle: 'font-weight:bold'
                                            },
                                            defaults: {
                                                margins: '0 0 10 0'
                                            },
                                            id: 'formONVIF',
                                            name: 'formONVIF',
                                            hidden: true,
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    title: 'Información del acceso de puertos',
                                                    defaultType: 'textfield',
                                                    layout: {
                                                        type: 'hbox',
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'DDNSCamaraONVIF',
                                                            name: 'DDNSCamaraONVIF',
                                                            minWidth: 200,
                                                            fieldLabel: '* DDNS',
                                                            displayField: 'DDNS',
                                                            value: '',
                                                            validator : function(value){
                                                                return validateFormActivacion('ONVIF', value);
                                                            }
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            minWidth: 20
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'puertoHTTPCamaraONVIF',
                                                            width: 90,
                                                            name: 'puertoHTTPCamaraONVIF',
                                                            fieldLabel: '* Puerto HTTP',
                                                            value: '',
                                                            validator : function(value){
                                                                return validateFormActivacion('ONVIF', value);
                                                            }
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            minWidth: 10
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'puertoRTSPCamaraONVIF',
                                                            width: 90,
                                                            name: 'puertoRTSPCamaraONVIF',
                                                            fieldLabel: '* Puerto RTSP',
                                                            value: '',
                                                            validator : function(value){
                                                                return validateFormActivacion('ONVIF', value);
                                                            }
                                                        }
                                                    ]
                                                },
                                            ]
                                        },
                                        {
                                            xtype: 'form',
                                            layout: {
                                                type: 'vbox',
                                                align: 'stretch'
                                            },
                                            border: false,
                                            bodyPadding: 10,
                                            fieldDefaults: {
                                                labelAlign: 'top',
                                                labelWidth: 130,
                                                labelStyle: 'font-weight:bold'
                                            },
                                            defaults: {
                                                margins: '0 0 10 0'
                                            },
                                            id: 'formGENERIC',
                                            name: 'formGENERIC',
                                            hidden: true,
                                            items: [
                                                {
                                                    queryMode: 'local',
                                                    xtype: 'combobox',
                                                    id: 'deviceBrandGENERIC',
                                                    name: 'deviceBrandGENERIC',
                                                    fieldLabel: '* Fabricante',
                                                    displayField: 'name',
                                                    valueField: 'value',
                                                    store: storeFabricantes,
                                                    width: 225,
                                                    validator : function(value){
                                                        return validateFormActivacion('GENERIC', value);
                                                    }
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    title: 'Información del acceso HTTP',
                                                    defaultType: 'textfield',
                                                    layout: {
                                                        type: 'hbox',
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'DDNSCamaraGENERIC',
                                                            name: 'DDNSCamaraGENERIC',
                                                            minWidth: 250,
                                                            fieldLabel: '* DDNS',
                                                            displayField: 'DDNS',
                                                            value: '',
                                                            validator : function(value){
                                                                return validateFormActivacion('GENERIC', value);
                                                            }
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            minWidth: 30
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'puertoHTTPCamaraGENERIC',
                                                            width: 90,
                                                            name: 'puertoHTTPCamaraGENERIC',
                                                            fieldLabel: '* Puerto HTTP',
                                                            value: '',
                                                            validator : function(value){
                                                                return validateFormActivacion('GENERIC', value);
                                                            }
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    title: 'Información del acceso RTSP',
                                                    defaultType: 'textfield',
                                                    layout: {
                                                        type: 'hbox',
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'RTSPCamaraGENERIC',
                                                            name: 'RTSPCamaraGENERIC',
                                                            minWidth: 250,
                                                            fieldLabel: '* RTSP',
                                                            displayField: 'RTSP',
                                                            value: '',
                                                            validator : function(value){
                                                                return validateFormActivacion('GENERIC', value);
                                                            }
                                                        },
                                                        {
                                                            xtype: 'displayfield',
                                                            minWidth: 30
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'puertoRTSPCamaraGENERIC',
                                                            width: 90,
                                                            name: 'puertoRTSPCamaraGENERIC',
                                                            fieldLabel: '* Puerto RTSP',
                                                            value: '',
                                                            validator : function(value){
                                                                return validateFormActivacion('GENERIC', value);
                                                            }
                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'fieldset',
                                            title: 'Administración de la cámara',
                                            defaultType: 'textfield',
                                            layout: {
                                                type: 'hbox',
                                                align: 'stretch'
                                            },
                                            items: [
                                                {
                                                    xtype: 'textfield',
                                                    id: 'adminName',
                                                    name: 'adminName',
                                                    minWidth: 150,
                                                    fieldLabel: '* Usuario',
                                                    displayField: 'Usuario',
                                                    value: '',
                                                    allowBlank: false
                                                },
                                                {
                                                    xtype: 'displayfield',
                                                    minWidth: 100
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    id: 'adminPassword',
                                                    name: 'adminPassword',
                                                    minWidth: 150,
                                                    fieldLabel: '* Contraseña',
                                                    displayField: 'Contraseña',
                                                    value: '',
                                                    allowBlank: false
                                                }
                                            ]
                                        }
                                    ]
                                });

                                Ext.define('ModelFormasContactoCliente',
                                {
                                    extend: 'Ext.data.Model',
                                    fields:
                                        [
                                            {name: 'strValorFormaContacto',             type: 'string'},
                                        ],
                                    idProperty: 'strValorFormaContacto'
                                });

                                storeFormasContactoCliente = new Ext.data.Store({
                                    pageSize: 10,
                                    model: 'ModelFormasContactoCliente',
                                    total: 'intTotal',
                                    proxy: {
                                        type: 'ajax',
                                        timeout: 600000,
                                        url: strUrlGetFormasContactosClienteNetlifeCam,
                                        reader:
                                            {
                                                type: 'json',
                                                totalProperty: 'intTotal',
                                                root: 'arrayResultado'
                                            },
                                        extraParams: {
                                            intIdPunto:             json.intIdPunto,
                                            intIdPersona:           json.intIdPersona
                                        }
                                    },
                                    autoLoad: true,
                                });

                                var gripContactosCliente = Ext.create('Ext.grid.Panel', {
                                    id: 'emailUsuario',
                                    name: 'emailUsuario',
                                    store: storeFormasContactoCliente,
                                    selModel: Ext.create('Ext.selection.CheckboxModel', {
                                        checkOnly: true,
                                        showHeaderCheckbox: false,  //here is where it is added.
                                        mode: 'SINGLE'
                                    }),
                                    disabled: boolUserExiste,
                                    viewConfig: {
                                        listeners: {
                                            viewready: function (grid) {
                                                var data = grid.getStore().data.items;
                                                Ext.each(data, function(item, index){
                                                    if (item.data.strValorFormaContacto === strCorreoUsuario) {
                                                        grid.getSelectionModel().select(index);
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    columns: [
                                        {
                                            header: 'Correo',
                                            dataIndex: 'strValorFormaContacto',
                                            width: 400,
                                            sortable: true
                                        },
                                    ],
                                    height: 200,
                                    width: 400
                                });

                                var formContactosCliente = Ext.create('Ext.form.Panel', {
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    border: false,
                                    bodyPadding: 10,
                                    fieldDefaults: {
                                        labelAlign: 'top',
                                        labelWidth: 130,
                                        labelStyle: 'font-weight:bold'
                                    },
                                    defaults: {
                                        margins: '0 0 10 0'
                                    },
                                    title: 'Selección de Contacto',
                                    id: 'formContactosCliente',
                                    items: [
                                        gripContactosCliente
                                    ]
                                });

                                var formPrincipal = Ext.create('Ext.form.Panel', {
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    border: false,
                                    bodyPadding: 10,
                                    fieldDefaults: {
                                        labelAlign: 'top',
                                        labelWidth: 130,
                                        labelStyle: 'font-weight:bold'
                                    },
                                    defaults: {
                                        margins: '0 0 10 0'
                                    },
                                    autoScroll: true,
                                    items: [
                                        {
                                            xtype: 'displayfield',
                                            name: 'strTitulo',
                                            value: "<div class='infomessage1' style='display: block;'>"
                                                +"1.Ingrese la informaci&oacute;n t&eacute;cnica de la c&aacute;mara.<br>2."
                                                + (boolUserExiste ?
                                                    "*El cliente ya posee usuario y credencial.<br>3."
                                                    : "Seleccione el correo electr&oacute;nico que será el usuario del portal donde se "
                                                    +"enviar&aacute; la informaci&oacute;n de activaci&oacute;n.<br>3.")
                                                +"Ingrese la configuraci&oacute;n del usuario en la c&aacute;mara.",
                                            textAlign: 'center'
                                        },
                                        {
                                            xtype: 'hiddenfield',
                                            id: 'strTienePortalActivo',
                                            name: 'strTienePortalActivo',
                                            value: boolUserExiste ? "SI" : "NO",
                                            textAlign: 'center'
                                        },
                                        {
                                            xtype: 'tabpanel',
                                            activeTab: 0,
                                            autoScroll: false,
                                            layoutOnTabChange: true,
                                            items: [formInfoCamara, formContactosCliente]
                                        }
                                    ],

                                    buttons: [
                                        {
                                            text: 'Confirmar',
                                            handler: function() {
                                                var form = this.up('form').getForm();

                                                if (form.isValid()) {
                                                    var tipoActivacionCamara    = Ext.getCmp('tipoActivacionCamara').getValue();
                                                    var adminName               = Ext.getCmp('adminName').getValue();
                                                    var adminPassword           = Ext.getCmp('adminPassword').getValue();

                                                    // P2P
                                                    var registrationCodeP2P     = Ext.getCmp('registrationCodeP2P').getValue();
                                                    // ONVIF
                                                    var DDNSCamaraONVIF         = Ext.getCmp('DDNSCamaraONVIF').getValue();
                                                    var puertoHTTPCamaraONVIF   = Ext.getCmp('puertoHTTPCamaraONVIF').getValue();
                                                    var puertoRTSPCamaraONVIF   = Ext.getCmp('puertoRTSPCamaraONVIF').getValue();

                                                    // GENERIC
                                                    var deviceBrandGENERIC      = Ext.getCmp('deviceBrandGENERIC').getValue();
                                                    var DDNSCamaraGENERIC       = Ext.getCmp('DDNSCamaraGENERIC').getValue();
                                                    var RTSPCamaraGENERIC       = Ext.getCmp('RTSPCamaraGENERIC').getValue();
                                                    var puertoHTTPCamaraGENERIC = Ext.getCmp('puertoHTTPCamaraGENERIC').getValue();
                                                    var puertoRTSPCamaraGENERIC = Ext.getCmp('puertoRTSPCamaraGENERIC').getValue();


                                                    // Valores predeterminados
                                                    var archiveDurationHours    = null;
                                                    var addToRole               = null;
                                                    var emailUsuarioValue       = strCorreoUsuario ? strCorreoUsuario: null;

                                                    var emailUsuario = Ext.getCmp('emailUsuario').getSelectionModel();
                                                    var selectedRecordsEmailUsuario = emailUsuario.getSelection();
                                                    if (emailUsuario.hasSelection()){
                                                        emailUsuarioValue = selectedRecordsEmailUsuario[0].get('strValorFormaContacto');
                                                    }

                                                    var arrayDataCreateCamara = {
                                                        "strTipoActivacion"         : tipoActivacionCamara,
                                                        "strNombreCamara"           : json.strNombreCamara,
                                                        "strRegistrationCode"       : registrationCodeP2P,
                                                        "strDDNSCamaraONVIF"        : DDNSCamaraONVIF,
                                                        "strPuertoHTTPCamaraONVIF"  : puertoHTTPCamaraONVIF,
                                                        "strPuertoRTSPCamaraONVIF"  : puertoRTSPCamaraONVIF,
                                                        "strDeviceBrandGENERIC"     : deviceBrandGENERIC,
                                                        "strDDNSCamaraGENERIC"      : DDNSCamaraGENERIC,
                                                        "strRTSPCamaraGENERIC"      : RTSPCamaraGENERIC,
                                                        "strPuertoHTTPCamaraGENERIC": puertoHTTPCamaraGENERIC,
                                                        "strPuertoRTSPCamaraGENERIC": puertoRTSPCamaraGENERIC,
                                                        "strAdminName"              : adminName,
                                                        "strAdminPassword"          : adminPassword,
                                                        "strArchiveDurationHours"   : archiveDurationHours,
                                                        "strAddToRole"              : addToRole
                                                    };

                                                    var arrayDataCreateUser = {
                                                        "boolUserExiste"    : boolUserExiste,
                                                        "arrayUser3dEYE"    : json.arrayUser3dEYE,
                                                        "strFirstName"      : json.arrayNuevoUser.firstName,
                                                        "strLastName"       : json.arrayNuevoUser.lastName,
                                                        "strPassword"       : json.arrayNuevoUser.password,
                                                        "strEmail"          : emailUsuarioValue
                                                    };

                                                    var arrayDataCreateRol = {
                                                        "boolRolExiste"     : boolRolExiste,
                                                        "arrayRol3dEYE"     : json.arrayRol3dEYE,
                                                        "strName"           : json.arrayNuevoRol.name,
                                                        "strDescription"    : json.arrayNuevoRol.description,
                                                        "strType"           : json.arrayNuevoRol.type
                                                    };

                                                    guardarInformacion(data, intIdAccion, tipoActivacionCamara,
                                                                        arrayDataCreateCamara, arrayDataCreateUser, arrayDataCreateRol);
                                                }
                                            }
                                        },
                                        {
                                            text: 'Cancelar',
                                            handler: function() {
                                                if (typeof winConfirmarServicioNCSP !== 'undefined'
                                                    && winConfirmarServicioNCSP != null)
                                                {
                                                    winConfirmarServicioNCSP.destroy();
                                                }
                                            }
                                        }
                                    ]
                                });

                                winConfirmarServicioNCSP = Ext.create('Ext.window.Window', {
                                    title: 'Confirmar Servicio',
                                    close: function()
                                    {
                                        if (this.fireEvent('beforeclose', this) !== false)
                                        {
                                            this.doClose();
                                        }
                                    },
                                    doClose: function()
                                    {
                                        this.fireEvent('close', this);
                                        this[this.closeAction]();
                                    },
                                    width: 520,
                                    height: 615,
                                    minHeight: 300,
                                    layout: 'fit',
                                    resizable: true,
                                    modal: true,
                                    items: [formPrincipal]
                                });

                                Ext.MessageBox.hide();
                                winConfirmarServicioNCSP.show();
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', json.strMensaje);
                            }
                        },
                        failure: function()
                        {
                            Ext.Msg.alert('Error', 'Error al realizar la acción, Favor Notificar a Sistemas');
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Error ', "No existe un servicio de Internet válido asociado a este punto");
                }
            }
            else
            {
                Ext.Msg.alert('Error ', "Ha ocurrido un problema. Por favor notifique a Sistemas!");
            }
        },
        failure: function()
        {
            Ext.Msg.alert('Error', 'Error al realizar la acción, Favor Notificar a Sistemas');
        }
    });
}

function guardarInformacion(data, intIdAccion, tipoActivacionCamara, arrayDataCreateCamara, arrayDataCreateUser, arrayDataCreateRol) {
    var connValidaSerie = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function() {
                    Ext.get(document.body).mask('Verificando Serie... Por favor espere!');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    var connGuardarInfo = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function() {
                    Ext.get(document.body).mask('Guardando Información de Cámara y Contactos...Por favor espere');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    // Valido email strCorreoUsuario
    if (!arrayDataCreateUser.strEmail){
        Ext.Msg.alert('Error', "Por favor seleccione el correo que será su usuario");
    } else if (arrayDataCreateUser.arrayUser3dEYE && arrayDataCreateUser.arrayUser3dEYE.userName !== arrayDataCreateUser.strEmail){
        Ext.Msg.show({
            title: 'Cambiar usuario?',
            msg: 'Se cambiara el usuario padre de la cámara, esto implicará la creación de un nuevo rol para el usuario seleccionado',
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            buttonText: {
                yes: "Si",
                no: "No"
            },
            fn: function (btn) {
                if (btn === "yes"){
                    alert("Cambiar");
                }
            }
        });
    } else if (arrayDataCreateCamara.strRegistrationCode.length < 15 && arrayDataCreateCamara.strRegistrationCode){
        Ext.Msg.alert('Error', "Por favor el código push debe tener una longitud de 15 a 40 caracteres");
    } else if (arrayDataCreateCamara.strDDNSCamaraONVIF && (arrayDataCreateCamara.strDDNSCamaraONVIF.indexOf("http://") != 0
                                                        && arrayDataCreateCamara.strDDNSCamaraONVIF.indexOf("https://") != 0)){
        Ext.Msg.alert('Error', "Por favor el DDNS debe utilizar el siguiente formato http(s)://hostname o http(s)://ip");
    } else if (arrayDataCreateCamara.strDDNSCamaraGENERIC && (arrayDataCreateCamara.strDDNSCamaraGENERIC.indexOf("http://") != 0
                                                        && arrayDataCreateCamara.strDDNSCamaraGENERIC.indexOf("https://") != 0)){
        Ext.Msg.alert('Error', "Por favor el DDNS debe utilizar el siguiente formato http(s)://hostname o http(s)://ip");
    } else if (arrayDataCreateCamara.strRTSPCamaraGENERIC && arrayDataCreateCamara.strRTSPCamaraGENERIC.indexOf("rtsp://") != 0){
        Ext.Msg.alert('Error', "Por favor el RTSP debe utilizar el siguiente formato rtsp://hostname o rtsp://ip");
    } else if (arrayDataCreateCamara.strPuertoHTTPCamaraGENERIC && Number.isInteger(arrayDataCreateCamara.strPuertoRTSPCamaraGENERIC)){
        Ext.Msg.alert('Error', "Por favor el puerto HTTP debe ser números enteros");
    } else if (arrayDataCreateCamara.strPuertoRTSPCamaraGENERIC && arrayDataCreateCamara.strPuertoRTSPCamaraGENERIC % 1 != 0){
        Ext.Msg.alert('Error', "Por favor el puerto RTSP debe ser números enteros");
    } else if (arrayDataCreateCamara.strPuertoHTTPCamaraONVIF && arrayDataCreateCamara.strPuertoHTTPCamaraONVIF % 1 != 0){
        Ext.Msg.alert('Error', "Por favor el puerto HTTP debe ser números enteros");
    } else if (arrayDataCreateCamara.strPuertoRTSPCamaraONVIF && arrayDataCreateCamara.strPuertoRTSPCamaraONVIF % 1 != 0){
        Ext.Msg.alert('Error', "Por favor el puerto RTSP debe ser números enteros");
    } else{
        // Mensaje de confirmacion de activacion
        Ext.Msg.show({
            title:'Confirmar datos?',
            msg: 'Se activará la cámara '+arrayDataCreateCamara.strNombreCamara+' con tipo de activación '+tipoActivacionCamara,
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            buttonText:{
                yes: "Si",
                no: "No"
            },
            fn: function (btn) {
                if (btn == "yes"){
                    // Obtengo los datos restantes del formulario
                    var strMACCam           = Ext.getCmp('macCamara').getValue();
                    var strSerieCam         = Ext.getCmp('serieCamara').getValue();
                    var valueModeloCamara   = Ext.getCmp('modeloCamara').getValue();
                    var indexModeloCamara   = storeModelosCamaras.find('intIdModeloElemento', valueModeloCamara);
                    var strModeloCam        = "";
                    if(indexModeloCamara !== -1 && indexModeloCamara !== 0)
                    {
                        var recordModelo    = storeModelosCamaras.getAt(indexModeloCamara);
                        strModeloCam        = recordModelo.data.strNombreModeloElemento;
                    }

                    connValidaSerie.request({
                        url: strUrlBuscarCamNafNetlifeCamStoragePortal,
                        timeout: 120000,
                        method: 'post',
                        params: {
                            serieCam:           strSerieCam,
                            modeloCam:          strModeloCam
                        },
                        success: function(response) {
                            var respuesta = Ext.decode(response.responseText);
                            if(respuesta.strStatus==="OK")
                            {
                                connGuardarInfo.request({
                                    method: 'POST',
                                    timeout: 400000,
                                    params: {
                                        intIdServicio:                  data.idServicio,
                                        intIdPersonaEmpresaRol:         data.idPersonaEmpresaRol,
                                        intIdAccion:                    intIdAccion,
                                        strSerieCam:                    strSerieCam,
                                        intIdModeloCam:                 valueModeloCamara,
                                        strModeloCam:                   strModeloCam,
                                        strMACCam:                      strMACCam,
                                        strTipoActivacionCamara:        tipoActivacionCamara,
                                        arrayDataCreateCamara:          JSON.stringify(arrayDataCreateCamara),
                                        arrayDataCreateUser:            JSON.stringify(arrayDataCreateUser),
                                        arrayDataCreateRol:             JSON.stringify(arrayDataCreateRol)
                                    },
                                    url: strUrlGuardarConfirmaNetlifeCam,
                                    success: function(response) {
                                        var json = Ext.JSON.decode(response.responseText);
                                        if (json.strStatus === "OK")
                                        {
                                            Ext.MessageBox.show({
                                                title: "Información",
                                                msg: "La información se guardó con éxito. "+json.strMensaje,
                                                icon: Ext.MessageBox.INFO,
                                                buttons: Ext.Msg.OK,
                                                fn: function(buttonId)
                                                {
                                                    if (buttonId === "ok")
                                                    {
                                                        store.load();
                                                        if (typeof winConfirmarServicioNCSP !== 'undefined' && winConfirmarServicioNCSP != null)
                                                        {
                                                            winConfirmarServicioNCSP.destroy();
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Error ', json.strMensaje);
                                        }
                                    },
                                    failure: function()
                                    {
                                        Ext.Msg.alert('Error ', 'Error al realizar la acción');
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', respuesta.strMensaje + '<br>Por favor verifique la información de la cámara');
                            }
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error', result.responseText + ', Favor Notificar a Sistemas');
                        }
                    });
                }
            }
        });
    }
}


function guardarInformacionConfirmarServicio(data,intIdAccion,jsonUserPassCam)
{
    var connValidaSerie = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function() {
                    Ext.get(document.body).mask('Verificando Serie... Por favor espere!');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    
    var connGuardarInfo = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function() {
                    Ext.get(document.body).mask('Guardando Información de Cámara y Contactos...Por favor espere');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    
    var formInfoCamara      = Ext.getCmp('formInfoCamara').getForm();
    var valueModeloCamara   = Ext.getCmp('modeloCamara').getValue();
    if( formInfoCamara.isValid())
    {
        if(valueModeloCamara !== 0)
        {
            var strFormasContactoGestionados  = obtenerFormasContactosGestionadas();
            var objContactosGestionados = JSON.parse(strFormasContactoGestionados);
            if(objContactosGestionados['boolSeleccionFormaContacto'])
            {
                if(objContactosGestionados['boolSeleccionCorreoUsuario'])
                {        
                    var intIdElementoCam    = Ext.getCmp('idElementoCam').getValue();
                    var strSerieCam         = Ext.getCmp('serieCamara').getValue();
                    var intIdModeloCam      = valueModeloCamara;
                    var index               = storeModelosCamaras.find('intIdModeloElemento', intIdModeloCam);
                    var strModeloCam        = "";
                    if(index !== -1 && index !== 0)
                    {
                        var recordModelo    = storeModelosCamaras.getAt(index);
                        strModeloCam        = recordModelo.data.strNombreModeloElemento;
                    }
                    var strDDNSCam          = Ext.getCmp('DDNSCamara').getValue();
                    var strPuertoDDNSCam    = Ext.getCmp('puertoDDNSCamara').getValue();
                    var strIPCam            = Ext.getCmp('IPCamara').getValue();
                    var strPuertoIPCam      = Ext.getCmp('puertoIPCamara').getValue();
                    var strNombreCam        = Ext.getCmp('nombreCamara').getValue();
                    var strMACCam           = Ext.getCmp('macCamara').getValue();
                    connValidaSerie.request({
                        url: strUrlBuscarCamNafNetlifeCamStoragePortal,
                        timeout: 120000,
                        method: 'post',
                        params: {
                            serieCam:           strSerieCam,
                            modeloCam:          strModeloCam,
                            idElementoCam:      intIdElementoCam
                        },
                        success: function(response) {
                            var respuesta = Ext.decode(response.responseText);
                            if(respuesta.strStatus==="OK")
                            {
                                var strTienePortalActivo = Ext.getCmp('strTienePortalActivo').getValue();
                                connGuardarInfo.request({
                                        method: 'POST',
                                        timeout: 120000,
                                        params: {
                                            intIdServicio:                  data.idServicio,
                                            intIdPersonaEmpresaRol:         data.idPersonaEmpresaRol,
                                            strSerieCam:                    strSerieCam,
                                            intIdModeloCam:                 intIdModeloCam,
                                            strNombreCam:                   strNombreCam,
                                            strDDNSCam:                     strDDNSCam,
                                            strPuertoDDNSCam:               strPuertoDDNSCam,
                                            strIPCam:                       strIPCam,
                                            strPuertoIPCam:                 strPuertoIPCam,
                                            strMACCam:                      strMACCam,
                                            strFormasContactoGestionados:   strFormasContactoGestionados,
                                            strTienePortalActivo:           strTienePortalActivo,
                                            intIdAccion:                    intIdAccion,
                                            strUserAdminCam:                jsonUserPassCam.strUserAdminCam,
                                            strPassAdminCam:                jsonUserPassCam.strPassAdminCam,
                                            strUserVisitorCam:              jsonUserPassCam.strUserVisitorCam,
                                            strPassVisitorCam:              jsonUserPassCam.strPassVisitorCam
                                        },
                                        url: strUrlGuardarConfirmaNetlifeCamStoragePortal,
                                        success: function(response) {
                                            var json = Ext.JSON.decode(response.responseText);

                                            if (json.strStatus === "OK")
                                            {
                                                Ext.MessageBox.show({
                                                    title: "Información",
                                                    msg: "La información se guardó con éxito. "+json.strMensaje,
                                                    icon: Ext.MessageBox.INFO,
                                                    buttons: Ext.Msg.OK,
                                                    fn: function(buttonId)
                                                    {
                                                        if (buttonId === "ok")
                                                        {
                                                            store.load();
                                                            if (typeof winConfirmarServicioNCSP !== 'undefined' && winConfirmarServicioNCSP != null)
                                                            {
                                                                winConfirmarServicioNCSP.destroy();
                                                            }
                                                        }
                                                    }
                                                });
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error ', json.strMsjError);
                                                
                                            }
                                        },
                                        failure: function() 
                                        {
                                            Ext.Msg.alert('Error ', 'Error al realizar la acción');
                                        }
                                    });
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', respuesta.strMensaje + '<br>Por favor verifique la información de la cámara');
                            }
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error', result.responseText + ', Favor Notificar a Sistemas');
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Error', "Por favor seleccione el correo que será su usuario");
                }
            }
            else
            {
                Ext.Msg.alert('Error', "Por favor seleccione al menos un correo del listado");
            }
        }
        else
        {
            Ext.Msg.alert('Error', "Por favor seleccione el modelo de la cámara");
        }
    }
    else
    {
        Ext.Msg.alert('Error', "Por favor revisar la información de la cámara ingresada");
    }
}

function setCorreoUsuario(rowIdx)
{
    for(index in gridFormasContactoCliente.getStore().data.items)
    {
        if(gridFormasContactoCliente.getStore().data.items[index].data.boolEsCorreoUsuarioSeleccionado===1)
        {
            gridFormasContactoCliente.getStore().data.items[index].set('boolEsCorreoUsuarioSeleccionado',false);
            gridFormasContactoCliente.getStore().data.items[index].set('strValorCorreoUsuarioSeleccionado',"0"); 
        }
    }
    gridFormasContactoCliente.getStore().data.items[rowIdx].set('boolEsCorreoUsuarioSeleccionado',true);
    gridFormasContactoCliente.getStore().data.items[rowIdx].set('strValorCorreoUsuarioSeleccionado',"1"); 
}

function obtenerFormasContactosGestionadas()
{
    var objFormasContactosGestionados               = {};
    objFormasContactosGestionados['arrayRegistros'] = [];
    objFormasContactosGestionados['intTotal']       = 0;
    var intTotalContactosGestionados                = 0;
    var arrayMergeRows                              = [];
    var boolSeleccionFormaContacto                  = false;
    var boolSeleccionCorreoUsuario                  = false;

    if(storeFormasContactoCliente.getUpdatedRecords().length > 0)
    {
        var registrosActualizados = storeFormasContactoCliente.getUpdatedRecords();
        Ext.each(registrosActualizados,function(record)
        {
            arrayMergeRows.push(record.data);
            if(record.data.strValorFormaContactoSeleccionado==="1" && !boolSeleccionFormaContacto)
            {
                boolSeleccionFormaContacto = true;
            }
            
            if(record.data.strValorCorreoUsuarioSeleccionado==="1" && !boolSeleccionCorreoUsuario)
            {
                boolSeleccionCorreoUsuario = true;
            }
        });
        
        /*
         * Se verifica que en caso de que los registros modificados sean aquellos a los que se le has quitado la selección, 
         * exista alguno previamente seleccionado
         */
        if(!boolSeleccionFormaContacto && Ext.getCmp('strTienePortalActivo').getValue()==="SI")
        {
            storeFormasContactoCliente.each(function(record)
            {
                if(record.data.strValorFormaContactoSeleccionado==="1" && !boolSeleccionFormaContacto)
                {
                    boolSeleccionFormaContacto = true;
                }
            });
        }
        if(!boolSeleccionCorreoUsuario)
        {
            boolSeleccionCorreoUsuario  = boolCorreoUsuarioSeleccionadoLoad;
        }
        intTotalContactosGestionados = intTotalContactosGestionados + storeFormasContactoCliente.getUpdatedRecords().length;
    }
    else
    {
        boolSeleccionFormaContacto  = boolFormaContactoSeleccionadoLoad;
        boolSeleccionCorreoUsuario  = boolCorreoUsuarioSeleccionadoLoad;
    }
    
    objFormasContactosGestionados['arrayRegistros']                 = arrayMergeRows;
    objFormasContactosGestionados['intTotal']                       = intTotalContactosGestionados;
    objFormasContactosGestionados['boolSeleccionFormaContacto']     = boolSeleccionFormaContacto;
    objFormasContactosGestionados['boolSeleccionCorreoUsuario']     = boolSeleccionCorreoUsuario;

    strFormasContactosClienteGestionados = Ext.JSON.encode(objFormasContactosGestionados);
    return strFormasContactosClienteGestionados;
}

function confirmarProdAdicionalNetlifeCamStoragePortal(data,intIdAccion,strAliasProducto)
{
    Ext.MessageBox.wait("Cargando información...Por favor espere!");
    storeCamaras = new Ext.data.Store({
        pageSize: 10,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: strUrlGetServiciosCamarasDisponibles,
            extraParams: {
                intIdServicio: data.idServicio
            },
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayResultado'
            }
        },
        fields:
            [
                {name: 'intIdServCam',  mapping: 'idServ'},
                {name: 'strNombreCam',  mapping: 'nombre'}
            ],
        listeners: 
        {
            load: function(store)
            {
                 store.insert(0,[{ intIdServCam: 0, strNombreCam: 'Seleccione una cámara'}]);
            }      
        }
    });
    
    var formConfirmarProdAdic = Ext.widget('form', {
        layout: {
            type: 'vbox',
            align: 'stretch'
        },
        border: false,
        bodyPadding: 10,
        fieldDefaults: {
            labelAlign: 'top',
            labelWidth: 130,
            labelStyle: 'font-weight:bold'
        },
        defaults: {
            margins: '0 0 10 0'
        },
        id: 'formConfirmarProdAdic',
        items: [
            {
                xtype: 'displayfield',
                name: 'strTitulo',
                value: "<div class='infomessage1' style='display: block;'>Seleccione la cámara a la que desea agregarle el producto adicional<br>",
                textAlign: 'center'
            },
            {
                queryMode: 'local',
                xtype: 'combobox',
                id: 'servCamaraProdAdic',
                name: 'servCamaraProdAdic',
                fieldLabel: 'Cámara',
                displayField: 'strNombreCam',
                valueField: 'intIdServCam',
                loadingText: 'Buscando...',
                store: storeCamaras,
                width: 225,
                allowBlank: false,
                value: 0
            }
        ],
        buttons: [
            {
                text: 'Guardar',
                name: 'guardar',
                handler: function() {
                    guardarConfirmarServicioProdAdic(data,intIdAccion,strAliasProducto);
                }
            },
            {
                text: 'Cancelar',
                handler: function() {
                    if (typeof winConfirmarServicioProdAdic !== 'undefined' && winConfirmarServicioProdAdic != null)
                    {
                        winConfirmarServicioProdAdic.destroy();
                    }
                }
            }
        ]
    });
    
    
    winConfirmarServicioProdAdic = Ext.widget('window', {
        title: 'Confirmar Producto Adicional',
        close: function()
        {
            if (this.fireEvent('beforeclose', this) !== false) 
            {
                this.doClose();
            }
        },
        doClose: function()
        {
            this.fireEvent('close', this);
            this[this.closeAction]();
        },
        width: 420,
        height: 220,
        minHeight: 220,
        layout: 'fit',
        resizable: true,
        modal: true,
        items: [formConfirmarProdAdic]
    });
    
    Ext.MessageBox.hide();
    winConfirmarServicioProdAdic.show();
}

function guardarConfirmarServicioProdAdic(data,intIdAccion,strAliasProducto)
{
    var connGuardarInfo = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function() {
                    Ext.get(document.body).mask('Guardando Información de Cámara...Por favor espere');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    
    var formConfirmarProdAdic   = Ext.getCmp('formConfirmarProdAdic').getForm();
    var intIdServCamaraProdAdic = Ext.getCmp('servCamaraProdAdic').getValue();
    if( formConfirmarProdAdic.isValid() && intIdServCamaraProdAdic !== 0)
    {
        connGuardarInfo.request({
            method: 'POST',
            params: {
                intIdServicio:                  data.idServicio,
                intIdPersonaEmpresaRol:         data.idPersonaEmpresaRol,
                intIdServCamaraProdAdic:        intIdServCamaraProdAdic,
                intIdAccion:                    intIdAccion,
                intIdProducto:                  data.productoId,
                strAliasProducto:               strAliasProducto,
                intCantidad:                    data.cantidadReal
            },
            url: strUrlGuardarServiciosProdsAdicionales,
            success: function(response) {
                var json = Ext.JSON.decode(response.responseText);

                if (json.status === "OK")
                {
                    Ext.MessageBox.show({
                        title: "Información",
                        msg: "La información se guardó con éxito. "+json.msj,
                        icon: Ext.MessageBox.INFO,
                        buttons: Ext.Msg.OK,
                        fn: function(buttonId)
                        {
                            if (buttonId === "ok")
                            {
                                store.load();
                                if (typeof winConfirmarServicioProdAdic !== 'undefined' && winConfirmarServicioProdAdic != null)
                                {
                                    winConfirmarServicioProdAdic.destroy();
                                }
                            }
                        }
                    });
                }
                else if (typeof winConfirmarServicioProdAdic !== 'undefined' && winConfirmarServicioProdAdic != null)
                {
                    winConfirmarServicioProdAdic.destroy();
                    Ext.Msg.alert('Error ', json.msj);
                }
            },
            failure: function() 
            {
                Ext.Msg.alert('Error ', 'Error al realizar la acción');
            }
        });
    }
}

function reenviarCodigoTemporal(data)
{
    Ext.Msg.confirm('Alerta','Está seguro que desea reenviar la información del codigo temporal de verificación?', function(btn)
    {
        if(btn==='yes')
        {
            var connEsperaAccion = new Ext.data.Connection
            ({
                listeners:
                {
                    'beforerequest': 
                    {
                        fn: function ()
                        {						
                            Ext.MessageBox.show({
                               msg: 'Enviando correo, Por favor espere!',
                               progressText: 'Saving...',
                               width:300,
                               wait:true,
                               waitConfig: {interval:200}
                            });
                        },
                        scope: this
                    },
                    'requestcomplete':
                    {
                        fn: function ()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                    'requestexception': 
                    {
                        fn: function ()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
                }
            });
            
            
            connEsperaAccion.request({
                url: strUrlReenviarCodTmpPortalNetlifeCam,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    intIdPersonaEmpresaRol : data.idPersonaEmpresaRol
                },
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);

                    if (json.strStatus === "OK")
                    {
                        Ext.Msg.alert('Información', 'EL correo se ha reenviado de manera correcta');
                    }
                    else
                    {
                        Ext.Msg.alert('Error ', json.strMsj);
                    }
                },
                failure: function()
                {
                    Ext.Msg.alert('Error ', 'Error al realizar la acción');
                }
            });
        }
    });
}

function cortarServicioNetlifeCamStoragePortal(data, idAccion) {
    var connCortarServicio = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function() {
                    Ext.get(document.body).mask('Cortando el Servicio...Por favor espere');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Cortar el Servicio?', function(strBtn) {
        if (strBtn === 'ok') {
            connCortarServicio.request({
                url: strUrlCortarServiciosNetlifeCam,
                method: 'POST',
                timeout: 400000,
                params: {
                    intIdServicio: data.idServicio,
                    intIdAccion: idAccion
                },
                success: function(response) {
                    var json = Ext.JSON.decode(response.responseText);
                    if (json.strStatus === "OK")
                    {
                        Ext.Msg.alert('Mensaje', json.strMensaje, function(strBtn) {
                            if (strBtn === 'ok') {
                                store.load();
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje', json.strMensaje);
                    }
                },
                failure: function()
                {
                    Ext.Msg.alert('Error ', 'Error al realizar la acción');
                }
            });
        }
    });
}

function reconectarServicioNetlifeCamStoragePortal(data, idAccion) {
    var connReconectarServicio = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function() {
                    Ext.get(document.body).mask('Reconectando el Servicio...Por favor espere');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Reconectar el Servicio?', function(strBtn) {
        if (strBtn === 'ok') {
            connReconectarServicio.request({
                url: strUrlReconectarServiciosNetlifeCam,
                method: 'post',
                timeout: 400000,
                params: {
                    intIdServicio: data.idServicio,
                    intIdAccion: idAccion
                },
                success: function(response) {
                    var json = Ext.JSON.decode(response.responseText);
                    if (json.strStatus === "OK")
                    {
                        Ext.Msg.alert('Mensaje', json.strMensaje, function(strBtn) {
                            if (strBtn === 'ok') {
                                store.load();
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje', json.strMensaje);
                    }
                },
                failure: function()
                {
                    Ext.Msg.alert('Error ', 'Error al realizar la acción');
                }
            });
        }
    });
}



function cancelarServicioNetlifeCamStorage(data, idAccion){
    var connCancelarServicio = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function() {
                    Ext.get(document.body).mask('Cancelando el Servicio...Por favor espere');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function() {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar el Servicio seleccionado?', function(btn){
        if(btn=='ok'){
            connCancelarServicio.request({
                url: strUrlCancelarServiciosNetlifeCam,
                method: 'post',
                timeout: 400000,
                params: {
                    intIdServicio: data.idServicio,
                    intIdAccion: idAccion
                },
                success: function(response){
                    var json = Ext.JSON.decode(response.responseText);
                    if (json.strStatus === "OK")
                    {
                        Ext.Msg.alert('Mensaje', json.strMensaje, function(strBtn) {
                            if (strBtn === 'ok') {
                                store.load();
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje', json.strMensaje);
                    }
                },
                failure: function()
                {
                    Ext.Msg.alert('Error ', 'Error al realizar la acción');
                }

            });
        }
    });
}




