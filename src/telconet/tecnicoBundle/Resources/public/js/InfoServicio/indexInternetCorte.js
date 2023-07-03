/* Funcion que sirve para mostrar la pantalla de corte 
 * y realizar la llamada ajax para la inactivacion del servicio
 * para la empresa TTCO
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param int   idAccion    id de accion de la credencial
 */
function cortarCliente(data, idAccion){
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
            title: 'Cortar Cliente',
            defaultType: 'textfield',
            defaults: {
                width: 650
            },
            items: [
                
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                        xtype: 'combo',
                        id:'comboMotivos',
                        name: 'comboMotivos',
                        store: storeMotivos,
                        fieldLabel: 'Motivo',
                        displayField: 'nombreMotivo',
                        valueField: 'idMotivo',
                        queryMode: 'local'
                    }]
                }

            ]
        }],
        buttons: [{
            text: 'Ejecutar',
            formBind: true,
            handler: function(){
                var motivo = Ext.getCmp('comboMotivos').getValue();
                Ext.Msg.alert('Mensaje','Está seguro que desea cortar al Servicio?', function(btn){
                    if(btn=='ok'){
                        Ext.get(gridServicios.getId()).mask('Esperando Respuesta del Elemento...');

                        Ext.Ajax.request({
                            url: cortarClienteBoton,
                            method: 'post',
                            timeout: 400000,
                            params: { 
                                idServicio: data.idServicio,
                                idProducto: data.productoId,
                                perfil: data.perfilDslam,
                                login: data.login,
                                capacidad1: data.capacidadUno,
                                capacidad2: data.capacidadDos,
                                motivo: motivo,
                                idAccion: idAccion
                            },
                            success: function(response){
                                Ext.get(gridServicios.getId()).unmask();
                                var objData = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.status;
                                var strMensaje = objData.mensaje;
                                if(strStatus == "OK"){
                    //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                    Ext.Msg.alert('Mensaje', strMensaje, function(btn){
                                        if(btn=='ok'){
                                            store.load();
                                            win.destroy();
                                        }
                                    });
                                }
                                else if(strMensaje=="NO EXISTE TAREA"){
                                    Ext.Msg.alert('Mensaje ','No existe la Tarea, favor revisar!' );
                                }
                                else if(strMensaje=="OK SIN EJECUCION"){
                                    Ext.Msg.alert('Mensaje ','Se Corto el Servicio, Sin ejecutar Script' );
                                }
                                else{
                                    Ext.Msg.alert('Mensaje ',strMensaje);
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
        width: 350,
        closable: false,
        layout: 'fit',
        items: [formPanel]
    }).show();

    
       
}

/* Funcion que sirve para mostrar la pantalla de corte 
 * y realizar la llamada ajax para la inactivacion del servicio
 * para la empresa MD
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param int   idAccion    id de accion de la credencial
 */
function cortarServicioMd(data,idAccion){
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
            var strName         = 'plan';
            var strFieldLabel   = 'Plan';
            var strDisplayField = data.nombrePlan;
            var strValue        = data.nombrePlan;
            var strVelocidad         = '';
            var strLabelVelocidad    = '';
            var strDisplayVelocidad  = '';
            var strValueVelocidad    = '';
            var strDescVelocidad     = '';
            var boolOcultaCapacidad  = false;
            var boolOcultaVelocidad  = true;
            
            if (data.descripcionProducto == 'INTERNET SMALL BUSINESS' || data.descripcionProducto == 'TELCOHOME')
            {
                strName         = 'producto';
                strFieldLabel   = 'Producto';
                strDisplayField = data.descripcionProducto;
                strValue        = data.descripcionProducto;
                
                //Velocidad.
                strVelocidad        = 'velocidad';
                strLabelVelocidad   = 'Velocidad';
                strDisplayVelocidad = data.velocidadISB;
                strDescVelocidad    = data.velocidadISB;
                strValueVelocidad   = strDescVelocidad + ' MB';
                
                boolOcultaCapacidad = true;
                boolOcultaVelocidad = false;
            }
            
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
                      {name:'nombreMotivo', mapping:'nombreMotivo'},
                      {name:'boolEsMotivoInaudit', mapping:'boolEsMotivoInaudit'}
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
        //                                id:'capacidadUno',
                                        name:         strName,
                                        fieldLabel:   strFieldLabel,
                                        displayField: strDisplayField,
                                        value:        strValue,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'capacidadUno',
                                        fieldLabel: 'Capacidad Uno',
                                        displayField: data.capacidadUno,
                                        value: data.capacidadUno,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%',
                                        hidden : boolOcultaCapacidad
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'capacidadDos',
                                        fieldLabel: 'Capacidad Dos',
                                        displayField: data.capacidadDos,
                                        value: data.capacidadDos,
                                        readOnly: true,
                                        width: '30%',
                                        hidden : boolOcultaCapacidad
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'capacidadTres',
                                        fieldLabel: 'Capacidad Int/Prom Uno',
                                        displayField: data.capacidadTres,
                                        value: data.capacidadTres,
                                        readOnly: true,
                                        width: '30%',
                                        hidden : boolOcultaCapacidad
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'numeroPcCliente',
                                        name: 'capacidadCuatro',
                                        fieldLabel: 'Capacidad Int/Prom Dos',
                                        displayField: data.capacidadCuatro,
                                        value: data.capacidadCuatro,
                                        readOnly: true,
                                        width: '30%',
                                        hidden : boolOcultaCapacidad
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'numeroPcCliente',
                                        name: strVelocidad,
                                        fieldLabel:  strLabelVelocidad,
                                        displayField:  strDisplayVelocidad,
                                        value: strValueVelocidad,
                                        readOnly: true,
                                        width: '30%',
                                        hidden : boolOcultaVelocidad
                                    }
                                    //---------------------------------------------
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
                                        queryMode: 'local',
                                        listeners:
                                        {
                                            select: function(combo, record, index)
                                            {
                                                var boolEtiqueta = record[0].data.boolEsMotivoInaudit                                               
                                                if ( boolEtiqueta && data.idEmpresa == 18)
                                                {
                                                    Ext.getCmp('lbl_abusador').setText('* Posible Abusador');
                                                    Ext.getCmp('lbl_abusador').show();
                                                }
                                                else
                                                {
                                                    Ext.getCmp('lbl_abusador').hide();
                                                }
                                                
                                            },
                                        }
                                    },
                                    { width: '15%', border: false},
                                    { width: '30%', border: false},
                                    //{ width: '10%', border: false},
                                    {
                                        xtype: 'label',
                                        id: 'lbl_abusador',
                                        style: 'font-weight: bold; color: red; background-color: transparent; border: transparent 1px groove;border-radius: .5em; font-size: 14px;',
                                        margin: '0 0 0 20',
                                    },

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
                id: 'btnEjecutar',
                handler: function(){
                    var motivo = Ext.getCmp('comboMotivos').getValue();
                    var validacion = false;
                    var boolEsMotivoInaudit = Ext.getCmp('comboMotivos').findRecord('idMotivo', motivo).data.boolEsMotivoInaudit;
                    if(motivo!=null){
                        validacion=true;
                    }
                    
                    if(validacion){
                        Ext.Msg.confirm('Mensaje','Esta seguro que desea Cortar el Servicio?', function(btn){
                            if(btn=='yes'){
                                Ext.getCmp("btnEjecutar").disable();
                                Ext.get(formPanel.getId()).mask('Procesando...');
                                Ext.get(gridServicios.getId()).mask('Esperando Respuesta del Elemento...');
                                Ext.Ajax.request({
                                    url: cortarClienteBoton,
                                    method: 'post',
                                    timeout: 600000,
                                    params: { 
                                        idServicio: data.idServicio,
                                        idProducto: data.productoId,
                                        perfil:     data.perfilDslam,
                                        login:      data.login,
                                        capacidad1: data.capacidadUno,
                                        capacidad2: data.capacidadDos,
                                        motivo:     motivo,
                                        idAccion:   idAccion,
                                        boolEsMotivoInaudit: boolEsMotivoInaudit
                                    },
                                    success: function(response){
                                        Ext.get(gridServicios.getId()).unmask();
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.getCmp("btnEjecutar").setDisabled(false);
                                        var objData = Ext.JSON.decode(response.responseText);
                                        var strStatus = objData.status;
                                        var strMensaje = objData.mensaje;
                                        if(strStatus == "OK"){
                                            Ext.Msg.alert('Mensaje',strMensaje, function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else if(strMensaje=="NO EXISTE TAREA"){
                                            Ext.Msg.alert('Mensaje ','No existe la Tarea, favor revisar!' );
                                        }
                                        else if(strMensaje=="OK SIN EJECUCION"){
                                            Ext.Msg.alert('Mensaje ','Se Corto el Servicio, Sin ejecutar Script' );
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje ',strMensaje );
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(gridServicios.getId()).unmask();
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.getCmp("btnEjecutar").setDisabled(false);
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

/* Funcion que sirve para mostrar la pantalla de corte temporal
 * y realizar la llamada ajax para la inactivacion del servicio
 * para la empresa TTCO
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 */
function cortarTemporalCliente(data){
    var storeMotivos = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getMotivosSuspensionTemporal,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                accion: "index",
                nombreModulo: "solicitudsuspensiontemporal"
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
            title: 'Suspension Temporal Cliente',
            defaultType: 'textfield',
            defaults: {
                width: 650
            },
            items: [
                
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                        xtype: 'combo',
                        id:'comboMotivos',
                        name: 'comboMotivos',
                        store: storeMotivos,
                        fieldLabel: 'Motivo',
                        displayField: 'nombreMotivo',
                        valueField: 'idMotivo',
                        queryMode: 'local'
                    }]
                }

            ]
        }],
        buttons: [{
            text: 'Ejecutar',
            formBind: true,
            handler: function(){
                var motivo = Ext.getCmp('comboMotivos').getValue();
                Ext.Msg.alert('Mensaje','Está seguro que desea cortar al Cliente?', function(btn){
                    if(btn=='ok'){
                        Ext.get(gridServicios.getId()).mask('Esperando Respuesta del Elemento...');

                        Ext.Ajax.request({
                            url: cortarTemporalClienteBoton,
                            method: 'post',
                            timeout: 400000,
                            params: { 
                                idServicio: data.idServicio,
                                idProducto: data.productoId,
                                perfil: data.perfilDslam,
                                login: data.login,
                                capacidad1: data.capacidadUno,
                                capacidad2: data.capacidadDos,
                                motivo: motivo
                            },
                            success: function(response){
                                Ext.get(gridServicios.getId()).unmask();
                                if(response.responseText == "OK"){
                    //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                    Ext.Msg.alert('Mensaje','Se Corto el Cliente', function(btn){
                                        if(btn=='ok'){
                                            store.load();
                                            win.destroy();
                                        }
                                    });
                                }
                                else if(response.responseText=="NO EXISTE TAREA"){
                                    Ext.Msg.alert('Mensaje ','No existe la Tarea, favor revisar!' );
                                }
                                else if(response.responseText=="OK SIN EJECUCION"){
                                    Ext.Msg.alert('Mensaje ','Se Corto el Cliente, Sin ejecutar Script' );
                                }
                                else{
                                    Ext.Msg.alert('Mensaje ','No se pudo Cortar el cliente, problemas en la Ejecucion del Script!' );
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
        },{
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Cortar Cliente',
        modal: true,
        width: 350,
        closable: false,
        layout: 'fit',
        items: [formPanel]
    }).show();

    
       
}