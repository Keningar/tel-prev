

function cancelarServicioProdAdicional(arrayParametros){
    
    var data                     = arrayParametros["data"];
    var subtotal                 = arrayParametros["subtotal"];
    var equipos                  = arrayParametros["equipos"];
    var instalacion              = arrayParametros["instalacion"];
    var subtotalnc               = arrayParametros["subtotalnc"];
    var caracteristicas          = arrayParametros["caracteristicas"];
    var strFacturaCancelacion    = arrayParametros["strFacturaCancelacion"];
    var motivoCancelacion        = arrayParametros["motivoCancelacion"];
    var observacion              = arrayParametros["observacion"];
    var idAccion                 = arrayParametros["accion"];
    var subtotalNDI              = arrayParametros["subtotalNDI"];
    var activarActaCancelacion        = arrayParametros["activarActaCancelacion"];
    var codigoPlantillaCancelacion    = arrayParametros["codigoPlantillaCancelacion"];
    var equiposSeleccionados        = arrayParametros["equiposSeleccionados"];
    var arrayGeneralDescuentos   = arrayParametros["arrayGeneralDescuentos"];
    var arrayGeneralProdFacturar = arrayParametros["arrayGeneralProdFacturar"];
    var strCreaNC                = arrayParametros["strCreaNC"];
 
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
            var strName         = 'producto';
            var strFieldLabel   = 'Producto Adicional';
            var strDisplayField = data.descripcionProducto;
            var strValue        = data.descripcionProducto;
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
                title: 'Cancelar Servicio',
                defaultType: 'textfield',
                defaults: {
                    width: 520
                },
                items: [

                    //informacion del cliente
                    {
                        xtype: 'fieldset',
                        title: 'Informacion Cliente',
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
                        title: 'Informacion Servicio',
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
                                    {//producto
                                        xtype: 'textfield',
                                        name:         strName,
                                        fieldLabel:   strFieldLabel,
                                        displayField: strDisplayField,
                                        value:        strValue,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {//servicio
                                        xtype: 'textfield',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                ]
                            }

                        ]
                    },//cierre de la informacion servicio/producto

                    //motivo de cancelacion
                    {
                        xtype: 'fieldset',
                        title: 'Motivo Cancelacion',
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
                                ]
                            }

                        ]
                    },//cierre del motivo de cancelacion
                    //Observacion Cancelacion
                    {
                        xtype: 'fieldset',
                        title: 'Observacion Cancelacion',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500
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
                                        xtype: 'textareafield',
                                        id:'observacionCancelacion',
                                        name: 'observacionCancelacion',
                                        fieldLabel: 'Observacion Cancelacion',
                                        width: '90%'
                                        
                                    }

                                    //---------------------------------------------
                                ]
                            }

                        ],
                        listeners:{
                            afterRender: function() {
                                    
                                    if (activarActaCancelacion == 'N')
                                        this.hide();
                                }
                            }
                    }//cierre del Observacion Cancelacion

                ]
            }],
            buttons: [{
                text: 'Ejecutar',
                formBind: true,
                handler: function(){
                                     
                    var motivo = Ext.getCmp('comboMotivos').getValue();
                    var observacionCancelacion = "";
                    var motivoCancelacionText = "";
                    if(activarActaCancelacion=='S'){
                        observacionCancelacion = Ext.getCmp('observacionCancelacion').getValue();
                        motivoCancelacionText = Ext.getCmp('comboMotivos').getRawValue();
                    }else{
                        motivoCancelacionText = Ext.getCmp('comboMotivos').getRawValue();
                    }
                    var validacion = false;
                    
                     if(motivo!=null && !isNaN(motivo)){
                        validacion=true;
                    }
                    
                    if(validacion){
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar el Producto?', function(btn){
                            if(btn=='ok')
                            {
                                win.destroy();                                
                                if(activarActaCancelacion=='S')
                                {                                     
                                    Ext.get(gridServicios.getId()).mask('Generando acta de cancelacion de servicio');
                                    Ext.Ajax.request({
                                        url: solicitudCancelacionClienteBoton,
                                        method: 'post',
                                        timeout: 300000, 
                                        params: { 
                                            idServicio: data.idServicio,
                                            idProducto: data.productoId,
                                            perfil: data.perfilDslam,
                                            login: data.login,
                                            capacidad1: data.capacidadUno,
                                            capacidad2: data.capacidadDos,
                                            strMotivoCliente: motivo,
                                            idAccion: idAccion,
                                            floatSubtotal:subtotal,
                                            floatEquipos:equipos,                                   
                                            arrayGeneralDescuentos:JSON.stringify(arrayGeneralDescuentos),
                                            arrayGeneralProdFacturar:JSON.stringify(arrayGeneralProdFacturar),
                                            strCreaNC:strCreaNC,
                                            floatInstalacion:instalacion,
                                            floatSubtotalnc:subtotalnc,                                        
                                            strCaracteristicas:caracteristicas,
                                            strFacturaCancelacion:strFacturaCancelacion,
                                            motivoCancelacion:motivoCancelacion,
                                            observacion:observacion,
                                            floatSubtotalNDI:subtotalNDI,
                                            strObservacionesCancelacion:observacionCancelacion,
                                            codigoPlantillaCancelacion:codigoPlantillaCancelacion,
                                            equiposSeleccionados:JSON.stringify(equiposSeleccionados),
                                            motivoCancelacionText:motivoCancelacionText
                                        },
                                        success: function(response){
                                            Ext.get(gridServicios.getId()).unmask();
                                            var objData = Ext.JSON.decode(response.responseText);
                                            var strStatus = objData.strStatus;
                                            var strMensaje = objData.strMensaje;
                                            if(strStatus == "Ok"){
                                                Ext.Msg.alert('Mensaje',strMensaje, function(btn){
                                                    if(btn=='ok'){
                                                        Ext.get("grid").mask('Cancelando el Servicio...');
                                                        Ext.Ajax.request({
                                                            url: urlCancelarServicioProdAdicional,
                                                            method: 'post',
                                                            timeout: 400000,
                                                            params: { 
                                                                idServicio: data.idServicio,
                                                                idAccion: idAccion,
                                                                strMotivo: motivo,
                                                                floatSubtotal:subtotal,
                                                                floatEquipos:10,
                                                                arrayGeneralDescuentos:JSON.stringify(arrayGeneralDescuentos),
                                                                arrayGeneralProdFacturar:JSON.stringify(arrayGeneralProdFacturar),
                                                                strCreaNC:strCreaNC,
                                                                floatInstalacion:instalacion,
                                                                floatSubtotalnc:subtotalnc,                   
                                                                strCaracteristicas:caracteristicas,
                                                                strFacturaCancelacion:strFacturaCancelacion,
                                                                motivoCancelacion:motivoCancelacion,
                                                                observacion:observacion,
                                                                floatSubtotalNDI:subtotalNDI
                                                            },
                                                            success: function(response){
                                                                Ext.get("grid").unmask();
                                                                if(response.responseText == "OK"){
                                                                    Ext.Msg.alert('Mensaje','Se Cancelo el Servicio!', function(btn){
                                                                        if(btn=='ok'){
                                                                            store.load();
                                                                        }
                                                                    });
                                                                }
                                                                else if(response.responseText == "MOTIVO ERRONEO"){
                                                                    Ext.Msg.alert('Mensaje ','No se pudo cancelar el Producto NetlifeCam, el motivo ingresado no es correcto!' );
                                                                }
                                                                else{
                                                                    Ext.Msg.alert('Mensaje ','No se pudo cancelar el Producto NetlifeCam!' );
                                                                }

                                                            }

                                                        }); 
                                                        win.destroy();
                                                    }else{
                                                        Ext.Msg.alert('Mensaje ', strMensaje );
                                                    }
                                                });
                                            }
                                            else{
                                                Ext.Msg.alert('Mensaje ', strMensaje );
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.get(gridServicios.getId()).unmask();
                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                        }
                                    }); 
                                }
                                else
                                {
                                    Ext.get("grid").mask('Cancelando el Servicio...');
                                    Ext.Ajax.request({
                                        url: urlCancelarServicioProdAdicional,
                                        method: 'post',
                                        timeout: 400000,
                                        params: { 
                                            idServicio: data.idServicio,
                                            idAccion: idAccion,
                                            strMotivo: motivo,
                                            floatSubtotal:subtotal,
                                            floatEquipos:10,
                                            arrayGeneralDescuentos:JSON.stringify(arrayGeneralDescuentos),
                                            arrayGeneralProdFacturar:JSON.stringify(arrayGeneralProdFacturar),
                                            strCreaNC:strCreaNC,
                                            floatInstalacion:instalacion,
                                            floatSubtotalnc:subtotalnc,                   
                                            strCaracteristicas:caracteristicas,
                                            strFacturaCancelacion:strFacturaCancelacion,
                                            motivoCancelacion:motivoCancelacion,
                                            observacion:observacion,
                                            floatSubtotalNDI:subtotalNDI
                                        },
                                        success: function(response){
                                            Ext.get("grid").unmask();
                                            if(response.responseText == "OK"){
                                                Ext.Msg.alert('Mensaje','Se Cancelo el Servicio!', function(btn){
                                                    if(btn=='ok'){
                                                        store.load();
                                                    }
                                                });
                                            }
                                            else if(response.responseText == "MOTIVO ERRONEO"){
                                                Ext.Msg.alert('Mensaje ','No se pudo cancelar el Producto NetlifeCam, el motivo ingresado no es correcto!' );
                                            }
                                            else{
                                                Ext.Msg.alert('Mensaje ','No se pudo cancelar el Producto NetlifeCam!' );
                                            }

                                        }

                                    });
                                }     
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert("Advertencia","Por Favor Escoja un Motivo");
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
            title: 'Cancelar Servicio NetlifeCam',
            modal: true,
            width: 580,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
            
        }//cierre response
    });
}



