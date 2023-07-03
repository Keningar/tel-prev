let objFieldStyle = {
    'backgroundColor': '#F0F2F2',
    'backgrodunImage': 'none'
};

function confirmarServicioOtros(data, idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Confirmar el Servicio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Confirmando el Servicio...');
            Ext.Ajax.request({
                url: confirmarServicioOtrosBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();
                    var datos = Ext.JSON.decode(response.responseText);
                    
                    if(datos.srtResultado == "OK"){
                        
                        var mensajeFox = "";
                        if ("FOXPREMIUM" === data.descripcionProducto && datos.strCorreo!="" && datos.strMovil!="")
                        {
                            mensajeFox = "<br/> El usuario y contraseña fue enviado al correo : "+ datos.strCorreo + " y Teléfono: "+ datos.strMovil;
                        }
                        Ext.Msg.alert('Mensaje','Se Confirmo el Servicio!' + mensajeFox, function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje', 'No se pudo confirmar el servicio!<br />'+datos.srtResultado);
                    }

                }

            });
        }
    });
}

function confirmarServicioKonibit(data, idAccion) {
    Ext.Msg.alert('Mensaje','Esta seguro que desea activar el Servicio?', function(btn) {
        if(btn=='ok'){
            Ext.get("grid").mask('Activando el Servicio...');
            Ext.Ajax.request({
                url: confirmarServicioKonibitBoton,
                method: 'post',
                timeout: 40000,
                params: {
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();
                    var datos = Ext.JSON.decode(response.responseText);
                    console.log("Resultado: ",datos);
                    if(datos.strResultado == "Ok"){
                        Ext.Msg.alert('Mensaje','Se activo el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje', 'No se pudo activar el servicio!<br />'+datos.strMensaje);
                    }

                }
            });
        }
    });
}

