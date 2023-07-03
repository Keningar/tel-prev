Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
        'Ext.window.MessageBox'
]);

Ext.define('CuadrillasList', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id_cuadrilla', type: 'int'},
        {name: 'nombre_cuadrilla', type: 'string'}
    ]
});

var itemsPerPage = 999999;
var store='';
var motivo_id='';
var relacion_sistema_id='';
var tipo_solicitud_id='';
var area_id='';
var login_id='';
var tipo_asignacion='';
var pto_sucursal='';
var idClienteSucursalSesion;
var arrayElementoSeleccionado = [];
var flagDeselectPorValidacion = false;
var strLblFacturableOVenta    = strAplicaFacturacionCambioModem == 'S' ? 'Facturable' : 'Venta';
var cuadrillaAsignada = "N";
var banderaEscogido = "";

Ext.onReady(function(){

    Ext.form.VTypes["valorVtypeVal"] =/(^\d{1,6}\.\d{1,2}$)|(^\d{1,6}$)/;		
    Ext.form.VTypes["valorVtype"]=function(v){
        return Ext.form.VTypes["valorVtypeVal"].test(v);
    }
    Ext.form.VTypes["valorVtypeText"]="Puede ingresar hasta 6 enteros y al menos 1 decimal o puede ingresar hasta 6 enteros sin decimales";
    Ext.form.VTypes["valorVtypeMask"]=/[\d\.]/;

    Ext.form.VTypes["porcentajeVtypeVal"] =/(^\d{1,3}\.\d{1,2}$)|(^\d{1,3}$)/;		
    Ext.form.VTypes["porcentajeVtype"]=function(v){
        return Ext.form.VTypes["porcentajeVtypeVal"].test(v);
    }
    Ext.form.VTypes["porcentajeVtypeText"]="Puede ingresar hasta 3 enteros y al menos 1 decimal o puede ingresar hasta 3 enteros sin decimales";
    Ext.form.VTypes["porcentajeVtypeMask"]=/[\d\.]/;
            
    function solicitarCambioElemento(){
        var param = '';
        if(sm.getSelection().length > 0)
        {
            var estado = 0;
            for(var i=0 ;  i < sm.getSelection().length ; ++i)
            {
                param = param + sm.getSelection()[i].data.idServicio;

                if (sm.getSelection()[i].data.idElemento != 0)
                    param = param + "@" + sm.getSelection()[i].data.idElemento;

                if(i < (sm.getSelection().length -1))
                {
                    param = param + '|';
                }
            }
            
            if(motivo_id)
            {
                if(TFObservacion.getValue()){
                    if(TipoValor.getValue().tipoDoc=='v'){
                        if(TFPrecio.getValue()>0 || strAplicaFacturacionCambioModem == 'S')
                            ejecutaEnvioSolicitud(param);   
                        else
                            Ext.Msg.alert('Alerta ','Por favor ingresar un valor mayor a 0');
                    }
                    else{
                        ejecutaEnvioSolicitud(param);
                    }
                }
                else{
                    Ext.Msg.alert('Alerta ','Por favor ingresar la observacion');
                }    

            }
            else
            {
                alert('Seleccione el Motivo de la solicitud');
            }
        }
        else
        {
          alert('Seleccione por lo menos un registro de la lista');
        }
    }
    
    /*
     * Funcion utilizada para realizar la regularización de información de servicios radio TN
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 26-08-2016 
     * @since 1.0
     */
    function cambioEquipoHwMigrado(intIdServicio, param)
    {
        Ext.get("grid").mask('Consultando Datos...');
        Ext.get("opcionesPanel").mask('Consultando Datos...');
        Ext.get("observacionPanel").mask('Consultando Datos...');

        Ext.Ajax.request({
            url: url_obtene_informacion_servicio,
            method: 'post',
            timeout: 400000,
            params: {
                idServicio: intIdServicio
            },
            success: function(response) {
                Ext.get("grid").unmask();
                Ext.get("opcionesPanel").unmask();
                Ext.get("observacionPanel").unmask();
                var json = Ext.JSON.decode(response.responseText);
                if (json)
                {
                    if (json.strStatus == "OK")
                    {
                        if (json.strSolicitudMigracionFinalizada == "SI")
                        {
                            var elementoWifi = json.intElementoWifi;
                            var formPanel = Ext.create('Ext.form.Panel', {
                                bodyPadding: 2,
                                waitMsgTarget: true,
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 85,
                                    msgTarget: 'side'
                                },
                                items: [
                                    //informacion del cliente
                                    {
                                        xtype: 'fieldset',
                                        title: 'Cpe wifi hw existente',
                                        defaultType: 'textfield',
                                        defaults: {
                                            width: 350
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
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'nombreElemento',
                                                        fieldLabel: 'Nombre Elemento',
                                                        displayField: json.strNombreCpe,
                                                        value: json.strNombreCpe,
                                                        readOnly: true,
                                                        width: 300
                                                    },
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'modeloElemento',
                                                        fieldLabel: 'Modelo Elemento',
                                                        displayField: json.strModeloCpe,
                                                        value: json.strModeloCpe,
                                                        readOnly: true,
                                                        width: 300
                                                    },
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'marcaElemento',
                                                        fieldLabel: 'Marca Elemento',
                                                        displayField: json.strMarcaCpe,
                                                        value: json.strMarcaCpe,
                                                        readOnly: true,
                                                        width: 300
                                                    },
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'combobox',
                                                        name: 'accionHw',
                                                        fieldLabel: 'Accion',
                                                        id: 'equipoCpe',
                                                        value: 'MANTENER EQUIPO',
                                                        editable: false,
                                                        store:
                                                                [
                                                                    ['CAMBIAR EQUIPO', 'CAMBIAR EQUIPO'],
                                                                    ['MANTENER EQUIPO', 'MANTENER EQUIPO']
                                                                ],
                                                        width: 300
                                                    }
                                                ]
                                            }
                                        ]
                                    }, //cierre de la informacion del cliente
                                    {
                                        xtype: 'fieldset',
                                        title: 'Equipo wifi adicional',
                                        id: 'wifiExistente',
                                        defaultType: 'textfield',
                                        defaults: {
                                            width: 350
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
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'nombreElementoAdi',
                                                        fieldLabel: 'Nombre Elemento',
                                                        displayField: json.strNombreWifi,
                                                        value: json.strNombreWifi,
                                                        readOnly: true,
                                                        width: 300
                                                    },
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'modeloElementoAdi',
                                                        fieldLabel: 'Modelo Elemento',
                                                        displayField: json.strModeloWifi,
                                                        value: json.strModeloWifi,
                                                        readOnly: true,
                                                        width: 300
                                                    },
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'textfield',
                                                        name: 'marcaElementoAdi',
                                                        fieldLabel: 'Marca Elemento',
                                                        displayField: json.strMarcaWifi,
                                                        value: json.strMarcaWifi,
                                                        readOnly: true,
                                                        width: 300
                                                    },
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'combobox',
                                                        name: 'accionHw',
                                                        fieldLabel: 'Accion',
                                                        id: 'equipoWifi',
                                                        value: 'MANTENER EQUIPO',
                                                        editable: false,
                                                        store:
                                                                [
                                                                    ['CAMBIAR EQUIPO', 'CAMBIAR EQUIPO'],
                                                                    ['MANTENER EQUIPO', 'MANTENER EQUIPO']
                                                                ],
                                                        width: 300
                                                    }
                                                ]
                                            }
                                        ]
                                    }, //cierre de la informacion de equipo adicional wifi
                                    {
                                        xtype: 'fieldset',
                                        title: 'Equipo wifi adicional',
                                        id: 'wifiNoExistente',
                                        defaultType: 'textfield',
                                        defaults: {
                                            width: 350
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
                                                    {width: '5%', border: false},
                                                    {
                                                        xtype: 'combobox',
                                                        name: 'agregarWifi',
                                                        fieldLabel: 'Agregar Wifi',
                                                        id: 'agregarWifi',
                                                        value: 'NO',
                                                        editable: false,
                                                        store:
                                                                [
                                                                    ['SI', 'SI'],
                                                                    ['NO', 'NO']
                                                                ],
                                                        width: 300
                                                    }
                                                ]
                                            }
                                        ]
                                    } //cierre de la informacion de equipo adicional wifi
                                ],
                                buttons: [{
                                        text: 'Solicitar',
                                        handler: function() {
                                            var equipoCpe = "";
                                            var equipoWifi = "";
                                            var agregarWifi = "";
                                            var validaciones = false;
                                            if (json.strEquipoWifiAdicional == "SI")
                                            {
                                                if (Ext.getCmp('equipoCpe').getValue()  == 'MANTENER EQUIPO' &&
                                                    Ext.getCmp('equipoWifi').getValue() == 'MANTENER EQUIPO')
                                                {
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: "Debe de realiar el cambio de algún equipo",
                                                        buttons: Ext.MessageBox.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });
                                                }
                                                else
                                                {
                                                    var equipoCpe    = Ext.getCmp('equipoCpe').getValue();
                                                    var equipoWifi   = Ext.getCmp('equipoWifi').getValue();
                                                    var validaciones = true;
                                                }
                                                
                                            }
                                            else
                                            {
                                                if (Ext.getCmp('equipoCpe').getValue()   == 'MANTENER EQUIPO' &&
                                                    Ext.getCmp('agregarWifi').getValue() == 'NO')
                                                {
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: "Debe de realizar el cambio de algún equipo",
                                                        buttons: Ext.MessageBox.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });
                                                }
                                                else
                                                {
                                                    var equipoCpe    = Ext.getCmp('equipoCpe').getValue();
                                                    var agregarWifi  = Ext.getCmp('agregarWifi').getValue();
                                                    var validaciones = true;
                                                }
                                            }
                                            if (validaciones == true)
                                            {
                                                Ext.get(formPanel.getId()).mask('Guardando datos!');
                                                Ext.Ajax.request({
                                                    url: url_solicitar_cancelacion_ajax,
                                                    method: 'post',
                                                    timeout: 400000,
                                                    params: { param : param, 
                                                              motivoId:motivo_id, 
                                                              rs: relacion_sistema_id, 
                                                              ts:tipo_solicitud_id, 
                                                              fecha:DTFechaCancelacion.getValue(), 
                                                              obs:TFObservacion.getValue(), 
                                                              td:TipoValor.getValue().tipoDoc, 
                                                              valor:TFPrecio.getValue(),
                                                              equipoCpe: equipoCpe,
                                                              equipoWifi: equipoWifi,
                                                              agregarWifi: agregarWifi,
                                                              elementoWifi: elementoWifi
                                                            },
                                                    success: function(response){
                                                        Ext.get(formPanel.getId()).unmask();
                                                        var text = response.responseText;
                                                        Ext.Msg.alert('Mensaje ',response.responseText);
                                                        win.destroy();
                                                        store.load();
                                                    },
                                                    failure: function(response)
                                                    {
                                                        Ext.get(formPanel.getId()).unmask();
                                                        Ext.Msg.alert('Error ','Error: ' + response.responseText);
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
                                title: 'Información del Cambio de equipo',
                                modal: true,
                                width: 350,
                                closable: true,
                                layout: 'fit',
                                items: [formPanel]
                            }).show();

                            if (json.strEquipoWifiAdicional == "SI")
                            {
                                if (json.strNombreWifi.includes('SmartWifi') ||
                                    json.strNombreWifi.includes('ApWifi') ||
                                    json.strNombreWifi.includes('ExtenderDualBand'))
                                {
                                    Ext.getCmp('wifiExistente').setVisible(false);
                                }
                                else
                                {
                                    Ext.getCmp('wifiNoExistente').setVisible(false);
                                }
                                
                            }
                            else
                            {
                                Ext.getCmp('wifiExistente').setVisible(false);
                            }
                        }
                        else
                        {
                            Ext.get("grid").mask('Guardando datos!');
                            Ext.get("opcionesPanel").mask('Guardando datos!');
                            Ext.get("observacionPanel").mask('Guardando datos!');
                            Ext.Ajax.request({
                                url: url_solicitar_cancelacion_ajax,
                                method: 'post',
                                timeout: 400000,
                                params: { param : param, 
                                          motivoId:motivo_id, 
                                          rs: relacion_sistema_id, 
                                          ts:tipo_solicitud_id, 
                                          fecha:DTFechaCancelacion.getValue(), 
                                          obs:TFObservacion.getValue(), 
                                          td:TipoValor.getValue().tipoDoc, 
                                          valor:TFPrecio.getValue()},
                                success: function(response){
                                    Ext.get("grid").unmask();
                                    Ext.get("opcionesPanel").unmask();
                                    Ext.get("observacionPanel").unmask();
                                    Ext.Msg.alert('Mensaje ',response.responseText);
                                    store.load();
                                },
                                failure: function(response)
                                {
                                    Ext.get("grid").unmask();
                                    Ext.get("opcionesPanel").unmask();
                                    Ext.get("observacionPanel").unmask();
                                    Ext.Msg.alert('Error ','Error: ' + response.responseText);
                                }
                            });
                        }
                    }
                    else
                    {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: json.strMensaje,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                }
            },
            failure: function(result)
            {
                Ext.get("grid").unmask();
                Ext.get("opcionesPanel").unmask();
                Ext.get("observacionPanel").unmask();
                Ext.Msg.alert('Error ','Se presentó un error al ejecutar la transacción, favor notificar a sistemas.');
            }
        });
    }
    
    function ejecutaEnvioSolicitud(param){
        var intIdServicio = null;
        if(strEsPuntoGpon == "S"){
            storeCuadrillas = Ext.create('Ext.data.Store', {
                model: 'CuadrillasList',
                autoLoad: false,
                proxy: {
                    type: 'ajax',
                    url : url_asignar_cuadrilla,
                    extraParams : { strEsPuntoGpon: strEsPuntoGpon},
                    reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                    },
                    afterRequest: function(req, res) {
                        const responseContent = JSON.parse(req.operation.response.responseText)
                        if (responseContent.msg !== '')
                        {
                            Ext.Msg.alert('Mensaje ', responseContent.msg);
                        }
                    }
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
                    columns: 1
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },
                items: [
                    {
                        colspan: 1,
                        xtype: 'panel',
                        items: [
                            {
                                mode:           'local',
                                xtype:          'combobox',
                                id:             'cmb_cuadrilla',
                                name:           'cmb_cuadrilla',
                                store:          storeCuadrillas,
                                fieldLabel:     'Cuadrilla',
                                displayField:   'nombre_cuadrilla',
                                valueField:     'id_cuadrilla',
                                width:          350,
                                listeners: {
                                    select: function(combo) {
                                        seteaLiderCuadrilla(combo.getId(), combo.getValue());
                                    }
                                }
                            },
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'PersonaEmpresaRol:',
                                id: 'idPersonaEmpresaRol',
                                name: 'idPersonaEmpresaRol',
                                hidden: true,
                                value: ""
                            },
                            {
                                xtype: 'displayfield',
                                fieldLabel: 'Lider Cuadrilla',
                                id: 'nombreLiderCuadrilla',
                                name: 'nombreLiderCuadrilla',
                                value: '---'
                            }
                        ]
                    }
                ],
                buttons: 
                [{
                    text: 'Guardar',
                    formBind: true,
                    handler: function()
                    {
                        //datos
                        var validacion = true;
                        var idPersonaEmpresaRol = Ext.getCmp('idPersonaEmpresaRol').getValue();
                        var idCuadrilla         = Ext.getCmp('cmb_cuadrilla').getValue();
                        if( idPersonaEmpresaRol == "" || cuadrillaAsignada=="N" || idCuadrilla=="" )
                        {
                            validacion = false;
                        }
                        if (validacion)
                        {
                            Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');
                            Ext.Ajax.request({
                                url: url_solicitar_cancelacion_ajax,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    param : param, 
                                    motivoId:motivo_id, 
                                    rs: relacion_sistema_id, 
                                    ts:tipo_solicitud_id, 
                                    fecha:DTFechaCancelacion.getValue(), 
                                    obs:TFObservacion.getValue(), 
                                    td:TipoValor.getValue().tipoDoc, 
                                    valor:TFPrecio.getValue(),
                                    strEsPuntoGpon: strEsPuntoGpon,
                                    strPersonaEmpresaRolId: idPersonaEmpresaRol,
                                    intIdCuadrilla: idCuadrilla
                                },
                                success: function(response){
                                    winAsignar.destroy();
                                    Ext.Msg.alert('Mensaje ',response.responseText);
                                    store.load();
                                },
                                failure: function(response)
                                {
                                    winAsignar.destroy();
                                    Ext.Msg.alert('Error ','Error: ' + response.responseText);
                                }
                            });
                        }
                        else
                        {
                            Ext.Msg.alert('Validacion ','Debe llenar todos los campos para realizar el cambio de equipo.');
                        }
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function()
                    {
                        winAsignar.destroy();
                    }
                }]
            });
            var winAsignar = Ext.create('Ext.window.Window', {
                title: 'Cambio Equipo Servicios SafeCity GPON',
                modal: true,
                width: 450,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
        }
        else
        {
            Ext.Msg.confirm('Alerta','Se solicitara cambio de equipo para los registros seleccionados. Desea continuar?', function(btn){
                if(btn=='yes'){
                    //alert(motivo_id);
                    if(prefijoEmpresa == 'MD')
                    {
                        for(var i=0 ;  i < sm.getSelection().length ; ++i)
                        {
                            intIdServicio = sm.getSelection()[i].data.idServicio;
                        }
                        cambioEquipoHwMigrado(intIdServicio, param);
                    }
                    else
                    {
                        Ext.Ajax.request({
                            url: url_solicitar_cancelacion_ajax,
                            method: 'post',
                            timeout: 400000,
                            params: { param : param, 
                                      motivoId:motivo_id, 
                                      rs: relacion_sistema_id, 
                                      ts:tipo_solicitud_id, 
                                      fecha:DTFechaCancelacion.getValue(), 
                                      obs:TFObservacion.getValue(), 
                                      td:TipoValor.getValue().tipoDoc, 
                                      valor:TFPrecio.getValue()},
                            success: function(response){
                                var text = response.responseText;
                                Ext.Msg.alert('Mensaje ',response.responseText);
                                store.load();
                            },
                            failure: function(response)
                            {
                                Ext.Msg.alert('Error ','Error: ' + response.responseText);
                            }
                        });
                    }
                }
            });
        }
    }

    //CREAMOS DATA STORE PARA MOTIVO
    Ext.define('modelMotivo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idMotivo', type: 'string'},
            {name: 'descripcion',  type: 'string'},
            {name: 'idRelacionSistema',  type: 'string'},
            {name: 'idTipoSolicitud',  type: 'string'}                    
        ]
    });	
    
    var ajaxProxyCancelacion = new Ext.data.proxy.Ajax({
        type: 'ajax',
        url : url_lista_motivos,
        reader: {
            type: 'json',
            root: 'motivos'
        }
    });
    
    motivo_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelMotivo",
        proxy: ajaxProxyCancelacion
    });	
    
    var motivo_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: motivo_store,
        labelAlign : 'left',
        id:'idMotivo',
        name: 'idMotivo',
        valueField:'idMotivo',
        displayField:'descripcion',
        fieldLabel: 'Motivo',
        width: 340,
        mode: 'local',	
					
        listeners: {
            select:
            function(e) {
                //alert(Ext.getCmp('idestado').getValue());
                motivo_id = Ext.getCmp('idMotivo').getValue();
                relacion_sistema_id=e.displayTplData[0].idRelacionSistema;
                tipo_solicitud_id=e.displayTplData[0].idTipoSolicitud;
            },		
        }
    });
    //fin de combo motivo

    //creamos radio button
    TipoValor = new Ext.form.RadioGroup(
    {
        xtype      : 'fieldcontainer',
        defaultType: 'radiofield',
         width: '170px',
        defaults: {
            flex: 1
        },
        layout: 'hbox',
        items: [
            {
                boxLabel  : 'Cortesia',
                name      : 'tipoDoc',
                inputValue: 'c',
                id        : 'radio_venta',
                listeners:{                    
                    change:
                    function(radio1, newValue, oldValue, eOpts) {
                        if (radio1.checked){
                            TFPrecio.setVisible(false);
                        }
                    }
                }
            }, 
            {
                boxLabel  : strLblFacturableOVenta,
                name      : 'tipoDoc',
                inputValue: 'v',
                id        : 'radio_cortesia',
                checked   : true,
                listeners:{
                    change:
                    function(radio2, newValue, oldValue, eOpts) {
                        if (radio2.checked && strAplicaFacturacionCambioModem != 'S'){
                            TFPrecio.setVisible(true);
                        }
                    }
                }
            }
            ]
    });
    
    TFPrecio = new Ext.form.TextField({
        id: 'valorPrecio',
        name: 'valorPrecio',
        labelAlign:'right',
        fieldLabel: 'Valor',
        //labelWidth: '50px',
        xtype: 'textfield',
        width: '170px',
        vtype: 'valorVtype'
    });
    
    DTFechaCancelacion = new Ext.form.DateField({
        id: 'fechaCancelacion',
        fieldLabel: 'Fecha Suspension',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:250
        //anchor : '65%',
        //layout: 'anchor'
    });
           
    TFObservacion = new Ext.form.field.TextArea({
        xtype     : 'textareafield',
        //grow      : true,
        name      : 'observacion',
        fieldLabel: 'Observacion',
        //width    : '400px',
        cols     : 80,
        rows     : 2,
        maxLength: 200
    }); 
            
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idServicio'              , type: 'int'},
            {name:'idPunto'                 , type: 'int'},
            {name:'idElemento'              , type: 'int'},
            {name:'tipo'                    , type: 'string'},
            {name:'nombreModeloElemento'    , type: 'string'},
            {name:'nombreElemento'          , type: 'string'},
            {name:'nombreTipoElemento'      , type: 'string'},
            {name:'nombreProducto'          , type: 'string'},
            {name:'login'                   , type: 'string'},
            {name:'loginAux'                , type: 'string'},
            {name:'estado'                  , type: 'string'},
            {name:'yaFueSolicitada'         , type: 'string'},
            {name:'marcaEquipo'             , type: 'string'}
        ]
    }); 
    
    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_grid,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams:{
                fechaDesde:'',
                fechaHasta:'', 
                estado:'',
                nombre:''
            },
            simpleSortMode: true
        }
    });

    var sm = new Ext.selection.CheckboxModel({
        listeners:{
            select: function( selectionModel, record, index, eOpts )
            {
                idElemento                = record.data.idElemento;
                flagDeselectPorValidacion = false;
                
                if(arrayElementoSeleccionado.indexOf(idElemento)=== -1 || arrayElementoSeleccionado.lenght===0)
                {
                    arrayElementoSeleccionado.push(idElemento);                    
                }
                else
                {
                    flagDeselectPorValidacion = true;
                    Ext.Msg.alert('Alerta','Ya fue solicitado cambio de '+record.data.nombreTipoElemento+' para un Servicio conectado \n\
                                  al mismo Equipo, no es necesario crear varias Solicitudes para el mismo equipo');
                    sm.deselect(index);                               
                }
                                                
                if(record.data.yaFueSolicitada == 'S'){
                    if (record.data.marcaEquipo == '')
                    {
                        sm.deselect(index);
                        Ext.Msg.alert('Alerta','Ya fue solicitado cambio de equipo para el servicio: '+record.data.nombreProducto);
                    }
                    else
                    {
                        sm.deselect(index);
                        Ext.Msg.alert('Alerta','Elemento wifi Adicional, gestionar al solicitar el cambio del CPE ONT HW');
                    }
                }
            },
            deselect:function(selectionModel, record, index, eOpts)
            {                
                if(!flagDeselectPorValidacion)
                {                   
                    arrayElementoSeleccionado.splice(arrayElementoSeleccionado.indexOf(record.data.idElemento),1);                
                }
                else
                {
                    flagDeselectPorValidacion = false;
                }
                
                               
            }
        }
    });

    //Si aplica al proceso de facturación, se oculta la caja de texto porque este valor debe ser cargado de la plantilla de equipos.
    if(strAplicaFacturacionCambioModem != 'S'){
        TFPrecio.setVisible(true);
    }
    else
    {
        TFPrecio.setVisible(false);
    }
    if(prefijoEmpresa == 'TN'){
        TipoValor.setValue({ tipoDoc : 'c' });
        TipoValor.setVisible(false);
        TFPrecio.setVisible(false);
    }
    
    //opciones y motivo
    var opcionesPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 0,
        id: 'opcionesPanel',
        border:true,
        buttonAlign: 'center',
        bodyStyle: {
                    background: '#fff'
        },                     
        defaults: {
            bodyStyle: 'padding:10px'
        },
        //collapsible : true,
        //collapsed: true,
        width: 900,
        title: 'Opciones',
        items: [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha,
                    TipoValor,
                    TFPrecio,                                        
                    { 
                        xtype: 'tbfill' 
                    },
                    motivo_cmb,
                    {
                        iconCls: 'icon_solicitud',
                        text: 'solicitar',
                        disabled: false,
                        itemId: 'delete',
                        scope: this,
                        handler: function(){ 
                            solicitarCambioElemento();
                        }
                    }

                ]
            }
        ],	
        renderTo: 'filtro_servicios'
    });
    
    //observacion
    var observacionPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        id: 'observacionPanel',
        border:true,
        //buttonAlign: 'center',
        bodyStyle: {
            background: '#fff'
        },                     
        defaults: {
            bodyStyle: 'padding:10px'
        },
        //collapsible : true,
        //collapsed: true,
        width: 900,
        title: '',
        items: [
            TFObservacion
        ],	
        renderTo: 'panel_observacion'
    });
    
    //grid de elementos
    var listView = Ext.create('Ext.grid.Panel', {
        width:900,
        height:350,
        collapsible:false,
        title: 'Elementos del Servicios',
        id: 'grid',
        selModel: sm,                    
        renderTo: Ext.get('lista_servicios'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando servicios {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),	
        store: store,
        multiSelect: false,
        viewConfig: {
            getRowClass: function(record, index) {
                var c = record.get('yaFueSolicitada');
                //console.log(c);
                if (c == 'S') {
                    return 'grisTextGrid';
                } else{
                    return 'blackTextGrid';
                }
            },
            emptyText: 'No hay datos para mostrar'
        } ,
        columns: [new Ext.grid.RowNumberer(),  
        {
            text: 'Elemento',
            width: 115,
            dataIndex: 'nombreElemento'
        },
        {
            text: 'Modelo',
            width: 115,
            dataIndex: 'nombreModeloElemento'
        },
        {
            text: 'Tipo',
            width: 115,
            dataIndex: 'nombreTipoElemento'
        },
        {
            text: 'Producto',
            width: 130,
            dataIndex: 'nombreProducto'
        },
        {
            text: 'Login',
            width: 130,
            dataIndex: 'login'
        },
        {
            text: 'LoginAux',
            width: 130,
            dataIndex: 'loginAux'
        },
        {
            text: 'Estado',
            dataIndex: 'estado',
            align: 'right',
            flex: 40
        }

        ]
    });

    function seteaLiderCuadrilla(id, cuadrilla)
    {
        var connAsignarResponsable = new Ext.data.Connection({
            listeners: {
                'beforerequest': {
                    fn: function(con, opt) {
                        Ext.MessageBox.show({
                            msg: 'Consultando el lider, Por favor espere!!',
                            progressText: 'Saving...',
                            width: 300,
                            wait: true,
                            waitConfig: {interval: 200}
                        });
                    },
                    scope: this
                },
                'requestcomplete': {
                    fn: function(con, res, opt) {
                        Ext.MessageBox.hide();
                    },
                    scope: this
                },
                'requestexception': {
                    fn: function(con, res, opt) {
                        Ext.MessageBox.hide();
                    },
                    scope: this
                }
            }
        });
        connAsignarResponsable.request({
            url: url_asignar_responsable,
            method: 'post',
            params:
                {
                    cuadrillaId: cuadrilla
                },
            success: function(response) {
                var text = Ext.decode(response.responseText);
                if (text.existeTablet == "S"){
                    cuadrillaAsignada = "S";
                    Ext.getCmp('idPersonaEmpresaRol').setValue(text.idPersonaEmpresaRol);
                    Ext.getCmp('nombreLiderCuadrilla').setValue(text.nombres);
                }else{
                    var alerta = Ext.Msg.alert("Alerta", "La cuadrilla " + text.nombreCuadrilla +
                            " no posee tablet asignada. Realice la asignación de<br>tablet correspondiente o seleccione otra cuadrilla.");
                    Ext.defer(function() {
                        alerta.toFront();
                    }, 50);
                    cuadrillaAsignada = "N";
                    Ext.getCmp('cmb_cuadrilla').setValue("");
                    Ext.getCmp('idPersonaEmpresaRol').setValue("");
                }
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