function confirmarRegistrarElemento(data, gridIndex){

    var valorProducto;
    var titulo                  = 'Información de los Elementos del Cliente';
    var tituloTarjeta           = "";
    var hiddenTarjeta           = true;
    var etiquetaMac             = "* Mac";
    var propiedadSoloLecturaMac = false;
    var boolAlowBlankSerieTarjet= true;

    valorProducto = data.nombreProducto;

    if(data.productoPermitidoRegistroEle == "S")
    {
        valorProducto           = data.descripcionProducto;
        titulo                  = 'Información del elemento';
        tituloTarjeta           = 'Información de la tarjeta';
        hiddenTarjeta           = false;
        etiquetaMac             = "&nbsp;&nbsp;&nbsp;Mac";
        propiedadSoloLecturaMac = true;
        boolAlowBlankSerieTarjet= false;
    }

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


            var storeModelosCpe = new Ext.data.Store({
                pageSize: 1000,
                proxy: {
                    type: 'ajax',
                    url : getModelosElemento,
                    extraParams: {
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

            var storeModelosTarjeta = new Ext.data.Store({
                pageSize: 1000,
                proxy: {
                    type: 'ajax',
                    url : getModelosElemento,
                    extraParams: {
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
                                        name: 'producto',
                                        id: 'txtProducto',
                                        fieldLabel: 'Producto',
                                        value: valorProducto,
                                        readOnly: true,
                                        width: '50%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        name: 'descripcionFactura',
                                        fieldLabel: 'Descripción Factura',
                                        value: data.descripcionPresentaFactura,
                                        readOnly: true,
                                        width: '50%',
                                    }

                                ]//cierre del container table
                            }
                        ]//cierre del fieldset
                    },
                    {
                        xtype: 'fieldset',
                        title: titulo,
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [
                            {
                                xtype: 'container',
                                id: 'fs_infoElementosCliente',
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                    align: 'stretch'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        id: 'serieCpe',
                                        allowBlank: false,
                                        name: 'serieCpe',
                                        fieldLabel: '* Serie',
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modeloCpe',
                                        name: 'modeloCpe',
                                        fieldLabel: '* Modelo',
                                        displayField: 'modelo',
                                        valueField: 'modelo',
                                        // allowBlank: false,
                                        loadingText: 'Buscando...',
                                        store: storeModelosCpe,
                                        width: '25%',
                                        listeners: {
                                            blur: function(combo) {
                                                if (typeof data.boolValidaNaf != "undefined" && data.boolValidaNaf)
                                                {
                                                    Ext.Ajax.request({
                                                        url: buscarCpeHuaweiNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe: Ext.getCmp('serieCpe').getValue(),
                                                            modeloElemento: combo.getValue(),
                                                            estado: 'PI',
                                                            bandera: 'ActivarServicio'
                                                        },
                                                        success: function (response) {
                                                            var respuesta = response.responseText.split("|");
                                                            var status = respuesta[0];
                                                            var mensaje = respuesta[1].split(",");
                                                            var descripcion = mensaje[0];
                                                            var macOntNaf = mensaje[1];
                                                            var marca = mensaje[4];
                                                            //agregar mas valores en caso de que caracteristica necesite validarse con naf
                                                            var caractAdicionalesNaf = {
                                                                'MARCA ELEMENTO': marca
                                                            };

                                                            if (status == "OK")
                                                            {
                                                                Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                                Ext.getCmp('descripcionCpe').setRawValue(descripcion);

                                                                Ext.getCmp('macCpe').setValue = macOntNaf;
                                                                Ext.getCmp('macCpe').setRawValue(macOntNaf);

                                                                // autocompleta caracteristica adicional con datos del elemento desde naf
                                                                if (data.arrayCaractAdicionales) {
                                                                    Object.entries(caractAdicionalesNaf).forEach(
                                                                        ([caracteristica, valor]) => {
                                                                            caracteristicaEncontrada = data.arrayCaractAdicionales.some(obj => 
                                                                                obj['DESCRIPCION_CARACTERISTICA'] === caracteristica);
                    
                                                                            if (caracteristicaEncontrada) {
                                                                                const campoCaracteristica = Ext.getCmp(caracteristica.toLowerCase());

                                                                                campoCaracteristica.setValue = valor;
                                                                                campoCaracteristica.setRawValue(valor);
                                                                            }
                                                                        }
                                                                    );                                                           
                                                                }
                                                            } 
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('descripcionCpe').setValue = status;
                                                                Ext.getCmp('descripcionCpe').setRawValue(status);

                                                                Ext.getCmp('macCpe').setValue = status;
                                                                Ext.getCmp('macCpe').setRawValue(status);

                                                                if (data.arrayCaractAdicionales) {
                                                                    Object.entries(caractAdicionalesNaf).forEach(
                                                                        ([caracteristica, valor]) => {
                                                                            caracteristicaEncontrada = data.arrayCaractAdicionales.some(obj => 
                                                                                obj['DESCRIPCION_CARACTERISTICA'] === caracteristica);
                    
                                                                            if (caracteristicaEncontrada) {
                                                                                const campoCaracteristica = Ext.getCmp(caracteristica.toLowerCase());
                    
                                                                                campoCaracteristica.setValue = status;
                                                                                campoCaracteristica.setRawValue(status);
                                                                            }
                                                                        }
                                                                    );                                                           
                                                                }
                                                            }
                                                        },
                                                        failure: function (result) {
                                                            Ext.get(formPanel.getId()).unmask();
                                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                        }
                                                    });
                                                }

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
                                                        var marca = mensaje[4];
                                                        //agregar mas valores en caractAdicionalesNaf en caso de que caracteristica 
                                                        //necesite validarse con naf
                                                        var caractAdicionalesNaf = {
                                                            'MARCA ELEMENTO': marca
                                                        };

                                                        if (status == "OK")
                                                        {
                                                            Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                            Ext.getCmp('descripcionCpe').setRawValue(descripcion);

                                                            Ext.getCmp('macCpe').setValue = macOntNaf;
                                                            Ext.getCmp('macCpe').setRawValue(macOntNaf);
                                                          
                                                            // autocompleta caracteristica adicional con datos del elemento desde naf
                                                            if (data.arrayCaractAdicionales) {
                                                                Object.entries(caractAdicionalesNaf).forEach(
                                                                    ([caracteristica, valor]) => {
                                                                        caracteristicaEncontrada = data.arrayCaractAdicionales.some(obj => 
                                                                            obj['DESCRIPCION_CARACTERISTICA'] === caracteristica);
                
                                                                        if (caracteristicaEncontrada) {
                                                                            const campoCaracteristica = Ext.getCmp(caracteristica.toLowerCase());
                                                                            campoCaracteristica.setValue = valor;
                                                                            campoCaracteristica.setRawValue(valor);
                                                                        }
                                                                    }
                                                                );                                                           
                                                            }
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Mensaje ', mensaje);
                                                            Ext.getCmp('descripcionCpe').setValue = status;
                                                            Ext.getCmp('descripcionCpe').setRawValue(status);

                                                            Ext.getCmp('macCpe').setValue = status;
                                                            Ext.getCmp('macCpe').setRawValue(status);

                                                            if (data.arrayCaractAdicionales) {
                                                                Object.entries(caractAdicionalesNaf).forEach(
                                                                    ([caracteristica, valor]) => {
                                                                        caracteristicaEncontrada = data.arrayCaractAdicionales.some(obj => 
                                                                            obj['DESCRIPCION_CARACTERISTICA'] === caracteristica);
                
                                                                        if (caracteristicaEncontrada) {
                                                                            const campoCaracteristica = Ext.getCmp(caracteristica.toLowerCase());
                
                                                                            campoCaracteristica.setValue = status;
                                                                            campoCaracteristica.setRawValue(status);
                                                                        }
                                                                    }
                                                                );                                                           
                                                            }
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
                                        xtype: 'textfield',
                                        id: 'macCpe',
                                        readOnly: propiedadSoloLecturaMac,
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                        name: 'macCpe',
                                        fieldLabel: etiquetaMac,
                                        // allowBlank: false,
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'descripcionCpe',
                                        readOnly: propiedadSoloLecturaMac,
                                        name: 'descripcionCpe',
                                        fieldLabel: '* Descripción',
                                        displayField: "",
                                        value: "",
                                        fieldCls: 'details-disabled',
                                        fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                        width: '25%'
                                    },
                                    {
                                        xtype: 'textareafield',
                                        id:'observacionCliente',
                                        name: 'observacionCliente',
                                        fieldLabel: '* Observación',
                                        displayField: "",
                                        labelPad: -56.9,
                                        colspan: 5,
                                        allowBlank: false,
                                        value: "",
                                        width: '85%'
                                    },
                                    {
                                        xtype: 'hidden',
                                        id: 'validacionMacOnt',
                                        name: 'validacionMacOnt',
                                        value: "",
                                        hidden:true,
                                        hideLabel:true,
                                        fieldStyle: `display:none;`
                                    },

                                ]//cierre del container table
                            }
                        ]
                    },//cierre informacion de los elementos del cliente
                    {
                        xtype: 'fieldset',
                        hidden: hiddenTarjeta,
                        title: tituloTarjeta,
                        defaultType: 'textfield',
                        defaults: {
                            width: 585
                        },
                        items: [
                            {
                                xtype: 'container',
                                id: 'fs_infoTarjeta',
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                    align: 'stretch'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        id: 'serieTarjeta',
                                        allowBlank: boolAlowBlankSerieTarjet,
                                        name: 'serieTarjeta',
                                        fieldLabel: '* Serie',
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modeloTarjeta',
                                        name: 'modeloTarjeta',
                                        fieldLabel: '* Modelo',
                                        displayField: 'modelo',
                                        valueField: 'modelo',
                                        loadingText: 'Buscando...',
                                        store: storeModelosTarjeta,
                                        width: '25%',
                                        listeners: {
                                            blur: function(combo) {
                                                if (typeof data.boolValidaNaf != "undefined" && data.boolValidaNaf)
                                                {
                                                    Ext.Ajax.request({
                                                        url: buscarCpeHuaweiNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe: Ext.getCmp('serieTarjeta').getValue(),
                                                            modeloElemento: combo.getValue(),
                                                            estado: 'PI',
                                                            bandera: 'ActivarServicio'
                                                        },
                                                        success: function (response) {
                                                            var respuesta = response.responseText.split("|");
                                                            var status = respuesta[0];
                                                            var mensaje = respuesta[1].split(",");
                                                            var capacidadTarjeta = mensaje[3];
                                                            var marca = mensaje[4];

                                                            if (status == "OK")
                                                            {
                                                                Ext.getCmp('capacidadCamara').setValue = capacidadTarjeta;
                                                                Ext.getCmp('capacidadCamara').setRawValue(capacidadTarjeta);

                                                                Ext.getCmp('marcaTarjeta').setValue = marca;
                                                                Ext.getCmp('marcaTarjeta').setRawValue(marca);
                                                            } else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('capacidadCamara').setValue = status;
                                                                Ext.getCmp('capacidadCamara').setRawValue(status);

                                                                Ext.getCmp('marcaTarjeta').setValue = status;
                                                                Ext.getCmp('marcaTarjeta').setRawValue(status);
                                                            }
                                                        },
                                                        failure: function (result) {
                                                            Ext.get(formPanel.getId()).unmask();
                                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                        }
                                                    });
                                                }

                                                Ext.Ajax.request({
                                                    url: buscarCpeHuaweiNaf,
                                                    method: 'post',
                                                    params: {
                                                        serieCpe: Ext.getCmp('serieTarjeta').getValue(),
                                                        modeloElemento: combo.getValue(),
                                                        estado: 'PI',
                                                        bandera: 'ActivarServicio'
                                                    },
                                                    success: function(response) {
                                                        var respuesta = response.responseText.split("|");
                                                        var status = respuesta[0];
                                                        var mensaje = respuesta[1].split(",");
                                                        var capacidadTarjeta = mensaje[3];
                                                        var marca = mensaje[4];

                                                        if (status == "OK")
                                                        {
                                                            Ext.getCmp('capacidadCamara').setValue = capacidadTarjeta;
                                                            Ext.getCmp('capacidadCamara').setRawValue(capacidadTarjeta);

                                                            Ext.getCmp('marcaTarjeta').setValue = marca;
                                                            Ext.getCmp('marcaTarjeta').setRawValue(marca);
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Mensaje ', mensaje);
                                                            Ext.getCmp('capacidadCamara').setValue = status;
                                                            Ext.getCmp('capacidadCamara').setRawValue(status);

                                                            Ext.getCmp('marcaTarjeta').setValue = status;
                                                            Ext.getCmp('marcaTarjeta').setRawValue(status);
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
                                        xtype: 'textfield',
                                        id: 'capacidadCamara',
                                        readOnly: propiedadSoloLecturaMac,
                                        name: 'capacidadCamara',
                                        fieldLabel: '* Capacidad',
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'marcaTarjeta',
                                        name: 'marcaTarjeta',
                                        fieldLabel: '* Marca',
                                        displayField: "",
                                        value: "",
                                        readOnly: true,
                                        width: '25%'
                                    },
                                ]//cierre del container camara
                            }
                        ]
                    }//cierre informacion de los elementos del cliente
                ],
                buttons: [{
                    text: 'Activar',
                    formBind: true,
                    handler: function(){
                        Ext.Msg.confirm('Confirmación', '¿Esta seguro que desea <b class="blue-text">registrar</b> y <b class="blue-text">activar</b> este servicio?', function(id) {
                            if (id === 'yes')
                            {
                                if (data.estado === 'Asignada' || data.estado === 'Pendiente')
                                {
                                    let modeloCpe       = Ext.getCmp('modeloCpe').getValue();
                                    let serieCpe        = Ext.getCmp('serieCpe').getValue();
                                    let mac             = Ext.getCmp('macCpe').getValue();
                                    let observacion     = Ext.getCmp('observacionCliente').getValue();

                                    let validacion      = true;

                                    if (serieCpe == "")
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert("Validación", "Debe ingresar la serie.");
                                        validacion = false;
                                    }

                                    if (data.arrayCaractAdicionales)
                                    {
                                        data.arrayCaractAdicionales  .forEach((el, index) => {
                                            let itemVal = Ext.getCmp(el['DESCRIPCION_CARACTERISTICA'].toLowerCase()).getValue();
                                            data.arrayCaractAdicionales  [index]['FIELD_VALUE'] = itemVal;
                                        });
                                    }

                                    var bandera = 1;
                                    if(data.productoPermitidoRegistroEle == "S")
                                    {
                                        var modeloTarjeta    = Ext.getCmp('modeloTarjeta').getValue();
                                        var serieTarjeta     = Ext.getCmp('serieTarjeta').getValue();
                                        var capacidadTarjeta = Ext.getCmp('capacidadCamara').getValue();
                                        var marcaTarjeta     = Ext.getCmp('marcaTarjeta').getValue();
                                    }
                                    else
                                    {
                                        if(mac)
                                        {

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
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.Msg.alert('Mensaje ', 'Favor ingrese la mac en formato correcto (aaaa.bbbb.cccc) * ');
                                                validacion = false;
                                            }
                                        }
                                        else
                                        {
                                            Ext.get(formPanel.getId()).unmask();
                                            Ext.Msg.alert('Mensaje ', 'Favor ingrese la MAC.');
                                            validacion = false;
                                        }
                                    }

                                    if (observacion == "")
                                    {
                                        Ext.get(formPanel.getId()).unmask();
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
                                                strNombreTecnico: data.descripcionProducto,
                                                productoPermitidoRegistroEle:data.productoPermitidoRegistroEle,
                                                serieTarjeta:serieTarjeta,
                                                modeloTarjeta:modeloTarjeta,
                                                capacidadTarjeta:capacidadTarjeta,
                                                marcaTarjeta:marcaTarjeta,
                                                strTipoElemento:data.tipoElemento,
                                                strSerieSmartWifi: serieCpe,
                                                strModeloSmartWifi: modeloCpe,
                                                strMacSmartWifi:mac,
                                                intIdServicioInternet: data.intIdServicioInternet
                                            },
                                            success: function (response) {
                                                if (response.responseText == "OK")
                                                {
                                                    if(data.productoPermitidoRegistroEle != "S")
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
                                                                login: data.login,
                                                                capacidad1: data.capacidadUno,
                                                                capacidad2: data.capacidadDos,
                                                                interfaceElementoId: data.interfaceElementoId,
                                                                plan: data.planId,
                                                                serieWifi: serieCpe,
                                                                modeloWifi: modeloCpe,
                                                                macWifi: mac,
                                                                observacionCliente: observacion,
                                                                boolRequiereRegistro: data.boolRequiereRegistro,
                                                                boolTieneFlujo: data.boolTieneFlujo,
                                                                boolValidaNaf: data.boolValidaNaf,
                                                                arrayCaractAdicionales  : JSON.stringify(data.arrayCaractAdicionales)
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
                                                    else
                                                    {
                                                        Ext.get(formPanel.getId()).unmask();
                                                        Ext.Msg.alert('Mensaje ', 'Se Activo el servicio exitosamente: ' + data.login);
                                                        win.destroy();
                                                        store.load();
                                                    }
                                                }
                                                else {
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje','Error: ocurrió un error en la activación, favor notificar a Sistemas.');
                                                }
                                            },
                                            failure: function (result) {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                    else {
                                        console.log(Ext.get(formPanel.getId()));
                                        Ext.get(formPanel.getId()).unmask();
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

            if (typeof data.boolValidaNaf !== "undefined" && !data.boolValidaNaf)
            {
                if(data.productoPermitidoRegistroEle != "S")
                {
                    Ext.getCmp('macCpe').readOnly         = false;
                    Ext.getCmp('descripcionCpe').readOnly = false;
                }

                Ext.getCmp('macCpe').fieldStyle = '';
                Ext.getCmp('macCpe').fieldCls = '';

                Ext.getCmp('descripcionCpe').fieldStyle = '';
                Ext.getCmp('descripcionCpe').fieldCls = '';
            }

            if (data.arrayCaractAdicionales)
            {
                data.arrayCaractAdicionales.forEach((el, index) => {
                    if (el['VALORES_SELECCIONABLES'] && el['VALOR_DEFECTO'] && el['VALORES_SELECCIONABLES']) {
                        var values = el['VALORES_SELECCIONABLES'].split(',');

                        // Create an ExtJS store with the values array
                        var storeSeleccion = Ext.create('Ext.data.Store', {
                            fields: ['value'],
                            data: values.map(function(value) {
                                return { value: titleCase(value) } ;
                            })
                        });

                        Ext.getCmp('fs_infoElementosCliente').insert(4 + index, 
                            [
                                {
                                    xtype: 'combo',
                                    id: el['DESCRIPCION_CARACTERISTICA'].toLowerCase(),
                                    name: el['DESCRIPCION_CARACTERISTICA'].toLowerCase(),
                                    fieldLabel: '* ' + titleCase(el['LABEL']),
                                    displayField: 'value',
                                    valueField: 'value',
                                    allowBlank: false,
                                    editable: false,
                                    width: '25%',
                                    store: storeSeleccion,
                                    value: titleCase(el['VALOR_DEFECTO'])
                                }
                            ]
                        );
                    } else {
                        Ext.getCmp('fs_infoElementosCliente').insert(4 + index,
                            [
                                {
                                    xtype: 'textfield',
                                    id: el['DESCRIPCION_CARACTERISTICA'].toLowerCase(),
                                    name: el['DESCRIPCION_CARACTERISTICA'].toLowerCase(),
                                    fieldLabel: '* ' + titleCase(el['LABEL']),
                                    fieldCls: el['DESCRIPCION_CARACTERISTICA'] === 'MARCA ELEMENTO' ? 'details-disabled' : null,
                                    displayField: "",
                                    allowBlank: el['ALLOW_BLANK'],
                                    value: "",
                                    width: '25%'
                                },
                            ]
                        );
                    }
                });
            }

            var win = Ext.create('Ext.window.Window', {
                title: 'Activación y Registro Elemento',
                width: 630,
                modal: true,
                items: [formPanel]
            }).show();

            storeModelosCpe.load({ });
            storeModelosTarjeta.load({ });

        }//cierre response
    });
}

/**
 * Funcion que muestra la ventana con la informacion tecnica de un producto NetlifeCam Trasladado.
 *
 * @author Daniel Reyes <djreyes@telconet.ec>
 * @version 1.0 25-10-2020
 *
 */
function confirmarActElemTrasladado(data, gridIndex) {
    
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosTrasladoNetlifeCam,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;
            var datCam = datos[0];
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
                            name: 'producto',
                            id: 'txtProducto',
                            fieldLabel: 'Producto',
                            value: data.descripcionProducto,
                            readOnly: true,
                            width: '50%'
                        },
                        {
                            xtype: 'textfield',
                            name: 'descripcionFactura',
                            fieldLabel: 'Descripción Factura',
                            value: data.descripcionPresentaFactura,
                            readOnly: true,
                            width: '50%',
                        }]
                    }]
                }, //cierre del fieldset
                {
                    xtype: 'fieldset',
                    title: 'Información del elemento',
                    defaultType: 'textfield',
                    defaults: {
                        width: 585
                    },
                    items: [
                    {
                        xtype: 'container',
                        id: 'fs_infoElementosCliente',
                        layout: {
                            type: 'table',
                            columns: 2,
                            align: 'stretch'
                        },
                        items: [
                        {
                            xtype: 'textfield',
                            id: 'serieCpe',
                            allowBlank: false,
                            name: 'serieCpe',
                            readOnly: true,
                            fieldLabel: 'Serie',
                            displayField: "",
                            value: datCam.strSerieElemento,
                            width: '25%'
                        },
                        {
                            xtype: 'textfield',
                            id: 'modeloCpe',
                            allowBlank: false,
                            name: 'modeloCpe',
                            readOnly: true,
                            fieldLabel: 'Modelo',
                            displayField: "",
                            value: datCam.strModeloElemento,
                            width: '25%'
                        },
                        {
                            xtype: 'textfield',
                            id: 'macCpe',
                            readOnly: true,
                            fieldCls: 'details-disabled',
                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                            name: 'macCpe',
                            fieldLabel: 'Mac',
                            displayField: "",
                            value: datCam.strMacElemento,
                            width: '25%'
                        },
                        {
                            xtype: 'textfield',
                            id: 'descripcionCpe',
                            readOnly: true,
                            name: 'descripcionCpe',
                            fieldLabel: 'Descripción',
                            displayField: "",
                            value: datCam.strDescriElemento,
                            fieldCls: 'details-disabled',
                            fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                            width: '25%'
                        },
                        {
                            xtype: 'hidden',
                            id: 'validacionMacOnt',
                            name: 'validacionMacOnt',
                            value: "",
                            hidden:true,
                            hideLabel:true,
                            fieldStyle: `display:none;`
                        }]
                    }]
                },//cierre informacion de los elementos del cliente
                {
                    xtype: 'fieldset',
                    hidden: false,
                    title: 'Información de la tarjeta',
                    defaultType: 'textfield',
                    defaults: {
                        width: 585
                    },
                    items: [
                    {
                        xtype: 'container',
                        id: 'fs_infoTarjeta',
                        layout: {
                            type: 'table',
                            columns: 2,
                            align: 'stretch'
                        },
                        items: [
                        {
                            xtype: 'textfield',
                            id: 'serieTarjeta',
                            allowBlank: true,
                            name: 'serieTarjeta',
                            fieldLabel: 'Serie',
                            readOnly: true,
                            displayField: "",
                            value: datCam.strSerieTarjeta,
                            width: '25%'
                        },
                        {
                            xtype: 'textfield',
                            id: 'modeloTarjeta',
                            allowBlank: false,
                            name: 'modeloTarjeta',
                            fieldLabel: 'Modelo',
                            readOnly: true,
                            displayField: "",
                            value: datCam.strModeloTarjeta,
                            width: '25%'
                        }]
                    }]
                }],
                buttons: [
                {
                    text: 'Activar',
                    formBind: true,
                    handler: function()
                    {
                        Ext.Msg.confirm('Confirmación', '¿Esta seguro que desea activar este servicio?', function(id)
                        {
                            if (id === 'yes')
                            {
                                var modeloCpe       = Ext.getCmp('modeloCpe').getValue();
                                var serieCpe        = Ext.getCmp('serieCpe').getValue();
                                var modeloTarjeta    = Ext.getCmp('modeloTarjeta').getValue();
                                var serieTarjeta     = Ext.getCmp('serieTarjeta').getValue();

                                Ext.get(formPanel.getId()).mask('Activando servicio...');
                                Ext.Ajax.request({
                                    url: trasladarNetlifeCam,
                                    method: 'post',
                                    timeout: 400000,
                                    params: {
                                        idServicio: data.idServicio,
                                        strNombreTecnico: data.descripcionProducto,
                                        serieTarjeta:serieTarjeta,
                                        modeloTarjeta:modeloTarjeta,
                                        serieCamara: serieCpe,
                                        modeloCamara: modeloCpe,
                                        idServicioInternet: data.intIdServicioInternet
                                    },
                                    success: function(response){
                                        console.log(response);
                                        Ext.get(formPanel.getId()).unmask();
                                        var objData = Ext.JSON.decode(response.responseText);
                                        var strStatus = objData.status;
                                        var strMensaje = objData.mensaje;
                                        if(strStatus == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Activo el servicio exitosamente', function(btn){
                                                if(btn=='ok'){
                                                    win.destroy();
                                                    store.load();
                                                }
                                            });
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje ',strMensaje );
                                        }
                                    },
                                    failure: function (result) {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                            }
                        });
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function(){
                        win.destroy();
                    }
                }]
            });
        
            var win = Ext.create('Ext.window.Window', {
                title: 'Activación y Registro Elemento',
                width: 630,
                modal: true,
                items: [formPanel]
            }).show();
        }        
    });
}

/**
 * Funcion que muestra la ventana con la informacion tecnica de un servicio en simultaneo sin flujo.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 03-06-2019
 *
 */
function verInformacionTecnicaSimultanea(data) {

    objFieldStyle = {
        'backgroundColor': '#F0F2F2',
        'backgrodunImage': 'none'
    };
    
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDataCaracteristicas,
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
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        value: data.nombreProducto,
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
                                    }

                                ]//cierre del container table
                            }
                        ]//cierre del fieldset
                    },
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
                                id: 'fs_infoElementosCliente',
                                layout: {
                                    type: 'table',
                                    columns: 2,
                                    align: 'stretch'
                                },
                                items: []
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

            if (objData)
            {
                objData.forEach((el, index) => {
                    if (el['valor'])
                    {
                        Ext.getCmp('fs_infoElementosCliente').insert(index,
                            [
                                {
                                    xtype: 'textfield',
                                    id: el['descripcionCaracteristica'].toLowerCase(),
                                    name: el['descripcionCaracteristica'].toLowerCase(),
                                    fieldLabel: titleCase(el['label']),
                                    displayField: "",
                                    value: el['valor'],
                                    width: '25%',
                                    readOnly: true,
                                    fieldCls: 'details-disabled',
                                    fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`
                                }
                            ]
                        );
                    }

                });
            }

            var win = Ext.create('Ext.window.Window', {
                title: 'Información del Servicio',
                width: 585,
                modal: true,
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

function cancelarServicioOtros(data, idAccion){
    var permiteCancelLogica     = data.strPermiteCancelLogica;
    var sigueFlujoCancelacion   = "";
    var tituloAdicVentana       = "";
    var serie = consultarInformacion(data.idServicio, 'SERIE_EQUIPO_PTZ');


    if(permiteCancelLogica === "SI")
    {
        if(permiteRolCancelacionLogica)
        {
            sigueFlujoCancelacion   = "SI";
            tituloAdicVentana       = ' Lógicamente';
        }
        else
        {
            sigueFlujoCancelacion = "NO";
            Ext.Msg.alert(  'Mensaje', 
                            'Usted no tiene el perfil correspondiente para realizar la cancelación lógica. Por favor comuníquese con Sistemas!');
        }
    }
    else
    {
        sigueFlujoCancelacion = "SI";
    }

    if(sigueFlujoCancelacion === "SI")
    {
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
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [{

                        xtype: 'fieldset',
                        title: 'Motivo Cancelacion',
                        defaultType: 'textfield',
                        defaults: {
                            width: 250
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

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combo',
                                        id:'comboMotivosOtros',
                                        name: 'comboMotivosOtros',
                                        store: storeMotivos,
                                        fieldLabel: 'Motivo',
                                        displayField: 'nombreMotivo',
                                        valueField: 'idMotivo',
                                        queryMode: 'local'
                                    }
                                ]
                            }
                        ]
                }],
                buttons: [{
                    text: 'Ejecutar',
                    formBind: true,
                    handler: function(){
                        var motivo = Ext.getCmp('comboMotivosOtros').getValue();
                        var validacion = false;

                        if(motivo!=null){
                            validacion=true;
                        }

                        if(validacion){
                            Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar el cliente?', function(btn){
                                if(btn=='ok')
                                {
                                    Ext.get(formPanel.getId()).mask('Cancelando el Servicio...');
                                    Ext.Ajax.request({
                                        url: cancelarServicioOtrosBoton,
                                        method: 'post',
                                        timeout: 400000,
                                        params: { 
                                            idServicio: data.idServicio,
                                            esServicioRequeridoSafeCity: data.esServicioRequeridoSafeCity,
                                            idAccion:   idAccion,
                                            motivo:     motivo,
                                            productoId: data.productoId,
                                            login: data.login,
                                            serie: serie['valor']

                                        },
                                        success: function(response){
                                            Ext.get(formPanel.getId()).unmask();
                                            if(response.responseText == "OK"){
                                                Ext.Msg.alert('Mensaje','Se Cancelo el Servicio!', function(btn){
                                                    if(btn=='ok'){
                                                        store.load();
                                                        winOtros.destroy();
                                                    }
                                                });
                                            }
                                            else{

                                                Ext.Msg.alert('Mensaje', 'No se pudo cancelar el servicio! '+response.responseText);
                                            }

                                        },
                                        failure: function(result)
                                        {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.Msg.alert('Error ',result.statusText);
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
                        winOtros.destroy();
                    }
                }]
            });

            var winOtros = Ext.create('Ext.window.Window', {
                title: 'Cancelar Servicio'+tituloAdicVentana,
                modal: true,
                width: 290,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
    }
            
}

function cortarServicioOtros(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cortar el Servicio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cortando el Servicio...');
            Ext.Ajax.request({
                url: cortarServicioOtrosBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Corto el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje', 'No se pudo cortar el servicio! '+response.responseText);
                    }

                }

            });
        }
    });
}

function reconectarServicioOtros(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Reconectar el Servicio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Reconectando el Servicio...');
            Ext.Ajax.request({
                url: reconectarServicioOtrosBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Reconecto el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje', 'No se pudo reconectar el servicio! '+response.responseText);
                        store.load();
                    }

                }

            });
        }
    });
}

function cambiarEstado(data){
    Ext.Msg.alert('Mensaje','Esta seguro que desea <br> Cambiar el Estado al Cliente?', function(btn){
        if(btn=='ok'){
            Ext.get(gridServicios.getId()).mask('Cambiando el estado...');
            Ext.Ajax.request({
                url: cambiarEstadoCliente,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio
                },
                success: function(response){
                    Ext.get(gridServicios.getId()).unmask();
                    if(response.responseText == "OK"){
        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                        Ext.Msg.alert('Mensaje','Se el estado al Cliente', function(btn){
                            if(btn=='ok'){
                                store.load();
                                win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo cambiar el estado el cliente!' );
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

function reenviarInformacionOffice(data) {
    Ext.Msg.alert('Mensaje', 'Está seguro que desea reenviar<br> al cliente las credenciales del producto NetlifeCloud?', function (btn) {
        if (btn == 'ok') {
            Ext.get(gridServicios.getId()).mask('Enviando Información...');
            Ext.Ajax.request({
                url: reenviarInformacionClienteOffice,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio:     data.idServicio,
                    nombreProducto: data.nombreProducto,
                    productoId:     data.productoId
                },
                success: function (response) {
                    Ext.get(gridServicios.getId()).unmask();
                    var json = Ext.JSON.decode(response.responseText);
                    if (json.status == "OK") {
                        Ext.Msg.alert('Mensaje', json.mensaje);
                    } else {
                        Ext.Msg.alert('Error ', json.mensaje);
                    }
                },
                failure: function (result)
                {
                    Ext.get(gridServicios.getId()).unmask();
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}

function titleCase(str) {
    return str.toLowerCase().split(' ').map(function(word) {
        return (word.charAt(0).toUpperCase() + word.slice(1));
    }).join(' ');
}

function confirmarServicioPlan(data, idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Confirmar el Servicio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Confirmando el Servicio...');
            Ext.Ajax.request({
                url: confirmarServicioPlanBotton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idProducto: data.productoId,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();
                    var datos = Ext.JSON.decode(response.responseText);
                    
                    if(datos.srtResultado == "OK"){
                        
                        var mensajeFox = "";
                        Ext.Msg.alert('Mensaje','Se Confirmo el Servicio!' + mensajeFox, function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje', 'No se pudo confirmar el servicio!<br />'+datos.srtResultado);
                    }

                }

            });
        }
    });
}